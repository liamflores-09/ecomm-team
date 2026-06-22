# Team Page Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add role tab navigation and clean up profile card visual styles on the Team page.

**Architecture:** Pure Blade/CSS/JS changes in one file. No controller, model, route, or migration changes. Tab switching uses a small inline `<script>` block — no external JS files.

**Tech Stack:** Laravel 11, Blade templates, CSS custom properties, vanilla JS.

## Global Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards, `9999px` for pills and tab buttons
- CSS variables only for structural/layout colors — no hardcoded hex for structural elements
- Role badge hex colors (`#1e293b`, `#6366f1`, `#10b981`, `#0ea5e9`, `#f59e0b`, `#f43f5e`) are **kept as-is** — they are data identifiers on badges only, not structural UI
- Active tab: `var(--primary)` (`#5757f8`) background, white text
- No `transform: translateY()` hover effects — hover state is border-color change only
- Pre-existing test failure: `Tests\Feature\ExampleTest` returns 302 on `GET /` — expected, not a regression

---

## Files

- Modify: `resources/views/team.blade.php` — all three tasks touch this one file

---

### Task 1: Role Tab Navigation

**Files:**
- Modify: `resources/views/team.blade.php`

**Interfaces:**
- Consumes: nothing from other tasks
- Produces: `.tm-section[data-role]` wrappers that Tasks 2 and 3 leave intact

- [ ] **Step 1: Replace hero CSS with tab CSS**

In `@section('styles')`, find and delete the entire "Hero stat bar" CSS block:

```css
/* ── Hero stat bar ───────────────────────────────────────────── */
.tm-hero {
    display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;
    padding: 0.875rem 1.25rem; margin-bottom: 2rem;
    background: var(--card); border: 1px solid var(--border); border-radius: 8px;
}
.tm-hero-label {
    font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--gray-400); margin-right: 0.25rem;
}
.tm-hero-pill {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.28rem 0.7rem; border-radius: 9999px;
    font-size: 0.75rem; font-weight: 600;
    background: var(--muted); color: var(--fg);
}
.tm-hero-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
```

In its place, add:

```css
/* ── Role tabs ───────────────────────────────────────────────── */
.tm-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 2rem;
}
.tm-tab {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.875rem;
    border-radius: 9999px;
    border: 1px solid var(--border-light);
    background: var(--muted);
    color: var(--foreground);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}
.tm-tab:hover {
    border-color: var(--foreground);
}
.tm-tab.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}
.tm-tab-count {
    font-size: 0.7rem;
    font-weight: 700;
    opacity: 0.75;
}
```

- [ ] **Step 2: Replace the hero HTML with the tab row**

In `@section('content')`, find and replace the entire `{{-- Hero stat pills --}}` block:

```html
    {{-- Hero stat pills --}}
    <div class="tm-hero anim-up d1">
        <span class="tm-hero-label">{{ $total }} members</span>
        @if($managers->count())
        <span class="tm-hero-pill"><span class="tm-hero-dot" style="background:#1e293b;"></span> Manager <strong style="margin-left:2px;">{{ $managers->count() }}</strong></span>
        @endif
        @if($leads->count())
        <span class="tm-hero-pill"><span class="tm-hero-dot" style="background:#6366f1;"></span> Lead <strong style="margin-left:2px;">{{ $leads->count() }}</strong></span>
        @endif
        @if($researchers->count())
        <span class="tm-hero-pill"><span class="tm-hero-dot" style="background:#10b981;"></span> Research <strong style="margin-left:2px;">{{ $researchers->count() }}</strong></span>
        @endif
        @if($content->count())
        <span class="tm-hero-pill"><span class="tm-hero-dot" style="background:#0ea5e9;"></span> Content <strong style="margin-left:2px;">{{ $content->count() }}</strong></span>
        @endif
        @if($graphics->count())
        <span class="tm-hero-pill"><span class="tm-hero-dot" style="background:#f59e0b;"></span> Graphics <strong style="margin-left:2px;">{{ $graphics->count() }}</strong></span>
        @endif
        @if($backend->count())
        <span class="tm-hero-pill"><span class="tm-hero-dot" style="background:#f43f5e;"></span> Backend <strong style="margin-left:2px;">{{ $backend->count() }}</strong></span>
        @endif
    </div>
```

Replace with:

```html
    {{-- Role tabs --}}
    <div class="tm-tabs anim-up d1">
        <button class="tm-tab active" data-filter="all">All <span class="tm-tab-count">{{ $total }}</span></button>
        @if($managers->count())
        <button class="tm-tab" data-filter="manager">Manager <span class="tm-tab-count">{{ $managers->count() }}</span></button>
        @endif
        @if($leads->count())
        <button class="tm-tab" data-filter="lead">Lead <span class="tm-tab-count">{{ $leads->count() }}</span></button>
        @endif
        @if($researchers->count())
        <button class="tm-tab" data-filter="researcher">Researcher <span class="tm-tab-count">{{ $researchers->count() }}</span></button>
        @endif
        @if($content->count())
        <button class="tm-tab" data-filter="content">Content <span class="tm-tab-count">{{ $content->count() }}</span></button>
        @endif
        @if($graphics->count())
        <button class="tm-tab" data-filter="graphics">Graphics <span class="tm-tab-count">{{ $graphics->count() }}</span></button>
        @endif
        @if($backend->count())
        <button class="tm-tab" data-filter="backend">Backend <span class="tm-tab-count">{{ $backend->count() }}</span></button>
        @endif
    </div>
```

- [ ] **Step 3: Wrap the Manager section**

Find:

```html
    {{-- ════ MANAGER ════ --}}
    @if($managers->count())
    <div class="tm-hd anim-up d2">
```

Replace with:

```html
    {{-- ════ MANAGER ════ --}}
    @if($managers->count())
    <div class="tm-section" data-role="manager">
    <div class="tm-hd anim-up d2">
```

Then find the closing `@endif` of the manager block (after `</div>` that closes `.tm-leaders`):

```html
    </div>
    @endif

    {{-- ════ LEAD ════ --}}
```

Replace with:

```html
    </div>
    </div>
    @endif

    {{-- ════ LEAD ════ --}}
```

- [ ] **Step 4: Wrap the Lead section**

Find:

```html
    {{-- ════ LEAD ════ --}}
    @if($leads->count())
    <div class="tm-hd anim-up d3">
```

Replace with:

```html
    {{-- ════ LEAD ════ --}}
    @if($leads->count())
    <div class="tm-section" data-role="lead">
    <div class="tm-hd anim-up d3">
```

Then find the closing `@endif` of the lead block (after `</div>` that closes `.tm-leaders`):

```html
    </div>
    @endif

    {{-- ════ RESEARCHER ════ --}}
```

Replace with:

```html
    </div>
    </div>
    @endif

    {{-- ════ RESEARCHER ════ --}}
```

- [ ] **Step 5: Wrap the Researcher section**

Find:

```html
    {{-- ════ RESEARCHER ════ --}}
    @if($researchers->count())
    <div class="tm-hd anim-up d4">
```

Replace with:

```html
    {{-- ════ RESEARCHER ════ --}}
    @if($researchers->count())
    <div class="tm-section" data-role="researcher">
    <div class="tm-hd anim-up d4">
```

Then find the closing `@endif` of the researcher block (after `</div>` that closes `.tm-members`):

```html
    </div>
    @endif

    {{-- ════ CONTENT ════ --}}
```

Replace with:

```html
    </div>
    </div>
    @endif

    {{-- ════ CONTENT ════ --}}
```

- [ ] **Step 6: Wrap the Content section**

Find:

```html
    {{-- ════ CONTENT ════ --}}
    <div class="tm-hd anim-up d4">
```

Replace with:

```html
    {{-- ════ CONTENT ════ --}}
    <div class="tm-section" data-role="content">
    <div class="tm-hd anim-up d4">
```

Then find the end of the content block (the `@endif` + graphics comment):

```html
    @else
    <div class="tm-empty anim-up d4"><i class="fas fa-users"></i> No content members yet.</div>
    @endif

    {{-- ════ GRAPHICS ════ --}}
```

Replace with:

```html
    @else
    <div class="tm-empty anim-up d4"><i class="fas fa-users"></i> No content members yet.</div>
    @endif
    </div>

    {{-- ════ GRAPHICS ════ --}}
```

- [ ] **Step 7: Wrap the Graphics section**

Find:

```html
    {{-- ════ GRAPHICS ════ --}}
    <div class="tm-hd anim-up d5">
```

Replace with:

```html
    {{-- ════ GRAPHICS ════ --}}
    <div class="tm-section" data-role="graphics">
    <div class="tm-hd anim-up d5">
```

Then find the end of the graphics block:

```html
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-palette"></i> No graphics members yet.</div>
    @endif

    {{-- ════ BACKEND ════ --}}
```

Replace with:

```html
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-palette"></i> No graphics members yet.</div>
    @endif
    </div>

    {{-- ════ BACKEND ════ --}}
```

- [ ] **Step 8: Wrap the Backend section**

Find:

```html
    {{-- ════ BACKEND ════ --}}
    <div class="tm-hd anim-up d5">
```

Replace with:

```html
    {{-- ════ BACKEND ════ --}}
    <div class="tm-section" data-role="backend">
    <div class="tm-hd anim-up d5">
```

Then find the end of the backend block (just before `</div>` that closes `.main-content`):

```html
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-server"></i> No backend members yet.</div>
    @endif

</div>
@endsection
```

Replace with:

```html
    @else
    <div class="tm-empty anim-up d5"><i class="fas fa-server"></i> No backend members yet.</div>
    @endif
    </div>

</div>
```

Then add the script block immediately after that closing `</div>` (the `.main-content` div), before `@endsection`:

```html
<script>
(function () {
    var tabs = document.querySelectorAll('.tm-tab');
    var sections = document.querySelectorAll('.tm-section');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            var filter = tab.dataset.filter;
            sections.forEach(function (s) {
                s.style.display = (filter === 'all' || s.dataset.role === filter) ? '' : 'none';
            });
        });
    });
}());
</script>
@endsection
```

- [ ] **Step 9: Run tests and verify**

```bash
php artisan test
```

Expected: pre-existing `ExampleTest` 302 failure only — no new failures.

Open the Team page in a browser and verify:
- Tab row appears with "All" active and one pill per role that has members
- Clicking a role tab shows only that section, hides the rest
- Clicking "All" restores all sections
- No hero stat bar visible

- [ ] **Step 10: Commit**

```bash
git add resources/views/team.blade.php
git commit -m "ui: role tab navigation on team page"
```

---

### Task 2: Leader Card Redesign

**Files:**
- Modify: `resources/views/team.blade.php`

**Interfaces:**
- Consumes: `.tm-section` wrappers from Task 1 (left untouched)
- Produces: nothing consumed by Task 3

- [ ] **Step 1: Update leader card CSS**

In `@section('styles')`, find the entire leader card CSS block (the "Leader grid" section):

```css
/* ── Leader grid (managers & leads — 2-col) ──────────────────── */
.tm-leaders {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
}

/* Leader card: gradient strip + overlapping avatar */
.tm-lcard {
    background: var(--card); border-radius: 8px;
    border: 1px solid var(--border); overflow: hidden;
    transition: transform 0.2s, border-color 0.2s;
}
.tm-lcard:hover { transform: translateY(-3px); border-color: var(--foreground); }

.tm-lcard-strip { height: 72px; }

.tm-lcard-body { text-align: center; padding: 0 1.5rem 1.5rem; margin-top: -38px; }

.tm-lcard-avatar {
    width: 80px; height: 80px; border-radius: 50%;
    border: 4px solid var(--card); display: block; margin: 0 auto 0.75rem;
    background: var(--muted); object-fit: cover;
}
.tm-lcard-name { font-weight: 800; font-size: 1.05rem; margin-bottom: 0.2rem; line-height: 1.2; }
.tm-lcard-sub  { font-size: 0.73rem; color: var(--gray-400); font-weight: 500; margin-bottom: 0.5rem; }
```

Replace with:

```css
/* ── Leader grid (managers & leads — 2-col) ──────────────────── */
.tm-leaders {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
}

.tm-lcard {
    background: var(--card);
    border-radius: 8px;
    border: 1px solid var(--border-light);
    overflow: hidden;
    transition: border-color 0.2s;
}
.tm-lcard:hover { border-color: var(--foreground); }

.tm-lcard-body {
    text-align: center;
    padding: 2rem 1.5rem 1.5rem;
}

.tm-lcard-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 3px solid var(--border);
    display: block;
    margin: 0 auto 0.875rem;
    background: var(--muted);
    object-fit: cover;
}
.tm-lcard-name { font-weight: 800; font-size: 1.05rem; margin-bottom: 0.2rem; line-height: 1.2; }
.tm-lcard-sub  { font-size: 0.73rem; color: var(--muted-foreground); font-weight: 500; margin-bottom: 0.5rem; }
```

- [ ] **Step 2: Remove the strip div from Manager cards**

In the Manager `@foreach` loop, find this pattern (inside `.tm-lcard`):

```html
        <div class="tm-lcard">
            <div class="tm-lcard-strip" style="background: #1e293b;"></div>
            <div class="tm-lcard-body">
```

Replace with:

```html
        <div class="tm-lcard">
            <div class="tm-lcard-body">
```

- [ ] **Step 3: Remove the strip div from Lead cards**

In the Lead `@foreach` loop, find:

```html
        <div class="tm-lcard">
            <div class="tm-lcard-strip" style="background: #6366f1;"></div>
            <div class="tm-lcard-body">
```

Replace with:

```html
        <div class="tm-lcard">
            <div class="tm-lcard-body">
```

- [ ] **Step 4: Run tests and verify**

```bash
php artisan test
```

Expected: pre-existing `ExampleTest` 302 failure only — no new failures.

Open the Team page in a browser and verify:
- Manager and Lead cards have no color strip at the top
- Avatar is centered with ink border (`var(--border)`), no overlap
- Card top padding gives the avatar breathing room
- Hover state: border darkens to `var(--foreground)`, no upward movement
- Role badge and Viber link are unchanged

- [ ] **Step 5: Commit**

```bash
git add resources/views/team.blade.php
git commit -m "ui: leader card redesign, remove color strip"
```

---

### Task 3: Member Card Redesign

**Files:**
- Modify: `resources/views/team.blade.php`

**Interfaces:**
- Consumes: nothing from Tasks 1 or 2 (independent CSS/HTML changes)
- Produces: nothing — final task

- [ ] **Step 1: Update member card CSS**

In `@section('styles')`, find the entire member card CSS block (the "Member grid" section):

```css
/* ── Member grid (3-col) ─────────────────────────────────────── */
.tm-members {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.875rem;
}

/* Member card: centered portrait style */
.tm-card {
    background: var(--card); border-radius: 8px;
    border: 1px solid var(--border); border-top-width: 3px;
    padding: 1.5rem 1rem 1.25rem; text-align: center;
    transition: transform 0.2s, border-color 0.2s;
}
.tm-card:hover { transform: translateY(-3px); border-color: var(--foreground); }

.tm-avatar {
    width: 68px; height: 68px; border-radius: 50%;
    border: 3px solid var(--border); display: block;
    margin: 0 auto 0.75rem; object-fit: cover;
    background: var(--muted); transition: border-color 0.2s;
}
.tm-card:hover .tm-avatar { border-color: var(--tm-role-color, var(--border)); }

.tm-name { font-weight: 800; font-size: 0.9rem; line-height: 1.25; margin-bottom: 0.35rem; }
.tm-username { font-size: 0.7rem; color: var(--gray-400); font-weight: 500; margin-bottom: 0.45rem; }
```

Replace with:

```css
/* ── Member grid (3-col) ─────────────────────────────────────── */
.tm-members {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.875rem;
}

.tm-card {
    background: var(--card);
    border-radius: 8px;
    border: 1px solid var(--border-light);
    padding: 1.5rem 1rem 1.25rem;
    text-align: center;
    transition: border-color 0.2s;
}
.tm-card:hover { border-color: var(--foreground); }

.tm-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    border: 2px solid var(--border-light);
    display: block;
    margin: 0 auto 0.75rem;
    object-fit: cover;
    background: var(--muted);
    transition: border-color 0.2s;
}
.tm-card:hover .tm-avatar { border-color: var(--foreground); }

.tm-name { font-weight: 800; font-size: 0.9rem; line-height: 1.25; margin-bottom: 0.35rem; }
.tm-username { font-size: 0.7rem; color: var(--muted-foreground); font-weight: 500; margin-bottom: 0.45rem; }
```

- [ ] **Step 2: Remove inline color styles from Researcher cards**

Find every `.tm-card` in the Researcher `@foreach` loop:

```html
        <div class="tm-card" style="border-top-color:#10b981; --tm-role-color:#10b981;">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" style="border-color:#10b981;" alt="{{ $u->full_name }}">
```

Replace with:

```html
        <div class="tm-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
```

- [ ] **Step 3: Remove inline color styles from Content cards**

Find every `.tm-card` in the Content `@foreach` loop:

```html
        <div class="tm-card" style="border-top-color:#0ea5e9; --tm-role-color:#0ea5e9;">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" style="border-color:#0ea5e9;" alt="{{ $u->full_name }}">
```

Replace with:

```html
        <div class="tm-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
```

- [ ] **Step 4: Remove inline color styles from Graphics cards**

Find every `.tm-card` in the Graphics `@foreach` loop:

```html
        <div class="tm-card" style="border-top-color:#f59e0b; --tm-role-color:#f59e0b;">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" style="border-color:#f59e0b;" alt="{{ $u->full_name }}">
```

Replace with:

```html
        <div class="tm-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
```

- [ ] **Step 5: Remove inline color styles from Backend cards**

Find every `.tm-card` in the Backend `@foreach` loop:

```html
        <div class="tm-card" style="border-top-color:#f43f5e; --tm-role-color:#f43f5e;">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" style="border-color:#f43f5e;" alt="{{ $u->full_name }}">
```

Replace with:

```html
        <div class="tm-card">
            <img src="https://api.dicebear.com/7.x/notionists/svg?seed={{ $avatarSeed($u) }}" class="tm-avatar" alt="{{ $u->full_name }}">
```

- [ ] **Step 6: Run tests and verify**

```bash
php artisan test
```

Expected: pre-existing `ExampleTest` 302 failure only — no new failures.

Open the Team page in a browser and verify:
- Member cards (Researcher, Content, Graphics, Backend) have no colored top border
- Avatar circles have a light border (`var(--border-light)`), no role color
- On hover: card border and avatar border both go ink (`var(--foreground)`), no upward movement
- Role badges still show their role colors (unchanged)
- Card layout (3-col grid, spacing) is unchanged

- [ ] **Step 7: Commit**

```bash
git add resources/views/team.blade.php
git commit -m "ui: member card redesign, remove role color decorations"
```
