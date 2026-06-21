@extends('layouts.app')

@section('title', 'The Team — Ecomm Dept')
@section('has-sidebar', true)

@section('styles')
<style>
    .team-hero {
        background: var(--white);
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .team-hero .th-icon {
        width: 64px;
        height: 64px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .team-hero h3 { font-weight: 800; font-size: 1.25rem; margin-bottom: 0.25rem; }
    .team-hero p { color: var(--gray-500); font-weight: 500; font-size: 0.9rem; margin: 0; }

    .team-hero .th-stats {
        display: flex;
        gap: 1rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }

    .th-stat {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .th-stat .dot { width: 8px; height: 8px; border-radius: 50%; }

    /* Section divider */
    .team-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 2rem 0 1.25rem;
    }

    .team-divider .td-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .team-divider h3 { font-weight: 800; font-size: 1.1rem; margin: 0; }

    .team-divider .td-count {
        background: var(--muted);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--gray-400);
    }

    .team-divider .td-line { flex: 1; height: 2px; background: var(--muted); }

    /* Leadership cards */
    .leader-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .leader-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.2s;
    }

    .leader-card:hover { transform: scale(1.01); }

    .leader-card.manager { background: var(--primary); }
    .leader-card.manager * { color: white !important; }
    .leader-card.manager .lc-role { color: rgba(255,255,255,0.75) !important; }
    .leader-card.manager .lc-badge { background: rgba(255,255,255,0.2); color: white !important; }

    .leader-card.lead { border: 2px solid #6366f1; }
    .leader-card.lead .lc-badge { background: #6366f1; color: #ffffff; }

    .lc-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 3px solid var(--muted);
        object-fit: cover;
    }

    .leader-card.manager .lc-avatar { border-color: rgba(255,255,255,0.3); }

    .lc-info { flex: 1; }

    .lc-name { font-weight: 800; font-size: 1.1rem; margin-bottom: 0.125rem; }
    .lc-role { font-size: 0.8rem; font-weight: 500; color: var(--gray-400); margin-bottom: 0.5rem; }

    .lc-mobile {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .lc-mobile a {
        color: inherit;
        text-decoration: none;
        transition: opacity 0.15s;
    }

    .lc-mobile a:hover { opacity: 0.7; }

    .viber-icon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* Role Badge — Cleopatra monochrome gradient */
    .role-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .role-badge.manager { background: #171717; color: #ffffff; }
    .role-badge.lead { background: #6366f1; color: #ffffff; }
    .role-badge.content { background: #0ea5e9; color: #ffffff; }
    .role-badge.graphics { background: #f59e0b; color: #ffffff; }
    .role-badge.backend { background: #f43f5e; color: #ffffff; }
    .role-badge.researcher { background: #10b981; color: #ffffff; }

    /* Member grid */
    .member-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .member-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s;
    }

    .member-card:hover { transform: scale(1.02); }

    .member-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        object-fit: cover;
    }

    .member-name { font-weight: 700; font-size: 0.85rem; }
    .member-role { font-size: 0.7rem; font-weight: 500; color: var(--gray-400); }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--gray-400);
        font-weight: 500;
        font-size: 0.85rem;
    }

    .empty-state i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-300); }

    /* Design team */
    .design-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .design-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
        transition: all 0.2s;
    }

    .design-card:hover { transform: scale(1.01); }

    .design-card-header {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }

    .dc-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        flex-shrink: 0;
        object-fit: cover;
    }

    .dc-name { font-weight: 800; font-size: 1rem; }
    .dc-role { font-size: 0.75rem; font-weight: 500; color: var(--gray-400); }

    @media (max-width: 768px) {
        .leader-row { grid-template-columns: 1fr; }
        .member-grid { grid-template-columns: 1fr 1fr; }
        .design-grid { grid-template-columns: 1fr; }
        .team-hero { flex-direction: column; text-align: center; }
        .team-hero .th-stats { justify-content: center; }
    }

    @media (max-width: 480px) {
        .member-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="team" />

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>The <span class="highlight">Team</span></h2>
            <p>Meet the people behind Ecomm Dept</p>
        </div>
    </div>

    @php
        $total = $managers->count() + $leads->count() + $researchers->count() + $content->count() + $graphics->count() + $backend->count();
    @endphp

    <!-- Hero Card -->
    <div class="team-hero anim-up d1">
        <div class="th-icon"><i class="fas fa-users"></i></div>
        <div>
            <h3>Ecomm Department</h3>
            <p>Content, PR, Design, and Backend teams working together across e-commerce platforms</p>
            <div class="th-stats">
                <div class="th-stat"><div class="dot" style="background: #0ea5e9;"></div> Content — {{ $content->count() }}</div>
                <div class="th-stat"><div class="dot" style="background: #10b981;"></div> Research — {{ $researchers->count() }}</div>
                <div class="th-stat"><div class="dot" style="background: #f59e0b;"></div> Graphics — {{ $graphics->count() }}</div>
                <div class="th-stat"><div class="dot" style="background: #f43f5e;"></div> Backend — {{ $backend->count() }}</div>
                <div class="th-stat"><div class="dot" style="background: #6366f1;"></div> Lead — {{ $leads->count() }}</div>
                <div class="th-stat"><div class="dot" style="background: #171717;"></div> Manager — {{ $managers->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Manager -->
    @if($managers->count())
    <div class="team-divider anim-up d2">
        <div class="td-icon" style="background: var(--primary);"><i class="fas fa-crown"></i></div>
        <h3>Manager</h3>
        <span class="td-count">{{ $managers->count() }}</span>
        <div class="td-line"></div>
    </div>
    <div class="leader-row anim-up d2">
        @foreach($managers as $m)
        <div class="leader-card manager">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($m->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $m->username . 'Female' : $m->username }}" class="lc-avatar" alt="{{ $m->username }}">
            <div class="lc-info">
                <div class="lc-name">{{ $m->first_name }} {{ $m->last_name }}</div>
                @if($m->mobile_number)
                <div class="lc-mobile">
                    <svg class="viber-icon" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="white"/></svg>
                    <a href="viber://chat?number={{ $m->mobile_number }}">{{ $m->mobile_number }}</a>
                </div>
                @endif
                <span class="role-badge manager">Manager</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Lead -->
    @if($leads->count())
    <div class="team-divider anim-up d3">
        <div class="td-icon" style="background: #6366f1;"><i class="fas fa-star"></i></div>
        <h3>Lead</h3>
        <span class="td-count">{{ $leads->count() }}</span>
        <div class="td-line"></div>
    </div>
    <div class="leader-row anim-up d3">
        @foreach($leads as $l)
        <div class="leader-card lead">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($l->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $l->username . 'Female' : $l->username }}" class="lc-avatar" alt="{{ $l->username }}">
            <div class="lc-info">
                <div class="lc-name">{{ $l->first_name }} {{ $l->last_name }}</div>
                @if($l->mobile_number)
                <div class="lc-mobile">
                    <svg class="viber-icon" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="currentColor"/></svg>
                    <a href="viber://chat?number={{ $l->mobile_number }}">{{ $l->mobile_number }}</a>
                </div>
                @endif
                <span class="role-badge lead">Content / PR Lead</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Product Researcher -->
    @if($researchers->count())
    <div class="team-divider anim-up d3b">
        <div class="td-icon" style="background: #10b981;"><i class="fas fa-magnifying-glass"></i></div>
        <h3>Product Researcher</h3>
        <span class="td-count">{{ $researchers->count() }}</span>
        <div class="td-line"></div>
    </div>
    <div class="member-grid anim-up d3b">
        @foreach($researchers as $r)
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($r->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $r->username . 'Female' : $r->username }}" class="member-avatar" alt="{{ $r->username }}">
            <div style="flex: 1; min-width: 0;">
                <div class="member-name">{{ $r->first_name }} {{ $r->last_name }}</div>
                <span class="role-badge researcher">Researcher</span>
                @if($r->mobile_number)
                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.375rem; font-size: 0.7rem; color: var(--gray-400);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="#92400E"/></svg>
                    <a href="viber://chat?number={{ $r->mobile_number }}" style="color: var(--gray-400); text-decoration: none;">{{ $r->mobile_number }}</a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Content Team -->
    <div class="team-divider anim-up d4">
        <div class="td-icon" style="background: var(--secondary);"><i class="fas fa-pen-nib"></i></div>
        <h3>Content Team</h3>
        <span class="td-count">{{ $content->count() }}</span>
        <div class="td-line"></div>
    </div>

    @if($content->count())
    <div class="member-grid anim-up d4">
        @foreach($content as $c)
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($c->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $c->username . 'Female' : $c->username }}" class="member-avatar" alt="{{ $c->username }}">
            <div style="flex: 1; min-width: 0;">
                <div class="member-name">{{ $c->first_name }} {{ $c->last_name }}</div>
                <span class="role-badge content">Content</span>
                @if($c->mobile_number)
                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.375rem; font-size: 0.7rem; color: var(--gray-400);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="#7360F2"/></svg>
                    <a href="viber://chat?number={{ $c->mobile_number }}" style="color: var(--gray-400); text-decoration: none;">{{ $c->mobile_number }}</a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state anim-up d4">
        <i class="fas fa-users"></i>
        No content team members yet.
    </div>
    @endif

    <!-- Design Team -->
    <div class="team-divider anim-up d5">
        <div class="td-icon" style="background: var(--accent);"><i class="fas fa-palette"></i></div>
        <h3>Design Team</h3>
        <span class="td-count">{{ $graphics->count() }}</span>
        <div class="td-line"></div>
    </div>

    @if($graphics->count())
    <div class="design-grid anim-up d5">
        @foreach($graphics as $g)
        <div class="design-card">
            <div class="design-card-header">
                <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($g->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $g->username . 'Female' : $g->username }}" class="dc-avatar" alt="{{ $g->username }}">
                <div>
                    <div class="dc-name">{{ $g->first_name }} {{ $g->last_name }}</div>
                    <span class="role-badge graphics">Graphics</span>
                    @if($g->mobile_number)
                    <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.375rem; font-size: 0.7rem; color: var(--gray-400);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="#7360F2"/></svg>
                        <a href="viber://chat?number={{ $g->mobile_number }}" style="color: var(--gray-400); text-decoration: none;">{{ $g->mobile_number }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state anim-up d5">
        <i class="fas fa-palette"></i>
        No graphics team members yet.
    </div>
    @endif

    <!-- Backend Team -->
    <div class="team-divider anim-up d6">
        <div class="td-icon" style="background: #f43f5e;"><i class="fas fa-server"></i></div>
        <h3>Backend Team</h3>
        <span class="td-count">{{ $backend->count() }}</span>
        <div class="td-line"></div>
    </div>

    @if($backend->count())
    <div class="member-grid anim-up d6">
        @foreach($backend as $b)
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($b->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $b->username . 'Female' : $b->username }}" class="member-avatar" alt="{{ $b->username }}">
            <div style="flex: 1; min-width: 0;">
                <div class="member-name">{{ $b->first_name }} {{ $b->last_name }}</div>
                <span class="role-badge backend">Backend</span>
                @if($b->mobile_number)
                <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.375rem; font-size: 0.7rem; color: var(--gray-400);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="#7360F2"/></svg>
                    <a href="viber://chat?number={{ $b->mobile_number }}" style="color: var(--gray-400); text-decoration: none;">{{ $b->mobile_number }}</a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state anim-up d6">
        <i class="fas fa-server"></i>
        No backend team members yet.
    </div>
    @endif
</div>
@endsection
