@extends('layouts.app')

@section('title', 'Price Calculator — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='4' y='2' width='16' height='20' rx='2'/><line x1='8' y1='6' x2='16' y2='6'/><line x1='16' y1='14' x2='16' y2='14.01'/><line x1='12' y1='14' x2='12' y2='14.01'/><line x1='8' y1='14' x2='8' y2='14.01'/><line x1='16' y1='18' x2='16' y2='18.01'/><line x1='12' y1='18' x2='12' y2='18.01'/><line x1='8' y1='18' x2='8' y2='18.01'/></svg>">
@endsection

@section('styles')
<style>
    /* Add Row Card */
    .add-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .add-card-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
        margin-bottom: 1rem;
    }

    .add-card-title .title-icon {
        width: 24px;
        height: 24px;
        background: var(--primary);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.65rem;
    }

    .add-fields {
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .add-field {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .add-field label {
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gray-500);
    }

    .add-field .input-flat {
        height: 44px;
    }

    /* SKU Type Pills */
    .type-pills {
        display: flex;
        gap: 0;
        border: 2px solid var(--border);
        border-radius: 6px;
        overflow: hidden;
        height: 44px;
    }

    .type-pills button {
        padding: 0 1rem;
        border: none;
        background: var(--muted);
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gray-500);
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .type-pills button + button {
        border-left: 2px solid var(--border);
    }

    .type-pills button.active {
        background: var(--primary);
        color: white;
    }

    .type-pills button:hover:not(.active) {
        background: var(--gray-200);
    }

    /* Toolbar */
    .toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--white);
        border: 2px solid var(--border);
        border-radius: 6px;
        padding: 0 0.75rem;
        height: 40px;
        min-width: 220px;
    }

    .search-box input {
        border: none;
        outline: none;
        background: transparent;
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--fg);
        width: 100%;
    }

    .search-box input::placeholder { color: var(--gray-300); }

    /* Custom Dropdown */
    .custom-dropdown {
        position: relative;
        height: 40px;
    }

    .custom-dropdown .dd-trigger {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        height: 100%;
        padding: 0 0.75rem;
        background: var(--white);
        border: 2px solid var(--border);
        border-radius: 6px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--fg);
        cursor: pointer;
        transition: all 0.15s;
        user-select: none;
        white-space: nowrap;
    }

    .custom-dropdown .dd-trigger:hover {
        border-color: var(--primary);
    }

    .custom-dropdown .dd-trigger .dd-arrow {
        margin-left: auto;
        font-size: 0.6rem;
        color: var(--gray-400);
        transition: transform 0.2s;
    }

    .custom-dropdown.open .dd-trigger {
        border-color: var(--primary);
    }

    .custom-dropdown.open .dd-trigger .dd-arrow {
        transform: rotate(180deg);
    }

    .custom-dropdown .dd-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: var(--white);
        border: 2px solid var(--border);
        border-radius: 6px;
        z-index: 20;
        max-height: 200px;
        overflow-y: auto;
        padding: 0.25rem;
    }

    .custom-dropdown.open .dd-menu {
        display: block;
        animation: fadeInUp 0.15s ease-out;
    }

    .custom-dropdown .dd-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.625rem;
        border-radius: 4px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--fg);
        cursor: pointer;
        transition: all 0.1s;
        gap: 0.5rem;
    }

    .custom-dropdown .dd-item:hover {
        background: var(--muted);
    }

    .custom-dropdown .dd-item.selected {
        background: var(--primary);
        color: white;
    }

    .custom-dropdown .dd-item .dd-check {
        width: 16px;
        height: 16px;
        border: 2px solid var(--border);
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.5rem;
        flex-shrink: 0;
        transition: all 0.1s;
    }

    .custom-dropdown .dd-item.selected .dd-check {
        border-color: white;
        background: rgba(255,255,255,0.2);
    }

    .result-count {
        background: var(--muted);
        padding: 0.25rem 0.625rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-500);
    }

    /* Table */
    .table-wrap {
        background: var(--white);
        border-radius: 8px;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-title-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        background: var(--muted);
        border-bottom: 2px solid var(--border);
    }

    .table-title-bar .t-icon {
        width: 24px;
        height: 24px;
        background: var(--primary);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.65rem;
    }

    .table-title-bar span {
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
    }

    .table-title-bar small {
        margin-left: auto;
        font-size: 0.75rem;
        font-weight: 400;
        text-transform: none;
        letter-spacing: 0;
        color: var(--gray-400);
    }

    .price-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        min-width: 850px;
    }

    .price-table thead th {
        background: var(--fg);
        color: var(--white);
        padding: 0.75rem;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        text-align: left;
        white-space: nowrap;
    }

    .price-table thead th small {
        font-weight: 400;
        text-transform: none;
        letter-spacing: 0;
        opacity: 0.6;
    }

    .price-table tbody td {
        padding: 0.5rem 0.75rem;
        border-top: 2px solid var(--muted);
        color: var(--gray-700);
        font-weight: 500;
        vertical-align: middle;
    }

    .price-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .price-table tbody tr.group-sep td {
        border-top: 3px solid var(--primary);
    }

    .price-table tbody tr.dupe-highlight td {
        background: #FEF2F2;
    }

    .price-table .c-input {
        border: 2px solid transparent;
        border-radius: 4px;
        padding: 0.375rem 0.5rem;
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--fg);
        background: var(--muted);
        outline: none;
        transition: all 0.15s;
        width: 100%;
    }

    .price-table .c-input:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    .price-table .c-input.err {
        border-color: #DC2626;
        background: #FEF2F2;
    }

    .price-table .c-num { width: 100px; }
    .price-table .c-grp { width: 70px; }

    .price-table .computed {
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .cell-check {
        width: 40px;
        text-align: center;
    }

    .cell-check input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .badge-good {
        display: inline-block;
        background: #D1FAE5;
        color: #059669;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .badge-err {
        display: inline-block;
        background: #FEE2E2;
        color: #DC2626;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .badge-dupe {
        display: inline-block;
        background: #FEF3C7;
        color: #92400E;
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
        font-size: 0.6rem;
        font-weight: 700;
        margin-left: 0.375rem;
        vertical-align: middle;
    }

    /* Footer */
    .calc-footer {
        margin-top: 1rem;
        padding: 0.875rem 1rem;
        background: var(--muted);
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-500);
        line-height: 1.6;
    }

    .calc-footer strong { color: var(--fg); }

    /* Responsive */
    @media (max-width: 768px) {
        .add-fields { flex-direction: column; align-items: stretch; }
        .add-fields .add-field { width: 100%; }
        .toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-left, .toolbar-right { width: 100%; }
        .toolbar-right { margin-left: 0; justify-content: stretch; flex-wrap: wrap; }
        .toolbar-right .btn-flat-primary,
        .toolbar-right .btn-flat-secondary { flex: 1; min-width: 80px; }
        .search-box { min-width: 0; width: 100%; }
        .table-wrap { overflow-x: auto; }
    }

    @media (max-width: 480px) {
        .add-card { padding: 1rem; }
        .type-pills button { padding: 0 0.625rem; font-size: 0.8rem; }
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">EC</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>PR x Content</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('posting-procedure') }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
        <li><a href="{{ route('price-calculator') }}" class="active"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
        <li><a href="{{ route('important-links') }}"><i class="fas fa-link"></i> Important Links</a></li>
        <li><a href="{{ route('team') }}"><i class="fas fa-users"></i> The Team</a></li>
    </ul>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</div>

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Price <span class="highlight">Calculator</span></h2>
            <p>Fill in Group, SKU, and Unit Price — then click Calculate</p>
        </div>
    </div>

    <!-- Add Row Card -->
    <div class="add-card anim-up d1">
        <div class="add-card-title">
            <div class="title-icon"><i class="fas fa-plus"></i></div>
            Add Row
        </div>
        <div class="add-fields">
            <div class="add-field">
                <label>Group</label>
                <input type="number" id="addGroupInput" class="input-flat" placeholder="#" style="width: 80px;">
            </div>
            <div class="add-field">
                <label>SKU Type</label>
                <div class="type-pills">
                    <button type="button" class="active" onclick="setSkuType('single', this)">
                        <i class="fas fa-cube" style="font-size: 0.7rem;"></i> Single
                    </button>
                    <button type="button" onclick="setSkuType('variant', this)">
                        <i class="fas fa-cubes" style="font-size: 0.7rem;"></i> Variant
                    </button>
                </div>
            </div>
            <div class="add-field" id="variantCountField" style="display: none;">
                <label>Variant Count</label>
                <input type="number" id="variantCountInput" class="input-flat" placeholder="#" min="1" style="width: 90px;">
            </div>
            <div class="add-field">
                <label>Unit Price</label>
                <input type="number" id="addPriceInput" class="input-flat" placeholder="0.00" step="any" style="width: 120px;">
            </div>
            <div class="add-field" style="align-self: flex-end;">
                <button class="btn-flat-primary" style="height: 44px; padding: 0 1.25rem; font-size: 0.85rem;" onclick="addRow()">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar anim-up d2">
        <div class="toolbar-left">
            <div class="search-box">
                <i class="fas fa-search" style="color: var(--gray-300); font-size: 0.8rem;"></i>
                <input type="text" id="searchInput" placeholder="Search by Group, SKU, or Price..." oninput="handleSearch(this.value)">
            </div>
            <select class="filter-select" id="groupFilterSelect" onchange="handleGroupFilter(this.value)" style="display: none;">
                <option value="all">All Groups</option>
            </select>
            <div class="custom-dropdown" id="groupDropdown">
                <div class="dd-trigger" onclick="toggleDropdown('groupDropdown')">
                    <span id="groupDropdownLabel">All Groups</span>
                    <span class="dd-arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="dd-menu" id="groupDropdownMenu"></div>
            </div>
        </div>
        <div class="toolbar-right">
            <span class="result-count" id="resultCount">0 results</span>
            <button class="btn-flat-secondary" style="height: 40px; padding: 0 0.75rem; font-size: 0.8rem;" onclick="clearFilters()">Clear</button>
            <button class="btn-flat-secondary" style="height: 40px; padding: 0 0.875rem; font-size: 0.8rem;" onclick="deleteSelectedRows()">
                <i class="fas fa-trash-can"></i> Delete
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrap anim-up d3">
        <div class="table-title-bar">
            <div class="t-icon"><i class="fas fa-table"></i></div>
            <span>Price Calculator Table</span>
            <small>Check box to select row for deletion | Same group numbers are calculated together</small>
        </div>
        <table class="price-table">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)" style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;"></th>
                    <th>Group (A)<br><small>For Variation</small></th>
                    <th>SKU (B)</th>
                    <th>Unit Price (C)</th>
                    <th>Min (D)</th>
                    <th>Max (E)</th>
                    <th>Shopee SRP (F)</th>
                    <th>Checker (G)</th>
                    <th>Lazada SRP (H)</th>
                    <th>Checker (I)</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>

    <div class="calc-footer anim-up d4">
        <strong>Formula:</strong> Min/Max based on same Group | Shopee SRP = MIN(ROUNDUP(((Min × 4.5 − UnitPrice) ÷ 10 + UnitPrice), 0), 150000)<br>
        <strong>Tip:</strong> Select "Variant" and enter the number of variants to auto-generate rows under the same group. Duplicate SKUs are highlighted in red.
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    var priceData = [];
    var nextId = 1;
    var currentSearch = '';
    var currentGroupFilter = 'all';
    var skuType = 'single';
    var STORAGE_KEY = 'ecommPriceData';

    function load() {
        try {
            var s = localStorage.getItem(STORAGE_KEY);
            if (s) { priceData = JSON.parse(s); nextId = priceData.length > 0 ? Math.max.apply(null, priceData.map(function(i){return i.id;})) + 1 : 1; }
        } catch(e) { priceData = []; nextId = 1; }
    }

    function save() { localStorage.setItem(STORAGE_KEY, JSON.stringify(priceData)); }

    function getMinForGroup(g) {
        var items = priceData.filter(function(i){return i.group === g;});
        return items.length === 0 ? 0 : Math.min.apply(null, items.map(function(i){return i.unitPrice;}));
    }

    function getMaxForGroup(g) {
        var items = priceData.filter(function(i){return i.group === g;});
        return items.length === 0 ? 0 : Math.max.apply(null, items.map(function(i){return i.unitPrice;}));
    }

    function shopeeSRP(up, min, max) {
        if (!up || up === 0 || isNaN(up)) return '';
        if (max / min > 4.5 || up > 149999) return 0;
        return Math.min(Math.ceil(((min * 4.5 - up) / 10 + up)), 150000);
    }

    function shopeeChecker(up, max, min) {
        if (!up || up === 0 || isNaN(up)) return '';
        return (max / min > 4.5 || up > 149999) ? 'Error' : 'Good';
    }

    function lazadaSRP(up, min) {
        if (!up || up === 0 || isNaN(up)) return '';
        return Math.ceil(((min * 4.5 - up) / 10 + up));
    }

    function lazadaChecker(up, max, min) {
        if (!up || up === 0 || isNaN(up)) return '';
        return (max / min > 4.5) ? 'Error' : 'Good';
    }

    function getDupeSkus() {
        var counts = {};
        priceData.forEach(function(i) {
            var k = (i.sku || '').trim().toLowerCase();
            if (k) counts[k] = (counts[k] || 0) + 1;
        });
        var dupes = {};
        for (var k in counts) { if (counts[k] > 1) dupes[k] = true; }
        return dupes;
    }

    function updateGroupOptions() {
        var groups = [];
        priceData.forEach(function(i) { if (i.group && groups.indexOf(i.group) === -1) groups.push(i.group); });
        groups.sort(function(a,b){return a-b;});
        var sel = document.getElementById('groupFilterSelect');
        var cur = sel.value;
        sel.innerHTML = '<option value="all">All Groups</option>';
        groups.forEach(function(g) { sel.innerHTML += '<option value="' + g + '">Group ' + g + '</option>'; });
        if (cur !== 'all' && groups.indexOf(parseInt(cur)) !== -1) sel.value = cur;
        else { currentGroupFilter = 'all'; sel.value = 'all'; }

        var menu = document.getElementById('groupDropdownMenu');
        var html = '<div class="dd-item' + (currentGroupFilter === 'all' ? ' selected' : '') + '" onclick="selectGroupOption(\'all\', this)"><span class="dd-check">' + (currentGroupFilter === 'all' ? '<i class="fas fa-check"></i>' : '') + '</span>All Groups</div>';
        groups.forEach(function(g) {
            var isActive = currentGroupFilter === g.toString();
            html += '<div class="dd-item' + (isActive ? ' selected' : '') + '" onclick="selectGroupOption(\'' + g + '\', this)"><span class="dd-check">' + (isActive ? '<i class="fas fa-check"></i>' : '') + '</span>Group ' + g + '</div>';
        });
        menu.innerHTML = html;

        var label = document.getElementById('groupDropdownLabel');
        if (currentGroupFilter === 'all') label.textContent = 'All Groups';
        else label.textContent = 'Group ' + currentGroupFilter;
    }

    function getFiltered() {
        var f = priceData;
        if (currentSearch) {
            var sl = currentSearch.toLowerCase();
            f = f.filter(function(i) {
                return i.group.toString().indexOf(sl) !== -1 || (i.sku || '').toLowerCase().indexOf(sl) !== -1 || i.unitPrice.toString().indexOf(sl) !== -1;
            });
        }
        if (currentGroupFilter !== 'all') {
            var gn = parseInt(currentGroupFilter);
            f = f.filter(function(i) { return i.group === gn; });
        }
        return f;
    }

    function esc(s) {
        if (!s) return '';
        return s.replace(/[&<>"]/g, function(m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]; });
    }

    function render() {
        var tbody = document.getElementById('tableBody');
        updateGroupOptions();
        var filtered = getFiltered();
        document.getElementById('resultCount').textContent = filtered.length + ' result' + (filtered.length !== 1 ? 's' : '');
        var dupes = getDupeSkus();

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:3rem;color:var(--gray-300);">' + (priceData.length === 0 ? '<i class="fas fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;"></i>No data yet. Add a row above to start.' : 'No matching results found.') + '</td></tr>';
            document.getElementById('selectAllCheckbox').checked = false;
            return;
        }

        var html = '';
        var lastGroup = null;
        filtered.forEach(function(item) {
            var isNewGroup = item.group !== lastGroup;
            var classes = [];
            if (isNewGroup && lastGroup !== null) classes.push('group-sep');
            var skuKey = (item.sku || '').trim().toLowerCase();
            if (skuKey && dupes[skuKey]) classes.push('dupe-highlight');

            var min = getMinForGroup(item.group);
            var max = getMaxForGroup(item.group);
            var sSrp = shopeeSRP(item.unitPrice, min, max);
            var sChk = shopeeChecker(item.unitPrice, max, min);
            var lSrp = lazadaSRP(item.unitPrice, min);
            var lChk = lazadaChecker(item.unitPrice, max, min);

            html += '<tr class="' + classes.join(' ') + '">';
            html += '<td class="cell-check"><input type="checkbox" class="row-cb" data-id="' + item.id + '"></td>';
            html += '<td><input type="number" class="c-input c-grp" value="' + item.group + '" onchange="updateField(' + item.id + ',\'group\',this.value)"></td>';
            html += '<td><input type="text" class="c-input' + (skuKey && dupes[skuKey] ? ' err' : '') + '" value="' + esc(item.sku) + '" placeholder="SKU" onchange="updateField(' + item.id + ',\'sku\',this.value)">';
            if (skuKey && dupes[skuKey]) html += ' <span class="badge-dupe">Duplicate</span>';
            html += '</td>';
            html += '<td><input type="number" class="c-input c-num" value="' + (item.unitPrice || '') + '" step="any" placeholder="0.00" onchange="updateField(' + item.id + ',\'unitPrice\',this.value)"></td>';
            html += '<td class="computed">' + (min ? min.toLocaleString() : '-') + '</td>';
            html += '<td class="computed">' + (max ? max.toLocaleString() : '-') + '</td>';
            html += '<td class="computed">' + (sSrp === 0 ? '0' : (sSrp ? sSrp.toLocaleString() : '-')) + '</td>';
            html += '<td>' + (sChk === 'Error' ? '<span class="badge-err">Error</span>' : (sChk === 'Good' ? '<span class="badge-good">Good</span>' : '-')) + '</td>';
            html += '<td class="computed">' + (lSrp ? lSrp.toLocaleString() : '-') + '</td>';
            html += '<td>' + (lChk === 'Error' ? '<span class="badge-err">Error</span>' : (lChk === 'Good' ? '<span class="badge-good">Good</span>' : '-')) + '</td>';
            html += '</tr>';
            lastGroup = item.group;
        });
        tbody.innerHTML = html;

        var cbs = document.querySelectorAll('.row-cb');
        var sa = document.getElementById('selectAllCheckbox');
        if (cbs.length > 0) sa.checked = Array.from(cbs).every(function(c){return c.checked;});
        else sa.checked = false;
    }

    window.updateField = function(id, field, value) {
        var item = priceData.find(function(i){return i.id === id;});
        if (!item) return;
        if (field === 'group') item.group = parseInt(value) || 0;
        else if (field === 'sku') item.sku = value;
        else if (field === 'unitPrice') item.unitPrice = parseFloat(value) || 0;
        save(); render();
    };

    window.setSkuType = function(type, btn) {
        skuType = type;
        document.querySelectorAll('.type-pills button').forEach(function(b){b.classList.remove('active');});
        btn.classList.add('active');
        document.getElementById('variantCountField').style.display = type === 'variant' ? '' : 'none';
    };

    window.addRow = function() {
        var group = parseInt(document.getElementById('addGroupInput').value) || 0;
        var price = parseFloat(document.getElementById('addPriceInput').value) || 0;
        var count = 1;

        if (skuType === 'variant') {
            count = parseInt(document.getElementById('variantCountInput').value) || 0;
            if (count < 1) { alert('Please enter the number of variants.'); return; }
        }

        for (var i = 0; i < count; i++) {
            priceData.push({ id: nextId++, group: group, sku: '', unitPrice: price, min: 0, max: 0 });
        }

        save(); render();
        document.getElementById('addGroupInput').value = '';
        document.getElementById('addPriceInput').value = '';
        document.getElementById('variantCountInput').value = '';
    };

    window.deleteSelectedRows = function() {
        var cbs = document.querySelectorAll('.row-cb:checked');
        var ids = Array.from(cbs).map(function(c){return parseInt(c.getAttribute('data-id'));});
        if (ids.length === 0) { alert('Please select at least one row to delete.'); return; }
        if (confirm('Delete ' + ids.length + ' selected row(s)?')) {
            priceData = priceData.filter(function(i){return ids.indexOf(i.id) === -1;});
            save(); render();
        }
    };

    window.toggleSelectAll = function(checked) {
        document.querySelectorAll('.row-cb').forEach(function(c){c.checked = checked;});
    };

    window.handleSearch = function(val) { currentSearch = val.trim(); render(); };
    window.handleGroupFilter = function(val) { currentGroupFilter = val; render(); };
    window.clearFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('groupFilterSelect').value = 'all';
        currentSearch = '';
        currentGroupFilter = 'all';
        document.getElementById('groupDropdownLabel').textContent = 'All Groups';
        render();
    };

    // --- Custom Dropdown ---
    window.toggleDropdown = function(id) {
        var dd = document.getElementById(id);
        var isOpen = dd.classList.contains('open');
        document.querySelectorAll('.custom-dropdown').forEach(function(d){ d.classList.remove('open'); });
        if (!isOpen) dd.classList.add('open');
    };

    window.selectGroupOption = function(val, el) {
        document.getElementById('groupFilterSelect').value = val;
        document.getElementById('groupDropdownLabel').textContent = el.textContent.trim();
        document.querySelectorAll('.custom-dropdown').forEach(function(d){ d.classList.remove('open'); });
        currentGroupFilter = val;
        render();
    };

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown').forEach(function(d){ d.classList.remove('open'); });
        }
    });

    load(); render();
})();
</script>
@endsection
