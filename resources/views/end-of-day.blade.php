@extends('layouts.app')

@section('title', 'End-of-Day Report — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='4' width='18' height='18' rx='2' ry='2'/><line x1='16' y1='2' x2='16' y2='6'/><line x1='8' y1='2' x2='8' y2='6'/><line x1='3' y1='10' x2='21' y2='10'/><path d='M8 14h.01'/><path d='M12 14h.01'/><path d='M16 14h.01'/><path d='M8 18h.01'/><path d='M12 18h.01'/></svg>">
@endsection

@section('styles')
<style>
    /* Columns overview */
    .col-overview {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .col-head {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        background: var(--fg);
    }

    .col-head div {
        padding: 0.75rem;
        text-align: center;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: white;
    }

    .col-body {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
    }

    .col-body div {
        padding: 0.875rem;
        text-align: center;
        border-right: 2px solid var(--muted);
        border-bottom: 2px solid var(--muted);
    }

    .col-body div:last-child { border-right: none; }

    .col-body .col-name {
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--fg);
        margin-bottom: 0.125rem;
    }

    .col-body .col-desc {
        font-size: 0.7rem;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* Rules grid */
    .rules-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        margin-bottom: 1.5rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
    }

    .rule-cell {
        padding: 1.25rem;
        border-right: 2px solid var(--border);
        border-bottom: 2px solid var(--border);
    }

    .rule-cell:nth-child(2n) { border-right: none; }
    .rule-cell:nth-last-child(-n+2) { border-bottom: none; }

    .rule-head {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.625rem;
        border-bottom: 2px solid var(--fg);
    }

    .rule-icon {
        width: 32px;
        height: 32px;
        background: var(--fg);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .rule-title {
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--fg);
    }

    .rule-list {
        list-style: none;
        padding: 0;
        margin: 0 0 0.75rem;
    }

    .rule-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.375rem 0;
        font-size: 0.85rem;
        color: var(--gray-700);
        line-height: 1.5;
        font-weight: 500;
    }

    .rule-list li + li {
        border-top: 1px solid var(--muted);
    }

    .rule-list .rl-icon {
        width: 18px;
        height: 18px;
        background: #D1FAE5;
        color: #059669;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .rule-list strong {
        color: var(--fg);
        font-weight: 700;
    }

    .example-tag {
        display: inline-block;
        background: var(--muted);
        border: 2px solid var(--border);
        padding: 0.375rem 0.75rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--gray-700);
        border-radius: 4px;
    }

    /* Example table */
    .ex-table-wrap {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .ex-table-head {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        background: var(--muted);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
    }

    .ex-table-head .t-icon {
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

    .ex-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .ex-table thead th {
        background: var(--fg);
        color: white;
        padding: 0.75rem;
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        text-align: left;
    }

    .ex-table tbody td {
        padding: 0.625rem 0.75rem;
        border-top: 2px solid var(--muted);
        color: var(--gray-700);
        font-weight: 500;
    }

    .ex-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .ex-table tbody td:first-child {
        font-weight: 700;
        color: var(--primary);
    }

    /* Quick ref grid */
    .ref-card {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .ref-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
    }

    .ref-item {
        padding: 1rem 1.25rem;
        border-right: 2px solid var(--muted);
        border-bottom: 2px solid var(--muted);
    }

    .ref-item:nth-child(3n) { border-right: none; }
    .ref-item:nth-last-child(-n+3) { border-bottom: none; }

    .ref-item strong {
        display: block;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--primary);
        margin-bottom: 0.375rem;
    }

    .ref-item p {
        font-size: 0.8rem;
        color: var(--gray-700);
        margin: 0;
        line-height: 1.5;
        font-weight: 500;
    }

    /* Info banner */
    .info-banner {
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        background: var(--white);
        border-left: 4px solid var(--fg);
        border-radius: 0 8px 8px 0;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-banner .ib-icon {
        width: 36px;
        height: 36px;
        background: var(--fg);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .info-banner p {
        color: var(--gray-700);
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .col-head { grid-template-columns: repeat(3, 1fr); }
        .col-head div:nth-child(4),
        .col-head div:nth-child(5),
        .col-head div:nth-child(6) {
            display: none;
        }
        .col-body { grid-template-columns: repeat(3, 1fr); }
        .col-body div:nth-child(4),
        .col-body div:nth-child(5),
        .col-body div:nth-child(6) {
            display: none;
        }

        .rules-grid { grid-template-columns: 1fr; }
        .rule-cell { border-right: none !important; }
        .rule-cell:last-child { border-bottom: none !important; }

        .ref-grid { grid-template-columns: 1fr 1fr; }
        .ref-item:nth-child(2n) { border-right: none; }
        .ref-item:nth-last-child(-n+2) { border-bottom: none; }
        .ref-item:nth-child(3n) { border-right: 2px solid var(--muted); }
    }

    @media (max-width: 480px) {
        .col-head, .col-body { grid-template-columns: 1fr 1fr; }
        .col-head div:nth-child(3),
        .col-body div:nth-child(3) { display: none; }

        .ref-grid { grid-template-columns: 1fr; }
        .ref-item { border-right: none !important; border-bottom: 2px solid var(--muted) !important; }
        .ref-item:last-child { border-bottom: none !important; }
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
        <li><a href="{{ route('price-calculator') }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}" class="active"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
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
            <h2>End-of-Day <span class="highlight">Report</span></h2>
            <p>Guidelines for completing your daily EOD report</p>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="info-banner anim-up d1">
        <div class="ib-icon"><i class="fas fa-circle-info"></i></div>
        <p>This is where you'll input the things you did for the day/duty. Follow the guidelines below to properly log your daily activities.</p>
    </div>

    <!-- Column Overview -->
    <div class="col-overview anim-up d2">
        <div class="col-head">
            <div>New SKU</div>
            <div>Variation SKU</div>
            <div>Advance Data Gathering</div>
            <div>Update Listings</div>
            <div>Other</div>
            <div>Remarks</div>
        </div>
        <div class="col-body">
            <div>
                <div class="col-name">Parent/Single</div>
                <div class="col-desc">New product posted</div>
            </div>
            <div>
                <div class="col-name">Child/Variant</div>
                <div class="col-desc">Variation posted</div>
            </div>
            <div>
                <div class="col-name">Data Gathered</div>
                <div class="col-desc">Research completed</div>
            </div>
            <div>
                <div class="col-name">Updated Listings</div>
                <div class="col-desc">Old SKUs updated</div>
            </div>
            <div>
                <div class="col-name">Extra Tasks</div>
                <div class="col-desc">Canva, etc.</div>
            </div>
            <div>
                <div class="col-name">Description</div>
                <div class="col-desc">Explain OTHER</div>
            </div>
        </div>
    </div>

    <!-- Rules Grid -->
    <div class="rules-grid anim-up d3">
        <!-- New SKU & Variation -->
        <div class="rule-cell">
            <div class="rule-head">
                <div class="rule-icon"><i class="fas fa-box"></i></div>
                <div class="rule-title">New SKU & Variation SKU</div>
            </div>
            <ul class="rule-list">
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span><strong>New SKU (Parent / Single)</strong> — Each parent product or single SKU posted counts as 1</span>
                </li>
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span><strong>Variation SKU (Child)</strong> — Each child/variant under a parent SKU counts as 1</span>
                </li>
            </ul>
            <div class="example-tag">Example: 1 parent + 4 children = 1 NEW SKU, 4 VARIATION SKU</div>
        </div>

        <!-- Advance Data Gathering -->
        <div class="rule-cell">
            <div class="rule-head">
                <div class="rule-icon"><i class="fas fa-magnifying-glass"></i></div>
                <div class="rule-title">Advance Data Gathering</div>
            </div>
            <ul class="rule-list">
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span>If you gathered data in your mined/chosen SKU, add a value depending on how many SKUs you data gathered</span>
                </li>
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span>Includes product research from PR files, collecting specifications, gathering images</span>
                </li>
            </ul>
            <div class="example-tag">Example: Data gathered 5 SKUs = 5</div>
        </div>

        <!-- Update Listings -->
        <div class="rule-cell">
            <div class="rule-head">
                <div class="rule-icon"><i class="fas fa-pencil"></i></div>
                <div class="rule-title">Update Listings</div>
            </div>
            <ul class="rule-list">
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span>If you have old posting and updated data such as: Photos, Text, Long Description, Wrong SKU input</span>
                </li>
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span>Count depends on how many you updated</span>
                </li>
            </ul>
            <div class="example-tag">Example: Updated photos for 2 SKUs + corrected SKU for 1 = 3</div>
        </div>

        <!-- Other & Remarks -->
        <div class="rule-cell">
            <div class="rule-head">
                <div class="rule-icon"><i class="fas fa-list-check"></i></div>
                <div class="rule-title">Other & Remarks</div>
            </div>
            <ul class="rule-list">
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span><strong>Canva Usage</strong> — Automatically counts as 1, Remark: "Canva"</span>
                </li>
                <li>
                    <span class="rl-icon"><i class="fas fa-check"></i></span>
                    <span><strong>Post Pending SKU</strong> — For unfinished posting from previous day. Remark format: "Post Pending SKU: (number of SKUs)"</span>
                </li>
            </ul>
            <div class="example-tag">Example: Canva → OTHER = 1, REMARKS = "Canva"</div>
        </div>
    </div>

    <!-- Example Table -->
    <div class="ex-table-wrap anim-up d4">
        <div class="ex-table-head">
            <div class="t-icon"><i class="fas fa-table"></i></div>
            How Your EOD Report Should Look
        </div>
        <table class="ex-table">
            <thead>
                <tr>
                    <th>New SKU</th>
                    <th>Variation SKU</th>
                    <th>Adv. Data Gathering</th>
                    <th>Update Listings</th>
                    <th>Other</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>2</td><td>5</td><td>0</td><td>0</td><td>1</td><td>Canva</td></tr>
                <tr><td>1</td><td>3</td><td>4</td><td>0</td><td>3</td><td>Post Pending SKU: 3</td></tr>
                <tr><td>4</td><td>8</td><td>0</td><td>2</td><td>0</td><td>—</td></tr>
                <tr><td>0</td><td>0</td><td>6</td><td>0</td><td>0</td><td>—</td></tr>
                <tr><td>2</td><td>0</td><td>3</td><td>4</td><td>1</td><td>Canva</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Quick Reference -->
    <div class="ref-card anim-up d5">
        <div class="ex-table-head">
            <div class="t-icon" style="background: var(--secondary);"><i class="fas fa-star"></i></div>
            Quick Reference — Column by Column
        </div>
        <div class="ref-grid">
            <div class="ref-item">
                <strong>New SKU</strong>
                <p>Parent/Single = 1 each</p>
            </div>
            <div class="ref-item">
                <strong>Variation SKU</strong>
                <p>Child/Variant = 1 each</p>
            </div>
            <div class="ref-item">
                <strong>Adv. Data Gathering</strong>
                <p>Count SKUs gathered</p>
            </div>
            <div class="ref-item">
                <strong>Update Listings</strong>
                <p>Count updated SKUs</p>
            </div>
            <div class="ref-item">
                <strong>Other</strong>
                <p>Canva = 1<br>Post Pending = # of SKUs</p>
            </div>
            <div class="ref-item">
                <strong>Remarks</strong>
                <p>Describe what you did in OTHER</p>
            </div>
        </div>
    </div>
</div>
@endsection
