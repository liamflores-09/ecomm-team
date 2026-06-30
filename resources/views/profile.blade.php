@extends('layouts.app')

@section('title', 'My Profile — Ecomm Dept')
@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2'/><circle cx='12' cy='7' r='4'/></svg>">
@endsection
@section('has-sidebar', true)

@section('styles')
<style>
/* ── Hero ─────────────────────────────────────────────── */
.pf-hero {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 1.25rem;
    position: relative;
}
.pf-banner { height: 84px; }

.pf-hero-body {
    padding: 0 1.625rem 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
}

/* Avatar column — pull up to overlap banner */
.pf-avatar-col {
    flex-shrink: 0;
    margin-top: -44px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}
.pf-avatar-wrap {
    position: relative;
    width: 92px;
    height: 92px;
    cursor: pointer;
    flex-shrink: 0;
}
.pf-avatar {
    width: 92px;
    height: 92px;
    border-radius: 50%;
    border: 4px solid var(--card);
    object-fit: cover;
    display: block;
}
.pf-avatar-overlay {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(0,0,0,0.48); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; opacity: 0; transition: opacity 0.18s;
}
.pf-avatar-wrap:hover .pf-avatar-overlay { opacity: 1; }
.pf-avatar-actions { display: flex; gap: 0.375rem; flex-wrap: wrap; justify-content: center; }

/* Hero info */
.pf-hero-info { flex: 1; min-width: 0; padding-top: 0.625rem; }
.pf-hero-name {
    font-size: 1.45rem; font-weight: 800; line-height: 1.15;
    font-family: 'Space Grotesk', sans-serif;
    margin-bottom: 0.25rem;
    color: var(--foreground);
}
.pf-hero-handle {
    font-size: 0.83rem; color: var(--muted-foreground);
    font-weight: 500; margin-bottom: 0.625rem;
}
.pf-hero-badges {
    display: flex; align-items: center; gap: 0.425rem; flex-wrap: wrap;
}
.pf-badge-custom {
    display: inline-flex; align-items: center; padding: 2px 9px;
    background: #f0f0ff; border: 1px solid #a5a5fc; border-radius: 9999px;
    font-size: 0.62rem; font-weight: 700; color: #5757f8;
}
.pf-joined-pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 2px 9px; border-radius: 9999px;
    border: 1px solid var(--border); background: var(--muted);
    font-size: 0.62rem; font-weight: 600; color: var(--muted-foreground);
}
.pf-hero-actions {
    flex-shrink: 0;
    padding-top: 0.5rem;
}

/* ── Utility button ───────────────────────────────────── */
.btn-flat-sm {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 6px;
    border: 1px solid var(--border);
    background: var(--secondary); color: var(--secondary-foreground);
    font-size: 0.72rem; font-weight: 600; cursor: pointer;
    transition: background 0.15s;
}
.btn-flat-sm:hover { background: var(--hover); }

/* ── Info grid ────────────────────────────────────────── */
.pf-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.125rem;
    margin-bottom: 1.125rem;
}

/* ── Section cards ────────────────────────────────────── */
.pf-section {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}
.pf-section-head {
    display: flex; align-items: center; gap: 0.55rem;
    padding: 0.7rem 1.25rem;
    background: var(--muted);
    border-bottom: 1px solid var(--border);
}
.pf-section-icon {
    width: 22px; height: 22px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.62rem; color: #fff;
    flex-shrink: 0;
}
.pf-section-title {
    font-size: 0.69rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--foreground); margin: 0;
}

/* ── Field rows ───────────────────────────────────────── */
.pf-row {
    display: flex; align-items: center; gap: 0.875rem;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.12s;
}
.pf-row:last-child { border-bottom: none; }
.pf-row-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.68rem; color: var(--muted-foreground);
    flex-shrink: 0;
}
.pf-row-body { flex: 1; min-width: 0; }
.pf-row-label {
    font-size: 0.6rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--muted-foreground); margin-bottom: 2px;
    display: flex; align-items: center; gap: 4px;
}
.pf-row-value {
    font-size: 0.88rem; font-weight: 600; color: var(--foreground);
    line-height: 1.4;
}
.pf-row-value.empty {
    color: var(--muted-foreground); font-weight: 400; font-style: italic;
}

/* ── Secret / private rows ────────────────────────────── */
.pf-row-secret { cursor: pointer; }
.pf-row-secret:hover { background: var(--muted); }
.pf-row-value.secret-blurred {
    filter: blur(5px); user-select: none;
    transition: filter 0.28s ease;
}
.pf-row-value.secret-blurred.revealed { filter: none; user-select: text; }
.pf-secret-footer {
    display: flex; align-items: center; gap: 0.5rem;
    margin-top: 0.4rem; flex-wrap: wrap;
}
.secret-pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 2px 8px;
    border: 1px solid var(--border); border-radius: 9999px;
    font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--muted-foreground); transition: all 0.15s;
    pointer-events: none;
}
.pf-row-secret:hover .secret-pill { border-color: var(--foreground); color: var(--foreground); }
.secret-privacy-badge {
    display: inline-flex; align-items: center; gap: 0.25rem;
    font-size: 0.6rem; font-weight: 600; color: var(--muted-foreground); opacity: 0.6;
}
.secret-privacy-badge.is-hidden { color: #f59e0b; opacity: 0.85; }

/* Private section 2-col inner grid */
.pf-private-grid { display: grid; grid-template-columns: 1fr 1fr; }
.pf-private-grid .pf-row { border-right: 1px solid var(--border); }
.pf-private-grid .pf-row:last-child { border-right: none; }

/* ── Modal form ───────────────────────────────────────── */
.pf-group { display: flex; flex-direction: column; gap: 0.3rem; }
.pf-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
.pf-input, .pf-select {
    height: 44px; padding: 0 0.875rem;
    background: var(--muted); border: 2px solid transparent;
    border-radius: 8px; font-family: var(--p-font-family-sans);
    font-size: 0.9rem; font-weight: 500; color: var(--fg);
    outline: none; transition: all 0.15s; width: 100%; box-sizing: border-box;
}
.pf-input:focus, .pf-select:focus { border-color: var(--primary); background: var(--white); }
.pf-input::placeholder { color: var(--gray-300); }
.pf-select { appearance: none; cursor: pointer; }
.pf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; }
.pf-check-row { display: flex; align-items: center; gap: 0.4rem; margin-top: 0.35rem; }
.pf-check-row input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--primary); cursor: pointer; flex-shrink: 0; }
.pf-check-row label { font-size: 0.72rem; color: var(--muted-foreground); cursor: pointer; }
.pf-form-divider {
    display: flex; align-items: center; gap: 0.625rem;
    font-size: 0.62rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.07em; color: var(--muted-foreground);
    margin: 0.25rem 0;
}
.pf-form-divider::before, .pf-form-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}

/* ── Responsive ───────────────────────────────────────── */
@media (max-width: 640px) {
    .pf-hero-body { flex-direction: column; align-items: center; text-align: center; }
    .pf-hero-info { text-align: center; }
    .pf-hero-badges { justify-content: center; }
    .pf-hero-actions { width: 100%; display: flex; justify-content: center; }
    .pf-grid-2 { grid-template-columns: 1fr; }
    .pf-private-grid { grid-template-columns: 1fr; }
    .pf-private-grid .pf-row { border-right: none; border-bottom: 1px solid var(--border); }
    .pf-private-grid .pf-row:last-child { border-bottom: none; }
    .pf-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
@php
$isAdmin = $user->isAdmin();
$roleColors = [
    'content'    => '#0ea5e9',
    'researcher' => '#10b981',
    'graphics'   => '#f59e0b',
    'backend'    => '#f43f5e',
    'manager'    => '#7c3aed',
    'head'       => '#334155',
    'analyst'    => '#ec4899',
];
$roleColor = $roleColors[$user->role] ?? '#6366f1';
@endphp
<x-sidebar :isAdmin="$isAdmin" active="profile" />

<div class="main-content">

    @if(session('success'))
    <div class="alert-flat success anim-fade" style="margin-bottom:1.25rem;"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    {{-- ── Hero ── --}}
    <div class="pf-hero anim-up">
        <div class="pf-banner" style="background: linear-gradient(130deg, {{ $roleColor }}28 0%, {{ $roleColor }}0e 60%, transparent 100%);"></div>

        <div class="pf-hero-body">
            {{-- Avatar column --}}
            <div class="pf-avatar-col">
                <div class="pf-avatar-wrap" onclick="document.getElementById('avatarInput').click()" title="Change photo">
                    <img src="{{ $user->avatarUrl() }}" alt="" class="pf-avatar" id="profileAvatar"
                         style="box-shadow: 0 0 0 4px var(--card), 0 0 0 6px {{ $roleColor }}55;">
                    <div class="pf-avatar-overlay"><i class="fas fa-camera"></i></div>
                </div>
                <div class="pf-avatar-actions">
                    <button class="btn-flat-sm" onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i> Photo
                    </button>
                    @if($user->avatar)
                    <form method="POST" action="{{ route('profile.avatar.remove') }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-flat-sm" style="color:var(--destructive);">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="pf-hero-info">
                <div class="pf-hero-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="pf-hero-handle">{{ '@' . $user->username }}</div>
                <div class="pf-hero-badges">
                    <span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    @if($user->badge)
                    <span class="pf-badge-custom">{{ $user->badge }}</span>
                    @endif
                    <span class="pf-joined-pill">
                        <i class="fas fa-calendar-check"></i>
                        Since {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>

            {{-- Edit button --}}
            <div class="pf-hero-actions">
                <button class="btn-flat-primary" style="height:38px;padding:0 1.1rem;font-size:0.82rem;"
                        onclick="openModal('editProfileModal')">
                    <i class="fas fa-pen"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden avatar form --}}
    <form id="avatarForm" method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" style="display:none;">
        @csrf
        <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="previewAndUpload(this)">
    </form>

    {{-- ── Info grid (2 cols) ── --}}
    <div class="pf-grid-2 anim-up d1">

        {{-- Personal --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <div class="pf-section-icon" style="background:#6366f1;"><i class="fas fa-user"></i></div>
                <span class="pf-section-title">Personal</span>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-face-smile"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Nickname</div>
                    <div class="pf-row-value {{ $user->nickname ? '' : 'empty' }}">{{ $user->nickname ?: 'Not set' }}</div>
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-venus-mars"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Gender</div>
                    <div class="pf-row-value {{ $user->gender ? '' : 'empty' }}">{{ $user->gender ? ucfirst($user->gender) : 'Not set' }}</div>
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-mobile-screen"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Mobile</div>
                    <div class="pf-row-value {{ $user->mobile_number ? '' : 'empty' }}">{{ $user->mobile_number ?: 'Not set' }}</div>
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-location-dot"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Address</div>
                    <div class="pf-row-value {{ $user->address ? '' : 'empty' }}">{{ $user->address ?: 'Not set' }}</div>
                </div>
            </div>
        </div>

        {{-- Account --}}
        <div class="pf-section">
            <div class="pf-section-head">
                <div class="pf-section-icon" style="background:#0ea5e9;"><i class="fas fa-id-card"></i></div>
                <span class="pf-section-title">Account</span>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-at"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Username</div>
                    <div class="pf-row-value">{{ $user->username }}</div>
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-id-badge"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">ID No.</div>
                    <div class="pf-row-value {{ $user->id_number ? '' : 'empty' }}">{{ $user->id_number ?: 'Not set' }}</div>
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-shield-halved"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Role</div>
                    <div class="pf-row-value">
                        <span class="role-badge {{ $user->role }}" style="font-size:0.68rem;">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>
            </div>
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-calendar"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">Member Since</div>
                    <div class="pf-row-value">{{ $user->created_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Private section (TIN + SSS) ── --}}
    @if($user->tin || $user->sss)
    <div class="pf-section anim-up d2" style="margin-bottom:1.125rem;">
        <div class="pf-section-head">
            <div class="pf-section-icon" style="background:#f59e0b;"><i class="fas fa-lock"></i></div>
            <span class="pf-section-title">Private Information</span>
            <span style="margin-left:auto;font-size:0.6rem;color:var(--muted-foreground);font-weight:600;">Click a field to reveal</span>
        </div>
        <div class="pf-private-grid">

            {{-- TIN --}}
            @if($user->tin)
            <div class="pf-row pf-row-secret" onclick="toggleSecretRow(this)">
                <div class="pf-row-icon" style="color:#f59e0b;"><i class="fas fa-file-invoice"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">TIN <i class="fas fa-lock" style="font-size:0.5rem;opacity:0.4;"></i></div>
                    <div class="pf-row-value secret-blurred">{{ $user->tin }}</div>
                    <div class="pf-secret-footer">
                        <div class="secret-pill"><i class="fas fa-eye secret-eye"></i><span class="secret-label">Reveal</span></div>
                        <span class="secret-privacy-badge {{ $user->tin_hidden ? 'is-hidden' : '' }}">
                            <i class="fas {{ $user->tin_hidden ? 'fa-eye-slash' : 'fa-users' }}"></i>
                            {{ $user->tin_hidden ? 'Hidden from team' : 'Visible to team' }}
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">TIN</div>
                    <div class="pf-row-value empty">Not set</div>
                </div>
            </div>
            @endif

            {{-- SSS --}}
            @if($user->sss)
            <div class="pf-row pf-row-secret" onclick="toggleSecretRow(this)">
                <div class="pf-row-icon" style="color:#10b981;"><i class="fas fa-shield-halved"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">SSS <i class="fas fa-lock" style="font-size:0.5rem;opacity:0.4;"></i></div>
                    <div class="pf-row-value secret-blurred">{{ $user->sss }}</div>
                    <div class="pf-secret-footer">
                        <div class="secret-pill"><i class="fas fa-eye secret-eye"></i><span class="secret-label">Reveal</span></div>
                        <span class="secret-privacy-badge {{ $user->sss_hidden ? 'is-hidden' : '' }}">
                            <i class="fas {{ $user->sss_hidden ? 'fa-eye-slash' : 'fa-users' }}"></i>
                            {{ $user->sss_hidden ? 'Hidden from team' : 'Visible to team' }}
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="pf-row">
                <div class="pf-row-icon"><i class="fas fa-shield-halved"></i></div>
                <div class="pf-row-body">
                    <div class="pf-row-label">SSS</div>
                    <div class="pf-row-value empty">Not set</div>
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

</div>

{{-- ── Edit Profile Modal ── --}}
<div class="modal-overlay" id="editProfileModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <h5 style="font-weight:700;font-size:1rem;margin:0;">Edit Profile</h5>
            <button class="modal-close" onclick="closeModal('editProfileModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="modal-body" style="display:flex;flex-direction:column;gap:0.875rem;padding:1.375rem;">

                <div class="pf-form-divider">Name</div>
                <div class="pf-grid">
                    <div class="pf-group">
                        <label class="pf-label">First Name</label>
                        <input type="text" name="first_name" class="pf-input" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Last Name</label>
                        <input type="text" name="last_name" class="pf-input" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                </div>

                <div class="pf-form-divider">Personal</div>
                <div class="pf-grid">
                    <div class="pf-group">
                        <label class="pf-label">Nickname</label>
                        <input type="text" name="nickname" class="pf-input" value="{{ old('nickname', $user->nickname) }}" placeholder="e.g. Jay">
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Mobile Number</label>
                        <input type="text" name="mobile_number" class="pf-input" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="09171234567">
                    </div>
                </div>
                <div class="pf-grid">
                    <div class="pf-group">
                        <label class="pf-label">Gender</label>
                        <select name="gender" class="pf-select" required>
                            <option value="male" {{ ($user->gender ?? 'male') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ ($user->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">ID No.</label>
                        <input type="text" name="id_number" class="pf-input" value="{{ old('id_number', $user->id_number) }}" placeholder="Company / Gov't ID">
                    </div>
                </div>
                <div class="pf-group">
                    <label class="pf-label">Address</label>
                    <input type="text" name="address" class="pf-input" value="{{ old('address', $user->address) }}" placeholder="Home address">
                </div>

                <div class="pf-form-divider">Credentials</div>
                <div class="pf-grid">
                    <div class="pf-group">
                        <label class="pf-label">TIN</label>
                        <input type="text" name="tin" class="pf-input" value="{{ old('tin', $user->tin) }}" placeholder="Tax Identification No.">
                        <div class="pf-check-row">
                            <input type="checkbox" name="tin_hidden" value="1" id="chkTinHidden" {{ old('tin_hidden', $user->tin_hidden) ? 'checked' : '' }}>
                            <label for="chkTinHidden">Hide from team members</label>
                        </div>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">SSS</label>
                        <input type="text" name="sss" class="pf-input" value="{{ old('sss', $user->sss) }}" placeholder="SSS Number">
                        <div class="pf-check-row">
                            <input type="checkbox" name="sss_hidden" value="1" id="chkSssHidden" {{ old('sss_hidden', $user->sss_hidden) ? 'checked' : '' }}>
                            <label for="chkSssHidden">Hide from team members</label>
                        </div>
                    </div>
                </div>

                <div class="pf-form-divider">Security</div>
                <div class="pf-group">
                    <label class="pf-label">New Password <span style="font-weight:400;text-transform:none;letter-spacing:0;">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="pf-input" placeholder="Min. 6 characters">
                </div>
                <div class="pf-group">
                    <label class="pf-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="pf-input" placeholder="Repeat new password">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('editProfileModal')" style="height:38px;font-size:0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height:38px;font-size:0.85rem;"><i class="fas fa-check"></i> Save</button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded', function() { openModal('editProfileModal'); });</script>
@endif
<script>
function previewAndUpload(input) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) { document.getElementById('profileAvatar').src = e.target.result; };
    reader.readAsDataURL(input.files[0]);
    document.getElementById('avatarForm').submit();
}
function toggleSecretRow(row) {
    var val   = row.querySelector('.secret-blurred');
    var eye   = row.querySelector('.secret-eye');
    var label = row.querySelector('.secret-label');
    if (!val) return;
    var revealing = !val.classList.contains('revealed');
    val.classList.toggle('revealed', revealing);
    if (eye)   eye.className    = revealing ? 'fas fa-eye-slash secret-eye' : 'fas fa-eye secret-eye';
    if (label) label.textContent = revealing ? 'Hide' : 'Reveal';
}
</script>
@endsection
