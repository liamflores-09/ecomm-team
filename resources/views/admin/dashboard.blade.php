@extends('layouts.app')

@section('title', 'Admin Dashboard — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='20' x2='18' y2='10'/><line x1='12' y1='20' x2='12' y2='4'/><line x1='6' y1='20' x2='6' y2='14'/></svg>">
@endsection

@section('styles')
<style>
/* ── KPIs ─────────────────────────────────────────────── */
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.125rem; margin-bottom: 1.25rem; }
.kpi-card {
    background: var(--card); border-radius: 10px; padding: 1.375rem 1.25rem;
    border: 1px solid var(--border); transition: border-color 0.18s, box-shadow 0.18s;
    position: relative; overflow: hidden;
}
.kpi-card:hover { border-color: var(--border-strong); box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
.kpi-card[data-color="indigo"]  { border-top: 3px solid #6366f1; }
.kpi-card[data-color="emerald"] { border-top: 3px solid #10b981; }
.kpi-card[data-color="sky"]     { border-top: 3px solid #0ea5e9; }
.kpi-card[data-color="amber"]   { border-top: 3px solid #f59e0b; }
.kpi-card[data-color="rose"]    { border-top: 3px solid #f43f5e; }
.kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.875rem; }
.kpi-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted-foreground); }
.kpi-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; color: white; flex-shrink: 0; }
.kpi-icon[data-color="indigo"]  { background: #6366f1; }
.kpi-icon[data-color="emerald"] { background: #10b981; }
.kpi-icon[data-color="sky"]     { background: #0ea5e9; }
.kpi-icon[data-color="amber"]   { background: #f59e0b; }
.kpi-icon[data-color="rose"]    { background: #f43f5e; }
.kpi-value { font-size: 1.7rem; font-weight: 800; line-height: 1; margin-bottom: 0.4rem; font-family: 'Space Grotesk', sans-serif; }
.kpi-bottom { display: flex; align-items: center; justify-content: space-between; }
.kpi-sub { font-size: 0.72rem; color: var(--muted-foreground); font-weight: 500; }
.kpi-spark { width: 60px; height: 22px; }
.kpi-trend { display: inline-flex; align-items: center; gap: 3px; font-weight: 700; font-size: 0.68rem; padding: 3px 8px; border-radius: 9999px; }
.kpi-trend.up   { background: #f0fdf4; color: #15803d; }
.kpi-trend.down { background: #fef2f2; color: #b91c1c; }

/* ── Welcome banner ───────────────────────────────────── */
.welcome-banner {
    background: var(--card); border: 1px solid var(--border); border-radius: 12px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1.5rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
}
.welcome-banner h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem; font-family: 'Space Grotesk', sans-serif; }
.welcome-banner p  { color: var(--muted-foreground); font-size: 0.88rem; font-weight: 500; margin: 0; }
.wb-stats { display: flex; align-items: center; gap: 1.5rem; flex-shrink: 0; }
.wb-stat { text-align: right; }
.wb-stat-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); margin-bottom: 2px; }
.wb-stat-val { font-size: 1.1rem; font-weight: 800; font-family: 'Space Grotesk', sans-serif; }
.wb-divider { width: 1px; height: 36px; background: var(--border); }

/* ── Two-col layouts ──────────────────────────────────── */
.dash-2col      { display: grid; grid-template-columns: 57fr 43fr; gap: 1.125rem; margin-bottom: 1.25rem; align-items: start; }
.dash-2col-main { display: grid; grid-template-columns: 60fr 40fr; gap: 1.125rem; align-items: start; }

/* ── Section heading row ──────────────────────────────── */
.dash-heading {
    display: flex; align-items: baseline; justify-content: space-between;
    margin-bottom: 0.875rem;
}
.dash-heading h4 { font-size: 0.88rem; font-weight: 700; margin: 0 0 2px; }
.dash-heading p  { font-size: 0.72rem; color: var(--muted-foreground); margin: 0; }
.dash-heading a  { font-size: 0.72rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; display: flex; align-items: center; gap: 4px; transition: color 0.15s; white-space: nowrap; }
.dash-heading a:hover { color: var(--foreground); }

/* ── Team Health ──────────────────────────────────────── */
.health-card { background: var(--card); border-radius: 10px; border: 1px solid var(--border); padding: 1.25rem; }
.health-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.125rem; }
.health-header h4 { font-size: 0.88rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
.health-header .pulse { width: 7px; height: 7px; border-radius: 50%; background: #10b981; animation: pulseAnim 2s infinite; flex-shrink: 0; }
@keyframes pulseAnim { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }
.health-bar-wrap { width: 100%; height: 5px; background: var(--border); border-radius: 9999px; overflow: hidden; margin-bottom: 1rem; }
.health-bar { height: 100%; border-radius: 9999px; transition: width 1s ease; }
.health-avatars { display: flex; align-items: center; flex-wrap: wrap; gap: 2px; }
.health-avatar { width: 34px; height: 34px; border-radius: 50%; border: 2px solid var(--card); object-fit: cover; display: block; transition: transform 0.18s; }
.health-avatar.logged  { border-color: #10b981; }
.health-avatar.pending { border-color: var(--destructive); opacity: 0.45; }
.avatar-tip-wrap { position: relative; display: inline-flex; margin-left: -8px; }
.avatar-tip-wrap:first-child { margin-left: 0; }
.avatar-tip-wrap:hover { z-index: 10; }
.avatar-tip-wrap:hover .health-avatar { transform: translateY(-2px); }
.avatar-tip-wrap::after {
    content: attr(data-tooltip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%);
    background: var(--foreground); color: var(--card); padding: 4px 10px; border-radius: 6px;
    font-size: 0.7rem; font-weight: 600; white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 20;
}
.avatar-tip-wrap:hover::after { opacity: 1; }
.health-legend { display: flex; gap: 1.25rem; margin-top: 0.75rem; font-size: 0.72rem; color: var(--muted-foreground); font-weight: 500; }
.health-legend .dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 4px; flex-shrink: 0; }

/* ── Who Logged Today ─────────────────────────────────── */
.wlt-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.wlt-header { display: flex; align-items: center; justify-content: space-between; padding: 0.7rem 1.125rem; background: var(--muted); border-bottom: 1px solid var(--border); }
.wlt-title { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--foreground); }
.wlt-count { font-size: 0.68rem; font-weight: 700; color: var(--muted-foreground); }
.wlt-list { max-height: 296px; overflow-y: auto; }
.wlt-item { display: flex; align-items: center; gap: 0.7rem; padding: 0.575rem 1.125rem; border-bottom: 1px solid var(--border); transition: background 0.12s; }
.wlt-item:last-child { border-bottom: none; }
.wlt-item:hover { background: var(--muted); }
.wlt-avatar { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid transparent; }
.wlt-avatar.logged  { border-color: #10b981; }
.wlt-avatar.pending { border-color: var(--border); opacity: 0.5; }
.wlt-meta { flex: 1; min-width: 0; }
.wlt-name { font-size: 0.8rem; font-weight: 600; color: var(--foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.wlt-role { font-size: 0.6rem; color: var(--muted-foreground); font-weight: 500; }
.wlt-tasks { font-size: 0.7rem; font-weight: 700; color: var(--muted-foreground); flex-shrink: 0; }
.wlt-status { font-size: 0.6rem; font-weight: 700; padding: 2px 7px; border-radius: 9999px; flex-shrink: 0; }
.wlt-status.logged  { background: #dcfce7; color: #15803d; }
.wlt-status.pending { background: var(--muted); color: var(--muted-foreground); border: 1px solid var(--border); }

/* ── Role Overview ────────────────────────────────────── */
.role-ov-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
.role-ov-card {
    background: var(--card); border-radius: 10px; padding: 1.125rem;
    border: 1px solid var(--border); transition: border-color 0.18s;
}
.role-ov-card:hover { border-color: var(--border-strong); }
.role-ov-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem; }
.role-ov-members { font-size: 0.68rem; font-weight: 600; color: var(--muted-foreground); }
.role-ov-link { font-size: 0.7rem; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 4px; transition: gap 0.15s; margin-top: 0.25rem; }
.role-ov-link:hover { gap: 7px; }

/* ── Activity feed ────────────────────────────────────── */
.activity-card { background: var(--card); border-radius: 10px; border: 1px solid var(--border); overflow: hidden; }
.activity-header { display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border); }
.activity-header h4 { font-size: 0.88rem; font-weight: 700; margin: 0; }
.activity-header a  { font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; transition: color 0.15s; }
.activity-header a:hover { color: var(--foreground); }
.activity-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.8rem 1.25rem; border-bottom: 1px solid var(--border); transition: background 0.12s; }
.activity-item:last-child { border-bottom: none; }
.activity-item:hover { background: var(--muted); }
.activity-dot-wrap { display: flex; flex-direction: column; align-items: center; padding-top: 3px; }
.activity-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.activity-line { width: 1px; flex: 1; background: var(--border); margin-top: 5px; min-height: 18px; }
.activity-content { flex: 1; min-width: 0; }
.activity-title { font-weight: 500; font-size: 0.82rem; }
.activity-meta  { font-size: 0.72rem; color: var(--muted-foreground); margin-top: 2px; }
.activity-time  { font-size: 0.72rem; color: var(--muted-foreground); white-space: nowrap; flex-shrink: 0; }
.activity-empty { text-align: center; padding: 2rem 1rem; color: var(--muted-foreground); font-size: 0.82rem; }
.activity-empty i { font-size: 1.25rem; display: block; margin-bottom: 0.5rem; opacity: 0.3; }

/* ── Quick Actions ────────────────────────────────────── */
.qa-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.qa-card-header { display: flex; align-items: center; gap: 0.4rem; padding: 0.7rem 1.125rem; background: var(--muted); border-bottom: 1px solid var(--border); font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--foreground); }
.qa-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 1.125rem; border-bottom: 1px solid var(--border); text-decoration: none; color: var(--foreground); transition: background 0.12s; }
.qa-item:last-child { border-bottom: none; }
.qa-item:hover { background: var(--muted); }
.qa-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; color: #fff; flex-shrink: 0; }
.qa-label { flex: 1; font-size: 0.82rem; font-weight: 600; }
.qa-arrow { font-size: 0.6rem; color: var(--muted-foreground); transition: all 0.15s; }
.qa-item:hover .qa-arrow { color: var(--foreground); transform: translateX(2px); }

/* ── Responsive ───────────────────────────────────────── */
@media (max-width: 640px)  { .wb-stats { display: none; } }
@media (max-width: 1100px) { .role-ov-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { .role-ov-grid { grid-template-columns: 1fr; } }
@media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { .kpi-grid { grid-template-columns: 1fr; } }
@media (max-width: 900px)  { .dash-2col, .dash-2col-main { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
@php
$todayLogMap = $todayLogs->keyBy('user_id');
@endphp
<x-sidebar active="admin.dashboard" :isAdmin="true" />

<div class="main-content">

    @if(now()->dayOfWeek === 0)
    <div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.125rem;background:#fef9ec;border:1px solid #fde68a;border-radius:10px;color:#92400e;font-size:0.85rem;font-weight:500;margin-bottom:1.125rem;">
        <i class="fas fa-umbrella-beach" style="font-size:1rem;flex-shrink:0;"></i>
        <span><strong>Today is Sunday — Rest Day (RDO).</strong> No EOD submissions are expected.</span>
    </div>
    @endif

    {{-- ── Welcome banner ── --}}
    <div class="welcome-banner anim-up">
        <div>
            <h2>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $user->first_name }} <span style="font-weight:400;opacity:0.4;">👋</span></h2>
            <p>{{ now()->format('l, M d, Y') }} &mdash; Team overview at a glance</p>
        </div>
        <div class="wb-stats">
            @if(now()->dayOfWeek === 0)
            <div class="wb-stat">
                <div class="wb-stat-val" style="font-size:1.1rem;color:#d97706;">RDO</div>
                <div class="wb-stat-label">Rest Day</div>
            </div>
            @else
            <div class="wb-stat">
                <div class="wb-stat-val" style="color:{{ $healthColor }};">{{ $healthPct }}%</div>
                <div class="wb-stat-label">Team Logged</div>
            </div>
            <div class="wb-divider"></div>
            <div class="wb-stat">
                <div class="wb-stat-val">{{ $todayLogged }}<span style="font-size:0.9rem;color:var(--muted-foreground);font-weight:500;">/{{ $nonManagerCount }}</span></div>
                <div class="wb-stat-label">Members In</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── KPIs ── --}}
    <div class="kpi-grid anim-up d1">

        {{-- This Month --}}
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

        {{-- Today --}}
        <div class="kpi-card" data-color="emerald">
            <div class="kpi-top">
                <span class="kpi-label">Today</span>
                <div class="kpi-icon" data-color="emerald">
                    <i class="fas {{ now()->dayOfWeek === 0 ? 'fa-umbrella-beach' : 'fa-clipboard-check' }}"></i>
                </div>
            </div>
            @if(now()->dayOfWeek === 0)
            <div class="kpi-value" style="font-size:1.2rem;">Rest Day</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">No EOD expected</span>
                <span class="kpi-trend" style="background:#fef3c7;color:#d97706;"><i class="fas fa-moon"></i> RDO</span>
            </div>
            @else
            <div class="kpi-value">{{ $todayLogged }}<span style="font-size:0.9rem;color:var(--muted-foreground);font-weight:500;">/{{ $nonManagerCount }}</span></div>
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

        {{-- Avg / Person --}}
        <div class="kpi-card" data-color="sky">
            <div class="kpi-top">
                <span class="kpi-label">Avg / Person</span>
                <div class="kpi-icon" data-color="sky"><i class="fas fa-chart-simple"></i></div>
            </div>
            <div class="kpi-value">{{ $avgTasksPerson }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">tasks per member</span>
                <div id="sparkAvg" class="kpi-spark"></div>
            </div>
        </div>

        {{-- Top Contributor --}}
        <div class="kpi-card" data-color="amber">
            <div class="kpi-top">
                <span class="kpi-label">Top This Month</span>
                <div class="kpi-icon" data-color="amber"><i class="fas fa-trophy"></i></div>
            </div>
            @if($topContributor)
            <div class="kpi-value" style="font-size:1.25rem;letter-spacing:-0.3px;">{{ $topContributor->first_name }}</div>
            <div class="kpi-bottom">
                <span class="kpi-sub">{{ number_format($topContributor->total) }} tasks</span>
                <span class="kpi-trend up"><i class="fas fa-crown"></i> MVP</span>
            </div>
            @else
            <div class="kpi-value" style="font-size:1.2rem;color:var(--muted-foreground);">—</div>
            <div class="kpi-bottom"><span class="kpi-sub">No logs yet</span></div>
            @endif
        </div>

    </div>

    {{-- ── Team Pulse + Who Logged Today ── --}}
    <div class="dash-2col anim-up d2">

        {{-- Team Health --}}
        <div class="health-card">
            <div class="health-header">
                <h4>
                    @if(now()->dayOfWeek !== 0)<div class="pulse"></div>@endif
                    Today's Pulse
                </h4>
                @if(now()->dayOfWeek === 0)
                <span style="font-size:0.72rem;font-weight:700;color:#d97706;display:flex;align-items:center;gap:5px;"><i class="fas fa-umbrella-beach"></i> Rest Day</span>
                @else
                <span style="font-size:0.72rem;font-weight:700;color:{{ $healthColor }};">{{ $healthPct }}% logged in</span>
                @endif
            </div>

            @if(now()->dayOfWeek === 0)
            <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;padding:0.5rem 0;text-align:center;">
                <div class="health-avatars" style="opacity:0.3;filter:grayscale(1);justify-content:center;flex-wrap:wrap;gap:4px;">
                    @foreach($allMembers as $m)
                    <span class="avatar-tip-wrap" data-tooltip="{{ $m->first_name }} · RDO">
                        <img class="health-avatar" style="margin-left:0;object-fit:cover;" src="{{ $m->avatarUrl() }}" alt="{{ $m->first_name }}">
                    </span>
                    @endforeach
                </div>
                <p style="margin:0;font-size:0.78rem;color:var(--muted-foreground);">The team is on Rest Day. No EOD submissions expected on Sundays.</p>
            </div>
            @else
            <div class="health-bar-wrap">
                <div class="health-bar" style="width:{{ $healthPct }}%;background:{{ $healthColor }};"></div>
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                <div class="health-avatars">
                    @foreach($allMembers as $m)
                    @php $isL = in_array($m->id, $loggedUserIds); @endphp
                    <span class="avatar-tip-wrap" data-tooltip="{{ $m->first_name }} {{ $m->last_name }} · {{ $isL ? 'Logged' : 'Pending' }}">
                        <img class="health-avatar {{ $isL ? 'logged' : 'pending' }}" src="{{ $m->avatarUrl() }}" style="object-fit:cover;" alt="{{ $m->first_name }}">
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="health-legend">
                <span><div class="dot" style="background:#10b981;"></div> Logged ({{ $todayLogged }})</span>
                <span><div class="dot" style="background:var(--destructive);"></div> Pending ({{ $todayPending }})</span>
            </div>
            @endif
        </div>

        {{-- Who Logged Today --}}
        <div class="wlt-card">
            <div class="wlt-header">
                <span class="wlt-title">Who Logged Today</span>
                <span class="wlt-count">{{ $todayLogged }}/{{ $nonManagerCount }}</span>
            </div>
            <div class="wlt-list">
                @foreach($allMembers->sortByDesc(fn($m) => in_array($m->id, $loggedUserIds)) as $m)
                @php
                    $isL = in_array($m->id, $loggedUserIds);
                    $ml  = $todayLogMap->get($m->id);
                    $mt  = $ml ? ($ml->task_1 + $ml->task_2 + $ml->task_3 + $ml->task_4 + $ml->task_5) : null;
                @endphp
                <div class="wlt-item">
                    <img src="{{ $m->avatarUrl() }}" class="wlt-avatar {{ $isL ? 'logged' : 'pending' }}" alt="" style="object-fit:cover;">
                    <div class="wlt-meta">
                        <div class="wlt-name">{{ $m->first_name }} {{ $m->last_name }}</div>
                        <div class="wlt-role">{{ ucfirst($m->role) }}</div>
                    </div>
                    @if($isL && $mt !== null)
                    <span class="wlt-tasks">{{ $mt }} tasks</span>
                    @endif
                    <span class="wlt-status {{ $isL ? 'logged' : 'pending' }}">{{ $isL ? 'Logged' : 'Pending' }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Role Activity ── --}}
    @php
    $roleHexColors = [
        'content'    => '#0ea5e9',
        'graphics'   => '#f59e0b',
        'backend'    => '#f43f5e',
        'researcher' => '#10b981',
    ];
    @endphp
    <div class="dash-heading anim-up d3">
        <div>
            <h4>Role Activity — Last 7 Days</h4>
            <p>Total tasks output per role per day. <span style="color:#f43f5e;font-weight:600;">Sunday = RDO</span></p>
        </div>
        <a href="{{ route('admin.reports') }}">Full Reports <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i></a>
    </div>
    <div class="role-ov-grid anim-up d3">
        @foreach($roleBreakdown as $r)
        @php $hex = $roleHexColors[$r['role']] ?? '#6366f1'; @endphp
        <div class="role-ov-card" style="border-top:3px solid {{ $hex }};">
            <div class="role-ov-header">
                <span class="role-badge {{ $r['role'] }}">{{ ucfirst($r['role']) }}</span>
                <span class="role-ov-members">{{ $r['members'] }} {{ $r['members'] === 1 ? 'member' : 'members' }}</span>
            </div>
            <p style="font-size:0.65rem;color:var(--muted-foreground);margin:0.25rem 0 0;font-weight:500;">Daily output — last 7 days</p>
            <div id="roleChart-{{ $r['role'] }}" style="height:100px;margin:0 -0.375rem;"></div>
            <a href="{{ route('admin.reports') }}?role={{ $r['role'] }}" class="role-ov-link">
                View Reports <i class="fas fa-arrow-right" style="font-size:0.58rem;"></i>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ── Bottom two-col: Activity + Quick Actions ── --}}
    <div class="dash-2col-main anim-up d4">

        {{-- Recent Activity --}}
        <div class="activity-card">
            <div class="activity-header">
                <h4>Recent Activity</h4>
                <a href="{{ route('admin.daily-logs') }}">View All <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i></a>
            </div>
            @forelse($recentActivity as $activity)
            @php
                $actColors = [
                    'eod_submitted' => '#10b981', 'eod_updated'  => '#3b82f6',
                    'eod_deleted'   => '#ef4444', 'user_created' => '#8b5cf6',
                    'user_updated'  => '#f59e0b', 'user_deleted' => '#ef4444',
                ];
                $ac = $actColors[$activity->type] ?? '#64748b';
            @endphp
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot" style="background:{{ $ac }};"></div>
                    @if(!$loop->last)<div class="activity-line"></div>@endif
                </div>
                <div class="activity-content">
                    <div class="activity-title">{{ $activity->description }}</div>
                    <div class="activity-meta">{{ $activity->user->first_name ?? 'System' }} · {{ ucfirst(str_replace('_', ' ', $activity->type)) }}</div>
                </div>
                <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div class="activity-empty"><i class="fas fa-inbox"></i>No activity yet</div>
            @endforelse
        </div>

        {{-- Top Contributor + Quick Actions --}}
        <div>
            {{-- Quick Actions --}}
            <div class="qa-card">
                <div class="qa-card-header"><i class="fas fa-bolt" style="color:#f59e0b;font-size:0.6rem;"></i> Quick Actions</div>
                <a href="{{ route('admin.users') }}" class="qa-item">
                    <div class="qa-icon" style="background:#6366f1;"><i class="fas fa-user-plus"></i></div>
                    <span class="qa-label">Manage Users</span>
                    <i class="fas fa-chevron-right qa-arrow"></i>
                </a>
                <a href="{{ route('admin.daily-logs') }}" class="qa-item">
                    <div class="qa-icon" style="background:#0ea5e9;"><i class="fas fa-clipboard-list"></i></div>
                    <span class="qa-label">Daily Logs</span>
                    <i class="fas fa-chevron-right qa-arrow"></i>
                </a>
                <a href="{{ route('admin.attendance') }}" class="qa-item">
                    <div class="qa-icon" style="background:#10b981;"><i class="fas fa-calendar-check"></i></div>
                    <span class="qa-label">Attendance</span>
                    <i class="fas fa-chevron-right qa-arrow"></i>
                </a>
                <a href="{{ route('admin.reports') }}" class="qa-item">
                    <div class="qa-icon" style="background:#f59e0b;"><i class="fas fa-chart-pie"></i></div>
                    <span class="qa-label">Reports</span>
                    <i class="fas fa-chevron-right qa-arrow"></i>
                </a>
                <a href="{{ route('team') }}" class="qa-item">
                    <div class="qa-icon" style="background:#f43f5e;"><i class="fas fa-users"></i></div>
                    <span class="qa-label">The Team</span>
                    <i class="fas fa-chevron-right qa-arrow"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var style = getComputedStyle(document.documentElement);
    var isDark = document.documentElement.classList.contains('dark')
        || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);

    // Sparkline
    var sparkEl = document.getElementById('sparkAvg');
    if (sparkEl) {
        var sparkData = {!! json_encode($sparkData) !!};
        new ApexCharts(sparkEl, {
            chart: { type: 'area', height: 22, width: 60, sparkline: { enabled: true } },
            series: [{ data: sparkData }],
            colors: ['#6366f1'],
            stroke: { width: 2, curve: 'smooth' },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            tooltip: { enabled: false }
        }).render();
    }

    // Role charts
    var weekLabels    = {!! json_encode($weekLabels) !!};
    var sundayIdx     = {!! json_encode($weekSundayIndices) !!};
    var roleCharts    = {!! json_encode($roleBreakdown->map(fn($r) => ['role' => $r['role'], 'series' => $r['series']])) !!};
    var roleHexColors = { content: '#0ea5e9', graphics: '#f59e0b', backend: '#f43f5e', researcher: '#10b981' };
    var labelColors   = weekLabels.map(function(_, i) { return sundayIdx.indexOf(i) !== -1 ? '#ef4444' : '#94a3b8'; });

    roleCharts.forEach(function (r) {
        var el = document.getElementById('roleChart-' + r.role);
        if (!el) return;
        new ApexCharts(el, {
            chart: { type: 'bar', height: 100, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#94a3b8' },
            series: [{ name: 'Tasks', data: r.series }],
            colors: [roleHexColors[r.role] || '#6366f1'],
            plotOptions: { bar: { columnWidth: '62%', borderRadius: 3, borderRadiusApplication: 'end' } },
            xaxis: {
                categories: weekLabels,
                labels: { style: { fontSize: '10px', fontWeight: 600, colors: labelColors } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { show: false, min: 0 },
            grid: { show: false, padding: { left: 2, right: 2, top: 0, bottom: 0 } },
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'light', style: { fontSize: '12px' },
                x: { formatter: function(v, o) { var i = o.dataPointIndex; return sundayIdx.indexOf(i) !== -1 ? weekLabels[i] + ' (RDO)' : weekLabels[i]; } },
                y: { formatter: function(v) { return v + ' tasks'; } }
            }
        }).render();
    });
});
</script>
@endsection
