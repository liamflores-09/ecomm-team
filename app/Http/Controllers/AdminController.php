<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DailyLog;
use App\Models\ActivityLog;
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
        $leads = User::where('role', 'lead')->count();
        $researchers = User::where('role', 'researcher')->count();
        $content = User::where('role', 'content')->count();
        $graphics = User::where('role', 'graphics')->count();
        $backend = User::where('role', 'backend')->count();

        $totalLogs = DailyLog::count();
        $thisMonthLogs = DailyLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $dailyTotals = DailyLog::where('date', '>=', now()->subDays(6)->startOfDay())
            ->select(
                'date',
                DB::raw('SUM(new_sku) as total_new_sku'),
                DB::raw('SUM(variation_sku) as total_variation_sku'),
                DB::raw('SUM(advance_data_gathering) as total_data_gathering'),
                DB::raw('SUM(update_listings) as total_update_listings'),
                DB::raw('SUM(other_tasks) as total_other_tasks')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $dailyTotals->pluck('date')->map(fn($d) => $d->format('M d'))->toArray();
        $chartNewSku = $dailyTotals->pluck('total_new_sku')->toArray();
        $chartVariationSku = $dailyTotals->pluck('total_variation_sku')->toArray();
        $chartDataGathering = $dailyTotals->pluck('total_data_gathering')->toArray();
        $chartUpdateListings = $dailyTotals->pluck('total_update_listings')->toArray();
        $chartOtherTasks = $dailyTotals->pluck('total_other_tasks')->toArray();

        $userProductivity = DailyLog::where('date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select(
                'users.username',
                DB::raw('SUM(new_sku + variation_sku + advance_data_gathering + update_listings + other_tasks) as total_tasks')
            )
            ->groupBy('users.username')
            ->orderByDesc('total_tasks')
            ->take(8)
            ->get();

        $prodLabels = $userProductivity->pluck('username')->toArray();
        $prodData = $userProductivity->pluck('total_tasks')->toArray();

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        $todayLogs = DailyLog::where('date', now()->toDateString())
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select('daily_logs.*', 'users.username', 'users.role')
            ->get();

        return view('admin.dashboard', compact(
            'user', 'totalUsers', 'managers', 'leads', 'researchers', 'content', 'graphics', 'backend',
            'totalLogs', 'thisMonthLogs', 'recentActivity',
            'chartLabels', 'chartNewSku', 'chartVariationSku', 'chartDataGathering', 'chartUpdateListings', 'chartOtherTasks',
            'prodLabels', 'prodData',
            'todayLogs'
        ));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'))->with('user', Auth::user());
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:manager,lead,content,graphics,backend,researcher',
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role' => 'required|in:manager,lead,content,graphics,backend,researcher',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->mobile_number = $validated['mobile_number'] ?? null;
        $user->username = $validated['username'];
        $user->role = $validated['role'];

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

        $totalTasks = (clone $logQuery)->sum(DB::raw('daily_logs.new_sku + daily_logs.variation_sku + daily_logs.advance_data_gathering + daily_logs.update_listings + daily_logs.other_tasks'));
        $totalDays = (clone $logQuery)->distinct('daily_logs.date')->count('daily_logs.date');
        $avgDailyTasks = $totalDays > 0 ? round($totalTasks / $totalDays) : 0;

        $dailyTotals = (clone $logQuery)
            ->where('daily_logs.date', '>=', now()->subDays(6)->startOfDay())
            ->select(
                'daily_logs.date',
                DB::raw('SUM(daily_logs.new_sku) as total_new_sku'),
                DB::raw('SUM(daily_logs.variation_sku) as total_variation_sku'),
                DB::raw('SUM(daily_logs.advance_data_gathering) as total_data_gathering'),
                DB::raw('SUM(daily_logs.update_listings) as total_update_listings'),
                DB::raw('SUM(daily_logs.other_tasks) as total_other_tasks')
            )
            ->groupBy('daily_logs.date')
            ->orderBy('daily_logs.date')
            ->get();

        $chartLabels = $dailyTotals->pluck('date')->map(fn($d) => $d->format('M d'))->toArray();
        $chartNewSku = $dailyTotals->pluck('total_new_sku')->toArray();
        $chartVariationSku = $dailyTotals->pluck('total_variation_sku')->toArray();
        $chartDataGathering = $dailyTotals->pluck('total_data_gathering')->toArray();
        $chartUpdateListings = $dailyTotals->pluck('total_update_listings')->toArray();
        $chartOtherTasks = $dailyTotals->pluck('total_other_tasks')->toArray();

        $userProductivity = DailyLog::where('daily_logs.date', '>=', now()->subDays(6)->startOfDay())
            ->join('users', 'daily_logs.user_id', '=', 'users.id');
        if ($roleFilter) {
            $userProductivity->where('users.role', $roleFilter);
        }
        $userProductivity = $userProductivity
            ->select(
                'users.username',
                DB::raw('SUM(daily_logs.new_sku + daily_logs.variation_sku + daily_logs.advance_data_gathering + daily_logs.update_listings + daily_logs.other_tasks) as total_tasks')
            )
            ->groupBy('users.username')
            ->orderByDesc('total_tasks')
            ->take(8)
            ->get();

        $prodLabels = $userProductivity->pluck('username')->toArray();
        $prodData = $userProductivity->pluck('total_tasks')->toArray();

        $todayLogs = User::where('role', '!=', 'manager');
        if ($roleFilter) {
            $todayLogs->where('role', $roleFilter);
        }
        $todayLogs = $todayLogs->leftJoin('daily_logs', function ($join) {
            $join->on('users.id', '=', 'daily_logs.user_id')
                 ->where('daily_logs.date', '=', now()->toDateString());
        })
        ->select('users.id', 'users.username', 'users.role',
            DB::raw('COALESCE(daily_logs.new_sku, 0) as new_sku'),
            DB::raw('COALESCE(daily_logs.variation_sku, 0) as variation_sku'),
            DB::raw('COALESCE(daily_logs.advance_data_gathering, 0) as advance_data_gathering'),
            DB::raw('COALESCE(daily_logs.update_listings, 0) as update_listings'),
            DB::raw('COALESCE(daily_logs.other_tasks, 0) as other_tasks'),
            'daily_logs.remarks',
            DB::raw('CASE WHEN daily_logs.id IS NULL THEN 0 ELSE 1 END as has_logged')
        )
        ->orderBy('has_logged', 'desc')
        ->orderBy('users.first_name')
        ->get();

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
            ->where('role', '!=', 'manager');
        if ($roleFilter) {
            $missingLogs->where('role', $roleFilter);
        }
        $missingLogs = $missingLogs->get();

        // Member log status for role filter
        $members = User::where('role', '!=', 'manager');
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
                ->select('daily_logs.*', 'users.username', 'users.role');
            if ($roleFilter) {
                $selectedDayLogs->where('users.role', $roleFilter);
            }
            $selectedDayLogs = $selectedDayLogs->get();
        }

        // Day-by-day history (last 14 days)
        $historyDays = DailyLog::where('daily_logs.date', '>=', now()->subDays(14)->startOfDay());
        if ($roleFilter) {
            $historyDays->join('users', 'daily_logs.user_id', '=', 'users.id')
                ->where('users.role', $roleFilter);
        }
        $historyDays = $historyDays->select(
                'daily_logs.date',
                DB::raw('COUNT(DISTINCT daily_logs.user_id) as user_count'),
                DB::raw('SUM(daily_logs.new_sku) as total_new_sku'),
                DB::raw('SUM(daily_logs.variation_sku) as total_variation_sku'),
                DB::raw('SUM(daily_logs.advance_data_gathering) as total_data_gathering'),
                DB::raw('SUM(daily_logs.update_listings) as total_update_listings'),
                DB::raw('SUM(daily_logs.other_tasks) as total_other_tasks')
            )
            ->groupBy('daily_logs.date')
            ->orderByDesc('daily_logs.date')
            ->get();

        return view('admin.daily-logs', compact(
            'user', 'totalLogs', 'thisMonthLogs', 'todayLogCount', 'avgDailyTasks',
            'chartLabels', 'chartNewSku', 'chartVariationSku', 'chartDataGathering', 'chartUpdateListings', 'chartOtherTasks',
            'prodLabels', 'prodData',
            'todayLogs', 'allLogs', 'missingLogs',
            'memberLogStatus', 'roleFilter',
            'calendarMonth', 'calendarDays', 'selectedDay', 'selectedDayLogs',
            'historyDays'
        ));
    }
}
