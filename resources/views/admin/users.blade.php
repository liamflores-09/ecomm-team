@extends('layouts.app')

@section('title', 'Manage Users — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 00-3-3.87'/><path d='M16 3.13a4 4 0 010 7.75'/></svg>">
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
    .page-top h2 { font-size: 1.5rem; font-weight: 800; margin: 0 0 0.2rem; }
    .page-top p  { color: var(--muted-foreground); font-size: 0.875rem; font-weight: 500; margin: 0; }

    /* Stats Row */
    .user-stats {
        display: flex; align-items: stretch;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
        overflow: hidden; margin-bottom: 1.25rem;
    }
    .ustat { flex: 1; padding: 1rem 1.25rem; text-align: center; }
    .ustat-val   { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .ustat-label { font-size: 0.65rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }
    .ustat-div   { width: 1px; background: var(--border); flex-shrink: 0; }

    /* Toolbar */
    .users-toolbar {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.625rem 0.875rem; background: var(--card); border-radius: 8px;
        border: 1px solid var(--border); margin-bottom: 1rem; flex-wrap: wrap;
    }
    .users-toolbar .search-box {
        display: flex; align-items: center; gap: 0.5rem;
        background: var(--muted); border: 1.5px solid transparent; border-radius: 8px;
        padding: 0 0.75rem; height: 36px; flex: 1; min-width: 180px;
        transition: border-color 0.15s, background 0.15s;
    }
    .users-toolbar .search-box:focus-within { border-color: var(--primary); background: var(--card); }
    .users-toolbar .search-box i { color: var(--muted-foreground); font-size: 0.75rem; flex-shrink: 0; }
    .users-toolbar .search-box input {
        border: none; outline: none; background: transparent; width: 100%;
        font-family: var(--p-font-family-sans); font-size: 0.85rem; font-weight: 500; color: var(--foreground);
    }
    .users-toolbar .search-box input::placeholder { color: var(--muted-foreground); }
    .toolbar-divider { width: 1px; height: 22px; background: var(--border); flex-shrink: 0; }
    .result-count { font-size: 0.8rem; font-weight: 600; color: var(--muted-foreground); white-space: nowrap; }

    /* Table */
    .users-table-wrap { background: var(--card); border-radius: 8px; border: 1px solid var(--border); overflow: hidden; }
    .users-table { width: 100%; border-collapse: collapse; }
    .users-table thead th {
        padding: 0.75rem 1.25rem; font-size: 0.65rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground);
        background: var(--muted); border-bottom: 1px solid var(--border); text-align: left;
    }
    .users-table tbody td { padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .users-table tbody tr:last-child td { border-bottom: none; }
    .users-table tbody tr:hover td { background: var(--secondary); }

    .user-cell img { width: 36px; height: 36px; }
    .cell-muted { color: var(--muted-foreground); font-size: 0.85rem; }
    .cell-time  { color: var(--muted-foreground); font-size: 0.8rem; white-space: nowrap; }

    /* Row Actions */
    .row-actions { display: flex; gap: 0.25rem; justify-content: flex-end; }
    .row-btn {
        width: 32px; height: 32px; border: 1px solid var(--border); border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; cursor: pointer; transition: all 0.15s;
        background: var(--card); color: var(--muted-foreground);
    }
    .row-btn:hover            { border-color: var(--foreground); color: var(--foreground); background: var(--secondary); }
    .row-btn.btn-danger:hover { border-color: var(--destructive); color: var(--destructive); background: #fef2f2; }
    .row-btn:disabled         { opacity: 0.3; cursor: not-allowed; pointer-events: none; }

    /* Modal Form Sections */
    .form-section-label {
        display: block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: var(--muted-foreground); margin-bottom: 0.625rem;
    }
    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
    .form-field { display: flex; flex-direction: column; gap: 0.25rem; }
    .mb-3 { margin-bottom: 0.875rem; }
    .mb-4 { margin-bottom: 1.125rem; }

    /* Avatar Preview in Modal */
    .avatar-preview-wrap {
        display: flex; align-items: center; gap: 1rem;
        background: var(--muted); border-radius: 8px; padding: 0.875rem 1rem; margin-bottom: 1.25rem;
    }
    .avatar-preview {
        width: 52px; height: 52px; border-radius: 50%;
        border: 2px solid var(--border); flex-shrink: 0; object-fit: cover;
    }
    .avatar-preview-name { font-weight: 700; font-size: 0.9rem; }
    .avatar-preview-hint { font-size: 0.75rem; color: var(--muted-foreground); margin-top: 2px; }

    /* Role Select */
    .role-select {
        height: 40px; padding: 0 0.75rem;
        background: var(--card); border: 1.5px solid var(--border); border-radius: 8px;
        font-family: var(--p-font-family-sans); font-size: 0.85rem; font-weight: 500;
        color: var(--foreground); cursor: pointer; outline: none; transition: border-color 0.15s;
        width: 100%; appearance: auto;
    }
    .role-select:focus { border-color: var(--primary); }

    @media (max-width: 768px) {
        .users-toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-divider { display: none; }
        .filter-pills { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 0.25rem; }
        .users-table-wrap { overflow-x: auto; }
        .users-table { min-width: 600px; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="admin.users" :isAdmin="true" />

<div class="main-content">

    <!-- Page Header -->
    <div class="page-top anim-up">
        <div>
            <h2>Manage Users</h2>
            <p>Add, edit, or remove team members</p>
        </div>
        <button class="btn-flat-primary" style="height: 38px; padding: 0 1.25rem; font-size: 0.85rem;" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>

    @if (session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- Stats Bar -->
    <div class="user-stats anim-up d1">
        <div class="ustat">
            <div class="ustat-val">{{ $totalCount }}</div>
            <div class="ustat-label">Total Users</div>
        </div>
        <div class="ustat-div"></div>
        <div class="ustat">
            <div class="ustat-val">{{ $memberCount }}</div>
            <div class="ustat-label">Active Members</div>
        </div>
        <div class="ustat-div"></div>
        <div class="ustat">
            <div class="ustat-val">{{ $managerCount }}</div>
            <div class="ustat-label">Managers</div>
        </div>
        <div class="ustat-div"></div>
        <div class="ustat">
            <div class="ustat-val">{{ $roleCount }}</div>
            <div class="ustat-label">Roles</div>
        </div>
    </div>

    <!-- Toolbar: Search + Filter + Count -->
    <div class="users-toolbar anim-up d2">
        <div class="search-box">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search by name, username, or role…" oninput="handleSearch(this.value)">
        </div>
        <div class="toolbar-divider"></div>
        <div class="filter-pills" id="filterPills">
            <button class="filter-pill active" onclick="filterByRole('all', this)">All</button>
            <button class="filter-pill" onclick="filterByRole('head', this)">Ecomm Head</button>
            <button class="filter-pill" onclick="filterByRole('manager', this)">Manager</button>
            <button class="filter-pill" onclick="filterByRole('lead', this)">Lead</button>
            <button class="filter-pill" onclick="filterByRole('content', this)">Content</button>
            <button class="filter-pill" onclick="filterByRole('graphics', this)">Graphics</button>
            <button class="filter-pill" onclick="filterByRole('backend', this)">Backend</button>
            <button class="filter-pill" onclick="filterByRole('researcher', this)">Researcher</button>
        </div>
        <div class="toolbar-divider"></div>
        <span class="result-count" id="resultCount">{{ $totalCount }} users</span>
    </div>

    <!-- Users Table -->
    <div class="users-table-wrap anim-up d3">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th style="width: 120px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @forelse ($users as $u)
                <tr data-role="{{ $u->role }}" data-search="{{ strtolower($u->first_name . ' ' . $u->last_name . ' ' . $u->username . ' ' . $u->role) }}">
                    <td>
                        <div class="user-cell">
                            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $u->gender === 'female' ? $u->username . 'Female' : $u->username }}" alt="">
                            <div>
                                <div class="name">{{ $u->first_name }} {{ $u->last_name }}</div>
                                <div class="handle">{{ '@' . $u->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="cell-muted">{{ $u->mobile_number ?: '—' }}</td>
                    <td>
                        <span class="role-badge {{ $u->role }}">{{ ucfirst($u->role) }}</span>
                        @if($u->badge)
                        <span style="display:inline-flex;align-items:center;margin-left:5px;padding:2px 8px;background:#f0f0ff;border:1px solid #a5a5fc;border-radius:9999px;font-size:0.6rem;font-weight:700;color:#5757f8;white-space:nowrap;">{{ $u->badge }}</span>
                        @endif
                    </td>
                    <td class="cell-time">{{ $u->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="row-actions">
                            <button class="row-btn" title="View Profile"
                                data-first="{{ $u->first_name }}"
                                data-last="{{ $u->last_name }}"
                                data-username="{{ $u->username }}"
                                data-role="{{ $u->role }}"
                                data-gender="{{ $u->gender }}"
                                data-mobile="{{ $u->mobile_number }}"
                                data-badge="{{ $u->badge }}"
                                data-joined="{{ $u->created_at->format('M d, Y') }}"
                                onclick="openProfileModal(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="row-btn" title="Edit"
                                data-id="{{ $u->id }}"
                                data-first="{{ $u->first_name }}"
                                data-last="{{ $u->last_name }}"
                                data-username="{{ $u->username }}"
                                data-role="{{ $u->role }}"
                                data-gender="{{ $u->gender }}"
                                data-mobile="{{ $u->mobile_number }}"
                                data-badge="{{ $u->badge }}"
                                onclick="openEditModal(this)">
                                <i class="fas fa-pen"></i>
                            </button>
                            @if ($u->id !== $user->id)
                            <button type="button" class="row-btn btn-danger" title="Delete"
                                data-name="{{ $u->first_name }} {{ $u->last_name }}"
                                data-action="{{ route('admin.users.destroy', $u) }}"
                                onclick="confirmDelete(this)">
                                <i class="fas fa-trash-can"></i>
                            </button>
                            @else
                            <button class="row-btn" disabled title="Cannot delete your own account">
                                <i class="fas fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-state">
                        <i class="fas fa-users"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Single shared delete form — action set by JS before submit -->
<form id="deleteUserForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- ============================================================ -->
<!-- View Profile Modal                                           -->
<!-- ============================================================ -->
<div class="modal-overlay" id="viewProfileModal">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h5 style="font-weight:700;font-size:1rem;margin:0;">User Profile</h5>
            <button type="button" class="modal-close" onclick="closeModal('viewProfileModal')"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="padding:1.5rem;">
            <!-- Avatar + name section -->
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
                <img id="vpAvatar" src="" alt="" style="width:72px;height:72px;border-radius:50%;border:2px solid var(--border-light);flex-shrink:0;">
                <div>
                    <div id="vpName" style="font-size:1rem;font-weight:700;margin-bottom:0.25rem;font-family:'Space Grotesk',sans-serif;"></div>
                    <div id="vpHandle" style="font-size:0.8rem;color:var(--muted-foreground);margin-bottom:0.375rem;"></div>
                    <div style="display:flex;gap:0.375rem;flex-wrap:wrap;" id="vpBadges"></div>
                </div>
            </div>
            <!-- Details -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div style="background:var(--muted);border-radius:8px;padding:0.875rem;">
                    <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);margin-bottom:0.25rem;">Mobile</div>
                    <div id="vpMobile" style="font-size:0.875rem;font-weight:600;"></div>
                </div>
                <div style="background:var(--muted);border-radius:8px;padding:0.875rem;">
                    <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);margin-bottom:0.25rem;">Gender</div>
                    <div id="vpGender" style="font-size:0.875rem;font-weight:600;"></div>
                </div>
                <div style="background:var(--muted);border-radius:8px;padding:0.875rem;">
                    <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);margin-bottom:0.25rem;">Username</div>
                    <div id="vpUsername" style="font-size:0.875rem;font-weight:600;"></div>
                </div>
                <div style="background:var(--muted);border-radius:8px;padding:0.875rem;">
                    <div style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);margin-bottom:0.25rem;">Joined</div>
                    <div id="vpJoined" style="font-size:0.875rem;font-weight:600;"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-flat-secondary" onclick="closeModal('viewProfileModal')" style="height:38px;font-size:0.85rem;">Close</button>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Add User Modal                                               -->
<!-- ============================================================ -->
<div class="modal-overlay" id="addUserModal">
    <div class="modal-box">
        <div class="modal-header">
            <h5><i class="fas fa-user-plus" style="color: var(--indigo); margin-right: 0.5rem;"></i>Add New User</h5>
            <button type="button" class="modal-close" onclick="closeModal('addUserModal')"><i class="fas fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" id="addUserForm">
            @csrf
            <div class="modal-body">

                <div class="avatar-preview-wrap">
                    <img id="addAvatarPreview" class="avatar-preview" src="https://api.dicebear.com/7.x/notionists/svg?seed=male" alt="Avatar">
                    <div>
                        <div class="avatar-preview-name" id="addAvatarName">New Member</div>
                        <div class="avatar-preview-hint">Avatar is auto-generated</div>
                    </div>
                </div>

                <span class="form-section-label">Personal Information</span>
                <div class="form-row mb-4">
                    <div class="form-field">
                        <label class="label-flat">First Name</label>
                        <input type="text" name="first_name" id="addFirstName" class="input-flat" placeholder="e.g. Juan" required>
                    </div>
                    <div class="form-field">
                        <label class="label-flat">Last Name</label>
                        <input type="text" name="last_name" id="addLastName" class="input-flat" placeholder="e.g. Dela Cruz" required>
                    </div>
                </div>
                <div class="form-row mb-4">
                    <div class="form-field">
                        <label class="label-flat">Mobile Number</label>
                        <input type="text" name="mobile_number" class="input-flat" placeholder="e.g. 09171234567">
                    </div>
                    <div class="form-field">
                        <label class="label-flat">Gender</label>
                        <select name="gender" id="addGender" class="role-select" required onchange="updateAddAvatar()">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <span class="form-section-label">Account Details</span>
                <div class="form-field mb-3">
                    <label class="label-flat">Username</label>
                    <input type="text" name="username" id="addUsername" class="input-flat" placeholder="e.g. juandelacruz" required oninput="updateAddAvatar()">
                    @error('username')
                    <small style="color: var(--destructive); font-weight: 600; font-size: 0.8rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label class="label-flat">Password</label>
                        <input type="password" name="password" class="input-flat" placeholder="Min. 6 characters" required>
                        @error('password')
                        <small style="color: var(--destructive); font-weight: 600; font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label class="label-flat">Role</label>
                        <select name="role" class="role-select" required>
                            <option value="head">Ecomm Head</option>
                            <option value="manager">Manager</option>
                            <option value="analyst">Analyst</option>
                            <option value="content">Content</option>
                            <option value="graphics">Graphics</option>
                            <option value="backend">Backend</option>
                            <option value="researcher">Researcher</option>
                        </select>
                    </div>
                </div>
                <div class="form-field" style="margin-top:0.75rem;">
                    <label class="label-flat">
                        Badge <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:0.75rem;color:var(--muted-foreground);">(optional — e.g. Content/PR Lead)</span>
                    </label>
                    <input type="text" name="badge" class="input-flat" placeholder="e.g. Content/PR Lead">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('addUserModal')" style="height: 38px; font-size: 0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height: 38px; font-size: 0.85rem;">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- Edit User Modal                                              -->
<!-- ============================================================ -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal-box">
        <div class="modal-header">
            <h5><i class="fas fa-pen" style="color: var(--indigo); margin-right: 0.5rem;"></i>Edit User</h5>
            <button type="button" class="modal-close" onclick="closeModal('editUserModal')"><i class="fas fa-xmark"></i></button>
        </div>
        <form method="POST" id="editUserForm">
            @csrf
            @method('PUT')
            <div class="modal-body">

                <div class="avatar-preview-wrap">
                    <img id="editAvatarPreview" class="avatar-preview" src="https://api.dicebear.com/7.x/notionists/svg?seed=default" alt="Avatar">
                    <div>
                        <div class="avatar-preview-name" id="editAvatarName"></div>
                        <div class="avatar-preview-hint">Avatar is auto-generated</div>
                    </div>
                </div>

                <span class="form-section-label">Personal Information</span>
                <div class="form-row mb-4">
                    <div class="form-field">
                        <label class="label-flat">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" class="input-flat" required>
                    </div>
                    <div class="form-field">
                        <label class="label-flat">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" class="input-flat" required>
                    </div>
                </div>
                <div class="form-row mb-4">
                    <div class="form-field">
                        <label class="label-flat">Mobile Number</label>
                        <input type="text" name="mobile_number" id="editMobile" class="input-flat" placeholder="e.g. 09171234567">
                    </div>
                    <div class="form-field">
                        <label class="label-flat">Gender</label>
                        <select name="gender" id="editGender" class="role-select" required onchange="updateEditAvatar()">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <span class="form-section-label">Account Details</span>
                <div class="form-row mb-3">
                    <div class="form-field">
                        <label class="label-flat">Username</label>
                        <input type="text" name="username" id="editUsername" class="input-flat" required oninput="updateEditAvatar()">
                    </div>
                    <div class="form-field">
                        <label class="label-flat">Role</label>
                        <select name="role" id="editRoleSelect" class="role-select" required>
                            <option value="head">Ecomm Head</option>
                            <option value="manager">Manager</option>
                            <option value="analyst">Analyst</option>
                            <option value="content">Content</option>
                            <option value="graphics">Graphics</option>
                            <option value="backend">Backend</option>
                            <option value="researcher">Researcher</option>
                        </select>
                    </div>
                </div>
                <div class="form-field" style="margin-bottom:0.75rem;">
                    <label class="label-flat">
                        Badge <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:0.75rem;color:var(--muted-foreground);">(optional)</span>
                    </label>
                    <input type="text" name="badge" id="editBadge" class="input-flat" placeholder="e.g. Content/PR Lead">
                </div>
                <div class="form-field">
                    <label class="label-flat">
                        New Password
                        <span style="font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 0.75rem; color: var(--muted-foreground);">(leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" class="input-flat" placeholder="Enter new password to change it">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('editUserModal')" style="height: 38px; font-size: 0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height: 38px; font-size: 0.85rem;">
                    <i class="fas fa-check"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function avatarUrl(username, gender) {
    var u = (username || '').toLowerCase().trim() || 'default';
    var seed = gender === 'female' ? u + 'Female' : u;
    return 'https://api.dicebear.com/7.x/notionists/svg?seed=' + encodeURIComponent(seed);
}

function updateAddAvatar() {
    var username = document.getElementById('addUsername').value;
    var gender   = document.getElementById('addGender').value;
    document.getElementById('addAvatarPreview').src = avatarUrl(username, gender);
    var first = document.getElementById('addFirstName').value.trim();
    var last  = document.getElementById('addLastName').value.trim();
    document.getElementById('addAvatarName').textContent = (first + ' ' + last).trim() || 'New Member';
}
function updateEditAvatar() {
    var username = document.getElementById('editUsername').value;
    var gender   = document.getElementById('editGender').value;
    document.getElementById('editAvatarPreview').src = avatarUrl(username, gender);
}

['addFirstName', 'addLastName'].forEach(function(id) {
    document.getElementById(id).addEventListener('input', function() {
        updateAddAvatar();
    });
});

/* ---- Filtering ---- */
var currentRole   = 'all';
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
    var rows    = document.querySelectorAll('#userTableBody tr[data-role]');
    var visible = 0;
    rows.forEach(function(row) {
        var matchRole   = currentRole === 'all' || row.getAttribute('data-role') === currentRole;
        var matchSearch = !currentSearch || row.getAttribute('data-search').indexOf(currentSearch) !== -1;
        if (matchRole && matchSearch) { row.style.display = ''; visible++; }
        else { row.style.display = 'none'; }
    });
    document.getElementById('resultCount').textContent = visible + ' user' + (visible !== 1 ? 's' : '');
}

/* ---- Modals ---- */
function openAddModal() {
    document.getElementById('addUserForm').reset();
    document.getElementById('addAvatarPreview').src = avatarUrl('', 'male');
    document.getElementById('addAvatarName').textContent = 'New Member';
    openModal('addUserModal');
}

function openEditModal(btn) {
    var d = btn.dataset;
    document.getElementById('editUserForm').action   = '/admin/users/' + d.id;
    document.getElementById('editFirstName').value   = d.first;
    document.getElementById('editLastName').value    = d.last;
    document.getElementById('editUsername').value    = d.username;
    document.getElementById('editMobile').value      = d.mobile || '';
    document.getElementById('editRoleSelect').value  = d.role;
    document.getElementById('editGender').value      = d.gender || 'male';
    document.getElementById('editBadge').value       = d.badge || '';
    document.getElementById('editAvatarPreview').src = avatarUrl(d.username, d.gender || 'male');
    document.getElementById('editAvatarName').textContent = d.first + ' ' + d.last;
    openModal('editUserModal');
}

/* ---- View Profile ---- */
function openProfileModal(btn) {
    var d = btn.dataset;
    var seed = d.gender === 'female' ? d.username + 'Female' : d.username;
    document.getElementById('vpAvatar').src = 'https://api.dicebear.com/7.x/notionists/svg?seed=' + encodeURIComponent(seed);
    document.getElementById('vpName').textContent = d.first + ' ' + d.last;
    document.getElementById('vpHandle').textContent = '@' + d.username;
    document.getElementById('vpMobile').textContent = d.mobile || '—';
    document.getElementById('vpGender').textContent = d.gender ? (d.gender.charAt(0).toUpperCase() + d.gender.slice(1)) : '—';
    document.getElementById('vpUsername').textContent = d.username;
    document.getElementById('vpJoined').textContent = d.joined || '—';

    var badges = document.getElementById('vpBadges');
    var roleColors = { head:'#7c3aed', manager:'#1e293b', lead:'#6366f1', analyst:'#ec4899', content:'#0ea5e9', graphics:'#f59e0b', backend:'#f43f5e', researcher:'#10b981' };
    var badgeHtml = '<span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:9999px;font-size:0.6rem;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;background:' + (roleColors[d.role] || '#5757f8') + ';color:white;">' + (d.role ? d.role.charAt(0).toUpperCase() + d.role.slice(1) : '') + '</span>';
    if (d.badge) {
        badgeHtml += '<span style="display:inline-flex;align-items:center;padding:2px 8px;background:#f0f0ff;border:1px solid #a5a5fc;border-radius:9999px;font-size:0.6rem;font-weight:700;color:#5757f8;">' + d.badge + '</span>';
    }
    badges.innerHTML = badgeHtml;
    openModal('viewProfileModal');
}

/* ---- Custom Delete Confirm ---- */
function confirmDelete(btn) {
    var name   = btn.dataset.name;
    var action = btn.dataset.action;
    showConfirm(
        'Delete User',
        'Are you sure you want to delete "' + name + '"? This cannot be undone.',
        'Delete',
        function() {
            var form = document.getElementById('deleteUserForm');
            form.action = action;
            form.submit();
        }
    );
}
</script>
@endsection
