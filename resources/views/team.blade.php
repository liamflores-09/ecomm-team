@extends('layouts.app')

@section('title', 'The Team — Ecomm Dept')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 00-3-3.87'/><path d='M16 3.13a4 4 0 010 7.75'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Role tabs ───────────────────────────────────────────────── */
    .tm-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .tm-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.875rem;
        border-radius: 9999px;
        border: 1px solid var(--border-light);
        background: var(--muted);
        color: var(--foreground);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .tm-tab:hover {
        border-color: var(--foreground);
    }
    .tm-tab.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    .tm-tab-count {
        font-size: 0.7rem;
        font-weight: 700;
        opacity: 0.75;
    }

    /* ── Section header ──────────────────────────────────────────── */
    .tm-hd {
        display: flex; align-items: center; gap: 0.625rem;
        margin: 2rem 0 1rem;
    }
    .tm-hd-icon {
        width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; color: white;
    }
    .tm-hd h3 { font-weight: 800; font-size: 1rem; margin: 0; }
    .tm-hd-count {
        font-size: 0.65rem; font-weight: 700; background: var(--muted);
        color: var(--gray-400); padding: 0.15rem 0.45rem; border-radius: 8px;
    }
    .tm-hd-line { flex: 1; height: 1px; background: var(--border); }

    /* ── Leader grid (managers & leads — 2-col) ──────────────────── */
    .tm-leaders {
        display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
    }

    .tm-lcard {
        background: var(--card);
        border-radius: 8px;
        border: 1px solid var(--border-light);
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .tm-lcard:hover { border-color: var(--foreground); }

    .tm-lcard-body {
        text-align: center;
        padding: 2rem 1.5rem 1.5rem;
    }

    .tm-lcard-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        border: 3px solid var(--border);
        display: block;
        margin: 0 auto 0.875rem;
        background: var(--muted);
        object-fit: cover;
    }
    .tm-lcard-name { font-weight: 800; font-size: 1.05rem; margin-bottom: 0.2rem; line-height: 1.2; }
    .tm-lcard-sub  { font-size: 0.73rem; color: var(--muted-foreground); font-weight: 500; margin-bottom: 0.5rem; }

    .tm-viber-link {
        display: inline-flex; align-items: center; gap: 0.35rem;
        font-size: 0.73rem; font-weight: 600; color: var(--gray-400);
        text-decoration: none; margin-top: 0.5rem; transition: color 0.15s;
    }
    .tm-viber-link:hover { color: var(--fg); }

    /* ── Member grid (3-col) ─────────────────────────────────────── */
    .tm-members {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.875rem;
    }

    .tm-card {
        background: var(--card);
        border-radius: 8px;
        border: 1px solid var(--border-light);
        padding: 1.5rem 1rem 1.25rem;
        text-align: center;
        transition: border-color 0.2s;
    }
    .tm-card:hover { border-color: var(--foreground); }

    .tm-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        border: 2px solid var(--border-light);
        display: block;
        margin: 0 auto 0.75rem;
        object-fit: cover;
        background: var(--muted);
        transition: border-color 0.2s;
    }
    .tm-card:hover .tm-avatar { border-color: var(--foreground); }

    .tm-name { font-weight: 800; font-size: 0.9rem; line-height: 1.25; margin-bottom: 0.35rem; }
    .tm-username { font-size: 0.7rem; color: var(--muted-foreground); font-weight: 500; margin-bottom: 0.45rem; }

    /* ── Role badges ─────────────────────────────────────────────── */
    .role-badge {
        display: inline-block; padding: 0.18rem 0.5rem; border-radius: 8px;
        font-size: 0.59rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .role-badge.head       { background: #7c3aed; color: #fff; }
    .role-badge.manager    { background: #1e293b; color: #fff; }
    .role-badge.lead       { background: #6366f1; color: #fff; }
    .role-badge.content    { background: #0ea5e9; color: #fff; }
    .role-badge.graphics   { background: #f59e0b; color: #fff; }
    .role-badge.backend    { background: #f43f5e; color: #fff; }
    .role-badge.analyst    { background: #ec4899; color: #fff; }
    .role-badge.researcher { background: #10b981; color: #fff; }

    /* ── Empty state ─────────────────────────────────────────────── */
    .tm-empty {
        text-align: center; padding: 2.5rem; background: var(--card);
        border-radius: 8px; border: 1px dashed var(--border);
        color: var(--gray-400); font-size: 0.85rem; font-weight: 500;
    }
    .tm-empty i { font-size: 1.75rem; display: block; margin-bottom: 0.625rem; opacity: 0.35; }

    /* ── Responsive ──────────────────────────────────────────────── */
    @media (max-width: 960px) {
        .tm-leaders { grid-template-columns: 1fr; }
        .tm-members { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 540px) {
        .tm-members { grid-template-columns: 1fr; }
    }

    /* ── Flip card ───────────────────────────────────────────────── */
    .flip-card { cursor: pointer; perspective: 1000px; }
    .flip-card-inner {
        display: grid; grid-template-columns: 1fr;
        transition: transform 0.5s cubic-bezier(0.4,0,0.2,1);
        transform-style: preserve-3d;
    }
    .flip-card-front, .flip-card-back {
        grid-area: 1 / 1;
        backface-visibility: hidden; -webkit-backface-visibility: hidden;
        border-radius: 8px; overflow: hidden;
    }
    .flip-card-back { transform: rotateY(180deg); }
    .flip-card.flipped .flip-card-inner { transform: rotateY(180deg); }

    /* ── ID card back ────────────────────────────────────────────── */
    .tm-idback {
        background: var(--card); border: 1px solid var(--border-light);
        display: flex; flex-direction: column; align-items: center;
        text-align: center;
    }
    .tm-id-hole {
        width: 12px; height: 12px; border-radius: 50%;
        background: var(--background); border: 1.5px solid rgba(0,0,0,0.12);
        margin: 7px auto 0; flex-shrink: 0;
    }
    .tm-id-header {
        width: 100%; padding: 8px 12px 10px; margin-top: 4px;
        position: relative; display: flex; align-items: center; gap: 8px; flex-shrink: 0;
    }
    .tm-id-dots {
        position: absolute; inset: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px);
        background-size: 12px 12px;
    }
    .tm-id-logo-circle {
        width: 28px; height: 28px; border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; position: relative; z-index: 1;
        color: white; font-size: 0.75rem;
    }
    .tm-id-header-text { position: relative; z-index: 1; text-align: left; }
    .tm-id-company-name {
        font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em;
        text-transform: uppercase; color: rgba(255,255,255,0.95); line-height: 1.2;
    }
    .tm-id-company-sub {
        font-size: 0.55rem; color: rgba(255,255,255,0.65);
        font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .tm-id-photo-ring {
        width: 72px; height: 72px; border-radius: 50%;
        background: white; padding: 3px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        margin: 10px auto 8px; flex-shrink: 0;
    }
    .flip-leader .tm-id-photo-ring { width: 80px; height: 80px; }
    .tm-id-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }
    .tm-id-fullname { font-weight: 800; font-size: 0.9rem; line-height: 1.2; padding: 0 12px; margin-bottom: 3px; }
    .tm-id-designation { font-size: 0.65rem; color: var(--muted-foreground); font-weight: 500; margin-bottom: 6px; }
    .tm-id-divider { width: 75%; height: 1px; background: var(--border); margin: 6px auto; flex-shrink: 0; }
    .tm-id-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 2px 14px; width: 100%; font-size: 0.67rem;
    }
    .tm-id-lbl {
        color: var(--muted-foreground); font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.04em; font-size: 0.6rem;
    }
    .tm-id-val { font-weight: 600; color: var(--foreground); }
    .tm-id-viber-link { color: var(--foreground); text-decoration: none; font-weight: 600; transition: opacity 0.15s; }
    .tm-id-viber-link:hover { opacity: 0.7; }
    .tm-id-footer {
        margin-top: auto; width: 100%;
        border-top: 1px solid var(--border-light);
        padding: 7px 12px 9px;
        display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    }
    .tm-id-barcode { width: 70px; height: 18px; opacity: 0.45; fill: var(--foreground); }
    .tm-id-idnum { font-size: 0.58rem; font-weight: 700; color: var(--muted-foreground); font-family: monospace; letter-spacing: 0.04em; }
    .idc-head      .tm-id-header { background: #7c3aed; }
    .idc-manager   .tm-id-header { background: #1e293b; }
    .idc-lead      .tm-id-header { background: #6366f1; }
    .idc-analyst   .tm-id-header { background: #ec4899; }
    .idc-researcher .tm-id-header { background: #10b981; }
    .idc-content   .tm-id-header { background: #0ea5e9; }
    .idc-graphics  .tm-id-header { background: #f59e0b; }
    .idc-backend   .tm-id-header { background: #f43f5e; }
    .tm-flip-hint {
        font-size: 0.62rem; color: var(--gray-400); opacity: 0.7;
        margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.3rem;
    }
    .tm-flip-hint i { font-size: 0.6rem; }
</style>
@endsection

@section('content')
<x-sidebar :isAdmin="Auth::user()->isAdmin()" active="team" />

<div class="main-content">

    @php
        $avatarSeed = fn($u) => ($u->gender === 'female') ? $u->username . 'Female' : $u->username;
        $idNum  = fn($u) => 'ECD-' . str_pad(abs(crc32($u->username)) % 9999 + 1, 4, '0', STR_PAD_LEFT);
        $barcode = '<svg class="tm-id-barcode" viewBox="0 0 80 18" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="0" width="2" height="18"/><rect x="4" y="0" width="1" height="18"/><rect x="7" y="0" width="3" height="18"/><rect x="12" y="0" width="1" height="18"/><rect x="15" y="0" width="2" height="18"/><rect x="19" y="0" width="3" height="18"/><rect x="24" y="0" width="1" height="18"/><rect x="27" y="0" width="2" height="18"/><rect x="31" y="0" width="1" height="18"/><rect x="34" y="0" width="3" height="18"/><rect x="39" y="0" width="2" height="18"/><rect x="43" y="0" width="1" height="18"/><rect x="46" y="0" width="3" height="18"/><rect x="51" y="0" width="1" height="18"/><rect x="54" y="0" width="2" height="18"/><rect x="58" y="0" width="1" height="18"/><rect x="61" y="0" width="3" height="18"/><rect x="66" y="0" width="2" height="18"/><rect x="69" y="0" width="1" height="18"/><rect x="72" y="0" width="2" height="18"/><rect x="76" y="0" width="1" height="18"/><rect x="79" y="0" width="1" height="18"/></svg>';
        $total      = $heads->count() + $managers->count() + $leads->count() + $analysts->count()
                    + $researchers->count() + $content->count() + $graphics->count() + $backend->count();
        $viber = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" style="flex-shrink:0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 13.6c-.24.68-1.18 1.26-1.92 1.44-.52.12-1.2.18-3.48-.74-2.92-1.18-4.8-4.08-4.94-4.28-.14-.2-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.26-.28.56-.36.76-.36h.54c.18 0 .42-.06.66.52.24.58.82 2 .88 2.16.06.16.1.34.02.54-.08.2-.12.32-.24.48-.12.16-.24.36-.34.48-.12.14-.24.28-.1.54.14.26.62 1.02 1.34 1.64.92.8 1.68 1.04 1.94 1.16.26.12.42.1.56-.06.14-.16.6-.7.76-.94.16-.24.32-.2.54-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.08.06.48-.18 1.16z" fill="currentColor"/></svg>';
    @endphp

    <div class="top-bar anim-up" style="margin-bottom: 1.25rem;">
        <div>
            <h2>The <span class="highlight">Team</span></h2>
            <p>Meet the people behind Ecomm Dept</p>
        </div>
    </div>

    {{-- Role tabs --}}
    <div class="tm-tabs anim-up d1">
        <button class="tm-tab active" data-filter="all">All <span class="tm-tab-count">{{ $total }}</span></button>
        @if($heads->count())
        <button class="tm-tab" data-filter="head">Ecomm Head <span class="tm-tab-count">{{ $heads->count() }}</span></button>
        @endif
        @if($managers->count())
        <button class="tm-tab" data-filter="manager">Manager <span class="tm-tab-count">{{ $managers->count() }}</span></button>
        @endif
        @if($leads->count())
        <button class="tm-tab" data-filter="lead">Lead <span class="tm-tab-count">{{ $leads->count() }}</span></button>
        @endif
        @if($analysts->count())
        <button class="tm-tab" data-filter="analyst">Analyst <span class="tm-tab-count">{{ $analysts->count() }}</span></button>
        @endif
        @if($researchers->count())
        <button class="tm-tab" data-filter="researcher">Researcher <span class="tm-tab-count">{{ $researchers->count() }}</span></button>
        @endif
        @if($content->count())
        <button class="tm-tab" data-filter="content">Content <span class="tm-tab-count">{{ $content->count() }}</span></button>
        @endif
        @if($graphics->count())
        <button class="tm-tab" data-filter="graphics">Graphics <span class="tm-tab-count">{{ $graphics->count() }}</span></button>
        @endif
        @if($backend->count())
        <button class="tm-tab" data-filter="backend">Backend <span class="tm-tab-count">{{ $backend->count() }}</span></button>
        @endif
    </div>

    {{-- ════ HEAD ════ --}}
    @if($heads->count())
    <div class="tm-section" data-role="head">
    <div class="tm-hd anim-up d2">
        <div class="tm-hd-icon" style="background:#7c3aed;"><i class="fas fa-crown"></i></div>
        <h3>Ecomm Head</h3>
        <span class="tm-hd-count">{{ $heads->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    <div class="tm-leaders anim-up d2">
        @foreach($heads as $u)
        <div class="flip-card flip-leader" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-lcard">
                    <div class="tm-lcard-body">
                        <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-lcard-avatar" alt="{{ $u->full_name }}">
                        <div class="tm-lcard-name">{{ $u->full_name }}</div>
                        <div class="tm-lcard-sub">Ecomm Department Head</div>
                        <span class="role-badge head">Ecomm Head</span>
                        <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                    </div>
                </div>
                <div class="flip-card-back tm-idback idc-head">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-crown"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Ecomm Department Head</div>
                    <span class="role-badge head">Ecomm Head</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>
    @endif

    {{-- ════ MANAGER ════ --}}
    @if($managers->count())
    <div class="tm-section" data-role="manager">
    <div class="tm-hd anim-up d2">
        <div class="tm-hd-icon" style="background:#1e293b;"><i class="fas fa-crown"></i></div>
        <h3>Manager</h3>
        <span class="tm-hd-count">{{ $managers->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    <div class="tm-leaders anim-up d2">
        @foreach($managers as $u)
        <div class="flip-card flip-leader" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-lcard">
                    <div class="tm-lcard-body">
                        <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-lcard-avatar" alt="{{ $u->full_name }}">
                        <div class="tm-lcard-name">{{ $u->full_name }}</div>
                        <div class="tm-lcard-sub">Ecomm Department</div>
                        <span class="role-badge manager">Manager</span>
                        <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                    </div>
                </div>
                <div class="flip-card-back tm-idback idc-manager">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-crown"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Department Manager</div>
                    <span class="role-badge manager">Manager</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>
    @endif

    {{-- ════ LEAD ════ --}}
    @if($leads->count())
    <div class="tm-section" data-role="lead">
    <div class="tm-hd anim-up d3">
        <div class="tm-hd-icon" style="background:#6366f1;"><i class="fas fa-star"></i></div>
        <h3>Lead</h3>
        <span class="tm-hd-count">{{ $leads->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    <div class="tm-leaders anim-up d3">
        @foreach($leads as $u)
        <div class="flip-card flip-leader" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-lcard">
                    <div class="tm-lcard-body">
                        <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-lcard-avatar" alt="{{ $u->full_name }}">
                        <div class="tm-lcard-name">{{ $u->full_name }}</div>
                        <div class="tm-lcard-sub">Content &amp; PR Lead</div>
                        <span class="role-badge lead">Lead</span>
                        <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                    </div>
                </div>
                <div class="flip-card-back tm-idback idc-lead">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-star"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Team Lead</div>
                    <span class="role-badge lead">Lead</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>
    @endif

    {{-- ════ ANALYST ════ --}}
    @if($analysts->count())
    <div class="tm-section" data-role="analyst">
    <div class="tm-hd anim-up d4">
        <div class="tm-hd-icon" style="background:#ec4899;"><i class="fas fa-chart-bar"></i></div>
        <h3>Analyst</h3>
        <span class="tm-hd-count">{{ $analysts->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    <div class="tm-members anim-up d4">
        @foreach($analysts as $u)
        <div class="flip-card flip-member" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-card">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
                    <div class="tm-name">{{ $u->full_name }}</div>
                    <span class="role-badge analyst">Analyst</span>
                    <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                </div>
                <div class="flip-card-back tm-idback idc-analyst">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-chart-bar"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Data Analyst</div>
                    <span class="role-badge analyst">Analyst</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>
    @endif

    {{-- ════ RESEARCHER ════ --}}
    @if($researchers->count())
    <div class="tm-section" data-role="researcher">
    <div class="tm-hd anim-up d4">
        <div class="tm-hd-icon" style="background:#10b981;"><i class="fas fa-magnifying-glass"></i></div>
        <h3>Product Researcher</h3>
        <span class="tm-hd-count">{{ $researchers->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    <div class="tm-members anim-up d4">
        @foreach($researchers as $u)
        <div class="flip-card flip-member" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-card">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
                    <div class="tm-name">{{ $u->full_name }}</div>
                    <span class="role-badge researcher">Researcher</span>
                    <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                </div>
                <div class="flip-card-back tm-idback idc-researcher">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-magnifying-glass"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Product Researcher</div>
                    <span class="role-badge researcher">Researcher</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>
    @endif

    {{-- ════ CONTENT ════ --}}
    <div class="tm-section" data-role="content">
    <div class="tm-hd anim-up d4">
        <div class="tm-hd-icon" style="background:#0ea5e9;"><i class="fas fa-pen-nib"></i></div>
        <h3>Content Team</h3>
        <span class="tm-hd-count">{{ $content->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    @if($content->count())
    <div class="tm-members anim-up d4">
        @foreach($content as $u)
        <div class="flip-card flip-member" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-card">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
                    <div class="tm-name">{{ $u->full_name }}</div>
                    <span class="role-badge content">Content</span>
                    <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                </div>
                <div class="flip-card-back tm-idback idc-content">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-pen-nib"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Content Creator</div>
                    <span class="role-badge content">Content</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="tm-empty anim-up d4"><i class="fas fa-users"></i> No content members yet.</div>
    @endif
    </div>

    {{-- ════ GRAPHICS ════ --}}
    <div class="tm-section" data-role="graphics">
    <div class="tm-hd anim-up d5">
        <div class="tm-hd-icon" style="background:#f59e0b;"><i class="fas fa-palette"></i></div>
        <h3>Design Team</h3>
        <span class="tm-hd-count">{{ $graphics->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    @if($graphics->count())
    <div class="tm-members anim-up d5">
        @foreach($graphics as $u)
        <div class="flip-card flip-member" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-card">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
                    <div class="tm-name">{{ $u->full_name }}</div>
                    <span class="role-badge graphics">Graphics</span>
                    <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                </div>
                <div class="flip-card-back tm-idback idc-graphics">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-palette"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Graphic Designer</div>
                    <span class="role-badge graphics">Graphics</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-palette"></i> No graphics members yet.</div>
    @endif
    </div>

    {{-- ════ BACKEND ════ --}}
    <div class="tm-section" data-role="backend">
    <div class="tm-hd anim-up d5">
        <div class="tm-hd-icon" style="background:#f43f5e;"><i class="fas fa-server"></i></div>
        <h3>Backend Team</h3>
        <span class="tm-hd-count">{{ $backend->count() }}</span>
        <div class="tm-hd-line"></div>
    </div>
    @if($backend->count())
    <div class="tm-members anim-up d5">
        @foreach($backend as $u)
        <div class="flip-card flip-member" onclick="this.classList.toggle('flipped')">
            <div class="flip-card-inner">
                <div class="flip-card-front tm-card">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
                    <div class="tm-name">{{ $u->full_name }}</div>
                    <span class="role-badge backend">Backend</span>
                    <div class="tm-flip-hint"><i class="fas fa-id-card"></i> Tap for ID card</div>
                </div>
                <div class="flip-card-back tm-idback idc-backend">
                    <div class="tm-id-hole"></div>
                    <div class="tm-id-header">
                        <div class="tm-id-dots"></div>
                        <div class="tm-id-logo-circle"><i class="fas fa-server"></i></div>
                        <div class="tm-id-header-text"><div class="tm-id-company-name">Ecomm Dept</div><div class="tm-id-company-sub">ID Card</div></div>
                    </div>
                    <div class="tm-id-photo-ring"><img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-id-photo" alt="{{ $u->full_name }}"></div>
                    <div class="tm-id-fullname">{{ $u->full_name }}</div>
                    <div class="tm-id-designation">Backend Developer</div>
                    <span class="role-badge backend">Backend</span>
                    <div class="tm-id-divider"></div>
                    <div class="tm-id-row"><span class="tm-id-lbl">Username</span><span class="tm-id-val">{{ '@'.$u->username }}</span></div>
                    @if($u->mobile_number)
                    <div class="tm-id-row"><span class="tm-id-lbl">Viber</span><a href="viber://chat?number={{ $u->mobile_number }}" class="tm-id-val tm-id-viber-link" onclick="event.stopPropagation()">{{ $u->mobile_number }}</a></div>
                    @endif
                    <div class="tm-id-footer">{!! $barcode !!}<div class="tm-id-idnum">{{ $idNum($u) }}</div></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-server"></i> No backend members yet.</div>
    @endif
    </div>

</div>
<script>
(function () {
    var tabs = document.querySelectorAll('.tm-tab');
    var sections = document.querySelectorAll('.tm-section');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            var filter = tab.dataset.filter;
            sections.forEach(function (s) {
                s.style.display = (filter === 'all' || s.dataset.role === filter) ? '' : 'none';
            });
        });
    });
}());
</script>
@endsection
