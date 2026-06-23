@extends('layouts.app')

@section('title', 'Brands — Admin')
@section('has-sidebar', true)

@section('styles')
<style>
    .brands-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .brands-table thead th { background: var(--muted); padding: 0.75rem 1rem; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); text-align: left; }
    .brands-table tbody td { padding: 0.75rem 1rem; border-top: 1px solid var(--border-light); font-weight: 500; vertical-align: middle; }
    .brands-table tbody tr:hover td { background: var(--muted); }
    .brand-logo-thumb { width: 32px; height: 32px; border-radius: 8px; object-fit: cover; }
    .brand-initial { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.85rem; background: var(--primary); }
    .action-btns { display: flex; gap: 0.25rem; }
    .action-btn-sm { width: 28px; height: 28px; border: 1px solid var(--border-light); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; cursor: pointer; transition: border-color 0.15s; background: transparent; color: var(--muted-foreground); }
    .action-btn-sm:hover { border-color: var(--foreground); color: var(--foreground); }
    .action-btn-sm.btn-danger:hover { border-color: #dc2626; color: #dc2626; }
    .logo-preview { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; display: none; margin-top: 0.5rem; }
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
        <div style="overflow-x: auto;">
            @if($brands->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--muted-foreground); font-size: 0.85rem;">
                <i class="fas fa-tag" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--border);"></i>
                No brands yet. Add the first one.
            </div>
            @else
            <table class="brands-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Name</th>
                        <th>Description</th>
                        <th style="text-align: center;">Catalogs</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                    <tr>
                        <td>
                            @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" class="brand-logo-thumb" alt="{{ $brand->name }}">
                            @else
                            <span class="brand-initial">{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 700;">{{ $brand->name }}</td>
                        <td style="color: var(--muted-foreground);">{{ $brand->description ?: '—' }}</td>
                        <td style="text-align: center;">{{ $brand->catalogs_count }}</td>
                        <td>
                            <div class="action-btns" style="justify-content: flex-end;">
                                <button class="action-btn-sm" title="Edit"
                                    onclick="openEditBrand(this)"
                                    data-id="{{ $brand->id }}"
                                    data-name="{{ $brand->name }}"
                                    data-description="{{ $brand->description ?? '' }}"
                                    data-logo="{{ $brand->logo ? asset('storage/' . $brand->logo) : '' }}">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete {{ addslashes($brand->name) }}?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
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
                    <img id="brandLogoPreview" src="" alt="Logo preview" class="logo-preview">
                    <input type="file" name="logo" id="brandLogo" accept=".jpg,.jpeg,.png,.svg,.webp" style="font-size: 0.85rem; margin-top: 0.375rem;" onchange="previewLogo(this)">
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
function openAddBrand() {
    document.getElementById('brandModalTitle').textContent = 'Add Brand';
    document.getElementById('brandForm').action = '{{ route("admin.brands.store") }}';
    document.getElementById('brandMethod').value = '';
    document.getElementById('brandForm').reset();
    document.getElementById('brandLogoPreview').style.display = 'none';
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
    var preview = document.getElementById('brandLogoPreview');
    if (d.logo) {
        preview.src = d.logo;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    openModal('brandModal');
}

function previewLogo(input) {
    var preview = document.getElementById('brandLogoPreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
