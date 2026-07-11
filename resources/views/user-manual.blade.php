@extends('layouts.app')

@section('title', 'User Manual — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M4 19.5A2.5 2.5 0 016.5 17H20'/><path d='M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Sticky tab bar ─────────────────────────────────────────── */
    .um-tabs-wrap {
        position: sticky; top: 80px; z-index: 30;
        background: var(--background);
        padding: 0.75rem 0 1rem;
        margin-bottom: 0.25rem;
    }
    .um-tabs {
        display: flex; gap: 0.5rem; flex-wrap: nowrap;
        overflow-x: auto; padding-bottom: 0.25rem;
        scrollbar-width: none;
    }
    .um-tabs::-webkit-scrollbar { display: none; }
    .um-tab {
        display: inline-flex; align-items: center; gap: 0.4rem; white-space: nowrap;
        padding: 0.45rem 0.875rem; border-radius: 9999px;
        border: 1px solid var(--border-light); background: var(--card);
        color: var(--muted-foreground); font-size: 0.8rem; font-weight: 600;
        cursor: pointer; transition: all 0.15s; font-family: inherit; text-decoration: none;
    }
    .um-tab i { font-size: 0.72rem; }
    .um-tab:hover { border-color: var(--foreground); color: var(--foreground); }
    .um-tab.active { background: var(--primary); border-color: var(--primary); color: white; }

    .um-content { display: flex; flex-direction: column; gap: 1.25rem; }

    .um-section {
        background: var(--card); border: 1px solid var(--border-light); border-radius: 12px;
        padding: 1.75rem; scroll-margin-top: 150px;
    }
    .um-section-hd { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
    .um-icon {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: var(--primary); color: white; font-size: 0.95rem;
    }
    .um-title { font-family: 'Space Grotesk', sans-serif; font-size: 1.15rem; font-weight: 700; color: var(--foreground); }
    .um-desc { font-size: 0.86rem; color: var(--muted-foreground); font-weight: 500; line-height: 1.65; margin-bottom: 1rem; }

    .um-sub {
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em;
        color: var(--primary); margin: 1.25rem 0 0.625rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .um-sub::after { content: ''; flex: 1; height: 1px; background: var(--border-light); }
    .um-sub:first-child { margin-top: 0; }

    .um-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.55rem; }
    .um-list li { display: flex; align-items: flex-start; gap: 0.625rem; font-size: 0.85rem; font-weight: 500; color: var(--foreground); line-height: 1.6; }
    .um-dot { width: 18px; height: 18px; border-radius: 5px; background: rgba(87,87,248,0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; flex-shrink: 0; margin-top: 2px; }

    .um-call {
        border-radius: 8px; padding: 0.75rem 0.875rem; margin-top: 0.875rem;
        display: flex; align-items: flex-start; gap: 0.625rem;
        font-size: 0.8rem; font-weight: 500; line-height: 1.6; color: var(--foreground);
        background: rgba(87,87,248,0.07); border: 1px solid rgba(87,87,248,0.2);
    }
    .um-call.warn { background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.25); }
    .um-call.warn i { color: var(--warning); }
    .um-call i { color: var(--primary); font-size: 0.8rem; flex-shrink: 0; margin-top: 2px; }

    .um-table-wrap { overflow-x: auto; }
    .um-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; margin-top: 0.25rem; }
    .um-table th {
        background: var(--muted); padding: 0.5rem 0.75rem; text-align: left;
        font-size: 0.63rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.05em; color: var(--muted-foreground); white-space: nowrap;
    }
    .um-table th:first-child { border-radius: 6px 0 0 6px; }
    .um-table th:last-child  { border-radius: 0 6px 6px 0; }
    .um-table td {
        padding: 0.55rem 0.75rem; border-top: 1px solid var(--border-light);
        color: var(--foreground); font-weight: 500;
    }

    .um-pill-row { display: flex; gap: 0.375rem; flex-wrap: wrap; margin-top: 0.25rem; }
    .um-pill {
        background: var(--muted); padding: 0.2rem 0.625rem; border-radius: 9999px;
        font-size: 0.72rem; font-weight: 700; color: var(--foreground);
    }

    .um-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 700px) { .um-grid-2 { grid-template-columns: 1fr; } }

    /* ── Back to top ── */
    #umBackToTop {
        position: fixed; bottom: 2rem; right: 2rem; width: 40px; height: 40px; border-radius: 50%;
        background: var(--primary); color: white; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
        opacity: 0; pointer-events: none; transition: opacity 0.25s, transform 0.25s;
        transform: translateY(10px); z-index: 100;
    }
    #umBackToTop.visible { opacity: 1; pointer-events: all; transform: translateY(0); }
    #umBackToTop:hover { background: #4444e0; }
</style>
@endsection

@section('content')
<x-sidebar active="user-manual" />

<div class="main-content">

    <div class="top-bar anim-up" style="margin-bottom:0.5rem;">
        <div>
            <h2>User <span class="highlight">Manual</span></h2>
            <p>A detailed reference for everything in the Ecomm Dept Hub</p>
        </div>
    </div>

    <div class="um-tabs-wrap anim-up d1">
        <div class="um-tabs" id="umTabs">
            <a href="#getting-around" class="um-tab"><i class="fas fa-compass"></i> Getting Around</a>
            <a href="#dashboard" class="um-tab"><i class="fas fa-table-cells-large"></i> Dashboard</a>
            <a href="#eod" class="um-tab"><i class="fas fa-calendar-check"></i> EOD Reports</a>
            <a href="#sku-tracker" class="um-tab"><i class="fas fa-box"></i> SKU Tracker</a>
            <a href="#announcements" class="um-tab"><i class="fas fa-bullhorn"></i> Announcements</a>
            <a href="#calendar" class="um-tab"><i class="fas fa-calendar-days"></i> Calendar</a>
            <a href="#brand-catalogs" class="um-tab"><i class="fas fa-book-open"></i> Brand Catalogs</a>
            <a href="#important-links" class="um-tab"><i class="fas fa-bookmark"></i> Important Links</a>
            <a href="#price-calculator" class="um-tab"><i class="fas fa-calculator"></i> Price Calculator</a>
            <a href="#content-tools" class="um-tab"><i class="fas fa-list-check"></i> Content Tools</a>
            <a href="#team" class="um-tab"><i class="fas fa-people-group"></i> The Team</a>
            <a href="#profile" class="um-tab"><i class="fas fa-circle-user"></i> Profile & Theme</a>
            @if(Auth::user()->isAdmin())
            <a href="#admin" class="um-tab"><i class="fas fa-gauge"></i> Admin Dashboard</a>
            @endif
        </div>
    </div>

    <div class="um-content anim-up d2">

        <!-- GETTING AROUND -->
        <div class="um-section" id="getting-around">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-compass"></i></div>
                <div class="um-title">Getting Around</div>
            </div>
            <div class="um-desc">Every page shares the same shell: a sidebar on the left for navigation and a fixed header on top for quick actions. Once you know the header, you know the whole app.</div>

            <div class="um-sub">Sidebar</div>
            <div class="um-desc" style="margin-bottom:0.5rem;">Lists only the pages your role can access. Admins get an entirely separate sidebar (Dashboard, Users, Daily Logs, Reports, Attendance, Brands, Brand Catalogs, SKU Management) plus a "Member View" shortcut to preview the app as any role.</div>

            <div class="um-sub">Top Header</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-clock"></i></span><strong>Clock</strong> — live time and date, top-left.</li>
                <li><span class="um-dot"><i class="fas fa-bell"></i></span><strong>Notification bell</strong> — shows a red badge with your unread count. Opens a panel listing announcements and activity relevant to you; "Clear all" empties it. Heads/managers/analysts get a "Post" shortcut here straight to the announcement composer.</li>
                <li><span class="um-dot"><i class="fas fa-moon"></i></span><strong>Dark mode toggle</strong> — flips the color theme instantly and remembers your choice in the browser (localStorage), so it persists across visits and pages.</li>
                <li><span class="um-dot"><i class="fas fa-magnifying-glass"></i></span><strong>Search / Command Palette (Ctrl+K)</strong> — a spotlight-style overlay. Type to filter every page you can access plus quick actions (toggle theme, open notifications, logout, and for admins: member view, new announcement). Use ↑/↓ to move, Enter to open, Esc to close.</li>
                <li><span class="um-dot"><i class="fas fa-circle-user"></i></span><strong>Avatar menu</strong> — top-right corner. Click your photo for View Profile, and (for admins) Admin Dashboard / Member View, plus this User Manual and Logout near the bottom.</li>
            </ul>

            <div class="um-call">
                <i class="fas fa-circle-info"></i>
                <span>Admins can click <strong>Member View</strong> to preview the app exactly as a chosen role sees it. A yellow banner appears confirming you're in read-only preview mode, with a one-click <strong>Return to Admin</strong> button.</span>
            </div>
        </div>

        <!-- DASHBOARD -->
        <div class="um-section" id="dashboard">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-table-cells-large"></i></div>
                <div class="um-title">Dashboard</div>
            </div>
            <div class="um-desc">Your home page right after login. It's a personal + team snapshot, built from your own logs and the whole team's activity for today.</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Whether you've submitted today's EOD report yet, and your most recent 5 entries.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Your total tasks logged this week and this month.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Which teammates have logged today and how many are still missing (excludes managers/heads).</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>The two most recent active announcements, pinned ones shown first.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Analysts only:</strong> a brand catalog breakdown — total, available, upcoming, and seasonal counts.</li>
            </ul>
        </div>

        <!-- EOD REPORTS -->
        <div class="um-section" id="eod">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="um-title">EOD Reports</div>
            </div>
            <div class="um-desc">Everyone except analysts logs a daily End-of-Day report: 5 numeric task fields plus attendance and optional remarks, one entry per person per day.</div>

            <div class="um-sub">What the 5 task fields mean</div>
            <div class="um-desc" style="margin-bottom:0.5rem;">The 5 task fields aren't fixed — their labels and descriptions change per role (content, graphics, backend, researcher each have their own set, managed by admins under Task Categories). Fill in a count for each task type you completed that day.</div>

            <div class="um-sub">Rules</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Only one report per day — saving again for the same date overwrites (updates) it rather than creating a duplicate.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>You can only edit or delete your own logs; other users' entries are locked to you.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Submitting a brand-new report (not an update) notifies all managers and heads automatically.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Your last 10 entries show inline; full history with pagination lives under Report History.</li>
            </ul>
            <div class="um-call warn">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Analysts don't have an EOD Reports page at all — the "not.analyst" access rule hides it from their sidebar entirely.</span>
            </div>
        </div>

        <!-- SKU TRACKER -->
        <div class="um-section" id="sku-tracker">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-box"></i></div>
                <div class="um-title">SKU Tracker & SLA / Weekly Output</div>
            </div>
            <div class="um-desc">A spreadsheet-style grid that follows a SKU from product research (PR) all the way to posted content. Every cell is inline-editable — click it to change it directly, no separate edit form.</div>

            <div class="um-sub">Who can edit what</div>
            <div class="um-table-wrap">
                <table class="um-table">
                    <thead><tr><th>Field group</th><th>Fields</th><th>Can edit</th></tr></thead>
                    <tbody>
                        <tr><td>Product Research (PR)</td><td>Brand, SKU, Variant, PR file location, PR assignee, PR status, Ready for CVP, Remarks, PR dates</td><td>Researcher, Backend, Manager, Head</td></tr>
                        <tr><td>Content</td><td>Content assignee, Content date started, Content date posted</td><td>Content, Backend, Manager, Head</td></tr>
                        <tr><td>New rows</td><td>Adding SKUs (single or bulk)</td><td>Researcher, Backend, Manager, Head</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="um-sub">Field options</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Variant</strong> — Single, Variant/Parent, Variant/Child, or Add Variant.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>PR Status</strong> — In Progress, Done, or On Hold.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Remarks</strong> — No Resources, Out-of-Stock, SKU Issue, Posted, Advance PR, or Old Posted.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Ready for CVP</strong> — a simple checkbox flag.</li>
            </ul>

            <div class="um-sub">Adding SKUs</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Add one row at a time with just Brand + SKU, or use <strong>Bulk Add</strong> to paste many at once — one per line, as "Brand, SKU" or "Brand&nbsp;&#8594;Tab&#8594;SKU". Blank or malformed lines are skipped and reported back to you.</li>
            </ul>

            <div class="um-sub">Filtering & stats</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>The search bar filters live by brand or SKU as you type — no filter button needed.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Filter by PR status, or by posted / not-posted.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>A month filter defaults to the current month (matched against PR or content start dates); switch it or clear it to see everything.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Stat cards show total SKUs, posted count, and average PR / content SLA (turnaround time) — all recalculated live against whatever filters are active.</li>
            </ul>

            <div class="um-sub">SLA & Weekly Output</div>
            <div class="um-desc" style="margin-bottom:0;">A separate analytics page that compares two chosen months side-by-side, week by week — average PR SLA and average content SLA per week, plus the percent change between the two months for each week.</div>
        </div>

        <!-- ANNOUNCEMENTS -->
        <div class="um-section" id="announcements">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-bullhorn"></i></div>
                <div class="um-title">Announcements</div>
            </div>
            <div class="um-desc">Team-wide notices, visible to everyone, paginated 10 per page with pinned posts always sorted to the top.</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Only <strong>Head, Manager, and Analyst</strong> roles can post, edit, delete, or pin an announcement.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Each post has a title and body, an optional pin, and an optional expiry date/time — expired posts stop showing as "active" (e.g. on the dashboard).</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>New posts also surface in everyone's notification bell.</li>
            </ul>
        </div>

        <!-- CALENDAR -->
        <div class="um-section" id="calendar">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-calendar-days"></i></div>
                <div class="um-title">Calendar</div>
            </div>
            <div class="um-desc">A shared team calendar with two kinds of entries: Events (meetings, deadlines) and Tasks (assigned work), both organized into color-coded categories.</div>

            <div class="um-grid-2">
                <div>
                    <div class="um-sub">Events</div>
                    <ul class="um-list">
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>Has a title, start/end time, location, description, and optional attendee list.</li>
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>If you don't set attendees, it's visible to the whole team; if you do, only those attendees (plus managers/heads, who always see everything) can see it.</li>
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>Creating an event notifies its attendees (or everyone, if none set).</li>
                    </ul>
                </div>
                <div>
                    <div class="um-sub">Tasks</div>
                    <ul class="um-list">
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>Assigned to a whole <strong>role</strong> (not a single person), with a due date and optional subtasks/checklist.</li>
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>Checking off every subtask automatically marks the parent task done.</li>
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>Completion notifies managers/heads and whoever created it.</li>
                        <li><span class="um-dot"><i class="fas fa-check"></i></span>Non-managers only see tasks assigned to their own role; managers/heads see all.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- BRAND CATALOGS -->
        <div class="um-section" id="brand-catalogs">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-book-open"></i></div>
                <div class="um-title">Brand Catalogs</div>
            </div>
            <div class="um-desc">A shared library of brand catalog files and links, browsable by everyone.</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Filter by brand, by classification (<span class="um-pill">Tech</span> <span class="um-pill">Design/Consumer</span> <span class="um-pill">Both</span>), or search by title, notes, or brand name.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Each entry needs either an external link or an uploaded file (PDF/JPG/PNG, up to 10MB) — at least one is required.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Status tag per entry: <span class="um-pill">Available</span> <span class="um-pill">Upcoming</span> <span class="um-pill">Seasonal</span>.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Only <strong>Managers and Researchers</strong> can add, edit, or delete catalogs; adding one notifies the rest of the team.</li>
            </ul>
        </div>

        <!-- IMPORTANT LINKS -->
        <div class="um-section" id="important-links">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-bookmark"></i></div>
                <div class="um-title">Important Links</div>
            </div>
            <div class="um-desc">A curated, tabbed directory of the external tools and sheets the team relies on daily. Switch between tabs to filter: <span class="um-pill">All</span> <span class="um-pill">Posted SKUs</span> <span class="um-pill">Reports</span> <span class="um-pill">Directories</span> <span class="um-pill">Training</span>. Not available to analysts.</div>
        </div>

        <!-- PRICE CALCULATOR -->
        <div class="um-section" id="price-calculator">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-calculator"></i></div>
                <div class="um-title">Price Calculator</div>
            </div>
            <div class="um-desc">Computes Suggested Retail Price (SRP) for Shopee and Lazada from a SKU's unit price, grouped so variants of the same product are priced together.</div>

            <div class="um-sub">Adding rows</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Enter a <strong>Group</strong> number, choose <strong>Single</strong> or <strong>Variant</strong> (variant lets you set a count to auto-generate that many rows sharing the group), and a <strong>Unit Price</strong>.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Min and Max price are computed automatically from every row sharing the same group number.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Duplicate SKUs are highlighted in red so they're easy to catch.</li>
            </ul>

            <div class="um-sub">Formulas</div>
            <div class="um-table-wrap">
                <table class="um-table">
                    <thead><tr><th>Platform</th><th>Formula</th><th>Checker fails when</th></tr></thead>
                    <tbody>
                        <tr><td>Shopee SRP</td><td>MIN(ROUNDUP((Min × 4.5 − Price) ÷ 10 + Price), 150000)</td><td>Max ÷ Min &gt; 4.5, or price &gt; ₱149,999</td></tr>
                        <tr><td>Lazada SRP</td><td>ROUNDUP((Min × 4.5 − Price) ÷ 10 + Price)</td><td>Max ÷ Min &gt; 4.5</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="um-sub">Managing the table</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Search by group, SKU, or price; filter to a single group with the group dropdown.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Check rows and click Delete to bulk-remove them.</li>
            </ul>
        </div>

        <!-- CONTENT TOOLS -->
        <div class="um-section" id="content-tools">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-list-check"></i></div>
                <div class="um-title">Content Tools</div>
            </div>
            <div class="um-desc">Reference guides that walk the content team through posting a product end to end.</div>

            <div class="um-sub">Posting Procedure — 8 steps</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-1"></i></span><strong>Mine SKU</strong> from the Link Sheet once it has no name assigned in the Content column, then let the team know it's claimed.</li>
                <li><span class="um-dot"><i class="fas fa-2"></i></span><strong>Data Gathering</strong> — collect product info and images from the PR file, organized into folders (1000×1000 for e-commerce, 500×500 for inFlow, 1000×2000 for long description), following the Parent/Child SKU folder structure for variations.</li>
                <li><span class="um-dot"><i class="fas fa-3"></i></span><strong>Wait for the Go Signal</strong> from the Product Researcher before posting anything.</li>
                <li><span class="um-dot"><i class="fas fa-4"></i></span><strong>E-commerce Posting</strong>, in order: Lazada Main → Shopify → Shopee Main → TikTok → Lazada Pro → Shopee Pro. For variation SKUs, wait for the "-grp" product group in Selluseller before Shopee Main.</li>
                <li><span class="um-dot"><i class="fas fa-5"></i></span><strong>inFlow Update</strong> — description plus Length/Width/Height/Weight from the PR file, ideally before posting so it isn't forgotten later.</li>
                <li><span class="um-dot"><i class="fas fa-6"></i></span><strong>Pro Posting</strong> to Lazada Pro and Shopee Pro (skip Lazada Pro if inaccessible).</li>
                <li><span class="um-dot"><i class="fas fa-7"></i></span><strong>Brand Malls</strong> — post if the brand has an official Brand Mall account, otherwise skip.</li>
                <li><span class="um-dot"><i class="fas fa-8"></i></span><strong>Update Link Sheet</strong> with every platform's listing URL and verify each one opens correctly.</li>
            </ul>

            <div class="um-sub">Also available</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Requirements</strong> — platform-specific listing rules.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Data Gathering</strong> — a dedicated page for collecting and organizing product info before posting.</li>
            </ul>
        </div>

        <!-- TEAM -->
        <div class="um-section" id="team">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-people-group"></i></div>
                <div class="um-title">The Team</div>
            </div>
            <div class="um-desc">A directory of everyone in the department, grouped by role — Head, Manager, Analyst, Researcher, Content, Graphics, and Backend — each shown with a colored role badge.</div>
        </div>

        <!-- PROFILE & THEME -->
        <div class="um-section" id="profile">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-circle-user"></i></div>
                <div class="um-title">Profile & Theme</div>
            </div>
            <div class="um-desc">Click your avatar in the top-right corner to reach your account menu.</div>
            <div class="um-sub">On your Profile page</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Edit your name, nickname, mobile number, gender, address, ID number, TIN, and SSS — TIN and SSS each have a "hide" toggle if you'd rather keep them masked.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Change your password.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Upload a new avatar (JPG/PNG/WEBP/GIF, up to 2MB) or remove your current one — it falls back to an auto-generated avatar based on your name.</li>
            </ul>
            <div class="um-sub">From the avatar menu</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>User Manual</strong> — this page.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Logout</strong> — ends your session.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Dark mode</strong> — toggled from the header, not the profile page; your choice is saved in the browser and applies everywhere immediately.</li>
            </ul>
        </div>

        @if(Auth::user()->isAdmin())
        <!-- ADMIN DASHBOARD -->
        <div class="um-section" id="admin">
            <div class="um-section-hd">
                <div class="um-icon"><i class="fas fa-gauge"></i></div>
                <div class="um-title">Admin Dashboard</div>
            </div>
            <div class="um-desc">Everything below is only reachable by Manager/Head accounts.</div>

            <div class="um-sub">Overview</div>
            <div class="um-desc" style="margin-bottom:0.5rem;">Team headcount by role, this-month vs last-month task totals with percent change, today's submission health (percentage of the team that has logged, color-coded), top contributor of the month, a 30-day activity trend, per-role weekly mini-charts, per-role task-type breakdowns, and this week's attendance snapshot (who's out today).</div>

            <div class="um-sub">Users</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Create, edit, or delete accounts — set first/last name, mobile number, gender, badge, username, password, and role.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>You can't delete your own account.</li>
            </ul>

            <div class="um-sub">Daily Logs</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Browse everyone's EOD submissions, filterable by role, with a monthly calendar view showing which days have logs.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Click any day to see exactly who logged what; a "missing logs" list flags who hasn't submitted today.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Per-role 7-day mini charts and top-contributor lists (last 7 days).</li>
            </ul>

            <div class="um-sub">Reports</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Pick a month and role to see week-by-week totals, a full 12-month year overview, and a per-task-type "share" breakdown showing each member's percentage contribution per week.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span>Per-member monthly totals across all task types, viewable per-role or for everyone at once.</li>
            </ul>

            <div class="um-sub">Attendance</div>
            <div class="um-table-wrap">
                <table class="um-table">
                    <thead><tr><th>Status</th><th>Meaning</th></tr></thead>
                    <tbody>
                        <tr><td>Present</td><td>Full day worked</td></tr>
                        <tr><td>Half Day</td><td>Partial day worked</td></tr>
                        <tr><td>VL</td><td>Vacation leave</td></tr>
                        <tr><td>SL</td><td>Sick leave</td></tr>
                        <tr><td>UT</td><td>Undertime</td></tr>
                        <tr><td>Absent</td><td>No attendance recorded</td></tr>
                        <tr><td>Holiday</td><td>Company-wide holiday, can be applied to everyone at once</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="um-sub">Brands & Member View</div>
            <ul class="um-list">
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Brands</strong> — manage the brand list (classification: Tech, Design/Consumer, or Both) used across Brand Catalogs and SKU Tracker.</li>
                <li><span class="um-dot"><i class="fas fa-check"></i></span><strong>Member View</strong> — preview the app read-only as Content, Researcher, Graphics, Backend, or Analyst, with a banner and one-click return to your admin account.</li>
            </ul>
        </div>
        @endif

    </div>
</div>

<!-- Back to top -->
<button id="umBackToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection

@section('scripts')
<script>
(function() {
    var tabs = document.querySelectorAll('#umTabs .um-tab');
    var sections = Array.prototype.map.call(tabs, function(a) {
        return document.getElementById(a.getAttribute('href').slice(1));
    }).filter(Boolean);

    if (!sections.length) return;

    function setActive(id) {
        tabs.forEach(function(a) {
            a.classList.toggle('active', a.getAttribute('href') === '#' + id);
        });
    }

    // Track each section against a thin band near the top of the viewport,
    // rather than doing cumulative offset math — avoids skipping short
    // sections when several sit back-to-back near the bottom of the page.
    var observer = new IntersectionObserver(function(entries) {
        var visible = entries.filter(function(e) { return e.isIntersecting; });
        if (!visible.length) return;
        visible.sort(function(a, b) { return a.boundingClientRect.top - b.boundingClientRect.top; });
        setActive(visible[0].target.id);
    }, { rootMargin: '-160px 0px -70% 0px', threshold: 0 });

    sections.forEach(function(sec) { observer.observe(sec); });

    setActive(sections[0].id);
})();

(function() {
    var btn = document.getElementById('umBackToTop');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 320) { btn.classList.add('visible'); }
        else { btn.classList.remove('visible'); }
    });
})();
</script>
@endsection
