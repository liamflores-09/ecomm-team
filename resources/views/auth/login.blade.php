@extends('layouts.app')

@section('title', 'Login — Ecomm Dept Hub')

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
        background: var(--accent);
        border-radius: 50%;
        opacity: 0.06;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        background: var(--white);
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
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .login-brand p {
        color: var(--gray-500);
        font-weight: 500;
        font-size: 0.875rem;
    }

    .login-form .field {
        margin-bottom: 1.25rem;
    }

    .login-form .input-icon-wrap {
        position: relative;
    }

    .login-form .input-icon-wrap .icon-pos {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-300);
        font-size: 1rem;
        pointer-events: none;
    }

    .login-form .input-icon-wrap .input-flat {
        padding-left: 2.75rem;
    }

    .login-form .btn-submit {
        width: 100%;
        height: 56px;
        margin-top: 0.5rem;
    }

    .login-footer {
        text-align: center;
        margin-top: 2rem;
        color: var(--gray-300);
        font-size: 0.8rem;
        font-weight: 500;
    }

    .login-footer i {
        margin-right: 0.25rem;
    }

    .error-msg {
        background: #FEE2E2;
        color: #991B1B;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
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
