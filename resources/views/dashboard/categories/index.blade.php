@extends('layouts.app')
@section('title', 'Kategori — Keshir')

@push('styles')
<style>
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
    .category-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .category-card:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }
    .category-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--primary-50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .category-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.35rem;
    }
    .category-count {
        font-size: 0.9rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .category-actions {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.5rem;
    }
    
    .view-toggle {
        display: flex;
        gap: 0.35rem;
        background: var(--bg);
        padding: 0.25rem;
        border-radius: 0.5rem;
    }
    .view-btn {
        padding: 0.4rem 0.75rem;
        border: none;
        background: transparent;
        color: var(--muted);
        border-radius: 0.35rem;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .view-btn.active {
        background: var(--card);
        color: var(--primary);
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>📂 Kategori</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Organisasi produk berdasarkan kategori</p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <div class="view-toggle">
            <button class="view-btn active" onclick="setView('grid')">📦 Grid</button>
            <button class="view-btn" onclick="setView('table')">📋 Table</button>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
    </div>
</div>

<!-- Grid View -->
<div class="category-grid" id="grid-view">
    @forelse($categories as $cat)
    <div class="category-card">
        <div class="category-icon">📂</div>
        <div class="category-name">{{ $cat->name }}</div>
        <div class="category-count">
            <span>📦</span>
            <span>{{ $cat->products_count }} produk</span>
        </div>
        <div class="category-actions">
            <a href="{{ route('categories.edit', $cat) }}" class="btn btn-sm btn-outline" style="flex:1;">✏️ Edit</a>
            <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Semua produk dalam kategori ini akan kehilangan kategorinya.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📂</div>
        <p style="color: var(--muted);">Belum ada kategori. <a href="{{ route('categories.create') }}" style="color: var(--primary);">Tambah sekarang →</a></p>
    </div>
    @endforelse
</div>

<!-- Table View (hidden by default) -->
<div class="card" id="table-view" style="display: none;">
    <table>
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Jumlah Produk</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-50); display: flex; align-items: center; justify-content: center;">📂</div>
                        <span style="font-weight: 700;">{{ $cat->name }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge badge-info">{{ $cat->products_count }} produk</span>
                </td>
                <td style="text-align: right;">
                    <a href="{{ route('categories.edit', $cat) }}" class="btn btn-xs btn-outline">✏️ Edit</a>
                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center; padding: 2rem; color: var(--muted);">Belum ada kategori.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($categories->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--border);">
        {{ $categories->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function setView(view) {
    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
    if (view === 'grid') {
        document.getElementById('grid-view').style.display = 'grid';
        document.getElementById('table-view').style.display = 'none';
        document.querySelector('.view-btn:first-child').classList.add('active');
    } else {
        document.getElementById('grid-view').style.display = 'none';
        document.getElementById('table-view').style.display = 'block';
        document.querySelector('.view-btn:last-child').classList.add('active');
    }
    localStorage.setItem('categoryView', view);
}
// Restore view preference
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('categoryView');
    if (saved) setView(saved);
});
</script>
@endpush
@endsection
