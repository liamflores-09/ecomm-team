@extends('layouts.app')

@section('title', 'The Team — Ecomm Dept')

@section('styles')
<style>
    .team-section {
        margin-bottom: 2rem;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin-bottom: 1.25rem;
    }

    .section-title .st-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
    }

    .section-title h3 {
        font-weight: 800;
        font-size: 1.1rem;
        margin: 0;
    }

    .section-title .st-count {
        background: var(--muted);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--gray-400);
        margin-left: 0.25rem;
    }

    /* Content Team Grid */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .member-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s;
    }

    .member-card:hover {
        transform: scale(1.02);
    }

    .member-avatar {
        width: 40px;
        height: 40px;
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
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.2;
    }

    .member-info span {
        font-size: 0.7rem;
        font-weight: 500;
        color: var(--gray-400);
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
    }

    .design-card:hover {
        transform: scale(1.01);
    }

    .design-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--muted);
    }

    .design-card-header .dc-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        color: white;
        flex-shrink: 0;
    }

    .design-card-header .dc-name {
        font-weight: 800;
        font-size: 1rem;
    }

    .design-card-header .dc-role {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--gray-400);
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
        .design-grid { grid-template-columns: 1fr; }
        .content-grid { grid-template-columns: 1fr 1fr; }
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

    <div class="top-bar anim-up" style="margin-bottom: 2rem;">
        <div>
            <h2>The <span class="highlight">Team</span></h2>
            <p>Meet the people behind Ecomm Dept</p>
        </div>
    </div>

    <!-- Content Team -->
    <div class="team-section anim-up d1">
        <div class="section-title">
            <div class="st-icon" style="background: var(--primary);"><i class="fas fa-pen-nib"></i></div>
            <h3>Content Team</h3>
            <span class="st-count">9 members</span>
        </div>
        <div class="content-grid">
            <div class="member-card">
                <div class="member-avatar av-1">KL</div>
                <div class="member-info">
                    <h5>Kevin Lim</h5>
                    <span>E-Commerce Manager</span>
                </div>
            </div>
            <div class="member-card">
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
    </div>

    <!-- Design Team -->
    <div class="team-section anim-up d2">
        <div class="section-title">
            <div class="st-icon" style="background: var(--secondary);"><i class="fas fa-palette"></i></div>
            <h3>Design Team</h3>
            <span class="st-count">4 members</span>
        </div>
        <div class="design-grid">
            <!-- Fern -->
            <div class="design-card">
                <div class="design-card-header">
                    <div class="dc-avatar av-5">F</div>
                    <div>
                        <div class="dc-name">Fern</div>
                        <div class="dc-role">Graphic Designer</div>
                    </div>
                </div>
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-blue">Ecom</span>
                    <span class="prio-tag pt-green">Internal</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                </div>
            </div>

            <!-- Tim -->
            <div class="design-card">
                <div class="design-card-header">
                    <div class="dc-avatar av-4">T</div>
                    <div>
                        <div class="dc-name">Tim</div>
                        <div class="dc-role">Graphic Tantalizer</div>
                    </div>
                </div>
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-amber">Events</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                    <span class="prio-tag pt-blue">Ecom</span>
                </div>
            </div>

            <!-- Angelo -->
            <div class="design-card">
                <div class="design-card-header">
                    <div class="dc-avatar av-1">A</div>
                    <div>
                        <div class="dc-name">Angelo</div>
                        <div class="dc-role">Graphic Designer</div>
                    </div>
                </div>
                <div class="prio-label">Priorities</div>
                <div class="prio-tags">
                    <span class="prio-tag pt-blue">Ecom</span>
                    <span class="prio-tag pt-purple">Marketing</span>
                    <span class="prio-tag pt-red">Retail</span>
                </div>
            </div>

            <!-- Latrell -->
            <div class="design-card">
                <div class="design-card-header">
                    <div class="dc-avatar av-6">L</div>
                    <div>
                        <div class="dc-name">Latrell</div>
                        <div class="dc-role">Graphic Designer</div>
                    </div>
                </div>
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
