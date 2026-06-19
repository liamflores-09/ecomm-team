@extends('layouts.app')

@section('title', 'Admin Dashboard — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
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

    .quick-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .quick-card { background: var(--white); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; text-decoration: none; color: var(--fg); transition: all 0.2s; border: 2px solid transparent; }
    .quick-card:hover { transform: translateY(-2px); border-color: var(--primary); box-shadow: 0 4px 12px rgba(59,130,246,0.1); }
    .quick-card .qc-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; flex-shrink: 0; }
    .quick-card h5 { font-weight: 700; font-size: 0.9rem; margin: 0; }
    .quick-card p { font-size: 0.75rem; color: var(--gray-400); margin: 0.25rem 0 0; font-weight: 500; }

    .recent-card { background: var(--white); border-radius: 12px; overflow: hidden; }
    .recent-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 2px solid var(--muted); }
    .recent-header h4 { font-weight: 800; font-size: 0.85rem; margin: 0; }
    .recent-header a { font-size: 0.8rem; font-weight: 600; color: var(--primary); text-decoration: none; }
    .recent-list { padding: 0; }
    .recent-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; border-bottom: 1px solid var(--muted); transition: all 0.15s; }
    .recent-item:last-child { border-bottom: none; }
    .recent-item:hover { background: #F8FAFC; }
    .recent-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; }
    .recent-name { font-weight: 700; font-size: 0.9rem; }
    .recent-info { font-size: 0.75rem; color: var(--gray-400); }
    .recent-time { margin-left: auto; font-size: 0.75rem; color: var(--gray-400); font-weight: 500; white-space: nowrap; }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .quick-grid { grid-template-columns: 1fr 1fr; }
        .welcome-banner { padding: 2rem; }
        .welcome-banner .wb-date { display: none; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: 1fr; }
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
        <li class="nav-dropdown" id="dailyLogsDropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown()" class="has-submenu">
                <i class="fas fa-clipboard-list"></i> Daily Logs <i class="fas fa-chevron-down dropdown-arrow" id="dropdownArrow"></i>
            </a>
            <ul class="submenu" id="dailyLogsSubmenu">
                <li><a href="{{ route('admin.daily-logs') }}">All Roles</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'content']) }}">Content</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'lead']) }}">Lead</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'researcher']) }}">Researcher</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'graphics']) }}">Graphics</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'backend']) }}">Backend</a></li>
            </ul>
        </li>
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
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $user->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="avatar" alt="{{ $user->username }}" style="width: 40px; height: 40px; border-radius: 50%;">
            <div class="user-info">
                <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
                <span class="role-tag admin-role">Manager</span>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up d1">
        <div>
            <h2>Welcome back, {{ $user->first_name }}!</h2>
            <p>Here's an overview of your team and system.</p>
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
                <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-users"></i></div>
                <div>
                    <div class="stat-count">{{ $totalUsers }}</div>
                    <div class="stat-label">Team Members</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #059669;"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <div class="stat-count">{{ $totalLogs }}</div>
                    <div class="stat-label">Total Logs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #F59E0B;"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="stat-count">{{ $thisMonthLogs }}</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-divider anim-up d3">
        <div class="sd-icon" style="background: var(--accent);"><i class="fas fa-bolt"></i></div>
        <h4>Quick Actions</h4>
        <div class="sd-line"></div>
    </div>

    <div class="quick-grid anim-up d3">
        <a href="{{ route('admin.users') }}" class="quick-card">
            <div class="qc-icon" style="background: var(--primary);"><i class="fas fa-user-plus"></i></div>
            <div>
                <h5>Manage Users</h5>
                <p>Add, edit, or remove members</p>
            </div>
        </a>
        <a href="{{ route('admin.daily-logs') }}" class="quick-card">
            <div class="qc-icon" style="background: #059669;"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <h5>View Logs</h5>
                <p>Check daily team activity</p>
            </div>
        </a>
        <a href="{{ route('team') }}" class="quick-card">
            <div class="qc-icon" style="background: #EC4899;"><i class="fas fa-users"></i></div>
            <div>
                <h5>The Team</h5>
                <p>View team directory</p>
            </div>
        </a>
        <a href="{{ route('dashboard') }}" class="quick-card">
            <div class="qc-icon" style="background: #8B5CF6;"><i class="fas fa-arrow-right-from-bracket"></i></div>
            <div>
                <h5>User View</h5>
                <p>Switch to user dashboard</p>
            </div>
        </a>
    </div>

    <!-- Recent Users -->
    <div class="section-divider anim-up d4">
        <div class="sd-icon" style="background: var(--secondary);"><i class="fas fa-clock-rotate-left"></i></div>
        <h4>Recent Users</h4>
        <div class="sd-line"></div>
    </div>

    <div class="recent-card anim-up d4">
        <div class="recent-header">
            <h4>Recently Added</h4>
            <a href="{{ route('admin.users') }}">View All <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i></a>
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
            <div style="text-align: center; padding: 2.5rem; color: var(--gray-400); font-weight: 500;">
                <i class="fas fa-users" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
                No users yet.
            </div>
            @endforelse
        </div>
    </div>
</div>
<script>
function toggleDropdown() {
    var submenu = document.getElementById('dailyLogsSubmenu');
    var arrow = document.getElementById('dropdownArrow');
    if (submenu.style.display === 'none' || submenu.style.display === '') {
        submenu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        submenu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
<style>
.nav-dropdown .has-submenu {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
}
.dropdown-arrow {
    font-size: 0.65rem;
    transition: transform 0.2s;
    margin-left: auto;
}
.submenu {
    list-style: none;
    padding: 0;
    margin: 0.25rem 0 0.5rem 1.75rem;
}
.submenu li { margin: 0.125rem 0; }
.submenu a {
    display: block;
    padding: 0.5rem 0.875rem;
    color: var(--gray-300);
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.15s;
}
.submenu a:hover { background: var(--gray-700); color: white; }
.submenu a.active { background: var(--primary); color: white; }
</style>
@endsection
