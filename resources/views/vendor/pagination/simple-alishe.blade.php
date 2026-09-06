@if ($paginator->hasPages())
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span aria-disabled="true" aria-label="@lang('pagination.previous')">
            <i class="fa-solid fa-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
    @endif

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    @else
        <span aria-disabled="true" aria-label="@lang('pagination.next')">
            <i class="fa-solid fa-chevron-right"></i>
        </span>
    @endif
@endif