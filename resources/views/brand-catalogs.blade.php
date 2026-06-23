@extends('layouts.app')

@section('title', 'Brand Catalogs — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('styles')
<style>
    .bc-tabs { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
    .bc-tab { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.875rem; border-radius: 9999px; border: 1px solid var(--border-light); background: var(--muted); color: var(--foreground); font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s; font-family: inherit; }
    .bc-tab:hover { border-color: var(--foreground); }
    .bc-tab.active { background: var(--primary); border-color: var(--primary); color: white; }
    .bc-status-tabs { margin-bottom: 1.5rem; }
    .bc-status-tab { font-size: 0.75rem; padding: 0.3rem 0.75rem; }

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
    .bc-badge.upcoming { background: rgba(87,87,248,0.12); color: var(--primary); }
    .bc-badge.seasonal { background: rgba(245,158,11,0.12); color: #f59e0b; }

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
</style>
@endsection

@section('content')
<x-sidebar active="brand-catalogs" />

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

    <!-- Brand filter tabs -->
    <div class="bc-tabs anim-up d1">
        <button class="bc-tab bc-brand-tab active" data-brand="all">All</button>
        @foreach($brands as $brand)
        <button class="bc-tab bc-brand-tab" data-brand="{{ $brand->id }}">{{ $brand->name }}</button>
        @endforeach
    </div>

    <!-- Status filter tabs -->
    <div class="bc-tabs bc-status-tabs anim-up d1">
        <button class="bc-tab bc-status-tab active" data-status="all">All</button>
        <button class="bc-tab bc-status-tab" data-status="available">Available</button>
        <button class="bc-tab bc-status-tab" data-status="upcoming">Upcoming</button>
        <button class="bc-tab bc-status-tab" data-status="seasonal">Seasonal</button>
    </div>

    @if($catalogs->isEmpty())
    <div class="bc-empty anim-up d2">
        <i class="fas fa-book-open"></i>
        No catalogs yet.@if(in_array($user->role, ['manager', 'researcher'])) Add the first one using the button above.@endif
    </div>
    @else
    @php
    $initialColors = ['#5757f8', '#10b981', '#f59e0b', '#f43f5e', '#6366f1', '#0ea5e9'];
    @endphp
    <div class="bc-grid anim-up d2">
        @foreach($catalogs as $catalog)
        @php $initColor = $initialColors[ord(strtoupper($catalog->brand->name[0])) % count($initialColors)]; @endphp
        <div class="bc-card" data-brand="{{ $catalog->brand_id }}" data-status="{{ $catalog->status }}">
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
                @if(in_array($user->role, ['manager', 'researcher']))
                <div class="bc-actions">
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
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
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
                    <select name="brand_id" id="catalogBrand" class="form-select" required>
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
                    <select name="status" id="catalogStatus" class="form-select" required>
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
                    <div id="catalogCurrentFile" style="font-size: 0.8rem; color: var(--muted-foreground); margin-bottom: 0.375rem;"></div>
                    <input type="file" name="file" id="catalogFile" accept=".pdf,.jpg,.jpeg,.png" style="font-size: 0.85rem;">
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
(function () {
    var brandTabs = document.querySelectorAll('.bc-brand-tab');
    var statusTabs = document.querySelectorAll('.bc-status-tab');
    var cards = document.querySelectorAll('.bc-card');
    var activeBrand = 'all';
    var activeStatus = 'all';

    function applyFilters() {
        cards.forEach(function (card) {
            var brandMatch = activeBrand === 'all' || card.dataset.brand === activeBrand;
            var statusMatch = activeStatus === 'all' || card.dataset.status === activeStatus;
            card.style.display = (brandMatch && statusMatch) ? '' : 'none';
        });
    }

    brandTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            brandTabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            activeBrand = tab.dataset.brand;
            applyFilters();
        });
    });

    statusTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            statusTabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            activeStatus = tab.dataset.status;
            applyFilters();
        });
    });
}());

function addCatalog() {
    document.getElementById('catalogModalTitle').textContent = 'Add Catalog';
    document.getElementById('catalogForm').reset();
    document.getElementById('catalogForm').action = '{{ route("brand-catalogs.store") }}';
    document.getElementById('catalogMethod').value = '';
    document.getElementById('catalogCurrentFile').textContent = '';
    openModal('catalogModal');
}

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
    openModal('catalogModal');
}
</script>
@endsection
