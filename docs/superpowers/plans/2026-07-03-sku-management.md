# SKU Management Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a "SKU Management" nav group with two pages — SKU Tracker (CRUD table for the PR→Content SKU pipeline, seeded from historical spreadsheet data) and SLA and Weekly Output (read-only weekly SLA analytics) — with role-based edit permissions.

**Architecture:** A new `skus` Eloquent model/table backs both pages. A single `SkuController` serves `index` (list/filter), `store` (create), `update` (field-permission-gated edit), and `slaWeeklyOutput` (read-only analytics). SLA/Posted/Status values are computed via model accessors, never stored, so they can't go stale. Historical data is imported once via an Artisan command reading a committed JSON extract (produced from the source workbook with a one-off Python script — no new PHP dependency needed for the import).

**Tech Stack:** Laravel 12, Blade views following this repo's existing KPI-card / flat-modal / `x-select` component conventions, PHPUnit feature tests with `RefreshDatabase` + sqlite in-memory.

## Global Constraints

- Roles in this system: `head`, `manager`, `analyst`, `content`, `graphics`, `backend`, `researcher`. `manager`/`head` are "admin" (`User::isAdmin()` returns true for them and they see the Admin Panel sidebar block); everyone else sees the member sidebar block.
- `analyst` must never see or reach SKU Management routes — reuse the existing `not.analyst` middleware alias (`App\Http\Middleware\NotAnalystMiddleware`), already registered in `bootstrap/app.php`.
- Permission matrix (from the design spec, `docs/superpowers/specs/2026-07-03-sku-management-design.md`):
  | Role | Create row | Edit PR fields | Edit Content fields | View |
  |---|---|---|---|---|
  | Researcher | yes | yes | no | yes |
  | Content | no | no | yes | yes |
  | Graphics | no | no | no | yes (read-only) |
  | Backend / Manager / Head | yes | yes | yes | yes |
  | Analyst | blocked entirely |
- "PR fields" = `brand, sku, variant, pr_file_location, pr_assignee, pr_status, ready_for_cvp, remarks, pr_date_started, pr_date_completed`. "Content fields" = `content_assignee, content_date_started, content_date_posted, cvp_uploaded` + the 10 marketplace link fields.
- No stored `pr_sla`, `content_sla`, `content_status`, or `posted` columns — these are always computed from the date columns via accessors.
- No Co-Authored-By lines in any commit (project convention).
- Commit messages: short and casual (e.g. "add sku tracker page"), not verbose multi-paragraph descriptions.

---

## Task 1: `skus` table + `Sku` model with computed accessors

**Files:**
- Create: `database/migrations/2026_07_03_000001_create_skus_table.php`
- Create: `app/Models/Sku.php`
- Test: `tests/Unit/SkuModelTest.php`

**Interfaces:**
- Produces: `Sku` model with `fillable` covering all stored columns, casts (`ready_for_cvp`, `cvp_uploaded` => boolean; 4 date columns => date), and accessors `getPrSlaAttribute(): ?int`, `getContentSlaAttribute(): ?int`, `getContentStatusAttribute(): string`, `getPostedAttribute(): bool`. Later tasks read/write these column names directly and call `$sku->pr_sla`, `$sku->content_sla`, `$sku->content_status`, `$sku->posted`.

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('sku');
            $table->string('variant')->nullable();

            // PR section
            $table->text('pr_file_location')->nullable();
            $table->string('pr_assignee')->nullable();
            $table->string('pr_status')->nullable();
            $table->boolean('ready_for_cvp')->default(false);
            $table->text('remarks')->nullable();
            $table->date('pr_date_started')->nullable();
            $table->date('pr_date_completed')->nullable();

            // Content section
            $table->string('content_assignee')->nullable();
            $table->date('content_date_started')->nullable();
            $table->date('content_date_posted')->nullable();
            $table->boolean('cvp_uploaded')->default(false);

            // Marketplace links
            $table->text('shopee_link')->nullable();
            $table->text('lazada_link')->nullable();
            $table->text('tiktok_link')->nullable();
            $table->text('jg_pro_shopee_link')->nullable();
            $table->text('jg_pro_lazada_link')->nullable();
            $table->text('shopify_link')->nullable();
            $table->text('cinepro_link')->nullable();
            $table->text('lzd_brand_mall_link')->nullable();
            $table->text('shp_brand_mall_link')->nullable();
            $table->text('tt_brand_mall_link')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
```

- [ ] **Step 2: Write the failing accessor tests**

```php
<?php

namespace Tests\Unit;

use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pr_sla_is_null_without_both_dates(): void
    {
        $sku = Sku::create(['brand' => 'B', 'sku' => 'S1', 'pr_date_started' => '2026-01-05']);
        $this->assertNull($sku->pr_sla);
    }

    public function test_pr_sla_is_day_difference(): void
    {
        $sku = Sku::create([
            'brand' => 'B', 'sku' => 'S2',
            'pr_date_started' => '2026-01-05',
            'pr_date_completed' => '2026-01-09',
        ]);
        $this->assertSame(4, $sku->pr_sla);
    }

    public function test_pr_sla_is_one_when_same_day(): void
    {
        $sku = Sku::create([
            'brand' => 'B', 'sku' => 'S3',
            'pr_date_started' => '2026-01-05',
            'pr_date_completed' => '2026-01-05',
        ]);
        $this->assertSame(1, $sku->pr_sla);
    }

    public function test_content_sla_is_posted_minus_pr_completed(): void
    {
        $sku = Sku::create([
            'brand' => 'B', 'sku' => 'S4',
            'pr_date_completed' => '2026-01-09',
            'content_date_posted' => '2026-01-17',
        ]);
        $this->assertSame(8, $sku->content_sla);
    }

    public function test_content_status_progression(): void
    {
        $notStarted = Sku::create(['brand' => 'B', 'sku' => 'S5']);
        $this->assertSame('—', $notStarted->content_status);

        $pending = Sku::create(['brand' => 'B', 'sku' => 'S6', 'content_date_started' => '2026-01-10']);
        $this->assertSame('PENDING', $pending->content_status);

        $done = Sku::create([
            'brand' => 'B', 'sku' => 'S7',
            'content_date_started' => '2026-01-10',
            'content_date_posted' => '2026-01-17',
        ]);
        $this->assertSame('DONE', $done->content_status);
    }

    public function test_posted_reflects_content_date_posted(): void
    {
        $notPosted = Sku::create(['brand' => 'B', 'sku' => 'S8']);
        $this->assertFalse($notPosted->posted);

        $posted = Sku::create(['brand' => 'B', 'sku' => 'S9', 'content_date_posted' => '2026-01-17']);
        $this->assertTrue($posted->posted);
    }
}
```

- [ ] **Step 3: Run the tests to verify they fail**

Run: `php artisan test tests/Unit/SkuModelTest.php`
Expected: FAIL — `Class "App\Models\Sku" not found` (model and migration don't exist yet in the DB schema used by the test run).

- [ ] **Step 4: Write the `Sku` model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $fillable = [
        'brand', 'sku', 'variant',
        'pr_file_location', 'pr_assignee', 'pr_status', 'ready_for_cvp', 'remarks',
        'pr_date_started', 'pr_date_completed',
        'content_assignee', 'content_date_started', 'content_date_posted', 'cvp_uploaded',
        'shopee_link', 'lazada_link', 'tiktok_link',
        'jg_pro_shopee_link', 'jg_pro_lazada_link', 'shopify_link', 'cinepro_link',
        'lzd_brand_mall_link', 'shp_brand_mall_link', 'tt_brand_mall_link',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'ready_for_cvp' => 'boolean',
            'cvp_uploaded' => 'boolean',
            'pr_date_started' => 'date',
            'pr_date_completed' => 'date',
            'content_date_started' => 'date',
            'content_date_posted' => 'date',
        ];
    }

    public function getPrSlaAttribute(): ?int
    {
        if (!$this->pr_date_started || !$this->pr_date_completed) {
            return null;
        }
        $diff = $this->pr_date_started->diffInDays($this->pr_date_completed);
        return $diff === 0 ? 1 : $diff;
    }

    public function getContentSlaAttribute(): ?int
    {
        if (!$this->pr_date_completed || !$this->content_date_posted) {
            return null;
        }
        $diff = $this->pr_date_completed->diffInDays($this->content_date_posted);
        return $diff === 0 ? 1 : $diff;
    }

    public function getContentStatusAttribute(): string
    {
        if ($this->content_date_posted) {
            return 'DONE';
        }
        if ($this->content_date_started) {
            return 'PENDING';
        }
        return '—';
    }

    public function getPostedAttribute(): bool
    {
        return $this->content_date_posted !== null;
    }
}
```

- [ ] **Step 5: Run the migration and tests to verify they pass**

Run: `php artisan test tests/Unit/SkuModelTest.php`
Expected: PASS (6 tests) — `RefreshDatabase` runs the new migration automatically against the sqlite test DB.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_03_000001_create_skus_table.php app/Models/Sku.php tests/Unit/SkuModelTest.php
git commit -m "add skus table and model with computed SLA accessors"
```

---

## Task 2: Routes + `SkuController@index` + SKU Tracker list view

**Files:**
- Create: `app/Http/Controllers/SkuController.php`
- Create: `resources/views/sku/tracker.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/SkuTrackerTest.php`

**Interfaces:**
- Consumes: `Sku` model from Task 1 (`pr_sla`, `content_sla`, `content_status`, `posted` accessors).
- Produces: `GET /sku-tracker` route named `sku-tracker`, `SkuController::index()`, private helper `SkuController::permissions(string $role): array` returning `['can_create' => bool, 'can_edit_pr' => bool, 'can_edit_content' => bool]` — Tasks 4 and 5 reuse this helper.

- [ ] **Step 1: Write the failing access-control tests**

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuTrackerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_analyst_is_blocked_from_sku_tracker(): void
    {
        $response = $this->actingAs($this->makeUser('analyst'))->get('/sku-tracker');
        $response->assertStatus(403);
    }

    public function test_other_roles_can_view_sku_tracker(): void
    {
        foreach (['content', 'researcher', 'graphics', 'backend', 'manager', 'head'] as $role) {
            $response = $this->actingAs($this->makeUser($role))->get('/sku-tracker');
            $response->assertStatus(200);
        }
    }

    public function test_unauthenticated_users_are_redirected(): void
    {
        $response = $this->get('/sku-tracker');
        $response->assertRedirect('/login');
    }

    public function test_add_sku_button_hidden_for_graphics(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->get('/sku-tracker');
        $response->assertDontSee('Add SKU');
    }

    public function test_add_sku_button_visible_for_researcher(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('Add SKU');
    }

    public function test_existing_sku_codes_are_passed_to_view_for_duplicate_check(): void
    {
        \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-DUP-1']);

        $response = $this->actingAs($this->makeUser('researcher'))->get('/sku-tracker');
        $response->assertSee('acme-dup-1');
    }
}
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test tests/Feature/SkuTrackerTest.php`
Expected: FAIL — route `/sku-tracker` doesn't exist (404s instead of 200/403).

- [ ] **Step 3: Add the route**

In `routes/web.php`, add `use App\Http\Controllers\SkuController;` to the `use` block at the top, then add this new middleware group right after the `catalog.manager` group (before the `admin` group starting at line 85):

```php
    // SKU Management — all roles except analyst
    Route::middleware(['not.analyst'])->group(function () {
        Route::get('/sku-tracker', [SkuController::class, 'index'])->name('sku-tracker');
        Route::post('/sku-tracker', [SkuController::class, 'store'])->name('sku-tracker.store');
        Route::put('/sku-tracker/{sku}', [SkuController::class, 'update'])->name('sku-tracker.update');
        Route::get('/sla-weekly-output', [SkuController::class, 'slaWeeklyOutput'])->name('sla-weekly-output');
    });
```

- [ ] **Step 4: Write the controller**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkuController extends Controller
{
    private const VARIANTS = ['Single', 'Variant/Parent', 'Variant/Child', 'Add Variant'];
    private const PR_STATUSES = ['DONE', 'IN PROGRESS', 'On Hold'];

    private function permissions(string $role): array
    {
        $prEditors = ['researcher', 'backend', 'manager', 'head'];
        $contentEditors = ['content', 'backend', 'manager', 'head'];

        return [
            'can_create' => in_array($role, $prEditors),
            'can_edit_pr' => in_array($role, $prEditors),
            'can_edit_content' => in_array($role, $contentEditors),
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $perms = $this->permissions($user->role);

        $query = Sku::query();

        if ($request->filled('brand')) {
            $query->where(function ($q) use ($request) {
                $q->where('brand', 'like', '%' . $request->query('brand') . '%')
                  ->orWhere('sku', 'like', '%' . $request->query('brand') . '%');
            });
        }
        if ($request->filled('pr_status')) {
            $query->where('pr_status', $request->query('pr_status'));
        }
        if ($request->query('posted') === '1') {
            $query->whereNotNull('content_date_posted');
        } elseif ($request->query('posted') === '0') {
            $query->whereNull('content_date_posted');
        }
        if ($request->filled('month')) {
            $month = $request->query('month');
            $query->where(function ($q) use ($month) {
                $q->whereRaw("strftime('%Y-%m', pr_date_started) = ?", [$month])
                  ->orWhereRaw("strftime('%Y-%m', content_date_started) = ?", [$month]);
            });
        }

        $skus = $query->orderByDesc('id')->paginate(25)->withQueryString();

        $allSkus = Sku::select('content_date_posted', 'pr_date_started', 'pr_date_completed', 'content_date_started')->get();
        $stats = [
            'total' => Sku::count(),
            'posted' => Sku::whereNotNull('content_date_posted')->count(),
            'avg_pr_sla' => round($allSkus->map->pr_sla->filter()->avg() ?? 0, 1),
            'avg_content_sla' => round($allSkus->map->content_sla->filter()->avg() ?? 0, 1),
        ];

        $availableMonths = Sku::selectRaw("strftime('%Y-%m', pr_date_started) as m")
            ->whereNotNull('pr_date_started')
            ->distinct()
            ->orderByDesc('m')
            ->pluck('m')
            ->filter()
            ->values();

        return view('sku.tracker', [
            'skus' => $skus,
            'stats' => $stats,
            'perms' => $perms,
            'variants' => self::VARIANTS,
            'prStatuses' => self::PR_STATUSES,
            'filters' => $request->only(['brand', 'pr_status', 'posted', 'month']),
            'availableMonths' => $availableMonths,
            'existingSkuCodes' => Sku::pluck('sku')->map(fn ($s) => strtolower($s))->values(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->permissions(Auth::user()->role)['can_create'], 403);

        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'variant' => 'nullable|in:' . implode(',', self::VARIANTS),
            'pr_file_location' => 'nullable|string',
            'pr_assignee' => 'nullable|string|max:255',
            'pr_status' => 'nullable|in:' . implode(',', self::PR_STATUSES),
            'ready_for_cvp' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'pr_date_started' => 'nullable|date',
            'pr_date_completed' => 'nullable|date|after_or_equal:pr_date_started',
        ]);
        $data['created_by'] = Auth::id();

        Sku::create($data);

        return back()->with('success', 'SKU added.');
    }

    public function update(Request $request, Sku $sku)
    {
        $perms = $this->permissions(Auth::user()->role);
        abort_unless($perms['can_edit_pr'] || $perms['can_edit_content'], 403);

        $rules = [];
        if ($perms['can_edit_pr']) {
            $rules += [
                'brand' => 'required|string|max:255',
                'sku' => 'required|string|max:255',
                'variant' => 'nullable|in:' . implode(',', self::VARIANTS),
                'pr_file_location' => 'nullable|string',
                'pr_assignee' => 'nullable|string|max:255',
                'pr_status' => 'nullable|in:' . implode(',', self::PR_STATUSES),
                'ready_for_cvp' => 'nullable|boolean',
                'remarks' => 'nullable|string',
                'pr_date_started' => 'nullable|date',
                'pr_date_completed' => 'nullable|date|after_or_equal:pr_date_started',
            ];
        }
        if ($perms['can_edit_content']) {
            $rules += [
                'content_assignee' => 'nullable|string|max:255',
                'content_date_started' => 'nullable|date',
                'content_date_posted' => 'nullable|date|after_or_equal:content_date_started',
                'cvp_uploaded' => 'nullable|boolean',
                'shopee_link' => 'nullable|string|max:2000',
                'lazada_link' => 'nullable|string|max:2000',
                'tiktok_link' => 'nullable|string|max:2000',
                'jg_pro_shopee_link' => 'nullable|string|max:2000',
                'jg_pro_lazada_link' => 'nullable|string|max:2000',
                'shopify_link' => 'nullable|string|max:2000',
                'cinepro_link' => 'nullable|string|max:2000',
                'lzd_brand_mall_link' => 'nullable|string|max:2000',
                'shp_brand_mall_link' => 'nullable|string|max:2000',
                'tt_brand_mall_link' => 'nullable|string|max:2000',
            ];
        }

        $data = $request->validate($rules);
        if (array_key_exists('ready_for_cvp', $rules)) {
            $data['ready_for_cvp'] = $request->boolean('ready_for_cvp');
        }
        if (array_key_exists('cvp_uploaded', $rules)) {
            $data['cvp_uploaded'] = $request->boolean('cvp_uploaded');
        }

        $sku->update($data);

        return back()->with('success', 'SKU updated.');
    }

    public function slaWeeklyOutput(Request $request)
    {
        return view('sku.sla-weekly-output');
    }
}
```

- [ ] **Step 5: Write the tracker view**

```blade
@extends('layouts.app')

@section('title', 'SKU Tracker')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z'/><line x1='7' y1='7' x2='7.01' y2='7'/></svg>">
@endsection

@section('styles')
<style>
    .sku-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
    .sku-kpi-card { background: var(--card); border-radius: 8px; padding: 1.5rem; border: 1px solid var(--border-light); border-top: 2px solid var(--primary); }
    .sku-kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .sku-kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .sku-kpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: white; background: var(--primary); flex-shrink: 0; }
    .sku-kpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1; font-family: 'Space Grotesk', sans-serif; color: var(--foreground); }

    .sku-filter-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
    .sku-filter-group { display: flex; flex-direction: column; gap: 0.3rem; min-width: 160px; }
    .sku-filter-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--muted-foreground); }

    .sku-table-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; overflow-x: auto; }
    table.sku-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; white-space: nowrap; }
    table.sku-table th { text-align: left; padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); color: var(--muted-foreground); font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.04em; }
    table.sku-table td { padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); }
    .sku-chip { display: inline-flex; padding: 0.18rem 0.6rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 700; }
    .sku-chip.done { background: rgba(34,197,94,0.12); color: var(--success); }
    .sku-chip.pending { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .sku-chip.none { background: var(--muted); color: var(--muted-foreground); }
    .sku-row-btn { border: 1px solid var(--border-light); border-radius: 6px; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: var(--muted-foreground); cursor: pointer; }
    .sku-row-btn:hover { border-color: var(--foreground); color: var(--foreground); }

    .sku-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; }
    .sku-form-section-title { grid-column: 1 / -1; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); margin-top: 0.5rem; border-top: 1px solid var(--border-light); padding-top: 0.75rem; }
    .sku-form-section-title:first-child { border-top: none; margin-top: 0; padding-top: 0; }
</style>
@endsection

@section('content')
<x-sidebar active="sku-tracker" :isAdmin="Auth::user()->isAdmin()" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>SKU <span class="highlight">Tracker</span></h2>
            <p>Product research to content posting pipeline</p>
        </div>
        @if($perms['can_create'])
        <button type="button" class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="openAddSku()">
            <i class="fas fa-plus"></i> Add SKU
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="sku-kpi-grid anim-up d1">
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Total SKUs</span><div class="sku-kpi-icon"><i class="fas fa-box"></i></div></div>
            <div class="sku-kpi-value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Posted</span><div class="sku-kpi-icon"><i class="fas fa-circle-check"></i></div></div>
            <div class="sku-kpi-value">{{ number_format($stats['posted']) }}</div>
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Avg PR SLA</span><div class="sku-kpi-icon"><i class="fas fa-magnifying-glass"></i></div></div>
            <div class="sku-kpi-value">{{ $stats['avg_pr_sla'] }}d</div>
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Avg Content SLA</span><div class="sku-kpi-icon"><i class="fas fa-pen"></i></div></div>
            <div class="sku-kpi-value">{{ $stats['avg_content_sla'] }}d</div>
        </div>
    </div>

    <form method="GET" class="sku-filter-card anim-up d2">
        <div class="sku-filter-group">
            <span class="sku-filter-label">Search</span>
            <input type="text" name="brand" class="input-flat" placeholder="Brand or SKU..." value="{{ $filters['brand'] ?? '' }}">
        </div>
        <div class="sku-filter-group">
            <span class="sku-filter-label">PR Status</span>
            <select name="pr_status" class="input-flat">
                <option value="">All</option>
                @foreach($prStatuses as $status)
                <option value="{{ $status }}" @selected(($filters['pr_status'] ?? '') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="sku-filter-group">
            <span class="sku-filter-label">Posted</span>
            <select name="posted" class="input-flat">
                <option value="">All</option>
                <option value="1" @selected(($filters['posted'] ?? '') === '1')>Posted</option>
                <option value="0" @selected(($filters['posted'] ?? '') === '0')>Not posted</option>
            </select>
        </div>
        <div class="sku-filter-group">
            <span class="sku-filter-label">Month</span>
            <select name="month" class="input-flat">
                <option value="">All</option>
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected(($filters['month'] ?? '') === $m)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-flat-secondary" style="height: 40px;">Filter</button>
    </form>

    <div class="sku-table-card anim-up d3">
        <table class="sku-table">
            <thead>
                <tr>
                    <th>Brand</th><th>SKU</th><th>Variant</th>
                    <th>PR Assignee</th><th>PR Status</th><th>PR SLA</th>
                    <th>Content Assignee</th><th>Content Status</th><th>Content SLA</th>
                    <th>Posted</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($skus as $sku)
                <tr>
                    <td>{{ $sku->brand }}</td>
                    <td>{{ $sku->sku }}</td>
                    <td>{{ $sku->variant ?? '—' }}</td>
                    <td>{{ $sku->pr_assignee ?? '—' }}</td>
                    <td>{{ $sku->pr_status ?? '—' }}</td>
                    <td>{{ $sku->pr_sla !== null ? $sku->pr_sla . 'd' : '—' }}</td>
                    <td>{{ $sku->content_assignee ?? '—' }}</td>
                    <td>
                        @php $csKey = match($sku->content_status) { 'DONE' => 'done', 'PENDING' => 'pending', default => 'none' }; @endphp
                        <span class="sku-chip {{ $csKey }}">{{ $sku->content_status }}</span>
                    </td>
                    <td>{{ $sku->content_sla !== null ? $sku->content_sla . 'd' : '—' }}</td>
                    <td>{{ $sku->posted ? 'Yes' : 'No' }}</td>
                    <td>
                        <button class="sku-row-btn" title="Edit" onclick='openEditSku(@json($sku))'>
                            <i class="fas fa-pencil"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="empty-state">No SKUs match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="anim-up d4" style="margin-top: 1rem;">{{ $skus->links() }}</div>
</div>

<!-- SKU Modal -->
<div class="modal-overlay" id="skuModal">
    <div class="modal-box" style="max-width: 640px;">
        <div class="modal-header">
            <h5 id="skuModalTitle">Add SKU</h5>
            <button class="modal-close" onclick="closeModal('skuModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="skuForm">
            @csrf
            <input type="hidden" name="_method" id="skuMethod" value="">
            <div class="modal-body">
                <div class="sku-form-grid">
                    <div class="sku-form-section-title">Basic Info</div>
                    <div class="form-group"><label class="form-label">Brand</label><input type="text" name="brand" id="skuBrand" class="form-input" required></div>
                    <div class="form-group">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" id="skuSku" class="form-input" oninput="checkDuplicateSku(this.value)" required>
                        <span id="skuDuplicateWarning" style="display:none;color:#f59e0b;font-size:0.72rem;font-weight:600;margin-top:0.2rem;">
                            <i class="fas fa-triangle-exclamation"></i> A SKU with this code already exists — you can still save.
                        </span>
                    </div>
                    <div class="form-group"><label class="form-label">Variant</label>
                        <select name="variant" id="skuVariant" class="form-input">
                            <option value="">—</option>
                            @foreach($variants as $v)<option value="{{ $v }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>

                    <div class="sku-form-section-title">PR Section</div>
                    <div class="form-group"><label class="form-label">PR Assignee</label><input type="text" name="pr_assignee" id="skuPrAssignee" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>
                    <div class="form-group"><label class="form-label">PR Status</label>
                        <select name="pr_status" id="skuPrStatus" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}>
                            <option value="">—</option>
                            @foreach($prStatuses as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">PR Date Started</label><input type="date" name="pr_date_started" id="skuPrStarted" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>
                    <div class="form-group"><label class="form-label">PR Date Completed</label><input type="date" name="pr_date_completed" id="skuPrCompleted" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>

                    @if($perms['can_edit_content'])
                    <div class="sku-form-section-title">Content Section</div>
                    <div class="form-group"><label class="form-label">Content Assignee</label><input type="text" name="content_assignee" id="skuContentAssignee" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Content Date Started</label><input type="date" name="content_date_started" id="skuContentStarted" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Content Date Posted</label><input type="date" name="content_date_posted" id="skuContentPosted" class="form-input"></div>

                    <div class="sku-form-section-title">Marketplace Links</div>
                    <div class="form-group"><label class="form-label">Shopee</label><input type="text" name="shopee_link" id="skuShopee" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Lazada</label><input type="text" name="lazada_link" id="skuLazada" class="form-input"></div>
                    <div class="form-group"><label class="form-label">TikTok</label><input type="text" name="tiktok_link" id="skuTiktok" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Shopify</label><input type="text" name="shopify_link" id="skuShopify" class="form-input"></div>
                    <div class="form-group"><label class="form-label">CinePro</label><input type="text" name="cinepro_link" id="skuCinepro" class="form-input"></div>
                    <div class="form-group"><label class="form-label">JG PRO Shopee</label><input type="text" name="jg_pro_shopee_link" id="skuJgShopee" class="form-input"></div>
                    <div class="form-group"><label class="form-label">JG PRO Lazada</label><input type="text" name="jg_pro_lazada_link" id="skuJgLazada" class="form-input"></div>
                    <div class="form-group"><label class="form-label">LZD Brand Mall</label><input type="text" name="lzd_brand_mall_link" id="skuLzdMall" class="form-input"></div>
                    <div class="form-group"><label class="form-label">SHP Brand Mall</label><input type="text" name="shp_brand_mall_link" id="skuShpMall" class="form-input"></div>
                    <div class="form-group"><label class="form-label">TT Brand Mall</label><input type="text" name="tt_brand_mall_link" id="skuTtMall" class="form-input"></div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('skuModal')">Cancel</button>
                <button type="submit" class="btn-flat-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
var existingSkuCodes = @json($existingSkuCodes);
var editingSkuCode = null;

function checkDuplicateSku(value) {
    var warning = document.getElementById('skuDuplicateWarning');
    var normalized = (value || '').trim().toLowerCase();
    var isDuplicate = normalized && normalized !== editingSkuCode && existingSkuCodes.indexOf(normalized) !== -1;
    warning.style.display = isDuplicate ? 'inline' : 'none';
}

function openAddSku() {
    document.getElementById('skuModalTitle').textContent = 'Add SKU';
    document.getElementById('skuForm').action = '{{ route("sku-tracker.store") }}';
    document.getElementById('skuMethod').value = '';
    document.getElementById('skuForm').reset();
    document.getElementById('skuDuplicateWarning').style.display = 'none';
    editingSkuCode = null;
    openModal('skuModal');
}

function openEditSku(sku) {
    document.getElementById('skuModalTitle').textContent = 'Edit SKU';
    document.getElementById('skuForm').action = '/sku-tracker/' + sku.id;
    document.getElementById('skuMethod').value = 'PUT';
    document.getElementById('skuDuplicateWarning').style.display = 'none';
    editingSkuCode = (sku.sku || '').trim().toLowerCase();
    document.getElementById('skuBrand').value = sku.brand || '';
    document.getElementById('skuSku').value = sku.sku || '';
    document.getElementById('skuVariant').value = sku.variant || '';
    document.getElementById('skuPrAssignee').value = sku.pr_assignee || '';
    document.getElementById('skuPrStatus').value = sku.pr_status || '';
    document.getElementById('skuPrStarted').value = sku.pr_date_started || '';
    document.getElementById('skuPrCompleted').value = sku.pr_date_completed || '';
    @if($perms['can_edit_content'])
    document.getElementById('skuContentAssignee').value = sku.content_assignee || '';
    document.getElementById('skuContentStarted').value = sku.content_date_started || '';
    document.getElementById('skuContentPosted').value = sku.content_date_posted || '';
    document.getElementById('skuShopee').value = sku.shopee_link || '';
    document.getElementById('skuLazada').value = sku.lazada_link || '';
    document.getElementById('skuTiktok').value = sku.tiktok_link || '';
    document.getElementById('skuShopify').value = sku.shopify_link || '';
    document.getElementById('skuCinepro').value = sku.cinepro_link || '';
    document.getElementById('skuJgShopee').value = sku.jg_pro_shopee_link || '';
    document.getElementById('skuJgLazada').value = sku.jg_pro_lazada_link || '';
    document.getElementById('skuLzdMall').value = sku.lzd_brand_mall_link || '';
    document.getElementById('skuShpMall').value = sku.shp_brand_mall_link || '';
    document.getElementById('skuTtMall').value = sku.tt_brand_mall_link || '';
    @endif
    openModal('skuModal');
}
</script>
@endsection
```

- [ ] **Step 6: Run the tests to verify they pass**

Run: `php artisan test tests/Feature/SkuTrackerTest.php`
Expected: PASS (6 tests)

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/SkuController.php resources/views/sku/tracker.blade.php routes/web.php tests/Feature/SkuTrackerTest.php
git commit -m "add sku tracker list page with role-based view access"
```

---

## Task 3: Historical data import

**Files:**
- Create: `database/seeders/data/sku_import.json` (generated, not hand-written)
- Create: `app/Console/Commands/ImportSkus.php`
- Modify: `.gitignore` (exclude the raw source workbook)
- Test: `tests/Feature/SkuImportTest.php`

**Interfaces:**
- Consumes: `Sku` model from Task 1.
- Produces: `php artisan sku:import` command; no other task depends on its internals.

- [ ] **Step 1: Generate the JSON extract from the source workbook**

The source file `Content x PR Posted SKUs 2026.xlsx` sits in the project root (untracked — it contains local file paths and should not be committed; add it to `.gitignore` in Step 2). Run this one-off Python script (uses `openpyxl`, already available in this environment) to produce the committed JSON extract:

```bash
python3 -c "
import openpyxl, json, datetime, os

wb = openpyxl.load_workbook('Content x PR Posted SKUs 2026.xlsx', data_only=True)
ws = wb['PR x Content']

def s(v):
    if v is None:
        return None
    if isinstance(v, datetime.datetime):
        return v.strftime('%Y-%m-%d')
    return str(v).strip() or None

def d(v):
    if isinstance(v, datetime.datetime):
        return v.strftime('%Y-%m-%d')
    return None

rows = []
for r in range(5, ws.max_row + 1):
    sku = ws.cell(row=r, column=5).value
    if not sku:
        continue
    rows.append({
        'brand': s(ws.cell(row=r, column=4).value),
        'sku': s(sku),
        'variant': s(ws.cell(row=r, column=6).value),
        'pr_file_location': s(ws.cell(row=r, column=7).value),
        'pr_assignee': s(ws.cell(row=r, column=8).value),
        'pr_status': s(ws.cell(row=r, column=9).value),
        'ready_for_cvp': bool(ws.cell(row=r, column=10).value) if ws.cell(row=r, column=10).value is not None else False,
        'remarks': s(ws.cell(row=r, column=11).value),
        'pr_date_started': d(ws.cell(row=r, column=13).value),
        'pr_date_completed': d(ws.cell(row=r, column=14).value),
        'content_assignee': s(ws.cell(row=r, column=16).value),
        'content_date_started': d(ws.cell(row=r, column=19).value),
        'content_date_posted': d(ws.cell(row=r, column=20).value),
        'cvp_uploaded': False,
        'shopee_link': s(ws.cell(row=r, column=23).value),
        'lazada_link': s(ws.cell(row=r, column=24).value),
        'tiktok_link': s(ws.cell(row=r, column=25).value),
        'jg_pro_shopee_link': s(ws.cell(row=r, column=26).value),
        'jg_pro_lazada_link': s(ws.cell(row=r, column=27).value),
        'shopify_link': s(ws.cell(row=r, column=28).value),
        'cinepro_link': s(ws.cell(row=r, column=29).value),
        'lzd_brand_mall_link': s(ws.cell(row=r, column=30).value),
        'shp_brand_mall_link': s(ws.cell(row=r, column=31).value),
        'tt_brand_mall_link': s(ws.cell(row=r, column=32).value),
    })

os.makedirs('database/seeders/data', exist_ok=True)
with open('database/seeders/data/sku_import.json', 'w', encoding='utf-8') as f:
    json.dump(rows, f, ensure_ascii=False)

print('wrote', len(rows), 'rows')
"
```

Expected output: `wrote 1230 rows`

- [ ] **Step 2: Exclude the raw workbook from git**

Add this line to `.gitignore`:

```
Content x PR Posted SKUs 2026.xlsx
```

- [ ] **Step 3: Write the failing import command test**

```php
<?php

namespace Tests\Feature;

use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_rows_from_json_fixture(): void
    {
        $path = storage_path('framework/testing/sku_import_test.json');
        file_put_contents($path, json_encode([
            ['brand' => 'TestBrand', 'sku' => 'TEST-SKU-1', 'variant' => 'Single'],
            ['brand' => 'TestBrand', 'sku' => 'TEST-SKU-2', 'variant' => 'Single'],
        ]));

        $this->artisan('sku:import', ['--path' => $path])
            ->assertExitCode(0);

        $this->assertDatabaseCount('skus', 2);
        $this->assertDatabaseHas('skus', ['sku' => 'TEST-SKU-1']);

        unlink($path);
    }

    public function test_import_refuses_to_run_twice(): void
    {
        Sku::create(['brand' => 'Existing', 'sku' => 'EXISTING-1']);

        $path = storage_path('framework/testing/sku_import_test2.json');
        file_put_contents($path, json_encode([
            ['brand' => 'TestBrand', 'sku' => 'TEST-SKU-1'],
        ]));

        $this->artisan('sku:import', ['--path' => $path])
            ->assertExitCode(1);

        $this->assertDatabaseCount('skus', 1);

        unlink($path);
    }
}
```

- [ ] **Step 4: Run the tests to verify they fail**

Run: `php artisan test tests/Feature/SkuImportTest.php`
Expected: FAIL — `Command "sku:import" is not defined`

- [ ] **Step 5: Write the import command**

```php
<?php

namespace App\Console\Commands;

use App\Models\Sku;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportSkus extends Command
{
    protected $signature = 'sku:import {--path=database/seeders/data/sku_import.json}';
    protected $description = 'One-time import of historical SKU rows from the JSON extract of the source workbook';

    public function handle(): int
    {
        if (Sku::count() > 0) {
            $this->error('skus table already has data. Aborting to avoid duplicate import.');
            return self::FAILURE;
        }

        $path = $this->option('path');
        if (!File::exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $rows = json_decode(File::get($path), true);
        $now = now();

        foreach (array_chunk($rows, 200) as $chunk) {
            $insertable = array_map(function ($row) use ($now) {
                $row['ready_for_cvp'] = !empty($row['ready_for_cvp']) ? 1 : 0;
                $row['cvp_uploaded'] = !empty($row['cvp_uploaded']) ? 1 : 0;
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                return $row;
            }, $chunk);
            Sku::insert($insertable);
        }

        $this->info('Imported ' . count($rows) . ' SKU rows.');
        return self::SUCCESS;
    }
}
```

- [ ] **Step 6: Run the tests to verify they pass**

Run: `php artisan test tests/Feature/SkuImportTest.php`
Expected: PASS (2 tests)

- [ ] **Step 7: Run the real import against the generated JSON**

Run: `php artisan sku:import`
Expected: `Imported 1230 SKU rows.`

- [ ] **Step 8: Commit**

```bash
git add database/seeders/data/sku_import.json app/Console/Commands/ImportSkus.php tests/Feature/SkuImportTest.php .gitignore
git commit -m "add historical sku import command and data"
```

---

## Task 4: SKU creation permission tests

Task 2 already implemented `store()` and the Add SKU modal. This task adds the missing test coverage for creation permissions (the design's permission matrix specifically calls out who may create rows).

**Files:**
- Test: `tests/Feature/SkuTrackerTest.php` (append)

**Interfaces:**
- Consumes: `SkuController::store()` and `SkuController::permissions()` from Task 2.

- [ ] **Step 1: Write the failing creation-permission tests**

Append to `tests/Feature/SkuTrackerTest.php`:

```php
    public function test_researcher_can_create_sku(): void
    {
        $response = $this->actingAs($this->makeUser('researcher'))->post('/sku-tracker', [
            'brand' => 'Acme',
            'sku' => 'ACME-001',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['sku' => 'ACME-001']);
    }

    public function test_content_role_cannot_create_sku(): void
    {
        $response = $this->actingAs($this->makeUser('content'))->post('/sku-tracker', [
            'brand' => 'Acme',
            'sku' => 'ACME-002',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('skus', ['sku' => 'ACME-002']);
    }

    public function test_graphics_cannot_create_sku(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->post('/sku-tracker', [
            'brand' => 'Acme',
            'sku' => 'ACME-003',
        ]);

        $response->assertStatus(403);
    }
```

- [ ] **Step 2: Run the tests to verify they fail or pass**

Run: `php artisan test tests/Feature/SkuTrackerTest.php`
Expected: PASS immediately — `store()` and its permission check already exist from Task 2. This step is a coverage check, not new implementation; if any of these three fail, the bug is in `SkuController::permissions()`'s `can_create` list — fix it there, not by weakening the test.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/SkuTrackerTest.php
git commit -m "add sku creation permission tests"
```

---

## Task 5: Field-level edit permission tests

Task 2 already implemented `update()` with field-level gating. This task adds test coverage proving Researcher can only touch PR fields, Content can only touch Content fields, and Graphics can touch neither.

**Files:**
- Test: `tests/Feature/SkuTrackerTest.php` (append)

**Interfaces:**
- Consumes: `SkuController::update()` from Task 2, `Sku` model from Task 1.

- [ ] **Step 1: Write the failing edit-permission tests**

Append to `tests/Feature/SkuTrackerTest.php`:

```php
    private function makeSku(): \App\Models\Sku
    {
        return \App\Models\Sku::create(['brand' => 'Acme', 'sku' => 'ACME-EDIT']);
    }

    public function test_researcher_can_edit_pr_fields(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('researcher'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-EDIT',
            'pr_status' => 'DONE',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_status' => 'DONE']);
    }

    public function test_researcher_cannot_edit_content_fields(): void
    {
        $sku = $this->makeSku();

        $this->actingAs($this->makeUser('researcher'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-EDIT',
            'content_assignee' => 'ShouldNotSave',
        ]);

        $this->assertDatabaseMissing('skus', ['id' => $sku->id, 'content_assignee' => 'ShouldNotSave']);
    }

    public function test_content_can_edit_content_fields(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('content'))->put("/sku-tracker/{$sku->id}", [
            'content_assignee' => 'Em',
            'content_date_started' => '2026-07-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'content_assignee' => 'Em']);
    }

    public function test_content_cannot_edit_pr_fields(): void
    {
        $sku = $this->makeSku();

        $this->actingAs($this->makeUser('content'))->put("/sku-tracker/{$sku->id}", [
            'pr_status' => 'DONE',
        ]);

        $this->assertDatabaseMissing('skus', ['id' => $sku->id, 'pr_status' => 'DONE']);
    }

    public function test_graphics_cannot_edit_anything(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('graphics'))->put("/sku-tracker/{$sku->id}", [
            'pr_status' => 'DONE',
        ]);

        $response->assertStatus(403);
    }

    public function test_backend_can_edit_both_sections(): void
    {
        $sku = $this->makeSku();

        $response = $this->actingAs($this->makeUser('backend'))->put("/sku-tracker/{$sku->id}", [
            'brand' => 'Acme',
            'sku' => 'ACME-EDIT',
            'pr_status' => 'DONE',
            'content_assignee' => 'Vin',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('skus', ['id' => $sku->id, 'pr_status' => 'DONE', 'content_assignee' => 'Vin']);
    }
```

- [ ] **Step 2: Run the tests to verify they pass**

Run: `php artisan test tests/Feature/SkuTrackerTest.php`
Expected: PASS. If `test_researcher_cannot_edit_content_fields` or `test_content_cannot_edit_pr_fields` fail, the `update()` method in `SkuController` (Task 2) is validating fields outside the caller's permission — re-check that `$rules` is only ever built from the two `if ($perms[...])` blocks and never merged unconditionally.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/SkuTrackerTest.php
git commit -m "add field-level sku edit permission tests"
```

---

## Task 6: SLA and Weekly Output page

**Files:**
- Modify: `app/Http/Controllers/SkuController.php` (`slaWeeklyOutput` method)
- Create: `resources/views/sku/sla-weekly-output.blade.php`
- Test: `tests/Feature/SkuSlaWeeklyOutputTest.php`

**Interfaces:**
- Consumes: `Sku` model (`pr_sla`, `content_sla` accessors) from Task 1.
- Produces: fully-populated `GET /sla-weekly-output` view; nothing downstream depends on this beyond the sidebar links added in Task 7.

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature;

use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkuSlaWeeklyOutputTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_analyst_is_blocked(): void
    {
        $response = $this->actingAs($this->makeUser('analyst'))->get('/sla-weekly-output');
        $response->assertStatus(403);
    }

    public function test_graphics_can_view_but_page_has_no_edit_form(): void
    {
        $response = $this->actingAs($this->makeUser('graphics'))->get('/sla-weekly-output');
        $response->assertStatus(200);
        $response->assertDontSee('<form method="POST"', false);
    }

    public function test_page_shows_weekly_averages_grouped_by_iso_week(): void
    {
        Sku::create([
            'brand' => 'B', 'sku' => 'S1',
            'pr_date_started' => '2026-06-01', 'pr_date_completed' => '2026-06-05',
        ]);
        Sku::create([
            'brand' => 'B', 'sku' => 'S2',
            'pr_date_started' => '2026-07-01', 'pr_date_completed' => '2026-07-08',
        ]);

        $response = $this->actingAs($this->makeUser('backend'))
            ->get('/sla-weekly-output?month_a=2026-07&month_b=2026-06');

        $response->assertStatus(200);
        $response->assertSee('Week');
    }
}
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test tests/Feature/SkuSlaWeeklyOutputTest.php`
Expected: FAIL — `assertDontSee`/`assertSee` fail since `slaWeeklyOutput()` currently returns an empty view with no content (from Task 2's stub).

- [ ] **Step 3: Implement `slaWeeklyOutput()`**

Replace the stub method in `app/Http/Controllers/SkuController.php`:

```php
    public function slaWeeklyOutput(Request $request)
    {
        $availableMonths = Sku::selectRaw("strftime('%Y-%m', pr_date_started) as m")
            ->whereNotNull('pr_date_started')
            ->distinct()
            ->orderByDesc('m')
            ->pluck('m')
            ->filter()
            ->values();

        $monthA = $request->query('month_a', $availableMonths->first());
        $monthB = $request->query('month_b', $availableMonths->get(1, $availableMonths->first()));

        $weeklyAverages = function (?string $month) {
            if (!$month) {
                return collect();
            }
            return Sku::whereNotNull('pr_date_started')
                ->whereRaw("strftime('%Y-%m', pr_date_started) = ?", [$month])
                ->get()
                ->groupBy(fn ($sku) => (int) $sku->pr_date_started->format('W'))
                ->map(function ($rows, $week) {
                    return [
                        'week' => $week,
                        'avg_pr_sla' => round($rows->map->pr_sla->filter()->avg() ?? 0, 1),
                        'avg_content_sla' => round($rows->map->content_sla->filter()->avg() ?? 0, 1),
                    ];
                })
                ->sortKeys()
                ->values();
        };

        $weeksA = $weeklyAverages($monthA)->keyBy('week');
        $weeksB = $weeklyAverages($monthB)->keyBy('week');
        $allWeeks = $weeksA->keys()->merge($weeksB->keys())->unique()->sort()->values();

        $comparison = $allWeeks->map(function ($week) use ($weeksA, $weeksB) {
            $a = $weeksA->get($week, ['avg_pr_sla' => 0, 'avg_content_sla' => 0]);
            $b = $weeksB->get($week, ['avg_pr_sla' => 0, 'avg_content_sla' => 0]);
            $prChange = $a['avg_pr_sla'] > 0 ? round((($b['avg_pr_sla'] - $a['avg_pr_sla']) / $a['avg_pr_sla']) * 100, 1) : null;
            $contentChange = $a['avg_content_sla'] > 0 ? round((($b['avg_content_sla'] - $a['avg_content_sla']) / $a['avg_content_sla']) * 100, 1) : null;

            return [
                'week' => $week,
                'pr_a' => $a['avg_pr_sla'],
                'pr_b' => $b['avg_pr_sla'],
                'pr_change' => $prChange,
                'content_a' => $a['avg_content_sla'],
                'content_b' => $b['avg_content_sla'],
                'content_change' => $contentChange,
            ];
        });

        return view('sku.sla-weekly-output', [
            'availableMonths' => $availableMonths,
            'monthA' => $monthA,
            'monthB' => $monthB,
            'comparison' => $comparison,
        ]);
    }
```

- [ ] **Step 4: Write the view**

```blade
@extends('layouts.app')

@section('title', 'SLA and Weekly Output')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M3 3v18h18'/><path d='M18 17V9'/><path d='M13 17V5'/><path d='M8 17v-3'/></svg>">
@endsection

@section('styles')
<style>
    .slaw-controls { display: flex; gap: 0.75rem; align-items: flex-end; margin-bottom: 1.25rem; }
    .slaw-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .slaw-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--muted-foreground); }
    .slaw-table-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; overflow-x: auto; }
    table.slaw-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    table.slaw-table th { text-align: left; padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); color: var(--muted-foreground); font-size: 0.68rem; text-transform: uppercase; }
    table.slaw-table td { padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); }
    .slaw-change.up { color: var(--destructive); }
    .slaw-change.down { color: var(--success); }
</style>
@endsection

@section('content')
<x-sidebar active="sla-weekly-output" :isAdmin="Auth::user()->isAdmin()" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>SLA and <span class="highlight">Weekly Output</span></h2>
            <p>Weekly SLA averages, compared month over month — read-only</p>
        </div>
    </div>

    <form method="GET" class="slaw-controls anim-up d1">
        <div class="slaw-group">
            <span class="slaw-label">Month A (baseline)</span>
            <select name="month_a" class="input-flat" onchange="this.form.submit()">
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected($m === $monthA)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
        <div class="slaw-group">
            <span class="slaw-label">Month B (compare to)</span>
            <select name="month_b" class="input-flat" onchange="this.form.submit()">
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected($m === $monthB)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="slaw-table-card anim-up d2">
        <table class="slaw-table">
            <thead>
                <tr>
                    <th>Week</th>
                    <th>PR SLA ({{ $monthA ? \Carbon\Carbon::parse($monthA)->format('M') : '—' }})</th>
                    <th>PR SLA ({{ $monthB ? \Carbon\Carbon::parse($monthB)->format('M') : '—' }})</th>
                    <th>% Change</th>
                    <th>Content SLA ({{ $monthA ? \Carbon\Carbon::parse($monthA)->format('M') : '—' }})</th>
                    <th>Content SLA ({{ $monthB ? \Carbon\Carbon::parse($monthB)->format('M') : '—' }})</th>
                    <th>% Change</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comparison as $row)
                <tr>
                    <td>Week {{ $row['week'] }}</td>
                    <td>{{ $row['pr_a'] }}d</td>
                    <td>{{ $row['pr_b'] }}d</td>
                    <td class="slaw-change {{ $row['pr_change'] !== null && $row['pr_change'] > 0 ? 'up' : 'down' }}">
                        {{ $row['pr_change'] !== null ? $row['pr_change'] . '%' : '—' }}
                    </td>
                    <td>{{ $row['content_a'] }}d</td>
                    <td>{{ $row['content_b'] }}d</td>
                    <td class="slaw-change {{ $row['content_change'] !== null && $row['content_change'] > 0 ? 'up' : 'down' }}">
                        {{ $row['content_change'] !== null ? $row['content_change'] . '%' : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty-state">No data for the selected months.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

- [ ] **Step 5: Run the tests to verify they pass**

Run: `php artisan test tests/Feature/SkuSlaWeeklyOutputTest.php`
Expected: PASS (3 tests)

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/SkuController.php resources/views/sku/sla-weekly-output.blade.php tests/Feature/SkuSlaWeeklyOutputTest.php
git commit -m "add sla and weekly output analytics page"
```

---

## Task 7: Sidebar navigation + command palette entries

**Files:**
- Modify: `resources/views/components/sidebar.blade.php`
- Modify: `resources/views/layouts/app.blade.php`
- Test: `tests/Feature/SkuTrackerTest.php` (append)

**Interfaces:**
- Consumes: route names `sku-tracker` and `sla-weekly-output` from Task 2.

- [ ] **Step 1: Write the failing sidebar visibility tests**

Append to `tests/Feature/SkuTrackerTest.php`:

```php
    public function test_sku_management_nav_visible_to_non_analyst_member(): void
    {
        $response = $this->actingAs($this->makeUser('content'))->get('/dashboard');
        $response->assertSee('SKU Management');
        $response->assertSee('SKU Tracker');
    }

    public function test_sku_management_nav_hidden_from_analyst(): void
    {
        $response = $this->actingAs($this->makeUser('analyst'))->get('/dashboard');
        $response->assertDontSee('SKU Management');
    }

    public function test_sku_management_nav_visible_to_admin(): void
    {
        $response = $this->actingAs($this->makeUser('manager'))->get('/admin');
        $response->assertSee('SKU Management');
    }
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test tests/Feature/SkuTrackerTest.php`
Expected: FAIL on the three new tests — sidebar doesn't have the "SKU Management" section yet.

- [ ] **Step 3: Add the nav group to the admin sidebar block**

In `resources/views/components/sidebar.blade.php`, insert this right after the "Brand Management" block (after line 37, the `</li>` for Brand Catalogs, before the "General" `<li style="height:1px...">` divider on line 40):

```blade
            {{-- ── SKU Management ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">SKU Management</li>
            <li><a href="{{ route('sku-tracker') }}"        class="{{ $active === 'sku-tracker'        ? 'active' : '' }}"><i class="fas fa-box"></i> SKU Tracker</a></li>
            <li><a href="{{ route('sla-weekly-output') }}"  class="{{ $active === 'sla-weekly-output'  ? 'active' : '' }}"><i class="fas fa-chart-line"></i> SLA and Weekly Output</a></li>
```

- [ ] **Step 4: Add the nav group to the member sidebar block**

In the same file, in the `@else` branch, insert this right after the "Work" block's closing `@endif` (after line 63, before the "Tools" block starting at line 65):

```blade
            {{-- ── SKU Management (non-analyst) ── --}}
            @if($role !== 'analyst')
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">SKU Management</li>
            <li><a href="{{ route('sku-tracker') }}"        class="{{ $active === 'sku-tracker'        ? 'active' : '' }}"><i class="fas fa-box"></i> SKU Tracker</a></li>
            <li><a href="{{ route('sla-weekly-output') }}"  class="{{ $active === 'sla-weekly-output'  ? 'active' : '' }}"><i class="fas fa-chart-line"></i> SLA and Weekly Output</a></li>
            @endif
```

- [ ] **Step 5: Register the pages in the command palette**

In `resources/views/layouts/app.blade.php`, add an entry to the `adminPages` array (after the `Brand Catalogs` entry, around line 853):

```js
            { name: 'SKU Tracker',    desc: 'PR to content pipeline', icon: 'fa-box',        url: '{{ route("sku-tracker") }}' },
            { name: 'SLA and Weekly Output', desc: 'Weekly SLA analytics', icon: 'fa-chart-line', url: '{{ route("sla-weekly-output") }}' },
```

And to the `memberPages` array, inside the existing `@if($cmdRole !== 'analyst')` block (around line 864, alongside `Calendar`):

```js
            { name: 'SKU Tracker',    desc: 'PR to content pipeline', icon: 'fa-box',        url: '{{ route("sku-tracker") }}' },
            { name: 'SLA and Weekly Output', desc: 'Weekly SLA analytics', icon: 'fa-chart-line', url: '{{ route("sla-weekly-output") }}' },
```

- [ ] **Step 6: Run the tests to verify they pass**

Run: `php artisan test tests/Feature/SkuTrackerTest.php`
Expected: PASS (all tests in this file)

- [ ] **Step 7: Run the full test suite**

Run: `php artisan test`
Expected: PASS — all existing tests plus the new `Sku*` tests pass, no regressions.

- [ ] **Step 8: Commit**

```bash
git add resources/views/components/sidebar.blade.php resources/views/layouts/app.blade.php tests/Feature/SkuTrackerTest.php
git commit -m "add sku management to sidebar nav and command palette"
```

---

## Manual verification (after Task 7)

Not automatable via PHPUnit — run through this once with `php artisan serve` before considering the feature done:

1. Log in as a `researcher` user → confirm "SKU Management" appears in the sidebar, "Add SKU" button is visible on SKU Tracker, PR fields are editable, Content fields are disabled in the edit modal.
2. Log in as a `content` user → confirm no "Add SKU" button, Content fields editable, PR fields disabled.
3. Log in as a `graphics` user → confirm the page is visible but the edit modal has no enabled inputs and no Add button.
4. Log in as an `analyst` user → confirm `/sku-tracker` and `/sla-weekly-output` both return a 403 page, and neither link appears anywhere in the sidebar or command palette (Ctrl+K).
5. Log in as `manager` or `head` → confirm SKU Management shows up under the Admin Panel sidebar and both PR and Content fields are editable.
6. On SLA and Weekly Output, switch Month A / Month B dropdowns and confirm the week-by-week table updates.
7. Spot-check a handful of imported rows against the original spreadsheet (e.g. search "DEARKOL" on SKU Tracker) to confirm the import mapped columns correctly.
8. In the Add SKU modal, type a SKU code that already exists (e.g. an imported one) and confirm the inline duplicate warning appears but does not block saving.
