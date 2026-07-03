@extends('layouts.app')

@section('title', 'SLA and Weekly Output')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M3 3v18h18'/><path d='M18 17V9'/><path d='M13 17V5'/><path d='M8 17v-3'/></svg>">
@endsection

@section('styles')
<style>
    .slaw-controls { display: flex; gap: 0.75rem; align-items: flex-end; margin-bottom: 1.25rem; }
    .slaw-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .slaw-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; color: var(--muted-foreground); }
    .slaw-table-card { background: var(--card); border: 1px solid var(--border-light); border-radius: 8px; overflow-x: auto; }
    table.slaw-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    table.slaw-table th { text-align: left; padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); color: var(--muted-foreground); font-size: 0.68rem; text-transform: uppercase; }
    table.slaw-table td { padding: 0.7rem 0.9rem; border-bottom: 1px solid var(--border-light); }
    .slaw-change.up { color: var(--destructive); }
    .slaw-change.down { color: var(--success); }
</style>
@endsection

@section('content')
<x-sidebar active="sla-weekly-output" :isAdmin="Auth::user()->isAdmin()" />

<div class="main-content">
    <div class="top-bar anim-up" style="margin-bottom: 1.5rem;">
        <div>
            <h2>SLA and <span class="highlight">Weekly Output</span></h2>
            <p>Weekly SLA averages, compared month over month — read-only</p>
        </div>
    </div>

    <form method="GET" class="slaw-controls anim-up d1">
        <div class="slaw-group">
            <span class="slaw-label">Month A (baseline)</span>
            <select name="month_a" class="input-flat" onchange="this.form.submit()">
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected($m === $monthA)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
        <div class="slaw-group">
            <span class="slaw-label">Month B (compare to)</span>
            <select name="month_b" class="input-flat" onchange="this.form.submit()">
                @foreach($availableMonths as $m)
                <option value="{{ $m }}" @selected($m === $monthB)>{{ \Carbon\Carbon::parse($m)->format('M Y') }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="slaw-table-card anim-up d2">
        <table class="slaw-table">
            <thead>
                <tr>
                    <th>Week</th>
                    <th>PR SLA ({{ $monthA ? \Carbon\Carbon::parse($monthA)->format('M') : '—' }})</th>
                    <th>PR SLA ({{ $monthB ? \Carbon\Carbon::parse($monthB)->format('M') : '—' }})</th>
                    <th>% Change</th>
                    <th>Content SLA ({{ $monthA ? \Carbon\Carbon::parse($monthA)->format('M') : '—' }})</th>
                    <th>Content SLA ({{ $monthB ? \Carbon\Carbon::parse($monthB)->format('M') : '—' }})</th>
                    <th>% Change</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comparison as $row)
                <tr>
                    <td>Week {{ $row['week'] }}</td>
                    <td>{{ $row['pr_a'] }}d</td>
                    <td>{{ $row['pr_b'] }}d</td>
                    <td class="slaw-change {{ $row['pr_change'] !== null && $row['pr_change'] > 0 ? 'up' : 'down' }}">
                        {{ $row['pr_change'] !== null ? $row['pr_change'] . '%' : '—' }}
                    </td>
                    <td>{{ $row['content_a'] }}d</td>
                    <td>{{ $row['content_b'] }}d</td>
                    <td class="slaw-change {{ $row['content_change'] !== null && $row['content_change'] > 0 ? 'up' : 'down' }}">
                        {{ $row['content_change'] !== null ? $row['content_change'] . '%' : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty-state">No data for the selected months.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
