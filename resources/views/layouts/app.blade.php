<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecomm Dept')</title>
    @yield('favicon')
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.min.js"></script>
    <style>
        :root {
            --radius: 8px;

            /* Design system surfaces */
            --background: #f5f5f5;
            --foreground: #202020;
            --card: #ffffff;
            --card-foreground: #202020;
            --popover: #ffffff;
            --popover-foreground: #202020;

            /* Primary = Violet */
            --primary: #5757f8;
            --primary-foreground: #ffffff;

            /* Secondary / Muted */
            --secondary: #f5f5f5;
            --secondary-foreground: #202020;
            --muted: #f5f5f5;
            --muted-foreground: #333333;
            --accent: #f5f5f5;
            --accent-foreground: #202020;

            /* Status */
            --destructive: #ef4444;
            --destructive-foreground: #ffffff;
            --success: #22c55e;
            --warning: #f59e0b;
            --info: #3b82f6;

            /* Borders & inputs */
            --border: #202020;
            --input: #202020;
            --ring: #5757f8;

            /* Sidebar */
            --sidebar: #ffffff;
            --sidebar-foreground: #202020;
            --sidebar-primary: #5757f8;
            --sidebar-primary-foreground: #ffffff;
            --sidebar-accent: #f5f5f5;
            --sidebar-accent-foreground: #202020;
            --sidebar-border: #e5e5e5;

            /* Legacy aliases */
            --white: #ffffff;
            --bg: #f5f5f5;
            --bg-card: #ffffff;
            --fg: #202020;
            --fg-secondary: #333333;
            --fg-tertiary: #737373;
            --border-strong: #202020;
            --border-light: #e5e5e5;
            --hover: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-300: #d4d4d4;
            --gray-400: #737373;
            --gray-500: #333333;
            --gray-600: #202020;
            --gray-700: #202020;
            --p-font-family-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;

            /* Accent palette — kept only for semantic data indicators */
            --indigo: #5757f8;
            --emerald: #22c55e;
            --sky: #5757f8;
            --amber: #f59e0b;
            --rose: #ef4444;
            --violet: #5757f8;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--foreground);
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', 'Inter', sans-serif;
            color: var(--foreground);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 64px; left: 0;
            width: 260px;
            height: calc(100vh - 64px);
            background: var(--sidebar);
            border-right: 1px solid var(--sidebar-border);
            z-index: 40;
            display: flex;
            flex-direction: column;
            font-size: 14px;
            overflow-y: auto;
            transition: transform 0.3s;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--sidebar-border);
        }
        .sidebar-brand .brand-icon {
            width: 32px; height: 32px;
            background: var(--sidebar-primary);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            color: var(--sidebar-primary-foreground); font-weight: 700; font-size: 12px;
        }
        .sidebar-brand h5 { font-weight: 600; font-size: 16px; margin: 0; color: var(--sidebar-foreground); }
        .sidebar-brand span { font-size: 12px; color: var(--muted-foreground); }

        .sidebar-nav { list-style: none; padding: 8px; flex: 1; }
        .sidebar-nav li { margin: 2px 0; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: var(--muted-foreground);
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 14px;
            transition: all 0.15s;
        }
        .sidebar-nav a:hover { background: var(--sidebar-accent); color: var(--sidebar-accent-foreground); }
        .sidebar-nav a.active { background: var(--primary); color: var(--primary-foreground); font-weight: 600; }
        .sidebar-nav a i { width: 20px; text-align: center; font-size: 16px; }

        .dropdown-nav { list-style: none; padding: 0; }
        .dropdown-nav li { margin: 2px 0; }
        .dropdown-nav a {
            display: block; padding: 6px 12px 6px 42px; color: var(--muted-foreground);
            text-decoration: none; border-radius: var(--radius); font-weight: 500; font-size: 13px;
            transition: all 0.15s;
        }
        .dropdown-nav a:hover { background: var(--sidebar-accent); color: var(--sidebar-accent-foreground); }
        .dropdown-nav a.active { background: var(--primary); color: var(--primary-foreground); font-weight: 600; }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--sidebar-border);
        }
        .sidebar-footer .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: transparent;
            color: var(--muted-foreground);
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .sidebar-footer .btn-logout:hover { background: var(--secondary); color: var(--foreground); border-color: var(--border); }

        /* Top Header */
        .top-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 64px;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            z-index: 50;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        .top-header .logo-section {
            width: 260px;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            border-right: 1px solid var(--border);
            flex-shrink: 0;
        }
        .top-header .logo-section .brand-icon {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-foreground); font-weight: 700; font-size: 12px;
        }
        .sidebar-brand .brand-icon {
            background: var(--primary);
            color: var(--primary-foreground);
        }
        .top-header .logo-section h4 { font-size: 18px; font-weight: 600; margin: 0; }
        .top-header .nav-area { flex: 1; display: flex; align-items: center; padding: 0 16px; }
        .top-header .nav-area a {
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--muted-foreground);
            text-decoration: none;
            border-radius: var(--radius);
            transition: all 0.15s;
        }
        .top-header .nav-area a:hover { background: var(--secondary); color: var(--foreground); }
        .top-header .actions { display: flex; align-items: center; gap: 4px; margin-left: auto; }
        .top-header .actions button {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            background: transparent;
            border: none;
            border-radius: var(--radius);
            color: var(--muted-foreground);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .top-header .actions button:hover { background: var(--secondary); color: var(--foreground); }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 96px 2.5rem 2.5rem 2.5rem;
            min-height: 100vh;
        }

        /* Page Header */
        .page-header-bar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 20px 24px;
        }

        /* Content Area */
        .content-area { padding: 24px; }

        /* Mobile */
        .mobile-toggle {
            display: none;
            width: 40px; height: 40px;
            align-items: center; justify-content: center;
            background: transparent;
            border: none;
            border-radius: var(--radius);
            color: var(--muted-foreground);
            font-size: 18px;
            cursor: pointer;
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 39; }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); z-index: 150; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .top-header .logo-section { display: none; }
            .top-header .nav-area { display: none; }
            .mobile-toggle { display: flex; }
        }

        /* Legacy component styles */
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .page-header p { color: var(--muted-foreground); font-size: 14px; }

        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .top-bar h1 { font-size: 24px; font-weight: 700; margin-bottom: 2px; }
        .top-bar p { color: var(--muted-foreground); font-size: 14px; margin: 0; }

        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--muted-foreground); text-decoration: none;
            font-size: 14px; font-weight: 500; margin-bottom: 16px;
            transition: color 0.15s;
        }
        .back-link:hover { color: var(--foreground); }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: var(--primary); color: var(--primary-foreground);
            border: 1px solid var(--primary); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.88; }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: transparent; color: var(--foreground);
            border: 1px solid var(--foreground); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-secondary:hover { background: var(--secondary); }

        .btn-flat-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: var(--primary); color: var(--primary-foreground);
            border: 1px solid var(--primary); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-flat-primary:hover { opacity: 0.88; }

        .btn-flat-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: transparent; color: var(--foreground);
            border: 1px solid var(--foreground); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-flat-secondary:hover { background: var(--secondary); }

        .alert, .alert-flat {
            padding: 12px 16px; border-radius: var(--radius); font-size: 14px; font-weight: 500;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }
        .alert.success, .alert-flat.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert.danger, .alert-flat.danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .input-flat {
            width: 100%; height: 40px; padding: 0 12px;
            background: var(--card); border: 1px solid var(--border-light); border-radius: var(--radius);
            font-family: 'Inter', sans-serif; font-size: 14px; color: var(--foreground);
            outline: none; transition: border-color 0.15s;
        }
        .input-flat:focus { border-color: var(--primary); }
        .input-flat::placeholder { color: var(--muted-foreground); }

        .label-flat { display: block; font-weight: 500; font-size: 14px; color: var(--foreground); margin-bottom: 6px; }

        /* Confirm Dialog */
        .confirm-icon-wrap { display: flex; justify-content: center; padding: 1.75rem 1.5rem 1rem; }
        .confirm-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: #fef2f2; display: flex; align-items: center; justify-content: center;
            color: var(--destructive); font-size: 1.35rem;
        }
        .btn-destructive {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; background: var(--destructive); color: var(--destructive-foreground);
            border: none; border-radius: var(--radius);
            font-family: var(--p-font-family-sans); font-weight: 500; font-size: 14px;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn-destructive:hover { opacity: 0.88; }

        /* Custom Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 9000;
            align-items: center; justify-content: center; padding: 1rem;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);
            width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto;
            animation: fadeInUp 0.2s ease-out;
        }
        .modal-header { border-bottom: 1px solid var(--border); padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; }
        .modal-header h5 { font-weight: 700; font-size: 1rem; margin: 0; }
        .modal-body { padding: 20px; }
        .modal-footer { border-top: 1px solid var(--border); padding: 12px 20px; display: flex; justify-content: flex-end; gap: 8px; }
        .modal-close {
            width: 28px; height: 28px; border: none; background: transparent;
            border-radius: var(--radius); cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--muted-foreground); font-size: 13px; transition: all 0.15s; flex-shrink: 0;
        }
        .modal-close:hover { background: var(--secondary); color: var(--foreground); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        .user-badge { display: flex; align-items: center; gap: 8px; }
        .user-badge .avatar { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--border); }
        .user-badge .user-info { display: flex; flex-direction: column; }
        .user-badge .user-name { font-weight: 500; font-size: 13px; }
        .user-badge .role-tag { font-size: 11px; font-weight: 500; color: var(--muted-foreground); }

        /* Nav User Badge */
        .nav-user-badge { display: flex; align-items: center; gap: 10px; padding: 4px 8px; border-radius: var(--radius); }
        .nav-avatar { width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid var(--border); flex-shrink: 0; }
        .nav-user-info { display: flex; flex-direction: column; line-height: 1.2; }
        .nav-user-name { font-size: 13px; font-weight: 600; color: var(--foreground); }
        .nav-user-role { font-size: 11px; font-weight: 500; color: var(--muted-foreground); }
        @media (max-width: 768px) { .nav-user-info { display: none; } }

        /* Custom Select */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: var(--white);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 8px 36px 8px 12px;
            font-family: var(--p-font-family-sans);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--foreground);
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
            line-height: 1.5;
        }
        select:hover  { border-color: var(--border-strong); }
        select:focus  { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(87,87,248,0.12); }
        select option { padding: 8px 12px; font-weight: 500; }

        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .anim-up { animation: fadeInUp 0.25s ease-out both; }
        .anim-fade { animation: fadeIn 0.25s ease-out both; }
        .d1 { animation-delay: 0.03s; } .d2 { animation-delay: 0.06s; } .d3 { animation-delay: 0.09s; }
        .d4 { animation-delay: 0.12s; } .d5 { animation-delay: 0.15s; }

        /* Shared: Role Badges */
        .role-badge {
            display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px;
            font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .role-badge.manager { background: var(--primary); color: var(--primary-foreground); }
        .role-badge.lead    { background: var(--indigo);  color: #fff; }
        .role-badge.content { background: var(--sky);     color: #fff; }
        .role-badge.graphics   { background: var(--amber); color: #fff; }
        .role-badge.backend    { background: var(--rose);  color: #fff; }
        .role-badge.researcher { background: var(--emerald); color: #fff; }

        /* Shared: User Cell */
        .user-cell { display: flex; align-items: center; gap: 0.625rem; }
        .user-cell img { width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid var(--border); flex-shrink: 0; }
        .user-cell .name   { font-weight: 600; font-size: 0.85rem; }
        .user-cell .handle { font-size: 0.75rem; color: var(--muted-foreground); }

        /* Shared: Empty State */
        .empty-state { text-align: center; padding: 3rem; color: var(--muted-foreground); font-size: 0.85rem; }
        .empty-state i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--border-strong); }

        /* Shared: Filter Pills */
        .filter-pills { display: flex; gap: 0.375rem; flex-wrap: wrap; }
        .filter-pill {
            padding: 0.25rem 0.625rem; border-radius: 9999px;
            font-family: var(--p-font-family-sans); font-size: 0.75rem; font-weight: 600;
            cursor: pointer; transition: all 0.15s; border: 1px solid var(--foreground);
            background: transparent; color: var(--foreground);
        }
        .filter-pill:hover  { background: var(--secondary); }
        .filter-pill.active { background: var(--primary); border-color: var(--primary); color: var(--primary-foreground); }

        /* Command Palette */
        .cmd-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; align-items: flex-start; justify-content: center; padding-top: 15vh; }
        .cmd-overlay.open { display: flex; }
        .cmd-palette { width: 100%; max-width: 520px; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .cmd-input-wrap { display: flex; align-items: center; gap: 10px; padding: 0 16px; border-bottom: 1px solid var(--border); }
        .cmd-input-wrap i { color: var(--muted-foreground); font-size: 14px; }
        .cmd-input { flex: 1; height: 48px; border: none; outline: none; background: transparent; font-family: 'Inter', sans-serif; font-size: 15px; color: var(--foreground); }
        .cmd-input::placeholder { color: var(--muted-foreground); }
        .cmd-results { max-height: 320px; overflow-y: auto; padding: 8px; }
        .cmd-group-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted-foreground); padding: 8px 12px 4px; }
        .cmd-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: var(--radius); cursor: pointer; text-decoration: none; color: var(--foreground); font-size: 14px; transition: background 0.15s; }
        .cmd-item:hover, .cmd-item.active { background: var(--secondary); }
        .cmd-item .ci-icon { width: 32px; height: 32px; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; background: var(--muted); color: var(--muted-foreground); font-size: 13px; flex-shrink: 0; }
        .cmd-item:hover .ci-icon, .cmd-item.active .ci-icon { background: var(--primary); color: white; }
        .cmd-item .ci-name { font-weight: 500; }
        .cmd-item .ci-desc { font-size: 12px; color: var(--muted-foreground); }
        .cmd-footer { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-top: 1px solid var(--border); font-size: 12px; color: var(--muted-foreground); }
        .cmd-footer kbd { background: var(--muted); border: 1px solid var(--border); border-radius: 4px; padding: 2px 6px; font-family: ui-monospace, monospace; font-size: 11px; font-weight: 600; }
    </style>
    @yield('styles')
</head>
<body class="bg-background text-foreground">
    <!-- Top Header -->
    <header class="top-header">
        <div class="logo-section">
            <div class="brand-icon">ED</div>
            <h4>Ecomm Dept</h4>
        </div>

        <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>

        <div class="nav-area">
            <a href="{{ route('dashboard') }}">Dashboard</a>
        </div>

        <div class="actions">
            <button onclick="openCmdPalette()" title="Search (Ctrl+K)"><i class="fas fa-search"></i></button>
            @if(Auth::check())
            <div class="nav-user-badge">
                <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ Auth::user()->gender === 'female' ? Auth::user()->username . 'Female' : Auth::user()->username }}" alt="" class="nav-avatar">
                <div class="nav-user-info">
                    <span class="nav-user-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    <span class="nav-user-role">{{ ucfirst(Auth::user()->role) }}</span>
                </div>
            </div>
            @endif
        </div>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @yield('content')

    <!-- Command Palette -->
    <div class="cmd-overlay" id="cmdOverlay">
        <div class="cmd-palette">
            <div class="cmd-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" class="cmd-input" id="cmdInput" placeholder="Search or jump to..." autocomplete="off">
            </div>
            <div class="cmd-results" id="cmdResults"></div>
            <div class="cmd-footer">
                <span><kbd>↑</kbd><kbd>↓</kbd> Navigate</span>
                <span><kbd>↵</kbd> Open</span>
                <span><kbd>esc</kbd> Close</span>
            </div>
        </div>
    </div>

    <script>
    function openCmdPalette() {
        var overlay = document.getElementById('cmdOverlay');
        overlay.classList.add('open');
        var inp = document.getElementById('cmdInput');
        inp.value = '';
        if (window._cmdRender) window._cmdRender('');
        inp.focus();
    }

    (function() {
        var sidebar = document.querySelector('.sidebar');
        var toggle = document.getElementById('mobileToggle');
        var overlay = document.getElementById('sidebarOverlay');

        function isMobile() { return window.innerWidth <= 1024; }
        function update() {
            if (!sidebar) { if(toggle) toggle.style.display = 'none'; return; }
            if (isMobile()) {
                if(toggle) toggle.style.display = 'flex';
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            } else {
                if(toggle) toggle.style.display = 'none';
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        }
        if(toggle) toggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
        if(overlay) overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
        if (sidebar) {
            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (isMobile()) { sidebar.classList.remove('open'); overlay.classList.remove('show'); }
                });
            });
        }
        update();
        window.addEventListener('resize', update);
    })();

    (function() {
        var overlay = document.getElementById('cmdOverlay');
        var input = document.getElementById('cmdInput');
        var results = document.getElementById('cmdResults');
        var activeIndex = -1;
        var flatList = [];
        var isAdmin = window.location.pathname.startsWith('/admin');

        var userPages = [
            { name: 'Dashboard', desc: 'Overview', icon: 'fa-grip', url: '{{ route("dashboard") }}' },
            @if(Auth::check() && Auth::user()->role === 'content')
            { name: 'Posting Procedure', desc: 'Product posting guide', icon: 'fa-list-check', url: '{{ route("posting-procedure") }}' },
            { name: 'Data Gathering', desc: 'Collect product info', icon: 'fa-folder-open', url: '{{ route("data-gathering") }}' },
            { name: 'E-commerce Requirements', desc: 'Platform rules', icon: 'fa-clipboard-list', url: '{{ route("ecommerce-requirements") }}' },
            @endif
            { name: 'Price Calculator', desc: 'Compute SRP', icon: 'fa-calculator', url: '{{ route("price-calculator") }}' },
            { name: 'End-of-Day Report', desc: 'Log daily tasks', icon: 'fa-calendar-check', url: '{{ route("end-of-day") }}' },
            { name: 'Important Links', desc: 'Quick access', icon: 'fa-link', url: '{{ route("important-links") }}' },
            { name: 'The Team', desc: 'Team directory', icon: 'fa-users', url: '{{ route("team") }}' },
            { name: 'Brand Catalogs', desc: 'Browse brand catalogs', icon: 'fa-book-open', url: '{{ route("brand-catalogs") }}' }
        ];

        var adminPages = [
            { name: 'Admin Dashboard', desc: 'Overview', icon: 'fa-grip', url: '{{ route("admin.dashboard") }}' },
            { name: 'Manage Users', desc: 'User management', icon: 'fa-users', url: '{{ route("admin.users") }}' },
            { name: 'Daily Logs', desc: 'Team activity', icon: 'fa-clipboard-list', url: '{{ route("admin.daily-logs") }}' },
            { name: 'Brands', desc: 'Manage brands', icon: 'fa-tag', url: '{{ route("admin.brands") }}' },
            { name: 'User Dashboard', desc: 'Switch view', icon: 'fa-arrow-right-from-bracket', url: '{{ route("dashboard") }}' }
        ];

        var pages = isAdmin ? adminPages : userPages;

        function render(query) {
            var q = (query || '').toLowerCase();
            var filtered = pages.filter(function(p) { return p.name.toLowerCase().indexOf(q) !== -1 || p.desc.toLowerCase().indexOf(q) !== -1; });
            flatList = [];
            if (filtered.length === 0) { results.innerHTML = '<div style="text-align:center;padding:32px;color:var(--muted-foreground);font-size:14px;">No results</div>'; return; }
            var html = '<div class="cmd-group-label">Pages</div>';
            filtered.forEach(function(p, i) {
                flatList.push(p);
                html += '<a href="' + p.url + '" class="cmd-item" data-idx="' + i + '">';
                html += '<div class="ci-icon"><i class="fas ' + p.icon + '"></i></div>';
                html += '<div style="flex:1;"><div class="ci-name">' + p.name + '</div><div class="ci-desc">' + p.desc + '</div></div>';
                html += '</a>';
            });
            results.innerHTML = html;
        }
        window._cmdRender = render;

        function closePalette() { overlay.classList.remove('open'); input.value = ''; }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); if (overlay.classList.contains('open')) closePalette(); else openCmdPalette(); }
            if (e.key === 'Escape' && overlay.classList.contains('open')) closePalette();
            if (overlay.classList.contains('open') && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
                e.preventDefault();
                var items = results.querySelectorAll('.cmd-item');
                if (items.length === 0) return;
                if (e.key === 'ArrowDown') activeIndex = Math.min(activeIndex + 1, items.length - 1);
                else activeIndex = Math.max(activeIndex - 1, 0);
                items.forEach(function(item) { item.classList.remove('active'); });
                items[activeIndex].classList.add('active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
            if (e.key === 'Enter' && overlay.classList.contains('open')) {
                e.preventDefault();
                var items = results.querySelectorAll('.cmd-item');
                if (items.length > 0 && activeIndex >= 0) window.location.href = flatList[activeIndex].url;
            }
        });

        overlay.addEventListener('click', function(e) { if (e.target === overlay) closePalette(); });
        input.addEventListener('input', function() { activeIndex = -1; render(this.value); });

        document.querySelectorAll('.sidebar').forEach(function(sb) {
            var brand = sb.querySelector('.sidebar-brand');
            if (brand) {
                var trigger = document.createElement('div');
                trigger.style.cssText = 'padding: 8px 8px 4px;';
                var btn = document.createElement('button');
                btn.style.cssText = 'display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius);background:var(--card);color:var(--muted-foreground);font-family:Inter,sans-serif;font-weight:500;font-size:14px;cursor:pointer;transition:all 0.15s;';
                btn.innerHTML = '<i class="fas fa-search" style="font-size:12px;"></i> Search <kbd style="margin-left:auto;background:var(--muted);border:1px solid var(--border);border-radius:4px;padding:2px 6px;font-size:10px;color:var(--muted-foreground);font-family:ui-monospace,monospace;">Ctrl+K</kbd>';
                btn.addEventListener('click', function() { openCmdPalette(); });
                btn.addEventListener('mouseenter', function() { this.style.background = 'var(--secondary)'; });
                btn.addEventListener('mouseleave', function() { this.style.background = 'var(--card)'; });
                trigger.appendChild(btn);
                brand.insertAdjacentElement('afterend', trigger);
            }
        });
    })();
    </script>

    <script>
    function openModal(id) {
        var el = document.getElementById(id);
        if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(id) {
        var el = document.getElementById(id);
        if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
    }
    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeModal(overlay.id); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(function(m) { closeModal(m.id); });
    });

    var _confirmCb = null;
    function showConfirm(title, message, label, onConfirm) {
        document.getElementById('confirmTitle').textContent   = title;
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmActionBtn').textContent = label || 'Confirm';
        _confirmCb = onConfirm;
        openModal('confirmModal');
    }
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        closeModal('confirmModal');
        if (_confirmCb) { _confirmCb(); _confirmCb = null; }
    });
    </script>

    <!-- Global Confirm Dialog -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box" style="max-width: 380px;">
            <div class="confirm-icon-wrap">
                <div class="confirm-icon"><i class="fas fa-triangle-exclamation"></i></div>
            </div>
            <div class="modal-body" style="text-align: center; padding-top: 0.25rem;">
                <div id="confirmTitle" style="font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem;"></div>
                <div id="confirmMessage" style="font-size: 0.85rem; color: var(--muted-foreground); line-height: 1.55;"></div>
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 0.5rem; padding-top: 1.25rem;">
                <button class="btn-flat-secondary" onclick="closeModal('confirmModal')" style="height: 38px; min-width: 90px; font-size: 0.85rem;">Cancel</button>
                <button id="confirmActionBtn" class="btn-destructive" style="height: 38px; min-width: 90px;">Delete</button>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
