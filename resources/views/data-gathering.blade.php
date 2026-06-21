@extends('layouts.app')

@section('title', 'Data Gathering — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z'/></svg>">
@endsection

@section('styles')
<style>
    .placeholder-card {
        background: var(--white);
        border-radius: 8px;
        padding: 3rem 2rem;
        text-align: center;
    }

    .placeholder-card .icon {
        width: 64px;
        height: 64px;
        background: var(--muted);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 1.5rem;
        color: var(--gray-300);
    }

    .placeholder-card h4 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .placeholder-card p {
        color: var(--gray-500);
        font-weight: 500;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<x-sidebar active="data-gathering" />

<div class="main-content">
    <a href="{{ route('dashboard') }}" class="back-link anim-fade"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar anim-up" style="margin-bottom: 2.5rem;">
        <div>
            <h2>Data Gathering</h2>
            <p>Collect product information and assets for posting</p>
        </div>
    </div>

    <div class="placeholder-card anim-up d1">
        <div class="icon"><i class="fas fa-folder-open"></i></div>
        <h4>Data Gathering Module</h4>
        <p>This section is coming soon. Content will be added here.</p>
    </div>
</div>
@endsection
