<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DailyLog;
use App\Models\User;
use App\Notifications\EodSubmitted;
use App\Support\TaskLabels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $effectiveRole = ($user->isAdmin() && session()->has('preview_role'))
            ? session('preview_role')
            : $user->role;
        $taskLabels = TaskLabels::get($effectiveRole);

        $existingLog = DailyLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $recentLogs = DailyLog::where('user_id', $user->id)
            ->latest('date')
            ->take(10)
            ->get();

        return view('end-of-day', compact('user', 'existingLog', 'recentLogs', 'taskLabels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'nullable|string|max:10',
            'task_1' => 'required|integer|min:0',
            'task_2' => 'required|integer|min:0',
            'task_3' => 'required|integer|min:0',
            'task_4' => 'required|integer|min:0',
            'task_5' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $log = DailyLog::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $validated['date'],
            ],
            [
                'attendance' => $validated['attendance'] ?? null,
                'task_1' => $validated['task_1'],
                'task_2' => $validated['task_2'],
                'task_3' => $validated['task_3'],
                'task_4' => $validated['task_4'],
                'task_5' => $validated['task_5'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'eod_submitted',
            'description' => Auth::user()->first_name . ' submitted EOD report for ' . now()->format('M d, Y'),
            'metadata' => [
                'date' => $validated['date'],
                'total_tasks' => $validated['task_1'] + $validated['task_2'] + $validated['task_3'] + $validated['task_4'] + $validated['task_5'],
            ],
        ]);

        if ($log->wasRecentlyCreated) {
            $submitter = Auth::user();
            User::whereIn('role', ['manager', 'head'])->get()
                ->each(fn($admin) => $admin->notify(new EodSubmitted($submitter, $validated['date'])));
        }

        return redirect()->route('end-of-day')->with('success', 'Daily log saved successfully!');
    }

    public function edit(DailyLog $dailyLog)
    {
        $user = Auth::user();

        if ($dailyLog->user_id !== $user->id) {
            abort(403);
        }

        $recentLogs = DailyLog::where('user_id', $user->id)
            ->latest('date')
            ->take(10)
            ->get();

        return view('end-of-day', compact('user', 'existingLog', 'recentLogs'));
    }

    public function update(Request $request, DailyLog $dailyLog)
    {
        if ($dailyLog->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'attendance' => 'nullable|string|max:10',
            'task_1' => 'required|integer|min:0',
            'task_2' => 'required|integer|min:0',
            'task_3' => 'required|integer|min:0',
            'task_4' => 'required|integer|min:0',
            'task_5' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $dailyLog->update([
            'attendance' => $validated['attendance'] ?? null,
            'task_1' => $validated['task_1'],
            'task_2' => $validated['task_2'],
            'task_3' => $validated['task_3'],
            'task_4' => $validated['task_4'],
            'task_5' => $validated['task_5'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'eod_updated',
            'description' => Auth::user()->first_name . ' updated EOD report for ' . $dailyLog->date->format('M d, Y'),
        ]);

        return redirect()->route('end-of-day')->with('success', 'Daily log updated!');
    }

    public function destroy(DailyLog $dailyLog)
    {
        if ($dailyLog->user_id !== Auth::id()) {
            abort(403);
        }

        $dailyLog->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'eod_deleted',
            'description' => Auth::user()->first_name . ' deleted EOD report',
        ]);

        return redirect()->route('end-of-day')->with('success', 'Daily log deleted.');
    }

    public function history()
    {
        $user = Auth::user();

        $logs = DailyLog::where('user_id', $user->id)
            ->latest('date')
            ->paginate(20);

        return view('daily-logs.history', compact('user', 'logs'));
    }
}
