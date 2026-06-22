# Team Page Redesign — Design Spec

**Date:** 2026-06-22
**Scope:** One file — `resources/views/team.blade.php`

---

## Goal

Make the Team page faster to navigate and more visually cohesive — role tabs to jump between groups, and cleaner profile cards that use a consistent ink-border language instead of per-role color decorations.

## Design System Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards, `9999px` for pills/badges
- CSS variables only for structural colors — no hardcoded hex for layout
- Role badge colors (`#1e293b`, `#6366f1`, `#10b981`, `#0ea5e9`, `#f59e0b`, `#f43f5e`) are **kept** as-is — they are data identifiers, not UI chrome
- `var(--primary)` = `#5757f8` for the active tab state
- Font: Space Grotesk for names/headings, Inter for body

---

## Change 1 — Role Tab Navigation

### Tab row replaces hero stat bar

The `.tm-hero` bar is replaced with a `.tm-tabs` tab row. The "All" tab is first, followed by one tab per role that has members. Each tab shows the role name and member count.

**Tab HTML structure:**
```html
<div class="tm-tabs anim-up d1">
    <button class="tm-tab active" data-filter="all">All <span class="tm-tab-count">{{ $total }}</span></button>
    <button class="tm-tab" data-filter="manager">Manager <span class="tm-tab-count">1</span></button>
    <button class="tm-tab" data-filter="lead">Lead <span class="tm-tab-count">2</span></button>
    <!-- etc. for each role with members -->
</div>
```

Only roles with at least one member get a tab (guarded by `@if($role->count())`).

**Tab CSS:**
```css
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

### Section data attributes

Each role section pair (the `.tm-hd` header + the grid/empty div) is wrapped in a `<div class="tm-section" data-role="manager">` wrapper. The JS targets these wrappers to show/hide. `.tm-section` requires no CSS — it is a grouping container only.

**Wrapper pattern:**
```html
<div class="tm-section" data-role="manager">
    <div class="tm-hd ...">...</div>
    <div class="tm-leaders ...">...</div>
</div>
```

### Inline JS for tab switching

A single `<script>` block at the bottom of `@section('content')`, before `</div>`:

```javascript
(function () {
    const tabs = document.querySelectorAll('.tm-tab');
    const sections = document.querySelectorAll('.tm-section');

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
```

### Remove old hero CSS

The following CSS classes are no longer used and must be removed from `@section('styles')`:
- `.tm-hero`
- `.tm-hero-label`
- `.tm-hero-pill`
- `.tm-hero-dot`

---

## Change 2 — Leader Card Redesign (Manager & Lead)

### Remove the color strip

Delete `.tm-lcard-strip` from CSS and remove every `<div class="tm-lcard-strip" ...>` element from the HTML. The overlapping avatar trick (`margin-top: -38px` on `.tm-lcard-body`) is removed — body gets standard top padding instead.

### Updated leader card CSS

```css
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
```

Remove `.tm-lcard:hover { transform: translateY(-3px); ... }` — no transform on hover, border change only.

`.tm-lcard-name` and `.tm-lcard-sub` are unchanged. `.tm-viber-link` is unchanged.

---

## Change 3 — Member Card Redesign

### Remove role color decorations

Remove all inline `style="border-top-color:#hex; --tm-role-color:#hex;"` attributes from `.tm-card` divs. Remove all inline `style="border-color:#hex;"` attributes from `.tm-avatar` img tags.

### Updated member card CSS

```css
.tm-card {
    background: var(--card);
    border-radius: 8px;
    border: 1px solid var(--border-light);
    border-top-width: 1px;        /* was 3px colored — now uniform */
    padding: 1.5rem 1rem 1.25rem;
    text-align: center;
    transition: border-color 0.2s;
}
.tm-card:hover { border-color: var(--foreground); }

.tm-avatar {
    width: 72px;                  /* was 68px */
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
```

Remove the `.tm-card:hover { transform: translateY(-3px); ... }` rule — border change only, no transform.

Remove the `.tm-card:hover .tm-avatar { border-color: var(--tm-role-color, var(--border)); }` rule — replaced by the foreground hover above.

### Role badges unchanged

`.role-badge` and all its role-specific variants (`manager`, `lead`, `content`, etc.) are kept exactly as-is. These are the only role-color elements that remain.

---

## What Is Not Changing

- The `.tm-hd` section header component — kept as-is
- The 2-column `.tm-leaders` grid — kept as-is
- The 3-column `.tm-members` grid — kept as-is
- The Viber link SVG icon and `.tm-viber-link` style — kept as-is
- The `.tm-empty` empty state — kept as-is
- Avatar URLs (DiceBear API) — kept as-is
- Responsive breakpoints — kept as-is
- The role badge CSS block — kept as-is
- The `:root` role color palette variables — kept as-is (used by badges)
