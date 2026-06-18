@extends('layouts.app')

@section('title', 'Dashboard — Ecomm Dept')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><rect x='3' y='14' width='7' height='7'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        background: var(--primary);
        border-radius: 8px;
        padding: 2rem 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 0;
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

    /* Section Divider */
    .section-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.75rem 0 1rem;
    }

    .section-divider .sd-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .section-divider h4 {
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0;
    }

    .section-divider .sd-line {
        flex: 1;
        height: 2px;
        background: var(--muted);
    }

    /* Quick Access */
    .quick-section {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
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

    .ql-text { flex: 1; }

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

    .quick-link:hover .ql-arrow { color: white; }

    /* Quick Reference */
    .ref-section {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
    }

    .ref-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }

    .ref-card {
        background: var(--muted);
        border-radius: 6px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
    }

    .ref-card:hover {
        transform: scale(1.03);
    }

    .ref-card .rc-icon {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-size: 0.9rem;
        color: white;
    }

    .ref-card h5 {
        font-weight: 700;
        font-size: 0.8rem;
        margin: 0;
    }

    /* Team Preview */
    .team-preview {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
    }

    .team-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .team-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-left: -8px;
        border: 2px solid var(--white);
    }

    .team-avatar:first-child { margin-left: 0; }

    .team-more {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.65rem;
        color: var(--gray-500);
        margin-left: -8px;
        border: 2px solid var(--white);
    }

    @media (max-width: 768px) {
        .quick-links { grid-template-columns: 1fr; }
        .ref-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">ED</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>PR x Content</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('dashboard') }}" class="active"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
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
    <div class="top-bar anim-up">
        <div>
            <h2>Dashboard</h2>
            <p>Overview of your training system</p>
        </div>
        <div class="user-badge">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $user->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="avatar" alt="{{ $user->username }}" style="width: 36px; height: 36px; border-radius: 50%;">
            <div class="user-info">
                <span class="user-name">{{ $user->username }}</span>
                <span class="role-tag">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up d1">
        <h2>Welcome back, {{ $user->username }}!</h2>
        <p>You're all caught up. Pick up where you left off or explore a new module.</p>
    </div>

    <!-- Divider: Quick Access -->
    <div class="section-divider anim-up d2">
        <div class="sd-icon" style="background: var(--accent);"><i class="fas fa-bolt"></i></div>
        <h4>Quick Access</h4>
        <div class="sd-line"></div>
    </div>

    <!-- Quick Access Links -->
    <div class="quick-section anim-up d2">
        <div class="quick-links">
            <a href="{{ route('posting-procedure') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-book-open"></i></div>
                <div class="ql-text">
                    <strong>Posting Procedure</strong>
                    <small>8-step guide for product posting</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('data-gathering') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-folder-open"></i></div>
                <div class="ql-text">
                    <strong>Data Gathering</strong>
                    <small>Collect product info and assets</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('ecommerce-requirements') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="ql-text">
                    <strong>E-commerce Requirements</strong>
                    <small>Platform-specific posting rules</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text">
                    <strong>Price Calculator</strong>
                    <small>Compute SRP across platforms</small>
                </div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>
    </div>

    <!-- Divider: Quick Reference -->
    <div class="section-divider anim-up d3">
        <div class="sd-icon" style="background: var(--secondary);"><i class="fas fa-star"></i></div>
        <h4>Quick Reference</h4>
        <div class="sd-line"></div>
    </div>

    <!-- Reference Cards -->
    <div class="ref-section anim-up d3">
        <div class="ref-grid">
            <a href="{{ route('end-of-day') }}" class="ref-card" style="text-decoration: none; color: var(--fg);">
                <div class="rc-icon" style="background: var(--primary);"><i class="fas fa-calendar-check"></i></div>
                <h5>EOD Report</h5>
            </a>
            <a href="{{ route('important-links') }}" class="ref-card" style="text-decoration: none; color: var(--fg);">
                <div class="rc-icon" style="background: var(--secondary);"><i class="fas fa-link"></i></div>
                <h5>Important Links</h5>
            </a>
            <a href="{{ route('team') }}" class="ref-card" style="text-decoration: none; color: var(--fg);">
                <div class="rc-icon" style="background: var(--accent);"><i class="fas fa-users"></i></div>
                <h5>The Team</h5>
            </a>
            <div class="ref-card">
                <div class="rc-icon" style="background: var(--gray-300);"><i class="fas fa-chart-line"></i></div>
                <h5 style="color: var(--gray-400);">Progress</h5>
            </div>
        </div>
    </div>

    <!-- Divider: Team -->
    <div class="section-divider anim-up d4">
        <div class="sd-icon" style="background: #8B5CF6;"><i class="fas fa-users"></i></div>
        <h4>Team</h4>
        <div class="sd-line"></div>
    </div>

    <!-- Team Preview -->
    <div class="team-preview anim-up d4">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div class="team-row">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=KevinLim&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="KL">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=MiloGorospe&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="MG">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=AngelynCatolico&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="AC">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=CzeinLaruscain&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="CL">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=JamieOrtiz&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="JO">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=WellDacoco&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="WD">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=EmDelosSantos&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="ED">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=MarkIvanEmpleo&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="ME">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=LiamFlores&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="team-avatar" alt="LF">
                <div class="team-more">+4</div>
            </div>
            <a href="{{ route('team') }}" class="btn-flat-secondary" style="height: 36px; padding: 0 0.75rem; font-size: 0.8rem; text-decoration: none;">
                View All <i class="fas fa-arrow-right" style="margin-left: 0.25rem;"></i>
            </a>
        </div>
    </div>
</div>
@endsection
