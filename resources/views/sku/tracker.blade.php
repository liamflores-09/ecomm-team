@extends('layouts.app')

@section('title', 'SKU Tracker')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z'/><line x1='7' y1='7' x2='7.01' y2='7'/></svg>">
@endsection

@section('styles')
<style>
    .sku-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
    .sku-kpi-card { background: var(--card); border-radius: 8px; padding: 1.5rem; border: 1px solid var(--border-light); border-top: 2px solid var(--primary); }
    .sku-kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .sku-kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .sku-kpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: white; background: var(--primary); flex-shrink: 0; }
    .sku-kpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1; font-family: 'Space Grotesk', sans-serif; color: var(--foreground); }
    .sku-kpi-range { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; margin-top: 0.2rem; }

    .sku-filter-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
    .sku-filter-group { display: flex; flex-direction: column; gap: 0.3rem; min-width: 160px; }
    .sku-filter-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--muted-foreground); }

    .sku-actions-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 0.875rem 1.25rem; margin-bottom: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
    .sku-actions-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .sku-actions-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--muted-foreground); }

    .sku-table-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; overflow-x: auto; }
    table.sku-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; white-space: nowrap; }
    table.sku-table th { text-align: left; padding: 0.6rem 0.6rem; border-bottom: 1px solid var(--border-light); color: var(--muted-foreground); font-size: 0.66rem; text-transform: uppercase; letter-spacing: 0.03em; background: var(--card); }
    table.sku-table td { padding: 0.3rem 0.5rem; border-bottom: 1px solid var(--border-light); max-width: 200px; }
    .sku-col-sticky-1, .sku-col-sticky-2, .sku-col-sticky-3 { position: sticky; z-index: 2; background: var(--card); }
    .sku-col-sticky-1 { left: 0; min-width: 120px; max-width: 120px; }
    .sku-col-sticky-2 { left: 120px; min-width: 150px; max-width: 150px; }
    .sku-col-sticky-3 { left: 270px; min-width: 140px; max-width: 140px; border-right: 1px solid var(--border-light); }
    /* Sticky cells all share z-index:2, and ties resolve by DOM order — so a later
       row's sticky cells can paint over an earlier row's open dropdown. Bump the
       open cell's own z-index while its dropdown is open to guarantee it wins. */
    .sku-dd-cell-active { z-index: 50 !important; }
    .sku-col-content { background: rgba(14,165,233,0.06); }
    [data-theme="dark"] .sku-col-content { background: rgba(14,165,233,0.1); }
    th.sku-col-content { background: rgba(14,165,233,0.12); }

    .sku-chip { display: inline-flex; padding: 0.18rem 0.6rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 700; }
    .sku-chip.done { background: rgba(34,197,94,0.12); color: var(--success); }
    .sku-chip.pending { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .sku-chip.none { background: var(--muted); color: var(--muted-foreground); }

    .sku-cell-input, .sku-cell-select { width: 100%; min-width: 90px; height: 30px; padding: 2px 6px; border: 1px solid transparent; background: transparent; font-family: inherit; font-size: 0.8rem; color: inherit; border-radius: 5px; transition: border-color 0.12s, background 0.12s; }
    .sku-cell-input:hover, .sku-cell-select:hover { border-color: var(--border-light); }
    .sku-cell-input:focus, .sku-cell-select:focus { border-color: var(--primary); background: var(--card); outline: none; }
    .sku-cell-input:disabled, .sku-cell-select:disabled { color: var(--muted-foreground); cursor: not-allowed; }
    .sku-cell-flash-ok { background: rgba(34,197,94,0.18) !important; }
    .sku-cell-flash-err { background: rgba(239,68,68,0.18) !important; }
    .sku-readonly-cell { color: var(--muted-foreground); font-size: 0.8rem; }

    .sku-table .app-dd .dd-trigger { height: 30px; padding: 0 6px; font-size: 0.8rem; border-radius: 5px; background: transparent; border-width: 1px; }
    .sku-table .app-dd .dd-trigger:hover { background: var(--card); }
    .sku-table .app-dd .dd-menu { font-size: 0.8rem; }
    .sku-table .app-dd .dd-item { padding: 0.4rem 0.5rem; font-size: 0.8rem; }
    .sku-table td { overflow: visible; }

    [data-theme="dark"] .sku-cell-input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(0.85); }

    #addRowForm { display: flex; gap: 0.6rem; align-items: flex-end; }
    #addRowForm input { width: 180px; }

    .bulk-add-step { background: var(--card); border: 1px solid var(--border-light); border-top: 2px solid var(--primary); border-radius: 10px; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .bulk-add-step-head { display: flex; gap: 0.625rem; align-items: flex-start; margin-bottom: 0.75rem; }
    .bulk-add-step-badge { width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: white; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(87,87,248,0.3); }
    .bulk-add-step-title { font-size: 0.86rem; font-weight: 700; color: var(--foreground); }
    .bulk-add-step-optional { font-size: 0.72rem; font-weight: 500; color: var(--muted-foreground); text-transform: none; }
    .bulk-add-step-desc { font-size: 0.78rem; color: var(--muted-foreground); margin: 0.2rem 0 0; line-height: 1.45; }

    .bulk-add-textarea {
        width: 100%; resize: vertical; box-sizing: border-box;
        border: 2px solid var(--border-light); border-radius: 8px;
        padding: 0.7rem 0.85rem; background: var(--muted); color: var(--fg);
        font-family: var(--p-font-family-sans); font-size: 0.85rem; line-height: 1.6;
        outline: none; transition: border-color 0.15s, background 0.15s;
    }
    .bulk-add-textarea:focus { border-color: var(--primary); background: var(--card); }
    .bulk-add-textarea::placeholder { color: var(--gray-300); }
    .bulk-add-textarea-readonly {
        font-family: ui-monospace, 'SF Mono', Menlo, Consolas, monospace; font-size: 0.76rem; line-height: 1.55;
        color: var(--muted-foreground); background: var(--muted); cursor: text;
    }
    .bulk-add-textarea-readonly:focus { border-color: var(--border-light); background: var(--muted); }
    .bulk-add-copy-btn { display: inline-flex; align-items: center; gap: 0.4rem; }
</style>
@endsection

@section('content')
<x-sidebar active="sku-tracker" :isAdmin="Auth::user()->isAdmin()" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>SKU <span class="highlight">Tracker</span></h2>
            <p>Product research to content posting pipeline — edits save automatically</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <div class="sku-kpi-grid anim-up d1">
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Total SKUs</span><div class="sku-kpi-icon"><i class="fas fa-box"></i></div></div>
            <div class="sku-kpi-value">{{ number_format($stats['total']) }}</div>
            @if($globalTotal !== null)
            <div class="sku-kpi-range">of {{ number_format($globalTotal) }} total</div>
            @endif
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Posted</span><div class="sku-kpi-icon"><i class="fas fa-circle-check"></i></div></div>
            <div class="sku-kpi-value">{{ number_format($stats['posted']) }}</div>
            @if($globalPosted !== null)
            <div class="sku-kpi-range">of {{ number_format($globalPosted) }} total</div>
            @endif
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Avg PR SLA</span><div class="sku-kpi-icon"><i class="fas fa-magnifying-glass"></i></div></div>
            <div class="sku-kpi-value">{{ $stats['avg_pr_sla'] }}d</div>
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Avg Content SLA</span><div class="sku-kpi-icon"><i class="fas fa-pen"></i></div></div>
            <div class="sku-kpi-value">{{ $stats['avg_content_sla'] }}d</div>
        </div>
    </div>

    <form method="GET" class="sku-filter-card anim-up d2">
        <div class="sku-filter-group">
            <span class="sku-filter-label">Search</span>
            <input type="text" name="brand" class="input-flat" placeholder="Brand or SKU..." value="{{ $filters['brand'] ?? '' }}" oninput="filterSkuRows(this.value)">
        </div>
        <div class="sku-filter-group">
            <span class="sku-filter-label">PR Status</span>
            <select name="pr_status" class="input-flat">
                <option value="">All</option>
                @foreach($prStatuses as $status)
                <option value="{{ $status }}" @selected(($filters['pr_status'] ?? '') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="sku-filter-group">
            <span class="sku-filter-label">Posted</span>
            <select name="posted" class="input-flat">
                <option value="">All</option>
                <option value="1" @selected(($filters['posted'] ?? '') === '1')>Posted</option>
                <option value="0" @selected(($filters['posted'] ?? '') === '0')>Not posted</option>
            </select>
        </div>
        <div class="sku-filter-group">
            <span class="sku-filter-label">Month</span>
            <select name="month" class="input-flat">
                <option value="" @selected($selectedMonth === '')>All Months</option>
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected($selectedMonth === $m)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-flat-secondary" style="height: 40px;">Filter</button>
    </form>

    @if($perms['can_create'])
    <div class="sku-actions-card anim-up d2">
        <form id="addRowForm" method="POST" action="{{ route('sku-tracker.store') }}">
            @csrf
            <div class="sku-actions-group">
                <span class="sku-actions-label">Brand</span>
                <input type="text" name="brand" class="input-flat" placeholder="Brand" required>
            </div>
            <div class="sku-actions-group">
                <span class="sku-actions-label">SKU</span>
                <input type="text" name="sku" class="input-flat" placeholder="SKU code" oninput="checkDuplicateSku(this.value)" required>
            </div>
            <button type="submit" class="btn-flat-primary" style="height: 40px;"><i class="fas fa-plus"></i> Add Row</button>
            <span id="addRowDuplicateWarning" style="display:none;color:#f59e0b;font-size:0.72rem;font-weight:600;align-self:center;">
                <i class="fas fa-triangle-exclamation"></i> SKU already exists — still addable.
            </span>
        </form>
        <button type="button" class="btn-flat-secondary" style="height: 40px;" onclick="openModal('bulkAddModal')" id="bulkAddModal-trigger">
            <i class="fas fa-file-import"></i> Bulk Add (JSON)
        </button>
    </div>
    @endif

    <div class="sku-table-card anim-up d3">
        <table class="sku-table">
            <thead>
                <tr>
                    <th class="sku-col-sticky-1">Brand</th><th class="sku-col-sticky-2">SKU</th><th class="sku-col-sticky-3">Variant</th>
                    <th>PR File Location</th><th>PR Assignee</th><th>PR Status</th><th>Ready for CVP</th><th>Remarks</th>
                    <th>PR Date Started</th><th>PR Date Completed</th><th>PR SLA</th>
                    <th class="sku-col-content">Content Assignee</th><th class="sku-col-content">Content Status</th>
                    <th class="sku-col-content">Content Date Started</th><th class="sku-col-content">Content Date Posted</th>
                    <th class="sku-col-content">Content SLA</th><th class="sku-col-content">Posted</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $variantOptions = collect(['' => '—'])->merge(collect($variants)->mapWithKeys(fn ($v) => [$v => $v]));
                    $prStatusOptions = collect(['' => '—'])->merge(collect($prStatuses)->mapWithKeys(fn ($s) => [$s => $s]));
                    $remarksOptionsMap = collect(['' => '—'])->merge(collect($remarksOptions)->mapWithKeys(fn ($r) => [$r => $r]));
                @endphp
                @forelse($skus as $sku)
                @php
                    $prAssigneeOptions = collect(['' => '—'])->merge($researchers->mapWithKeys(fn ($n) => [$n => $n]));
                    if ($sku->pr_assignee && !$researchers->contains($sku->pr_assignee)) {
                        $prAssigneeOptions[$sku->pr_assignee] = $sku->pr_assignee;
                    }
                    $contentAssigneeOptions = collect(['' => '—'])->merge($contentUsers->mapWithKeys(fn ($n) => [$n => $n]));
                    if ($sku->content_assignee && !$contentUsers->contains($sku->content_assignee)) {
                        $contentAssigneeOptions[$sku->content_assignee] = $sku->content_assignee;
                    }
                @endphp
                <tr data-sku-id="{{ $sku->id }}">
                    <td class="sku-col-sticky-1">
                        <input type="text" class="sku-cell-input" value="{{ $sku->brand }}" title="{{ $sku->brand }}"
                            {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'brand', this.value, this)">
                    </td>
                    <td class="sku-col-sticky-2">
                        <input type="text" class="sku-cell-input" value="{{ $sku->sku }}" title="{{ $sku->sku }}"
                            {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'sku', this.value, this)">
                    </td>
                    <td class="sku-col-sticky-3">
                        <x-select id="sku-variant-{{ $sku->id }}" name="variant_{{ $sku->id }}" data-color-type="variant"
                            :options="$variantOptions" :selected="$sku->variant ?? ''" :disabled="!$perms['can_edit_pr']"
                            onchange="var el=document.getElementById('sku-variant-{{ $sku->id }}').closest('.app-dd'); colorizeAppDd(el); saveField({{ $sku->id }}, 'variant', value, el.querySelector('.dd-trigger'))" />
                    </td>
                    <td>
                        <input type="text" class="sku-cell-input" value="{{ $sku->pr_file_location }}" title="{{ $sku->pr_file_location }}"
                            {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'pr_file_location', this.value, this)">
                    </td>
                    <td>
                        <x-select id="sku-pr-assignee-{{ $sku->id }}" name="pr_assignee_{{ $sku->id }}" data-color-type="assignee"
                            :options="$prAssigneeOptions" :selected="$sku->pr_assignee ?? ''" :disabled="!$perms['can_edit_pr']"
                            onchange="var el=document.getElementById('sku-pr-assignee-{{ $sku->id }}').closest('.app-dd'); colorizeAppDd(el); saveField({{ $sku->id }}, 'pr_assignee', value, el.querySelector('.dd-trigger'))" />
                    </td>
                    <td>
                        <x-select id="sku-pr-status-{{ $sku->id }}" name="pr_status_{{ $sku->id }}" data-color-type="pr_status"
                            :options="$prStatusOptions" :selected="$sku->pr_status ?? ''" :disabled="!$perms['can_edit_pr']"
                            onchange="var el=document.getElementById('sku-pr-status-{{ $sku->id }}').closest('.app-dd'); colorizeAppDd(el); saveField({{ $sku->id }}, 'pr_status', value, el.querySelector('.dd-trigger'))" />
                    </td>
                    <td style="text-align:center;">
                        <input type="checkbox" {{ $sku->ready_for_cvp ? 'checked' : '' }} {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'ready_for_cvp', this.checked, this)">
                    </td>
                    <td>
                        <x-select id="sku-remarks-{{ $sku->id }}" name="remarks_{{ $sku->id }}" data-color-type="remarks"
                            :options="$remarksOptionsMap" :selected="$sku->remarks ?? ''" :disabled="!$perms['can_edit_pr']"
                            onchange="var el=document.getElementById('sku-remarks-{{ $sku->id }}').closest('.app-dd'); colorizeAppDd(el); saveField({{ $sku->id }}, 'remarks', value, el.querySelector('.dd-trigger'))" />
                    </td>
                    <td>
                        <input type="date" class="sku-cell-input" value="{{ $sku->pr_date_started?->format('Y-m-d') }}"
                            {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'pr_date_started', this.value, this)">
                    </td>
                    <td>
                        <input type="date" class="sku-cell-input" value="{{ $sku->pr_date_completed?->format('Y-m-d') }}"
                            {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'pr_date_completed', this.value, this)">
                    </td>
                    <td class="sku-readonly-cell sku-pr-sla">{{ $sku->pr_sla !== null ? $sku->pr_sla . 'd' : '—' }}</td>

                    <td class="sku-col-content">
                        <x-select id="sku-content-assignee-{{ $sku->id }}" name="content_assignee_{{ $sku->id }}" data-color-type="assignee"
                            :options="$contentAssigneeOptions" :selected="$sku->content_assignee ?? ''" :disabled="!$perms['can_edit_content']"
                            onchange="var el=document.getElementById('sku-content-assignee-{{ $sku->id }}').closest('.app-dd'); colorizeAppDd(el); saveField({{ $sku->id }}, 'content_assignee', value, el.querySelector('.dd-trigger'))" />
                    </td>
                    <td class="sku-col-content sku-content-status">
                        @php $csKey = match($sku->content_status) { 'DONE' => 'done', 'PENDING' => 'pending', default => 'none' }; @endphp
                        <span class="sku-chip {{ $csKey }}">{{ $sku->content_status }}</span>
                    </td>
                    <td class="sku-col-content">
                        <input type="date" class="sku-cell-input" value="{{ $sku->content_date_started?->format('Y-m-d') }}"
                            {{ $perms['can_edit_content'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'content_date_started', this.value, this)">
                    </td>
                    <td class="sku-col-content">
                        <input type="date" class="sku-cell-input" value="{{ $sku->content_date_posted?->format('Y-m-d') }}"
                            {{ $perms['can_edit_content'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'content_date_posted', this.value, this)">
                    </td>
                    <td class="sku-col-content sku-readonly-cell sku-content-sla">{{ $sku->content_sla !== null ? $sku->content_sla . 'd' : '—' }}</td>
                    <td class="sku-col-content sku-posted-cell" style="text-align:center;">
                        <input type="checkbox" {{ $sku->posted ? 'checked' : '' }} disabled title="Automatically checked when Content Date Posted is set">
                    </td>
                </tr>
                @empty
                <tr><td colspan="17" class="empty-state">No SKUs match your filters.</td></tr>
                @endforelse
                <tr id="skuLiveSearchEmpty" style="display:none;"><td colspan="17" class="empty-state">No SKUs match your search.</td></tr>
            </tbody>
        </table>
    </div>
</div>

@if($perms['can_create'])
<!-- Bulk Add Modal -->
<div class="modal-overlay" id="bulkAddModal">
    <div class="modal-box" style="max-width: 640px;">
        <div class="modal-header">
            <h5>Bulk Add SKUs</h5>
            <button class="modal-close" onclick="closeModal('bulkAddModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="display:flex;flex-direction:column;gap:0.875rem;">
            <div class="bulk-add-step">
                <div class="bulk-add-step-head">
                    <span class="bulk-add-step-badge">1</span>
                    <div>
                        <div class="bulk-add-step-title">Step 1: Format your list with AI <span class="bulk-add-step-optional">— optional</span></div>
                        <p class="bulk-add-step-desc">Don't have a clean Brand/SKU list yet? Copy this prompt, paste it into an AI chat tool along with your raw list, then bring the result back here.</p>
                    </div>
                </div>
                <textarea id="aiPromptText" class="bulk-add-textarea bulk-add-textarea-readonly" rows="5" readonly
                >I have a list of products with brand names and SKU/product codes. Reformat it into plain text lines, one product per line, in this exact format: Brand, SKU (brand name, a comma, then the SKU code — nothing else: no numbering, no bullets, no extra words). Output only the reformatted lines so I can copy them directly. Here is my list:

[paste your raw list here]</textarea>
                <button type="button" id="copyAiPromptBtn" class="btn-flat-primary bulk-add-copy-btn" style="height: 36px; font-size: 0.78rem; margin-top: 0.6rem;" onclick="copyAiPrompt()">
                    <i class="fas fa-copy"></i> <span id="copyAiPromptLabel">Copy Prompt</span>
                </button>
            </div>

            <form method="POST" action="{{ route('sku-tracker.bulk-store') }}">
                @csrf
                <div class="bulk-add-step">
                    <div class="bulk-add-step-head">
                        <span class="bulk-add-step-badge">2</span>
                        <div>
                            <div class="bulk-add-step-title">Step 2: Paste your list</div>
                            <p class="bulk-add-step-desc">One row per line: <code>Brand, SKU</code> — also works pasting straight from Excel/Sheets columns.</p>
                        </div>
                    </div>
                    <textarea name="rows_text" class="bulk-add-textarea" rows="8" placeholder="Acme, ACME-001&#10;Acme, ACME-002" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-flat-secondary" onclick="closeModal('bulkAddModal')">Cancel</button>
                    <button type="submit" class="btn-flat-primary"><i class="fas fa-plus"></i> Add Rows</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
var existingSkuCodes = @json($existingSkuCodes);

function checkDuplicateSku(value) {
    var warning = document.getElementById('addRowDuplicateWarning');
    var normalized = (value || '').trim().toLowerCase();
    warning.style.display = (normalized && existingSkuCodes.indexOf(normalized) !== -1) ? 'inline' : 'none';
}

// Live client-side search — filters the already-rendered rows instantly as you
// type, no need to click Filter. (The other filters still submit to the server,
// since they depend on data/date logic beyond what's currently loaded.)
function filterSkuRows(query) {
    var q = (query || '').trim().toLowerCase();
    var rows = document.querySelectorAll('.sku-table tbody tr[data-sku-id]');
    var visibleCount = 0;
    rows.forEach(function (row) {
        var brandInput = row.querySelector('.sku-col-sticky-1 input');
        var skuInput = row.querySelector('.sku-col-sticky-2 input');
        var brand = brandInput ? brandInput.value.toLowerCase() : '';
        var sku = skuInput ? skuInput.value.toLowerCase() : '';
        var match = !q || brand.indexOf(q) !== -1 || sku.indexOf(q) !== -1;
        row.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });
    var emptyRow = document.getElementById('skuLiveSearchEmpty');
    if (emptyRow) emptyRow.style.display = (q && visibleCount === 0) ? '' : 'none';
}

// ── Dropdown coloring ─────────────────────────────────────────
var SKU_SELECT_COLORS = {
    pr_status: { 'In Progress': '#f59e0b', 'Done': '#10b981', 'On Hold': '#f43f5e' },
    remarks: {
        'No Resources': '#f43f5e', 'Out-of-Stock': '#f43f5e', 'SKU Issue': '#f43f5e',
        'Posted': '#10b981', 'Advance PR': '#0ea5e9', 'Old Posted': '#94a3b8'
    },
    variant: { 'Single': '#64748b', 'Variant/Parent': '#6366f1', 'Variant/Child': '#7c3aed', 'Add Variant': '#f59e0b' }
};
var SKU_ASSIGNEE_PALETTE = ['#7c3aed', '#6366f1', '#ec4899', '#10b981', '#0ea5e9', '#f59e0b', '#f43f5e', '#14b8a6'];

function assigneeColor(name) {
    var hash = 0;
    for (var i = 0; i < name.length; i++) { hash = (hash * 31 + name.charCodeAt(i)) >>> 0; }
    return SKU_ASSIGNEE_PALETTE[hash % SKU_ASSIGNEE_PALETTE.length];
}

function colorizeAppDd(appDd) {
    var trigger = appDd.querySelector('.dd-trigger');
    var input = appDd.querySelector('input[type="hidden"]');
    var type = appDd.dataset.colorType;
    var value = input ? input.value : '';
    if (!type || !value) {
        trigger.style.borderColor = '';
        trigger.style.color = '';
        trigger.style.background = '';
        trigger.style.fontWeight = '';
        return;
    }
    var color = type === 'assignee' ? assigneeColor(value) : (SKU_SELECT_COLORS[type] || {})[value];
    if (!color) {
        trigger.style.borderColor = '';
        trigger.style.color = '';
        trigger.style.background = '';
        trigger.style.fontWeight = '';
        return;
    }
    trigger.style.borderColor = color;
    trigger.style.color = color;
    trigger.style.background = color + '1A';
    trigger.style.fontWeight = '700';
}

document.querySelectorAll('.app-dd[data-color-type]').forEach(colorizeAppDd);

// Reposition sku-table dropdown menus to escape the table's scroll clipping, and
// elevate the containing cell's z-index while open — sticky cells all share
// z-index:2, and ties resolve by DOM order, so a later row's sticky cells can
// otherwise paint over an earlier row's open dropdown.
// Deferred to DOMContentLoaded: the shared `function appDdToggle(uid)` declaration
// lives in a later <script> block in the layout, so wrapping it synchronously here
// (before that block runs) would capture undefined and then get silently overwritten
// when the later declaration executes.
document.addEventListener('DOMContentLoaded', function () {
    var originalToggle = window.appDdToggle;
    window.appDdToggle = function (uid) {
        document.querySelectorAll('.sku-dd-cell-active').forEach(function (cell) {
            cell.classList.remove('sku-dd-cell-active');
        });
        originalToggle(uid);
        var dd = document.getElementById(uid);
        if (!dd || !dd.closest('.sku-table')) return;
        var menu = dd.querySelector('.dd-menu');
        var cell = dd.closest('td, th');
        if (!dd.classList.contains('open')) {
            menu.style.position = '';
            menu.style.top = '';
            menu.style.left = '';
            menu.style.minWidth = '';
            menu.style.zIndex = '';
            return;
        }
        if (cell) cell.classList.add('sku-dd-cell-active');
        var rect = dd.querySelector('.dd-trigger').getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.top = (rect.bottom + 4) + 'px';
        menu.style.left = rect.left + 'px';
        menu.style.minWidth = rect.width + 'px';
        menu.style.zIndex = '9999';
    };

    // The shared layout's outside-click handler closes .app-dd directly
    // (bypassing appDdToggle), so it wouldn't clean up the styles/class set
    // above. Run the same cleanup independently whenever a click lands
    // outside every .app-dd.
    document.addEventListener('click', function (e) {
        if (e.target.closest('.app-dd')) return;
        document.querySelectorAll('.sku-dd-cell-active').forEach(function (cell) {
            cell.classList.remove('sku-dd-cell-active');
        });
        document.querySelectorAll('.sku-table .app-dd .dd-menu').forEach(function (menu) {
            menu.style.position = '';
            menu.style.top = '';
            menu.style.left = '';
            menu.style.minWidth = '';
            menu.style.zIndex = '';
        });
    });
});

// ── Bulk add AI prompt ───────────────────────────────────────
function copyAiPrompt() {
    var text = document.getElementById('aiPromptText');
    var label = document.getElementById('copyAiPromptLabel');
    navigator.clipboard.writeText(text.value).then(function () {
        var original = label.textContent;
        label.textContent = 'Copied!';
        setTimeout(function () { label.textContent = original; }, 1500);
    }).catch(function () {
        text.select();
    });
}

function flashCell(el, ok) {
    var cls = ok ? 'sku-cell-flash-ok' : 'sku-cell-flash-err';
    el.classList.add(cls);
    setTimeout(function () { el.classList.remove(cls); }, 700);
}

function saveField(skuId, field, value, el) {
    fetch('/sku-tracker/' + skuId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ field: field, value: value })
    })
    .then(function (res) { return res.json().then(function (body) { return { ok: res.ok, body: body }; }); })
    .then(function (result) {
        flashCell(el, result.ok);
        if (!result.ok) {
            console.warn('Save failed for ' + field + ':', result.body.message || result.body);
            return;
        }
        var row = document.querySelector('tr[data-sku-id="' + skuId + '"]');
        if (!row || !result.body.computed) return;
        var c = result.body.computed;

        var prSla = row.querySelector('.sku-pr-sla');
        if (prSla) prSla.textContent = c.pr_sla !== null ? c.pr_sla + 'd' : '—';

        var contentSla = row.querySelector('.sku-content-sla');
        if (contentSla) contentSla.textContent = c.content_sla !== null ? c.content_sla + 'd' : '—';

        var statusCell = row.querySelector('.sku-content-status');
        if (statusCell) {
            var key = c.content_status === 'DONE' ? 'done' : (c.content_status === 'PENDING' ? 'pending' : 'none');
            statusCell.innerHTML = '<span class="sku-chip ' + key + '">' + c.content_status + '</span>';
        }

        var postedCheckbox = row.querySelector('.sku-posted-cell input[type="checkbox"]');
        if (postedCheckbox) postedCheckbox.checked = !!c.posted;
    })
    .catch(function (err) {
        flashCell(el, false);
        console.error('Save request failed:', err);
    });
}
</script>
@endsection
