@if ($paginator->hasPages())
    <div class="pager">
        @if ($paginator->onFirstPage())
            <span class="btn ghost off">← Previous</span>
        @else
            <a class="btn ghost" href="{{ $paginator->previousPageUrl() }}">← Previous</a>
        @endif
        <span class="muted">Page {{ $paginator->currentPage() }}</span>
        @if ($paginator->hasMorePages())
            <a class="btn ghost" href="{{ $paginator->nextPageUrl() }}">Next →</a>
        @else
            <span class="btn ghost off">Next →</span>
        @endif
    </div>
@endif
