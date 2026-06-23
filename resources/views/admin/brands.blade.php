@extends('layouts.app')

@section('title', 'Brands — Admin')
@section('has-sidebar', true)

@section('styles')
<style>
    .brand-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; padding: 1.25rem; }
    .brand-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; padding: 1.5rem 1.25rem 1rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.375rem; transition: border-color 0.2s; }
    .brand-card:hover { border-color: var(--foreground); }
    .brand-card-logo { width: 64px; height: 64px; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.4rem; background: var(--primary); margin-bottom: 0.5rem; flex-shrink: 0; }
    .brand-card-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-card-name { font-weight: 700; font-size: 0.95rem; color: var(--foreground); }
    .brand-card-desc { font-size: 0.8rem; color: var(--muted-foreground); }
    .brand-card-footer { display: flex; align-items: center; justify-content: space-between; width: 100%; margin-top: 0.875rem; padding-top: 0.75rem; border-top: 1px solid var(--border-light); }
    .brand-catalog-pill { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.65rem; border-radius: 9999px; background: var(--muted); border: 1px solid var(--border-light); font-size: 0.72rem; font-weight: 700; color: var(--muted-foreground); }
    .action-btns { display: flex; gap: 0.25rem; }
    .action-btn-sm { width: 28px; height: 28px; border: 1px solid var(--border-light); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; cursor: pointer; transition: border-color 0.15s; background: transparent; color: var(--muted-foreground); }
    .action-btn-sm:hover { border-color: var(--foreground); color: var(--foreground); }
    .action-btn-sm.btn-danger:hover { border-color: #dc2626; color: #dc2626; }
    .form-group { display: flex; flex-direction: column; gap: 0.375rem; }
    .form-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); }
    .form-input { height: 44px; padding: 0 0.875rem; background: var(--muted); border: 2px solid transparent; border-radius: 8px; font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500; color: var(--fg); outline: none; transition: all 0.15s; width: 100%; }
    .form-input:focus { border-color: var(--primary); background: var(--white); }
    .form-input::placeholder { color: var(--gray-300); }

    .file-upload-area {
        border: 1.5px dashed var(--border-light);
        border-radius: 8px;
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: border-color 0.15s;
        background: var(--muted);
    }
    .file-upload-area:hover {
        border-color: var(--primary);
    }
    .file-upload-area input[type="file"] {
        display: none;
    }
    .file-upload-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: var(--card);
        border: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted-foreground);
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .file-upload-label {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }
    .file-upload-label span:first-child {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--foreground);
    }
    .file-upload-label span:last-child {
        font-size: 0.72rem;
        color: var(--muted-foreground);
    }
    .file-upload-area.has-file {
        border-style: solid;
        border-color: var(--primary);
    }
    .file-upload-area.has-file .file-upload-icon {
        background: rgba(87,87,248,0.08);
        color: var(--primary);
        border-color: var(--primary);
    }
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

    <div class="eod-card anim-up d1">
        <div class="eod-card-header">
            <div class="t-icon"><i class="fas fa-tag"></i></div>
            Brands ({{ $brands->count() }})
        </div>
        @if($brands->isEmpty())
        <div style="text-align: center; padding: 2.5rem; color: var(--muted-foreground); font-size: 0.85rem;">
            <i class="fas fa-tag" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--border);"></i>
            No brands yet. Add the first one.
        </div>
        @else
        <div class="brand-grid">
            @foreach($brands as $brand)
            <div class="brand-card">
                <div class="brand-card-logo">
                    @if($brand->logo)
                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}">
                    @else
                    {{ strtoupper(substr($brand->name, 0, 1)) }}
                    @endif
                </div>
                <div class="brand-card-name">{{ $brand->name }}</div>
                @if($brand->description)
                <div class="brand-card-desc">{{ $brand->description }}</div>
                @endif
                <div class="brand-card-footer">
                    <span class="brand-catalog-pill">
                        <i class="fas fa-book-open"></i>
                        {{ $brand->catalogs_count }} {{ Str::plural('catalog', $brand->catalogs_count) }}
                    </span>
                    <div class="action-btns">
                        <button class="action-btn-sm" title="Edit"
                            onclick="openEditBrand(this)"
                            data-id="{{ $brand->id }}"
                            data-name="{{ $brand->name }}"
                            data-description="{{ $brand->description ?? '' }}"
                            data-logo="{{ $brand->logo ? asset('storage/' . $brand->logo) : '' }}">
                            <i class="fas fa-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm({{ json_encode('Delete ' . $brand->name . '?') }});" style="display:inline;">
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
        @endif
    </div>
</div>

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
                    <input type="text" name="name" id="brandName" class="form-input" placeholder="e.g. Samsung" required style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Description <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span></label>
                    <input type="text" name="description" id="brandDescription" class="form-input" placeholder="Short tagline" style="width: 100%;">
                </div>
                <div class="form-group">
                    <label class="form-label">Logo <span style="font-weight: 400; text-transform: none; letter-spacing: 0;">(image, max 2MB)</span></label>
                    <div id="brandLogoArea" class="file-upload-area" onclick="document.getElementById('brandLogo').click()">
                        <div class="file-upload-icon" id="brandLogoIconBox"><i class="fas fa-image"></i></div>
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
