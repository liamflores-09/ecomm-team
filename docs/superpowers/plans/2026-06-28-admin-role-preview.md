# Admin Role Preview Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow admins to enter a read-only preview of any member role, with role-accurate sidebar nav, a persistent amber banner, and all forms/inputs fully disabled.

**Architecture:** Session-based — `session('preview_role')` stores the active role string. A Laravel View Composer injects `$isPreview` (bool) and `$previewRole` (string|null) into every Blade view. The sidebar, layout banner, and member pages all react to these two variables. No DB changes needed.

**Tech Stack:** PHP/Laravel — Blade components, session, View Composers, middleware groups, PHPUnit feature tests.

## Global Constraints

- Admin roles (only these can enter preview): `manager`, `head` — checked via `Auth::user()->isAdmin()`
- Previewable member roles: `lead`, `content`, `researcher`, `graphics`, `backend`, `analyst`
- No DB migrations
- Commit style: short casual (e.g. `"add preview routes"`, not verbose descriptions)
- No `Co-Authored-By` lines in commits

---

## File Structure

| File | Status | Responsibility |
|------|--------|----------------|
| `app/Http/Controllers/AdminController.php` | Modify | Add `setPreviewRole()` and `clearPreviewRole()` |
| `app/View/Composers/PreviewRoleComposer.php` | Create | Share `$isPreview`, `$previewRole` to every view |
| `app/Providers/AppServiceProvider.php` | Modify | Register the view composer |
| `routes/web.php` | Modify | POST + DELETE `/admin/preview-role` routes |
| `resources/views/layouts/app.blade.php` | Modify | Preview banner, role picker modal, CSS |
| `resources/views/components/sidebar.blade.php` | Modify | Preview-aware role/isAdmin override; "Member View" opens modal |
| `resources/views/end-of-day.blade.php` | Modify | `.preview-locked` on form card + inline notice |
| `resources/views/calendar.blade.php` | Modify | `.preview-locked` on event drawer |
| `resources/views/price-calculator.blade.php` | Modify | `.preview-locked` on add-card |
| `resources/views/announcements.blade.php` | Modify | Hide New button + lock form drawer |
| `tests/Feature/AdminPreviewRoleTest.php` | Create | Feature tests for routes and view composer |

---

### Task 1: Routes + Controller Methods

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/AdminController.php`
- Create: `tests/Feature/AdminPreviewRoleTest.php`

**Interfaces:**
- Produces: `route('admin.preview-role.set')` — POST, body: `{role: string}`
- Produces: `route('admin.preview-role.clear')` — DELETE

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/AdminPreviewRoleTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPreviewRoleTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    private function makeMember(): User
    {
        return User::factory()->create(['role' => 'content']);
    }

    public function test_admin_can_set_preview_role(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->post(route('admin.preview-role.set'), ['role' => 'content']);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('content', session('preview_role'));
    }

    public function test_admin_can_clear_preview_role(): void
    {
        $admin = $this->makeAdmin();
        session(['preview_role' => 'content']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.preview-role.clear'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertNull(session('preview_role'));
    }

    public function test_invalid_role_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->post(route('admin.preview-role.set'), ['role' => 'hacker']);

        $response->assertSessionHasErrors('role');
    }

    public function test_non_admin_cannot_set_preview_role(): void
    {
        $member = $this->makeMember();
        $response = $this->actingAs($member)
            ->post(route('admin.preview-role.set'), ['role' => 'lead']);

        $response->assertForbidden();
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test tests/Feature/AdminPreviewRoleTest.php
```

Expected: 4 failures — routes and methods don't exist yet.

- [ ] **Step 3: Add routes to `routes/web.php`**

Inside the existing `Route::middleware(['admin'])->prefix('admin')->group(...)` block, before its closing `});`, add:

```php
Route::post('/preview-role', [AdminController::class, 'setPreviewRole'])->name('admin.preview-role.set');
Route::delete('/preview-role', [AdminController::class, 'clearPreviewRole'])->name('admin.preview-role.clear');
```

- [ ] **Step 4: Add methods to `AdminController.php`**

Add these two public methods anywhere inside the `AdminController` class body:

```php
public function setPreviewRole(Request $request)
{
    $request->validate([
        'role' => 'required|in:lead,content,researcher,graphics,backend,analyst',
    ]);
    session(['preview_role' => $request->role]);
    return redirect()->route('dashboard');
}

public function clearPreviewRole()
{
    session()->forget('preview_role');
    return redirect()->route('admin.dashboard');
}
```

- [ ] **Step 5: Run tests to confirm they pass**

```bash
php artisan test tests/Feature/AdminPreviewRoleTest.php
```

Expected: 4 tests, all green.

- [ ] **Step 6: Commit**

```bash
git add routes/web.php app/Http/Controllers/AdminController.php tests/Feature/AdminPreviewRoleTest.php
git commit -m "add preview role routes and controller methods"
```

---

### Task 2: View Composer

**Files:**
- Create: `app/View/Composers/PreviewRoleComposer.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `tests/Feature/AdminPreviewRoleTest.php` (add 2 tests)

**Interfaces:**
- Produces: `$isPreview` (bool) and `$previewRole` (string|null) injected into every Blade view

- [ ] **Step 1: Add composer tests to the existing test file**

Append these two methods inside the `AdminPreviewRoleTest` class:

```php
public function test_preview_variables_shared_when_session_set(): void
{
    $admin = $this->makeAdmin();
    session(['preview_role' => 'lead']);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertViewHas('isPreview', true);
    $response->assertViewHas('previewRole', 'lead');
}

public function test_preview_variables_false_when_session_empty(): void
{
    $admin = $this->makeAdmin();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertViewHas('isPreview', false);
    $response->assertViewHas('previewRole', null);
}
```

- [ ] **Step 2: Run to confirm the two new tests fail**

```bash
php artisan test tests/Feature/AdminPreviewRoleTest.php --filter=preview_variables
```

Expected: 2 failures.

- [ ] **Step 3: Create `app/View/Composers/PreviewRoleComposer.php`**

```php
<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PreviewRoleComposer
{
    public function compose(View $view): void
    {
        $isPreview = false;
        $previewRole = null;

        if (Auth::check() && Auth::user()->isAdmin() && session()->has('preview_role')) {
            $previewRole = session('preview_role');
            $isPreview = true;
        }

        $view->with('isPreview', $isPreview)
             ->with('previewRole', $previewRole);
    }
}
```

- [ ] **Step 4: Register the composer in `app/Providers/AppServiceProvider.php`**

Replace the entire file with:

```php
<?php

namespace App\Providers;

use App\View\Composers\PreviewRoleComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', PreviewRoleComposer::class);
    }
}
```

- [ ] **Step 5: Run the full test suite to confirm all 6 pass**

```bash
php artisan test tests/Feature/AdminPreviewRoleTest.php
```

Expected: 6 tests, all green.

- [ ] **Step 6: Commit**

```bash
git add app/View/Composers/PreviewRoleComposer.php app/Providers/AppServiceProvider.php tests/Feature/AdminPreviewRoleTest.php
git commit -m "add preview role view composer"
```

---

### Task 3: Preview Banner + Role Picker Modal

**Files:**
- Modify: `resources/views/layouts/app.blade.php`

**Interfaces:**
- Consumes: `$isPreview` (bool), `$previewRole` (string|null) — from Task 2
- Consumes: `route('admin.preview-role.set')`, `route('admin.preview-role.clear')` — from Task 1
- Produces: JS function `openModal('rolePickerModal')` — called by the sidebar in Task 4

- [ ] **Step 1: Add CSS to `app.blade.php`**

In the `<style>` block, find the closing `</style>` tag just before `@yield('styles')` and insert the following block immediately before it:

```css
/* Preview mode */
.preview-banner {
    position: fixed;
    top: 64px; left: 0; right: 0;
    height: 40px;
    background: #fef3c7;
    border-bottom: 1px solid #f59e0b;
    z-index: 45;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px 0 280px;
    font-size: 13px;
    font-weight: 500;
    color: #92400e;
}
[data-theme="dark"] .preview-banner {
    background: #1c1100;
    border-bottom-color: #92400e;
    color: #fde68a;
}
body.preview-mode .sidebar { top: 104px; height: calc(100vh - 104px); }
body.preview-mode .main-content { padding-top: 136px; }
@media (max-width: 1024px) {
    .preview-banner { padding-left: 20px; }
    body.preview-mode .main-content { padding-top: 136px; margin-left: 0; }
}
.preview-locked { pointer-events: none; opacity: 0.55; user-select: none; }
```

- [ ] **Step 2: Add `preview-mode` class to `<body>`**

Find:

```blade
<body class="bg-background text-foreground">
```

Replace with:

```blade
<body class="bg-background text-foreground {{ $isPreview ? 'preview-mode' : '' }}">
```

- [ ] **Step 3: Add preview banner after `</header>`**

Find the closing `</header>` tag (it closes the `<header class="top-header">` element) and insert this block immediately after it:

```blade
@if($isPreview)
<div class="preview-banner">
    <div style="display:flex;align-items:center;gap:8px;">
        <i class="fas fa-eye" style="color:#f59e0b;font-size:12px;"></i>
        <span>Viewing as:</span>
        <span class="role-badge {{ $previewRole }}">{{ ucfirst($previewRole) }}</span>
        <span style="color:#b45309;font-size:12px;">— read-only, no submissions</span>
    </div>
    <div style="display:flex;align-items:center;gap:6px;">
        <button onclick="openModal('rolePickerModal')"
            style="height:28px;padding:0 10px;border:1px solid #f59e0b;border-radius:var(--radius);background:transparent;cursor:pointer;font-size:12px;font-weight:600;color:#92400e;font-family:Inter,sans-serif;transition:background 0.15s;"
            onmouseover="this.style.background='rgba(245,158,11,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-arrows-rotate" style="font-size:10px;margin-right:4px;"></i>Switch Role
        </button>
        <form method="POST" action="{{ route('admin.preview-role.clear') }}" style="margin:0;">
            @csrf
            @method('DELETE')
            <button type="submit"
                style="height:28px;padding:0 10px;border:none;border-radius:var(--radius);background:#f59e0b;cursor:pointer;font-size:12px;font-weight:600;color:white;font-family:Inter,sans-serif;transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-arrow-left" style="font-size:10px;margin-right:4px;"></i>Return to Admin
            </button>
        </form>
    </div>
</div>
@endif
```

- [ ] **Step 4: Add role picker modal**

Find the comment `<!-- Global Confirm Dialog -->` and insert the following block immediately before it:

```blade
{{-- Role Picker Modal --}}
@if(Auth::check() && Auth::user()->isAdmin())
<div class="modal-overlay" id="rolePickerModal">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <h5><i class="fas fa-eye" style="color:var(--primary);margin-right:6px;font-size:0.9rem;"></i>{{ $isPreview ? 'Switch Preview Role' : 'Preview as Role' }}</h5>
            <button class="modal-close" onclick="closeModal('rolePickerModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.82rem;color:var(--muted-foreground);margin:0 0 14px;">Select a role to preview the member experience. All inputs will be read-only.</p>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                @foreach(['lead' => 'Lead', 'content' => 'Content', 'researcher' => 'Researcher', 'graphics' => 'Graphics', 'backend' => 'Backend', 'analyst' => 'Analyst'] as $roleKey => $roleLabel)
                <form method="POST" action="{{ route('admin.preview-role.set') }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="role" value="{{ $roleKey }}">
                    <button type="submit" style="width:100%;padding:10px 12px;border:1.5px solid {{ ($previewRole ?? '') === $roleKey ? 'var(--primary)' : 'var(--border-light)' }};border-radius:var(--radius);background:{{ ($previewRole ?? '') === $roleKey ? 'var(--primary)' : 'var(--card)' }};cursor:pointer;text-align:left;transition:all 0.15s;color:{{ ($previewRole ?? '') === $roleKey ? 'white' : 'var(--foreground)' }};">
                        <span class="role-badge {{ $roleKey }}">{{ $roleLabel }}</span>
                        <div style="margin-top:5px;font-size:0.78rem;font-weight:500;font-family:Inter,sans-serif;">{{ $roleLabel }} member view</div>
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
```

- [ ] **Step 5: Manual test**

```bash
php artisan serve
```

Log in as an admin. In a terminal, set the session via Tinker to simulate preview:

```bash
php artisan tinker
# Then: session(['preview_role' => 'content'])  ← won't work directly in Tinker
```

Instead, use the role picker once it's wired up in Task 4. For now, confirm:
- Admin pages load without errors
- No banner on admin pages (session key not set)
- The modal HTML is present in the DOM (check browser DevTools for `#rolePickerModal`)

- [ ] **Step 6: Commit**

```bash
git add resources/views/layouts/app.blade.php
git commit -m "add preview banner and role picker modal"
```

---

### Task 4: Sidebar Preview Awareness

**Files:**
- Modify: `resources/views/components/sidebar.blade.php`

**Interfaces:**
- Consumes: `$isPreview` (bool), `$previewRole` (string|null) — from Task 2
- Consumes: `openModal('rolePickerModal')` — JS function added in Task 3

- [ ] **Step 1: Override `$role` and `$isAdmin` when in preview**

Find the existing top two lines of the file:

```blade
@props(['active' => '', 'isAdmin' => false])

@php $role = Auth::user()->role ?? ''; @endphp
```

Replace with:

```blade
@props(['active' => '', 'isAdmin' => false])

@php
    $role = Auth::user()->role ?? '';
    if ($isPreview) {
        $role = $previewRole;
        $isAdmin = false;
    }
@endphp
```

- [ ] **Step 2: Update the brand subtitle**

Find:

```blade
<span>{{ $isAdmin ? 'Admin Panel' : 'PR x Content' }}</span>
```

Replace with:

```blade
<span>{{ $isPreview ? 'Previewing: ' . ucfirst($previewRole) : ($isAdmin ? 'Admin Panel' : 'PR x Content') }}</span>
```

- [ ] **Step 3: Change "Member View" link to open the role picker modal**

Find:

```blade
<li><a href="{{ route('dashboard') }}"        class="{{ $active === 'dashboard'       ? 'active' : '' }}"><i class="fas fa-arrow-right-from-bracket"></i> Member View</a></li>
```

Replace with:

```blade
<li><a href="#" onclick="openModal('rolePickerModal');return false;" class="{{ $active === 'dashboard' ? 'active' : '' }}"><i class="fas fa-arrow-right-from-bracket"></i> Member View</a></li>
```

- [ ] **Step 4: Manual test — full preview flow**

```bash
php artisan serve
```

1. Log in as admin → navigate to `/admin`
2. Click "Member View" in the sidebar → role picker modal opens (not a page navigation)
3. Click "Content" → redirected to `/dashboard`
4. Confirm: amber banner shows "Viewing as: Content — read-only, no submissions"
5. Confirm: sidebar brand subtitle reads "Previewing: Content"
6. Confirm: sidebar nav shows only Content member links (Posting Procedure, Requirements, EOD, Announcements, Brand Catalogs, Tools section, The Team)
7. Confirm: sidebar does NOT show any admin links (Users, Daily Logs, Reports, Brands)
8. Click "Switch Role" in banner → role picker modal opens with Content card highlighted
9. Click "Lead" → page reloads, sidebar switches to Lead nav
10. Click "Return to Admin" → redirected to `/admin/dashboard`, no banner, admin sidebar restored

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/sidebar.blade.php
git commit -m "make sidebar preview-aware"
```

---

### Task 5: Read-Only Enforcement on Member Pages

**Files:**
- Modify: `resources/views/end-of-day.blade.php`
- Modify: `resources/views/calendar.blade.php`
- Modify: `resources/views/price-calculator.blade.php`
- Modify: `resources/views/announcements.blade.php`

**Interfaces:**
- Consumes: `$isPreview` (bool) — from Task 2
- Consumes: `.preview-locked` CSS — defined in Task 3

- [ ] **Step 1: Lock EOD form + add inline notice**

In `end-of-day.blade.php`, find the line `@if (session('error'))` block and the line just after it (the log form card):

```blade
<!-- Log Form -->
<div class="eod-card anim-up d1">
```

Replace with:

```blade
<!-- Log Form -->
@if($isPreview)
<div class="alert-flat anim-fade" style="background:#fef3c7;color:#92400e;border:1px solid #f59e0b;margin-bottom:12px;"><i class="fas fa-eye"></i> Admin preview — form is read-only</div>
@endif
<div class="eod-card anim-up d1 {{ $isPreview ? 'preview-locked' : '' }}">
```

- [ ] **Step 2: Lock price calculator add-card**

In `price-calculator.blade.php`, find:

```blade
<div class="add-card anim-up d1">
```

Replace with:

```blade
<div class="add-card anim-up d1 {{ $isPreview ? 'preview-locked' : '' }}">
```

- [ ] **Step 3: Lock calendar event drawer**

In `calendar.blade.php`, find:

```blade
<div class="cal-ev-drawer" id="evDrawer">
```

Replace with:

```blade
<div class="cal-ev-drawer {{ $isPreview ? 'preview-locked' : '' }}" id="evDrawer">
```

- [ ] **Step 4: Hide announcements New button + lock form drawer**

In `announcements.blade.php`, find:

```blade
<button class="ann-new-btn" onclick="openForm()"><i class="fas fa-plus"></i> New</button>
```

Replace with:

```blade
@if(!$isPreview)
<button class="ann-new-btn" onclick="openForm()"><i class="fas fa-plus"></i> New</button>
@endif
```

Then find:

```blade
<div class="ann-form-overlay" id="annFormOverlay" onclick="closeForm()"></div>
```

The `ann-form-drawer` div immediately follows this line. Find that line (it starts with `<div class="ann-form-drawer"`) and add the preview class:

```blade
<div class="ann-form-drawer {{ $isPreview ? 'preview-locked' : '' }}"
```

(Preserve everything else on that line after the opening tag — the `id`, `style`, or other attributes that follow.)

- [ ] **Step 5: Full end-to-end manual test**

```bash
php artisan serve
```

1. Log in as admin → "Member View" → pick "Content"
2. **EOD:** Click "EOD Report" in sidebar → amber notice appears above the card, card is dimmed, clicking inputs does nothing
3. **Price Calculator:** Click "Price Calculator" → add-card is dimmed and unclickable
4. **Calendar:** Click "Calendar" → calendar renders normally (read-only nav), clicking a date opens the drawer but it is dimmed and unclickable
5. **Announcements:** Click "Announcements" → "New" button is absent from the panel header; list of announcements is visible and browsable
6. **Switch to Analyst:** Click "Switch Role" → pick "Analyst" → sidebar switches (no EOD, no Price Calculator, no Calendar per analyst rules)
7. **Return:** Click "Return to Admin" → `/admin/dashboard`, full admin sidebar, no banner

- [ ] **Step 6: Commit**

```bash
git add resources/views/end-of-day.blade.php resources/views/calendar.blade.php resources/views/price-calculator.blade.php resources/views/announcements.blade.php
git commit -m "lock member forms in preview mode"
```
