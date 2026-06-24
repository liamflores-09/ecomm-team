@extends('layouts.app')

@section('title', 'Brand Catalogs — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('styles')
<style>
    .bc-filter-bar { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
    .bc-filter-tab { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.875rem; border-radius: 9999px; border: 1px solid var(--border-light); background: var(--muted); color: var(--foreground); font-size: 0.8rem; font-weight: 600; text-decoration: none; transition: all 0.15s; }
    .bc-filter-tab:hover { border-color: var(--foreground); color: var(--foreground); }
    .bc-filter-tab.active { background: var(--primary); border-color: var(--primary); color: white; }
    .bc-filter-tab.tech.active   { background: #5757f8; border-color: #5757f8; }
    .bc-filter-tab.consumer.active { background: #f59e0b; border-color: #f59e0b; }
    .bc-filter-tab.both.active   { background: var(--success); border-color: var(--success); }

    .bc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media (max-width: 768px) { .bc-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .bc-grid { grid-template-columns: 1fr; } }

    .bc-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem; transition: border-color 0.2s; }
    .bc-card:hover { border-color: var(--foreground); }

    .bc-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem; }
    .bc-logo { width: 40px; height: 40px; border-radius: 8px; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1rem; }
    .bc-logo img { width: 100%; height: 100%; object-fit: cover; }

    .bc-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .bc-badge.available { background: rgba(34,197,94,0.12); color: var(--success); }
    .bc-badge.upcoming  { background: rgba(87,87,248,0.12);  color: var(--primary); }
    .bc-badge.seasonal  { background: rgba(245,158,11,0.12); color: #f59e0b; }

    .bc-title { font-weight: 700; font-size: 0.9rem; color: var(--foreground); }
    .bc-brand-name { font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500; }
    .bc-notes { font-size: 0.8rem; color: var(--muted-foreground); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .bc-meta { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 0.625rem; border-top: 1px solid var(--border-light); }
    .bc-meta-left { display: flex; align-items: center; gap: 0.5rem; }
    .bc-icon-link { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; color: var(--muted-foreground); font-size: 0.7rem; text-decoration: none; transition: border-color 0.15s; }
    .bc-icon-link:hover { border-color: var(--foreground); color: var(--foreground); }
    .bc-date { font-size: 0.7rem; color: var(--muted-foreground); }

    .bc-actions { display: flex; align-items: center; gap: 0.25rem; }
    .bc-action-btn { width: 28px; height: 28px; border: 1px solid var(--border-light); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; cursor: pointer; transition: border-color 0.15s; background: transparent; color: var(--muted-foreground); }
    .bc-action-btn:hover { border-color: var(--foreground); color: var(--foreground); }
    .bc-action-btn.btn-danger:hover { border-color: #dc2626; color: #dc2626; }

    .bc-empty { text-align: center; padding: 3rem; color: var(--muted-foreground); }
    .bc-empty i { font-size: 2rem; margin-bottom: 0.75rem; display: block; color: var(--border); }

    .bc-brand-bar {
        display: flex; align-items: center; gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding: 0.75rem 1rem;
        background: var(--card); border: 1px solid var(--border-light);
        border-radius: 10px;
    }
    .bc-brand-bar-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--muted-foreground);
        white-space: nowrap; flex-shrink: 0;
    }
    .bc-brand-bar .form-select { max-width: 280px; width: auto; flex: 1; }
    .bc-brand-bar-clear {
        font-size: 0.78rem; font-weight: 600; color: var(--muted-foreground);
        text-decoration: none; white-space: nowrap; flex-shrink: 0;
        transition: color 0.15s;
    }
    .bc-brand-bar-clear:hover { color: var(--foreground); }

    .bc-pagination-row { display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; gap: 1rem; flex-wrap: wrap; }
    .bc-pagination-count { font-size: 0.78rem; color: var(--muted-foreground); font-weight: 500; }
    .bc-pagination { display: flex; align-items: center; gap: 0.375rem; }
    .bc-page-btn {
        min-width: 36px; height: 36px; padding: 0 0.625rem;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; font-size: 0.85rem; font-weight: 600;
        text-decoration: none; color: var(--foreground);
        background: var(--card); border: 1px solid var(--border-light);
        transition: all 0.15s; cursor: pointer;
    }
    .bc-page-btn:hover { border-color: var(--foreground); }
    .bc-page-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
    .bc-page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .bc-page-info { font-size: 0.78rem; color: var(--muted-foreground); font-weight: 500; padding: 0 0.25rem; }

    .form-group { display: flex; flex-direction: column; gap: 0.375rem; }
    .form-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); }
    .form-input { height: 44px; padding: 0 0.875rem; background: var(--muted); border: 2px solid transparent; border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500; color: var(--fg); outline: none; transition: all 0.15s; width: 100%; }
    .form-input:focus { border-color: var(--primary); background: var(--white); }
    .form-input::placeholder { color: var(--gray-300); }
    .form-select { height: 44px; padding: 0 0.875rem; background: var(--muted); border: 2px solid transparent; border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500; color: var(--fg); outline: none; cursor: pointer; transition: all 0.15s; width: 100%; appearance: auto; }
    .form-select:focus { border-color: var(--primary); background: var(--white); }
    .form-textarea { padding: 0.75rem 0.875rem; background: var(--muted); border: 2px solid transparent; border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500; color: var(--fg); outline: none; resize: vertical; min-height: 80px; transition: all 0.15s; width: 100%; }
    .form-textarea:focus { border-color: var(--primary); background: var(--white); }

    /* Side drawer */
    .bc-drawer-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:198; opacity:0; pointer-events:none; transition:opacity 0.25s; }
    .bc-drawer-overlay.open { opacity:1; pointer-events:all; }
    .bc-drawer { position:fixed; top:0; right:0; bottom:0; width:420px; max-width:90vw; background:var(--card); border-left:1px solid var(--border-light); z-index:199; transform:translateX(100%); transition:transform 0.3s cubic-bezier(0.4,0,0.2,1); display:flex; flex-direction:column; overflow:hidden; }
    .bc-drawer.open { transform:translateX(0); }
    .bc-drawer-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--border-light); flex-shrink:0; }
    .bc-drawer-logo { width:44px; height:44px; border-radius:8px; overflow:hidden; flex-shrink:0; display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:1rem; }
    .bc-drawer-logo img { width:100%; height:100%; object-fit:cover; }
    .bc-drawer-brand { font-weight:700; font-size:0.9rem; color:var(--foreground); }
    .bc-drawer-close { width:32px; height:32px; border:1px solid var(--border-light); border-radius:6px; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--muted-foreground); font-size:0.8rem; transition:all 0.15s; }
    .bc-drawer-close:hover { border-color:var(--foreground); color:var(--foreground); }
    .bc-drawer-body { flex:1; overflow-y:auto; padding:1.25rem; display:flex; flex-direction:column; gap:1.25rem; }
    .bc-drawer-footer { padding:1rem 1.25rem; border-top:1px solid var(--border-light); display:flex; gap:0.5rem; flex-shrink:0; }
    .bc-drawer-section-label { font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--muted-foreground); margin-bottom:0.4rem; }
    .bc-drawer-title { font-size:1.05rem; font-weight:800; color:var(--foreground); line-height:1.35; font-family:'Space Grotesk', sans-serif; }
    .bc-drawer-notes { font-size:0.85rem; color:var(--foreground); line-height:1.65; white-space:pre-line; }
    .bc-res-btn { display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 0.875rem; border:1px solid var(--border-light); border-radius:6px; font-size:0.8rem; font-weight:600; color:var(--foreground); text-decoration:none; transition:border-color 0.15s; }
    .bc-res-btn:hover { border-color:var(--foreground); }
    .brand-class-badge { display:inline-flex; align-items:center; padding:0.18rem 0.6rem; border-radius:9999px; font-size:0.68rem; font-weight:700; white-space:nowrap; }
    .brand-class-badge.tech     { background:rgba(87,87,248,0.12); color:var(--primary); }
    .brand-class-badge.consumer { background:rgba(245,158,11,0.12); color:#f59e0b; }
    .brand-class-badge.both     { background:rgba(34,197,94,0.12);  color:var(--success); }

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
<x-sidebar :isAdmin="$user->role === 'manager'" active="brand-catalogs" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>Brand <span class="highlight">Catalogs</span></h2>
            <p>Browse brand catalogs and upcoming product lists</p>
        </div>
        @if(in_array($user->role, ['manager', 'researcher']))
        <button type="button" class="btn-flat-primary" style="height: 40px; padding: 0 1rem; font-size: 0.85rem;" onclick="addCatalog()">
            <i class="fas fa-plus"></i> Add Catalog
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-flat success anim-fade"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-flat danger anim-fade"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
    @endif

    <!-- Classification tabs -->
    <div class="bc-filter-bar anim-up d1">
        <a href="{{ route('brand-catalogs', $brandId ? ['brand_id' => $brandId] : []) }}"
           class="bc-filter-tab {{ !$classification ? 'active' : '' }}">All</a>
        <a href="{{ route('brand-catalogs', array_merge(['classification' => 'Tech'], $brandId ? ['brand_id' => $brandId] : [])) }}"
           class="bc-filter-tab tech {{ $classification === 'Tech' ? 'active' : '' }}">Tech</a>
        <a href="{{ route('brand-catalogs', array_merge(['classification' => 'Design/Consumer'], $brandId ? ['brand_id' => $brandId] : [])) }}"
           class="bc-filter-tab consumer {{ $classification === 'Design/Consumer' ? 'active' : '' }}">Design / Consumer</a>
        <a href="{{ route('brand-catalogs', array_merge(['classification' => 'Both'], $brandId ? ['brand_id' => $brandId] : [])) }}"
           class="bc-filter-tab both {{ $classification === 'Both' ? 'active' : '' }}">Both</a>
    </div>

    <!-- Brand filter -->
    <div class="bc-brand-bar anim-up d2">
        <span class="bc-brand-bar-label"><i class="fas fa-tag" style="margin-right:0.375rem;"></i>Brand</span>
        <select class="form-select" onchange="filterByBrand(this.value)">
            <option value="">All brands</option>
            @foreach($brands as $brand)
            <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
            @endforeach
        </select>
        @if($brandId)
        <a href="{{ route('brand-catalogs', $classification ? ['classification' => $classification] : []) }}" class="bc-brand-bar-clear">
            <i class="fas fa-times" style="margin-right:0.25rem;"></i>Clear
        </a>
        @endif
    </div>

    @if($catalogs->isEmpty())
    <div class="bc-empty anim-up d2">
        <i class="fas fa-book-open"></i>
        No catalogs found.@if(in_array($user->role, ['manager', 'researcher'])) Add one using the button above.@endif
    </div>
    @else
    @php
    $initialColors = ['#5757f8', '#10b981', '#f59e0b', '#f43f5e', '#6366f1', '#0ea5e9'];
    @endphp
    <div class="bc-grid anim-up d2">
        @foreach($catalogs as $catalog)
        @php $initColor = $initialColors[ord(strtoupper($catalog->brand->name[0])) % count($initialColors)]; @endphp
        <div class="bc-card">
            <div class="bc-card-top">
                <div class="bc-logo" @if(!$catalog->brand->logo) style="background: {{ $initColor }};" @endif>
                    @if($catalog->brand->logo)
                    <img src="{{ asset('storage/' . $catalog->brand->logo) }}" alt="{{ $catalog->brand->name }}">
                    @else
                    {{ strtoupper(substr($catalog->brand->name, 0, 1)) }}
                    @endif
                </div>
                <span class="bc-badge {{ $catalog->status }}">{{ ucfirst($catalog->status) }}</span>
            </div>
            <div class="bc-title">{{ $catalog->title }}</div>
            <div class="bc-brand-name">{{ $catalog->brand->name }}</div>
            @if($catalog->notes)
            <div class="bc-notes">{{ $catalog->notes }}</div>
            @endif
            <div class="bc-meta">
                <div class="bc-meta-left">
                    @if($catalog->link)
                    <a href="{{ $catalog->link }}" target="_blank" rel="noopener" class="bc-icon-link" title="Open link"><i class="fas fa-link"></i></a>
                    @endif
                    @if($catalog->file_path)
                    <a href="{{ asset('storage/' . $catalog->file_path) }}" target="_blank" class="bc-icon-link" title="View file"><i class="fas fa-file"></i></a>
                    @endif
                    <span class="bc-date">{{ $catalog->created_at->format('M j, Y') }}</span>
                </div>
                <div class="bc-actions">
                    <button class="bc-action-btn" title="View full details" onclick="openDetails({{ $catalog->id }})">
                        <i class="fas fa-up-right-and-down-left-from-center"></i>
                    </button>
                    @if(in_array($user->role, ['manager', 'researcher']))
                    <button class="bc-action-btn" title="Edit"
                        onclick="editCatalog(this)"
                        data-id="{{ $catalog->id }}"
                        data-brand="{{ $catalog->brand_id }}"
                        data-title="{{ $catalog->title }}"
                        data-notes="{{ $catalog->notes ?? '' }}"
                        data-status="{{ $catalog->status }}"
                        data-link="{{ $catalog->link ?? '' }}"
                        data-file="{{ $catalog->file_path ? basename($catalog->file_path) : '' }}">
                        <i class="fas fa-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('brand-catalogs.destroy', $catalog) }}" onsubmit="return confirm('Delete this catalog?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bc-action-btn btn-danger" title="Delete"><i class="fas fa-trash-can"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($catalogs->hasPages())
    <div class="bc-pagination-row">
        <span class="bc-pagination-count">Showing {{ $catalogs->firstItem() }}–{{ $catalogs->lastItem() }} of {{ $catalogs->total() }} catalogs</span>
        <div class="bc-pagination">
            @if($catalogs->onFirstPage())
            <span class="bc-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
            @else
            <a class="bc-page-btn" href="{{ $catalogs->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($catalogs->getUrlRange(1, $catalogs->lastPage()) as $page => $url)
                @if(abs($page - $catalogs->currentPage()) <= 2 || $page === 1 || $page === $catalogs->lastPage())
                <a class="bc-page-btn {{ $page == $catalogs->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                @elseif(abs($page - $catalogs->currentPage()) === 3)
                <span class="bc-page-info">…</span>
                @endif
            @endforeach

            @if($catalogs->hasMorePages())
            <a class="bc-page-btn" href="{{ $catalogs->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
            <span class="bc-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif

    @endif
</div>

<!-- Side Drawer -->
<div class="bc-drawer-overlay" id="drawerOverlay" onclick="closeDetails()"></div>
<div class="bc-drawer" id="detailsDrawer">
    <div class="bc-drawer-header">
        <div style="display:flex;align-items:center;gap:0.875rem;">
            <div class="bc-drawer-logo" id="drawerLogo"></div>
            <div>
                <div class="bc-drawer-brand" id="drawerBrandName"></div>
                <div id="drawerBrandCls" style="margin-top:0.2rem;"></div>
            </div>
        </div>
        <button class="bc-drawer-close" onclick="closeDetails()"><i class="fas fa-times"></i></button>
    </div>
    <div class="bc-drawer-body" id="drawerBody"></div>
    <div class="bc-drawer-footer" id="drawerFooter" style="display:none;"></div>
</div>

<!-- Catalog Modal -->
<div class="modal-overlay" id="catalogModal">
    <div class="modal-box" style="max-width: 520px;">
        <div class="modal-header">
            <h5 id="catalogModalTitle" style="font-weight: 700; font-size: 1rem; margin: 0;">Add Catalog</h5>
            <button class="modal-close" onclick="closeModal('catalogModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="catalogForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="catalogMethod" value="">
            <div class="modal-body" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" id="catalogBrand" class="form-select" style="width: 100%;" required>
                        <option value="">— Select brand —</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="catalogTitle" class="form-input" placeholder="e.g. Samsung Q3 2026 New Arrivals" required style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="catalogStatus" class="form-select" style="width: 100%;" required>
                        <option value="available">Available</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="seasonal">Seasonal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <textarea name="notes" id="catalogNotes" class="form-textarea" placeholder="What's notable about this catalog?" style="min-height: 70px; width: 100%;"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">External Link <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <input type="url" name="link" id="catalogLink" class="form-input" placeholder="https://drive.google.com/..." style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Upload File <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(PDF or image, max 10MB)</span></label>
                    <div id="catalogFileArea" class="file-upload-area" onclick="document.getElementById('catalogFile').click()">
                        <div class="file-upload-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                        <div class="file-upload-label">
                            <span id="catalogFileLabel">Click to choose file</span>
                            <span>PDF, JPG, PNG — max 10MB</span>
                        </div>
                        <input type="file" name="file" id="catalogFile" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileChange(this, 'catalogFileArea', 'catalogFileLabel')">
                    </div>
                    <div id="catalogCurrentFile" style="font-size: 0.8rem; color: var(--muted-foreground); margin-top: 0.375rem;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-flat-secondary" onclick="closeModal('catalogModal')" style="height: 40px; font-size: 0.85rem;">Cancel</button>
                <button type="submit" class="btn-flat-primary" style="height: 40px; font-size: 0.85rem;">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterByBrand(val) {
    var base = '{{ route("brand-catalogs") }}';
    var cls  = '{{ $classification ?? "" }}';
    var params = [];
    if (val)  params.push('brand_id=' + val);
    if (cls)  params.push('classification=' + encodeURIComponent(cls));
    window.location.href = base + (params.length ? '?' + params.join('&') : '');
}

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

function addCatalog() {
    document.getElementById('catalogModalTitle').textContent = 'Add Catalog';
    document.getElementById('catalogForm').reset();
    document.getElementById('catalogForm').action = '{{ route("brand-catalogs.store") }}';
    document.getElementById('catalogMethod').value = '';
    document.getElementById('catalogCurrentFile').textContent = '';
    document.getElementById('catalogFileArea').classList.remove('has-file');
    document.getElementById('catalogFileLabel').textContent = 'Click to choose file';
    openModal('catalogModal');
}

@php
$_catalogJson = $catalogs->getCollection()->map(function($c) {
    return [
        'id'         => $c->id,
        'brand_id'   => $c->brand_id,
        'brand_name' => $c->brand->name,
        'brand_logo' => $c->brand->logo ? asset('storage/' . $c->brand->logo) : null,
        'brand_cls'  => $c->brand->classification,
        'title'      => $c->title,
        'status'     => $c->status,
        'notes'      => $c->notes,
        'link'       => $c->link,
        'file_url'   => $c->file_path ? asset('storage/' . $c->file_path) : null,
        'file_name'  => $c->file_path ? basename($c->file_path) : null,
        'date'       => $c->created_at->format('F j, Y'),
    ];
})->keyBy('id');
@endphp
var catalogData = @json($_catalogJson);
var canEdit = {{ in_array($user->role, ['manager', 'researcher']) ? 'true' : 'false' }};
var _activeDetail = null;

function esc(s) { if (s == null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function openDetails(id) {
    var c = catalogData[id];
    if (!c) return;
    _activeDetail = id;

    var logoEl = document.getElementById('drawerLogo');
    if (c.brand_logo) {
        logoEl.innerHTML = '<img src="' + esc(c.brand_logo) + '" alt="">';
        logoEl.style.background = '';
    } else {
        var bgMap = {'Tech':'#5757f8','Design/Consumer':'#f59e0b','Both':'#22c55e'};
        logoEl.style.background = bgMap[c.brand_cls] || '#6366f1';
        logoEl.textContent = c.brand_name.charAt(0).toUpperCase();
    }
    document.getElementById('drawerBrandName').textContent = c.brand_name;

    var clsEl = document.getElementById('drawerBrandCls');
    if (c.brand_cls) {
        var clsMap = {'Tech':'tech','Design/Consumer':'consumer','Both':'both'};
        clsEl.innerHTML = '<span class="brand-class-badge ' + (clsMap[c.brand_cls]||'') + '">' + esc(c.brand_cls) + '</span>';
    } else { clsEl.innerHTML = ''; }

    var html = '<div>' +
        '<div class="bc-drawer-title">' + esc(c.title) + '</div>' +
        '<div style="margin-top:0.5rem;"><span class="bc-badge ' + esc(c.status) + '">' + cap(c.status) + '</span></div>' +
    '</div>';

    if (c.notes) {
        html += '<div><div class="bc-drawer-section-label">Notes</div>' +
            '<div class="bc-drawer-notes">' + esc(c.notes) + '</div></div>';
    }

    var res = '';
    if (c.link) res += '<a href="' + esc(c.link) + '" target="_blank" rel="noopener" class="bc-res-btn"><i class="fas fa-link"></i> Open Link</a>';
    if (c.file_url) res += '<a href="' + esc(c.file_url) + '" target="_blank" class="bc-res-btn"><i class="fas fa-file"></i> ' + esc(c.file_name) + '</a>';
    if (res) html += '<div><div class="bc-drawer-section-label">Resources</div><div style="display:flex;gap:0.5rem;flex-wrap:wrap;">' + res + '</div></div>';

    html += '<div style="font-size:0.78rem;color:var(--muted-foreground);">Added ' + esc(c.date) + '</div>';
    document.getElementById('drawerBody').innerHTML = html;

    var footer = document.getElementById('drawerFooter');
    if (canEdit) {
        footer.style.display = '';
        footer.innerHTML =
            '<button class="btn-flat-primary" style="height:36px;font-size:0.82rem;flex:1;" onclick="editFromDetails()">' +
                '<i class="fas fa-pencil"></i> Edit Catalog' +
            '</button>' +
            '<form method="POST" action="/brand-catalogs/' + id + '" onsubmit="return confirm(\'Delete this catalog?\')" style="display:contents;">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<button type="submit" class="btn-flat-secondary" style="height:36px;font-size:0.82rem;padding:0 0.875rem;">' +
                    '<i class="fas fa-trash-can" style="color:#dc2626;"></i>' +
                '</button>' +
            '</form>';
    } else {
        footer.style.display = 'none';
    }

    document.getElementById('drawerOverlay').classList.add('open');
    document.getElementById('detailsDrawer').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function editFromDetails() {
    var c = catalogData[_activeDetail];
    if (!c) return;
    closeDetails();
    setTimeout(function() {
        document.getElementById('catalogModalTitle').textContent = 'Edit Catalog';
        document.getElementById('catalogForm').action = '/brand-catalogs/' + c.id;
        document.getElementById('catalogMethod').value = 'PUT';
        document.getElementById('catalogBrand').value = c.brand_id;
        document.getElementById('catalogTitle').value = c.title;
        document.getElementById('catalogStatus').value = c.status;
        document.getElementById('catalogNotes').value = c.notes || '';
        document.getElementById('catalogLink').value = c.link || '';
        document.getElementById('catalogCurrentFile').textContent = c.file_name ? 'Current file: ' + c.file_name : '';
        document.getElementById('catalogFile').value = '';
        if (c.file_name) {
            document.getElementById('catalogFileArea').classList.add('has-file');
            document.getElementById('catalogFileLabel').textContent = c.file_name;
        } else {
            document.getElementById('catalogFileArea').classList.remove('has-file');
            document.getElementById('catalogFileLabel').textContent = 'Click to choose file';
        }
        openModal('catalogModal');
    }, 320);
}

function closeDetails() {
    document.getElementById('drawerOverlay').classList.remove('open');
    document.getElementById('detailsDrawer').classList.remove('open');
    document.body.style.overflow = '';
    _activeDetail = null;
}

document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDetails(); });

function editCatalog(btn) {
    var d = btn.dataset;
    document.getElementById('catalogModalTitle').textContent = 'Edit Catalog';
    document.getElementById('catalogForm').action = '/brand-catalogs/' + d.id;
    document.getElementById('catalogMethod').value = 'PUT';
    document.getElementById('catalogBrand').value = d.brand;
    document.getElementById('catalogTitle').value = d.title;
    document.getElementById('catalogStatus').value = d.status;
    document.getElementById('catalogNotes').value = d.notes;
    document.getElementById('catalogLink').value = d.link;
    document.getElementById('catalogCurrentFile').textContent = d.file ? 'Current file: ' + d.file : '';
    document.getElementById('catalogFile').value = '';
    if (d.file) {
        document.getElementById('catalogFileArea').classList.add('has-file');
        document.getElementById('catalogFileLabel').textContent = d.file;
    } else {
        document.getElementById('catalogFileArea').classList.remove('has-file');
        document.getElementById('catalogFileLabel').textContent = 'Click to choose file';
    }
    openModal('catalogModal');
}
</script>
@endsection
