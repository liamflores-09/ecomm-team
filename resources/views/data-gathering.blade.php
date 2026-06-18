@extends('layouts.app')

@section('title', 'Data Gathering — EC Training Hub')

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
            <h5>EC Training</h5>
            <span>PR x Content</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}" class="active"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
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
