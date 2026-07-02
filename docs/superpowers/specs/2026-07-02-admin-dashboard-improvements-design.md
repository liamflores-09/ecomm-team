# Admin Dashboard Improvements — Design

**Date:** 2026-07-02
**Status:** Approved

## Goal

Add three new insight sections to the admin dashboard (team trend chart, task-type breakdown with role filter, attendance week summary), consolidate redundant content, and apply small visual-polish fixes — while keeping the existing look and architecture.

## Approach (decided)

Everything server-rendered in `AdminController::dashboard()`, all datasets passed to the view as `json_encode`d JSON, interactivity (role filter, 7d/30d toggle) handled client-side by swapping ApexCharts data. No new routes, no AJAX. This matches how the dashboard already works.

## Changes

### 1. Consolidation

Top Contributor currently appears in three places. Keep only the amber **KPI card**:

- Remove the standalone Top Contributor card (`.tc-card`) from the bottom-right column.
- Remove the "Top This Month" stat (and its divider) from the welcome banner.

This frees the bottom-right column for the new Attendance card.

### 2. Team Trend chart (new)

- Area chart of **total team tasks per day**, last 30 days, in a card with the standard header style.
- **7d / 30d toggle pills** in the card header — client-side slice of the same dataset, chart updates via `chart.updateOptions`/`updateSeries`, no reload. Default: 30d.
- Sundays get red x-axis labels and "(RDO)" tooltip suffix, same pattern as the existing role charts.
- Indigo (`#6366f1`) stroke + gradient fill, matching the existing sparkline.

**Data:** extend the existing `$dailyTotals` query from 7 days to 30 days — one query then serves both the trend chart and the sparkline (which takes the last-7-days slice). The per-role 7-day charts keep their own existing `$roleWeeklyRaw` query, unchanged. Zero-fill missing days. Pass `trendLabels` (e.g. "Jun 03"), `trendData`, `trendSundayIndices`.

### 3. Task-Type Breakdown with role filter (new)

- Horizontal bar chart of **this month's totals for task_1–task_5**, one card, placed beside the Team Trend chart (new two-column row, roughly 60/40).
- **Role filter pills** (Content / Graphics / Backend / Researcher) in the card header. Clicking a pill swaps both the series **and the category labels**, since task_1–task_5 mean different things per role. Default: Content.
- Bar color = the role's existing hex (`content #0ea5e9`, `graphics #f59e0b`, `backend #f43f5e`, `researcher #10b981`).
- Empty state: if a role has no logs this month, show the chart with zeros (labels still visible) — no special empty card.

**Data:** one grouped query — this month's `SUM(task_1)…SUM(task_5)` joined to `users`, grouped by `users.role`, limited to the four member roles. Labels via `TaskLabels::get($role)` per role. Shape passed to JS:

```json
{
  "content":   { "labels": ["New SKU", "...x5"], "data": [12, 4, 9, 3, 1] },
  "graphics":  { "labels": [...], "data": [...] },
  "backend":   { "labels": [...], "data": [...] },
  "researcher":{ "labels": [...], "data": [...] }
}
```

### 4. Attendance This Week (new)

- Compact card in the bottom-right column (above Quick Actions, where Top Contributor was).
- Header: "Attendance This Week" + link to `admin.attendance`.
- Body:
  - **Status chips** — per-status counts for the current week (Mon–Sat): Present, Half-day, VL, SL, UT, Absent. Small colored stat chips (count + label). Holiday rows are excluded from chips (not a per-person status worth counting here); if all six counts are zero show a muted "No attendance marked yet this week" empty state.
  - **"Out today" row** — avatars + names of members whose attendance today is anything other than `present` (absent / vl / sl / half_day / ut). Hidden on Sundays and when nobody is out.

**Data:** one query — `Attendance` records for the four member roles, current week `Mon..Sat`, eager user. Derive counts + today's non-present list in the controller.

**Status colors:** present `#10b981`, half_day `#f59e0b`, vl `#0ea5e9`, sl `#8b5cf6`, ut `#f97316`, absent `#f43f5e`.

### 5. Visual polish

- New cards reuse existing patterns: `dash-heading`-style headers, `anim-up` staggered entrances (extend delays d5/d6 if needed).
- Fix hard-coded `theme: 'light'` in ApexCharts tooltips — use the already-computed `isDark` variable for all charts (existing + new).
- Filter/toggle pills: shared small pill style (muted background, active = filled with role/indigo color), used by both the role filter and the 7d/30d toggle.

## Resulting layout (top → bottom)

1. Welcome banner (trimmed)
2. KPI row (4 cards, unchanged)
3. Team Pulse + Who Logged Today (unchanged)
4. **Team Trend (30d) + Task-Type Breakdown** ← new row
5. Role Activity — Last 7 Days (unchanged)
6. Recent Activity + (**Attendance This Week** + Quick Actions)

## Error handling / edge cases

- Sunday (RDO): trend chart and attendance card render normally; "Out today" row hidden.
- Role with no logs this month: breakdown shows zeroed bars with labels.
- No attendance rows this week: muted empty state in the card.
- Missing days in the 30-day window: zero-filled.

## Testing

No view/controller test suite exists for the dashboard; verify by loading the page (light + dark mode) and exercising the role filter and 7d/30d toggle. Controller changes are plain aggregations over existing tables.

## Out of scope

- Leaderboard (declined), live refresh, date-range pickers beyond 7d/30d, changes to other pages.
