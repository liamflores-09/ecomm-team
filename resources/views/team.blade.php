@extends('layouts.app')

@section('title', 'The Team — Ecomm Dept')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 00-3-3.87'/><path d='M16 3.13a4 4 0 010 7.75'/></svg>">
@endsection

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

    .team-hero h3 { font-weight: 800; font-size: 1.25rem; margin-bottom: 0.25rem; }
    .team-hero p { color: var(--gray-500); font-weight: 500; font-size: 0.9rem; margin: 0; }

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

    .th-stat .dot { width: 8px; height: 8px; border-radius: 50%; }

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

    .team-divider h3 { font-weight: 800; font-size: 1.1rem; margin: 0; }

    .team-divider .td-count {
        background: var(--muted);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--gray-400);
    }

    .team-divider .td-line { flex: 1; height: 2px; background: var(--muted); }

    /* Leadership cards */
    .leader-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .leader-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    .leader-card:hover { transform: scale(1.01); }

    .leader-card.manager {
        background: var(--primary);
    }

    .leader-card.manager * { color: white !important; }
    .leader-card.manager .lc-role { color: rgba(255,255,255,0.75) !important; }
    .leader-card.manager .lc-badge { background: rgba(255,255,255,0.2); color: white !important; }

    .leader-card.lead {
        border: 2px solid var(--secondary);
    }

    .leader-card.lead .lc-badge { background: #D1FAE5; color: #059669; }

    .lc-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 3px solid var(--muted);
        object-fit: cover;
    }

    .leader-card.manager .lc-avatar { border-color: rgba(255,255,255,0.3); }

    .lc-info { flex: 1; }

    .lc-name {
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 0.125rem;
    }

    .lc-role {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-400);
        margin-bottom: 0.5rem;
    }

    .lc-badge {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    /* Content team grid */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .member-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s;
    }

    .member-card:hover { transform: scale(1.02); }

    .member-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        object-fit: cover;
    }

    .member-info h5 { font-weight: 700; font-size: 0.85rem; margin: 0; line-height: 1.2; }
    .member-info span { font-size: 0.7rem; font-weight: 500; color: var(--gray-400); }

    /* Design team */
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

    .design-card:hover { transform: scale(1.01); }

    .design-card-header {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--muted);
    }

    .dc-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        flex-shrink: 0;
        object-fit: cover;
    }

    .dc-name { font-weight: 800; font-size: 1rem; }
    .dc-role { font-size: 0.75rem; font-weight: 500; color: var(--gray-400); }

    .prio-label {
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gray-400);
        margin-bottom: 0.5rem;
    }

    .prio-tags { display: flex; flex-wrap: wrap; gap: 0.375rem; }

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

    @media (max-width: 768px) {
        .leader-row { grid-template-columns: 1fr; }
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

    <!-- Leadership Row -->
    <div class="leader-row anim-up d2">
        <div class="leader-card manager">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=KevinLim&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="lc-avatar" alt="Kevin Lim">
            <div class="lc-info">
                <div class="lc-name">Kevin Lim</div>
                <div class="lc-role">E-Commerce Manager</div>
                <span class="lc-badge">Manager</span>
            </div>
        </div>
        <div class="leader-card lead">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=MiloGorospe&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="lc-avatar" alt="Milo Gorospe">
            <div class="lc-info">
                <div class="lc-name">Milo Gorospe</div>
                <div class="lc-role">Content / PR Lead</div>
                <span class="lc-badge">Lead</span>
            </div>
        </div>
    </div>

    <!-- Content Associates -->
    <div class="content-grid anim-up d3">
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=AngelynCatolico&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Angelyn Catolico">
            <div class="member-info">
                <h5>Angelyn Catolico</h5>
                <span>Content Associate / Backend</span>
            </div>
        </div>
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=CzeinLaruscain&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Czein Laruscain">
            <div class="member-info">
                <h5>Czein Laruscain</h5>
                <span>Content Associate</span>
            </div>
        </div>
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=JamieOrtiz&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Jamie Ortiz">
            <div class="member-info">
                <h5>Jamie Ortiz</h5>
                <span>Product Researcher</span>
            </div>
        </div>
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=WellDacoco&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Well Dacoco">
            <div class="member-info">
                <h5>Well Dacoco</h5>
                <span>Product Researcher</span>
            </div>
        </div>
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=EmDelosSantos&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Em Delos Santos">
            <div class="member-info">
                <h5>Em Delos Santos</h5>
                <span>Content Associate</span>
            </div>
        </div>
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=MarkIvanEmpleo&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Mark Ivan Empleo">
            <div class="member-info">
                <h5>Mark Ivan Empleo</h5>
                <span>Content Associate</span>
            </div>
        </div>
        <div class="member-card">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=LiamFlores&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="member-avatar" alt="Liam Flores">
            <div class="member-info">
                <h5>Liam Flores</h5>
                <span>Content Associate</span>
            </div>
        </div>
    </div>

    <!-- Design Team Divider -->
    <div class="team-divider anim-up d4">
        <div class="td-icon" style="background: var(--secondary);"><i class="fas fa-palette"></i></div>
        <h3>Design Team</h3>
        <span class="td-count">4 members</span>
        <div class="td-line"></div>
    </div>

    <!-- Design Team Grid -->
    <div class="design-grid anim-up d4">
        <div class="design-card">
            <div class="design-card-header">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=FernDesigner&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="dc-avatar" alt="Fern">
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

        <div class="design-card">
            <div class="design-card-header">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=TimTantalizer&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="dc-avatar" alt="Tim">
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

        <div class="design-card">
            <div class="design-card-header">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=AngeloDesigner&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="dc-avatar" alt="Angelo">
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

        <div class="design-card">
            <div class="design-card-header">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed=LatrellDesigner&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="dc-avatar" alt="Latrell">
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
@endsection
