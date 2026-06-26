@extends('layouts.app')

@section('title', 'Announcements — Ecomm Dept')
@section('has-sidebar', true)

@section('styles')
<style>
    /* ── Full-height split layout ───────────────────────────── */
    .main-content:has(.ann-split) { padding: 0; display: flex; flex-direction: column; overflow: hidden; }
    .ann-split {
        display: grid;
        grid-template-columns: 300px 1fr;
        height: calc(100vh - 56px); /* subtract topbar */
        overflow: hidden;
    }

    /* ── LEFT: List panel ───────────────────────────────────── */
    .ann-list-panel {
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column;
        background: var(--background); overflow: hidden;
    }
    .ann-list-hd {
        padding: 1rem 1rem 0.75rem;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
    .ann-list-hd-top {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .ann-list-hd-title { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted-foreground); }
    .ann-new-btn {
        height: 30px; padding: 0 0.75rem; border-radius: 7px;
        background: var(--primary); color: white; border: none;
        font-family: var(--p-font-family-sans); font-size: 0.75rem; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; gap: 0.35rem;
        transition: opacity 0.15s;
    }
    .ann-new-btn:hover { opacity: 0.88; }

    /* Search */
    .ann-search-wrap { position: relative; }
    .ann-search-wrap i { position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); color: var(--gray-400); font-size: 0.7rem; pointer-events: none; }
    .ann-search {
        width: 100%; height: 32px; padding: 0 0.625rem 0 1.875rem;
        background: var(--muted); border: 1.5px solid transparent;
        border-radius: 7px; font-family: var(--p-font-family-sans);
        font-size: 0.8rem; color: var(--fg); outline: none; box-sizing: border-box;
        transition: border-color 0.15s;
    }
    .ann-search:focus { border-color: var(--primary); background: var(--card); }

    /* List */
    .ann-list-scroll { flex: 1; overflow-y: auto; }
    .ann-list-section-label {
        padding: 0.625rem 1rem 0.25rem;
        font-size: 0.6rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.08em; color: var(--gray-400);
    }

    .ann-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-light);
        cursor: pointer; transition: background 0.12s;
        position: relative;
    }
    .ann-item:hover { background: var(--muted); }
    .ann-item.active { background: color-mix(in srgb, var(--primary) 10%, transparent); }
    .ann-item.active::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0;
        width: 3px; background: var(--primary); border-radius: 0 2px 2px 0;
    }

    .ann-item-top { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem; }
    .ann-item-pin { color: #f59e0b; font-size: 0.6rem; flex-shrink: 0; }
    .ann-item-title {
        font-size: 0.82rem; font-weight: 700; line-height: 1.25;
        color: var(--fg); flex: 1; overflow: hidden;
        white-space: nowrap; text-overflow: ellipsis;
    }
    .ann-item.active .ann-item-title { color: var(--primary); }
    .ann-item-preview {
        font-size: 0.75rem; color: var(--muted-foreground); font-weight: 500;
        line-height: 1.4; overflow: hidden; white-space: nowrap;
        text-overflow: ellipsis; margin-bottom: 0.35rem;
    }
    .ann-item-foot { display: flex; align-items: center; gap: 0.5rem; }
    .ann-item-date { font-size: 0.68rem; font-weight: 600; color: var(--gray-400); }
    .ann-item-badge {
        font-size: 0.58rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.04em; padding: 0.1rem 0.4rem; border-radius: 9999px;
    }
    .ann-item-badge.pinned { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .ann-item-badge.expired { background: rgba(239,68,68,0.1); color: #ef4444; }
    .ann-item-badge.expiring { background: rgba(245,158,11,0.1); color: #d97706; }

    .ann-list-empty {
        text-align: center; padding: 2.5rem 1rem;
        color: var(--muted-foreground); font-size: 0.8rem;
    }
    .ann-list-empty i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.3; }

    /* Pagination */
    .ann-list-pager {
        padding: 0.625rem 1rem; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        font-size: 0.72rem; font-weight: 600; color: var(--muted-foreground);
        flex-shrink: 0;
    }
    .ann-list-pager a {
        color: var(--primary); text-decoration: none; font-weight: 700;
    }
    .ann-list-pager a:hover { text-decoration: underline; }

    /* ── RIGHT: Detail panel ────────────────────────────────── */
    .ann-detail-panel {
        display: flex; flex-direction: column;
        background: var(--card); overflow: hidden;
    }

    /* Empty state */
    .ann-detail-empty {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: var(--muted-foreground); gap: 0.75rem;
    }
    .ann-detail-empty-icon {
        width: 64px; height: 64px; border-radius: 16px;
        background: var(--muted); display: flex; align-items: center;
        justify-content: center; font-size: 1.5rem;
        color: var(--muted-foreground); opacity: 0.5;
    }
    .ann-detail-empty p { font-size: 0.875rem; font-weight: 500; margin: 0; }

    /* Detail content */
    .ann-detail-content { display: none; flex-direction: column; height: 100%; overflow: hidden; }
    .ann-detail-content.visible { display: flex; }

    .ann-detail-topbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.875rem 1.5rem; border-bottom: 1px solid var(--border);
        flex-shrink: 0; gap: 1rem;
    }
    .ann-detail-topbar-left { display: flex; align-items: center; gap: 0.5rem; }
    .ann-detail-actions { display: flex; gap: 0.375rem; }
    .ann-det-btn {
        height: 32px; padding: 0 0.75rem; border: 1px solid var(--border-light);
        border-radius: 7px; background: transparent; font-family: var(--p-font-family-sans);
        font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground);
        cursor: pointer; display: flex; align-items: center; gap: 0.35rem;
        transition: all 0.15s;
    }
    .ann-det-btn:hover { border-color: var(--foreground); color: var(--foreground); }
    .ann-det-btn.pin-on { border-color: #f59e0b; color: #f59e0b; background: rgba(245,158,11,0.06); }
    .ann-det-btn.danger:hover { border-color: #dc2626; color: #dc2626; }

    .ann-detail-body { flex: 1; overflow-y: auto; padding: 2rem 2.5rem; }

    .ann-detail-pin-strip {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 0.875rem; background: rgba(245,158,11,0.08);
        border: 1px solid rgba(245,158,11,0.2); border-radius: 8px;
        font-size: 0.75rem; font-weight: 700; color: #d97706;
        margin-bottom: 1.25rem;
    }
    .ann-detail-title {
        font-size: 1.5rem; font-weight: 800; line-height: 1.25;
        color: var(--fg); margin-bottom: 1rem;
    }
    .ann-detail-meta {
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
        margin-bottom: 1.5rem; padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-light);
    }
    .ann-meta-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        border: 2px solid var(--border); flex-shrink: 0;
    }
    .ann-meta-info { display: flex; flex-direction: column; gap: 0.1rem; }
    .ann-meta-name { font-size: 0.8rem; font-weight: 700; color: var(--fg); }
    .ann-meta-date { font-size: 0.72rem; font-weight: 500; color: var(--muted-foreground); }
    .ann-meta-chip {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.2rem 0.6rem; border-radius: 9999px;
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .ann-meta-chip.expired { background: rgba(239,68,68,0.1); color: #ef4444; }
    .ann-meta-chip.expiring { background: rgba(245,158,11,0.1); color: #d97706; }

    .ann-detail-text {
        font-size: 0.925rem; font-weight: 500; color: var(--fg);
        line-height: 1.75; white-space: pre-wrap; word-break: break-word;
    }

    /* ── Form drawer (overlays everything) ─────────────────── */
    .ann-form-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.35);
        z-index: 1040; opacity: 0; pointer-events: none; transition: opacity 0.25s;
    }
    .ann-form-overlay.open { opacity: 1; pointer-events: all; }
    .ann-form-drawer {
        position: fixed; top: 0; right: -460px; width: 460px; height: 100vh;
        background: var(--card); border-left: 1px solid var(--border);
        z-index: 1050; display: flex; flex-direction: column;
        transition: right 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: -8px 0 32px rgba(0,0,0,0.12);
    }
    .ann-form-drawer.open { right: 0; }
    .ann-form-hd {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
    .ann-form-hd-title { font-size: 0.9rem; font-weight: 800; }
    .ann-form-close {
        width: 30px; height: 30px; border-radius: 8px; border: none;
        background: transparent; color: var(--muted-foreground); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; transition: background 0.15s;
    }
    .ann-form-close:hover { background: var(--muted); }
    .ann-form-body {
        flex: 1; overflow-y: auto; padding: 1.25rem;
        display: flex; flex-direction: column; gap: 1rem;
    }
    .ann-form-ft {
        padding: 1rem 1.25rem; border-top: 1px solid var(--border);
        display: flex; gap: 0.5rem; flex-shrink: 0;
    }

    /* Form fields */
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    .form-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); }
    .form-input {
        height: 42px; padding: 0 0.875rem; box-sizing: border-box;
        background: var(--muted); border: 2px solid transparent; border-radius: 9px;
        font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500;
        color: var(--fg); outline: none; transition: all 0.15s; width: 100%;
    }
    .form-input:focus { border-color: var(--primary); background: var(--card); }
    input[type="datetime-local"].form-input { padding: 0 0.625rem; font-size: 0.84rem; }
    .form-textarea {
        padding: 0.625rem 0.875rem; box-sizing: border-box;
        background: var(--muted); border: 2px solid transparent; border-radius: 9px;
        font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500;
        color: var(--fg); outline: none; resize: vertical; min-height: 160px;
        transition: all 0.15s; width: 100%;
    }
    .form-textarea:focus { border-color: var(--primary); background: var(--card); }
    .form-check { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .form-check input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; }
    .form-check-label { font-size: 0.85rem; font-weight: 600; color: var(--fg); }
</style>
@endsection

@section('content')
<x-sidebar :isAdmin="$user->isAdmin()" active="announcements" />

<div class="main-content">
<div class="ann-split">

    {{-- ══ LEFT: List panel ══ --}}
    <div class="ann-list-panel">
        <div class="ann-list-hd">
            <div class="ann-list-hd-top">
                <span class="ann-list-hd-title">Announcements</span>
                @if(in_array($user->role, ['head', 'manager', 'analyst']))
                <button class="ann-new-btn" onclick="openForm()">
                    <i class="fas fa-plus"></i> New
                </button>
                @endif
            </div>
            <div class="ann-search-wrap">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" class="ann-search" id="annSearch" placeholder="Search announcements…" oninput="filterList(this.value)">
            </div>
        </div>

        <div class="ann-list-scroll" id="annListScroll">
            @php
                $pinned  = $announcements->filter(fn($a) => $a->pinned);
                $regular = $announcements->reject(fn($a) => $a->pinned);
            @endphp

            @if($pinned->count())
            <div class="ann-list-section-label"><i class="fas fa-thumbtack" style="margin-right:4px;color:#f59e0b;"></i> Pinned</div>
            @foreach($pinned as $ann)
            @php $expired = $ann->expires_at && $ann->expires_at->isPast(); @endphp
            <div class="ann-item {{ $loop->first && !request('page') ? 'active' : '' }}"
                 data-id="{{ $ann->id }}"
                 data-title="{{ $ann->title }}"
                 data-body="{{ $ann->body }}"
                 data-pinned="{{ $ann->pinned ? '1' : '0' }}"
                 data-expires="{{ $ann->expires_at ? $ann->expires_at->format('Y-m-d\TH:i') : '' }}"
                 data-expires-label="{{ $ann->expires_at ? $ann->expires_at->format('M d, Y g:i A') : '' }}"
                 data-expired="{{ $expired ? '1' : '0' }}"
                 data-creator="{{ $ann->creator->first_name }} {{ $ann->creator->last_name }}"
                 data-creator-username="{{ $ann->creator->username }}"
                 data-creator-gender="{{ $ann->creator->gender }}"
                 data-date="{{ $ann->created_at->format('M d, Y') }}"
                 data-ago="{{ $ann->created_at->diffForHumans() }}"
                 onclick="selectItem(this)">
                <div class="ann-item-top">
                    <i class="fas fa-thumbtack ann-item-pin"></i>
                    <div class="ann-item-title">{{ $ann->title }}</div>
                </div>
                <div class="ann-item-preview">{{ Str::limit($ann->body, 72) }}</div>
                <div class="ann-item-foot">
                    <span class="ann-item-date">{{ $ann->created_at->diffForHumans() }}</span>
                    @if($expired)
                    <span class="ann-item-badge expired">Expired</span>
                    @elseif($ann->expires_at)
                    <span class="ann-item-badge expiring">Exp {{ $ann->expires_at->format('M d') }}</span>
                    @endif
                </div>
            </div>
            @endforeach
            @endif

            @if($regular->count())
            @if($pinned->count())
            <div class="ann-list-section-label">All</div>
            @endif
            @foreach($regular as $ann)
            @php $expired = $ann->expires_at && $ann->expires_at->isPast(); @endphp
            <div class="ann-item {{ !$pinned->count() && $loop->first && !request('page') ? 'active' : '' }}"
                 data-id="{{ $ann->id }}"
                 data-title="{{ $ann->title }}"
                 data-body="{{ $ann->body }}"
                 data-pinned="0"
                 data-expires="{{ $ann->expires_at ? $ann->expires_at->format('Y-m-d\TH:i') : '' }}"
                 data-expires-label="{{ $ann->expires_at ? $ann->expires_at->format('M d, Y g:i A') : '' }}"
                 data-expired="{{ $expired ? '1' : '0' }}"
                 data-creator="{{ $ann->creator->first_name }} {{ $ann->creator->last_name }}"
                 data-creator-username="{{ $ann->creator->username }}"
                 data-creator-gender="{{ $ann->creator->gender }}"
                 data-date="{{ $ann->created_at->format('M d, Y') }}"
                 data-ago="{{ $ann->created_at->diffForHumans() }}"
                 onclick="selectItem(this)">
                <div class="ann-item-top">
                    <div class="ann-item-title">{{ $ann->title }}</div>
                </div>
                <div class="ann-item-preview">{{ Str::limit($ann->body, 72) }}</div>
                <div class="ann-item-foot">
                    <span class="ann-item-date">{{ $ann->created_at->diffForHumans() }}</span>
                    @if($expired)
                    <span class="ann-item-badge expired">Expired</span>
                    @elseif($ann->expires_at)
                    <span class="ann-item-badge expiring">Exp {{ $ann->expires_at->format('M d') }}</span>
                    @endif
                </div>
            </div>
            @endforeach
            @endif

            @if($announcements->isEmpty())
            <div class="ann-list-empty">
                <i class="fas fa-bullhorn"></i>
                No announcements yet.
            </div>
            @endif
        </div>

        @if($announcements->hasPages())
        <div class="ann-list-pager">
            <span>Page {{ $announcements->currentPage() }} of {{ $announcements->lastPage() }}</span>
            <div style="display:flex;gap:0.75rem;">
                @if($announcements->onFirstPage())
                <span style="opacity:0.35;">← Prev</span>
                @else
                <a href="{{ $announcements->previousPageUrl() }}">← Prev</a>
                @endif
                @if($announcements->hasMorePages())
                <a href="{{ $announcements->nextPageUrl() }}">Next →</a>
                @else
                <span style="opacity:0.35;">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ══ RIGHT: Detail panel ══ --}}
    <div class="ann-detail-panel">

        {{-- Empty state --}}
        <div class="ann-detail-empty" id="annEmptyState" style="{{ $announcements->isEmpty() ? '' : 'display:none;' }}">
            <div class="ann-detail-empty-icon"><i class="fas fa-bullhorn"></i></div>
            <p>{{ $announcements->isEmpty() ? 'No announcements yet.' : 'Select an announcement to read.' }}</p>
        </div>

        {{-- Detail view --}}
        <div class="ann-detail-content" id="annDetailContent">
            <div class="ann-detail-topbar">
                <div class="ann-detail-topbar-left">
                    <span id="detailPinnedChip" style="display:none;" class="ann-item-badge pinned" style="font-size:0.7rem;padding:0.2rem 0.6rem;">
                        <i class="fas fa-thumbtack"></i> Pinned
                    </span>
                </div>
                @if(in_array($user->role, ['head', 'manager', 'analyst']))
                <div class="ann-detail-actions">
                    <button class="ann-det-btn" id="detPinBtn" onclick="togglePin()">
                        <i class="fas fa-thumbtack"></i> <span id="detPinLabel">Pin</span>
                    </button>
                    <button class="ann-det-btn" onclick="editCurrent()">
                        <i class="fas fa-pencil"></i> Edit
                    </button>
                    <button class="ann-det-btn danger" onclick="deleteCurrent()">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
                @endif
            </div>

            <div class="ann-detail-body">
                <div id="detPinStrip" class="ann-detail-pin-strip" style="display:none;">
                    <i class="fas fa-thumbtack"></i> This announcement is pinned
                </div>
                <div class="ann-detail-title" id="detTitle"></div>
                <div class="ann-detail-meta">
                    <img id="detAvatar" src="" class="ann-meta-avatar" alt="">
                    <div class="ann-meta-info">
                        <span class="ann-meta-name" id="detCreator"></span>
                        <span class="ann-meta-date" id="detDate"></span>
                    </div>
                    <span id="detExpiredChip" class="ann-meta-chip expired" style="display:none;"><i class="fas fa-clock"></i> Expired</span>
                    <span id="detExpiringChip" class="ann-meta-chip expiring" style="display:none;"></span>
                </div>
                <div class="ann-detail-text" id="detBody"></div>
            </div>
        </div>

    </div>
</div>
</div>

{{-- ══ Form Drawer ══ --}}
@if(in_array($user->role, ['head', 'manager', 'analyst']))
<div class="ann-form-overlay" id="annFormOverlay" onclick="closeForm()"></div>
<div class="ann-form-drawer" id="annFormDrawer">
    <div class="ann-form-hd">
        <span class="ann-form-hd-title" id="formTitle">New Announcement</span>
        <button class="ann-form-close" onclick="closeForm()"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="ann-form-body">
        <form id="annForm" method="POST" action="{{ route('announcements.store') }}">
            @csrf
            <span id="methodField"></span>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="fTitle" class="form-input" placeholder="Announcement title" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="body" id="fBody" class="form-textarea" placeholder="Write your announcement…" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Expires At
                        <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-400);margin-left:4px;">(optional)</span>
                    </label>
                    <input type="datetime-local" name="expires_at" id="fExpires" class="form-input">
                </div>
                <label class="form-check">
                    <input type="checkbox" name="pinned" id="fPinned" value="1">
                    <span class="form-check-label">
                        <i class="fas fa-thumbtack" style="color:#f59e0b;margin-right:4px;"></i>
                        Pin this announcement
                    </span>
                </label>
            </div>
        </form>
    </div>
    <div class="ann-form-ft">
        <button type="submit" form="annForm" class="btn-flat-primary" style="flex:1;height:42px;font-size:0.9rem;">
            <span id="formSubmitLabel">Post Announcement</span>
        </button>
        <button type="button" class="btn-flat-secondary" style="height:42px;padding:0 1rem;" onclick="closeForm()">Cancel</button>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
var _csrf    = document.querySelector('meta[name="csrf-token"]').content;
var _current = null; // currently selected item element
var _canEdit = {{ in_array($user->role, ['head', 'manager', 'analyst']) ? 'true' : 'false' }};

// ── Auto-select first item on load ──────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var first = document.querySelector('.ann-item.active');
    if (first) selectItem(first);

    @if(session('success'))
    showToast('{{ session('success') }}', 'success');
    @endif
});

// ── Select item ─────────────────────────────────────────────
function selectItem(el) {
    document.querySelectorAll('.ann-item').forEach(function(i) { i.classList.remove('active'); });
    el.classList.add('active');
    _current = el;

    var d = el.dataset;
    var isPinned  = d.pinned === '1';
    var isExpired = d.expired === '1';

    // Title
    document.getElementById('detTitle').textContent = d.title;

    // Body
    document.getElementById('detBody').textContent = d.body;

    // Avatar
    var seed = d.creatorGender === 'female' ? d.creatorUsername + 'Female' : d.creatorUsername;
    document.getElementById('detAvatar').src = 'https://api.dicebear.com/7.x/notionists/svg?seed=' + encodeURIComponent(seed);

    // Creator + date
    document.getElementById('detCreator').textContent = d.creator;
    document.getElementById('detDate').textContent = d.date + ' · ' + d.ago;

    // Pin strip + chip
    document.getElementById('detPinStrip').style.display  = isPinned ? 'flex' : 'none';
    document.getElementById('detPinnedChip').style.display = isPinned ? 'inline-flex' : 'none';

    // Expiry chips
    var expiredChip  = document.getElementById('detExpiredChip');
    var expiringChip = document.getElementById('detExpiringChip');
    expiredChip.style.display  = 'none';
    expiringChip.style.display = 'none';
    if (isExpired) {
        expiredChip.style.display = 'inline-flex';
    } else if (d.expiresLabel) {
        expiringChip.textContent = '';
        expiringChip.innerHTML = '<i class="fas fa-hourglass-half"></i> Expires ' + d.expiresLabel;
        expiringChip.style.display = 'inline-flex';
    }

    // Pin button state
    if (_canEdit) {
        var pinBtn = document.getElementById('detPinBtn');
        document.getElementById('detPinLabel').textContent = isPinned ? 'Unpin' : 'Pin';
        pinBtn.classList.toggle('pin-on', isPinned);
    }

    // Show detail panel
    document.getElementById('annEmptyState').style.display = 'none';
    document.getElementById('annDetailContent').classList.add('visible');
}

// ── Search filter ────────────────────────────────────────────
function filterList(q) {
    var term = q.toLowerCase();
    document.querySelectorAll('.ann-item').forEach(function(el) {
        var match = el.dataset.title.toLowerCase().includes(term)
                 || el.dataset.body.toLowerCase().includes(term);
        el.style.display = match ? '' : 'none';
    });
}

// ── Pin toggle ───────────────────────────────────────────────
function togglePin() {
    if (!_current) return;
    var id = _current.dataset.id;
    fetch('/announcements/' + id + '/pin', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': _csrf },
    }).then(r => r.json()).then(function(data) {
        _current.dataset.pinned = data.pinned ? '1' : '0';
        showToast(data.pinned ? 'Announcement pinned' : 'Announcement unpinned', 'success');
        setTimeout(function() { location.reload(); }, 700);
    });
}

// ── Edit current ─────────────────────────────────────────────
function editCurrent() {
    if (!_current) return;
    var d = _current.dataset;
    openForm(d.id, d.title, d.body, d.pinned === '1', d.expires);
}

// ── Delete current ───────────────────────────────────────────
function deleteCurrent() {
    if (!_current) return;
    var id = _current.dataset.id;
    showConfirm('Delete Announcement', 'This announcement will be permanently deleted.', 'Delete', function() {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/announcements/' + id;
        form.innerHTML = '<input type="hidden" name="_token" value="' + _csrf + '">'
                       + '<input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    });
}

// ── Form drawer ──────────────────────────────────────────────
function openForm(id, title, body, pinned, expires) {
    var form = document.getElementById('annForm');
    if (id) {
        document.getElementById('formTitle').textContent        = 'Edit Announcement';
        document.getElementById('formSubmitLabel').textContent  = 'Save Changes';
        form.action = '/announcements/' + id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('fTitle').value   = title   || '';
        document.getElementById('fBody').value    = body    || '';
        document.getElementById('fPinned').checked = !!pinned;
        document.getElementById('fExpires').value  = expires || '';
    } else {
        document.getElementById('formTitle').textContent        = 'New Announcement';
        document.getElementById('formSubmitLabel').textContent  = 'Post Announcement';
        form.action = '{{ route("announcements.store") }}';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('fTitle').value    = '';
        document.getElementById('fBody').value     = '';
        document.getElementById('fPinned').checked  = false;
        document.getElementById('fExpires').value   = '';
    }
    document.getElementById('annFormOverlay').classList.add('open');
    document.getElementById('annFormDrawer').classList.add('open');
}

function closeForm() {
    document.getElementById('annFormOverlay').classList.remove('open');
    document.getElementById('annFormDrawer').classList.remove('open');
}
</script>
@endsection
