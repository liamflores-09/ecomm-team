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

    /* ── Month card (nav + legend two-row) ──────────────────────── */
    .att-month-card {
        margin-bottom: 1.25rem;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
        overflow: hidden;
    }
    .att-nav-row {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1rem;
    }
    .att-nav-spacer { flex: 1; }
    .att-month-btn {
        display: flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 6px;
        border: 1px solid var(--border); background: transparent;
        color: var(--muted-foreground); text-decoration: none; font-size: 0.8rem;
        transition: all 0.15s;
    }
    .att-month-btn:hover { background: var(--muted); color: var(--foreground); }
    .att-picker-wrap { position: relative; }
    .att-nav-picker {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 10px; border-radius: 6px;
        border: 1px solid var(--border); background: transparent;
        font-size: 0.9rem; font-weight: 800; color: var(--foreground);
        font-family: inherit; cursor: pointer; transition: all 0.15s;
        white-space: nowrap;
    }
    .att-nav-picker:hover { background: var(--muted); border-color: var(--foreground); }
    .att-picker-caret { font-size: 0.6rem; color: var(--muted-foreground); transition: transform 0.18s; }
    .att-picker-panel {
        position: fixed; z-index: 9998;
        background: var(--card); border: 1px solid var(--border);
        border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.14);
        padding: 5px; min-width: 80px;
    }
    .att-month-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; }
    .att-picker-opt {
        display: block; width: 100%; padding: 6px 10px; border: none;
        background: transparent; cursor: pointer; border-radius: 6px;
        font-size: 0.78rem; font-weight: 600; color: var(--foreground);
        font-family: inherit; text-align: center; transition: background 0.1s;
    }
    .att-picker-opt:hover { background: var(--muted); }
    .att-picker-opt.is-active { color: var(--primary); font-weight: 800; background: rgba(99,102,241,0.08); }
    .att-today-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 13px; border-radius: 6px;
        border: 1px solid var(--border); background: transparent;
        font-size: 0.73rem; font-weight: 700; color: var(--muted-foreground);
        text-decoration: none; transition: all 0.15s;
    }
    .att-today-pill:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
    .att-legend-row {
        display: flex; align-items: center;
        padding: 0.45rem 1rem; gap: 0;
        border-top: 1px solid var(--border); background: var(--muted);
    }
    .att-legend-item {
        flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 5px;
        font-size: 0.71rem; font-weight: 600; color: var(--muted-foreground);
    }
    .att-legend-sep { width: 1px; height: 14px; background: var(--border); flex-shrink: 0; }

    /* ── Scroll wrapper ──────────────────────────────────────────── */
    .att-scroll { border-radius: 10px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }

    /* ── Table — matches Reports page style ──────────────────────── */
    .att-table { border-collapse: collapse; width: 100%; table-layout: fixed; }
    .att-table th, .att-table td { padding: 0; border-bottom: 1px solid var(--border); }
    .att-table tbody tr:last-child td { border-bottom: none; }

    /* Header row */
    .att-table thead th { background: var(--muted); border-bottom: 2px solid var(--border); }

    /* Sticky name column */
    .att-name-col {
        position: sticky; left: 0; z-index: 2;
        background: var(--card);
        width: 155px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        padding: 0.55rem 0.9rem 0.55rem 1.25rem;
        font-size: 0.78rem; font-weight: 600;
        border-right: 1px solid var(--border);
    }
    .att-table thead .att-name-col {
        z-index: 3; background: var(--muted);
        font-size: 0.7rem; text-transform: uppercase;
        letter-spacing: 0.06em; color: var(--muted-foreground); font-weight: 700;
    }

    /* Striped rows */
    .att-stripe td         { background: rgba(0,0,0,0.018); }
    .att-stripe .att-name-col { background: rgba(0,0,0,0.018); }
    [data-theme="dark"] .att-stripe td,
    [data-theme="dark"] .att-stripe .att-name-col { background: rgba(255,255,255,0.025); }

    /* Row hover — use var(--muted) so it works in dark mode too */
    .att-table tbody tr:not(.att-role-row):hover td,
    .att-table tbody tr:not(.att-role-row):hover .att-name-col { background: var(--muted) !important; }

    /* Day header */
    .att-day-th { text-align: center; vertical-align: middle; padding: 0.45rem 0.15rem 0.35rem; white-space: nowrap; }
    .att-day-th.att-today-col { background: rgba(99,102,241,0.08); border-top: 2px solid #6366f1 !important; }
    .att-day-th.att-sunday-col { background: rgba(245,158,11,0.07); }
    .att-day-dow { font-size: 0.5rem; font-weight: 700; color: var(--muted-foreground); opacity: 0.5; letter-spacing: 0.04em; line-height: 1; margin-bottom: 2px; }
    .att-day-num { font-size: 0.72rem; font-weight: 800; color: var(--muted-foreground); line-height: 1; margin-bottom: 2px; }
    .att-today-col .att-day-dow,
    .att-today-col .att-day-num { color: #6366f1; opacity: 1; }
    .att-sunday-col .att-day-dow,
    .att-sunday-col .att-day-num { color: #d97706; opacity: 1; }
    .att-holiday-btn {
        display: flex; align-items: center; justify-content: center;
        width: 100%; height: 14px; margin: 0 auto;
        border: none; background: transparent; cursor: pointer;
        color: var(--muted-foreground); font-size: 0.52rem; padding: 0;
        border-radius: 3px; transition: all 0.15s; opacity: 0.4;
    }
    .att-holiday-btn:hover { color: #6366f1; background: rgba(99,102,241,0.1); opacity: 1; }
    .att-rdo-icon { font-size: 0.55rem; color: #d97706; opacity: 0.7; display: block; }

    /* Role section row */
    .att-role-row td {
        background: var(--muted);
        padding: 0.35rem 0.9rem;
        font-size: 0.6rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.1em; color: var(--muted-foreground);
        border-left: 3px solid var(--border); border-top: 2px solid var(--border);
    }

    /* Data cells */
    .att-cell {
        display: flex; align-items: center; justify-content: center;
        min-height: 36px; cursor: pointer; padding: 3px;
        transition: background 0.1s;
    }
    .att-today-cell { background: rgba(99,102,241,0.04); }
    .att-rdo-cell   { background: rgba(245,158,11,0.06); cursor: default; }
    .att-rdo-cell i { color: #d97706; font-size: 0.75rem; }
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

    {{-- Month card: nav row + legend row --}}
    @php
        $navMonths   = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
        $navCurMonth = (int)$month->format('n');
        $navCurYear  = (int)$month->format('Y');
        $navYears    = range($navCurYear - 2, $navCurYear + 2);
    @endphp
    <div class="att-month-card anim-up d1">
        {{-- Row 1: Navigation --}}
        <div class="att-nav-row">
            <a href="{{ route('admin.attendance', ['month' => $prevMonth]) }}" class="att-month-btn" title="Previous month">
                <i class="fas fa-chevron-left"></i>
            </a>
            {{-- Month picker --}}
            <div class="att-picker-wrap">
                <button class="att-nav-picker" id="attMonthBtn" data-val="{{ $navCurMonth }}" onclick="togglePicker('month', event)">
                    {{ $navMonths[$navCurMonth] }}
                    <i class="fas fa-chevron-down att-picker-caret" id="attMonthCaret"></i>
                </button>
            </div>
            {{-- Year picker --}}
            <div class="att-picker-wrap">
                <button class="att-nav-picker" id="attYearBtn" data-val="{{ $navCurYear }}" onclick="togglePicker('year', event)">
                    {{ $navCurYear }}
                    <i class="fas fa-chevron-down att-picker-caret" id="attYearCaret"></i>
                </button>
            </div>
            <a href="{{ route('admin.attendance', ['month' => $nextMonth]) }}" class="att-month-btn" title="Next month">
                <i class="fas fa-chevron-right"></i>
            </a>
            <div class="att-nav-spacer"></div>
            <a href="{{ route('admin.attendance') }}" class="att-today-pill" title="Jump to current month">
                <i class="fas fa-calendar-day"></i> Today
            </a>
        </div>
        {{-- Row 2: Legend --}}
        <div class="att-legend-row">
            <span class="att-legend-item"><span class="att-chip present">P</span> Present</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><span class="att-chip half_day">HD</span> Half Day</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><span class="att-chip vl">VL</span> Vacation Leave</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><span class="att-chip sl">SL</span> Sick Leave</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><span class="att-chip absent">A</span> Absent</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><span class="att-chip ut">UT</span> Undertime</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><span class="att-chip holiday">H</span> Holiday</span>
            <div class="att-legend-sep"></div>
            <span class="att-legend-item"><i class="fas fa-umbrella-beach" style="color:#d97706;font-size:0.7rem;"></i> RDO</span>
        </div>
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
                'date'   => $ds,
                'dow'    => $dowLabels[$dow],
                'sunday' => $dow === 0,
                'today'  => $ds === $today,
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
                    <th class="att-day-th{{ $meta['sunday'] ? ' att-sunday-col' : '' }}{{ $meta['today'] ? ' att-today-col' : '' }}">
                        <div class="att-day-dow">{{ $meta['dow'] }}</div>
                        <div class="att-day-num">{{ $d }}</div>
                        @if ($meta['sunday'])
                        <i class="fas fa-umbrella-beach att-rdo-icon" title="Sunday — Rest Day (RDO)"></i>
                        @else
                        <button class="att-holiday-btn" title="Mark all Holiday — {{ $meta['date'] }}" onclick="markHoliday('{{ $meta['date'] }}')">
                            <i class="fas fa-flag"></i>
                        </button>
                        @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($usersByRole as $role => $users)
                @php $roleColor = $roleColors[$role] ?? 'var(--border)'; $stripe = false; @endphp
                <tr class="att-role-row">
                    <td colspan="{{ $daysInMonth + 1 }}" style="border-left-color:{{ $roleColor }};border-left-width:3px">{{ ucfirst($role) }}</td>
                </tr>
                @foreach ($users as $u)
                @php $stripe = !$stripe; @endphp
                <tr class="{{ $stripe ? '' : 'att-stripe' }}">
                    <td class="att-name-col">{{ $u->first_name }} {{ $u->last_name }}</td>
                    @foreach ($dayMeta as $d => $meta)
                    @php $status = $attendanceJson[$u->id][$meta['date']] ?? null; @endphp
                    <td>
                        @if ($meta['sunday'])
                        <div class="att-cell att-rdo-cell" title="Sunday — Rest Day (RDO)">
                            <i class="fas fa-umbrella-beach"></i>
                        </div>
                        @else
                        <div class="att-cell{{ $meta['today'] ? ' att-today-cell' : '' }}"
                             data-uid="{{ $u->id }}"
                             data-date="{{ $meta['date'] }}"
                             onclick="openDropdown(event, this)">
                            @if ($status)
                            <span class="att-chip {{ $status }}" title="{{ $chipLabels[$status] ?? $status }}">{{ $chipAbbrev[$status] ?? $status }}</span>
                            @endif
                        </div>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Month picker panel --}}
<div class="att-picker-panel att-month-panel" id="attMonthPanel" style="display:none;">
    <div class="att-month-grid">
        @foreach ($navMonths as $n => $name)
        <button class="att-picker-opt{{ $n === $navCurMonth ? ' is-active' : '' }}"
                onclick="pickMonth({{ $n }})">{{ substr($name, 0, 3) }}</button>
        @endforeach
    </div>
</div>

{{-- Year picker panel --}}
<div class="att-picker-panel att-year-panel" id="attYearPanel" style="display:none;">
    @foreach ($navYears as $y)
    <button class="att-picker-opt{{ $y === $navCurYear ? ' is-active' : '' }}"
            onclick="pickYear({{ $y }})">{{ $y }}</button>
    @endforeach
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
var CSRF      = '{{ csrf_token() }}';
var ATT_ROUTE = '{{ route("admin.attendance") }}';

function togglePicker(type, event) {
    event.stopPropagation();
    var btn   = document.getElementById(type === 'month' ? 'attMonthBtn' : 'attYearBtn');
    var panel = document.getElementById(type === 'month' ? 'attMonthPanel' : 'attYearPanel');
    var caret = document.getElementById(type === 'month' ? 'attMonthCaret' : 'attYearCaret');
    var isOpen = panel.style.display !== 'none';
    closePickers();
    if (!isOpen) {
        if (caret) caret.style.transform = 'rotate(180deg)';

        panel.style.visibility = 'hidden';
        panel.style.top  = '-9999px';
        panel.style.left = '-9999px';
        panel.style.display = 'block';

        var rect   = btn.getBoundingClientRect();
        var panelW = panel.offsetWidth;
        var panelH = panel.offsetHeight;
        var margin = 6;
        var top    = rect.bottom + margin;
        var left   = rect.left;

        if (top + panelH > window.innerHeight) top = rect.top - panelH - margin;
        if (left + panelW > window.innerWidth)  left = window.innerWidth - panelW - 4;
        if (left < 4) left = 4;

        panel.style.top        = top  + 'px';
        panel.style.left       = left + 'px';
        panel.style.visibility = '';
    }
}

function closePickers() {
    document.querySelectorAll('.att-picker-panel').forEach(function(p) { p.style.display = 'none'; });
    document.querySelectorAll('.att-picker-caret').forEach(function(c) { c.style.transform = ''; });
}

function pickMonth(n) {
    var m = String(n).padStart(2, '0');
    var y = document.getElementById('attYearBtn').dataset.val;
    window.location.href = ATT_ROUTE + '?month=' + y + '-' + m;
}

function pickYear(y) {
    var m = String(document.getElementById('attMonthBtn').dataset.val).padStart(2, '0');
    window.location.href = ATT_ROUTE + '?month=' + y + '-' + m;
}
var CHIP_MAP = { present:'P', half_day:'HD', vl:'VL', sl:'SL', absent:'A', ut:'UT', holiday:'H' };
var CHIP_LBL = { present:'Present', half_day:'Half Day', vl:'Vacation Leave', sl:'Sick Leave', absent:'Absent', ut:'Undertime', holiday:'Holiday' };
var activeCell = null;
var dropdown   = document.getElementById('attDropdown');

function openDropdown(event, cell) {
    event.stopPropagation();
    activeCell = cell;
    dropdown.style.display = 'block';

    var rect     = cell.getBoundingClientRect();
    var ddHeight = dropdown.offsetHeight;
    var ddWidth  = dropdown.offsetWidth;
    var margin   = 6;
    var top, left;

    if (rect.bottom + ddHeight + margin > window.innerHeight && rect.top - ddHeight - margin > 0) {
        top = rect.top + window.scrollY - ddHeight - margin;
    } else {
        top = rect.bottom + window.scrollY + margin;
    }

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
    if (!e.target.closest('.att-picker-wrap')) closePickers();
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
