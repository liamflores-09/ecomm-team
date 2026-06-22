# Important Links Page Revamp — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the sidebar-tab layout with a full-width pill-tab filter + card grid that matches the project design system.

**Architecture:** Single Blade file rewrite. Task 1 establishes the CSS and structural skeleton (tab row + JS). Task 2 inserts all category sections with link cards. No backend changes needed — the page is fully static.

**Tech Stack:** Laravel 11, Blade, Tailwind-adjacent CSS custom properties, FontAwesome 6, vanilla JS (IIFE)

## Global Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards and icon boxes, `9999px` for pill tabs
- CSS variables only for structural colors — no hardcoded hex for layout
- No `transform` on hover — hover is border-color change only (`border-color: var(--foreground)`)
- Active tab: `var(--primary)` background, `var(--primary)` border, white text
- Pre-existing test failure: `Tests\Feature\ExampleTest` returns 302 on `GET /` — this is NOT a regression, ignore it
- Run tests with: `php artisan test` (Windows — use PowerShell)

---

### Task 1: CSS Overhaul + Tab Row + JS

**Files:**
- Modify: `resources/views/important-links.blade.php`

**Interfaces:**
- Produces: CSS classes `.il-tabs`, `.il-tab`, `.il-tab.active`, `.il-tab-count`, `.il-section`, `.il-hd`, `.il-hd-name`, `.il-hd-count`, `.il-grid`, `.il-card`, `.il-card-top`, `.il-card-icon`, `.il-card-ext`, `.il-card-name`, `.il-card-type` — all consumed by Task 2

- [ ] **Step 1: Run tests to establish baseline**

```powershell
php artisan test
```

Expected: 1 passed, 1 failed (`Tests\Feature\ExampleTest` 302). Note these numbers — no new failures should appear after your changes.

- [ ] **Step 2: Replace `@section('styles')` entirely**

Delete everything between `@section('styles')` and `@endsection` (the opening/closing tags of the styles section) and replace with:

```blade
@section('styles')
<style>
    /* ── Tabs ─────────────────────────────────────────────────── */
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

    /* ── Section headers ──────────────────────────────────────── */
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

    /* ── Link grid ────────────────────────────────────────────── */
    .il-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    /* ── Link cards ───────────────────────────────────────────── */
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
        transition: background 0.2s, color 0.2s;
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

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 768px) {
        .il-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .il-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
```

- [ ] **Step 3: Replace `@section('content')` with the structural skeleton**

Delete everything between `@section('content')` and `@endsection` and replace with:

```blade
@section('content')
<x-sidebar active="important-links" />

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Important <span class="highlight">Links</span></h2>
            <p>Quick access to essential resources and tracking sheets</p>
        </div>
    </div>

    <div class="il-tabs anim-up d1">
        <button class="il-tab active" data-filter="all">All <span class="il-tab-count">10</span></button>
        <button class="il-tab" data-filter="skus">Posted SKUs <span class="il-tab-count">2</span></button>
        <button class="il-tab" data-filter="reports">Reports <span class="il-tab-count">4</span></button>
        <button class="il-tab" data-filter="dirs">Directories <span class="il-tab-count">3</span></button>
        <button class="il-tab" data-filter="training">Training <span class="il-tab-count">1</span></button>
    </div>

    {{-- Category sections go here (Task 2) --}}

    <script>
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
    </script>
</div>
@endsection
```

- [ ] **Step 4: Remove the `@section('scripts')` block**

Delete the entire block:

```blade
@section('scripts')
<script>
function switchTab(id, btn) {
    document.querySelectorAll('.cat-tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.link-panel').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('panel-' + id).classList.add('active');
}
</script>
@endsection
```

The file should now have no `@section('scripts')` block at all.

- [ ] **Step 5: Run tests**

```powershell
php artisan test
```

Expected: same result as Step 1 — 1 passed, 1 failed (pre-existing 302). No new failures.

- [ ] **Step 6: Commit**

```powershell
git add resources/views/important-links.blade.php
git commit -m "ui: important links CSS overhaul and tab row"
```

---

### Task 2: Category Sections + Link Cards

**Files:**
- Modify: `resources/views/important-links.blade.php`

**Interfaces:**
- Consumes: All CSS classes from Task 1 — `.il-section`, `.il-hd`, `.il-hd-name`, `.il-hd-count`, `.il-grid`, `.il-card`, `.il-card-top`, `.il-card-icon`, `.il-card-ext`, `.il-card-name`, `.il-card-type`
- Tab `data-filter` values from Task 1: `skus`, `reports`, `dirs`, `training` — the `data-category` on each section wrapper **must match exactly**

- [ ] **Step 1: Replace the `{{-- Category sections go here (Task 2) --}}` comment**

Find this line in `@section('content')`:

```blade
    {{-- Category sections go here (Task 2) --}}
```

Replace it with all 4 category sections:

```blade
    <!-- Posted SKUs -->
    <div class="il-section" data-category="skus">
        <div class="il-hd">
            <span class="il-hd-name">Posted SKUs</span>
            <span class="il-hd-count">2 links</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content x PR Posted SKUs 2026</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content x PR Posted SKUs 2025</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
        </div>
    </div>

    <!-- Reports -->
    <div class="il-section" data-category="reports">
        <div class="il-hd">
            <span class="il-hd-name">Reports & Tracking</span>
            <span class="il-hd-count">4 links</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content x GA Dept Report 2026 V2</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">JG QC Tracker</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Operation x Content Inactive Monitoring</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-table"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">JG Ecom CP Tracker</div>
                <div class="il-card-type">Google Sheet</div>
            </a>
        </div>
    </div>

    <!-- Directories -->
    <div class="il-section" data-category="dirs">
        <div class="il-hd">
            <span class="il-hd-name">Directories & Master Files</span>
            <span class="il-hd-count">3 links</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">JG SUPERSTORE ECOMMERCE DIRECTORY</div>
                <div class="il-card-type">Folder</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Change SKU Tracker</div>
                <div class="il-card-type">Folder</div>
            </a>
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Freebie & Update CVP Monitoring V2 2026</div>
                <div class="il-card-type">Folder</div>
            </a>
        </div>
    </div>

    <!-- Training -->
    <div class="il-section" data-category="training">
        <div class="il-hd">
            <span class="il-hd-name">Training Resources</span>
            <span class="il-hd-count">1 link</span>
        </div>
        <div class="il-grid">
            <a href="#" target="_blank" class="il-card">
                <div class="il-card-top">
                    <div class="il-card-icon"><i class="fas fa-folder-open"></i></div>
                    <i class="fas fa-arrow-up-right-from-square il-card-ext"></i>
                </div>
                <div class="il-card-name">Content Associate Training Files</div>
                <div class="il-card-type">Folder</div>
            </a>
        </div>
    </div>
```

- [ ] **Step 2: Verify data-category values match tab data-filter values**

Do a quick self-check — each `data-category` on a section must exactly match the corresponding `data-filter` on the tab button from Task 1:

| Tab `data-filter` | Section `data-category` |
|-------------------|------------------------|
| `skus`            | `skus` ✓               |
| `reports`         | `reports` ✓            |
| `dirs`            | `dirs` ✓               |
| `training`        | `training` ✓           |

If any value doesn't match, fix it before continuing.

- [ ] **Step 3: Verify link and section counts**

Check that the `il-tab-count` values in the tab row match the actual number of `.il-card` elements per section, and that `il-hd-count` labels match:

| Section  | Tab count | Cards in grid | `il-hd-count` label |
|----------|-----------|---------------|----------------------|
| skus     | 2         | 2             | "2 links"            |
| reports  | 4         | 4             | "4 links"            |
| dirs     | 3         | 3             | "3 links"            |
| training | 1         | 1             | "1 link"             |
| All tab  | 10        | 10 total      | —                    |

- [ ] **Step 4: Run tests**

```powershell
php artisan test
```

Expected: 1 passed, 1 failed (pre-existing 302 only). No new failures.

- [ ] **Step 5: Commit**

```powershell
git add resources/views/important-links.blade.php
git commit -m "ui: important links category sections and link cards"
```
