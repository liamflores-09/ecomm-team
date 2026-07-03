<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();</script>
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
            --indigo: #6366f1;
            --emerald: #10b981;
            --sky: #0ea5e9;
            --amber: #f59e0b;
            --rose: #f43f5e;
            --violet: #7c3aed;
        }

        [data-theme="dark"] {
            --background: #111111;
            --foreground: #ebebeb;
            --card: #1c1c1c;
            --card-foreground: #ebebeb;
            --muted: #252525;
            --muted-foreground: #999999;
            --border: #2e2e2e;
            --border-light: #282828;
            --border-strong: #3e3e3e;
            --secondary: #252525;
            --secondary-foreground: #ebebeb;
            --accent: #252525;
            --accent-foreground: #ebebeb;
            --sidebar: #161616;
            --sidebar-foreground: #ebebeb;
            --sidebar-accent: #252525;
            --sidebar-accent-foreground: #ebebeb;
            --sidebar-border: #282828;
            --popover: #1c1c1c;
            --popover-foreground: #ebebeb;
            --white: #1c1c1c;
            --bg: #111111;
            --bg-card: #1c1c1c;
            --fg: #ebebeb;
            --fg-secondary: #999999;
            --fg-tertiary: #666666;
            --hover: #252525;
            --gray-200: #282828;
            --gray-300: #333333;
            --gray-400: #666666;
            --gray-500: #999999;
            --gray-600: #cccccc;
            --gray-700: #e0e0e0;
        }
        [data-theme="dark"] .notif-panel,
        [data-theme="dark"] .user-menu-dropdown { box-shadow: 0 4px 24px rgba(0,0,0,0.5); }
        [data-theme="dark"] .alert.success, [data-theme="dark"] .alert-flat.success { background: #052e16; color: #86efac; border-color: #166534; }
        [data-theme="dark"] .alert.danger,  [data-theme="dark"] .alert-flat.danger  { background: #2d0a0a; color: #fca5a5; border-color: #7f1d1d; }
        [data-theme="dark"] .cmd-overlay { background: rgba(0,0,0,0.7); }

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
        .sidebar-nav a i { width: 16px; text-align: center; font-size: 13px; flex-shrink: 0; }

        .dropdown-nav { list-style: none; padding: 0; }
        .dropdown-nav li { margin: 2px 0; }
        .dropdown-nav a {
            display: block; padding: 6px 12px 6px 42px; color: var(--muted-foreground);
            text-decoration: none; border-radius: var(--radius); font-weight: 500; font-size: 13px;
            transition: all 0.15s;
        }
        .dropdown-nav a:hover { background: var(--sidebar-accent); color: var(--sidebar-accent-foreground); }
        .dropdown-nav a.active { background: var(--primary); color: var(--primary-foreground); font-weight: 600; }

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
            justify-content: center;
            border-right: 1px solid var(--border);
            flex-shrink: 0;
        }
        .nav-clock { display: flex; flex-direction: column; align-items: center; gap: 2px; }
        .nav-clock-time {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--foreground);
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }
        .nav-clock-date {
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--muted-foreground);
            letter-spacing: 0.02em;
        }
        .top-header .nav-area { flex: 1; }
        .top-header .actions { display: flex; align-items: center; gap: 6px; margin-left: auto; }
        .top-header .actions > button {
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
        .top-header .actions > button:hover { background: var(--secondary); color: var(--foreground); }
        .notif-wrap, .user-menu-wrap { display: flex; align-items: center; }

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
        /* Custom Select */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: var(--muted);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 0 2.25rem 0 0.875rem;
            height: 40px;
            font-family: var(--p-font-family-sans);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--fg);
            cursor: pointer;
            transition: all 0.15s;
            width: 100%;
        }
        select:hover  { border-color: var(--primary); }
        select:focus  { outline: none; border-color: var(--primary); background-color: var(--white); }
        select option { padding: 8px 12px; font-weight: 500; }

        /* Animations */
        /* App Dropdown (x-select component) */
        .app-dd { position: relative; }
        .app-dd .dd-trigger {
            display: flex; align-items: center; gap: 0.5rem; width: 100%; height: 40px;
            padding: 0 0.75rem; background: var(--muted); border: 2px solid transparent;
            border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.875rem;
            font-weight: 500; color: var(--fg); cursor: pointer; transition: all 0.15s; user-select: none;
        }
        .app-dd .dd-trigger:hover,
        .app-dd.open .dd-trigger { border-color: var(--primary); background: var(--white); }
        .app-dd .dd-arrow { margin-left: auto; font-size: 0.6rem; color: var(--gray-400); transition: transform 0.2s; flex-shrink: 0; }
        .app-dd.open .dd-arrow { transform: rotate(180deg); }
        .app-dd .dd-menu {
            display: none; position: absolute; top: calc(100% + 4px); left: 0; min-width: 100%;
            background: var(--white); border: 2px solid var(--border); border-radius: 8px;
            z-index: 200; max-height: 240px; overflow-y: auto; padding: 0.25rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .app-dd.open .dd-menu { display: block; animation: fadeInUp 0.15s ease-out; }
        .app-dd .dd-item {
            padding: 0.5rem 0.625rem; border-radius: 6px; font-family: var(--p-font-family-sans);
            font-size: 0.875rem; font-weight: 500; color: var(--fg); cursor: pointer; transition: background 0.1s;
        }
        .app-dd .dd-item:hover { background: var(--muted); }
        .app-dd .dd-item.selected { background: var(--primary); color: white; }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .anim-up { animation: fadeInUp 0.25s ease-out backwards; }
        .anim-fade { animation: fadeIn 0.25s ease-out both; }
        .d1 { animation-delay: 0.03s; } .d2 { animation-delay: 0.06s; } .d3 { animation-delay: 0.09s; }
        .d4 { animation-delay: 0.12s; } .d5 { animation-delay: 0.15s; }

        /* Shared: Role Badges */
        .role-badge {
            display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px;
            font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .role-badge.head       { background: #7c3aed; color: #fff; }
        .role-badge.manager    { background: #1e293b; color: #fff; }
        .role-badge.analyst    { background: #ec4899; color: #fff; }
        .role-badge.content    { background: var(--sky);     color: #fff; }
        .role-badge.graphics   { background: var(--amber);   color: #fff; }
        .role-badge.backend    { background: var(--rose);    color: #fff; }
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
        .cmd-overlay { display: flex; position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; align-items: flex-start; justify-content: center; padding-top: 15vh; opacity: 0; pointer-events: none; transition: opacity 0.2s ease; }
        .cmd-overlay.open { opacity: 1; pointer-events: all; }
        .cmd-palette { width: 100%; max-width: 520px; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; transform: translateY(-10px); transition: transform 0.22s cubic-bezier(0.16,1,0.3,1), opacity 0.22s ease; opacity: 0; }
        .cmd-overlay.open .cmd-palette { transform: translateY(0); opacity: 1; }
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

        /* Notification Bell */
        .notif-wrap { position: relative; }
        .notif-btn { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: transparent; border: none; border-radius: var(--radius); color: var(--muted-foreground); font-size: 16px; cursor: pointer; transition: all 0.15s; position: relative; }
        .notif-btn:hover { background: var(--secondary); color: var(--foreground); }
        .notif-badge { position: absolute; top: 5px; right: 5px; min-width: 16px; height: 16px; border-radius: 9999px; background: var(--destructive); color: white; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; padding: 0 3px; border: 2px solid var(--card); pointer-events: none; }
        .notif-panel { display: none; position: absolute; top: calc(100% + 8px); right: 0; width: 340px; background: var(--card); border: 1px solid var(--border-light); border-radius: 10px; z-index: 500; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .notif-panel.open { display: block; animation: fadeInUp 0.15s ease-out both; }
        .notif-panel-header { display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; border-bottom: 1px solid var(--border-light); }
        .notif-panel-title { font-size: 0.85rem; font-weight: 700; color: var(--foreground); }
        .notif-clear-btn { font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground); background: none; border: none; cursor: pointer; padding: 0; transition: color 0.15s; }
        .notif-clear-btn:hover { color: var(--destructive); }
        .notif-list { max-height: 360px; overflow-y: auto; }
        .notif-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.875rem 1rem; border-bottom: 1px solid var(--border-light); position: relative; transition: background 0.15s; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--muted); }
        .notif-item.unread::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--primary); border-radius: 0 2px 2px 0; }
        .notif-icon-box { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: white; flex-shrink: 0; }
        .notif-icon-box.primary { background: var(--primary); }
        .notif-icon-box.success { background: var(--success); }
        .notif-icon-box.warning { background: #f59e0b; }
        .notif-body { flex: 1; min-width: 0; text-decoration: none; color: inherit; }
        .notif-title { font-size: 0.8rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.1rem; }
        .notif-msg { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; line-height: 1.4; margin-bottom: 0.2rem; }
        .notif-time { font-size: 0.68rem; color: var(--muted-foreground); }
        .notif-remove { width: 22px; height: 22px; border: none; background: none; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); font-size: 0.68rem; flex-shrink: 0; opacity: 0; transition: opacity 0.15s, color 0.15s; }
        .notif-item:hover .notif-remove { opacity: 1; }
        .notif-remove:hover { color: var(--destructive); }
        .notif-empty { text-align: center; padding: 2.5rem 1rem; color: var(--muted-foreground); font-size: 0.82rem; }
        .notif-empty i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--border-light); }

        /* User Menu Dropdown */
        .user-menu-wrap { position: relative; }
        .user-menu-btn { display: flex; align-items: center; gap: 8px; padding: 4px 8px; border-radius: var(--radius); border: none; background: transparent; cursor: pointer; transition: background 0.15s; }
        .user-menu-btn:hover { background: var(--secondary); }
        .user-menu-chevron { font-size: 10px; color: var(--muted-foreground); transition: transform 0.2s; margin-left: 2px; }
        .user-menu-wrap.open .user-menu-chevron { transform: rotate(180deg); }
        .user-menu-dropdown { display: none; position: absolute; top: calc(100% + 8px); right: 0; width: 220px; background: var(--card); border: 1px solid var(--border-light); border-radius: 10px; z-index: 500; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .user-menu-dropdown.open { display: block; animation: fadeInUp 0.15s ease-out both; }
        .user-menu-hd { padding: 0.875rem 1rem; border-bottom: 1px solid var(--border-light); }
        .user-menu-hd-name { font-size: 0.85rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.15rem; }
        .user-menu-hd-role { font-size: 0.72rem; color: var(--muted-foreground); font-weight: 500; }
        .user-menu-items { padding: 0.375rem; }
        .user-menu-item { display: flex; align-items: center; gap: 0.625rem; width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; font-family: var(--p-font-family-sans); font-size: 0.82rem; font-weight: 500; color: var(--foreground); text-decoration: none; background: none; border: none; cursor: pointer; transition: background 0.15s; }
        .user-menu-item:hover { background: var(--secondary); }
        .user-menu-item.danger { color: var(--destructive); }
        .user-menu-item.danger:hover { background: #fef2f2; }
        .user-menu-divider { height: 1px; background: var(--border-light); margin: 0.25rem 0; }
        @media (max-width: 768px) { .nav-user-info { display: none; } .user-menu-chevron { display: none; } }

        /* ── Toast notifications ─────────────────────────────── */
        .app-toast-wrap {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
            display: flex; flex-direction: column; align-items: flex-end;
            gap: 0.5rem; pointer-events: none;
        }
        .app-toast-item {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.75rem 1rem; border-radius: 10px;
            min-width: 240px; max-width: 340px;
            background: var(--card); border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.13);
            font-family: var(--p-font-family-sans); font-size: 0.875rem;
            font-weight: 500; color: var(--fg); pointer-events: all;
            animation: toastIn 0.2s ease-out both;
            transition: opacity 0.2s, transform 0.2s;
        }
        .app-toast-item.t-success { border-left: 3px solid #10b981; }
        .app-toast-item.t-error   { border-left: 3px solid #f43f5e; }
        .app-toast-item.t-warning { border-left: 3px solid #f59e0b; }
        .app-toast-item.t-info    { border-left: 3px solid #0ea5e9; }
        .app-toast-item .t-icon   { font-size: 0.9rem; flex-shrink: 0; }
        .app-toast-item.t-success .t-icon { color: #10b981; }
        .app-toast-item.t-error   .t-icon { color: #f43f5e; }
        .app-toast-item.t-warning .t-icon { color: #f59e0b; }
        .app-toast-item.t-info    .t-icon { color: #0ea5e9; }
        .app-toast-item.t-hiding  { opacity: 0; transform: translateX(14px); }
        @keyframes toastIn { from { opacity: 0; transform: translateX(14px); } to { opacity: 1; transform: translateX(0); } }

        /* Preview mode */
        .preview-banner {
            position: fixed;
            top: 64px; left: 0; right: 0;
            height: 40px;
            background: #fef3c7;
            border-bottom: 1px solid #f59e0b;
            z-index: 45;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px 0 280px;
            font-size: 13px;
            font-weight: 500;
            color: #92400e;
        }
        [data-theme="dark"] .preview-banner {
            background: #1c1100;
            border-bottom-color: #92400e;
            color: #fde68a;
        }
        body.preview-mode .sidebar { top: 104px; height: calc(100vh - 104px); }
        body.preview-mode .main-content { padding-top: 136px; }
        @media (max-width: 1024px) {
            .preview-banner { padding-left: 20px; }
            body.preview-mode .main-content { padding-top: 136px; margin-left: 0; }
        }
        .preview-locked { pointer-events: none; opacity: 0.55; user-select: none; }
    </style>
    @yield('styles')
</head>
<body class="bg-background text-foreground {{ $isPreview ? 'preview-mode' : '' }}">
    <!-- Top Header -->
    <header class="top-header">
        <div class="logo-section">
            <div class="nav-clock">
                <div class="nav-clock-time" id="navClockTime">--:--:--</div>
                <div class="nav-clock-date" id="navClockDate">---</div>
            </div>
        </div>

        <button class="mobile-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>

        <div class="actions">
            @if(Auth::check())
            {{-- Notification Bell --}}
            <div class="notif-wrap" id="notifWrap">
                <button class="notif-btn" id="notifBtn" onclick="toggleNotifPanel()" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                </button>
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-panel-header">
                        <span class="notif-panel-title">Notifications</span>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if(in_array(Auth::user()->role, ['head','manager','analyst']))
                            <button class="notif-clear-btn" onclick="openAnnModal(event)" style="color:var(--primary);display:flex;align-items:center;gap:4px;"><i class="fas fa-plus" style="font-size:0.6rem;"></i> Post</button>
                            <span style="color:var(--border-light);">·</span>
                            @endif
                            <button class="notif-clear-btn" onclick="clearAllNotifs()">Clear all</button>
                        </div>
                    </div>
                    <div class="notif-list" id="notifList">
                        <div class="notif-empty"><i class="fas fa-bell-slash"></i>No notifications yet</div>
                    </div>
                </div>
            </div>

            <button onclick="toggleTheme()" title="Toggle dark mode" id="themeToggleBtn"><i class="fas fa-moon" id="themeToggleIcon"></i></button>
            <button onclick="openCmdPalette()" title="Search (Ctrl+K)"><i class="fas fa-search"></i></button>
            <div style="width:1px;height:20px;background:var(--border-light);flex-shrink:0;margin:0 2px;"></div>

            {{-- User Menu --}}
            <div class="user-menu-wrap" id="userMenuWrap">
                <button class="user-menu-btn" onclick="toggleUserMenu()">
                    <img src="{{ Auth::user()->avatarUrl() }}" alt="" class="nav-avatar" style="object-fit:cover;">
                    <div class="nav-user-info">
                        <span class="nav-user-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                        <span class="nav-user-role">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                    <i class="fas fa-chevron-down user-menu-chevron"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-menu-hd">
                        <div class="user-menu-hd-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        <div class="user-menu-hd-role">{{ ucfirst(Auth::user()->role) }}</div>
                    </div>
                    <div class="user-menu-items">
                        <a href="{{ route('profile') }}" class="user-menu-item"><i class="fas fa-circle-user" style="width:14px;text-align:center;font-size:0.8rem;"></i> View Profile</a>
                        <div class="user-menu-divider"></div>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="user-menu-item"><i class="fas fa-gauge" style="width:14px;text-align:center;font-size:0.8rem;"></i> Admin Dashboard</a>
                        <a href="#" onclick="closeUserMenu();setTimeout(function(){openModal('rolePickerModal');},50);return false;" class="user-menu-item"><i class="fas fa-arrow-right-from-bracket" style="width:14px;text-align:center;font-size:0.8rem;"></i> Member View</a>
                        @else
                        <a href="{{ route('dashboard') }}" class="user-menu-item"><i class="fas fa-grip" style="width:14px;text-align:center;font-size:0.8rem;"></i> Dashboard</a>
                        @endif
                        <div class="user-menu-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="user-menu-item danger"><i class="fas fa-right-from-bracket" style="width:14px;text-align:center;font-size:0.8rem;"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </header>

    @if($isPreview)
    <div class="preview-banner">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-eye" style="color:#f59e0b;font-size:12px;"></i>
            <span>Viewing as:</span>
            <span class="role-badge {{ $previewRole }}">{{ ucfirst($previewRole) }}</span>
            <span style="color:#b45309;font-size:12px;">— read-only, no submissions</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <button onclick="openModal('rolePickerModal')"
                style="height:28px;padding:0 10px;border:1px solid #f59e0b;border-radius:var(--radius);background:transparent;cursor:pointer;font-size:12px;font-weight:600;color:#92400e;font-family:Inter,sans-serif;transition:background 0.15s;"
                onmouseover="this.style.background='rgba(245,158,11,0.1)'" onmouseout="this.style.background='transparent'">
                <i class="fas fa-arrows-rotate" style="font-size:10px;margin-right:4px;"></i>Switch Role
            </button>
            <form method="POST" action="{{ route('admin.preview-role.clear') }}" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="height:28px;padding:0 10px;border:none;border-radius:var(--radius);background:#f59e0b;cursor:pointer;font-size:12px;font-weight:600;color:white;font-family:Inter,sans-serif;transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-arrow-left" style="font-size:10px;margin-right:4px;"></i>Return to Admin
                </button>
            </form>
        </div>
    </div>
    @endif

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
    (function() {
        function updateIcon() {
            var dark = document.documentElement.getAttribute('data-theme') === 'dark';
            var icon = document.getElementById('themeToggleIcon');
            if (icon) icon.className = dark ? 'fas fa-sun' : 'fas fa-moon';
        }
        function toggleTheme() {
            var dark = document.documentElement.getAttribute('data-theme') === 'dark';
            document.documentElement.setAttribute('data-theme', dark ? 'light' : 'dark');
            localStorage.setItem('theme', dark ? 'light' : 'dark');
            updateIcon();
        }
        window.toggleTheme = toggleTheme;
        updateIcon();
    })();
    </script>

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

    @php
        $cmdIsAdmin = !$isPreview && Auth::check() && Auth::user()->isAdmin();
        $cmdRole    = $isPreview ? $previewRole : (Auth::check() ? Auth::user()->role : '');
    @endphp

    (function() {
        var overlay = document.getElementById('cmdOverlay');
        var input = document.getElementById('cmdInput');
        var results = document.getElementById('cmdResults');
        var activeIndex = -1;
        var flatList    = [];
        var isAdmin     = {{ $cmdIsAdmin ? 'true' : 'false' }};

        var adminPages = [
            { name: 'Admin Dashboard', desc: 'Overview',          icon: 'fa-table-cells-large',  url: '{{ route("admin.dashboard") }}' },
            { name: 'Users',           desc: 'User management',   icon: 'fa-user-group',          url: '{{ route("admin.users") }}' },
            { name: 'Daily Logs',      desc: 'Team activity',     icon: 'fa-clock-rotate-left',   url: '{{ route("admin.daily-logs") }}' },
            { name: 'Reports',         desc: 'Role reports',      icon: 'fa-chart-column',        url: '{{ route("admin.reports") }}' },
            { name: 'Brands',          desc: 'Manage brands',     icon: 'fa-layer-group',         url: '{{ route("admin.brands") }}' },
            { name: 'Brand Catalogs',  desc: 'Browse catalogs',   icon: 'fa-book-open',           url: '{{ route("brand-catalogs") }}' },
            { name: 'SKU Tracker',    desc: 'PR to content pipeline', icon: 'fa-box',        url: '{{ route("sku-tracker") }}' },
            { name: 'SLA and Weekly Output', desc: 'Weekly SLA analytics', icon: 'fa-chart-line', url: '{{ route("sla-weekly-output") }}' },
            { name: 'Announcements',   desc: 'Team announcements',icon: 'fa-bullhorn',            url: '{{ route("announcements") }}' },
            { name: 'Calendar',        desc: 'Team calendar',     icon: 'fa-calendar-days',       url: '{{ route("calendar") }}' },
            { name: 'The Team',        desc: 'Team directory',    icon: 'fa-people-group',        url: '{{ route("team") }}' },
        ];

        var memberPages = [
            { name: 'Dashboard',      desc: 'Overview',            icon: 'fa-table-cells-large', url: '{{ route("dashboard") }}' },
            @if($cmdRole !== 'analyst')
            { name: 'EOD Report',     desc: 'Log daily tasks',     icon: 'fa-calendar-check',    url: '{{ route("end-of-day") }}' },
            { name: 'Price Calculator', desc: 'Compute SRP',       icon: 'fa-calculator',        url: '{{ route("price-calculator") }}' },
            { name: 'Important Links',  desc: 'Quick access',      icon: 'fa-bookmark',          url: '{{ route("important-links") }}' },
            { name: 'Calendar',         desc: 'Team calendar',     icon: 'fa-calendar-days',     url: '{{ route("calendar") }}' },
            { name: 'SKU Tracker',    desc: 'PR to content pipeline', icon: 'fa-box',        url: '{{ route("sku-tracker") }}' },
            { name: 'SLA and Weekly Output', desc: 'Weekly SLA analytics', icon: 'fa-chart-line', url: '{{ route("sla-weekly-output") }}' },
            @endif
            @if($cmdRole === 'content')
            { name: 'Posting Procedure',  desc: 'Product posting guide', icon: 'fa-list-check',           url: '{{ route("posting-procedure") }}' },
            { name: 'Requirements',       desc: 'Platform rules',        icon: 'fa-clipboard-list',       url: '{{ route("ecommerce-requirements") }}' },
            { name: 'Data Gathering',     desc: 'Collect product info',  icon: 'fa-magnifying-glass-chart', url: '{{ route("data-gathering") }}' },
            @endif
            { name: 'Brand Catalogs',  desc: 'Browse catalogs',    icon: 'fa-book-open',         url: '{{ route("brand-catalogs") }}' },
            { name: 'Announcements',   desc: 'Team announcements', icon: 'fa-bullhorn',           url: '{{ route("announcements") }}' },
            { name: 'The Team',        desc: 'Team directory',     icon: 'fa-people-group',       url: '{{ route("team") }}' },
        ];

        var actions = [
            { name: 'Toggle Theme',  desc: 'Switch dark/light mode', icon: 'fa-moon',                   color: '#6366f1', fn: 'toggleTheme' },
            { name: 'Profile',       desc: 'Your profile',           icon: 'fa-user',                   color: '#0ea5e9', url: '{{ route("profile") }}' },
            { name: 'Notifications', desc: 'Open notifications',     icon: 'fa-bell',                   color: '#f59e0b', fn: 'openNotifPanel' },
            { name: 'Logout',        desc: 'Sign out',               icon: 'fa-right-from-bracket',     color: '#ef4444', fn: 'submitLogout' },
            @if($cmdIsAdmin && !$isPreview)
            { name: 'Member View',   desc: 'Preview a member role',  icon: 'fa-arrow-right-from-bracket', color: '#10b981', fn: 'openMemberView' },
            @endif
            @if($cmdIsAdmin && !$isPreview)
            { name: 'New Announcement', desc: 'Post an announcement', icon: 'fa-bullhorn',              color: '#f59e0b', url: '{{ route("announcements") }}' },
            @endif
        ];

        function openNotifPanel() { toggleNotifPanel(); }
        function submitLogout()   { document.querySelector('form[action*="logout"]').submit(); }
        @if($cmdIsAdmin && !$isPreview)
        function openMemberView() { openModal('rolePickerModal'); }
        @endif

        function render(query) {
            var q = (query || '').toLowerCase();
            var pages = isAdmin ? adminPages : memberPages;
            var filteredPages   = pages.filter(function(p) { return p.name.toLowerCase().indexOf(q) !== -1 || p.desc.toLowerCase().indexOf(q) !== -1; });
            var filteredActions = actions.filter(function(a) { return a.name.toLowerCase().indexOf(q) !== -1 || a.desc.toLowerCase().indexOf(q) !== -1; });
            flatList = [];

            if (filteredPages.length === 0 && filteredActions.length === 0) {
                var noResult = document.createElement('div');
                noResult.style.cssText = 'text-align:center;padding:32px;color:var(--muted-foreground);font-size:14px;';
                noResult.textContent = 'No results' + (q ? ' for "' + q + '"' : '');
                results.innerHTML = '';
                results.appendChild(noResult);
                return;
            }

            var html = '';

            if (filteredPages.length > 0) {
                html += '<div class="cmd-group-label">Navigation</div>';
                filteredPages.forEach(function(p) {
                    flatList.push(p);
                    html += '<a href="' + p.url + '" class="cmd-item" data-idx="' + (flatList.length - 1) + '">';
                    html += '<div class="ci-icon"><i class="fas ' + p.icon + '"></i></div>';
                    html += '<div style="flex:1;"><div class="ci-name">' + p.name + '</div><div class="ci-desc">' + p.desc + '</div></div>';
                    html += '</a>';
                });
            }

            if (filteredActions.length > 0) {
                html += '<div class="cmd-group-label" style="margin-top:4px;">Actions</div>';
                filteredActions.forEach(function(a) {
                    flatList.push(a);
                    var idx = flatList.length - 1;
                    if (a.fn) {
                        html += '<div class="cmd-item" data-idx="' + idx + '" data-action="' + a.fn + '" style="cursor:pointer;">';
                    } else {
                        html += '<a href="' + a.url + '" class="cmd-item" data-idx="' + idx + '">';
                    }
                    html += '<div class="ci-icon" style="background:' + a.color + ';color:white;"><i class="fas ' + a.icon + '"></i></div>';
                    html += '<div style="flex:1;"><div class="ci-name">' + a.name + '</div><div class="ci-desc">' + a.desc + '</div></div>';
                    html += a.fn ? '</div>' : '</a>';
                });
            }

            results.innerHTML = html;

            results.querySelectorAll('.cmd-item[data-action]').forEach(function(el) {
                el.addEventListener('click', function() {
                    var fn = this.getAttribute('data-action');
                    closePalette();
                    if (window[fn]) window[fn]();
                });
            });
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
                if (activeIndex >= 0 && flatList[activeIndex]) {
                    var item = flatList[activeIndex];
                    closePalette();
                    if (item.fn && window[item.fn]) {
                        window[item.fn]();
                    } else if (item.url) {
                        window.location.href = item.url;
                    }
                }
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
    (function() {
        var days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        function updateClock() {
            var now = new Date();
            var h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            var time = (h < 10 ? '0'+h : h) + ':' + (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s) + ' ' + ampm;
            var date = days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate();
            document.getElementById('navClockTime').textContent = time;
            document.getElementById('navClockDate').textContent = date;
        }
        updateClock();
        setInterval(updateClock, 1000);
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

    function showToast(message, type) {
        var icons = { success: 'fas fa-circle-check', error: 'fas fa-circle-xmark', warning: 'fas fa-triangle-exclamation', info: 'fas fa-circle-info' };
        type = type || 'success';
        var wrap = document.getElementById('appToastWrap');
        var item = document.createElement('div');
        item.className = 'app-toast-item t-' + type;
        item.innerHTML = '<i class="' + (icons[type] || icons.info) + ' t-icon"></i><span>' + message + '</span>';
        wrap.appendChild(item);
        setTimeout(function() {
            item.classList.add('t-hiding');
            setTimeout(function() { item.remove(); }, 220);
        }, 3500);
    }

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

    <!-- Toast container -->
    <div id="appToastWrap" class="app-toast-wrap"></div>

    {{-- Quick Announcement Modal --}}
    @if(Auth::check() && in_array(Auth::user()->role, ['head','manager','analyst']))
    <div id="annQuickOverlay" onclick="closeAnnModal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:8000;"></div>
    <div id="annQuickModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 32px);background:var(--card);border:1px solid var(--border-light);border-radius:12px;z-index:8001;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.12);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.875rem 1.125rem;border-bottom:1px solid var(--border-light);">
            <span style="font-size:0.875rem;font-weight:700;color:var(--foreground);">New Announcement</span>
            <button onclick="closeAnnModal()" style="background:none;border:none;cursor:pointer;color:var(--muted-foreground);font-size:0.9rem;padding:4px;line-height:1;"><i class="fas fa-xmark"></i></button>
        </div>
        <div style="padding:1.125rem;">
            <form id="annQuickForm" method="POST" action="{{ route('announcements.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--muted-foreground);text-transform:uppercase;letter-spacing:0.04em;display:block;margin-bottom:5px;">Title</label>
                        <input type="text" name="title" class="form-input" placeholder="Announcement title" required>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--muted-foreground);text-transform:uppercase;letter-spacing:0.04em;display:block;margin-bottom:5px;">Message</label>
                        <textarea name="body" class="form-textarea" placeholder="Write your announcement…" required style="min-height:90px;resize:vertical;"></textarea>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-end;">
                        <div style="flex:1;">
                            <label style="font-size:0.72rem;font-weight:600;color:var(--muted-foreground);text-transform:uppercase;letter-spacing:0.04em;display:block;margin-bottom:5px;">Expires At <span style="text-transform:none;font-weight:400;letter-spacing:0;color:var(--gray-400);">(optional)</span></label>
                            <input type="datetime-local" name="expires_at" id="annQuickExpires" class="form-input">
                        </div>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;padding-bottom:10px;font-size:0.8rem;color:var(--foreground);white-space:nowrap;">
                            <input type="checkbox" name="pinned" value="1">
                            <i class="fas fa-thumbtack" style="color:#f59e0b;font-size:0.72rem;"></i> Pin
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div style="padding:0.75rem 1.125rem;border-top:1px solid var(--border-light);display:flex;gap:8px;">
            <button type="submit" form="annQuickForm" class="btn-flat-primary" style="flex:1;height:38px;font-size:0.8rem;">Post Announcement</button>
            <button type="button" onclick="closeAnnModal()" class="btn-flat-secondary" style="height:38px;padding:0 0.875rem;font-size:0.8rem;">Cancel</button>
        </div>
    </div>
    @endif

    {{-- Role Picker Modal --}}
    @if(Auth::check() && Auth::user()->isAdmin())
    <div class="modal-overlay" id="rolePickerModal">
        <div class="modal-box" style="max-width:460px;">
            <div class="modal-header">
                <h5><i class="fas fa-eye" style="color:var(--primary);margin-right:6px;font-size:0.9rem;"></i>{{ $isPreview ? 'Switch Preview Role' : 'Preview as Role' }}</h5>
                <button class="modal-close" onclick="closeModal('rolePickerModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.82rem;color:var(--muted-foreground);margin:0 0 14px;">Select a role to preview the member experience. All inputs will be read-only.</p>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                    @foreach(['content' => 'Content', 'researcher' => 'Researcher', 'graphics' => 'Graphics', 'backend' => 'Backend', 'analyst' => 'Analyst'] as $roleKey => $roleLabel)
                    <form method="POST" action="{{ route('admin.preview-role.set') }}" style="margin:0;">
                        @csrf
                        <input type="hidden" name="role" value="{{ $roleKey }}">
                        <button type="submit" style="width:100%;padding:10px 12px;border:1.5px solid {{ ($previewRole ?? '') === $roleKey ? 'var(--primary)' : 'var(--border-light)' }};border-radius:var(--radius);background:{{ ($previewRole ?? '') === $roleKey ? 'var(--primary)' : 'var(--card)' }};cursor:pointer;text-align:left;transition:all 0.15s;color:{{ ($previewRole ?? '') === $roleKey ? 'white' : 'var(--foreground)' }};">
                            <span class="role-badge {{ $roleKey }}">{{ $roleLabel }}</span>
                            <div style="margin-top:5px;font-size:0.78rem;font-weight:500;font-family:Inter,sans-serif;">{{ $roleLabel }} member view</div>
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

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

    @if(Auth::check())
    <script>
    (function() {
        var _notifOpen = false;
        var _notifData = [];
        var _csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function toggleNotifPanel() {
            _notifOpen = !_notifOpen;
            var panel = document.getElementById('notifPanel');
            if (_notifOpen) {
                panel.classList.add('open');
                fetchNotifications(true);
                // close user menu if open
                closeUserMenu();
            } else {
                panel.classList.remove('open');
            }
        }
        window.toggleNotifPanel = toggleNotifPanel;

        function fetchNotifications(markRead) {
            fetch('/notifications')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var badge = document.getElementById('notifBadge');
                    if (data.unread > 0 && !_notifOpen) {
                        badge.textContent = data.unread > 99 ? '99+' : data.unread;
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }
                    _notifData = data.notifications;
                    if (_notifOpen) renderNotifications();
                    if (markRead && data.unread > 0) {
                        fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrf } });
                    }
                })
                .catch(function() {});
        }

        function renderNotifications() {
            var list = document.getElementById('notifList');
            if (!list) return;
            if (_notifData.length === 0) {
                list.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i>No notifications yet</div>';
                return;
            }
            list.innerHTML = _notifData.map(function(n) {
                var color = n.data.color || 'primary';
                var cls = n.read ? '' : ' unread';
                return '<div class="notif-item' + cls + '" id="ni-' + n.id + '">' +
                    '<div class="notif-icon-box ' + color + '"><i class="fas ' + (n.data.icon || 'fa-bell') + '"></i></div>' +
                    '<a class="notif-body" href="' + (n.data.url || '#') + '">' +
                        '<div class="notif-title">' + escHtml(n.data.title) + '</div>' +
                        '<div class="notif-msg">' + escHtml(n.data.message) + '</div>' +
                        '<div class="notif-time">' + n.time + '</div>' +
                    '</a>' +
                    '<button class="notif-remove" onclick="removeNotif(\'' + n.id + '\')" title="Remove"><i class="fas fa-times"></i></button>' +
                    '</div>';
            }).join('');
        }

        function escHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function removeNotif(id) {
            fetch('/notifications/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf } })
                .then(function(r) { return r.json(); })
                .then(function() {
                    _notifData = _notifData.filter(function(n) { return n.id !== id; });
                    renderNotifications();
                });
        }
        window.removeNotif = removeNotif;

        function clearAllNotifs() {
            fetch('/notifications/clear', { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrf } })
                .then(function(r) { return r.json(); })
                .then(function() {
                    _notifData = [];
                    renderNotifications();
                    document.getElementById('notifBadge').style.display = 'none';
                });
        }
        window.clearAllNotifs = clearAllNotifs;

        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            var wrap = document.getElementById('notifWrap');
            if (wrap && !wrap.contains(e.target) && _notifOpen) {
                _notifOpen = false;
                document.getElementById('notifPanel').classList.remove('open');
            }
        });

        // Initial fetch + poll every 30s (only when panel is closed)
        fetchNotifications(false);
        setInterval(function() { if (!_notifOpen) fetchNotifications(false); }, 30000);


        // User Menu
        function toggleUserMenu() {
            var wrap = document.getElementById('userMenuWrap');
            var dd = document.getElementById('userMenuDropdown');
            if (!wrap || !dd) return;
            var isOpen = wrap.classList.contains('open');
            if (isOpen) {
                closeUserMenu();
            } else {
                wrap.classList.add('open');
                dd.classList.add('open');
                // close notif panel if open
                if (_notifOpen) { _notifOpen = false; document.getElementById('notifPanel').classList.remove('open'); }
            }
        }
        window.toggleUserMenu = toggleUserMenu;
        window.closeUserMenu = closeUserMenu;

        function closeUserMenu() {
            var wrap = document.getElementById('userMenuWrap');
            var dd = document.getElementById('userMenuDropdown');
            if (wrap) wrap.classList.remove('open');
            if (dd) dd.classList.remove('open');
        }

        document.addEventListener('click', function(e) {
            var wrap = document.getElementById('userMenuWrap');
            if (wrap && !wrap.contains(e.target) && wrap.classList.contains('open')) {
                closeUserMenu();
            }
        });
    })();
    </script>
    @endif

    <script>
    // Quick Announcement Modal
    function openAnnModal(e) {
        if (e) e.stopPropagation();
        var overlay = document.getElementById('annQuickOverlay');
        var modal   = document.getElementById('annQuickModal');
        if (!modal) return;
        // Default expiry to 7 days from now
        var df = new Date(); df.setDate(df.getDate() + 7);
        var pad = function(n){ return n < 10 ? '0'+n : n; };
        var el = document.getElementById('annQuickExpires');
        if (el) el.value = df.getFullYear()+'-'+pad(df.getMonth()+1)+'-'+pad(df.getDate())+'T'+pad(df.getHours())+':'+pad(df.getMinutes());
        overlay.style.display = 'block';
        modal.style.display   = 'block';
        // Close notif panel if open
        var np = document.getElementById('notifPanel');
        if (np) np.classList.remove('open');
    }
    function closeAnnModal() {
        var overlay = document.getElementById('annQuickOverlay');
        var modal   = document.getElementById('annQuickModal');
        if (overlay) overlay.style.display = 'none';
        if (modal)   modal.style.display   = 'none';
        var form = document.getElementById('annQuickForm');
        if (form) form.reset();
    }
    </script>

    <script>
    // App Dropdown (x-select component)
    function appDdToggle(uid) {
        var dd = document.getElementById(uid);
        var isOpen = dd.classList.contains('open');
        document.querySelectorAll('.app-dd.open').forEach(function(d) { d.classList.remove('open'); });
        if (!isOpen) dd.classList.add('open');
    }
    function appDdSelect(uid, val, label) {
        var dd = document.getElementById(uid);
        dd.classList.remove('open');
        document.getElementById(uid + '-label').textContent = label;
        dd.querySelector('input[type="hidden"]').value = val;
        dd.querySelectorAll('.dd-item').forEach(function(item) {
            item.classList.toggle('selected', item.dataset.value == val);
        });
        var cb = dd.dataset.onchange;
        if (cb) (new Function('value', cb))(val);
    }
    function appDdSetValue(inputId, val) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var dd = input.closest('.app-dd');
        if (!dd) return;
        input.value = val;
        var item = dd.querySelector('.dd-item[data-value="' + val + '"]');
        if (item) {
            document.getElementById(dd.id + '-label').textContent = item.textContent.trim();
            dd.querySelectorAll('.dd-item').forEach(function(i) {
                i.classList.toggle('selected', i.dataset.value == val);
            });
        }
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.app-dd')) {
            document.querySelectorAll('.app-dd.open').forEach(function(d) { d.classList.remove('open'); });
        }
    });
    </script>

    @yield('scripts')
</body>
</html>
