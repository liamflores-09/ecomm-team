@extends('layouts.app')

@section('title', 'Data Gathering — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z'/></svg>">
@endsection

@section('styles')
<style>
    .placeholder-card {
        background: var(--white);
        border-radius: 8px;
        padding: 3rem 2rem;
        text-align: center;
    }

    .placeholder-card .icon {
        width: 64px;
        height: 64px;
        background: var(--muted);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 1.5rem;
        color: var(--gray-300);
    }

    .placeholder-card h4 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .placeholder-card p {
        color: var(--gray-500);
        font-weight: 500;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">EC</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>PR x Content</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        @if(Auth::user()->role === 'content')
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}" class="active"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
        @endif
        <li><a href="{{ route('price-calculator') }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
        <li><a href="{{ route('important-links') }}"><i class="fas fa-link"></i> Important Links</a></li>
        <li><a href="{{ route('team') }}"><i class="fas fa-users"></i> The Team</a></li>
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

    <div class="top-bar anim-up" style="margin-bottom: 2.5rem;">
        <div>
            <h2>Data Gathering</h2>
            <p>Collect product information and assets for posting</p>
        </div>
    </div>

    <div class="placeholder-card anim-up d1">
        <div class="icon"><i class="fas fa-folder-open"></i></div>
        <h4>Data Gathering Module</h4>
        <p>This section is coming soon. Content will be added here.</p>
    </div>
</div>
@endsection
