@extends('layouts.app')

@section('title', 'User Manual — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M4 19.5A2.5 2.5 0 016.5 17H20'/><path d='M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z'/></svg>">
@endsection

@section('styles')
<style>
    .um-layout { display: grid; grid-template-columns: 220px 1fr; gap: 1.75rem; align-items: start; }

    .um-nav { position: sticky; top: 96px; background: var(--card); border: 1px solid var(--border-light); border-radius: 10px; padding: 0.75rem; }
    .um-nav a {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.5rem 0.625rem; border-radius: 6px;
        font-size: 0.82rem; font-weight: 500; color: var(--muted-foreground);
        text-decoration: none; transition: all 0.15s;
    }
    .um-nav a i { width: 14px; text-align: center; font-size: 0.75rem; flex-shrink: 0; }
    .um-nav a:hover { background: var(--muted); color: var(--foreground); }
    .um-nav a.active { background: var(--primary); color: white; }

    .um-content { display: flex; flex-direction: column; gap: 1.25rem; }

    .um-section {
        background: var(--card); border: 1px solid var(--border-light); border-radius: 12px;
        padding: 1.5rem; scroll-margin-top: 90px;
    }
    .um-section-hd { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
    .um-icon {
        width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: var(--primary); color: white; font-size: 0.9rem;
    }
    .um-title { font-family: 'Space Grotesk', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--foreground); }
    .um-desc { font-size: 0.85rem; color: var(--muted-foreground); font-weight: 500; line-height: 1.6; margin-bottom: 0.875rem; }

    .um-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; }
    .um-list li { display: flex; align-items: flex-start; gap: 0.625rem; font-size: 0.85rem; font-weight: 500; color: var(--foreground); line-height: 1.55; }
    .um-dot { width: 18px; height: 18px; border-radius: 5px; background: rgba(87,87,248,0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; flex-shrink: 0; margin-top: 2px; }

    .um-call {
        border-radius: 8px; padding: 0.75rem 0.875rem; margin-top: 0.875rem;
        display: flex; align-items: flex-start; gap: 0.625rem;
        font-size: 0.8rem; font-weight: 500; line-height: 1.55; color: var(--foreground);
        background: rgba(87,87,248,0.07); border: 1px solid rgba(87,87,248,0.2);
    }
    .um-call i { color: var(--primary); font-size: 0.8rem; flex-shrink: 0; margin-top: 2px; }

    @media (max-width: 900px) {
        .um-layout { grid-template-columns: 1fr; }
        .um-nav { position: static; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="user-manual" />

<div class="main-content">

    <div class="top-bar anim-up" style="margin-bottom:1.5rem;">
        <div>
            <h2>User <span class="highlight">Manual</span></h2>
            <p>A quick reference for everything in the Ecomm Dept Hub</p>
        </div>
    </div>

    <div class="um-layout anim-up d1">
        <nav class="um-nav" id="umNav">
            <a href="#getting-around"><i class="fas fa-compass"></i> Getting Around</a>
            <a href="#dashboard"><i class="fas fa-table-cells-large"></i> Dashboard</a>
            <a href="#eod"><i class="fas fa-calendar-check"></i> EOD Reports</a>
            <a href="#sku-tracker"><i class="fas fa-box"></i> SKU Tracker</a>
            <a href="#announcements"><i class="fas fa-bullhorn"></i> Announcements</a>
            <a href="#calendar"><i class="fas fa-calendar-days"></i> Calendar</a>
            <a href="#brand-catalogs"><i class="fas fa-book-open"></i> Brand Catalogs</a>
            <a href="#important-links"><i class="fas fa-bookmark"></i> Important Links</a>
            <a href="#price-calculator"><i class="fas fa-calculator"></i> Price Calculator</a>
            <a href="#content-tools"><i class="fas fa-list-check"></i> Content Tools</a>
            <a href="#team"><i class="fas fa-people-group"></i> The Team</a>
            <a href="#profile"><i class="fas fa-circle-user"></i> Profile & Theme</a>
            @if(Auth::user()->isAdmin())
            <a href="#admin"><i class="fas fa-gauge"></i> Admin Dashboard</a>
            @endif
        </nav>

        <div class="um-content">

            <div class="um-section" id="getting-around">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-compass"></i></div>
                    <div class="um-title">Getting Around</div>
                </div>
                <div class="um-desc">The layout is the same on every page: a sidebar for navigation and a top header with quick actions.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Sidebar</strong> — links to every page you have access to, based on your role.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Search (Ctrl+K)</strong> — the magnifying glass in the top header opens a command palette to jump to any page or run quick actions.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Notification bell</strong> — shows announcements and updates relevant to you.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Dark mode toggle</strong> — the moon/sun icon switches the color theme; your choice is remembered.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Avatar menu</strong> — top-right corner, click your photo for your profile, this manual, and logout.</li>
                </ul>
            </div>

            <div class="um-section" id="dashboard">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-table-cells-large"></i></div>
                    <div class="um-title">Dashboard</div>
                </div>
                <div class="um-desc">Your home page after logging in — a snapshot of your recent activity and the team's.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Today's task progress and whether you've submitted your EOD report.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Weekly and monthly task totals.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Who on the team has logged today, and who hasn't.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>The latest pinned announcements.</li>
                </ul>
            </div>

            <div class="um-section" id="eod">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="um-title">EOD Reports</div>
                </div>
                <div class="um-desc">Log your daily tasks so the team can track progress and workload.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Fill in your tasks for the day and submit before end of shift.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>You can edit today's entry if something changes.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Past entries are viewable under report history.</li>
                </ul>
                <div class="um-call"><i class="fas fa-circle-info"></i><span>Analysts don't submit EOD reports — this section won't appear in their sidebar.</span></div>
            </div>

            <div class="um-section" id="sku-tracker">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-box"></i></div>
                    <div class="um-title">SKU Tracker & SLA / Weekly Output</div>
                </div>
                <div class="um-desc">The spreadsheet-style grid that tracks a SKU's progress from product research through to posted content.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Cells are editable inline — click a cell to update it directly.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Use "Bulk Add" to paste in multiple SKUs at once instead of adding rows one by one.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>The search bar filters the grid live as you type.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>SLA and Weekly Output gives a rolled-up analytics view of turnaround times and team output.</li>
                </ul>
            </div>

            <div class="um-section" id="announcements">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-bullhorn"></i></div>
                    <div class="um-title">Announcements</div>
                </div>
                <div class="um-desc">Team-wide updates and notices. Everyone can view them; heads, managers, and analysts can post.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Pinned announcements always show at the top.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>New announcements also show up in your notification bell.</li>
                </ul>
            </div>

            <div class="um-section" id="calendar">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-calendar-days"></i></div>
                    <div class="um-title">Calendar</div>
                </div>
                <div class="um-desc">Team calendar for events and shared tasks.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Add events under a category so they're color-coded and easy to scan.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span>Tasks can be checked off directly from the calendar.</li>
                </ul>
            </div>

            <div class="um-section" id="brand-catalogs">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-book-open"></i></div>
                    <div class="um-title">Brand Catalogs</div>
                </div>
                <div class="um-desc">Browse catalogs by brand and availability status. Managers and researchers can add or edit entries; everyone else can browse.</div>
            </div>

            <div class="um-section" id="important-links">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-bookmark"></i></div>
                    <div class="um-title">Important Links</div>
                </div>
                <div class="um-desc">A shared, categorized directory of the external tools and sheets the team uses day to day — grouped into tabs so you can jump straight to what you need.</div>
            </div>

            <div class="um-section" id="price-calculator">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-calculator"></i></div>
                    <div class="um-title">Price Calculator</div>
                </div>
                <div class="um-desc">Computes suggested retail price (SRP) from cost and markup inputs.</div>
            </div>

            <div class="um-section" id="content-tools">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-list-check"></i></div>
                    <div class="um-title">Content Tools</div>
                </div>
                <div class="um-desc">Reference guides for the content team's posting workflow.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Posting Procedure</strong> — the step-by-step guide for posting a product across platforms.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Requirements</strong> — platform-specific rules to follow when listing products.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Data Gathering</strong> — how to collect and organize product info before posting.</li>
                </ul>
            </div>

            <div class="um-section" id="team">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-people-group"></i></div>
                    <div class="um-title">The Team</div>
                </div>
                <div class="um-desc">A directory of everyone in the department, grouped by role.</div>
            </div>

            <div class="um-section" id="profile">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-circle-user"></i></div>
                    <div class="um-title">Profile & Theme</div>
                </div>
                <div class="um-desc">Click your avatar in the top-right corner to open your account menu.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>View Profile</strong> — update your name, avatar, and account details.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>User Manual</strong> — this page.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Logout</strong> — signs you out of your session.</li>
                </ul>
            </div>

            @if(Auth::user()->isAdmin())
            <div class="um-section" id="admin">
                <div class="um-section-hd">
                    <div class="um-icon"><i class="fas fa-gauge"></i></div>
                    <div class="um-title">Admin Dashboard</div>
                </div>
                <div class="um-desc">Management tools available to admins only.</div>
                <ul class="um-list">
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Users</strong> — create, edit, and remove team member accounts.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Daily Logs & Reports</strong> — review team EOD activity and role-based reports.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Attendance</strong> — track attendance and mark holidays.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Brands</strong> — manage the brand list used across catalogs and SKU tracking.</li>
                    <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Member View</strong> — preview the app as any role, read-only, to see what that role sees.</li>
                </ul>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    var links = document.querySelectorAll('#umNav a');
    var sections = Array.prototype.map.call(links, function(a) {
        return document.getElementById(a.getAttribute('href').slice(1));
    });

    function onScroll() {
        var pos = window.scrollY + 110;
        var current = sections[0];
        sections.forEach(function(sec) {
            if (sec && sec.offsetTop <= pos) current = sec;
        });
        links.forEach(function(a) {
            a.classList.toggle('active', current && a.getAttribute('href') === '#' + current.id);
        });
    }

    window.addEventListener('scroll', onScroll);
    onScroll();
})();
</script>
@endsection
