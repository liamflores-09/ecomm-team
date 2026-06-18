@extends('layouts.app')

@section('title', 'Manage Users — EC Training Hub')

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="background: #DC2626;">EC</div>
        <div>
            <h5>EC Training</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.users') }}" class="active"><i class="fas fa-users"></i> Manage Users</a></li>
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
            <h2>Manage <span class="highlight">Users</span></h2>
            <p>Add or remove team members</p>
        </div>
        <div class="user-badge">
            <div class="avatar admin-av">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ $user->username }}</span>
                <span class="role-tag admin-role">Admin</span>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <div class="section-header anim-up d1" style="margin-bottom: 1rem;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Team Members</h3>
                <p>{{ $users->count() }} user(s) registered</p>
            </div>
            <button class="btn-flat-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" style="height: 44px; padding: 0 1.25rem; font-size: 0.875rem;">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
    </div>

    <div class="flat-card anim-up d2" style="padding: 0; overflow: hidden;">
        <table class="table-flat">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; background: var(--primary); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem;">
                                {{ strtoupper(substr($u->username, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600;">{{ $u->username }}</div>
                                <div style="font-size: 0.75rem; color: var(--gray-500);">ID: {{ $u->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="tag tag-{{ $u->role }}">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td style="color: var(--gray-500);">{{ $u->created_at->diffForHumans() }}</td>
                    <td style="text-align: right;">
                        @if ($u->id !== $user->id)
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-flat-secondary" style="height: 36px; padding: 0 0.75rem; font-size: 0.8rem; color: #DC2626;">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </form>
                        @else
                        <span style="font-size: 0.75rem; color: var(--gray-300); font-weight: 600;">Current user</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--gray-300);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;"><i class="fas fa-users"></i></div>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel" style="font-weight: 700; font-size: 1.1rem;">
                    <i class="fas fa-user-plus" style="color: var(--primary); margin-right: 0.5rem;"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Username</label>
                        <input type="text" name="username" class="input-flat" placeholder="Enter username" required>
                        @error('username')
                        <small style="color: #DC2626; font-weight: 600; font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Password</label>
                        <input type="password" name="password" class="input-flat" placeholder="Enter password" required>
                        @error('password')
                        <small style="color: #DC2626; font-weight: 600; font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label class="label-flat">Role</label>
                        <select name="role" class="input-flat" required style="appearance: auto;">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-flat-secondary" data-bs-dismiss="modal" style="height: 44px; font-size: 0.875rem;">Cancel</button>
                    <button type="submit" class="btn-flat-primary" style="height: 44px; font-size: 0.875rem;">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
