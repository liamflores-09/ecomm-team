# Admin Dashboard Re-layout Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Merge "Today's Pulse" and "Who Logged Today" into one pending-focused card and re-lay the dashboard into a 67/33 analytics-column + people-rail grid.

**Architecture:** View-only refactor of `resources/views/admin/dashboard.blade.php` — **no controller changes**; the merged card consumes data already passed (`$healthPct`, `$healthColor`, `$allMembers`, `$loggedUserIds`, `$todayLogged`, `$todayPending`, `$nonManagerCount`) plus the view-local `$todayLogMap`. Spec: `docs/superpowers/specs/2026-07-02-admin-dashboard-relayout-design.md`.

**Tech Stack:** Laravel 12 Blade, inline per-view CSS with theme variables, PHPUnit feature tests (sqlite `:memory:`).

## Global Constraints

- Commit messages: short, casual, lowercase. **Never add a `Co-Authored-By` line** (user rule).
- Run tests with: `php artisan test --filter=AdminDashboardTest`.
- Do NOT touch `app/Http/Controllers/AdminController.php` — this is a view-only refactor.
- Out of scope (explicit user decision): welcome-banner stats, KPI cards, chart overlap — leave untouched.
- The `@php $todayLogMap = $todayLogs->keyBy('user_id'); @endphp` block at the top of the content section **stays** (merged-card tooltips use it).
- Test helpers already exist in `tests/Feature/AdminDashboardTest.php`: `makeAdmin()`, `makeMember(string $role = 'content')`; tests that depend on weekday state freeze time with `$this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0));` (Wednesday).

---

### Task 1: Merged "Today's Pulse" card, remove "Who Logged Today"

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: view vars `$healthPct`, `$healthColor`, `$todayLogged`, `$todayPending`, `$nonManagerCount`, `$allMembers`, `$loggedUserIds`, `$todayLogMap`; existing CSS `.health-card`, `.health-header`, `.health-bar-wrap`, `.health-bar`, `.health-avatars`, `.health-avatar`, `.avatar-tip-wrap`.
- Produces: the merged card markup (still inside the existing `.dash-2col` row for now — Task 2 moves it into the rail) and new CSS classes `.pulse-group-label`, `.pulse-row`, `.pulse-avatar`, `.pulse-row-meta`, `.pulse-row-name`, `.pulse-row-role`, `.pulse-chip`, `.pulse-all-logged`.

- [ ] **Step 1: Write the failing tests**

Add to `tests/Feature/AdminDashboardTest.php`:

```php
    public function test_merged_pulse_card_renders(): void
    {
        $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0)); // Wednesday

        $admin   = $this->makeAdmin();
        $logged  = $this->makeMember();
        $missing = $this->makeMember('graphics');
        DailyLog::create([
            'user_id' => $logged->id, 'date' => now()->toDateString(),
            'task_1' => 4, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee("Today's Pulse", false);
        $response->assertSee('Logged (1)');
        $response->assertSee('Pending (1)');
        $response->assertSee('4 tasks');
        $response->assertDontSee('wlt-card');
        $response->assertDontSee('Who Logged Today');
    }

    public function test_merged_pulse_card_all_logged(): void
    {
        $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(10, 0)); // Wednesday

        $admin  = $this->makeAdmin();
        $member = $this->makeMember();
        DailyLog::create([
            'user_id' => $member->id, 'date' => now()->toDateString(),
            'task_1' => 2, 'task_2' => 0, 'task_3' => 0, 'task_4' => 0, 'task_5' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('All members logged in');
        $response->assertDontSee('Pending (');
    }
```

(Note: `assertSee("Today's Pulse", false)` needs escaping disabled — the apostrophe would otherwise be compared as `&#039;`.)

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=test_merged_pulse_card`
Expected: FAIL ×2 — `Logged (1)` not found / `Who Logged Today` IS found; `All members logged in` not found.

- [ ] **Step 3: Add the new CSS, remove the dead CSS**

In `@section('styles')`:

**3a.** In the `/* ── Team Health ──…*/` section, DELETE the two `.health-legend` rules (the legend is replaced by group labels):

```css
.health-legend { display: flex; gap: 1.25rem; margin-top: 0.75rem; font-size: 0.72rem; color: var(--muted-foreground); font-weight: 500; }
.health-legend .dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 4px; flex-shrink: 0; }
```

and ADD in their place:

```css
.pulse-group-label { font-size: 0.62rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted-foreground); margin: 0.875rem 0 0.45rem; }
.health-bar-wrap + .pulse-group-label { margin-top: 0; }
.pulse-group-label.rose { color: #f43f5e; }
.pulse-row { display: flex; align-items: center; gap: 0.7rem; padding: 0.4rem 0; border-bottom: 1px solid var(--border); }
.pulse-row:last-child { border-bottom: none; padding-bottom: 0; }
.pulse-avatar { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid var(--destructive); opacity: 0.6; }
.pulse-row-meta { flex: 1; min-width: 0; }
.pulse-row-name { font-size: 0.8rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pulse-row-role { font-size: 0.6rem; color: var(--muted-foreground); font-weight: 500; }
.pulse-chip { font-size: 0.6rem; font-weight: 700; padding: 2px 7px; border-radius: 9999px; background: #fee2e2; color: #b91c1c; flex-shrink: 0; }
.pulse-all-logged { display: flex; align-items: center; gap: 0.45rem; margin-top: 0.875rem; font-size: 0.78rem; font-weight: 600; color: #15803d; }
```

**3b.** DELETE the entire `/* ── Who Logged Today ──…*/` CSS section (all rules from `.wlt-card` through `.wlt-status.pending`, including the section comment).

- [ ] **Step 4: Rework the card markup**

In the content section's `.dash-2col` row:

**4a.** In the Team Health card's header, change the weekday status span from

```blade
                <span style="font-size:0.72rem;font-weight:700;color:{{ $healthColor }};">{{ $healthPct }}% logged in</span>
```

to

```blade
                <span style="font-size:0.72rem;font-weight:700;color:{{ $healthColor }};">{{ $healthPct }}% · {{ $todayLogged }}/{{ $nonManagerCount }} logged</span>
```

**4b.** Leave the Sunday branch (`@if(now()->dayOfWeek === 0) … @else`) untouched. Replace everything in the weekday branch AFTER the `.health-bar-wrap` div (i.e. the avatar-row wrapper div AND the `.health-legend` div) with:

```blade
            <div class="pulse-group-label">Logged ({{ $todayLogged }})</div>
            <div class="health-avatars">
                @foreach($allMembers->filter(fn($m) => in_array($m->id, $loggedUserIds)) as $m)
                @php
                    $ml = $todayLogMap->get($m->id);
                    $mt = $ml ? ($ml->task_1 + $ml->task_2 + $ml->task_3 + $ml->task_4 + $ml->task_5) : null;
                @endphp
                <span class="avatar-tip-wrap" data-tooltip="{{ $m->first_name }} {{ $m->last_name }} · {{ $mt !== null ? $mt . ' tasks' : 'Logged' }}">
                    <img class="health-avatar logged" src="{{ $m->avatarUrl() }}" style="object-fit:cover;" alt="{{ $m->first_name }}">
                </span>
                @endforeach
            </div>

            @if($todayPending > 0)
            <div class="pulse-group-label rose">Pending ({{ $todayPending }})</div>
            @foreach($allMembers->filter(fn($m) => !in_array($m->id, $loggedUserIds)) as $m)
            <div class="pulse-row">
                <img class="pulse-avatar" src="{{ $m->avatarUrl() }}" alt="{{ $m->first_name }}">
                <div class="pulse-row-meta">
                    <div class="pulse-row-name">{{ $m->first_name }} {{ $m->last_name }}</div>
                    <div class="pulse-row-role">{{ ucfirst($m->role) }}</div>
                </div>
                <span class="pulse-chip">Pending</span>
            </div>
            @endforeach
            @else
            <div class="pulse-all-logged"><i class="fas fa-check-circle"></i> All members logged in</div>
            @endif
```

Note: keep the `.health-bar-wrap` div directly before the first group label — the `.health-bar-wrap + .pulse-group-label` adjacent-sibling rule zeroes that label's top margin so the bar's own `margin-bottom: 1rem` provides the spacing.

**4c.** DELETE the entire "Who Logged Today" card (the `{{-- Who Logged Today --}}` comment and the whole `.wlt-card` div).

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all, including both new tests)

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/dashboard.blade.php tests/Feature/AdminDashboardTest.php
git commit -m "merge pulse and who logged into one card"
```

---

### Task 2: Re-layout — analytics column + people rail

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: the merged pulse card from Task 1 (a `.health-card`), plus the existing trend/`insight-card`s, role heading + `.role-ov-grid`, `.activity-card`, `.att-card`, `.qa-card` blocks.
- Produces: `.dash-body` grid (67fr/33fr), `.dash-col` columns, `.dash-rail` modifier; removes `.dash-2col`, `.dash-2col-insights`, `.dash-2col-main` containers.

- [ ] **Step 1: Write the failing test**

Add to `AdminDashboardTest`:

```php
    public function test_relayout_grid_renders(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('dash-body');
        $response->assertSee('dash-rail');
        $response->assertDontSee('dash-2col');
    }
```

(`assertDontSee('dash-2col')` is a substring check, so it also covers `dash-2col-insights` and `dash-2col-main`.)

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_relayout_grid_renders`
Expected: FAIL — `dash-body` not found.

- [ ] **Step 3: CSS changes**

**3a.** Replace the `/* ── Two-col layouts ──…*/` section (both `.dash-2col` and `.dash-2col-main` rules) with:

```css
/* ── Body grid: analytics column + people rail ────────── */
.dash-body { display: grid; grid-template-columns: 67fr 33fr; gap: 1.125rem; align-items: start; }
.dash-col { display: flex; flex-direction: column; gap: 1.125rem; min-width: 0; }
```

**3b.** In the `/* ── Insights row…*/` section, DELETE only the `.dash-2col-insights` rule (keep `.insight-card`, `.insight-header`, `.pill-row`, `.pill` rules — rename the section comment to `/* ── Insight cards (trend + task types) ─── */`).

**3c.** `.role-ov-grid`: change `grid-template-columns: repeat(4, 1fr)` → `repeat(2, 1fr)` and remove its `margin-bottom: 1.25rem` (the column gap handles spacing).

**3d.** `.att-card`: remove `margin-bottom: 0.875rem` (rail gap handles it).

**3e.** Responsive section: DELETE these three lines:

```css
@media (max-width: 1100px) { .role-ov-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 900px)  { .dash-2col, .dash-2col-main { grid-template-columns: 1fr; } }
@media (max-width: 900px)  { .dash-2col-insights { grid-template-columns: 1fr; } }
```

and ADD:

```css
@media (max-width: 900px)  { .dash-body { grid-template-columns: 1fr; } .dash-rail { order: -1; } }
```

(keep the existing `640px` rules, including `.role-ov-grid { grid-template-columns: 1fr; }`).

- [ ] **Step 4: Markup restructure**

In the content section, after the KPI grid's closing `</div>`, restructure everything below into (existing card markup moves verbatim — only the wrappers and anim classes change):

```blade
    {{-- ── Body: analytics column + people rail ── --}}
    <div class="dash-body anim-up d2">

        <div class="dash-col">

            {{-- Team Output Trend --}}
            [the existing trend .insight-card block, unchanged]

            {{-- Task Types — this month --}}
            [the existing task-types .insight-card block, unchanged]

            {{-- Role Activity --}}
            [the existing @php $roleHexColors … @endphp block, unchanged]
            <div>
                [the existing .dash-heading block, with class="dash-heading" (anim-up d4 removed)]
                [the existing .role-ov-grid block, with class="role-ov-grid" (anim-up d4 removed)]
            </div>

            {{-- Recent Activity --}}
            [the existing .activity-card block, unchanged]

        </div>

        <div class="dash-col dash-rail">

            {{-- Today's Pulse (merged) --}}
            [the merged .health-card block from Task 1, unchanged]

            {{-- Attendance This Week --}}
            [the existing .att-card block, unchanged]

            {{-- Quick Actions --}}
            [the existing .qa-card block, unchanged]

        </div>

    </div>
```

Concretely, the wrappers that DISAPPEAR:
- `<div class="dash-2col anim-up d2">…</div>` (the pulse row — its only remaining child, the merged card, moves to the rail)
- `<div class="dash-2col-insights anim-up d3">…</div>` (its two cards move to the top of the left column)
- `<div class="dash-2col-main anim-up d5">…</div>` and the inner plain `<div>` wrapping att-card + qa-card (activity-card → left column; att-card and qa-card → rail)
- The `anim-up d4` classes on `.dash-heading` and `.role-ov-grid` (the heading + grid pair gets one plain `<div>` wrapper so the column gap treats them as one block)

The `.dash-body` wrapper carries the single `anim-up d2` entrance for everything inside it. DOM order in the grid: left column first, rail second (the `order: -1` media rule puts the rail on top on mobile).

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=AdminDashboardTest`
Expected: PASS (all 11 tests — the render tests for trendChart/taskTypeChart/attendance/pulse all still hit the same markup in its new position)

- [ ] **Step 6: Run the full suite**

Run: `php artisan test`
Expected: only the known pre-existing failure `Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response` (GET / expects 200, app redirects to login — unrelated); everything else PASS.

- [ ] **Step 7: Commit**

```bash
git add resources/views/admin/dashboard.blade.php tests/Feature/AdminDashboardTest.php
git commit -m "relayout dashboard into analytics column and people rail"
```

- [ ] **Step 8: Manual verification (human)**

Load the admin dashboard: check light + dark mode, desktop (rail on the right, role cards 2×2) and a narrow window (single column, Pulse/Attendance/Quick Actions on top). Charts still render and toggles work.
