@extends('layouts.app')

@section('title', 'Posting Procedure — Ecomm Dept Hub')

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233B82F6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M9 11l3 3L22 4'/><path d='M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11'/></svg>">
@endsection

@section('styles')
<style>
    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 3rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 17px;
        width: 2px;
        height: 100%;
        background: var(--border);
    }

    .timeline-step {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-step:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -3rem;
        top: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        color: white;
        z-index: 1;
    }

    .timeline-dot.dot-blue { background: var(--primary); }
    .timeline-dot.dot-green { background: var(--secondary); }
    .timeline-dot.dot-amber { background: var(--accent); }

    /* Step Card */
    .step-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.75rem 2rem;
        transition: all 0.2s;
    }

    .step-card:hover {
        transform: scale(1.005);
    }

    .step-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .step-head-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
    }

    .step-head-icon.hi-blue { background: var(--primary); }
    .step-head-icon.hi-green { background: var(--secondary); }
    .step-head-icon.hi-amber { background: var(--accent); }

    .step-head h5 {
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }

    /* Definition block */
    .def-block {
        background: var(--muted);
        border-left: 4px solid var(--primary);
        border-radius: 0 6px 6px 0;
        padding: 0.875rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    .def-block.def-green { border-left-color: var(--secondary); }
    .def-block.def-amber { border-left-color: var(--accent); }

    .def-tag {
        display: inline-block;
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }

    .def-block.def-green .def-tag { color: var(--secondary); }
    .def-block.def-amber .def-tag { color: var(--accent); }

    .def-block p {
        color: var(--gray-700);
        font-weight: 500;
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }

    /* Section label */
    .sec-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gray-500);
        margin-bottom: 0.75rem;
    }

    .sec-label::after {
        content: '';
        flex: 1;
        height: 2px;
        background: var(--muted);
    }

    /* Task list */
    .task-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1.5rem;
    }

    .task-list:last-child {
        margin-bottom: 0;
    }

    .task-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.625rem;
        padding: 0.5rem 0;
        color: var(--gray-700);
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .task-list li + li {
        border-top: 1px solid var(--muted);
    }

    .tick {
        width: 22px;
        height: 22px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .tick.tick-green { background: #D1FAE5; color: #059669; }
    .tick.tick-red { background: #FEE2E2; color: #DC2626; }
    .tick.tick-amber { background: #FEF3C7; color: #D97706; }

    /* Folder tree */
    .folder-tree {
        background: var(--fg);
        border-radius: 6px;
        padding: 1.5rem;
        margin: 0.75rem 0 1.5rem;
        overflow-x: auto;
    }

    .folder-tree pre {
        margin: 0;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        line-height: 1.7;
        color: var(--gray-300);
        white-space: pre;
    }

    .folder-tree .ft-title { color: var(--primary); font-weight: 700; }
    .folder-tree .ft-folder { color: #60A5FA; }
    .folder-tree .ft-note { color: var(--gray-500); }

    /* Platform flow — vertical numbered list */
    .platform-list {
        list-style: none;
        padding: 0;
        margin: 0.75rem 0 1.5rem;
    }

    .platform-list li {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0;
    }

    .platform-list li + li {
        border-top: 1px solid var(--muted);
    }

    .pl-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        color: white;
        flex-shrink: 0;
    }

    .pl-num.pn-blue { background: var(--primary); }
    .pl-num.pn-green { background: var(--secondary); }

    .pl-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--fg);
    }

    .pl-arrow {
        color: var(--gray-300);
        font-size: 0.7rem;
        margin-left: auto;
    }

    .pl-tag {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        margin-left: 0.5rem;
    }

    .pl-tag.tag-standard { background: #DBEAFE; color: #2563EB; }
    .pl-tag.tag-pro { background: #D1FAE5; color: #059669; }

    /* Info icon — clickable */
    .info-trigger {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    .info-icon {
        width: 22px;
        height: 22px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.65rem;
        font-weight: 800;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .info-trigger:hover .info-icon {
        transform: scale(1.1);
    }

    .info-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--primary);
    }

    .info-popup {
        display: none;
        position: absolute;
        bottom: calc(100% + 10px);
        left: 0;
        width: 320px;
        background: var(--fg);
        border-radius: 6px;
        padding: 1rem 1.25rem;
        z-index: 10;
    }

    .info-popup::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 12px;
        border: 6px solid transparent;
        border-top-color: var(--fg);
    }

    .info-popup.show {
        display: block;
        animation: fadeInUp 0.2s ease-out;
    }

    .info-popup p {
        color: var(--gray-300);
        font-weight: 500;
        font-size: 0.85rem;
        margin: 0;
        line-height: 1.5;
    }

    .info-popup strong {
        color: var(--white);
        font-weight: 700;
    }

    /* Callout boxes */
    .callout {
        border-radius: 0 6px 6px 0;
        padding: 1rem 1.25rem;
        margin: 0 0 1.5rem;
    }

    .callout:last-child { margin-bottom: 0; }

    .callout-rules {
        background: #FFFBEB;
        border-left: 4px solid var(--accent);
    }

    .callout-note {
        background: #EFF6FF;
        border-left: 4px solid var(--primary);
    }

    .callout-title {
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .callout-rules .callout-title { color: #92400E; }
    .callout-note .callout-title { color: #1E40AF; }

    .callout p, .callout li {
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .callout-rules p, .callout-rules li { color: #78350F; }
    .callout-note p { color: #1E40AF; }

    /* Image table */
    .img-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0.75rem 0 1.5rem;
    }

    .img-table th,
    .img-table td {
        padding: 0.625rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        text-align: left;
    }

    .img-table th {
        background: var(--muted);
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
    }

    .img-table th:first-child { border-radius: 6px 0 0 6px; }
    .img-table th:last-child { border-radius: 0 6px 6px 0; }

    .img-table td {
        border-top: 2px solid var(--muted);
        color: var(--gray-700);
    }

    /* SKU definitions */
    .sku-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin: 0.75rem 0 1.5rem;
    }

    .sku-box {
        background: var(--muted);
        border-radius: 6px;
        padding: 1rem 1.25rem;
    }

    .sku-box strong {
        display: block;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--primary);
        margin-bottom: 0.25rem;
    }

    .sku-box span {
        color: var(--gray-700);
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .timeline { padding-left: 2.25rem; }
        .timeline::before { left: 13px; }

        .timeline-dot {
            left: -2.25rem;
            width: 28px;
            height: 28px;
            font-size: 0.7rem;
        }

        .step-card { padding: 1.25rem; }

        .step-head { gap: 0.5rem; }
        .step-head-icon { width: 36px; height: 36px; font-size: 0.9rem; }
        .step-head h5 { font-size: 1rem; }

        .sku-grid { grid-template-columns: 1fr; }

        .platform-list li { gap: 0.5rem; }
        .pl-num { width: 24px; height: 24px; font-size: 0.65rem; }

        .folder-tree { overflow-x: auto; }
        .folder-tree pre { font-size: 0.7rem; }
    }

    @media (max-width: 480px) {
        .step-head h5 { font-size: 0.9rem; }
        .def-block p { font-size: 0.85rem; }
        .task-list li { font-size: 0.85rem; }
        .callout p, .callout li { font-size: 0.8rem; }
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
        <li><a href="{{ route('posting-procedure') }}" class="active"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
        <li><a href="{{ route('data-gathering') }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
        <li><a href="{{ route('ecommerce-requirements') }}"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
        <li><a href="{{ route('price-calculator') }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
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

    <div class="top-bar anim-up" style="margin-bottom: 2.5rem;">
        <div>
            <h2>Posting Procedure</h2>
            <p>8-step guide for e-commerce product posting</p>
        </div>
    </div>

    <div class="timeline">

        <!-- ==================== STEP 1 ==================== -->
        <div class="timeline-step anim-up d1">
            <div class="timeline-dot dot-blue">1</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-blue"><i class="fas fa-magnifying-glass"></i></div>
                    <h5>Mine SKU from Link Sheet</h5>
                </div>

                <div class="def-block">
                    <div class="def-tag">Definition</div>
                    <p>Claim an available SKU from the Link Sheet before starting any work.</p>
                </div>

                <div class="sec-label">Tasks</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Open the Link Sheet and locate the SKU column.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Check the Content Column.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>If no name is assigned, the SKU is available.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Notify the team that you have mined the SKU.</li>
                </ul>
            </div>
        </div>

        <!-- ==================== STEP 2 ==================== -->
        <div class="timeline-step anim-up d2">
            <div class="timeline-dot dot-green">2</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-green"><i class="fas fa-folder-open"></i></div>
                    <h5>Data Gathering</h5>
                </div>

                <div class="def-block def-green">
                    <div class="def-tag">Definition</div>
                    <p>Collect all product information and assets required for posting.</p>
                </div>

                <div class="sec-label">Tasks</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Locate the Product Research (PR) file directory from the Link Sheet.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Gather all product information from the PR file.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Collect and organize all images.</li>
                </ul>

                <div class="sec-label">Folder Naming Rules</div>
                <div class="folder-tree"><pre>
<span class="ft-title">Single SKU Structure</span>
[SKU_Name]/
├── <span class="ft-folder">1000x1000/</span>     <span class="ft-note">(Ecommerce Posting Images)</span>
├── <span class="ft-folder">500x500/</span>       <span class="ft-note">(inFlow Images)</span>
└── <span class="ft-folder">Long Desc/</span>     <span class="ft-note">(1000x2000 Images)</span>

<span class="ft-title">Variation SKU Structure</span>
[Parent_SKU]/
└── [Child_SKU_Variation1]/
    ├── <span class="ft-folder">1000x1000/</span>
    ├── <span class="ft-folder">500x500/</span>
    └── <span class="ft-folder">Long Desc/</span></pre></div>

                <div class="sec-label">Image Requirements</div>
                <table class="img-table">
                    <thead><tr><th>Usage</th><th>Size</th></tr></thead>
                    <tbody>
                        <tr><td>Ecommerce Posting</td><td>1000 × 1000 px</td></tr>
                        <tr><td>inFlow</td><td>500 × 500 px</td></tr>
                        <tr><td>Long Description</td><td>1000 × 2000 px</td></tr>
                    </tbody>
                </table>

                <div class="sec-label">SKU Definitions</div>
                <div class="sku-grid">
                    <div class="sku-box">
                        <strong>Parent SKU</strong>
                        <span>Main folder that contains all variations.</span>
                    </div>
                    <div class="sku-box">
                        <strong>Child SKU</strong>
                        <span>Individual variation stored inside the Parent SKU folder.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 3 ==================== -->
        <div class="timeline-step anim-up d3">
            <div class="timeline-dot dot-amber">3</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-amber"><i class="fas fa-clock"></i></div>
                    <h5>Wait for Go Signal</h5>
                </div>

                <div class="def-block def-amber">
                    <div class="def-tag">Definition</div>
                    <p>Do not begin posting until approval is received.</p>
                </div>

                <div class="sec-label">Tasks</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Monitor Viber for updates.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Wait for the Product Researcher's Go Signal.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Do not proceed without approval.</li>
                </ul>
            </div>
        </div>

        <!-- ==================== STEP 4 ==================== -->
        <div class="timeline-step anim-up d4">
            <div class="timeline-dot dot-blue">4</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-blue"><i class="fas fa-cart-shopping"></i></div>
                    <h5>E-commerce Posting</h5>
                </div>

                <div class="def-block">
                    <div class="def-tag">Definition</div>
                    <p>Post products across all e-commerce platforms in the required sequence.</p>
                </div>

                <div class="sec-label">Posting Order</div>
                <ul class="platform-list">
                    <li>
                        <span class="pl-num pn-blue">1</span>
                        <span class="pl-name">Lazada Main</span>
                        <span class="pl-tag tag-standard">Standard</span>
                        <span class="pl-arrow"><i class="fas fa-arrow-right"></i></span>
                    </li>
                    <li>
                        <span class="pl-num pn-blue">2</span>
                        <span class="pl-name">Shopify</span>
                        <span class="pl-tag tag-standard">Standard</span>
                        <span class="pl-arrow"><i class="fas fa-arrow-right"></i></span>
                    </li>
                    <li>
                        <span class="pl-num pn-blue">3</span>
                        <span class="pl-name">Shopee Main</span>
                        <span class="pl-tag tag-standard">Standard</span>
                        <span class="pl-arrow"><i class="fas fa-arrow-right"></i></span>
                    </li>
                    <li>
                        <span class="pl-num pn-blue">4</span>
                        <span class="pl-name">TikTok</span>
                        <span class="pl-tag tag-standard">Standard</span>
                        <span class="pl-arrow"><i class="fas fa-arrow-right"></i></span>
                    </li>
                    <li>
                        <span class="pl-num pn-green">5</span>
                        <span class="pl-name">Lazada Pro</span>
                        <span class="pl-tag tag-pro">Pro</span>
                        <span class="pl-arrow"><i class="fas fa-arrow-right"></i></span>
                    </li>
                    <li>
                        <span class="pl-num pn-green">6</span>
                        <span class="pl-name">Shopee Pro</span>
                        <span class="pl-tag tag-pro">Pro</span>
                        <span class="pl-arrow"><i class="fas fa-arrow-right"></i></span>
                    </li>
                </ul>

                <div style="margin: 0.75rem 0 1.5rem; position: relative;">
                    <div class="info-trigger" onclick="toggleInfo(this)">
                        <span class="info-icon"><i class="fas fa-info"></i></span>
                        <span class="info-label">Important for variation SKUs</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.6rem; color: var(--primary); margin-left: 0.25rem;"></i>
                    </div>
                    <div class="info-popup">
                        <p>For variation SKUs, wait for the <strong>-grp product group</strong> to appear in Selluseller → Catalog → Grouped Products before proceeding to Shopee Main.</p>
                    </div>
                </div>

                <div class="callout callout-rules">
                    <div class="callout-title"><i class="fas fa-triangle-exclamation"></i> Rules</div>
                    <ul class="task-list" style="margin-bottom: 0;">
                        <li><span class="tick tick-amber"><i class="fas fa-minus"></i></span>Follow the posting order exactly.</li>
                        <li><span class="tick tick-amber"><i class="fas fa-minus"></i></span>Update inFlow information before posting to Lazada Pro and Shopee Pro.</li>
                        <li><span class="tick tick-amber"><i class="fas fa-minus"></i></span>After Shopee Pro, check for available Brand Mall accounts and upload if applicable.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 5 ==================== -->
        <div class="timeline-step anim-up d5">
            <div class="timeline-dot dot-green">5</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-green"><i class="fas fa-database"></i></div>
                    <h5>inFlow Update</h5>
                </div>

                <div class="def-block def-green">
                    <div class="def-tag">Definition</div>
                    <p>Update inventory information inside the inFlow system.</p>
                </div>

                <div style="margin: 0.75rem 0 1.5rem; position: relative;">
                    <div class="info-trigger" onclick="toggleInfo(this)">
                        <span class="info-icon"><i class="fas fa-info"></i></span>
                        <span class="info-label">Suggested approach by Sir Milo</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.6rem; color: var(--primary); margin-left: 0.25rem;"></i>
                    </div>
                    <div class="info-popup">
                        <p>It's entirely up to you how you approach posting and updating SKU details in inFlow. However, Sir Milo suggested updating the SKU in inFlow first before posting, as there is a tendency to forget this step if done later.</p>
                    </div>
                </div>

                <div class="sec-label">Tasks</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Open the mined SKU in inFlow.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Update the Description field. Reference the <strong>INFLOW TITLE</strong> and <strong>INFLOW DESCRIPTION</strong> from the PR file.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Click <strong>Extra Info</strong> → Locate the Measurements section.</li>
                    <li>
                        <span class="tick tick-green"><i class="fas fa-check"></i></span>
                        <span>Input the following dimensions from the PR file:
                            <span style="display: inline-flex; gap: 0.375rem; flex-wrap: wrap; margin-top: 0.375rem;">
                                <span style="background: var(--muted); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Length (cm)</span>
                                <span style="background: var(--muted); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Width (cm)</span>
                                <span style="background: var(--muted); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Height (cm)</span>
                                <span style="background: var(--muted); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Weight (kg)</span>
                            </span>
                        </span>
                    </li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Verify all dimensions using the PR file.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Save all changes.</li>
                </ul>
            </div>
        </div>

        <!-- ==================== STEP 6 ==================== -->
        <div class="timeline-step anim-up">
            <div class="timeline-dot dot-amber">6</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-amber"><i class="fas fa-star"></i></div>
                    <h5>Pro E-commerce Posting</h5>
                </div>

                <div class="def-block def-amber">
                    <div class="def-tag">Definition</div>
                    <p>Publish products to professional marketplace accounts after completing the inFlow update.</p>
                </div>

                <div class="sec-label">Platforms</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span><strong>Lazada Pro</strong> — Proceed after completing the inFlow update.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span><strong>Shopee Pro</strong> — Proceed after completing the inFlow update.</li>
                </ul>

                <div class="callout callout-note">
                    <div class="callout-title"><i class="fas fa-circle-info"></i> Note</div>
                    <p>If Lazada Pro is inaccessible or product authorization is unavailable, skip Lazada Pro and proceed directly to Shopee Pro.</p>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 7 ==================== -->
        <div class="timeline-step anim-up">
            <div class="timeline-dot dot-blue">7</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-blue"><i class="fas fa-store"></i></div>
                    <h5>Brand Malls</h5>
                </div>

                <div class="def-block">
                    <div class="def-tag">Definition</div>
                    <p>Post products to official Brand Mall stores when available.</p>
                </div>

                <div class="sec-label">Tasks</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Check if the SKU's brand has a Brand Mall account.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>If available, post the product according to platform guidelines.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>If unavailable, skip this step.</li>
                </ul>
            </div>
        </div>

        <!-- ==================== STEP 8 ==================== -->
        <div class="timeline-step anim-up">
            <div class="timeline-dot dot-green">8</div>
            <div class="step-card">
                <div class="step-head">
                    <div class="step-head-icon hi-green"><i class="fas fa-link"></i></div>
                    <h5>Update Link Sheet</h5>
                </div>

                <div class="def-block def-green">
                    <div class="def-tag">Definition</div>
                    <p>Record all product listing URLs after posting.</p>
                </div>

                <div class="sec-label">Tasks</div>
                <ul class="task-list">
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Open the Link Sheet.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Add listing URLs from all platforms.</li>
                    <li><span class="tick tick-green"><i class="fas fa-check"></i></span>Verify that all links open correctly, are accessible, and point to the correct product.</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleInfo(el) {
    const popup = el.nextElementSibling;
    const chevron = el.querySelector('.fa-chevron-down, .fa-chevron-up');

    document.querySelectorAll('.info-popup.show').forEach(p => {
        if (p !== popup) {
            p.classList.remove('show');
            const prevChevron = p.previousElementSibling.querySelector('.fa-chevron-up');
            if (prevChevron) {
                prevChevron.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    });

    popup.classList.toggle('show');
    if (chevron) {
        chevron.classList.toggle('fa-chevron-down');
        chevron.classList.toggle('fa-chevron-up');
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.info-trigger') && !e.target.closest('.info-popup')) {
        document.querySelectorAll('.info-popup.show').forEach(p => {
            p.classList.remove('show');
            const chevron = p.previousElementSibling.querySelector('.fa-chevron-up');
            if (chevron) {
                chevron.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
    }
});
</script>
@endsection
