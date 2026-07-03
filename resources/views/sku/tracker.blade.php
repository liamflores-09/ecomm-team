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
    .sku-col-sticky-1, .sku-col-sticky-2 { position: sticky; z-index: 2; background: var(--card); }
    .sku-col-sticky-1 { left: 0; min-width: 120px; max-width: 120px; }
    .sku-col-sticky-2 { left: 120px; min-width: 150px; max-width: 150px; border-right: 1px solid var(--border-light); }
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

    #addRowForm { display: flex; gap: 0.6rem; align-items: flex-end; }
    #addRowForm input { width: 180px; }

    .sku-pagination { display: flex; align-items: center; justify-content: center; gap: 0.3rem; flex-wrap: wrap; }
    .sku-page-link { min-width: 32px; height: 32px; padding: 0 8px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid var(--border-light); color: var(--foreground); text-decoration: none; font-size: 0.8rem; font-weight: 600; background: var(--card); }
    .sku-page-link:hover { border-color: var(--primary); }
    .sku-page-link.active { background: var(--primary); border-color: var(--primary); color: white; }
    .sku-page-link.disabled { color: var(--muted-foreground); pointer-events: none; opacity: 0.5; }
    .sku-page-dots { padding: 0 4px; color: var(--muted-foreground); }
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
            <input type="text" name="brand" class="input-flat" placeholder="Brand or SKU..." value="{{ $filters['brand'] ?? '' }}">
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
                <option value="">All</option>
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected(($filters['month'] ?? '') === $m)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
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
                    <th class="sku-col-sticky-1">Brand</th><th class="sku-col-sticky-2">SKU</th><th>Variant</th>
                    <th>PR File Location</th><th>PR Assignee</th><th>PR Status</th><th>Ready for CVP</th><th>Remarks</th>
                    <th>PR Date Started</th><th>PR Date Completed</th><th>PR SLA</th>
                    <th class="sku-col-content">Content Assignee</th><th class="sku-col-content">Content Status</th>
                    <th class="sku-col-content">Content Date Started</th><th class="sku-col-content">Content Date Posted</th>
                    <th class="sku-col-content">Content SLA</th><th class="sku-col-content">Posted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($skus as $sku)
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
                    <td>
                        <select class="sku-cell-select" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'variant', this.value, this)">
                            <option value="" @selected(!$sku->variant)>—</option>
                            @foreach($variants as $v)<option value="{{ $v }}" @selected($sku->variant === $v)>{{ $v }}</option>@endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="sku-cell-input" value="{{ $sku->pr_file_location }}" title="{{ $sku->pr_file_location }}"
                            {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'pr_file_location', this.value, this)">
                    </td>
                    <td>
                        <select class="sku-cell-select" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'pr_assignee', this.value, this)">
                            <option value="" @selected(!$sku->pr_assignee)>—</option>
                            @foreach($researchers as $name)<option value="{{ $name }}" @selected($sku->pr_assignee === $name)>{{ $name }}</option>@endforeach
                            @if($sku->pr_assignee && !$researchers->contains($sku->pr_assignee))
                            <option value="{{ $sku->pr_assignee }}" selected>{{ $sku->pr_assignee }}</option>
                            @endif
                        </select>
                    </td>
                    <td>
                        <select class="sku-cell-select" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'pr_status', this.value, this)">
                            <option value="" @selected(!$sku->pr_status)>—</option>
                            @foreach($prStatuses as $s)<option value="{{ $s }}" @selected($sku->pr_status === $s)>{{ $s }}</option>@endforeach
                        </select>
                    </td>
                    <td style="text-align:center;">
                        <input type="checkbox" {{ $sku->ready_for_cvp ? 'checked' : '' }} {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'ready_for_cvp', this.checked, this)">
                    </td>
                    <td>
                        <select class="sku-cell-select" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'remarks', this.value, this)">
                            <option value="" @selected(!$sku->remarks)>—</option>
                            @foreach($remarksOptions as $r)<option value="{{ $r }}" @selected($sku->remarks === $r)>{{ $r }}</option>@endforeach
                        </select>
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
                        <select class="sku-cell-select" {{ $perms['can_edit_content'] ? '' : 'disabled' }}
                            onchange="saveField({{ $sku->id }}, 'content_assignee', this.value, this)">
                            <option value="" @selected(!$sku->content_assignee)>—</option>
                            @foreach($contentUsers as $name)<option value="{{ $name }}" @selected($sku->content_assignee === $name)>{{ $name }}</option>@endforeach
                            @if($sku->content_assignee && !$contentUsers->contains($sku->content_assignee))
                            <option value="{{ $sku->content_assignee }}" selected>{{ $sku->content_assignee }}</option>
                            @endif
                        </select>
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
            </tbody>
        </table>
    </div>

    <div class="anim-up d4" style="margin-top: 1rem;">{{ $skus->links('sku.partials.pagination') }}</div>
</div>

@if($perms['can_create'])
<!-- Bulk Add Modal -->
<div class="modal-overlay" id="bulkAddModal">
    <div class="modal-box" style="max-width: 560px;">
        <div class="modal-header">
            <h5>Bulk Add SKUs</h5>
            <button class="modal-close" onclick="closeModal('bulkAddModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('sku-tracker.bulk-store') }}">
            @csrf
            <div class="modal-body" style="display:flex;flex-direction:column;gap:0.75rem;">
                <p style="font-size:0.8rem;color:var(--muted-foreground);margin:0;">
                    Paste a JSON array of Brand/SKU pairs, e.g. <code>[{"brand":"Acme","sku":"ACME-1"},{"brand":"Acme","sku":"ACME-2"}]</code>
                </p>
                <textarea name="rows_json" class="form-input" rows="8" style="font-family:ui-monospace,monospace;font-size:0.8rem;" placeholder='[{"brand":"Acme","sku":"ACME-1"}]' required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('bulkAddModal')">Cancel</button>
                <button type="submit" class="btn-flat-primary">Add Rows</button>
            </div>
        </form>
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
