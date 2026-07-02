<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\DailyLog;
use App\Models\ActivityLog;
use App\Support\TaskLabels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $totalUsers = User::count();
        $managers = User::where('role', 'manager')->count();
        $researchers = User::where('role', 'researcher')->count();
        $content = User::where('role', 'content')->count();
        $graphics = User::where('role', 'graphics')->count();
        $backend = User::where('role', 'backend')->count();

        $totalLogs = DailyLog::count();
        $thisMonthLogs = DailyLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        // This month total tasks
        $thisMonthTasks = DailyLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum(DB::raw('task_1 + task_2 + task_3 + task_4 + task_5'));

        // Last month for comparison
        $lastMonthTasks = DailyLog::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->sum(DB::raw('task_1 + task_2 + task_3 + task_4 + task_5'));

        // Today's status — analysts excluded (they don't submit EOD)
        $eodRoles = ['manager', 'head', 'analyst'];
        $todayLogged = DailyLog::where('date', now()->toDateString())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', $eodRoles)
            ->distinct('daily_logs.user_id')
            ->count('daily_logs.user_id');
        $nonManagerUsers = User::whereNotIn('role', $eodRoles)->count();
        $todayPending = max(0, $nonManagerUsers - $todayLogged);

        // Top contributor this month
        $topContributor = DailyLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select('users.username', 'users.first_name', DB::raw('SUM(task_1 + task_2 + task_3 + task_4 + task_5) as total'))
            ->groupBy('users.username', 'users.first_name')
            ->orderByDesc('total')
            ->first();

        $dailyTotals = DailyLog::where('date', '>=', now()->subDays(29)->startOfDay())
            ->select(
                'date',
                DB::raw('SUM(task_1) as total_task_1'),
                DB::raw('SUM(task_2) as total_task_2'),
                DB::raw('SUM(task_3) as total_task_3'),
                DB::raw('SUM(task_4) as total_task_4'),
                DB::raw('SUM(task_5) as total_task_5')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $dailyTotals->pluck('date')->map(fn($d) => $d->format('D'))->toArray();
        $chartNewSku = $dailyTotals->pluck('total_task_1')->toArray();
        $chartVariationSku = $dailyTotals->pluck('total_task_2')->toArray();
        $chartDataGathering = $dailyTotals->pluck('total_task_3')->toArray();
        $chartUpdateListings = $dailyTotals->pluck('total_task_4')->toArray();
        $chartOtherTasks = $dailyTotals->pluck('total_task_5')->toArray();

        $userProductivity = DailyLog::where('date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select(
                'users.username',
                'users.first_name',
                DB::raw('SUM(task_1 + task_2 + task_3 + task_4 + task_5) as total_tasks')
            )
            ->groupBy('users.username', 'users.first_name')
            ->orderByDesc('total_tasks')
            ->take(8)
            ->get();

        $prodLabels = $userProductivity->pluck('username')->toArray();
        $prodData = $userProductivity->pluck('total_tasks')->toArray();

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $todayLogs = DailyLog::where('date', now()->toDateString())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select('daily_logs.*', 'users.username', 'users.first_name', 'users.role')
            ->get();

        // Derived values — kept out of the view
        $nonManagerCount  = User::whereNotIn('role', ['manager', 'head', 'analyst'])->count();
        $taskChange       = $lastMonthTasks > 0
            ? round(($thisMonthTasks - $lastMonthTasks) / $lastMonthTasks * 100)
            : null;
        $healthPct        = $nonManagerCount > 0 ? round($todayLogged / $nonManagerCount * 100) : 0;
        $healthColor      = $healthPct >= 80 ? 'var(--emerald)' : ($healthPct >= 50 ? 'var(--amber)' : 'var(--rose)');
        $avgTasksPerson   = $nonManagerCount > 0 ? round($thisMonthTasks / $nonManagerCount) : 0;
        $allMembers       = User::whereNotIn('role', ['manager', 'head', 'analyst'])->get();
        $loggedUserIds    = $todayLogs->pluck('user_id')->toArray();

        // 30-day trend — derived from $dailyTotals already in memory, no extra queries
        $trendMap           = $dailyTotals->keyBy(fn($d) => $d->date->format('Y-m-d'));
        $trendLabels        = [];
        $trendData          = [];
        $trendSundayIndices = [];
        for ($i = 29; $i >= 0; $i--) {
            $date          = now()->subDays($i);
            $trendLabels[] = $date->format('M j');
            if ($date->dayOfWeek === 0) {
                $trendSundayIndices[] = 29 - $i;
            }
            $day         = $trendMap->get($date->format('Y-m-d'));
            $trendData[] = $day
                ? (int) ($day->total_task_1 + $day->total_task_2 + $day->total_task_3 + $day->total_task_4 + $day->total_task_5)
                : 0;
        }
        $sparkData = array_slice($trendData, -7);

        // Per-role breakdown
        $roleMemberCounts = User::whereNotIn('role', ['manager', 'head'])
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get()->keyBy('role');

        // Per-role daily totals for last 7 days (one query, grouped by role + date)
        $roleWeeklyRaw = DailyLog::where('date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head'])
            ->select(
                'users.role',
                'daily_logs.date',
                DB::raw('SUM(daily_logs.task_1 + daily_logs.task_2 + daily_logs.task_3 + daily_logs.task_4 + daily_logs.task_5) as total')
            )
            ->groupBy('users.role', 'daily_logs.date')
            ->get()
            ->groupBy('role')
            ->map(fn($rows) => $rows->keyBy(fn($r) => $r->date->format('Y-m-d')));

        $weekLabels        = [];
        $weekSundayIndices = [];
        for ($i = 6; $i >= 0; $i--) {
            $date        = now()->subDays($i);
            $weekLabels[]= $date->format('D');
            if ($date->dayOfWeek === 0) {
                $weekSundayIndices[] = 6 - $i;
            }
        }

        $roleBreakdown = collect(['content', 'graphics', 'backend', 'researcher'])
            ->map(function ($role) use ($roleMemberCounts, $roleWeeklyRaw, $weekLabels) {
                $members  = (int) ($roleMemberCounts->get($role)->count ?? 0);
                $roleData = $roleWeeklyRaw->get($role) ?? collect();

                $series = [];
                for ($i = 6; $i >= 0; $i--) {
                    $series[] = (int) ($roleData->get(now()->subDays($i)->format('Y-m-d'))->total ?? 0);
                }

                return [
                    'role'    => $role,
                    'members' => $members,
                    'series'  => $series,
                ];
            })
            ->filter(fn($r) => $r['members'] > 0)
            ->values();

        // Task-type breakdown per role — this month
        $memberRoles = ['content', 'graphics', 'backend', 'researcher'];
        $taskTypeRaw = DailyLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereIn('users.role', $memberRoles)
            ->select(
                'users.role',
                DB::raw('SUM(task_1) as t1'), DB::raw('SUM(task_2) as t2'),
                DB::raw('SUM(task_3) as t3'), DB::raw('SUM(task_4) as t4'),
                DB::raw('SUM(task_5) as t5')
            )
            ->groupBy('users.role')
            ->get()->keyBy('role');

        $taskTypeBreakdown = collect($memberRoles)->mapWithKeys(function ($role) use ($taskTypeRaw) {
            $labels = TaskLabels::get($role);
            $row    = $taskTypeRaw->get($role);
            $names  = [];
            $data   = [];
            for ($i = 1; $i <= 5; $i++) {
                $names[] = $labels["task_$i"] ?? "Task $i";
                $data[]  = $row ? (int) $row->{"t$i"} : 0;
            }
            return [$role => ['labels' => $names, 'data' => $data]];
        });

        // Attendance — current week Mon–Sat
        $weekStart      = now()->startOfWeek();          // Monday
        $weekEnd        = $weekStart->copy()->addDays(5); // Saturday
        $weekAttendance = Attendance::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereHas('user', fn($q) => $q->whereIn('role', $memberRoles))
            ->with('user')
            ->get();

        $attWeekCounts = collect(['present', 'half_day', 'vl', 'sl', 'ut', 'absent'])
            ->mapWithKeys(fn($s) => [$s => $weekAttendance->where('status', $s)->count()]);

        $outToday = $weekAttendance
            ->filter(fn($a) => $a->date->isToday() && in_array($a->status, ['absent', 'vl', 'sl', 'half_day', 'ut']))
            ->map(fn($a) => $a->user)
            ->values();

        return view('admin.dashboard', compact(
            'user', 'totalUsers', 'managers', 'researchers', 'content', 'graphics', 'backend',
            'totalLogs', 'thisMonthLogs', 'thisMonthTasks', 'lastMonthTasks',
            'todayLogged', 'todayPending', 'topContributor',
            'recentActivity',
            'chartLabels', 'chartNewSku', 'chartVariationSku', 'chartDataGathering', 'chartUpdateListings', 'chartOtherTasks',
            'prodLabels', 'prodData',
            'todayLogs',
            'nonManagerCount', 'taskChange', 'healthPct', 'healthColor',
            'avgTasksPerson', 'allMembers', 'loggedUserIds', 'sparkData',
            'trendLabels', 'trendData', 'trendSundayIndices',
            'roleBreakdown', 'weekLabels', 'weekSundayIndices', 'taskTypeBreakdown',
            'attWeekCounts', 'outToday'
        ));
    }

    public function users()
    {
        $users        = User::all();
        $totalCount   = $users->count();
        $memberCount  = $users->whereNotIn('role', ['manager', 'head'])->count();
        $managerCount = $users->whereIn('role', ['manager', 'head'])->count();
        $roleCount    = $users->pluck('role')->unique()->count();

        return view('admin.users', compact('users', 'totalCount', 'memberCount', 'managerCount', 'roleCount'))
            ->with('user', Auth::user());
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female',
            'badge'         => 'nullable|string|max:50',
            'username'      => 'required|string|max:255|unique:users',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:head,manager,analyst,content,graphics,backend,researcher',
        ]);

        User::create([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'gender'        => $validated['gender'],
            'badge'         => $validated['badge'] ?? null,
            'username'      => $validated['username'],
            'password'      => Hash::make($validated['password']),
            'role'          => $validated['role'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'user_created',
            'description' => Auth::user()->first_name . ' added ' . $validated['first_name'] . ' ' . $validated['last_name'] . ' as ' . $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female',
            'badge'         => 'nullable|string|max:50',
            'username'      => 'required|string|max:255|unique:users,username,' . $user->id,
            'role'          => 'required|in:head,manager,analyst,content,graphics,backend,researcher',
            'password'      => 'nullable|string|min:6',
        ]);

        $user->first_name    = $validated['first_name'];
        $user->last_name     = $validated['last_name'];
        $user->mobile_number = $validated['mobile_number'] ?? null;
        $user->gender        = $validated['gender'];
        $user->badge         = $validated['badge'] ?? null;
        $user->username      = $validated['username'];
        $user->role          = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'user_updated',
            'description' => Auth::user()->first_name . ' updated ' . $user->first_name . ' ' . $user->last_name,
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->first_name . ' ' . $user->last_name;
        $user->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'user_deleted',
            'description' => Auth::user()->first_name . ' deleted ' . $userName,
        ]);

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function dailyLogs()
    {
        $user = Auth::user();
        $roleFilter = request()->query('role');

        // Base query for daily logs, optionally filtered by role
        $logQuery = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id');
        if ($roleFilter) {
            $logQuery->where('users.role', $roleFilter);
        }

        $totalLogs = (clone $logQuery)->count();
        $thisMonthLogs = (clone $logQuery)
            ->whereMonth('daily_logs.date', now()->month)
            ->whereYear('daily_logs.date', now()->year)
            ->count();
        $todayLogCount = (clone $logQuery)
            ->where('daily_logs.date', now()->toDateString())
            ->count();

        $totalTasks = (clone $logQuery)->sum(DB::raw('daily_logs.task_1 + daily_logs.task_2 + daily_logs.task_3 + daily_logs.task_4 + daily_logs.task_5'));
        $totalDays = (clone $logQuery)->distinct('daily_logs.date')->count('daily_logs.date');
        $avgDailyTasks = $totalDays > 0 ? round($totalTasks / $totalDays) : 0;

        $dailyTotals = (clone $logQuery)
            ->where('daily_logs.date', '>=', now()->subDays(6)->startOfDay())
            ->select(
                'daily_logs.date',
                DB::raw('SUM(daily_logs.task_1) as total_task_1'),
                DB::raw('SUM(daily_logs.task_2) as total_task_2'),
                DB::raw('SUM(daily_logs.task_3) as total_task_3'),
                DB::raw('SUM(daily_logs.task_4) as total_task_4'),
                DB::raw('SUM(daily_logs.task_5) as total_task_5')
            )
            ->groupBy('daily_logs.date')
            ->orderBy('daily_logs.date')
            ->get();

        $chartLabels = $dailyTotals->pluck('date')->map(fn($d) => $d->format('M d'))->toArray();
        $chartNewSku = $dailyTotals->pluck('total_task_1')->toArray();
        $chartVariationSku = $dailyTotals->pluck('total_task_2')->toArray();
        $chartDataGathering = $dailyTotals->pluck('total_task_3')->toArray();
        $chartUpdateListings = $dailyTotals->pluck('total_task_4')->toArray();
        $chartOtherTasks = $dailyTotals->pluck('total_task_5')->toArray();

        $userProductivity = DailyLog::where('daily_logs.date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id');
        if ($roleFilter) {
            $userProductivity->where('users.role', $roleFilter);
        }
        $userProductivity = $userProductivity
            ->select(
                'users.username',
                DB::raw('SUM(daily_logs.task_1 + daily_logs.task_2 + daily_logs.task_3 + daily_logs.task_4 + daily_logs.task_5) as total_tasks')
            )
            ->groupBy('users.username')
            ->orderByDesc('total_tasks')
            ->take(8)
            ->get();

        $prodLabels = $userProductivity->pluck('username')->toArray();
        $prodData = $userProductivity->pluck('total_tasks')->toArray();

        $todayLogs = User::whereNotIn('role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $todayLogs->where('role', $roleFilter);
        }
        $todayLogs = $todayLogs->leftJoin('daily_logs', function ($join) {
            $join->on('users.id', '=', 'daily_logs.user_id')
                 ->where('daily_logs.date', '=', now()->toDateString());
        })
        ->select('users.id', 'users.username', 'users.first_name', 'users.last_name', 'users.role', 'users.gender', 'users.badge',
            DB::raw('COALESCE(daily_logs.task_1, 0) as task_1'),
            DB::raw('COALESCE(daily_logs.task_2, 0) as task_2'),
            DB::raw('COALESCE(daily_logs.task_3, 0) as task_3'),
            DB::raw('COALESCE(daily_logs.task_4, 0) as task_4'),
            DB::raw('COALESCE(daily_logs.task_5, 0) as task_5'),
            'daily_logs.remarks',
            DB::raw('CASE WHEN daily_logs.id IS NULL THEN 0 ELSE 1 END as has_logged')
        )
        ->orderBy('users.role')
        ->orderBy('has_logged', 'desc')
        ->orderBy('users.first_name')
        ->get();

        // Group todayLogs by role for "All Roles" view
        $todayLogsByRole = $todayLogs->groupBy('role');

        $allLogs = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select('daily_logs.*', 'users.username', 'users.role')
            ->latest('daily_logs.date')
            ->take(50);
        if ($roleFilter) {
            $allLogs->where('users.role', $roleFilter);
        }
        $allLogs = $allLogs->get();

        $loggedTodayUserIds = DailyLog::where('date', now()->toDateString())
            ->pluck('user_id')
            ->toArray();

        $missingLogs = User::whereNotIn('id', $loggedTodayUserIds)
            ->whereNotIn('role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $missingLogs->where('role', $roleFilter);
        }
        $missingLogs = $missingLogs->get();

        // Member log status for role filter
        $members = User::whereNotIn('role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $members = $members->where('role', $roleFilter);
        }
        $members = $members->get();

        $memberLogStatus = $members->map(function ($member) {
            $todayLog = DailyLog::where('user_id', $member->id)
                ->where('date', now()->toDateString())
                ->first();

            $lastLog = DailyLog::where('user_id', $member->id)
                ->latest('date')
                ->first();

            return [
                'user' => $member,
                'todayLog' => $todayLog,
                'lastLog' => $lastLog,
            ];
        });

        // Calendar data - which days have logs
        $calendarMonth = request()->query('month') ? \Carbon\Carbon::parse(request()->query('month')) : now();
        $calendarDays = DailyLog::whereMonth('date', $calendarMonth->month)
            ->whereYear('date', $calendarMonth->year);
        if ($roleFilter) {
            $calendarDays->join('users', 'daily_logs.user_id', '=', 'users.id')
                ->where('users.role', $roleFilter);
        }
        $calendarDays = $calendarDays->distinct('daily_logs.date')
            ->pluck('daily_logs.date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $selectedDay = request()->query('day');
        $selectedDayLogs = null;
        if ($selectedDay) {
            $selectedDayLogs = DailyLog::where('daily_logs.date', $selectedDay)
                ->join('users', 'daily_logs.user_id', '=', 'users.id')
                ->whereNotIn('users.role', ['manager', 'head', 'analyst'])
                ->select('daily_logs.*', 'users.username', 'users.first_name', 'users.last_name', 'users.role', 'users.gender', 'users.badge', 'users.avatar')
                ->orderBy('users.role')->orderBy('users.first_name');
            if ($roleFilter) {
                $selectedDayLogs->where('users.role', $roleFilter);
            }
            $selectedDayLogs = $selectedDayLogs->get();
        }

        // Day-by-day history (last 14 days)
        $historyQuery = DailyLog::where('daily_logs.date', '>=', now()->subDays(14)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $historyQuery->where('users.role', $roleFilter);
        }
        $historyDays = $historyQuery->select(
                'daily_logs.date',
                'users.role',
                DB::raw('COUNT(DISTINCT daily_logs.user_id) as user_count'),
                DB::raw('SUM(daily_logs.task_1) as total_task_1'),
                DB::raw('SUM(daily_logs.task_2) as total_task_2'),
                DB::raw('SUM(daily_logs.task_3) as total_task_3'),
                DB::raw('SUM(daily_logs.task_4) as total_task_4'),
                DB::raw('SUM(daily_logs.task_5) as total_task_5')
            )
            ->groupBy('daily_logs.date', 'users.role')
            ->orderByDesc('daily_logs.date')
            ->get();

        // Group history by role for "All Roles" view
        $historyByRole = $historyDays->groupBy('role');

        // Roles present in data
        $rolesWithData = $todayLogsByRole->keys()->merge($historyByRole->keys())->unique()->sort()->values();

        // ── Per-role 7-day activity (for mini charts) ──────────────────────────
        $dlWeekLabels        = [];
        $dlWeekSundayIndices = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $dlWeekLabels[]      = $d->format('D');
            if ($d->dayOfWeek === 0) $dlWeekSundayIndices[] = 6 - $i;
        }

        $dlRoleWeeklyRaw = DailyLog::where('daily_logs.date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst'])
            ->select('users.role', 'daily_logs.date',
                DB::raw('SUM(daily_logs.task_1 + daily_logs.task_2 + daily_logs.task_3 + daily_logs.task_4 + daily_logs.task_5) as total'))
            ->groupBy('users.role', 'daily_logs.date')
            ->get()->groupBy('role')
            ->map(fn($rows) => $rows->keyBy(fn($r) => $r->date->format('Y-m-d')));

        $dlRoleMemberCounts = User::whereNotIn('role', ['manager', 'head', 'analyst'])
            ->select('role', DB::raw('COUNT(*) as count'))->groupBy('role')
            ->get()->keyBy('role');

        $rolesToShow     = $roleFilter ? [$roleFilter] : ['content', 'graphics', 'backend', 'researcher'];

        $dlRoleBreakdown = collect($rolesToShow)
            ->map(function ($role) use ($dlRoleWeeklyRaw, $dlRoleMemberCounts) {
                $roleData = $dlRoleWeeklyRaw->get($role) ?? collect();
                $series   = [];
                for ($i = 6; $i >= 0; $i--) {
                    $series[] = (int) ($roleData->get(now()->subDays($i)->format('Y-m-d'))->total ?? 0);
                }
                return [
                    'role'    => $role,
                    'members' => (int) ($dlRoleMemberCounts->get($role)->count ?? 0),
                    'series'  => $series,
                ];
            })
            ->filter(fn($r) => $r['members'] > 0)->values();

        // ── Per-role top contributors (last 7 days) ────────────────────────────
        $dlRoleTopContributors = DailyLog::where('daily_logs.date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst'])
            ->when($roleFilter, fn($q) => $q->where('users.role', $roleFilter))
            ->select('users.username', 'users.first_name', 'users.last_name', 'users.role', 'users.gender', 'users.badge', 'users.avatar',
                DB::raw('SUM(daily_logs.task_1 + daily_logs.task_2 + daily_logs.task_3 + daily_logs.task_4 + daily_logs.task_5) as total'))
            ->groupBy('users.username', 'users.first_name', 'users.last_name', 'users.role', 'users.gender', 'users.badge', 'users.avatar')
            ->orderByDesc('total')
            ->get()
            ->groupBy('role')
            ->map(fn($members) => $members->take(5)->values());

        // ── Calendar month logs pre-loaded for client-side rendering ───────────
        $monthLogsFull = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst'])
            ->whereMonth('daily_logs.date', $calendarMonth->month)
            ->whereYear('daily_logs.date', $calendarMonth->year)
            ->select('daily_logs.date', 'daily_logs.task_1', 'daily_logs.task_2', 'daily_logs.task_3',
                'daily_logs.task_4', 'daily_logs.task_5', 'daily_logs.remarks',
                'users.username', 'users.first_name', 'users.last_name', 'users.role', 'users.gender', 'users.badge', 'users.avatar')
            ->orderBy('users.role')->orderBy('users.first_name')
            ->get()
            ->groupBy(fn($l) => $l->date->format('Y-m-d'));

        $calendarLogsJson = [];
        foreach ($monthLogsFull as $dateStr => $logs) {
            foreach ($logs->groupBy('role') as $role => $roleLogs) {
                $calendarLogsJson[$dateStr][$role] = $roleLogs->map(fn($l) => [
                    'first_name' => $l->first_name,
                    'last_name'  => $l->last_name,
                    'username'   => $l->username,
                    'badge'      => $l->badge,
                    'avatar'     => \App\Models\User::resolveAvatarUrl($l->avatar, $l->first_name, $l->last_name, $l->username),
                    'tasks'      => [(int)$l->task_1,(int)$l->task_2,(int)$l->task_3,(int)$l->task_4,(int)$l->task_5],
                    'remarks'    => $l->remarks,
                ])->values()->toArray();
            }
        }

        $isSunday = now()->dayOfWeek === 0;

        return view('admin.daily-logs', compact(
            'user', 'totalLogs', 'thisMonthLogs', 'todayLogCount', 'avgDailyTasks',
            'todayLogs', 'todayLogsByRole', 'missingLogs',
            'roleFilter',
            'dlWeekLabels', 'dlWeekSundayIndices', 'dlRoleBreakdown', 'dlRoleTopContributors',
            'calendarMonth', 'calendarDays', 'selectedDay', 'selectedDayLogs', 'calendarLogsJson',
            'historyDays', 'historyByRole', 'rolesWithData', 'isSunday'
        ));
    }

    public function reports()
    {
        $user = Auth::user();
        $roleFilter = request()->query('role');
        $month = request()->query('month', now()->format('Y-m'));

        $logQuery = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $logQuery->where('users.role', $roleFilter);
        }

        $rolesWithData = $logQuery->clone()->distinct()->pluck('users.role')->sort()->values()->toArray();

        // Available months
        $availableMonthsQuery = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $availableMonthsQuery->where('users.role', $roleFilter);
        }
        $availableMonths = $availableMonthsQuery
            ->selectRaw("DISTINCT DATE_FORMAT(daily_logs.date, '%Y-%m') as month")
            ->orderByDesc('month')
            ->pluck('month')
            ->toArray();
        if (empty($availableMonths)) $availableMonths = [now()->format('Y-m')];

        // Weekly data for selected month
        $monthStart = \Carbon\Carbon::parse($month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $weeklyLogs = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst'])
            ->whereBetween('daily_logs.date', [$monthStart, $monthEnd]);
        if ($roleFilter) {
            $weeklyLogs->where('users.role', $roleFilter);
        }
        $weeklyLogs = $weeklyLogs
            ->select(
                'users.username', 'users.first_name', 'users.role', 'users.gender', 'daily_logs.date',
                DB::raw('WEEK(daily_logs.date, 1) as week_num'),
                'daily_logs.task_1', 'daily_logs.task_2', 'daily_logs.task_3',
                'daily_logs.task_4', 'daily_logs.task_5'
            )
            ->orderBy('daily_logs.date')
            ->orderBy('users.role')
            ->orderBy('users.username')
            ->get();

        // Group by week number
        $weeks = $weeklyLogs->groupBy('week_num')->map(function ($logs, $weekNum) {
            return [
                'week_num' => $weekNum,
                'days' => $logs->groupBy('date')->map(function ($dayLogs, $date) {
                    return [
                        'date' => $date,
                        'members' => $dayLogs->map(fn($l) => [
                            'username'   => $l->username,
                            'first_name' => $l->first_name,
                            'role'       => $l->role,
                            'gender'     => $l->gender,
                            'task_1'     => $l->task_1,
                            'task_2'     => $l->task_2,
                            'task_3'     => $l->task_3,
                            'task_4'     => $l->task_4,
                            'task_5'     => $l->task_5,
                        ])->values(),
                    ];
                })->values(),
                'total_t1' => $logs->sum('task_1'),
                'total_t2' => $logs->sum('task_2'),
                'total_t3' => $logs->sum('task_3'),
                'total_t4' => $logs->sum('task_4'),
                'total_t5' => $logs->sum('task_5'),
            ];
        })->values();

        // Month totals
        $monthTotal = [
            't1' => $weeklyLogs->sum('task_1'),
            't2' => $weeklyLogs->sum('task_2'),
            't3' => $weeklyLogs->sum('task_3'),
            't4' => $weeklyLogs->sum('task_4'),
            't5' => $weeklyLogs->sum('task_5'),
        ];

        // Task labels for this role
        $taskLabels = \App\Support\TaskLabels::get($roleFilter ?: 'content');

        // Share/pivot data: per task type, per week, per member
        $taskKeys = ['task_1', 'task_2', 'task_3', 'task_4', 'task_5'];
        $taskNames = [
            'task_1' => $taskLabels['task_1'] ?? 'Task 1',
            'task_2' => $taskLabels['task_2'] ?? 'Task 2',
            'task_3' => $taskLabels['task_3'] ?? 'Task 3',
            'task_4' => $taskLabels['task_4'] ?? 'Task 4',
            'task_5' => $taskLabels['task_5'] ?? 'Task 5',
        ];
        $memberNames = $weeklyLogs->pluck('username')->unique()->sort()->values()->toArray();

        $shareData = collect($taskKeys)->map(function ($tk) use ($weeklyLogs, $taskNames, $memberNames) {
            $weekData = $weeklyLogs->groupBy('week_num')->map(function ($weekLogs, $wk) use ($tk, $memberNames) {
                $weekTotal = $weekLogs->sum($tk);
                $members = collect($memberNames)->mapWithKeys(function ($name) use ($weekLogs, $tk, $weekTotal) {
                    $val = $weekLogs->where('username', $name)->sum($tk);
                    $pct = $weekTotal > 0 ? round(($val / $weekTotal) * 100, 2) : 0;
                    return [$name => ['tasks' => $val, 'share' => $pct]];
                });
                return [
                    'week_num' => $wk,
                    'total' => $weekTotal,
                    'members' => $members,
                ];
            })->values();

            return [
                'task_key' => $tk,
                'task_name' => $taskNames[$tk],
                'weeks' => $weekData,
            ];
        });

        // Previous months totals
        $allMonths = collect($availableMonths)->map(function ($m) use ($roleFilter) {
            $start = \Carbon\Carbon::parse($m)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $totalsQuery = DailyLog::whereBetween('date', [$start, $end])
                ->whereHas('user', fn($q) => $q->whereNotIn('role', ['manager', 'head', 'analyst']));
            if ($roleFilter) {
                $totalsQuery->whereHas('user', fn($q) => $q->where('role', $roleFilter));
            }
            $totals = $totalsQuery->selectRaw('
                    SUM(task_1) as t1, SUM(task_2) as t2, SUM(task_3) as t3,
                    SUM(task_4) as t4, SUM(task_5) as t5
                ')->first();
            return [
                'month' => $m,
                'label' => $start->format('F'),
                'short' => $start->format('M'),
                'year' => $start->format('Y'),
                't1' => $totals->t1 ?? 0, 't2' => $totals->t2 ?? 0,
                't3' => $totals->t3 ?? 0, 't4' => $totals->t4 ?? 0,
                't5' => $totals->t5 ?? 0,
                'total' => ($totals->t1 ?? 0) + ($totals->t2 ?? 0) + ($totals->t3 ?? 0) + ($totals->t4 ?? 0) + ($totals->t5 ?? 0),
            ];
        })->sortBy('month')->values();

        // Per-member monthly breakdown: each member's task_1..task_5 per month
        $memberMonthlyQuery = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereNotIn('users.role', ['manager', 'head', 'analyst']);
        if ($roleFilter) {
            $memberMonthlyQuery->where('users.role', $roleFilter);
        }
        $memberMonthlyRaw = $memberMonthlyQuery
            ->select(
                'users.username',
                'users.first_name',
                'users.role',
                'users.gender',
                DB::raw("DATE_FORMAT(daily_logs.date, '%Y-%m') as month_key"),
                DB::raw('SUM(task_1) as t1'),
                DB::raw('SUM(task_2) as t2'),
                DB::raw('SUM(task_3) as t3'),
                DB::raw('SUM(task_4) as t4'),
                DB::raw('SUM(task_5) as t5')
            )
            ->groupBy('users.username', 'users.first_name', 'users.role', 'users.gender', 'month_key')
            ->orderBy('month_key')
            ->orderBy('users.username')
            ->get();

        // Structure: [ month_key => [ [username, t1..t5, total], ... ], ... ]
        $memberMonthly = $memberMonthlyRaw->groupBy('month_key')->map(function ($rows) {
            return $rows->map(function ($r) {
                $total = $r->t1 + $r->t2 + $r->t3 + $r->t4 + $r->t5;
                return [
                    'username'   => $r->username,
                    'first_name' => $r->first_name,
                    'role'       => $r->role,
                    'gender'     => $r->gender,
                    't1' => $r->t1, 't2' => $r->t2, 't3' => $r->t3,
                    't4' => $r->t4, 't5' => $r->t5,
                    'total' => $total,
                ];
            })->values();
        });

        // Per-role grouped data for All Roles view
        $roleColorMap = ['researcher'=>'#10b981','content'=>'#0ea5e9','graphics'=>'#f59e0b','backend'=>'#f43f5e'];
        $roleNameMap  = ['researcher'=>'Researcher','content'=>'Content','graphics'=>'Graphics','backend'=>'Backend'];

        $roleGroupedData = collect();
        if (!$roleFilter) {
            $availableRoles = $weeklyLogs->pluck('role')->unique()->sort()->values();
            foreach ($availableRoles as $role) {
                $rLogs = $weeklyLogs->where('role', $role)->values();
                if ($rLogs->isEmpty()) continue;

                $rTaskLabels  = \App\Support\TaskLabels::get($role);
                $rMemberNames = $rLogs->pluck('username')->unique()->sort()->values()->toArray();

                $rWeeks = $rLogs->groupBy('week_num')->map(function ($logs, $weekNum) {
                    return [
                        'week_num'  => $weekNum,
                        'days'      => $logs->groupBy('date')->map(fn($dl, $date) => [
                            'date'    => $date,
                            'members' => $dl->map(fn($l) => [
                                'username'   => $l->username, 'first_name' => $l->first_name,
                                'role'       => $l->role,    'gender'     => $l->gender,
                                'task_1' => $l->task_1, 'task_2' => $l->task_2, 'task_3' => $l->task_3,
                                'task_4' => $l->task_4, 'task_5' => $l->task_5,
                            ])->values(),
                        ])->values(),
                        'total_t1'  => $logs->sum('task_1'), 'total_t2' => $logs->sum('task_2'),
                        'total_t3'  => $logs->sum('task_3'), 'total_t4' => $logs->sum('task_4'),
                        'total_t5'  => $logs->sum('task_5'),
                    ];
                })->values();

                $rMonthTotal = [
                    't1' => $rLogs->sum('task_1'), 't2' => $rLogs->sum('task_2'),
                    't3' => $rLogs->sum('task_3'), 't4' => $rLogs->sum('task_4'),
                    't5' => $rLogs->sum('task_5'),
                ];

                $rShareData = collect(['task_1','task_2','task_3','task_4','task_5'])
                    ->map(function ($tk) use ($rLogs, $rTaskLabels, $rMemberNames) {
                        $weekData = $rLogs->groupBy('week_num')->map(function ($wl, $wk) use ($tk, $rMemberNames) {
                            $wTotal  = $wl->sum($tk);
                            $members = collect($rMemberNames)->mapWithKeys(function ($name) use ($wl, $tk, $wTotal) {
                                $val = $wl->where('username', $name)->sum($tk);
                                return [$name => ['tasks' => $val, 'share' => $wTotal > 0 ? round($val/$wTotal*100, 2) : 0]];
                            });
                            return ['week_num' => $wk, 'total' => $wTotal, 'members' => $members];
                        })->values();
                        return ['task_key' => $tk, 'task_name' => $rTaskLabels[$tk] ?? $tk, 'weeks' => $weekData];
                    });

                $rMemberMonthly = $memberMonthlyRaw->where('role', $role)
                    ->groupBy('month_key')
                    ->map(fn($rows) => $rows->map(fn($r) => [
                        'username' => $r->username, 'first_name' => $r->first_name, 'gender' => $r->gender,
                        't1' => $r->t1, 't2' => $r->t2, 't3' => $r->t3, 't4' => $r->t4, 't5' => $r->t5,
                        'total' => $r->t1 + $r->t2 + $r->t3 + $r->t4 + $r->t5,
                    ])->values());

                $rAllMonths = collect($availableMonths)->map(function ($m) use ($role, $memberMonthlyRaw) {
                    $mRows = $memberMonthlyRaw->where('role', $role)->where('month_key', $m);
                    $t1=$mRows->sum('t1'); $t2=$mRows->sum('t2'); $t3=$mRows->sum('t3');
                    $t4=$mRows->sum('t4'); $t5=$mRows->sum('t5');
                    return [
                        'month' => $m, 'label' => \Carbon\Carbon::parse($m)->format('F'),
                        'short' => \Carbon\Carbon::parse($m)->format('M'),
                        't1'=>$t1,'t2'=>$t2,'t3'=>$t3,'t4'=>$t4,'t5'=>$t5,
                        'total' => $t1+$t2+$t3+$t4+$t5,
                    ];
                })->filter(fn($m) => $m['total'] > 0)->sortBy('month')->values();

                $roleGroupedData[$role] = [
                    'role'          => $role,
                    'label'         => $roleNameMap[$role]  ?? ucfirst($role),
                    'color'         => $roleColorMap[$role] ?? '#94a3b8',
                    'taskLabels'    => $rTaskLabels,
                    'weeks'         => $rWeeks,
                    'monthTotal'    => $rMonthTotal,
                    'memberNames'   => $rMemberNames,
                    'shareData'     => $rShareData,
                    'memberMonthly' => $rMemberMonthly,
                    'allMonths'     => $rAllMonths,
                ];
            }
        }

        // Per-role monthly totals (legacy, kept for compatibility)
        $roleMonthTotals = $roleGroupedData->mapWithKeys(fn($rd, $role) => [
            $role => array_merge($rd['monthTotal'], [
                'total'   => array_sum($rd['monthTotal']),
                'members' => count($rd['memberNames']),
            ])
        ])->filter(fn($r) => $r['total'] > 0);

        // Full 12-month year overview for selected year
        $selectedYear = \Carbon\Carbon::parse($month)->year;
        $yearDataQuery = DailyLog::join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereYear('daily_logs.date', $selectedYear);
        if ($roleFilter) {
            $yearDataQuery->where('users.role', $roleFilter);
        }
        $yearData = $yearDataQuery
            ->select(
                DB::raw("MONTH(daily_logs.date) as month_num"),
                DB::raw('SUM(task_1) as t1'),
                DB::raw('SUM(task_2) as t2'),
                DB::raw('SUM(task_3) as t3'),
                DB::raw('SUM(task_4) as t4'),
                DB::raw('SUM(task_5) as t5')
            )
            ->groupBy('month_num')
            ->get()
            ->keyBy('month_num');

        $yearOverview = collect();
        for ($m = 1; $m <= 12; $m++) {
            $row = $yearData->get($m);
            $t1 = $row->t1 ?? 0; $t2 = $row->t2 ?? 0;
            $t3 = $row->t3 ?? 0; $t4 = $row->t4 ?? 0; $t5 = $row->t5 ?? 0;
            $yearOverview->push([
                'month' => \Carbon\Carbon::create($selectedYear, $m, 1)->format('F'),
                'month_num' => $m,
                't1' => $t1, 't2' => $t2, 't3' => $t3, 't4' => $t4, 't5' => $t5,
                'total' => $t1 + $t2 + $t3 + $t4 + $t5,
            ]);
        }

        return view('admin.reports', compact(
            'user', 'roleFilter', 'rolesWithData',
            'month', 'availableMonths', 'weeks', 'monthTotal', 'roleMonthTotals', 'allMonths', 'memberMonthly', 'yearOverview', 'selectedYear',
            'shareData', 'memberNames', 'roleGroupedData', 'roleColorMap', 'roleNameMap'
        ));
    }

    public function setPreviewRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:content,researcher,graphics,backend,analyst',
        ]);
        session(['preview_role' => $request->role]);
        return redirect()->route('dashboard');
    }

    public function clearPreviewRole()
    {
        session()->forget('preview_role');
        return redirect()->route('admin.dashboard');
    }
}
