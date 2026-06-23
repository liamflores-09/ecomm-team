@extends('layouts.app')

@section('title', 'Sign In — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M13 2L3 14h9l-1 8 10-12h-9l1-8z'/></svg>">
@endsection

@section('styles')
<style>
    .top-header { display: none !important; }

    body { background: var(--background); overflow: hidden; }

    .login-split {
        min-height: 100vh;
        display: flex;
    }

    /* ── Left panel ── */
    .login-left {
        width: 42%;
        flex-shrink: 0;
        background: var(--primary);
        background-image: radial-gradient(ellipse at 20% 50%, rgba(255,255,255,0.08) 0%, transparent 60%),
                          radial-gradient(ellipse at 80% 20%, rgba(255,255,255,0.06) 0%, transparent 50%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Decorative circles */
    .login-left::before {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.1);
        bottom: -80px; left: -80px;
    }
    .login-left::after {
        content: '';
        position: absolute;
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.08);
        top: 60px; right: -40px;
    }
    .login-deco-dot {
        position: absolute;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        top: 30%; right: 12%;
    }

    .login-left-content { position: relative; z-index: 1; }

    .login-brand-icon {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; font-weight: 800;
        color: white;
        font-family: 'Space Grotesk', sans-serif;
        margin-bottom: 2rem;
        letter-spacing: -0.03em;
    }

    .login-brand-name {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: white;
        line-height: 1.15;
        letter-spacing: -0.03em;
        margin-bottom: 0.625rem;
    }

    .login-brand-sub {
        font-size: 0.875rem;
        color: rgba(255,255,255,0.65);
        font-weight: 500;
        margin-bottom: 2.5rem;
        line-height: 1.5;
    }

    .login-features {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }

    .login-feature {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: rgba(255,255,255,0.8);
        font-size: 0.82rem;
        font-weight: 500;
    }

    .login-feature-icon {
        width: 28px; height: 28px;
        border-radius: 7px;
        background: rgba(255,255,255,0.12);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.72rem;
        color: rgba(255,255,255,0.9);
        flex-shrink: 0;
    }

    .login-left-footer {
        position: absolute;
        bottom: 2rem; left: 3.5rem;
        font-size: 0.72rem;
        color: rgba(255,255,255,0.35);
        font-weight: 500;
    }

    /* ── Right panel ── */
    .login-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem;
        background: var(--card);
    }

    .login-form-wrap {
        width: 100%;
        max-width: 380px;
    }

    .login-form-heading {
        margin-bottom: 2rem;
    }

    .login-form-heading h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--foreground);
        margin-bottom: 0.25rem;
        letter-spacing: -0.02em;
    }

    .login-form-heading p {
        font-size: 0.85rem;
        color: var(--muted-foreground);
        font-weight: 500;
    }

    .lf-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        margin-bottom: 1.125rem;
    }

    .lf-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted-foreground);
    }

    .lf-input-wrap { position: relative; }

    .lf-input-icon {
        position: absolute;
        left: 1rem; top: 50%;
        transform: translateY(-50%);
        color: var(--muted-foreground);
        font-size: 0.82rem;
        pointer-events: none;
    }

    .lf-input {
        width: 100%;
        height: 52px;
        padding: 0 1rem 0 2.75rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 10px;
        font-family: var(--p-font-family-sans);
        font-size: 0.925rem;
        font-weight: 500;
        color: var(--foreground);
        outline: none;
        transition: all 0.15s;
        box-sizing: border-box;
    }

    .lf-input:focus {
        border-color: var(--primary);
        background: white;
    }

    .lf-input::placeholder { color: var(--gray-300); }

    .lf-pwd-wrap { position: relative; }

    .lf-pwd-toggle {
        position: absolute;
        right: 0.875rem; top: 50%;
        transform: translateY(-50%);
        background: none; border: none;
        cursor: pointer; padding: 0.25rem;
        color: var(--muted-foreground);
        font-size: 0.82rem;
        transition: color 0.15s;
        line-height: 1;
    }

    .lf-pwd-toggle:hover { color: var(--foreground); }

    .lf-pwd-wrap .lf-input { padding-right: 3rem; }

    .lf-submit {
        width: 100%;
        height: 52px;
        margin-top: 0.5rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: var(--p-font-family-sans);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        letter-spacing: -0.01em;
    }

    .lf-submit:hover { opacity: 0.88; }
    .lf-submit:disabled { opacity: 0.6; cursor: not-allowed; }

    .lf-footer {
        text-align: center;
        margin-top: 1.75rem;
        color: var(--muted-foreground);
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .error-msg {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.82rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 768px) {
        .login-left { display: none; }
        .login-right { background: var(--background); }
        body { overflow: auto; }
    }
</style>
@endsection

@section('content')
<div class="login-split">

    <!-- Left branded panel -->
    <div class="login-left">
        <div class="login-deco-dot"></div>
        <div class="login-left-content">
            <div class="login-brand-icon">ED</div>
            <div class="login-brand-name">Ecomm Dept<br>Hub</div>
            <div class="login-brand-sub">PR × Content Training System<br>for the Ecommerce Department</div>

            <div class="login-features">
                <div class="login-feature">
                    <div class="login-feature-icon"><i class="fas fa-calendar-check"></i></div>
                    Daily EOD reporting & tracking
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon"><i class="fas fa-book-open"></i></div>
                    Brand catalog management
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon"><i class="fas fa-chart-pie"></i></div>
                    Team performance reports
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon"><i class="fas fa-calculator"></i></div>
                    Tools: Price calculator & more
                </div>
            </div>
        </div>

        <div class="login-left-footer">© {{ date('Y') }} Ecomm Dept</div>
    </div>

    <!-- Right form panel -->
    <div class="login-right">
        <div class="login-form-wrap anim-up">
            <div class="login-form-heading">
                <h2>Welcome back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            @if ($errors->any())
            <div class="error-msg anim-fade">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf

                <div class="lf-group">
                    <label class="lf-label">Username</label>
                    <div class="lf-input-wrap">
                        <span class="lf-input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="lf-input" placeholder="Enter your username" value="{{ old('username') }}" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="lf-group">
                    <label class="lf-label">Password</label>
                    <div class="lf-input-wrap lf-pwd-wrap">
                        <span class="lf-input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="pwdInput" class="lf-input" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="lf-pwd-toggle" onclick="togglePwd()" id="pwdToggle" tabindex="-1">
                            <i class="fas fa-eye" id="pwdEye"></i>
                        </button>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.125rem;">
                    <input type="checkbox" name="remember" id="rememberMe" style="width:16px;height:16px;accent-color:var(--primary);cursor:pointer;flex-shrink:0;">
                    <label for="rememberMe" style="font-size:0.82rem;font-weight:500;color:var(--muted-foreground);cursor:pointer;user-select:none;">Remember me</label>
                </div>

                <button type="submit" class="lf-submit" id="loginBtn">
                    Sign In <i class="fas fa-arrow-right" style="font-size:0.8rem;"></i>
                </button>
            </form>

            <div class="lf-footer">
                <i class="fas fa-shield-halved"></i>
                Secure access · Ecomm Dept Hub
            </div>
        </div>
    </div>

</div>

<script>
function togglePwd() {
    var input = document.getElementById('pwdInput');
    var eye   = document.getElementById('pwdEye');
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('loginForm').addEventListener('submit', function() {
    var btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Signing in...';
});
</script>
@endsection
