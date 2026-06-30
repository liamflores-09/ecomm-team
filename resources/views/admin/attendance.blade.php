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
        var savedCell = activeCell;
        closeDropdown();
        saveStatus(uid, date, status, savedCell);
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
    })
    .catch(function(e) { console.error('Attendance save failed:', e); });
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
    })
    .catch(function(e) { console.error('Holiday mark failed:', e); });
}
</script>
@endsection
