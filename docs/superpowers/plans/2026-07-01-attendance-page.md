# Attendance Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build an admin-only monthly attendance roster grid where the admin logs attendance per user per day, with a one-click Holiday button per day column.

**Architecture:** New `attendance` table (separate from `daily_logs`), an `Attendance` model, `AdminAttendanceController` with 3 actions (index/upsert/markHoliday), and a full-page blade grid view. All cell updates are AJAX — no page reload needed per cell.

**Tech Stack:** Laravel 11, Blade, vanilla JS (fetch API), CSS grid/table, Font Awesome icons.

## Global Constraints

- Admin middleware group already exists in `routes/web.php` at prefix `/admin`
- Roles excluded from attendance: `manager`, `head`, `analyst`
- Status enum values (exact strings): `present`, `half_day`, `vl`, `sl`, `absent`, `ut`, `holiday`
- Follow existing controller patterns (see `AdminBrandController.php`)
- Follow existing sidebar patterns (see `resources/views/components/sidebar.blade.php`)
- Views extend `layouts.app`, use `@section('content')`, `@section('styles')`, `@section('scripts')`
- CSRF token available via `{{ csrf_token() }}` in blade

---

### Task 1: Migrations + Models

**Files:**
- Create: `database/migrations/2026_07_01_000001_create_attendance_table.php`
- Create: `database/migrations/2026_07_01_000002_drop_attendance_from_daily_logs.php`
- Create: `app/Models/Attendance.php`
- Modify: `app/Models/DailyLog.php` — remove `'attendance'` from `$fillable`

**Interfaces:**
- Produces: `Attendance` model with `updateOrCreate(['user_id', 'date'], ['status'])` available; `DailyLog::$fillable` no longer contains `'attendance'`

- [ ] **Step 1: Create the attendance table migration**

Create `database/migrations/2026_07_01_000001_create_attendance_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'half_day', 'vl', 'sl', 'absent', 'ut', 'holiday']);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
```

- [ ] **Step 2: Create the drop-attendance-column migration**

Create `database/migrations/2026_07_01_000002_drop_attendance_from_daily_logs.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn('attendance');
        });
    }

    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->string('attendance', 10)->nullable()->after('date');
        });
    }
};
```

- [ ] **Step 3: Run migrations**

```bash
php artisan migrate
```

Expected output: two migrations run successfully, no errors.

- [ ] **Step 4: Create the Attendance model**

Create `app/Models/Attendance.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 5: Remove `attendance` from DailyLog fillable**

In `app/Models/DailyLog.php`, change:

```php
    protected $fillable = [
        'user_id',
        'date',
        'attendance',
        'task_1',
        'task_2',
        'task_3',
        'task_4',
        'task_5',
        'remarks',
    ];
```

To:

```php
    protected $fillable = [
        'user_id',
        'date',
        'task_1',
        'task_2',
        'task_3',
        'task_4',
        'task_5',
        'remarks',
    ];
```

- [ ] **Step 6: Verify manually**

Run `php artisan tinker` and confirm:
```php
\App\Models\Attendance::count(); // returns 0, no error
\Schema::hasColumn('daily_logs', 'attendance'); // returns false
```

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_07_01_000001_create_attendance_table.php
git add database/migrations/2026_07_01_000002_drop_attendance_from_daily_logs.php
git add app/Models/Attendance.php
git add app/Models/DailyLog.php
git commit -m "add attendance table and model, drop attendance from daily_logs"
```

---

### Task 2: Controller + Routes

**Files:**
- Create: `app/Http/Controllers/AdminAttendanceController.php`
- Modify: `routes/web.php` — add 3 routes inside admin middleware group

**Interfaces:**
- Consumes: `App\Models\Attendance`, `App\Models\User`
- Produces:
  - `GET /admin/attendance` → renders `admin.attendance` view with `$month`, `$prevMonth`, `$nextMonth`, `$daysInMonth`, `$usersByRole`, `$attendanceJson`
  - `POST /admin/attendance` → JSON `{ "success": true }`
  - `POST /admin/attendance/holiday` → JSON `{ "success": true, "count": N }`

- [ ] **Step 1: Create AdminAttendanceController**

Create `app/Http/Controllers/AdminAttendanceController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    private const EXCLUDED_ROLES = ['manager', 'head', 'analyst'];
    private const VALID_STATUSES = ['present', 'half_day', 'vl', 'sl', 'absent', 'ut', 'holiday'];

    public function index()
    {
        $month = Carbon::parse(request()->query('month', now()->format('Y-m')) . '-01');

        $users = User::whereNotIn('role', self::EXCLUDED_ROLES)
            ->orderBy('role')
            ->orderBy('first_name')
            ->get();

        $usersByRole = $users->groupBy('role');

        $records = Attendance::whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->whereIn('user_id', $users->pluck('id'))
            ->get();

        $attendanceJson = [];
        foreach ($records as $record) {
            $attendanceJson[$record->user_id][$record->date->format('Y-m-d')] = $record->status;
        }

        return view('admin.attendance', [
            'month'          => $month,
            'prevMonth'      => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth'      => $month->copy()->addMonth()->format('Y-m'),
            'daysInMonth'    => $month->daysInMonth,
            'usersByRole'    => $usersByRole,
            'attendanceJson' => $attendanceJson,
        ]);
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date_format:Y-m-d',
            'status'  => 'nullable|in:' . implode(',', self::VALID_STATUSES),
        ]);

        if (empty($validated['status'])) {
            Attendance::where('user_id', $validated['user_id'])
                ->where('date', $validated['date'])
                ->delete();
        } else {
            Attendance::updateOrCreate(
                ['user_id' => $validated['user_id'], 'date' => $validated['date']],
                ['status'  => $validated['status']]
            );
        }

        return response()->json(['success' => true]);
    }

    public function markHoliday(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $userIds = User::whereNotIn('role', self::EXCLUDED_ROLES)->pluck('id');

        foreach ($userIds as $userId) {
            Attendance::updateOrCreate(
                ['user_id' => $userId, 'date' => $validated['date']],
                ['status'  => 'holiday']
            );
        }

        return response()->json(['success' => true, 'count' => $userIds->count()]);
    }
}
```

- [ ] **Step 2: Register routes**

In `routes/web.php`, inside the `Route::middleware(['admin'])->prefix('admin')->group(function () {` block, add after the last existing admin route:

```php
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
        Route::post('/attendance', [AdminAttendanceController::class, 'upsert'])->name('admin.attendance.upsert');
        Route::post('/attendance/holiday', [AdminAttendanceController::class, 'markHoliday'])->name('admin.attendance.holiday');
```

Also add the import at the top of `routes/web.php` with the other use statements:

```php
use App\Http\Controllers\AdminAttendanceController;
```

- [ ] **Step 3: Verify routes registered**

```bash
php artisan route:list --path=admin/attendance
```

Expected: 3 routes listed — GET, POST (upsert), POST (holiday).

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/AdminAttendanceController.php routes/web.php
git commit -m "add AdminAttendanceController and attendance routes"
```

---

### Task 3: Attendance Grid View

**Files:**
- Create: `resources/views/admin/attendance.blade.php`

**Interfaces:**
- Consumes from controller: `$month` (Carbon), `$prevMonth` (string Y-m), `$nextMonth` (string Y-m), `$daysInMonth` (int), `$usersByRole` (Collection grouped by role), `$attendanceJson` (array `[userId][date] => status`)

- [ ] **Step 1: Create the blade view**

Create `resources/views/admin/attendance.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Attendance — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='4' width='18' height='18' rx='2'/><line x1='16' y1='2' x2='16' y2='6'/><line x1='8' y1='2' x2='8' y2='6'/><line x1='3' y1='10' x2='21' y2='10'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Layout ──────────────────────────────────────────────────── */
    .att-header { margin-bottom: 1.5rem; }
    .att-header h2 { font-size: 1.5rem; font-weight: 800; margin: 0 0 0.2rem; }
    .att-header p  { font-size: 0.88rem; color: var(--muted-foreground); font-weight: 500; margin: 0; }

    /* ── Month switcher ──────────────────────────────────────────── */
    .att-month-bar {
        display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;
        padding: 0.75rem 1rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
    }
    .att-month-btn {
        display: flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid var(--border); background: transparent;
        color: var(--muted-foreground); text-decoration: none; font-size: 0.85rem;
        transition: all 0.15s;
    }
    .att-month-btn:hover { background: var(--muted); color: var(--fg); }
    .att-month-label { font-size: 1rem; font-weight: 800; }

    /* ── Scroll wrapper ──────────────────────────────────────────── */
    .att-scroll { overflow-x: auto; border-radius: 8px; border: 1px solid var(--border); }

    /* ── Table ───────────────────────────────────────────────────── */
    .att-table { border-collapse: collapse; width: 100%; min-width: max-content; }
    .att-table th, .att-table td {
        border: 1px solid var(--border);
        padding: 0; white-space: nowrap;
    }

    /* Sticky name column */
    .att-name-col {
        position: sticky; left: 0; z-index: 2;
        background: var(--card);
        min-width: 160px; max-width: 200px;
        padding: 0.5rem 0.75rem;
        font-size: 0.78rem; font-weight: 600;
    }
    .att-table thead .att-name-col { z-index: 3; }

    /* Day header */
    .att-day-th {
        text-align: center; vertical-align: top;
        min-width: 48px; padding: 0.25rem 0.2rem 0.2rem;
        background: var(--card);
    }
    .att-day-num { font-size: 0.7rem; font-weight: 700; color: var(--muted-foreground); line-height: 1; margin-bottom: 2px; }
    .att-holiday-btn {
        display: block; margin: 0 auto;
        border: none; background: transparent; cursor: pointer;
        color: var(--muted-foreground); font-size: 0.6rem; padding: 2px 4px;
        border-radius: 3px; line-height: 1; transition: all 0.15s;
    }
    .att-holiday-btn:hover { color: var(--indigo); background: rgba(99,102,241,0.1); }

    /* Role section row */
    .att-role-row td {
        background: var(--muted); padding: 0.3rem 0.75rem;
        font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--muted-foreground);
    }

    /* Data cells */
    .att-cell {
        display: flex; align-items: center; justify-content: center;
        min-height: 36px; cursor: pointer; padding: 4px;
        transition: background 0.12s;
    }
    .att-cell:hover { background: var(--muted); }

    /* Status chips */
    .att-chip {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 2px 5px; border-radius: 4px;
        font-size: 0.6rem; font-weight: 800; letter-spacing: 0.03em;
        color: #fff; line-height: 1;
    }
    .att-chip.present  { background: #10b981; }
    .att-chip.half_day { background: #f59e0b; }
    .att-chip.vl       { background: #0ea5e9; }
    .att-chip.sl       { background: #f97316; }
    .att-chip.absent   { background: #f43f5e; }
    .att-chip.ut       { background: #a855f7; }
    .att-chip.holiday  { background: #6366f1; }

    /* ── Dropdown ────────────────────────────────────────────────── */
    .att-dropdown {
        position: fixed; z-index: 9999;
        background: var(--card); border: 1px solid var(--border);
        border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        padding: 4px; min-width: 160px;
    }
    .att-dd-item {
        display: flex; align-items: center; gap: 8px;
        width: 100%; padding: 6px 10px; border: none;
        background: transparent; cursor: pointer; border-radius: 5px;
        font-size: 0.78rem; font-weight: 600; color: var(--fg);
        font-family: var(--p-font-family-sans); text-align: left;
        transition: background 0.1s;
    }
    .att-dd-item:hover { background: var(--muted); }
    .att-dd-sep { height: 1px; background: var(--border); margin: 3px 0; }
    .att-dd-clear { color: var(--muted-foreground); }
</style>
@endsection

@section('content')
<x-sidebar active="admin.attendance" :isAdmin="true" />

<div class="main-content">

    <div class="att-header anim-up">
        <h2>Attendance</h2>
        <p>Monthly attendance roster — log attendance for all team members.</p>
    </div>

    {{-- Month switcher --}}
    <div class="att-month-bar anim-up d1">
        <a href="{{ route('admin.attendance', ['month' => $prevMonth]) }}" class="att-month-btn">
            <i class="fas fa-chevron-left"></i>
        </a>
        <span class="att-month-label">{{ $month->format('F Y') }}</span>
        <a href="{{ route('admin.attendance', ['month' => $nextMonth]) }}" class="att-month-btn">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>

    {{-- Grid --}}
    <div class="att-scroll anim-up d2">
        <table class="att-table">
            <thead>
                <tr>
                    <th class="att-name-col">Member</th>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                    @php $dateStr = $month->format('Y-m') . '-' . str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                    <th class="att-day-th">
                        <div class="att-day-num">{{ $d }}</div>
                        <button class="att-holiday-btn" title="Mark all Holiday" onclick="markHoliday('{{ $dateStr }}')">
                            <i class="fas fa-flag"></i>
                        </button>
                    </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($usersByRole as $role => $users)
                <tr class="att-role-row">
                    <td colspan="{{ $daysInMonth + 1 }}">{{ ucfirst($role) }}</td>
                </tr>
                @foreach ($users as $u)
                <tr>
                    <td class="att-name-col">{{ $u->first_name }} {{ $u->last_name }}</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                    @php $dateStr = $month->format('Y-m') . '-' . str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                    <td>
                        <div class="att-cell"
                             data-uid="{{ $u->id }}"
                             data-date="{{ $dateStr }}"
                             onclick="openDropdown(this)">
                            @php $status = $attendanceJson[$u->id][$dateStr] ?? null; @endphp
                            @if ($status)
                            <span class="att-chip {{ $status }}">{{ strtoupper(str_replace('_', '', ['present'=>'P','half_day'=>'HD','vl'=>'VL','sl'=>'SL','absent'=>'A','ut'=>'UT','holiday'=>'H'][$status] ?? $status)) }}</span>
                            @endif
                        </div>
                    </td>
                    @endfor
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Dropdown --}}
<div id="attDropdown" class="att-dropdown" style="display:none;">
    <button class="att-dd-item" data-status="present">
        <span class="att-chip present">P</span> Present
    </button>
    <button class="att-dd-item" data-status="half_day">
        <span class="att-chip half_day">HD</span> Half Day
    </button>
    <button class="att-dd-item" data-status="vl">
        <span class="att-chip vl">VL</span> Vacation Leave
    </button>
    <button class="att-dd-item" data-status="sl">
        <span class="att-chip sl">SL</span> Sick Leave
    </button>
    <button class="att-dd-item" data-status="absent">
        <span class="att-chip absent">A</span> Absent
    </button>
    <button class="att-dd-item" data-status="ut">
        <span class="att-chip ut">UT</span> Undertime
    </button>
    <button class="att-dd-item" data-status="holiday">
        <span class="att-chip holiday">H</span> Holiday
    </button>
    <div class="att-dd-sep"></div>
    <button class="att-dd-item att-dd-clear" data-status="">
        <i class="fas fa-xmark" style="width:18px;text-align:center;"></i> Clear
    </button>
</div>
@endsection

@section('scripts')
<script>
var CSRF = '{{ csrf_token() }}';
var CHIP_MAP = { present:'P', half_day:'HD', vl:'VL', sl:'SL', absent:'A', ut:'UT', holiday:'H' };
var activeCell = null;
var dropdown   = document.getElementById('attDropdown');

function openDropdown(cell) {
    activeCell = cell;
    var rect = cell.getBoundingClientRect();
    dropdown.style.display = 'block';
    var top  = rect.bottom + window.scrollY + 4;
    var left = rect.left + window.scrollX;
    // Keep dropdown within viewport
    if (left + 170 > window.innerWidth) left = window.innerWidth - 174;
    dropdown.style.top  = top + 'px';
    dropdown.style.left = left + 'px';
}

function closeDropdown() {
    dropdown.style.display = 'none';
    activeCell = null;
}

dropdown.querySelectorAll('.att-dd-item').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (!activeCell) return;
        var status = this.dataset.status;
        var uid    = activeCell.dataset.uid;
        var date   = activeCell.dataset.date;
        closeDropdown();
        saveStatus(uid, date, status, activeCell);
    });
});

document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) closeDropdown();
});

function saveStatus(uid, date, status, cell) {
    fetch('{{ route("admin.attendance.upsert") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ user_id: uid, date: date, status: status || null }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) updateCell(cell, status);
    });
}

function updateCell(cell, status) {
    cell.innerHTML = '';
    if (status) {
        var chip = document.createElement('span');
        chip.className = 'att-chip ' + status;
        chip.textContent = CHIP_MAP[status] || status;
        cell.appendChild(chip);
    }
}

function markHoliday(date) {
    fetch('{{ route("admin.attendance.holiday") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ date: date }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.querySelectorAll('.att-cell[data-date="' + date + '"]').forEach(function(cell) {
                updateCell(cell, 'holiday');
            });
        }
    });
}
</script>
@endsection
```

- [ ] **Step 2: Visit the page and verify the grid renders**

With the dev server running, go to `/admin/attendance`. Confirm:
- Month label shows current month
- Prev/next arrows navigate months
- User rows grouped by role
- Day columns 1 through N (correct count for the month)
- Flag buttons appear in each column header

- [ ] **Step 3: Test cell click**

Click any cell — the dropdown should appear near the cell with all 7 status options + Clear.
Select a status — the cell should immediately update with a colored chip, no page reload.
Select Clear on the same cell — chip disappears.

- [ ] **Step 4: Test Holiday button**

Click a flag icon in a day column header — all cells in that column should instantly show indigo `H` chips.

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/attendance.blade.php
git commit -m "add attendance grid view"
```

---

### Task 4: Sidebar Link

**Files:**
- Modify: `resources/views/components/sidebar.blade.php`

**Interfaces:**
- Consumes: route `admin.attendance`
- Produces: "Attendance" link visible in admin sidebar under Analytics section, active when `$active === 'admin.attendance'`

- [ ] **Step 1: Add Attendance link to sidebar**

In `resources/views/components/sidebar.blade.php`, add after the Daily Logs line (inside the `@if($isAdmin)` block, under the Analytics section):

```blade
            <li><a href="{{ route('admin.attendance') }}" class="{{ $active === 'admin.attendance' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Attendance</a></li>
```

So the Analytics section becomes:

```blade
            {{-- ── Analytics ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Analytics</li>
            <li><a href="{{ route('admin.daily-logs') }}" class="{{ $active === 'admin.daily-logs' ? 'active' : '' }}"><i class="fas fa-clock-rotate-left"></i> Daily Logs</a></li>
            <li><a href="{{ route('admin.reports') }}"    class="{{ $active === 'admin.reports'    ? 'active' : '' }}"><i class="fas fa-chart-column"></i> Reports</a></li>
            <li><a href="{{ route('admin.attendance') }}" class="{{ $active === 'admin.attendance' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Attendance</a></li>
```

- [ ] **Step 2: Verify sidebar**

Visit any admin page. Confirm "Attendance" link appears under Analytics. Click it — should navigate to `/admin/attendance` and the link should be highlighted as active.

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/sidebar.blade.php
git commit -m "add attendance link to admin sidebar"
```
