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
        display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;
        padding: 0.6rem 1rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
    }
    .att-month-btn {
        display: flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid var(--border); background: transparent;
        color: var(--muted-foreground); text-decoration: none; font-size: 0.85rem;
        transition: all 0.15s;
    }
    .att-month-btn:hover { background: var(--muted); color: var(--foreground); }
    .att-month-label { font-size: 1rem; font-weight: 800; min-width: 130px; text-align: center; }
    .att-today-pill {
        margin-left: auto;
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 6px;
        border: 1px solid var(--border); background: transparent;
        font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground);
        text-decoration: none; transition: all 0.15s;
    }
    .att-today-pill:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

    /* ── Legend ──────────────────────────────────────────────────── */
    .att-legend {
        display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem 1.1rem;
        margin-bottom: 1.25rem; padding: 0.65rem 1rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
        font-size: 0.75rem; font-weight: 600; color: var(--muted-foreground);
    }
    .att-legend-item { display: inline-flex; align-items: center; gap: 5px; }

    /* ── Scroll wrapper ──────────────────────────────────────────── */
    .att-scroll { overflow-x: auto; border-radius: 10px; border: 1px solid var(--border); box-shadow: 0 1px 4px rgba(0,0,0,0.06); }

    /* ── Table ───────────────────────────────────────────────────── */
    .att-table { border-collapse: collapse; width: 100%; min-width: max-content; }
    .att-table th, .att-table td { border: 1px solid var(--border); padding: 0; white-space: nowrap; }

    /* Header row */
    .att-table thead tr th { background: var(--muted) !important; }
    .att-table thead tr { border-bottom: 2px solid var(--border); }

    /* Sticky name column */
    .att-name-col {
        position: sticky; left: 0; z-index: 2;
        background: var(--card);
        min-width: 170px; max-width: 210px;
        padding: 0.55rem 0.9rem;
        font-size: 0.78rem; font-weight: 600;
        box-shadow: 3px 0 8px -3px rgba(0,0,0,0.1);
    }
    .att-table thead .att-name-col {
        z-index: 3;
        background: var(--muted) !important;
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--muted-foreground); font-weight: 700;
    }

    /* Row hover — entire row lights up */
    .att-table tbody tr:not(.att-role-row):hover td,
    .att-table tbody tr:not(.att-role-row):hover .att-name-col {
        background: rgba(99,102,241,0.05) !important;
    }

    /* Day header */
    .att-day-th {
        text-align: center; vertical-align: middle;
        min-width: 46px; padding: 0.55rem 0.2rem 0.4rem;
    }
    .att-day-th.att-weekend     { background: rgba(0,0,0,0.03) !important; }
    .att-day-th.att-today-col   { background: rgba(99,102,241,0.09) !important; border-top: 2px solid #6366f1 !important; }
    .att-day-dow { font-size: 0.54rem; font-weight: 700; color: var(--muted-foreground); opacity: 0.55; letter-spacing: 0.05em; line-height: 1; margin-bottom: 3px; }
    .att-day-num { font-size: 0.75rem; font-weight: 800; color: var(--muted-foreground); line-height: 1; margin-bottom: 3px; }
    .att-today-col .att-day-dow,
    .att-today-col .att-day-num { color: #6366f1; opacity: 1; }
    .att-holiday-btn {
        display: flex; align-items: center; justify-content: center;
        width: 20px; height: 16px; margin: 0 auto;
        border: none; background: transparent; cursor: pointer;
        color: var(--muted-foreground); font-size: 0.56rem; padding: 0;
        border-radius: 3px; transition: all 0.15s; opacity: 0.5;
    }
    .att-holiday-btn:hover { color: #6366f1; background: rgba(99,102,241,0.12); opacity: 1; }

    /* Role section row */
    .att-role-row td {
        background: var(--muted) !important;
        padding: 0.45rem 0.9rem;
        font-size: 0.62rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.1em; color: var(--muted-foreground);
        border-left-width: 3px; border-top: 2px solid var(--border);
    }

    /* Data cells */
    .att-cell {
        display: flex; align-items: center; justify-content: center;
        min-height: 40px; cursor: pointer; padding: 4px;
        transition: background 0.1s;
    }
    .att-weekend-cell { background: rgba(0,0,0,0.018); }
    .att-today-cell   { background: rgba(99,102,241,0.04); }
    .att-cell--loading { opacity: 0.35; pointer-events: none; cursor: wait; }

    /* Status chips */
    .att-chip {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 3px 6px; border-radius: 5px;
        font-size: 0.62rem; font-weight: 800; letter-spacing: 0.02em;
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
        border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.14);
        padding: 4px; min-width: 164px;
    }
    .att-dd-item {
        display: flex; align-items: center; gap: 8px;
        width: 100%; padding: 7px 10px; border: none;
        background: transparent; cursor: pointer; border-radius: 6px;
        font-size: 0.78rem; font-weight: 600; color: var(--foreground);
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
        <a href="{{ route('admin.attendance', ['month' => $prevMonth]) }}" class="att-month-btn" title="Previous month">
            <i class="fas fa-chevron-left"></i>
        </a>
        <span class="att-month-label">{{ $month->format('F Y') }}</span>
        <a href="{{ route('admin.attendance', ['month' => $nextMonth]) }}" class="att-month-btn" title="Next month">
            <i class="fas fa-chevron-right"></i>
        </a>
        <a href="{{ route('admin.attendance') }}" class="att-today-pill" title="Jump to current month">
            <i class="fas fa-calendar-day"></i> Today
        </a>
    </div>

    {{-- Status legend --}}
    <div class="att-legend anim-up d2">
        <span class="att-legend-item"><span class="att-chip present">P</span> Present</span>
        <span class="att-legend-item"><span class="att-chip half_day">HD</span> Half Day</span>
        <span class="att-legend-item"><span class="att-chip vl">VL</span> Vacation Leave</span>
        <span class="att-legend-item"><span class="att-chip sl">SL</span> Sick Leave</span>
        <span class="att-legend-item"><span class="att-chip absent">A</span> Absent</span>
        <span class="att-legend-item"><span class="att-chip ut">UT</span> Undertime</span>
        <span class="att-legend-item"><span class="att-chip holiday">H</span> Holiday</span>
    </div>

    {{-- Precompute day metadata --}}
    @php
        $today      = now()->format('Y-m-d');
        $dowLabels  = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $roleColors = [
            'content'    => '#10b981',
            'researcher' => '#0ea5e9',
            'graphics'   => '#f59e0b',
            'backend'    => '#6366f1',
        ];
        $chipLabels = [
            'present'  => 'Present',       'half_day' => 'Half Day',
            'vl'       => 'Vacation Leave', 'sl'       => 'Sick Leave',
            'absent'   => 'Absent',         'ut'       => 'Undertime',
            'holiday'  => 'Holiday',
        ];
        $chipAbbrev = [
            'present'  => 'P',  'half_day' => 'HD', 'vl' => 'VL',
            'sl'       => 'SL', 'absent'   => 'A',  'ut' => 'UT',
            'holiday'  => 'H',
        ];
        $dayMeta = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $ds  = $month->format('Y-m') . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
            $dow = \Carbon\Carbon::parse($ds)->dayOfWeek;
            $dayMeta[$d] = [
                'date'    => $ds,
                'dow'     => $dowLabels[$dow],
                'weekend' => in_array($dow, [0, 6]),
                'today'   => $ds === $today,
            ];
        }
    @endphp

    {{-- Grid --}}
    <div class="att-scroll anim-up d3">
        <table class="att-table">
            <thead>
                <tr>
                    <th class="att-name-col">Member</th>
                    @foreach ($dayMeta as $d => $meta)
                    <th class="att-day-th{{ $meta['weekend'] ? ' att-weekend' : '' }}{{ $meta['today'] ? ' att-today-col' : '' }}">
                        <div class="att-day-dow">{{ $meta['dow'] }}</div>
                        <div class="att-day-num">{{ $d }}</div>
                        <button class="att-holiday-btn" title="Mark all Holiday — {{ $meta['date'] }}" onclick="markHoliday('{{ $meta['date'] }}')">
                            <i class="fas fa-flag"></i>
                        </button>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($usersByRole as $role => $users)
                @php $roleColor = $roleColors[$role] ?? 'var(--border)'; @endphp
                <tr class="att-role-row">
                    <td colspan="{{ $daysInMonth + 1 }}" style="border-left-color:{{ $roleColor }}">{{ ucfirst($role) }}</td>
                </tr>
                @foreach ($users as $u)
                <tr>
                    <td class="att-name-col">{{ $u->first_name }} {{ $u->last_name }}</td>
                    @foreach ($dayMeta as $d => $meta)
                    @php $status = $attendanceJson[$u->id][$meta['date']] ?? null; @endphp
                    <td>
                        <div class="att-cell{{ $meta['weekend'] ? ' att-weekend-cell' : '' }}{{ $meta['today'] ? ' att-today-cell' : '' }}"
                             data-uid="{{ $u->id }}"
                             data-date="{{ $meta['date'] }}"
                             onclick="openDropdown(event, this)">
                            @if ($status)
                            <span class="att-chip {{ $status }}" title="{{ $chipLabels[$status] ?? $status }}">{{ $chipAbbrev[$status] ?? $status }}</span>
                            @endif
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Dropdown --}}
<div id="attDropdown" class="att-dropdown" style="display:none;">
    <button class="att-dd-item" data-status="present"><span class="att-chip present">P</span> Present</button>
    <button class="att-dd-item" data-status="half_day"><span class="att-chip half_day">HD</span> Half Day</button>
    <button class="att-dd-item" data-status="vl"><span class="att-chip vl">VL</span> Vacation Leave</button>
    <button class="att-dd-item" data-status="sl"><span class="att-chip sl">SL</span> Sick Leave</button>
    <button class="att-dd-item" data-status="absent"><span class="att-chip absent">A</span> Absent</button>
    <button class="att-dd-item" data-status="ut"><span class="att-chip ut">UT</span> Undertime</button>
    <button class="att-dd-item" data-status="holiday"><span class="att-chip holiday">H</span> Holiday</button>
    <div class="att-dd-sep"></div>
    <button class="att-dd-item att-dd-clear" data-status="">
        <i class="fas fa-xmark" style="width:18px;text-align:center;"></i> Clear
    </button>
</div>
@endsection

@section('scripts')
<script>
var CSRF     = '{{ csrf_token() }}';
var CHIP_MAP = { present:'P', half_day:'HD', vl:'VL', sl:'SL', absent:'A', ut:'UT', holiday:'H' };
var CHIP_LBL = { present:'Present', half_day:'Half Day', vl:'Vacation Leave', sl:'Sick Leave', absent:'Absent', ut:'Undertime', holiday:'Holiday' };
var activeCell = null;
var dropdown   = document.getElementById('attDropdown');

function openDropdown(event, cell) {
    event.stopPropagation();
    activeCell = cell;
    dropdown.style.display = 'block';

    var rect       = cell.getBoundingClientRect();
    var ddHeight   = dropdown.offsetHeight;
    var ddWidth    = dropdown.offsetWidth;
    var margin     = 6;
    var top, left;

    // Flip above if not enough space below
    if (rect.bottom + ddHeight + margin > window.innerHeight && rect.top - ddHeight - margin > 0) {
        top = rect.top + window.scrollY - ddHeight - margin;
    } else {
        top = rect.bottom + window.scrollY + margin;
    }

    // Clamp horizontally
    left = rect.left + window.scrollX;
    if (left + ddWidth + 4 > window.innerWidth) left = window.innerWidth - ddWidth - 4;
    if (left < 4) left = 4;

    dropdown.style.top  = top  + 'px';
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
        var status    = this.dataset.status;
        var uid       = activeCell.dataset.uid;
        var date      = activeCell.dataset.date;
        var savedCell = activeCell;
        closeDropdown();
        saveStatus(uid, date, status, savedCell);
    });
});

document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) closeDropdown();
});

function saveStatus(uid, date, status, cell) {
    cell.classList.add('att-cell--loading');
    fetch('{{ route("admin.attendance.upsert") }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ user_id: uid, date: date, status: status || null }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        cell.classList.remove('att-cell--loading');
        if (data.success) updateCell(cell, status);
    })
    .catch(function(e) {
        cell.classList.remove('att-cell--loading');
        console.error('Attendance save failed:', e);
    });
}

function updateCell(cell, status) {
    cell.innerHTML = '';
    if (status) {
        var chip = document.createElement('span');
        chip.className   = 'att-chip ' + status;
        chip.title       = CHIP_LBL[status] || status;
        chip.textContent = CHIP_MAP[status]  || status;
        cell.appendChild(chip);
    }
}

function markHoliday(date) {
    fetch('{{ route("admin.attendance.holiday") }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ date: date }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.querySelectorAll('.att-cell[data-date="' + date + '"]').forEach(function(cell) {
                updateCell(cell, 'holiday');
            });
        }
    })
    .catch(function(e) { console.error('Holiday mark failed:', e); });
}
</script>
@endsection
