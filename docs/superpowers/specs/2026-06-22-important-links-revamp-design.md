# Important Links Page Revamp — Design Spec

**Date:** 2026-06-22
**Scope:** One file — `resources/views/important-links.blade.php`

---

## Goal

Replace the dated sidebar-tab layout with a full-width card grid, pill-tab filtering, and link cards that match the project's design system — clean, monochrome, consistent with the team page.

## Design System Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards and icon boxes, `9999px` for pill tabs
- CSS variables only for structural colors — no hardcoded hex for layout
- No `transform` on hover — hover is border-color change only
- `var(--primary)` = `#5757f8` for the active tab state
- Font: Space Grotesk for headings, Inter for body

---

## What Is Removed

The entire sidebar layout is deleted:

- `.links-layout` (the `220px 1fr` grid wrapper)
- `.cat-tabs` and all `.cat-tab` CSS and HTML
- `.ct-icon`, `.ct-text`, `.ct-name`, `.ct-count`
- `.link-content`, `.link-content-header`, `.lch-icon`
- `.link-rows`, `.link-row`, `.lr-num`, `.lr-info`, `.lr-name`, `.lr-desc`, `.lr-arrow`
- `.link-panel` and the `switchTab()` JS function
- All per-category colored icon classes (`ci-blue`, `ci-green`, `ci-amber`, `ci-dark`)

---

## Change 1 — Pill Tab Row

Same pattern as the team page's `.tm-tabs`. An "All" tab is first, followed by one tab per category. Each tab shows the category name and link count.

**Tab HTML structure:**
```html
<div class="il-tabs">
    <button class="il-tab active" data-filter="all">All <span class="il-tab-count">10</span></button>
    <button class="il-tab" data-filter="skus">Posted SKUs <span class="il-tab-count">2</span></button>
    <button class="il-tab" data-filter="reports">Reports <span class="il-tab-count">4</span></button>
    <button class="il-tab" data-filter="dirs">Directories <span class="il-tab-count">3</span></button>
    <button class="il-tab" data-filter="training">Training <span class="il-tab-count">1</span></button>
</div>
```

**Tab CSS:**
```css
.il-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 2rem;
}
.il-tab {
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
.il-tab:hover { border-color: var(--foreground); }
.il-tab.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}
.il-tab-count {
    font-size: 0.7rem;
    font-weight: 700;
    opacity: 0.75;
}
```

---

## Change 2 — Category Sections

Each category is wrapped in a `<div class="il-section" data-category="X">` container. The tab JS targets these to show/hide. `.il-section` requires no CSS — it is a grouping container only.

**Section wrapper pattern:**
```html
<div class="il-section" data-category="skus">
    <div class="il-hd">
        <span class="il-hd-name">Posted SKUs</span>
        <span class="il-hd-count">2 links</span>
    </div>
    <div class="il-grid">
        <!-- link cards -->
    </div>
</div>
```

**Section header CSS:**
```css
.il-hd {
    display: flex;
    align-items: baseline;
    gap: 0.625rem;
    margin-bottom: 1rem;
    padding-bottom: 0.625rem;
    border-bottom: 1px solid var(--border-light);
}
.il-hd-name {
    font-weight: 800;
    font-size: 0.95rem;
    color: var(--foreground);
}
.il-hd-count {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--muted-foreground);
}
```

**Grid CSS:**
```css
.il-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}
```

**Responsive:**
```css
@media (max-width: 768px) {
    .il-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    .il-grid { grid-template-columns: 1fr; }
}
```

---

## Change 3 — Link Cards

Each link is a full `<a>` element (opens `target="_blank"`). The card is entirely clickable.

**Card HTML structure:**
```html
<a href="#" target="_blank" class="il-card">
    <div class="il-card-top">
        <div class="il-card-icon"><i class="fas fa-table"></i></div>
        <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
    </div>
    <div class="il-card-name">Content x PR Posted SKUs 2026</div>
    <div class="il-card-type">Google Sheet</div>
</a>
```

**Icon choice by resource type:**
- Google Sheet → `fa-table`
- Folder → `fa-folder-open`

**Card CSS:**
```css
.il-card {
    background: var(--card);
    border: 1px solid var(--border-light);
    border-radius: 8px;
    padding: 1.25rem;
    text-decoration: none;
    color: var(--foreground);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    transition: border-color 0.2s;
}
.il-card:hover { border-color: var(--foreground); }

.il-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.25rem;
}
.il-card-icon {
    width: 36px;
    height: 36px;
    background: var(--muted);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted-foreground);
    font-size: 0.875rem;
    flex-shrink: 0;
}
.il-card:hover .il-card-icon {
    background: var(--primary);
    color: white;
}
.il-card-ext {
    font-size: 0.65rem;
    color: var(--border);
    transition: color 0.2s;
}
.il-card:hover .il-card-ext { color: var(--muted-foreground); }

.il-card-name {
    font-weight: 700;
    font-size: 0.875rem;
    line-height: 1.35;
    color: var(--foreground);
}
.il-card-type {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--muted-foreground);
}
```

---

## Change 4 — Inline JS for Tab Filtering

Replace the old `switchTab()` function with the same IIFE pattern used on the team page. Place the script block at the bottom of `@section('content')`, before the closing `</div>`.

```javascript
(function () {
    var tabs = document.querySelectorAll('.il-tab');
    var sections = document.querySelectorAll('.il-section');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            var filter = tab.dataset.filter;
            sections.forEach(function (s) {
                s.style.display = (filter === 'all' || s.dataset.category === filter) ? '' : 'none';
            });
        });
    });
}());
```

---

## Full Link Inventory

All links currently use `href="#"` (placeholder). The HTML keeps them as `#` — the revamp does not change link targets.

| Category | Key | Links |
|----------|-----|-------|
| Posted SKUs | `skus` | Content x PR Posted SKUs 2026 (Sheet), Content x PR Posted SKUs 2025 (Sheet) |
| Reports | `reports` | Content x GA Dept Report 2026 V2 (Sheet), JG QC Tracker (Sheet), Operation x Content Inactive Monitoring (Sheet), JG Ecom CP Tracker (Sheet) |
| Directories | `dirs` | JG SUPERSTORE ECOMMERCE DIRECTORY (Folder), Change SKU Tracker (Folder), Freebie & Update CVP Monitoring V2 2026 (Folder) |
| Training | `training` | Content Associate Training Files (Folder) |

---

## What Is Not Changing

- The page title, back link, and `.top-bar` header
- The sidebar component (`<x-sidebar active="important-links" />`)
- The favicon
- Link targets (all remain `#` placeholder)
- The `@section('scripts')` block position (JS moves inline to `@section('content')`)
