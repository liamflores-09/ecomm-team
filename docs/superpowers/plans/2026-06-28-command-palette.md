# Command Palette Enhancement — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the existing command palette JS block with a role-aware, two-group (Navigation + Actions) palette that correctly filters pages per role and supports JS-triggered actions.

**Architecture:** All changes are in a single Blade template (`layouts/app.blade.php`). Two PHP variables (`$cmdIsAdmin`, `$cmdRole`) are computed in a `@php` block above the palette script and used to server-render the JS arrays. The `render()` function is updated to output two labelled groups; the Enter key handler is updated to either navigate or call a named JS function.

**Tech Stack:** Laravel Blade, vanilla JS, Font Awesome icons, existing `toggleTheme()` / `toggleNotifPanel()` / `openModal()` globals.

## Global Constraints

- All changes in `resources/views/layouts/app.blade.php` only — no new files, no new routes
- Palette script lives inside a single `(function() { ... })();` IIFE starting at line 835
- Use existing CSS classes: `.cmd-overlay`, `.cmd-palette`, `.cmd-input`, `.cmd-results`, `.cmd-group-label`, `.cmd-item`, `.ci-icon`, `.ci-name`, `.ci-desc`
- Action icon backgrounds use fixed hex colors (not CSS vars) so they stay colored at all times
- `toggleTheme()`, `toggleNotifPanel()`, `openModal()` are existing globals — do not redefine them
- Logout uses `document.querySelector('form[action*="logout"]').submit()` — the form has no ID

---

### Task 1: Fix isAdmin detection and rebuild role-aware navigation lists

**Files:**
- Modify: `resources/views/layouts/app.blade.php` (the `(function(){...})();` palette block, lines 835–924, and add a `@php` block just before it)
- Test: `tests/Feature/CommandPaletteTest.php` (new)

**Interfaces:**
- Produces: `$cmdIsAdmin` (bool, PHP), `$cmdRole` (string, PHP), `var adminPages`, `var memberPages` (JS arrays) consumed by Task 2's `render()`

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/CommandPaletteTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandPaletteTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    public function test_admin_sees_admin_pages_in_palette()
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        // Check unique palette description strings that only appear in the JS arrays
        $response->assertSee('Team activity', false);   // Daily Logs desc
        $response->assertSee('Role reports', false);    // Reports desc
        $response->assertSee('User management', false); // Users desc
        $response->assertSee('Manage brands', false);   // Brands desc
    }

    public function test_content_sees_content_pages_in_palette()
    {
        $user = $this->makeUser('content');
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertSee('Product posting guide', false); // Posting Procedure desc
        $response->assertSee('Collect product info', false);  // Data Gathering desc
        $response->assertSee('Platform rules', false);        // Requirements desc
        $response->assertSee('Log daily tasks', false);       // EOD desc
    }

    public function test_analyst_does_not_see_eod_or_calculator_in_palette()
    {
        $user = $this->makeUser('analyst');
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertDontSee('Log daily tasks', false); // EOD desc not in analyst palette
        $response->assertDontSee('Compute SRP', false);     // Calculator desc not in analyst palette
    }

    public function test_admin_does_not_see_member_view_action_in_preview()
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->withSession(['preview_role' => 'content'])
            ->get(route('dashboard'));
        $response->assertDontSee('openMemberView', false);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```
php artisan test tests/Feature/CommandPaletteTest.php
```

Expected: 4 failures (palette not yet updated).

- [ ] **Step 3: Add `@php` block and rebuild page arrays**

Find the existing `(function() {` palette block (around line 835). Directly **before** the `<script>` tag that opens this block, add a `@php` block and replace the old `var isAdmin`, `var userPages`, `var adminPages`, `var pages` lines with the following.

Add before the palette `<script>` tag:

```blade
@php
    $cmdIsAdmin = !$isPreview && Auth::check() && Auth::user()->isAdmin();
    $cmdRole    = $isPreview ? $previewRole : (Auth::check() ? Auth::user()->role : '');
@endphp
```

Then inside the IIFE, replace the block from `var activeIndex` through `var pages = isAdmin ? adminPages : userPages;` with:

```js
        var activeIndex = -1;
        var flatList    = [];
        var isAdmin     = {{ $cmdIsAdmin ? 'true' : 'false' }};

        var adminPages = [
            { name: 'Admin Dashboard', desc: 'Overview',          icon: 'fa-table-cells-large',  url: '{{ route("admin.dashboard") }}' },
            { name: 'Users',           desc: 'User management',   icon: 'fa-user-group',          url: '{{ route("admin.users") }}' },
            { name: 'Daily Logs',      desc: 'Team activity',     icon: 'fa-clock-rotate-left',   url: '{{ route("admin.daily-logs") }}' },
            { name: 'Reports',         desc: 'Role reports',      icon: 'fa-chart-column',        url: '{{ route("admin.reports") }}' },
            { name: 'Brands',          desc: 'Manage brands',     icon: 'fa-layer-group',         url: '{{ route("admin.brands") }}' },
            { name: 'Brand Catalogs',  desc: 'Browse catalogs',   icon: 'fa-book-open',           url: '{{ route("brand-catalogs") }}' },
            { name: 'Announcements',   desc: 'Team announcements',icon: 'fa-bullhorn',            url: '{{ route("announcements") }}' },
            { name: 'Calendar',        desc: 'Team calendar',     icon: 'fa-calendar-days',       url: '{{ route("calendar") }}' },
            { name: 'The Team',        desc: 'Team directory',    icon: 'fa-people-group',        url: '{{ route("team") }}' },
        ];

        var memberPages = [
            { name: 'Dashboard',      desc: 'Overview',            icon: 'fa-table-cells-large', url: '{{ route("dashboard") }}' },
            @if($cmdRole !== 'analyst')
            { name: 'EOD Report',     desc: 'Log daily tasks',     icon: 'fa-calendar-check',    url: '{{ route("end-of-day") }}' },
            { name: 'Price Calculator', desc: 'Compute SRP',       icon: 'fa-calculator',        url: '{{ route("price-calculator") }}' },
            { name: 'Important Links',  desc: 'Quick access',      icon: 'fa-bookmark',          url: '{{ route("important-links") }}' },
            { name: 'Calendar',         desc: 'Team calendar',     icon: 'fa-calendar-days',     url: '{{ route("calendar") }}' },
            @endif
            @if($cmdRole === 'content')
            { name: 'Posting Procedure',  desc: 'Product posting guide', icon: 'fa-list-check',           url: '{{ route("posting-procedure") }}' },
            { name: 'Requirements',       desc: 'Platform rules',        icon: 'fa-clipboard-list',       url: '{{ route("ecommerce-requirements") }}' },
            { name: 'Data Gathering',     desc: 'Collect product info',  icon: 'fa-magnifying-glass-chart', url: '{{ route("data-gathering") }}' },
            @endif
            { name: 'Brand Catalogs',  desc: 'Browse catalogs',    icon: 'fa-book-open',         url: '{{ route("brand-catalogs") }}' },
            { name: 'Announcements',   desc: 'Team announcements', icon: 'fa-bullhorn',           url: '{{ route("announcements") }}' },
            { name: 'The Team',        desc: 'Team directory',     icon: 'fa-people-group',       url: '{{ route("team") }}' },
        ];
```

- [ ] **Step 4: Run tests**

```
php artisan test tests/Feature/CommandPaletteTest.php
```

Expected: `test_admin_sees_admin_pages_in_palette` and `test_content_sees_content_pages_in_palette` pass. The analyst and preview tests may still fail — that's fine, they're covered next.

- [ ] **Step 5: Commit**

```
git add resources/views/layouts/app.blade.php tests/Feature/CommandPaletteTest.php
git commit -m "fix palette isAdmin detection and rebuild role-aware page lists"
```

---

### Task 2: Add Actions group, update render() and keyboard handler

**Files:**
- Modify: `resources/views/layouts/app.blade.php` (same palette IIFE)
- Test: `tests/Feature/CommandPaletteTest.php` (extend existing)

**Interfaces:**
- Consumes: `var isAdmin`, `var adminPages`, `var memberPages`, `$cmdIsAdmin`, `$isPreview` from Task 1
- Produces: complete palette with two groups, working action execution

- [ ] **Step 1: Add action array and handler functions to the IIFE**

Inside the IIFE, after the `memberPages` array, add:

```js
        var actions = [
            { name: 'Toggle Theme',  desc: 'Switch dark/light mode', icon: 'fa-moon',                   color: '#6366f1', fn: 'toggleTheme' },
            { name: 'Profile',       desc: 'Your profile',           icon: 'fa-user',                   color: '#0ea5e9', url: '{{ route("profile") }}' },
            { name: 'Notifications', desc: 'Open notifications',     icon: 'fa-bell',                   color: '#f59e0b', fn: 'openNotifPanel' },
            { name: 'Logout',        desc: 'Sign out',               icon: 'fa-right-from-bracket',     color: '#ef4444', fn: 'submitLogout' },
            @if($cmdIsAdmin && !$isPreview)
            { name: 'Member View',   desc: 'Preview a member role',  icon: 'fa-arrow-right-from-bracket', color: '#10b981', fn: 'openMemberView' },
            @endif
            @if(Auth::check() && Auth::user()->isAdmin())
            { name: 'New Announcement', desc: 'Post an announcement', icon: 'fa-bullhorn',              color: '#f59e0b', url: '{{ route("announcements") }}' },
            @endif
        ];

        function openNotifPanel() { toggleNotifPanel(); }
        function submitLogout()   { document.querySelector('form[action*="logout"]').submit(); }
        function openMemberView() { openModal('rolePickerModal'); }
```

- [ ] **Step 2: Replace `render()` with two-group version**

Replace the existing `function render(query) { ... }` and `window._cmdRender = render;` with:

```js
        function render(query) {
            var q = (query || '').toLowerCase();
            var pages = isAdmin ? adminPages : memberPages;
            var filteredPages   = pages.filter(function(p) { return p.name.toLowerCase().indexOf(q) !== -1 || p.desc.toLowerCase().indexOf(q) !== -1; });
            var filteredActions = actions.filter(function(a) { return a.name.toLowerCase().indexOf(q) !== -1 || a.desc.toLowerCase().indexOf(q) !== -1; });
            flatList = [];

            if (filteredPages.length === 0 && filteredActions.length === 0) {
                results.innerHTML = '<div style="text-align:center;padding:32px;color:var(--muted-foreground);font-size:14px;">No results' + (q ? ' for “' + q + '”' : '') + '</div>';
                return;
            }

            var html = '';

            if (filteredPages.length > 0) {
                html += '<div class="cmd-group-label">Navigation</div>';
                filteredPages.forEach(function(p) {
                    flatList.push(p);
                    html += '<a href="' + p.url + '" class="cmd-item" data-idx="' + (flatList.length - 1) + '">';
                    html += '<div class="ci-icon"><i class="fas ' + p.icon + '"></i></div>';
                    html += '<div style="flex:1;"><div class="ci-name">' + p.name + '</div><div class="ci-desc">' + p.desc + '</div></div>';
                    html += '</a>';
                });
            }

            if (filteredActions.length > 0) {
                html += '<div class="cmd-group-label" style="margin-top:4px;">Actions</div>';
                filteredActions.forEach(function(a) {
                    flatList.push(a);
                    var idx = flatList.length - 1;
                    if (a.fn) {
                        html += '<div class="cmd-item" data-idx="' + idx + '" data-action="' + a.fn + '" style="cursor:pointer;">';
                    } else {
                        html += '<a href="' + a.url + '" class="cmd-item" data-idx="' + idx + '">';
                    }
                    html += '<div class="ci-icon" style="background:' + a.color + ';color:white;"><i class="fas ' + a.icon + '"></i></div>';
                    html += '<div style="flex:1;"><div class="ci-name">' + a.name + '</div><div class="ci-desc">' + a.desc + '</div></div>';
                    html += a.fn ? '</div>' : '</a>';
                });
            }

            results.innerHTML = html;

            results.querySelectorAll('.cmd-item[data-action]').forEach(function(el) {
                el.addEventListener('click', function() {
                    var fn = this.getAttribute('data-action');
                    closePalette();
                    if (window[fn]) window[fn]();
                });
            });
        }
        window._cmdRender = render;
```

- [ ] **Step 3: Update Enter key handler to support actions**

Find the `if (e.key === 'Enter' && overlay.classList.contains('open'))` block and replace its body with:

```js
            if (e.key === 'Enter' && overlay.classList.contains('open')) {
                e.preventDefault();
                if (activeIndex >= 0 && flatList[activeIndex]) {
                    var item = flatList[activeIndex];
                    closePalette();
                    if (item.fn && window[item.fn]) {
                        window[item.fn]();
                    } else if (item.url) {
                        window.location.href = item.url;
                    }
                }
            }
```

- [ ] **Step 4: Run all palette tests**

```
php artisan test tests/Feature/CommandPaletteTest.php
```

Expected: all 4 tests pass.

- [ ] **Step 5: Manual smoke test in browser**

Open the app as admin, press `Ctrl+K`:
- Type "log" → Daily Logs appears under Navigation
- Type "theme" → Toggle Theme appears under Actions with purple icon
- Type "member" → Member View appears under Actions with green icon
- Press `Enter` on Toggle Theme → theme switches, palette closes
- Press `Esc` → palette closes

Open as analyst, press `Ctrl+K`:
- EOD Report and Price Calculator should NOT appear
- Brand Catalogs, Announcements, The Team should appear
- Member View should NOT appear in Actions

Enter preview mode as Content, press `Ctrl+K`:
- Member pages for Content role appear (Posting Procedure, Data Gathering, etc.)
- Member View action NOT in list
- New Announcement IS in list (admin is still the real user)

- [ ] **Step 6: Commit**

```
git add resources/views/layouts/app.blade.php tests/Feature/CommandPaletteTest.php
git commit -m "add actions group and fix palette render for two groups"
```
