@extends('layouts.app')

@section('title', 'The Team — Ecomm Dept')

@section('styles')
<style>
    .team-hero {
        background: var(--white);
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .team-hero .th-icon {
        width: 64px;
        height: 64px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .team-hero h3 {
        font-weight: 800;
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .team-hero p {
        color: var(--gray-500);
        font-weight: 500;
        font-size: 0.9rem;
        margin: 0;
    }

    .team-hero .th-stats {
        display: flex;
        gap: 1rem;
        margin-top: 0.75rem;
    }

    .th-stat {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .th-stat .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    /* Section divider */
    .team-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 2rem 0 1.25rem;
    }

    .team-divider .td-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .team-divider h3 {
        font-weight: 800;
        font-size: 1.1rem;
        margin: 0;
    }

    .team-divider .td-count {
        background: var(--muted);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--gray-400);
    }

    .team-divider .td-line {
        flex: 1;
        height: 2px;
        background: var(--muted);
    }

    /* Content Team */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .member-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    .member-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .member-card:hover {
        transform: scale(1.02);
    }

    .member-card:hover::before {
        opacity: 1;
    }

    .member-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: white;
        flex-shrink: 0;
    }

    .member-info h5 {
        font-weight: 700;
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.2;
    }

    .member-info span {
        font-size: 0.7rem;
        font-weight: 500;
        color: var(--gray-400);
    }

    /* Manager special card */
    .member-card.manager {
        grid-column: span 1;
        background: var(--primary);
        color: white;
    }

    .member-card.manager .member-info h5 { color: white; }
    .member-card.manager .member-info span { color: rgba(255,255,255,0.7); }
    .member-card.manager::before { display: none; }

    /* Lead special card */
    .member-card.lead {
        border: 2px solid var(--secondary);
    }

    .member-card.lead::before {
        background: var(--secondary);
    }

    /* Design Team */
    .design-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .design-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    .design-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: var(--muted);
        border-radius: 0 0 0 50%;
        opacity: 0.5;
    }

    .design-card:hover {
        transform: scale(1.01);
    }

    .design-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .dc-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
    }

    .dc-name {
        font-weight: 800;
        font-size: 1.05rem;
    }

    .dc-role {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--gray-400);
    }

    .prio-section {
        position: relative;
        z-index: 1;
    }

    .prio-label {
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gray-400);
        margin-bottom: 0.5rem;
    }

    .prio-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
    }

    .prio-tag {
        padding: 0.25rem 0.625rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .prio-tag.pt-blue { background: #DBEAFE; color: #2563EB; }
    .prio-tag.pt-green { background: #D1FAE5; color: #059669; }
    .prio-tag.pt-amber { background: #FEF3C7; color: #D97706; }
    .prio-tag.pt-purple { background: #EDE9FE; color: #7C3AED; }
    .prio-tag.pt-red { background: #FEE2E2; color: #DC2626; }

    /* Avatar colors */
    .av-1 { background: var(--primary); }
    .av-2 { background: var(--secondary); }
    .av-3 { background: var(--accent); }
    .av-4 { background: #8B5CF6; }
    .av-5 { background: #EC4899; }
    .av-6 { background: #14B8A6; }
    .av-7 { background: #F97316; }
    .av-8 { background: #06B6D4; }
    .av-9 { background: #84CC16; }

    @media (max-width: 768px) {
        .content-grid { grid-template-columns: 1fr 1fr; }
        .design-grid { grid-template-columns: 1fr; }
        .team-hero { flex-direction: column; text-align: center; }
        .team-hero .th-stats { justify-content: center; }
    }

    @media (max-width: 480px) {
        .content-grid { grid-template-columns: 1fr; }
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
        <li><a href="{{ route('dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
        <li><a href="{{ route('price-calculator') }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
        <li><a href="{{ route('important-links') }}"><i class="fas fa-link"></i> Important Links</a></li>
        <li><a href="{{ route('team') }}" class="active"><i class="fas fa-users"></i> The Team</a></li>
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
            <h2>The <span class="highlight">Team</span></h2>
            <p>Meet the people behind Ecomm Dept</p>
        </div>
    </div>

    <!-- Hero Card -->
    <div class="team-hero anim-up d1">
        <div class="th-icon"><i class="fas fa-users"></i></div>
        <div>
            <h3>Ecomm Department</h3>
            <p>Content and Design teams working together across e-commerce platforms</p>
            <div class="th-stats">
                <div class="th-stat"><div class="dot" style="background: var(--primary);"></div> Content Team — 9</div>
                <div class="th-stat"><div class="dot" style="background: var(--secondary);"></div> Design Team — 4</div>
            </div>
        </div>
    </div>

    <!-- Content Team Divider -->
    <div class="team-divider anim-up d2">
        <div class="td-icon" style="background: var(--primary);"><i class="fas fa-pen-nib"></i></div>
        <h3>Content Team</h3>
        <span class="td-count">9 members</span>
        <div class="td-line"></div>
    </div>

    <!-- Content Team Grid -->
    <div class="content-grid anim-up d2">
        <div class="member-card manager">
            <div class="member-avatar" style="background: rgba(255,255,255,0.2);">KL</div>
            <div class="member-info">
                <h5>Kevin Lim</h5>
                <span>E-Commerce Manager</span>
            </div>
        </div>
        <div class="member-card lead">
            <div class="member-avatar av-2">MG</div>
            <div class="member-info">
                <h5>Milo Gorospe</h5>
                <span>Content / PR Lead</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-3">AC</div>
            <div class="member-info">
                <h5>Angelyn Catolico</h5>
                <span>Content Associate / Backend</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-4">CL</div>
            <div class="member-info">
                <h5>Czein Laruscain</h5>
                <span>Content Associate</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-5">JO</div>
            <div class="member-info">
                <h5>Jamie Ortiz</h5>
                <span>Product Researcher</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-6">WD</div>
            <div class="member-info">
                <h5>Well Dacoco</h5>
                <span>Product Researcher</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-7">ED</div>
            <div class="member-info">
                <h5>Em Delos Santos</h5>
                <span>Content Associate</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-8">ME</div>
            <div class="member-info">
                <h5>Mark Ivan Empleo</h5>
                <span>Content Associate</span>
            </div>
        </div>
        <div class="member-card">
            <div class="member-avatar av-9">LF</div>
            <div class="member-info">
                <h5>Liam Flores</h5>
                <span>Content Associate</span>
            </div>
        </div>
    </div>

    <!-- Design Team Divider -->
    <div class="team-divider anim-up d3">
        <div class="td-icon" style="background: var(--secondary);"><i class="fas fa-palette"></i></div>
        <h3>Design Team</h3>
        <span class="td-count">4 members</span>
        <div class="td-line"></div>
    </div>

    <!-- Design Team Grid -->
    <div class="design-grid anim-up d3">
        <div class="design-card">
            <div class="design-card-header">
                <div class="dc-avatar av-5">F</div>
                <div>
                    <div class="dc-name">Fern</div>
                    <div class="dc-role">Graphic Designer</div>
                </div>
            </div>
            <div class="prio-section">
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-blue">Ecom</span>
                    <span class="prio-tag pt-green">Internal</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                </div>
            </div>
        </div>

        <div class="design-card">
            <div class="design-card-header">
                <div class="dc-avatar av-4">T</div>
                <div>
                    <div class="dc-name">Tim</div>
                    <div class="dc-role">Graphic Tantalizer</div>
                </div>
            </div>
            <div class="prio-section">
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-amber">Events</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                    <span class="prio-tag pt-blue">Ecom</span>
                </div>
            </div>
        </div>

        <div class="design-card">
            <div class="design-card-header">
                <div class="dc-avatar av-1">A</div>
                <div>
                    <div class="dc-name">Angelo</div>
                    <div class="dc-role">Graphic Designer</div>
                </div>
            </div>
            <div class="prio-section">
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-blue">Ecom</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                    <span class="prio-tag pt-red">Retail</span>
                </div>
            </div>
        </div>

        <div class="design-card">
            <div class="design-card-header">
                <div class="dc-avatar av-6">L</div>
                <div>
                    <div class="dc-name">Latrell</div>
                    <div class="dc-role">Graphic Designer</div>
                </div>
            </div>
            <div class="prio-section">
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-blue">Ecom</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                    <span class="prio-tag pt-amber">Events</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
