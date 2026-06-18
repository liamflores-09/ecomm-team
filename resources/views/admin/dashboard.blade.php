@extends('layouts.app')

@section('title', 'Admin Dashboard — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
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
            <div class="avatar admin-av">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ $user->username }}</span>
                <span class="role-tag admin-role">Admin</span>
            </div>
        </div>
    </div>

    <div class="section-header anim-up d1">
        <h3>What will you do right now?</h3>
        <p>Choose an action to get started</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4 anim-up d2">
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="flat-card h-100">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <h5>Manage Users</h5>
                    <p>Add, view, or remove team members from the training system.</p>
                    <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
                </div>
            </a>
        </div>

        <div class="col-md-4 anim-up d3">
            <div class="flat-card card-amber h-100 disabled">
                <div class="card-icon"><i class="fas fa-chart-simple"></i></div>
                <h5>Analytics</h5>
                <p>View training progress and completion metrics across all users.</p>
                <div class="arrow-icon"><i class="fas fa-lock"></i></div>
            </div>
        </div>

        <div class="col-md-4 anim-up d4">
            <div class="flat-card card-muted h-100 disabled">
                <div class="card-icon"><i class="fas fa-gear"></i></div>
                <h5>Settings</h5>
                <p>Configure system settings and training module content.</p>
                <div class="arrow-icon"><i class="fas fa-lock"></i></div>
            </div>
        </div>
    </div>
</div>
@endsection
