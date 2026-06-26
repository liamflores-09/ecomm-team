@extends('layouts.app')

@section('title', 'Dashboard — Ecomm Dept')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><rect x='3' y='14' width='7' height='7'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        border-radius: 12px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        min-height: 140px;
    }
    .welcome-banner.collapsed {
        min-height: unset;
        padding: 0 1.25rem;
        height: 52px;
    }
    .wb-content {
        position: relative; z-index: 3;
        transition: opacity 0.22s ease, transform 0.25s ease;
        opacity: 1; transform: translateY(0);
    }
    .welcome-banner.collapsed .wb-content {
        opacity: 0; transform: translateY(-8px); pointer-events: none;
    }
    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; font-weight: 700; }
    .welcome-banner p { color: rgba(255,255,255,0.8); font-weight: 500; font-size: 0.9rem; margin: 0; }
    .wb-date { color: rgba(255,255,255,0.7); font-size: 0.8rem; font-weight: 600; margin-top: 0.625rem; }
    .wb-avatar-zone {
        position: absolute; right: 0; top: 0; bottom: 0; width: 200px;
        display: flex; align-items: flex-end; overflow: hidden; pointer-events: none;
        transition: opacity 0.18s ease;
    }
    .welcome-banner.collapsed .wb-avatar-zone { opacity: 0; }
    .wb-avatar { height: 140px; width: auto; display: block; position: relative; z-index: 1; margin-left: auto; }
    .wb-fade {
        position: absolute; inset: 0;
        background: linear-gradient(to right, var(--wb-color) 0%, transparent 70%);
        z-index: 2;
    }
    @media (max-width: 480px) { .wb-avatar-zone { display: none; } }

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

    /* ── Bento grid ── */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        grid-template-rows: auto auto;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: start;
    }
    .bento-ann {
        grid-column: 1; grid-row: 1 / 3;
        background: var(--card); border: 1px solid var(--border-light);
        border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;
    }
    .bento-ann-hd {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-light); flex-shrink: 0;
    }
    .bento-ann-hd h4 { font-size: 0.85rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .bento-ann-hd a { font-size: 0.78rem; font-weight: 600; color: var(--primary); text-decoration: none; }
    .bento-ann-item {
        display: block; text-decoration: none; color: inherit;
        padding: 0.875rem 1.25rem; border-top: 1px solid var(--border-light);
        transition: background 0.12s; cursor: pointer;
    }
    .bento-ann-item:first-of-type { border-top: none; }
    .bento-ann-item:hover { background: var(--muted); }
    .bento-ann-item.pinned { border-left: 3px solid #f59e0b; padding-left: calc(1.25rem - 3px); }
    .bento-ann-item-top { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem; }
    .bento-ann-pin-badge {
        display: inline-flex; align-items: center; gap: 0.2rem;
        padding: 0.1rem 0.375rem; background: rgba(245,158,11,0.1); color: #d97706;
        border-radius: 9999px; font-size: 0.57rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.04em; flex-shrink: 0;
    }
    .bento-ann-title { font-weight: 700; font-size: 0.875rem; line-height: 1.3; flex: 1; color: var(--fg); }
    .bento-ann-body {
        font-size: 0.79rem; color: var(--muted-foreground); font-weight: 500;
        line-height: 1.55; overflow: hidden; display: -webkit-box;
        -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 0.375rem;
    }
    .bento-ann-foot { display: flex; align-items: center; gap: 0.625rem; }
    .bento-ann-author { font-size: 0.68rem; font-weight: 600; color: var(--gray-400); }
    .bento-ann-expiry { font-size: 0.64rem; font-weight: 700; color: #d97706; display: inline-flex; align-items: center; gap: 0.2rem; }
    .bento-ann-empty { padding: 2.5rem 1.25rem; text-align: center; color: var(--muted-foreground); font-size: 0.82rem; }
    .bento-ann-empty i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.25; }

    /* ── Banner toggle ── */
    .wb-toggle {
        position: absolute; top: 0.875rem; right: 0.875rem; z-index: 4;
        width: 28px; height: 28px; border-radius: 7px;
        background: rgba(255,255,255,0.18); border: none; color: white;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        font-size: 0.72rem; transition: background 0.15s, top 0.35s cubic-bezier(0.4,0,0.2,1);
    }
    .wb-toggle:hover { background: rgba(255,255,255,0.3); }
    .wb-toggle i { transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
    .welcome-banner.collapsed .wb-toggle { top: 50%; transform: translateY(-50%); }
    .wb-collapsed-label {
        position: absolute; left: 1.25rem; right: 3rem;
        color: rgba(255,255,255,0.9); font-size: 0.82rem; font-weight: 700;
        opacity: 0; transform: translateY(4px);
        transition: opacity 0.25s ease 0.05s, transform 0.25s ease 0.05s;
        pointer-events: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .welcome-banner.collapsed .wb-collapsed-label {
        opacity: 1; transform: translateY(0); pointer-events: auto;
    }

    .bento-quick {
        grid-column: 2; grid-row: 1;
        background: var(--card); border: 1px solid var(--border-light);
        border-radius: 12px; overflow: hidden;
    }
    .bento-quick-hd {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.875rem 1.125rem; border-bottom: 1px solid var(--border-light);
        font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.06em; color: var(--gray-400);
    }
    .bento-ql {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.75rem 1.125rem; text-decoration: none; color: var(--fg);
        transition: background 0.12s; border-bottom: 1px solid var(--border-light);
    }
    .bento-ql:last-child { border-bottom: none; }
    .bento-ql:hover { background: var(--muted); }
    .bento-ql-icon {
        width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
        background: var(--primary); color: white; display: flex;
        align-items: center; justify-content: center; font-size: 0.82rem;
        transition: background 0.12s;
    }
    .bento-ql:hover .bento-ql-icon { background: var(--fg); }
    .bento-ql-name { font-size: 0.8rem; font-weight: 700; display: block; }
    .bento-ql-desc { font-size: 0.7rem; color: var(--muted-foreground); font-weight: 500; }

    /* ── EOD bento card ── */
    .bento-eod {
        grid-column: 2; grid-row: 2;
        border-radius: 12px; overflow: hidden;
    }
    .bento-eod-pending {
        background: var(--primary); padding: 1.25rem;
        display: flex; flex-direction: column; gap: 0.875rem;
    }
    .bento-eod-pending-top { display: flex; align-items: center; gap: 0.75rem; }
    .bento-eod-icon {
        width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
        background: rgba(255,255,255,0.18); display: flex;
        align-items: center; justify-content: center; color: white; font-size: 1rem;
    }
    .bento-eod-title { font-size: 0.9rem; font-weight: 800; color: white; line-height: 1.2; }
    .bento-eod-sub { font-size: 0.75rem; color: rgba(255,255,255,0.7); font-weight: 500; margin-top: 0.2rem; }
    .bento-eod-btn {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        width: 100%; height: 38px; border-radius: 8px;
        border: 1.5px solid rgba(255,255,255,0.5); background: rgba(255,255,255,0.12);
        color: white; font-family: var(--p-font-family-sans);
        font-size: 0.82rem; font-weight: 700; text-decoration: none;
        transition: background 0.15s, border-color 0.15s;
    }
    .bento-eod-btn:hover { background: rgba(255,255,255,0.22); border-color: white; color: white; }

    .bento-eod-submitted {
        background: var(--card); border: 1px solid var(--border-light);
        border-left: 4px solid #10b981; padding: 1.25rem;
        display: flex; flex-direction: column; gap: 0.75rem;
    }
    .bento-eod-submitted-top { display: flex; align-items: center; gap: 0.75rem; }
    .bento-eod-done-icon {
        width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
        background: rgba(16,185,129,0.12); display: flex;
        align-items: center; justify-content: center; color: #10b981; font-size: 1rem;
    }
    .bento-eod-done-title { font-size: 0.9rem; font-weight: 800; color: #10b981; }
    .bento-eod-done-sub { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; margin-top: 0.15rem; }
    .bento-eod-edit {
        display: flex; align-items: center; justify-content: center; gap: 0.4rem;
        width: 100%; height: 34px; border-radius: 8px;
        border: 1px solid var(--border-light); background: transparent;
        color: var(--muted-foreground); font-family: var(--p-font-family-sans);
        font-size: 0.78rem; font-weight: 700; text-decoration: none;
        transition: all 0.15s;
    }
    .bento-eod-edit:hover { border-color: var(--fg); color: var(--fg); }
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
    'analyst'    => '#ec4899',
    default      => '#5757f8',
};
$avatarSeed = ($user->gender === 'female') ? $user->username . 'Female' : $user->username;
@endphp
<x-sidebar active="dashboard" />

<div class="main-content">
    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up" id="welcomeBanner" style="--wb-color: {{ $roleColor }}; background: var(--wb-color);">
        <span class="wb-collapsed-label"><i class="fas fa-hand-wave" style="margin-right:0.4rem;"></i>Welcome back, {{ $user->first_name }}!</span>
        <button class="wb-toggle" id="wbToggle" title="Toggle banner" onclick="toggleBanner()">
            <i class="fas fa-chevron-up" id="wbToggleIcon"></i>
        </button>
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
            @elseif($user->role === 'analyst')
            <p>Brand catalog hub — manage and update product catalogs across brands.</p>
            @endif
            <div class="wb-date">{{ now()->format('l, F j') }}</div>
        </div>
        <div class="wb-avatar-zone">
            <div class="wb-fade"></div>
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ urlencode($avatarSeed) }}" class="wb-avatar" alt="{{ $user->full_name }}">
        </div>
    </div>

    @if($user->role === 'analyst')

    {{-- ── Analyst: Catalog Stats ── --}}
    <div class="anim-up d1">
        <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div class="stat-card" style="border-top: 3px solid #ec4899;">
                <div class="stat-icon" style="background: #ec4899;"><i class="fas fa-book-open"></i></div>
                <div>
                    <div class="stat-count">{{ $catalogStats['total'] }}</div>
                    <div class="stat-label">Total Catalogs</div>
                </div>
            </div>
            <div class="stat-card" style="border-top: 3px solid #10b981;">
                <div class="stat-icon" style="background: #10b981;"><i class="fas fa-circle-check"></i></div>
                <div>
                    <div class="stat-count">{{ $catalogStats['available'] }}</div>
                    <div class="stat-label">Available</div>
                </div>
            </div>
            <div class="stat-card" style="border-top: 3px solid #f59e0b;">
                <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-count">{{ $catalogStats['upcoming'] }}</div>
                    <div class="stat-label">Upcoming</div>
                </div>
            </div>
            <div class="stat-card" style="border-top: 3px solid #0ea5e9;">
                <div class="stat-icon" style="background: #0ea5e9;"><i class="fas fa-calendar-days"></i></div>
                <div>
                    <div class="stat-count">{{ $catalogStats['seasonal'] }}</div>
                    <div class="stat-label">Seasonal</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Analyst: Announcements + Quick Access bento ── --}}
    <div class="bento-grid anim-up d2">
        {{-- Announcements --}}
        <div class="bento-ann">
            <div class="bento-ann-hd">
                <h4><i class="fas fa-bullhorn" style="color:var(--primary);"></i> Announcements</h4>
                <a href="{{ route('announcements') }}">View all →</a>
            </div>
            @forelse($recentAnnouncements as $ann)
            <a href="{{ route('announcements') }}" class="bento-ann-item {{ $ann->pinned ? 'pinned' : '' }}">
                <div class="bento-ann-item-top">
                    @if($ann->pinned)
                    <span class="bento-ann-pin-badge"><i class="fas fa-thumbtack"></i> Pinned</span>
                    @endif
                    <span class="bento-ann-title">{{ $ann->title }}</span>
                </div>
                <div class="bento-ann-body">{{ $ann->body }}</div>
                <div class="bento-ann-foot">
                    <span class="bento-ann-author"><i class="fas fa-user" style="font-size:0.58rem;margin-right:2px;"></i>{{ $ann->creator->first_name }} · {{ $ann->created_at->diffForHumans() }}</span>
                    @if($ann->expires_at)
                    <span class="bento-ann-expiry"><i class="fas fa-hourglass-half"></i> Exp {{ $ann->expires_at->format('M d') }}</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="bento-ann-empty"><i class="fas fa-bullhorn"></i> No announcements yet.</div>
            @endforelse
        </div>
        {{-- Quick Access --}}
        <div class="bento-quick">
            <div class="bento-quick-hd"><i class="fas fa-bolt" style="color:var(--primary);font-size:0.65rem;"></i> Quick Access</div>
            <a href="{{ route('brand-catalogs') }}" class="bento-ql">
                <div class="bento-ql-icon" style="background:#ec4899;"><i class="fas fa-book-open"></i></div>
                <div><span class="bento-ql-name">Brand Catalogs</span><span class="bento-ql-desc">Manage product catalogs</span></div>
            </a>
            <a href="{{ route('announcements') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-bullhorn"></i></div>
                <div><span class="bento-ql-name">Announcements</span><span class="bento-ql-desc">Team updates and notices</span></div>
            </a>
            <a href="{{ route('team') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-users"></i></div>
                <div><span class="bento-ql-name">The Team</span><span class="bento-ql-desc">View your colleagues</span></div>
            </a>
        </div>
    </div>

    @else

    {{-- ── Non-analyst Bento: Announcements + Quick Access + EOD ── --}}
    <div class="bento-grid anim-up">

        {{-- Left: Announcements --}}
        <div class="bento-ann">
            <div class="bento-ann-hd">
                <h4><i class="fas fa-bullhorn" style="color:var(--primary);"></i> Announcements</h4>
                <a href="{{ route('announcements') }}">View all →</a>
            </div>
            @forelse($recentAnnouncements as $ann)
            <a href="{{ route('announcements') }}" class="bento-ann-item {{ $ann->pinned ? 'pinned' : '' }}">
                <div class="bento-ann-item-top">
                    @if($ann->pinned)
                    <span class="bento-ann-pin-badge"><i class="fas fa-thumbtack"></i> Pinned</span>
                    @endif
                    <span class="bento-ann-title">{{ $ann->title }}</span>
                </div>
                <div class="bento-ann-body">{{ $ann->body }}</div>
                <div class="bento-ann-foot">
                    <span class="bento-ann-author"><i class="fas fa-user" style="font-size:0.58rem;margin-right:2px;"></i>{{ $ann->creator->first_name }} · {{ $ann->created_at->diffForHumans() }}</span>
                    @if($ann->expires_at)
                    <span class="bento-ann-expiry"><i class="fas fa-hourglass-half"></i> Exp {{ $ann->expires_at->format('M d') }}</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="bento-ann-empty"><i class="fas fa-bullhorn"></i> No announcements yet.</div>
            @endforelse
        </div>

        {{-- Top Right: Quick Access --}}
        <div class="bento-quick">
            <div class="bento-quick-hd"><i class="fas fa-bolt" style="color:var(--primary);font-size:0.65rem;"></i> Quick Access</div>
            @if($user->role === 'content')
            <a href="{{ route('posting-procedure') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-book-open"></i></div>
                <div><span class="bento-ql-name">Posting Procedure</span><span class="bento-ql-desc">8-step product posting guide</span></div>
            </a>
            <a href="{{ route('data-gathering') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-folder-open"></i></div>
                <div><span class="bento-ql-name">Data Gathering</span><span class="bento-ql-desc">Collect product info</span></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-calculator"></i></div>
                <div><span class="bento-ql-name">Price Calculator</span><span class="bento-ql-desc">Compute SRP</span></div>
            </a>
            <a href="{{ route('important-links') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-link"></i></div>
                <div><span class="bento-ql-name">Important Links</span><span class="bento-ql-desc">Quick resources</span></div>
            </a>
            @else
            <a href="{{ route('end-of-day') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-calendar-check"></i></div>
                <div><span class="bento-ql-name">End-of-Day Report</span><span class="bento-ql-desc">Log your daily tasks</span></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-calculator"></i></div>
                <div><span class="bento-ql-name">Price Calculator</span><span class="bento-ql-desc">Compute SRP</span></div>
            </a>
            <a href="{{ route('team') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-users"></i></div>
                <div><span class="bento-ql-name">The Team</span><span class="bento-ql-desc">View your colleagues</span></div>
            </a>
            <a href="{{ route('important-links') }}" class="bento-ql">
                <div class="bento-ql-icon"><i class="fas fa-link"></i></div>
                <div><span class="bento-ql-name">Important Links</span><span class="bento-ql-desc">Quick resources</span></div>
            </a>
            @endif
        </div>

        {{-- Bottom Right: EOD Card --}}
        <div class="bento-eod">
            @if($todayLog)
            <div class="bento-eod-submitted">
                <div class="bento-eod-submitted-top">
                    <div class="bento-eod-done-icon"><i class="fas fa-circle-check"></i></div>
                    <div>
                        <div class="bento-eod-done-title">EOD Submitted</div>
                        <div class="bento-eod-done-sub">{{ now()->format('l, F j') }}</div>
                    </div>
                </div>
                <a href="{{ route('end-of-day') }}" class="bento-eod-edit"><i class="fas fa-pencil"></i> Edit Report</a>
            </div>
            @else
            <div class="bento-eod-pending">
                <div class="bento-eod-pending-top">
                    <div class="bento-eod-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <div class="bento-eod-title">No EOD yet today</div>
                        <div class="bento-eod-sub">{{ now()->format('l, F j') }}</div>
                    </div>
                </div>
                <a href="{{ route('end-of-day') }}" class="bento-eod-btn"><i class="fas fa-arrow-right"></i> Submit EOD Report</a>
            </div>
            @endif
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

    @endif

</div>
@endsection

@section('scripts')
<script>
(function() {
    var banner = document.getElementById('welcomeBanner');
    var icon   = document.getElementById('wbToggleIcon');
    if (!banner) return;
    var DUR = 380, animating = false;
    var ease = 'cubic-bezier(0.4,0,0.2,1)';

    function setIcon(c) { icon.style.transform = c ? 'rotate(180deg)' : 'rotate(0deg)'; }

    function applyInstant(collapsed) {
        banner.classList.toggle('collapsed', collapsed);
        setIcon(collapsed);
    }

    function animateTo(collapsed) {
        if (animating) return;
        animating = true;
        if (collapsed) {
            // Measure full height, then animate down to strip
            banner.style.height = banner.offsetHeight + 'px';
            banner.style.overflow = 'hidden';
            banner.classList.add('collapsed');
            void banner.offsetHeight;
            banner.style.transition = 'height ' + DUR + 'ms ' + ease;
            banner.style.height = '52px';
            setIcon(true);
        } else {
            // Start at strip height, remove collapsed, measure target, animate up
            banner.style.height = '52px';
            banner.style.overflow = 'hidden';
            banner.classList.remove('collapsed');
            void banner.offsetHeight;
            var toH = banner.scrollHeight;
            banner.style.transition = 'height ' + DUR + 'ms ' + ease;
            banner.style.height = toH + 'px';
            setIcon(false);
        }
        setTimeout(function() {
            banner.style.transition = '';
            banner.style.height = '';
            banner.style.overflow = '';
            animating = false;
        }, DUR);
        localStorage.setItem('wb_hidden', collapsed ? '1' : '0');
    }

    applyInstant(localStorage.getItem('wb_hidden') === '1');

    window.toggleBanner = function() {
        animateTo(!banner.classList.contains('collapsed'));
    };
})();
</script>
@if($user->role !== 'analyst')
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
@endif
@endsection
