@extends('layouts.app')

@section('title', 'Daily Logs — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z'/><polyline points='14 2 14 8 20 8'/><line x1='16' y1='13' x2='8' y2='13'/><line x1='16' y1='17' x2='8' y2='17'/></svg>">
@endsection

@section('styles')
<style>
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.78rem; font-weight: 700; color: var(--muted-foreground);
        text-decoration: none; margin-bottom: 0.5rem; transition: color 0.15s;
    }
    .back-link:hover { color: var(--foreground); }

    /* KPIs */
    .dl-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .dl-kpi-card {
        background: var(--card); border-radius: 8px; padding: 1.25rem 1.5rem;
        border: 1px solid var(--border); display: flex; align-items: center; gap: 1rem;
        transition: border-color 0.2s;
    }
    .dl-kpi-card:hover { border-color: var(--border-strong); }
    .dl-kpi-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem; flex-shrink: 0; }
    .dl-kpi-val  { font-size: 1.6rem; font-weight: 800; line-height: 1; }
    .dl-kpi-lbl  { font-size: 0.73rem; font-weight: 600; color: var(--gray-400); margin-top: 3px; }

    /* Section header */
    .dl-section { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 0.875rem; }
    .dl-section h4 { font-size: 0.9rem; font-weight: 700; margin: 0 0 2px; }
    .dl-section p  { font-size: 0.75rem; color: var(--muted-foreground); margin: 0; }
    .dl-section a  { font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; white-space: nowrap; }

    /* Role overview grid (shared with dashboard) */
    .dl-role-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.1rem; margin-bottom: 1.75rem; }
    .dl-role-grid--single { grid-template-columns: 1fr; }
    .dl-role-card {
        background: var(--card); border-radius: 8px; border: 1px solid var(--border);
        padding: 1.1rem 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;
        transition: border-color 0.2s;
    }
    .dl-role-grid--single .dl-role-card { padding: 1.5rem 1.75rem; gap: 1rem; }
    .dl-role-grid--single .contrib-list { gap: 0.85rem; }
    .dl-role-grid--single .contrib-avatar { width: 30px; height: 30px; }
    .dl-role-grid--single .contrib-name { font-size: 0.85rem; }
    .dl-role-grid--single .contrib-bar-wrap { height: 7px; }
    .dl-role-grid--single .contrib-total { font-size: 0.8rem; min-width: 34px; }
    .dl-role-card:hover { border-color: var(--border-strong); }
    .dl-role-header { display: flex; align-items: center; justify-content: space-between; }
    .dl-role-sub    { font-size: 0.7rem; color: var(--muted-foreground); font-weight: 500; margin: 0; line-height: 1.4; }
    .dl-role-link   { font-size: 0.75rem; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 4px; transition: gap 0.15s; }
    .dl-role-link:hover { gap: 7px; }

    /* Contributor list */
    .contrib-list { display: flex; flex-direction: column; gap: 0.5rem; }
    .contrib-item { display: flex; align-items: center; gap: 0.5rem; }
    .contrib-rank { font-size: 0.65rem; font-weight: 800; color: var(--gray-300); width: 14px; flex-shrink: 0; }
    .contrib-avatar { width: 22px; height: 22px; border-radius: 50%; border: 1px solid var(--border); flex-shrink: 0; }
    .contrib-name { font-size: 0.78rem; font-weight: 600; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }
    .contrib-bar-wrap { flex: 1.5; height: 5px; background: var(--muted); border-radius: 3px; overflow: hidden; }
    .contrib-bar { height: 100%; border-radius: 3px; transition: width 0.6s ease; }
    .contrib-total { font-size: 0.73rem; font-weight: 800; color: var(--gray-500); min-width: 28px; text-align: right; }

    /* Table cards */
    .table-card { background: var(--card); border-radius: 8px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 1.75rem; }
    .table-header {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;
        padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .th-left { display: flex; align-items: center; gap: 0.5rem; }
    .th-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.7rem; }
    .th-title { font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .th-badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        background: var(--muted); border-radius: 4px; font-size: 0.65rem; font-weight: 700; color: var(--gray-500);
    }

    .ut { width: 100%; border-collapse: collapse; }
    .ut thead th {
        padding: 0.6rem 1rem; font-size: 0.65rem; font-weight: 700; color: var(--gray-400);
        background: var(--muted); border-bottom: 1px solid var(--border);
        text-align: left; text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap;
    }
    .ut tbody td { padding: 0.6rem 1rem; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
    .ut tbody tr:last-child td { border-bottom: none; }
    .ut tbody tr:hover td { background: var(--secondary); }
    .ut .num { text-align: center; font-variant-numeric: tabular-nums; font-weight: 700; }

    /* Role subheader row in table */
    .role-divider-row td {
        padding: 0.45rem 1rem; background: var(--muted);
        border-bottom: 1px solid var(--border); border-top: 2px solid var(--border);
    }
    .role-divider-row:first-child td { border-top: none; }
    .role-divider-row td span.rd-count { font-size: 0.72rem; font-weight: 600; color: var(--gray-400); margin-left: 6px; }

    .status-pill { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 0.65rem; font-weight: 700; }
    .status-pill.logged  { background: #f0fdf4; color: #166534; }
    .status-pill.pending { background: #fef2f2; color: #991b1b; }
    .status-pill.rdo     { background: #fef9ec; color: #92400e; }

    .task-pip { display: inline-flex; gap: 3px; }
    .task-pip span { min-width: 22px; text-align: center; padding: 2px 4px; background: var(--muted); border-radius: 4px; font-size: 0.7rem; font-weight: 700; font-variant-numeric: tabular-nums; }

    /* Calendar */
    .cal-layout { display: grid; grid-template-columns: 290px 1fr; gap: 1rem; margin-bottom: 1.75rem; }
    .cal-card { background: var(--card); border-radius: 8px; border: 1px solid var(--border); padding: 1rem; }
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
    .cal-day-label.sun { color: #ef4444; }
    .cal-day {
        display: flex; align-items: center; justify-content: center; height: 32px;
        border-radius: 6px; font-size: 0.75rem; font-weight: 500; color: var(--gray-400);
        cursor: pointer; transition: all 0.1s; position: relative; border: none; background: transparent;
        font-family: inherit; width: 100%;
    }
    .cal-day:hover { background: var(--secondary); }
    .cal-day.today { background: #dbeafe; color: #1e40af; font-weight: 700; border: 2px solid #3b82f6; }
    .cal-day.has-logs { color: var(--fg); font-weight: 700; }
    .cal-day.selected { background: var(--primary); color: white; border-color: transparent; }
    .cal-day.is-rdo   { color: #ef4444; font-weight: 600; }
    .cal-day.is-rdo.today { background: #dbeafe; color: #1e40af; border: 2px solid #3b82f6; }
    .cal-day.is-rdo.selected { background: var(--primary); color: white; border-color: transparent; }
    .cal-day .dot { position: absolute; bottom: 2px; width: 4px; height: 4px; border-radius: 50%; background: var(--fg); }
    .cal-day.selected .dot { background: rgba(255,255,255,0.6); }
    .cal-day.today .dot { background: #1e40af; }
    .cal-legend { display: flex; gap: 0.75rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border); font-size: 0.6rem; color: var(--gray-400); flex-wrap: wrap; }
    .cal-legend span { display: flex; align-items: center; gap: 3px; }
    .cal-legend .cl-dot { width: 6px; height: 6px; border-radius: 2px; }
    .cal-rdo-label { font-size: 0.55rem; font-weight: 700; color: #ef4444; position: absolute; bottom: 1px; line-height: 1; }

    .day-panel { background: var(--card); border-radius: 8px; border: 1px solid var(--border); overflow: hidden; display: flex; flex-direction: column; }
    .day-panel-header { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .day-panel-header h4 { font-size: 0.82rem; font-weight: 800; margin: 0; }
    .day-panel-body { flex: 1; overflow-y: auto; max-height: 380px; }
    .day-role-header { display: flex; align-items: center; gap: 6px; padding: 0.4rem 1rem; background: var(--muted); border-bottom: 1px solid var(--border); position: sticky; top: 0; }
    .day-item { display: flex; align-items: center; gap: 0.625rem; padding: 0.6rem 1rem; border-bottom: 1px solid var(--border); }
    .day-item:last-child { border-bottom: none; }
    .day-item:hover { background: var(--secondary); }

    @media (max-width: 1024px) {
        .dl-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .dl-role-grid { grid-template-columns: repeat(2, 1fr); }
        .cal-layout   { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .dl-kpi-grid { grid-template-columns: 1fr; }
        .dl-role-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.daily-logs" :isAdmin="true" />

<div class="main-content">
    @php
        $roleColors = ['content'=>'var(--sky)','graphics'=>'var(--amber)','backend'=>'var(--rose)','researcher'=>'var(--emerald)'];
        $roleNames  = ['content'=>'Content','graphics'=>'Graphics','backend'=>'Backend','researcher'=>'Researcher'];
        $todayStr   = now()->format('Y-m-d');
        $todayFmt   = now()->format('l, M d, Y');
    @endphp

    <!-- Header -->
    <div class="anim-up" style="margin-bottom: 1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
            <div>
                <h2 style="font-size:1.5rem;font-weight:800;margin:0 0 0.25rem;">Daily Logs</h2>
                <p style="color:var(--gray-400);font-size:0.9rem;font-weight:500;margin:0;">{{ $todayFmt }}</p>
            </div>
            @if($isSunday)
            <div style="display:flex;align-items:center;gap:0.625rem;padding:0.6rem 1rem;background:#fef9ec;border:1px solid #fde68a;border-radius:8px;color:#92400e;font-size:0.82rem;font-weight:600;">
                <i class="fas fa-umbrella-beach"></i> Today is Sunday — Rest Day (RDO)
            </div>
            @endif
        </div>
    </div>

    <!-- KPIs -->
    <div class="dl-kpi-grid anim-up d1">
        <div class="dl-kpi-card">
            <div class="dl-kpi-icon" style="background:var(--primary);"><i class="fas fa-clipboard-list"></i></div>
            <div><div class="dl-kpi-val">{{ $totalLogs }}</div><div class="dl-kpi-lbl">Total Logs</div></div>
        </div>
        <div class="dl-kpi-card">
            <div class="dl-kpi-icon" style="background:var(--indigo);"><i class="fas fa-chart-line"></i></div>
            <div><div class="dl-kpi-val">{{ $thisMonthLogs }}</div><div class="dl-kpi-lbl">This Month</div></div>
        </div>
        <div class="dl-kpi-card">
            <div class="dl-kpi-icon" style="background:{{ $isSunday ? '#d97706' : 'var(--emerald)' }};"><i class="fas {{ $isSunday ? 'fa-umbrella-beach' : 'fa-calendar-check' }}"></i></div>
            <div>
                @if($isSunday)
                <div class="dl-kpi-val" style="font-size:1.1rem;">Rest Day</div>
                <div class="dl-kpi-lbl">No log expected today</div>
                @else
                <div class="dl-kpi-val">{{ $todayLogCount }}</div>
                <div class="dl-kpi-lbl">Logged Today</div>
                @endif
            </div>
        </div>
        <div class="dl-kpi-card">
            <div class="dl-kpi-icon" style="background:{{ ($isSunday || $missingLogs->isEmpty()) ? 'var(--gray-400)' : 'var(--rose)' }};"><i class="fas fa-user-clock"></i></div>
            <div>
                @if($isSunday)
                <div class="dl-kpi-val" style="font-size:1.1rem;color:#d97706;">RDO</div>
                <div class="dl-kpi-lbl">Sunday — Rest Day</div>
                @else
                <div class="dl-kpi-val" style="color:{{ $missingLogs->isNotEmpty() ? 'var(--rose)' : 'var(--fg)' }};">{{ $missingLogs->count() }}</div>
                <div class="dl-kpi-lbl">Missing Today</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Weekly Activity Overview -->
    <div class="dl-section anim-up d2">
        <div>
            <h4>Weekly Activity — Last 7 Days</h4>
            <p>Daily task output per role. <span style="color:var(--rose);font-weight:600;">Sunday = RDO</span> — no output expected.</p>
        </div>
    </div>
    <div class="dl-role-grid{{ $roleFilter ? ' dl-role-grid--single' : '' }} anim-up d2">
        @foreach($dlRoleBreakdown as $r)
        @php $color = $roleColors[$r['role']] ?? 'var(--gray-400)'; @endphp
        <div class="dl-role-card" style="border-top:3px solid {{ $color }};">
            <div class="dl-role-header">
                <span class="role-badge {{ $r['role'] }}">{{ ucfirst($r['role']) }}</span>
                <span style="font-size:0.75rem;font-weight:600;color:var(--muted-foreground);">{{ $r['members'] }} {{ $r['members']===1?'member':'members' }}</span>
            </div>
            <p class="dl-role-sub">Daily task output (all fields) &mdash; last 7 days</p>
            <div id="dlChart-{{ $r['role'] }}" style="height:{{ $roleFilter ? '180px' : '90px' }};margin:0 -0.25rem;"></div>
            <a href="{{ route('admin.reports') }}?role={{ $r['role'] }}" class="dl-role-link">
                View Reports <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Top Contributors -->
    <div class="dl-section anim-up d3">
        <div>
            <h4>Top Contributors — Last 7 Days</h4>
            <p>Highest task output per role. Click View Reports for full breakdowns.</p>
        </div>
    </div>
    <div class="dl-role-grid{{ $roleFilter ? ' dl-role-grid--single' : '' }} anim-up d3">
        @foreach($dlRoleTopContributors->sortKeys() as $role => $contribs)
        @php
            $color  = $roleColors[$role] ?? 'var(--gray-400)';
            $maxVal = $contribs->max('total') ?: 1;
        @endphp
        <div class="dl-role-card" style="border-top:3px solid {{ $color }};">
            <div class="dl-role-header">
                <span class="role-badge {{ $role }}">{{ ucfirst($role) }}</span>
                <span style="font-size:0.7rem;font-weight:600;color:var(--muted-foreground);">Top {{ $contribs->count() }}</span>
            </div>
            <div class="contrib-list">
                @foreach($contribs as $i => $c)
                <div class="contrib-item">
                    <span class="contrib-rank">#{{ $i+1 }}</span>
                    <img class="contrib-avatar" src="{{ \App\Models\User::resolveAvatarUrl($c->avatar, $c->first_name, $c->last_name, $c->username) }}" alt="" style="object-fit:cover;">
                    <div style="flex:1;min-width:0;">
                        <span class="contrib-name">{{ $c->first_name }}</span>
                        @if($c->badge)<span style="display:block;font-size:0.55rem;font-weight:700;color:#0369a1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $c->badge }}</span>@endif
                    </div>
                    <div class="contrib-bar-wrap">
                        <div class="contrib-bar" style="width:{{ round(($c->total/$maxVal)*100) }}%;background:{{ $color }};"></div>
                    </div>
                    <span class="contrib-total">{{ $c->total }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('admin.reports') }}?role={{ $role }}" class="dl-role-link">
                View Reports <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Today's Logs -->
    <div class="table-card anim-up d4">
        <div class="table-header">
            <div class="th-left">
                <div class="th-icon" style="background:var(--primary);"><i class="fas fa-clipboard-check"></i></div>
                <h4 class="th-title">Today's Logs</h4>
                <span class="th-badge">{{ now()->format('M d, Y') }}</span>
                @if($isSunday)<span class="th-badge" style="background:#fef3c7;color:#d97706;margin-left:4px;"><i class="fas fa-moon" style="margin-right:3px;"></i>RDO</span>@endif
            </div>
            @if(!$isSunday && !$roleFilter)
            <div class="filter-pills" id="todayFilter">
                <button class="filter-pill active" onclick="filterTable('todayTable','todayFilter','all',this)">All</button>
                @foreach($todayLogsByRole->sortKeys()->keys() as $r)
                <button class="filter-pill" onclick="filterTable('todayTable','todayFilter','{{ $r }}',this)">{{ $roleNames[$r] ?? ucfirst($r) }}</button>
                @endforeach
            </div>
            @endif
        </div>

        @if($isSunday)
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.75rem;padding:2rem;text-align:center;">
            <i class="fas fa-umbrella-beach" style="font-size:1.75rem;color:#d97706;"></i>
            <div>
                <p style="font-weight:700;margin:0 0 4px;font-size:0.95rem;">Rest Day (RDO)</p>
                <p style="color:var(--muted-foreground);font-size:0.8rem;margin:0;">No EOD submissions expected on Sundays.</p>
            </div>
        </div>
        @else
        <div style="overflow-x:auto;">
            <table class="ut" id="todayTable">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Role</th>
                        <th style="text-align:center;">Tasks</th>
                        <th style="text-align:center;">Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php $lastRole = null; @endphp
                    @forelse($todayLogs as $log)
                    @php $logLabels = \App\Support\TaskLabels::get($log->role); @endphp
                    @if(!$roleFilter && $lastRole !== $log->role)
                    <tr class="role-divider-row" data-role-header="{{ $log->role }}">
                        <td colspan="5">
                            <span class="role-badge {{ $log->role }}">{{ $roleNames[$log->role] ?? ucfirst($log->role) }}</span>
                            <span class="rd-count">{{ $todayLogsByRole[$log->role]->count() }} members</span>
                        </td>
                    </tr>
                    @endif
                    @php $lastRole = $log->role; @endphp
                    <tr data-role="{{ $log->role }}">
                        <td>
                            <div class="user-cell">
                                <img src="{{ \App\Models\User::resolveAvatarUrl($log->avatar ?? null, $log->first_name ?? '', $log->last_name ?? '', $log->username ?? '') }}" alt="" style="object-fit:cover;">
                                <div>
                                    <span class="name">{{ $log->first_name ?? $log->username }}</span>
                                    @if($log->badge)<span style="display:block;font-size:0.6rem;font-weight:700;color:#0369a1;margin-top:1px;">{{ $log->badge }}</span>@endif
                                </div>
                            </div>
                        </td>
                        <td><span class="role-badge {{ $log->role }}">{{ $log->role }}</span></td>
                        <td style="text-align:center;">
                            <div class="task-pip">
                                @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                                <span title="{{ $logLabels[$tk] }}">{{ $log->$tk }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @if($log->has_logged)<span class="status-pill logged"><i class="fas fa-check" style="font-size:0.55rem;"></i> Logged</span>
                            @else<span class="status-pill pending"><i class="fas fa-clock" style="font-size:0.55rem;"></i> Pending</span>@endif
                        </td>
                        <td style="color:var(--gray-400);font-size:0.8rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->remarks ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state">No logs today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Calendar + Day Panel -->
    <div class="cal-layout anim-up d5">
        @php
            $firstDay     = $calendarMonth->copy()->startOfMonth();
            $startOffset  = $firstDay->dayOfWeek;
            $daysInMonth  = $calendarMonth->daysInMonth;
            $calMonthStr  = $calendarMonth->format('Y-m');
        @endphp
        <div class="cal-card">
            <div class="cal-nav">
                <button onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->except('day'), ['month' => $calendarMonth->copy()->subMonth()->format('Y-m')])) }}'">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span>{{ $calendarMonth->format('F Y') }}</span>
                <button onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->except('day'), ['month' => $calendarMonth->copy()->addMonth()->format('Y-m')])) }}'">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="cal-grid">
                @foreach(['S','M','T','W','T','F','S'] as $di => $d)
                <div class="cal-day-label {{ $di===0||$di===6 ? 'sun' : '' }}">{{ $d }}</div>
                @endforeach
                @for($i = 0; $i < $startOffset; $i++)<div></div>@endfor
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr   = $calendarMonth->copy()->day($day)->format('Y-m-d');
                        $hasLogs   = in_array($dateStr, $calendarDays);
                        $isToday   = $dateStr === $todayStr;
                        $isSun     = $calendarMonth->copy()->day($day)->dayOfWeek === 0;
                    @endphp
                    <button
                        class="cal-day{{ $isToday?' today':'' }}{{ $hasLogs?' has-logs':'' }}{{ $isSun?' is-rdo':'' }}"
                        data-date="{{ $dateStr }}"
                        onclick="selectDay('{{ $dateStr }}', this)"
                        title="{{ $isSun ? 'Sunday — RDO' : $dateStr }}">
                        {{ $day }}
                        @if($hasLogs)<span class="dot"></span>@endif
                        @if($isSun && !$hasLogs)<span class="cal-rdo-label">RDO</span>@endif
                    </button>
                @endfor
            </div>
            <div class="cal-legend">
                <span><span class="cl-dot" style="background:#dbeafe;border:1.5px solid #3b82f6;border-radius:50%;"></span> Today</span>
                <span><span class="cl-dot" style="background:var(--primary);border-radius:50%;"></span> Selected</span>
                <span><span class="cl-dot" style="background:var(--fg);"></span> Has logs</span>
                <span style="color:#ef4444;"><i class="fas fa-circle" style="font-size:5px;"></i> RDO (Sunday)</span>
            </div>
        </div>

        <div class="day-panel" id="dayPanel">
            <div class="day-panel-header" id="dayPanelHeader">
                <h4 id="dayPanelTitle" style="color:var(--gray-300);">Select a date</h4>
                <button onclick="clearDaySelection()" style="display:none;font-size:0.75rem;font-weight:600;color:var(--muted-foreground);background:none;border:none;cursor:pointer;" id="dayPanelClear">Clear</button>
            </div>
            <div class="day-panel-body" id="dayPanelBody">
                <div class="empty-state" style="min-height:200px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <i class="fas fa-calendar-day" style="font-size:1.5rem;margin-bottom:0.5rem;color:var(--gray-200);"></i>
                    Click a date to view logs
                </div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="table-card anim-up" style="animation-delay:0.18s;">
        <div class="table-header">
            <div class="th-left">
                <div class="th-icon" style="background:var(--gray-500);"><i class="fas fa-clock-rotate-left"></i></div>
                <h4 class="th-title">History</h4>
                <span class="th-badge">Last 14 Days</span>
            </div>
            @if(!$roleFilter)
            <div class="filter-pills" id="historyFilter">
                <button class="filter-pill active" onclick="filterHistory('all',this)">All</button>
                @foreach($historyByRole->sortKeys()->keys() as $r)
                <button class="filter-pill" onclick="filterHistory('{{ $r }}',this)">{{ $roleNames[$r] ?? ucfirst($r) }}</button>
                @endforeach
            </div>
            @endif
        </div>
        <div style="overflow-x:auto;">
            <table class="ut" id="historyTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Role</th>
                        <th style="text-align:center;">Members</th>
                        <th style="text-align:center;">Tasks (combined)</th>
                        <th style="text-align:center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyByRole->sortKeys() as $role => $dateRows)
                    @if(!$roleFilter)
                    <tr class="role-divider-row hist-role-row" data-role-header="{{ $role }}">
                        <td colspan="5">
                            <span class="role-badge {{ $role }}">{{ $roleNames[$role] ?? ucfirst($role) }}</span>
                            <span class="rd-count">{{ $dateRows->count() }} entries</span>
                        </td>
                    </tr>
                    @endif
                    @foreach($dateRows->sortByDesc('date') as $hd)
                    @php
                        $hLabels  = \App\Support\TaskLabels::get($hd->role);
                        $hTotal   = $hd->total_task_1+$hd->total_task_2+$hd->total_task_3+$hd->total_task_4+$hd->total_task_5;
                        $isHdSun  = \Carbon\Carbon::parse($hd->date)->dayOfWeek === 0;
                    @endphp
                    <tr data-role="{{ $role }}" onclick="selectDay('{{ $hd->date->format('Y-m-d') }}',null)" style="cursor:pointer;">
                        <td style="font-weight:600;">
                            {{ $hd->date->format('M d, Y') }}
                            @if($isHdSun)<span class="th-badge" style="background:#fef3c7;color:#d97706;margin-left:6px;font-size:0.6rem;">RDO</span>@endif
                        </td>
                        <td><span class="role-badge {{ $role }}">{{ $roleNames[$role] ?? ucfirst($role) }}</span></td>
                        <td class="num">{{ $hd->user_count }}</td>
                        <td style="text-align:center;">
                            <div class="task-pip">
                                @foreach(['total_task_1','total_task_2','total_task_3','total_task_4','total_task_5'] as $i => $tk)
                                <span title="{{ $hLabels['task_'.($i+1)] }}">{{ $hd->$tk ?: '—' }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="num" style="font-weight:800;">{{ $hTotal }}</td>
                    </tr>
                    @endforeach
                    @empty
                    <tr><td colspan="5" class="empty-state">No history</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ── Pre-loaded calendar data ──────────────────────────────────────────────────
var calendarLogs  = {!! json_encode($calendarLogsJson) !!};
var roleColors    = { content:'var(--sky)',graphics:'var(--amber)',backend:'var(--rose)',researcher:'var(--emerald)' };
var roleNames     = { content:'Content',graphics:'Graphics',backend:'Backend',researcher:'Researcher' };
var activeDay     = null;

// ── Today's Logs table filter ─────────────────────────────────────────────────
function filterTable(tableId, filterId, role, btn) {
    document.getElementById(filterId).querySelectorAll('.filter-pill').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var table = document.getElementById(tableId);
    table.querySelectorAll('tr[data-role]').forEach(function(row) {
        row.style.display = (role === 'all' || row.getAttribute('data-role') === role) ? '' : 'none';
    });
    table.querySelectorAll('tr[data-role-header]').forEach(function(row) {
        row.style.display = (role === 'all') ? '' : 'none';
    });
}

// ── History table filter ──────────────────────────────────────────────────────
function filterHistory(role, btn) {
    document.getElementById('historyFilter').querySelectorAll('.filter-pill').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('#historyTable tr[data-role]').forEach(function(row) {
        row.style.display = (role === 'all' || row.getAttribute('data-role') === role) ? '' : 'none';
    });
    document.querySelectorAll('#historyTable tr.hist-role-row').forEach(function(row) {
        row.style.display = (role === 'all') ? '' : 'none';
    });
}

// ── Calendar day selection (no page reload) ───────────────────────────────────
function selectDay(dateStr, btn) {
    activeDay = dateStr;

    // Update visual selection
    document.querySelectorAll('.cal-day').forEach(function(d) { d.classList.remove('selected'); });
    if (btn) btn.classList.add('selected');

    // Show clear button
    var clearBtn = document.getElementById('dayPanelClear');
    if (clearBtn) clearBtn.style.display = '';

    // Parse date for display
    var parts = dateStr.split('-');
    var d = new Date(parseInt(parts[0]), parseInt(parts[1])-1, parseInt(parts[2]));
    var dayNames  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var monNames  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var isSunday  = d.getDay() === 0;
    var dayLabel  = dayNames[d.getDay()] + ', ' + monNames[d.getMonth()] + ' ' + d.getDate();

    document.getElementById('dayPanelTitle').textContent = dayLabel;

    var data    = calendarLogs[dateStr] || {};
    var roles   = Object.keys(data).sort();
    var body    = document.getElementById('dayPanelBody');

    if (isSunday && roles.length === 0) {
        body.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.625rem;padding:2rem;text-align:center;">'
            + '<i class="fas fa-umbrella-beach" style="font-size:1.5rem;color:#d97706;"></i>'
            + '<p style="margin:0;font-size:0.85rem;font-weight:700;color:#92400e;">Rest Day (RDO)</p>'
            + '<p style="margin:0;font-size:0.78rem;color:var(--muted-foreground);">No submissions expected on Sunday.</p>'
            + '</div>';
        return;
    }

    if (roles.length === 0) {
        body.innerHTML = '<div class="empty-state" style="min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;">'
            + '<i class="fas fa-inbox" style="font-size:1.25rem;margin-bottom:6px;color:var(--gray-200);"></i>No logs on this day</div>';
        return;
    }

    var html = '';
    roles.forEach(function(role) {
        var members = data[role];
        var rc = roleColors[role] || '#64748b';
        var rn = roleNames[role]  || role;
        html += '<div class="day-role-header">'
            + '<span class="role-badge ' + role + '">' + rn + '</span>'
            + '<span style="font-size:0.7rem;color:var(--gray-400);margin-left:4px;">' + members.length + ' member' + (members.length > 1 ? 's' : '') + '</span>'
            + (isSunday ? '<span style="font-size:0.65rem;font-weight:700;color:#ef4444;margin-left:auto;">RDO</span>' : '')
            + '</div>';
        members.forEach(function(m) {
            var total = m.tasks.reduce(function(a,b){ return a+b; }, 0);
            html += '<div class="day-item">'
                + '<img src="' + (m.avatar || '') + '" style="width:28px;height:28px;border-radius:50%;border:1.5px solid var(--border);object-fit:cover;" alt="">'
                + '<div style="flex:1;min-width:0;">'
                +   '<span style="font-weight:600;font-size:0.8rem;">' + (m.first_name || m.username) + ' ' + (m.last_name || '') + '</span>'
                +   (m.badge ? '<span style="display:block;font-size:0.6rem;font-weight:700;color:#0369a1;">' + m.badge + '</span>' : '')
                + '</div>'
                + '<div class="task-pip">';
            m.tasks.forEach(function(t) {
                html += '<span>' + t + '</span>';
            });
            html += '</div>'
                + '<span style="font-size:0.75rem;font-weight:800;color:var(--gray-500);margin-left:6px;">' + total + '</span>'
                + '</div>';
        });
    });
    body.innerHTML = html;
}

function clearDaySelection() {
    activeDay = null;
    document.querySelectorAll('.cal-day').forEach(function(d) { d.classList.remove('selected'); });
    document.getElementById('dayPanelTitle').textContent = 'Select a date';
    document.getElementById('dayPanelClear').style.display = 'none';
    document.getElementById('dayPanelBody').innerHTML = '<div class="empty-state" style="min-height:200px;display:flex;flex-direction:column;align-items:center;justify-content:center;">'
        + '<i class="fas fa-calendar-day" style="font-size:1.5rem;margin-bottom:0.5rem;color:var(--gray-200);"></i>Click a date to view logs</div>';
}

// ── ApexCharts ────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var style    = getComputedStyle(document.documentElement);
    var colorMap = {
        content:    style.getPropertyValue('--sky').trim(),
        graphics:   style.getPropertyValue('--amber').trim(),
        backend:    style.getPropertyValue('--rose').trim(),
        researcher: style.getPropertyValue('--emerald').trim(),
    };

    var weekLabels    = {!! json_encode($dlWeekLabels) !!};
    var sundayIndices = {!! json_encode($dlWeekSundayIndices) !!};
    var labelColors   = weekLabels.map(function(_, i) {
        return sundayIndices.indexOf(i) !== -1 ? '#ef4444' : '#94a3b8';
    });

    var roleBreakdown = {!! json_encode($dlRoleBreakdown->map(fn($r) => ['role'=>$r['role'],'series'=>$r['series']])) !!};
    roleBreakdown.forEach(function(r) {
        var el = document.getElementById('dlChart-' + r.role);
        if (!el) return;
        var c = colorMap[r.role] || '#6366f1';
        new ApexCharts(el, {
            chart: { type:'bar', height: el.style.height === '180px' ? 180 : 90, toolbar:{show:false}, fontFamily:'Inter', foreColor:'#94a3b8' },
            series: [{ name:'Tasks', data:r.series }],
            colors: [c],
            plotOptions: { bar: { columnWidth:'65%', borderRadius:3, borderRadiusApplication:'end' } },
            xaxis: {
                categories: weekLabels,
                labels: { style:{ fontSize:'9px', fontWeight:600, colors:labelColors } },
                axisBorder:{show:false}, axisTicks:{show:false}
            },
            yaxis: { show:false, min:0 },
            grid:  { show:false, padding:{ left:2,right:2,top:0,bottom:0 } },
            dataLabels: { enabled:false },
            tooltip: {
                theme:'light', style:{fontSize:'12px',fontFamily:'Inter'},
                x:{ formatter:function(val,opts) {
                    var i = opts.dataPointIndex;
                    return sundayIndices.indexOf(i) !== -1 ? weekLabels[i]+' (RDO)' : weekLabels[i];
                }},
                y:{ formatter:function(v){ return v+' tasks'; } }
            }
        }).render();
    });

    // ── Restore selected day from URL (on initial load) ───────────────────────
    var urlParams  = new URLSearchParams(window.location.search);
    var initialDay = urlParams.get('day') || '{{ $selectedDay ?? '' }}';
    if (initialDay) {
        var btn = document.querySelector('.cal-day[data-date="' + initialDay + '"]');
        selectDay(initialDay, btn);
    }
});
</script>
@endsection
