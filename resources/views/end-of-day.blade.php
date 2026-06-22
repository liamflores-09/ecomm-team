@extends('layouts.app')

@section('title', 'End-of-Day Report — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='4' width='18' height='18' rx='2' ry='2'/><line x1='16' y1='2' x2='16' y2='6'/><line x1='8' y1='2' x2='8' y2='6'/><line x1='3' y1='10' x2='21' y2='10'/><path d='M8 14h.01'/><path d='M12 14h.01'/><path d='M16 14h.01'/><path d='M8 18h.01'/><path d='M12 18h.01'/></svg>">
@endsection

@section('styles')
<style>
    .eod-card {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .eod-card-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 1.25rem;
        background: var(--muted);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
    }

    .eod-card-header .t-icon {
        width: 24px;
        height: 24px;
        background: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.65rem;
    }

    .eod-card-body {
        padding: 1.25rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group.attendance-row {
        grid-column: 1 / -1;
    }
    .form-group.attendance-row .form-select {
        max-width: 240px;
    }

    .form-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
    }

    .form-input {
        height: 44px;
        padding: 0 0.875rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 8px;
        font-family: var(--p-font-family-sans);
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--fg);
        outline: none;
        transition: all 0.15s;
    }

    .form-input:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    .form-input[type="number"] {
        font-size: 1.1rem;
        font-weight: 700;
        text-align: center;
    }

    .form-input::placeholder {
        color: var(--gray-300);
    }

    .form-select {
        height: 44px;
        padding: 0 0.875rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 8px;
        font-family: var(--p-font-family-sans);
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--fg);
        outline: none;
        cursor: pointer;
        transition: all 0.15s;
        width: 100%;
        appearance: auto;
    }

    .form-select:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    .form-textarea {
        padding: 0.75rem 0.875rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 8px;
        font-family: var(--p-font-family-sans);
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--fg);
        outline: none;
        resize: vertical;
        min-height: 80px;
        transition: all 0.15s;
    }

    .form-textarea:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 2px solid var(--muted);
    }

    /* Recent logs table */
    .logs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .logs-table thead th {
        background: var(--muted);
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
        text-align: left;
    }

    .logs-table tbody td {
        padding: 0.75rem 1rem;
        border-top: 2px solid var(--muted);
        font-weight: 500;
    }

    .logs-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .logs-table .num {
        font-weight: 700;
        text-align: center;
    }

    .action-btns {
        display: flex;
        gap: 0.25rem;
    }

    .action-btn-sm {
        width: 28px;
        height: 28px;
        border: 2px solid var(--border);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.15s;
        background: transparent;
        color: var(--gray-400);
    }

    .action-btn-sm:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .action-btn-sm.btn-danger:hover {
        border-color: #DC2626;
        color: #DC2626;
    }

    .empty-logs {
        text-align: center;
        padding: 2rem;
        color: var(--gray-400);
        font-weight: 500;
        font-size: 0.85rem;
    }

    .empty-logs i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 0.5rem;
        color: var(--gray-300);
    }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: repeat(3, 1fr); }
        .logs-table-wrap { overflow-x: auto; }
        .logs-table { min-width: 600px; }
    }

    @media (max-width: 480px) {
        .form-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
<x-sidebar active="end-of-day" />

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>End-of-Day <span class="highlight">Report</span></h2>
            <p>Log your daily tasks and activities</p>
        </div>
        @if($user->role === 'content')
        <button type="button" class="btn-flat-secondary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="openModal('tutorialModal')">
            <i class="fas fa-circle-info"></i> How to Fill
        </button>
        @endif
    </div>

    @if (session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- Log Form -->
    <div class="eod-card anim-up d1">
        <div class="eod-card-header">
            <div class="t-icon"><i class="fas fa-pen"></i></div>
            {{ $existingLog ? 'Edit Today\'s Log' : 'Log Today\'s Tasks' }} — {{ now()->format('F j, Y') }}
        </div>
        <div class="eod-card-body">
            <form method="POST" action="{{ $existingLog ? route('daily-logs.update', $existingLog) : route('daily-logs.store') }}">
                @csrf
                @if($existingLog)
                @method('PUT')
                @endif

                <input type="hidden" name="date" value="{{ now()->toDateString() }}">

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

                <div class="form-actions">
                    <button type="submit" class="btn-flat-primary" style="height: 44px; padding: 0 1.5rem; font-size: 0.9rem;">
                        <i class="fas fa-check"></i> {{ $existingLog ? 'Update Log' : 'Save Log' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="eod-card anim-up d2">
        <div class="eod-card-header">
            <div class="t-icon" style="background: var(--secondary);"><i class="fas fa-clock"></i></div>
            Recent Logs
        </div>
        <div class="logs-table-wrap">
            @if($recentLogs->count())
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Attendance</th>
                        <th style="text-align: center;">New SKU</th>
                        <th style="text-align: center;">Var. SKU</th>
                        <th style="text-align: center;">Data Gather</th>
                        <th style="text-align: center;">Update</th>
                        <th style="text-align: center;">Other</th>
                        <th>Remarks</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                    <tr>
                        <td style="font-weight: 700;">{{ $log->date->format('M d, Y') }}</td>
                        <td>
                            @if($log->attendance)
                            <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.65rem; font-weight: 700; background: #FEF3C7; color: #92400E;">{{ $log->attendance }}</span>
                            @else
                            <span style="color: var(--gray-300);">—</span>
                            @endif
                        </td>
                    <td class="num">{{ $log->task_1 }}</td>
                    <td class="num">{{ $log->task_2 }}</td>
                    <td class="num">{{ $log->task_3 }}</td>
                    <td class="num">{{ $log->task_4 }}</td>
                    <td class="num">{{ $log->task_5 }}</td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--gray-500); font-size: 0.8rem;">{{ $log->remarks ?: '—' }}</td>
                        <td>
                            @if($log->date->toDateString() === now()->toDateString())
                            <div class="action-btns">
                                <form method="POST" action="{{ route('daily-logs.destroy', $log) }}" onsubmit="return confirm('Delete this log?');">
                                    @csrf
                                    @method('DELETE')
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
                No logs yet. Start by filling in today's tasks above.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tutorial Modal -->
<div class="modal-overlay" id="tutorialModal">
    <div class="modal-box" style="max-width: 760px;">
        <div class="modal-header">
            <h5 style="font-weight: 700; font-size: 1rem; margin: 0;">
                <i class="fas fa-circle-info" style="color: var(--primary); margin-right: 0.5rem;"></i>How to Fill Your EOD Report
            </h5>
            <button class="modal-close" onclick="closeModal('tutorialModal')"><i class="fas fa-times"></i></button>
        </div>
            <div class="modal-body" style="padding: 1.5rem;">

                <!-- Column Overview -->
                <h6 style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); margin-bottom: 0.75rem;">Column Overview</h6>
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; border: 2px solid var(--border); border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); background: var(--fg); color: white; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">New SKU</div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); background: var(--fg); color: white; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">Variation SKU</div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); background: var(--fg); color: white; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">Adv. Data Gathering</div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); background: var(--fg); color: white; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">Update Listings</div>
                    <div style="padding: 0.75rem; text-align: center; background: var(--fg); color: white; font-weight: 800; font-size: 0.65rem; text-transform: uppercase;">Other</div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); border-top: 2px solid var(--border);"><strong style="font-size: 0.7rem;">Parent/Single</strong><br><span style="font-size: 0.6rem; color: var(--gray-400);">New product posted</span></div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); border-top: 2px solid var(--border);"><strong style="font-size: 0.7rem;">Child/Variant</strong><br><span style="font-size: 0.6rem; color: var(--gray-400);">Variation posted</span></div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); border-top: 2px solid var(--border);"><strong style="font-size: 0.7rem;">Data Gathered</strong><br><span style="font-size: 0.6rem; color: var(--gray-400);">Research completed</span></div>
                    <div style="padding: 0.75rem; text-align: center; border-right: 2px solid var(--border); border-top: 2px solid var(--border);"><strong style="font-size: 0.7rem;">Updated Listings</strong><br><span style="font-size: 0.6rem; color: var(--gray-400);">Old SKUs updated</span></div>
                    <div style="padding: 0.75rem; text-align: center; border-top: 2px solid var(--border);"><strong style="font-size: 0.7rem;">Extra Tasks</strong><br><span style="font-size: 0.6rem; color: var(--gray-400);">Canva, etc.</span></div>
                </div>

                <!-- Rules Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; margin-bottom: 1.5rem; border: 2px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <!-- New SKU & Variation -->
                    <div style="padding: 1rem; border-right: 2px solid var(--border); border-bottom: 2px solid var(--border);">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--fg);">
                            <div style="width: 28px; height: 28px; background: var(--fg); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem;"><i class="fas fa-box"></i></div>
                            <strong style="font-size: 0.7rem; text-transform: uppercase;">New SKU & Variation SKU</strong>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.8rem; color: var(--gray-600);">
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> <strong>New SKU (Parent/Single)</strong> — Each parent or single SKU = 1</li>
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> <strong>Variation SKU (Child)</strong> — Each variant = 1</li>
                        </ul>
                        <div style="margin-top: 0.5rem; padding: 0.375rem 0.625rem; background: var(--muted); border: 1px solid var(--border); border-radius: 4px; font-size: 0.65rem; font-weight: 600;">Example: 1 parent + 4 children = 1 NEW SKU, 4 VARIATION SKU</div>
                    </div>

                    <!-- Advance Data Gathering -->
                    <div style="padding: 1rem; border-bottom: 2px solid var(--border);">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--fg);">
                            <div style="width: 28px; height: 28px; background: var(--fg); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem;"><i class="fas fa-magnifying-glass"></i></div>
                            <strong style="font-size: 0.7rem; text-transform: uppercase;">Advance Data Gathering</strong>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.8rem; color: var(--gray-600);">
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> Count how many SKUs you data gathered</li>
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> Includes product research, specs, images</li>
                        </ul>
                        <div style="margin-top: 0.5rem; padding: 0.375rem 0.625rem; background: var(--muted); border: 1px solid var(--border); border-radius: 4px; font-size: 0.65rem; font-weight: 600;">Example: Data gathered 5 SKUs = 5</div>
                    </div>

                    <!-- Update Listings -->
                    <div style="padding: 1rem; border-right: 2px solid var(--border);">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--fg);">
                            <div style="width: 28px; height: 28px; background: var(--fg); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem;"><i class="fas fa-pencil"></i></div>
                            <strong style="font-size: 0.7rem; text-transform: uppercase;">Update Listings</strong>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.8rem; color: var(--gray-600);">
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> Updated Photos, Text, Long Description, Wrong SKU</li>
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> Count depends on how many you updated</li>
                        </ul>
                        <div style="margin-top: 0.5rem; padding: 0.375rem 0.625rem; background: var(--muted); border: 1px solid var(--border); border-radius: 4px; font-size: 0.65rem; font-weight: 600;">Example: Updated photos for 2 + corrected SKU for 1 = 3</div>
                    </div>

                    <!-- Other & Remarks -->
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--fg);">
                            <div style="width: 28px; height: 28px; background: var(--fg); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem;"><i class="fas fa-list-check"></i></div>
                            <strong style="font-size: 0.7rem; text-transform: uppercase;">Other & Remarks</strong>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.8rem; color: var(--gray-600);">
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> <strong>Canva Usage</strong> — OTHER = 1, Remarks: "Canva"</li>
                            <li style="padding: 0.25rem 0; display: flex; gap: 0.5rem;"><span style="color: #059669;">✓</span> <strong>Post Pending SKU</strong> — Remarks: "Post Pending SKU: #"</li>
                        </ul>
                        <div style="margin-top: 0.5rem; padding: 0.375rem 0.625rem; background: var(--muted); border: 1px solid var(--border); border-radius: 4px; font-size: 0.65rem; font-weight: 600;">Example: Canva → OTHER = 1, REMARKS = "Canva"</div>
                    </div>
                </div>

                <!-- Example Table -->
                <h6 style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); margin-bottom: 0.75rem;">Example EOD Report</h6>
                <div style="border: 2px solid var(--border); border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th style="background: var(--fg); color: white; padding: 0.625rem; font-size: 0.65rem; text-transform: uppercase; text-align: left;">New SKU</th>
                                <th style="background: var(--fg); color: white; padding: 0.625rem; font-size: 0.65rem; text-transform: uppercase; text-align: left;">Var. SKU</th>
                                <th style="background: var(--fg); color: white; padding: 0.625rem; font-size: 0.65rem; text-transform: uppercase; text-align: left;">Data Gather</th>
                                <th style="background: var(--fg); color: white; padding: 0.625rem; font-size: 0.65rem; text-transform: uppercase; text-align: left;">Update</th>
                                <th style="background: var(--fg); color: white; padding: 0.625rem; font-size: 0.65rem; text-transform: uppercase; text-align: left;">Other</th>
                                <th style="background: var(--fg); color: white; padding: 0.625rem; font-size: 0.65rem; text-transform: uppercase; text-align: left;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">2</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">5</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">1</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted); color: var(--gray-500);">Canva</td></tr>
                            <tr><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">1</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">3</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">4</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">3</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted); color: var(--gray-500);">Post Pending SKU: 3</td></tr>
                            <tr><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">4</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">8</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">2</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted); color: var(--gray-400);">—</td></tr>
                            <tr><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">6</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted); color: var(--gray-400);">—</td></tr>
                            <tr><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">2</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">0</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">3</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">4</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted);">1</td><td style="padding: 0.5rem; border-top: 1px solid var(--muted); color: var(--gray-500);">Canva</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Quick Reference -->
                <h6 style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); margin-bottom: 0.75rem;">Quick Reference</h6>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; border: 2px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <div style="padding: 0.75rem; border-right: 2px solid var(--border); border-bottom: 2px solid var(--border);"><strong style="font-size: 0.65rem; color: var(--primary); text-transform: uppercase;">New SKU</strong><p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-600);">Parent/Single = 1 each</p></div>
                    <div style="padding: 0.75rem; border-right: 2px solid var(--border); border-bottom: 2px solid var(--border);"><strong style="font-size: 0.65rem; color: var(--primary); text-transform: uppercase;">Variation SKU</strong><p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-600);">Child/Variant = 1 each</p></div>
                    <div style="padding: 0.75rem; border-bottom: 2px solid var(--border);"><strong style="font-size: 0.65rem; color: var(--primary); text-transform: uppercase;">Data Gathering</strong><p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-600);">Count SKUs gathered</p></div>
                    <div style="padding: 0.75rem; border-right: 2px solid var(--border);"><strong style="font-size: 0.65rem; color: var(--primary); text-transform: uppercase;">Update Listings</strong><p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-600);">Count updated SKUs</p></div>
                    <div style="padding: 0.75rem; border-right: 2px solid var(--border);"><strong style="font-size: 0.65rem; color: var(--primary); text-transform: uppercase;">Other</strong><p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-600);">Canva = 1<br>Pending = # of SKUs</p></div>
                    <div style="padding: 0.75rem;"><strong style="font-size: 0.65rem; color: var(--primary); text-transform: uppercase;">Remarks</strong><p style="font-size: 0.75rem; margin: 0.25rem 0 0; color: var(--gray-600);">Describe what you did in OTHER</p></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-primary" onclick="closeModal('tutorialModal')" style="height: 40px; font-size: 0.85rem;">Got it!</button>
            </div>
        </div>
    </div>
</div>
@endsection
