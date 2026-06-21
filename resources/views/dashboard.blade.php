@extends('layouts.app')

@section('title', 'Dashboard — Ecomm Dept')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><rect x='3' y='14' width='7' height='7'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        border-radius: 12px;
        padding: 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -80px; right: -40px;
        width: 250px; height: 250px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        bottom: -60px; right: 120px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; position: relative; z-index: 1; font-weight: 800; }
    .welcome-banner p { color: rgba(255,255,255,0.8); font-weight: 500; font-size: 0.9rem; margin: 0; position: relative; z-index: 1; }
    .welcome-banner .wb-date { position: absolute; top: 2rem; right: 2.5rem; text-align: right; z-index: 1; }
    .welcome-banner .wb-date .wd-day { font-size: 2rem; font-weight: 800; line-height: 1; }
    .welcome-banner .wb-date .wd-month { font-size: 0.8rem; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.08em; }

    .section-divider { display: flex; align-items: center; gap: 0.75rem; margin: 2rem 0 1rem; }
    .section-divider .sd-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem; flex-shrink: 0; }
    .section-divider h4 { font-weight: 800; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .section-divider .sd-line { flex: 1; height: 2px; background: var(--muted); }

    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 0.25rem; }
    .stat-card { background: var(--white); border-radius: 12px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s; border: 2px solid transparent; }
    .stat-card:hover { transform: translateY(-2px); border-color: var(--border-strong); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0; }
    .stat-count { font-size: 1.75rem; font-weight: 800; line-height: 1; margin-bottom: 0.125rem; }
    .stat-label { font-size: 0.8rem; font-weight: 600; color: var(--gray-400); }

    .chart-section { background: var(--white); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border); }
    .chart-section #weeklyChart { width: 100% !important; }

    .quick-section { background: var(--white); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; }
    .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .quick-link { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: var(--muted); border-radius: 10px; text-decoration: none; color: var(--fg); transition: all 0.2s; border: 2px solid transparent; }
    .quick-link:hover { background: var(--primary); color: white; border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .quick-link:hover .ql-icon { background: rgba(255,255,255,0.2); color: white; }
    .quick-link:hover .ql-text small { color: rgba(255,255,255,0.75); }
    .quick-link:hover .ql-arrow { color: white; }
    .ql-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--white); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: var(--primary); transition: all 0.2s; flex-shrink: 0; }
    .ql-text { flex: 1; }
    .ql-text strong { display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 0.125rem; }
    .ql-text small { color: var(--gray-400); font-size: 0.75rem; font-weight: 500; }
    .ql-arrow { color: var(--gray-300); font-size: 0.85rem; transition: all 0.2s; }

    .logs-section { background: var(--white); border-radius: 12px; overflow: hidden; margin-bottom: 2rem; }
    .logs-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 2px solid var(--muted); }
    .logs-header h4 { font-weight: 800; font-size: 0.85rem; margin: 0; }
    .logs-header a { font-size: 0.8rem; font-weight: 600; color: var(--primary); text-decoration: none; }
    .logs-header a:hover { text-decoration: underline; }

    .logs-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .logs-table thead th { background: var(--muted); padding: 0.75rem 1rem; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); text-align: left; }
    .logs-table tbody td { padding: 0.875rem 1rem; border-top: 1px solid var(--muted); font-weight: 500; }
    .logs-table tbody tr:hover td { background: #F8FAFC; }
    .logs-table .num { font-weight: 700; text-align: center; font-variant-numeric: tabular-nums; }
    .logs-table .date-cell { font-weight: 700; white-space: nowrap; }

    .ref-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .ref-card { background: var(--white); border-radius: 12px; padding: 1.25rem; text-align: center; transition: all 0.2s; border: 2px solid transparent; text-decoration: none; color: var(--fg); }
    .ref-card:hover { transform: translateY(-2px); border-color: var(--border-strong); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .ref-card .rc-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1.1rem; color: white; }
    .ref-card h5 { font-weight: 700; font-size: 0.85rem; margin: 0; }

    .empty-state { text-align: center; padding: 2.5rem; color: var(--gray-400); font-weight: 500; font-size: 0.9rem; }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 0.75rem; color: var(--gray-200); }
    .empty-state a { color: var(--primary); font-weight: 600; }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .quick-links { grid-template-columns: 1fr; }
        .ref-grid { grid-template-columns: repeat(2, 1fr); }
        .welcome-banner { padding: 2rem; }
        .welcome-banner .wb-date { display: none; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
        .ref-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="dashboard" />

<div class="main-content">
    @php
        $bannerColors = ['content' => '#171717', 'lead' => '#262626', 'researcher' => '#333333', 'graphics' => '#1a1a1a', 'backend' => '#0d0d0d'];
        $bannerColor = $bannerColors[$user->role] ?? '#171717';
    @endphp

    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up" style="background: linear-gradient(135deg, {{ $bannerColor }} 0%, {{ $bannerColor }}dd 100%);">
        <div>
            <h2>Welcome back, {{ $user->first_name }}!</h2>
            @if($user->role === 'content')
            <p>Your content workspace — posting, data gathering, and daily logs.</p>
            @elseif($user->role === 'lead')
            <p>PR leadership — product research, team coordination, and task oversight.</p>
            @elseif($user->role === 'researcher')
            <p>Product research hub — advance PR, trade-in tracking, and vendor data.</p>
            @elseif($user->role === 'graphics')
            <p>Design dashboard — CVP, banners, drafts, and visual assets.</p>
            @elseif($user->role === 'backend')
            <p>Backend operations — bulk uploads, cross-listing, QC, and Q&A.</p>
            @endif
        </div>
        <div class="wb-date">
            <div class="wd-day">{{ now()->format('d') }}</div>
            <div class="wd-month">{{ now()->format('M Y') }}</div>
        </div>
    </div>

    <!-- Stats -->
    <div class="anim-up d1">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="stat-count">{{ $thisWeekTasks }}</div>
                    <div class="stat-label">Tasks This Week</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gray-500);"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="stat-count">{{ $thisMonthTasks }}</div>
                    <div class="stat-label">Tasks This Month</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: {{ $todayLog ? 'var(--primary)' : '#991B1B' }};"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <div class="stat-count" style="color: {{ $todayLog ? 'var(--fg)' : '#991B1B' }};">{{ $todayLog ? 'Done' : 'Pending' }}</div>
                    <div class="stat-label">Today's EOD</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Chart -->
    @php
        $chartDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayLogs = $recentLogs->filter(fn($l) => $l->date->toDateString() === $date->toDateString());
            $total = 0;
            foreach ($dayLogs as $dl) {
                $total += ($dl->task_1 ?? 0) + ($dl->task_2 ?? 0) + ($dl->task_3 ?? 0) + ($dl->task_4 ?? 0) + ($dl->task_5 ?? 0);
            }
            $chartDays[] = ['label' => $date->format('D'), 'total' => $total, 'isToday' => $i === 0];
        }
    @endphp
    <div class="chart-section anim-up d2">
        <div class="section-divider" style="margin-top: 0;">
            <div class="sd-icon" style="background: var(--primary);"><i class="fas fa-chart-bar"></i></div>
            <h4>Weekly Activity</h4>
            <div class="sd-line"></div>
        </div>
        <div id="weeklyChart"></div>
    </div>

    <!-- Quick Access -->
    <div class="section-divider anim-up d3">
        <div class="sd-icon" style="background: var(--primary);"><i class="fas fa-bolt"></i></div>
        <h4>Quick Access</h4>
        <div class="sd-line"></div>
    </div>

    <div class="quick-section anim-up d3">
        <div class="quick-links">
            @if($user->role === 'content')
            <a href="{{ route('posting-procedure') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-book-open"></i></div>
                <div class="ql-text"><strong>Posting Procedure</strong><small>8-step guide for product posting</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('data-gathering') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-folder-open"></i></div>
                <div class="ql-text"><strong>Data Gathering</strong><small>Collect product info and assets</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('ecommerce-requirements') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="ql-text"><strong>E-commerce Requirements</strong><small>Platform-specific posting rules</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text"><strong>Price Calculator</strong><small>Compute SRP across platforms</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            @else
            <a href="{{ route('end-of-day') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="ql-text"><strong>End-of-Day Report</strong><small>Log your daily tasks</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text"><strong>Price Calculator</strong><small>Compute SRP across platforms</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('team') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-users"></i></div>
                <div class="ql-text"><strong>The Team</strong><small>View your colleagues</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('important-links') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-link"></i></div>
                <div class="ql-text"><strong>Important Links</strong><small>Quick access to resources</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            @endif
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="section-divider anim-up d4">
        <div class="sd-icon" style="background: var(--gray-500);"><i class="fas fa-clock-rotate-left"></i></div>
        <h4>Recent Logs</h4>
        <div class="sd-line"></div>
    </div>

    <div class="logs-section anim-up d4">
        <div class="logs-header">
            <h4>Last {{ $recentLogs->count() }} Entries</h4>
            <a href="{{ route('end-of-day') }}">View EOD <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i></a>
        </div>
        @if($recentLogs->count())
        @php $tl = \App\Support\TaskLabels::get($user->role); @endphp
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Date</th>
                    @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                    <th style="text-align: center;">{{ $tl[$tk] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($recentLogs as $log)
                <tr>
                    <td class="date-cell">{{ $log->date->format('M d, Y') }}</td>
                    @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                    <td class="num">{{ $log->$tk }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            No logs yet. <a href="{{ route('end-of-day') }}">Submit your first EOD report</a>
        </div>
        @endif
    </div>

    <!-- Quick Reference -->
    <div class="section-divider anim-up d5">
        <div class="sd-icon" style="background: var(--primary);"><i class="fas fa-star"></i></div>
        <h4>Quick Reference</h4>
        <div class="sd-line"></div>
    </div>

    <div class="ref-grid anim-up d5">
        <a href="{{ route('end-of-day') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--primary);"><i class="fas fa-calendar-check"></i></div>
            <h5>EOD Report</h5>
        </a>
        <a href="{{ route('important-links') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--gray-500);"><i class="fas fa-link"></i></div>
            <h5>Important Links</h5>
        </a>
        <a href="{{ route('team') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--gray-700);"><i class="fas fa-users"></i></div>
            <h5>The Team</h5>
        </a>
        <a href="{{ route('price-calculator') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--primary);"><i class="fas fa-calculator"></i></div>
            <h5>Price Calculator</h5>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('weeklyChart');
    if (!el) return;
    var totals = {!! json_encode(collect($chartDays)->pluck('total')->toArray()) !!};
    var labels = {!! json_encode(collect($chartDays)->pluck('label')->toArray()) !!};
    var barColors = totals.map(function(v, i) {
        return i === totals.length - 1 ? '#6366f1' : '#c7d2fe';
    });
    new ApexCharts(el, {
        chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#64748b' },
        series: [{ name: 'Tasks', data: totals }],
        colors: barColors,
        plotOptions: {
            bar: { borderRadius: 8, columnWidth: '55%', distributed: true }
        },
        xaxis: {
            categories: labels,
            labels: { style: { fontWeight: 600, fontSize: '12px', colors: '#94a3b8' } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { fontWeight: 500, fontSize: '12px', colors: '#94a3b8' }, padding: 4 },
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
});
</script>
@endsection
