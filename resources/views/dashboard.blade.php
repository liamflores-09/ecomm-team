@extends('layouts.app')

@section('title', 'Dashboard — EC Training Hub')

@section('styles')
<style>
    .welcome-banner {
        background: var(--primary);
        border-radius: 8px;
        padding: 2rem 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        bottom: -40px;
        right: 80px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .welcome-banner h2 {
        color: white;
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        position: relative;
        z-index: 1;
    }

    .welcome-banner p {
        color: rgba(255,255,255,0.75);
        font-weight: 500;
        font-size: 0.9rem;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.s-blue { background: var(--primary); }
    .stat-icon.s-green { background: var(--secondary); }
    .stat-icon.s-amber { background: var(--accent); }

    .stat-body h4 {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.125rem;
    }

    .stat-body span {
        color: var(--gray-500);
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .quick-section {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
    }

    .quick-section h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .quick-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        background: var(--muted);
        border-radius: 6px;
        text-decoration: none;
        color: var(--fg);
        transition: all 0.2s;
    }

    .quick-link:hover {
        background: var(--primary);
        color: white;
        transform: scale(1.02);
    }

    .quick-link:hover .ql-icon {
        background: rgba(255,255,255,0.15);
        color: white;
    }

    .ql-icon {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        color: var(--primary);
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .ql-text {
        flex: 1;
    }

    .ql-text strong {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .ql-text small {
        color: var(--gray-500);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .quick-link:hover .ql-text small {
        color: rgba(255,255,255,0.75);
    }

    .ql-arrow {
        color: var(--gray-300);
        font-size: 0.8rem;
        transition: all 0.2s;
    }

    .quick-link:hover .ql-arrow {
        color: white;
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
        <li><a href="{{ route('dashboard') }}" class="active"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
    </ul>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="top-bar anim-up">
        <div>
            <h2>Dashboard</h2>
            <p>Overview of your training system</p>
        </div>
        <div class="user-badge">
            <div class="avatar">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ $user->username }}</span>
                <span class="role-tag">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
    </div>

    <div class="welcome-banner anim-up d1">
        <h2>Welcome back, {{ $user->username }}!</h2>
        <p>You're all caught up. Pick up where you left off or explore a new module.</p>
    </div>

    <div class="quick-section anim-up d2">
        <h4><i class="fas fa-bolt" style="color: var(--accent); margin-right: 0.375rem;"></i> Quick Access</h4>
        <div class="quick-links">
            <a href="{{ route('posting-procedure') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-book-open"></i></div>
                <div class="ql-text">
                    <strong>Posting Procedure</strong>
                    <small>8-step guide for product posting</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <div class="quick-link" style="opacity: 0.45; cursor: not-allowed;">
                <div class="ql-icon" style="background: var(--gray-200); color: var(--gray-500);"><i class="fas fa-chart-line"></i></div>
                <div class="ql-text">
                    <strong>My Progress</strong>
                    <small>Coming soon</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-lock"></i></div>
            </div>
            <div class="quick-link" style="opacity: 0.45; cursor: not-allowed;">
                <div class="ql-icon" style="background: var(--gray-200); color: var(--gray-500);"><i class="fas fa-clipboard-list"></i></div>
                <div class="ql-text">
                    <strong>Quick Reference</strong>
                    <small>Coming soon</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-lock"></i></div>
            </div>
            <div class="quick-link" style="opacity: 0.45; cursor: not-allowed;">
                <div class="ql-icon" style="background: var(--gray-200); color: var(--gray-500);"><i class="fas fa-image"></i></div>
                <div class="ql-text">
                    <strong>Image Guide</strong>
                    <small>Coming soon</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-lock"></i></div>
            </div>
        </div>
    </div>
</div>
@endsection
