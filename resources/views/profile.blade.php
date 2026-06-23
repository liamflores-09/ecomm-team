@extends('layouts.app')

@section('title', 'My Profile — Ecomm Dept')
@section('has-sidebar', true)

@section('styles')
<style>
    .profile-hero {
        background: var(--card); border: 1px solid var(--border-light); border-radius: 8px;
        padding: 2rem; display: flex; align-items: center; gap: 1.75rem;
        margin-bottom: 1.25rem; position: relative;
    }
    .profile-avatar { width: 96px; height: 96px; border-radius: 50%; border: 3px solid var(--border-light); flex-shrink: 0; }
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
@php $isAdmin = $user->role === 'manager'; @endphp
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
        <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $user->gender === 'female' ? $user->username . 'Female' : $user->username }}" alt="" class="profile-avatar" id="profileAvatar">
        <div class="profile-hero-body">
            <div class="profile-hero-name">{{ $user->first_name }} {{ $user->last_name }}</div>
            <div class="profile-hero-handle">{{ '@' . $user->username }}</div>
            <div class="profile-hero-badges">
                <span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                @if($user->badge)
                <span style="display:inline-flex;align-items:center;padding:2px 8px;background:#f0f0ff;border:1px solid #a5a5fc;border-radius:9999px;font-size:0.6rem;font-weight:700;color:#5757f8;">{{ $user->badge }}</span>
                @endif
            </div>
        </div>
        <div class="profile-edit-btn">
            <button class="btn-flat-primary" style="height:38px;padding:0 1rem;font-size:0.82rem;" onclick="openModal('editProfileModal')">
                <i class="fas fa-pen"></i> Edit Profile
            </button>
        </div>
    </div>

    <!-- Info grid -->
    <div class="profile-info-grid anim-up d2">
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-mobile-screen" style="margin-right:0.375rem;"></i>Mobile</div>
            <div class="info-card-value {{ $user->mobile_number ? '' : 'muted' }}">{{ $user->mobile_number ?: 'Not set' }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-venus-mars" style="margin-right:0.375rem;"></i>Gender</div>
            <div class="info-card-value">{{ ucfirst($user->gender ?? 'Not set') }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-at" style="margin-right:0.375rem;"></i>Username</div>
            <div class="info-card-value">{{ $user->username }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label"><i class="fas fa-calendar" style="margin-right:0.375rem;"></i>Member Since</div>
            <div class="info-card-value">{{ $user->created_at->format('M d, Y') }}</div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editProfileModal">
    <div class="modal-box" style="max-width:480px;">
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
                        <label class="pf-label">Mobile Number</label>
                        <input type="text" name="mobile_number" class="pf-input" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="e.g. 09171234567">
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Gender</label>
                        <select name="gender" class="pf-select" required>
                            <option value="male" {{ ($user->gender ?? 'male') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ ($user->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
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
@endsection
