@extends('layouts.app')

@section('title', 'Important Links — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71'/><path d='M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71'/></svg>">
@endsection

@section('styles')
<style>
    .links-layout {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 0;
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        min-height: 400px;
    }

    /* Category Tabs */
    .cat-tabs {
        background: var(--muted);
        border-right: 2px solid var(--border);
        padding: 0.5rem;
    }

    .cat-tab {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.75rem 0.875rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
        margin-bottom: 0.25rem;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        font-family: var(--p-font-family-sans);
    }

    .cat-tab:hover {
        background: var(--white);
    }

    .cat-tab.active {
        background: var(--white);
        border: 1px solid var(--border-light, var(--border));
    }

    .cat-tab .ct-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: white;
        flex-shrink: 0;
        transition: transform 0.2s;
    }

    .cat-tab:hover .ct-icon,
    .cat-tab.active .ct-icon {
        transform: scale(1.1);
    }

    .ct-icon.ci-blue { background: var(--primary); }
    .ct-icon.ci-green { background: var(--secondary); }
    .ct-icon.ci-amber { background: var(--accent); }
    .ct-icon.ci-dark { background: var(--fg); }

    .cat-tab .ct-text {
        flex: 1;
        min-width: 0;
    }

    .cat-tab .ct-name {
        display: block;
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--fg);
        line-height: 1.2;
    }

    .cat-tab .ct-count {
        display: block;
        font-size: 0.65rem;
        font-weight: 500;
        color: var(--gray-400);
        margin-top: 0.125rem;
    }

    /* Link Content */
    .link-content {
        padding: 1.5rem;
    }

    .link-content-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--muted);
    }

    .link-content-header .lch-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .link-content-header h3 {
        font-weight: 800;
        font-size: 1.1rem;
        margin: 0;
    }

    .link-content-header span {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--gray-400);
    }

    .link-rows {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .link-row {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        transition: all 0.15s;
        text-decoration: none;
        color: var(--fg);
        border: 2px solid transparent;
    }

    .link-row:hover {
        background: var(--muted);
        border-color: var(--border);
    }

    .link-row .lr-num {
        width: 28px;
        height: 28px;
        background: var(--muted);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--gray-400);
        flex-shrink: 0;
        transition: all 0.15s;
    }

    .link-row:hover .lr-num {
        background: var(--primary);
        color: white;
    }

    .link-row .lr-info {
        flex: 1;
        min-width: 0;
    }

    .link-row .lr-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.125rem;
    }

    .link-row .lr-desc {
        font-size: 0.75rem;
        color: var(--gray-400);
        font-weight: 500;
    }

    .link-row .lr-arrow {
        color: var(--gray-300);
        font-size: 0.75rem;
        transition: all 0.15s;
    }

    .link-row:hover .lr-arrow {
        color: var(--primary);
        transform: translateX(4px);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--gray-300);
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    /* Panel visibility */
    .link-panel { display: none; }
    .link-panel.active { display: block; animation: fadeIn 0.2s ease-out; }

    /* Responsive */
    @media (max-width: 768px) {
        .links-layout {
            grid-template-columns: 1fr;
        }

        .cat-tabs {
            border-right: none;
            border-bottom: 2px solid var(--border);
            display: flex;
            overflow-x: auto;
            padding: 0.5rem;
            gap: 0.25rem;
        }

        .cat-tab {
            flex-direction: column;
            text-align: center;
            padding: 0.5rem 0.75rem;
            min-width: 80px;
            margin-bottom: 0;
        }

        .cat-tab .ct-count { display: none; }

        .link-content { padding: 1rem; }
    }

    @media (max-width: 480px) {
        .cat-tab { min-width: 60px; padding: 0.375rem 0.5rem; font-size: 0.7rem; }
        .link-row { padding: 0.625rem 0.75rem; }
        .link-row .lr-name { font-size: 0.8rem; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="important-links" />

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Important <span class="highlight">Links</span></h2>
            <p>Quick access to essential resources and tracking sheets</p>
        </div>
    </div>

    <div class="links-layout anim-up d1">
        <!-- Category Tabs -->
        <div class="cat-tabs">
            <button class="cat-tab active" onclick="switchTab('skus', this)">
                <div class="ct-icon ci-blue"><i class="fas fa-box"></i></div>
                <div class="ct-text">
                    <span class="ct-name">Posted SKUs</span>
                    <span class="ct-count">2 sheets</span>
                </div>
            </button>
            <button class="cat-tab" onclick="switchTab('reports', this)">
                <div class="ct-icon ci-green"><i class="fas fa-chart-simple"></i></div>
                <div class="ct-text">
                    <span class="ct-name">Reports</span>
                    <span class="ct-count">4 sheets</span>
                </div>
            </button>
            <button class="cat-tab" onclick="switchTab('dirs', this)">
                <div class="ct-icon ci-amber"><i class="fas fa-folder-open"></i></div>
                <div class="ct-text">
                    <span class="ct-name">Directories</span>
                    <span class="ct-count">3 sheets</span>
                </div>
            </button>
            <button class="cat-tab" onclick="switchTab('training', this)">
                <div class="ct-icon ci-dark"><i class="fas fa-graduation-cap"></i></div>
                <div class="ct-text">
                    <span class="ct-name">Training</span>
                    <span class="ct-count">1 folder</span>
                </div>
            </button>
        </div>

        <!-- Link Panels -->
        <div class="link-content">

            <!-- Posted SKUs -->
            <div class="link-panel active" id="panel-skus">
                <div class="link-content-header">
                    <div class="lch-icon" style="background: var(--primary);"><i class="fas fa-box"></i></div>
                    <div>
                        <h3>Posted SKUs Tracking</h3>
                        <span>2 resources</span>
                    </div>
                </div>
                <div class="link-rows">
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">01</div>
                        <div class="lr-info">
                            <div class="lr-name">Content x PR Posted SKUs 2026</div>
                            <div class="lr-desc">Google Sheet</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">02</div>
                        <div class="lr-info">
                            <div class="lr-name">Content x PR Posted SKUs 2025</div>
                            <div class="lr-desc">Google Sheet</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>
            </div>

            <!-- Reports -->
            <div class="link-panel" id="panel-reports">
                <div class="link-content-header">
                    <div class="lch-icon" style="background: var(--secondary);"><i class="fas fa-chart-simple"></i></div>
                    <div>
                        <h3>Reports & Tracking</h3>
                        <span>4 resources</span>
                    </div>
                </div>
                <div class="link-rows">
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">01</div>
                        <div class="lr-info">
                            <div class="lr-name">Content x GA Dept Report 2026 V2</div>
                            <div class="lr-desc">Google Sheet</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">02</div>
                        <div class="lr-info">
                            <div class="lr-name">JG QC Tracker</div>
                            <div class="lr-desc">Google Sheet</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">03</div>
                        <div class="lr-info">
                            <div class="lr-name">Operation x Content Inactive Monitoring</div>
                            <div class="lr-desc">Google Sheet</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">04</div>
                        <div class="lr-info">
                            <div class="lr-name">JG Ecom CP Tracker</div>
                            <div class="lr-desc">Google Sheet</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>
            </div>

            <!-- Directories -->
            <div class="link-panel" id="panel-dirs">
                <div class="link-content-header">
                    <div class="lch-icon" style="background: var(--accent);"><i class="fas fa-folder-open"></i></div>
                    <div>
                        <h3>Directories & Master Files</h3>
                        <span>3 resources</span>
                    </div>
                </div>
                <div class="link-rows">
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">01</div>
                        <div class="lr-info">
                            <div class="lr-name">JG SUPERSTORE ECOMMERCE DIRECTORY</div>
                            <div class="lr-desc">Folder</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">02</div>
                        <div class="lr-info">
                            <div class="lr-name">Change SKU Tracker</div>
                            <div class="lr-desc">Folder</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">03</div>
                        <div class="lr-info">
                            <div class="lr-name">Freebie & Update CVP Monitoring V2 2026</div>
                            <div class="lr-desc">Folder</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>
            </div>

            <!-- Training -->
            <div class="link-panel" id="panel-training">
                <div class="link-content-header">
                    <div class="lch-icon" style="background: var(--fg);"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <h3>Training Resources</h3>
                        <span>1 resource</span>
                    </div>
                </div>
                <div class="link-rows">
                    <a href="#" target="_blank" class="link-row">
                        <div class="lr-num">01</div>
                        <div class="lr-info">
                            <div class="lr-name">Content Associate Training Files</div>
                            <div class="lr-desc">Folder</div>
                        </div>
                        <div class="lr-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(id, btn) {
    document.querySelectorAll('.cat-tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.link-panel').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('panel-' + id).classList.add('active');
}
</script>
@endsection
