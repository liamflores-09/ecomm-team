@extends('layouts.app')

@section('title', 'Announcements — Ecomm Dept')
@section('has-sidebar', true)

@section('styles')
<style>
    .ann-layout { max-width: 760px; }

    /* ── Header ── */
    .ann-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.75rem; gap: 1rem; }
    .ann-top h2 { font-size: 1.5rem; font-weight: 800; margin: 0 0 0.2rem; }
    .ann-top p  { color: var(--muted-foreground); font-size: 0.875rem; font-weight: 500; margin: 0; }

    /* ── Card ── */
    .ann-card {
        background: var(--card); border: 1px solid var(--border-light);
        border-radius: 10px; padding: 1.375rem 1.5rem;
        margin-bottom: 1rem; transition: border-color 0.2s;
        position: relative;
    }
    .ann-card:hover { border-color: var(--foreground); }
    .ann-card.pinned { border-top: 3px solid #f59e0b; }

    .ann-card-top { display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.625rem; }
    .ann-pin-icon { color: #f59e0b; font-size: 0.75rem; margin-top: 3px; flex-shrink: 0; }
    .ann-title { font-size: 1rem; font-weight: 800; line-height: 1.3; flex: 1; }

    .ann-body {
        font-size: 0.875rem; color: var(--muted-foreground); font-weight: 500;
        line-height: 1.65; white-space: pre-wrap; word-break: break-word;
        margin-bottom: 0.875rem;
    }

    .ann-meta {
        display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;
        font-size: 0.72rem; font-weight: 600; color: var(--gray-400);
    }
    .ann-meta-sep { opacity: 0.4; }
    .ann-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.15rem 0.5rem; border-radius: 9999px;
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .ann-badge.pinned { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .ann-badge.expired { background: rgba(239,68,68,0.1); color: #ef4444; }
    .ann-badge.expiring { background: rgba(245,158,11,0.1); color: #f59e0b; }

    .ann-actions { display: flex; gap: 0.375rem; margin-left: auto; flex-shrink: 0; }
    .ann-act-btn {
        width: 28px; height: 28px; border: 1px solid var(--border-light);
        border-radius: 6px; background: transparent; color: var(--muted-foreground);
        cursor: pointer; font-size: 0.7rem; display: flex; align-items: center;
        justify-content: center; transition: all 0.15s;
    }
    .ann-act-btn:hover { border-color: var(--foreground); color: var(--foreground); }
    .ann-act-btn.danger:hover { border-color: #dc2626; color: #dc2626; }
    .ann-act-btn.pin-active { color: #f59e0b; border-color: #f59e0b; }

    /* ── Expired dim ── */
    .ann-card.expired { opacity: 0.55; }

    /* ── Empty state ── */
    .ann-empty {
        text-align: center; padding: 3.5rem 2rem;
        background: var(--card); border: 1px dashed var(--border);
        border-radius: 10px; color: var(--muted-foreground); font-size: 0.875rem;
    }
    .ann-empty i { font-size: 2rem; display: block; margin-bottom: 0.75rem; opacity: 0.3; }

    /* ── Drawer ── */
    .ann-drawer-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.35);
        z-index: 1040; opacity: 0; pointer-events: none; transition: opacity 0.25s;
    }
    .ann-drawer-overlay.open { opacity: 1; pointer-events: all; }
    .ann-drawer {
        position: fixed; top: 0; right: -460px; width: 460px; height: 100vh;
        background: var(--card); border-left: 1px solid var(--border);
        z-index: 1050; display: flex; flex-direction: column;
        transition: right 0.28s cubic-bezier(0.4,0,0.2,1);
    }
    .ann-drawer.open { right: 0; }
    .ann-drawer-hd {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
    .ann-drawer-hd-title { font-size: 0.9rem; font-weight: 800; }
    .ann-drawer-close {
        width: 30px; height: 30px; border-radius: 8px; border: none;
        background: transparent; color: var(--muted-foreground); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; transition: background 0.15s;
    }
    .ann-drawer-close:hover { background: var(--muted); }
    .ann-drawer-body {
        flex: 1; overflow-y: auto; padding: 1.25rem;
        display: flex; flex-direction: column; gap: 1rem;
    }
    .ann-drawer-ft {
        padding: 1rem 1.25rem; border-top: 1px solid var(--border);
        display: flex; gap: 0.5rem; flex-shrink: 0;
    }

    /* Form elements */
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    .form-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--gray-500); }
    .form-input {
        height: 42px; padding: 0 0.875rem; box-sizing: border-box;
        background: var(--muted); border: 2px solid transparent; border-radius: 9px;
        font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500;
        color: var(--fg); outline: none; transition: all 0.15s; width: 100%;
    }
    .form-input:focus { border-color: var(--primary); background: var(--white); }
    input[type="datetime-local"].form-input { padding: 0 0.625rem; font-size: 0.84rem; }
    .form-textarea {
        padding: 0.625rem 0.875rem; box-sizing: border-box;
        background: var(--muted); border: 2px solid transparent; border-radius: 9px;
        font-family: var(--p-font-family-sans); font-size: 0.9rem; font-weight: 500;
        color: var(--fg); outline: none; resize: vertical; min-height: 140px;
        transition: all 0.15s; width: 100%;
    }
    .form-textarea:focus { border-color: var(--primary); background: var(--white); }
    .form-check { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .form-check input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; }
    .form-check-label { font-size: 0.85rem; font-weight: 600; color: var(--fg); }
</style>
@endsection

@section('content')
<x-sidebar :isAdmin="$user->isAdmin()" active="announcements" />

<div class="main-content">
<div class="ann-layout">

    @if(session('success'))
    <div class="alert-flat success anim-fade" style="margin-bottom:1rem;">
        <i class="fas fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    <div class="ann-top anim-up">
        <div>
            <h2><span class="highlight">Announcements</span></h2>
            <p>Updates and notices from the team</p>
        </div>
        @if(in_array($user->role, ['head', 'manager', 'analyst']))
        <button class="btn-flat-primary" style="height:40px;padding:0 1rem;font-size:0.85rem;white-space:nowrap;" onclick="openDrawer()">
            <i class="fas fa-plus"></i> New Announcement
        </button>
        @endif
    </div>

    @if($announcements->count())
    @foreach($announcements as $ann)
    @php $expired = $ann->expires_at && $ann->expires_at->isPast(); @endphp
    <div class="ann-card anim-up {{ $ann->pinned ? 'pinned' : '' }} {{ $expired ? 'expired' : '' }}">
        <div class="ann-card-top">
            @if($ann->pinned)
            <i class="fas fa-thumbtack ann-pin-icon"></i>
            @endif
            <div class="ann-title">{{ $ann->title }}</div>
            @if(in_array($user->role, ['head', 'manager', 'analyst']))
            <div class="ann-actions">
                <button class="ann-act-btn {{ $ann->pinned ? 'pin-active' : '' }}" title="{{ $ann->pinned ? 'Unpin' : 'Pin' }}"
                    onclick="togglePin({{ $ann->id }}, this)">
                    <i class="fas fa-thumbtack"></i>
                </button>
                <button class="ann-act-btn" title="Edit" onclick="openDrawer({{ $ann->id }}, {{ Js::from($ann->title) }}, {{ Js::from($ann->body) }}, {{ $ann->pinned ? 'true' : 'false' }}, '{{ $ann->expires_at ? $ann->expires_at->format('Y-m-d\TH:i') : '' }}')">
                    <i class="fas fa-pencil"></i>
                </button>
                <form method="POST" action="{{ route('announcements.destroy', $ann) }}" style="display:inline;" onsubmit="return confirm('Delete this announcement?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="ann-act-btn danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="ann-body">{{ $ann->body }}</div>

        <div class="ann-meta">
            <span><i class="fas fa-user" style="margin-right:3px;"></i>{{ $ann->creator->first_name }} {{ $ann->creator->last_name }}</span>
            <span class="ann-meta-sep">·</span>
            <span>{{ $ann->created_at->format('M d, Y') }}</span>
            @if($ann->pinned)
            <span class="ann-badge pinned"><i class="fas fa-thumbtack"></i> Pinned</span>
            @endif
            @if($expired)
            <span class="ann-badge expired"><i class="fas fa-clock"></i> Expired</span>
            @elseif($ann->expires_at)
            <span class="ann-badge expiring"><i class="fas fa-hourglass-half"></i> Expires {{ $ann->expires_at->format('M d') }}</span>
            @endif
        </div>
    </div>
    @endforeach

    <div style="margin-top:1.25rem;">
        {{ $announcements->links() }}
    </div>

    @else
    <div class="ann-empty anim-up">
        <i class="fas fa-bullhorn"></i>
        No announcements yet.
        @if(in_array($user->role, ['head', 'manager', 'analyst']))
        <br><span style="margin-top:0.375rem;display:inline-block;">Post one using the button above.</span>
        @endif
    </div>
    @endif

</div>
</div>

{{-- ── Drawer ── --}}
@if(in_array($user->role, ['head', 'manager', 'analyst']))
<div class="ann-drawer-overlay" id="annOverlay" onclick="closeDrawer()"></div>
<div class="ann-drawer" id="annDrawer">
    <div class="ann-drawer-hd">
        <span class="ann-drawer-hd-title" id="drawerTitle">New Announcement</span>
        <button class="ann-drawer-close" onclick="closeDrawer()"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="ann-drawer-body">
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
                    <textarea name="body" id="fBody" class="form-textarea" placeholder="Write your announcement here…" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Expires At <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-400)">(optional)</span></label>
                    <input type="datetime-local" name="expires_at" id="fExpires" class="form-input">
                </div>
                <label class="form-check">
                    <input type="checkbox" name="pinned" id="fPinned" value="1">
                    <span class="form-check-label"><i class="fas fa-thumbtack" style="color:#f59e0b;margin-right:4px;"></i> Pin this announcement</span>
                </label>
            </div>
        </form>
    </div>
    <div class="ann-drawer-ft">
        <button type="submit" form="annForm" class="btn-flat-primary" style="flex:1;height:42px;font-size:0.9rem;">
            <span id="drawerSubmitLabel">Post Announcement</span>
        </button>
        <button type="button" class="btn-flat-secondary" style="height:42px;padding:0 1rem;" onclick="closeDrawer()">Cancel</button>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
var _editingId = null;

function openDrawer(id, title, body, pinned, expires) {
    _editingId = id || null;
    var form = document.getElementById('annForm');
    var methodField = document.getElementById('methodField');

    if (id) {
        document.getElementById('drawerTitle').textContent = 'Edit Announcement';
        document.getElementById('drawerSubmitLabel').textContent = 'Save Changes';
        form.action = '/announcements/' + id;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('fTitle').value = title || '';
        document.getElementById('fBody').value = body || '';
        document.getElementById('fPinned').checked = !!pinned;
        document.getElementById('fExpires').value = expires || '';
    } else {
        document.getElementById('drawerTitle').textContent = 'New Announcement';
        document.getElementById('drawerSubmitLabel').textContent = 'Post Announcement';
        form.action = '{{ route("announcements.store") }}';
        methodField.innerHTML = '';
        document.getElementById('fTitle').value = '';
        document.getElementById('fBody').value = '';
        document.getElementById('fPinned').checked = false;
        document.getElementById('fExpires').value = '';
    }

    document.getElementById('annOverlay').classList.add('open');
    document.getElementById('annDrawer').classList.add('open');
}

function closeDrawer() {
    document.getElementById('annOverlay').classList.remove('open');
    document.getElementById('annDrawer').classList.remove('open');
}

function togglePin(id, btn) {
    fetch('/announcements/' + id + '/pin', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    }).then(r => r.json()).then(function(data) {
        btn.classList.toggle('pin-active', data.pinned);
        btn.title = data.pinned ? 'Unpin' : 'Pin';
        showToast(data.pinned ? 'Announcement pinned' : 'Announcement unpinned', 'success');
        setTimeout(function() { location.reload(); }, 800);
    });
}
</script>
@endsection
