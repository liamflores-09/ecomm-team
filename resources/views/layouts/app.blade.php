<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecomm Dept')</title>
    @yield('favicon')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #FFFFFF;
            --fg: #111827;
            --primary: #3B82F6;
            --primary-hover: #2563EB;
            --secondary: #10B981;
            --accent: #F59E0B;
            --muted: #F3F4F6;
            --border: #E5E7EB;
            --gray-200: #D1D5DB;
            --gray-300: #9CA3AF;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--fg);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ====== FLAT DESIGN TOKENS ====== */

        /* Headings: Bold 700/800, tight tracking */
        h1, h2, h3, h4, h5, h6 {
            letter-spacing: -0.02em;
            font-weight: 800;
            color: var(--fg);
            line-height: 1.1;
        }

        /* ====== ANIMATIONS ====== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .anim-up { animation: fadeInUp 0.5s ease-out both; }
        .anim-fade { animation: fadeIn 0.4s ease-out both; }
        .d1 { animation-delay: 0.05s; }
        .d2 { animation-delay: 0.1s; }
        .d3 { animation-delay: 0.15s; }
        .d4 { animation-delay: 0.2s; }
        .d5 { animation-delay: 0.25s; }

        /* ====== SIDEBAR ====== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: var(--fg);
            padding: 2rem 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 0 1.5rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand .brand-icon {
            width: 44px;
            height: 44px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .sidebar-brand h5 {
            font-weight: 800;
            color: var(--white);
            margin: 0;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
        }

        .sidebar-brand span {
            font-size: 0.75rem;
            color: var(--gray-300);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            flex: 1;
        }

        .sidebar-nav li {
            margin: 0.125rem 0.75rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray-300);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .sidebar-nav a:hover {
            background: var(--gray-700);
            color: var(--white);
        }

        .sidebar-nav a.active {
            background: var(--primary);
            color: var(--white);
        }

        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-footer {
            padding: 0 0.75rem;
        }

        .sidebar-footer .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--gray-700);
            border-radius: 6px;
            background: transparent;
            color: var(--gray-300);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sidebar-footer .btn-logout:hover {
            background: #DC2626;
            border-color: #DC2626;
            color: var(--white);
            transform: scale(1.02);
        }

        /* ====== CMD TRIGGER ====== */
        .cmd-trigger {
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
        }

        .cmd-trigger-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 2px solid var(--gray-600);
            border-radius: 6px;
            background: var(--gray-700);
            color: var(--gray-300);
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.15s;
        }

        .cmd-trigger-btn:hover {
            border-color: var(--primary);
            color: var(--white);
            background: var(--gray-600);
        }

        .cmd-trigger-btn i { font-size: 0.75rem; }

        .cmd-trigger-btn kbd {
            margin-left: auto;
            font-size: 0.55rem;
            background: var(--gray-600);
            border: 1px solid var(--gray-500);
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
            color: var(--gray-400);
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        /* ====== MAIN ====== */
        .main-content {
            margin-left: 280px;
            padding: 2rem 2.5rem;
            min-height: 100vh;
            background: var(--muted);
        }

        /* ====== TOP BAR ====== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .top-bar h2 {
            font-size: 1.75rem;
            margin-bottom: 0.125rem;
        }

        .top-bar p {
            color: var(--gray-500);
            font-weight: 500;
            font-size: 0.9rem;
            margin: 0;
        }

        .top-bar .highlight {
            color: var(--primary);
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            background: var(--white);
            padding: 0.5rem 1rem 0.5rem 0.5rem;
            border-radius: 8px;
        }

        .user-badge .avatar {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .user-badge .avatar.admin-av {
            background: #DC2626;
        }

        .user-badge .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-badge .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .user-badge .role-tag {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--primary);
        }

        .user-badge .role-tag.admin-role {
            color: #DC2626;
        }

        /* ====== SECTION HEADER ====== */
        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .section-header p {
            color: var(--gray-500);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* ====== CARDS (Flat Color Block) ====== */
        .flat-card {
            background: var(--white);
            border-radius: 8px;
            padding: 2rem;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .flat-card:hover {
            transform: scale(1.02);
        }

        .flat-card .card-icon {
            width: 56px;
            height: 56px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
            transition: transform 0.2s;
        }

        .flat-card:hover .card-icon {
            transform: scale(1.1);
        }

        .flat-card.card-green .card-icon { background: var(--secondary); }
        .flat-card.card-amber .card-icon { background: var(--accent); }
        .flat-card.card-muted .card-icon { background: var(--gray-200); color: var(--fg); }

        .flat-card h5 {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .flat-card p {
            color: var(--gray-500);
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            margin: 0;
        }

        .flat-card .arrow-icon {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 32px;
            height: 32px;
            background: var(--muted);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            transition: all 0.2s;
        }

        .flat-card:hover .arrow-icon {
            background: var(--primary);
            color: var(--white);
        }

        .flat-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .flat-card.disabled:hover {
            transform: none;
        }

        .flat-card.disabled .arrow-icon i::before {
            content: "\1f512";
        }

        /* ====== BUTTONS (Flat) ====== */
        .btn-flat-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            height: 52px;
            padding: 0 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-flat-primary:hover {
            background: var(--primary-hover);
            transform: scale(1.05);
        }

        .btn-flat-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            height: 52px;
            padding: 0 1.5rem;
            background: var(--muted);
            color: var(--fg);
            border: none;
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-flat-secondary:hover {
            background: var(--gray-200);
            transform: scale(1.05);
        }

        .btn-flat-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            height: 52px;
            padding: 0 1.5rem;
            background: transparent;
            color: var(--primary);
            border: 4px solid var(--primary);
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-flat-outline:hover {
            background: var(--primary);
            color: var(--white);
            transform: scale(1.05);
        }

        /* ====== FORM INPUTS (Flat) ====== */
        .input-flat {
            width: 100%;
            height: 52px;
            padding: 0 1rem;
            background: var(--muted);
            border: 2px solid transparent;
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            color: var(--fg);
            outline: none;
            transition: all 0.2s;
        }

        .input-flat:focus {
            background: var(--white);
            border-color: var(--primary);
        }

        .input-flat::placeholder {
            color: var(--gray-300);
        }

        .label-flat {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        /* ====== TAG / BADGE (Flat) ====== */
        .tag {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .tag-admin { background: #FEE2E2; color: #DC2626; }
        .tag-user { background: #D1FAE5; color: #059669; }
        .tag-primary { background: #DBEAFE; color: #2563EB; }

        /* ====== TABLE (Flat) ====== */
        .table-flat {
            width: 100%;
            border-collapse: collapse;
        }

        .table-flat thead th {
            background: var(--muted);
            padding: 0.875rem 1rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            border: none;
        }

        .table-flat thead th:first-child { border-radius: 6px 0 0 6px; }
        .table-flat thead th:last-child { border-radius: 0 6px 6px 0; }

        .table-flat tbody td {
            padding: 0.875rem 1rem;
            border-top: 2px solid var(--muted);
            font-weight: 500;
            vertical-align: middle;
        }

        .table-flat tbody tr {
            background: var(--white);
            transition: all 0.2s;
        }

        .table-flat tbody tr:hover {
            background: var(--muted);
        }

        /* ====== ALERTS ====== */
        .alert-flat {
            padding: 1rem 1.25rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-flat.success { background: #D1FAE5; color: #065F46; }
        .alert-flat.danger { background: #FEE2E2; color: #991B1B; }

        /* ====== MODAL (Flat) ====== */
        .modal-content {
            background: var(--white);
            border: none;
            border-radius: 8px;
        }

        .modal-header {
            border-bottom: 2px solid var(--muted);
            padding: 1.25rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 2px solid var(--muted);
            padding: 1rem 1.5rem;
        }

        /* ====== BACK LINK ====== */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-500);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
            transform: translateX(-4px);
        }

        /* ====== SCROLLBAR ====== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--muted); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 3px; }

        /* ====== COMMAND PALETTE ====== */
        .cmd-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 9999;
            align-items: flex-start;
            justify-content: center;
            padding-top: 15vh;
        }

        .cmd-overlay.open {
            display: flex;
            animation: fadeIn 0.15s ease-out;
        }

        .cmd-palette {
            width: 100%;
            max-width: 520px;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            animation: fadeInUp 0.15s ease-out;
        }

        .cmd-input-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.25rem;
            border-bottom: 2px solid var(--muted);
        }

        .cmd-input-wrap i {
            color: var(--gray-400);
            font-size: 1rem;
        }

        .cmd-input {
            flex: 1;
            height: 52px;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--fg);
        }

        .cmd-input::placeholder { color: var(--gray-300); }

        .cmd-kbd {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .cmd-kbd kbd {
            background: var(--muted);
            border: 2px solid var(--border);
            border-radius: 4px;
            padding: 0.15rem 0.375rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--gray-500);
        }

        .cmd-results {
            max-height: 320px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .cmd-group-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-400);
            padding: 0.5rem 0.75rem 0.25rem;
        }

        .cmd-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.1s;
            text-decoration: none;
            color: var(--fg);
        }

        .cmd-item:hover, .cmd-item.active {
            background: var(--primary);
            color: white;
        }

        .cmd-item .ci-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--muted);
            color: var(--gray-500);
            font-size: 0.8rem;
            flex-shrink: 0;
            transition: all 0.1s;
        }

        .cmd-item:hover .ci-icon, .cmd-item.active .ci-icon {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .cmd-item .ci-text {
            flex: 1;
        }

        .cmd-item .ci-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .cmd-item .ci-desc {
            font-size: 0.7rem;
            font-weight: 500;
            opacity: 0.6;
        }

        .cmd-item .ci-arrow {
            font-size: 0.7rem;
            opacity: 0;
            transition: all 0.1s;
        }

        .cmd-item:hover .ci-arrow, .cmd-item.active .ci-arrow {
            opacity: 1;
        }

        .cmd-footer {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.625rem 1rem;
            border-top: 2px solid var(--muted);
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--gray-400);
        }

        .cmd-footer .cmd-hint {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .cmd-footer kbd {
            background: var(--muted);
            border: 1px solid var(--border);
            border-radius: 3px;
            padding: 0.1rem 0.3rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.6rem;
            font-weight: 700;
        }

        .cmd-empty {
            text-align: center;
            padding: 2rem;
            color: var(--gray-400);
            font-weight: 500;
            font-size: 0.85rem;
        }

        .cmd-empty i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.5rem;
            color: var(--gray-300);
        }
    </style>
    @yield('styles')
</head>
<body>
    @yield('content')

    <!-- Command Palette -->
    <div class="cmd-overlay" id="cmdOverlay">
        <div class="cmd-palette">
            <div class="cmd-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" class="cmd-input" id="cmdInput" placeholder="Type a command or search..." autocomplete="off">
                <div class="cmd-kbd">
                    <kbd>ESC</kbd>
                </div>
            </div>
            <div class="cmd-results" id="cmdResults"></div>
            <div class="cmd-footer">
                <div class="cmd-hint"><kbd>&uarr;</kbd><kbd>&darr;</kbd> Navigate</div>
                <div class="cmd-hint"><kbd>&crarr;</kbd> Open</div>
                <div class="cmd-hint"><kbd>ESC</kbd> Close</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function() {
        var overlay = document.getElementById('cmdOverlay');
        var input = document.getElementById('cmdInput');
        var results = document.getElementById('cmdResults');
        var activeIndex = -1;
        var flatList = [];

        var pages = [
            { name: 'Dashboard', desc: 'Overview of your training system', icon: 'fa-grip', url: '{{ route("dashboard") }}' },
            { name: 'Posting Procedure', desc: '8-step guide for product posting', icon: 'fa-list-check', url: '{{ route("posting-procedure") }}' },
            { name: 'Data Gathering', desc: 'Collect product info and assets', icon: 'fa-folder-open', url: '{{ route("data-gathering") }}' },
            { name: 'E-commerce Requirements', desc: 'Platform-specific posting rules', icon: 'fa-clipboard-list', url: '{{ route("ecommerce-requirements") }}' },
            { name: 'Price Calculator', desc: 'Compute SRP across platforms', icon: 'fa-calculator', url: '{{ route("price-calculator") }}' },
            { name: 'End-of-Day Report', desc: 'Daily activity logging guidelines', icon: 'fa-calendar-check', url: '{{ route("end-of-day") }}' },
            { name: 'Important Links', desc: 'Quick access to resources', icon: 'fa-link', url: '{{ route("important-links") }}' },
            { name: 'The Team', desc: 'Meet the people behind Ecomm Dept', icon: 'fa-users', url: '{{ route("team") }}' }
        ];

        function render(query) {
            var q = (query || '').toLowerCase();
            var filtered = pages.filter(function(p) {
                return p.name.toLowerCase().indexOf(q) !== -1 || p.desc.toLowerCase().indexOf(q) !== -1;
            });

            flatList = [];
            if (filtered.length === 0) {
                results.innerHTML = '<div class="cmd-empty"><i class="fas fa-search"></i>No results found</div>';
                return;
            }

            var html = '<div class="cmd-group-label">Pages</div>';
            filtered.forEach(function(p, i) {
                flatList.push(p);
                html += '<a href="' + p.url + '" class="cmd-item" data-idx="' + i + '">';
                html += '<div class="ci-icon"><i class="fas ' + p.icon + '"></i></div>';
                html += '<div class="ci-text"><div class="ci-name">' + p.name + '</div><div class="ci-desc">' + p.desc + '</div></div>';
                html += '<div class="ci-arrow"><i class="fas fa-arrow-right"></i></div>';
                html += '</a>';
            });
            results.innerHTML = html;
        }

        function openPalette() {
            overlay.classList.add('open');
            input.value = '';
            activeIndex = -1;
            render('');
            setTimeout(function() { input.focus(); }, 100);
        }

        function closePalette() {
            overlay.classList.remove('open');
            input.value = '';
        }

        // Keyboard shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if (overlay.classList.contains('open')) closePalette();
                else openPalette();
            }
            if (e.key === 'Escape' && overlay.classList.contains('open')) {
                closePalette();
            }
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
                if (items.length > 0 && activeIndex >= 0) {
                    window.location.href = flatList[activeIndex].url;
                }
            }
        });

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closePalette();
        });

        input.addEventListener('input', function() {
            activeIndex = -1;
            render(this.value);
        });

        // Inject search trigger into each sidebar
        document.querySelectorAll('.sidebar').forEach(function(sidebar) {
            var brand = sidebar.querySelector('.sidebar-brand');
            if (brand) {
                var trigger = document.createElement('div');
                trigger.className = 'cmd-trigger';
                var btn = document.createElement('button');
                btn.className = 'cmd-trigger-btn';
                btn.innerHTML = '<i class="fas fa-search"></i> Search<kbd>Ctrl+K</kbd>';
                btn.addEventListener('click', function() { openPalette(); });
                trigger.appendChild(btn);
                brand.insertAdjacentElement('afterend', trigger);
            }
        });
    })();
    </script>

    @yield('scripts')
</body>
</html>
