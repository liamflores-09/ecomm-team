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

    .sku-filter-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
    .sku-filter-group { display: flex; flex-direction: column; gap: 0.3rem; min-width: 160px; }
    .sku-filter-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--muted-foreground); }

    .sku-table-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; overflow-x: auto; }
    table.sku-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; white-space: nowrap; }
    table.sku-table th { text-align: left; padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); color: var(--muted-foreground); font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.04em; background: var(--card); }
    table.sku-table td { padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); max-width: 220px; overflow: hidden; text-overflow: ellipsis; }
    .sku-col-sticky-1, .sku-col-sticky-2 { position: sticky; z-index: 2; background: var(--card); overflow: hidden; text-overflow: ellipsis; }
    .sku-col-sticky-1 { left: 0; min-width: 130px; max-width: 130px; }
    .sku-col-sticky-2 { left: 130px; min-width: 160px; max-width: 160px; border-right: 1px solid var(--border-light); }
    .sku-chip { display: inline-flex; padding: 0.18rem 0.6rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 700; }
    .sku-chip.done { background: rgba(34,197,94,0.12); color: var(--success); }
    .sku-chip.pending { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .sku-chip.none { background: var(--muted); color: var(--muted-foreground); }
    .sku-row-btn { border: 1px solid var(--border-light); border-radius: 6px; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: var(--muted-foreground); cursor: pointer; }
    .sku-row-btn:hover { border-color: var(--foreground); color: var(--foreground); }

    .sku-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; }
    .sku-form-section-title { grid-column: 1 / -1; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); margin-top: 0.5rem; border-top: 1px solid var(--border-light); padding-top: 0.75rem; }
    .sku-form-section-title:first-child { border-top: none; margin-top: 0; padding-top: 0; }
</style>
@endsection

@section('content')
<x-sidebar active="sku-tracker" :isAdmin="Auth::user()->isAdmin()" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>SKU <span class="highlight">Tracker</span></h2>
            <p>Product research to content posting pipeline</p>
        </div>
        @if($perms['can_create'])
        <button type="button" class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="openAddSku()">
            <i class="fas fa-plus"></i> Add SKU
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="sku-kpi-grid anim-up d1">
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Total SKUs</span><div class="sku-kpi-icon"><i class="fas fa-box"></i></div></div>
            <div class="sku-kpi-value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="sku-kpi-card">
            <div class="sku-kpi-top"><span class="sku-kpi-label">Posted</span><div class="sku-kpi-icon"><i class="fas fa-circle-check"></i></div></div>
            <div class="sku-kpi-value">{{ number_format($stats['posted']) }}</div>
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

    <div class="sku-table-card anim-up d3">
        <table class="sku-table">
            <thead>
                <tr>
                    <th class="sku-col-sticky-1">Brand</th><th class="sku-col-sticky-2">SKU</th><th>Variant</th>
                    <th>PR Assignee</th><th>PR Status</th><th>PR Date Started</th><th>PR Date Completed</th><th>PR SLA</th>
                    <th>PR File Location</th><th>Ready for CVP</th><th>Remarks</th>
                    <th>Content Assignee</th><th>Content Date Started</th><th>Content Date Posted</th><th>Content SLA</th><th>Content Status</th>
                    <th>Posted</th><th>CVP Uploaded</th>
                    <th>Shopee</th><th>Lazada</th><th>TikTok</th>
                    <th>JG PRO Shopee</th><th>JG PRO Lazada</th><th>Shopify</th><th>CinePro</th>
                    <th>LZD Brand Mall</th><th>SHP Brand Mall</th><th>TT Brand Mall</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($skus as $sku)
                <tr>
                    <td class="sku-col-sticky-1" title="{{ $sku->brand }}">{{ $sku->brand }}</td>
                    <td class="sku-col-sticky-2" title="{{ $sku->sku }}">{{ $sku->sku }}</td>
                    <td>{{ $sku->variant ?? '—' }}</td>
                    <td>{{ $sku->pr_assignee ?? '—' }}</td>
                    <td>{{ $sku->pr_status ?? '—' }}</td>
                    <td>{{ $sku->pr_date_started?->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ $sku->pr_date_completed?->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ $sku->pr_sla !== null ? $sku->pr_sla . 'd' : '—' }}</td>
                    <td title="{{ $sku->pr_file_location }}">{{ $sku->pr_file_location ?? '—' }}</td>
                    <td>{{ $sku->ready_for_cvp ? 'Yes' : 'No' }}</td>
                    <td title="{{ $sku->remarks }}">{{ $sku->remarks ?? '—' }}</td>
                    <td>{{ $sku->content_assignee ?? '—' }}</td>
                    <td>{{ $sku->content_date_started?->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ $sku->content_date_posted?->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ $sku->content_sla !== null ? $sku->content_sla . 'd' : '—' }}</td>
                    <td>
                        @php $csKey = match($sku->content_status) { 'DONE' => 'done', 'PENDING' => 'pending', default => 'none' }; @endphp
                        <span class="sku-chip {{ $csKey }}">{{ $sku->content_status }}</span>
                    </td>
                    <td>{{ $sku->posted ? 'Yes' : 'No' }}</td>
                    <td>{{ $sku->cvp_uploaded ? 'Yes' : 'No' }}</td>
                    <td title="{{ $sku->shopee_link }}">{{ $sku->shopee_link ?? '—' }}</td>
                    <td title="{{ $sku->lazada_link }}">{{ $sku->lazada_link ?? '—' }}</td>
                    <td title="{{ $sku->tiktok_link }}">{{ $sku->tiktok_link ?? '—' }}</td>
                    <td title="{{ $sku->jg_pro_shopee_link }}">{{ $sku->jg_pro_shopee_link ?? '—' }}</td>
                    <td title="{{ $sku->jg_pro_lazada_link }}">{{ $sku->jg_pro_lazada_link ?? '—' }}</td>
                    <td title="{{ $sku->shopify_link }}">{{ $sku->shopify_link ?? '—' }}</td>
                    <td title="{{ $sku->cinepro_link }}">{{ $sku->cinepro_link ?? '—' }}</td>
                    <td title="{{ $sku->lzd_brand_mall_link }}">{{ $sku->lzd_brand_mall_link ?? '—' }}</td>
                    <td title="{{ $sku->shp_brand_mall_link }}">{{ $sku->shp_brand_mall_link ?? '—' }}</td>
                    <td title="{{ $sku->tt_brand_mall_link }}">{{ $sku->tt_brand_mall_link ?? '—' }}</td>
                    <td>
                        @if($perms['can_edit_pr'] || $perms['can_edit_content'])
                        <button class="sku-row-btn" title="Edit" onclick='openEditSku(@json($sku))'>
                            <i class="fas fa-pencil"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="29" class="empty-state">No SKUs match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="anim-up d4" style="margin-top: 1rem;">{{ $skus->links() }}</div>
</div>

<!-- SKU Modal -->
<div class="modal-overlay" id="skuModal">
    <div class="modal-box" style="max-width: 640px;">
        <div class="modal-header">
            <h5 id="skuModalTitle">SKU Details</h5>
            <button class="modal-close" onclick="closeModal('skuModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="skuForm">
            @csrf
            <input type="hidden" name="_method" id="skuMethod" value="">
            <div class="modal-body">
                <div class="sku-form-grid">
                    <div class="sku-form-section-title">Basic Info</div>
                    <div class="form-group"><label class="form-label">Brand</label><input type="text" name="brand" id="skuBrand" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }} required></div>
                    <div class="form-group">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" id="skuSku" class="form-input" oninput="checkDuplicateSku(this.value)" {{ $perms['can_edit_pr'] ? '' : 'disabled' }} required>
                        <span id="skuDuplicateWarning" style="display:none;color:#f59e0b;font-size:0.72rem;font-weight:600;margin-top:0.2rem;">
                            <i class="fas fa-triangle-exclamation"></i> A SKU with this code already exists — you can still save.
                        </span>
                    </div>
                    <div class="form-group"><label class="form-label">Variant</label>
                        <select name="variant" id="skuVariant" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}>
                            <option value="">—</option>
                            @foreach($variants as $v)<option value="{{ $v }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>

                    <div class="sku-form-section-title">PR Section</div>
                    <div class="form-group"><label class="form-label">PR Assignee</label><input type="text" name="pr_assignee" id="skuPrAssignee" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>
                    <div class="form-group"><label class="form-label">PR Status</label>
                        <select name="pr_status" id="skuPrStatus" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}>
                            <option value="">—</option>
                            @foreach($prStatuses as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">PR Date Started</label><input type="date" name="pr_date_started" id="skuPrStarted" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>
                    <div class="form-group"><label class="form-label">PR Date Completed</label><input type="date" name="pr_date_completed" id="skuPrCompleted" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>
                    <div class="form-group"><label class="form-label">PR File Location</label><input type="text" name="pr_file_location" id="skuPrFileLocation" class="form-input" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></div>
                    <div class="form-group">
                        <label class="form-label"><input type="checkbox" name="ready_for_cvp" id="skuReadyForCvp" value="1" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}> Ready for CVP</label>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;"><label class="form-label">Remarks</label><textarea name="remarks" id="skuRemarks" class="form-input" rows="3" {{ $perms['can_edit_pr'] ? '' : 'disabled' }}></textarea></div>

                    @if($perms['can_edit_content'])
                    <div class="sku-form-section-title">Content Section</div>
                    <div class="form-group"><label class="form-label">Content Assignee</label><input type="text" name="content_assignee" id="skuContentAssignee" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Content Date Started</label><input type="date" name="content_date_started" id="skuContentStarted" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Content Date Posted</label><input type="date" name="content_date_posted" id="skuContentPosted" class="form-input"></div>

                    <div class="sku-form-section-title">Marketplace Links</div>
                    <div class="form-group"><label class="form-label">Shopee</label><input type="text" name="shopee_link" id="skuShopee" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Lazada</label><input type="text" name="lazada_link" id="skuLazada" class="form-input"></div>
                    <div class="form-group"><label class="form-label">TikTok</label><input type="text" name="tiktok_link" id="skuTiktok" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Shopify</label><input type="text" name="shopify_link" id="skuShopify" class="form-input"></div>
                    <div class="form-group"><label class="form-label">CinePro</label><input type="text" name="cinepro_link" id="skuCinepro" class="form-input"></div>
                    <div class="form-group"><label class="form-label">JG PRO Shopee</label><input type="text" name="jg_pro_shopee_link" id="skuJgShopee" class="form-input"></div>
                    <div class="form-group"><label class="form-label">JG PRO Lazada</label><input type="text" name="jg_pro_lazada_link" id="skuJgLazada" class="form-input"></div>
                    <div class="form-group"><label class="form-label">LZD Brand Mall</label><input type="text" name="lzd_brand_mall_link" id="skuLzdMall" class="form-input"></div>
                    <div class="form-group"><label class="form-label">SHP Brand Mall</label><input type="text" name="shp_brand_mall_link" id="skuShpMall" class="form-input"></div>
                    <div class="form-group"><label class="form-label">TT Brand Mall</label><input type="text" name="tt_brand_mall_link" id="skuTtMall" class="form-input"></div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('skuModal')">Cancel</button>
                <button type="submit" class="btn-flat-primary" {{ ($perms['can_edit_pr'] || $perms['can_edit_content']) ? '' : 'disabled' }}>Save</button>
            </div>
        </form>
    </div>
</div>

<script>
var existingSkuCodes = @json($existingSkuCodes);
var editingSkuCode = null;

function checkDuplicateSku(value) {
    var warning = document.getElementById('skuDuplicateWarning');
    var normalized = (value || '').trim().toLowerCase();
    var isDuplicate = normalized && normalized !== editingSkuCode && existingSkuCodes.indexOf(normalized) !== -1;
    warning.style.display = isDuplicate ? 'inline' : 'none';
}

@if($perms['can_create'])
function openAddSku() {
    document.getElementById('skuModalTitle').textContent = 'Add SKU';
    document.getElementById('skuForm').action = '{{ route("sku-tracker.store") }}';
    document.getElementById('skuMethod').value = '';
    document.getElementById('skuForm').reset();
    document.getElementById('skuDuplicateWarning').style.display = 'none';
    document.getElementById('skuPrFileLocation').value = '';
    document.getElementById('skuRemarks').value = '';
    document.getElementById('skuReadyForCvp').checked = false;
    editingSkuCode = null;
    openModal('skuModal');
}
@endif

function openEditSku(sku) {
    document.getElementById('skuModalTitle').textContent = 'Edit SKU';
    document.getElementById('skuForm').action = '/sku-tracker/' + sku.id;
    document.getElementById('skuMethod').value = 'PUT';
    document.getElementById('skuDuplicateWarning').style.display = 'none';
    editingSkuCode = (sku.sku || '').trim().toLowerCase();
    document.getElementById('skuBrand').value = sku.brand || '';
    document.getElementById('skuSku').value = sku.sku || '';
    document.getElementById('skuVariant').value = sku.variant || '';
    document.getElementById('skuPrAssignee').value = sku.pr_assignee || '';
    document.getElementById('skuPrStatus').value = sku.pr_status || '';
    document.getElementById('skuPrStarted').value = sku.pr_date_started || '';
    document.getElementById('skuPrCompleted').value = sku.pr_date_completed || '';
    document.getElementById('skuPrFileLocation').value = sku.pr_file_location || '';
    document.getElementById('skuRemarks').value = sku.remarks || '';
    document.getElementById('skuReadyForCvp').checked = !!sku.ready_for_cvp;
    @if($perms['can_edit_content'])
    document.getElementById('skuContentAssignee').value = sku.content_assignee || '';
    document.getElementById('skuContentStarted').value = sku.content_date_started || '';
    document.getElementById('skuContentPosted').value = sku.content_date_posted || '';
    document.getElementById('skuShopee').value = sku.shopee_link || '';
    document.getElementById('skuLazada').value = sku.lazada_link || '';
    document.getElementById('skuTiktok').value = sku.tiktok_link || '';
    document.getElementById('skuShopify').value = sku.shopify_link || '';
    document.getElementById('skuCinepro').value = sku.cinepro_link || '';
    document.getElementById('skuJgShopee').value = sku.jg_pro_shopee_link || '';
    document.getElementById('skuJgLazada').value = sku.jg_pro_lazada_link || '';
    document.getElementById('skuLzdMall').value = sku.lzd_brand_mall_link || '';
    document.getElementById('skuShpMall').value = sku.shp_brand_mall_link || '';
    document.getElementById('skuTtMall').value = sku.tt_brand_mall_link || '';
    @endif
    openModal('skuModal');
}
</script>
@endsection
