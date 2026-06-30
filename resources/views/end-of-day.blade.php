@extends('layouts.app')

@section('title', 'End-of-Day Report — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='4' width='18' height='18' rx='2' ry='2'/><line x1='16' y1='2' x2='16' y2='6'/><line x1='8' y1='2' x2='8' y2='6'/><line x1='3' y1='10' x2='21' y2='10'/><path d='M8 14h.01'/><path d='M12 14h.01'/><path d='M16 14h.01'/><path d='M8 18h.01'/><path d='M12 18h.01'/></svg>">
@endsection

@section('styles')
<style>
/* ── Shared card shell ────────────────────────────────── */
.eod-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.eod-card-header {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: var(--muted);
    border-bottom: 1px solid var(--border);
    font-weight: 700; font-size: 0.72rem;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--muted-foreground);
}
.eod-card-header .t-icon {
    width: 22px; height: 22px;
    background: var(--primary); border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 0.6rem; flex-shrink: 0;
}

/* ── Date / status bar inside form card ──────────────── */
.eod-date-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.875rem 1.375rem;
    border-bottom: 1px solid var(--border);
    gap: 1rem; flex-wrap: wrap;
}
.eod-date-label {
    font-size: 0.85rem; font-weight: 700;
    color: var(--foreground);
    display: flex; align-items: center; gap: 0.5rem;
}
.eod-date-label i { color: var(--muted-foreground); font-size: 0.75rem; }
.eod-status-pill {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 4px 12px; border-radius: 9999px;
    font-size: 0.68rem; font-weight: 700;
}
.eod-status-pill.submitted { background: #dcfce7; color: #15803d; }
.eod-status-pill.pending   { background: var(--muted); color: var(--muted-foreground); border: 1px solid var(--border); }

/* ── Task counter grid ────────────────────────────────── */
.eod-form-body { padding: 1.25rem 1.375rem; }
.task-cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.875rem;
    margin-bottom: 1.125rem;
}
.task-card {
    background: var(--muted);
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 1.125rem 0.75rem 0.875rem;
    text-align: center;
    transition: border-color 0.15s, background 0.15s;
}
.task-card:focus-within {
    border-color: var(--primary);
    background: var(--card);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 12%, transparent);
}
.task-card-label {
    font-size: 0.6rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--muted-foreground);
    margin-bottom: 0.75rem; line-height: 1.3;
    min-height: 2.2em; display: flex; align-items: center; justify-content: center;
}
.task-stepper {
    display: flex; align-items: center; justify-content: center; gap: 0.375rem;
}
.stepper-btn {
    width: 26px; height: 26px; border-radius: 6px;
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--muted-foreground);
    cursor: pointer; font-size: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.12s; flex-shrink: 0; user-select: none;
}
.stepper-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--card); }
.stepper-btn:active { transform: scale(0.9); }
.task-num-input {
    width: 48px; text-align: center;
    background: transparent; border: none;
    font-size: 1.55rem; font-weight: 800;
    color: var(--foreground); outline: none;
    font-family: 'Space Grotesk', sans-serif;
    -moz-appearance: textfield;
}
.task-num-input::-webkit-outer-spin-button,
.task-num-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.task-card:focus-within .task-num-input { color: var(--primary); }

/* ── Total bar ────────────────────────────────────────── */
.eod-total-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.6rem 0.875rem;
    background: var(--muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-bottom: 1rem;
}
.eod-total-bar .total-label {
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--muted-foreground);
    display: flex; align-items: center; gap: 0.4rem;
}
.eod-total-bar .total-value {
    font-size: 0.95rem; font-weight: 800;
    color: var(--foreground);
    font-family: 'Space Grotesk', sans-serif;
}
.eod-total-bar .total-value span { color: var(--primary); }

/* ── Remarks ──────────────────────────────────────────── */
.remarks-wrap { position: relative; }
.form-label {
    display: block; font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--muted-foreground); margin-bottom: 0.375rem;
}
.form-textarea {
    width: 100%; box-sizing: border-box;
    padding: 0.75rem 0.875rem;
    background: var(--muted); border: 2px solid transparent;
    border-radius: 8px; font-family: var(--p-font-family-sans);
    font-size: 0.88rem; font-weight: 500; color: var(--fg);
    outline: none; resize: vertical; min-height: 78px;
    transition: all 0.15s;
}
.form-textarea:focus { border-color: var(--primary); background: var(--card); }
.form-textarea::placeholder { color: var(--gray-300); }
.remarks-char {
    position: absolute; right: 0.75rem; bottom: 0.5rem;
    font-size: 0.62rem; color: var(--muted-foreground); font-weight: 600;
    pointer-events: none;
}

/* ── Form actions ─────────────────────────────────────── */
.eod-form-footer {
    display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem;
    margin-top: 1.125rem; padding-top: 1rem;
    border-top: 1px solid var(--border);
}

/* ── Recent logs table ────────────────────────────────── */
.logs-table-wrap { overflow-x: auto; }
.logs-table {
    width: 100%; border-collapse: collapse; font-size: 0.84rem;
}
.logs-table thead th {
    background: var(--muted);
    padding: 0.625rem 0.875rem;
    font-weight: 700; font-size: 0.62rem;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--muted-foreground); text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid var(--border);
}
.logs-table tbody td {
    padding: 0.7rem 0.875rem;
    border-bottom: 1px solid var(--border);
    font-weight: 500;
    color: var(--foreground);
}
.logs-table tbody tr:last-child td { border-bottom: none; }
.logs-table tbody tr:hover td { background: var(--muted); }
.logs-table .num { font-weight: 700; text-align: center; color: var(--foreground); }
.logs-table .num.zero { color: var(--muted-foreground); font-weight: 400; }
.log-total-chip {
    display: inline-flex; align-items: center; padding: 2px 8px;
    border-radius: 9999px; font-size: 0.7rem; font-weight: 700;
    white-space: nowrap;
}
.log-total-chip.high   { background: #dcfce7; color: #15803d; }
.log-total-chip.mid    { background: #fef3c7; color: #b45309; }
.log-total-chip.low    { background: var(--muted); color: var(--muted-foreground); }
.log-date-col { font-weight: 700; white-space: nowrap; }
.log-today-badge {
    display: inline-block; margin-left: 0.375rem;
    padding: 1px 6px; border-radius: 4px;
    font-size: 0.58rem; font-weight: 800;
    background: var(--primary); color: white; vertical-align: middle;
    text-transform: uppercase; letter-spacing: 0.04em;
}
.log-remarks { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--muted-foreground); font-size: 0.78rem; }
.action-btns { display: flex; gap: 0.25rem; }
.action-btn-sm {
    width: 28px; height: 28px;
    border: 1px solid var(--border); border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.68rem; cursor: pointer; transition: all 0.12s;
    background: transparent; color: var(--muted-foreground);
}
.action-btn-sm:hover { border-color: var(--primary); color: var(--primary); }
.action-btn-sm.btn-danger:hover { border-color: #dc2626; color: #dc2626; }

/* ── Empty state ──────────────────────────────────────── */
.empty-logs {
    text-align: center; padding: 2.5rem 1rem;
    color: var(--muted-foreground); font-size: 0.85rem; font-weight: 500;
}
.empty-logs i { font-size: 1.75rem; display: block; margin-bottom: 0.6rem; opacity: 0.35; }

/* ── Preview lock ─────────────────────────────────────── */
.preview-locked { pointer-events: none; opacity: 0.7; }

/* ── Responsive ───────────────────────────────────────── */
@media (max-width: 768px) {
    .task-cards { grid-template-columns: repeat(3, 1fr); }
    .logs-table { min-width: 580px; }
}
@media (max-width: 480px) {
    .task-cards { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endsection

@section('content')
@php
$isPreview    = $isPreview ?? false;
$previewRole  = $previewRole ?? null;
$taskLabels   = $taskLabels ?? \App\Support\TaskLabels::get($user->role);
$todayString  = now()->toDateString();
$today        = now()->format('l, F j, Y');
$vals = [
    'task_1' => $existingLog ? $existingLog->task_1 : 0,
    'task_2' => $existingLog ? $existingLog->task_2 : 0,
    'task_3' => $existingLog ? $existingLog->task_3 : 0,
    'task_4' => $existingLog ? $existingLog->task_4 : 0,
    'task_5' => $existingLog ? $existingLog->task_5 : 0,
];
$initTotal = array_sum($vals);
@endphp
<x-sidebar active="end-of-day" />

<div class="main-content">

    <div class="top-bar anim-up" style="margin-bottom:1.25rem;">
        <div>
            <h2>End-of-Day <span class="highlight">Report</span></h2>
            <p>Log your daily tasks and activities</p>
        </div>
        @if(($isPreview ? $previewRole : $user->role) === 'content')
        <button type="button" class="btn-flat-secondary" style="height:40px;padding:0 1rem;font-size:0.85rem;" onclick="openModal('tutorialModal')">
            <i class="fas fa-circle-info"></i> How to Fill
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    @if($isPreview)
    <div class="alert-flat anim-fade" style="background:#fef3c7;color:#92400e;border:1px solid #f59e0b;margin-bottom:1rem;"><i class="fas fa-eye"></i> Admin preview — form is read-only</div>
    @endif

    {{-- ── Log Form ── --}}
    <div class="eod-card anim-up d1 {{ $isPreview ? 'preview-locked' : '' }}">
        <div class="eod-card-header">
            <div class="t-icon"><i class="fas fa-pen"></i></div>
            {{ $existingLog ? 'Edit Today\'s Log' : 'Log Today\'s Tasks' }}
            <div style="margin-left:auto;display:flex;align-items:center;gap:0.5rem;">
            </div>
        </div>

        {{-- Date + status bar --}}
        <div class="eod-date-bar">
            <div class="eod-date-label">
                <i class="fas fa-calendar-day"></i>
                {{ $today }}
            </div>
            @if($existingLog)
            <span class="eod-status-pill submitted">
                <i class="fas fa-circle-check"></i> Submitted
            </span>
            @else
            <span class="eod-status-pill pending">
                <i class="fas fa-circle-dot"></i> Not yet submitted
            </span>
            @endif
        </div>

        <div class="eod-form-body">
            <form method="POST" action="{{ $existingLog ? route('daily-logs.update', $existingLog) : route('daily-logs.store') }}">
                @csrf
                @if($existingLog) @method('PUT') @endif
                <input type="hidden" name="date" value="{{ $todayString }}">

                {{-- Task counter cards --}}
                <div class="task-cards">
                    @foreach(['task_1','task_2','task_3','task_4','task_5'] as $tk)
                    <div class="task-card">
                        <div class="task-card-label">{{ $taskLabels[$tk] ?? 'Task '.substr($tk,-1) }}</div>
                        <div class="task-stepper">
                            <button type="button" class="stepper-btn" onclick="stepTask('{{ $tk }}',-1)" tabindex="-1">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input
                                type="number"
                                name="{{ $tk }}"
                                id="{{ $tk }}"
                                class="task-num-input"
                                min="0"
                                value="{{ $vals[$tk] }}"
                                required
                                oninput="updateTotal()"
                            >
                            <button type="button" class="stepper-btn" onclick="stepTask('{{ $tk }}',1)" tabindex="-1">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Live total --}}
                <div class="eod-total-bar">
                    <div class="total-label">
                        <i class="fas fa-sigma"></i>
                        Total tasks today
                    </div>
                    <div class="total-value"><span id="eodTotal">{{ $initTotal }}</span> tasks</div>
                </div>

                {{-- Remarks --}}
                <div class="remarks-wrap">
                    <label class="form-label" for="eodRemarks">Remarks</label>
                    <textarea
                        name="remarks"
                        id="eodRemarks"
                        class="form-textarea"
                        maxlength="500"
                        placeholder="e.g. Canva, Change Price: 20, Repost: 5"
                        oninput="updateRemarks(this)"
                    >{{ $existingLog ? $existingLog->remarks : '' }}</textarea>
                    <div class="remarks-char" id="remarksChar">{{ $existingLog ? strlen($existingLog->remarks ?? '') : 0 }}/500</div>
                </div>

                <div class="eod-form-footer">
                    <button type="submit" class="btn-flat-primary" style="height:42px;padding:0 1.5rem;font-size:0.88rem;">
                        <i class="fas fa-check"></i> {{ $existingLog ? 'Update Log' : 'Save Log' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Recent Logs ── --}}
    <div class="eod-card anim-up d2">
        <div class="eod-card-header">
            <div class="t-icon" style="background:var(--muted-foreground);"><i class="fas fa-clock-rotate-left"></i></div>
            Recent Logs
            <span style="margin-left:auto;font-size:0.6rem;font-weight:600;color:var(--muted-foreground);">Last 10 entries</span>
        </div>
        <div class="logs-table-wrap">
            @if($recentLogs->count())
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th style="text-align:center;">{{ Str::limit($taskLabels['task_1'] ?? 'T1', 10) }}</th>
                        <th style="text-align:center;">{{ Str::limit($taskLabels['task_2'] ?? 'T2', 10) }}</th>
                        <th style="text-align:center;">{{ Str::limit($taskLabels['task_3'] ?? 'T3', 10) }}</th>
                        <th style="text-align:center;">{{ Str::limit($taskLabels['task_4'] ?? 'T4', 10) }}</th>
                        <th style="text-align:center;">{{ Str::limit($taskLabels['task_5'] ?? 'T5', 10) }}</th>
                        <th style="text-align:center;">Total</th>
                        <th>Remarks</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                    @php
                        $rowTotal = $log->task_1 + $log->task_2 + $log->task_3 + $log->task_4 + $log->task_5;
                        $chipClass = $rowTotal >= 10 ? 'high' : ($rowTotal >= 4 ? 'mid' : 'low');
                        $isToday = $log->date->toDateString() === $todayString;
                    @endphp
                    <tr>
                        <td class="log-date-col">
                            {{ $log->date->format('M d, Y') }}
                            @if($isToday)<span class="log-today-badge">Today</span>@endif
                        </td>
                        <td class="num {{ $log->task_1 == 0 ? 'zero' : '' }}">{{ $log->task_1 }}</td>
                        <td class="num {{ $log->task_2 == 0 ? 'zero' : '' }}">{{ $log->task_2 }}</td>
                        <td class="num {{ $log->task_3 == 0 ? 'zero' : '' }}">{{ $log->task_3 }}</td>
                        <td class="num {{ $log->task_4 == 0 ? 'zero' : '' }}">{{ $log->task_4 }}</td>
                        <td class="num {{ $log->task_5 == 0 ? 'zero' : '' }}">{{ $log->task_5 }}</td>
                        <td style="text-align:center;">
                            <span class="log-total-chip {{ $chipClass }}">{{ $rowTotal }}</span>
                        </td>
                        <td class="log-remarks" title="{{ $log->remarks }}">{{ $log->remarks ?: '—' }}</td>
                        <td>
                            @if($isToday)
                            <div class="action-btns">
                                <form method="POST" action="{{ route('daily-logs.destroy', $log) }}" onsubmit="return confirm('Delete this log?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-logs">
                <i class="fas fa-clipboard-list"></i>
                No logs yet — start by filling in today's tasks above.
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Tutorial Modal (content role only) ── --}}
<div class="modal-overlay" id="tutorialModal">
    <div class="modal-box" style="max-width:760px;">
        <div class="modal-header">
            <h5 style="font-weight:700;font-size:1rem;margin:0;">
                <i class="fas fa-circle-info" style="color:var(--primary);margin-right:0.5rem;"></i>How to Fill Your EOD Report
            </h5>
            <button class="modal-close" onclick="closeModal('tutorialModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="padding:1.5rem;">

            <h6 style="font-weight:800;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--muted-foreground);margin-bottom:0.75rem;">Column Overview</h6>
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.5rem;">
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);background:var(--foreground);color:var(--card);font-weight:800;font-size:0.65rem;text-transform:uppercase;">New SKU</div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);background:var(--foreground);color:var(--card);font-weight:800;font-size:0.65rem;text-transform:uppercase;">Variation SKU</div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);background:var(--foreground);color:var(--card);font-weight:800;font-size:0.65rem;text-transform:uppercase;">Adv. Data Gathering</div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);background:var(--foreground);color:var(--card);font-weight:800;font-size:0.65rem;text-transform:uppercase;">Update Listings</div>
                <div style="padding:0.75rem;text-align:center;background:var(--foreground);color:var(--card);font-weight:800;font-size:0.65rem;text-transform:uppercase;">Other</div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);border-top:1px solid var(--border);"><strong style="font-size:0.7rem;">Parent/Single</strong><br><span style="font-size:0.6rem;color:var(--muted-foreground);">New product posted</span></div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);border-top:1px solid var(--border);"><strong style="font-size:0.7rem;">Child/Variant</strong><br><span style="font-size:0.6rem;color:var(--muted-foreground);">Variation posted</span></div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);border-top:1px solid var(--border);"><strong style="font-size:0.7rem;">Data Gathered</strong><br><span style="font-size:0.6rem;color:var(--muted-foreground);">Research completed</span></div>
                <div style="padding:0.75rem;text-align:center;border-right:1px solid var(--border);border-top:1px solid var(--border);"><strong style="font-size:0.7rem;">Updated Listings</strong><br><span style="font-size:0.6rem;color:var(--muted-foreground);">Old SKUs updated</span></div>
                <div style="padding:0.75rem;text-align:center;border-top:1px solid var(--border);"><strong style="font-size:0.7rem;">Extra Tasks</strong><br><span style="font-size:0.6rem;color:var(--muted-foreground);">Canva, etc.</span></div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;margin-bottom:1.5rem;border:1px solid var(--border);border-radius:8px;overflow:hidden;">
                <div style="padding:1rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">
                        <div style="width:28px;height:28px;background:var(--foreground);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--card);font-size:0.7rem;"><i class="fas fa-box"></i></div>
                        <strong style="font-size:0.7rem;text-transform:uppercase;">New SKU & Variation SKU</strong>
                    </div>
                    <ul style="list-style:none;padding:0;margin:0;font-size:0.8rem;color:var(--muted-foreground);">
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> <strong>New SKU (Parent/Single)</strong> — Each parent or single SKU = 1</li>
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> <strong>Variation SKU (Child)</strong> — Each variant = 1</li>
                    </ul>
                    <div style="margin-top:0.5rem;padding:0.375rem 0.625rem;background:var(--muted);border:1px solid var(--border);border-radius:4px;font-size:0.65rem;font-weight:600;">Example: 1 parent + 4 children = 1 NEW SKU, 4 VARIATION SKU</div>
                </div>
                <div style="padding:1rem;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">
                        <div style="width:28px;height:28px;background:var(--foreground);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--card);font-size:0.7rem;"><i class="fas fa-magnifying-glass"></i></div>
                        <strong style="font-size:0.7rem;text-transform:uppercase;">Advance Data Gathering</strong>
                    </div>
                    <ul style="list-style:none;padding:0;margin:0;font-size:0.8rem;color:var(--muted-foreground);">
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> Count how many SKUs you data gathered</li>
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> Includes product research, specs, images</li>
                    </ul>
                    <div style="margin-top:0.5rem;padding:0.375rem 0.625rem;background:var(--muted);border:1px solid var(--border);border-radius:4px;font-size:0.65rem;font-weight:600;">Example: Data gathered 5 SKUs = 5</div>
                </div>
                <div style="padding:1rem;border-right:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">
                        <div style="width:28px;height:28px;background:var(--foreground);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--card);font-size:0.7rem;"><i class="fas fa-pencil"></i></div>
                        <strong style="font-size:0.7rem;text-transform:uppercase;">Update Listings</strong>
                    </div>
                    <ul style="list-style:none;padding:0;margin:0;font-size:0.8rem;color:var(--muted-foreground);">
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> Updated Photos, Text, Long Description, Wrong SKU</li>
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> Count depends on how many you updated</li>
                    </ul>
                    <div style="margin-top:0.5rem;padding:0.375rem 0.625rem;background:var(--muted);border:1px solid var(--border);border-radius:4px;font-size:0.65rem;font-weight:600;">Example: Updated photos for 2 + corrected SKU for 1 = 3</div>
                </div>
                <div style="padding:1rem;">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">
                        <div style="width:28px;height:28px;background:var(--foreground);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--card);font-size:0.7rem;"><i class="fas fa-list-check"></i></div>
                        <strong style="font-size:0.7rem;text-transform:uppercase;">Other & Remarks</strong>
                    </div>
                    <ul style="list-style:none;padding:0;margin:0;font-size:0.8rem;color:var(--muted-foreground);">
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> <strong>Canva Usage</strong> — OTHER = 1, Remarks: "Canva"</li>
                        <li style="padding:0.25rem 0;display:flex;gap:0.5rem;"><span style="color:#059669;">✓</span> <strong>Post Pending SKU</strong> — Remarks: "Post Pending SKU: #"</li>
                    </ul>
                    <div style="margin-top:0.5rem;padding:0.375rem 0.625rem;background:var(--muted);border:1px solid var(--border);border-radius:4px;font-size:0.65rem;font-weight:600;">Example: Canva → OTHER = 1, REMARKS = "Canva"</div>
                </div>
            </div>

            <h6 style="font-weight:800;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--muted-foreground);margin-bottom:0.75rem;">Example EOD Report</h6>
            <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.5rem;">
                <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                    <thead>
                        <tr>
                            <th style="background:var(--foreground);color:var(--card);padding:0.625rem;font-size:0.65rem;text-transform:uppercase;text-align:left;">New SKU</th>
                            <th style="background:var(--foreground);color:var(--card);padding:0.625rem;font-size:0.65rem;text-transform:uppercase;text-align:left;">Var. SKU</th>
                            <th style="background:var(--foreground);color:var(--card);padding:0.625rem;font-size:0.65rem;text-transform:uppercase;text-align:left;">Data Gather</th>
                            <th style="background:var(--foreground);color:var(--card);padding:0.625rem;font-size:0.65rem;text-transform:uppercase;text-align:left;">Update</th>
                            <th style="background:var(--foreground);color:var(--card);padding:0.625rem;font-size:0.65rem;text-transform:uppercase;text-align:left;">Other</th>
                            <th style="background:var(--foreground);color:var(--card);padding:0.625rem;font-size:0.65rem;text-transform:uppercase;text-align:left;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding:0.5rem;border-top:1px solid var(--muted);">2</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">5</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">0</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">0</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">1</td><td style="padding:0.5rem;border-top:1px solid var(--muted);color:var(--muted-foreground);">Canva</td></tr>
                        <tr><td style="padding:0.5rem;border-top:1px solid var(--muted);">1</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">3</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">4</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">0</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">3</td><td style="padding:0.5rem;border-top:1px solid var(--muted);color:var(--muted-foreground);">Post Pending SKU: 3</td></tr>
                        <tr><td style="padding:0.5rem;border-top:1px solid var(--muted);">4</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">8</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">0</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">2</td><td style="padding:0.5rem;border-top:1px solid var(--muted);">0</td><td style="padding:0.5rem;border-top:1px solid var(--muted);color:var(--muted-foreground);">—</td></tr>
                    </tbody>
                </table>
            </div>

            <h6 style="font-weight:800;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--muted-foreground);margin-bottom:0.75rem;">Quick Reference</h6>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;border:1px solid var(--border);border-radius:8px;overflow:hidden;">
                <div style="padding:0.75rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border);"><strong style="font-size:0.65rem;color:var(--primary);text-transform:uppercase;">New SKU</strong><p style="font-size:0.75rem;margin:0.25rem 0 0;color:var(--muted-foreground);">Parent/Single = 1 each</p></div>
                <div style="padding:0.75rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border);"><strong style="font-size:0.65rem;color:var(--primary);text-transform:uppercase;">Variation SKU</strong><p style="font-size:0.75rem;margin:0.25rem 0 0;color:var(--muted-foreground);">Child/Variant = 1 each</p></div>
                <div style="padding:0.75rem;border-bottom:1px solid var(--border);"><strong style="font-size:0.65rem;color:var(--primary);text-transform:uppercase;">Data Gathering</strong><p style="font-size:0.75rem;margin:0.25rem 0 0;color:var(--muted-foreground);">Count SKUs gathered</p></div>
                <div style="padding:0.75rem;border-right:1px solid var(--border);"><strong style="font-size:0.65rem;color:var(--primary);text-transform:uppercase;">Update Listings</strong><p style="font-size:0.75rem;margin:0.25rem 0 0;color:var(--muted-foreground);">Count updated SKUs</p></div>
                <div style="padding:0.75rem;border-right:1px solid var(--border);"><strong style="font-size:0.65rem;color:var(--primary);text-transform:uppercase;">Other</strong><p style="font-size:0.75rem;margin:0.25rem 0 0;color:var(--muted-foreground);">Canva = 1 · Pending = # of SKUs</p></div>
                <div style="padding:0.75rem;"><strong style="font-size:0.65rem;color:var(--primary);text-transform:uppercase;">Remarks</strong><p style="font-size:0.75rem;margin:0.25rem 0 0;color:var(--muted-foreground);">Describe what you did in OTHER</p></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-flat-primary" onclick="closeModal('tutorialModal')" style="height:40px;font-size:0.85rem;">Got it!</button>
        </div>
    </div>
</div>

<script>
function stepTask(id, delta) {
    var input = document.getElementById(id);
    var val = parseInt(input.value) || 0;
    val = Math.max(0, val + delta);
    input.value = val;
    updateTotal();
}
function updateTotal() {
    var tasks = ['task_1','task_2','task_3','task_4','task_5'];
    var total = tasks.reduce(function(sum, id) {
        return sum + (parseInt(document.getElementById(id).value) || 0);
    }, 0);
    document.getElementById('eodTotal').textContent = total;
}
function updateRemarks(el) {
    var len = el.value.length;
    document.getElementById('remarksChar').textContent = len + '/500';
}
</script>
@endsection
