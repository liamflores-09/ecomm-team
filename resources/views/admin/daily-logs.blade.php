@extends('layouts.app')

@section('title', 'Daily Logs — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        transition: all 0.2s;
    }

    .stat-card:hover { transform: scale(1.02); }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .stat-count { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.75rem; font-weight: 500; color: var(--gray-500); }

    .admin-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.75rem 0 1rem;
    }

    .admin-divider .ad-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .admin-divider h4 { font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .admin-divider .ad-line { flex: 1; height: 2px; background: var(--muted); }

    .logs-card {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
    }

    .logs-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid var(--muted);
    }

    .logs-header h4 { font-weight: 800; font-size: 0.85rem; margin: 0; }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-select {
        height: 36px;
        padding: 0 0.75rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 6px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--fg);
        outline: none;
        cursor: pointer;
        appearance: auto;
    }

    .filter-select:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    .logs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .logs-table thead th {
        background: var(--muted);
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
        text-align: left;
        white-space: nowrap;
    }

    .logs-table tbody td {
        padding: 0.75rem 1rem;
        border-top: 2px solid var(--muted);
        font-weight: 500;
    }

    .logs-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .logs-table .num {
        font-weight: 700;
        text-align: center;
    }

    .empty-logs {
        text-align: center;
        padding: 3rem;
        color: var(--gray-400);
        font-weight: 500;
    }

    .empty-logs i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-300); }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .logs-table-wrap { overflow-x: auto; }
        .logs-table { min-width: 800px; }
    }

    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="background: #DC2626;">ED</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Manage Users</a></li>
        <li class="nav-dropdown" id="dailyLogsDropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown()" class="has-submenu">
                <i class="fas fa-clipboard-list"></i> Daily Logs <i class="fas fa-chevron-down dropdown-arrow" id="dropdownArrow"></i>
            </a>
            <ul class="submenu" id="dailyLogsSubmenu" style="display: {{ request()->query('role') ? 'block' : 'none' }};">
                <li><a href="{{ route('admin.daily-logs') }}" class="{{ !request()->query('role') ? 'active' : '' }}">All Roles</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'content']) }}" class="{{ request()->query('role') === 'content' ? 'active' : '' }}">Content</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'lead']) }}" class="{{ request()->query('role') === 'lead' ? 'active' : '' }}">Lead</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'researcher']) }}" class="{{ request()->query('role') === 'researcher' ? 'active' : '' }}">Researcher</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'graphics']) }}" class="{{ request()->query('role') === 'graphics' ? 'active' : '' }}">Graphics</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'backend']) }}" class="{{ request()->query('role') === 'backend' ? 'active' : '' }}">Backend</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="top-bar anim-up">
        <div>
            <h2>Daily <span class="highlight">Logs</span></h2>
            <p>View and track team daily activity</p>
        </div>
    </div>

    <!-- Bento Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); grid-auto-rows: minmax(140px, auto); gap: 1rem; margin-bottom: 2rem;" class="anim-up d1 bento-grid">

        <!-- Stat: Total Logs (2 cols) -->
        <div style="background: linear-gradient(135deg, #3B82F6, #2563EB); border-radius: 12px; padding: 1.5rem; color: white; grid-column: span 2; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.8;">Total Logs</div>
                <div style="font-size: 2.5rem; font-weight: 800; line-height: 1; margin-top: 0.25rem;">{{ $totalLogs }}</div>
            </div>
            <div style="font-size: 0.8rem; opacity: 0.7; font-weight: 500;">All time submissions</div>
        </div>

        <!-- Stat: This Month -->
        <div style="background: var(--white); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-400);">This Month</div>
                <div style="font-size: 2rem; font-weight: 800; line-height: 1; margin-top: 0.25rem; color: var(--fg);">{{ $thisMonthLogs }}</div>
            </div>
            <div style="font-size: 0.75rem; color: #059669; font-weight: 600;"><i class="fas fa-arrow-up" style="margin-right: 0.25rem;"></i>{{ now()->format('F') }}</div>
        </div>

        <!-- Stat: Today -->
        <div style="background: var(--white); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-400);">Logged Today</div>
                <div style="font-size: 2rem; font-weight: 800; line-height: 1; margin-top: 0.25rem; color: var(--fg);">{{ $todayLogCount }}</div>
            </div>
            <div style="font-size: 0.75rem; color: var(--gray-400); font-weight: 500;">{{ now()->format('M d, Y') }}</div>
        </div>

        <!-- Chart: Daily Tasks (2 cols) -->
        <div style="background: var(--white); border-radius: 12px; padding: 1.25rem; grid-column: span 2;">
            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-400); margin-bottom: 0.75rem;">Daily Tasks (7 Days)</div>
            <div style="position: relative; height: 160px;">
                <canvas id="dailyTasksChart"></canvas>
            </div>
        </div>

        <!-- Chart: Top Performers (1 col) -->
        <div style="background: var(--white); border-radius: 12px; padding: 1.25rem; grid-column: span 1;">
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-400); margin-bottom: 0.75rem;">Top Performers</div>
            <div style="position: relative; height: 160px;">
                <canvas id="productivityChart"></canvas>
            </div>
        </div>

        <!-- Missing Logs (1 col) -->
        <div style="background: {{ $missingLogs->count() ? '#FEF2F2' : '#F0FDF4' }}; border-radius: 12px; padding: 1.25rem; border: 2px solid {{ $missingLogs->count() ? '#FECACA' : '#BBF7D0' }};">
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: {{ $missingLogs->count() ? '#DC2626' : '#059669' }}; margin-bottom: 0.5rem;">Missing Logs</div>
            <div style="font-size: 2rem; font-weight: 800; color: {{ $missingLogs->count() ? '#DC2626' : '#059669' }};">{{ $missingLogs->count() }}</div>
            <div style="font-size: 0.75rem; color: {{ $missingLogs->count() ? '#991B1B' : '#059669' }}; font-weight: 500; margin-top: 0.25rem;">
                @if($missingLogs->count())
                <i class="fas fa-exclamation-triangle" style="margin-right: 0.25rem;"></i>pending
                @else
                <i class="fas fa-check-circle" style="margin-right: 0.25rem;"></i>all logged
                @endif
            </div>
        </div>

        <!-- Avg Tasks (1 col) -->
        <div style="background: var(--white); border-radius: 12px; padding: 1.25rem;">
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-400); margin-bottom: 0.5rem;">Avg Tasks/Day</div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--fg);">{{ $avgDailyTasks }}</div>
        </div>
    </div>

    <style>
    @media (max-width: 900px) {
        .bento-grid { grid-template-columns: repeat(2, 1fr) !important; }
        .bento-grid > div { grid-column: span 1 !important; }
    }
    @media (max-width: 600px) {
        .bento-grid { grid-template-columns: 1fr !important; }
    }
    </style>

    <!-- Member Status -->
    <div class="admin-divider anim-up d2">
        <div class="ad-icon" style="background: #8B5CF6;"><i class="fas fa-users"></i></div>
        <h4>{{ $roleFilter ? ucfirst($roleFilter) . ' Team' : 'All Members' }} — {{ $memberLogStatus->count() }}</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Compact Member Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.5rem; margin-bottom: 2rem;" class="anim-up d2">
        @foreach($memberLogStatus as $m)
        @php
            $roleBadgeColors = [
                'content' => ['bg' => '#D1FAE5', 'text' => '#059669'],
                'lead' => ['bg' => '#FCE7F3', 'text' => '#DB2777'],
                'researcher' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                'graphics' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                'backend' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
            ];
            $rbc = $roleBadgeColors[$m['user']->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
        @endphp
        <div style="background: var(--white); border-radius: 8px; padding: 0.75rem 1rem; border-left: 3px solid {{ $m['todayLog'] ? '#059669' : '#DC2626' }}; display: flex; align-items: center; gap: 0.625rem;">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $m['user']->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;" alt="">
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 700; font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $m['user']->first_name }} {{ $m['user']->last_name }}</div>
                <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.125rem;">
                    <span style="padding: 0.05rem 0.3rem; border-radius: 2px; font-size: 0.5rem; font-weight: 700; text-transform: uppercase; background: {{ $rbc['bg'] }}; color: {{ $rbc['text'] }};">{{ $m['user']->role }}</span>
                    @if($m['todayLog'])
                    <span style="color: #059669; font-size: 0.55rem; font-weight: 700;"><i class="fas fa-check-circle"></i></span>
                    @else
                    <span style="color: #DC2626; font-size: 0.55rem; font-weight: 700;"><i class="fas fa-times-circle"></i></span>
                    @endif
                </div>
            </div>
            @if($m['todayLog'])
            @php
                $mLabels = \App\Support\TaskLabels::get($m['user']->role);
            @endphp
            <div style="display: flex; gap: 0.25rem;">
                <span title="{{ $mLabels['task_1'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #3B82F6; cursor: help;">{{ $m['todayLog']->task_1 }}</span>
                <span title="{{ $mLabels['task_2'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #8B5CF6; cursor: help;">{{ $m['todayLog']->task_2 }}</span>
                <span title="{{ $mLabels['task_3'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #059669; cursor: help;">{{ $m['todayLog']->task_3 }}</span>
                <span title="{{ $mLabels['task_4'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #F59E0B; cursor: help;">{{ $m['todayLog']->task_4 }}</span>
                <span title="{{ $mLabels['task_5'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #EC4899; cursor: help;">{{ $m['todayLog']->task_5 }}</span>
            </div>
            @else
            <span style="font-size: 0.65rem; color: var(--gray-400); font-weight: 500; white-space: nowrap;">{{ $m['lastLog'] ? $m['lastLog']->date->diffForHumans() : 'Never' }}</span>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Today's Logs -->
    <div class="admin-divider anim-up d3">
        <div class="ad-icon" style="background: #059669;"><i class="fas fa-list-check"></i></div>
        <h4>Today's Logs — {{ now()->format('F j, Y') }}</h4>
        <div class="ad-line"></div>
    </div>

    @php
        $currentLabels = $roleFilter ? \App\Support\TaskLabels::get($roleFilter) : \App\Support\TaskLabels::get('content');
    @endphp

    <div style="background: var(--white); border-radius: 10px; overflow: hidden; margin-bottom: 2rem;" class="anim-up d3">
        @if($todayLogs->count())
        <div class="logs-table-wrap">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th style="text-align: center;">{{ $currentLabels['col1'] }}</th>
                        <th style="text-align: center;">{{ $currentLabels['col2'] }}</th>
                        <th style="text-align: center;">{{ $currentLabels['col3'] }}</th>
                        <th style="text-align: center;">{{ $currentLabels['col4'] }}</th>
                        <th style="text-align: center;">{{ $currentLabels['col5'] }}</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayLogs as $log)
                    @php
                        $roleBadgeColors = [
                            'content' => ['bg' => '#D1FAE5', 'text' => '#059669'],
                            'lead' => ['bg' => '#FCE7F3', 'text' => '#DB2777'],
                            'researcher' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                            'graphics' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                            'backend' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
                        ];
                        $rc = $roleBadgeColors[$log->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                    @endphp
                    <tr style="{{ !$log->has_logged ? 'opacity: 0.6;' : '' }}">
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $log->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 24px; height: 24px; border-radius: 50%;" alt="">
                                <span style="font-weight: 600;">{{ $log->username }}</span>
                            </div>
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; background: {{ $rc['bg'] }}; color: {{ $rc['text'] }};">{{ $log->role }}</span>
                        </td>
                        <td class="num">{{ $log->task_1 }}</td>
                        <td class="num">{{ $log->task_2 }}</td>
                        <td class="num">{{ $log->task_3 }}</td>
                        <td class="num">{{ $log->task_4 }}</td>
                        <td class="num">{{ $log->task_5 }}</td>
                        <td>
                            @if($log->has_logged)
                            <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; background: #D1FAE5; color: #059669;"><i class="fas fa-check" style="margin-right: 0.15rem;"></i>Logged</span>
                            @else
                            <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; background: #FEE2E2; color: #DC2626;"><i class="fas fa-clock" style="margin-right: 0.15rem;"></i>Pending</span>
                            @endif
                        </td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--gray-500); font-size: 0.8rem;">{{ $log->remarks ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 3rem; color: var(--gray-400); font-weight: 500;">
            <i class="fas fa-clipboard-list" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
            No members found.
        </div>
        @endif
    </div>

    <!-- Calendar & Day View -->
    <div class="admin-divider anim-up d4">
        <div class="ad-icon" style="background: var(--primary);"><i class="fas fa-calendar"></i></div>
        <h4>Activity Calendar</h4>
        <div class="ad-line"></div>
    </div>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 1rem; margin-bottom: 2rem;" class="anim-up d4 cal-layout">
        <!-- Calendar (Left) -->
        <div style="background: var(--white); border-radius: 10px; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->copy()->subMonth()->format('Y-m')])) }}" style="text-decoration: none; color: var(--gray-400); width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.15s;" onmouseover="this.style.background='var(--muted)'" onmouseout="this.style.background='transparent'"><i class="fas fa-chevron-left" style="font-size: 0.7rem;"></i></a>
                <h4 style="font-weight: 800; font-size: 0.9rem; margin: 0;">{{ $calendarMonth->format('F Y') }}</h4>
                <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->copy()->addMonth()->format('Y-m')])) }}" style="text-decoration: none; color: var(--gray-400); width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.15s;" onmouseover="this.style.background='var(--muted)'" onmouseout="this.style.background='transparent'"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></a>
            </div>
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px;">
                @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $dayName)
                <div style="text-align: center; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; color: var(--gray-300); padding: 0.375rem 0;">{{ $dayName }}</div>
                @endforeach
                @php
                    $firstDay = $calendarMonth->copy()->startOfMonth();
                    $startOffset = $firstDay->dayOfWeek;
                    $daysInMonth = $calendarMonth->daysInMonth;
                @endphp
                @for($i = 0; $i < $startOffset; $i++)
                <div></div>
                @endfor
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = $calendarMonth->copy()->day($day)->format('Y-m-d');
                        $hasLogs = in_array($dateStr, $calendarDays);
                        $isToday = $dateStr === now()->format('Y-m-d');
                        $isSelected = $dateStr === $selectedDay;
                    @endphp
                    <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['day' => $dateStr])) }}"
                       style="display: flex; align-items: center; justify-content: center; height: 32px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: all 0.15s; position: relative;
                       @if($isSelected) background: var(--primary); color: white; box-shadow: 0 2px 8px rgba(59,130,246,0.3);
                       @elseif($isToday) background: #DBEAFE; color: #2563EB;
                       @elseif($hasLogs) background: #D1FAE5; color: #059669;
                       @else color: var(--gray-500);
                       @endif">
                        {{ $day }}
                        @if($hasLogs && !$isSelected)
                        <span style="position: absolute; bottom: 2px; width: 4px; height: 4px; border-radius: 50%; background: #059669;"></span>
                        @endif
                    </a>
                @endfor
            </div>
            <div style="display: flex; gap: 0.75rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--muted); font-size: 0.6rem; color: var(--gray-400);">
                <span><span style="display: inline-block; width: 8px; height: 8px; border-radius: 2px; background: #D1FAE5; vertical-align: middle; margin-right: 0.2rem;"></span>Logs</span>
                <span><span style="display: inline-block; width: 8px; height: 8px; border-radius: 2px; background: #DBEAFE; vertical-align: middle; margin-right: 0.2rem;"></span>Today</span>
                <span><span style="display: inline-block; width: 8px; height: 8px; border-radius: 2px; background: var(--primary); vertical-align: middle; margin-right: 0.2rem;"></span>Selected</span>
            </div>
        </div>

        <!-- Day Detail (Right) -->
        <div style="background: var(--white); border-radius: 10px; overflow: hidden;">
            @if($selectedDay)
            <div style="padding: 1rem 1.25rem; border-bottom: 2px solid var(--muted); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 style="font-weight: 800; font-size: 0.9rem; margin: 0;">{{ \Carbon\Carbon::parse($selectedDay)->format('l, F j') }}</h4>
                    <span style="font-size: 0.7rem; color: var(--gray-400); font-weight: 500;">{{ $selectedDayLogs->count() }} member{{ $selectedDayLogs->count() !== 1 ? 's' : '' }} logged</span>
                </div>
                <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->format('Y-m')])) }}" style="text-decoration: none; color: var(--gray-400); font-size: 0.7rem; font-weight: 600;">Clear <i class="fas fa-times" style="font-size: 0.6rem;"></i></a>
            </div>
            @if($selectedDayLogs->count())
            <div style="max-height: 380px; overflow-y: auto;">
                @foreach($selectedDayLogs as $log)
                @php
                    $rbc2 = $roleBadgeColors[$log->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                @endphp
                <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--muted); display: flex; align-items: center; gap: 0.625rem;">
                    <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $log->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;" alt="">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 700; font-size: 0.8rem;">{{ $log->username }}</div>
                        <span style="padding: 0.05rem 0.3rem; border-radius: 2px; font-size: 0.5rem; font-weight: 700; text-transform: uppercase; background: {{ $rbc2['bg'] }}; color: {{ $rbc2['text'] }};">{{ $log->role }}</span>
                    </div>
                    @php
                        $logLabels = \App\Support\TaskLabels::get($log->role);
                    @endphp
                    <div style="display: flex; gap: 0.25rem;">
                        <span title="{{ $logLabels['task_1'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #3B82F6; cursor: help;">{{ $log->task_1 }}</span>
                        <span title="{{ $logLabels['task_2'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #8B5CF6; cursor: help;">{{ $log->task_2 }}</span>
                        <span title="{{ $logLabels['task_3'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #059669; cursor: help;">{{ $log->task_3 }}</span>
                        <span title="{{ $logLabels['task_4'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #F59E0B; cursor: help;">{{ $log->task_4 }}</span>
                        <span title="{{ $logLabels['task_5'] }}" style="min-width: 24px; text-align: center; font-size: 0.75rem; font-weight: 800; color: #EC4899; cursor: help;">{{ $log->task_5 }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 3rem 2rem; color: var(--gray-400); font-weight: 500;">
                <i class="fas fa-clipboard-list" style="font-size: 1.25rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
                No logs on this day.
            </div>
            @endif
            @else
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px; color: var(--gray-300); text-align: center;">
                <i class="fas fa-calendar-day" style="font-size: 2rem; margin-bottom: 0.75rem; opacity: 0.4;"></i>
                <p style="font-weight: 600; font-size: 0.85rem; margin: 0;">Select a date</p>
                <p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-300);">Click any day on the calendar</p>
            </div>
            @endif
        </div>
    </div>

    <style>
    @media (max-width: 900px) {
        .cal-layout { grid-template-columns: 1fr !important; }
    }
    </style>

    <!-- History -->
    <div class="admin-divider anim-up d5">
        <div class="ad-icon" style="background: var(--secondary);"><i class="fas fa-history"></i></div>
        <h4>History — Last 14 Days</h4>
        <div class="ad-line"></div>
    </div>

    <div style="background: var(--white); border-radius: 10px; overflow: hidden; margin-bottom: 2rem;" class="anim-up d5">
        @if($historyDays->count())
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th style="text-align: center;">Members</th>
                    <th style="text-align: center;">{{ $currentLabels['col1'] }}</th>
                    <th style="text-align: center;">{{ $currentLabels['col2'] }}</th>
                    <th style="text-align: center;">{{ $currentLabels['col3'] }}</th>
                    <th style="text-align: center;">{{ $currentLabels['col4'] }}</th>
                    <th style="text-align: center;">{{ $currentLabels['col5'] }}</th>
                    <th style="text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historyDays as $hd)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->query(), ['day' => $hd->date->format('Y-m-d')])) }}'">
                    <td style="font-weight: 700; white-space: nowrap;">{{ $hd->date->format('M d, Y') }}</td>
                    <td class="num">{{ $hd->user_count }}</td>
                    <td class="num">{{ $hd->total_task_1 }}</td>
                    <td class="num">{{ $hd->total_task_2 }}</td>
                    <td class="num">{{ $hd->total_task_3 }}</td>
                    <td class="num">{{ $hd->total_task_4 }}</td>
                    <td class="num">{{ $hd->total_task_5 }}</td>
                    <td class="num" style="font-weight: 800;">{{ $hd->total_task_1 + $hd->total_task_2 + $hd->total_task_3 + $hd->total_task_4 + $hd->total_task_5 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 2.5rem; color: var(--gray-400); font-weight: 500;">
            <i class="fas fa-history" style="font-size: 1.25rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
            No history yet.
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var labels = @json($chartLabels);
    var newSku = @json($chartNewSku);
    var variationSku = @json($chartVariationSku);
    var dataGathering = @json($chartDataGathering);
    var updateListings = @json($chartUpdateListings);
    var otherTasks = @json($chartOtherTasks);

    var prodLabels = @json($prodLabels);
    var prodData = @json($prodData);

    var dailyCtx = document.getElementById('dailyTasksChart');
    if (dailyCtx) {
        new Chart(dailyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [
                    { label: 'New SKU', data: newSku.length ? newSku : [0], backgroundColor: '#3B82F6', borderRadius: 4 },
                    { label: 'Var. SKU', data: variationSku.length ? variationSku : [0], backgroundColor: '#8B5CF6', borderRadius: 4 },
                    { label: 'Data Gather', data: dataGathering.length ? dataGathering : [0], backgroundColor: '#059669', borderRadius: 4 },
                    { label: 'Update', data: updateListings.length ? updateListings : [0], backgroundColor: '#F59E0B', borderRadius: 4 },
                    { label: 'Other', data: otherTasks.length ? otherTasks : [0], backgroundColor: '#EC4899', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10, family: 'Outfit' } } } },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10, family: 'Outfit' } } },
                    y: { stacked: true, beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 10, family: 'Outfit' } } }
                }
            }
        });
    }

    var prodCtx = document.getElementById('productivityChart');
    if (prodCtx) {
        var barColors = ['#3B82F6', '#8B5CF6', '#059669', '#F59E0B', '#EC4899', '#06B6D4', '#F97316', '#6366F1'];
        new Chart(prodCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: prodLabels.length ? prodLabels : ['No Data'],
                datasets: [{ label: 'Tasks', data: prodData.length ? prodData : [0], backgroundColor: barColors.slice(0, prodLabels.length || 1), borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 10, family: 'Outfit' } } },
                    y: { grid: { display: false }, ticks: { font: { size: 10, family: 'Outfit', weight: 600 } } }
                }
            }
        });
    }
});
</script>
<script>
function toggleDropdown() {
    var submenu = document.getElementById('dailyLogsSubmenu');
    var arrow = document.getElementById('dropdownArrow');
    if (submenu.style.display === 'none' || submenu.style.display === '') {
        submenu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        submenu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
<style>
.nav-dropdown .has-submenu { display: flex !important; align-items: center; justify-content: space-between; }
.dropdown-arrow { font-size: 0.65rem; transition: transform 0.2s; margin-left: auto; }
.submenu { list-style: none; padding: 0; margin: 0.25rem 0 0.5rem 1.75rem; }
.submenu li { margin: 0.125rem 0; }
.submenu a { display: block; padding: 0.5rem 0.875rem; color: var(--gray-300); text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.85rem; transition: all 0.15s; }
.submenu a:hover { background: var(--gray-700); color: white; }
.submenu a.active { background: var(--primary); color: white; }
</style>
@endsection
