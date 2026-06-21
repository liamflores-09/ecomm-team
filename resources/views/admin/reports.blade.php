@extends('layouts.app')

@section('title', 'Reports — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    .report-controls { display: flex; align-items: center; gap: 1rem; margin: 1.5rem 0; flex-wrap: wrap; }
    .report-tabs { display: flex; gap: 0; border: 2px solid var(--border); border-radius: 8px; overflow: hidden; }
    .report-tab {
        padding: 0.5rem 1.25rem; font-size: 0.8rem; font-weight: 700; cursor: pointer;
        transition: all 0.15s; border: none; background: var(--white); color: var(--gray-400);
        font-family: var(--p-font-family-sans); border-right: 2px solid var(--border);
    }
    .report-tab:last-child { border-right: none; }
    .report-tab:hover { background: var(--muted); color: var(--fg); }
    .report-tab.active { background: var(--primary); color: white; }

    .month-select {
        height: 36px; padding: 0 2rem 0 0.75rem; background: var(--white); border: 2px solid var(--border);
        border-radius: 8px; font-size: 0.8rem; font-weight: 600; color: var(--fg); cursor: pointer;
        outline: none; appearance: auto; font-family: var(--p-font-family-sans);
    }
    .month-select:focus { border-color: var(--ring); }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* KPI Cards */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .kpi-card { background: var(--white); border-radius: 12px; padding: 1.25rem; border: 1px solid var(--border); }
    .kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-400); }
    .kpi-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; }
    .kpi-value { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 0.125rem; }
    .kpi-sub { font-size: 0.75rem; color: var(--gray-400); font-weight: 500; }

    /* Charts Grid */
    .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
    .chart-card {
        background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden;
    }
    .chart-card-header {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--muted);
    }
    .chart-card-header .cc-icon {
        width: 28px; height: 28px; border-radius: 6px; display: flex;
        align-items: center; justify-content: center; color: white; font-size: 0.7rem; flex-shrink: 0;
    }
    .chart-card-header h4 { font-size: 0.8rem; font-weight: 700; margin: 0; }
    .chart-card-body { padding: 1rem 1.25rem; }

    /* Section */
    .section-header {
        display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;
    }
    .section-header .sh-icon {
        width: 28px; height: 28px; border-radius: 6px; display: flex;
        align-items: center; justify-content: center; color: white; font-size: 0.7rem; flex-shrink: 0;
    }
    .section-header h3 { font-size: 0.9rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .section-header .sh-line { flex: 1; height: 1px; background: var(--border); margin-left: 0.5rem; }

    /* Tables */
    .table-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 1rem; }
    .table-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.75rem 1rem; border-bottom: 1px solid var(--muted);
    }
    .table-header .th-left { display: flex; align-items: center; gap: 0.5rem; }
    .table-header .th-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; flex-shrink: 0; }
    .table-header h4 { font-size: 0.8rem; font-weight: 700; margin: 0; }

    .wt { width: 100%; border-collapse: collapse; }
    .wt thead th {
        padding: 0.5rem 0.75rem; font-size: 0.6rem; font-weight: 700; color: var(--gray-400);
        background: var(--muted); border-bottom: 1px solid var(--border); text-align: left;
        text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap;
    }
    .wt thead th.num { text-align: center; }
    .wt tbody td {
        padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--border); font-size: 0.8rem;
    }
    .wt tbody td.num { text-align: center; font-variant-numeric: tabular-nums; font-weight: 600; }
    .wt tbody tr:last-child td { border-bottom: none; }
    .wt tbody tr:hover td { background: #FAFAFA; }
    .wt tbody tr.total-row:hover td,
    .wt tbody tr.month-total:hover td { background: inherit; }

    .wt .week-sep td {
        background: var(--muted); font-weight: 800; font-size: 0.7rem;
        text-transform: uppercase; letter-spacing: 0.04em; color: var(--gray-500);
        padding: 0.375rem 0.75rem; border-bottom: 1px solid var(--border);
    }
    .wt .total-row td {
        background: var(--fg); color: white; font-weight: 800; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.04em; padding: 0.5rem 0.75rem;
    }
    .wt .month-total td {
        background: var(--indigo); color: white; font-weight: 800; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.04em; padding: 0.625rem 0.75rem;
    }

    /* Member Performance Cards */
    .member-perf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .member-perf-card {
        background: var(--white); border-radius: 12px; border: 1px solid var(--border);
        padding: 1.25rem; transition: all 0.2s;
    }
    .member-perf-card:hover { border-color: var(--border-strong); box-shadow: 0 4px 16px rgba(0,0,0,0.06); transform: translateY(-1px); }
    .mpc-top { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; }
    .mpc-avatar {
        width: 44px; height: 44px; border-radius: 50%; border: 2.5px solid var(--muted);
        flex-shrink: 0; transition: border-color 0.2s;
    }
    .member-perf-card:hover .mpc-avatar { border-color: var(--border-strong); }
    .mpc-info { flex: 1; min-width: 0; }
    .mpc-name { font-weight: 700; font-size: 0.95rem; margin-bottom: 0.125rem; }
    .mpc-total { font-size: 0.75rem; color: var(--gray-400); font-weight: 500; }
    .mpc-share {
        display: flex; flex-direction: column; align-items: center;
        background: var(--muted); border-radius: 10px; padding: 0.5rem 0.75rem;
        min-width: 60px;
    }
    .mpc-share-val { font-size: 1.15rem; font-weight: 800; color: var(--indigo); line-height: 1; }
    .mpc-share-label { font-size: 0.55rem; font-weight: 700; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }
    .mpc-divider { height: 1px; background: var(--border); margin-bottom: 0.75rem; }
    .mpc-tasks { display: flex; flex-direction: column; gap: 0.5rem; }
    .mpc-task { display: flex; align-items: center; gap: 0.5rem; }
    .mpc-task-dot { width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0; }
    .mpc-task-label { flex: 1; font-size: 0.72rem; color: var(--gray-500); font-weight: 500; }
    .mpc-task-bar-wrap { width: 60px; height: 5px; background: var(--muted); border-radius: 3px; overflow: hidden; flex-shrink: 0; }
    .mpc-task-bar { height: 100%; border-radius: 3px; transition: width 0.6s ease; }
    .mpc-task-val { font-size: 0.75rem; font-weight: 700; font-variant-numeric: tabular-nums; min-width: 28px; text-align: right; }

    @media (max-width: 1024px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid { grid-template-columns: 1fr; }
        .member-perf-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .member-perf-grid { grid-template-columns: 1fr; }
        .report-controls { flex-direction: column; align-items: stretch; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.reports" :isAdmin="true" />

<div class="main-content">
    @php
        $taskLabels = \App\Support\TaskLabels::get($roleFilter ?: 'content');
        $isAllRoles = !$roleFilter;
        // When viewing All Roles, use generic labels since columns mean different things per role
        if ($isAllRoles) {
            $tableLabels = ['task_1' => 'Task 1', 'task_2' => 'Task 2', 'task_3' => 'Task 3', 'task_4' => 'Task 4', 'task_5' => 'Task 5'];
        } else {
            $tableLabels = $taskLabels;
        }
        $roleColors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'];
        $roleNames = ['lead' => 'Lead', 'researcher' => 'Researcher', 'content' => 'Content', 'graphics' => 'Graphics', 'backend' => 'Backend'];
        $taskColors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'];

        $grandTotal = $monthTotal['t1'] + $monthTotal['t2'] + $monthTotal['t3'] + $monthTotal['t4'] + $monthTotal['t5'];
        $daysInMonth = \Carbon\Carbon::parse($month)->daysInMonth;
        $daysWithLogs = $weeks->flatMap(fn($w) => $w['days'])->pluck('date')->unique()->count();
        $avgPerDay = $daysWithLogs > 0 ? round($grandTotal / $daysWithLogs) : 0;

        // Find top contributor
        $memberTotals = collect();
        foreach ($weeks as $wk) {
            foreach ($wk['days'] as $day) {
                foreach ($day['members'] as $m) {
                    $total = $m['task_1'] + $m['task_2'] + $m['task_3'] + $m['task_4'] + $m['task_5'];
                    $memberTotals[$m['username']] = ($memberTotals[$m['username']] ?? 0) + $total;
                }
            }
        }
        $topMember = $memberTotals->sortDesc()->first() ? $memberTotals->sortDesc()->keys()->first() : '—';
        $topMemberTasks = $memberTotals->sortDesc()->first() ?? 0;

        // Most common task
        $taskTotals = [
            ['key' => 't1', 'label' => $tableLabels['task_1'], 'val' => $monthTotal['t1']],
            ['key' => 't2', 'label' => $tableLabels['task_2'], 'val' => $monthTotal['t2']],
            ['key' => 't3', 'label' => $tableLabels['task_3'], 'val' => $monthTotal['t3']],
            ['key' => 't4', 'label' => $tableLabels['task_4'], 'val' => $monthTotal['t4']],
            ['key' => 't5', 'label' => $tableLabels['task_5'], 'val' => $monthTotal['t5']],
        ];
        $topTask = collect($taskTotals)->sortByDesc('val')->first();

        // Leaderboard sorted
        $leaderboard = $memberTotals->sortDesc()->take(8)->toArray();
    @endphp

    <!-- Header -->
    <div class="anim-up">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem;">Reports @if($roleFilter) — <span style="text-transform: capitalize;">{{ $roleFilter }}</span>@endif</h2>
        <p style="color: var(--gray-400); font-size: 0.9rem; font-weight: 500; margin: 0;">{{ \Carbon\Carbon::parse($month)->format('F Y') }} performance overview</p>
    </div>

    <!-- Controls -->
    <div class="report-controls anim-up d1">
        <select class="month-select" onchange="window.location.href=updateParam('month', this.value)">
            @foreach($availableMonths as $m)
            <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
            @endforeach
        </select>
        <div class="report-tabs">
            <button class="report-tab active" onclick="switchTab('weekly', this)">Weekly</button>
            <button class="report-tab" onclick="switchTab('monthly', this)">Monthly</button>
        </div>
    </div>

    <!-- Weekly Tab -->
    <div class="tab-content active" id="tab-weekly">

        <!-- 1. KPIs — At a Glance -->
        <div class="kpi-grid anim-up d2">
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Total Tasks</span>
                    <div class="kpi-icon" style="background: #6366f1;"><i class="fas fa-list-check"></i></div>
                </div>
                <div class="kpi-value">{{ number_format($grandTotal) }}</div>
                <div class="kpi-sub">{{ $tableLabels['task_1'] }}: {{ $monthTotal['t1'] }} · {{ $tableLabels['task_2'] }}: {{ $monthTotal['t2'] }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Avg / Day</span>
                    <div class="kpi-icon" style="background: #0ea5e9;"><i class="fas fa-gauge-high"></i></div>
                </div>
                <div class="kpi-value">{{ $avgPerDay }}</div>
                <div class="kpi-sub">Across {{ $daysWithLogs }} active days</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Top Contributor</span>
                    <div class="kpi-icon" style="background: #10b981;"><i class="fas fa-trophy"></i></div>
                </div>
                <div class="kpi-value" style="font-size: 1.25rem;">{{ $topMember }}</div>
                <div class="kpi-sub">{{ $topMemberTasks }} tasks this month</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Most Common</span>
                    <div class="kpi-icon" style="background: #f59e0b;"><i class="fas fa-fire"></i></div>
                </div>
                <div class="kpi-value" style="font-size: 1.1rem;">{{ $topTask['label'] ?? '—' }}</div>
                <div class="kpi-sub">{{ $topTask['val'] ?? 0 }} tasks ({{ $grandTotal > 0 ? round(($topTask['val'] ?? 0) / $grandTotal * 100) : 0 }}%)</div>
            </div>
        </div>

        <!-- 2. Trends — Weekly Trend + Leaderboard -->
        @if($grandTotal > 0)
        <div class="charts-grid anim-up d3">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="cc-icon" style="background: #0ea5e9;"><i class="fas fa-chart-line"></i></div>
                    <h4>Weekly Trend</h4>
                </div>
                <div class="chart-card-body">
                    <div id="trendChart" style="height: 280px;"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="cc-icon" style="background: #10b981;"><i class="fas fa-ranking-star"></i></div>
                    <h4>Member Leaderboard</h4>
                </div>
                <div class="chart-card-body">
                    <div id="leaderboardChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        @endif

        <!-- 3. Reference — Weekly Task Summary -->
        @if($weeks->count())
        <div class="section-header anim-up d5">
            <div class="sh-icon" style="background: #0ea5e9;"><i class="fas fa-chart-simple"></i></div>
            <h3>Weekly Summary</h3>
            <div class="sh-line"></div>
        </div>
        <div class="table-card anim-up d5">
            <div style="overflow-x: auto;">
            <table class="wt">
                <thead>
                    <tr>
                        <th></th>
                        @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                        <th class="num">{{ $tableLabels[$tk] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $st1 = 0; $st2 = 0; $st3 = 0; $st4 = 0; $st5 = 0; @endphp
                    @foreach($weeks as $wk)
                    @php
                        $st1 += $wk['total_t1']; $st2 += $wk['total_t2']; $st3 += $wk['total_t3'];
                        $st4 += $wk['total_t4']; $st5 += $wk['total_t5'];
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: var(--gray-500);">W{{ $wk['week_num'] }}</td>
                        <td class="num" style="font-weight: 700;">{{ $wk['total_t1'] }}</td>
                        <td class="num" style="font-weight: 700;">{{ $wk['total_t2'] }}</td>
                        <td class="num" style="font-weight: 700;">{{ $wk['total_t3'] }}</td>
                        <td class="num" style="font-weight: 700;">{{ $wk['total_t4'] }}</td>
                        <td class="num" style="font-weight: 700;">{{ $wk['total_t5'] }}</td>
                    </tr>
                    @endforeach
                    <tr class="month-total">
                        <td style="font-weight: 800;">Total</td>
                        <td class="num">{{ $st1 }}</td>
                        <td class="num">{{ $st2 }}</td>
                        <td class="num">{{ $st3 }}</td>
                        <td class="num">{{ $st4 }}</td>
                        <td class="num">{{ $st5 }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- 4. Drill-down — Daily Breakdown -->
        <div class="section-header anim-up d5" style="margin-top: 1.5rem;">
            <div class="sh-icon" style="background: var(--primary);"><i class="fas fa-table"></i></div>
            <h3>Daily Breakdown</h3>
            <div class="sh-line"></div>
        </div>
        <div class="table-card anim-up d5">
            <div style="overflow-x: auto;">
            <table class="wt">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Date</th>
                        <th>Member</th>
                        @if($isAllRoles)
                        <th>Role</th>
                        @endif
                        <th class="num">{{ $tableLabels['task_1'] }}</th>
                        <th class="num">{{ $tableLabels['task_2'] }}</th>
                        <th class="num">{{ $tableLabels['task_3'] }}</th>
                        <th class="num">{{ $tableLabels['task_4'] }}</th>
                        <th class="num">{{ $tableLabels['task_5'] }}</th>
                        <th class="num" style="border-left: 2px solid var(--border);">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNum = 0; @endphp
                    @foreach($weeks as $week)
                    @php $colspan = $isAllRoles ? 10 : 9; @endphp
                    <tr class="week-sep">
                        <td colspan="{{ $colspan }}">Week {{ $week['week_num'] }}</td>
                    </tr>
                    @foreach($week['days'] as $day)
                    @php $dayMemberCount = count($day['members']); @endphp
                    @foreach($day['members'] as $m)
                    @php
                        $rowNum++;
                        $rowTotal = $m['task_1'] + $m['task_2'] + $m['task_3'] + $m['task_4'] + $m['task_5'];
                        $mRole = $m['role'] ?? 'content';
                    @endphp
                    <tr>
                        <td style="color: var(--gray-300); font-size: 0.75rem;">{{ $rowNum }}</td>
                        @if($loop->first)
                        <td rowspan="{{ $dayMemberCount }}" style="white-space: nowrap; font-weight: 600; font-size: 0.8rem; vertical-align: middle; border-bottom: 2px solid var(--border);">{{ \Carbon\Carbon::parse($day['date'])->format('D, M d') }}</td>
                        @endif
                        <td>
                            <div class="user-cell">
                                <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($m['username'], ['jamie', 'em', 'ange', 'czein', 'well']) ? $m['username'] . 'Female' : $m['username'] }}" alt="">
                                <span class="name">{{ $m['first_name'] }}</span>
                            </div>
                        </td>
                        @if($isAllRoles)
                        <td><span class="role-badge {{ $mRole }}" style="font-size: 0.55rem; padding: 0.1rem 0.35rem;">{{ $roleNames[$mRole] ?? ucfirst($mRole) }}</span></td>
                        @endif
                        <td class="num">{{ $m['task_1'] ?: '—' }}</td>
                        <td class="num">{{ $m['task_2'] ?: '—' }}</td>
                        <td class="num">{{ $m['task_3'] ?: '—' }}</td>
                        <td class="num">{{ $m['task_4'] ?: '—' }}</td>
                        <td class="num">{{ $m['task_5'] ?: '—' }}</td>
                        <td class="num" style="border-left: 2px solid var(--border); font-weight: 800;">{{ $rowTotal }}</td>
                    </tr>
                    @endforeach
                    @endforeach
                    <tr class="total-row">
                        <td colspan="{{ $isAllRoles ? 4 : 3 }}" style="text-align: right;">Week {{ $week['week_num'] }} Total</td>
                        <td class="num">{{ $week['total_t1'] }}</td>
                        <td class="num">{{ $week['total_t2'] }}</td>
                        <td class="num">{{ $week['total_t3'] }}</td>
                        <td class="num">{{ $week['total_t4'] }}</td>
                        <td class="num">{{ $week['total_t5'] }}</td>
                        <td class="num" style="border-left: 2px solid rgba(255,255,255,0.2);">{{ $week['total_t1'] + $week['total_t2'] + $week['total_t3'] + $week['total_t4'] + $week['total_t5'] }}</td>
                    </tr>
                    @endforeach
                    <tr class="month-total">
                        <td colspan="{{ $isAllRoles ? 4 : 3 }}" style="text-align: right;">{{ \Carbon\Carbon::parse($month)->format('F') }} Total</td>
                        <td class="num">{{ $monthTotal['t1'] }}</td>
                        <td class="num">{{ $monthTotal['t2'] }}</td>
                        <td class="num">{{ $monthTotal['t3'] }}</td>
                        <td class="num">{{ $monthTotal['t4'] }}</td>
                        <td class="num">{{ $monthTotal['t5'] }}</td>
                        <td class="num" style="border-left: 2px solid rgba(255,255,255,0.2);">{{ $grandTotal }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- 5. Contribution % per Task -->
        @if($shareData->count() && count($memberNames))
        @php
            // Compute average contribution % per member per task type across all weeks
            $contribData = $shareData->map(function ($tg) use ($memberNames) {
                $avgShares = collect($memberNames)->mapWithKeys(function ($name) use ($tg) {
                    $avg = $tg['weeks']->avg(fn($wk) => $wk['members'][$name]['share'] ?? 0);
                    return [$name => round($avg, 1)];
                });
                return ['task' => $tg['task_name'], 'shares' => $avgShares];
            });
            // Pre-compute chart series for JavaScript
            $contribLabels = $contribData->pluck('task')->toArray();
            $contribSeries = collect($memberNames)->map(function ($name) use ($contribData) {
                return [
                    'name' => $name,
                    'data' => $contribData->pluck('shares')->map(fn($s) => $s[$name] ?? 0)->toArray(),
                ];
            })->values()->toArray();
        @endphp
        <div class="chart-card anim-up d5" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
            <div class="chart-card-header">
                <div class="cc-icon" style="background: #f59e0b;"><i class="fas fa-chart-bar"></i></div>
                <h4>Avg Contribution % by Task</h4>
            </div>
            <div class="chart-card-body">
                <div id="contribChart" style="height: 300px;"></div>
            </div>
        </div>

        <!-- 6. Deep-dive — Contribution Analysis -->
        <div class="section-header anim-up d5" style="margin-top: 1.5rem;">
            <div class="sh-icon" style="background: #6366f1;"><i class="fas fa-chart-pie"></i></div>
            <h3>Contribution Analysis</h3>
            <div class="sh-line"></div>
        </div>
        <div class="table-card anim-up d5">
            <div style="overflow-x: auto;">
            <table class="wt">
                <thead>
                    <tr>
                        <th></th>
                        @foreach($memberNames as $m)
                        <th colspan="2" style="text-align: center; border-left: 2px solid var(--border); font-size: 0.7rem; text-transform: uppercase;">{{ $m }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th></th>
                        @foreach($memberNames as $m)
                        <th class="num" style="font-size: 0.55rem;">TASKS</th>
                        <th class="num" style="font-size: 0.55rem; border-right: 2px solid var(--border);">SHARE</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($shareData as $taskGroup)
                    <tr style="background: var(--muted);">
                        <td colspan="{{ count($memberNames) * 2 + 1 }}" style="font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--gray-500); padding: 0.5rem 0.75rem;">{{ $taskGroup['task_name'] }}</td>
                    </tr>
                    @foreach($taskGroup['weeks'] as $wk)
                    <tr>
                        <td style="font-weight: 700; color: var(--gray-400); font-size: 0.75rem;">W{{ $wk['week_num'] }}</td>
                        @foreach($memberNames as $m)
                        @php $md = $wk['members'][$m] ?? ['tasks' => 0, 'share' => 0]; @endphp
                        <td class="num" style="font-weight: 700;">{{ $md['tasks'] }}</td>
                        <td class="num" style="border-right: 2px solid var(--border); font-size: 0.75rem;">{{ number_format($md['share'], 2) }}%</td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        @endif
        @else
        <div class="table-card anim-up d3">
            <div class="empty-state"><i class="fas fa-inbox"></i>No data for this month</div>
        </div>
        @endif
    </div>

    <!-- Monthly Tab -->
    <div class="tab-content" id="tab-monthly">
        @php
            $mTotalAll = $allMonths->sum('total');
        @endphp

        @if($allMonths->count())
        <!-- Monthly KPIs -->
        <div class="kpi-grid anim-up d2">
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Total (All Months)</span>
                    <div class="kpi-icon" style="background: #6366f1;"><i class="fas fa-layer-group"></i></div>
                </div>
                <div class="kpi-value">{{ number_format($mTotalAll) }}</div>
                <div class="kpi-sub">{{ $allMonths->count() }} month{{ $allMonths->count() > 1 ? 's' : '' }} of data</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Avg / Month</span>
                    <div class="kpi-icon" style="background: #0ea5e9;"><i class="fas fa-calculator"></i></div>
                </div>
                <div class="kpi-value">{{ $allMonths->count() > 0 ? round($mTotalAll / $allMonths->count()) : 0 }}</div>
                <div class="kpi-sub">tasks per month</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Best Month</span>
                    <div class="kpi-icon" style="background: #10b981;"><i class="fas fa-arrow-trend-up"></i></div>
                </div>
                <div class="kpi-value" style="font-size: 1.25rem;">{{ $allMonths->sortByDesc('total')->first()['short'] ?? '—' }}</div>
                <div class="kpi-sub">{{ $allMonths->sortByDesc('total')->first()['total'] ?? 0 }} tasks</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-top">
                    <span class="kpi-label">Peak Task Type</span>
                    <div class="kpi-icon" style="background: #f59e0b;"><i class="fas fa-fire"></i></div>
                </div>
                @php
                    $allT1 = $allMonths->sum('t1'); $allT2 = $allMonths->sum('t2');
                    $allT3 = $allMonths->sum('t3'); $allT4 = $allMonths->sum('t4'); $allT5 = $allMonths->sum('t5');
                    $peakTask = collect(['t1' => $allT1, 't2' => $allT2, 't3' => $allT3, 't4' => $allT4, 't5' => $allT5])->sortDesc()->first();
                @endphp
                <div class="kpi-value" style="font-size: 1.1rem;">{{ $taskLabels[array_keys(collect(['t1' => $allT1, 't2' => $allT2, 't3' => $allT3, 't4' => $allT4, 't5' => $allT5])->sortDesc()->toArray())[0]] ?? '—' }}</div>
                <div class="kpi-sub">{{ $peakTask }} tasks total</div>
            </div>
        </div>

        <!-- Monthly Charts -->
        <div class="charts-grid anim-up d3">
            <!-- Monthly Trend -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="cc-icon" style="background: #0ea5e9;"><i class="fas fa-chart-line"></i></div>
                    <h4>Monthly Trend</h4>
                </div>
                <div class="chart-card-body">
                    <div id="monthlyTrendChart" style="height: 280px;"></div>
                </div>
            </div>
            <!-- Task Composition -->
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="cc-icon" style="background: #6366f1;"><i class="fas fa-chart-bar"></i></div>
                    <h4>Task Composition</h4>
                </div>
                <div class="chart-card-body">
                    <div id="compositionChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>

        <!-- Member Performance — Selected Month -->
        @php
            $selectedMonthKey = \Carbon\Carbon::parse($month)->format('Y-m');
            $selectedMonthMembers = $memberMonthly->get($selectedMonthKey, collect())->sortByDesc('total');
        @endphp
        @if($selectedMonthMembers->count())
        <div class="section-header anim-up d4">
            <div class="sh-icon" style="background: #10b981;"><i class="fas fa-users"></i></div>
            <h3>Team Performance</h3>
            <div class="sh-line"></div>
        </div>
        <div class="member-perf-grid anim-up d4">
            @php
                $totalMonthTasks = $selectedMonthMembers->sum('total');
            @endphp
            @foreach($selectedMonthMembers as $mp)
            @php
                $mpTotal = $mp['total'];
                $mpPct = $totalMonthTasks > 0 ? round($mpTotal / $totalMonthTasks * 100, 1) : 0;
                $taskColors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'];
                $taskKeys = ['t1', 't2', 't3', 't4', 't5'];
                $taskMaxVal = max([$mp['t1'], $mp['t2'], $mp['t3'], $mp['t4'], $mp['t5']]);
            @endphp
            <div class="member-perf-card">
                <div class="mpc-top">
                    <img class="mpc-avatar" src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($mp['username'], ['jamie', 'em', 'ange', 'czein', 'well']) ? $mp['username'] . 'Female' : $mp['username'] }}" alt="">
                    <div class="mpc-info">
                        <div class="mpc-name">{{ $mp['first_name'] }}</div>
                        <div class="mpc-total">{{ number_format($mpTotal) }} tasks</div>
                    </div>
                    <div class="mpc-share">
                        <span class="mpc-share-val">{{ $mpPct }}%</span>
                        <span class="mpc-share-label">share</span>
                    </div>
                </div>
                <div class="mpc-divider"></div>
                <div class="mpc-tasks">
                    @foreach($taskKeys as $i => $tk)
                    @php $tv = $mp[$tk]; $tBar = $taskMaxVal > 0 ? round($tv / $taskMaxVal * 100) : 0; @endphp
                    <div class="mpc-task">
                        <div class="mpc-task-dot" style="background: {{ $taskColors[$i] }};"></div>
                        <span class="mpc-task-label">{{ $tableLabels['task_' . ($i + 1)] }}</span>
                        <div class="mpc-task-bar-wrap">
                            <div class="mpc-task-bar" style="width: {{ $tBar }}%; background: {{ $taskColors[$i] }};"></div>
                        </div>
                        <span class="mpc-task-val">{{ $tv }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- 12-Month Overview -->
        <div class="section-header anim-up d4">
            <div class="sh-icon" style="background: #0ea5e9;"><i class="fas fa-calendar-days"></i></div>
            <h3>{{ $selectedYear }} Overview</h3>
            <div class="sh-line"></div>
        </div>
        <div class="table-card anim-up d4">
            <div style="overflow-x: auto;">
            <table class="wt">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="num">{{ $tableLabels['task_1'] }}</th>
                        <th class="num">{{ $tableLabels['task_2'] }}</th>
                        <th class="num">{{ $tableLabels['task_3'] }}</th>
                        <th class="num">{{ $tableLabels['task_4'] }}</th>
                        <th class="num">{{ $tableLabels['task_5'] }}</th>
                        <th class="num" style="border-left: 2px solid var(--border);">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $yt1 = 0; $yt2 = 0; $yt3 = 0; $yt4 = 0; $yt5 = 0;
                    @endphp
                    @foreach($yearOverview as $ym)
                    @php
                        $yt1 += $ym['t1']; $yt2 += $ym['t2']; $yt3 += $ym['t3'];
                        $yt4 += $ym['t4']; $yt5 += $ym['t5'];
                    @endphp
                    <tr>
                        <td style="font-weight: 600;">{{ $ym['month'] }}</td>
                        <td class="num">{{ $ym['t1'] ?: '—' }}</td>
                        <td class="num">{{ $ym['t2'] ?: '—' }}</td>
                        <td class="num">{{ $ym['t3'] ?: '—' }}</td>
                        <td class="num">{{ $ym['t4'] ?: '—' }}</td>
                        <td class="num">{{ $ym['t5'] ?: '—' }}</td>
                        <td class="num" style="border-left: 2px solid var(--border); font-weight: 700;">{{ $ym['total'] ?: '—' }}</td>
                    </tr>
                    @endforeach
                    <tr class="month-total">
                        <td style="font-weight: 800;">Total</td>
                        <td class="num">{{ $yt1 }}</td>
                        <td class="num">{{ $yt2 }}</td>
                        <td class="num">{{ $yt3 }}</td>
                        <td class="num">{{ $yt4 }}</td>
                        <td class="num">{{ $yt5 }}</td>
                        <td class="num" style="border-left: 2px solid rgba(255,255,255,0.2);">{{ $yt1 + $yt2 + $yt3 + $yt4 + $yt5 }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Monthly Summary Table -->
        <div class="section-header anim-up d4">
            <div class="sh-icon" style="background: var(--primary);"><i class="fas fa-table"></i></div>
            <h3>Monthly Summary</h3>
            <div class="sh-line"></div>
        </div>
        <div class="table-card anim-up d4">
            <div style="overflow-x: auto;">
            <table class="wt">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Name</th>
                        <th class="num">{{ $tableLabels['task_1'] }}</th>
                        <th class="num">{{ $tableLabels['task_2'] }}</th>
                        <th class="num">{{ $tableLabels['task_3'] }}</th>
                        <th class="num">{{ $tableLabels['task_4'] }}</th>
                        <th class="num">{{ $tableLabels['task_5'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $gt1 = 0; $gt2 = 0; $gt3 = 0; $gt4 = 0; $gt5 = 0;
                    @endphp
                    @foreach($allMonths as $m)
                    @php
                        $mKey = $m['month'];
                        $mRows = $memberMonthly->get($mKey, collect());
                        $mt1 = $mRows->sum('t1'); $mt2 = $mRows->sum('t2');
                        $mt3 = $mRows->sum('t3'); $mt4 = $mRows->sum('t4'); $mt5 = $mRows->sum('t5');
                        $gt1 += $mt1; $gt2 += $mt2; $gt3 += $mt3; $gt4 += $mt4; $gt5 += $mt5;
                    @endphp
                    @foreach($mRows as $row)
                    <tr>
                        @if($loop->first)
                        <td rowspan="{{ $mRows->count() + 1 }}" style="font-weight: 700; white-space: nowrap; vertical-align: middle; border-bottom: 2px solid var(--border); font-size: 0.85rem;">{{ $m['label'] }}</td>
                        @endif
                        <td>
                            <div class="user-cell">
                                <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($row['username'], ['jamie', 'em', 'ange', 'czein', 'well']) ? $row['username'] . 'Female' : $row['username'] }}" alt="">
                                <span class="name">{{ $row['first_name'] }}</span>
                            </div>
                        </td>
                        <td class="num">{{ $row['t1'] }}</td>
                        <td class="num">{{ $row['t2'] }}</td>
                        <td class="num">{{ $row['t3'] }}</td>
                        <td class="num">{{ $row['t4'] }}</td>
                        <td class="num">{{ $row['t5'] }}</td>
                    </tr>
                    @endforeach
                    <!-- Month Total -->
                    <tr class="total-row">
                        <td style="text-align: right;">{{ $m['label'] }} Total</td>
                        <td class="num">{{ $mt1 }}</td>
                        <td class="num">{{ $mt2 }}</td>
                        <td class="num">{{ $mt3 }}</td>
                        <td class="num">{{ $mt4 }}</td>
                        <td class="num">{{ $mt5 }}</td>
                    </tr>
                    @endforeach
                    <!-- Grand Total -->
                    <tr class="month-total">
                        <td colspan="2" style="text-align: right;">Total</td>
                        <td class="num">{{ $gt1 }}</td>
                        <td class="num">{{ $gt2 }}</td>
                        <td class="num">{{ $gt3 }}</td>
                        <td class="num">{{ $gt4 }}</td>
                        <td class="num">{{ $gt5 }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        @else
        <div class="table-card anim-up d2">
            <div class="empty-state"><i class="fas fa-inbox"></i>No monthly data available</div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.report-tab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
}

function updateParam(key, value) {
    var url = new URL(window.location);
    url.searchParams.set(key, value);
    return url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    var colors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'];
    var tooltipStyle = { theme: 'light', style: { fontSize: '13px', fontFamily: 'Inter' } };

    // Member Leaderboard
    var leaderEl = document.getElementById('leaderboardChart');
    if (leaderEl) {
        var lLabels = {!! json_encode(array_keys($leaderboard)) !!};
        var lData = {!! json_encode(array_values($leaderboard)) !!};

        if (lData.length > 0) {
            var lColors = ['#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe', '#ddd6fe', '#e0e7ff', '#4f46e5', '#4338ca'];
            new ApexCharts(leaderEl, {
                chart: { type: 'bar', height: 280, fontFamily: 'Inter', foreColor: '#64748b', toolbar: { show: false } },
                series: [{ name: 'Tasks', data: lData }],
                colors: lColors,
                plotOptions: { bar: { borderRadius: 6, columnWidth: '60%', distributed: true, horizontal: true } },
                xaxis: { labels: { style: { fontWeight: 500, fontSize: '12px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { style: { fontWeight: 600, fontSize: '12px', colors: '#64748b' } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 0, xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                tooltip: { enabled: true, y: { formatter: function(val) { return val + ' tasks'; } } },
                legend: { show: false }
            }).render();
        }
    }

    // Weekly Trend
    var trendEl = document.getElementById('trendChart');
    if (trendEl) {
        var tLabels = {!! json_encode($weeks->pluck('week_num')->map(fn($w) => 'Week ' . $w)->toArray()) !!};
        var tData = {!! json_encode($weeks->map(fn($w) => $w['total_t1'] + $w['total_t2'] + $w['total_t3'] + $w['total_t4'] + $w['total_t5'])->toArray()) !!};

        if (tData.length > 0) {
            new ApexCharts(trendEl, {
                chart: { type: 'area', height: 220, fontFamily: 'Inter', foreColor: '#64748b', toolbar: { show: false } },
                series: [{ name: 'Tasks', data: tData }],
                colors: ['#6366f1'],
                stroke: { width: 3, curve: 'smooth' },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.2, opacityTo: 0.05, stops: [0, 100] } },
                markers: { size: 5, colors: ['#ffffff'], strokeColors: '#6366f1', strokeWidth: 2, hover: { sizeOffset: 3 } },
                xaxis: { categories: tLabels, labels: { style: { fontWeight: 600, fontSize: '12px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { style: { fontWeight: 500, fontSize: '12px', colors: '#94a3b8' }, padding: 4 }, tickAmount: 4 },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
                tooltip: { enabled: true, y: { formatter: function(val) { return val + ' tasks'; } } },
                dataLabels: { enabled: false }
            }).render();
        }
    }

    // === Monthly Tab Charts ===

    // Monthly Trend
    var monthlyTrendEl = document.getElementById('monthlyTrendChart');
    if (monthlyTrendEl) {
        var mLabels = {!! json_encode($allMonths->pluck('short')->toArray()) !!};
        var mData = {!! json_encode($allMonths->pluck('total')->toArray()) !!};

        if (mData.length > 0) {
            new ApexCharts(monthlyTrendEl, {
                chart: { type: 'area', height: 280, fontFamily: 'Inter', foreColor: '#64748b', toolbar: { show: false } },
                series: [{ name: 'Total Tasks', data: mData }],
                colors: ['#6366f1'],
                stroke: { width: 3, curve: 'smooth' },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.02, stops: [0, 100] } },
                markers: { size: 6, colors: ['#ffffff'], strokeColors: '#6366f1', strokeWidth: 2.5, hover: { sizeOffset: 3 } },
                xaxis: { categories: mLabels, labels: { style: { fontWeight: 600, fontSize: '12px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { style: { fontWeight: 500, fontSize: '12px', colors: '#94a3b8' }, padding: 4 }, tickAmount: 4 },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
                tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: 'Inter' }, y: { formatter: function(val) { return val.toLocaleString() + ' tasks'; } } },
                dataLabels: { enabled: false }
            }).render();
        } else {
            monthlyTrendEl.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:280px;color:#94a3b8;font-size:14px;font-weight:500;">No data yet</div>';
        }
    }

    // Task Composition (Stacked Bar)
    var compEl = document.getElementById('compositionChart');
    if (compEl) {
        var cLabels = {!! json_encode($allMonths->pluck('short')->toArray()) !!};
        var cT1 = {!! json_encode($allMonths->pluck('t1')->toArray()) !!};
        var cT2 = {!! json_encode($allMonths->pluck('t2')->toArray()) !!};
        var cT3 = {!! json_encode($allMonths->pluck('t3')->toArray()) !!};
        var cT4 = {!! json_encode($allMonths->pluck('t4')->toArray()) !!};
        var cT5 = {!! json_encode($allMonths->pluck('t5')->toArray()) !!};
        var cNames = {!! json_encode([$tableLabels['task_1'], $tableLabels['task_2'], $tableLabels['task_3'], $tableLabels['task_4'], $tableLabels['task_5']]) !!};

        if (cLabels.length > 0) {
            new ApexCharts(compEl, {
                chart: { type: 'bar', height: 280, fontFamily: 'Inter', foreColor: '#64748b', stacked: true, toolbar: { show: false } },
                series: [
                    { name: cNames[0], data: cT1 },
                    { name: cNames[1], data: cT2 },
                    { name: cNames[2], data: cT3 },
                    { name: cNames[3], data: cT4 },
                    { name: cNames[4], data: cT5 }
                ],
                colors: colors,
                plotOptions: { bar: { columnWidth: '50%', borderRadius: { topLeft: 4, topRight: 4 } } },
                xaxis: { categories: cLabels, labels: { style: { fontWeight: 600, fontSize: '12px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { style: { fontWeight: 500, fontSize: '12px', colors: '#94a3b8' }, padding: 4 }, tickAmount: 4 },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
                legend: { position: 'bottom', labels: { colors: '#64748b', useSeriesColors: true, fontWeight: 600, fontSize: '11px', padding: 12 }, markers: { width: 10, height: 10, radius: 3, strokeWidth: 0 }, itemMargin: { horizontal: 6, vertical: 2 } },
                tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: 'Inter' }, y: { formatter: function(val) { return val + ' tasks'; } } },
                dataLabels: { enabled: false }
            }).render();
        } else {
            compEl.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:280px;color:#94a3b8;font-size:14px;font-weight:500;">No data yet</div>';
        }
    }

    // Contribution % per Task (Grouped Bar)
    var contribEl = document.getElementById('contribChart');
    if (contribEl) {
        var cLabels = {!! json_encode($contribLabels) !!};
        var cSeries = {!! json_encode($contribSeries) !!};
        var cColors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#ec4899', '#14b8a6'];

        if (cLabels.length > 0 && cSeries.length > 0) {
            new ApexCharts(contribEl, {
                chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#64748b' },
                series: cSeries,
                colors: cColors,
                plotOptions: { bar: { borderRadius: 4, columnWidth: '70%' } },
                xaxis: { categories: cLabels, labels: { style: { fontWeight: 600, fontSize: '11px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { style: { fontWeight: 500, fontSize: '11px', colors: '#94a3b8' }, formatter: function(val) { return val + '%'; }, max: 100 }, tickAmount: 5 },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
                legend: { position: 'bottom', labels: { colors: '#64748b', useSeriesColors: true, fontWeight: 600, fontSize: '11px', padding: 12 }, markers: { width: 10, height: 10, radius: 3, strokeWidth: 0 }, itemMargin: { horizontal: 6, vertical: 2 } },
                tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: 'Inter' }, y: { formatter: function(val) { return val + '%'; } } },
                dataLabels: { enabled: false }
            }).render();
        }
    }
});
</script>
@endsection
