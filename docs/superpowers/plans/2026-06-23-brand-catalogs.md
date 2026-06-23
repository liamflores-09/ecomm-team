# Brand Catalogs — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a Brand Catalogs feature where admin manages brands and admin/researcher manage catalogs, visible to all users.

**Architecture:** Three independent tasks — backend foundation (migrations, models, middleware, routes), admin brand management UI, then the catalog browse/CRUD page. File uploads use Laravel's `public` disk. Role check: admin role in this app is `manager` (NOT `admin`) — `User::isAdmin()` checks `role === 'manager'`.

**Tech Stack:** Laravel 11, Blade, CSS custom properties, Laravel Storage (public disk), FontAwesome 6

## Global Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards/inputs/icon boxes, `9999px` for pill tabs
- CSS variables only for structural colors; `#f59e0b` (seasonal amber) is acceptable as a semantic value
- No `transform` on hover — hover is border-color or background change only
- `var(--primary)` = `#5757f8`, `var(--success)` = `#22c55e`
- Custom modal system only — `openModal(id)` / `closeModal(id)` — no Bootstrap
- Run tests with: `php artisan test` (Windows — use PowerShell)
- Pre-existing test failure: `Tests\Feature\ExampleTest` returns 302 on `GET /` — NOT a regression

---

### Task 1: Backend Foundation

**Files:**
- Create: `database/migrations/TIMESTAMP_create_brands_table.php`
- Create: `database/migrations/TIMESTAMP_create_brand_catalogs_table.php`
- Create: `app/Models/Brand.php`
- Create: `app/Models/BrandCatalog.php`
- Create: `app/Http/Middleware/CatalogManagerMiddleware.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/web.php`

**Interfaces:**
- Produces: `Brand` model with `catalogs()` hasMany, `BrandCatalog` model with `brand()` belongsTo, `catalog.manager` middleware alias, routes `brand-catalogs`, `brand-catalogs.store`, `brand-catalogs.update`, `brand-catalogs.destroy`, `admin.brands`, `admin.brands.store`, `admin.brands.update`, `admin.brands.destroy`

- [ ] **Step 1: Run baseline tests**

```powershell
php artisan test
```
Expected: 1 passed, 1 failed (pre-existing 302). Note results — no new failures should appear after your changes.

- [ ] **Step 2: Create brands migration**

```powershell
php artisan make:migration create_brands_table
```

Open the generated file in `database/migrations/` and replace the `up()` body:

```php
Schema::create('brands', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('description')->nullable();
    $table->string('logo')->nullable();
    $table->timestamps();
});
```

- [ ] **Step 3: Create brand_catalogs migration**

```powershell
php artisan make:migration create_brand_catalogs_table
```

Open the generated file and replace the `up()` body:

```php
Schema::create('brand_catalogs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('notes')->nullable();
    $table->enum('status', ['available', 'upcoming', 'seasonal']);
    $table->string('link')->nullable();
    $table->string('file_path')->nullable();
    $table->timestamps();
});
```

- [ ] **Step 4: Run migrations**

```powershell
php artisan migrate
```

Expected: both tables created, no errors.

- [ ] **Step 5: Create Brand model**

Create `app/Models/Brand.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'description', 'logo'];

    public function catalogs(): HasMany
    {
        return $this->hasMany(BrandCatalog::class);
    }
}
```

- [ ] **Step 6: Create BrandCatalog model**

Create `app/Models/BrandCatalog.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandCatalog extends Model
{
    protected $fillable = ['brand_id', 'title', 'notes', 'status', 'link', 'file_path'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
```

- [ ] **Step 7: Create CatalogManagerMiddleware**

Create `app/Http/Middleware/CatalogManagerMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CatalogManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['manager', 'researcher'])) {
            abort(403);
        }

        return $next($request);
    }
}
```

- [ ] **Step 8: Register middleware alias in bootstrap/app.php**

Find the `withMiddleware` block in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

Replace with:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin'            => \App\Http\Middleware\AdminMiddleware::class,
        'catalog.manager'  => \App\Http\Middleware\CatalogManagerMiddleware::class,
    ]);
})
```

- [ ] **Step 9: Add routes to routes/web.php**

Inside the `Route::middleware(['auth'])->group(...)` block, add after the existing routes (before the closing `});`):

```php
    // Brand Catalogs — all authenticated users can browse
    Route::get('/brand-catalogs', [BrandCatalogController::class, 'index'])->name('brand-catalogs');

    // Brand Catalogs CRUD — admin (manager) and researcher only
    Route::middleware(['catalog.manager'])->group(function () {
        Route::post('/brand-catalogs', [BrandCatalogController::class, 'store'])->name('brand-catalogs.store');
        Route::put('/brand-catalogs/{catalog}', [BrandCatalogController::class, 'update'])->name('brand-catalogs.update');
        Route::delete('/brand-catalogs/{catalog}', [BrandCatalogController::class, 'destroy'])->name('brand-catalogs.destroy');
    });
```

Inside the `Route::middleware(['admin'])->prefix('admin')->group(...)` block, add after the existing admin routes:

```php
        Route::get('/brands', [AdminBrandController::class, 'index'])->name('admin.brands');
        Route::post('/brands', [AdminBrandController::class, 'store'])->name('admin.brands.store');
        Route::put('/brands/{brand}', [AdminBrandController::class, 'update'])->name('admin.brands.update');
        Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy'])->name('admin.brands.destroy');
```

At the top of `routes/web.php`, add the two new controller imports:

```php
use App\Http\Controllers\BrandCatalogController;
use App\Http\Controllers\AdminBrandController;
```

- [ ] **Step 10: Run storage:link**

```powershell
php artisan storage:link
```

Expected: `The [public/storage] link has been connected to [storage/app/public].` (or "already exists" if done before).

- [ ] **Step 11: Write Feature tests**

Create `tests/Feature/BrandCatalogTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\BrandCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_all_authenticated_users_can_view_brand_catalogs(): void
    {
        foreach (['content', 'lead', 'researcher', 'graphics', 'backend', 'manager'] as $role) {
            $response = $this->actingAs($this->makeUser($role))->get('/brand-catalogs');
            $response->assertStatus(200);
        }
    }

    public function test_unauthenticated_users_are_redirected(): void
    {
        $response = $this->get('/brand-catalogs');
        $response->assertRedirect('/login');
    }

    public function test_researcher_can_create_catalog(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        $user = $this->makeUser('researcher');

        $response = $this->actingAs($user)->post('/brand-catalogs', [
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
            'link'     => 'https://example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('brand_catalogs', ['title' => 'Test Catalog']);
    }

    public function test_content_role_cannot_create_catalog(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        $user = $this->makeUser('content');

        $response = $this->actingAs($user)->post('/brand-catalogs', [
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
            'link'     => 'https://example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_catalog_requires_link_or_file(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        $user = $this->makeUser('manager');

        $response = $this->actingAs($user)->post('/brand-catalogs', [
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('brand_catalogs', ['title' => 'Test Catalog']);
    }

    public function test_admin_can_create_brand(): void
    {
        $user = $this->makeUser('manager');

        $response = $this->actingAs($user)->post('/admin/brands', [
            'name' => 'Samsung',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('brands', ['name' => 'Samsung']);
    }

    public function test_brand_delete_blocked_when_catalogs_exist(): void
    {
        $brand = Brand::create(['name' => 'TestBrand']);
        BrandCatalog::create([
            'brand_id' => $brand->id,
            'title'    => 'Test Catalog',
            'status'   => 'available',
            'link'     => 'https://example.com',
        ]);

        $user = $this->makeUser('manager');
        $response = $this->actingAs($user)->delete('/admin/brands/' . $brand->id);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('brands', ['id' => $brand->id]);
    }
}
```

- [ ] **Step 12: Run tests — expect new test failures until controllers exist**

```powershell
php artisan test
```

Expected: `BrandCatalogTest` fails (controllers not yet created). Note exactly which assertions fail — they should all be route-not-found or 500 errors, not logic failures.

- [ ] **Step 13: Commit foundation**

```powershell
git add database/migrations app/Models/Brand.php app/Models/BrandCatalog.php app/Http/Middleware/CatalogManagerMiddleware.php bootstrap/app.php routes/web.php tests/Feature/BrandCatalogTest.php
git commit -m "feat: brand catalogs backend foundation (migrations, models, middleware, routes)"
```

---

### Task 2: Admin Brand Management

**Files:**
- Create: `app/Http/Controllers/AdminBrandController.php`
- Create: `resources/views/admin/brands.blade.php`
- Modify: `resources/views/components/sidebar.blade.php`

**Interfaces:**
- Consumes: `Brand` model (`name`, `description`, `logo`, `catalogs()`) from Task 1; `admin.brands.*` routes from Task 1
- Produces: `/admin/brands` page where admin can add/edit/delete brands and upload logos

- [ ] **Step 1: Run baseline tests**

```powershell
php artisan test
```

Note current results.

- [ ] **Step 2: Create AdminBrandController**

Create `app/Http/Controllers/AdminBrandController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminBrandController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $brands = Brand::withCount('catalogs')->orderBy('name')->get();
        return view('admin.brands', compact('user', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);
        return back()->with('success', 'Brand added.');
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);
        return back()->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->catalogs()->exists()) {
            return back()->with('error', 'Cannot delete brand with existing catalogs. Remove the catalogs first.');
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        return back()->with('success', 'Brand deleted.');
    }
}
```

- [ ] **Step 3: Create admin/brands.blade.php**

Create `resources/views/admin/brands.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Brands — Admin')
@section('has-sidebar', true)

@section('styles')
<style>
    .brands-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .brands-table thead th { background: var(--muted); padding: 0.75rem 1rem; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); text-align: left; }
    .brands-table tbody td { padding: 0.75rem 1rem; border-top: 1px solid var(--border-light); font-weight: 500; vertical-align: middle; }
    .brands-table tbody tr:hover td { background: var(--muted); }
    .brand-logo-thumb { width: 32px; height: 32px; border-radius: 8px; object-fit: cover; }
    .brand-initial { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.85rem; background: var(--primary); }
    .action-btns { display: flex; gap: 0.25rem; }
    .action-btn-sm { width: 28px; height: 28px; border: 1px solid var(--border-light); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; cursor: pointer; transition: border-color 0.15s; background: transparent; color: var(--muted-foreground); }
    .action-btn-sm:hover { border-color: var(--foreground); color: var(--foreground); }
    .action-btn-sm.btn-danger:hover { border-color: #dc2626; color: #dc2626; }
    .logo-preview { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; display: none; margin-top: 0.5rem; }
</style>
@endsection

@section('content')
<x-sidebar :isAdmin="true" active="admin.brands" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Brands <span class="highlight">Management</span></h2>
            <p>Manage brands for the catalog feature</p>
        </div>
        <button type="button" class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="openAddBrand()">
            <i class="fas fa-plus"></i> Add Brand
        </button>
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <div class="eod-card anim-up d1">
        <div class="eod-card-header">
            <div class="t-icon"><i class="fas fa-tag"></i></div>
            Brands ({{ $brands->count() }})
        </div>
        <div style="overflow-x: auto;">
            @if($brands->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--muted-foreground); font-size: 0.85rem;">
                <i class="fas fa-tag" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--border);"></i>
                No brands yet. Add the first one.
            </div>
            @else
            <table class="brands-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Name</th>
                        <th>Description</th>
                        <th style="text-align: center;">Catalogs</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                    <tr>
                        <td>
                            @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" class="brand-logo-thumb" alt="{{ $brand->name }}">
                            @else
                            <span class="brand-initial">{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 700;">{{ $brand->name }}</td>
                        <td style="color: var(--muted-foreground);">{{ $brand->description ?: '—' }}</td>
                        <td style="text-align: center;">{{ $brand->catalogs_count }}</td>
                        <td>
                            <div class="action-btns" style="justify-content: flex-end;">
                                <button class="action-btn-sm" title="Edit"
                                    onclick="openEditBrand(this)"
                                    data-id="{{ $brand->id }}"
                                    data-name="{{ $brand->name }}"
                                    data-description="{{ $brand->description ?? '' }}"
                                    data-logo="{{ $brand->logo ? asset('storage/' . $brand->logo) : '' }}">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete {{ addslashes($brand->name) }}?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<!-- Brand Modal -->
<div class="modal-overlay" id="brandModal">
    <div class="modal-box" style="max-width: 460px;">
        <div class="modal-header">
            <h5 id="brandModalTitle" style="font-weight: 700; font-size: 1rem; margin: 0;">Add Brand</h5>
            <button class="modal-close" onclick="closeModal('brandModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="brandForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="brandMethod" value="">
            <div class="modal-body" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="brandName" class="form-input" placeholder="e.g. Samsung" required style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Description <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <input type="text" name="description" id="brandDescription" class="form-input" placeholder="Short tagline" style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Logo <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(image, max 2MB)</span></label>
                    <img id="brandLogoPreview" src="" alt="Logo preview" class="logo-preview">
                    <input type="file" name="logo" id="brandLogo" accept=".jpg,.jpeg,.png,.svg,.webp" style="font-size: 0.85rem; margin-top: 0.375rem;" onchange="previewLogo(this)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('brandModal')" style="height: 40px; font-size: 0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height: 40px; font-size: 0.85rem;">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddBrand() {
    document.getElementById('brandModalTitle').textContent = 'Add Brand';
    document.getElementById('brandForm').action = '{{ route("admin.brands.store") }}';
    document.getElementById('brandMethod').value = '';
    document.getElementById('brandForm').reset();
    document.getElementById('brandLogoPreview').style.display = 'none';
    openModal('brandModal');
}

function openEditBrand(btn) {
    var d = btn.dataset;
    document.getElementById('brandModalTitle').textContent = 'Edit Brand';
    document.getElementById('brandForm').action = '/admin/brands/' + d.id;
    document.getElementById('brandMethod').value = 'PUT';
    document.getElementById('brandName').value = d.name;
    document.getElementById('brandDescription').value = d.description;
    document.getElementById('brandLogo').value = '';
    var preview = document.getElementById('brandLogoPreview');
    if (d.logo) {
        preview.src = d.logo;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    openModal('brandModal');
}

function previewLogo(input) {
    var preview = document.getElementById('brandLogoPreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
```

- [ ] **Step 4: Add Brands link to admin sidebar**

Open `resources/views/components/sidebar.blade.php`. Find the admin nav links block:

```blade
        <li><a href="{{ route('admin.reports') }}" class="{{ $active === 'admin.reports' ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Reports</a></li>
```

Add after it:

```blade
        <li><a href="{{ route('admin.brands') }}" class="{{ $active === 'admin.brands' ? 'active' : '' }}"><i class="fas fa-tag"></i> Brands</a></li>
```

- [ ] **Step 5: Run tests**

```powershell
php artisan test
```

Expected: `test_admin_can_create_brand` and `test_brand_delete_blocked_when_catalogs_exist` now pass. Other `BrandCatalogTest` tests still fail (BrandCatalogController not yet created).

- [ ] **Step 6: Commit**

```powershell
git add app/Http/Controllers/AdminBrandController.php resources/views/admin/brands.blade.php resources/views/components/sidebar.blade.php
git commit -m "feat: admin brand management page with logo upload"
```

---

### Task 3: Brand Catalog Browse Page

**Files:**
- Create: `app/Http/Controllers/BrandCatalogController.php`
- Create: `resources/views/brand-catalogs.blade.php`
- Modify: `resources/views/components/sidebar.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

**Interfaces:**
- Consumes: `Brand` model, `BrandCatalog` model, `brand-catalogs.*` routes — all from Task 1; `asset('storage/...')` pattern for serving files from public disk
- Produces: `/brand-catalogs` page visible to all users, with CRUD modals for admin/researcher

- [ ] **Step 1: Run baseline tests**

```powershell
php artisan test
```

Note current results.

- [ ] **Step 2: Create BrandCatalogController**

Create `app/Http/Controllers/BrandCatalogController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandCatalogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $brands = Brand::orderBy('name')->get();
        $catalogs = BrandCatalog::with('brand')->latest()->get();
        return view('brand-catalogs', compact('user', 'brands', 'catalogs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'title'    => 'required|string|max:255',
            'notes'    => 'nullable|string',
            'status'   => 'required|in:available,upcoming,seasonal',
            'link'     => 'nullable|url|max:2048',
            'file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if (!$request->filled('link') && !$request->hasFile('file')) {
            return back()->with('error', 'Please provide an external link or upload a file.');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('catalogs', 'public');
        }

        unset($data['file']);
        BrandCatalog::create($data);
        return back()->with('success', 'Catalog added.');
    }

    public function update(Request $request, BrandCatalog $catalog)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'title'    => 'required|string|max:255',
            'notes'    => 'nullable|string',
            'status'   => 'required|in:available,upcoming,seasonal',
            'link'     => 'nullable|url|max:2048',
            'file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($catalog->file_path) {
                Storage::disk('public')->delete($catalog->file_path);
            }
            $data['file_path'] = $request->file('file')->store('catalogs', 'public');
        } else {
            $data['file_path'] = $catalog->file_path;
        }

        if (!$request->filled('link') && !$data['file_path']) {
            return back()->with('error', 'Please provide an external link or upload a file.');
        }

        unset($data['file']);
        $catalog->update($data);
        return back()->with('success', 'Catalog updated.');
    }

    public function destroy(BrandCatalog $catalog)
    {
        if ($catalog->file_path) {
            Storage::disk('public')->delete($catalog->file_path);
        }
        $catalog->delete();
        return back()->with('success', 'Catalog deleted.');
    }
}
```

- [ ] **Step 3: Create brand-catalogs.blade.php**

Create `resources/views/brand-catalogs.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Brand Catalogs — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('styles')
<style>
    .bc-tabs { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
    .bc-tab { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.875rem; border-radius: 9999px; border: 1px solid var(--border-light); background: var(--muted); color: var(--foreground); font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s; font-family: inherit; }
    .bc-tab:hover { border-color: var(--foreground); }
    .bc-tab.active { background: var(--primary); border-color: var(--primary); color: white; }
    .bc-status-tabs { margin-bottom: 1.5rem; }
    .bc-status-tab { font-size: 0.75rem; padding: 0.3rem 0.75rem; }

    .bc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media (max-width: 768px) { .bc-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .bc-grid { grid-template-columns: 1fr; } }

    .bc-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem; transition: border-color 0.2s; }
    .bc-card:hover { border-color: var(--foreground); }

    .bc-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem; }
    .bc-logo { width: 40px; height: 40px; border-radius: 8px; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1rem; }
    .bc-logo img { width: 100%; height: 100%; object-fit: cover; }

    .bc-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .bc-badge.available { background: rgba(34,197,94,0.12); color: var(--success); }
    .bc-badge.upcoming { background: rgba(87,87,248,0.12); color: var(--primary); }
    .bc-badge.seasonal { background: rgba(245,158,11,0.12); color: #f59e0b; }

    .bc-title { font-weight: 700; font-size: 0.9rem; color: var(--foreground); }
    .bc-brand-name { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .bc-notes { font-size: 0.8rem; color: var(--muted-foreground); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .bc-meta { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 0.625rem; border-top: 1px solid var(--border-light); }
    .bc-meta-left { display: flex; align-items: center; gap: 0.5rem; }
    .bc-icon-link { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); font-size: 0.7rem; text-decoration: none; transition: border-color 0.15s; }
    .bc-icon-link:hover { border-color: var(--foreground); color: var(--foreground); }
    .bc-date { font-size: 0.7rem; color: var(--muted-foreground); }

    .bc-actions { display: flex; align-items: center; gap: 0.25rem; }
    .bc-action-btn { width: 28px; height: 28px; border: 1px solid var(--border-light); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; cursor: pointer; transition: border-color 0.15s; background: transparent; color: var(--muted-foreground); }
    .bc-action-btn:hover { border-color: var(--foreground); color: var(--foreground); }
    .bc-action-btn.btn-danger:hover { border-color: #dc2626; color: #dc2626; }

    .bc-empty { text-align: center; padding: 3rem; color: var(--muted-foreground); }
    .bc-empty i { font-size: 2rem; margin-bottom: 0.75rem; display: block; color: var(--border); }
</style>
@endsection

@section('content')
<x-sidebar active="brand-catalogs" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Brand <span class="highlight">Catalogs</span></h2>
            <p>Browse brand catalogs and upcoming product lists</p>
        </div>
        @if(in_array($user->role, ['manager', 'researcher']))
        <button type="button" class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="addCatalog()">
            <i class="fas fa-plus"></i> Add Catalog
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- Brand filter tabs -->
    <div class="bc-tabs anim-up d1">
        <button class="bc-tab bc-brand-tab active" data-brand="all">All</button>
        @foreach($brands as $brand)
        <button class="bc-tab bc-brand-tab" data-brand="{{ $brand->id }}">{{ $brand->name }}</button>
        @endforeach
    </div>

    <!-- Status filter tabs -->
    <div class="bc-tabs bc-status-tabs anim-up d1">
        <button class="bc-tab bc-status-tab active" data-status="all">All</button>
        <button class="bc-tab bc-status-tab" data-status="available">Available</button>
        <button class="bc-tab bc-status-tab" data-status="upcoming">Upcoming</button>
        <button class="bc-tab bc-status-tab" data-status="seasonal">Seasonal</button>
    </div>

    @if($catalogs->isEmpty())
    <div class="bc-empty anim-up d2">
        <i class="fas fa-book-open"></i>
        No catalogs yet.@if(in_array($user->role, ['manager', 'researcher'])) Add the first one using the button above.@endif
    </div>
    @else
    @php
    $initialColors = ['#5757f8', '#10b981', '#f59e0b', '#f43f5e', '#6366f1', '#0ea5e9'];
    @endphp
    <div class="bc-grid anim-up d2">
        @foreach($catalogs as $catalog)
        @php $initColor = $initialColors[ord(strtoupper($catalog->brand->name[0])) % count($initialColors)]; @endphp
        <div class="bc-card" data-brand="{{ $catalog->brand_id }}" data-status="{{ $catalog->status }}">
            <div class="bc-card-top">
                <div class="bc-logo" @if(!$catalog->brand->logo) style="background: {{ $initColor }};" @endif>
                    @if($catalog->brand->logo)
                    <img src="{{ asset('storage/' . $catalog->brand->logo) }}" alt="{{ $catalog->brand->name }}">
                    @else
                    {{ strtoupper(substr($catalog->brand->name, 0, 1)) }}
                    @endif
                </div>
                <span class="bc-badge {{ $catalog->status }}">{{ ucfirst($catalog->status) }}</span>
            </div>
            <div class="bc-title">{{ $catalog->title }}</div>
            <div class="bc-brand-name">{{ $catalog->brand->name }}</div>
            @if($catalog->notes)
            <div class="bc-notes">{{ $catalog->notes }}</div>
            @endif
            <div class="bc-meta">
                <div class="bc-meta-left">
                    @if($catalog->link)
                    <a href="{{ $catalog->link }}" target="_blank" rel="noopener" class="bc-icon-link" title="Open link"><i class="fas fa-link"></i></a>
                    @endif
                    @if($catalog->file_path)
                    <a href="{{ asset('storage/' . $catalog->file_path) }}" target="_blank" class="bc-icon-link" title="View file"><i class="fas fa-file"></i></a>
                    @endif
                    <span class="bc-date">{{ $catalog->created_at->format('M j, Y') }}</span>
                </div>
                @if(in_array($user->role, ['manager', 'researcher']))
                <div class="bc-actions">
                    <button class="bc-action-btn" title="Edit"
                        onclick="editCatalog(this)"
                        data-id="{{ $catalog->id }}"
                        data-brand="{{ $catalog->brand_id }}"
                        data-title="{{ $catalog->title }}"
                        data-notes="{{ $catalog->notes ?? '' }}"
                        data-status="{{ $catalog->status }}"
                        data-link="{{ $catalog->link ?? '' }}"
                        data-file="{{ $catalog->file_path ? basename($catalog->file_path) : '' }}">
                        <i class="fas fa-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('brand-catalogs.destroy', $catalog) }}" onsubmit="return confirm('Delete this catalog?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bc-action-btn btn-danger" title="Delete"><i class="fas fa-trash-can"></i></button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Catalog Modal -->
<div class="modal-overlay" id="catalogModal">
    <div class="modal-box" style="max-width: 520px;">
        <div class="modal-header">
            <h5 id="catalogModalTitle" style="font-weight: 700; font-size: 1rem; margin: 0;">Add Catalog</h5>
            <button class="modal-close" onclick="closeModal('catalogModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="catalogForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="catalogMethod" value="">
            <div class="modal-body" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" id="catalogBrand" class="form-select" required>
                        <option value="">— Select brand —</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="catalogTitle" class="form-input" placeholder="e.g. Samsung Q3 2026 New Arrivals" required style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="catalogStatus" class="form-select" required>
                        <option value="available">Available</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="seasonal">Seasonal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <textarea name="notes" id="catalogNotes" class="form-textarea" placeholder="What's notable about this catalog?" style="min-height: 70px; width: 100%;"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">External Link <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <input type="url" name="link" id="catalogLink" class="form-input" placeholder="https://drive.google.com/..." style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Upload File <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(PDF or image, max 10MB)</span></label>
                    <div id="catalogCurrentFile" style="font-size: 0.8rem; color: var(--muted-foreground); margin-bottom: 0.375rem;"></div>
                    <input type="file" name="file" id="catalogFile" accept=".pdf,.jpg,.jpeg,.png" style="font-size: 0.85rem;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('catalogModal')" style="height: 40px; font-size: 0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height: 40px; font-size: 0.85rem;">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
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

function addCatalog() {
    document.getElementById('catalogModalTitle').textContent = 'Add Catalog';
    document.getElementById('catalogForm').reset();
    document.getElementById('catalogForm').action = '{{ route("brand-catalogs.store") }}';
    document.getElementById('catalogMethod').value = '';
    document.getElementById('catalogCurrentFile').textContent = '';
    openModal('catalogModal');
}

function editCatalog(btn) {
    var d = btn.dataset;
    document.getElementById('catalogModalTitle').textContent = 'Edit Catalog';
    document.getElementById('catalogForm').action = '/brand-catalogs/' + d.id;
    document.getElementById('catalogMethod').value = 'PUT';
    document.getElementById('catalogBrand').value = d.brand;
    document.getElementById('catalogTitle').value = d.title;
    document.getElementById('catalogStatus').value = d.status;
    document.getElementById('catalogNotes').value = d.notes;
    document.getElementById('catalogLink').value = d.link;
    document.getElementById('catalogCurrentFile').textContent = d.file ? 'Current file: ' + d.file : '';
    document.getElementById('catalogFile').value = '';
    openModal('catalogModal');
}
</script>
@endsection
```

- [ ] **Step 4: Add Brand Catalogs to user sidebar**

Open `resources/views/components/sidebar.blade.php`. Find:

```blade
            <li><a href="{{ route('team') }}" class="{{ $active === 'team' ? 'active' : '' }}"><i class="fas fa-users"></i> The Team</a></li>
```

Add after it:

```blade
            <li><a href="{{ route('brand-catalogs') }}" class="{{ $active === 'brand-catalogs' ? 'active' : '' }}"><i class="fas fa-book-open"></i> Brand Catalogs</a></li>
```

- [ ] **Step 5: Add Brand Catalogs to command palette**

Open `resources/views/layouts/app.blade.php`. Find the `userPages` array entry for The Team:

```javascript
            { name: 'The Team', desc: 'Team directory', icon: 'fa-users', url: '{{ route("team") }}' }
```

Replace with:

```javascript
            { name: 'The Team', desc: 'Team directory', icon: 'fa-users', url: '{{ route("team") }}' },
            { name: 'Brand Catalogs', desc: 'Browse brand catalogs', icon: 'fa-book-open', url: '{{ route("brand-catalogs") }}' }
```

- [ ] **Step 6: Run tests**

```powershell
php artisan test
```

Expected: All `BrandCatalogTest` tests pass. Overall: multiple passed, 1 failed (pre-existing 302 only).

- [ ] **Step 7: Commit**

```powershell
git add app/Http/Controllers/BrandCatalogController.php resources/views/brand-catalogs.blade.php resources/views/components/sidebar.blade.php resources/views/layouts/app.blade.php
git commit -m "feat: brand catalog browse page with filters and CRUD modals"
```
