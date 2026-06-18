@extends('layouts.app')

@section('title', 'Manage Users — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 00-3-3.87'/><path d='M16 3.13a4 4 0 010 7.75'/></svg>">
@endsection

@section('styles')
<style>
    .admin-stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .admin-stat {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }

    .admin-stat .as-icon {
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

    .admin-stat .as-count {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
    }

    .admin-stat .as-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--gray-500);
    }

    .user-table-wrap {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .user-table thead th {
        background: var(--muted);
        padding: 0.875rem 1rem;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
        text-align: left;
    }

    .user-table tbody td {
        padding: 0.875rem 1rem;
        border-top: 2px solid var(--muted);
        vertical-align: middle;
    }

    .user-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .user-name {
        font-weight: 600;
    }

    .user-fullname {
        font-size: 0.75rem;
        color: var(--gray-500);
    }

    .action-btns {
        display: flex;
        gap: 0.375rem;
        justify-content: flex-end;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border: 2px solid var(--border);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.15s;
        background: transparent;
        color: var(--gray-500);
    }

    .action-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .action-btn.btn-danger:hover {
        border-color: #DC2626;
        color: #DC2626;
    }

    .action-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    /* Role Select */
    .role-select {
        height: 48px;
        padding: 0 1rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 6px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--fg);
        cursor: pointer;
        outline: none;
        transition: all 0.15s;
        width: 100%;
        appearance: auto;
    }

    .role-select:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    /* Role Filter */
    .role-filter {
        display: flex;
        gap: 0;
        border: 2px solid var(--border);
        border-radius: 6px;
        overflow: hidden;
    }

    .rf-btn {
        padding: 0.375rem 0.75rem;
        border: none;
        border-right: 2px solid var(--border);
        background: var(--white);
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.75rem;
        color: var(--gray-500);
        cursor: pointer;
        transition: all 0.15s;
    }

    .rf-btn:last-child { border-right: none; }

    .rf-btn.active {
        background: var(--primary);
        color: white;
    }

    .rf-btn:hover:not(.active) {
        background: var(--muted);
    }

    @media (max-width: 768px) {
        .admin-stat-row { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
        .admin-stat-row { grid-template-columns: 1fr; }
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
    <a href="{{ route('admin.dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Manage <span class="highlight">Users</span></h2>
            <p>Add, edit, or remove team members</p>
        </div>
        <div class="user-badge">
            <div class="avatar admin-av">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ $user->username }}</span>
                <span class="role-tag admin-role">Manager</span>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="admin-stat-row anim-up d1">
        <div class="admin-stat">
            <div class="as-icon" style="background: var(--primary);"><i class="fas fa-users"></i></div>
            <div>
                <div class="as-count">{{ $users->count() }}</div>
                <div class="as-label">Total Users</div>
            </div>
        </div>
        <div class="admin-stat">
            <div class="as-icon" style="background: #DC2626;"><i class="fas fa-user-shield"></i></div>
            <div>
                <div class="as-count">{{ $users->where('role', 'manager')->count() }}</div>
                <div class="as-label">Managers</div>
            </div>
        </div>
        <div class="admin-stat">
            <div class="as-icon" style="background: #EC4899;"><i class="fas fa-crown"></i></div>
            <div>
                <div class="as-count">{{ $users->where('role', 'lead')->count() }}</div>
                <div class="as-label">Leads</div>
            </div>
        </div>
        <div class="admin-stat">
            <div class="as-icon" style="background: var(--secondary);"><i class="fas fa-pen-nib"></i></div>
            <div>
                <div class="as-count">{{ $users->where('role', 'content')->count() }}</div>
                <div class="as-label">Content</div>
            </div>
        </div>
        <div class="admin-stat">
            <div class="as-icon" style="background: var(--accent);"><i class="fas fa-palette"></i></div>
            <div>
                <div class="as-count">{{ $users->where('role', 'graphics')->count() }}</div>
                <div class="as-label">Graphics</div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;" class="anim-up d2">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <h3 style="font-weight: 800; font-size: 1rem;">Team Members</h3>
            <div class="role-filter" id="roleFilter">
                <button class="rf-btn active" onclick="filterByRole('all', this)">All</button>
                <button class="rf-btn" onclick="filterByRole('manager', this)">Manager</button>
                <button class="rf-btn" onclick="filterByRole('lead', this)">Lead</button>
                <button class="rf-btn" onclick="filterByRole('content', this)">Content</button>
                <button class="rf-btn" onclick="filterByRole('graphics', this)">Graphics</button>
            </div>
        </div>
        <button class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>

    <!-- Table -->
    <div class="user-table-wrap anim-up d2">
        <table class="user-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Full Name</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr data-role="{{ $u->role }}">
                    <td>
                        <div class="user-cell">
                            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $u->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" class="user-avatar" alt="{{ $u->username }}">
                            <div>
                                <div class="user-name">{{ $u->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight: 500;">{{ $u->first_name }} {{ $u->last_name }}</td>
                    <td style="color: var(--gray-500);">{{ $u->mobile_number ?: '—' }}</td>
                    <td>
                        @php
                            $roleColors = [
                                'manager' => ['bg' => '#FEF3C7', 'text' => '#D97706'],
                                'lead' => ['bg' => '#FCE7F3', 'text' => '#DB2777'],
                                'content' => ['bg' => '#D1FAE5', 'text' => '#059669'],
                                'graphics' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                            ];
                            $rc = $roleColors[$u->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                        @endphp
                        <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; background: {{ $rc['bg'] }}; color: {{ $rc['text'] }};">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td style="color: var(--gray-500);">{{ $u->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="action-btns">
                            <button class="action-btn" title="Edit" onclick="openEditModal({{ $u->id }}, '{{ $u->first_name }}', '{{ $u->last_name }}', '{{ $u->username }}', '{{ $u->role }}', '{{ $u->mobile_number }}')">
                                <i class="fas fa-pen"></i>
                            </button>
                            @if ($u->id !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Delete {{ $u->username }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-danger" title="Delete">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                            @else
                            <button class="action-btn" disabled title="Current user">
                                <i class="fas fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--gray-300);">
                        <i class="fas fa-users" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-weight: 700; font-size: 1rem;">
                    <i class="fas fa-user-plus" style="color: var(--primary); margin-right: 0.5rem;"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="label-flat">First Name</label>
                            <input type="text" name="first_name" class="input-flat" placeholder="e.g. Juan" required>
                        </div>
                        <div>
                            <label class="label-flat">Last Name</label>
                            <input type="text" name="last_name" class="input-flat" placeholder="e.g. Dela Cruz" required>
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Mobile Number</label>
                        <input type="text" name="mobile_number" class="input-flat" placeholder="e.g. 09171234567">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Username</label>
                        <input type="text" name="username" class="input-flat" placeholder="e.g. juandelacruz" required>
                        @error('username')
                        <small style="color: #DC2626; font-weight: 600; font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Password</label>
                        <input type="password" name="password" class="input-flat" placeholder="Min. 6 characters" required>
                        @error('password')
                        <small style="color: #DC2626; font-weight: 600; font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label class="label-flat">Role</label>
                        <select name="role" class="role-select" required>
                            <option value="content">Content</option>
                            <option value="graphics">Graphics</option>
                            <option value="lead">Lead</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-flat-secondary" data-bs-dismiss="modal" style="height: 40px; font-size: 0.85rem;">Cancel</button>
                    <button type="submit" class="btn-flat-primary" style="height: 40px; font-size: 0.85rem;">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-weight: 700; font-size: 1rem;">
                    <i class="fas fa-pen" style="color: var(--primary); margin-right: 0.5rem;"></i>Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="label-flat">First Name</label>
                            <input type="text" name="first_name" id="editFirstName" class="input-flat" placeholder="e.g. Juan" required>
                        </div>
                        <div>
                            <label class="label-flat">Last Name</label>
                            <input type="text" name="last_name" id="editLastName" class="input-flat" placeholder="e.g. Dela Cruz" required>
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Mobile Number</label>
                        <input type="text" name="mobile_number" id="editMobile" class="input-flat" placeholder="e.g. 09171234567">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">Username</label>
                        <input type="text" name="username" id="editUsername" class="input-flat" placeholder="e.g. juandelacruz" required>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label class="label-flat">New Password <span style="color: var(--gray-400); font-weight: 400; text-transform: none; letter-spacing: 0;">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="input-flat" placeholder="Enter new password">
                    </div>
                    <div>
                        <label class="label-flat">Role</label>
                        <select name="role" id="editRoleSelect" class="role-select" required>
                            <option value="content">Content</option>
                            <option value="graphics">Graphics</option>
                            <option value="lead">Lead</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-flat-secondary" data-bs-dismiss="modal" style="height: 40px; font-size: 0.85rem;">Cancel</button>
                    <button type="submit" class="btn-flat-primary" style="height: 40px; font-size: 0.85rem;">
                        <i class="fas fa-check"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function filterByRole(role, btn) {
    document.querySelectorAll('.rf-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    document.querySelectorAll('.user-table tbody tr[data-role]').forEach(function(row) {
        if (role === 'all' || row.getAttribute('data-role') === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openAddModal() {
    document.getElementById('addUserModal').querySelector('form').reset();
    new bootstrap.Modal(document.getElementById('addUserModal')).show();
}

function openEditModal(id, firstName, lastName, username, role, mobile) {
    document.getElementById('editUserForm').action = '/admin/users/' + id;
    document.getElementById('editFirstName').value = firstName;
    document.getElementById('editLastName').value = lastName;
    document.getElementById('editUsername').value = username;
    document.getElementById('editMobile').value = mobile || '';
    document.getElementById('editRoleSelect').value = role;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
</script>
@endsection
