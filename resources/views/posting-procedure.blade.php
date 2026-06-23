@extends('layouts.app')

@section('title', 'Posting Procedure — Ecomm Dept Hub')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235757f8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M9 11l3 3L22 4'/><path d='M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11'/></svg>">
@endsection

@section('styles')
<style>
    /* ── Step pill nav ── */
    .step-nav-bar {
        position: sticky;
        top: 64px;
        z-index: 30;
        background: var(--background);
        border-bottom: 1px solid var(--border-light);
        padding: 0.625rem 0;
        margin: 0 -2.5rem 1.75rem;
        padding-left: 2.5rem;
        padding-right: 2.5rem;
        display: flex;
        gap: 0.375rem;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .step-nav-bar::-webkit-scrollbar { display: none; }
    .step-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--muted-foreground);
        background: var(--card);
        border: 1px solid var(--border-light);
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .step-pill:hover { color: var(--foreground); border-color: var(--border); }
    .step-pill.active { background: var(--primary); color: white; border-color: var(--primary); }

    /* ── Step card ── */
    .proc-step {
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        margin-bottom: 1.125rem;
        overflow: hidden;
        scroll-margin-top: 130px;
    }
    .proc-bar { height: 3px; }
    .proc-bar.cp { background: var(--primary); }
    .proc-bar.cs { background: var(--success); }
    .proc-bar.cw { background: var(--warning); }

    .proc-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.375rem 1.75rem 1.125rem;
        position: relative;
        overflow: hidden;
    }
    .proc-ghost-num {
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        font-family: 'Space Grotesk', sans-serif;
        font-size: 5rem;
        font-weight: 800;
        color: var(--border-light);
        line-height: 1;
        pointer-events: none;
        user-select: none;
        letter-spacing: -0.05em;
    }
    .proc-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; color: white; flex-shrink: 0;
    }
    .proc-icon.cp { background: var(--primary); }
    .proc-icon.cs { background: var(--success); }
    .proc-icon.cw { background: var(--warning); }
    .proc-title {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.05rem; font-weight: 700;
        color: var(--foreground); line-height: 1.2;
    }
    .proc-sub { font-size: 0.78rem; color: var(--muted-foreground); font-weight: 500; margin-top: 0.15rem; }

    .proc-body {
        padding: 0 1.75rem 1.625rem;
        display: flex; flex-direction: column; gap: 1.125rem;
    }

    /* ── Section label ── */
    .p-sec {
        font-size: 0.63rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: var(--muted-foreground);
        margin-bottom: 0.625rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .p-sec::after { content: ''; flex: 1; height: 1px; background: var(--border-light); }

    /* ── Checklist ── */
    .p-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; }
    .p-list li {
        display: flex; align-items: flex-start;
        gap: 0.75rem; font-size: 0.875rem; font-weight: 500;
        color: var(--foreground); line-height: 1.55;
    }
    .p-dot {
        width: 20px; height: 20px; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.55rem; flex-shrink: 0; margin-top: 2px;
    }
    .p-dot.ok   { background: rgba(34,197,94,0.15);  color: #16a34a; }
    .p-dot.warn { background: rgba(245,158,11,0.15); color: #d97706; }
    .p-dot.info { background: rgba(87,87,248,0.12);  color: var(--primary); }

    /* ── Callouts ── */
    .p-call {
        border-radius: 8px; padding: 0.875rem 1rem;
        display: flex; align-items: flex-start; gap: 0.75rem;
        font-size: 0.85rem; font-weight: 500; line-height: 1.55;
        color: var(--foreground);
    }
    .p-call.warn    { background: rgba(245,158,11,0.08);  border: 1px solid rgba(245,158,11,0.25); }
    .p-call.info    { background: rgba(87,87,248,0.07);   border: 1px solid rgba(87,87,248,0.2); }
    .p-call.success { background: rgba(34,197,94,0.07);   border: 1px solid rgba(34,197,94,0.2); }
    .p-call i { font-size: 0.85rem; flex-shrink: 0; margin-top: 2px; }
    .p-call.warn i    { color: var(--warning); }
    .p-call.info i    { color: var(--primary); }
    .p-call.success i { color: var(--success); }

    /* ── Platform chips ── */
    .plat-list { display: flex; flex-direction: column; gap: 0.375rem; }
    .plat-chip {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.5rem 0.875rem;
        background: var(--muted); border-radius: 8px;
        font-size: 0.875rem; font-weight: 600; color: var(--foreground);
    }
    .plat-num {
        width: 22px; height: 22px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem; font-weight: 800; color: white; flex-shrink: 0;
    }
    .plat-num.cp { background: var(--primary); }
    .plat-num.cs { background: var(--success); }
    .plat-badge {
        margin-left: auto; font-size: 0.62rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.04em;
        padding: 0.15rem 0.5rem; border-radius: 9999px;
    }
    .plat-badge.std { background: rgba(87,87,248,0.12); color: var(--primary); }
    .plat-badge.pro { background: rgba(34,197,94,0.12); color: #16a34a; }

    /* ── Code block ── */
    .p-code {
        background: #18181b; border-radius: 8px;
        padding: 1.125rem 1.25rem; overflow-x: auto;
        font-family: ui-monospace, 'Courier New', monospace;
        font-size: 0.8rem; line-height: 1.8; color: #a1a1aa;
    }
    .p-code .ct { color: #818cf8; font-weight: 700; }
    .p-code .cf { color: #60a5fa; }
    .p-code .cn { color: #3f3f46; }

    /* ── Table ── */
    .p-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .p-table th {
        background: var(--muted); padding: 0.5rem 0.875rem; text-align: left;
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: var(--muted-foreground);
    }
    .p-table th:first-child { border-radius: 6px 0 0 6px; }
    .p-table th:last-child  { border-radius: 0 6px 6px 0; }
    .p-table td {
        padding: 0.6rem 0.875rem; border-top: 1px solid var(--border-light);
        color: var(--foreground); font-weight: 500;
    }

    /* ── SKU grid ── */
    .p-sku { display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; }
    .p-sku-box { background: var(--muted); border-radius: 8px; padding: 0.875rem 1rem; }
    .p-sku-label {
        font-size: 0.62rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--primary); margin-bottom: 0.3rem;
    }
    .p-sku-val { font-size: 0.85rem; font-weight: 500; color: var(--foreground); }

    /* ── Dim tags ── */
    .dim-tags { display: flex; gap: 0.375rem; flex-wrap: wrap; margin-top: 0.375rem; }
    .dim-tag {
        background: var(--muted); padding: 0.2rem 0.5rem;
        border-radius: 4px; font-size: 0.78rem; font-weight: 600; color: var(--foreground);
    }

    @media (max-width: 768px) {
        .step-nav-bar { margin: 0 -1.25rem 1.5rem; padding-left: 1.25rem; padding-right: 1.25rem; }
        .proc-header { padding: 1.125rem; }
        .proc-body { padding: 0 1.125rem 1.25rem; }
        .proc-ghost-num { font-size: 3.5rem; }
        .p-sku { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<x-sidebar active="posting-procedure" />

<div class="main-content">

    <div class="top-bar anim-up" style="margin-bottom:1.25rem;">
        <div>
            <h2>Posting <span class="highlight">Procedure</span></h2>
            <p>8-step guide for e-commerce product posting</p>
        </div>
    </div>

    <!-- Step pills -->
    <div class="step-nav-bar" id="stepNav">
        <a class="step-pill active" href="#step-1">1 · Mine SKU</a>
        <a class="step-pill" href="#step-2">2 · Data Gathering</a>
        <a class="step-pill" href="#step-3">3 · Go Signal</a>
        <a class="step-pill" href="#step-4">4 · Ecomm Posting</a>
        <a class="step-pill" href="#step-5">5 · inFlow Update</a>
        <a class="step-pill" href="#step-6">6 · Pro Posting</a>
        <a class="step-pill" href="#step-7">7 · Brand Malls</a>
        <a class="step-pill" href="#step-8">8 · Link Sheet</a>
    </div>

    <!-- STEP 1 -->
    <div class="proc-step anim-up d1" id="step-1">
        <div class="proc-bar cp"></div>
        <div class="proc-header">
            <div class="proc-icon cp"><i class="fas fa-magnifying-glass"></i></div>
            <div>
                <div class="proc-title">Mine SKU from Link Sheet</div>
                <div class="proc-sub">Claim an available SKU before starting any work</div>
            </div>
            <div class="proc-ghost-num">01</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Tasks</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Open the Link Sheet and locate the SKU column.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Check the Content Column.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>If no name is assigned, the SKU is available.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Notify the team that you have mined the SKU.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- STEP 2 -->
    <div class="proc-step anim-up d2" id="step-2">
        <div class="proc-bar cs"></div>
        <div class="proc-header">
            <div class="proc-icon cs"><i class="fas fa-folder-open"></i></div>
            <div>
                <div class="proc-title">Data Gathering</div>
                <div class="proc-sub">Collect all product information and assets required for posting</div>
            </div>
            <div class="proc-ghost-num">02</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Tasks</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Locate the Product Research (PR) file directory from the Link Sheet.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Gather all product information from the PR file.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Collect and organize all images.</li>
                </ul>
            </div>
            <div>
                <div class="p-sec">Folder Naming Rules</div>
                <div class="p-code"><span class="ct">Single SKU</span>
[SKU_Name]/
├── <span class="cf">1000x1000/</span>     <span class="cn">← Ecommerce Posting Images</span>
├── <span class="cf">500x500/</span>       <span class="cn">← inFlow Images</span>
└── <span class="cf">Long Desc/</span>     <span class="cn">← 1000x2000 Images</span>

<span class="ct">Variation SKU</span>
[Parent_SKU]/
└── [Child_SKU_Variation1]/
    ├── <span class="cf">1000x1000/</span>
    ├── <span class="cf">500x500/</span>
    └── <span class="cf">Long Desc/</span></div>
            </div>
            <div>
                <div class="p-sec">SKU Definitions</div>
                <div class="p-sku">
                    <div class="p-sku-box">
                        <div class="p-sku-label">Parent SKU</div>
                        <div class="p-sku-val">Main folder that contains all variations.</div>
                    </div>
                    <div class="p-sku-box">
                        <div class="p-sku-label">Child SKU</div>
                        <div class="p-sku-val">Individual variation stored inside the Parent SKU folder.</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="p-sec">Image Requirements</div>
                <table class="p-table">
                    <thead><tr><th>Usage</th><th>Size</th></tr></thead>
                    <tbody>
                        <tr><td>Ecommerce Posting</td><td>1000 × 1000 px</td></tr>
                        <tr><td>inFlow</td><td>500 × 500 px</td></tr>
                        <tr><td>Long Description</td><td>1000 × 2000 px</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STEP 3 -->
    <div class="proc-step anim-up d3" id="step-3">
        <div class="proc-bar cw"></div>
        <div class="proc-header">
            <div class="proc-icon cw"><i class="fas fa-clock"></i></div>
            <div>
                <div class="proc-title">Wait for Go Signal</div>
                <div class="proc-sub">Do not begin posting until approval is received</div>
            </div>
            <div class="proc-ghost-num">03</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Tasks</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Monitor Viber for updates.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Wait for the Product Researcher's Go Signal.</li>
                    <li><span class="p-dot warn"><i class="fas fa-minus"></i></span>Do not proceed without approval.</li>
                </ul>
            </div>
            <div class="p-call warn">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Never begin posting without receiving the Go Signal from the Product Researcher. Starting early may result in incorrect listings.</span>
            </div>
        </div>
    </div>

    <!-- STEP 4 -->
    <div class="proc-step anim-up d4" id="step-4">
        <div class="proc-bar cp"></div>
        <div class="proc-header">
            <div class="proc-icon cp"><i class="fas fa-cart-shopping"></i></div>
            <div>
                <div class="proc-title">E-commerce Posting</div>
                <div class="proc-sub">Post products across all platforms in the required sequence</div>
            </div>
            <div class="proc-ghost-num">04</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Posting Order</div>
                <div class="plat-list">
                    <div class="plat-chip"><span class="plat-num cp">1</span>Lazada Main<span class="plat-badge std">Standard</span></div>
                    <div class="plat-chip"><span class="plat-num cp">2</span>Shopify<span class="plat-badge std">Standard</span></div>
                    <div class="plat-chip"><span class="plat-num cp">3</span>Shopee Main<span class="plat-badge std">Standard</span></div>
                    <div class="plat-chip"><span class="plat-num cp">4</span>TikTok<span class="plat-badge std">Standard</span></div>
                    <div class="plat-chip"><span class="plat-num cs">5</span>Lazada Pro<span class="plat-badge pro">Pro</span></div>
                    <div class="plat-chip"><span class="plat-num cs">6</span>Shopee Pro<span class="plat-badge pro">Pro</span></div>
                </div>
            </div>
            <div class="p-call info">
                <i class="fas fa-circle-info"></i>
                <span>For <strong>variation SKUs</strong>, wait for the <strong>-grp product group</strong> to appear in Selluseller → Catalog → Grouped Products before proceeding to Shopee Main.</span>
            </div>
            <div>
                <div class="p-sec">Rules</div>
                <ul class="p-list">
                    <li><span class="p-dot warn"><i class="fas fa-minus"></i></span>Follow the posting order exactly.</li>
                    <li><span class="p-dot warn"><i class="fas fa-minus"></i></span>Update inFlow information before posting to Lazada Pro and Shopee Pro.</li>
                    <li><span class="p-dot warn"><i class="fas fa-minus"></i></span>After Shopee Pro, check for available Brand Mall accounts and upload if applicable.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- STEP 5 -->
    <div class="proc-step anim-up d5" id="step-5">
        <div class="proc-bar cs"></div>
        <div class="proc-header">
            <div class="proc-icon cs"><i class="fas fa-database"></i></div>
            <div>
                <div class="proc-title">inFlow Update</div>
                <div class="proc-sub">Update inventory information inside the inFlow system</div>
            </div>
            <div class="proc-ghost-num">05</div>
        </div>
        <div class="proc-body">
            <div class="p-call info">
                <i class="fas fa-lightbulb"></i>
                <span>Sir Milo suggests updating inFlow <strong>before</strong> posting, as there is a tendency to forget this step if done later. It's up to you, but this order is recommended.</span>
            </div>
            <div>
                <div class="p-sec">Tasks</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Open the mined SKU in inFlow.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Update the Description field using the <strong>INFLOW TITLE</strong> and <strong>INFLOW DESCRIPTION</strong> from the PR file.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Click <strong>Extra Info</strong> → Locate the Measurements section.</li>
                    <li>
                        <span class="p-dot ok"><i class="fas fa-check"></i></span>
                        <span>Input the following dimensions from the PR file:
                            <div class="dim-tags">
                                <span class="dim-tag">Length (cm)</span>
                                <span class="dim-tag">Width (cm)</span>
                                <span class="dim-tag">Height (cm)</span>
                                <span class="dim-tag">Weight (kg)</span>
                            </div>
                        </span>
                    </li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Verify all dimensions using the PR file.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Save all changes.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- STEP 6 -->
    <div class="proc-step anim-up" id="step-6">
        <div class="proc-bar cw"></div>
        <div class="proc-header">
            <div class="proc-icon cw"><i class="fas fa-star"></i></div>
            <div>
                <div class="proc-title">Pro E-commerce Posting</div>
                <div class="proc-sub">Publish to professional accounts after completing the inFlow update</div>
            </div>
            <div class="proc-ghost-num">06</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Platforms</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span><strong>Lazada Pro</strong> — Proceed after completing the inFlow update.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span><strong>Shopee Pro</strong> — Proceed after completing the inFlow update.</li>
                </ul>
            </div>
            <div class="p-call info">
                <i class="fas fa-circle-info"></i>
                <span>If Lazada Pro is inaccessible or product authorization is unavailable, skip Lazada Pro and proceed directly to Shopee Pro.</span>
            </div>
        </div>
    </div>

    <!-- STEP 7 -->
    <div class="proc-step anim-up" id="step-7">
        <div class="proc-bar cp"></div>
        <div class="proc-header">
            <div class="proc-icon cp"><i class="fas fa-store"></i></div>
            <div>
                <div class="proc-title">Brand Malls</div>
                <div class="proc-sub">Post products to official Brand Mall stores when available</div>
            </div>
            <div class="proc-ghost-num">07</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Tasks</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Check if the SKU's brand has a Brand Mall account.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>If available, post the product according to platform guidelines.</li>
                    <li><span class="p-dot info"><i class="fas fa-minus"></i></span>If unavailable, skip this step.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- STEP 8 -->
    <div class="proc-step anim-up" id="step-8">
        <div class="proc-bar cs"></div>
        <div class="proc-header">
            <div class="proc-icon cs"><i class="fas fa-link"></i></div>
            <div>
                <div class="proc-title">Update Link Sheet</div>
                <div class="proc-sub">Record all product listing URLs after posting</div>
            </div>
            <div class="proc-ghost-num">08</div>
        </div>
        <div class="proc-body">
            <div>
                <div class="p-sec">Tasks</div>
                <ul class="p-list">
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Open the Link Sheet.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Add listing URLs from all platforms.</li>
                    <li><span class="p-dot ok"><i class="fas fa-check"></i></span>Verify that all links open correctly, are accessible, and point to the correct product.</li>
                </ul>
            </div>
            <div class="p-call success">
                <i class="fas fa-circle-check"></i>
                <span>Once the Link Sheet is updated and all links verified, the posting procedure for this SKU is <strong>complete</strong>.</span>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function() {
    var ids   = ['step-1','step-2','step-3','step-4','step-5','step-6','step-7','step-8'];
    var pills = document.querySelectorAll('#stepNav .step-pill');

    function setActive(i) {
        pills.forEach(function(p, idx) { p.classList.toggle('active', idx === i); });
    }

    window.addEventListener('scroll', function() {
        var atBottom = window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 40;
        if (atBottom) { setActive(ids.length - 1); return; }

        var active = 0;
        ids.forEach(function(id, i) {
            var el = document.getElementById(id);
            if (el && el.getBoundingClientRect().top <= 150) active = i;
        });
        setActive(active);
    }, { passive: true });

    pills.forEach(function(pill, i) {
        pill.addEventListener('click', function() { setActive(i); });
    });
})();
</script>
@endsection
