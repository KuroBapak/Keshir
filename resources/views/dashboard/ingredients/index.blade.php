@extends('layouts.app')
@section('title', 'Bahan Baku — Keshir')

@push('styles')
<style>
    .stock-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stock-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stock-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stock-icon.primary { background: var(--primary-50); }
    .stock-icon.success { background: #d1fae5; }
    .stock-icon.danger { background: #fee2e2; }
    .stock-value { font-size: 1.5rem; font-weight: 800; color: var(--text); }
    .stock-label { font-size: 0.85rem; color: var(--muted); }
    
    .ingredient-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ingredient-table thead th {
        background: var(--bg);
        padding: 0.85rem 1rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        border-bottom: 2px solid var(--border);
    }
    .ingredient-table tbody tr {
        transition: all 0.2s ease;
    }
    .ingredient-table tbody tr:hover { background: var(--primary-50); }
    .ingredient-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    
    .ingredient-name {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .ingredient-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: var(--primary-50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .ingredient-label { font-weight: 700; color: var(--text); }
    
    .stock-bar {
        width: 80px;
        height: 6px;
        background: var(--border);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.35rem;
    }
    .stock-bar-fill { height: 100%; border-radius: 3px; }
    .stock-bar-fill.ok { background: #10b981; }
    .stock-bar-fill.low { background: #f59e0b; }
    .stock-bar-fill.critical { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>🧪 Bahan Baku</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Kelola stok bahan baku dan inventori</p>
    </div>
    <a href="{{ route('ingredients.create') }}" class="btn btn-primary">+ Tambah Bahan</a>
</div>

<!-- Stock Overview -->
<div class="stock-overview">
    <div class="stock-card">
        <div class="stock-icon primary">📦</div>
        <div>
            <div class="stock-value">{{ $ingredients->total() }}</div>
            <div class="stock-label">Total Bahan</div>
        </div>
    </div>
    <div class="stock-card">
        <div class="stock-icon success">✅</div>
        <div>
            <div class="stock-value">{{ $ingredients->filter(fn($i) => !$i->isBelowMinimum())->count() }}</div>
            <div class="stock-label">Stok Normal</div>
        </div>
    </div>
    <div class="stock-card">
        <div class="stock-icon danger">⚠️</div>
        <div>
            <div class="stock-value">{{ $ingredients->filter(fn($i) => $i->isBelowMinimum())->count() }}</div>
            <div class="stock-label">Stok Rendah</div>
        </div>
    </div>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 0.75rem; padding: 1rem; background: var(--bg); border-radius: var(--radius); margin-bottom: 1rem;">
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari bahan baku..." value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        @if(request('search'))
            <a href="{{ route('ingredients.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>
    
    <div style="overflow-x: auto;">
        <table class="ingredient-table">
            <thead>
                <tr>
                    <th>Bahan</th>
                    <th>Stok Saat Ini</th>
                    <th>Min. Stok</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredients as $ing)
                @php
                    $stockPercent = $ing->minimum_stock > 0 ? min(100, ($ing->total_stock / $ing->minimum_stock) * 50) : 100;
                    $stockClass = $ing->isBelowMinimum() ? 'critical' : ($stockPercent < 70 ? 'low' : 'ok');
                @endphp
                <tr>
                    <td>
                        <div class="ingredient-name">
                            <div class="ingredient-icon">🧪</div>
                            <span class="ingredient-label">{{ $ing->name }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text);">{{ number_format($ing->total_stock, 2) }}</div>
                        <div class="stock-bar">
                            <div class="stock-bar-fill {{ $stockClass }}" style="width: {{ $stockPercent }}%;"></div>
                        </div>
                    </td>
                    <td style="color: var(--muted);">{{ number_format($ing->minimum_stock, 2) }}</td>
                    <td><span class="badge badge-info">{{ $ing->unit }}</span></td>
                    <td>
                        @if($ing->isBelowMinimum())
                            <span class="badge badge-danger">⚠️ Stok Rendah</span>
                        @else
                            <span class="badge badge-success">✅ OK</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                            <a href="{{ route('ingredients.show', $ing) }}" class="btn btn-xs btn-outline">👁️ Detail</a>
                            <a href="{{ route('ingredients.edit', $ing) }}" class="btn btn-xs btn-outline">✏️ Edit</a>
                            <form action="{{ route('ingredients.destroy', $ing) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus bahan baku ini?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline" style="color: var(--danger); border-color: var(--danger);">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🧪</div>
                        <p style="color: var(--muted);">Belum ada bahan baku. <a href="{{ route('ingredients.create') }}" style="color: var(--primary);">Tambah sekarang →</a></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($ingredients->hasPages())
    <div class="pagination-wrapper" style="padding: 1.25rem; border-top: 1px solid var(--border); background: var(--bg);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="color: var(--muted); font-size: 0.875rem;">
                Menampilkan <strong style="color: var(--text);">{{ $ingredients->firstItem() }}</strong> - <strong style="color: var(--text);">{{ $ingredients->lastItem() }}</strong> dari <strong style="color: var(--text);">{{ $ingredients->total() }}</strong> bahan
            </div>
            <div style="display: flex; gap: 0.35rem; align-items: center;">
                @if($ingredients->onFirstPage())
                    <span style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; border: 1px solid var(--border); background: var(--bg); color: var(--muted); opacity: 0.5; cursor: not-allowed;">
                        ← Sebelumnya
                    </span>
                @else
                    <a href="{{ $ingredients->previousPageUrl() }}" style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; border: 1px solid var(--border); background: var(--card); color: var(--text); text-decoration: none; transition: all 0.2s ease;">
                        ← Sebelumnya
                    </a>
                @endif
                
                @foreach($ingredients->getUrlRange(1, $ingredients->lastPage()) as $page => $url)
                    @if($page == $ingredients->currentPage())
                        <span style="padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-size: 0.85rem; background: var(--primary); color: #fff; font-weight: 600; min-width: 40px; text-align: center;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-size: 0.85rem; border: 1px solid var(--border); background: var(--card); color: var(--text); text-decoration: none; transition: all 0.2s ease; min-width: 40px; text-align: center; display: inline-block;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
                
                @if($ingredients->hasMorePages())
                    <a href="{{ $ingredients->nextPageUrl() }}" style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; border: 1px solid var(--border); background: var(--card); color: var(--text); text-decoration: none; transition: all 0.2s ease;">
                        Selanjutnya →
                    </a>
                @else
                    <span style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; border: 1px solid var(--border); background: var(--bg); color: var(--muted); opacity: 0.5; cursor: not-allowed;">
                        Selanjutnya →
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
