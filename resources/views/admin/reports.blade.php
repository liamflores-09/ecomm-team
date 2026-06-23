@extends('layouts.app')

@section('title', 'Reports — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Page layout ─────────────────────────────────────────────── */
    .rpt-header { margin-bottom: 1.5rem; }
    .rpt-header h2 { font-size: 1.5rem; font-weight: 800; margin: 0 0 0.2rem; }
    .rpt-header p  { font-size: 0.88rem; color: var(--muted-foreground); font-weight: 500; margin: 0; }

    .rpt-controls {
        display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;
        flex-wrap: wrap; padding: 0.875rem 1.125rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
    }
    .rpt-controls-label { font-size: 0.72rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.25rem; }
    .rpt-month-wrap { min-width: 140px; max-width: 160px; }
    .rpt-divider { width: 1px; height: 28px; background: var(--border); }
    .rpt-tabs { display: flex; gap: 0; border: 1.5px solid var(--border); border-radius: 8px; overflow: hidden; }
    .rpt-tab {
        padding: 0.45rem 1.1rem; font-size: 0.78rem; font-weight: 700; cursor: pointer;
        border: none; background: transparent; color: var(--muted-foreground);
        font-family: var(--p-font-family-sans); border-right: 1.5px solid var(--border);
        transition: all 0.15s;
    }
    .rpt-tab:last-child { border-right: none; }
    .rpt-tab:hover { background: var(--muted); color: var(--fg); }
    .rpt-tab.active { background: var(--primary); color: white; }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ── Back link ───────────────────────────────────────────────── */
    .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; color: var(--muted-foreground); text-decoration: none; margin-bottom: 1rem; transition: color 0.15s; }
    .back-link:hover { color: var(--fg); }

    /* ── KPI cards ───────────────────────────────────────────────── */
    .rpt-kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .rpt-kpi-card { background: var(--card); border-radius: 8px; padding: 1.25rem; border: 1px solid var(--border); }
    .rpt-kpi-top  { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.625rem; }
    .rpt-kpi-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-400); }
    .rpt-kpi-icon  { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: white; }
    .rpt-kpi-value { font-size: 1.65rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .rpt-kpi-sub   { font-size: 0.73rem; color: var(--gray-400); font-weight: 500; }

    /* ── Section header ──────────────────────────────────────────── */
    .rpt-section { margin-bottom: 1.75rem; }
    .rpt-section-header { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .rpt-section-icon { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.65rem; flex-shrink: 0; }
    .rpt-section-header h3 { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .rpt-section-line { flex: 1; height: 1px; background: var(--border); }

    /* ── Role overview 2x2 ───────────────────────────────────────── */
    .rpt-role-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .rpt-role-card {
        background: var(--card); border-radius: 8px; border: 1px solid var(--border);
        padding: 1.25rem 1.5rem; transition: border-color 0.2s;
        display: flex; flex-direction: column; gap: 0.875rem;
    }
    .rpt-role-card:hover { border-color: var(--border-strong); }
    .rpt-role-card-header { display: flex; align-items: center; justify-content: space-between; }
    .rpt-role-total { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .rpt-role-sub   { font-size: 0.7rem; color: var(--muted-foreground); margin-top: 2px; }
    .rpt-role-meta  { display: flex; gap: 1.25rem; }
    .rpt-role-meta-item { display: flex; flex-direction: column; }
    .rpt-role-meta-val { font-size: 0.95rem; font-weight: 800; }
    .rpt-role-meta-lbl { font-size: 0.62rem; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
    .rpt-role-link { font-size: 0.72rem; font-weight: 700; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.15s; }
    .rpt-role-link:hover { gap: 7px; }

    /* ── Charts grid ─────────────────────────────────────────────── */
    .rpt-charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
    .rpt-chart-card { background: var(--card); border-radius: 8px; border: 1px solid var(--border); overflow: hidden; }
    .rpt-chart-header { display: flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--muted); }
    .rpt-chart-icon  { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.65rem; flex-shrink: 0; }
    .rpt-chart-header h4 { font-size: 0.82rem; font-weight: 700; margin: 0; }
    .rpt-chart-body { padding: 1rem 1.25rem; }

    /* ── Table card ──────────────────────────────────────────────── */
    .rpt-table-card { background: var(--card); border-radius: 8px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 1rem; }
    .rpt-table-header { display: flex; align-items: center; justify-content: space-between; padding: 0.7rem 1rem; border-bottom: 1px solid var(--muted); }
    .rpt-table-header .th-left { display: flex; align-items: center; gap: 0.5rem; }
    .rpt-table-header h4 { font-size: 0.8rem; font-weight: 700; margin: 0; }
    .rpt-table-header .th-badge { font-size: 0.6rem; font-weight: 700; padding: 2px 7px; border-radius: 4px; background: var(--muted); color: var(--muted-foreground); }

    /* ── Table styles ────────────────────────────────────────────── */
    .wt { width: 100%; border-collapse: collapse; }
    .wt thead th {
        padding: 0.45rem 0.75rem; font-size: 0.6rem; font-weight: 700; color: var(--gray-400);
        background: var(--muted); border-bottom: 1px solid var(--border); text-align: left;
        text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap;
    }
    .wt thead th.num { text-align: center; }
    .wt tbody td { padding: 0.45rem 0.75rem; border-bottom: 1px solid var(--border); font-size: 0.8rem; }
    .wt tbody td.num { text-align: center; font-variant-numeric: tabular-nums; font-weight: 600; }
    .wt tbody tr:last-child td { border-bottom: none; }
    .wt tbody tr:hover td { background: #FAFAFA; }
    .wt .week-sep td {
        background: var(--muted); font-weight: 800; font-size: 0.68rem;
        text-transform: uppercase; letter-spacing: 0.04em; color: var(--gray-500);
        padding: 0.35rem 0.75rem; border-bottom: 1px solid var(--border);
    }
    .wt .total-row td {
        background: var(--fg); color: white; font-weight: 800; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.04em; padding: 0.45rem 0.75rem;
    }
    .wt .month-total td {
        background: var(--indigo); color: white; font-weight: 800; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.04em; padding: 0.55rem 0.75rem;
    }
    .wt tbody tr:hover.total-row td,
    .wt tbody tr:hover.month-total td { background: inherit; }

    /* ── Role section divider in tables ──────────────────────────── */
    .rpt-role-divider td {
        padding: 0.45rem 1rem; background: var(--muted);
        border-bottom: 1px solid var(--border); border-top: 2px solid var(--border);
    }
    .rpt-role-divider:first-child td { border-top: none; }
    .rpt-role-divider td .rd-count { font-size: 0.7rem; font-weight: 600; color: var(--gray-400); margin-left: 6px; }

    /* ── Member perf cards ───────────────────────────────────────── */
    .rpt-perf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .rpt-perf-card {
        background: var(--card); border-radius: 8px; border: 1px solid var(--border);
        padding: 1.1rem; transition: border-color 0.2s;
    }
    .rpt-perf-card:hover { border-color: var(--border-strong); }
    .mpc-top { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1rem; }
    .mpc-avatar { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--muted); flex-shrink: 0; }
    .mpc-info { flex: 1; min-width: 0; }
    .mpc-name { font-weight: 700; font-size: 0.9rem; }
    .mpc-total { font-size: 0.72rem; color: var(--gray-400); font-weight: 500; }
    .mpc-share { display: flex; flex-direction: column; align-items: center; background: var(--muted); border-radius: 8px; padding: 0.4rem 0.6rem; min-width: 52px; }
    .mpc-share-val   { font-size: 1rem; font-weight: 800; color: var(--indigo); line-height: 1; }
    .mpc-share-label { font-size: 0.5rem; font-weight: 700; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }
    .mpc-divider { height: 1px; background: var(--border); margin-bottom: 0.625rem; }
    .mpc-tasks { display: flex; flex-direction: column; gap: 0.375rem; }
    .mpc-task { display: flex; align-items: center; gap: 0.4rem; }
    .mpc-task-dot { width: 7px; height: 7px; border-radius: 2px; flex-shrink: 0; }
    .mpc-task-label { flex: 1; font-size: 0.68rem; color: var(--gray-500); font-weight: 500; }
    .mpc-task-bar-wrap { width: 55px; height: 4px; background: var(--muted); border-radius: 3px; overflow: hidden; flex-shrink: 0; }
    .mpc-task-bar { height: 100%; border-radius: 3px; }
    .mpc-task-val { font-size: 0.72rem; font-weight: 700; font-variant-numeric: tabular-nums; min-width: 24px; text-align: right; }

    /* ── Role-grouped perf section ───────────────────────────────── */
    .rpt-role-section { margin-bottom: 2rem; }
    .rpt-role-section-title {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 0; margin-bottom: 0.875rem;
        border-bottom: 2px solid var(--border);
    }
    .rpt-role-section-title h4 { font-size: 0.82rem; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.04em; }
    .rpt-role-section-title .rs-count { font-size: 0.7rem; font-weight: 600; color: var(--muted-foreground); }

    /* ── Role sub-tab navigation ─────────────────────────────────── */
    .rpt-role-nav {
        display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;
        padding: 0.75rem 1rem; margin-bottom: 1.5rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
    }
    .rpt-role-nav-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: var(--gray-400); margin-right: 0.25rem; white-space: nowrap;
    }
    .rpt-role-nav-btn {
        display: inline-flex; align-items: center; gap: 0.45rem;
        padding: 0.45rem 0.9rem; border-radius: 8px;
        border: 1.5px solid var(--border); background: transparent;
        cursor: pointer; font-family: var(--p-font-family-sans);
        font-size: 0.78rem; font-weight: 700; color: var(--fg);
        transition: border-color 0.15s, background 0.15s, color 0.15s;
        outline: none; white-space: nowrap;
    }
    .rpt-role-nav-btn:hover {
        border-color: var(--rn-color, var(--primary));
    }
    .rpt-role-nav-btn.active {
        background: var(--rn-color, var(--primary));
        border-color: var(--rn-color, var(--primary));
        color: white;
    }
    .rn-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; transition: opacity 0.15s; }
    .rpt-role-nav-btn.active .rn-dot { opacity: 0.5; }
    .rn-count {
        font-size: 0.62rem; font-weight: 600; border-radius: 4px;
        padding: 1px 5px; background: rgba(0,0,0,0.06);
        transition: background 0.15s;
    }
    .rpt-role-nav-btn.active .rn-count { background: rgba(255,255,255,0.22); }

    /* ── Role content panels ─────────────────────────────────────── */
    .rpt-role-panel { display: none; }
    .rpt-role-panel.active { display: block; animation: panelFade 0.18s ease; }
    @keyframes panelFade { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }

    @media (max-width: 1024px) {
        .rpt-kpi-grid   { grid-template-columns: repeat(2, 1fr); }
        .rpt-charts-grid { grid-template-columns: 1fr; }
        .rpt-role-grid  { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .rpt-kpi-grid  { grid-template-columns: 1fr; }
        .rpt-controls  { flex-direction: column; align-items: stretch; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.reports" :isAdmin="true" />

<div class="main-content">
@php
    $isAllRoles = !$roleFilter;

    $roleColorMap = [
        'content'    => '#0ea5e9',
        'researcher' => '#10b981',
        'graphics'   => '#f59e0b',
        'backend'    => '#f43f5e',
    ];
    $roleNameMap = [
        'content'    => 'Content',
        'researcher' => 'Researcher',
        'graphics'   => 'Graphics',
        'backend'    => 'Backend',
    ];
    $taskColorMap = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#f43f5e'];

    // Generic task labels (used in All Roles view where columns differ per role)
    $genericLabels = ['task_1'=>'Task 1','task_2'=>'Task 2','task_3'=>'Task 3','task_4'=>'Task 4','task_5'=>'Task 5'];

    // Role-specific task labels (for filtered view)
    $taskLabels = \App\Support\TaskLabels::get($roleFilter ?: 'content');

    // Flatten all log rows with date + week for easy grouping
    $allRows = collect();
    foreach ($weeks as $wk) {
        foreach ($wk['days'] as $day) {
            foreach ($day['members'] as $m) {
                $allRows->push(array_merge($m, ['date' => $day['date'], 'week_num' => $wk['week_num']]));
            }
        }
    }

    $grandTotal     = $monthTotal['t1'] + $monthTotal['t2'] + $monthTotal['t3'] + $monthTotal['t4'] + $monthTotal['t5'];
    $daysWithLogs   = $allRows->pluck('date')->unique()->count();
    $avgPerDay      = $daysWithLogs > 0 ? round($grandTotal / $daysWithLogs) : 0;

    // Most common task type
    $taskSums = [
        'task_1' => $monthTotal['t1'],
        'task_2' => $monthTotal['t2'],
        'task_3' => $monthTotal['t3'],
        'task_4' => $monthTotal['t4'],
        'task_5' => $monthTotal['t5'],
    ];
    $topTaskKey = array_search(max($taskSums), $taskSums);
    $topTaskVal = max($taskSums) ?: 0;
    $topTaskLabel = $isAllRoles ? $genericLabels[$topTaskKey] : $taskLabels[$topTaskKey];

    // Rows grouped by role (for All Roles breakdown)
    $rowsByRole = $allRows->groupBy('role')->filter(fn($_, $r) => isset($roleColorMap[$r]));

    // Avatar seed helper (closure)
    $avatarSeed = fn($username, $gender) => $gender === 'female' ? $username . 'Female' : $username;
@endphp

    <!-- Header -->
    <div class="rpt-header anim-up">
        <h2>Reports
            @if($roleFilter)
            &mdash; <span style="color:{{ $roleColorMap[$roleFilter] ?? 'var(--primary)' }}">{{ $roleNameMap[$roleFilter] ?? ucfirst($roleFilter) }}</span>
            @endif
        </h2>
        <p>{{ \Carbon\Carbon::parse($month)->format('F Y') }} performance overview
            @if($isAllRoles)<span style="color:var(--muted-foreground);"> &mdash; All Roles</span>@endif
        </p>
    </div>

    <!-- Controls -->
    <div class="rpt-controls anim-up d1">
        <span class="rpt-controls-label">Month</span>
        <div class="rpt-month-wrap">
            <select onchange="window.location.href=updateParam('month', this.value)" style="margin:0;">
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
        <div class="rpt-divider"></div>
        <div class="rpt-tabs">
            <button class="rpt-tab active" onclick="switchTab('weekly',this)">Weekly</button>
            <button class="rpt-tab" onclick="switchTab('monthly',this)">Monthly</button>
        </div>
    </div>

    @if($isAllRoles)
    <div class="rpt-role-nav anim-up d2">
        <span class="rpt-role-nav-label">Role</span>
        @foreach($roleGroupedData as $role => $rd)
        <button class="rpt-role-nav-btn {{ $loop->first ? 'active' : '' }}"
                data-role="{{ $role }}"
                onclick="switchRole('{{ $role }}', this)"
                style="--rn-color: {{ $rd['color'] }};">
            <span class="rn-dot" style="background:{{ $rd['color'] }};"></span>
            {{ $rd['label'] }}
            <span class="rn-count">{{ number_format(array_sum($rd['monthTotal'])) }}</span>
        </button>
        @endforeach
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         WEEKLY TAB
    ════════════════════════════════════════════════════════════════ --}}
    <div class="tab-content active" id="tab-weekly">

        {{-- KPIs --}}
        <div class="rpt-kpi-grid anim-up d2">
            <div class="rpt-kpi-card">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-label">Total Tasks</span>
                    <div class="rpt-kpi-icon" style="background:var(--primary);"><i class="fas fa-list-check"></i></div>
                </div>
                <div class="rpt-kpi-value">{{ number_format($grandTotal) }}</div>
                <div class="rpt-kpi-sub">Across {{ $daysWithLogs }} active day{{ $daysWithLogs !== 1 ? 's' : '' }} this month</div>
            </div>
            <div class="rpt-kpi-card">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-label">Avg / Day</span>
                    <div class="rpt-kpi-icon" style="background:var(--success);"><i class="fas fa-gauge-high"></i></div>
                </div>
                <div class="rpt-kpi-value">{{ $avgPerDay }}</div>
                <div class="rpt-kpi-sub">Average tasks logged per active day</div>
            </div>
            <div class="rpt-kpi-card">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-label">Most Common</span>
                    <div class="rpt-kpi-icon" style="background:var(--warning);"><i class="fas fa-fire"></i></div>
                </div>
                <div class="rpt-kpi-value" style="font-size:1.1rem;">{{ $topTaskLabel }}</div>
                <div class="rpt-kpi-sub">{{ number_format($topTaskVal) }} tasks ({{ $grandTotal > 0 ? round($topTaskVal / $grandTotal * 100) : 0 }}% of total)</div>
            </div>
        </div>

        @if($grandTotal > 0)

        {{-- ══ ALL ROLES: Role breakdown overview ══ --}}
        @if($isAllRoles)
        <div class="rpt-section anim-up d3">
            <div class="rpt-section-header">
                <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-layer-group"></i></div>
                <h3>Role Overview</h3>
                <div class="rpt-section-line"></div>
                <span style="font-size:0.7rem;color:var(--muted-foreground);font-weight:500;">{{ \Carbon\Carbon::parse($month)->format('F Y') }} totals per role</span>
            </div>
            <div class="rpt-role-grid">
                @foreach($roleMonthTotals as $role => $rt)
                @php $rc = $roleColorMap[$role] ?? '#6366f1'; @endphp
                <div class="rpt-role-card" style="border-top:3px solid {{ $rc }};">
                    <div class="rpt-role-card-header">
                        <span class="role-badge {{ $role }}">{{ $roleNameMap[$role] ?? ucfirst($role) }}</span>
                        <a href="{{ route('admin.reports', ['role' => $role, 'month' => $month]) }}" class="rpt-role-link">
                            View Detail <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
                        </a>
                    </div>
                    <div>
                        <div class="rpt-role-total" style="color:{{ $rc }};">{{ number_format($rt['total']) }}</div>
                        <div class="rpt-role-sub">total tasks logged</div>
                    </div>
                    <div class="rpt-role-meta">
                        <div class="rpt-role-meta-item">
                            <span class="rpt-role-meta-val">{{ $rt['members'] }}</span>
                            <span class="rpt-role-meta-lbl">members</span>
                        </div>
                        <div class="rpt-role-meta-item">
                            <span class="rpt-role-meta-val">{{ $grandTotal > 0 ? round($rt['total'] / $grandTotal * 100) : 0 }}%</span>
                            <span class="rpt-role-meta-lbl">of total</span>
                        </div>
                        <div class="rpt-role-meta-item">
                            <span class="rpt-role-meta-val">{{ $rt['members'] > 0 ? round($rt['total'] / $rt['members']) : 0 }}</span>
                            <span class="rpt-role-meta-lbl">avg / member</span>
                        </div>
                    </div>
                    <div id="rptRoleChart-{{ $role }}" style="height:70px;margin:0 -0.5rem -0.5rem;"></div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ══ ALL ROLES: Per-role sections (trend, leaderboard, weekly summary, daily breakdown, contribution) ══ --}}
        @if($isAllRoles)
        @foreach($roleGroupedData as $role => $rd)
        @php
            $rdGrandTotal  = array_sum($rd['monthTotal']);
            $rdWeeks       = $rd['weeks'];
            $rdShareData   = $rd['shareData'];
            $rdMemberNames = $rd['memberNames'];
            $rdTaskLabels  = $rd['taskLabels'];
            $rdColor       = $rd['color'];
            $rdLabel       = $rd['label'];
        @endphp
        <div class="rpt-role-panel {{ $loop->first ? 'active' : '' }}" data-role="{{ $role }}" style="border-top:3px solid {{ $rdColor }};margin-bottom:0;background:var(--card);border-radius:8px;border:1px solid var(--border);overflow:hidden;">
            <div style="padding:0.875rem 1.25rem;border-bottom:1px solid var(--muted);display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.625rem;">
                    <span class="role-badge {{ $role }}">{{ $rdLabel }}</span>
                    <span style="font-size:0.72rem;color:var(--muted-foreground);font-weight:500;">{{ count($rdMemberNames) }} member{{ count($rdMemberNames)!=1?'s':'' }} &middot; {{ number_format($rdGrandTotal) }} tasks</span>
                </div>
                <a href="{{ route('admin.reports', ['role' => $role, 'month' => $month]) }}" class="rpt-role-link">
                    Full Report <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
                </a>
            </div>
            <div style="padding:1.25rem;">

                {{-- Charts: Weekly Trend + Leaderboard --}}
                <div class="rpt-charts-grid" style="margin-bottom:1.5rem;">
                    <div class="rpt-chart-card">
                        <div class="rpt-chart-header">
                            <div class="rpt-chart-icon" style="background:{{ $rdColor }};"><i class="fas fa-chart-line"></i></div>
                            <h4>Weekly Trend</h4>
                        </div>
                        <div class="rpt-chart-body">
                            <div id="trendChart-{{ $role }}" style="height:200px;"></div>
                        </div>
                    </div>
                    <div class="rpt-chart-card">
                        <div class="rpt-chart-header">
                            <div class="rpt-chart-icon" style="background:{{ $rdColor }};"><i class="fas fa-ranking-star"></i></div>
                            <h4>Member Leaderboard</h4>
                        </div>
                        <div class="rpt-chart-body">
                            <div id="leaderboardChart-{{ $role }}" style="height:200px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Weekly Summary --}}
                <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                    <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-chart-simple"></i></div>
                    <h3>Weekly Summary</h3>
                    <div class="rpt-section-line"></div>
                </div>
                <div class="rpt-table-card" style="margin-bottom:1.5rem;">
                    <div style="overflow-x:auto;">
                    <table class="wt">
                        <thead>
                            <tr>
                                <th>Wk</th>
                                @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                                <th class="num">{{ $rdTaskLabels[$tk] }}</th>
                                @endforeach
                                <th class="num" style="border-left:2px solid var(--border);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rdSt = [0,0,0,0,0]; @endphp
                            @foreach($rdWeeks as $wk)
                            @php $rdWt = [$wk['total_t1'],$wk['total_t2'],$wk['total_t3'],$wk['total_t4'],$wk['total_t5']]; foreach($rdWt as $i=>$v) $rdSt[$i]+=$v; @endphp
                            <tr>
                                <td style="font-weight:700;color:var(--gray-500);">W{{ $wk['week_num'] }}</td>
                                @foreach($rdWt as $v)<td class="num" style="font-weight:700;">{{ $v }}</td>@endforeach
                                <td class="num" style="border-left:2px solid var(--border);font-weight:800;">{{ array_sum($rdWt) }}</td>
                            </tr>
                            @endforeach
                            <tr class="month-total">
                                <td style="font-weight:800;">Total</td>
                                @foreach($rdSt as $v)<td class="num">{{ $v }}</td>@endforeach
                                <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($rdSt) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Daily Breakdown --}}
                <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                    <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-table"></i></div>
                    <h3>Daily Breakdown</h3>
                    <div class="rpt-section-line"></div>
                </div>
                <div class="rpt-table-card" style="margin-bottom:1.5rem;">
                    <div style="overflow-x:auto;">
                    <table class="wt">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Date</th>
                                <th>Member</th>
                                <th class="num">{{ $rdTaskLabels['task_1'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_2'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_3'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_4'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_5'] }}</th>
                                <th class="num" style="border-left:2px solid var(--border);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rdRowNum = 0; $rdGrand = [0,0,0,0,0]; @endphp
                            @foreach($rdWeeks as $wk)
                            @php $rdWkTots = [$wk['total_t1'],$wk['total_t2'],$wk['total_t3'],$wk['total_t4'],$wk['total_t5']]; foreach($rdWkTots as $i=>$v) $rdGrand[$i]+=$v; @endphp
                            <tr class="week-sep"><td colspan="9">Week {{ $wk['week_num'] }}</td></tr>
                            @foreach($wk['days'] as $day)
                            @php $dayCount = count($day['members']); @endphp
                            @foreach($day['members'] as $m)
                            @php $rdRowNum++; $rdRowTotal = $m['task_1']+$m['task_2']+$m['task_3']+$m['task_4']+$m['task_5']; @endphp
                            <tr>
                                <td style="color:var(--gray-300);font-size:0.72rem;">{{ $rdRowNum }}</td>
                                @if($loop->first)<td rowspan="{{ $dayCount }}" style="white-space:nowrap;font-weight:600;font-size:0.78rem;vertical-align:middle;border-bottom:2px solid var(--border);">{{ \Carbon\Carbon::parse($day['date'])->format('D, M d') }}</td>@endif
                                <td><div class="user-cell"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($m['username'], $m['gender'] ?? 'male') }}" alt=""><span class="name">{{ $m['first_name'] }}</span></div></td>
                                <td class="num">{{ $m['task_1'] ?: '—' }}</td>
                                <td class="num">{{ $m['task_2'] ?: '—' }}</td>
                                <td class="num">{{ $m['task_3'] ?: '—' }}</td>
                                <td class="num">{{ $m['task_4'] ?: '—' }}</td>
                                <td class="num">{{ $m['task_5'] ?: '—' }}</td>
                                <td class="num" style="border-left:2px solid var(--border);font-weight:800;">{{ $rdRowTotal }}</td>
                            </tr>
                            @endforeach
                            @endforeach
                            <tr class="total-row">
                                <td colspan="3" style="text-align:right;">Week {{ $wk['week_num'] }} Total</td>
                                @foreach($rdWkTots as $v)<td class="num">{{ $v }}</td>@endforeach
                                <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($rdWkTots) }}</td>
                            </tr>
                            @endforeach
                            <tr class="month-total">
                                <td colspan="3" style="text-align:right;">{{ \Carbon\Carbon::parse($month)->format('F') }} Total</td>
                                @foreach($rdGrand as $v)<td class="num">{{ $v }}</td>@endforeach
                                <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($rdGrand) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Avg Contribution % by Task + Contribution Analysis --}}
                @if($rdShareData->count() && count($rdMemberNames))
                @php
                    $rdContribLabels = $rdShareData->pluck('task_name')->toArray();
                    $rdContribSeries = collect($rdMemberNames)->map(fn($mn) => [
                        'name' => $mn,
                        'data' => $rdShareData->map(fn($tg) => round($tg['weeks']->avg(fn($wk) => ($wk['members'][$mn]['share'] ?? 0)), 1))->toArray(),
                    ])->values()->toArray();
                @endphp
                <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                    <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-chart-pie"></i></div>
                    <h3>Avg Contribution % by Task</h3>
                    <div class="rpt-section-line"></div>
                    <span style="font-size:0.7rem;color:var(--muted-foreground);font-weight:500;">Average % per member per task type</span>
                </div>
                <div class="rpt-chart-card" style="margin-bottom:1rem;">
                    <div class="rpt-chart-body">
                        <div id="contribChart-{{ $role }}" style="height:260px;"></div>
                    </div>
                </div>
                <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                    <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-table-list"></i></div>
                    <h3>Contribution Analysis</h3>
                    <div class="rpt-section-line"></div>
                </div>
                <div class="rpt-table-card">
                    <div style="overflow-x:auto;">
                    <table class="wt">
                        <thead>
                            <tr>
                                <th></th>
                                @foreach($rdMemberNames as $mn)
                                <th colspan="2" style="text-align:center;border-left:2px solid var(--border);font-size:0.68rem;text-transform:uppercase;">{{ $mn }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th></th>
                                @foreach($rdMemberNames as $mn)
                                <th class="num" style="font-size:0.52rem;">TASKS</th>
                                <th class="num" style="font-size:0.52rem;border-right:2px solid var(--border);">SHARE</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rdShareData as $taskGroup)
                            <tr style="background:var(--muted);">
                                <td colspan="{{ count($rdMemberNames)*2+1 }}" style="font-weight:800;font-size:0.68rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--gray-500);padding:0.4rem 0.75rem;">{{ $taskGroup['task_name'] }}</td>
                            </tr>
                            @foreach($taskGroup['weeks'] as $wk)
                            <tr>
                                <td style="font-weight:700;color:var(--gray-400);font-size:0.73rem;">W{{ $wk['week_num'] }}</td>
                                @foreach($rdMemberNames as $mn)
                                @php $md = $wk['members'][$mn] ?? ['tasks'=>0,'share'=>0]; @endphp
                                <td class="num" style="font-weight:700;">{{ $md['tasks'] }}</td>
                                <td class="num" style="border-right:2px solid var(--border);font-size:0.73rem;">{{ number_format($md['share'],2) }}%</td>
                                @endforeach
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endforeach
        @endif

        {{-- ══ SINGLE ROLE: Weekly Trend + Leaderboard ══ --}}
        @if(!$isAllRoles)
        <div class="rpt-charts-grid anim-up d3">
            <div class="rpt-chart-card">
                <div class="rpt-chart-header">
                    <div class="rpt-chart-icon" style="background:var(--primary);"><i class="fas fa-chart-line"></i></div>
                    <h4>Weekly Trend</h4>
                </div>
                <div class="rpt-chart-body">
                    <div id="trendChart" style="height:240px;"></div>
                </div>
            </div>
            <div class="rpt-chart-card">
                <div class="rpt-chart-header">
                    <div class="rpt-chart-icon" style="background:var(--primary);"><i class="fas fa-ranking-star"></i></div>
                    <h4>Member Leaderboard</h4>
                </div>
                <div class="rpt-chart-body">
                    <div id="leaderboardChart" style="height:240px;"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══ SINGLE ROLE: Weekly Summary ══ --}}
        @if(!$isAllRoles && $weeks->count())
        <div class="rpt-section anim-up d4">
            <div class="rpt-section-header">
                <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-chart-simple"></i></div>
                <h3>Weekly Summary</h3>
                <div class="rpt-section-line"></div>
            </div>
            <div class="rpt-table-card">
                <div style="overflow-x:auto;">
                <table class="wt">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                            <th class="num">{{ $taskLabels[$tk] }}</th>
                            @endforeach
                            <th class="num" style="border-left:2px solid var(--border);">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $st = [0,0,0,0,0]; @endphp
                        @foreach($weeks as $wk)
                        @php
                            $wt = [$wk['total_t1'],$wk['total_t2'],$wk['total_t3'],$wk['total_t4'],$wk['total_t5']];
                            foreach($wt as $i=>$v) $st[$i]+=$v;
                        @endphp
                        <tr>
                            <td style="font-weight:700;color:var(--gray-500);">W{{ $wk['week_num'] }}</td>
                            @foreach($wt as $v)<td class="num" style="font-weight:700;">{{ $v }}</td>@endforeach
                            <td class="num" style="border-left:2px solid var(--border);font-weight:800;">{{ array_sum($wt) }}</td>
                        </tr>
                        @endforeach
                        <tr class="month-total">
                            <td style="font-weight:800;">Total</td>
                            @foreach($st as $v)<td class="num">{{ $v }}</td>@endforeach
                            <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($st) }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ══ SINGLE ROLE: Daily Breakdown ══ --}}
        @if(!$isAllRoles)
        <div class="rpt-section anim-up d5">
            <div class="rpt-section-header">
                <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-table"></i></div>
                <h3>Daily Breakdown</h3>
                <div class="rpt-section-line"></div>
                <span style="font-size:0.7rem;color:var(--muted-foreground);font-weight:500;">Individual member entries by day</span>
            </div>
            <div class="rpt-table-card">
                <div style="overflow-x:auto;">
                <table class="wt">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Date</th>
                            <th>Member</th>
                            <th class="num">{{ $taskLabels['task_1'] }}</th>
                            <th class="num">{{ $taskLabels['task_2'] }}</th>
                            <th class="num">{{ $taskLabels['task_3'] }}</th>
                            <th class="num">{{ $taskLabels['task_4'] }}</th>
                            <th class="num">{{ $taskLabels['task_5'] }}</th>
                            <th class="num" style="border-left:2px solid var(--border);">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 0; $sGrand = [0,0,0,0,0]; @endphp
                        @foreach($weeks as $week)
                        @php
                            $sTotals = [$week['total_t1'],$week['total_t2'],$week['total_t3'],$week['total_t4'],$week['total_t5']];
                            foreach($sTotals as $i=>$v) $sGrand[$i]+=$v;
                        @endphp
                        <tr class="week-sep"><td colspan="9">Week {{ $week['week_num'] }}</td></tr>
                        @foreach($week['days'] as $day)
                        @php $dayCount = count($day['members']); @endphp
                        @foreach($day['members'] as $m)
                        @php
                            $rowNum++;
                            $rowTotal = $m['task_1']+$m['task_2']+$m['task_3']+$m['task_4']+$m['task_5'];
                        @endphp
                        <tr>
                            <td style="color:var(--gray-300);font-size:0.72rem;">{{ $rowNum }}</td>
                            @if($loop->first)
                            <td rowspan="{{ $dayCount }}" style="white-space:nowrap;font-weight:600;font-size:0.78rem;vertical-align:middle;border-bottom:2px solid var(--border);">
                                {{ \Carbon\Carbon::parse($day['date'])->format('D, M d') }}
                            </td>
                            @endif
                            <td>
                                <div class="user-cell">
                                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($m['username'], $m['gender'] ?? 'male') }}" alt="">
                                    <span class="name">{{ $m['first_name'] }}</span>
                                </div>
                            </td>
                            <td class="num">{{ $m['task_1'] ?: '—' }}</td>
                            <td class="num">{{ $m['task_2'] ?: '—' }}</td>
                            <td class="num">{{ $m['task_3'] ?: '—' }}</td>
                            <td class="num">{{ $m['task_4'] ?: '—' }}</td>
                            <td class="num">{{ $m['task_5'] ?: '—' }}</td>
                            <td class="num" style="border-left:2px solid var(--border);font-weight:800;">{{ $rowTotal }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" style="text-align:right;">Week {{ $week['week_num'] }} Total</td>
                            @foreach($sTotals as $v)<td class="num">{{ $v }}</td>@endforeach
                            <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($sTotals) }}</td>
                        </tr>
                        @endforeach
                        <tr class="month-total">
                            <td colspan="3" style="text-align:right;">{{ \Carbon\Carbon::parse($month)->format('F') }} Total</td>
                            @foreach($sGrand as $v)<td class="num">{{ $v }}</td>@endforeach
                            <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($sGrand) }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ══ SINGLE ROLE: Contribution Analysis ══ --}}
        @if(!$isAllRoles && $shareData->count() && count($memberNames))
        @php
            $contribLabels = $shareData->pluck('task_name')->toArray();
            $contribSeries = collect($memberNames)->map(function ($name) use ($shareData) {
                return [
                    'name' => $name,
                    'data' => $shareData->map(function ($tg) use ($name) {
                        return round($tg['weeks']->avg(fn($wk) => $wk['members'][$name]['share'] ?? 0), 1);
                    })->toArray(),
                ];
            })->values()->toArray();
        @endphp
        <div class="rpt-section anim-up d5">
            <div class="rpt-section-header">
                <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-chart-pie"></i></div>
                <h3>Avg Contribution % by Task</h3>
                <div class="rpt-section-line"></div>
                <span style="font-size:0.7rem;color:var(--muted-foreground);font-weight:500;">Average % contribution per member per task type</span>
            </div>
            <div class="rpt-chart-card" style="margin-bottom:1rem;">
                <div class="rpt-chart-body">
                    <div id="contribChart" style="height:280px;"></div>
                </div>
            </div>
            <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-table-list"></i></div>
                <h3>Contribution Analysis</h3>
                <div class="rpt-section-line"></div>
            </div>
            <div class="rpt-table-card">
                <div style="overflow-x:auto;">
                <table class="wt">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach($memberNames as $m)
                            <th colspan="2" style="text-align:center;border-left:2px solid var(--border);font-size:0.68rem;text-transform:uppercase;">{{ $m }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th></th>
                            @foreach($memberNames as $m)
                            <th class="num" style="font-size:0.52rem;">TASKS</th>
                            <th class="num" style="font-size:0.52rem;border-right:2px solid var(--border);">SHARE</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shareData as $taskGroup)
                        <tr style="background:var(--muted);">
                            <td colspan="{{ count($memberNames)*2+1 }}" style="font-weight:800;font-size:0.68rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--gray-500);padding:0.4rem 0.75rem;">{{ $taskGroup['task_name'] }}</td>
                        </tr>
                        @foreach($taskGroup['weeks'] as $wk)
                        <tr>
                            <td style="font-weight:700;color:var(--gray-400);font-size:0.73rem;">W{{ $wk['week_num'] }}</td>
                            @foreach($memberNames as $m)
                            @php $md = $wk['members'][$m] ?? ['tasks'=>0,'share'=>0]; @endphp
                            <td class="num" style="font-weight:700;">{{ $md['tasks'] }}</td>
                            <td class="num" style="border-right:2px solid var(--border);font-size:0.73rem;">{{ number_format($md['share'],2) }}%</td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        @endif

        @else
        <div class="rpt-table-card anim-up d3">
            <div class="empty-state"><i class="fas fa-inbox"></i>No data for this month</div>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════════════
         MONTHLY TAB
    ════════════════════════════════════════════════════════════════ --}}
    <div class="tab-content" id="tab-monthly">
    @php
        $mTotalAll = $allMonths->sum('total');
        $taskLabelsMonthly = $isAllRoles ? $genericLabels : $taskLabels;
    @endphp

    @if($allMonths->count())

        <!-- Monthly KPIs -->
        <div class="rpt-kpi-grid anim-up d2">
            <div class="rpt-kpi-card">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-label">Total (All Months)</span>
                    <div class="rpt-kpi-icon" style="background:var(--primary);"><i class="fas fa-layer-group"></i></div>
                </div>
                <div class="rpt-kpi-value">{{ number_format($mTotalAll) }}</div>
                <div class="rpt-kpi-sub">{{ $allMonths->count() }} month{{ $allMonths->count()>1?'s':'' }} of data</div>
            </div>
            <div class="rpt-kpi-card">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-label">Avg / Month</span>
                    <div class="rpt-kpi-icon" style="background:var(--success);"><i class="fas fa-calculator"></i></div>
                </div>
                <div class="rpt-kpi-value">{{ $allMonths->count()>0 ? round($mTotalAll/$allMonths->count()) : 0 }}</div>
                <div class="rpt-kpi-sub">tasks per month</div>
            </div>
            <div class="rpt-kpi-card">
                <div class="rpt-kpi-top">
                    <span class="rpt-kpi-label">Best Month</span>
                    <div class="rpt-kpi-icon" style="background:var(--success);"><i class="fas fa-arrow-trend-up"></i></div>
                </div>
                <div class="rpt-kpi-value" style="font-size:1.2rem;">{{ $allMonths->sortByDesc('total')->first()['short'] ?? '—' }}</div>
                <div class="rpt-kpi-sub">{{ number_format($allMonths->sortByDesc('total')->first()['total'] ?? 0) }} tasks</div>
            </div>
        </div>

        @if($isAllRoles)
        {{-- ══ ALL ROLES: Per-role monthly sections ══ --}}
        @php $selectedMonthKey = \Carbon\Carbon::parse($month)->format('Y-m'); @endphp
        @foreach($roleGroupedData as $role => $rd)
        @php
            $rdColor      = $rd['color'];
            $rdLabel      = $rd['label'];
            $rdTaskLabels = $rd['taskLabels'];
            $rdAllMonths  = $rd['allMonths'];
            $rdMonthlyMembers = collect($rd['memberMonthly'])->get($selectedMonthKey, collect())->sortByDesc('total');
            $rdMonthlyTotal   = $rdMonthlyMembers->sum('total');
        @endphp
        <div class="rpt-role-panel {{ $loop->first ? 'active' : '' }}" data-role="{{ $role }}" style="border-top:3px solid {{ $rdColor }};margin-bottom:0;background:var(--card);border-radius:8px;border:1px solid var(--border);overflow:hidden;">
            <div style="padding:0.875rem 1.25rem;border-bottom:1px solid var(--muted);display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.625rem;">
                    <span class="role-badge {{ $role }}">{{ $rdLabel }}</span>
                    <span style="font-size:0.72rem;color:var(--muted-foreground);font-weight:500;">{{ $rdAllMonths->count() }} month{{ $rdAllMonths->count()!=1?'s':'' }} of data</span>
                </div>
                <a href="{{ route('admin.reports', ['role' => $role, 'month' => $month]) }}" class="rpt-role-link">
                    Full Report <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
                </a>
            </div>
            <div style="padding:1.25rem;">

                {{-- Monthly Trend + Task Composition charts --}}
                @if($rdAllMonths->count())
                <div class="rpt-charts-grid" style="margin-bottom:1.5rem;">
                    <div class="rpt-chart-card">
                        <div class="rpt-chart-header">
                            <div class="rpt-chart-icon" style="background:{{ $rdColor }};"><i class="fas fa-chart-line"></i></div>
                            <h4>Monthly Trend</h4>
                        </div>
                        <div class="rpt-chart-body">
                            <div id="monthlyTrendChart-{{ $role }}" style="height:220px;"></div>
                        </div>
                    </div>
                    <div class="rpt-chart-card">
                        <div class="rpt-chart-header">
                            <div class="rpt-chart-icon" style="background:var(--primary);"><i class="fas fa-chart-bar"></i></div>
                            <h4>Task Composition</h4>
                        </div>
                        <div class="rpt-chart-body">
                            <div id="compositionChart-{{ $role }}" style="height:220px;"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Team Performance (selected month) --}}
                @if($rdMonthlyMembers->count())
                <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                    <div class="rpt-section-icon" style="background:var(--success);"><i class="fas fa-users"></i></div>
                    <h3>Team Performance</h3>
                    <div class="rpt-section-line"></div>
                    <span style="font-size:0.7rem;color:var(--muted-foreground);font-weight:500;">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</span>
                </div>
                <div class="rpt-perf-grid" style="margin-bottom:1.5rem;">
                    @foreach($rdMonthlyMembers as $mp)
                    @php
                        $mpPct = $rdMonthlyTotal > 0 ? round($mp['total']/$rdMonthlyTotal*100,1) : 0;
                        $taskMaxVal = max([$mp['t1'],$mp['t2'],$mp['t3'],$mp['t4'],$mp['t5'],1]);
                    @endphp
                    <div class="rpt-perf-card">
                        <div class="mpc-top">
                            <img class="mpc-avatar" src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($mp['username'], $mp['gender'] ?? 'male') }}" alt="">
                            <div class="mpc-info">
                                <div class="mpc-name">{{ $mp['first_name'] }}</div>
                                <div class="mpc-total">{{ number_format($mp['total']) }} tasks</div>
                            </div>
                            <div class="mpc-share">
                                <span class="mpc-share-val">{{ $mpPct }}%</span>
                                <span class="mpc-share-label">share</span>
                            </div>
                        </div>
                        <div class="mpc-divider"></div>
                        <div class="mpc-tasks">
                            @foreach(['t1','t2','t3','t4','t5'] as $i => $tk)
                            @php $tv = $mp[$tk]; $tBar = $taskMaxVal > 0 ? round($tv/$taskMaxVal*100) : 0; @endphp
                            <div class="mpc-task">
                                <div class="mpc-task-dot" style="background:{{ $taskColorMap[$i] }};"></div>
                                <span class="mpc-task-label">{{ $rdTaskLabels['task_'.($i+1)] }}</span>
                                <div class="mpc-task-bar-wrap"><div class="mpc-task-bar" style="width:{{ $tBar }}%;background:{{ $taskColorMap[$i] }};"></div></div>
                                <span class="mpc-task-val">{{ $tv }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Monthly Summary table --}}
                @if($rdAllMonths->count())
                <div class="rpt-section-header" style="margin-bottom:0.75rem;">
                    <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-calendar-days"></i></div>
                    <h3>Monthly Summary</h3>
                    <div class="rpt-section-line"></div>
                </div>
                <div class="rpt-table-card">
                    <div style="overflow-x:auto;">
                    <table class="wt">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="num">{{ $rdTaskLabels['task_1'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_2'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_3'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_4'] }}</th>
                                <th class="num">{{ $rdTaskLabels['task_5'] }}</th>
                                <th class="num" style="border-left:2px solid var(--border);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rdYt=[0,0,0,0,0]; @endphp
                            @foreach($rdAllMonths as $ym)
                            @php foreach(['t1','t2','t3','t4','t5'] as $i=>$k) $rdYt[$i]+=$ym[$k]; @endphp
                            <tr @if($ym['month'] === $selectedMonthKey) style="background:#f0f9ff;" @endif>
                                <td style="font-weight:600;">{{ $ym['label'] }}</td>
                                <td class="num">{{ $ym['t1'] ?: '—' }}</td>
                                <td class="num">{{ $ym['t2'] ?: '—' }}</td>
                                <td class="num">{{ $ym['t3'] ?: '—' }}</td>
                                <td class="num">{{ $ym['t4'] ?: '—' }}</td>
                                <td class="num">{{ $ym['t5'] ?: '—' }}</td>
                                <td class="num" style="border-left:2px solid var(--border);font-weight:700;">{{ $ym['total'] ?: '—' }}</td>
                            </tr>
                            @endforeach
                            <tr class="month-total">
                                <td style="font-weight:800;">Total</td>
                                @foreach($rdYt as $v)<td class="num">{{ $v }}</td>@endforeach
                                <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($rdYt) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endforeach

        @else
        {{-- ══ SINGLE ROLE: Monthly Charts + Team Performance + Year Overview ══ --}}

        <!-- Monthly Charts -->
        <div class="rpt-charts-grid anim-up d3">
            <div class="rpt-chart-card">
                <div class="rpt-chart-header">
                    <div class="rpt-chart-icon" style="background:var(--primary);"><i class="fas fa-chart-line"></i></div>
                    <h4>Monthly Trend</h4>
                </div>
                <div class="rpt-chart-body">
                    <div id="monthlyTrendChart" style="height:240px;"></div>
                </div>
            </div>
            <div class="rpt-chart-card">
                <div class="rpt-chart-header">
                    <div class="rpt-chart-icon" style="background:var(--primary);"><i class="fas fa-chart-bar"></i></div>
                    <h4>Task Composition</h4>
                </div>
                <div class="rpt-chart-body">
                    <div id="compositionChart" style="height:240px;"></div>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        @php
            $selectedMonthKey     = \Carbon\Carbon::parse($month)->format('Y-m');
            $selectedMonthMembers = $memberMonthly->get($selectedMonthKey, collect())->sortByDesc('total');
            $totalMonthTasks      = $selectedMonthMembers->sum('total');
        @endphp
        @if($selectedMonthMembers->count())
        <div class="rpt-section anim-up d4">
            <div class="rpt-section-header">
                <div class="rpt-section-icon" style="background:var(--success);"><i class="fas fa-users"></i></div>
                <h3>Team Performance</h3>
                <div class="rpt-section-line"></div>
                <span style="font-size:0.7rem;color:var(--muted-foreground);font-weight:500;">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</span>
            </div>
            <div class="rpt-perf-grid">
                @foreach($selectedMonthMembers as $mp)
                @php
                    $mpPct = $totalMonthTasks > 0 ? round($mp['total']/$totalMonthTasks*100,1) : 0;
                    $taskMaxVal = max([$mp['t1'],$mp['t2'],$mp['t3'],$mp['t4'],$mp['t5'],1]);
                @endphp
                <div class="rpt-perf-card">
                    <div class="mpc-top">
                        <img class="mpc-avatar" src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($mp['username'], $mp['gender'] ?? 'male') }}" alt="">
                        <div class="mpc-info">
                            <div class="mpc-name">{{ $mp['first_name'] }}</div>
                            <div class="mpc-total">{{ number_format($mp['total']) }} tasks</div>
                        </div>
                        <div class="mpc-share">
                            <span class="mpc-share-val">{{ $mpPct }}%</span>
                            <span class="mpc-share-label">share</span>
                        </div>
                    </div>
                    <div class="mpc-divider"></div>
                    <div class="mpc-tasks">
                        @foreach(['t1','t2','t3','t4','t5'] as $i => $tk)
                        @php $tv = $mp[$tk]; $tBar = $taskMaxVal > 0 ? round($tv/$taskMaxVal*100) : 0; @endphp
                        <div class="mpc-task">
                            <div class="mpc-task-dot" style="background:{{ $taskColorMap[$i] }};"></div>
                            <span class="mpc-task-label">{{ $taskLabels['task_'.($i+1)] }}</span>
                            <div class="mpc-task-bar-wrap"><div class="mpc-task-bar" style="width:{{ $tBar }}%;background:{{ $taskColorMap[$i] }};"></div></div>
                            <span class="mpc-task-val">{{ $tv }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Year Overview -->
        <div class="rpt-section anim-up d4">
            <div class="rpt-section-header">
                <div class="rpt-section-icon" style="background:var(--primary);"><i class="fas fa-calendar-days"></i></div>
                <h3>{{ $selectedYear }} Overview</h3>
                <div class="rpt-section-line"></div>
            </div>
            <div class="rpt-table-card">
                <div style="overflow-x:auto;">
                <table class="wt">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="num">{{ $taskLabelsMonthly['task_1'] }}</th>
                            <th class="num">{{ $taskLabelsMonthly['task_2'] }}</th>
                            <th class="num">{{ $taskLabelsMonthly['task_3'] }}</th>
                            <th class="num">{{ $taskLabelsMonthly['task_4'] }}</th>
                            <th class="num">{{ $taskLabelsMonthly['task_5'] }}</th>
                            <th class="num" style="border-left:2px solid var(--border);">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $yt=[0,0,0,0,0]; @endphp
                        @foreach($yearOverview as $ym)
                        @php foreach(['t1','t2','t3','t4','t5'] as $i=>$k) $yt[$i]+=$ym[$k]; @endphp
                        <tr @if(\Carbon\Carbon::parse($month)->format('F') === $ym['month']) style="background:#f0f9ff;" @endif>
                            <td style="font-weight:600;">{{ $ym['month'] }}</td>
                            <td class="num">{{ $ym['t1'] ?: '—' }}</td>
                            <td class="num">{{ $ym['t2'] ?: '—' }}</td>
                            <td class="num">{{ $ym['t3'] ?: '—' }}</td>
                            <td class="num">{{ $ym['t4'] ?: '—' }}</td>
                            <td class="num">{{ $ym['t5'] ?: '—' }}</td>
                            <td class="num" style="border-left:2px solid var(--border);font-weight:700;">{{ $ym['total'] ?: '—' }}</td>
                        </tr>
                        @endforeach
                        <tr class="month-total">
                            <td style="font-weight:800;">Total</td>
                            @foreach($yt as $v)<td class="num">{{ $v }}</td>@endforeach
                            <td class="num" style="border-left:2px solid rgba(255,255,255,0.2);">{{ array_sum($yt) }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        @endif

    @else
    <div class="rpt-table-card anim-up d2">
        <div class="empty-state"><i class="fas fa-inbox"></i>No monthly data available</div>
    </div>
    @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.rpt-tab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
}
var _rptInits = {};
var _rptDone  = {};
function switchRole(role) {
    document.querySelectorAll('.rpt-role-nav-btn').forEach(function(b) {
        b.classList.toggle('active', b.dataset.role === role);
    });
    document.querySelectorAll('.rpt-role-panel').forEach(function(p) {
        p.classList.toggle('active', p.dataset.role === role);
    });
    if (typeof _rptInits !== 'undefined' && _rptInits[role] && !_rptDone[role]) {
        _rptInits[role]();
        _rptDone[role] = true;
    }
}
function updateParam(key, value) {
    var url = new URL(window.location);
    url.searchParams.set(key, value);
    return url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    var roleColorMap = {
        content:    '#0ea5e9',
        researcher: '#10b981',
        graphics:   '#f59e0b',
        backend:    '#f43f5e',
    };
    var colors       = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#f43f5e'];
    var isAllRoles   = {{ $isAllRoles ? 'true' : 'false' }};

    // ── Role Overview mini charts (All Roles only) ───────────────
    @if($isAllRoles)
    var roleMonthTotals = {!! json_encode($roleMonthTotals) !!};
    var roleRows        = {!! json_encode($rowsByRole->map(fn($rows) => $rows->groupBy('week_num')->map(fn($wk) => $wk->sum('task_1')+$wk->sum('task_2')+$wk->sum('task_3')+$wk->sum('task_4')+$wk->sum('task_5'))->sortKeys()->values())->toArray()) !!};

    Object.keys(roleRows).forEach(function(role) {
        var el = document.getElementById('rptRoleChart-' + role);
        if (!el) return;
        var c = roleColorMap[role] || '#6366f1';
        new ApexCharts(el, {
            chart: { type:'bar', height:70, toolbar:{show:false}, sparkline:{enabled:false}, fontFamily:'Inter', foreColor:'#94a3b8' },
            series: [{ name:'Tasks', data: roleRows[role] }],
            colors: [c],
            plotOptions: { bar: { columnWidth:'65%', borderRadius:2, borderRadiusApplication:'end' } },
            xaxis: { labels:{show:false}, axisBorder:{show:false}, axisTicks:{show:false} },
            yaxis: { show:false, min:0 },
            grid:  { show:false, padding:{left:0,right:0,top:0,bottom:-10} },
            dataLabels: { enabled:false },
            tooltip: { theme:'light', style:{fontSize:'12px',fontFamily:'Inter'}, y:{ formatter:function(v){ return v+' tasks'; } } }
        }).render();
    });

    // ── Per-role charts: lazy-init registry (fixes hidden-element + data bugs) ──
    @foreach($roleGroupedData as $role => $rd)
    @php
        $rdWeekLabels = collect($rd['weeks'])->pluck('week_num')->map(fn($w) => 'Week '.$w)->toArray();
        $rdWeekTotals = collect($rd['weeks'])->map(fn($w) => $w['total_t1']+$w['total_t2']+$w['total_t3']+$w['total_t4']+$w['total_t5'])->toArray();
        $rdLeader = [];
        foreach ($rd['weeks'] as $wk) {
            foreach ($wk['days'] as $day) {
                foreach ($day['members'] as $m) {
                    $t = $m['task_1']+$m['task_2']+$m['task_3']+$m['task_4']+$m['task_5'];
                    $rdLeader[$m['first_name']] = ($rdLeader[$m['first_name']] ?? 0) + $t;
                }
            }
        }
        arsort($rdLeader);
        $rdLeader = array_slice($rdLeader, 0, 8, true);
        // Recompute contrib data fresh for this role (avoids bleed from HTML loop)
        $rdShareData   = $rd['shareData'];
        $rdMemberNames = $rd['memberNames'];
        if ($rdShareData->count() && count($rdMemberNames)) {
            $rdContribLabels = $rdShareData->pluck('task_name')->toArray();
            $rdContribSeries = collect($rdMemberNames)->map(fn($mn) => [
                'name' => $mn,
                'data' => $rdShareData->map(fn($tg) => round($tg['weeks']->avg(fn($wk) => ($wk['members'][$mn]['share'] ?? 0)), 1))->toArray(),
            ])->values()->toArray();
        } else {
            $rdContribLabels = [];
            $rdContribSeries = [];
        }
    @endphp
    _rptInits['{{ $role }}'] = function() {
        var c = '{{ $rd['color'] }}';
        var ccColors = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#f43f5e','#8b5cf6','#ec4899','#14b8a6'];

        var tEl = document.getElementById('trendChart-{{ $role }}');
        if (tEl) {
            var tData   = {!! json_encode($rdWeekTotals) !!};
            var tLabels = {!! json_encode($rdWeekLabels) !!};
            if (tData.length) {
                new ApexCharts(tEl, {
                    chart: { type:'bar', height:200, fontFamily:'Inter', foreColor:'#64748b', toolbar:{show:false} },
                    series: [{ name:'Tasks', data:tData }],
                    colors: [c],
                    plotOptions: { bar: { columnWidth:'60%', borderRadius:4, borderRadiusApplication:'end' } },
                    xaxis: { categories:tLabels, labels:{style:{fontWeight:600,fontSize:'11px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                    yaxis: { labels:{style:{fontWeight:500,fontSize:'11px',colors:'#94a3b8'},padding:4}, tickAmount:4 },
                    grid: { borderColor:'#f1f5f9', padding:{left:8} },
                    dataLabels: { enabled:false },
                    tooltip: { y:{ formatter:function(v){ return v+' tasks'; } } }
                }).render();
            }
        }

        var lEl = document.getElementById('leaderboardChart-{{ $role }}');
        if (lEl) {
            var lLabels = {!! json_encode(array_keys($rdLeader)) !!};
            var lData   = {!! json_encode(array_values($rdLeader)) !!};
            if (lData.length) {
                new ApexCharts(lEl, {
                    chart: { type:'bar', height:200, fontFamily:'Inter', foreColor:'#64748b', toolbar:{show:false} },
                    series: [{ name:'Tasks', data:lData }],
                    colors: [c],
                    plotOptions: { bar: { borderRadius:4, horizontal:true, barHeight:'60%' } },
                    xaxis: { labels:{style:{fontWeight:500,fontSize:'11px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                    yaxis: { labels:{style:{fontWeight:600,fontSize:'11px',colors:'#64748b'}} },
                    grid: { borderColor:'#f1f5f9', xaxis:{lines:{show:false}}, yaxis:{lines:{show:false}} },
                    dataLabels: { enabled:false },
                    tooltip: { y:{ formatter:function(v){ return v+' tasks'; } } },
                    legend: { show:false }
                }).render();
            }
        }

        var ccEl = document.getElementById('contribChart-{{ $role }}');
        if (ccEl) {
            var ccLabels = {!! json_encode($rdContribLabels) !!};
            var ccSeries = {!! json_encode($rdContribSeries) !!};
            if (ccLabels.length && ccSeries.length) {
                new ApexCharts(ccEl, {
                    chart: { type:'bar', height:260, toolbar:{show:false}, fontFamily:'Inter', foreColor:'#64748b' },
                    series: ccSeries,
                    colors: ccColors,
                    plotOptions: { bar: { borderRadius:4, columnWidth:'70%' } },
                    xaxis: { categories:ccLabels, labels:{style:{fontWeight:600,fontSize:'11px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                    yaxis: { labels:{style:{fontWeight:500,fontSize:'11px',colors:'#94a3b8'}, formatter:function(v){ return v+'%'; }, max:100}, tickAmount:5 },
                    grid: { borderColor:'#f1f5f9', padding:{left:8} },
                    legend: { position:'bottom', labels:{colors:'#64748b',useSeriesColors:true,fontWeight:600,fontSize:'11px'}, markers:{width:10,height:10,radius:3,strokeWidth:0}, itemMargin:{horizontal:6,vertical:2} },
                    dataLabels: { enabled:false },
                    tooltip: { y:{ formatter:function(v){ return v+'%'; } } }
                }).render();
            }
        }

        var mEl = document.getElementById('monthlyTrendChart-{{ $role }}');
        if (mEl) {
            var mLabels = {!! json_encode(collect($rd['allMonths'])->pluck('short')->toArray()) !!};
            var mData   = {!! json_encode(collect($rd['allMonths'])->pluck('total')->toArray()) !!};
            if (mData.length) {
                new ApexCharts(mEl, {
                    chart: { type:'bar', height:220, fontFamily:'Inter', foreColor:'#64748b', toolbar:{show:false} },
                    series: [{ name:'Tasks', data:mData }],
                    colors: [c],
                    plotOptions: { bar: { columnWidth:'55%', borderRadius:4, borderRadiusApplication:'end' } },
                    xaxis: { categories:mLabels, labels:{style:{fontWeight:600,fontSize:'11px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                    yaxis: { labels:{style:{fontWeight:500,fontSize:'11px',colors:'#94a3b8'},padding:4}, tickAmount:4 },
                    grid: { borderColor:'#f1f5f9', padding:{left:8} },
                    dataLabels: { enabled:false },
                    tooltip: { y:{ formatter:function(v){ return v.toLocaleString()+' tasks'; } } }
                }).render();
            }
        }

        var cpEl = document.getElementById('compositionChart-{{ $role }}');
        if (cpEl) {
            var cpLabels = {!! json_encode(collect($rd['allMonths'])->pluck('short')->toArray()) !!};
            var cpNames  = {!! json_encode(array_values($rd['taskLabels'])) !!};
            if (cpLabels.length) {
                new ApexCharts(cpEl, {
                    chart: { type:'bar', height:220, fontFamily:'Inter', foreColor:'#64748b', stacked:true, toolbar:{show:false} },
                    series: [
                        { name:cpNames[0], data:{!! json_encode(collect($rd['allMonths'])->pluck('t1')->toArray()) !!} },
                        { name:cpNames[1], data:{!! json_encode(collect($rd['allMonths'])->pluck('t2')->toArray()) !!} },
                        { name:cpNames[2], data:{!! json_encode(collect($rd['allMonths'])->pluck('t3')->toArray()) !!} },
                        { name:cpNames[3], data:{!! json_encode(collect($rd['allMonths'])->pluck('t4')->toArray()) !!} },
                        { name:cpNames[4], data:{!! json_encode(collect($rd['allMonths'])->pluck('t5')->toArray()) !!} }
                    ],
                    colors: ['#6366f1','#0ea5e9','#10b981','#f59e0b','#f43f5e'],
                    plotOptions: { bar: { columnWidth:'55%', borderRadius:{topLeft:4,topRight:4} } },
                    xaxis: { categories:cpLabels, labels:{style:{fontWeight:600,fontSize:'11px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                    yaxis: { labels:{style:{fontWeight:500,fontSize:'11px',colors:'#94a3b8'},padding:4}, tickAmount:4 },
                    grid: { borderColor:'#f1f5f9', padding:{left:8} },
                    legend: { position:'bottom', labels:{colors:'#64748b',useSeriesColors:true,fontWeight:600,fontSize:'11px'}, markers:{width:10,height:10,radius:3,strokeWidth:0}, itemMargin:{horizontal:6,vertical:2} },
                    dataLabels: { enabled:false },
                    tooltip: { y:{ formatter:function(v){ return v+' tasks'; } } }
                }).render();
            }
        }
    };
    @endforeach
    // init only the default active role immediately
    var _activeRoleBtn = document.querySelector('.rpt-role-nav-btn.active');
    if (_activeRoleBtn && _rptInits[_activeRoleBtn.dataset.role]) {
        _rptInits[_activeRoleBtn.dataset.role]();
        _rptDone[_activeRoleBtn.dataset.role] = true;
    }
    @endif

    // ── Single Role: Weekly Trend + Leaderboard + Monthly charts ─
    @if(!$isAllRoles)
    var trendEl = document.getElementById('trendChart');
    if (trendEl) {
        var tLabels = {!! json_encode($weeks->pluck('week_num')->map(fn($w) => 'Week '.$w)->toArray()) !!};
        var tData   = {!! json_encode($weeks->map(fn($w) => $w['total_t1']+$w['total_t2']+$w['total_t3']+$w['total_t4']+$w['total_t5'])->toArray()) !!};
        if (tData.length > 0) {
            new ApexCharts(trendEl, {
                chart: { type:'bar', height:240, fontFamily:'Inter', foreColor:'#64748b', toolbar:{show:false} },
                series: [{ name:'Tasks', data:tData }],
                colors: ['{{ $roleColorMap[$roleFilter] ?? '#6366f1' }}'],
                plotOptions: { bar: { columnWidth:'60%', borderRadius:4, borderRadiusApplication:'end' } },
                xaxis: { categories:tLabels, labels:{style:{fontWeight:600,fontSize:'12px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                yaxis: { labels:{style:{fontWeight:500,fontSize:'12px',colors:'#94a3b8'},padding:4}, tickAmount:4 },
                grid: { borderColor:'#f1f5f9', padding:{left:8} },
                dataLabels: { enabled:false },
                tooltip: { y:{ formatter:function(v){ return v+' tasks'; } } }
            }).render();
        }
    }

    var leaderEl = document.getElementById('leaderboardChart');
    if (leaderEl) {
        @php
            $leaderboard = collect();
            foreach ($allRows as $row) {
                $total = $row['task_1'] + $row['task_2'] + $row['task_3'] + $row['task_4'] + $row['task_5'];
                $key = $row['first_name'];
                $leaderboard[$key] = ($leaderboard[$key] ?? 0) + $total;
            }
            $leaderboard = collect($leaderboard)->sortDesc()->take(8);
        @endphp
        var lLabels = {!! json_encode($leaderboard->keys()->toArray()) !!};
        var lData   = {!! json_encode($leaderboard->values()->toArray()) !!};
        if (lData.length > 0) {
            new ApexCharts(leaderEl, {
                chart: { type:'bar', height:240, fontFamily:'Inter', foreColor:'#64748b', toolbar:{show:false} },
                series: [{ name:'Tasks', data:lData }],
                colors: ['{{ $roleColorMap[$roleFilter] ?? '#6366f1' }}'],
                plotOptions: { bar: { borderRadius:4, horizontal:true, barHeight:'60%' } },
                xaxis: { labels:{style:{fontWeight:500,fontSize:'12px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                yaxis: { labels:{style:{fontWeight:600,fontSize:'12px',colors:'#64748b'}} },
                grid: { borderColor:'#f1f5f9', xaxis:{lines:{show:false}}, yaxis:{lines:{show:false}} },
                dataLabels: { enabled:false },
                tooltip: { y:{ formatter:function(v){ return v+' tasks'; } } },
                legend: { show:false }
            }).render();
        }
    }

    var mTrendEl = document.getElementById('monthlyTrendChart');
    if (mTrendEl) {
        var mLabels = {!! json_encode($allMonths->pluck('short')->toArray()) !!};
        var mData   = {!! json_encode($allMonths->pluck('total')->toArray()) !!};
        if (mData.length > 0) {
            new ApexCharts(mTrendEl, {
                chart: { type:'bar', height:240, fontFamily:'Inter', foreColor:'#64748b', toolbar:{show:false} },
                series: [{ name:'Tasks', data:mData }],
                colors: ['{{ $roleColorMap[$roleFilter] ?? '#6366f1' }}'],
                plotOptions: { bar: { columnWidth:'55%', borderRadius:4, borderRadiusApplication:'end' } },
                xaxis: { categories:mLabels, labels:{style:{fontWeight:600,fontSize:'12px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                yaxis: { labels:{style:{fontWeight:500,fontSize:'12px',colors:'#94a3b8'},padding:4}, tickAmount:4 },
                grid: { borderColor:'#f1f5f9', padding:{left:8} },
                dataLabels: { enabled:false },
                tooltip: { y:{ formatter:function(v){ return v.toLocaleString()+' tasks'; } } }
            }).render();
        }
    }

    var compEl = document.getElementById('compositionChart');
    if (compEl) {
        var cLabels = {!! json_encode($allMonths->pluck('short')->toArray()) !!};
        var cNames  = {!! json_encode([$taskLabelsMonthly['task_1']??'Task 1',$taskLabelsMonthly['task_2']??'Task 2',$taskLabelsMonthly['task_3']??'Task 3',$taskLabelsMonthly['task_4']??'Task 4',$taskLabelsMonthly['task_5']??'Task 5']) !!};
        if (cLabels.length > 0) {
            new ApexCharts(compEl, {
                chart: { type:'bar', height:240, fontFamily:'Inter', foreColor:'#64748b', stacked:true, toolbar:{show:false} },
                series: [
                    { name:cNames[0], data:{!! json_encode($allMonths->pluck('t1')->toArray()) !!} },
                    { name:cNames[1], data:{!! json_encode($allMonths->pluck('t2')->toArray()) !!} },
                    { name:cNames[2], data:{!! json_encode($allMonths->pluck('t3')->toArray()) !!} },
                    { name:cNames[3], data:{!! json_encode($allMonths->pluck('t4')->toArray()) !!} },
                    { name:cNames[4], data:{!! json_encode($allMonths->pluck('t5')->toArray()) !!} }
                ],
                colors: colors,
                plotOptions: { bar: { columnWidth:'55%', borderRadius:{topLeft:4,topRight:4} } },
                xaxis: { categories:cLabels, labels:{style:{fontWeight:600,fontSize:'12px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                yaxis: { labels:{style:{fontWeight:500,fontSize:'12px',colors:'#94a3b8'},padding:4}, tickAmount:4 },
                grid: { borderColor:'#f1f5f9', padding:{left:8} },
                legend: { position:'bottom', labels:{colors:'#64748b',useSeriesColors:true,fontWeight:600,fontSize:'11px'}, markers:{width:10,height:10,radius:3,strokeWidth:0}, itemMargin:{horizontal:6,vertical:2} },
                dataLabels: { enabled:false },
                tooltip: { y:{ formatter:function(v){ return v+' tasks'; } } }
            }).render();
        }
    }

    @if(isset($contribLabels))
    var contribEl = document.getElementById('contribChart');
    if (contribEl) {
        var cCLabels = {!! json_encode($contribLabels) !!};
        var cCSeries = {!! json_encode($contribSeries) !!};
        var cColors  = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#f43f5e','#8b5cf6','#ec4899','#14b8a6'];
        if (cCLabels.length > 0 && cCSeries.length > 0) {
            new ApexCharts(contribEl, {
                chart: { type:'bar', height:280, toolbar:{show:false}, fontFamily:'Inter', foreColor:'#64748b' },
                series: cCSeries,
                colors: cColors,
                plotOptions: { bar: { borderRadius:4, columnWidth:'70%' } },
                xaxis: { categories:cCLabels, labels:{style:{fontWeight:600,fontSize:'11px',colors:'#94a3b8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
                yaxis: { labels:{style:{fontWeight:500,fontSize:'11px',colors:'#94a3b8'}, formatter:function(v){ return v+'%'; }, max:100}, tickAmount:5 },
                grid: { borderColor:'#f1f5f9', padding:{left:8} },
                legend: { position:'bottom', labels:{colors:'#64748b',useSeriesColors:true,fontWeight:600,fontSize:'11px'}, markers:{width:10,height:10,radius:3,strokeWidth:0}, itemMargin:{horizontal:6,vertical:2} },
                dataLabels: { enabled:false },
                tooltip: { y:{ formatter:function(v){ return v+'%'; } } }
            }).render();
        }
    }
    @endif
    @endif
});
</script>
@endsection
