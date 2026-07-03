@if ($paginator->hasPages())
<nav class="sku-pagination" aria-label="Pagination">
    @if ($paginator->onFirstPage())
        <span class="sku-page-link disabled"><i class="fas fa-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="sku-page-link" rel="prev"><i class="fas fa-chevron-left"></i></a>
    @endif

    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $window = 2;
    @endphp

    @if ($current - $window > 1)
        <a href="{{ $paginator->url(1) }}" class="sku-page-link">1</a>
        @if ($current - $window > 2)
            <span class="sku-page-dots">…</span>
        @endif
    @endif

    @for ($page = max(1, $current - $window); $page <= min($last, $current + $window); $page++)
        @if ($page == $current)
            <span class="sku-page-link active">{{ $page }}</span>
        @else
            <a href="{{ $paginator->url($page) }}" class="sku-page-link">{{ $page }}</a>
        @endif
    @endfor

    @if ($current + $window < $last)
        @if ($current + $window < $last - 1)
            <span class="sku-page-dots">…</span>
        @endif
        <a href="{{ $paginator->url($last) }}" class="sku-page-link">{{ $last }}</a>
    @endif

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="sku-page-link" rel="next"><i class="fas fa-chevron-right"></i></a>
    @else
        <span class="sku-page-link disabled"><i class="fas fa-chevron-right"></i></span>
    @endif
</nav>
@endif
