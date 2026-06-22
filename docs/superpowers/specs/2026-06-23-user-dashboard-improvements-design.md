# User Dashboard Improvements — Design Spec

**Date:** 2026-06-23
**Scope:** One file — `resources/views/dashboard.blade.php`

---

## Goal

Three targeted improvements to the user dashboard: a role-colored avatar banner, a more prominent EOD status strip, and a cleaner Quick Access section (with Quick Reference removed).

## Design System Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards and icon boxes
- CSS variables for structural colors; role identity hex values acceptable on role-specific elements
- No `transform` on hover — hover is border-color or background change only
- `var(--primary)` = `#5757f8`, `var(--success)` = `#22c55e`
- Font: Space Grotesk for headings, Inter for body

---

## What Is Not Changing

- Stats section (2 stat cards — Tasks This Week, Tasks This Month)
- Weekly Activity chart and its ApexCharts JS
- Recent Logs table
- Section dividers
- All Blade data bindings and controller variables (`$user`, `$todayLog`, `$thisWeekTasks`, `$thisMonthTasks`, `$recentLogs`)

---

## Change 1 — Welcome Banner

### Avatar and role color

A `@php` block at the top of `@section('content')` computes two values used by the banner:

```blade
@php
$roleColor = match($user->role) {
    'content'    => '#0ea5e9',
    'lead'       => '#6366f1',
    'researcher' => '#10b981',
    'graphics'   => '#f59e0b',
    'backend'    => '#f43f5e',
    default      => '#5757f8',
};
$avatarSeed = ($user->gender === 'female') ? $user->username . 'Female' : $user->username;
@endphp
```

### Layout

The banner becomes a `flex` row: content on the left, avatar zone on the right. The role color is applied as a CSS custom property on the element so it can be referenced inside `linear-gradient` without JavaScript.

**Banner HTML:**
```html
<div class="welcome-banner anim-up" style="--wb-color: {{ $roleColor }}; background: var(--wb-color);">
    <div class="wb-content">
        <h2>Welcome back, {{ $user->first_name }}!</h2>
        @if($user->role === 'content')
        <p>Your content workspace — posting, data gathering, and daily logs.</p>
        @elseif($user->role === 'lead')
        <p>PR leadership — product research, team coordination, and task oversight.</p>
        @elseif($user->role === 'researcher')
        <p>Product research hub — advance PR, trade-in tracking, and vendor data.</p>
        @elseif($user->role === 'graphics')
        <p>Design dashboard — CVP, banners, drafts, and visual assets.</p>
        @elseif($user->role === 'backend')
        <p>Backend operations — bulk uploads, cross-listing, QC, and Q&A.</p>
        @endif
        <div class="wb-date">{{ now()->format('l, F j') }}</div>
    </div>
    <div class="wb-avatar-zone">
        <div class="wb-fade"></div>
        <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed }}" class="wb-avatar" alt="{{ $user->full_name }}">
    </div>
</div>
```

### Fading effect

`.wb-fade` sits on top of the avatar (higher `z-index`) and applies a left-to-right gradient: fully opaque role color on the left, transparent on the right. This makes the avatar's left edge dissolve into the banner background seamlessly.

### Banner CSS (replaces all old `.welcome-banner` rules)

```css
.welcome-banner {
    border-radius: 8px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    min-height: 140px;
}
.wb-content {
    position: relative;
    z-index: 3;
}
.welcome-banner h2 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 0.375rem;
    font-weight: 700;
}
.welcome-banner p {
    color: rgba(255,255,255,0.8);
    font-weight: 500;
    font-size: 0.9rem;
    margin: 0;
}
.wb-date {
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.625rem;
}
.wb-avatar-zone {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 200px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    pointer-events: none;
}
.wb-avatar {
    height: 140px;
    width: auto;
    display: block;
    position: relative;
    z-index: 1;
    margin-left: auto;
}
.wb-fade {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, var(--wb-color) 0%, transparent 70%);
    z-index: 2;
}
@media (max-width: 480px) {
    .wb-avatar-zone { display: none; }
}
```

### Old CSS to remove

Remove the following old banner rules:
- `.welcome-banner .wb-date` (the absolute-positioned date)
- `.welcome-banner .wb-date .wd-day`
- `.welcome-banner .wb-date .wd-month`

---

## Change 2 — EOD Status Strip

### Two explicit state classes

Both states now carry an explicit class. The base `.eod-status-strip` holds only shared layout rules; `.pending` and `.submitted` define the visual identity.

**Pending HTML:**
```html
<div class="eod-status-strip pending anim-up">
    <div class="ess-left">
        <div class="ess-icon"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <div class="ess-title">EOD report not submitted yet</div>
            <div class="ess-sub">{{ now()->format('l, F j') }}</div>
        </div>
    </div>
    <a href="{{ route('end-of-day') }}" class="ess-btn">Submit EOD <i class="fas fa-arrow-right"></i></a>
</div>
```

**Submitted HTML:**
```html
<div class="eod-status-strip submitted anim-up">
    <div class="ess-left">
        <div class="ess-icon submitted"><i class="fas fa-circle-check"></i></div>
        <div class="ess-title">EOD submitted for today</div>
    </div>
    <a href="{{ route('end-of-day') }}" class="ess-edit">Edit <i class="fas fa-pencil"></i></a>
</div>
```

### EOD Strip CSS (replaces all old `.eod-status-strip` rules)

```css
.eod-status-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 8px;
    margin-bottom: 1.25rem;
    gap: 1rem;
}

/* Pending — full violet, action-oriented */
.eod-status-strip.pending {
    background: var(--primary);
    border: 1px solid var(--primary);
    padding: 1.5rem 1.25rem;
}
.eod-status-strip.pending .ess-icon {
    background: rgba(255,255,255,0.2);
    color: white;
}
.eod-status-strip.pending .ess-title {
    color: white;
    font-size: 0.95rem;
    font-weight: 700;
}
.eod-status-strip.pending .ess-sub {
    color: rgba(255,255,255,0.75);
    font-size: 0.8rem;
    margin-top: 0.125rem;
}

/* Submitted — quiet green signal */
.eod-status-strip.submitted {
    background: var(--card);
    border: 1px solid var(--border-light);
    border-left: 4px solid var(--success);
    padding: 1rem 1.25rem;
}
.eod-status-strip.submitted .ess-title {
    color: var(--success);
    font-size: 0.9rem;
    font-weight: 700;
}

/* Shared sub-elements */
.ess-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.ess-icon {
    width: 36px;
    height: 36px;
    background: var(--success);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}
.ess-icon.submitted { background: var(--success); }

/* Submit button — white-outlined on violet */
.ess-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0 1rem;
    height: 36px;
    border-radius: 8px;
    border: 1.5px solid rgba(255,255,255,0.6);
    color: white;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: border-color 0.15s, background 0.15s;
    font-family: inherit;
}
.ess-btn:hover {
    border-color: white;
    background: rgba(255,255,255,0.1);
    color: white;
}

/* Edit link */
.ess-edit {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--muted-foreground);
    text-decoration: none;
    white-space: nowrap;
}
.ess-edit:hover { color: var(--foreground); }
```

---

## Change 3 — Quick Access Redesign + Quick Reference Removal

### Remove Quick Reference

Delete from the file:
- CSS classes: `.ref-section`, `.ref-cards`, `.ref-card`, `.ref-card:hover`, `.ref-card .rc-icon`, `.ref-card .rc-label`, `.ref-card .rc-sub`
- HTML: the Quick Reference `.section-divider` block and the `.ref-cards` div that follows it

### Quick Access CSS (replaces old `.quick-link`, `.ql-icon` rules)

```css
.quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.quick-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: var(--background);
    border-radius: 8px;
    text-decoration: none;
    color: var(--foreground);
    transition: border-color 0.2s;
    border: 1px solid var(--border-light);
}
.quick-link:hover { border-color: var(--foreground); }
.ql-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    background: var(--primary);
    color: white;
    flex-shrink: 0;
    transition: background 0.2s;
}
.quick-link:hover .ql-icon { background: var(--foreground); }
.ql-name {
    display: block;
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--foreground);
}
.ql-desc {
    display: block;
    font-size: 0.75rem;
    color: var(--muted-foreground);
    margin-top: 0.125rem;
}
```

### Quick Access HTML changes

Replace the `<div class="ql-text">` content and remove `.ql-arrow` from every quick-link. Pattern before:

```html
<div class="ql-text"><strong>Posting Procedure</strong><small>8-step guide for product posting</small></div>
<div class="ql-arrow"><i class="fas fa-chevron-right"></i></div>
```

Pattern after (same for every link — swap labels and descriptions per link):

```html
<div class="ql-text">
    <span class="ql-name">Posting Procedure</span>
    <span class="ql-desc">8-step guide for product posting</span>
</div>
```

Apply this replacement to all 8 quick-link entries (4 for content role, 4 for other roles). No other changes to the link `href`, icon, or structure.

**Full label + description mapping (copy verbatim):**

Content role:
- Posting Procedure / `8-step guide for product posting`
- Data Gathering / `Collect product info and assets`
- E-commerce Requirements / `Platform-specific posting rules`
- Price Calculator / `Compute SRP across platforms`

Other roles:
- End-of-Day Report / `Log your daily tasks`
- Price Calculator / `Compute SRP across platforms`
- The Team / `View your colleagues`
- Important Links / `Quick access to resources`
