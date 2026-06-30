# Attendance Page — Design Spec
Date: 2026-07-01

## Overview
An admin-only monthly attendance roster where the admin logs attendance for all team members. The admin sees a grid (users × days) and clicks cells to set status. Holidays can be applied to all users for a given day in one click.

---

## Data

### New `attendance` table
| Column     | Type         | Notes                          |
|------------|--------------|--------------------------------|
| id         | bigint PK    |                                |
| user_id    | bigint FK    | → users, cascade delete        |
| date       | date         |                                |
| status     | enum         | see statuses below             |
| created_at | timestamp    |                                |
| updated_at | timestamp    |                                |

Unique constraint on `(user_id, date)` — safe to upsert.

### Statuses
| Value      | Label           | Chip | Color  |
|------------|-----------------|------|--------|
| present    | Present         | P    | green  |
| half_day   | Half Day        | HD   | amber  |
| vl         | Vacation Leave  | VL   | sky    |
| sl         | Sick Leave      | SL   | orange |
| absent     | Absent          | A    | rose   |
| ut         | Undertime       | UT   | purple |
| holiday    | Holiday         | H    | indigo |

### Migration changes
- Create `attendance` table (new migration)
- Drop `attendance` column from `daily_logs` (new migration)

---

## Users in scope
All roles **except** `manager`, `head`, `analyst`. Rows are grouped by role on the grid.

---

## UI — Monthly Grid

- **Month switcher** at the top: prev/next arrows + "July 2026" label. Navigating reloads the page with a `?month=YYYY-MM` query param.
- **Rows** = users, grouped by role section headers.
- **Columns** = days 1–31 (only days valid for the selected month are shown).
- **Cells** = colored chip showing status abbreviation, or empty if not yet logged.
- **Cell click** = opens a small inline dropdown with 7 status options + "Clear". Selecting an option fires an AJAX POST to `upsert`, updates the cell immediately on success.
- **Column header** = day number + a small flag icon button. Clicking the flag marks all in-scope users as `holiday` for that date (fires `markHoliday`), updates all cells in that column.

---

## Routes

All under existing `admin` middleware (`auth` + `isAdmin`):

```
GET  /admin/attendance          AdminAttendanceController@index
POST /admin/attendance          AdminAttendanceController@upsert
POST /admin/attendance/holiday  AdminAttendanceController@markHoliday
```

---

## Controller — `AdminAttendanceController`

### `index()`
- Reads `?month=YYYY-MM` query param (defaults to current month).
- Loads all in-scope users grouped by role.
- Loads all `attendance` records for the month, keyed as `[user_id][date]` and passes as JSON to the view.
- Returns `admin.attendance` view.

### `upsert(Request $request)`
- Validates: `user_id` (exists in users), `date` (valid date in Y-m-d), `status` (nullable, in enum list).
- If `status` is null/empty → deletes the record (clear).
- Otherwise → `updateOrCreate(['user_id', 'date'], ['status'])`.
- Returns JSON `{ success: true }`.

### `markHoliday(Request $request)`
- Validates: `date` (valid date).
- Upserts `status = holiday` for all in-scope users for that date.
- Returns JSON `{ success: true, count: N }`.

---

## Model — `Attendance`

```php
fillable: [user_id, date, status]
casts: [date => 'date']
belongs to User
```

---

## Sidebar
Add "Attendance" link in the admin sidebar section, active when route is `admin.attendance`.

---

## Out of scope
- Employees cannot view or edit their own attendance.
- No notifications on attendance changes.
- No export/report for attendance (can be added later).
