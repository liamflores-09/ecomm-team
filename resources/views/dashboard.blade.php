@extends('layouts.app')

@section('title', 'Dashboard — Ecomm Dept')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><rect x='3' y='14' width='7' height='7'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        border-radius: 8px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        min-height: 140px;
    }
    .wb-content { position: relative; z-index: 3; }
    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; font-weight: 700; }
    .welcome-banner p { color: rgba(255,255,255,0.8); font-weight: 500; font-size: 0.9rem; margin: 0; }
    .wb-date {
        color: rgba(255,255,255,0.7);
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.625rem;
    }
    .wb-avatar-zone {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 200px;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        pointer-events: none;
    }
    .wb-avatar {
        height: 140px;
        width: auto;
        display: block;
        position: relative;
        z-index: 1;
        margin-left: auto;
    }
    .wb-fade {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, var(--wb-color) 0%, transparent 70%);
        z-index: 2;
    }
    @media (max-width: 480px) {
        .wb-avatar-zone { display: none; }
    }

    .section-divider { display: flex; align-items: center; gap: 0.75rem; margin: 2rem 0 1rem; }
    .section-divider .sd-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; background: var(--primary); font-size: 0.75rem; flex-shrink: 0; }
    .section-divider h4 { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; font-family: 'Space Grotesk', sans-serif; }
    .section-divider .sd-line { flex: 1; height: 1px; background: var(--border-light); }

    .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 0.25rem; }
    .stat-card { background: var(--card); border-radius: 8px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: border-color 0.2s; border: 1px solid var(--border-light); }
    .stat-card:hover { border-color: var(--foreground); }
    .stat-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--primary); color: white; font-size: 1.1rem; flex-shrink: 0; }
    .stat-count { font-size: 1.75rem; font-weight: 700; line-height: 1; margin-bottom: 0.125rem; font-family: 'Space Grotesk', sans-serif; }
    .stat-label { font-size: 0.8rem; font-weight: 600; color: var(--muted-foreground); }

    .eod-status-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 8px;
        margin-bottom: 1.25rem;
        gap: 1rem;
    }
    .eod-status-strip.pending {
        background: var(--primary);
        border: 1px solid var(--primary);
        padding: 1.5rem 1.25rem;
    }
    .eod-status-strip.pending .ess-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    .eod-status-strip.pending .ess-title {
        color: white;
        font-size: 0.95rem;
        font-weight: 700;
    }
    .eod-status-strip.pending .ess-sub {
        color: rgba(255,255,255,0.75);
        font-size: 0.8rem;
        margin-top: 0.125rem;
    }
    .eod-status-strip.submitted {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-left: 4px solid var(--success);
        padding: 1rem 1.25rem;
    }
    .eod-status-strip.submitted .ess-title {
        color: var(--success);
        font-size: 0.9rem;
        font-weight: 700;
    }
    .ess-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .ess-icon {
        width: 36px;
        height: 36px;
        background: var(--success);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    .ess-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0 1rem;
        height: 36px;
        border-radius: 8px;
        border: 1.5px solid rgba(255,255,255,0.6);
        color: white;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: border-color 0.15s, background 0.15s;
        font-family: inherit;
    }
    .ess-btn:hover {
        border-color: white;
        background: rgba(255,255,255,0.1);
        color: white;
    }
    .ess-edit {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted-foreground);
        text-decoration: none;
        white-space: nowrap;
    }
    .ess-edit:hover { color: var(--foreground); }

    .chart-section { background: var(--card); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); }
    .chart-section #weeklyChart { width: 100% !important; }

    .quick-section { background: var(--card); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); }
    .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .quick-link { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: var(--background); border-radius: 8px; text-decoration: none; color: var(--foreground); transition: border-color 0.2s; border: 1px solid var(--border-light); }
    .quick-link:hover { border-color: var(--foreground); }
    .quick-link:hover .ql-icon { background: var(--foreground); }
    .ql-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background: var(--primary); color: white; flex-shrink: 0; transition: background 0.2s; }
    .ql-name { display: block; font-weight: 600; font-size: 0.875rem; color: var(--foreground); }
    .ql-desc { display: block; font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.125rem; }

    .logs-section { background: var(--card); border-radius: 8px; border: 1px solid var(--border-light); margin-bottom: 2rem; overflow: hidden; }
    .logs-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-light); }
    .logs-header h4 { font-size: 0.85rem; font-weight: 700; margin: 0; color: var(--foreground); }
    .logs-header a { font-size: 0.8rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; }
    .logs-header a:hover { color: var(--foreground); }
    .logs-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .logs-table thead tr { border-bottom: 1px solid var(--border-light); }
    .logs-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted-foreground); }
    .logs-table td { padding: 0.75rem 1rem; color: var(--foreground); border-bottom: 1px solid var(--border-light); }
    .logs-table tbody tr:last-child td { border-bottom: none; }
    .logs-table tbody tr:hover td { background: var(--background); }
    .date-cell { font-weight: 600; color: var(--foreground); white-space: nowrap; }
    .num { text-align: center; color: var(--muted-foreground); font-variant-numeric: tabular-nums; }
    .empty-state { padding: 2.5rem 1.5rem; text-align: center; color: var(--muted-foreground); font-size: 0.875rem; }
    .empty-state i { display: block; font-size: 1.5rem; margin-bottom: 0.75rem; opacity: 0.4; }
    .empty-state a { color: var(--foreground); font-weight: 600; text-decoration: underline; }
</style>
@endsection

@section('content')
@php
$roleColor = match($user->role) {
    'content'    => '#0ea5e9',
    'lead'       => '#6366f1',
    'researcher' => '#10b981',
    'graphics'   => '#f59e0b',
    'backend'    => '#f43f5e',
    default      => '#5757f8',
};
$avatarSeed = ($user->gender === 'female') ? $user->username . 'Female' : $user->username;
@endphp
<x-sidebar active="dashboard" />

<div class="main-content">
    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up" style="--wb-color: {{ $roleColor }}; background: var(--wb-color);">
        <div class="wb-content">
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
            <div class="wb-date">{{ now()->format('l, F j') }}</div>
        </div>
        <div class="wb-avatar-zone">
            <div class="wb-fade"></div>
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ urlencode($avatarSeed) }}" class="wb-avatar" alt="{{ $user->full_name }}">
        </div>
    </div>

    <!-- EOD Status Strip -->
    @if($todayLog)
    <div class="eod-status-strip submitted anim-up">
        <div class="ess-left">
            <div class="ess-icon"><i class="fas fa-circle-check"></i></div>
            <div class="ess-title">EOD submitted for today</div>
        </div>
        <a href="{{ route('end-of-day') }}" class="ess-edit">Edit <i class="fas fa-pencil"></i></a>
    </div>
    @else
    <div class="eod-status-strip pending anim-up">
        <div class="ess-left">
            <div class="ess-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="ess-title">EOD report not submitted yet</div>
                <div class="ess-sub">{{ now()->format('l, F j') }}</div>
            </div>
        </div>
        <a href="{{ route('end-of-day') }}" class="ess-btn">Submit EOD <i class="fas fa-arrow-right"></i></a>
    </div>
    @endif

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
                <div class="ql-text">
                    <span class="ql-name">Posting Procedure</span>
                    <span class="ql-desc">8-step guide for product posting</span>
                </div>
            </a>
            <a href="{{ route('data-gathering') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-folder-open"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Data Gathering</span>
                    <span class="ql-desc">Collect product info and assets</span>
                </div>
            </a>
            <a href="{{ route('ecommerce-requirements') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="ql-text">
                    <span class="ql-name">E-commerce Requirements</span>
                    <span class="ql-desc">Platform-specific posting rules</span>
                </div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Price Calculator</span>
                    <span class="ql-desc">Compute SRP across platforms</span>
                </div>
            </a>
            @else
            <a href="{{ route('end-of-day') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="ql-text">
                    <span class="ql-name">End-of-Day Report</span>
                    <span class="ql-desc">Log your daily tasks</span>
                </div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Price Calculator</span>
                    <span class="ql-desc">Compute SRP across platforms</span>
                </div>
            </a>
            <a href="{{ route('team') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-users"></i></div>
                <div class="ql-text">
                    <span class="ql-name">The Team</span>
                    <span class="ql-desc">View your colleagues</span>
                </div>
            </a>
            <a href="{{ route('important-links') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-link"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Important Links</span>
                    <span class="ql-desc">Quick access to resources</span>
                </div>
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
