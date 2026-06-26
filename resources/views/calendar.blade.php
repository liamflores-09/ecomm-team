@extends('layouts.app')

@section('title', 'Calendar — Ecomm Dept')
@section('has-sidebar', true)

@section('styles')
<style>
    /* ── Form base ───────────────────────────────────────────── */
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    .form-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); }
    .form-input {
        height: 42px; padding: 0 0.875rem; box-sizing: border-box;
        background: var(--muted); border: 2px solid transparent; border-radius: 9px;
        font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500;
        color: var(--fg); outline: none; transition: all 0.15s; width: 100%;
    }
    .form-input:focus { border-color: var(--primary); background: var(--white); }
    .form-input::placeholder { color: var(--gray-400); }
    input[type="datetime-local"].form-input,
    input[type="date"].form-input { padding: 0 0.625rem; font-size: 0.84rem; }
    .form-textarea {
        padding: 0.625rem 0.875rem; box-sizing: border-box;
        background: var(--muted); border: 2px solid transparent; border-radius: 9px;
        font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500;
        color: var(--fg); outline: none; resize: vertical; min-height: 88px;
        transition: all 0.15s; width: 100%;
    }
    .form-textarea:focus { border-color: var(--primary); background: var(--white); }
    /* Match x-select trigger to inputs */
    #evDrawer .app-dd .dd-trigger,
    #tkDrawer .app-dd .dd-trigger { height: 42px; font-size: 0.9rem; padding: 0 0.875rem; }

    /* ── Layout ──────────────────────────────────────────────── */
    .cal-layout {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 1.25rem;
        align-items: flex-start;
    }

    /* ── Left panel ──────────────────────────────────────────── */
    .cal-panel {
        display: flex; flex-direction: column; gap: 0.875rem;
    }
    .cal-panel-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 10px; overflow: hidden;
    }
    .cal-panel-card-hd {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 0.875rem; border-bottom: 1px solid var(--border);
    }
    .cal-panel-card-hd span {
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--muted-foreground);
    }
    .cal-panel-add {
        width: 20px; height: 20px; border-radius: 50%; border: none;
        background: transparent; color: var(--muted-foreground); font-size: 1rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; line-height: 1; padding: 0;
    }
    .cal-panel-add:hover { background: var(--primary); color: white; }

    .cal-cat-list { list-style: none; margin: 0; padding: 0.3rem 0; }
    .cal-cat-item {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.35rem 0.875rem; cursor: pointer;
        transition: background 0.1s; user-select: none;
    }
    .cal-cat-item:hover { background: var(--muted); }
    .cal-cat-dot {
        width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0;
        transition: opacity 0.15s;
    }
    .cal-cat-item:not(.active) .cal-cat-dot { opacity: 0.3; }
    .cal-cat-name {
        font-size: 0.8rem; font-weight: 500; color: var(--fg); flex: 1;
        transition: opacity 0.15s;
    }
    .cal-cat-item:not(.active) .cal-cat-name { opacity: 0.45; }
    .cal-cat-del {
        opacity: 0; width: 16px; height: 16px; border: none; background: none;
        color: var(--muted-foreground); cursor: pointer; font-size: 0.65rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 3px; transition: all 0.15s; padding: 0; flex-shrink: 0;
    }
    .cal-cat-item:hover .cal-cat-del { opacity: 1; }
    .cal-cat-del:hover { background: var(--destructive); color: white; }

    /* ── New category inline form ────────────────────────────── */
    .cal-newcat {
        border-top: 1px solid var(--border); padding: 0.875rem;
        display: none; flex-direction: column; gap: 0.625rem;
    }
    .cal-newcat.open { display: flex; }
    .cal-newcat-label { font-size: 0.72rem; font-weight: 700; color: var(--fg); }
    .cal-color-swatches { display: flex; gap: 0.375rem; flex-wrap: wrap; }
    .cal-swatch {
        width: 20px; height: 20px; border-radius: 50%; cursor: pointer;
        border: 2px solid transparent; transition: border-color 0.15s;
    }
    .cal-swatch.selected { border-color: var(--fg); }
    .cal-newcat-actions { display: flex; gap: 0.5rem; }
    .cal-newcat-actions button { flex: 1; height: 30px; font-size: 0.78rem; border-radius: 6px; }

    /* ── Main calendar card ──────────────────────────────────── */
    .cal-main {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 10px; overflow: hidden; min-width: 0;
    }
    .cal-toolbar {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.75rem 1rem; border-bottom: 1px solid var(--border);
    }
    .cal-title {
        font-size: 0.95rem; font-weight: 700; color: var(--fg); flex: 1;
        min-width: 140px;
    }
    .cal-btn {
        height: 32px; padding: 0 0.75rem; border: 1px solid var(--border);
        border-radius: 7px; background: var(--muted); font-family: var(--p-font-family-sans);
        font-size: 0.78rem; font-weight: 600; color: var(--fg); cursor: pointer;
        transition: all 0.15s; white-space: nowrap;
    }
    .cal-btn:hover { border-color: var(--primary); color: var(--primary); }
    .cal-icon-btn {
        width: 32px; height: 32px; border: 1px solid var(--border); border-radius: 7px;
        background: var(--muted); color: var(--fg); cursor: pointer; font-size: 0.72rem;
        display: flex; align-items: center; justify-content: center; transition: all 0.15s;
    }
    .cal-icon-btn:hover { border-color: var(--primary); color: var(--primary); }
    .cal-view-group {
        display: flex; border: 1px solid var(--border); border-radius: 7px; overflow: hidden;
    }
    .cal-view-btn {
        height: 32px; padding: 0 0.65rem; border: none; background: var(--muted);
        font-family: var(--p-font-family-sans); font-size: 0.75rem; font-weight: 600;
        color: var(--muted-foreground); cursor: pointer;
        border-right: 1px solid var(--border); transition: all 0.15s;
    }
    .cal-view-btn:last-child { border-right: none; }
    .cal-view-btn.active { background: var(--primary); color: white; }
    #fc-wrap { padding: 0.875rem 1rem 1rem; }

    /* ── FullCalendar overrides ──────────────────────────────── */
    .fc { font-family: var(--p-font-family-sans) !important; }
    .fc .fc-toolbar { display: none !important; }
    .fc-theme-standard td, .fc-theme-standard th,
    .fc-theme-standard .fc-scrollgrid { border-color: var(--border) !important; }
    .fc .fc-col-header-cell {
        background: var(--muted); padding: 0.25rem 0;
    }
    .fc .fc-col-header-cell-cushion {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.04em; color: var(--muted-foreground); text-decoration: none;
        padding: 6px 8px;
    }
    .fc .fc-daygrid-day-number {
        font-size: 0.78rem; font-weight: 600; color: var(--fg);
        text-decoration: none; padding: 4px 7px;
    }
    .fc .fc-daygrid-day.fc-day-today {
        background: color-mix(in srgb, var(--primary) 7%, transparent);
    }
    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: var(--primary); color: white; border-radius: 50%;
        width: 26px; height: 26px; display: flex; align-items: center;
        justify-content: center; padding: 0;
    }
    .fc .fc-daygrid-day.fc-day-other .fc-daygrid-day-number { color: var(--muted-foreground); }
    .fc .fc-event {
        border: none; border-radius: 4px; font-size: 0.72rem;
        font-weight: 600; cursor: pointer; padding: 1px 5px;
    }
    .fc .fc-event:hover { filter: brightness(1.08); }
    .fc .fc-list-event:hover td { background: var(--muted); }
    .fc .fc-list-day-cushion { background: var(--muted); }
    .fc .fc-list-event-title a { color: var(--fg); text-decoration: none; font-weight: 600; }
    .fc .fc-timegrid-slot { height: 2.5rem; }
    .fc .fc-scrollgrid-section-sticky > * { background: var(--card); }

    /* ── Event popup ─────────────────────────────────────────── */
    .cal-popup {
        position: fixed; z-index: 600; background: var(--card);
        border: 1px solid var(--border); border-radius: 10px; width: 288px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15); display: none;
    }
    .cal-popup.open { display: block; animation: fadeInUp 0.15s ease-out; }
    .cal-popup-top {
        padding: 0.875rem 1rem 0.75rem;
        display: flex; align-items: flex-start; gap: 0.625rem;
        border-bottom: 1px solid var(--border);
    }
    .cal-popup-stripe { width: 4px; border-radius: 2px; min-height: 36px; flex-shrink: 0; margin-top: 2px; }
    .cal-popup-title { font-size: 0.9rem; font-weight: 700; color: var(--fg); flex: 1; line-height: 1.35; }
    .cal-popup-x {
        width: 22px; height: 22px; border: none; background: none;
        color: var(--muted-foreground); cursor: pointer; font-size: 0.7rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 4px; flex-shrink: 0; transition: background 0.1s; padding: 0;
    }
    .cal-popup-x:hover { background: var(--muted); }
    .cal-popup-body { padding: 0.75rem 1rem; display: flex; flex-direction: column; gap: 0.45rem; }
    .cal-popup-row { display: flex; gap: 0.5rem; font-size: 0.78rem; align-items: flex-start; }
    .cal-popup-row i { color: var(--muted-foreground); width: 13px; text-align: center; flex-shrink: 0; margin-top: 2px; }
    .cal-popup-row span { color: var(--fg); line-height: 1.4; }
    .cal-popup-actions {
        display: flex; gap: 0.5rem;
        padding: 0.625rem 1rem; border-top: 1px solid var(--border);
    }
    .cal-popup-actions button {
        flex: 1; height: 30px; border: none; border-radius: 6px;
        font-family: var(--p-font-family-sans); font-size: 0.75rem; font-weight: 600;
        cursor: pointer; transition: all 0.15s; display: flex;
        align-items: center; justify-content: center; gap: 0.3rem;
    }
    .cal-pa-edit { background: var(--muted); color: var(--fg); }
    .cal-pa-edit:hover { background: var(--primary); color: white; }
    .cal-pa-del  { background: var(--muted); color: var(--fg); }
    .cal-pa-del:hover { background: var(--destructive); color: white; }

    /* ── Task drawer ─────────────────────────────────────────── */
    .cal-drawer-backdrop {
        display: none; position: fixed; inset: 0; z-index: 700;
    }
    .cal-drawer-backdrop.open { display: block; }
    .cal-drawer {
        position: fixed; top: 0; right: -400px; width: 360px; height: 100vh;
        background: var(--card); border-left: 1px solid var(--border);
        z-index: 701; display: flex; flex-direction: column;
        box-shadow: -6px 0 32px rgba(0,0,0,0.12);
        transition: right 0.25s cubic-bezier(0.4,0,0.2,1);
    }
    .cal-drawer.open { right: 0; }
    .cal-drawer-bar {
        height: 4px; flex-shrink: 0;
    }
    .cal-drawer-hd {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.75rem 1rem; border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
    .cal-drawer-hd-title {
        flex: 1; font-size: 1rem; font-weight: 700; color: var(--fg); line-height: 1.35;
    }
    .cal-drawer-hd-x {
        width: 26px; height: 26px; border: none; background: var(--muted);
        border-radius: 6px; color: var(--muted-foreground); cursor: pointer;
        font-size: 0.72rem; display: flex; align-items: center; justify-content: center;
        transition: all 0.15s; padding: 0; flex-shrink: 0;
    }
    .cal-drawer-hd-x:hover { background: var(--secondary); }
    .cal-drawer-meta {
        padding: 0.875rem 1.125rem; display: flex; flex-direction: column;
        gap: 0.5rem; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
    .cal-drawer-meta-row { display: flex; gap: 0.625rem; font-size: 0.8rem; align-items: center; }
    .cal-drawer-meta-row i { color: var(--muted-foreground); width: 14px; text-align: center; flex-shrink: 0; }
    .cal-drawer-meta-row span { color: var(--fg); }
    .cal-drawer-tasks {
        flex: 1; overflow-y: auto; padding: 0.875rem 1.125rem;
        display: flex; flex-direction: column; gap: 0.25rem;
    }
    .cal-drawer-tasks-hd {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.625rem;
    }
    .cal-drawer-tasks-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--muted-foreground);
    }
    .cal-drawer-progress {
        font-size: 0.72rem; font-weight: 700; color: var(--primary);
    }
    .cal-drawer-progress-bar {
        height: 4px; background: var(--muted); border-radius: 2px;
        margin-bottom: 0.75rem; overflow: hidden;
    }
    .cal-drawer-progress-fill {
        height: 100%; background: var(--primary); border-radius: 2px;
        transition: width 0.3s ease;
    }
    .cal-sub-row {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.5rem 0.625rem; border-radius: 7px; cursor: pointer;
        transition: background 0.1s;
    }
    .cal-sub-row:hover { background: var(--muted); }
    .cal-sub-check {
        width: 18px; height: 18px; border-radius: 5px; flex-shrink: 0;
        border: 2px solid var(--border); display: flex; align-items: center;
        justify-content: center; transition: all 0.15s;
    }
    .cal-sub-check.done { background: var(--primary); border-color: var(--primary); }
    .cal-sub-check.done::after {
        content: ''; width: 5px; height: 8px; border: 2px solid white;
        border-top: none; border-left: none; transform: rotate(45deg) translate(-1px,-1px);
        display: block;
    }
    .cal-sub-title {
        font-size: 0.82rem; font-weight: 500; color: var(--fg); flex: 1; line-height: 1.4;
        transition: all 0.15s;
    }
    .cal-sub-title.done { text-decoration: line-through; opacity: 0.45; }
    .cal-drawer-no-subtasks {
        font-size: 0.8rem; color: var(--muted-foreground); text-align: center;
        padding: 1.5rem 0;
    }
    .cal-drawer-ft {
        display: flex; gap: 0.625rem; padding: 0.875rem 1.125rem;
        border-top: 1px solid var(--border); flex-shrink: 0;
    }
    .cal-drawer-ft button {
        flex: 1; height: 38px; border: none; border-radius: 8px;
        font-family: var(--p-font-family-sans); font-size: 0.82rem; font-weight: 600;
        cursor: pointer; transition: all 0.15s; display: flex;
        align-items: center; justify-content: center; gap: 0.375rem;
    }
    .cal-drawer-edit { background: var(--muted); color: var(--fg); }
    .cal-drawer-edit:hover { background: var(--primary); color: white; }
    .cal-drawer-del  { background: var(--muted); color: var(--fg); }
    .cal-drawer-del:hover  { background: var(--destructive); color: white; }
    .cal-drawer-cancel { background: var(--muted); color: var(--fg); }
    .cal-drawer-cancel:hover { background: var(--secondary); }
    .cal-drawer-save   { background: var(--primary); color: white; }
    .cal-drawer-save:hover   { filter: brightness(1.1); }

    /* ── Form drawers (event + task) ────────────────────────── */
    .cal-ev-drawer {
        position: fixed; top: 0; right: -460px; width: 420px; height: 100vh;
        background: var(--card); border-left: 1px solid var(--border);
        z-index: 701; display: flex; flex-direction: column;
        box-shadow: -6px 0 32px rgba(0,0,0,0.12);
        transition: right 0.25s cubic-bezier(0.4,0,0.2,1);
    }
    .cal-ev-drawer.open { right: 0; }
    .cal-ev-drawer-body {
        flex: 1; overflow-y: auto; padding: 1.125rem 1.25rem;
        display: flex; flex-direction: column; gap: 0.875rem;
    }
    /* Prevent datetime-local from blowing out equal grid columns */
    .cal-form-grid {
        display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 0.625rem;
    }
    .cal-form-grid .form-group { min-width: 0; }

    /* ── Attendees ───────────────────────────────────────────── */
    .cal-attendees-wrap {
        display: flex; flex-wrap: wrap; gap: 0.375rem; min-height: 38px;
        padding: 0.375rem 0.5rem; background: var(--muted); border-radius: 8px;
        border: 2px solid transparent; cursor: text; transition: border-color 0.15s;
        position: relative;
    }
    .cal-attendees-wrap:focus-within { border-color: var(--primary); background: var(--white); }
    .cal-chip {
        display: flex; align-items: center; gap: 0.25rem; height: 24px;
        padding: 0 0.4rem 0 0.5rem; background: var(--card); border: 1px solid var(--border);
        border-radius: 5px; font-size: 0.73rem; font-weight: 600; color: var(--fg);
    }
    .cal-chip button {
        border: none; background: none; color: var(--muted-foreground); cursor: pointer;
        font-size: 0.6rem; padding: 0; display: flex; align-items: center; line-height: 1;
    }
    .cal-chip button:hover { color: var(--destructive); }
    .cal-att-input {
        border: none; background: none; outline: none;
        font-family: var(--p-font-family-sans); font-size: 0.8rem;
        color: var(--fg); min-width: 80px; flex: 1;
    }
    .cal-att-dd {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: var(--card); border: 1px solid var(--border); border-radius: 8px;
        z-index: 10; max-height: 150px; overflow-y: auto; display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .cal-att-dd.open { display: block; }
    .cal-att-opt {
        padding: 0.4rem 0.75rem; font-size: 0.8rem; font-weight: 500;
        cursor: pointer; color: var(--fg); transition: background 0.1s;
    }
    .cal-att-opt:hover { background: var(--muted); }

    @media (max-width: 860px) {
        .cal-layout { grid-template-columns: 1fr; }
        .cal-panel { flex-direction: row; flex-wrap: wrap; }
        .cal-panel-card { flex: 1; min-width: 200px; }
    }
</style>
@endsection

@section('content')
@php $user = Auth::user(); @endphp

<x-sidebar :isAdmin="$user->isAdmin()" active="calendar" />

<div class="main-content">

    <div class="top-bar anim-up">
        <div>
            <h2>Calendar</h2>
            <p>Team events, meetings, and appointments</p>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button class="btn-flat-secondary" style="height:38px;padding:0 1rem;font-size:0.85rem;" onclick="openTaskModal()">
                <i class="fas fa-list-check"></i> Task
            </button>
            <button class="btn-flat-primary" style="height:38px;padding:0 1rem;font-size:0.85rem;" onclick="openEventModal()">
                <i class="fas fa-plus"></i> Event
            </button>
        </div>
    </div>

    <div class="cal-layout anim-up d1">

        {{-- ── Left panel ─────────────────────────────────── --}}
        <div class="cal-panel">

            <div class="cal-panel-card">
                <div class="cal-panel-card-hd">
                    <span>My Calendars</span>
                    @if($user->isAdmin())
                    <button class="cal-panel-add" title="Add calendar" onclick="toggleNewCat()">+</button>
                    @endif
                </div>
                <ul class="cal-cat-list" id="catList">
                    @foreach($categories as $cat)
                    <li class="cal-cat-item active"
                        data-id="{{ $cat->id }}"
                        onclick="toggleCat(this)"
                        style="--cc: {{ $cat->color }}">
                        <div class="cal-cat-dot" style="background:{{ $cat->color }};"></div>
                        <span class="cal-cat-name">{{ $cat->name }}</span>
                        @if($user->isAdmin())
                        <button class="cal-cat-del" title="Delete" onclick="deleteCat(event, {{ $cat->id }}, this)">
                            <i class="fas fa-times"></i>
                        </button>
                        @endif
                    </li>
                    @endforeach
                </ul>

                @if($user->isAdmin())
                <div class="cal-newcat" id="newCatForm">
                    <div class="cal-newcat-label">New Calendar</div>
                    <input type="text" id="newCatName" class="form-input" placeholder="Calendar name" style="height:34px;font-size:0.8rem;">
                    <div>
                        <div style="font-size:0.72rem;font-weight:600;color:var(--muted-foreground);margin-bottom:0.375rem;">Color</div>
                        <div class="cal-color-swatches" id="colorSwatches">
                            @foreach(['#6366f1','#10b981','#f59e0b','#f43f5e','#0ea5e9','#8b5cf6','#ec4899','#14b8a6','#f97316'] as $c)
                            <div class="cal-swatch{{ $c === '#6366f1' ? ' selected' : '' }}" data-color="{{ $c }}" style="background:{{ $c }};" onclick="pickColor('{{ $c }}', this)"></div>
                            @endforeach
                        </div>
                        <input type="hidden" id="newCatColor" value="#6366f1">
                    </div>
                    <div class="cal-newcat-actions">
                        <button class="btn-flat-primary" onclick="saveCat()">Save</button>
                        <button class="btn-flat-secondary" onclick="toggleNewCat()">Cancel</button>
                    </div>
                </div>
                @endif
            </div>

            @if($user->isAdmin())
            <div class="cal-panel-card" style="margin-top:0.75rem;">
                <div class="cal-panel-card-hd">
                    <span>Role Colors</span>
                </div>
                <ul class="cal-cat-list">
                    @foreach([
                        '#7c3aed' => 'Ecomm Head',
                        '#1e293b' => 'Manager',
                        '#ec4899' => 'Analyst',
                        '#0ea5e9' => 'Content',
                        '#f59e0b' => 'Graphics',
                        '#f43f5e' => 'Backend',
                        '#10b981' => 'Researcher',
                    ] as $color => $label)
                    <li style="display:flex;align-items:center;gap:0.5rem;padding:0.35rem 0.875rem;">
                        <div style="width:10px;height:10px;border-radius:3px;background:{{ $color }};flex-shrink:0;"></div>
                        <span class="cal-cat-name">{{ $label }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>

        {{-- ── Main calendar ───────────────────────────────── --}}
        <div class="cal-main">
            <div class="cal-toolbar">
                <div class="cal-title" id="calTitle"></div>
                <button class="cal-btn" onclick="calGo('today')">Today</button>
                <button class="cal-icon-btn" onclick="calGo('prev')"><i class="fas fa-chevron-left"></i></button>
                <button class="cal-icon-btn" onclick="calGo('next')"><i class="fas fa-chevron-right"></i></button>
                <div class="cal-view-group">
                    <button class="cal-view-btn active" data-view="dayGridMonth"  onclick="calView(this)">Month</button>
                    <button class="cal-view-btn"        data-view="timeGridWeek"  onclick="calView(this)">Week</button>
                    <button class="cal-view-btn"        data-view="timeGridDay"   onclick="calView(this)">Day</button>
                    <button class="cal-view-btn"        data-view="listWeek"      onclick="calView(this)">List</button>
                </div>
            </div>
            <div id="fc-wrap"></div>
        </div>

    </div>
</div>

{{-- ── Event detail popup ───────────────────────────────── --}}
<div class="cal-popup" id="calPopup">
    <div class="cal-popup-top">
        <div class="cal-popup-stripe" id="ppStripe"></div>
        <div class="cal-popup-title"  id="ppTitle"></div>
        <button class="cal-popup-x" onclick="closePopup()"><i class="fas fa-times"></i></button>
    </div>
    <div class="cal-popup-body" id="ppBody"></div>
    <div class="cal-popup-actions">
        <button class="cal-pa-edit" id="ppEditBtn"><i class="fas fa-pencil"></i> Edit</button>
        <button class="cal-pa-del"  id="ppDelBtn"><i class="fas fa-trash-can"></i> Delete</button>
    </div>
</div>

{{-- ── Task drawer ─────────────────────────────────────────── --}}
<div class="cal-drawer-backdrop" id="drawerBackdrop" onclick="closeDrawer()"></div>
<div class="cal-drawer" id="calDrawer">
    <div class="cal-drawer-bar" id="drawerBar"></div>
    <div class="cal-drawer-hd">
        <div class="cal-drawer-hd-title" id="drawerTitle"></div>
        <button class="cal-drawer-hd-x" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
    </div>
    <div class="cal-drawer-meta" id="drawerMeta"></div>
    <div class="cal-drawer-tasks" id="drawerTasks"></div>
    <div class="cal-drawer-ft">
        <button class="cal-drawer-edit" id="drawerEditBtn"><i class="fas fa-pencil"></i> Edit</button>
        <button class="cal-drawer-del"  id="drawerDelBtn"><i class="fas fa-trash-can"></i> Delete</button>
    </div>
</div>

{{-- ── Create / Edit event drawer ───────────────────────── --}}
<div class="cal-drawer-backdrop" id="evDrawerBackdrop" onclick="closeEvDrawer()"></div>
<div class="cal-ev-drawer" id="evDrawer">
    <div class="cal-drawer-hd">
        <div class="cal-drawer-hd-title" id="evDrawerTitle">Create Event</div>
        <button class="cal-drawer-hd-x" onclick="closeEvDrawer()"><i class="fas fa-times"></i></button>
    </div>
    <div class="cal-ev-drawer-body">
        <input type="hidden" id="evId">
        <div class="form-group">
            <label class="form-label">Title <span style="color:var(--destructive)">*</span></label>
            <input type="text" id="evTitle" class="form-input" placeholder="Event title">
        </div>
        <div class="form-group">
            <label class="form-label">Calendar <span style="color:var(--destructive)">*</span></label>
            <x-select name="ev_category" id="evCategory"
                :options="$categories->pluck('name', 'id')->toArray()"
                :selected="$categories->first()?->id ?? ''"
                placeholder="Select calendar"
            />
        </div>
        <div class="cal-form-grid">
            <div class="form-group">
                <label class="form-label">Start <span style="color:var(--destructive)">*</span></label>
                <input type="datetime-local" id="evStart" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">End <span style="color:var(--destructive)">*</span></label>
                <input type="datetime-local" id="evEnd" class="form-input">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Location / Link <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <input type="text" id="evLocation" class="form-input" placeholder="Room, address, or meeting link">
        </div>
        <div class="form-group">
            <label class="form-label">Description <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <textarea id="evDesc" class="form-textarea" placeholder="Add notes or details..."></textarea>
        </div>
        <div class="form-group" style="position:relative;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <label class="form-label">Attendees <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
                <button type="button" onclick="selectAllAttendees()" style="font-size:0.68rem;font-weight:700;color:var(--primary);background:none;border:none;cursor:pointer;padding:0;text-transform:uppercase;letter-spacing:0.04em;">Select all</button>
            </div>
            <div class="cal-attendees-wrap" id="attWrap" onclick="document.getElementById('attInput').focus()">
                <input type="text" id="attInput" class="cal-att-input" placeholder="Search team member..."
                       autocomplete="off" oninput="attSearch(this.value)" onfocus="attSearch(this.value)">
            </div>
            <div class="cal-att-dd" id="attDd"></div>
        </div>
    </div>
    <div class="cal-drawer-ft">
        <button class="cal-drawer-cancel" onclick="closeEvDrawer()"><i class="fas fa-times"></i> Cancel</button>
        <button class="cal-drawer-save"   onclick="saveEvent()"><i class="fas fa-check"></i> Save</button>
    </div>
</div>

{{-- ── Create / Edit task drawer ─────────────────────────── --}}
<div class="cal-drawer-backdrop" id="tkDrawerBackdrop" onclick="closeTkDrawer()"></div>
<div class="cal-ev-drawer" id="tkDrawer">
    <div class="cal-drawer-hd">
        <div class="cal-drawer-hd-title" id="tkDrawerTitle">Create Task</div>
        <button class="cal-drawer-hd-x" onclick="closeTkDrawer()"><i class="fas fa-times"></i></button>
    </div>
    <div class="cal-ev-drawer-body">
        <input type="hidden" id="tkId">
        <div class="form-group">
            <label class="form-label">Title <span style="color:var(--destructive)">*</span></label>
            <input type="text" id="tkTitle" class="form-input" placeholder="Task title">
        </div>
        <div class="cal-form-grid">
            <div class="form-group">
                <label class="form-label">Calendar <span style="color:var(--destructive)">*</span></label>
                <x-select name="tk_category" id="tkCategory"
                    :options="$categories->pluck('name', 'id')->toArray()"
                    :selected="$categories->first()?->id ?? ''"
                    placeholder="Select"
                />
            </div>
            <div class="form-group">
                <label class="form-label">Due Date <span style="color:var(--destructive)">*</span></label>
                <input type="date" id="tkDueDate" class="form-input">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Assign to Role <span style="color:var(--destructive)">*</span></label>
            <x-select name="tk_role" id="tkRole"
                :options="['head'=>'Ecomm Head','manager'=>'Manager','analyst'=>'Analyst','content'=>'Content','graphics'=>'Graphics','backend'=>'Backend','researcher'=>'Researcher']"
                selected="content"
                placeholder="Select role"
            />
        </div>
        <div class="form-group">
            <label class="form-label">Description <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <textarea id="tkDesc" class="form-textarea" placeholder="Add notes or details..."></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Subtasks <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <div id="tkSubtaskList" style="display:flex;flex-direction:column;gap:0.375rem;margin-bottom:0.25rem;"></div>
            <button type="button" onclick="addSubtaskRow()"
                style="height:30px;padding:0 0.75rem;border:1px dashed var(--border);border-radius:7px;background:transparent;font-family:var(--p-font-family-sans);font-size:0.75rem;font-weight:600;color:var(--muted-foreground);cursor:pointer;width:100%;transition:all 0.15s;"
                onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted-foreground)'">
                <i class="fas fa-plus" style="font-size:0.65rem;"></i> Add subtask
            </button>
        </div>
    </div>
    <div class="cal-drawer-ft">
        <button class="cal-drawer-cancel" onclick="closeTkDrawer()"><i class="fas fa-times"></i> Cancel</button>
        <button class="cal-drawer-save"   onclick="saveTask()"><i class="fas fa-check"></i> Save</button>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
var _cal;
var _activeCats    = @json($categories->pluck('id')->toArray());
var _allUsers      = @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name]));
var _selAttendees  = [];
var _editId        = null;
var _csrf          = '{{ csrf_token() }}';
var _eventsUrl     = '{{ route("calendar.events") }}';
var _firstCatId    = {{ $categories->first()?->id ?? 'null' }};

document.addEventListener('DOMContentLoaded', function() {
    _cal = new FullCalendar.Calendar(document.getElementById('fc-wrap'), {
        initialView:    'dayGridMonth',
        headerToolbar:  false,
        height:         'auto',
        lazyFetching:   false,
        events: function(info, success, fail) {
            var url = _eventsUrl + '?start=' + info.startStr + '&end=' + info.endStr;
            _activeCats.forEach(function(id) { url += '&categories[]=' + id; });
            fetch(url).then(function(r) { return r.json(); }).then(success).catch(fail);
        },
        eventClick: function(info) {
            info.jsEvent.stopPropagation();
            showPopup(info.event, info.el);
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps.type === 'task' && info.event.extendedProps.status === 'done') {
                var title = info.el.querySelector('.fc-event-title');
                if (title) title.style.textDecoration = 'line-through';
                info.el.style.opacity = '0.55';
            }
        },
        dateClick: function(info) { openEventModal(info.dateStr); },
        datesSet:  function(info) { document.getElementById('calTitle').textContent = info.view.title; },
        eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
    });
    _cal.render();
});

// ── Nav ──────────────────────────────────────────────────
function calGo(cmd) {
    if (cmd === 'today') _cal.today();
    else if (cmd === 'prev') _cal.prev();
    else _cal.next();
}
function calView(btn) {
    _cal.changeView(btn.dataset.view);
    document.querySelectorAll('.cal-view-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
}

// ── Category toggle ──────────────────────────────────────
function toggleCat(item) {
    var id  = parseInt(item.dataset.id);
    var idx = _activeCats.indexOf(id);
    if (idx > -1) { _activeCats.splice(idx, 1); item.classList.remove('active'); }
    else          { _activeCats.push(id);        item.classList.add('active'); }
    _cal.refetchEvents();
}

// ── New category ─────────────────────────────────────────
function toggleNewCat() {
    var f = document.getElementById('newCatForm');
    f.classList.toggle('open');
    if (f.classList.contains('open')) {
        document.getElementById('newCatName').value = '';
        setTimeout(function() { document.getElementById('newCatName').focus(); }, 50);
    }
}
function pickColor(color, el) {
    document.getElementById('newCatColor').value = color;
    document.querySelectorAll('.cal-swatch').forEach(function(s) { s.classList.remove('selected'); });
    el.classList.add('selected');
}
function saveCat() {
    var name  = document.getElementById('newCatName').value.trim();
    var color = document.getElementById('newCatColor').value;
    if (!name) { document.getElementById('newCatName').focus(); return; }
    fetch('/calendar/categories', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
        body:    JSON.stringify({ name: name, color: color }),
    }).then(function(r) { return r.json(); }).then(function(d) {
        if (!d.success) return;
        var cat = d.category;
        _activeCats.push(cat.id);
        var li = document.createElement('li');
        li.className = 'cal-cat-item active';
        li.dataset.id = cat.id;
        li.style.setProperty('--cc', cat.color);
        li.setAttribute('onclick', 'toggleCat(this)');
        li.innerHTML =
            '<div class="cal-cat-dot" style="background:' + cat.color + ';"></div>' +
            '<span class="cal-cat-name">' + esc(cat.name) + '</span>' +
            '<button class="cal-cat-del" onclick="deleteCat(event,' + cat.id + ',this)"><i class="fas fa-times"></i></button>';
        document.getElementById('catList').appendChild(li);
        toggleNewCat();
    });
}
function deleteCat(e, id, btn) {
    e.stopPropagation();
    showConfirm('Delete Calendar', 'All events in this calendar will also be permanently deleted.', 'Delete', function() {
        fetch('/calendar/categories/' + id, {
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': _csrf },
        }).then(function() {
            btn.closest('.cal-cat-item').remove();
            var i = _activeCats.indexOf(id);
            if (i > -1) _activeCats.splice(i, 1);
            _cal.refetchEvents();
        });
    });
}

// ── Task drawer ──────────────────────────────────────────
var _drawerEv = null;
function openDrawer(ev) {
    _drawerEv = ev;
    renderDrawer(ev);
    document.getElementById('calDrawer').classList.add('open');
    document.getElementById('drawerBackdrop').classList.add('open');
}
function closeDrawer() {
    document.getElementById('calDrawer').classList.remove('open');
    document.getElementById('drawerBackdrop').classList.remove('open');
    _drawerEv = null;
}
function renderDrawer(ev) {
    var p = ev.extendedProps;
    var rawTitle = ev.title.replace(/\s*\(\d+\/\d+\)$/, '');
    var subtasks = p.subtasks || [];
    var total    = subtasks.length;
    var done     = subtasks.filter(function(s) { return s.status === 'done'; }).length;

    document.getElementById('drawerBar').style.background   = ev.backgroundColor;
    document.getElementById('drawerTitle').textContent      = rawTitle;

    // Meta
    var meta = '';
    meta += metaRow('fas fa-calendar-day', ev.start ? new Date(ev.start).toLocaleDateString('en-US', {weekday:'short',month:'long',day:'numeric',year:'numeric'}) : '');
    meta += metaRow('fas fa-users',        roleLabel(p.assigned_role));
    meta += metaRow('fas fa-tag',          esc(p.category_name));
    if (p.description) meta += metaRow('fas fa-align-left', esc(p.description));
    document.getElementById('drawerMeta').innerHTML = meta;

    // Subtasks / toggle
    var tasksHtml = '';
    if (total > 0) {
        var pct = total > 0 ? Math.round((done / total) * 100) : 0;
        tasksHtml += '<div class="cal-drawer-tasks-hd">' +
            '<span class="cal-drawer-tasks-label">Subtasks</span>' +
            '<span class="cal-drawer-progress">' + done + ' / ' + total + '</span></div>';
        tasksHtml += '<div class="cal-drawer-progress-bar"><div class="cal-drawer-progress-fill" style="width:' + pct + '%"></div></div>';
        subtasks.forEach(function(s) {
            var isDone = s.status === 'done';
            tasksHtml += '<div class="cal-sub-row" onclick="subtaskToggle(' + s.id + ', this)">' +
                '<div class="cal-sub-check' + (isDone ? ' done' : '') + '" data-sub-id="' + s.id + '"></div>' +
                '<span class="cal-sub-title' + (isDone ? ' done' : '') + '">' + esc(s.title) + '</span>' +
                '</div>';
        });
    } else {
        // No subtasks — show single done toggle
        var isDone = p.status === 'done';
        tasksHtml += '<div class="cal-sub-row" onclick="subtaskToggle(' + p.db_id + ', this, true)">' +
            '<div class="cal-sub-check' + (isDone ? ' done' : '') + '" data-sub-id="' + p.db_id + '"></div>' +
            '<span class="cal-sub-title' + (isDone ? ' done' : '') + '">' + esc(rawTitle) + '</span>' +
            '</div>';
    }
    document.getElementById('drawerTasks').innerHTML = tasksHtml;

    // Footer buttons
    document.getElementById('drawerEditBtn').onclick = function() { closeDrawer(); editTask(ev); };
    document.getElementById('drawerDelBtn').onclick  = function() { closeDrawer(); delTask(p.db_id); };
}
function metaRow(icon, content) {
    return '<div class="cal-drawer-meta-row"><i class="' + icon + '"></i><span>' + content + '</span></div>';
}
function subtaskToggle(id, el, isParent) {
    var row   = el.closest('.cal-sub-row');
    var check = row.querySelector('.cal-sub-check');
    var title = row.querySelector('.cal-sub-title');
    var isDone = check.classList.contains('done');

    // Optimistic visual update
    check.classList.toggle('done', !isDone);
    title.classList.toggle('done', !isDone);

    // Update progress bar if there are subtasks
    if (!isParent && _drawerEv) {
        var subs    = _drawerEv.extendedProps.subtasks || [];
        var sub     = subs.find(function(s) { return s.id === id; });
        if (sub) sub.status = isDone ? 'pending' : 'done';
        var total   = subs.length;
        var doneNow = subs.filter(function(s) { return s.status === 'done'; }).length;
        var pct     = total > 0 ? Math.round((doneNow / total) * 100) : 0;
        var fill    = document.querySelector('.cal-drawer-progress-fill');
        var prog    = document.querySelector('.cal-drawer-progress');
        if (fill) fill.style.width = pct + '%';
        if (prog) prog.textContent = doneNow + ' / ' + total;
    }

    fetch('/calendar/tasks/' + id + '/toggle', {
        method: 'PATCH', headers: { 'X-CSRF-TOKEN': _csrf },
    }).then(function() { _cal.refetchEvents(); });
}

// ── Popup (events only) ──────────────────────────────────
function showPopup(ev, el) {
    if (ev.extendedProps.type === 'task') { openDrawer(ev); return; }
    closePopup();
    var p = ev.extendedProps;
    document.getElementById('ppStripe').style.background = ev.backgroundColor;
    document.getElementById('ppTitle').textContent = ev.title;

    var html = '';
    var startStr = ev.start ? fmtDT(ev.start) : '';
    var endStr   = ev.end   ? fmtDT(ev.end)   : '';
    html += row('far fa-clock', startStr + (endStr && endStr !== startStr ? ' – ' + endStr : ''));
    if (p.category_name) html += row('fas fa-tag', esc(p.category_name));
    if (p.location) {
        var isUrl = /^https?:\/\//.test(p.location);
        html += row(isUrl ? 'fas fa-link' : 'fas fa-location-dot',
            isUrl ? '<a href="' + esc(p.location) + '" target="_blank" rel="noopener" style="color:var(--primary);">' + esc(p.location) + '</a>' : esc(p.location));
    }
    if (p.description) html += row('fas fa-align-left', esc(p.description));
    if (p.attendees && p.attendees.length)
        html += row('fas fa-users', p.attendees.map(function(a) { return esc(a.name); }).join(', '));
    document.getElementById('ppEditBtn').innerHTML = '<i class="fas fa-pencil"></i> Edit';
    document.getElementById('ppEditBtn').onclick = function() { closePopup(); editEvent(ev); };
    document.getElementById('ppDelBtn').onclick  = function() { closePopup(); delEvent(p.db_id); };

    document.getElementById('ppBody').innerHTML = html || '<div style="font-size:0.78rem;color:var(--muted-foreground);">No additional details.</div>';

    var popup = document.getElementById('calPopup');
    popup.classList.add('open');
    var rect = el.getBoundingClientRect();
    var pw = 288, ph = popup.offsetHeight || 200;
    var top  = rect.bottom + window.scrollY + 6;
    var left = rect.left   + window.scrollX;
    if (left + pw > window.innerWidth - 12) left = window.innerWidth - pw - 12;
    if (top + ph  > window.scrollY + window.innerHeight - 12) top = rect.top + window.scrollY - ph - 6;
    popup.style.top  = top  + 'px';
    popup.style.left = left + 'px';
}
function closePopup() { document.getElementById('calPopup').classList.remove('open'); }
function row(icon, content) {
    return '<div class="cal-popup-row"><i class="' + icon + '"></i><span>' + content + '</span></div>';
}
function roleLabel(role) {
    return { head:'Ecomm Head', manager:'Manager', analyst:'Analyst', content:'Content', graphics:'Graphics', backend:'Backend', researcher:'Researcher' }[role] || role;
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('#calPopup') && !e.target.closest('.fc-event')) closePopup();
});

// ── Event drawer ─────────────────────────────────────────
function openEvDrawer() {
    document.getElementById('evDrawer').classList.add('open');
    document.getElementById('evDrawerBackdrop').classList.add('open');
    setTimeout(function() { document.getElementById('evTitle').focus(); }, 60);
}
function closeEvDrawer() {
    document.getElementById('evDrawer').classList.remove('open');
    document.getElementById('evDrawerBackdrop').classList.remove('open');
}
function openEventModal(dateStr) {
    _editId = null; _selAttendees = [];
    document.getElementById('evDrawerTitle').textContent = 'Create Event';
    document.getElementById('evId').value       = '';
    document.getElementById('evTitle').value    = '';
    document.getElementById('evLocation').value = '';
    document.getElementById('evDesc').value     = '';
    document.getElementById('attInput').value   = '';
    renderChips();

    var now = dateStr ? new Date(dateStr + 'T09:00') : new Date();
    var end = new Date(now.getTime() + 60 * 60000);
    document.getElementById('evStart').value = dtLocal(now);
    document.getElementById('evEnd').value   = dtLocal(end);
    if (_firstCatId) appDdSetValue('evCategory', _firstCatId);

    openEvDrawer();
}
function editEvent(ev) {
    _editId = ev.extendedProps.db_id;
    _selAttendees = (ev.extendedProps.attendees || []).map(function(a) { return { id: a.id, name: a.name }; });
    document.getElementById('evDrawerTitle').textContent = 'Edit Event';
    document.getElementById('evId').value       = ev.extendedProps.db_id;
    document.getElementById('evTitle').value    = ev.title;
    document.getElementById('evLocation').value = ev.extendedProps.location    || '';
    document.getElementById('evDesc').value     = ev.extendedProps.description || '';
    document.getElementById('evStart').value    = dtLocal(ev.start);
    document.getElementById('evEnd').value      = dtLocal(ev.end || ev.start);
    appDdSetValue('evCategory', ev.extendedProps.category_id);
    renderChips();
    openEvDrawer();
}
function saveEvent() {
    var title = document.getElementById('evTitle').value.trim();
    var start = document.getElementById('evStart').value;
    var end   = document.getElementById('evEnd').value;
    if (!title) { showToast('Title is required.', 'error'); return; }
    if (!start || !end) { showToast('Start and end are required.', 'error'); return; }
    if (end < start)    { showToast('End must be after start.', 'error'); return; }

    var dbId   = document.getElementById('evId').value;
    var url    = dbId ? '/calendar/events/' + dbId : '/calendar/events';
    var method = dbId ? 'PUT' : 'POST';
    apiFetch(url, method, {
        category_id:    document.getElementById('evCategory').value,
        title:          title,
        start_datetime: start.replace('T', ' '),
        end_datetime:   end.replace('T', ' '),
        location:       document.getElementById('evLocation').value.trim(),
        description:    document.getElementById('evDesc').value.trim(),
        attendees:      _selAttendees.map(function(u) { return u.id; }),
    }, function() { closeEvDrawer(); _cal.refetchEvents(); });
}
function delEvent(id) {
    showConfirm('Delete Event', 'This event will be permanently removed from the calendar.', 'Delete', function() {
        fetch('/calendar/events/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf } })
            .then(function() { _cal.refetchEvents(); });
    });
}

// ── Task drawer ──────────────────────────────────────────
function openTkDrawer() {
    document.getElementById('tkDrawer').classList.add('open');
    document.getElementById('tkDrawerBackdrop').classList.add('open');
    setTimeout(function() { document.getElementById('tkTitle').focus(); }, 60);
}
function closeTkDrawer() {
    document.getElementById('tkDrawer').classList.remove('open');
    document.getElementById('tkDrawerBackdrop').classList.remove('open');
}
function openTaskModal(dateStr) {
    document.getElementById('tkDrawerTitle').textContent = 'Create Task';
    document.getElementById('tkId').value      = '';
    document.getElementById('tkTitle').value   = '';
    document.getElementById('tkDesc').value    = '';
    document.getElementById('tkDueDate').value = dateStr || new Date().toISOString().slice(0, 10);
    if (_firstCatId) appDdSetValue('tkCategory', _firstCatId);
    appDdSetValue('tkRole', 'content');
    document.getElementById('tkSubtaskList').innerHTML = '';
    openTkDrawer();
}
function editTask(ev) {
    var p = ev.extendedProps;
    var rawTitle = ev.title.replace(/\s*\(\d+\/\d+\)$/, '');
    document.getElementById('tkDrawerTitle').textContent = 'Edit Task';
    document.getElementById('tkId').value      = p.db_id;
    document.getElementById('tkTitle').value   = rawTitle;
    document.getElementById('tkDesc').value    = p.description || '';
    document.getElementById('tkDueDate').value = ev.start ? ev.start.toISOString().slice(0, 10) : '';
    appDdSetValue('tkCategory', p.category_id);
    appDdSetValue('tkRole', p.assigned_role);
    document.getElementById('tkSubtaskList').innerHTML = '';
    (p.subtasks || []).forEach(function(s) { addSubtaskRow(s.title, s.id); });
    openTkDrawer();
}
function addSubtaskRow(title, id) {
    var list = document.getElementById('tkSubtaskList');
    var row  = document.createElement('div');
    row.style.cssText = 'display:flex;align-items:center;gap:0.375rem;';
    row.innerHTML =
        '<input type="text" class="form-input tk-sub-input" data-sub-id="' + (id || '') + '" ' +
        'style="flex:1;" placeholder="Subtask title" value="' + esc(title || '') + '">' +
        '<button type="button" onclick="this.parentElement.remove()" ' +
        'style="width:28px;height:28px;border:none;background:var(--muted);border-radius:6px;cursor:pointer;color:var(--muted-foreground);font-size:0.7rem;flex-shrink:0;" ' +
        'onmouseover="this.style.background=\'var(--destructive)\';this.style.color=\'white\'" ' +
        'onmouseout="this.style.background=\'var(--muted)\';this.style.color=\'var(--muted-foreground)\'"><i class="fas fa-times"></i></button>';
    list.appendChild(row);
    row.querySelector('input').focus();
}
function getSubtaskRows() {
    return Array.from(document.querySelectorAll('.tk-sub-input')).map(function(inp) {
        return { id: inp.dataset.subId || null, title: inp.value.trim() };
    }).filter(function(s) { return s.title !== ''; });
}
function saveTask() {
    var title   = document.getElementById('tkTitle').value.trim();
    var dueDate = document.getElementById('tkDueDate').value;
    if (!title)   { showToast('Title is required.', 'error'); return; }
    if (!dueDate) { showToast('Due date is required.', 'error'); return; }

    var dbId   = document.getElementById('tkId').value;
    var url    = dbId ? '/calendar/tasks/' + dbId : '/calendar/tasks';
    var method = dbId ? 'PUT' : 'POST';
    apiFetch(url, method, {
        category_id:   document.getElementById('tkCategory').value,
        title:         title,
        due_date:      dueDate,
        assigned_role: document.getElementById('tkRole').value,
        description:   document.getElementById('tkDesc').value.trim(),
        subtasks:      getSubtaskRows(),
    }, function() { closeTkDrawer(); _cal.refetchEvents(); });
}
function toggleTask(id) {
    fetch('/calendar/tasks/' + id + '/toggle', {
        method: 'PATCH', headers: { 'X-CSRF-TOKEN': _csrf },
    }).then(function() { _cal.refetchEvents(); });
}
function delTask(id) {
    showConfirm('Delete Task', 'This task and all its subtasks will be permanently deleted.', 'Delete', function() {
        fetch('/calendar/tasks/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrf } })
            .then(function() { closeDrawer(); _cal.refetchEvents(); });
    });
}

// ── Attendees ────────────────────────────────────────────
function attSearch(q) {
    var dd = document.getElementById('attDd');
    var taken = _selAttendees.map(function(u) { return u.id; });
    var list  = _allUsers.filter(function(u) {
        return !taken.includes(u.id) && u.name.toLowerCase().includes(q.toLowerCase());
    });
    if (!list.length) { dd.classList.remove('open'); return; }
    dd.innerHTML = list.map(function(u) {
        return '<div class="cal-att-opt" onmousedown="addAtt(' + u.id + ',\'' + u.name.replace(/'/g,"\\'") + '\')">' + esc(u.name) + '</div>';
    }).join('');
    dd.classList.add('open');
}
function addAtt(id, name) {
    if (_selAttendees.find(function(u) { return u.id === id; })) return;
    _selAttendees.push({ id: id, name: name });
    document.getElementById('attInput').value = '';
    document.getElementById('attDd').classList.remove('open');
    renderChips();
}
function removeAtt(id) {
    _selAttendees = _selAttendees.filter(function(u) { return u.id !== id; });
    renderChips();
}
function selectAllAttendees() {
    @foreach($users as $u)
    if (!_selAttendees.find(function(u) { return u.id === {{ $u->id }}; })) {
        _selAttendees.push({ id: {{ $u->id }}, name: {{ Js::from($u->full_name) }} });
    }
    @endforeach
    renderChips();
}
function renderChips() {
    var wrap  = document.getElementById('attWrap');
    var input = document.getElementById('attInput');
    wrap.innerHTML = '';
    _selAttendees.forEach(function(u) {
        var c = document.createElement('div');
        c.className = 'cal-chip';
        c.innerHTML = esc(u.name) + '<button type="button" onclick="removeAtt(' + u.id + ')"><i class="fas fa-times"></i></button>';
        wrap.appendChild(c);
    });
    wrap.appendChild(input);
    input.placeholder = _selAttendees.length ? '' : 'Search team member...';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('#attWrap') && !e.target.closest('#attDd'))
        document.getElementById('attDd').classList.remove('open');
});

// ── Shared fetch helper ──────────────────────────────────
function apiFetch(url, method, payload, onSuccess) {
    fetch(url, {
        method:  method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf },
        body:    JSON.stringify(payload),
    }).then(function(r) {
        if (!r.ok) return r.json().then(function(e) { throw e; });
        return r.json();
    }).then(function(d) {
        if (d.success) onSuccess(d);
    }).catch(function(e) {
        var msg = (e && e.errors) ? Object.values(e.errors).flat().join(' ') : (e && e.message) ? e.message : 'Something went wrong.';
        showToast(msg, 'error');
    });
}

// ── Helpers ──────────────────────────────────────────────
function esc(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function dtLocal(d) {
    d = new Date(d);
    var z = function(n) { return String(n).padStart(2,'0'); };
    return d.getFullYear() + '-' + z(d.getMonth()+1) + '-' + z(d.getDate()) + 'T' + z(d.getHours()) + ':' + z(d.getMinutes());
}
function fmtDT(d) {
    return new Date(d).toLocaleString('en-US', { month:'short', day:'numeric', hour:'numeric', minute:'2-digit' });
}
</script>
@endsection
