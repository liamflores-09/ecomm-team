@extends('layouts.app')

@section('title', 'Dashboard — Ecomm Dept')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><rect x='3' y='14' width='7' height='7'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Hero Card ── */
    .dash-hero {
        background: var(--card); border: 1px solid var(--border-light);
        border-left: 5px solid var(--hero-color, #5757f8);
        border-radius: 16px; padding: 2rem 2.25rem 2rem 2.25rem;
        display: flex; align-items: center; gap: 2.5rem;
        margin-bottom: 1.25rem; position: relative; overflow: hidden;
        min-height: 168px;
    }
    .dh-bg-dots {
        position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(circle, var(--border-light) 1.2px, transparent 1.2px);
        background-size: 22px 22px; opacity: 0.7;
    }
    .dh-bg-circle {
        position: absolute; border-radius: 50%; pointer-events: none;
        background: var(--hero-color, #5757f8);
    }
    .dh-bg-c1 { width: 280px; height: 280px; opacity: 0.07; top: -110px; right: 230px; }
    .dh-bg-c2 { width: 130px; height: 130px; opacity: 0.045; bottom: -55px; right: 195px; }
    .dh-bg-c3 {
        width: 60px; height: 60px; top: 18px; right: 370px;
        background: transparent; opacity: 1;
        border: 1.5px solid var(--hero-color, #5757f8); opacity: 0.14;
    }
    .dh-bg-icon {
        position: absolute; font-size: 200px; line-height: 1;
        color: var(--hero-color, #5757f8); opacity: 0.04;
        right: 148px; top: 50%; transform: translateY(-50%);
        pointer-events: none;
    }
    .dh-info { flex: 1; min-width: 0; z-index: 1; }
    .dh-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        font-size: 0.62rem; font-weight: 800; letter-spacing: 0.14em;
        text-transform: uppercase; color: var(--hero-color, #5757f8);
        margin-bottom: 0.45rem;
    }
    .dh-eyebrow-line { width: 28px; height: 1.5px; background: var(--hero-color, #5757f8); opacity: 0.5; border-radius: 99px; flex-shrink: 0; }
    .dh-greeting-label { font-size: 0.76rem; font-weight: 500; color: var(--muted-foreground); margin-bottom: 0.1rem; }
    .dh-name {
        font-size: 2.8rem; font-weight: 900; color: var(--foreground);
        line-height: 1; margin-bottom: 0.3rem;
        font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.03em;
    }
    .dh-designation { font-size: 0.9rem; font-weight: 600; color: var(--muted-foreground); margin-bottom: 0.875rem; }
    .dh-divider {
        width: 52px; height: 3px; border-radius: 99px; margin-bottom: 0.875rem;
        background: linear-gradient(90deg, var(--hero-color, #5757f8) 0%, transparent 100%);
    }
    .dh-meta { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem; }
    .dh-sep { color: var(--border-strong); font-size: 0.75rem; }
    .dh-username { font-size: 0.78rem; color: var(--muted-foreground); font-weight: 600; }
    .dh-date { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .dh-eod { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.875rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: opacity 0.15s; }
    .dh-eod:hover { opacity: 0.85; }
    .dh-eod.done { background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.25); }
    .dh-eod.pending { background: var(--hero-color, #5757f8); color: white; box-shadow: 0 3px 14px rgba(0,0,0,0.18); }
    .dh-eod.rdo { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
    .dh-eod-edit { font-size: 0.72rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; padding: 0.3rem 0.75rem; border: 1px solid var(--border-light); border-radius: 9999px; transition: all 0.15s; }
    .dh-eod-edit:hover { border-color: var(--foreground); color: var(--foreground); }
    .dh-eod-row { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
    .dh-id-wrap { flex-shrink: 0; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .dh-id-hint { font-size: 0.6rem; color: var(--muted-foreground); text-align: center; }
    /* Dark mode — hero */
    [data-theme="dark"] .dh-bg-dots { opacity: 0.2; }
    [data-theme="dark"] .dh-bg-c1 { opacity: 0.1; }
    [data-theme="dark"] .dh-bg-c2 { opacity: 0.07; }
    [data-theme="dark"] .dh-bg-icon { opacity: 0.06; }

    /* ── Inline Mini ID Card ── */
    .mini-flip { width: 155px; cursor: pointer; perspective: 800px; --mini-color: #5757f8; }
    .mini-flip-inner { display: grid; grid-template-columns: 1fr; transform-style: preserve-3d; transition: transform 0.5s cubic-bezier(0.4,0,0.2,1); }
    .mini-flip-inner.flipped { transform: rotateY(180deg); }
    .mini-face { grid-area: 1/1; border-radius: 14px; overflow: hidden; backface-visibility: hidden; -webkit-backface-visibility: hidden; box-shadow: 0 10px 36px rgba(0,0,0,0.2), 0 2px 8px rgba(0,0,0,0.12); position: relative; }
    .mini-back { transform: rotateY(180deg); }

    /* Mini Front */
    .mini-front { background: #fff; display: flex; flex-direction: column; }
    .mini-top { height: 86px; position: relative; flex-shrink: 0; display: flex; flex-direction: column; padding: 10px 12px 0; overflow: visible; }
    .mini-top-dots { position: absolute; inset: 0; pointer-events: none; background-image: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px); background-size: 10px 10px; }
    .mini-hole { position: absolute; top: 7px; left: 50%; transform: translateX(-50%); width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.5); z-index: 2; }
    .mini-co-row { display: flex; align-items: center; gap: 6px; position: relative; z-index: 1; margin-top: 14px; }
    .mini-co-icon { width: 22px; height: 22px; border-radius: 6px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.6rem; flex-shrink: 0; }
    .mini-co-name { font-size: 0.58rem; font-weight: 800; letter-spacing: 0.06em; color: white; text-transform: uppercase; line-height: 1.1; }
    .mini-co-sub { font-size: 0.44rem; color: rgba(255,255,255,0.55); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
    .mini-bg-icon { position: absolute; bottom: -10px; right: -5px; font-size: 70px; color: rgba(255,255,255,0.1); pointer-events: none; line-height: 1; z-index: 0; }
    .mini-avatar-wrap { position: absolute; bottom: -34px; left: 50%; transform: translateX(-50%); z-index: 4; width: 68px; height: 68px; border-radius: 50%; background: white; padding: 3px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); }
    .mini-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }
    .mini-info-section { padding: 40px 12px 0; display: flex; flex-direction: column; align-items: center; flex: 1; }
    .mini-fullname { font-weight: 800; font-size: 0.76rem; color: #0f172a; text-align: center; line-height: 1.25; margin-bottom: 2px; }
    .mini-desg { font-size: 0.54rem; color: #64748b; font-weight: 500; margin-bottom: 6px; }
    .mini-rule { width: 28px; height: 2px; border-radius: 99px; background: var(--mini-color, #5757f8); margin: 5px auto 8px; opacity: 0.6; }
    .mini-front-foot { margin-top: auto; width: 100%; padding: 7px 12px 10px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; }
    .mini-front-org { font-size: 0.44rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #cbd5e1; }
    .mini-flip-hint { font-size: 0.46rem; color: #94a3b8; display: flex; align-items: center; gap: 3px; }

    /* Role tints — mini front */
    .mini-idr-head      .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(124,58,237,0.07) 100%); }
    .mini-idr-manager   .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(30,41,59,0.07) 100%); }
    .mini-idr-analyst   .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(236,72,153,0.07) 100%); }
    .mini-idr-researcher .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(16,185,129,0.07) 100%); }
    .mini-idr-content   .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(14,165,233,0.07) 100%); }
    .mini-idr-graphics  .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(245,158,11,0.07) 100%); }
    .mini-idr-backend   .mini-front { background: linear-gradient(145deg, #fff 55%, rgba(244,63,94,0.07) 100%); }

    /* Mini Back */
    .mini-back-face { background: #f8fafc; display: flex; flex-direction: column; position: relative; overflow: hidden; }
    .mini-idr-head      .mini-back-face { background: linear-gradient(160deg, #faf5ff 0%, #f3e8ff 100%); }
    .mini-idr-manager   .mini-back-face { background: linear-gradient(160deg, #f1f5f9 0%, #e2e8f0 100%); }
    .mini-idr-analyst   .mini-back-face { background: linear-gradient(160deg, #fdf2f8 0%, #fce7f3 100%); }
    .mini-idr-researcher .mini-back-face { background: linear-gradient(160deg, #f0fdf4 0%, #dcfce7 100%); }
    .mini-idr-content   .mini-back-face { background: linear-gradient(160deg, #f0f9ff 0%, #e0f2fe 100%); }
    .mini-idr-graphics  .mini-back-face { background: linear-gradient(160deg, #fffbeb 0%, #fef3c7 100%); }
    .mini-idr-backend   .mini-back-face { background: linear-gradient(160deg, #fff1f2 0%, #ffe4e6 100%); }
    .mini-back-wm { position: absolute; bottom: 10px; right: -14px; font-size: 80px; color: var(--mini-color, #5757f8); opacity: 0.06; pointer-events: none; line-height: 1; z-index: 0; }
    .mini-back-head { padding: 10px 12px; position: relative; flex-shrink: 0; display: flex; align-items: center; }
    .mini-back-co { font-size: 0.56rem; font-weight: 800; letter-spacing: 0.07em; color: rgba(255,255,255,0.92); text-transform: uppercase; position: relative; z-index: 1; }
    .mini-back-body { padding: 3px 12px 3px 16px; flex: 1; position: relative; z-index: 1; border-left: 2px solid var(--mini-color, #5757f8); margin: 8px 12px; }
    .mini-back-row { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid rgba(0,0,0,0.07); }
    .mini-back-row:last-child { border-bottom: none; }
    .mini-secret-row { cursor: pointer; border-radius: 3px; transition: background 0.12s; margin: 0 -4px; padding: 6px 4px; }
    .mini-secret-row:hover { background: rgba(0,0,0,0.05); }
    [data-theme="dark"] .mini-secret-row:hover { background: rgba(255,255,255,0.04); }
    .mini-sblur { filter: blur(3.5px); transition: filter 0.28s ease; user-select: none; }
    .mini-sblur.revealed { filter: blur(0); user-select: text; }
    .mini-seye { font-size: 0.48rem; opacity: 0.35; margin-left: 3px; flex-shrink: 0; transition: opacity 0.15s; }
    .mini-secret-row:hover .mini-seye { opacity: 0.65; }
    .mini-lbl { color: #94a3b8; font-weight: 700; text-transform: uppercase; font-size: 0.44rem; letter-spacing: 0.05em; }
    .mini-val { font-weight: 700; color: #0f172a; font-size: 0.58rem; text-align: right; max-width: 55%; word-break: break-all; }
    .mini-back-foot { padding: 6px 12px 10px; border-top: 1px solid rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; z-index: 1; position: relative; }
    .mini-barcode { width: 56px; height: 14px; fill: #1e293b; opacity: 0.3; }
    .mini-idnum { font-family: monospace; font-size: 0.5rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.03em; }

    /* Dark mode — hero */
    [data-theme="dark"] .dh-bg-dots { opacity: 0.15; }
    /* Dark mode — mini card */
    [data-theme="dark"] .mini-front { background: #1c1c1c; }
    [data-theme="dark"] .mini-fullname { color: #ebebeb; }
    [data-theme="dark"] .mini-desg { color: #999999; }
    [data-theme="dark"] .mini-front-foot { border-top-color: #2e2e2e; }
    [data-theme="dark"] .mini-avatar-wrap { background: #2a2a2a; }
    [data-theme="dark"] .mini-back-face { background: #1c1c1c; }
    [data-theme="dark"] .mini-back-row { border-bottom-color: #2e2e2e; }
    [data-theme="dark"] .mini-lbl { color: #555555; }
    [data-theme="dark"] .mini-val { color: #ebebeb; }
    [data-theme="dark"] .mini-back-foot { border-top-color: #2e2e2e; }
    [data-theme="dark"] .mini-barcode { fill: #ebebeb; opacity: 0.15; }
    [data-theme="dark"] .mini-idr-head      .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(124,58,237,0.12) 100%); }
    [data-theme="dark"] .mini-idr-manager   .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(30,41,59,0.2) 100%); }
    [data-theme="dark"] .mini-idr-analyst   .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(236,72,153,0.12) 100%); }
    [data-theme="dark"] .mini-idr-researcher .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(16,185,129,0.12) 100%); }
    [data-theme="dark"] .mini-idr-content   .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(14,165,233,0.12) 100%); }
    [data-theme="dark"] .mini-idr-graphics  .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(245,158,11,0.12) 100%); }
    [data-theme="dark"] .mini-idr-backend   .mini-front { background: linear-gradient(145deg, #1c1c1c 55%, rgba(244,63,94,0.12) 100%); }
    [data-theme="dark"] .mini-idr-head      .mini-back-face { background: linear-gradient(160deg, #1e1a2e 0%, #251a3a 100%); }
    [data-theme="dark"] .mini-idr-manager   .mini-back-face { background: linear-gradient(160deg, #1a1c22 0%, #1e2028 100%); }
    [data-theme="dark"] .mini-idr-analyst   .mini-back-face { background: linear-gradient(160deg, #2a1a22 0%, #311a2a 100%); }
    [data-theme="dark"] .mini-idr-researcher .mini-back-face { background: linear-gradient(160deg, #141e1a 0%, #162218 100%); }
    [data-theme="dark"] .mini-idr-content   .mini-back-face { background: linear-gradient(160deg, #121e26 0%, #152330 100%); }
    [data-theme="dark"] .mini-idr-graphics  .mini-back-face { background: linear-gradient(160deg, #261e10 0%, #2e2210 100%); }
    [data-theme="dark"] .mini-idr-backend   .mini-back-face { background: linear-gradient(160deg, #261212 0%, #2e1212 100%); }

    /* ── Stats ── */
    .section-divider { display: flex; align-items: center; gap: 0.75rem; margin: 1.5rem 0 1rem; }
    .section-divider .sd-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; background: var(--primary); font-size: 0.75rem; flex-shrink: 0; }
    .section-divider h4 { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; font-family: 'Space Grotesk', sans-serif; }
    .section-divider .sd-line { flex: 1; height: 1px; background: var(--border-light); }

    .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
    .stat-card { background: var(--card); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid var(--border-light); transition: border-color 0.2s, box-shadow 0.2s; }
    .stat-card:hover { border-color: var(--border-strong); box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; flex-shrink: 0; }
    .stat-count { font-size: 1.75rem; font-weight: 800; line-height: 1; margin-bottom: 0.125rem; font-family: 'Space Grotesk', sans-serif; }
    .stat-label { font-size: 0.78rem; font-weight: 600; color: var(--muted-foreground); }

    /* ── Bento grid ── */
    .bento-grid { display: flex; gap: 1rem; margin-bottom: 1.25rem; align-items: stretch; }
    .bento-ann { flex: 1; min-width: 0; background: var(--card); border: 1px solid var(--border-light); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; }
    .bento-right { width: 320px; flex-shrink: 0; display: flex; flex-direction: column; gap: 1rem; }
    .bento-ann-hd { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-light); flex-shrink: 0; }
    .bento-ann-hd h4 { font-size: 0.85rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .bento-ann-hd a { font-size: 0.78rem; font-weight: 600; color: var(--primary); text-decoration: none; }
    .bento-ann-item { flex: 1; display: flex; flex-direction: column; text-decoration: none; color: inherit; padding: 1.125rem 1.25rem; border-top: 1px solid var(--border-light); transition: background 0.15s; }
    .bento-ann-item:first-of-type { border-top: none; }
    .bento-ann-item:hover { background: var(--muted); }
    .bento-ann-item.pinned { border-left: 3px solid #f59e0b; padding-left: calc(1.25rem - 3px); }
    .bento-ann-pin-badge { display: inline-flex; align-items: center; gap: 0.25rem; color: #d97706; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.3rem; }
    .bento-ann-item-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.5rem; }
    .bento-ann-title { font-weight: 700; font-size: 0.925rem; line-height: 1.35; flex: 1; color: var(--fg); letter-spacing: -0.01em; }
    .bento-ann-chevron { color: var(--muted-foreground); font-size: 0.6rem; margin-top: 3px; flex-shrink: 0; opacity: 0; transition: opacity 0.15s, transform 0.15s; }
    .bento-ann-item:hover .bento-ann-chevron { opacity: 1; transform: translateX(2px); }
    .bento-ann-body { font-size: 0.8rem; color: var(--muted-foreground); font-weight: 400; line-height: 1.65; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }
    .bento-ann-foot { display: flex; align-items: center; gap: 0.5rem; margin-top: auto; padding-top: 0.75rem; }
    .bento-ann-avatar { width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0; background: var(--muted); object-fit: cover; border: 1px solid var(--border-light); }
    .bento-ann-author { font-size: 0.7rem; font-weight: 600; color: var(--muted-foreground); flex: 1; }
    .bento-ann-expiry { display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.65rem; font-weight: 700; color: #d97706; background: rgba(245,158,11,0.1); padding: 0.15rem 0.4rem; border-radius: 9999px; }
    .bento-ann-empty { padding: 2.5rem 1.25rem; text-align: center; color: var(--muted-foreground); font-size: 0.82rem; }
    .bento-ann-empty i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.25; }
    .bento-quick { flex: 1; background: var(--card); border: 1px solid var(--border-light); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; }
    .bento-quick-hd { display: flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.125rem; border-bottom: 1px solid var(--border-light); font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-400); flex-shrink: 0; }
    .bento-ql-grid { display: grid; grid-template-columns: 1fr 1fr; flex: 1; gap: 1px; background: var(--border-light); }
    .bento-ql-box { background: var(--card); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; padding: 1.375rem 0.75rem; text-decoration: none; color: var(--foreground); text-align: center; transition: background 0.12s; }
    .bento-ql-box:hover { background: var(--muted); }
    .bento-ql-box:hover .bento-ql-icon { transform: scale(1.08); }
    .bento-ql-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: white; transition: transform 0.15s; flex-shrink: 0; }
    .bento-ql-name { font-size: 0.72rem; font-weight: 700; line-height: 1.3; }
    .bento-ql-desc { display: none; }

    /* ── Chart / Logs ── */
    .chart-section { background: var(--card); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.25rem; border: 1px solid var(--border-light); }
    .chart-section #weeklyChart { width: 100% !important; }
    .logs-section { background: var(--card); border-radius: 12px; border: 1px solid var(--border-light); margin-bottom: 2rem; overflow: hidden; }
    .logs-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-light); }
    .logs-header h4 { font-size: 0.85rem; font-weight: 700; margin: 0; }
    .logs-header a { font-size: 0.8rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; }
    .logs-header a:hover { color: var(--foreground); }
    .logs-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .logs-table thead tr { border-bottom: 1px solid var(--border-light); }
    .logs-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted-foreground); }
    .logs-table td { padding: 0.75rem 1rem; color: var(--foreground); border-bottom: 1px solid var(--border-light); }
    .logs-table tbody tr:last-child td { border-bottom: none; }
    .logs-table tbody tr:hover td { background: var(--background); }
    .date-cell { font-weight: 600; white-space: nowrap; }
    .num { text-align: center; color: var(--muted-foreground); font-variant-numeric: tabular-nums; }
    .empty-state { padding: 2.5rem 1.5rem; text-align: center; color: var(--muted-foreground); font-size: 0.875rem; }
    .empty-state i { display: block; font-size: 1.5rem; margin-bottom: 0.75rem; opacity: 0.4; }
    .empty-state a { color: var(--foreground); font-weight: 600; text-decoration: underline; }
</style>
@endsection

@section('content')
@php
$effectiveRole = $isPreview ? $previewRole : $user->role;
$roleColor = match($effectiveRole) {
    'content'    => '#0ea5e9',
    'researcher' => '#10b981',
    'graphics'   => '#f59e0b',
    'backend'    => '#f43f5e',
    'analyst'    => '#ec4899',
    default      => '#5757f8',
};
$avatarUrl = $user->avatarUrl();
$idNum = 'ECD-' . str_pad(abs(crc32($user->username)) % 9999 + 1, 4, '0', STR_PAD_LEFT);
$roleIcon = match($user->role) {
    'head'       => 'fa-crown',
    'manager'    => 'fa-chart-bar',
    'analyst'    => 'fa-magnifying-glass',
    'researcher' => 'fa-magnifying-glass',
    'content'    => 'fa-pen-nib',
    'graphics'   => 'fa-palette',
    'backend'    => 'fa-server',
    default      => 'fa-user',
};
$designation = match($user->role) {
    'head'       => 'Ecomm Head',
    'manager'    => 'Manager',
    'analyst'    => 'Analyst',
    'researcher' => 'Researcher',
    'content'    => 'Content Associate',
    'graphics'   => 'Graphics Designer',
    'backend'    => 'Backend Ops',
    default      => ucfirst($user->role),
};
$greeting = match(true) {
    now()->hour < 12 => 'Good morning',
    now()->hour < 17 => 'Good afternoon',
    default          => 'Good evening',
};
@endphp
<x-sidebar active="dashboard" />

<div class="main-content">
    <!-- Hero Card -->
    <div class="dash-hero anim-up" style="--hero-color: {{ $roleColor }};">
        <!-- Background layers -->
        <div class="dh-bg-dots"></div>
        <div class="dh-bg-circle dh-bg-c1"></div>
        <div class="dh-bg-circle dh-bg-c2"></div>
        <div class="dh-bg-circle dh-bg-c3"></div>
        <i class="fas {{ $roleIcon }} dh-bg-icon"></i>

        <!-- Info block -->
        <div class="dh-info">
            <div class="dh-eyebrow">
                <span class="dh-eyebrow-line"></span>
                Ecomm Team &middot; Dashboard
            </div>
            <div class="dh-greeting-label">{{ $greeting }},</div>
            <div class="dh-name">{{ $user->first_name }}!</div>
            <div class="dh-designation">{{ $designation }}</div>
            <div class="dh-divider"></div>
            <div class="dh-meta">
                <span class="dh-username">{{ '@'.$user->username }}</span>
                <span class="dh-sep">·</span>
                <span class="dh-date">{{ now()->format('D, M j') }}</span>
            </div>
            @if($effectiveRole !== 'analyst')
            <div class="dh-eod-row">
                @if(now()->dayOfWeek === 0)
                <span class="dh-eod rdo"><i class="fas fa-umbrella-beach"></i> Rest Day (RDO)</span>
                @elseif($todayLog)
                <span class="dh-eod done"><i class="fas fa-circle-check"></i> EOD Submitted</span>
                <a href="{{ route('end-of-day') }}" class="dh-eod-edit">Edit report <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i></a>
                @else
                <a href="{{ route('end-of-day') }}" class="dh-eod pending"><i class="fas fa-clipboard-list"></i> Submit EOD Report</a>
                @endif
            </div>
            @endif
        </div>

        <!-- Mini ID Card -->
        <div class="dh-id-wrap">
            <div class="mini-flip" style="--mini-color: {{ $roleColor }};" onclick="this.querySelector('.mini-flip-inner').classList.toggle('flipped')">
                <div class="mini-flip-inner mini-idr-{{ $user->role }}">
                    <!-- Front -->
                    <div class="mini-face mini-front">
                        <div class="mini-top" style="background: {{ $roleColor }};">
                            <div class="mini-top-dots"></div>
                            <div class="mini-hole"></div>
                            <div class="mini-co-row">
                                <div class="mini-co-icon"><i class="fas {{ $roleIcon }}"></i></div>
                                <div>
                                    <div class="mini-co-name">Ecomm Dept</div>
                                    <div class="mini-co-sub">Employee ID</div>
                                </div>
                            </div>
                            <i class="fas {{ $roleIcon }} mini-bg-icon"></i>
                            <div class="mini-avatar-wrap">
                                <img src="{{ $avatarUrl }}" alt="{{ $user->full_name }}" class="mini-photo">
                            </div>
                        </div>
                        <div class="mini-info-section">
                            <div class="mini-fullname">{{ $user->full_name }}</div>
                            <div class="mini-desg">{{ $designation }}</div>
                            <span class="role-badge {{ $user->role }}" style="font-size:0.5rem;padding:2px 7px;">{{ ucfirst($user->role) }}</span>
                            <div class="mini-rule"></div>
                            <div class="mini-front-foot">
                                <span class="mini-front-org">Ecomm Team</span>
                                <span class="mini-flip-hint"><i class="fas fa-rotate"></i> Flip</span>
                            </div>
                        </div>
                    </div>
                    <!-- Back -->
                    <div class="mini-face mini-back-face mini-back">
                        <div class="mini-back-head" style="background: {{ $roleColor }};">
                            <div class="mini-top-dots"></div>
                            <div class="mini-back-co">Ecomm — ID Card</div>
                        </div>
                        <i class="fas {{ $roleIcon }} mini-back-wm"></i>
                        <div class="mini-back-body">
                            <div class="mini-back-row"><span class="mini-lbl">Name</span><span class="mini-val">{{ $user->full_name }}</span></div>
                            <div class="mini-back-row"><span class="mini-lbl">Nickname</span><span class="mini-val">{{ $user->nickname ?: $user->first_name }}</span></div>
                            <div class="mini-back-row"><span class="mini-lbl">ID No.</span><span class="mini-val">{{ $user->id_number ?: $idNum }}</span></div>
                            <div class="mini-back-row mini-secret-row" onclick="toggleMiniSecret(this, event)">
                                <span class="mini-lbl">TIN <i class="fas fa-lock" style="opacity:0.3;font-size:0.38rem;vertical-align:middle;margin-left:1px;"></i></span>
                                <span class="mini-val" style="display:flex;align-items:center;">
                                    <span class="mini-sblur">{{ $user->tin ?: '—' }}</span>
                                    <i class="fas fa-eye mini-seye"></i>
                                </span>
                            </div>
                            <div class="mini-back-row mini-secret-row" onclick="toggleMiniSecret(this, event)">
                                <span class="mini-lbl">SSS <i class="fas fa-lock" style="opacity:0.3;font-size:0.38rem;vertical-align:middle;margin-left:1px;"></i></span>
                                <span class="mini-val" style="display:flex;align-items:center;">
                                    <span class="mini-sblur">{{ $user->sss ?: '—' }}</span>
                                    <i class="fas fa-eye mini-seye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mini-back-foot">
                            <svg class="mini-barcode" viewBox="0 0 80 18" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="0" width="2" height="18"/><rect x="4" y="0" width="1" height="18"/><rect x="7" y="0" width="3" height="18"/><rect x="12" y="0" width="1" height="18"/><rect x="15" y="0" width="2" height="18"/><rect x="19" y="0" width="3" height="18"/><rect x="24" y="0" width="1" height="18"/><rect x="27" y="0" width="2" height="18"/><rect x="31" y="0" width="1" height="18"/><rect x="34" y="0" width="3" height="18"/><rect x="39" y="0" width="2" height="18"/><rect x="43" y="0" width="1" height="18"/><rect x="46" y="0" width="3" height="18"/><rect x="51" y="0" width="1" height="18"/><rect x="54" y="0" width="2" height="18"/><rect x="58" y="0" width="1" height="18"/><rect x="61" y="0" width="3" height="18"/><rect x="66" y="0" width="2" height="18"/><rect x="72" y="0" width="2" height="18"/><rect x="76" y="0" width="1" height="18"/><rect x="79" y="0" width="1" height="18"/></svg>
                            <div class="mini-idnum">{{ $idNum }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dh-id-hint">Click to flip</div>
        </div>
    </div>

    @if($effectiveRole === 'analyst')

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
                @if($ann->pinned)
                <div class="bento-ann-pin-badge"><i class="fas fa-thumbtack"></i> Pinned</div>
                @endif
                <div class="bento-ann-item-top">
                    <span class="bento-ann-title">{{ $ann->title }}</span>
                    <i class="fas fa-chevron-right bento-ann-chevron"></i>
                </div>
                <div class="bento-ann-body">{{ $ann->body }}</div>
                <div class="bento-ann-foot">
                    <img src="{{ $ann->creator->avatarUrl() }}" class="bento-ann-avatar" alt="{{ $ann->creator->first_name }}" style="object-fit:cover;">
                    <span class="bento-ann-author">{{ $ann->creator->first_name }} · {{ $ann->created_at->diffForHumans() }}</span>
                    @if($ann->expires_at)
                    <span class="bento-ann-expiry"><i class="fas fa-clock"></i> {{ $ann->expires_at->format('M d') }}</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="bento-ann-empty"><i class="fas fa-bullhorn"></i> No announcements yet.</div>
            @endforelse
        </div>
        {{-- Right column --}}
        <div class="bento-right">
        <div class="bento-quick">
            <div class="bento-quick-hd"><i class="fas fa-bolt" style="color:var(--primary);font-size:0.65rem;"></i> Quick Access</div>
            <div class="bento-ql-grid">
            <a href="{{ route('brand-catalogs') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#ec4899;"><i class="fas fa-book-open"></i></div>
                <span class="bento-ql-name">Brand Catalogs</span>
            </a>
            <a href="{{ route('announcements') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#6366f1;"><i class="fas fa-bullhorn"></i></div>
                <span class="bento-ql-name">Announcements</span>
            </a>
            <a href="{{ route('team') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#0ea5e9;"><i class="fas fa-users"></i></div>
                <span class="bento-ql-name">The Team</span>
            </a>
            <a href="{{ route('price-calculator') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#10b981;"><i class="fas fa-calculator"></i></div>
                <span class="bento-ql-name">Price Calculator</span>
            </a>
            </div>
        </div>
        </div>{{-- /.bento-right --}}
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
                @if($ann->pinned)
                <div class="bento-ann-pin-badge"><i class="fas fa-thumbtack"></i> Pinned</div>
                @endif
                <div class="bento-ann-item-top">
                    <span class="bento-ann-title">{{ $ann->title }}</span>
                    <i class="fas fa-chevron-right bento-ann-chevron"></i>
                </div>
                <div class="bento-ann-body">{{ $ann->body }}</div>
                <div class="bento-ann-foot">
                    <img src="{{ $ann->creator->avatarUrl() }}" class="bento-ann-avatar" alt="{{ $ann->creator->first_name }}" style="object-fit:cover;">
                    <span class="bento-ann-author">{{ $ann->creator->first_name }} · {{ $ann->created_at->diffForHumans() }}</span>
                    @if($ann->expires_at)
                    <span class="bento-ann-expiry"><i class="fas fa-clock"></i> {{ $ann->expires_at->format('M d') }}</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="bento-ann-empty"><i class="fas fa-bullhorn"></i> No announcements yet.</div>
            @endforelse
        </div>

        {{-- Right column --}}
        <div class="bento-right">
        {{-- Quick Access --}}
        <div class="bento-quick">
            <div class="bento-quick-hd"><i class="fas fa-bolt" style="color:var(--primary);font-size:0.65rem;"></i> Quick Access</div>
            <div class="bento-ql-grid">
            @if($effectiveRole === 'content')
            <a href="{{ route('posting-procedure') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#0ea5e9;"><i class="fas fa-book-open"></i></div>
                <span class="bento-ql-name">Posting Procedure</span>
            </a>
            <a href="{{ route('data-gathering') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#8b5cf6;"><i class="fas fa-folder-open"></i></div>
                <span class="bento-ql-name">Data Gathering</span>
            </a>
            <a href="{{ route('price-calculator') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#10b981;"><i class="fas fa-calculator"></i></div>
                <span class="bento-ql-name">Price Calculator</span>
            </a>
            <a href="{{ route('important-links') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#f59e0b;"><i class="fas fa-link"></i></div>
                <span class="bento-ql-name">Important Links</span>
            </a>
            @else
            <a href="{{ route('end-of-day') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#6366f1;"><i class="fas fa-calendar-check"></i></div>
                <span class="bento-ql-name">End-of-Day Report</span>
            </a>
            <a href="{{ route('price-calculator') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#10b981;"><i class="fas fa-calculator"></i></div>
                <span class="bento-ql-name">Price Calculator</span>
            </a>
            <a href="{{ route('team') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#0ea5e9;"><i class="fas fa-users"></i></div>
                <span class="bento-ql-name">The Team</span>
            </a>
            <a href="{{ route('important-links') }}" class="bento-ql-box">
                <div class="bento-ql-icon" style="background:#f59e0b;"><i class="fas fa-link"></i></div>
                <span class="bento-ql-name">Important Links</span>
            </a>
            @endif
            </div>
        </div>

        </div>{{-- /.bento-right --}}

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
        @php $tl = \App\Support\TaskLabels::get($effectiveRole); @endphp
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
@if($effectiveRole !== 'analyst')
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
<script>
function toggleMiniSecret(row, e) {
    e.stopPropagation();
    var blur = row.querySelector('.mini-sblur');
    var eye  = row.querySelector('.mini-seye');
    var isRevealing = !blur.classList.contains('revealed');
    blur.classList.toggle('revealed', isRevealing);
    eye.className = isRevealing ? 'fas fa-eye-slash mini-seye' : 'fas fa-eye mini-seye';
}
</script>
@endsection
