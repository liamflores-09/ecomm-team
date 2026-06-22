# EOD + Dashboard Readability — Design Spec

**Date:** 2026-06-22
**Scope:** Two files — `resources/views/end-of-day.blade.php` and `resources/views/dashboard.blade.php`

---

## Goal

Make the most frequent team member workflow — open app → check EOD status → submit report — as fast and friction-free as possible.

## Design System Constraints

All changes must comply with the existing Revenue-Grade Automation design system:
- No box-shadows — depth via borders and background contrast only
- Border radius: `8px` for cards/inputs, `9999px` for pills, `50%` for circles
- CSS variables only — no hardcoded hex colors except semantic values already defined in `:root`
- Violet accent (`var(--primary)` / `#5757f8`) for primary interactive elements
- Success: `var(--success)` / `#22c55e`
- Font: Space Grotesk for headings, Inter for body

---

## Change 1 — EOD Page Form Layout

**File:** `resources/views/end-of-day.blade.php`

### Remove: info banner

The `.info-banner` block at the top of the content section is removed. New users have the "How to Fill" modal (tutorial modal) for guidance; the banner adds noise for daily users.

### Remove: column guide strip

The `.col-guide` grid (5-column reference strip showing task names and descriptions) is removed. Its purpose is replaced by the form layout itself.

### Replace: form grid layout

The current `form-grid` is a `1fr 1fr` two-column grid. It is replaced with two logical rows:

**Row 1 — Attendance (full width):**
```
[Attendance dropdown — full width or constrained to ~240px max, left-aligned]
```

**Row 2 — 5 task inputs (5-column equal grid):**
```css
grid-template-columns: repeat(5, 1fr);
```
Each column: label above, number input below. Order left-to-right matches the old column guide order: `task_1 | task_2 | task_3 | task_4 | task_5`.

**Row 3 — Remarks (full width):**
Textarea spanning all 5 columns, unchanged in behavior.

**Form actions row** remains at the bottom, right-aligned.

### CSS changes

- Remove `.col-guide`, `.cg-item`, `.cg-name`, `.cg-desc` — these styles are no longer used
- Remove `.info-banner`, `.ib-icon` — no longer used
- Update `.form-grid` to use the new column structure:
  ```css
  .form-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 1rem;
  }
  ```
- Add `.form-group.attendance-row` with `grid-column: 1 / -1`; the select inside gets `max-width: 240px`
- `.form-group.full-width` remains for the remarks textarea
- Mobile breakpoint (`max-width: 768px`): attendance stays full-width, task inputs collapse to `repeat(3, 1fr)`, remarks stays full-width
- Small breakpoint (`max-width: 480px`): task inputs collapse to `1fr 1fr`

### HTML changes

1. Delete the `.info-banner` div block entirely
2. Delete the `.col-guide` div block entirely
3. Inside the form, restructure the inputs:
   - Attendance `<div class="form-group">` wraps in a new `<div class="form-group attendance-row">`; the empty filler `<div class="form-group"></div>` after it is removed
   - The 5 task input groups (`task_1`–`task_5`) are laid out consecutively with no filler divs between them — the 5-column grid handles placement automatically
   - The empty filler `<div class="form-group"></div>` after `task_5` is removed
   - The remarks group keeps `class="form-group full-width"`

---

## Change 2 — Team Member Dashboard EOD Status Strip

**File:** `resources/views/dashboard.blade.php`

### Remove: "Today's EOD" stat card

The third stat card (showing "Done"/"Pending" as the stat count) is removed from the `.stat-grid`. The stat grid becomes 2 cards: **Tasks This Week** and **Tasks This Month**.

### Add: EOD status strip

A full-width status strip is inserted **above** the stat grid (after the success/error flash messages, if any). It is wrapped in a Blade conditional: `@if($todayLog) ... @else ... @endif`. Two states based on whether `$todayLog` is set:

**State A — Pending (no log today):**

```html
<div class="eod-status-strip pending">
    <div class="ess-left">
        <div class="ess-icon"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <div class="ess-title">EOD report not submitted yet</div>
            <div class="ess-sub">{{ now()->format('l, F j') }}</div>
        </div>
    </div>
    <a href="{{ route('end-of-day') }}" class="ess-btn">Submit EOD <i class="fas fa-arrow-right"></i></a>
</div>
```

Visual: card with `border-left: 4px solid var(--primary)`, background `var(--card)`, icon div uses `var(--primary)` background (8px radius, 32px square), title is `var(--foreground)` at `0.9rem`/700 weight, sub-label is `var(--muted-foreground)` at `0.8rem`. Button styled as `.btn-flat-primary`.

**State B — Submitted:**

```html
<div class="eod-status-strip submitted">
    <div class="ess-left">
        <div class="ess-icon submitted"><i class="fas fa-circle-check"></i></div>
        <div class="ess-title">EOD submitted for today</div>
    </div>
    <a href="{{ route('end-of-day') }}" class="ess-edit">Edit <i class="fas fa-pencil"></i></a>
</div>
```

Visual: slim single-line strip, `border-left: 4px solid var(--success)`, icon background `var(--success)`, title `var(--foreground)` at `0.875rem`. Edit link is low-emphasis: `var(--muted-foreground)` text, no button styling.

### CSS for the status strip

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

### Stat grid update

The `.stat-grid` currently has 3 children. With the EOD card removed, it has 2. The grid CSS (defined in the layout or page styles) uses `repeat(auto-fit, minmax(...))` or a fixed `1fr 1fr 1fr`. Verify and adjust so 2 cards fill the row without leaving a gap — `grid-template-columns: repeat(2, 1fr)` or `auto-fit` handles this automatically.

The `$todayLog` variable is already passed to the view by the dashboard controller — no backend changes needed.

---

## What Is Not Changing

- The "How to Fill" tutorial modal on the EOD page — unchanged
- The recent logs table on the EOD page — unchanged
- The weekly chart and quick access sections on the dashboard — unchanged
- Admin dashboard — not in scope
- All other pages — not in scope
