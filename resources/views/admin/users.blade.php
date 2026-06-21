@extends('layouts.app')

@section('title', 'Manage Users — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 00-3-3.87'/><path d='M16 3.13a4 4 0 010 7.75'/></svg>">
@endsection

@section('styles')
<style>
    .users-toolbar {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 1rem 1.25rem; background: var(--white); border-radius: 12px;
        border: 2px solid var(--border); margin-bottom: 1rem; flex-wrap: wrap;
    }
    .users-toolbar .search-box {
        display: flex; align-items: center; gap: 0.5rem;
        background: var(--muted); border: 2px solid transparent; border-radius: 8px;
        padding: 0 0.75rem; height: 40px; flex: 1; min-width: 200px;
        transition: border-color 0.15s;
    }
    .users-toolbar .search-box:focus-within { border-color: var(--primary); background: var(--white); }
    .users-toolbar .search-box i { color: var(--gray-300); font-size: 0.8rem; flex-shrink: 0; }
    .users-toolbar .search-box input {
        border: none; outline: none; background: transparent; width: 100%;
        font-family: var(--p-font-family-sans); font-size: 0.85rem; font-weight: 500; color: var(--fg);
    }
    .users-toolbar .search-box input::placeholder { color: var(--gray-300); }
    .toolbar-divider { width: 1px; height: 24px; background: var(--border); flex-shrink: 0; }
    .users-toolbar .result-count {
        font-size: 0.8rem; font-weight: 600; color: var(--gray-400); white-space: nowrap;
    }

    .filter-pills { display: flex; gap: 0.375rem; flex-wrap: wrap; }
    .filter-pill {
        padding: 0.375rem 0.75rem; border-radius: 6px;
        font-family: var(--p-font-family-sans); font-size: 0.75rem; font-weight: 600;
        cursor: pointer; transition: all 0.15s; border: 2px solid var(--border);
        background: var(--white); color: var(--gray-400);
    }
    .filter-pill:hover { border-color: var(--border-strong); color: var(--fg); }
    .filter-pill.active { background: var(--primary); border-color: var(--primary); color: white; }

    .users-table-wrap {
        background: var(--white); border-radius: 12px; border: 2px solid var(--border);
        overflow: hidden;
    }
    .users-table { width: 100%; border-collapse: collapse; }
    .users-table thead th {
        padding: 0.875rem 1.25rem; font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-400);
        background: var(--muted); border-bottom: 2px solid var(--border); text-align: left;
        position: sticky; top: 0; z-index: 1;
    }
    .users-table tbody td {
        padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .users-table tbody tr:last-child td { border-bottom: none; }
    .users-table tbody tr:hover td { background: #FAFAFA; }

    .user-cell { display: flex; align-items: center; gap: 0.75rem; }
    .user-cell .avatar {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        border: 2px solid var(--muted);
    }
    .user-cell .info .name { font-weight: 700; font-size: 0.85rem; }
    .user-cell .info .handle { font-size: 0.75rem; color: var(--gray-400); }

    .role-badge {
        display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px;
        font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .role-badge.manager { background: #171717; color: #ffffff; }
    .role-badge.lead { background: #6366f1; color: #ffffff; }
    .role-badge.content { background: #0ea5e9; color: #ffffff; }
    .role-badge.graphics { background: #f59e0b; color: #ffffff; }
    .role-badge.backend { background: #f43f5e; color: #ffffff; }
    .role-badge.researcher { background: #10b981; color: #ffffff; }

    .cell-muted { color: var(--gray-400); font-size: 0.85rem; }
    .cell-time { color: var(--gray-300); font-size: 0.8rem; white-space: nowrap; }

    .row-actions { display: flex; gap: 0.25rem; justify-content: flex-end; }
    .row-btn {
        width: 32px; height: 32px; border: 2px solid var(--border); border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; cursor: pointer; transition: all 0.15s;
        background: var(--white); color: var(--gray-400);
    }
    .row-btn:hover { border-color: var(--primary); color: var(--primary); }
    .row-btn.btn-danger:hover { border-color: #DC2626; color: #DC2626; }
    .row-btn:disabled { opacity: 0.25; cursor: not-allowed; }

    .role-select {
        height: 44px; padding: 0 1rem; background: var(--muted);
        border: 2px solid transparent; border-radius: 8px;
        font-family: var(--p-font-family-sans); font-size: 0.85rem; font-weight: 500;
        color: var(--fg); cursor: pointer; outline: none; transition: all 0.15s;
        width: 100%; appearance: auto;
    }
    .role-select:focus { border-color: var(--primary); background: var(--white); }

    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--gray-300); }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 0.75rem; color: var(--gray-200); }

    @media (max-width: 768px) {
        .users-toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-divider { display: none; }
        .filter-pills { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 0.25rem; }
        .users-table-wrap { overflow-x: auto; }
        .users-table { min-width: 640px; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.users" :isAdmin="true" />

<div class="main-content">
    <a href="{{ route('admin.dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="anim-up" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem;">Manage Users</h2>
            <p style="color: var(--gray-400); font-size: 0.9rem; font-weight: 500; margin: 0;">Add, edit, or remove team members</p>
        </div>
        <button class="btn-flat-primary" style="height: 40px; padding: 0 1.25rem; font-size: 0.85rem;" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>

    @if (session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- Toolbar: Search + Filter + Count -->
    <div class="users-toolbar anim-up d1">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search users..." oninput="handleSearch(this.value)">
        </div>
        <div class="toolbar-divider"></div>
        <div class="filter-pills" id="filterPills">
            <button class="filter-pill active" onclick="filterByRole('all', this)">All</button>
            <button class="filter-pill" onclick="filterByRole('manager', this)">Manager</button>
            <button class="filter-pill" onclick="filterByRole('lead', this)">Lead</button>
            <button class="filter-pill" onclick="filterByRole('content', this)">Content</button>
            <button class="filter-pill" onclick="filterByRole('graphics', this)">Graphics</button>
            <button class="filter-pill" onclick="filterByRole('backend', this)">Backend</button>
            <button class="filter-pill" onclick="filterByRole('researcher', this)">Researcher</button>
        </div>
        <div class="toolbar-divider"></div>
        <span class="result-count" id="resultCount">{{ $users->count() }} users</span>
    </div>

    <!-- Users Table -->
    <div class="users-table-wrap anim-up d2">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Full Name</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th style="width: 100px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @forelse ($users as $u)
                <tr data-role="{{ $u->role }}" data-search="{{ strtolower($u->first_name . ' ' . $u->last_name . ' ' . $u->username . ' ' . $u->role) }}">
                    <td>
                        <div class="user-cell">
                            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ in_array($u->username, ['jamie', 'em', 'ange', 'czein', 'well']) ? $u->username . 'Female' : $u->username }}" class="avatar" alt="">
                            <div class="info">
                                <div class="name">{{ $u->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight: 500;">{{ $u->first_name }} {{ $u->last_name }}</td>
                    <td class="cell-muted">{{ $u->mobile_number ?: '—' }}</td>
                    <td><span class="role-badge {{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                    <td class="cell-time">{{ $u->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="row-actions">
                            <button class="row-btn" title="Edit" onclick="openEditModal({{ $u->id }}, '{{ $u->first_name }}', '{{ $u->last_name }}', '{{ $u->username }}', '{{ $u->role }}', '{{ $u->mobile_number }}')">
                                <i class="fas fa-pen"></i>
                            </button>
                            @if ($u->id !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Delete {{ $u->username }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn btn-danger" title="Delete">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                            @else
                            <button class="row-btn" disabled title="Current user">
                                <i class="fas fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-users"></i>
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
                            <option value="backend">Backend</option>
                            <option value="researcher">Researcher</option>
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
                            <option value="backend">Backend</option>
                            <option value="researcher">Researcher</option>
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
var currentRole = 'all';
var currentSearch = '';

function filterByRole(role, btn) {
    currentRole = role;
    document.querySelectorAll('.filter-pill').forEach(function(b) { b.classList.remove('active'); });
    if (btn) btn.classList.add('active');
    applyFilters();
}

function handleSearch(val) {
    currentSearch = val.toLowerCase().trim();
    applyFilters();
}

function applyFilters() {
    var rows = document.querySelectorAll('#userTableBody tr[data-role]');
    var visible = 0;
    rows.forEach(function(row) {
        var matchRole = currentRole === 'all' || row.getAttribute('data-role') === currentRole;
        var matchSearch = !currentSearch || row.getAttribute('data-search').indexOf(currentSearch) !== -1;
        if (matchRole && matchSearch) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });
    document.getElementById('resultCount').textContent = visible + ' user' + (visible !== 1 ? 's' : '');
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
