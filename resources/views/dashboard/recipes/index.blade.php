@extends('layouts.app')
@section('title', 'Resep — Keshir')
@section('content')
<div class="page-header"><h2>📋 Manajemen Resep</h2></div>
<div class="card">
    <table>
        <thead><tr><th>Produk</th><th>Kategori</th><th>Jumlah Bahan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($products as $p)
            <tr>
                <td style="font-weight:600;">{{ $p->name }}</td>
                <td><span class="badge badge-info">{{ $p->category->name }}</span></td>
                <td>{{ $p->recipe ? $p->recipe->details->count() . ' bahan' : '—' }}</td>
                <td>
                    @if($p->recipe && $p->recipe->details->count())
                        <span class="badge badge-success">✅ Ada Resep</span>
                    @else
                        <span class="badge badge-warning">⚠️ Belum Ada</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.35rem;">
                        <a href="{{ route('products.recipe.edit', $p) }}" class="btn btn-xs btn-primary">{{ $p->recipe ? '✏️ Edit Resep' : '+ Buat Resep' }}</a>
                        @if($p->recipe)
                            <form action="{{ route('products.recipe.destroy', $p) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus resep untuk produk ini?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline" style="color: var(--danger); border-color: var(--danger);">🗑️ Hapus Resep</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="empty-state">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $products->links() }}</div>
</div>
@endsection
