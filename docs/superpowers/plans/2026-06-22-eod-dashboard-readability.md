# EOD + Dashboard Readability Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the daily EOD workflow faster — prominent status strip on the dashboard and a cleaner 5-column form layout on the EOD page.

**Architecture:** Pure Blade/CSS changes in two view files. No controller, model, route, or migration changes. The `$todayLog` variable is already passed to `dashboard.blade.php` by the existing controller.

**Tech Stack:** Laravel 11, Blade templates, Tailwind CSS v4, CSS custom properties (`:root` tokens in `layouts/app.blade.php`).

## Global Constraints

- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards/inputs, `9999px` for pills
- CSS variables only — use `var(--primary)`, `var(--success)`, `var(--card)`, `var(--foreground)`, `var(--muted-foreground)`, `var(--border-light)`, `var(--border)`. No hardcoded hex colors.
- `var(--primary)` = `#5757f8` (violet) — used for pending/action state
- `var(--success)` = `#22c55e` (green) — used for submitted/done state
- Pre-existing test failure: `Tests\Feature\ExampleTest` returns 302 on `GET /` (auth redirect). This predates all our work — it is expected and not a regression.

---

## Files

- Modify: `resources/views/end-of-day.blade.php` — remove info banner + column guide, restructure form to 5-column layout
- Modify: `resources/views/dashboard.blade.php` — add EOD status strip, remove 3rd stat card, update stat-grid to 2 columns

---

### Task 1: EOD Form Layout

**Files:**
- Modify: `resources/views/end-of-day.blade.php`

**Interfaces:**
- Consumes: nothing from other tasks
- Produces: nothing consumed by Task 2

- [ ] **Step 1: Update `.form-grid` CSS and add `.attendance-row`**

In the `@section('styles')` block, find and replace the `.form-grid` rule (around line 48):

```css
/* FIND: */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* REPLACE WITH: */
.form-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}
```

Then, immediately after the `.form-group.full-width` rule (around line 62), add:

```css
.form-group.attendance-row {
    grid-column: 1 / -1;
}
.form-group.attendance-row .form-select {
    max-width: 240px;
}
```

- [ ] **Step 2: Remove the Column Guide CSS block**

Delete the entire "Column guide" comment section — from the comment line through the last closing brace of `.cg-item .cg-desc`. That is this exact block (around lines 152–183):

```css
/* Column guide */
.col-guide {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0;
    margin-bottom: 1.5rem;
    border: 2px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}

.cg-item {
    padding: 0.75rem;
    text-align: center;
    border-right: 2px solid var(--border);
}

.cg-item:last-child { border-right: none; }

.cg-item .cg-name {
    font-weight: 700;
    font-size: 0.7rem;
    color: var(--fg);
    margin-bottom: 0.125rem;
}

.cg-item .cg-desc {
    font-size: 0.6rem;
    color: var(--gray-400);
    font-weight: 500;
}
```

- [ ] **Step 3: Remove the Info Banner CSS block**

Delete the entire "Info banner" comment section — from the comment through `.info-banner p { ... }`. That is this exact block (around lines 247–278):

```css
/* Info banner */
.info-banner {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    background: var(--white);
    border-left: 4px solid var(--fg);
    border-radius: 0 8px 8px 0;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.info-banner .ib-icon {
    width: 32px;
    height: 32px;
    background: var(--fg);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.info-banner p {
    color: var(--gray-700);
    font-weight: 500;
    font-size: 0.85rem;
    line-height: 1.5;
    margin: 0;
}
```

- [ ] **Step 4: Update the mobile media queries**

Find the two media query blocks at the bottom of the styles (around lines 295–307) and replace them entirely:

```css
/* FIND AND REPLACE BOTH BLOCKS: */
@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    .col-guide { grid-template-columns: repeat(3, 1fr); }
    .col-guide .cg-item:nth-child(4),
    .col-guide .cg-item:nth-child(5) { display: none; }
    .logs-table-wrap { overflow-x: auto; }
    .logs-table { min-width: 600px; }
}

@media (max-width: 480px) {
    .col-guide { grid-template-columns: 1fr 1fr; }
    .col-guide .cg-item:nth-child(2n) { border-right: none; }
}

/* REPLACE WITH: */
@media (max-width: 768px) {
    .form-grid { grid-template-columns: repeat(3, 1fr); }
    .logs-table-wrap { overflow-x: auto; }
    .logs-table { min-width: 600px; }
}

@media (max-width: 480px) {
    .form-grid { grid-template-columns: repeat(2, 1fr); }
}
```

- [ ] **Step 5: Remove the Info Banner HTML block**

In `@section('content')`, find and delete this block (around lines 338–341):

```html
<!-- Info Banner -->
<div class="info-banner anim-up d1">
    <div class="ib-icon"><i class="fas fa-circle-info"></i></div>
    <p>Fill in your daily accomplishments below. Each field counts the number of items you completed for the day.</p>
</div>
```

- [ ] **Step 6: Remove the Column Guide HTML block**

Find and delete this entire block (around lines 344–365):

```html
<!-- Column Guide -->
<div class="col-guide anim-up d2">
    <div class="cg-item">
        <div class="cg-name">{{ $taskLabels['task_1'] }}</div>
        <div class="cg-desc">{{ $taskLabels['desc_task_1'] }}</div>
    </div>
    <div class="cg-item">
        <div class="cg-name">{{ $taskLabels['task_2'] }}</div>
        <div class="cg-desc">{{ $taskLabels['desc_task_2'] }}</div>
    </div>
    <div class="cg-item">
        <div class="cg-name">{{ $taskLabels['task_3'] }}</div>
        <div class="cg-desc">{{ $taskLabels['desc_task_3'] }}</div>
    </div>
    <div class="cg-item">
        <div class="cg-name">{{ $taskLabels['task_4'] }}</div>
        <div class="cg-desc">{{ $taskLabels['desc_task_4'] }}</div>
    </div>
    <div class="cg-item">
        <div class="cg-name">{{ $taskLabels['task_5'] }}</div>
        <div class="cg-desc">{{ $taskLabels['desc_task_5'] }}</div>
    </div>
</div>
```

- [ ] **Step 7: Restructure the form inputs**

Find the Log Form card (around line 368) and update its animation delay from `d3` to `d1`:

```html
<!-- FIND: -->
<div class="eod-card anim-up d3">

<!-- REPLACE WITH: -->
<div class="eod-card anim-up d1">
```

Inside the form, replace the entire `<div class="form-grid">` block. Find:

```html
                <div class="form-grid">
                    <!-- Attendance -->
                    <div class="form-group">
                        <label class="form-label">Attendance</label>
                        <select name="attendance" class="form-select">
                            <option value="">— Present —</option>
                            <option value="HD" {{ $existingLog && $existingLog->attendance === 'HD' ? 'selected' : '' }}>Half Day (HD)</option>
                            <option value="VL" {{ $existingLog && $existingLog->attendance === 'VL' ? 'selected' : '' }}>Vacation Leave (VL)</option>
                            <option value="SL" {{ $existingLog && $existingLog->attendance === 'SL' ? 'selected' : '' }}>Sick Leave (SL)</option>
                            <option value="A" {{ $existingLog && $existingLog->attendance === 'A' ? 'selected' : '' }}>Absent (A)</option>
                            <option value="UT" {{ $existingLog && $existingLog->attendance === 'UT' ? 'selected' : '' }}>Unpaid (UT)</option>
                        </select>
                    </div>

                    <div class="form-group"></div>

                    <!-- Col 1 -->
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_1'] }}</label>
                        <input type="number" name="task_1" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_1 : 0 }}" required>
                    </div>

                    <!-- Col 2 -->
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_2'] }}</label>
                        <input type="number" name="task_2" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_2 : 0 }}" required>
                    </div>

                    <!-- Col 3 -->
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_3'] }}</label>
                        <input type="number" name="task_3" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_3 : 0 }}" required>
                    </div>

                    <!-- Col 4 -->
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_4'] }}</label>
                        <input type="number" name="task_4" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_4 : 0 }}" required>
                    </div>

                    <!-- Col 5 -->
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_5'] }}</label>
                        <input type="number" name="task_5" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_5 : 0 }}" required>
                    </div>

                    <div class="form-group"></div>

                    <!-- Remarks -->
                    <div class="form-group full-width">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-textarea" placeholder="e.g. Canva, Change Price: 20, Repost: 5">{{ $existingLog ? $existingLog->remarks : '' }}</textarea>
                    </div>
                </div>
```

Replace with:

```html
                <div class="form-grid">
                    <!-- Attendance -->
                    <div class="form-group attendance-row">
                        <label class="form-label">Attendance</label>
                        <select name="attendance" class="form-select">
                            <option value="">— Present —</option>
                            <option value="HD" {{ $existingLog && $existingLog->attendance === 'HD' ? 'selected' : '' }}>Half Day (HD)</option>
                            <option value="VL" {{ $existingLog && $existingLog->attendance === 'VL' ? 'selected' : '' }}>Vacation Leave (VL)</option>
                            <option value="SL" {{ $existingLog && $existingLog->attendance === 'SL' ? 'selected' : '' }}>Sick Leave (SL)</option>
                            <option value="A" {{ $existingLog && $existingLog->attendance === 'A' ? 'selected' : '' }}>Absent (A)</option>
                            <option value="UT" {{ $existingLog && $existingLog->attendance === 'UT' ? 'selected' : '' }}>Unpaid (UT)</option>
                        </select>
                    </div>

                    <!-- Task counts — 5 columns -->
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_1'] }}</label>
                        <input type="number" name="task_1" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_1 : 0 }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_2'] }}</label>
                        <input type="number" name="task_2" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_2 : 0 }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_3'] }}</label>
                        <input type="number" name="task_3" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_3 : 0 }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_4'] }}</label>
                        <input type="number" name="task_4" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_4 : 0 }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ $taskLabels['task_5'] }}</label>
                        <input type="number" name="task_5" class="form-input" min="0" value="{{ $existingLog ? $existingLog->task_5 : 0 }}" required>
                    </div>

                    <!-- Remarks -->
                    <div class="form-group full-width">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-textarea" placeholder="e.g. Canva, Change Price: 20, Repost: 5">{{ $existingLog ? $existingLog->remarks : '' }}</textarea>
                    </div>
                </div>
```

- [ ] **Step 8: Update the Recent Logs card animation delay**

Find (around line 447):

```html
<div class="eod-card anim-up d4">
```

Replace with:

```html
<div class="eod-card anim-up d2">
```

- [ ] **Step 9: Run tests and verify**

Run:
```bash
php artisan test
```

Expected: same result as before (pre-existing `ExampleTest` 302 failure only — no new failures).

Then open the EOD page in a browser and verify:
- Info banner is gone
- Column guide strip is gone
- Attendance dropdown appears full-width at the top of the form, select width is capped at ~240px
- All 5 task number inputs appear in a single row beneath attendance
- Remarks textarea spans the full width below the 5 inputs
- Form submits successfully

- [ ] **Step 10: Commit**

```bash
git add resources/views/end-of-day.blade.php
git commit -m "ui: 5-column EOD form layout, remove redundant column guide"
```

---

### Task 2: Dashboard EOD Status Strip

**Files:**
- Modify: `resources/views/dashboard.blade.php`

**Interfaces:**
- Consumes: nothing from Task 1
- Produces: nothing — final task

- [ ] **Step 1: Add EOD status strip CSS**

In `@section('styles')`, after the `.stat-label` rule (around line 38), add the following block:

```css
    .eod-status-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: var(--card);
        border: 1px solid var(--border-light);
        border-left: 4px solid var(--primary);
        border-radius: 8px;
        margin-bottom: 1.25rem;
        gap: 1rem;
    }
    .eod-status-strip.submitted {
        border-left-color: var(--success);
        padding: 0.75rem 1.25rem;
    }
    .ess-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .ess-icon {
        width: 32px;
        height: 32px;
        background: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .ess-icon.submitted { background: var(--success); }
    .ess-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--foreground);
    }
    .ess-sub {
        font-size: 0.8rem;
        color: var(--muted-foreground);
        margin-top: 0.125rem;
    }
    .ess-edit {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted-foreground);
        text-decoration: none;
        white-space: nowrap;
    }
    .ess-edit:hover { color: var(--foreground); }
```

- [ ] **Step 2: Update `.stat-grid` to 2 columns**

Find (around line 33):

```css
.stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 0.25rem; }
```

Replace with:

```css
.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 0.25rem; }
```

- [ ] **Step 3: Insert the EOD status strip HTML**

In `@section('content')`, find the `<!-- Stats -->` comment (around line 104) and insert the EOD status strip block **immediately above** it:

```html
    <!-- EOD Status Strip -->
    @if($todayLog)
    <div class="eod-status-strip submitted anim-up">
        <div class="ess-left">
            <div class="ess-icon submitted"><i class="fas fa-circle-check"></i></div>
            <div class="ess-title">EOD submitted for today</div>
        </div>
        <a href="{{ route('end-of-day') }}" class="ess-edit">Edit <i class="fas fa-pencil"></i></a>
    </div>
    @else
    <div class="eod-status-strip anim-up">
        <div class="ess-left">
            <div class="ess-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="ess-title">EOD report not submitted yet</div>
                <div class="ess-sub">{{ now()->format('l, F j') }}</div>
            </div>
        </div>
        <a href="{{ route('end-of-day') }}" class="btn-flat-primary" style="height: 36px; padding: 0 1rem; font-size: 0.85rem; white-space: nowrap;">Submit EOD <i class="fas fa-arrow-right"></i></a>
    </div>
    @endif

```

- [ ] **Step 4: Remove the "Today's EOD" stat card**

Find and delete the 3rd stat card inside `.stat-grid` (around lines 121–127):

```html
            <div class="stat-card">
                <div class="stat-icon" style="background: {{ $todayLog ? 'var(--primary)' : '#991B1B' }};"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <div class="stat-count" style="color: {{ $todayLog ? 'var(--fg)' : '#991B1B' }};">{{ $todayLog ? 'Done' : 'Pending' }}</div>
                    <div class="stat-label">Today's EOD</div>
                </div>
            </div>
```

After deletion, the `.stat-grid` div contains exactly 2 stat cards (Tasks This Week and Tasks This Month).

- [ ] **Step 5: Run tests and verify**

Run:
```bash
php artisan test
```

Expected: same result as before (pre-existing `ExampleTest` 302 failure only — no new failures).

Then open the dashboard in a browser and verify both states:

**Pending state** (if no EOD submitted today): violet left-border strip with clipboard icon, "EOD report not submitted yet", today's date sub-label, violet "Submit EOD →" button. Clicking the button navigates to the EOD page.

**Submitted state** (if EOD already submitted): slim green left-border strip with checkmark icon, "EOD submitted for today", low-emphasis "Edit" link. No prominent button.

Confirm the stat grid shows 2 cards (Tasks This Week, Tasks This Month) in a 2-column row with no gap.

- [ ] **Step 6: Commit**

```bash
git add resources/views/dashboard.blade.php
git commit -m "ui: EOD status strip on dashboard, 2-col stat grid"
```
