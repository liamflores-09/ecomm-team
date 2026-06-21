@extends('layouts.app')

@section('title', 'Daily Logs — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    /* === Stat Row === */
    .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
    .stat-card {
        background: var(--white); border-radius: 12px; padding: 1.25rem;
        display: flex; align-items: center; gap: 1rem; transition: all 0.2s;
        border: 2px solid transparent;
    }
    .stat-card:hover { border-color: var(--border-strong); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; flex-shrink: 0; }
    .stat-count { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.75rem; font-weight: 600; color: var(--gray-400); margin-top: 0.125rem; }

    /* === Charts === */
    .charts-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem; }
    .chart-card { background: var(--white); border-radius: 12px; padding: 1.25rem; border: 1px solid var(--border); }
    .chart-card #weeklyChart, .chart-card #productivityChart { width: 100% !important; }
    .chart-title { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .chart-title .ct-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; flex-shrink: 0; }
    .chart-title h4 { font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }

    /* === Unified Table === */
    .table-card { background: var(--white); border-radius: 12px; border: 2px solid var(--border); overflow: hidden; margin-bottom: 1.5rem; }
    .table-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.875rem 1.25rem; border-bottom: 2px solid var(--muted);
    }
    .table-header .th-left { display: flex; align-items: center; gap: 0.5rem; }
    .table-header .th-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; flex-shrink: 0; }
    .table-header h4 { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .table-header .badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        background: var(--muted); border-radius: 4px; font-size: 0.65rem; font-weight: 700; color: var(--gray-500);
    }

    .filter-pills { display: flex; gap: 0.25rem; flex-wrap: wrap; }
    .filter-pill {
        padding: 0.25rem 0.625rem; border-radius: 5px; font-size: 0.7rem; font-weight: 700;
        cursor: pointer; transition: all 0.15s; border: 1.5px solid var(--border);
        background: var(--white); color: var(--gray-400); text-transform: capitalize;
    }
    .filter-pill:hover { border-color: var(--border-strong); color: var(--fg); }
    .filter-pill.active { background: var(--primary); border-color: var(--primary); color: white; }

    .ut { width: 100%; border-collapse: collapse; }
    .ut thead th {
        padding: 0.625rem 1rem; font-size: 0.65rem; font-weight: 700; color: var(--gray-400);
        background: var(--muted); border-bottom: 1px solid var(--border); text-align: left;
        text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap;
    }
    .ut tbody td { padding: 0.625rem 1rem; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
    .ut tbody tr:last-child td { border-bottom: none; }
    .ut tbody tr:hover td { background: #FAFAFA; }
    .ut .num { text-align: center; font-variant-numeric: tabular-nums; font-weight: 700; }

    .user-cell { display: flex; align-items: center; gap: 0.5rem; }
    .user-cell img { width: 28px; height: 28px; border-radius: 50%; border: 1.5px solid var(--muted); flex-shrink: 0; }
    .user-cell .name { font-weight: 600; }

    .role-badge {
        display: inline-block; padding: 0.15rem 0.4rem; border-radius: 4px;
        font-size: 0.55rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .role-badge.manager { background: #171717; color: #ffffff; }
    .role-badge.lead { background: #6366f1; color: #ffffff; }
    .role-badge.content { background: #0ea5e9; color: #ffffff; }
    .role-badge.graphics { background: #f59e0b; color: #ffffff; }
    .role-badge.backend { background: #f43f5e; color: #ffffff; }
    .role-badge.researcher { background: #10b981; color: #ffffff; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700;
    }
    .status-pill.logged { background: #F0FDF4; color: #166534; }
    .status-pill.pending { background: #FEF2F2; color: #991B1B; }

    .cell-muted { color: var(--gray-400); font-size: 0.8rem; }
    .cell-remark { color: var(--gray-400); font-size: 0.8rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .task-pip { display: inline-flex; gap: 3px; }
    .task-pip span {
        min-width: 22px; text-align: center; padding: 2px 4px; background: var(--muted);
        border-radius: 4px; font-size: 0.7rem; font-weight: 700; font-variant-numeric: tabular-nums;
    }

    .empty-state { text-align: center; padding: 3rem; color: var(--gray-300); font-size: 0.85rem; }
    .empty-state i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200); }

    /* === Calendar === */
    .cal-layout { display: grid; grid-template-columns: 280px 1fr; gap: 0.75rem; margin-bottom: 1.5rem; }
    .cal-card { background: var(--white); border-radius: 12px; border: 2px solid var(--border); padding: 1rem; }
    .cal-nav { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .cal-nav span { font-weight: 700; font-size: 0.85rem; }
    .cal-nav button {
        width: 28px; height: 28px; border: 1.5px solid var(--border); border-radius: 6px;
        background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--gray-400); font-size: 0.65rem; transition: all 0.1s;
    }
    .cal-nav button:hover { border-color: var(--border-strong); color: var(--fg); }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
    .cal-day-label { text-align: center; font-size: 0.6rem; font-weight: 700; color: var(--gray-300); padding: 2px 0; }
    .cal-day {
        display: flex; align-items: center; justify-content: center; height: 32px;
        border-radius: 6px; font-size: 0.75rem; font-weight: 500; color: var(--gray-400);
        text-decoration: none; transition: all 0.1s; position: relative;
    }
    .cal-day:hover { background: var(--hover); }
    .cal-day.today { background: var(--fg); color: white; }
    .cal-day.has-logs { color: var(--fg); font-weight: 700; }
    .cal-day.selected { background: var(--fg); color: white; }
    .cal-day .dot { position: absolute; bottom: 2px; width: 4px; height: 4px; border-radius: 50%; background: var(--fg); }

    .cal-legend { display: flex; gap: 0.75rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border); font-size: 0.6rem; color: var(--gray-300); }
    .cal-legend span { display: flex; align-items: center; gap: 3px; }
    .cal-legend .dot { width: 6px; height: 6px; border-radius: 2px; }

    .day-panel { background: var(--white); border-radius: 12px; border: 2px solid var(--border); overflow: hidden; }
    .day-panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.75rem 1rem; border-bottom: 2px solid var(--muted);
    }
    .day-panel-header h4 { font-size: 0.8rem; font-weight: 800; margin: 0; }
    .day-panel-header a { font-size: 0.75rem; color: var(--primary); text-decoration: none; font-weight: 600; }
    .day-panel-header a:hover { text-decoration: underline; }
    .day-item {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.625rem 1rem; border-bottom: 1px solid var(--border);
    }
    .day-item:last-child { border-bottom: none; }
    .day-item:hover { background: #FAFAFA; }

    @media (max-width: 1024px) {
        .charts-row { grid-template-columns: 1fr; }
        .cal-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
        .stat-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.daily-logs" :isAdmin="true" />

<div class="main-content">
    <div class="anim-up" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem;">Daily Logs</h2>
            <p style="color: var(--gray-400); font-size: 0.9rem; font-weight: 500; margin: 0;">View and track team daily activity</p>
        </div>
    </div>

    @php
        $roleNames = ['content' => 'Content', 'lead' => 'Lead', 'researcher' => 'Researcher', 'graphics' => 'Graphics', 'backend' => 'Backend'];
    @endphp

    <!-- KPIs -->
    <div class="stat-row anim-up d1">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-clipboard-list"></i></div>
            <div><div class="stat-count">{{ $totalLogs }}</div><div class="stat-label">Total Logs</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--gray-500);"><i class="fas fa-chart-line"></i></div>
            <div><div class="stat-count">{{ $thisMonthLogs }}</div><div class="stat-label">This Month</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-calendar-check"></i></div>
            <div><div class="stat-count">{{ $todayLogCount }}</div><div class="stat-label">Logged Today</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: {{ $missingLogs->count() ? '#991B1B' : 'var(--primary)' }};"><i class="fas fa-user-clock"></i></div>
            <div><div class="stat-count" style="color: {{ $missingLogs->count() ? '#991B1B' : 'var(--fg)' }};">{{ $missingLogs->count() }}</div><div class="stat-label">Missing</div></div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-row anim-up d2">
        <div class="chart-card">
            <div class="chart-title">
                <div class="ct-icon" style="background: var(--primary);"><i class="fas fa-chart-bar"></i></div>
                <h4>Weekly Task Breakdown</h4>
            </div>
            <div id="weeklyChart"></div>
        </div>
        <div class="chart-card">
            <div class="chart-title">
                <div class="ct-icon" style="background: var(--gray-500);"><i class="fas fa-trophy"></i></div>
                <h4>Top Contributors</h4>
            </div>
            <div id="productivityChart"></div>
        </div>
    </div>

    <!-- Today's Logs — Unified Table -->
    <div class="table-card anim-up d3">
        <div class="table-header">
            <div class="th-left">
                <div class="th-icon" style="background: var(--primary);"><i class="fas fa-clipboard-check"></i></div>
                <h4>Today's Logs</h4>
                <span class="badge">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="filter-pills" id="todayFilter">
                <button class="filter-pill active" onclick="filterToday('all', this)">All</button>
                @foreach($rolesWithData as $r)
                <button class="filter-pill" onclick="filterToday('{{ $r }}', this)">{{ $roleNames[$r] ?? ucfirst($r) }}</button>
                @endforeach
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="ut" id="todayTable">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Role</th>
                        <th class="th-pip" style="text-align: center;">Tasks</th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_1"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_2"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_3"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_4"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_5"></th>
                        <th style="text-align: center;">Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayLogs as $log)
                    @php $logLabels = \App\Support\TaskLabels::get($log->role); @endphp
                    <tr data-role="{{ $log->role }}">
                        <td>
                            <div class="user-cell">
                                <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($log->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $log->username . 'Female' : $log->username }}" alt="">
                                <span class="name">{{ $log->first_name ?? $log->username }}</span>
                            </div>
                        </td>
                        <td><span class="role-badge {{ $log->role }}">{{ $log->role }}</span></td>
                        <td class="td-pips" style="text-align: center;">
                            <div class="task-pip">
                                @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                                <span title="{{ $logLabels[$tk] }}">{{ $log->$tk }}</span>
                                @endforeach
                            </div>
                        </td>
                        @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                        <td class="td-cols num" style="display: none;" data-label="{{ $logLabels[$tk] }}">{{ $log->$tk ?: '—' }}</td>
                        @endforeach
                        <td style="text-align: center;">@if($log->has_logged)<span class="status-pill logged">Logged</span>@else<span class="status-pill pending">Pending</span>@endif</td>
                        <td class="cell-remark">{{ $log->remarks ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty-state"><i class="fas fa-clipboard-list"></i>No logs today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Calendar + Day Detail -->
    <div class="cal-layout anim-up d4">
        <div class="cal-card">
            <div class="cal-nav">
                <button onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->copy()->subMonth()->format('Y-m')])) }}'"><i class="fas fa-chevron-left"></i></button>
                <span>{{ $calendarMonth->format('F Y') }}</span>
                <button onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->copy()->addMonth()->format('Y-m')])) }}'"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="cal-grid">
                @foreach(['S','M','T','W','T','F','S'] as $d)
                <div class="cal-day-label">{{ $d }}</div>
                @endforeach
                @php $firstDay = $calendarMonth->copy()->startOfMonth(); $startOffset = $firstDay->dayOfWeek; $daysInMonth = $calendarMonth->daysInMonth; @endphp
                @for($i = 0; $i < $startOffset; $i++) <div></div> @endfor
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php $dateStr = $calendarMonth->copy()->day($day)->format('Y-m-d'); $hasLogs = in_array($dateStr, $calendarDays); $isToday = $dateStr === now()->format('Y-m-d'); $isSelected = $dateStr === $selectedDay; @endphp
                    <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['day' => $dateStr])) }}" class="cal-day @if($isToday) today @endif @if($hasLogs) has-logs @endif @if($isSelected) selected @endif">
                        {{ $day }}
                        @if($hasLogs && !$isSelected) <span class="dot"></span> @endif
                    </a>
                @endfor
            </div>
            <div class="cal-legend">
                <span><span class="dot" style="background: var(--fg);"></span> Today</span>
                <span><span class="dot" style="background: var(--gray-300);"></span> Has logs</span>
            </div>
        </div>

        <div class="day-panel">
            @if($selectedDay)
            <div class="day-panel-header">
                <h4>{{ \Carbon\Carbon::parse($selectedDay)->format('l, F j') }} <span style="font-weight: 400; color: var(--gray-400);">— {{ $selectedDayLogs->count() }}</span></h4>
                <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->format('Y-m')])) }}">Clear</a>
            </div>
            @if($selectedDayLogs->count())
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($selectedDayLogs as $log)
                @php $logLabels = \App\Support\TaskLabels::get($log->role); @endphp
                <div class="day-item">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($log->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $log->username . 'Female' : $log->username }}" style="width: 28px; height: 28px; border-radius: 50%; border: 1.5px solid var(--muted);" alt="">
                    <div style="flex: 1; min-width: 0;">
                        <span style="font-weight: 600; font-size: 0.8rem;">{{ $log->first_name ?? $log->username }}</span>
                        <span class="role-badge {{ $log->role }}" style="margin-left: 4px;">{{ $log->role }}</span>
                    </div>
                    <div class="task-pip">
                        @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                        <span title="{{ $logLabels[$tk] }}">{{ $log->$tk }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">No logs on this day</div>
            @endif
            @else
            <div class="empty-state" style="min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-day" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
                Select a date
            </div>
            @endif
        </div>
    </div>

    <!-- History — Unified Table -->
    <div class="table-card anim-up d5">
        <div class="table-header">
            <div class="th-left">
                <div class="th-icon" style="background: var(--gray-500);"><i class="fas fa-clock-rotate-left"></i></div>
                <h4>History</h4>
                <span class="badge">Last 14 Days</span>
            </div>
            <div class="filter-pills" id="historyFilter">
                <button class="filter-pill active" onclick="filterHistory('all', this)">All</button>
                @foreach($rolesWithData as $r)
                <button class="filter-pill" onclick="filterHistory('{{ $r }}', this)">{{ $roleNames[$r] ?? ucfirst($r) }}</button>
                @endforeach
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="ut" id="historyTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Role</th>
                        <th style="text-align: center;">Members</th>
                        <th class="th-pip" style="text-align: center;">Tasks</th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_1"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_2"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_3"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_4"></th>
                        <th class="th-col" style="text-align: center; display: none;" data-col="task_5"></th>
                        <th style="text-align: center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyDays as $hd)
                    @php $hLabels = \App\Support\TaskLabels::get($hd->role); @endphp
                    <tr data-role="{{ $hd->role }}" onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->query(), ['day' => $hd->date->format('Y-m-d')])) }}'" style="cursor: pointer;">
                        <td style="font-weight: 600;">{{ $hd->date->format('M d, Y') }}</td>
                        <td><span class="role-badge {{ $hd->role }}">{{ $roleNames[$hd->role] ?? ucfirst($hd->role) }}</span></td>
                        <td class="num">{{ $hd->user_count }}</td>
                        <td class="td-pips" style="text-align: center;">
                            <div class="task-pip">
                                @foreach(['total_task_1','total_task_2','total_task_3','total_task_4','total_task_5'] as $i => $tk)
                                <span title="{{ $hLabels['task_' . ($i+1)] }}">{{ $hd->$tk ?: '—' }}</span>
                                @endforeach
                            </div>
                        </td>
                        @foreach(['total_task_1','total_task_2','total_task_3','total_task_4','total_task_5'] as $i => $tk)
                        <td class="td-cols num" style="display: none;" data-label="{{ $hLabels['task_' . ($i+1)] }}">{{ $hd->$tk ?: '—' }}</td>
                        @endforeach
                        <td class="num" style="font-weight: 800;">{{ $hd->total_task_1 + $hd->total_task_2 + $hd->total_task_3 + $hd->total_task_4 + $hd->total_task_5 }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state"><i class="fas fa-clock-rotate-left"></i>No history</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// === Role task labels for column headers ===
var roleLabels = {};
@foreach($rolesWithData as $r)
    @php $rL = \App\Support\TaskLabels::get($r); @endphp
    roleLabels['{{ $r }}'] = ['{{ $rL['task_1'] }}', '{{ $rL['task_2'] }}', '{{ $rL['task_3'] }}', '{{ $rL['task_4'] }}', '{{ $rL['task_5'] }}'];
@endforeach

// === Filter: Today's Logs ===
function filterToday(role, btn) {
    btn.closest('.filter-pills').querySelectorAll('.filter-pill').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var thead = document.querySelector('#todayTable thead tr');
    var thPip = thead.querySelector('.th-pip');
    var thCols = thead.querySelectorAll('.th-col');
    var allPips = document.querySelectorAll('#todayTable .td-pips');
    var allCols = document.querySelectorAll('#todayTable .td-cols');

    if (role === 'all') {
        thPip.style.display = '';
        thCols.forEach(function(el) { el.style.display = 'none'; el.textContent = ''; });
        allPips.forEach(function(el) { el.style.display = ''; });
        allCols.forEach(function(el) { el.style.display = 'none'; });
    } else {
        var labels = roleLabels[role] || [];
        thPip.style.display = 'none';
        thCols.forEach(function(el, i) { el.style.display = ''; el.textContent = labels[i] || ''; });
        allPips.forEach(function(el) { el.style.display = 'none'; });
        allCols.forEach(function(el) { el.style.display = ''; });
    }

    document.querySelectorAll('#todayTable tbody tr[data-role]').forEach(function(row) {
        row.style.display = (role === 'all' || row.getAttribute('data-role') === role) ? '' : 'none';
    });
}

// === Filter: History ===
function filterHistory(role, btn) {
    btn.closest('.filter-pills').querySelectorAll('.filter-pill').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var thead = document.querySelector('#historyTable thead tr');
    var thPip = thead.querySelector('.th-pip');
    var thCols = thead.querySelectorAll('.th-col');
    var allPips = document.querySelectorAll('#historyTable .td-pips');
    var allCols = document.querySelectorAll('#historyTable .td-cols');

    if (role === 'all') {
        thPip.style.display = '';
        thCols.forEach(function(el) { el.style.display = 'none'; el.textContent = ''; });
        allPips.forEach(function(el) { el.style.display = ''; });
        allCols.forEach(function(el) { el.style.display = 'none'; });
    } else {
        var labels = roleLabels[role] || [];
        thPip.style.display = 'none';
        thCols.forEach(function(el, i) { el.style.display = ''; el.textContent = labels[i] || ''; });
        allPips.forEach(function(el) { el.style.display = 'none'; });
        allCols.forEach(function(el) { el.style.display = ''; });
    }

    document.querySelectorAll('#historyTable tbody tr[data-role]').forEach(function(row) {
        row.style.display = (role === 'all' || row.getAttribute('data-role') === role) ? '' : 'none';
    });
}

// === Charts ===
document.addEventListener('DOMContentLoaded', function() {
    var weeklyEl = document.getElementById('weeklyChart');
    if (weeklyEl) {
        new ApexCharts(weeklyEl, {
            chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Inter', stacked: true, foreColor: '#64748b' },
            series: [
                { name: 'New SKU', data: {!! json_encode($chartNewSku) !!} },
                { name: 'Var. SKU', data: {!! json_encode($chartVariationSku) !!} },
                { name: 'Data Gather', data: {!! json_encode($chartDataGathering) !!} },
                { name: 'Update', data: {!! json_encode($chartUpdateListings) !!} },
                { name: 'Other', data: {!! json_encode($chartOtherTasks) !!} }
            ],
            colors: ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'],
            plotOptions: { bar: { columnWidth: '55%', borderRadius: { topLeft: 4, topRight: 4 } } },
            xaxis: {
                categories: {!! json_encode($chartLabels) !!},
                labels: { style: { fontWeight: 600, fontSize: '11px', colors: '#94a3b8' } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { fontWeight: 500, fontSize: '11px', colors: '#94a3b8' }, padding: 4 },
                tickAmount: 4
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
            tooltip: {
                theme: 'light',
                style: { fontSize: '13px', fontFamily: 'Inter' },
                y: { formatter: function(val) { return val + ' tasks'; } }
            },
            legend: {
                position: 'bottom',
                labels: { colors: '#64748b', useSeriesColors: true, fontWeight: 600, fontSize: '11px' },
                markers: { width: 10, height: 10, radius: 3, strokeWidth: 0 },
                itemMargin: { horizontal: 8 }
            }
        }).render();
    }

    var prodEl = document.getElementById('productivityChart');
    if (prodEl) {
        var prodData = {!! json_encode($prodData) !!};
        var prodColors = prodData.map(function(v, i) {
            var palette = ['#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe', '#ddd6fe', '#e0e7ff', '#4f46e5', '#4338ca'];
            return palette[i % palette.length];
        });
        new ApexCharts(prodEl, {
            chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#64748b' },
            series: [{ name: 'Tasks', data: prodData }],
            colors: prodColors,
            plotOptions: {
                bar: { borderRadius: 8, columnWidth: '55%', distributed: true }
            },
            xaxis: {
                categories: {!! json_encode($prodLabels) !!},
                labels: { style: { fontWeight: 600, fontSize: '11px', colors: '#94a3b8' } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { fontWeight: 500, fontSize: '11px', colors: '#94a3b8' }, padding: 4 },
                tickAmount: 4
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
            tooltip: {
                theme: 'light',
                style: { fontSize: '13px', fontFamily: 'Inter' },
                marker: { show: false },
                y: { formatter: function(val) { return val + ' tasks'; } }
            },
            legend: { show: false }
        }).render();
    }
});
</script>
@endsection
