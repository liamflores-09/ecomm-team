# Command Palette — Design Spec
**Date:** 2026-06-28

## Overview

Enhance the existing command palette (`Ctrl+K`) in `layouts/app.blade.php` to be fully role-aware, properly grouped into Navigation and Actions, and visually polished with per-category icon styling.

---

## Current State

A working command palette exists with:
- `Ctrl+K` shortcut, `↑↓` navigation, `Enter` to open, `Esc` to close
- A sidebar "Search Ctrl+K" button below the brand
- A single "Pages" group with hardcoded page lists

Known issues:
- `isAdmin` detected via `window.location.pathname.startsWith('/admin')` — breaks on member pages visited by admin
- Page list ignores most role restrictions (e.g. analyst sees EOD, price calculator)
- No actions group
- Admin page list missing Reports, Announcements, Calendar, The Team

---

## Architecture

The palette lives entirely in `layouts/app.blade.php` as one self-contained JS block. No new routes or controllers needed.

Two changes to the data model:

1. **`isAdmin` detection** — replace URL sniffing with a PHP-rendered JS boolean:
   ```js
   var isAdmin = {{ ($isPreview ? false : Auth::user()->isAdmin()) ? 'true' : 'false' }};
   ```

2. **Two item types** — each command object gets a `type` field:
   - `page` — navigates to `url` on Enter
   - `action` — calls `data.fn` (a JS function name) on Enter instead of navigating

---

## Result Groups

### Navigation

Shown first. Label: `Navigation`. Items are page links.

**Admin** (when `isAdmin === true`):

| Name | Description | Icon |
|------|-------------|------|
| Admin Dashboard | Overview | `fa-table-cells-large` |
| Users | User management | `fa-user-group` |
| Daily Logs | Team activity | `fa-clock-rotate-left` |
| Reports | Role reports | `fa-chart-column` |
| Brands | Manage brands | `fa-layer-group` |
| Brand Catalogs | Browse catalogs | `fa-book-open` |
| Announcements | Team announcements | `fa-bullhorn` |
| Calendar | Team calendar | `fa-calendar-days` |
| The Team | Team directory | `fa-people-group` |

**Member** (when `isAdmin === false`, role-filtered via Blade `@if`):

| Name | Description | Icon | Roles |
|------|-------------|------|-------|
| Dashboard | Overview | `fa-table-cells-large` | All |
| EOD Report | Log daily tasks | `fa-calendar-check` | Non-analyst |
| Posting Procedure | Product posting guide | `fa-list-check` | Content only |
| Requirements | Platform rules | `fa-clipboard-list` | Content only |
| Data Gathering | Collect product info | `fa-magnifying-glass-chart` | Content only |
| Price Calculator | Compute SRP | `fa-calculator` | Non-analyst |
| Important Links | Quick access | `fa-bookmark` | Non-analyst |
| Brand Catalogs | Browse catalogs | `fa-book-open` | All |
| Announcements | Team announcements | `fa-bullhorn` | All |
| Calendar | Team calendar | `fa-calendar-days` | Non-analyst |
| The Team | Team directory | `fa-people-group` | All |

In preview mode, member pages are shown filtered to the simulated role (`$previewRole`).

---

### Actions

Shown second, after a separator. Label: `Actions`. Items trigger JS functions.

**All users:**

| Name | Description | Icon | Icon bg | `fn` |
|------|-------------|------|---------|------|
| Toggle Theme | Switch dark/light mode | `fa-moon` / `fa-sun` | `#6366f1` | `toggleTheme` |
| Profile | Your profile | `fa-user` | `#0ea5e9` | — (link to `/profile`) |
| Notifications | Open notifications | `fa-bell` | `#f59e0b` | `openNotifPanel` |
| Logout | Sign out | `fa-right-from-bracket` | `#ef4444` | `submitLogout` |

**Admin-only** (hidden in preview mode):

| Name | Description | Icon | Icon bg | `fn` |
|------|-------------|------|---------|------|
| Member View | Preview a member role | `fa-arrow-right-from-bracket` | `#10b981` | `openMemberView` |
| New Announcement | Post an announcement | `fa-bullhorn` | `#f59e0b` | — (link to `/announcements`) |

---

## Action Handlers

New named JS functions added to the palette script:

```js
function openNotifPanel() {
    toggleNotifPanel(); // existing global function in app.blade.php
}

function submitLogout() {
    document.getElementById('logout-form').submit();
}

function openMemberView() {
    openModal('rolePickerModal');
}
```

`toggleTheme()` already exists globally.

Profile and New Announcement use `url` links (type: `page`), not JS actions.

---

## Item Rendering

Each result item renders as:
```html
<div class="cmd-item" data-idx="N" data-action="fnName?">
  <div class="ci-icon" style="background: #COLOR">
    <i class="fas fa-ICON"></i>
  </div>
  <div style="flex:1">
    <div class="ci-name">Name</div>
    <div class="ci-desc">Description</div>
  </div>
</div>
```

- Navigation items: `ci-icon` uses `var(--muted)` background (existing style), colored on hover/active
- Action items: `ci-icon` uses a fixed color background always (purple, blue, amber, red, green)
- On Enter: if `data-action` is set, call the named function and close palette; otherwise navigate to `href`

---

## UX Details

- **Group separator** — a thin `1px var(--border)` line + label between Navigation and Actions sections
- **Empty state** — `No results for "query"` centered, 32px padding, muted color
- **Keyboard** — unchanged: `Ctrl+K` toggle, `↑↓` navigate, `↵` confirm, `Esc` close
- **Sidebar button** — existing "Search Ctrl+K" button stays, no changes needed
- **Click outside** — clicking the overlay closes the palette (already implemented)

---

## Files Modified

| File | Change |
|------|--------|
| `resources/views/layouts/app.blade.php` | Replace JS command palette block: fix `isAdmin`, rebuild page/action lists, add action handlers, update `render()` for two groups |

No new files, no new routes, no controller changes.

---

## Preview Mode Behavior

| Condition | Navigation shown | Actions shown |
|-----------|-----------------|---------------|
| Admin, no preview | Admin pages | All actions incl. Member View |
| Admin, in preview | Member pages for `$previewRole` | All actions except Member View |
| Member | Member pages for own role | All actions except admin-only |
