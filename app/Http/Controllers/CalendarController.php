<?php

namespace App\Http\Controllers;

use App\Models\CalendarCategory;
use App\Models\CalendarEvent;
use App\Models\CalendarTask;
use App\Models\User;
use App\Notifications\CalendarEventNotification;
use App\Notifications\CalendarTaskNotification;
use App\Notifications\CalendarTaskCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $categories = CalendarCategory::orderBy('name')->get();
        $users      = User::orderBy('first_name')->get();
        $user       = Auth::user();

        return view('calendar', compact('categories', 'users', 'user'));
    }

    private const ROLE_COLORS = [
        'manager'    => '#1e293b',
        'content'    => '#0ea5e9',
        'graphics'   => '#f59e0b',
        'backend'    => '#f43f5e',
        'researcher' => '#10b981',
    ];

    public function events(Request $request)
    {
        $start   = $request->query('start');
        $end     = $request->query('end');
        $catIds  = $request->query('categories');
        $user    = Auth::user();

        // Events — managers see all; others see team-wide (no attendees) + their own
        $events = CalendarEvent::with('category', 'attendees')
            ->when($start,  fn($q) => $q->where('end_datetime', '>=', $start))
            ->when($end,    fn($q) => $q->where('start_datetime', '<=', $end))
            ->when($catIds, fn($q) => $q->whereIn('category_id', (array) $catIds))
            ->when($user->role !== 'manager', function ($q) use ($user) {
                $q->where(function ($inner) use ($user) {
                    $inner->doesntHave('attendees')
                          ->orWhereHas('attendees', fn($a) => $a->where('users.id', $user->id));
                });
            })
            ->get()
            ->map(fn($e) => [
                'id'            => 'ev-' . $e->id,
                'title'         => $e->title,
                'start'         => $e->start_datetime->toIso8601String(),
                'end'           => $e->end_datetime->toIso8601String(),
                'color'         => $e->category->color,
                'extendedProps' => [
                    'type'          => 'event',
                    'db_id'         => $e->id,
                    'category_id'   => $e->category_id,
                    'category_name' => $e->category->name,
                    'location'      => $e->location,
                    'description'   => $e->description,
                    'attendees'     => $e->attendees->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name]),
                ],
            ]);

        // Tasks — only parent tasks (parent_id null); managers see all
        $tasks = CalendarTask::with(['category', 'subtasks'])
            ->whereNull('parent_id')
            ->when($start,  fn($q) => $q->where('due_date', '>=', substr($start, 0, 10)))
            ->when($end,    fn($q) => $q->where('due_date', '<=', substr($end,   0, 10)))
            ->when($catIds, fn($q) => $q->whereIn('category_id', (array) $catIds))
            ->when($user->role !== 'manager', fn($q) => $q->where('assigned_role', $user->role))
            ->get()
            ->map(function ($t) {
                $subtasks      = $t->subtasks;
                $total         = $subtasks->count();
                $done          = $subtasks->where('status', 'done')->count();
                $isDone        = $total > 0 ? $done === $total : $t->status === 'done';
                $displayTitle  = $total > 0 ? $t->title . ' (' . $done . '/' . $total . ')' : $t->title;
                $roleColor     = self::ROLE_COLORS[$t->assigned_role] ?? '#6b7280';

                return [
                    'id'            => 'tk-' . $t->id,
                    'title'         => $displayTitle,
                    'start'         => $t->due_date->toDateString(),
                    'allDay'        => true,
                    'color'         => $isDone ? '#9ca3af' : $roleColor,
                    'extendedProps' => [
                        'type'          => 'task',
                        'db_id'         => $t->id,
                        'category_id'   => $t->category_id,
                        'category_name' => $t->category->name,
                        'assigned_role' => $t->assigned_role,
                        'status'        => $isDone ? 'done' : 'pending',
                        'description'   => $t->description,
                        'subtasks'      => $subtasks->map(fn($s) => [
                            'id'     => $s->id,
                            'title'  => $s->title,
                            'status' => $s->status,
                        ]),
                    ],
                ];
            });

        return response()->json($events->merge($tasks)->values());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'    => 'required|exists:calendar_categories,id',
            'title'          => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after_or_equal:start_datetime',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'attendees'      => 'nullable|array',
            'attendees.*'    => 'exists:users,id',
        ]);

        $event = CalendarEvent::create([
            ...$data,
            'created_by' => Auth::id(),
        ]);

        if (!empty($data['attendees'])) {
            $event->attendees()->sync($data['attendees']);
        }

        // Notify attendees (or everyone if no attendees set), excluding creator
        $event->load('attendees');
        $toNotify = $event->attendees->isNotEmpty()
            ? $event->attendees->where('id', '!=', Auth::id())
            : User::where('id', '!=', Auth::id())->get();

        $creator = Auth::user();
        $toNotify->each(fn($u) => $u->notify(new CalendarEventNotification($event, $creator)));

        return response()->json(['success' => true, 'id' => $event->id]);
    }

    public function update(Request $request, CalendarEvent $event)
    {
        $data = $request->validate([
            'category_id'    => 'required|exists:calendar_categories,id',
            'title'          => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after_or_equal:start_datetime',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'attendees'      => 'nullable|array',
            'attendees.*'    => 'exists:users,id',
        ]);

        $event->update($data);
        $event->attendees()->sync($data['attendees'] ?? []);

        return response()->json(['success' => true]);
    }

    public function destroy(CalendarEvent $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

    public function storeTask(Request $request)
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:calendar_categories,id',
            'title'             => 'required|string|max:255',
            'due_date'          => 'required|date',
            'assigned_role'     => 'required|in:content,graphics,backend,researcher,manager',
            'description'       => 'nullable|string',
            'subtasks'          => 'nullable|array',
            'subtasks.*.title'  => 'required|string|max:255',
        ]);

        $task = CalendarTask::create([
            'category_id'   => $data['category_id'],
            'title'         => $data['title'],
            'due_date'      => $data['due_date'],
            'assigned_role' => $data['assigned_role'],
            'description'   => $data['description'] ?? null,
            'created_by'    => Auth::id(),
        ]);

        foreach ($data['subtasks'] ?? [] as $sub) {
            CalendarTask::create([
                'parent_id'     => $task->id,
                'category_id'   => $task->category_id,
                'title'         => trim($sub['title']),
                'due_date'      => $task->due_date,
                'assigned_role' => $task->assigned_role,
                'created_by'    => Auth::id(),
            ]);
        }

        // Notify users with the assigned role + managers, excluding creator
        $creator = Auth::user();
        User::where(function ($q) use ($task) {
                $q->where('role', $task->assigned_role)
                  ->orWhere('role', 'manager');
            })
            ->where('id', '!=', $creator->id)
            ->get()
            ->each(fn($u) => $u->notify(new CalendarTaskNotification($task, $creator)));

        return response()->json(['success' => true, 'id' => $task->id]);
    }

    public function updateTask(Request $request, CalendarTask $task)
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:calendar_categories,id',
            'title'         => 'required|string|max:255',
            'due_date'      => 'required|date',
            'assigned_role' => 'required|in:content,graphics,backend,researcher,manager',
            'description'   => 'nullable|string',
            'subtasks'      => 'nullable|array',
            'subtasks.*.id'     => 'nullable|integer',
            'subtasks.*.title'  => 'required|string|max:255',
        ]);

        $task->update([
            'category_id'   => $data['category_id'],
            'title'         => $data['title'],
            'due_date'      => $data['due_date'],
            'assigned_role' => $data['assigned_role'],
            'description'   => $data['description'] ?? null,
        ]);

        // Sync subtasks: keep existing by id, add new, remove deleted
        $incoming    = collect($data['subtasks'] ?? []);
        $incomingIds = $incoming->pluck('id')->filter()->values();

        // Delete removed subtasks
        $task->subtasks()->whereNotIn('id', $incomingIds)->delete();

        // Update existing / create new
        foreach ($incoming as $sub) {
            if (!empty($sub['id'])) {
                CalendarTask::where('id', $sub['id'])->where('parent_id', $task->id)
                    ->update(['title' => trim($sub['title'])]);
            } else {
                CalendarTask::create([
                    'parent_id'     => $task->id,
                    'category_id'   => $task->category_id,
                    'title'         => trim($sub['title']),
                    'due_date'      => $task->due_date,
                    'assigned_role' => $task->assigned_role,
                    'created_by'    => Auth::id(),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroyTask(CalendarTask $task)
    {
        $task->delete(); // cascades to subtasks via FK
        return response()->json(['success' => true]);
    }

    public function toggleTask(CalendarTask $task)
    {
        $newStatus = $task->status === 'done' ? 'pending' : 'done';
        $task->update(['status' => $newStatus]);

        $completedTask = null;

        if ($task->parent_id) {
            // Subtask — check if all siblings done → auto-complete parent
            $parent  = $task->parent;
            $allDone = $parent->subtasks->every(fn($s) => $s->status === 'done');
            $parent->update(['status' => $allDone ? 'done' : 'pending']);

            if ($allDone) {
                $completedTask = $parent; // notify about parent completing
            }
        } elseif ($newStatus === 'done') {
            $completedTask = $task;
        }

        // Notify managers + creator (if different) when a parent task is completed
        if ($completedTask) {
            $actor = Auth::user();
            $recipients = User::where(function ($q) use ($completedTask, $actor) {
                    $q->where('role', 'manager')
                      ->orWhere('id', $completedTask->created_by);
                })
                ->where('id', '!=', $actor->id)
                ->get();

            $recipients->each(fn($u) => $u->notify(new CalendarTaskCompletedNotification($completedTask, $actor)));
        }

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:calendar_categories,name',
            'color' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $cat = CalendarCategory::create([...$data, 'created_by' => Auth::id()]);

        return response()->json(['success' => true, 'category' => $cat]);
    }

    public function destroyCategory(CalendarCategory $category)
    {
        $category->delete();
        return response()->json(['success' => true]);
    }
}
