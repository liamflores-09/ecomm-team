@extends('layouts.app')

@section('title', 'Admin Dashboard — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    /* Hero KPIs */
    .kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.25rem; margin-bottom: 1.75rem; }
    .kpi-card {
        background: var(--white); border-radius: 12px; padding: 1.5rem;
        border: 1px solid var(--border); transition: all 0.2s; position: relative; overflow: hidden;
    }
    .kpi-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(99,102,241,0.08); }
    .kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-400); }
    .kpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: white; }
    .kpi-value { font-size: 1.75rem; font-weight: 800; line-height: 1; margin-bottom: 0.375rem; }
    .kpi-bottom { display: flex; align-items: center; justify-content: space-between; }
    .kpi-sub { font-size: 0.75rem; color: var(--gray-400); font-weight: 500; }
    .kpi-spark { width: 60px; height: 24px; }
    .kpi-trend { display: inline-flex; align-items: center; gap: 3px; font-weight: 700; font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; }
    .kpi-trend.up { background: #ecfdf5; color: #059669; }
    .kpi-trend.down { background: #fef2f2; color: #dc2626; }

    /* Team Health */
    .health-card {
        background: var(--white); border-radius: 12px; border: 1px solid var(--border);
        padding: 1.5rem; margin-bottom: 1.75rem;
    }
    .health-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .health-header h4 { font-size: 0.9rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .health-header .pulse { width: 8px; height: 8px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    .health-bar-wrap { width: 100%; height: 8px; background: var(--muted); border-radius: 4px; overflow: hidden; margin-bottom: 1rem; }
    .health-bar { height: 100%; border-radius: 4px; transition: width 1s ease; }
    .health-avatars { display: flex; align-items: center; gap: 0; }
    .health-avatar {
        width: 36px; height: 36px; border-radius: 50%; border: 2.5px solid var(--white);
        margin-left: -10px; position: relative; transition: transform 0.2s;
    }
    .health-avatar:first-child { margin-left: 0; }
    .health-avatar:hover { transform: translateY(-2px); z-index: 1; }
    .health-avatar.logged { border-color: #10b981; }
    .health-avatar.pending { border-color: #fca5a5; opacity: 0.5; }
    .health-legend { display: flex; gap: 1.25rem; margin-left: auto; }
    .health-legend span { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; color: var(--gray-500); }
    .health-legend .dot { width: 7px; height: 7px; border-radius: 50%; }

    /* Charts Grid */
    .charts-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.25rem; margin-bottom: 1.75rem; }
    .chart-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
    .chart-card-header { display: flex; align-items: center; gap: 0.625rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--muted); }
    .chart-card-header .cc-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; flex-shrink: 0; }
    .chart-card-header h4 { font-size: 0.85rem; font-weight: 700; margin: 0; }
    .chart-card-body { padding: 1.25rem 1.5rem; }

    /* Role Breakdown */
    .role-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
    .role-header { display: flex; align-items: center; gap: 0.625rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--muted); }
    .role-header .rh-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; flex-shrink: 0; }
    .role-header h4 { font-size: 0.85rem; font-weight: 700; margin: 0; }
    .role-list { padding: 1rem 1.5rem; }
    .role-row { display: flex; align-items: center; gap: 0.875rem; padding: 0.625rem 0; }
    .role-row:not(:last-child) { border-bottom: 1px solid var(--border); }
    .role-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .role-name { flex: 1; font-size: 0.85rem; font-weight: 600; }
    .role-bar-wrap { width: 80px; height: 6px; background: var(--muted); border-radius: 3px; overflow: hidden; }
    .role-bar { height: 100%; border-radius: 3px; }
    .role-count { font-size: 0.85rem; font-weight: 700; min-width: 24px; text-align: right; }

    /* Quick Actions */
    .quick-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .quick-link {
        display: flex; align-items: center; gap: 0.875rem; padding: 1rem 1.125rem;
        background: var(--white); border-radius: 10px; text-decoration: none; color: var(--fg);
        border: 1px solid var(--border); transition: all 0.2s;
    }
    .quick-link:hover { border-color: #6366f1; background: #f5f3ff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.1); }
    .ql-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; color: white; flex-shrink: 0; }
    .ql-text strong { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.125rem; }
    .ql-text small { color: var(--gray-400); font-size: 0.75rem; font-weight: 500; }
    .ql-arrow { margin-left: auto; color: var(--gray-300); font-size: 0.75rem; transition: all 0.2s; }
    .quick-link:hover .ql-arrow { color: #6366f1; }

    /* Activity Timeline */
    .activity-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
    .activity-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--muted); }
    .activity-header h4 { font-size: 0.85rem; font-weight: 700; margin: 0; }
    .activity-header a { font-size: 0.8rem; font-weight: 600; color: #6366f1; text-decoration: none; }
    .activity-header a:hover { text-decoration: underline; }
    .activity-item { display: flex; align-items: flex-start; gap: 0.875rem; padding: 0.875rem 1.5rem; border-bottom: 1px solid var(--border); transition: background 0.1s; }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #fafafa; }
    .activity-dot-wrap { display: flex; flex-direction: column; align-items: center; padding-top: 3px; }
    .activity-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .activity-line { width: 1px; flex: 1; background: var(--border); margin-top: 6px; min-height: 20px; }
    .activity-content { flex: 1; min-width: 0; }
    .activity-title { font-weight: 500; font-size: 0.85rem; }
    .activity-meta { font-size: 0.75rem; color: var(--gray-400); margin-top: 3px; }
    .activity-time { font-size: 0.75rem; color: var(--gray-300); white-space: nowrap; flex-shrink: 0; }

    .empty-state { text-align: center; padding: 2.5rem; color: var(--gray-400); font-size: 0.85rem; }

    @media (max-width: 1280px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 1024px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.dashboard" :isAdmin="true" />

<div class="main-content">
    @php
        $taskChange = $lastMonthTasks > 0 ? round(($thisMonthTasks - $lastMonthTasks) / $lastMonthTasks * 100) : null;
        $nonManagerCount = $totalUsers - $managers;
        $healthPct = $nonManagerCount > 0 ? round($todayLogged / $nonManagerCount * 100) : 0;
        $healthColor = $healthPct >= 80 ? '#10b981' : ($healthPct >= 50 ? '#f59e0b' : '#ef4444');
        $avgTasksPerson = $totalUsers > 0 ? round($thisMonthTasks / max(1, $totalUsers - $managers)) : 0;

        // Weekly sparkline data (last 7 days)
        $sparkData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $sparkData[] = \App\Models\DailyLog::whereDate('date', $d)
                ->sum(DB::raw('task_1 + task_2 + task_3 + task_4 + task_5'));
        }
        $sparkTrend = $sparkData[6] > 0 ? round(($sparkData[6] - $sparkData[5]) / max(1, $sparkData[5]) * 100) : 0;

        // Today's members
        $allMembers = \App\Models\User::where('role', '!=', 'manager')->get();
        $loggedUserIds = $todayLogs->pluck('user_id')->toArray();
    @endphp

    <!-- Header -->
    <div class="anim-up" style="margin-bottom: 1.75rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.375rem;">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}</h2>
        <p style="color: var(--gray-400); font-size: 0.9rem; font-weight: 500; margin: 0;">{{ now()->format('l, M d, Y') }} — Team overview at a glance</p>
    </div>

    <!-- KPIs with Sparklines -->
    <div class="kpi-grid anim-up d1">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">This Month</span>
                <div class="kpi-icon" style="background: #6366f1;"><i class="fas fa-bolt"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($thisMonthTasks) }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">tasks logged</span>
                @if($taskChange !== null)
                <span class="kpi-trend {{ $taskChange >= 0 ? 'up' : 'down' }}">
                    <i class="fas fa-arrow-{{ $taskChange >= 0 ? 'up' : 'down' }}"></i>{{ $taskChange >= 0 ? '+' : '' }}{{ $taskChange }}%
                </span>
                @endif
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Today</span>
                <div class="kpi-icon" style="background: #10b981;"><i class="fas fa-clipboard-check"></i></div>
            </div>
            <div class="kpi-value">{{ $todayLogged }}<span style="font-size: 0.9rem; color: var(--gray-400); font-weight: 500;">/{{ $nonManagerCount }}</span></div>
            <div class="kpi-bottom">
                <span class="kpi-sub">members logged in</span>
                @if($todayPending > 0)
                <span class="kpi-trend down"><i class="fas fa-clock"></i> {{ $todayPending }} pending</span>
                @else
                <span class="kpi-trend up"><i class="fas fa-check"></i> Complete</span>
                @endif
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Avg / Person</span>
                <div class="kpi-icon" style="background: #0ea5e9;"><i class="fas fa-user"></i></div>
            </div>
            <div class="kpi-value">{{ $avgTasksPerson }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">tasks per member</span>
                <div id="sparkAvg" class="kpi-spark"></div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Top Performer</span>
                <div class="kpi-icon" style="background: #f59e0b;"><i class="fas fa-trophy"></i></div>
            </div>
            <div class="kpi-value" style="font-size: 1.15rem;">{{ $topContributor->username ?? '—' }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">{{ $topContributor ? number_format($topContributor->total) . ' tasks' : 'No data' }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Team Size</span>
                <div class="kpi-icon" style="background: #f43f5e;"><i class="fas fa-users"></i></div>
            </div>
            <div class="kpi-value">{{ $totalUsers }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">{{ $nonManagerCount }} active members</span>
            </div>
        </div>
    </div>

    <!-- Team Health -->
    <div class="health-card anim-up d2">
        <div class="health-header">
            <h4><div class="pulse"></div> Today's Pulse</h4>
            <span style="font-size: 0.75rem; font-weight: 700; color: {{ $healthColor }};">{{ $healthPct }}% logged in</span>
        </div>
        <div class="health-bar-wrap">
            <div class="health-bar" style="width: {{ $healthPct }}%; background: {{ $healthColor }};"></div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="health-avatars">
                @foreach($allMembers as $m)
                    @php $isLogged = in_array($m->id, $loggedUserIds); @endphp
                    <img class="health-avatar {{ $isLogged ? 'logged' : 'pending' }}"
                         src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($m->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $m->username . 'Female' : $m->username }}"
                         alt="{{ $m->first_name }}" title="{{ $m->first_name }} — {{ $isLogged ? 'Logged' : 'Pending' }}">
                @endforeach
            </div>
            <div class="health-legend">
                <span><div class="dot" style="background: #10b981;"></div> Logged ({{ $todayLogged }})</span>
                <span><div class="dot" style="background: #ef4444;"></div> Pending ({{ $todayPending }})</span>
            </div>
        </div>
    </div>

    <!-- Charts + Role Distribution -->
    <div class="charts-grid anim-up d3">
        <!-- Weekly Activity -->
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="cc-icon" style="background: #0ea5e9;"><i class="fas fa-chart-bar"></i></div>
                <h4>Weekly Activity</h4>
            </div>
            <div class="chart-card-body">
                <div id="weeklyChart" style="height: 260px;"></div>
            </div>
        </div>
        <!-- Role Distribution -->
        <div class="role-card">
            <div class="role-header">
                <div class="rh-icon" style="background: #6366f1;"><i class="fas fa-users"></i></div>
                <h4>Team Composition</h4>
            </div>
            <div class="role-list">
                @php
                    $roleData = [
                        ['name' => 'Lead', 'count' => $leads, 'color' => '#6366f1'],
                        ['name' => 'Researcher', 'count' => $researchers, 'color' => '#10b981'],
                        ['name' => 'Content', 'count' => $content, 'color' => '#0ea5e9'],
                        ['name' => 'Graphics', 'count' => $graphics, 'color' => '#f59e0b'],
                        ['name' => 'Backend', 'count' => $backend, 'color' => '#f43f5e'],
                    ];
                    $maxRole = max(array_column($roleData, 'count'));
                @endphp
                @foreach($roleData as $r)
                <div class="role-row">
                    <div class="role-dot" style="background: {{ $r['color'] }};"></div>
                    <span class="role-name">{{ $r['name'] }}</span>
                    <div class="role-bar-wrap">
                        <div class="role-bar" style="width: {{ $maxRole > 0 ? ($r['count'] / $maxRole * 100) : 0 }}%; background: {{ $r['color'] }};"></div>
                    </div>
                    <span class="role-count">{{ $r['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-grid anim-up d4">
        <a href="{{ route('admin.users') }}" class="quick-link">
            <div class="ql-icon" style="background: #6366f1;"><i class="fas fa-user-plus"></i></div>
            <div class="ql-text"><strong>Manage Users</strong><small>Add, edit, remove</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        <a href="{{ route('admin.daily-logs') }}" class="quick-link">
            <div class="ql-icon" style="background: #0ea5e9;"><i class="fas fa-clipboard-list"></i></div>
            <div class="ql-text"><strong>Daily Logs</strong><small>Team activity tracking</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        <a href="{{ route('admin.reports') }}" class="quick-link">
            <div class="ql-icon" style="background: #10b981;"><i class="fas fa-chart-pie"></i></div>
            <div class="ql-text"><strong>Reports</strong><small>Performance analytics</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        <a href="{{ route('team') }}" class="quick-link">
            <div class="ql-icon" style="background: #f59e0b;"><i class="fas fa-users"></i></div>
            <div class="ql-text"><strong>The Team</strong><small>Team directory</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
    </div>

    <!-- Recent Activity Timeline -->
    <div class="activity-card anim-up d5">
        <div class="activity-header">
            <h4>Recent Activity</h4>
            <a href="{{ route('admin.daily-logs') }}">View All <i class="fas fa-arrow-right" style="font-size: 0.65rem;"></i></a>
        </div>
        @forelse($recentActivity as $activity)
        @php
            $activityColors = [
                'eod_submitted' => '#10b981',
                'eod_updated' => '#3b82f6',
                'eod_deleted' => '#ef4444',
                'user_created' => '#8b5cf6',
                'user_updated' => '#f59e0b',
                'user_deleted' => '#ef4444',
            ];
            $ac = $activityColors[$activity->type] ?? '#64748b';
        @endphp
        <div class="activity-item">
            <div class="activity-dot-wrap">
                <div class="activity-dot" style="background: {{ $ac }};"></div>
                @if(!$loop->last)<div class="activity-line"></div>@endif
            </div>
            <div class="activity-content">
                <div class="activity-title">{{ $activity->description }}</div>
                <div class="activity-meta">{{ $activity->user->first_name ?? 'System' }} · {{ ucfirst(str_replace('_', ' ', $activity->type)) }}</div>
            </div>
            <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div class="empty-state"><i class="fas fa-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>No activity yet</div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var colors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'];

    // Sparkline in KPI card
    var sparkEl = document.getElementById('sparkAvg');
    if (sparkEl) {
        var sparkData = {!! json_encode($sparkData) !!};
        new ApexCharts(sparkEl, {
            chart: { type: 'area', height: 24, width: 60, sparkline: { enabled: true } },
            series: [{ data: sparkData }],
            colors: ['#6366f1'],
            stroke: { width: 2, curve: 'smooth' },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            tooltip: { enabled: false }
        }).render();
    }

    // Weekly Activity (Stacked Bar)
    var weeklyEl = document.getElementById('weeklyChart');
    if (weeklyEl) {
        new ApexCharts(weeklyEl, {
            chart: { type: 'bar', height: 260, stacked: true, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#64748b' },
            series: [
                { name: '{!! addslashes($taskLabels["task_1"] ?? "Task 1") !!}', data: {!! json_encode($chartNewSku) !!} },
                { name: '{!! addslashes($taskLabels["task_2"] ?? "Task 2") !!}', data: {!! json_encode($chartVariationSku) !!} },
                { name: '{!! addslashes($taskLabels["task_3"] ?? "Task 3") !!}', data: {!! json_encode($chartDataGathering) !!} },
                { name: '{!! addslashes($taskLabels["task_4"] ?? "Task 4") !!}', data: {!! json_encode($chartUpdateListings) !!} },
                { name: '{!! addslashes($taskLabels["task_5"] ?? "Task 5") !!}', data: {!! json_encode($chartOtherTasks) !!} }
            ],
            colors: colors,
            plotOptions: { bar: { columnWidth: '55%', borderRadius: { topLeft: 4, topRight: 4 } } },
            xaxis: { categories: {!! json_encode($chartLabels) !!}, labels: { style: { fontWeight: 600, fontSize: '12px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { fontWeight: 500, fontSize: '12px', colors: '#94a3b8' }, padding: 4 }, tickAmount: 4 },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 0, padding: { left: 8 } },
            legend: { position: 'bottom', labels: { colors: '#64748b', useSeriesColors: true, fontWeight: 600, fontSize: '11px', padding: 12 }, markers: { width: 10, height: 10, radius: 3, strokeWidth: 0 }, itemMargin: { horizontal: 6, vertical: 2 } },
            tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: 'Inter' }, y: { formatter: function(val) { return val + ' tasks'; } } },
            dataLabels: { enabled: false }
        }).render();
    }
});
</script>
@endsection
