<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Models\DailyLog;
use App\Models\BrandCatalog;
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
            ->sum(DB::raw('task_1 + task_2 + task_3 + task_4 + task_5'));

        $thisMonthTasks = DailyLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum(DB::raw('task_1 + task_2 + task_3 + task_4 + task_5'));

        // Analyst-specific: brand catalog stats
        $effectiveRole = ($user->isAdmin() && session()->has('preview_role'))
            ? session('preview_role')
            : $user->role;
        $catalogStats = null;
        if ($effectiveRole === 'analyst') {
            $catalogStats = [
                'total'     => BrandCatalog::count(),
                'available' => BrandCatalog::where('status', 'available')->count(),
                'upcoming'  => BrandCatalog::where('status', 'upcoming')->count(),
                'seasonal'  => BrandCatalog::where('status', 'seasonal')->count(),
            ];
        }

        $teamLogsToday = DailyLog::where('date', $today)
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->select('daily_logs.*', 'users.username', 'users.role')
            ->get();

        $loggedTodayIds = DailyLog::where('date', $today)->pluck('user_id')->toArray();
        $missingCount = User::whereNotIn('id', $loggedTodayIds)
            ->whereNotIn('role', ['manager', 'head'])
            ->count();

        $totalTeam = User::whereNotIn('role', ['manager', 'head'])->count();

        $recentAnnouncements = Announcement::with('creator')
            ->active()
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->take(2)
            ->get();

        return view('dashboard', compact(
            'user', 'todayLog', 'recentLogs', 'thisWeekTasks', 'thisMonthTasks',
            'teamLogsToday', 'missingCount', 'totalTeam', 'catalogStats',
            'recentAnnouncements'
        ));
    }

    public function userManual()
    {
        $hubUrl = Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard');
        $html = str_replace('__HUB_URL__', $hubUrl, file_get_contents(resource_path('docs/user-manual.html')));

        return response($html)->header('Content-Type', 'text/html');
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

    public function importantLinks()
    {
        return view('important-links');
    }

    public function team()
    {
        $heads = User::where('role', 'head')->get();
        $managers = User::where('role', 'manager')->get();
        $analysts = User::where('role', 'analyst')->get();
        $researchers = User::where('role', 'researcher')->get();
        $content = User::where('role', 'content')->get();
        $graphics = User::where('role', 'graphics')->get();
        $backend = User::where('role', 'backend')->get();

        return view('team', compact('heads', 'managers', 'analysts', 'researchers', 'content', 'graphics', 'backend'));
    }
}
