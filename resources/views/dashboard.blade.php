@extends('layouts.app')

@section('title', 'Dashboard — Ecomm Dept')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='7' height='7'/><rect x='14' y='3' width='7' height='7'/><rect x='14' y='14' width='7' height='7'/><rect x='3' y='14' width='7' height='7'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        border-radius: 12px;
        padding: 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -80px; right: -40px;
        width: 250px; height: 250px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        bottom: -60px; right: 120px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .welcome-banner h2 { color: white; font-size: 1.6rem; margin-bottom: 0.375rem; position: relative; z-index: 1; font-weight: 800; }
    .welcome-banner p { color: rgba(255,255,255,0.8); font-weight: 500; font-size: 0.95rem; margin: 0; position: relative; z-index: 1; }
    .welcome-banner .wb-date { position: absolute; top: 2rem; right: 2.5rem; text-align: right; z-index: 1; }
    .welcome-banner .wb-date .wd-day { font-size: 2rem; font-weight: 800; line-height: 1; }
    .welcome-banner .wb-date .wd-month { font-size: 0.8rem; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.08em; }

    .section-divider { display: flex; align-items: center; gap: 0.75rem; margin: 2rem 0 1rem; }
    .section-divider .sd-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem; flex-shrink: 0; }
    .section-divider h4 { font-weight: 800; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .section-divider .sd-line { flex: 1; height: 2px; background: var(--muted); }

    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .stat-card { background: var(--white); border-radius: 12px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; transition: all 0.2s; border: 2px solid transparent; }
    .stat-card:hover { transform: translateY(-2px); border-color: var(--primary); box-shadow: 0 4px 12px rgba(59,130,246,0.1); }
    .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0; }
    .stat-count { font-size: 1.75rem; font-weight: 800; line-height: 1; margin-bottom: 0.125rem; }
    .stat-label { font-size: 0.8rem; font-weight: 600; color: var(--gray-400); }

    .quick-section { background: var(--white); border-radius: 12px; padding: 1.5rem; }
    .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .quick-link { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: var(--muted); border-radius: 10px; text-decoration: none; color: var(--fg); transition: all 0.2s; border: 2px solid transparent; }
    .quick-link:hover { background: var(--primary); color: white; border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59,130,246,0.2); }
    .quick-link:hover .ql-icon { background: rgba(255,255,255,0.2); color: white; }
    .ql-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--white); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: var(--primary); transition: all 0.2s; flex-shrink: 0; }
    .ql-text { flex: 1; }
    .ql-text strong { display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 0.125rem; }
    .ql-text small { color: var(--gray-400); font-size: 0.75rem; font-weight: 500; }
    .quick-link:hover .ql-text small { color: rgba(255,255,255,0.75); }
    .ql-arrow { color: var(--gray-300); font-size: 0.85rem; transition: all 0.2s; }
    .quick-link:hover .ql-arrow { color: white; }

    .logs-section { background: var(--white); border-radius: 12px; overflow: hidden; }
    .logs-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 2px solid var(--muted); }
    .logs-header h4 { font-weight: 800; font-size: 0.85rem; margin: 0; }
    .logs-header a { font-size: 0.8rem; font-weight: 600; color: var(--primary); text-decoration: none; }
    .logs-header a:hover { text-decoration: underline; }

    .logs-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .logs-table thead th { background: var(--muted); padding: 0.75rem 1rem; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-400); text-align: left; }
    .logs-table tbody td { padding: 0.875rem 1rem; border-top: 1px solid var(--muted); font-weight: 500; }
    .logs-table tbody tr:hover td { background: #F8FAFC; }
    .logs-table .num { font-weight: 700; text-align: center; }
    .logs-table .date-cell { font-weight: 700; white-space: nowrap; }

    .ref-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .ref-card { background: var(--white); border-radius: 12px; padding: 1.25rem; text-align: center; transition: all 0.2s; border: 2px solid transparent; text-decoration: none; color: var(--fg); }
    .ref-card:hover { transform: translateY(-2px); border-color: var(--primary); box-shadow: 0 4px 12px rgba(59,130,246,0.1); }
    .ref-card .rc-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1.1rem; color: white; }
    .ref-card h5 { font-weight: 700; font-size: 0.85rem; margin: 0; }

    .empty-state { text-align: center; padding: 2.5rem; color: var(--gray-400); font-weight: 500; font-size: 0.9rem; }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 0.75rem; color: var(--gray-200); }
    .empty-state a { color: var(--primary); font-weight: 600; }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .quick-links { grid-template-columns: 1fr; }
        .ref-grid { grid-template-columns: repeat(2, 1fr); }
        .welcome-banner { padding: 2rem; }
        .welcome-banner .wb-date { display: none; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
        .ref-grid { grid-template-columns: 1fr 1fr; }
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
        @if(Auth::user()->role === 'content')
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
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
    <div class="top-bar anim-up">
        <div>
            <h2>Dashboard</h2>
            <p>
                @if($user->role === 'content') Content Team Overview
                @elseif($user->role === 'lead') PR / Lead Overview
                @elseif($user->role === 'researcher') Product Research Overview
                @elseif($user->role === 'graphics') Graphics Team Overview
                @elseif($user->role === 'backend') Backend Team Overview
                @endif
            </p>
        </div>
        <div class="user-badge">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $user->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="avatar" alt="{{ $user->username }}" style="width: 40px; height: 40px; border-radius: 50%;">
            <div class="user-info">
                <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
                @php
                    $roleColors = ['content' => '#059669', 'lead' => '#DB2777', 'researcher' => '#92400E', 'graphics' => '#2563EB', 'backend' => '#7C3AED'];
                    $rc = $roleColors[$user->role] ?? '#6B7280';
                @endphp
                <span class="role-tag" style="color: {{ $rc }}; text-transform: capitalize;">{{ $user->role }}</span>
            </div>
        </div>
    </div>

    @php
        $bannerColors = ['content' => '#059669', 'lead' => '#DB2777', 'researcher' => '#92400E', 'graphics' => '#2563EB', 'backend' => '#7C3AED'];
        $bannerColor = $bannerColors[$user->role] ?? '#3B82F6';
    @endphp

    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up d1" style="background: linear-gradient(135deg, {{ $bannerColor }} 0%, {{ $bannerColor }}dd 100%);">
        <div>
            <h2>Welcome back, {{ $user->first_name }}!</h2>
            @if($user->role === 'content')
            <p>Your content workspace — posting, data gathering, and daily logs.</p>
            @elseif($user->role === 'lead')
            <p>PR leadership — product research, team coordination, and task oversight.</p>
            @elseif($user->role === 'researcher')
            <p>Product research hub — advance PR, trade-in tracking, and vendor data.</p>
            @elseif($user->role === 'graphics')
            <p>Design dashboard — CVP, banners, drafts, and visual assets.</p>
            @elseif($user->role === 'backend')
            <p>Backend operations — bulk uploads, cross-listing, QC, and Q&A.</p>
            @endif
        </div>
        <div class="wb-date">
            <div class="wd-day">{{ now()->format('d') }}</div>
            <div class="wd-month">{{ now()->format('M Y') }}</div>
        </div>
    </div>

    <!-- Stats -->
    <div style="margin-top: 1.5rem;" class="anim-up d2">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: {{ $bannerColor }};"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="stat-count">{{ $thisWeekTasks }}</div>
                    <div class="stat-label">Tasks This Week</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #F59E0B;"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="stat-count">{{ $thisMonthTasks }}</div>
                    <div class="stat-label">Tasks This Month</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: {{ $todayLog ? '#059669' : '#DC2626' }};"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <div class="stat-count" style="color: {{ $todayLog ? '#059669' : '#DC2626' }};">{{ $todayLog ? 'Done' : 'Pending' }}</div>
                    <div class="stat-label">Today's EOD</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="section-divider anim-up d3">
        <div class="sd-icon" style="background: var(--accent);"><i class="fas fa-bolt"></i></div>
        <h4>Quick Access</h4>
        <div class="sd-line"></div>
    </div>

    <div class="quick-section anim-up d3">
        <div class="quick-links">
            @if($user->role === 'content')
            <a href="{{ route('posting-procedure') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-book-open"></i></div>
                <div class="ql-text"><strong>Posting Procedure</strong><small>8-step guide for product posting</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('data-gathering') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-folder-open"></i></div>
                <div class="ql-text"><strong>Data Gathering</strong><small>Collect product info and assets</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('ecommerce-requirements') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="ql-text"><strong>E-commerce Requirements</strong><small>Platform-specific posting rules</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text"><strong>Price Calculator</strong><small>Compute SRP across platforms</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            @else
            <a href="{{ route('end-of-day') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="ql-text"><strong>End-of-Day Report</strong><small>Log your daily tasks</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text"><strong>Price Calculator</strong><small>Compute SRP across platforms</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('team') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-users"></i></div>
                <div class="ql-text"><strong>The Team</strong><small>View your colleagues</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="{{ route('important-links') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-link"></i></div>
                <div class="ql-text"><strong>Important Links</strong><small>Quick access to resources</small></div>
                <div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            @endif
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="section-divider anim-up d4">
        <div class="sd-icon" style="background: var(--secondary);"><i class="fas fa-clock-rotate-left"></i></div>
        <h4>Recent Logs</h4>
        <div class="sd-line"></div>
    </div>

    <div class="logs-section anim-up d4">
        <div class="logs-header">
            <h4>Last {{ $recentLogs->count() }} Entries</h4>
            <a href="{{ route('end-of-day') }}">View EOD <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i></a>
        </div>
        @if($recentLogs->count())
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Date</th>
                    @php
                        $tl = \App\Support\TaskLabels::get($user->role);
                    @endphp
                    <th style="text-align: center;">{{ $tl['col1'] }}</th>
                    <th style="text-align: center;">{{ $tl['col2'] }}</th>
                    <th style="text-align: center;">{{ $tl['col3'] }}</th>
                    <th style="text-align: center;">{{ $tl['col4'] }}</th>
                    <th style="text-align: center;">{{ $tl['col5'] }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentLogs as $log)
                <tr>
                    <td class="date-cell">{{ $log->date->format('M d, Y') }}</td>
                    <td class="num">{{ $log->new_sku }}</td>
                    <td class="num">{{ $log->variation_sku }}</td>
                    <td class="num">{{ $log->advance_data_gathering }}</td>
                    <td class="num">{{ $log->update_listings }}</td>
                    <td class="num">{{ $log->other_tasks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            No logs yet. <a href="{{ route('end-of-day') }}">Submit your first EOD report</a>
        </div>
        @endif
    </div>

    <!-- Quick Reference -->
    <div class="section-divider anim-up d5">
        <div class="sd-icon" style="background: #8B5CF6;"><i class="fas fa-star"></i></div>
        <h4>Quick Reference</h4>
        <div class="sd-line"></div>
    </div>

    <div class="ref-grid anim-up d5">
        <a href="{{ route('end-of-day') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--primary);"><i class="fas fa-calendar-check"></i></div>
            <h5>EOD Report</h5>
        </a>
        <a href="{{ route('important-links') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--secondary);"><i class="fas fa-link"></i></div>
            <h5>Important Links</h5>
        </a>
        <a href="{{ route('team') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--accent);"><i class="fas fa-users"></i></div>
            <h5>The Team</h5>
        </a>
        <a href="{{ route('price-calculator') }}" class="ref-card">
            <div class="rc-icon" style="background: #8B5CF6;"><i class="fas fa-calculator"></i></div>
            <h5>Price Calculator</h5>
        </a>
    </div>
</div>
@endsection
