# Brand Catalogs — Design Spec

**Date:** 2026-06-23
**Scope:** New feature — brand catalog browser with admin management

---

## Goal

Give the team a central place to browse brand catalogs, with a focus on upcoming new products. Admin manages brands; Admin and Researcher manage catalogs; all roles can view.

## Design System Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards and inputs, `9999px` for pill tabs
- CSS variables only for structural colors — no hardcoded hex for layout
- No `transform` on hover — hover is border-color change only
- `var(--primary)` = `#5757f8` for active tabs and primary actions
- Font: Space Grotesk for headings, Inter for body

---

## New Files

| File | Purpose |
|------|---------|
| `database/migrations/..._create_brands_table.php` | brands schema |
| `database/migrations/..._create_brand_catalogs_table.php` | brand_catalogs schema |
| `app/Models/Brand.php` | Brand Eloquent model |
| `app/Models/BrandCatalog.php` | BrandCatalog Eloquent model |
| `app/Http/Controllers/BrandCatalogController.php` | browse + CRUD for catalogs |
| `app/Http/Controllers/Admin/BrandController.php` | admin brand CRUD |
| `resources/views/brand-catalogs.blade.php` | browse page |
| `resources/views/admin/brands.blade.php` | admin brand management |

---

## Data Model

### `brands` table

| column | type | notes |
|--------|------|-------|
| id | bigint PK | auto-increment |
| name | string | e.g. "Samsung", "Anker" |
| description | string nullable | short tagline |
| logo | string nullable | file path under `storage/app/public/brands/` |
| created_at / updated_at | timestamps | |

### `brand_catalogs` table

| column | type | notes |
|--------|------|-------|
| id | bigint PK | auto-increment |
| brand_id | unsignedBigInteger FK → brands.id | cascades on delete |
| title | string | catalog name |
| notes | text nullable | description / what's notable |
| status | enum: `available`, `upcoming`, `seasonal` | |
| link | string nullable | external URL (Google Drive, brand site, etc.) |
| file_path | string nullable | uploaded file path under `storage/app/public/catalogs/` |
| created_at / updated_at | timestamps | |

At least one of `link` or `file_path` must be non-null — enforced at the controller level.

---

## Routes

```php
// All authenticated users
Route::get('/brand-catalogs', [BrandCatalogController::class, 'index'])->name('brand-catalogs');

// Admin + Researcher only (middleware: 'catalog.manager')
Route::post('/brand-catalogs', [BrandCatalogController::class, 'store'])->name('brand-catalogs.store');
Route::put('/brand-catalogs/{catalog}', [BrandCatalogController::class, 'update'])->name('brand-catalogs.update');
Route::delete('/brand-catalogs/{catalog}', [BrandCatalogController::class, 'destroy'])->name('brand-catalogs.destroy');

// Admin only (existing admin middleware)
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/brands', [BrandController::class, 'index'])->name('admin.brands');
    Route::post('/brands', [BrandController::class, 'store'])->name('admin.brands.store');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('admin.brands.destroy');
});
```

### `catalog.manager` middleware

A new middleware that allows through users whose role is `admin` or `researcher`. Returns 403 for all other roles.

---

## File Storage

Use Laravel's `public` disk with a storage symlink (`php artisan storage:link`):

- Brand logos: `storage/app/public/brands/{filename}` → served at `/storage/brands/{filename}`
- Catalog files: `storage/app/public/catalogs/{filename}` → served at `/storage/catalogs/{filename}`

Accepted file types:
- Logos: `jpg`, `jpeg`, `png`, `svg`, `webp` — max 2MB
- Catalogs: `pdf`, `jpg`, `jpeg`, `png` — max 10MB

On update, the old file is deleted from storage before saving the new one.

---

## Change 1 — Browse Page (`/brand-catalogs`)

### Layout

Top bar with page title "Brand Catalogs" and subtitle. An "Add Catalog" button appears on the right for admin and researcher roles only.

Below the top bar:

1. **Brand pill tabs** — "All" first, then one pill per brand. Clicking a brand pill filters to show only that brand's catalogs.
2. **Status pill row** — smaller pills below the brand row: All / Available / Upcoming / Seasonal. Works in combination with the brand filter.
3. **Catalog card grid** — 3 columns on desktop, 2 on tablet (≤768px), 1 on mobile (≤480px).

### Catalog Card

```
┌─────────────────────────────────┐
│ [Brand Logo / Initial]  [Badge] │
│                                 │
│ Catalog Title (bold)            │
│ Brand Name (small muted)        │
│                                 │
│ Notes preview (2 lines max)     │
│                                 │
│ [🔗] [📄]         Jun 23, 2026  │
│                        [✏] [🗑] │  ← admin/researcher only
└─────────────────────────────────┘
```

- **Brand logo**: 40×40px square, `border-radius: 8px`. If no logo, colored circle with brand initial (color derived from brand name, picking from the design system palette).
- **Status badge**: pill tag — green (`var(--success)`) for Available, violet (`var(--primary)`) for Upcoming, amber for Seasonal.
- **Link icon** (`fa-link`): shown if `link` is set — clicking opens the URL in a new tab.
- **File icon** (`fa-file-pdf` or `fa-image`): shown if `file_path` is set — clicking downloads/opens the file.
- **Edit/Delete**: icon buttons, visible only to admin and researcher, positioned bottom-right of card.

### Card CSS classes

```
.bc-grid         — 3-col grid wrapper
.bc-card         — individual card (flex-column, border, 8px radius, hover border-color)
.bc-brand-logo   — 40px square logo / initial circle
.bc-badge        — status pill (9999px radius)
.bc-badge.available / .upcoming / .seasonal
.bc-title        — catalog title
.bc-brand-name   — brand label (small, muted)
.bc-notes        — notes preview (2-line clamp)
.bc-meta         — bottom row (icons + date + actions)
```

### Tab filter JS

Inline IIFE at the bottom of `@section('content')`, same pattern as Important Links:

```javascript
(function () {
    var brandTabs = document.querySelectorAll('.bc-brand-tab');
    var statusTabs = document.querySelectorAll('.bc-status-tab');
    var cards = document.querySelectorAll('.bc-card');

    var activeBrand = 'all';
    var activeStatus = 'all';

    function applyFilters() {
        cards.forEach(function (card) {
            var brandMatch = activeBrand === 'all' || card.dataset.brand === activeBrand;
            var statusMatch = activeStatus === 'all' || card.dataset.status === activeStatus;
            card.style.display = (brandMatch && statusMatch) ? '' : 'none';
        });
    }

    brandTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            brandTabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            activeBrand = tab.dataset.brand;
            applyFilters();
        });
    });

    statusTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            statusTabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            activeStatus = tab.dataset.status;
            applyFilters();
        });
    });
}());
```

---

## Change 2 — Add/Edit Catalog Modal

Triggered by "Add Catalog" button (add mode) or the edit icon on a card (edit mode). Uses the app's custom modal system (`openModal` / `closeModal`).

### Form fields

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| Brand | `<select>` dropdown | Yes | Populated from all brands |
| Title | text input | Yes | |
| Status | `<select>`: Available / Upcoming / Seasonal | Yes | |
| Notes | textarea | No | |
| External Link | URL input | No* | |
| Upload File | file input (pdf/jpg/png) | No* | |

*At least one of link or file must be filled. Controller validates: `$request->filled('link') || $request->hasFile('file')`.

In edit mode:
- All fields pre-filled via a data attribute on the edit button (JSON-encoded catalog data)
- If a file exists, show the filename with a "Replace file" toggle
- JS populates the modal form fields from the button's data attributes

### Delete

Separate small form per card. `onsubmit="return confirm('Delete this catalog?')"` before POST with `@method('DELETE')`.

---

## Change 3 — Admin Brand Management (`/admin/brands`)

New section in the admin panel. Added as a nav tab alongside the existing admin sections.

### Brand table

Columns: Logo thumbnail (32px), Name, Description, Catalog count, Actions (Edit / Delete).

### Add/Edit Brand Modal

Fields:
| Field | Type | Required |
|-------|------|----------|
| Name | text input | Yes |
| Description | short text input | No |
| Logo | image file input (jpg/png/svg/webp, max 2MB) | No |

In edit mode, the current logo is shown as a preview above the file input with a "Replace logo" label.

### Delete brand rule

If the brand has one or more catalogs attached, delete is blocked. The controller returns back with an error: `"Cannot delete brand with existing catalogs. Remove the catalogs first."` No cascade delete on brands — only on `brand_catalogs` when a brand is hard-deleted directly via migration (the FK has `cascadeOnDelete` for safety, but the UI blocks it).

---

## Sidebar + Command Palette

- Add "Brand Catalogs" to the sidebar under the existing nav links (all roles see it).
- Add to the command palette pages list in `app.blade.php` with icon `fa-book-open` and route `brand-catalogs`.

---

## What Is Not Changing

- Existing admin panel structure and middleware
- All other pages and routes
- User roles and auth system
- No backend notification or activity log for catalog changes (can be added later)
