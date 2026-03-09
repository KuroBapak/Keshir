@extends('layouts.app')
@section('title', 'Produk — Keshir')
@section('content')
<div class="page-header">
    <h2>🍽️ Produk</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
</div>
<div class="card">
    <form method="GET" class="form-inline mb-2">
        <div class="form-group">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <select name="category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-outline btn-sm">🔍 Filter</button>
    </form>
    <table>
        <thead><tr><th>Nama</th><th>Kategori</th><th>Harga Dasar</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($products as $p)
            <tr>
                <td style="font-weight:600;">{{ $p->name }}</td>
                <td><span class="badge badge-info">{{ $p->category->name }}</span></td>
                <td>Rp {{ number_format($p->base_price, 0, ',', '.') }}</td>
                <td>
                    @if($p->is_active)<span class="badge badge-success">Aktif</span>
                    @else <span class="badge badge-danger">Nonaktif</span>@endif
                </td>
                <td>
                    <a href="{{ route('products.show', $p) }}" class="btn btn-xs btn-outline">Detail</a>
                    <a href="{{ route('products.edit', $p) }}" class="btn btn-xs btn-outline">Edit</a>
                    <form action="{{ route('products.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                    </form>
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
