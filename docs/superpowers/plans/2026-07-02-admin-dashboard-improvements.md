# Admin Dashboard Improvements Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a 30-day team trend chart, a role-filterable task-type breakdown, and an attendance week summary to the admin dashboard, while removing duplicated Top Contributor content and fixing dark-mode chart tooltips.

**Architecture:** All data is computed in `AdminController::dashboard()` and passed to the Blade view as `json_encode`d JSON; interactivity (role pills, 7d/30d toggle) is client-side ApexCharts `updateOptions` — no new routes. Spec: `docs/superpowers/specs/2026-07-02-admin-dashboard-improvements-design.md`.

**Tech Stack:** Laravel 12 (PHP), Blade, ApexCharts (loaded globally in layout), inline per-view CSS using theme CSS variables, PHPUnit feature tests (sqlite `:memory:`, array cache).

## Global Constraints

- Commit messages: short, casual, lowercase — e.g. `"remove duplicate top contributor"`. **Never add a `Co-Authored-By` line** (user rule).
- Run tests with: `php artisan test --filter=AdminDashboardTest` (full suite before final commit: `php artisan test`).
- All view work happens in `resources/views/admin/dashboard.blade.php`; all controller work in `app/Http/Controllers/AdminController.php` inside `dashboard()`.
- Styling: reuse existing CSS variables (`--card`, `--border`, `--muted`, `--muted-foreground`, `--foreground`); role hex colors are `content #0ea5e9`, `graphics #f59e0b`, `backend #f43f5e`, `researcher #10b981`.
- Member roles everywhere: `['content', 'graphics', 'backend', 'researcher']` (managers/head/analyst excluded).
- `TaskLabels::get($role)` (in `app/Support/TaskLabels.php`) returns `['task_1' => 'Label', 'desc_task_1' => '…', …]` and **falls back to the `content` role's labels when a role has no `TaskCategory` rows. If `content` itself has no rows it recurses infinitely — tests MUST seed `TaskCategory` rows for `content` (done once in the shared test `setUp`).**
- `TaskCategory` fillable is only `department`, `column_key`, `label` (NOT `description`).

---

### Task 1: Test scaffold + remove duplicate Top Contributor

**Files:**
- Create: `tests/Feature/AdminDashboardTest.php`
- Modify: `resources/views/admin/dashboard.blade.php`

**Interfaces:**
- Consumes: existing route `admin.dashboard` (admin middleware; a user with `role = 'manager'` passes).
- Produces: `AdminDashboardTest` class with helpers `makeAdmin()`, `makeMember(string $role = 'content')` and a `setUp()` that seeds `content` TaskCategory rows — **all later test steps add methods to this class and rely on that seeding**.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/AdminDashboardTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // TaskLabels::get() falls back to 'content' and recurses infinitely
        // if content has no categories — always seed them.
        $labels = ['New SKU', 'Variation SKU', 'Data Gathering', 'Update Listings', 'Other Tasks'];
        foreach ($labels as $i => $label) {
            TaskCategory::create([
                'department' => 'content',
                'column_key' => 'task_' . ($i + 1),
                'label'      => $label,
            ]);
        }
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    private function makeMember(string $role = 'content'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_top_contributor_shown_only_once(): void
    {
        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->toDateString(),
            'task_1' => 5, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('tc-card');
        // Only the amber KPI card keeps the "Top This Month" label
        $this->assertSame(1, substr_count($response->getContent(), 'Top This Month'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: FAIL — `tc-card` is found in the response (and "Top This Month" appears 2×).

- [ ] **Step 3: Remove the duplicates from the view**

In `resources/views/admin/dashboard.blade.php`:

**3a.** Delete the "Top This Month" stat from the welcome banner (inside `.wb-stats`, currently ~lines 216–222):

```blade
            @if($topContributor)
            <div class="wb-divider"></div>
            <div class="wb-stat">
                <div class="wb-stat-val" style="font-size:0.95rem;">{{ $topContributor->first_name }}</div>
                <div class="wb-stat-label">Top This Month</div>
            </div>
            @endif
```

**3b.** Delete the standalone Top Contributor card (in the bottom-right column, currently ~lines 455–471), including its `@if($topContributor)`/`@endif` wrapper:

```blade
            {{-- Top Contributor --}}
            @if($topContributor)
            <div class="tc-card">
                <div class="tc-header-label"><i class="fas fa-trophy" style="color:#f59e0b;"></i> Top Contributor — {{ now()->format('F') }}</div>
                <div class="tc-body">
                    <div class="tc-initial">{{ strtoupper(substr($topContributor->first_name, 0, 1)) }}</div>
                    <div class="tc-info">
                        <div class="tc-name">{{ $topContributor->first_name }}</div>
                        <div class="tc-role">@{{ $topContributor->username }}</div>
                    </div>
                    <div class="tc-score">
                        <div class="tc-score-val">{{ number_format($topContributor->total) }}</div>
                        <div class="tc-score-label">tasks</div>
                    </div>
                </div>
            </div>
            @endif
```

**3c.** Delete the whole `/* ── Top Contributor ──…*/` CSS block from the `styles` section (all rules starting `.tc-card`, `.tc-header-label`, `.tc-body`, `.tc-initial`, `.tc-info`, `.tc-name`, `.tc-role`, `.tc-score`, `.tc-score-val`, `.tc-score-label`).

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/AdminDashboardTest.php resources/views/admin/dashboard.blade.php
git commit -m "remove duplicate top contributor from dashboard"
```

---

### Task 2: Controller — 30-day trend data

**Files:**
- Modify: `app/Http/Controllers/AdminController.php` (the `dashboard()` method)
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: existing `$dailyTotals` query in `dashboard()`.
- Produces: view variables `trendLabels` (array of 30 strings like `"Jun 03"`), `trendData` (array of 30 ints, oldest→today), `trendSundayIndices` (array of ints, indices into the 30-slot arrays). `$sparkData` becomes `array_slice($trendData, -7)`.

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest`:

```php
    public function test_trend_data_covers_30_days_and_zero_fills(): void
    {
        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->toDateString(),
            'task_1' => 3, 'task_2' => 2, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->subDays(10)->toDateString(),
            'task_1' => 4, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $trendData = $response->viewData('trendData');
        $this->assertCount(30, $trendData);
        $this->assertSame(5, $trendData[29]);          // today = index 29
        $this->assertSame(4, $trendData[19]);          // 10 days ago
        $this->assertSame(0, $trendData[0]);           // zero-filled
        $this->assertCount(30, $response->viewData('trendLabels'));
        $this->assertSame(now()->format('M j'), $response->viewData('trendLabels')[29]);
        $this->assertSame(array_slice($trendData, -7), $response->viewData('sparkData'));
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_trend_data_covers_30_days_and_zero_fills`
Expected: FAIL — `trendData` view variable is null.

- [ ] **Step 3: Implement in the controller**

In `AdminController::dashboard()`:

**3a.** Widen the `$dailyTotals` window from 7 to 30 days — change:

```php
        $dailyTotals = DailyLog::where('date', '>=', now()->subDays(6)->startOfDay())
```

to:

```php
        $dailyTotals = DailyLog::where('date', '>=', now()->subDays(29)->startOfDay())
```

(The `$chartLabels`/`$chartNewSku`/etc. arrays derived from it are not used by the admin dashboard view — content change is harmless.)

**3b.** Replace the existing sparkline block (the `// Sparkline — derived from $dailyTotals…` comment through the `for` loop that fills `$sparkData`) with:

```php
        // 30-day trend — derived from $dailyTotals already in memory, no extra queries
        $trendMap           = $dailyTotals->keyBy(fn($d) => $d->date->format('Y-m-d'));
        $trendLabels        = [];
        $trendData          = [];
        $trendSundayIndices = [];
        for ($i = 29; $i >= 0; $i--) {
            $date          = now()->subDays($i);
            $trendLabels[] = $date->format('M j');
            if ($date->dayOfWeek === 0) {
                $trendSundayIndices[] = 29 - $i;
            }
            $day         = $trendMap->get($date->format('Y-m-d'));
            $trendData[] = $day
                ? (int) ($day->total_task_1 + $day->total_task_2 + $day->total_task_3 + $day->total_task_4 + $day->total_task_5)
                : 0;
        }
        $sparkData = array_slice($trendData, -7);
```

**3c.** Add `'trendLabels', 'trendData', 'trendSundayIndices'` to the `compact(...)` list in the `return view(...)` call (keep `'sparkData'` there).

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (both tests)

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AdminController.php tests/Feature/AdminDashboardTest.php
git commit -m "add 30 day trend data for admin dashboard"
```

---

### Task 3: View — Team Trend chart with 7d/30d toggle

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: `$trendLabels`, `$trendData`, `$trendSundayIndices` from Task 2; existing `isDark` JS variable (already computed at the top of the `DOMContentLoaded` handler).
- Produces: `.dash-2col-insights` grid row (Task 5 adds its second card), shared `.pill`/`.pill-row` CSS (Task 5 reuses), `#trendChart` element.

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest`:

```php
    public function test_trend_chart_and_toggle_render(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('id="trendChart"', false);
        $response->assertSee('data-days="7"', false);
        $response->assertSee('data-days="30"', false);
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_trend_chart_and_toggle_render`
Expected: FAIL — `id="trendChart"` not found.

- [ ] **Step 3: Add CSS**

In the `@section('styles')` block, after the `/* ── Role Overview ──…*/` section, add:

```css
/* ── Insights row (trend + task types) ─────────────────── */
.dash-2col-insights { display: grid; grid-template-columns: 60fr 40fr; gap: 1.125rem; margin-bottom: 1.25rem; align-items: start; }
.insight-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 1.125rem 1.25rem; }
.insight-header { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap; }
.insight-header h4 { font-size: 0.88rem; font-weight: 700; margin: 0; }
.pill-row { display: flex; gap: 0.3rem; flex-wrap: wrap; }
.pill {
    font-size: 0.68rem; font-weight: 700; padding: 3px 10px; border-radius: 9999px;
    border: 1px solid var(--border); background: transparent; color: var(--muted-foreground);
    cursor: pointer; font-family: inherit; transition: all 0.15s;
}
.pill:hover { color: var(--foreground); border-color: var(--border-strong); }
.pill.active { background: #6366f1; border-color: #6366f1; color: #fff; }
```

And add to the responsive section at the bottom of the styles:

```css
@media (max-width: 900px)  { .dash-2col-insights { grid-template-columns: 1fr; } }
```

- [ ] **Step 4: Add the HTML row**

In the content section, between the closing `</div>` of the `.dash-2col` row (Team Pulse + Who Logged Today) and the `{{-- ── Role Activity ── --}}` comment, insert:

```blade
    {{-- ── Team Trend + Task Types ── --}}
    <div class="dash-2col-insights anim-up d3">

        {{-- Team Output Trend --}}
        <div class="insight-card">
            <div class="insight-header">
                <h4>Team Output Trend</h4>
                <div class="pill-row" id="trendRange">
                    <button type="button" class="pill" data-days="7">7d</button>
                    <button type="button" class="pill active" data-days="30">30d</button>
                </div>
            </div>
            <div id="trendChart" style="height:210px;"></div>
        </div>

    </div>
```

Then bump the entrance stagger of everything below: on the Role Activity `dash-heading` and `role-ov-grid` change `d3` → `d4`, and on the bottom `.dash-2col-main` change `d4` → `d5`.

- [ ] **Step 5: Add the chart JS**

In the `@section('scripts')` `DOMContentLoaded` handler, after the sparkline block and after the existing `roleHexColors` declaration (order matters — later JS reuses it), add:

```js
    // Team trend chart (7d/30d)
    var trendLabels  = {!! json_encode($trendLabels) !!};
    var trendData    = {!! json_encode($trendData) !!};
    var trendSundays = {!! json_encode($trendSundayIndices) !!};
    var trendEl      = document.getElementById('trendChart');

    function trendOptions(days) {
        var offset = trendLabels.length - days;
        return {
            chart: { type: 'area', height: 210, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#94a3b8' },
            series: [{ name: 'Tasks', data: trendData.slice(-days) }],
            colors: ['#6366f1'],
            stroke: { width: 2, curve: 'smooth' },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.03 } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: trendLabels.slice(-days),
                tickAmount: days === 30 ? 9 : 6,
                labels: { style: { fontSize: '10px', fontWeight: 600 }, rotate: 0 },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { min: 0, forceNiceScale: true, labels: { style: { fontSize: '10px' } } },
            grid: { borderColor: 'rgba(148,163,184,0.15)', strokeDashArray: 4, padding: { left: 8, right: 8 } },
            tooltip: {
                theme: isDark ? 'dark' : 'light', style: { fontSize: '12px' },
                x: { formatter: function (v, o) {
                    var i = o.dataPointIndex + offset;
                    return trendSundays.indexOf(i) !== -1 ? trendLabels[i] + ' (RDO)' : trendLabels[i];
                } },
                y: { formatter: function (v) { return v + ' tasks'; } }
            }
        };
    }

    if (trendEl) {
        var trendChart = new ApexCharts(trendEl, trendOptions(30));
        trendChart.render();
        document.querySelectorAll('#trendRange .pill').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#trendRange .pill').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                trendChart.updateOptions(trendOptions(parseInt(btn.dataset.days, 10)));
            });
        });
    }
```

Note: `isDark` is already declared at the top of the handler — do not redeclare it.

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all)

- [ ] **Step 7: Commit**

```bash
git add resources/views/admin/dashboard.blade.php tests/Feature/AdminDashboardTest.php
git commit -m "add team output trend chart with 7d/30d toggle"
```

---

### Task 4: Controller — task-type breakdown per role

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: `App\Support\TaskLabels::get(string $role): array` (keys `task_1`…`task_5`).
- Produces: view variable `taskTypeBreakdown` — a Collection keyed by role, each value `['labels' => string[5], 'data' => int[5]]`. Roles with no logs get zeroed `data`; labels always resolve (per-role or content fallback).

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest`:

```php
    public function test_task_type_breakdown_groups_this_month_by_role(): void
    {
        $admin    = $this->makeAdmin();
        $content  = $this->makeMember();
        $graphics = $this->makeMember('graphics');
        TaskCategory::create(['department' => 'graphics', 'column_key' => 'task_1', 'label' => 'Banners']);
        TaskCategory::create(['department' => 'graphics', 'column_key' => 'task_2', 'label' => 'Thumbnails']);

        DailyLog::create([
            'user_id' => $content->id, 'date' => now()->toDateString(),
            'task_1' => 5, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);
        DailyLog::create([
            'user_id' => $graphics->id, 'date' => now()->toDateString(),
            'task_1' => 0, 'task_2' => 7, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);
        DailyLog::create([ // last day of previous month — excluded (avoid subMonth() overflow)
            'user_id' => $content->id, 'date' => now()->startOfMonth()->subDay()->toDateString(),
            'task_1' => 99, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $bd = $response->viewData('taskTypeBreakdown');
        $this->assertSame(5, $bd['content']['data'][0]);
        $this->assertSame('New SKU', $bd['content']['labels'][0]);
        $this->assertSame(7, $bd['graphics']['data'][1]);
        $this->assertSame('Banners', $bd['graphics']['labels'][0]);
        // no researcher logs -> zeroed data, labels fall back to content's
        $this->assertSame([0, 0, 0, 0, 0], $bd['researcher']['data']);
        $this->assertSame('New SKU', $bd['researcher']['labels'][0]);
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_task_type_breakdown_groups_this_month_by_role`
Expected: FAIL — `taskTypeBreakdown` view variable is null.

- [ ] **Step 3: Implement in the controller**

**3a.** Add the import at the top of `AdminController.php`:

```php
use App\Support\TaskLabels;
```

**3b.** In `dashboard()`, after the `$roleBreakdown` block, add:

```php
        // Task-type breakdown per role — this month
        $memberRoles = ['content', 'graphics', 'backend', 'researcher'];
        $taskTypeRaw = DailyLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->join('users', 'daily_logs.user_id', '=', 'users.id')
            ->whereIn('users.role', $memberRoles)
            ->select(
                'users.role',
                DB::raw('SUM(task_1) as t1'), DB::raw('SUM(task_2) as t2'),
                DB::raw('SUM(task_3) as t3'), DB::raw('SUM(task_4) as t4'),
                DB::raw('SUM(task_5) as t5')
            )
            ->groupBy('users.role')
            ->get()->keyBy('role');

        $taskTypeBreakdown = collect($memberRoles)->mapWithKeys(function ($role) use ($taskTypeRaw) {
            $labels = TaskLabels::get($role);
            $row    = $taskTypeRaw->get($role);
            $names  = [];
            $data   = [];
            for ($i = 1; $i <= 5; $i++) {
                $names[] = $labels["task_$i"] ?? "Task $i";
                $data[]  = $row ? (int) $row->{"t$i"} : 0;
            }
            return [$role => ['labels' => $names, 'data' => $data]];
        });
```

**3c.** Add `'taskTypeBreakdown'` to the `compact(...)` list.

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all)

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AdminController.php tests/Feature/AdminDashboardTest.php
git commit -m "add per role task type breakdown data"
```

---

### Task 5: View — Task-Type Breakdown card with role pills

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: `$taskTypeBreakdown` from Task 4; `.insight-card`/`.pill` CSS and the `.dash-2col-insights` row from Task 3; `roleHexColors` JS object (already declared for the role charts); `isDark`.
- Produces: `#taskTypeChart` element, `#ttRoles` pill group.

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest`:

```php
    public function test_task_type_chart_and_role_pills_render(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('id="taskTypeChart"', false);
        $response->assertSee('data-role="content"', false);
        $response->assertSee('data-role="graphics"', false);
        $response->assertSee('data-role="backend"', false);
        $response->assertSee('data-role="researcher"', false);
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_task_type_chart_and_role_pills_render`
Expected: FAIL — `id="taskTypeChart"` not found.

- [ ] **Step 3: Add the card HTML**

Inside the `.dash-2col-insights` row from Task 3, directly after the Team Output Trend `.insight-card` closes, add:

```blade
        {{-- Task Types — this month --}}
        <div class="insight-card">
            <div class="insight-header">
                <h4>Task Types — {{ now()->format('F') }}</h4>
                <div class="pill-row" id="ttRoles">
                    <button type="button" class="pill" data-role="content">Content</button>
                    <button type="button" class="pill" data-role="graphics">Graphics</button>
                    <button type="button" class="pill" data-role="backend">Backend</button>
                    <button type="button" class="pill" data-role="researcher">Research</button>
                </div>
            </div>
            <div id="taskTypeChart" style="height:210px;"></div>
        </div>
```

- [ ] **Step 4: Add the chart JS**

In the scripts section, after the trend chart block (and therefore after `roleHexColors` is declared), add:

```js
    // Task-type breakdown (role filter)
    var ttData = {!! json_encode($taskTypeBreakdown) !!};
    var ttEl   = document.getElementById('taskTypeChart');

    function ttSetActive(role) {
        document.querySelectorAll('#ttRoles .pill').forEach(function (b) {
            var on = b.dataset.role === role;
            b.classList.toggle('active', on);
            b.style.background  = on ? roleHexColors[role] : '';
            b.style.borderColor = on ? roleHexColors[role] : '';
        });
    }

    function ttOptions(role) {
        return {
            chart: { type: 'bar', height: 210, toolbar: { show: false }, fontFamily: 'Inter', foreColor: '#94a3b8' },
            series: [{ name: 'Tasks', data: ttData[role].data }],
            colors: [roleHexColors[role] || '#6366f1'],
            plotOptions: { bar: { horizontal: true, barHeight: '55%', borderRadius: 3, borderRadiusApplication: 'end' } },
            dataLabels: { enabled: false },
            xaxis: { categories: ttData[role].labels, labels: { style: { fontSize: '10px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { fontSize: '11px', fontWeight: 600 }, maxWidth: 120 } },
            grid: { borderColor: 'rgba(148,163,184,0.15)', strokeDashArray: 4, padding: { left: 2, right: 8 } },
            tooltip: { theme: isDark ? 'dark' : 'light', style: { fontSize: '12px' }, y: { formatter: function (v) { return v + ' tasks'; } } }
        };
    }

    if (ttEl && ttData.content) {
        var ttChart = new ApexCharts(ttEl, ttOptions('content'));
        ttChart.render();
        ttSetActive('content');
        document.querySelectorAll('#ttRoles .pill').forEach(function (btn) {
            btn.addEventListener('click', function () {
                ttSetActive(btn.dataset.role);
                ttChart.updateOptions(ttOptions(btn.dataset.role));
            });
        });
    }
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all)

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/dashboard.blade.php tests/Feature/AdminDashboardTest.php
git commit -m "add task type breakdown card with role filter"
```

---

### Task 6: Controller — attendance week summary

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: `App\Models\Attendance` (`user_id`, `date` (cast to date), `status`; statuses `present, half_day, vl, sl, absent, ut, holiday`), `user()` relation.
- Produces: view variables `attWeekCounts` (Collection: `present, half_day, vl, sl, ut, absent` → int; `holiday` excluded) and `outToday` (Collection of `User` models whose status today is `absent|vl|sl|half_day|ut`).

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest` (add `use App\Models\Attendance;` to the imports):

```php
    public function test_attendance_week_counts_and_out_today(): void
    {
        // Freeze to a Wednesday so the Mon–Sat window is deterministic
        $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0));

        $admin    = $this->makeAdmin();
        $content  = $this->makeMember();
        $graphics = $this->makeMember('graphics');

        Attendance::create(['user_id' => $content->id,  'date' => now()->toDateString(),           'status' => 'present']);
        Attendance::create(['user_id' => $graphics->id, 'date' => now()->toDateString(),           'status' => 'sl']);
        Attendance::create(['user_id' => $content->id,  'date' => now()->subDay()->toDateString(), 'status' => 'absent']);
        Attendance::create(['user_id' => $content->id,  'date' => now()->subWeek()->toDateString(),'status' => 'absent']);  // outside window
        Attendance::create(['user_id' => $admin->id,    'date' => now()->toDateString(),           'status' => 'present']); // manager excluded

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $counts = $response->viewData('attWeekCounts');
        $this->assertSame(1, $counts['present']);
        $this->assertSame(1, $counts['sl']);
        $this->assertSame(1, $counts['absent']);
        $this->assertSame(0, $counts['vl']);

        $outToday = $response->viewData('outToday');
        $this->assertCount(1, $outToday);
        $this->assertTrue($outToday->first()->is($graphics));
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_attendance_week_counts_and_out_today`
Expected: FAIL — `attWeekCounts` view variable is null.

- [ ] **Step 3: Implement in the controller**

**3a.** Add the import at the top of `AdminController.php`:

```php
use App\Models\Attendance;
```

**3b.** In `dashboard()`, after the task-type breakdown block from Task 4 (which defines `$memberRoles`), add:

```php
        // Attendance — current week Mon–Sat
        $weekStart      = now()->startOfWeek();          // Monday
        $weekEnd        = $weekStart->copy()->addDays(5); // Saturday
        $weekAttendance = Attendance::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereHas('user', fn($q) => $q->whereIn('role', $memberRoles))
            ->with('user')
            ->get();

        $attWeekCounts = collect(['present', 'half_day', 'vl', 'sl', 'ut', 'absent'])
            ->mapWithKeys(fn($s) => [$s => $weekAttendance->where('status', $s)->count()]);

        $outToday = $weekAttendance
            ->filter(fn($a) => $a->date->isToday() && in_array($a->status, ['absent', 'vl', 'sl', 'half_day', 'ut']))
            ->map(fn($a) => $a->user)
            ->values();
```

**3c.** Add `'attWeekCounts', 'outToday'` to the `compact(...)` list.

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all)

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/AdminController.php tests/Feature/AdminDashboardTest.php
git commit -m "add attendance week summary data"
```

---

### Task 7: View — Attendance This Week card

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: `$attWeekCounts`, `$outToday` from Task 6; existing `.avatar-tip-wrap` tooltip CSS; `User::avatarUrl()`; route `admin.attendance`.
- Produces: `.att-card` in the bottom-right column, above Quick Actions.

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest`:

```php
    public function test_attendance_card_renders_with_out_today(): void
    {
        $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0)); // Wednesday

        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        Attendance::create(['user_id' => $member->id, 'date' => now()->toDateString(), 'status' => 'vl']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Attendance This Week');
        $response->assertSee('Out today');
    }

    public function test_attendance_card_empty_state(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('No attendance marked yet this week');
        $response->assertDontSee('Out today');
    }
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=test_attendance_card`
Expected: FAIL ×2 — "Attendance This Week" / "No attendance marked yet this week" not found.

- [ ] **Step 3: Add CSS**

In the styles section, where the Top Contributor CSS used to be (before `/* ── Quick Actions ──…*/`), add:

```css
/* ── Attendance This Week ─────────────────────────────── */
.att-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 1.125rem; margin-bottom: 0.875rem; }
.att-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.875rem; }
.att-title { font-size: 0.62rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted-foreground); display: flex; align-items: center; gap: 0.35rem; }
.att-header a { font-size: 0.7rem; font-weight: 600; color: var(--muted-foreground); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
.att-header a:hover { color: var(--foreground); }
.att-chips { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
.att-chip { display: flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.55rem; background: var(--muted); border-radius: 8px; }
.att-chip-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.att-chip-count { font-size: 0.85rem; font-weight: 800; font-family: 'Space Grotesk', sans-serif; }
.att-chip-label { font-size: 0.62rem; font-weight: 600; color: var(--muted-foreground); }
.att-empty { font-size: 0.75rem; color: var(--muted-foreground); text-align: center; padding: 0.75rem 0; }
.att-out { margin-top: 0.875rem; padding-top: 0.75rem; border-top: 1px solid var(--border); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; }
.att-out-label { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); margin-right: 0.25rem; }
.att-out-avatar { width: 26px; height: 26px; border-radius: 50%; display: block; border: 2px solid var(--card); }
```

- [ ] **Step 4: Add the card HTML**

In the bottom-right column `<div>` (where the Top Contributor card used to sit, directly above the `.qa-card`), add:

```blade
            {{-- Attendance This Week --}}
            <div class="att-card">
                <div class="att-header">
                    <span class="att-title"><i class="fas fa-calendar-check" style="color:#10b981;"></i> Attendance This Week</span>
                    <a href="{{ route('admin.attendance') }}">View All <i class="fas fa-arrow-right" style="font-size:0.55rem;"></i></a>
                </div>
                @if($attWeekCounts->sum() === 0)
                <div class="att-empty">No attendance marked yet this week</div>
                @else
                <div class="att-chips">
                    @foreach([
                        'present'  => ['Present',  '#10b981'],
                        'half_day' => ['Half-day', '#f59e0b'],
                        'vl'       => ['VL',       '#0ea5e9'],
                        'sl'       => ['SL',       '#8b5cf6'],
                        'ut'       => ['UT',       '#f97316'],
                        'absent'   => ['Absent',   '#f43f5e'],
                    ] as $key => [$label, $hex])
                    <div class="att-chip">
                        <span class="att-chip-dot" style="background:{{ $hex }};"></span>
                        <span class="att-chip-count">{{ $attWeekCounts[$key] }}</span>
                        <span class="att-chip-label">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                @if(now()->dayOfWeek !== 0 && $outToday->isNotEmpty())
                <div class="att-out">
                    <span class="att-out-label">Out today</span>
                    @foreach($outToday as $u)
                    <span class="avatar-tip-wrap" data-tooltip="{{ $u->first_name }} {{ $u->last_name }}">
                        <img class="att-out-avatar" src="{{ $u->avatarUrl() }}" alt="{{ $u->first_name }}" style="object-fit:cover;">
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all)

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/dashboard.blade.php tests/Feature/AdminDashboardTest.php
git commit -m "add attendance this week card"
```

---

### Task 8: Dark-mode tooltip fix + final verification

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`

**Interfaces:**
- Consumes: `isDark` JS variable.
- Produces: nothing new — polish only.

- [ ] **Step 1: Fix the hard-coded tooltip theme**

In the role-charts JS block (the `roleCharts.forEach(...)` loop), change:

```js
            tooltip: {
                theme: 'light', style: { fontSize: '12px' },
```

to:

```js
            tooltip: {
                theme: isDark ? 'dark' : 'light', style: { fontSize: '12px' },
```

(The trend and task-type charts from Tasks 3/5 already use `isDark`.)

- [ ] **Step 2: Run the full test suite**

Run: `php artisan test`
Expected: PASS — no regressions in `AdminPreviewRoleTest`, `BrandCatalogTest`, `CommandPaletteTest`.

- [ ] **Step 3: Manual verification**

Load the admin dashboard in the browser (light and dark mode) and verify:
- Trend chart renders; 7d/30d toggle switches instantly; Sundays show "(RDO)" in tooltips.
- Task-type pills switch data, labels, and bar color per role.
- Attendance card shows chips (or empty state) and the "Out today" row when someone is out.
- Top Contributor appears only as the amber KPI card.
- Chart tooltips are readable in dark mode.

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/dashboard.blade.php
git commit -m "fix chart tooltip theme in dark mode"
```
