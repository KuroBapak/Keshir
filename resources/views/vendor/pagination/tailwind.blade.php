@if ($paginator->hasPages())
<div class="keshir-pagination">
    <div class="pagination-info">
        Menampilkan 
        <strong>{{ $paginator->firstItem() ?? 0 }}</strong> - <strong>{{ $paginator->lastItem() ?? 0 }}</strong> 
        dari <strong>{{ $paginator->total() }}</strong> data
    </div>
    
    <div class="pagination-controls">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn disabled">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" rel="prev">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="page-dots">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-num active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-num">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" rel="next">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="page-btn disabled">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </div>
</div>

<style>
.keshir-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 0.5rem 0;
}
.pagination-info {
    color: var(--muted, #64748b);
    font-size: 0.875rem;
}
.pagination-info strong {
    color: var(--text, #1e293b);
    font-weight: 600;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.page-btn, .page-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid var(--border, #e2e8f0);
    background: var(--card, #ffffff);
    color: var(--text, #1e293b);
    transition: all 0.2s ease;
    cursor: pointer;
}
.page-btn:hover:not(.disabled), .page-num:hover:not(.active) {
    background: var(--primary-50, #eff6ff);
    border-color: var(--primary, #2563eb);
    color: var(--primary, #2563eb);
}
.page-num.active {
    background: var(--primary, #2563eb);
    border-color: var(--primary, #2563eb);
    color: #ffffff;
    font-weight: 600;
}
.page-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: var(--bg, #f8fafc);
}
.page-dots {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    color: var(--muted, #64748b);
    font-size: 0.875rem;
}
@media (max-width: 640px) {
    .keshir-pagination {
        flex-direction: column;
        align-items: stretch;
    }
    .pagination-controls {
        justify-content: center;
    }
}
</style>
@endif
