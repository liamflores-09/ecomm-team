# User Dashboard Improvements — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Improve the user dashboard with a role-colored avatar banner, a more prominent EOD status strip, and a cleaner Quick Access section (Quick Reference removed).

**Architecture:** Single file edit — `resources/views/dashboard.blade.php`. Three independent changes: banner, EOD strip, Quick Access/Reference. Each task touches only its own CSS and HTML block; the Blade controller and data bindings are untouched.

**Tech Stack:** Laravel 11, Blade, CSS custom properties, FontAwesome 6, DiceBear 7.x (notionists SVG avatars)

## Global Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards and icon boxes
- CSS variables for structural colors; role identity hex values (`#0ea5e9`, `#6366f1`, `#10b981`, `#f59e0b`, `#f43f5e`) are acceptable on role-specific elements
- No `transform` on hover — hover is border-color or background change only
- Pre-existing test failure: `Tests\Feature\ExampleTest` returns 302 on `GET /` — NOT a regression
- Run tests with: `php artisan test` (Windows — use PowerShell)

---

### Task 1: Welcome Banner — Role Color + Avatar + Fade

**Files:**
- Modify: `resources/views/dashboard.blade.php`

**Interfaces:**
- Produces: CSS classes `.wb-content`, `.wb-date`, `.wb-avatar-zone`, `.wb-avatar`, `.wb-fade`; CSS custom property `--wb-color` set via inline style on `.welcome-banner`

- [ ] **Step 1: Run baseline tests**

```powershell
php artisan test
```

Expected: 1 passed, 1 failed (pre-existing 302). Note results — no new failures should appear after your changes.

- [ ] **Step 2: Replace the `@section('styles')` banner CSS block**

Find this exact block in the `<style>` tag (lines ~12–27):

```css
    .welcome-banner {
        border-radius: 8px;
        padding: 2.5rem;
        background: var(--foreground);
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid var(--foreground);
    }
    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; position: relative; z-index: 1; font-weight: 700; }
    .welcome-banner p { color: rgba(255,255,255,0.75); font-weight: 500; font-size: 0.9rem; margin: 0; position: relative; z-index: 1; }
    .welcome-banner .wb-date { position: absolute; top: 2rem; right: 2.5rem; text-align: right; z-index: 1; }
    .welcome-banner .wb-date .wd-day { font-size: 2rem; font-weight: 700; line-height: 1; font-family: 'Space Grotesk', sans-serif; }
    .welcome-banner .wb-date .wd-month { font-size: 0.8rem; font-weight: 600; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.08em; }
```

Replace with:

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
    .wb-content { position: relative; z-index: 3; }
    .welcome-banner h2 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; font-weight: 700; }
    .welcome-banner p { color: rgba(255,255,255,0.8); font-weight: 500; font-size: 0.9rem; margin: 0; }
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

- [ ] **Step 3: Add `@php` role color + avatar seed block at the top of `@section('content')`**

Find the opening of `@section('content')`:

```blade
@section('content')
<x-sidebar active="dashboard" />
```

Replace with:

```blade
@section('content')
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
<x-sidebar active="dashboard" />
```

- [ ] **Step 4: Replace the banner HTML block**

Find this exact block:

```blade
    <!-- Welcome Banner -->
    <div class="welcome-banner anim-up">
        <div>
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
        </div>
        <div class="wb-date">
            <div class="wd-day">{{ now()->format('d') }}</div>
            <div class="wd-month">{{ now()->format('M Y') }}</div>
        </div>
    </div>
```

Replace with:

```blade
    <!-- Welcome Banner -->
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

- [ ] **Step 5: Run tests**

```powershell
php artisan test
```

Expected: same as Step 1 — 1 passed, 1 failed (pre-existing 302). No new failures.

- [ ] **Step 6: Commit**

```powershell
git add resources/views/dashboard.blade.php
git commit -m "ui: role-colored avatar banner on user dashboard"
```

---

### Task 2: EOD Status Strip — More Prominent

**Files:**
- Modify: `resources/views/dashboard.blade.php`

**Interfaces:**
- Consumes: nothing from Task 1
- Produces: CSS classes `.eod-status-strip.pending`, `.eod-status-strip.submitted`, `.ess-btn`

- [ ] **Step 1: Replace the EOD strip CSS block**

Find this exact block in `<style>`:

```css
    .eod-status-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: var(--card);
        border: 1px solid var(--border-light);
        border-left: 4px solid var(--primary);
        border-radius: 8px;
        margin-bottom: 1.25rem;
        gap: 1rem;
    }
    .eod-status-strip.submitted {
        border-left-color: var(--success);
        padding: 0.75rem 1.25rem;
    }
    .ess-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .ess-icon {
        width: 32px;
        height: 32px;
        background: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .ess-icon.submitted { background: var(--success); }
    .ess-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--foreground);
    }
    .ess-sub {
        font-size: 0.8rem;
        color: var(--muted-foreground);
        margin-top: 0.125rem;
    }
    .ess-edit {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted-foreground);
        text-decoration: none;
        white-space: nowrap;
    }
    .ess-edit:hover { color: var(--foreground); }
```

Replace with:

```css
    .eod-status-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 8px;
        margin-bottom: 1.25rem;
        gap: 1rem;
    }
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
    .ess-edit {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted-foreground);
        text-decoration: none;
        white-space: nowrap;
    }
    .ess-edit:hover { color: var(--foreground); }
```

- [ ] **Step 2: Replace the EOD strip HTML block**

Find this exact block:

```blade
    <!-- EOD Status Strip -->
    @if($todayLog)
    <div class="eod-status-strip submitted anim-up">
        <div class="ess-left">
            <div class="ess-icon submitted"><i class="fas fa-circle-check"></i></div>
            <div class="ess-title">EOD submitted for today</div>
        </div>
        <a href="{{ route('end-of-day') }}" class="ess-edit">Edit <i class="fas fa-pencil"></i></a>
    </div>
    @else
    <div class="eod-status-strip anim-up">
        <div class="ess-left">
            <div class="ess-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="ess-title">EOD report not submitted yet</div>
                <div class="ess-sub">{{ now()->format('l, F j') }}</div>
            </div>
        </div>
        <a href="{{ route('end-of-day') }}" class="btn-flat-primary" style="height: 36px; padding: 0 1rem; font-size: 0.85rem; white-space: nowrap;">Submit EOD <i class="fas fa-arrow-right"></i></a>
    </div>
    @endif
```

Replace with:

```blade
    <!-- EOD Status Strip -->
    @if($todayLog)
    <div class="eod-status-strip submitted anim-up">
        <div class="ess-left">
            <div class="ess-icon"><i class="fas fa-circle-check"></i></div>
            <div class="ess-title">EOD submitted for today</div>
        </div>
        <a href="{{ route('end-of-day') }}" class="ess-edit">Edit <i class="fas fa-pencil"></i></a>
    </div>
    @else
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
    @endif
```

- [ ] **Step 3: Run tests**

```powershell
php artisan test
```

Expected: 1 passed, 1 failed (pre-existing 302 only). No new failures.

- [ ] **Step 4: Commit**

```powershell
git add resources/views/dashboard.blade.php
git commit -m "ui: prominent EOD status strip on user dashboard"
```

---

### Task 3: Quick Access Redesign + Quick Reference Removal

**Files:**
- Modify: `resources/views/dashboard.blade.php`

**Interfaces:**
- Consumes: nothing from Tasks 1 or 2
- Produces: CSS classes `.ql-name`, `.ql-desc`; removes `.ref-section`, `.ref-cards`, `.ref-card`

- [ ] **Step 1: Replace the Quick Access + Quick Reference CSS block**

Find this exact block in `<style>`:

```css
    .quick-section { background: var(--card); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); }
    .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .quick-link { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: var(--background); border-radius: 8px; text-decoration: none; color: var(--foreground); transition: all 0.2s; border: 1px solid var(--border-light); }
    .quick-link:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .quick-link:hover .ql-icon { background: rgba(255,255,255,0.2); }
    .ql-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background: var(--primary); color: white; flex-shrink: 0; }
    .ql-label { font-weight: 600; font-size: 0.875rem; }

    .ref-section { margin-bottom: 2rem; }
    .ref-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .ref-card { background: var(--card); border-radius: 8px; padding: 1.5rem; border: 1px solid var(--border-light); text-align: center; text-decoration: none; color: var(--foreground); transition: border-color 0.2s; display: block; }
    .ref-card:hover { border-color: var(--foreground); }
    .ref-card .rc-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1rem; background: var(--primary); color: white; }
    .ref-card .rc-label { font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem; }
    .ref-card .rc-sub { font-size: 0.75rem; color: var(--muted-foreground); }
```

Replace with:

```css
    .quick-section { background: var(--card); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); }
    .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .quick-link { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: var(--background); border-radius: 8px; text-decoration: none; color: var(--foreground); transition: border-color 0.2s; border: 1px solid var(--border-light); }
    .quick-link:hover { border-color: var(--foreground); }
    .quick-link:hover .ql-icon { background: var(--foreground); }
    .ql-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background: var(--primary); color: white; flex-shrink: 0; transition: background 0.2s; }
    .ql-name { display: block; font-weight: 600; font-size: 0.875rem; color: var(--foreground); }
    .ql-desc { display: block; font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.125rem; }
```

- [ ] **Step 2: Replace all 8 quick-link HTML entries**

Find the entire Quick Access HTML block and replace it. The full replacement is below — copy it verbatim:

```blade
    <div class="quick-section anim-up d3">
        <div class="quick-links">
            @if($user->role === 'content')
            <a href="{{ route('posting-procedure') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-book-open"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Posting Procedure</span>
                    <span class="ql-desc">8-step guide for product posting</span>
                </div>
            </a>
            <a href="{{ route('data-gathering') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-folder-open"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Data Gathering</span>
                    <span class="ql-desc">Collect product info and assets</span>
                </div>
            </a>
            <a href="{{ route('ecommerce-requirements') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="ql-text">
                    <span class="ql-name">E-commerce Requirements</span>
                    <span class="ql-desc">Platform-specific posting rules</span>
                </div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Price Calculator</span>
                    <span class="ql-desc">Compute SRP across platforms</span>
                </div>
            </a>
            @else
            <a href="{{ route('end-of-day') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="ql-text">
                    <span class="ql-name">End-of-Day Report</span>
                    <span class="ql-desc">Log your daily tasks</span>
                </div>
            </a>
            <a href="{{ route('price-calculator') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-calculator"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Price Calculator</span>
                    <span class="ql-desc">Compute SRP across platforms</span>
                </div>
            </a>
            <a href="{{ route('team') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-users"></i></div>
                <div class="ql-text">
                    <span class="ql-name">The Team</span>
                    <span class="ql-desc">View your colleagues</span>
                </div>
            </a>
            <a href="{{ route('important-links') }}" class="quick-link">
                <div class="ql-icon"><i class="fas fa-link"></i></div>
                <div class="ql-text">
                    <span class="ql-name">Important Links</span>
                    <span class="ql-desc">Quick access to resources</span>
                </div>
            </a>
            @endif
        </div>
    </div>
```

The old block to find starts with `<div class="quick-section anim-up d3">` and ends with the closing `</div>` after `@endif`.

- [ ] **Step 3: Remove the Quick Reference section divider and cards**

Find and delete this entire block (the Quick Reference section divider + ref-cards div):

```blade
    <!-- Quick Reference -->
    <div class="section-divider anim-up d5">
        <div class="sd-icon" style="background: var(--primary);"><i class="fas fa-star"></i></div>
        <h4>Quick Reference</h4>
        <div class="sd-line"></div>
    </div>

    <div class="ref-cards anim-up d5">
        <a href="{{ route('end-of-day') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--primary);"><i class="fas fa-calendar-check"></i></div>
            <h5>EOD Report</h5>
        </a>
        <a href="{{ route('important-links') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--gray-500);"><i class="fas fa-link"></i></div>
            <h5>Important Links</h5>
        </a>
        <a href="{{ route('team') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--gray-700);"><i class="fas fa-users"></i></div>
            <h5>The Team</h5>
        </a>
        <a href="{{ route('price-calculator') }}" class="ref-card">
            <div class="rc-icon" style="background: var(--primary);"><i class="fas fa-calculator"></i></div>
            <h5>Price Calculator</h5>
        </a>
    </div>
```

Delete the entire block — nothing replaces it.

- [ ] **Step 4: Run tests**

```powershell
php artisan test
```

Expected: 1 passed, 1 failed (pre-existing 302 only). No new failures.

- [ ] **Step 5: Commit**

```powershell
git add resources/views/dashboard.blade.php
git commit -m "ui: quick access redesign, remove quick reference"
```
