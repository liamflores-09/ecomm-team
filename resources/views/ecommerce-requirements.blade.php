@extends('layouts.app')

@section('title', 'E-commerce Requirements — Ecomm Dept Hub')

@section('styles')
<style>
    .req-table-wrap {
        background: var(--white);
        border-radius: 8px;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
    }

    .req-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .req-table thead th {
        background: var(--fg);
        color: var(--white);
        padding: 0.875rem 1rem;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        text-align: left;
        white-space: nowrap;
    }

    .req-table thead th:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        background: var(--fg);
    }

    .req-table tbody td {
        padding: 0.875rem 1rem;
        border-top: 2px solid var(--muted);
        color: var(--gray-700);
        font-weight: 500;
        vertical-align: top;
        line-height: 1.6;
    }

    .req-table tbody td:first-child {
        position: sticky;
        left: 0;
        background: var(--white);
        font-weight: 700;
        color: var(--fg);
        z-index: 1;
        white-space: nowrap;
        min-width: 160px;
    }

    .req-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .req-table tbody tr:hover td:first-child {
        background: #F1F5F9;
    }

    .req-table .check {
        color: var(--secondary);
        font-weight: 700;
    }

    .req-table .dash {
        color: var(--gray-300);
    }

    .req-table strong {
        font-weight: 700;
        color: var(--fg);
    }

    .req-table ul {
        margin: 0.25rem 0 0;
        padding-left: 1rem;
        list-style: disc;
    }

    .req-table ul li {
        margin-bottom: 0.125rem;
    }

    .platform-header {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .platform-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .platform-dot.dot-lazada { background: #0F146D; }
    .platform-dot.dot-shopify { background: #96BF48; }
    .platform-dot.dot-shopee { background: #EE4D2D; }
    .platform-dot.dot-tiktok { background: #000000; }

    @media (max-width: 768px) {
        .req-table-wrap {
            margin: 0 -1rem;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .req-table {
            min-width: 750px;
        }

        .req-table thead th:first-child,
        .req-table tbody td:first-child {
            position: sticky;
            left: 0;
            z-index: 2;
        }

        .req-table thead th:first-child {
            z-index: 3;
        }

        .req-table tbody td:first-child {
            box-shadow: 2px 0 0 var(--white);
        }
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
        <li><a href="{{ route('ecommerce-requirements') }}" class="active"><i class="fas fa-clipboard-list"></i> E-commerce Requirements</a></li>
        <li><a href="{{ route('price-calculator') }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
        <li><a href="{{ route('end-of-day') }}"><i class="fas fa-calendar-check"></i> End-of-Day Report</a></li>
        <li><a href="{{ route('important-links') }}"><i class="fas fa-link"></i> Important Links</a></li>
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

    <div class="top-bar anim-up" style="margin-bottom: 2rem;">
        <div>
            <h2>E-commerce Requirements</h2>
            <p>Platform-specific requirements for product posting</p>
        </div>
    </div>

    <div class="req-table-wrap anim-up d1">
        <table class="req-table">
            <thead>
                <tr>
                    <th>Required Details</th>
                    <th><span class="platform-header"><span class="platform-dot dot-lazada"></span> Lazada</span></th>
                    <th><span class="platform-header"><span class="platform-dot dot-shopify"></span> Shopify</span></th>
                    <th><span class="platform-header"><span class="platform-dot dot-shopee"></span> Shopee</span></th>
                    <th><span class="platform-header"><span class="platform-dot dot-tiktok"></span> TikTok</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Product Title</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                </tr>
                <tr>
                    <td>Long Title (PR)</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                </tr>
                <tr>
                    <td>Shopify Title (PR)</td>
                    <td><span class="dash">—</span></td>
                    <td>Max 100 characters</td>
                    <td><span class="dash">—</span></td>
                    <td><span class="dash">—</span></td>
                </tr>
                <tr>
                    <td>Short Title (PR)</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                    <td>Max 255 characters</td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td>Category</td>
                    <td>Tags</td>
                    <td><span class="dash">—</span></td>
                    <td><span class="dash">—</span></td>
                </tr>
                <tr>
                    <td>Product Images</td>
                    <td><span class="check">✓</span> Minimum 3 images required. First image must have a white background. High-resolution images only (not pixelated).</td>
                    <td><span class="check">✓</span> Minimum 3 images required. First image must have a white background. High-resolution images only (not pixelated).</td>
                    <td><span class="check">✓</span> Minimum 3 images required. First image must have a white background. High-resolution images only (not pixelated).</td>
                    <td><span class="check">✓</span> Minimum 3 images required. First image must have a white background. High-resolution images only (not pixelated).</td>
                </tr>
                <tr>
                    <td>Video</td>
                    <td>YouTube URL (&lt; 60 seconds)</td>
                    <td>YouTube URL (&lt; 60 seconds)</td>
                    <td>Direct video upload (&lt; 60 seconds)</td>
                    <td>Direct video upload (&lt; 60 seconds)</td>
                </tr>
                <tr>
                    <td>Product Specs / Attributes</td>
                    <td>Varies by category. Fill all fields with <strong>KEY</strong> and (*) whenever possible. Use specific Brand value.</td>
                    <td>Varies by category. Fill all fields with <strong>KEY</strong> and (*) whenever possible. Use specific Brand value.</td>
                    <td>Varies by category. Fill all fields with <strong>KEY</strong> and (*) whenever possible. Use specific Brand value.</td>
                    <td>Varies by category. Fill all fields with <strong>KEY</strong> and (*) whenever possible. For Brand, use <strong>JG</strong>.</td>
                </tr>
                <tr>
                    <td>Stocks</td>
                    <td>inFlow SKU Quantity on Hand*</td>
                    <td>inFlow SKU Quantity on Hand*</td>
                    <td>inFlow SKU Quantity on Hand*</td>
                    <td>inFlow SKU Quantity on Hand*</td>
                </tr>
                <tr>
                    <td>Seller SKU</td>
                    <td>Seller SKU<br><em>Does not require <code>-grp</code> SKU</em></td>
                    <td>
                        Posting Types:<br>
                        <ul>
                            <li>Single Posting = Single SKU</li>
                            <li>Variation Posting = Group of SKUs</li>
                            <li>Use Sellu to generate/sync <code>-grp</code></li>
                            <li>Integration: <code>app.selluseller.com</code></li>
                        </ul>
                    </td>
                    <td>Parent SKU (Single Product)<br>SKU (Variation Product)<br>Requires <code>-grp</code> for Parent SKU when Variation/Group Product</td>
                    <td>Seller SKU<br><em>Does not require <code>-grp</code> SKU</em></td>
                </tr>
                <tr>
                    <td>Track Quantity</td>
                    <td><span class="check">✓</span> Track Quantity</td>
                    <td><span class="check">✓</span> Track Quantity</td>
                    <td><span class="check">✓</span> Track Quantity</td>
                    <td><span class="check">✓</span> Track Quantity</td>
                </tr>
                <tr>
                    <td>Product Description</td>
                    <td>
                        <strong>No Limit</strong><br><br>
                        Required Sections:<br>
                        <ol style="margin: 0.25rem 0; padding-left: 1.25rem;">
                            <li>Product Highlights</li>
                            <li>Main Description</li>
                            <li>What's in the Box</li>
                        </ol><br>
                        Guidelines:
                        <ul>
                            <li>Use JG Banners at the beginning</li>
                            <li>Highlights must be bullet points</li>
                            <li>Use detailed high-resolution images</li>
                            <li>Lazada description should be finalized first</li>
                        </ul>
                    </td>
                    <td>No Limit</td>
                    <td>
                        <strong>Maximum 12 Images</strong><br>
                        <strong>Maximum 3,000 Characters</strong><br><br>
                        Guidelines:
                        <ul>
                            <li>Use JG Banners at the beginning</li>
                        </ul>
                    </td>
                    <td>
                        <strong>Maximum 4,000 Characters</strong> (including images)<br><br>
                        Guidelines:
                        <ul>
                            <li>Use JG Tagline only</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Warranty</td>
                    <td>Local Supplier Warranty<br>Warranty Duration depends on brand</td>
                    <td><span class="dash">—</span></td>
                    <td>Supplier Warranty</td>
                    <td>Supplier Warranty</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
