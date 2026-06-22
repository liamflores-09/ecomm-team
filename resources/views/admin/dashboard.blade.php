@extends('layouts.app')

@section('title', 'Admin Dashboard — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    /* KPIs */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.75rem; }
    .kpi-card {
        background: var(--card); border-radius: 8px; padding: 1.5rem;
        border: 1px solid var(--border-light); transition: border-color 0.2s; position: relative;
    }
    .kpi-card:hover { border-color: var(--foreground); }
    .kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .kpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: white; background: var(--primary); }
    .kpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1; margin-bottom: 0.375rem; font-family: 'Space Grotesk', sans-serif; }
    .kpi-bottom { display: flex; align-items: center; justify-content: space-between; }
    .kpi-sub { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .kpi-spark { width: 60px; height: 24px; }
    .kpi-trend { display: inline-flex; align-items: center; gap: 3px; font-weight: 700; font-size: 0.7rem; padding: 3px 8px; border-radius: 9999px; }
    .kpi-trend.up   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .kpi-trend.down { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .kpi-card[data-color="indigo"]  { border-top: 2px solid var(--primary); }
    .kpi-card[data-color="emerald"] { border-top: 2px solid var(--success); }
    .kpi-card[data-color="sky"]     { border-top: 2px solid var(--primary); }
    .kpi-card[data-color="amber"]   { border-top: 2px solid var(--warning); }
    .kpi-card[data-color="rose"]    { border-top: 2px solid var(--destructive); }
    .kpi-icon[data-color="emerald"] { background: var(--success); }
    .kpi-icon[data-color="amber"]   { background: var(--warning); }
    .kpi-icon[data-color="rose"]    { background: var(--destructive); }

    /* Team Health */
    .health-card {
        background: var(--card); border-radius: 8px; border: 1px solid var(--border-light);
        padding: 1.5rem; margin-bottom: 1.75rem;
    }
    .health-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .health-header h4 { font-size: 0.9rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; font-family: 'Space Grotesk', sans-serif; }
    .health-header .pulse { width: 8px; height: 8px; border-radius: 50%; background: var(--success); animation: pulse 2s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    .health-bar-wrap { width: 100%; height: 6px; background: var(--border-light); border-radius: 9999px; overflow: hidden; margin-bottom: 1rem; }
    .health-bar { height: 100%; border-radius: 9999px; transition: width 1s ease; }
    .health-avatars { display: flex; align-items: center; gap: 0; }
    .health-avatar {
        width: 36px; height: 36px; border-radius: 50%; border: 2px solid var(--card);
        display: block; transition: transform 0.2s;
    }
    .health-avatar.logged  { border-color: var(--success); }
    .health-avatar.pending { border-color: var(--destructive); opacity: 0.5; }

    .avatar-tip-wrap { position: relative; display: inline-flex; margin-left: -10px; }
    .avatar-tip-wrap:first-child { margin-left: 0; }
    .avatar-tip-wrap:hover { z-index: 10; }
    .avatar-tip-wrap:hover .health-avatar { transform: translateY(-2px); }
    .avatar-tip-wrap::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: var(--foreground);
        color: var(--card);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s;
        z-index: 10;
    }
    .avatar-tip-wrap:hover::after { opacity: 1; }

    .health-legend { display: flex; gap: 1.25rem; margin-top: 0.75rem; font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .health-legend .dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 4px; }

    /* Role Overview */
    .role-ov-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .role-ov-card {
        background: var(--card); border-radius: 8px; padding: 1.25rem; border: 1px solid var(--border-light);
        transition: border-color 0.2s;
    }
    .role-ov-card:hover { border-color: var(--foreground); }
    .role-ov-header { display: flex; align-items: center; justify-content: space-between; }
    .role-ov-members { font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); }
    .role-ov-link { font-size: 0.75rem; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 4px; transition: gap 0.15s; }
    .role-ov-link:hover { gap: 7px; }

    /* Quick Links */
    .ql-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .quick-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .quick-link {
        display: flex; align-items: center; gap: 0.875rem;
        background: var(--card); border-radius: 8px; padding: 1.25rem; text-decoration: none;
        color: var(--foreground); border: 1px solid var(--border-light); transition: border-color 0.2s;
    }
    .quick-link:hover { border-color: var(--foreground); }
    .ql-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: white; background: var(--primary); flex-shrink: 0; }
    .ql-text { flex: 1; }
    .ql-text strong { display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem; }
    .ql-text small { color: var(--muted-foreground); font-size: 0.78rem; font-weight: 500; line-height: 1.4; display: block; }
    .ql-arrow { color: var(--muted-foreground); font-size: 0.8rem; transition: all 0.2s; flex-shrink: 0; }
    .quick-link:hover .ql-arrow { color: var(--foreground); transform: translateX(3px); }

    /* Activity Feed */
    .activity-card { background: var(--card); border-radius: 8px; border: 1px solid var(--border-light); overflow: hidden; }
    .activity-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-light); }
    .activity-header h4 { font-size: 0.85rem; font-weight: 700; margin: 0; }
    .activity-header a { font-size: 0.8rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; transition: color 0.15s; }
    .activity-header a:hover { color: var(--foreground); }
    .activity-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border-light); transition: background 0.15s; }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: var(--background); }
    .activity-dot-wrap { display: flex; flex-direction: column; align-items: center; padding-top: 3px; }
    .activity-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .activity-line { width: 1px; flex: 1; background: var(--border-light); margin-top: 6px; min-height: 20px; }
    .activity-content { flex: 1; min-width: 0; }
    .activity-title { font-weight: 500; font-size: 0.85rem; }
    .activity-meta { font-size: 0.75rem; color: var(--muted-foreground); margin-top: 3px; }
    .activity-time { font-size: 0.75rem; color: var(--muted-foreground); white-space: nowrap; flex-shrink: 0; }

    /* Welcome bar */
    .welcome-banner {
        background: var(--card); border: 1px solid var(--border-light); border-radius: 8px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 1.5rem; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem;
    }
    .welcome-banner h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.375rem; font-family: 'Space Grotesk', sans-serif; }
    .welcome-banner p { color: var(--muted-foreground); font-size: 0.9rem; font-weight: 500; margin: 0; }
    .wb-stats { display: flex; align-items: center; gap: 1.5rem; flex-shrink: 0; }
    .wb-stat { text-align: right; }
    .wb-stat-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted-foreground); margin-bottom: 2px; }
    .wb-stat-val { font-size: 1.1rem; font-weight: 700; font-family: 'Space Grotesk', sans-serif; }
    .wb-divider { width: 1px; height: 36px; background: var(--border-light); }

    @media (max-width: 640px) { .wb-stats { display: none; } }
    @media (max-width: 1024px) { .role-ov-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px)  { .role-ov-grid { grid-template-columns: 1fr; } }
    @media (max-width: 1024px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px)  { .kpi-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<x-sidebar active="admin.dashboard" :isAdmin="true" />

<div class="main-content">
    @if(now()->dayOfWeek === 0)
    <div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.25rem;background:#fef9ec;border:1px solid #fde68a;border-radius:8px;color:#92400e;font-size:0.875rem;font-weight:500;margin-bottom:1.25rem;">
        <i class="fas fa-umbrella-beach" style="font-size:1rem;flex-shrink:0;"></i>
        <span><strong>Today is Sunday — Rest Day (RDO).</strong> No EOD submissions are expected. Charts mark today as an off day.</span>
    </div>
    @endif

    <!-- Header -->
    <div class="welcome-banner anim-up">
        <div>
            <h2>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}</h2>
            <p>{{ now()->format('l, M d, Y') }} &mdash; Team overview at a glance</p>
        </div>
        <div class="wb-stats">
            @if(now()->dayOfWeek === 0)
            <div class="wb-stat" style="text-align:right;">
                <div class="wb-stat-val" style="font-size:1.25rem;color:#d97706;">RDO</div>
                <div class="wb-stat-label">Rest Day — No log expected</div>
            </div>
            @else
            <div class="wb-stat">
                <div class="wb-stat-val" style="color: {{ $healthColor }};">{{ $healthPct }}%</div>
                <div class="wb-stat-label">Team Logged</div>
            </div>
            <div class="wb-divider"></div>
            <div class="wb-stat">
                <div class="wb-stat-val">{{ $todayLogged }}<span style="font-size: 1rem; color: var(--gray-400); font-weight: 500;">/{{ $nonManagerCount }}</span></div>
                <div class="wb-stat-label">Members In</div>
            </div>
            @endif
        </div>
    </div>

    <!-- KPIs with Sparklines -->
    <div class="kpi-grid anim-up d1">
        <div class="kpi-card" data-color="indigo">
            <div class="kpi-top">
                <span class="kpi-label">This Month</span>
                <div class="kpi-icon" data-color="indigo"><i class="fas fa-bolt"></i></div>
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
        <div class="kpi-card" data-color="emerald">
            <div class="kpi-top">
                <span class="kpi-label">Today</span>
                <div class="kpi-icon" data-color="emerald">
                    <i class="fas {{ now()->dayOfWeek === 0 ? 'fa-umbrella-beach' : 'fa-clipboard-check' }}"></i>
                </div>
            </div>
            @if(now()->dayOfWeek === 0)
            <div class="kpi-value" style="font-size:1.2rem;letter-spacing:-0.3px;">Rest Day</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">No EOD log expected</span>
                <span class="kpi-trend" style="background:#fef3c7;color:#d97706;"><i class="fas fa-moon"></i> RDO</span>
            </div>
            @else
            <div class="kpi-value">{{ $todayLogged }}<span style="font-size: 0.9rem; color: var(--muted-foreground); font-weight: 500;">/{{ $nonManagerCount }}</span></div>
            <div class="kpi-bottom">
                <span class="kpi-sub">members logged in</span>
                @if($todayPending > 0)
                <span class="kpi-trend down"><i class="fas fa-clock"></i> {{ $todayPending }} pending</span>
                @else
                <span class="kpi-trend up"><i class="fas fa-check"></i> Complete</span>
                @endif
            </div>
            @endif
        </div>
        <div class="kpi-card" data-color="sky">
            <div class="kpi-top">
                <span class="kpi-label">Avg / Person</span>
                <div class="kpi-icon" data-color="sky"><i class="fas fa-user"></i></div>
            </div>
            <div class="kpi-value">{{ $avgTasksPerson }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">tasks per member</span>
                <div id="sparkAvg" class="kpi-spark"></div>
            </div>
        </div>
        <div class="kpi-card" data-color="rose">
            <div class="kpi-top">
                <span class="kpi-label">Team Size</span>
                <div class="kpi-icon" data-color="rose"><i class="fas fa-users"></i></div>
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
            <h4>
                @if(now()->dayOfWeek !== 0)<div class="pulse"></div>@endif
                Today's Pulse
            </h4>
            @if(now()->dayOfWeek === 0)
            <span style="font-size:0.75rem;font-weight:700;color:#d97706;display:flex;align-items:center;gap:5px;">
                <i class="fas fa-umbrella-beach"></i> Rest Day (RDO)
            </span>
            @else
            <span style="font-size: 0.75rem; font-weight: 700; color: {{ $healthColor }};">{{ $healthPct }}% logged in</span>
            @endif
        </div>

        @if(now()->dayOfWeek === 0)
        <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;padding:0.75rem 0 0.5rem;text-align:center;">
            <div class="health-avatars" style="opacity:0.35;filter:grayscale(1);justify-content:center;">
                @foreach($allMembers as $m)
                <span class="avatar-tip-wrap" data-tooltip="{{ $m->first_name }} {{ $m->last_name }} · Off (RDO)">
                    <img class="health-avatar" style="border-color:var(--gray-200);"
                         src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $m->gender === 'female' ? $m->username . 'Female' : $m->username }}"
                         alt="{{ $m->first_name }}">
                </span>
                @endforeach
            </div>
            <p style="margin:0;font-size:0.8rem;color:var(--muted-foreground);line-height:1.5;">
                The team is on Rest Day (RDO). No EOD submissions are expected on Sundays.
            </p>
        </div>

        @else
        <div class="health-bar-wrap">
            <div class="health-bar" style="width: {{ $healthPct }}%; background: {{ $healthColor }};"></div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="health-avatars">
                @foreach($allMembers as $m)
                    @php $isLogged = in_array($m->id, $loggedUserIds); @endphp
                    <span class="avatar-tip-wrap" data-tooltip="{{ $m->first_name }} {{ $m->last_name }} · {{ $isLogged ? 'Logged' : 'Pending' }}">
                        <img class="health-avatar {{ $isLogged ? 'logged' : 'pending' }}"
                             src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $m->gender === 'female' ? $m->username . 'Female' : $m->username }}"
                             alt="{{ $m->first_name }}">
                    </span>
                @endforeach
            </div>
            <div class="health-legend">
                <span><div class="dot" style="background: var(--emerald);"></div> Logged ({{ $todayLogged }})</span>
                <span><div class="dot" style="background: var(--rose);"></div> Pending ({{ $todayPending }})</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Role Overview -->
    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:0.875rem;">
        <div>
            <h4 style="font-size:0.9rem;font-weight:700;margin:0 0 2px;">Role Activity — Last 7 Days</h4>
            <p style="font-size:0.75rem;color:var(--muted-foreground);margin:0;">Total tasks output per role per day. <span style="color:var(--rose);font-weight:600;">Sunday = RDO</span> — no output expected.</p>
        </div>
        <a href="{{ route('admin.reports') }}" style="font-size:0.75rem;font-weight:600;color:var(--muted-foreground);text-decoration:none;">Full Reports <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i></a>
    </div>
    @php
        $roleColors = [
            'lead'       => 'var(--indigo)',
            'content'    => 'var(--sky)',
            'graphics'   => 'var(--amber)',
            'backend'    => 'var(--rose)',
            'researcher' => 'var(--emerald)',
        ];
    @endphp
    <div class="role-ov-grid anim-up d3">
        @foreach($roleBreakdown as $r)
        @php $color = $roleColors[$r['role']] ?? 'var(--gray-400)'; @endphp
        <div class="role-ov-card" style="border-top: 3px solid {{ $color }};">
            <div class="role-ov-header">
                <span class="role-badge {{ $r['role'] }}">{{ ucfirst($r['role']) }}</span>
                <span class="role-ov-members">{{ $r['members'] }} {{ $r['members'] === 1 ? 'member' : 'members' }}</span>
            </div>
            <p style="font-size:0.7rem;color:var(--muted-foreground);margin:-0.25rem 0 0;font-weight:500;line-height:1.4;">
                Daily task output (all fields) &mdash; last 7 days
            </p>
            <div id="roleChart-{{ $r['role'] }}" style="height: 110px; margin: 0 -0.5rem;"></div>
            <a href="{{ route('admin.reports') }}?role={{ $r['role'] }}" class="role-ov-link">
                View Reports <i class="fas fa-arrow-right" style="font-size: 0.6rem;"></i>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--muted-foreground);margin-bottom:0.75rem;">Quick Actions</div>
    <div class="quick-grid anim-up d4">
        <a href="{{ route('admin.users') }}" class="quick-link">
            <div class="ql-icon"><i class="fas fa-user-plus"></i></div>
            <div class="ql-text"><strong>Manage Users</strong><small>Add, edit, or deactivate team members</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        <a href="{{ route('admin.daily-logs') }}" class="quick-link">
            <div class="ql-icon"><i class="fas fa-clipboard-list"></i></div>
            <div class="ql-text"><strong>Daily Logs</strong><small>Browse &amp; review submitted EOD reports</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        <a href="{{ route('admin.reports') }}" class="quick-link">
            <div class="ql-icon"><i class="fas fa-chart-pie"></i></div>
            <div class="ql-text"><strong>Reports</strong><small>Weekly &amp; monthly task analytics per role</small></div>
            <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>
        <a href="{{ route('team') }}" class="quick-link">
            <div class="ql-icon"><i class="fas fa-users"></i></div>
            <div class="ql-text"><strong>The Team</strong><small>Browse member profiles &amp; roles</small></div>
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
    var style   = getComputedStyle(document.documentElement);
    var colors  = [
        style.getPropertyValue('--indigo').trim(),
        style.getPropertyValue('--sky').trim(),
        style.getPropertyValue('--emerald').trim(),
        style.getPropertyValue('--amber').trim(),
        style.getPropertyValue('--rose').trim(),
    ];

    // Sparkline in KPI card
    var sparkEl = document.getElementById('sparkAvg');
    if (sparkEl) {
        var sparkData = {!! json_encode($sparkData) !!};
        new ApexCharts(sparkEl, {
            chart: { type: 'area', height: 24, width: 60, sparkline: { enabled: true } },
            series: [{ data: sparkData }],
            colors: [colors[0]],
            stroke: { width: 2, curve: 'smooth' },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            tooltip: { enabled: false }
        }).render();
    }

    // Role mini charts
    var weekLabels       = {!! json_encode($weekLabels) !!};
    var sundayIndices    = {!! json_encode($weekSundayIndices) !!};
    var roleCharts       = {!! json_encode($roleBreakdown->map(fn($r) => ['role' => $r['role'], 'series' => $r['series']])) !!};
    var roleColorMap     = {
        lead: colors[0], content: colors[1], graphics: colors[3], backend: colors[4], researcher: colors[2]
    };

    // Build label color array — Sunday labels shown in rose/red as RDO
    var labelColors = weekLabels.map(function(_, i) {
        return sundayIndices.indexOf(i) !== -1 ? '#ef4444' : '#94a3b8';
    });

    roleCharts.forEach(function(r) {
        var el = document.getElementById('roleChart-' + r.role);
        if (!el) return;
        var c = roleColorMap[r.role] || colors[0];
        new ApexCharts(el, {
            chart: { type: 'bar', height: 110, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#94a3b8' },
            series: [{ name: 'Tasks', data: r.series }],
            colors: [c],
            plotOptions: { bar: { columnWidth: '65%', borderRadius: 3, borderRadiusApplication: 'end' } },
            xaxis: {
                categories: weekLabels,
                labels: { style: { fontSize: '10px', fontWeight: 600, colors: labelColors } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { show: false, min: 0 },
            grid: { show: false, padding: { left: 4, right: 4, top: 0, bottom: 0 } },
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'light', style: { fontSize: '12px', fontFamily: 'Inter' },
                x: { formatter: function(val, opts) {
                    var i = opts.dataPointIndex;
                    return sundayIndices.indexOf(i) !== -1 ? weekLabels[i] + ' (RDO)' : weekLabels[i];
                }},
                y: { formatter: function(v) { return v + ' tasks'; } }
            }
        }).render();
    });

});
</script>
@endsection
