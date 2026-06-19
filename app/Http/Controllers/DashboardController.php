<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DailyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $todayLog = DailyLog::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $recentLogs = DailyLog::where('user_id', $user->id)
            ->latest('date')
            ->take(5)
            ->get();

        $thisWeekTasks = DailyLog::where('user_id', $user->id)
            ->where('date', '>=', now()->startOfWeek())
            ->sum(DB::raw('new_sku + variation_sku + advance_data_gathering + update_listings + other_tasks'));

        $thisMonthTasks = DailyLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum(DB::raw('new_sku + variation_sku + advance_data_gathering + update_listings + other_tasks'));

        $teamLogsToday = DailyLog::where('date', $today)
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select('daily_logs.*', 'users.username', 'users.role')
            ->get();

        $loggedTodayIds = DailyLog::where('date', $today)->pluck('user_id')->toArray();
        $missingCount = User::whereNotIn('id', $loggedTodayIds)
            ->where('role', '!=', 'manager')
            ->count();

        $totalTeam = User::where('role', '!=', 'manager')->count();

        return view('dashboard', compact(
            'user', 'todayLog', 'recentLogs', 'thisWeekTasks', 'thisMonthTasks',
            'teamLogsToday', 'missingCount', 'totalTeam'
        ));
    }

    public function postingProcedure()
    {
        return view('posting-procedure');
    }

    public function dataGathering()
    {
        return view('data-gathering');
    }

    public function ecommerceRequirements()
    {
        return view('ecommerce-requirements');
    }

    public function priceCalculator()
    {
        return view('price-calculator');
    }

    public function endOfDay()
    {
        return view('end-of-day');
    }

    public function importantLinks()
    {
        return view('important-links');
    }

    public function team()
    {
        $managers = User::where('role', 'manager')->get();
        $leads = User::where('role', 'lead')->get();
        $researchers = User::where('role', 'researcher')->get();
        $content = User::where('role', 'content')->get();
        $graphics = User::where('role', 'graphics')->get();
        $backend = User::where('role', 'backend')->get();

        return view('team', compact('managers', 'leads', 'researchers', 'content', 'graphics', 'backend'));
    }
}
