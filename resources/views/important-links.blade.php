@extends('layouts.app')

@section('title', 'Important Links — EC Training Hub')

@section('styles')
<style>
    .links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .link-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .link-card:hover {
        transform: scale(1.02);
    }

    .link-card .lc-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        margin-bottom: 1rem;
        transition: transform 0.2s;
    }

    .link-card:hover .lc-icon {
        transform: scale(1.1);
    }

    .lc-icon.ic-blue { background: var(--primary); }
    .lc-icon.ic-green { background: var(--secondary); }
    .lc-icon.ic-amber { background: var(--accent); }
    .lc-icon.ic-dark { background: var(--fg); }

    .link-card h5 {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.375rem;
    }

    .link-card p {
        color: var(--gray-500);
        font-size: 0.8rem;
        font-weight: 500;
        margin: 0;
        line-height: 1.5;
    }

    .link-card .lc-arrow {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 28px;
        height: 28px;
        background: var(--muted);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-400);
        font-size: 0.7rem;
        transition: all 0.2s;
    }

    .link-card:hover .lc-arrow {
        background: var(--primary);
        color: white;
    }

    @media (max-width: 768px) {
        .links-grid { grid-template-columns: 1fr; }
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
            <p>Quick access to tools and platforms</p>
        </div>
    </div>

    <div class="links-grid anim-up d1">
        <a href="https://app.selluseller.com" target="_blank" class="link-card">
            <div class="lc-icon ic-blue"><i class="fas fa-store"></i></div>
            <h5>SelluSeller</h5>
            <p>Product catalog management and sync across platforms</p>
            <div class="lc-arrow"><i class="fas fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="#" class="link-card">
            <div class="lc-icon ic-green"><i class="fas fa-database"></i></div>
            <h5>inFlow</h5>
            <p>Inventory and SKU management system</p>
            <div class="lc-arrow"><i class="fas fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="#" class="link-card">
            <div class="lc-icon ic-amber"><i class="fas fa-sheet-table"></i></div>
            <h5>Link Sheet</h5>
            <p>SKU tracking and listing URLs</p>
            <div class="lc-arrow"><i class="fas fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="#" class="link-card">
            <div class="lc-icon ic-dark"><i class="fas fa-file-lines"></i></div>
            <h5>PR Files</h5>
            <p>Product research documents and specifications</p>
            <div class="lc-arrow"><i class="fas fa-arrow-up-right-from-square"></i></div>
        </a>
    </div>
</div>
@endsection
