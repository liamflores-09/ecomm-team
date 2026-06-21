<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Ecomm Dept') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --p-font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --bg: #FAFAFA;
            --bg-card: #FFFFFF;
            --fg: #000000;
            --fg-secondary: #666666;
            --fg-tertiary: #999999;
            --border: #EAEAEA;
            --border-strong: #D4D4D4;
            --muted: #F5F5F5;
            --hover: #F5F5F5;
            --primary: #3B82F6;
            --secondary: #10B981;
        }
        body {
            font-family: var(--p-font-family-sans);
            background: var(--bg);
            color: var(--fg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-card {
            width: 100%;
            max-width: 440px;
            background: var(--bg-card);
            border-radius: 8px;
            padding: 3rem 2.5rem;
            text-align: center;
            border: 1px solid var(--border);
        }
        .brand-icon {
            width: 72px;
            height: 72px;
            background: var(--fg);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            color: white;
            font-size: 1.75rem;
            font-weight: 800;
        }
        h1 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        p {
            color: var(--fg-secondary);
            font-size: 14px;
            margin-bottom: 2rem;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--fg);
            color: white;
            border: none;
            border-radius: 6px;
            font-family: var(--p-font-family-sans);
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.1s;
        }
        .btn-primary:hover { opacity: 0.85; }
        .footer-text {
            margin-top: 2rem;
            color: var(--fg-tertiary);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <div class="brand-icon">ED</div>
        <h1>Ecomm Dept Hub</h1>
        <p>PR x Content Training System</p>

        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary">
                    <i class="fas fa-grip"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">
                    <i class="fas fa-right-to-bracket"></i> Sign In
                </a>
            @endauth
        @endif

        <div class="footer-text">
            <i class="fas fa-shield-halved"></i> Secure access to your training portal
        </div>
    </div>
</body>
</html>
