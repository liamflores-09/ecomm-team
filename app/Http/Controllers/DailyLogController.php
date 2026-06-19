<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DailyLog;
use App\Support\TaskLabels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $taskLabels = TaskLabels::get($user->role);

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
            'new_sku' => 'required|integer|min:0',
            'variation_sku' => 'required|integer|min:0',
            'advance_data_gathering' => 'required|integer|min:0',
            'update_listings' => 'required|integer|min:0',
            'other_tasks' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $log = DailyLog::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $validated['date'],
            ],
            [
                'attendance' => $validated['attendance'] ?? null,
                'new_sku' => $validated['new_sku'],
                'variation_sku' => $validated['variation_sku'],
                'advance_data_gathering' => $validated['advance_data_gathering'],
                'update_listings' => $validated['update_listings'],
                'other_tasks' => $validated['other_tasks'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'eod_submitted',
            'description' => Auth::user()->first_name . ' submitted EOD report for ' . now()->format('M d, Y'),
            'metadata' => [
                'date' => $validated['date'],
                'total_tasks' => $validated['new_sku'] + $validated['variation_sku'] + $validated['advance_data_gathering'] + $validated['update_listings'] + $validated['other_tasks'],
            ],
        ]);

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
            'new_sku' => 'required|integer|min:0',
            'variation_sku' => 'required|integer|min:0',
            'advance_data_gathering' => 'required|integer|min:0',
            'update_listings' => 'required|integer|min:0',
            'other_tasks' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $dailyLog->update([
            'attendance' => $validated['attendance'] ?? null,
            'new_sku' => $validated['new_sku'],
            'variation_sku' => $validated['variation_sku'],
            'advance_data_gathering' => $validated['advance_data_gathering'],
            'update_listings' => $validated['update_listings'],
            'other_tasks' => $validated['other_tasks'],
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'nullable|string|max:10',
            'new_sku' => 'required|integer|min:0',
            'variation_sku' => 'required|integer|min:0',
            'advance_data_gathering' => 'required|integer|min:0',
            'update_listings' => 'required|integer|min:0',
            'other_tasks' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $log = DailyLog::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $validated['date'],
            ],
            [
                'attendance' => $validated['attendance'] ?? null,
                'new_sku' => $validated['new_sku'],
                'variation_sku' => $validated['variation_sku'],
                'advance_data_gathering' => $validated['advance_data_gathering'],
                'update_listings' => $validated['update_listings'],
                'other_tasks' => $validated['other_tasks'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

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
            'new_sku' => 'required|integer|min:0',
            'variation_sku' => 'required|integer|min:0',
            'advance_data_gathering' => 'required|integer|min:0',
            'update_listings' => 'required|integer|min:0',
            'other_tasks' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $dailyLog->update([
            'attendance' => $validated['attendance'] ?? null,
            'new_sku' => $validated['new_sku'],
            'variation_sku' => $validated['variation_sku'],
            'advance_data_gathering' => $validated['advance_data_gathering'],
            'update_listings' => $validated['update_listings'],
            'other_tasks' => $validated['other_tasks'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('end-of-day')->with('success', 'Daily log updated!');
    }

    public function destroy(DailyLog $dailyLog)
    {
        if ($dailyLog->user_id !== Auth::id()) {
            abort(403);
        }

        $dailyLog->delete();

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
