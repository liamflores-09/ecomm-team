# Admin Role Preview — Design Spec
**Date:** 2026-06-28

## Overview

Admins (manager, head) can enter a read-only "member view" that simulates exactly what a specific member role sees — sidebar nav, accessible pages, and all. While previewing, no data can be submitted. A persistent banner identifies the preview state and provides a way to switch roles or return to admin.

---

## Roles

- **Admin roles** (can use this feature): `manager`, `head`
- **Previewable member roles**: `lead`, `content`, `researcher`, `graphics`, `backend`, `analyst`

---

## Session State

A single session key drives the feature:

```
session('preview_role') = 'content' | 'lead' | 'researcher' | 'graphics' | 'backend' | 'analyst' | null
```

When this key is set and the authenticated user is an admin, the app enters preview mode.

---

## Routes

Two new routes added inside the existing `admin` middleware group:

| Method | URI | Action |
|--------|-----|--------|
| `POST` | `/admin/preview-role` | Set `preview_role` in session, redirect to `/dashboard` |
| `DELETE` | `/admin/preview-role` | Clear `preview_role` from session, redirect to `/admin` |

Request body for POST: `{ role: string }` — validated to be one of the 6 member roles.

---

## View Composer

A `PreviewRoleComposer` is registered in `AppServiceProvider` and shares two variables with **all views**:

- `$isPreview` — `bool`: true when an admin has an active `preview_role` in session
- `$previewRole` — `string|null`: the role being previewed (e.g. `'content'`)

This means no controller needs to be changed to pass these values.

---

## Components

### 1. Role Picker Modal

- Triggered by the "Member View" link in the admin sidebar (replaces the direct `route('dashboard')` link)
- Opens a modal with 6 role cards: Lead, Content, Researcher, Graphics, Backend, Analyst
- Each card shows the role name and its colored badge
- Selecting a role submits a small form: `POST /admin/preview-role` with `role=content`
- If already in preview mode, "Member View" in sidebar should instead open the same modal pre-labeled as "Switch Role"

### 2. Preview Banner

Rendered in `layouts/app.blade.php` when `$isPreview` is true. Positioned below the top header, full-width, amber/warning tone so it is visually distinct from normal UI.

Contents:
- Left: `Viewing as:` + role badge (colored, matching existing `.role-badge` styles)
- Right: `Switch Role` button (opens picker modal) + `Return to Admin` button (sends `DELETE /admin/preview-role`)

The banner takes up fixed height and the main content area accounts for it (extra top padding).

### 3. Sidebar

The sidebar component (`components/sidebar.blade.php`) currently reads `$role` from `Auth::user()->role`. Changes:

- When `$isPreview` is true, override `$role` with `$previewRole` and set `$isAdmin = false`
- The sidebar brand subtitle changes from `'PR x Content'` to `'Previewing: [Role]'` when in preview mode
- The sidebar renders the member nav for the simulated role exactly as a real member of that role would see it

### 4. Read-Only Enforcement

A CSS class `.preview-locked` is added to form containers when `$isPreview` is true:

```css
.preview-locked { pointer-events: none; opacity: 0.6; }
```

In each affected view, the interactive container gets the class conditionally:

```blade
<div class="{{ $isPreview ? 'preview-locked' : '' }}">
    {{-- form / inputs here --}}
</div>
```

Pages that need this treatment:
- `end-of-day.blade.php` — EOD submission form
- `calendar.blade.php` — add/edit event actions
- `price-calculator.blade.php` — calculator inputs
- `data-gathering.blade.php` — any input fields
- `announcements.blade.php` — post announcement button (already role-gated, but banner still shown)

Pages that are naturally read-only and need no changes:
- `posting-procedure.blade.php`
- `ecommerce-requirements.blade.php`
- `important-links.blade.php`
- `team.blade.php`
- `brand-catalogs.blade.php`
- `dashboard.blade.php`

---

## Data Flow

```
Admin clicks "Member View" in sidebar
  → role picker modal opens (client-side only)
  → admin selects "Content"
  → POST /admin/preview-role { role: "content" }
  → AdminController stores session(['preview_role' => 'content'])
  → redirect to /dashboard

Every subsequent page load (member pages):
  → PreviewRoleComposer reads session('preview_role')
  → shares $isPreview = true, $previewRole = 'content' to all views
  → layouts/app.blade.php renders amber preview banner
  → sidebar.blade.php renders Content member nav
  → page adds .preview-locked to form containers

Admin clicks "Switch Role" in banner
  → same role picker modal opens
  → admin picks new role
  → POST /admin/preview-role overwrites session key
  → redirect to /dashboard

Admin clicks "Return to Admin"
  → DELETE /admin/preview-role
  → session()->forget('preview_role')
  → redirect to /admin/dashboard
```

---

## Files to Create / Modify

| File | Change |
|------|--------|
| `app/Http/Controllers/AdminController.php` | Add `setPreviewRole()` and `clearPreviewRole()` methods |
| `app/View/Composers/PreviewRoleComposer.php` | New — shares `$isPreview`, `$previewRole` to all views |
| `app/Providers/AppServiceProvider.php` | Register the view composer |
| `routes/web.php` | Add POST/DELETE `/admin/preview-role` routes |
| `resources/views/components/sidebar.blade.php` | Role/isAdmin override when in preview; label change; "Member View" link opens modal |
| `resources/views/layouts/app.blade.php` | Preview banner + role picker modal |
| `resources/views/end-of-day.blade.php` | Add `.preview-locked` to form |
| `resources/views/calendar.blade.php` | Add `.preview-locked` to interactive sections |
| `resources/views/price-calculator.blade.php` | Add `.preview-locked` to inputs |
| `resources/views/data-gathering.blade.php` | Add `.preview-locked` to inputs |
| `resources/views/announcements.blade.php` | Add `.preview-locked` to post action |

---

## Constraints

- Preview mode is only enterable by admins. If a non-admin somehow has the session key set, the View Composer checks `Auth::user()->isAdmin()` before activating preview.
- The `DELETE /admin/preview-role` route is inside the `admin` middleware group, so only admins can clear it via that route. Session also clears naturally on logout.
- No new database tables or columns required.
