@extends('layouts.app')

@section('title', 'Brands — Admin')
@section('has-sidebar', true)

@section('styles')
<style>
    /* KPI cards (matching dashboard format) */
    .brand-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
    .brand-kpi-card { background: var(--card); border-radius: 8px; padding: 1.5rem; border: 1px solid var(--border-light); transition: border-color 0.2s; }
    .brand-kpi-card:hover { border-color: var(--foreground); }
    .brand-kpi-card.total    { border-top: 2px solid var(--primary); }
    .brand-kpi-card.tech     { border-top: 2px solid #0ea5e9; }
    .brand-kpi-card.consumer { border-top: 2px solid #f59e0b; }
    .brand-kpi-card.both     { border-top: 2px solid var(--success); }
    .bkpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
    .bkpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .bkpi-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: white; flex-shrink: 0; }
    .bkpi-icon.total    { background: var(--primary); }
    .bkpi-icon.tech     { background: #0ea5e9; }
    .bkpi-icon.consumer { background: #f59e0b; }
    .bkpi-icon.both     { background: var(--success); }
    .bkpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1; margin-bottom: 0.375rem; font-family: 'Space Grotesk', sans-serif; color: var(--foreground); }
    .bkpi-sub { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }

    /* Search + A-Z card */
    .brand-controls-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1.25rem 1.5rem 1.25rem; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
    .brand-search-wrap { position: relative; }
    .brand-search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--muted-foreground); font-size: 0.9rem; pointer-events: none; }
    .brand-search-input { width: 100%; height: 48px; padding: 0 3rem 0 3rem; background: var(--muted); border: 2px solid transparent; border-radius: 10px; font-family: var(--p-font-family-sans); font-size: 0.95rem; font-weight: 500; color: var(--fg); outline: none; transition: all 0.15s; box-sizing: border-box; }
    .brand-search-input:focus { border-color: var(--primary); background: var(--white); }
    .brand-search-input::placeholder { color: var(--gray-300); }
    .brand-search-clear { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted-foreground); padding: 0 0.25rem; font-size: 0.85rem; line-height: 1; }
    .brand-search-clear:hover { color: var(--foreground); }
    .az-divider { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted-foreground); margin-bottom: 0.5rem; }
    .az-bar { display: flex; flex-wrap: wrap; gap: 0.25rem; }
    .az-btn { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 0.78rem; font-weight: 700; transition: all 0.15s; border: none; cursor: default; }
    .az-btn.has-brands { color: var(--foreground); background: var(--muted); border: 1px solid var(--border-light); cursor: pointer; }
    .az-btn.has-brands:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .az-btn.no-brands { color: var(--border-light); background: transparent; font-weight: 500; }

    /* Brand list card */
    .brand-list-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; }
    .letter-section { }
    .letter-heading { padding: 0.625rem 1.25rem; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted-foreground); border-bottom: 1px solid var(--border-light); background: var(--muted); scroll-margin-top: 80px; }
    .letter-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem; padding: 1rem 1.25rem; }

    /* Brand card — matches dashboard quick-link style */
    .brand-card { display: flex; align-items: center; gap: 0.875rem; background: var(--card); border-radius: 8px; padding: 1.25rem; border: 1px solid var(--border-light); cursor: pointer; transition: border-color 0.2s; }
    .brand-card:hover { border-color: var(--foreground); }
    .brand-card.tech     { border-top: 2px solid var(--primary); }
    .brand-card.consumer { border-top: 2px solid #f59e0b; }
    .brand-card.both     { border-top: 2px solid var(--success); }

    .bc-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; overflow: hidden; }
    .bc-icon.tech     { background: rgba(87,87,248,0.12); color: var(--primary); }
    .bc-icon.consumer { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .bc-icon.both     { background: rgba(34,197,94,0.12);  color: var(--success); }
    .bc-icon.none     { background: var(--muted); color: var(--muted-foreground); }
    .bc-icon img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }

    .brand-card-body { flex: 1; min-width: 0; }
    .brand-card-name { display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--foreground); }
    .brand-card-desc { display: block; font-size: 0.78rem; color: var(--muted-foreground); font-weight: 500; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .brand-card-right { display: flex; align-items: center; gap: 0.375rem; flex-shrink: 0; }
    .brand-class-badge { display: inline-flex; align-items: center; padding: 0.18rem 0.6rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 700; white-space: nowrap; }
    .brand-class-badge.tech     { background: rgba(87,87,248,0.12); color: var(--primary); }
    .brand-class-badge.consumer { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .brand-class-badge.both     { background: rgba(34,197,94,0.12);  color: var(--success); }
    .brand-catalog-pill { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.18rem 0.6rem; border-radius: 9999px; background: var(--muted); border: 1px solid var(--border-light); font-size: 0.68rem; font-weight: 700; color: var(--muted-foreground); }
    .action-btns { display: flex; gap: 0.25rem; }
    .action-btn-sm { width: 28px; height: 28px; border: 1px solid var(--border-light); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; cursor: pointer; transition: border-color 0.15s; background: transparent; color: var(--muted-foreground); }
    .action-btn-sm:hover { border-color: var(--foreground); color: var(--foreground); }
    .action-btn-sm.btn-danger:hover { border-color: #dc2626; color: #dc2626; }

    .no-results { text-align: center; padding: 2.5rem; color: var(--muted-foreground); font-size: 0.85rem; display: none; }
    .no-results i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--border); }

    /* Back to top */
    #backToTop { position: fixed; bottom: 2rem; right: 2rem; width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; opacity: 0; pointer-events: none; transition: opacity 0.25s, transform 0.25s; transform: translateY(10px); z-index: 100; }
    #backToTop.visible { opacity: 1; pointer-events: all; transform: translateY(0); }
    #backToTop:hover { background: #4444e0; }

    /* Modal form fields */
    .form-group { display: flex; flex-direction: column; gap: 0.375rem; }
    .form-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); }
    .form-input, .form-select { height: 44px; padding: 0 0.875rem; background: var(--muted); border: 2px solid transparent; border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500; color: var(--fg); outline: none; transition: all 0.15s; width: 100%; }
    .form-input:focus, .form-select:focus { border-color: var(--primary); background: var(--white); }
    .form-input::placeholder { color: var(--gray-300); }
    .form-select { appearance: none; cursor: pointer; }
    .file-upload-area { border: 1.5px dashed var(--border-light); border-radius: 8px; padding: 0.875rem 1rem; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: border-color 0.15s; background: var(--muted); }
    .file-upload-area:hover { border-color: var(--primary); }
    .file-upload-area input[type="file"] { display: none; }
    .file-upload-icon { width: 32px; height: 32px; border-radius: 6px; background: var(--card); border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); font-size: 0.8rem; flex-shrink: 0; }
    .file-upload-label { display: flex; flex-direction: column; gap: 0.1rem; }
    .file-upload-label span:first-child { font-size: 0.8rem; font-weight: 600; color: var(--foreground); }
    .file-upload-label span:last-child { font-size: 0.72rem; color: var(--muted-foreground); }
    .file-upload-area.has-file { border-style: solid; border-color: var(--primary); }
    .file-upload-area.has-file .file-upload-icon { background: rgba(87,87,248,0.08); color: var(--primary); border-color: var(--primary); }
</style>
@endsection

@section('content')
<x-sidebar :isAdmin="true" active="admin.brands" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Brands <span class="highlight">Management</span></h2>
            <p>Manage brands for the catalog feature</p>
        </div>
        <button type="button" class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="openAddBrand()">
            <i class="fas fa-plus"></i> Add Brand
        </button>
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- KPI Stats (matching dashboard card format) -->
    <div class="brand-kpi-grid anim-up d1">
        <div class="brand-kpi-card total">
            <div class="bkpi-top">
                <span class="bkpi-label">Total Brands</span>
                <div class="bkpi-icon total"><i class="fas fa-tags"></i></div>
            </div>
            <div class="bkpi-value">{{ $stats['total'] }}</div>
            <div class="bkpi-sub">All classifications</div>
        </div>
        <div class="brand-kpi-card tech">
            <div class="bkpi-top">
                <span class="bkpi-label">Tech</span>
                <div class="bkpi-icon tech"><i class="fas fa-microchip"></i></div>
            </div>
            <div class="bkpi-value">{{ $stats['tech'] }}</div>
            <div class="bkpi-sub">Technology brands</div>
        </div>
        <div class="brand-kpi-card consumer">
            <div class="bkpi-top">
                <span class="bkpi-label">Design / Consumer</span>
                <div class="bkpi-icon consumer"><i class="fas fa-store"></i></div>
            </div>
            <div class="bkpi-value">{{ $stats['consumer'] }}</div>
            <div class="bkpi-sub">Consumer-facing brands</div>
        </div>
        <div class="brand-kpi-card both">
            <div class="bkpi-top">
                <span class="bkpi-label">Both</span>
                <div class="bkpi-icon both"><i class="fas fa-layer-group"></i></div>
            </div>
            <div class="bkpi-value">{{ $stats['both'] }}</div>
            <div class="bkpi-sub">Multi-category brands</div>
        </div>
    </div>

    <!-- Search + A-Z Controls -->
    @php
    $availableLetters = collect($brands->map(fn($b) => strtoupper(substr($b->name, 0, 1)))->unique()->values());
    @endphp
    <div class="brand-controls-card anim-up d2">
        <div class="brand-search-wrap">
            <i class="fas fa-magnifying-glass brand-search-icon"></i>
            <input type="text" id="brandSearch" class="brand-search-input" placeholder="Search brands by name..." oninput="filterBrands(this.value)">
            <button id="brandSearchClear" class="brand-search-clear" onclick="clearSearch()" style="display:none;"><i class="fas fa-times"></i></button>
        </div>
        <div>
            <div class="az-divider">Jump to letter</div>
            <div class="az-bar" id="azBar">
                @foreach(range('A', 'Z') as $letter)
                @if($availableLetters->contains($letter))
                <button class="az-btn has-brands" onclick="scrollToLetter('{{ $letter }}')">{{ $letter }}</button>
                @else
                <span class="az-btn no-brands">{{ $letter }}</span>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Brand List -->
    @if($brands->isEmpty())
    <div class="brand-list-card anim-up d3" style="text-align:center;padding:2.5rem;color:var(--muted-foreground);font-size:0.85rem;">
        <i class="fas fa-tag" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;color:var(--border);"></i>
        No brands yet. Add the first one.
    </div>
    @else
    <div class="brand-list-card anim-up d3" id="brandGrid">
        @php $grouped = $brands->groupBy(fn($b) => strtoupper(substr($b->name, 0, 1))); @endphp

        <div class="no-results" id="noResults">
            <i class="fas fa-magnifying-glass"></i>
            No brands match your search.
        </div>

        @foreach($grouped as $letter => $letterBrands)
        <div class="letter-section" id="letter-{{ $letter }}" data-letter="{{ $letter }}">
            <div class="letter-heading">{{ $letter }}</div>
            <div class="letter-cards">
                @foreach($letterBrands as $brand)
                @php
                $clsKey  = match($brand->classification) { 'Tech' => 'tech', 'Design/Consumer' => 'consumer', 'Both' => 'both', default => 'none' };
                $clsIcon = match($brand->classification) { 'Tech' => 'fa-microchip', 'Design/Consumer' => 'fa-store', 'Both' => 'fa-layer-group', default => 'fa-tag' };
                @endphp
                <div class="brand-card {{ $clsKey }}"
                     data-name="{{ strtolower($brand->name) }}"
                     onclick="window.location.href='{{ route('brand-catalogs') }}?brand_id={{ $brand->id }}'">
                    <div class="bc-icon {{ $clsKey }}">
                        @if($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}">
                        @else
                        <i class="fas {{ $clsIcon }}"></i>
                        @endif
                    </div>
                    <div class="brand-card-body">
                        <span class="brand-card-name">{{ $brand->name }}</span>
                        @if($brand->description)
                        <span class="brand-card-desc">{{ $brand->description }}</span>
                        @endif
                    </div>
                    <div class="brand-card-right">
                        @if($brand->classification)
                        <span class="brand-class-badge {{ $clsKey }}">{{ $brand->classification }}</span>
                        @endif
                        <span class="brand-catalog-pill">
                            <i class="fas fa-book-open"></i> {{ $brand->catalogs_count }}
                        </span>
                        <div class="action-btns">
                            <button class="action-btn-sm" title="Edit"
                                onclick="event.stopPropagation(); openEditBrand(this)"
                                data-id="{{ $brand->id }}"
                                data-name="{{ $brand->name }}"
                                data-description="{{ $brand->description ?? '' }}"
                                data-classification="{{ $brand->classification ?? '' }}"
                                data-logo="{{ $brand->logo ? asset('storage/' . $brand->logo) : '' }}">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}"
                                  onclick="event.stopPropagation()"
                                  onsubmit="return confirm({{ json_encode('Delete ' . $brand->name . '?') }});"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Back to top -->
<button id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Brand Modal -->
<div class="modal-overlay" id="brandModal">
    <div class="modal-box" style="max-width: 460px;">
        <div class="modal-header">
            <h5 id="brandModalTitle" style="font-weight: 700; font-size: 1rem; margin: 0;">Add Brand</h5>
            <button class="modal-close" onclick="closeModal('brandModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="brandForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="brandMethod" value="">
            <div class="modal-body" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="brandName" class="form-input" placeholder="e.g. Samsung" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <input type="text" name="description" id="brandDescription" class="form-input" placeholder="Short tagline">
                </div>
                <div class="form-group">
                    <label class="form-label">Classification</label>
                    <select name="classification" id="brandClassification" class="form-select">
                        <option value="">— Select —</option>
                        <option value="Tech">Tech</option>
                        <option value="Design/Consumer">Design / Consumer</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Logo <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(image, max 2MB)</span></label>
                    <div id="brandLogoArea" class="file-upload-area" onclick="document.getElementById('brandLogo').click()">
                        <div class="file-upload-icon"><i class="fas fa-image"></i></div>
                        <div class="file-upload-label">
                            <span id="brandLogoLabel">Click to choose logo</span>
                            <span>JPG, PNG, WEBP — max 2MB</span>
                        </div>
                        <input type="file" name="logo" id="brandLogo" accept=".jpg,.jpeg,.png,.webp" onchange="handleFileChange(this, 'brandLogoArea', 'brandLogoLabel')">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('brandModal')" style="height: 40px; font-size: 0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height: 40px; font-size: 0.85rem;">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function scrollToLetter(letter) {
    var el = document.getElementById('letter-' + letter);
    if (!el) return;
    var offset = el.getBoundingClientRect().top + window.scrollY - 90;
    window.scrollTo({ top: offset, behavior: 'smooth' });
}

function filterBrands(query) {
    var q = query.trim().toLowerCase();
    var sections = document.querySelectorAll('.letter-section');
    var clearBtn = document.getElementById('brandSearchClear');
    var azBar = document.getElementById('azBar');
    var azLabel = azBar.previousElementSibling;
    var noResults = document.getElementById('noResults');
    var totalVisible = 0;

    clearBtn.style.display = q ? '' : 'none';
    azBar.style.display = q ? 'none' : '';
    azLabel.style.display = q ? 'none' : '';

    sections.forEach(function(section) {
        var cards = section.querySelectorAll('.brand-card');
        var visible = 0;
        cards.forEach(function(card) {
            var match = !q || card.dataset.name.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        section.style.display = visible > 0 ? '' : 'none';
        totalVisible += visible;
    });

    noResults.style.display = (q && totalVisible === 0) ? 'block' : 'none';
}

function clearSearch() {
    var input = document.getElementById('brandSearch');
    input.value = '';
    filterBrands('');
    input.focus();
}

window.addEventListener('scroll', function() {
    var btn = document.getElementById('backToTop');
    if (window.scrollY > 320) { btn.classList.add('visible'); }
    else { btn.classList.remove('visible'); }
});

function handleFileChange(input, areaId, labelId) {
    var area = document.getElementById(areaId);
    var label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        area.classList.add('has-file');
        label.textContent = input.files[0].name;
    } else {
        area.classList.remove('has-file');
        label.textContent = 'Click to choose file';
    }
}

function openAddBrand() {
    document.getElementById('brandModalTitle').textContent = 'Add Brand';
    document.getElementById('brandForm').action = '{{ route("admin.brands.store") }}';
    document.getElementById('brandMethod').value = '';
    document.getElementById('brandForm').reset();
    document.getElementById('brandClassification').value = '';
    document.getElementById('brandLogoArea').classList.remove('has-file');
    document.getElementById('brandLogoLabel').textContent = 'Click to choose logo';
    openModal('brandModal');
}

function openEditBrand(btn) {
    var d = btn.dataset;
    document.getElementById('brandModalTitle').textContent = 'Edit Brand';
    document.getElementById('brandForm').action = '/admin/brands/' + d.id;
    document.getElementById('brandMethod').value = 'PUT';
    document.getElementById('brandName').value = d.name;
    document.getElementById('brandDescription').value = d.description;
    document.getElementById('brandClassification').value = d.classification || '';
    document.getElementById('brandLogo').value = '';
    if (d.logo) {
        document.getElementById('brandLogoArea').classList.add('has-file');
        document.getElementById('brandLogoLabel').textContent = 'Current logo';
    } else {
        document.getElementById('brandLogoArea').classList.remove('has-file');
        document.getElementById('brandLogoLabel').textContent = 'Click to choose logo';
    }
    openModal('brandModal');
}
</script>
@endsection
