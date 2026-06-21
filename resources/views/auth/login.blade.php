@extends('layouts.app')

@section('title', 'Login — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M13 2L3 14h9l-1 8 10-12h-9l1-8z'/></svg>">
@endsection

@section('styles')
<style>
    body { background: var(--muted); }

    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Geometric background decoration */
    .login-page::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 400px;
        height: 400px;
        background: var(--primary);
        border-radius: 50%;
        opacity: 0.06;
    }

    .login-page::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 280px;
        height: 280px;
        background: var(--secondary);
        border-radius: 50%;
        opacity: 0.06;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        background: var(--bg-card);
        border-radius: 8px;
        padding: 3rem 2.5rem;
        position: relative;
        z-index: 1;
    }

    .login-brand {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .login-brand .icon {
        width: 72px;
        height: 72px;
        background: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        color: white;
        font-size: 1.75rem;
        font-weight: 800;
    }

    .login-brand h3 {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .login-brand p {
        color: var(--gray-500);
        font-weight: 500;
        font-size: 14px;
    }

    .login-form .field {
        margin-bottom: 20px;
    }

    .login-form .input-icon-wrap {
        position: relative;
    }

    .login-form .input-icon-wrap .icon-pos {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-300);
        font-size: 16px;
        pointer-events: none;
    }

    .login-form .input-icon-wrap .input-flat {
        padding-left: 44px;
    }

    .login-form .btn-submit {
        width: 100%;
        height: 56px;
        margin-top: 8px;
    }

    .login-footer {
        text-align: center;
        margin-top: 32px;
        color: var(--gray-300);
        font-size: 12px;
        font-weight: 500;
    }

    .login-footer i {
        margin-right: 4px;
    }

    .error-msg {
        background: #FEE2E2;
        color: #991B1B;
        padding: 12px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="login-page">
    <div class="login-card anim-up">
        <div class="login-brand">
            <div class="icon">EC</div>
            <h3>Ecomm Dept Hub</h3>
            <p>PR x Content Training System</p>
        </div>

        @if ($errors->any())
        <div class="error-msg anim-fade">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="login-form">
            @csrf

            <div class="field anim-up d1">
                <label class="label-flat">Username</label>
                <div class="input-icon-wrap">
                    <span class="icon-pos"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="input-flat" placeholder="Enter your username" value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="field anim-up d2">
                <label class="label-flat">Password</label>
                <div class="input-icon-wrap">
                    <span class="icon-pos"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="input-flat" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" class="btn-flat-primary btn-submit anim-up d3">
                Sign In
            </button>
        </form>

        <div class="login-footer anim-fade d4">
            <i class="fas fa-shield-halved"></i>
            Secure access to your training portal
        </div>
    </div>
</div>
@endsection
