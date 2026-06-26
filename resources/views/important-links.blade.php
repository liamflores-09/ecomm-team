@extends('layouts.app')

@section('title', 'Important Links — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71'/><path d='M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Tabs ─────────────────────────────────────────────────── */
    .il-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .il-tab {
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
    .il-tab:hover { border-color: var(--foreground); }
    .il-tab.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    .il-tab-count {
        font-size: 0.7rem;
        font-weight: 700;
        opacity: 0.75;
    }

    /* ── Section headers ──────────────────────────────────────── */
    .il-hd {
        display: flex;
        align-items: baseline;
        gap: 0.625rem;
        margin-bottom: 1rem;
        padding-bottom: 0.625rem;
        border-bottom: 1px solid var(--border-light);
    }
    .il-hd-name {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--foreground);
    }
    .il-hd-count {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--muted-foreground);
    }

    /* ── Link grid ────────────────────────────────────────────── */
    .il-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    /* ── Link cards ───────────────────────────────────────────── */
    .il-card {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: 8px;
        padding: 1.25rem;
        text-decoration: none;
        color: var(--foreground);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: border-color 0.2s;
    }
    .il-card:hover { border-color: var(--foreground); }
    .il-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.25rem;
    }
    .il-card-icon {
        width: 36px;
        height: 36px;
        background: var(--muted);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted-foreground);
        font-size: 0.875rem;
        flex-shrink: 0;
        transition: background 0.2s, color 0.2s;
    }
    .il-card:hover .il-card-icon {
        background: var(--primary);
        color: white;
    }
    .il-card-ext {
        font-size: 0.65rem;
        color: var(--border);
        transition: color 0.2s;
    }
    .il-card:hover .il-card-ext { color: var(--muted-foreground); }
    .il-card-name {
        font-weight: 700;
        font-size: 0.875rem;
        line-height: 1.35;
        color: var(--foreground);
    }
    .il-card-type {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--muted-foreground);
    }

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 768px) {
        .il-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .il-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="important-links" />

<div class="main-content">

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Important <span class="highlight">Links</span></h2>
            <p>Quick access to essential resources and tracking sheets</p>
        </div>
    </div>

    <div class="il-tabs anim-up d1">
        <button class="il-tab active" data-filter="all">All <span class="il-tab-count">10</span></button>
        <button class="il-tab" data-filter="skus">Posted SKUs <span class="il-tab-count">2</span></button>
        <button class="il-tab" data-filter="reports">Reports <span class="il-tab-count">4</span></button>
        <button class="il-tab" data-filter="dirs">Directories <span class="il-tab-count">3</span></button>
        <button class="il-tab" data-filter="training">Training <span class="il-tab-count">1</span></button>
    </div>

    <!-- Posted SKUs -->
    <div class="il-section" data-category="skus">
        <div class="il-hd">
            <span class="il-hd-name">Posted SKUs</span>
            <span class="il-hd-count">2 links</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content x PR Posted SKUs 2026</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content x PR Posted SKUs 2025</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
        </div>
    </div>

    <!-- Reports -->
    <div class="il-section" data-category="reports">
        <div class="il-hd">
            <span class="il-hd-name">Reports & Tracking</span>
            <span class="il-hd-count">4 links</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content x GA Dept Report 2026 V2</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">JG QC Tracker</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Operation x Content Inactive Monitoring</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">JG Ecom CP Tracker</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
        </div>
    </div>

    <!-- Directories -->
    <div class="il-section" data-category="dirs">
        <div class="il-hd">
            <span class="il-hd-name">Directories & Master Files</span>
            <span class="il-hd-count">3 links</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">JG SUPERSTORE ECOMMERCE DIRECTORY</div>
                <div class="il-card-type">Folder</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Change SKU Tracker</div>
                <div class="il-card-type">Folder</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Freebie & Update CVP Monitoring V2 2026</div>
                <div class="il-card-type">Folder</div>
            </a>
        </div>
    </div>

    <!-- Training -->
    <div class="il-section" data-category="training">
        <div class="il-hd">
            <span class="il-hd-name">Training Resources</span>
            <span class="il-hd-count">1 link</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content Associate Training Files</div>
                <div class="il-card-type">Folder</div>
            </a>
        </div>
    </div>

    <script>
    (function () {
        var tabs = document.querySelectorAll('.il-tab');
        var sections = document.querySelectorAll('.il-section');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');
                var filter = tab.dataset.filter;
                sections.forEach(function (s) {
                    s.style.display = (filter === 'all' || s.dataset.category === filter) ? '' : 'none';
                });
            });
        });
    }());
    </script>
</div>
@endsection
