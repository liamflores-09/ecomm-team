# Admin Dashboard Re-layout + Pulse/Who-Logged Merge — Design

**Date:** 2026-07-02
**Status:** Approved

## Goal

Remove the redundancy between "Today's Pulse" and "Who Logged Today" (same members, same status, shown side by side) by merging them into one pending-focused card, and re-lay the page into an analytics column + people rail so it reads better.

## Scope

View-only refactor of `resources/views/admin/dashboard.blade.php` (markup, CSS, anim classes) plus test adjustments. **No controller changes** — the merged card consumes data `AdminController::dashboard()` already passes: `$healthPct`, `$healthColor`, `$allMembers`, `$loggedUserIds`, `$todayLogMap` (built in the view's `@php` block), `$todayLogged`, `$todayPending`, `$nonManagerCount`.

Out of scope (explicit user decision): welcome-banner stats, KPI cards, chart overlap — all stay as-is.

## New page structure

Unchanged on top: welcome banner, KPI row (4 cards).

Below the KPI row, a two-column grid replaces the current row stack (user-selected layout "A — right rail"):

```
.dash-body  (grid: 67fr 33fr, gap 1.125rem, align-items start)
├── left column (analytics)          ├── right rail (people)
│   1. Team Output Trend (full w)    │   1. Today's Pulse (merged card)
│   2. Task Types (full w)           │   2. Attendance This Week
│   3. Role Activity heading + 2×2   │   3. Quick Actions
│   4. Recent Activity               │
```

- The old `.dash-2col` (Pulse + Who Logged), `.dash-2col-insights` (60/40), and `.dash-2col-main` (60/40 bottom) rows dissolve; their cards become full-width blocks inside their column. Each column is a flex column with `gap: 1.125rem` (drop the cards' bottom margins inside it).
- `.role-ov-grid` becomes `repeat(2, 1fr)` (2×2) inside the left column — cards get wider than today's 4-across.
- **Responsive:** at `max-width: 900px` the grid collapses to one column with the **rail first** (rail is second in DOM; swap with `order: -1` on the rail in the media query). Existing narrower breakpoints inside cards stay.
- **Entrance animation:** `.dash-body` gets `anim-up d2`; remove the per-row `d3`/`d4`/`d5` classes from the sections that move inside it (single entrance for the grid). Banner and KPI row keep `anim-up` / `d1`.

## Merged "Today's Pulse" card (user-selected form "B — pending-focused")

Replaces both the current `.health-card` and the `.wlt-card`. Lives at the top of the right rail. Structure:

1. **Header** (reuses current health-header pattern): pulse dot + "Today's Pulse"; right side `{{ $healthPct }}% · {{ $todayLogged }}/{{ $nonManagerCount }} logged` in `$healthColor`.
2. **Health bar** — unchanged markup (`.health-bar-wrap`/`.health-bar`).
3. **"Logged (n)" group label**, then the green avatar stack — reuses `.health-avatars`/`.avatar-tip-wrap`/`.health-avatar.logged`; tooltip text becomes `Name · N tasks` (task count from `$todayLogMap`, falling back to `Logged` if the log rows sum is unavailable). Only logged members appear in the stack.
4. **"Pending (n)" group label** (rose-colored), then one full row per pending member: avatar (`.pulse-avatar`, rose ring, dimmed), name + role, red "Pending" chip. New classes `.pulse-group-label`, `.pulse-row`, `.pulse-row-name`, `.pulse-row-role`, `.pulse-chip` styled like the removed `.wlt-*` equivalents.
5. **States:**
   - All logged (`$todayPending === 0`): pending section replaced by a single muted line "All members logged in ✓".
   - Sunday (RDO): keep the current health-card Sunday treatment — greyed/desaturated avatar stack + "The team is on Rest Day…" note; no bar, no logged/pending groups (unchanged logic, moved into this card).

## Removals

- The entire "Who Logged Today" card markup and all `.wlt-*` CSS rules.
- The `.dash-2col`, `.dash-2col-insights`, `.dash-2col-main` CSS (replaced by `.dash-body` + column styles) and their media-query lines.
- The view's `$todayLogMap` `@php` block **stays** (tooltips use it).

## Testing

Existing `AdminDashboardTest` tests keep passing (they assert content markers — `trendChart`, `taskTypeChart`, attendance strings, "Top This Month" count — not layout). Adjustments:

- New test `test_merged_pulse_card_renders`: freeze time to a Wednesday (`travelTo`, same pattern as the attendance tests — avoids the Sunday RDO branch), one logged member + one not-logged member, assert response contains `Today's Pulse`, `Pending (1)`, and `assertDontSee('wlt-card')` / `assertDontSee('Who Logged Today')`.
- New test `test_merged_pulse_card_all_logged`: Wednesday freeze, single member with a log today, assert `All members logged in`.

Manual check after implementation: light + dark mode, desktop + narrow viewport (rail-first collapse), a Sunday preview if feasible.

## Error handling / edge cases

- Empty roster (`$allMembers` empty): bar shows 0%, both groups render with (0) — acceptable for an internal tool.
- Member logged with 0 tasks: appears in logged stack; tooltip shows `· 0 tasks`.
