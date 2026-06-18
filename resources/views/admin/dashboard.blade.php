@extends('layouts.app')

@section('title', 'Admin Dashboard — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        background: #DC2626;
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

    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.25rem; position: relative; z-index: 1; }
    .welcome-banner p { color: rgba(255,255,255,0.75); font-weight: 500; font-size: 0.9rem; margin: 0; position: relative; z-index: 1; }

    /* Stats */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        transition: all 0.2s;
    }

    .stat-card:hover { transform: scale(1.02); }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .stat-count { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.75rem; font-weight: 500; color: var(--gray-500); }

    /* Section divider */
    .admin-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.75rem 0 1rem;
    }

    .admin-divider .ad-icon {
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

    .admin-divider h4 { font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .admin-divider .ad-line { flex: 1; height: 2px; background: var(--muted); }

    /* Quick links */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .quick-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        text-decoration: none;
        color: var(--fg);
        transition: all 0.2s;
    }

    .quick-card:hover { transform: scale(1.02); }

    .quick-card .qc-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .quick-card h5 { font-weight: 700; font-size: 0.9rem; margin: 0; }
    .quick-card p { font-size: 0.75rem; color: var(--gray-500); margin: 0.125rem 0 0; font-weight: 500; }

    /* Recent users */
    .recent-card {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
    }

    .recent-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid var(--muted);
    }

    .recent-header h4 { font-weight: 800; font-size: 0.85rem; margin: 0; }

    .recent-list {
        padding: 0;
    }

    .recent-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--muted);
        transition: all 0.15s;
    }

    .recent-item:last-child { border-bottom: none; }
    .recent-item:hover { background: var(--muted); }

    .recent-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .recent-name { font-weight: 600; font-size: 0.85rem; }
    .recent-info { font-size: 0.7rem; color: var(--gray-400); }
    .recent-time { margin-left: auto; font-size: 0.7rem; color: var(--gray-400); font-weight: 500; }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .quick-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="background: #DC2626;">ED</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Manage Users</a></li>
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
            <h2>Admin <span class="highlight">Dashboard</span></h2>
            <p>Manage your team and training system</p>
        </div>
        <div class="user-badge">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $user->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="avatar" alt="{{ $user->username }}" style="width: 36px; height: 36px; border-radius: 50%;">
            <div class="user-info">
                <span class="user-name">{{ $user->username }}</span>
                <span class="role-tag admin-role">Manager</span>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up d1">
        <h2>Welcome back, {{ $user->first_name }}!</h2>
        <p>Here's an overview of your team and system.</p>
    </div>

    <!-- Divider: Overview -->
    <div class="admin-divider anim-up d2">
        <div class="ad-icon" style="background: var(--primary);"><i class="fas fa-chart-simple"></i></div>
        <h4>Overview</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Stats -->
    <div class="stat-grid anim-up d2">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-count">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #DC2626;"><i class="fas fa-crown"></i></div>
            <div>
                <div class="stat-count">{{ $managers }}</div>
                <div class="stat-label">Managers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #EC4899;"><i class="fas fa-star"></i></div>
            <div>
                <div class="stat-count">{{ $leads }}</div>
                <div class="stat-label">Leads</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--secondary);"><i class="fas fa-pen-nib"></i></div>
            <div>
                <div class="stat-count">{{ $content }}</div>
                <div class="stat-label">Content</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--accent);"><i class="fas fa-palette"></i></div>
            <div>
                <div class="stat-count">{{ $graphics }}</div>
                <div class="stat-label">Graphics</div>
            </div>
        </div>
    </div>

    <!-- Divider: Quick Actions -->
    <div class="admin-divider anim-up d3">
        <div class="ad-icon" style="background: var(--accent);"><i class="fas fa-bolt"></i></div>
        <h4>Quick Actions</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Quick Links -->
    <div class="quick-grid anim-up d3">
        <a href="{{ route('admin.users') }}" class="quick-card">
            <div class="qc-icon" style="background: var(--primary);"><i class="fas fa-user-plus"></i></div>
            <div>
                <h5>Add User</h5>
                <p>Create a new team member account</p>
            </div>
        </a>
        <a href="{{ route('team') }}" class="quick-card">
            <div class="qc-icon" style="background: var(--secondary);"><i class="fas fa-users"></i></div>
            <div>
                <h5>View Team</h5>
                <p>See all team members on the Team page</p>
            </div>
        </a>
        <a href="{{ route('dashboard') }}" class="quick-card">
            <div class="qc-icon" style="background: var(--accent);"><i class="fas fa-arrow-right-from-bracket"></i></div>
            <div>
                <h5>Switch to User</h5>
                <p>Go to the user-facing dashboard</p>
            </div>
        </a>
    </div>

    <!-- Divider: Recent -->
    <div class="admin-divider anim-up d4">
        <div class="ad-icon" style="background: var(--secondary);"><i class="fas fa-clock"></i></div>
        <h4>Recent Users</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Recent Users -->
    <div class="recent-card anim-up d4">
        <div class="recent-header">
            <h4>Recently Added</h4>
            <a href="{{ route('admin.users') }}" style="font-size: 0.8rem; font-weight: 600; color: var(--primary); text-decoration: none;">View All <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i></a>
        </div>
        <div class="recent-list">
            @forelse($recentUsers as $ru)
            <div class="recent-item">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $ru->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="recent-avatar" alt="{{ $ru->username }}">
                <div>
                    <div class="recent-name">{{ $ru->first_name }} {{ $ru->last_name }}</div>
                    <div class="recent-info">{{ $ru->username }} · {{ ucfirst($ru->role) }}</div>
                </div>
                <div class="recent-time">{{ $ru->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="text-align: center; padding: 2rem; color: var(--gray-400); font-weight: 500;">
                No users yet.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
