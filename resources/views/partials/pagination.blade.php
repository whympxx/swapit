@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Halaman">
        @if (!$paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></a>
        @endif

        @foreach (range(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page)
            <a href="{{ $paginator->url($page) }}" class="page-link {{ $page === $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></a>
        @endif
    </nav>
@endif
