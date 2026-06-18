@extends('layouts.app')

@section('title', 'Important Links — EC Training Hub')

@section('styles')
<style>
    .links-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .cat-card {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
    }

    .cat-header {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1.25rem;
        background: var(--muted);
        border-bottom: 2px solid var(--border);
    }

    .cat-icon {
        width: 32px;
        height: 32px;
        background: var(--fg);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .cat-title {
        font-weight: 800;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--fg);
    }

    .cat-badge {
        margin-left: auto;
        background: var(--fg);
        color: white;
        padding: 0.15rem 0.5rem;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-radius: 4px;
    }

    .links-list {
        padding: 0;
    }

    .link-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--muted);
        transition: all 0.15s;
    }

    .link-item:last-child {
        border-bottom: none;
    }

    .link-item:hover {
        background: var(--muted);
        padding-left: 1.5rem;
    }

    .link-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }

    .link-icon {
        width: 32px;
        height: 32px;
        background: var(--muted);
        border: 2px solid var(--border);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .link-name a {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--fg);
        text-decoration: none;
        transition: color 0.15s;
    }

    .link-name a:hover {
        color: var(--primary);
    }

    .link-arrow {
        color: var(--gray-300);
        font-size: 0.7rem;
        transition: all 0.2s;
    }

    .link-item:hover .link-arrow {
        color: var(--primary);
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        .cat-header { padding: 0.75rem 1rem; }
        .link-item { padding: 0.625rem 1rem; }
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">EC</div>
        <div>
            <h5>EC Training</h5>
            <span>PR x Content</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
        <li><a href="{{ route('price-calculator') }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
        <li><a href="{{ route('important-links') }}" class="active"><i class="fas fa-link"></i> Important Links</a></li>
    </ul>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</div>

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Important <span class="highlight">Links</span></h2>
            <p>Quick access to essential resources and tracking sheets</p>
        </div>
    </div>

    <div class="links-container">

        <!-- Posted SKUs Tracking -->
        <div class="cat-card anim-up d1">
            <div class="cat-header">
                <div class="cat-icon"><i class="fas fa-box"></i></div>
                <div class="cat-title">Posted SKUs Tracking</div>
                <span class="cat-badge">2 sheets</span>
            </div>
            <div class="links-list">
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-file-excel"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Content x PR Posted SKUs 2026</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-file-excel"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Content x PR Posted SKUs 2025</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Reports & Tracking -->
        <div class="cat-card anim-up d2">
            <div class="cat-header">
                <div class="cat-icon"><i class="fas fa-chart-simple"></i></div>
                <div class="cat-title">Reports & Tracking</div>
                <span class="cat-badge">4 sheets</span>
            </div>
            <div class="links-list">
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-file-excel"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Content x GA Dept Report 2026 V2</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-file-excel"></i></div>
                        <div class="link-name"><a href="#" target="_blank">JG QC Tracker</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-file-excel"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Operation x Content Inactive Monitoring</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-file-excel"></i></div>
                        <div class="link-name"><a href="#" target="_blank">JG Ecom CP Tracker</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Directories & Master Files -->
        <div class="cat-card anim-up d3">
            <div class="cat-header">
                <div class="cat-icon"><i class="fas fa-folder-open"></i></div>
                <div class="cat-title">Directories & Master Files</div>
                <span class="cat-badge">3 sheets</span>
            </div>
            <div class="links-list">
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-folder"></i></div>
                        <div class="link-name"><a href="#" target="_blank">JG SUPERSTORE ECOMMERCE DIRECTORY</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-folder"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Change SKU Tracker</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-folder"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Freebie & Update CVP Monitoring V2 2026</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Training Resources -->
        <div class="cat-card anim-up d4">
            <div class="cat-header">
                <div class="cat-icon" style="background: var(--primary);"><i class="fas fa-graduation-cap"></i></div>
                <div class="cat-title">Training Resources</div>
                <span class="cat-badge">1 folder</span>
            </div>
            <div class="links-list">
                <div class="link-item">
                    <div class="link-info">
                        <div class="link-icon"><i class="fas fa-folder-open"></i></div>
                        <div class="link-name"><a href="#" target="_blank">Content Associate Training Files</a></div>
                    </div>
                    <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
