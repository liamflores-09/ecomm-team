<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    private const EXCLUDED_ROLES = ['manager', 'head', 'analyst'];
    private const VALID_STATUSES = ['present', 'half_day', 'vl', 'sl', 'absent', 'ut', 'holiday'];

    public function index()
    {
        $month = Carbon::parse(request()->query('month', now()->format('Y-m')) . '-01');

        $users = User::whereNotIn('role', self::EXCLUDED_ROLES)
            ->orderBy('role')
            ->orderBy('first_name')
            ->get();

        $usersByRole = $users->groupBy('role');

        $records = Attendance::whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->whereIn('user_id', $users->pluck('id'))
            ->get();

        $attendanceJson = [];
        foreach ($records as $record) {
            $attendanceJson[$record->user_id][$record->date->format('Y-m-d')] = $record->status;
        }

        return view('admin.attendance', [
            'month'          => $month,
            'prevMonth'      => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth'      => $month->copy()->addMonth()->format('Y-m'),
            'daysInMonth'    => $month->daysInMonth,
            'usersByRole'    => $usersByRole,
            'attendanceJson' => $attendanceJson,
        ]);
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date_format:Y-m-d',
            'status'  => 'nullable|in:' . implode(',', self::VALID_STATUSES),
        ]);

        if (empty($validated['status'])) {
            Attendance::where('user_id', $validated['user_id'])
                ->where('date', $validated['date'])
                ->delete();
        } else {
            Attendance::updateOrCreate(
                ['user_id' => $validated['user_id'], 'date' => $validated['date']],
                ['status'  => $validated['status']]
            );
        }

        return response()->json(['success' => true]);
    }

    public function markHoliday(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $userIds = User::whereNotIn('role', self::EXCLUDED_ROLES)->pluck('id');

        foreach ($userIds as $userId) {
            Attendance::updateOrCreate(
                ['user_id' => $userId, 'date' => $validated['date']],
                ['status'  => 'holiday']
            );
        }

        return response()->json(['success' => true, 'count' => $userIds->count()]);
    }
}
