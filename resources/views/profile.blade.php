@extends('layouts.app')

@section('title', 'My Profile — Ecomm Dept')
@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2'/><circle cx='12' cy='7' r='4'/></svg>">
@endsection
@section('has-sidebar', true)

@section('styles')
<style>
    .profile-hero {
        background: var(--card); border: 1px solid var(--border-light); border-radius: 8px;
        padding: 2rem; display: flex; align-items: center; gap: 1.75rem;
        margin-bottom: 1.25rem; position: relative;
    }
    .profile-avatar-wrap { position: relative; width: 96px; height: 96px; flex-shrink: 0; cursor: pointer; }
    .profile-avatar { width: 96px; height: 96px; border-radius: 50%; border: 3px solid var(--border-light); object-fit: cover; display: block; }
    .profile-avatar-overlay {
        position: absolute; inset: 0; border-radius: 50%;
        background: rgba(0,0,0,0.45); color: white; font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.2s;
    }
    .profile-avatar-wrap:hover .profile-avatar-overlay { opacity: 1; }
    .btn-flat-sm { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border); background: var(--secondary); color: var(--secondary-foreground); font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.15s; }
    .btn-flat-sm:hover { background: var(--hover); }
    .profile-hero-body { flex: 1; min-width: 0; }
    .profile-hero-name { font-size: 1.4rem; font-weight: 800; line-height: 1.1; margin-bottom: 0.25rem; font-family: 'Space Grotesk', sans-serif; }
    .profile-hero-handle { font-size: 0.85rem; color: var(--muted-foreground); font-weight: 500; margin-bottom: 0.625rem; }
    .profile-hero-badges { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
    .profile-edit-btn { position: absolute; top: 1.25rem; right: 1.25rem; }

    .profile-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
    .info-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1.25rem 1.5rem; }
    .info-card-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted-foreground); margin-bottom: 0.375rem; }
    .info-card-value { font-size: 0.95rem; font-weight: 600; color: var(--foreground); }
    .info-card-value.muted { color: var(--muted-foreground); font-weight: 500; }

    /* Form group in modal */
    .pf-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .pf-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .pf-input, .pf-select { height: 44px; padding: 0 0.875rem; background: var(--muted); border: 2px solid transparent; border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500; color: var(--fg); outline: none; transition: all 0.15s; width: 100%; box-sizing: border-box; }
    .pf-input:focus, .pf-select:focus { border-color: var(--primary); background: var(--white); }
    .pf-input::placeholder { color: var(--gray-300); }
    .pf-select { appearance: none; cursor: pointer; }
    .pf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; }
    .pf-hint { font-size: 0.72rem; color: var(--muted-foreground); margin-top: 0.2rem; }

    @media (max-width: 640px) {
        .profile-hero { flex-direction: column; text-align: center; }
        .profile-hero-badges { justify-content: center; }
        .profile-edit-btn { position: static; margin-top: 0.5rem; }
        .profile-info-grid { grid-template-columns: 1fr; }
        .pf-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
@php $isAdmin = $user->isAdmin(); @endphp
<x-sidebar :isAdmin="$isAdmin" active="profile" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom:1.5rem;">
        <div>
            <h2>My <span class="highlight">Profile</span></h2>
            <p>Your account information</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <!-- Hero card -->
    <div class="profile-hero anim-up d1">
        <div class="profile-avatar-wrap" onclick="document.getElementById('avatarInput').click()" title="Change photo">
            <img src="{{ $user->avatarUrl() }}" alt="" class="profile-avatar" id="profileAvatar">
            <div class="profile-avatar-overlay"><i class="fas fa-camera"></i></div>
        </div>
        <div class="profile-hero-body">
            <div class="profile-hero-name">{{ $user->first_name }} {{ $user->last_name }}</div>
            <div class="profile-hero-handle">{{ '@' . $user->username }}</div>
            <div class="profile-hero-badges">
                <span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                @if($user->badge)
                <span style="display:inline-flex;align-items:center;padding:2px 8px;background:#f0f0ff;border:1px solid #a5a5fc;border-radius:9999px;font-size:0.6rem;font-weight:700;color:#5757f8;">{{ $user->badge }}</span>
                @endif
            </div>
            <div style="display:flex;gap:0.5rem;margin-top:0.5rem;flex-wrap:wrap;">
                <button class="btn-flat-sm" onclick="document.getElementById('avatarInput').click()" style="font-size:0.72rem;">
                    <i class="fas fa-camera"></i> Change Photo
                </button>
                @if($user->avatar)
                <form method="POST" action="{{ route('profile.avatar.remove') }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-flat-sm" style="font-size:0.72rem;color:var(--destructive);">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </form>
                @endif
            </div>
        </div>
        <div class="profile-edit-btn">
            <button class="btn-flat-primary" style="height:38px;padding:0 1rem;font-size:0.82rem;" onclick="openModal('editProfileModal')">
                <i class="fas fa-pen"></i> Edit Profile
            </button>
        </div>
    </div>

    {{-- Hidden avatar upload form --}}
    <form id="avatarForm" method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" style="display:none;">
        @csrf
        <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="previewAndUpload(this)">
    </form>

    <!-- Info grid -->
    <div class="profile-info-grid anim-up d2">
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-at" style="margin-right:0.375rem;"></i>Username</div>
            <div class="info-card-value">{{ $user->username }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-face-smile" style="margin-right:0.375rem;"></i>Nickname</div>
            <div class="info-card-value {{ $user->nickname ? '' : 'muted' }}">{{ $user->nickname ?: 'Not set' }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-mobile-screen" style="margin-right:0.375rem;"></i>Mobile</div>
            <div class="info-card-value {{ $user->mobile_number ? '' : 'muted' }}">{{ $user->mobile_number ?: 'Not set' }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-venus-mars" style="margin-right:0.375rem;"></i>Gender</div>
            <div class="info-card-value">{{ ucfirst($user->gender ?? 'Not set') }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-id-card" style="margin-right:0.375rem;"></i>ID No.</div>
            <div class="info-card-value {{ $user->id_number ? '' : 'muted' }}">{{ $user->id_number ?: 'Not set' }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-calendar" style="margin-right:0.375rem;"></i>Member Since</div>
            <div class="info-card-value">{{ $user->created_at->format('M d, Y') }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-file-invoice" style="margin-right:0.375rem;"></i>TIN</div>
            <div class="info-card-value {{ $user->tin ? '' : 'muted' }}">{{ $user->tin ?: 'Not set' }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-shield-halved" style="margin-right:0.375rem;"></i>SSS</div>
            <div class="info-card-value {{ $user->sss ? '' : 'muted' }}">{{ $user->sss ?: 'Not set' }}</div>
        </div>
        <div class="info-card" style="grid-column: span 2;">
            <div class="info-card-label"><i class="fas fa-location-dot" style="margin-right:0.375rem;"></i>Address</div>
            <div class="info-card-value {{ $user->address ? '' : 'muted' }}">{{ $user->address ?: 'Not set' }}</div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editProfileModal">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <h5 style="font-weight:700;font-size:1rem;margin:0;">Edit Profile</h5>
            <button class="modal-close" onclick="closeModal('editProfileModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="modal-body" style="display:flex;flex-direction:column;gap:1rem;padding:1.25rem;">

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

                <div class="pf-grid">
                    <div class="pf-group">
                        <label class="pf-label">Nickname</label>
                        <input type="text" name="nickname" class="pf-input" value="{{ old('nickname', $user->nickname) }}" placeholder="e.g. Jay">
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Mobile Number</label>
                        <input type="text" name="mobile_number" class="pf-input" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="e.g. 09171234567">
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

                <div class="pf-grid">
                    <div class="pf-group">
                        <label class="pf-label">TIN</label>
                        <input type="text" name="tin" class="pf-input" value="{{ old('tin', $user->tin) }}" placeholder="Tax Identification No.">
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">SSS</label>
                        <input type="text" name="sss" class="pf-input" value="{{ old('sss', $user->sss) }}" placeholder="SSS Number">
                    </div>
                </div>

                <div class="pf-group">
                    <label class="pf-label">Address</label>
                    <input type="text" name="address" class="pf-input" value="{{ old('address', $user->address) }}" placeholder="Home address">
                </div>

                <hr style="border:none;border-top:1px solid var(--border-light);margin:0;">

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
    reader.onload = function(e) {
        document.getElementById('profileAvatar').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
    document.getElementById('avatarForm').submit();
}
</script>
@endsection
