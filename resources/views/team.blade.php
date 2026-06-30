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

    /* ── Clickable card hint ─────────────────────────────────────── */
    .tm-card, .tm-lcard { cursor: pointer; }
    .tm-id-hint {
        font-size: 0.62rem; color: var(--gray-400); opacity: 0.65;
        margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.3rem;
    }
    .tm-id-hint i { font-size: 0.6rem; }

    /* ── ID card modal ───────────────────────────────────────────── */
    .idm-overlay {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.65); backdrop-filter: blur(6px);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity 0.2s;
    }
    .idm-overlay.open { opacity: 1; pointer-events: all; }
    .idm-close {
        position: absolute; top: 18px; right: 18px;
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba(255,255,255,0.15); border: none; color: white;
        font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background 0.15s;
    }
    .idm-close:hover { background: rgba(255,255,255,0.25); }
    .idm-lanyard {
        width: 3px; height: 56px;
        background: linear-gradient(to bottom, #888, #555);
        border-radius: 2px; margin-bottom: -2px;
    }
    .idm-flip-card { width: 300px; cursor: pointer; perspective: 1200px; --idm-color: #7c3aed; }
    .idm-flip-inner {
        display: grid; grid-template-columns: 1fr;
        transition: transform 0.6s cubic-bezier(0.4,0,0.2,1);
        transform-style: preserve-3d;
    }
    .idm-face {
        grid-area: 1/1; border-radius: 20px; overflow: hidden;
        backface-visibility: hidden; -webkit-backface-visibility: hidden;
        box-shadow: 0 28px 72px rgba(0,0,0,0.5), 0 6px 20px rgba(0,0,0,0.25);
        position: relative;
    }
    .idm-back-face { transform: rotateY(180deg); }
    .idm-flip-card.flipped .idm-flip-inner { transform: rotateY(180deg); }

    /* ── FRONT ── */
    .idm-front { background: #fff; display: flex; flex-direction: column; }

    /* Colored top section */
    .idm-top {
        height: 155px; position: relative; flex-shrink: 0;
        display: flex; flex-direction: column; padding: 14px 18px 0; overflow: visible;
    }
    .idm-top-dots {
        position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(circle, rgba(255,255,255,0.18) 1px, transparent 1px);
        background-size: 14px 14px;
    }
    /* Large background icon */
    .idm-top-icon-bg {
        position: absolute; bottom: -14px; right: -8px;
        font-size: 110px; color: rgba(255,255,255,0.1); pointer-events: none; line-height: 1;
    }
    /* Lanyard hole inside the top */
    .idm-hole {
        position: absolute; top: 10px; left: 50%; transform: translateX(-50%);
        width: 14px; height: 14px; border-radius: 50%;
        background: rgba(255,255,255,0.25); border: 1.5px solid rgba(255,255,255,0.5);
        z-index: 2;
    }
    /* Company row */
    .idm-co-row { display: flex; align-items: center; gap: 9px; position: relative; z-index: 1; margin-top: 18px; }
    .idm-co-icon {
        width: 32px; height: 32px; border-radius: 9px;
        background: rgba(255,255,255,0.18); backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 0.85rem; flex-shrink: 0;
    }
    .idm-co-name { font-size: 0.8rem; font-weight: 800; letter-spacing: 0.08em; color: white; text-transform: uppercase; line-height: 1.15; }
    .idm-co-sub { font-size: 0.58rem; color: rgba(255,255,255,0.55); font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

    /* Avatar — overlaps split */
    .idm-avatar-wrap {
        position: absolute; bottom: -52px; left: 50%; transform: translateX(-50%);
        z-index: 4; width: 104px; height: 104px; border-radius: 50%;
        background: white; padding: 4px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.22);
    }
    .idm-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }

    /* White info section */
    .idm-info {
        padding: 62px 20px 0; /* 62px to clear avatar */
        display: flex; flex-direction: column; align-items: center; flex: 1;
    }
    .idm-fullname { font-weight: 800; font-size: 1.08rem; color: #0f172a; text-align: center; line-height: 1.25; margin-bottom: 3px; }
    .idm-desg { font-size: 0.7rem; color: #64748b; font-weight: 500; margin-bottom: 10px; letter-spacing: 0.01em; }

    /* Divider line */
    .idm-rule { width: 40px; height: 3px; border-radius: 99px; background: var(--idm-color); margin: 10px auto 14px; opacity: 0.6; }

    /* Front footer */
    .idm-front-foot {
        margin-top: auto; width: 100%; padding: 12px 18px 16px;
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid #f1f5f9;
    }
    .idm-front-org { font-size: 0.58rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #cbd5e1; }
    .idm-front-hint { font-size: 0.6rem; color: #94a3b8; display: flex; align-items: center; gap: 4px; }

    /* ── BACK ── */
    .idm-back-face { background: #f8fafc; display: flex; flex-direction: column; position: relative; overflow: hidden; }
    .idr-head      .idm-back-face { background: linear-gradient(160deg, #faf5ff 0%, #f3e8ff 100%); }
    .idr-manager   .idm-back-face { background: linear-gradient(160deg, #f1f5f9 0%, #e2e8f0 100%); }
    .idr-lead      .idm-back-face { background: linear-gradient(160deg, #f5f3ff 0%, #ede9fe 100%); }
    .idr-analyst   .idm-back-face { background: linear-gradient(160deg, #fdf2f8 0%, #fce7f3 100%); }
    .idr-researcher .idm-back-face { background: linear-gradient(160deg, #f0fdf4 0%, #dcfce7 100%); }
    .idr-content   .idm-back-face { background: linear-gradient(160deg, #f0f9ff 0%, #e0f2fe 100%); }
    .idr-graphics  .idm-back-face { background: linear-gradient(160deg, #fffbeb 0%, #fef3c7 100%); }
    .idr-backend   .idm-back-face { background: linear-gradient(160deg, #fff1f2 0%, #ffe4e6 100%); }

    /* Big faded watermark in back body */
    .idm-back-wm {
        position: absolute; bottom: 30px; right: -20px;
        font-size: 130px; color: var(--idm-color); opacity: 0.08;
        pointer-events: none; line-height: 1; z-index: 0;
    }

    /* Colored header strip */
    .idm-back-head {
        padding: 14px 18px; position: relative; flex-shrink: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .idm-back-co { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.08em; color: rgba(255,255,255,0.92); text-transform: uppercase; position: relative; z-index: 1; }

    /* Left accent bar on body */
    .idm-back-body {
        padding: 6px 18px 6px 22px; flex: 1; position: relative; z-index: 1;
        border-left: 3px solid var(--idm-color);
        margin: 12px 18px; border-radius: 0 0 0 4px;
    }
    .idm-back-row {
        display: flex; align-items: flex-start; justify-content: space-between;
        padding: 9px 0; border-bottom: 1px solid #cbd5e1;
    }
    .idm-back-row:last-child { border-bottom: none; }
    .idm-lbl { color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.58rem; letter-spacing: 0.06em; padding-top: 1px; }
    .idm-val { font-weight: 700; color: #0f172a; font-size: 0.78rem; text-align: right; max-width: 58%; word-break: break-all; }
    .idm-viber-val { text-decoration: none; color: #0f172a; font-weight: 700; transition: opacity 0.15s; }
    .idm-viber-val:hover { opacity: 0.6; }

    /* Back footer */
    .idm-back-foot {
        padding: 10px 18px 15px; border-top: 1px solid #cbd5e1;
        display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; z-index: 1; position: relative;
    }
    .idm-barcode { width: 82px; height: 22px; fill: #1e293b; opacity: 0.35; }
    .idm-idnum { font-family: monospace; font-size: 0.65rem; font-weight: 700; color: #64748b; letter-spacing: 0.04em; }

    .idm-tap-hint { color: rgba(255,255,255,0.4); font-size: 0.68rem; margin-top: 14px; letter-spacing: 0.02em; }

    /* ── Dark mode ── */
    [data-theme="dark"] .idm-front { background: #1c1c1c; }
    [data-theme="dark"] .idm-fullname { color: #ebebeb; }
    [data-theme="dark"] .idm-desg { color: #999999; }
    [data-theme="dark"] .idm-front-foot { border-top-color: #2e2e2e; }
    [data-theme="dark"] .idm-front-org { color: #444444; }
    [data-theme="dark"] .idm-front-hint { color: #555555; }
    [data-theme="dark"] .idm-avatar-wrap { background: #2a2a2a; }
    [data-theme="dark"] .idm-back-face { background: #1c1c1c; }
    [data-theme="dark"] .idm-back-body { border-left-color: var(--idm-color); }
    [data-theme="dark"] .idm-back-row { border-bottom-color: #2e2e2e; }
    [data-theme="dark"] .idm-lbl { color: #555555; }
    [data-theme="dark"] .idm-val { color: #ebebeb; }
    [data-theme="dark"] .idm-viber-val { color: #ebebeb; }
    [data-theme="dark"] .idm-back-foot { border-top-color: #2e2e2e; }
    [data-theme="dark"] .idm-barcode { fill: #ebebeb; opacity: 0.15; }
    [data-theme="dark"] .idm-idnum { color: #555555; }
    /* Secret rows on ID card back */
    .idm-secret-row { cursor: pointer; border-radius: 3px; transition: background 0.12s; }
    .idm-secret-row:hover { background: rgba(0,0,0,0.04); }
    [data-theme="dark"] .idm-secret-row:hover { background: rgba(255,255,255,0.05); }
    .idm-sblur { filter: blur(4px); transition: filter 0.28s ease; user-select: none; pointer-events: none; }
    .idm-sblur.revealed { filter: blur(0); user-select: text; }
    .idm-seye { font-size: 0.58rem; opacity: 0.35; margin-left: 4px; flex-shrink: 0; transition: opacity 0.15s; }
    .idm-secret-row:hover .idm-seye { opacity: 0.65; }
    .idm-slock { font-size: 0.42rem; opacity: 0.3; vertical-align: middle; margin-left: 2px; }
    /* Permanently hidden — owner set this as private */
    .idm-secret-row.idm-perm-hidden { cursor: default; pointer-events: none; }
    .idm-secret-row.idm-perm-hidden:hover { background: none; }
    .idm-secret-row.idm-perm-hidden .idm-seye { display: none; }
    .idm-perm-lock { font-size: 0.55rem; opacity: 0.35; margin-left: 4px; flex-shrink: 0; }
    [data-theme="dark"] .idr-head      .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(124,58,237,0.12) 100%); }
    [data-theme="dark"] .idr-manager   .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(30,41,59,0.2) 100%); }
    [data-theme="dark"] .idr-lead      .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(99,102,241,0.12) 100%); }
    [data-theme="dark"] .idr-analyst   .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(236,72,153,0.12) 100%); }
    [data-theme="dark"] .idr-researcher .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(16,185,129,0.12) 100%); }
    [data-theme="dark"] .idr-content   .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(14,165,233,0.12) 100%); }
    [data-theme="dark"] .idr-graphics  .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(245,158,11,0.12) 100%); }
    [data-theme="dark"] .idr-backend   .idm-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(244,63,94,0.12) 100%); }
    [data-theme="dark"] .idr-head      .idm-back-face { background: linear-gradient(160deg, #1e1a2e 0%, #251a3a 100%); }
    [data-theme="dark"] .idr-manager   .idm-back-face { background: linear-gradient(160deg, #1a1c22 0%, #1e2028 100%); }
    [data-theme="dark"] .idr-lead      .idm-back-face { background: linear-gradient(160deg, #1a1a2e 0%, #211a38 100%); }
    [data-theme="dark"] .idr-analyst   .idm-back-face { background: linear-gradient(160deg, #2a1a22 0%, #311a2a 100%); }
    [data-theme="dark"] .idr-researcher .idm-back-face { background: linear-gradient(160deg, #141e1a 0%, #162218 100%); }
    [data-theme="dark"] .idr-content   .idm-back-face { background: linear-gradient(160deg, #121e26 0%, #152330 100%); }
    [data-theme="dark"] .idr-graphics  .idm-back-face { background: linear-gradient(160deg, #261e10 0%, #2e2210 100%); }
    [data-theme="dark"] .idr-backend   .idm-back-face { background: linear-gradient(160deg, #261212 0%, #2e1212 100%); }
</style>
@endsection

@section('content')
<x-sidebar :isAdmin="Auth::user()->isAdmin()" active="team" />

<div class="main-content">

    @php
        $avatarUrl = fn($u) => $u->avatarUrl();
        $idNum  = fn($u) => 'ECD-' . str_pad(abs(crc32($u->username)) % 9999 + 1, 4, '0', STR_PAD_LEFT);
        $extraData = fn($u) => implode(' ', [
            'data-nickname="'   . e($u->nickname  ?: $u->first_name) . '"',
            'data-idnumber="'   . e($u->id_number ?: ('ECD-' . str_pad(abs(crc32($u->username)) % 9999 + 1, 4, '0', STR_PAD_LEFT))) . '"',
            'data-tin="'        . e($u->tin  ?: '—') . '"',
            'data-tinhidden="'  . ($u->tin_hidden  ? '1' : '0') . '"',
            'data-sss="'        . e($u->sss  ?: '—') . '"',
            'data-ssshidden="'  . ($u->sss_hidden  ? '1' : '0') . '"',
        ]);
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
        <div class="tm-lcard" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="head" data-role-label="Ecomm Head" data-designation="Ecomm Department Head"
             data-icon="fa-crown" data-color="#7c3aed" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <div class="tm-lcard-body">
                <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-lcard-avatar" alt="{{ $u->full_name }}">
                <div class="tm-lcard-name">{{ $u->full_name }}</div>
                <div class="tm-lcard-sub">Ecomm Department Head</div>
                <span class="role-badge head">Ecomm Head</span>
                <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-lcard" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="manager" data-role-label="Manager" data-designation="Department Manager"
             data-icon="fa-crown" data-color="#1e293b" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <div class="tm-lcard-body">
                <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-lcard-avatar" alt="{{ $u->full_name }}">
                <div class="tm-lcard-name">{{ $u->full_name }}</div>
                <div class="tm-lcard-sub">Ecomm Department</div>
                <span class="role-badge manager">Manager</span>
                <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-lcard" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="lead" data-role-label="Lead" data-designation="Team Lead"
             data-icon="fa-star" data-color="#6366f1" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <div class="tm-lcard-body">
                <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-lcard-avatar" alt="{{ $u->full_name }}">
                <div class="tm-lcard-name">{{ $u->full_name }}</div>
                <div class="tm-lcard-sub">Content &amp; PR Lead</div>
                <span class="role-badge lead">Lead</span>
                <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-card" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="analyst" data-role-label="Analyst" data-designation="Data Analyst"
             data-icon="fa-chart-bar" data-color="#ec4899" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-avatar" alt="{{ $u->full_name }}">
            <div class="tm-name">{{ $u->full_name }}</div>
            <span class="role-badge analyst">Analyst</span>
            <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-card" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="researcher" data-role-label="Researcher" data-designation="Product Researcher"
             data-icon="fa-magnifying-glass" data-color="#10b981" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-avatar" alt="{{ $u->full_name }}">
            <div class="tm-name">{{ $u->full_name }}</div>
            <span class="role-badge researcher">Researcher</span>
            <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-card" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="content" data-role-label="Content" data-designation="Content Associate"
             data-icon="fa-pen-nib" data-color="#0ea5e9" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-avatar" alt="{{ $u->full_name }}">
            <div class="tm-name">{{ $u->full_name }}</div>
            <span class="role-badge content">Content</span>
            <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-card" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="graphics" data-role-label="Graphics" data-designation="Graphic Designer"
             data-icon="fa-palette" data-color="#f59e0b" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-avatar" alt="{{ $u->full_name }}">
            <div class="tm-name">{{ $u->full_name }}</div>
            <span class="role-badge graphics">Graphics</span>
            <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
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
        <div class="tm-card" onclick="openIdCard(this)"
             data-name="{{ $u->full_name }}" data-username="{{ $u->username }}"
             data-avatar="{{ $avatarUrl($u) }}" data-mobile="{{ $u->mobile_number ?? '' }}"
             data-role="backend" data-role-label="Backend" data-designation="Backend Developer"
             data-icon="fa-server" data-color="#f43f5e" data-idnum="{{ $idNum($u) }}" {!! $extraData($u) !!}>
            <img src="{{ $avatarUrl($u) }}" style="object-fit:cover;" class="tm-avatar" alt="{{ $u->full_name }}">
            <div class="tm-name">{{ $u->full_name }}</div>
            <span class="role-badge backend">Backend</span>
            <div class="tm-id-hint"><i class="fas fa-id-card"></i> View ID card</div>
        </div>
        @endforeach
    </div>
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-server"></i> No backend members yet.</div>
    @endif
    </div>

</div>

{{-- ID Card Modal --}}
<div id="idCardModal" class="idm-overlay" onclick="if(event.target===this)closeIdModal()">
    <button class="idm-close" onclick="closeIdModal()"><i class="fas fa-times"></i></button>
    <div class="idm-lanyard"></div>
    <div class="idm-flip-card" id="idFlipCard" onclick="this.classList.toggle('flipped')">
        <div class="idm-flip-inner">
            {{-- Front --}}
            <div class="idm-face idm-front">
                <div class="idm-top" id="idmColorTop">
                    <div class="idm-top-dots"></div>
                    <div class="idm-hole"></div>
                    <div class="idm-co-row">
                        <div class="idm-co-icon"><i id="idmIcon" class="fas fa-crown"></i></div>
                        <div>
                            <div class="idm-co-name">Ecomm Dept</div>
                            <div class="idm-co-sub">Employee ID</div>
                        </div>
                    </div>
                    <i id="idmBgIcon" class="fas fa-crown idm-top-icon-bg"></i>
                    <div class="idm-avatar-wrap">
                        <img id="idmPhoto" src="" class="idm-photo" alt="">
                    </div>
                </div>
                <div class="idm-info">
                    <div id="idmName" class="idm-fullname"></div>
                    <div id="idmDesg" class="idm-desg"></div>
                    <span id="idmBadge" class="role-badge"></span>
                    <div class="idm-rule"></div>
                    <div class="idm-front-foot">
                        <span class="idm-front-org">Ecomm Team</span>
                        <span class="idm-front-hint"><i class="fas fa-rotate" style="font-size:0.55rem"></i> Tap to flip</span>
                    </div>
                </div>
            </div>
            {{-- Back --}}
            <div class="idm-face idm-back-face">
                <div class="idm-back-head" id="idmBackHeader">
                    <div class="idm-top-dots"></div>
                    <div class="idm-back-co">Ecomm Dept — ID Card</div>
                </div>
                <i id="idmBackIcon" class="fas fa-crown idm-back-wm"></i>
                <div class="idm-back-body">
                    <div class="idm-back-row"><span class="idm-lbl">Full Name</span><span id="idmBackName" class="idm-val"></span></div>
                    <div class="idm-back-row"><span class="idm-lbl">Nickname</span><span id="idmBackNickname" class="idm-val"></span></div>
                    <div class="idm-back-row"><span class="idm-lbl">Username</span><span id="idmBackUser" class="idm-val"></span></div>
                    <div class="idm-back-row" id="idmViberRow"><span class="idm-lbl">Viber</span><a id="idmViberLink" href="#" class="idm-val idm-viber-val" onclick="event.stopPropagation()"></a></div>
                    <div class="idm-back-row"><span class="idm-lbl">ID No.</span><span id="idmBackIdnum" class="idm-val"></span></div>
                    <div class="idm-back-row idm-secret-row" onclick="toggleIdmSecret(this, event)">
                        <span class="idm-lbl">TIN <i class="fas fa-lock idm-slock"></i></span>
                        <span class="idm-val" style="display:flex;align-items:center;">
                            <span id="idmBackTin" class="idm-sblur"></span>
                            <i class="fas fa-eye idm-seye"></i>
                            <i class="fas fa-lock idm-perm-lock" style="display:none;"></i>
                        </span>
                    </div>
                    <div class="idm-back-row idm-secret-row" onclick="toggleIdmSecret(this, event)">
                        <span class="idm-lbl">SSS <i class="fas fa-lock idm-slock"></i></span>
                        <span class="idm-val" style="display:flex;align-items:center;">
                            <span id="idmBackSss" class="idm-sblur"></span>
                            <i class="fas fa-eye idm-seye"></i>
                            <i class="fas fa-lock idm-perm-lock" style="display:none;"></i>
                        </span>
                    </div>
                </div>
                <div class="idm-back-foot">
                    <svg class="idm-barcode" viewBox="0 0 80 18" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="0" width="2" height="18"/><rect x="4" y="0" width="1" height="18"/><rect x="7" y="0" width="3" height="18"/><rect x="12" y="0" width="1" height="18"/><rect x="15" y="0" width="2" height="18"/><rect x="19" y="0" width="3" height="18"/><rect x="24" y="0" width="1" height="18"/><rect x="27" y="0" width="2" height="18"/><rect x="31" y="0" width="1" height="18"/><rect x="34" y="0" width="3" height="18"/><rect x="39" y="0" width="2" height="18"/><rect x="43" y="0" width="1" height="18"/><rect x="46" y="0" width="3" height="18"/><rect x="51" y="0" width="1" height="18"/><rect x="54" y="0" width="2" height="18"/><rect x="58" y="0" width="1" height="18"/><rect x="61" y="0" width="3" height="18"/><rect x="66" y="0" width="2" height="18"/><rect x="72" y="0" width="2" height="18"/><rect x="76" y="0" width="1" height="18"/><rect x="79" y="0" width="1" height="18"/></svg>
                    <div id="idmIdnum" class="idm-idnum"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="idm-tap-hint">Click the card to flip</div>
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

window.openIdCard = function(el) {
    var d = el.dataset;
    document.getElementById('idmPhoto').src = d.avatar;
    document.getElementById('idmName').textContent = d.name;
    document.getElementById('idmDesg').textContent = d.designation;
    var badge = document.getElementById('idmBadge');
    badge.textContent = d.roleLabel;
    badge.className = 'role-badge ' + d.role;
    var iconCls = 'fas ' + d.icon;
    document.getElementById('idmIcon').className = iconCls;
    document.getElementById('idmBgIcon').className = iconCls + ' idm-top-icon-bg';
    document.getElementById('idmBackIcon').className = iconCls + ' idm-back-wm';
    document.getElementById('idmColorTop').style.background = d.color;
    document.getElementById('idmBackHeader').style.background = d.color;
    var flipCard = document.getElementById('idFlipCard');
    flipCard.className = flipCard.className.replace(/\bidr-\w+/g, '').trim() + ' idr-' + d.role;
    flipCard.style.setProperty('--idm-color', d.color);
    document.getElementById('idmBackName').textContent = d.name;
    document.getElementById('idmBackNickname').textContent = d.nickname || d.name.split(' ')[0];
    document.getElementById('idmBackUser').textContent = '@' + d.username;
    document.getElementById('idmBackIdnum').textContent = d.idnumber || d.idnum;
    var tinEl = document.getElementById('idmBackTin');
    var sssEl = document.getElementById('idmBackSss');
    tinEl.textContent = d.tin || '—';
    sssEl.textContent = d.sss || '—';
    tinEl.classList.remove('revealed');
    sssEl.classList.remove('revealed');
    document.querySelectorAll('.idm-seye').forEach(function(el) { el.className = 'fas fa-eye idm-seye'; });
    // Apply or remove permanently-hidden class based on owner's privacy settings
    var tinRow = tinEl.closest('.idm-secret-row');
    var sssRow = sssEl.closest('.idm-secret-row');
    [
        { row: tinRow, hidden: d.tinhidden === '1' },
        { row: sssRow, hidden: d.ssshidden === '1' },
    ].forEach(function(item) {
        item.row.classList.toggle('idm-perm-hidden', item.hidden);
        var permLock = item.row.querySelector('.idm-perm-lock');
        if (permLock) permLock.style.display = item.hidden ? '' : 'none';
    });
    document.getElementById('idmIdnum').textContent = d.idnumber || d.idnum;
    var viberRow = document.getElementById('idmViberRow');
    var viberLink = document.getElementById('idmViberLink');
    if (d.mobile) {
        viberRow.style.display = '';
        viberLink.textContent = d.mobile;
        viberLink.href = 'viber://chat?number=' + d.mobile;
    } else {
        viberRow.style.display = 'none';
    }
    flipCard.classList.remove('flipped');
    document.getElementById('idCardModal').classList.add('open');
};

window.toggleIdmSecret = function(row, e) {
    e.stopPropagation();
    if (row.classList.contains('idm-perm-hidden')) return;
    var blur = row.querySelector('.idm-sblur');
    var eye  = row.querySelector('.idm-seye');
    var isRevealing = !blur.classList.contains('revealed');
    blur.classList.toggle('revealed', isRevealing);
    eye.className = isRevealing ? 'fas fa-eye-slash idm-seye' : 'fas fa-eye idm-seye';
};
window.closeIdModal = function() {
    document.getElementById('idCardModal').classList.remove('open');
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeIdModal();
});
</script>
@endsection
