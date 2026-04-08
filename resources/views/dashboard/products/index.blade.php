@extends('layouts.app')
@section('title', 'Produk — Keshir')

@push('styles')
<style>
    .product-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-icon.blue { background: var(--primary-50); }
    .stat-icon.green { background: #d1fae5; }
    .stat-icon.orange { background: #ffedd5; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text); }
    .stat-label { font-size: 0.85rem; color: var(--muted); }
    
    .filter-bar {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
        padding: 1rem;
        background: var(--bg);
        border-radius: var(--radius);
        margin-bottom: 1rem;
        border: 1px solid var(--border);
    }
    .filter-bar .form-control {
        min-width: 180px;
    }
    
    .product-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .product-table thead th {
        background: var(--bg);
        padding: 0.85rem 1rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        border-bottom: 2px solid var(--border);
    }
    .product-table tbody tr {
        transition: all 0.2s ease;
    }
    .product-table tbody tr:hover {
        background: var(--primary-50);
    }
    .product-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .product-name {
        font-weight: 700;
        color: var(--text);
        font-size: 0.95rem;
    }
    .product-price {
        font-weight: 700;
        color: var(--primary);
    }
    
    .action-btns {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>🍽️ Produk</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Kelola semua produk menu Anda</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <span>+</span> Tambah Produk
    </a>
</div>

<div class="product-stats">
    <div class="stat-card">
        <div class="stat-icon blue">📦</div>
        <div>
            <div class="stat-value">{{ $products->total() }}</div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value">{{ $products->where('is_active', true)->count() }}</div>
            <div class="stat-label">Produk Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">📂</div>
        <div>
            <div class="stat-value">{{ $categories->count() }}</div>
            <div class="stat-label">Kategori</div>
        </div>
    </div>
</div>

<div class="card">
    <form method="GET" class="filter-bar">
        <div class="form-group" style="flex:1; min-width:200px; margin:0;">
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari produk..." value="{{ request('search') }}">
        </div>
        <div class="form-group" style="margin:0;">
            <select name="category_id" class="form-control">
                <option value="">📂 Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request('search') || request('category_id'))
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>
    
    <div style="overflow-x: auto;">
        <table class="product-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>
                        <span class="product-name">{{ $p->name }}</span>
                        @if($p->variants->count() > 0)
                            <div style="font-size: 0.8rem; color: var(--muted); margin-top: 0.25rem;">
                                📦 {{ $p->variants->count() }} varian
                            </div>
                        @endif
                    </td>
                    <td><span class="badge badge-info">{{ $p->category->name }}</span></td>
                    <td class="product-price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</td>
                    <td>
                        @if($p->is_active)
                            <span class="badge badge-success">✅ Aktif</span>
                        @else 
                            <span class="badge badge-danger">❌ Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns" style="justify-content: flex-end;">
                            <a href="{{ route('products.show', $p) }}" class="btn btn-xs btn-outline">👁️ Detail</a>
                            <a href="{{ route('products.edit', $p) }}" class="btn btn-xs btn-outline">✏️ Edit</a>
                            <form action="{{ route('products.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📦</div>
                        <p style="color: var(--muted);">Belum ada produk. <a href="{{ route('products.create') }}" style="color: var(--primary);">Tambah sekarang →</a></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($products->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--border);">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
