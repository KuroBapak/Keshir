@extends('layouts.app')
@section('title', 'Kategori — Keshir')
@section('content')
<div class="page-header">
    <h2>📂 Kategori</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Nama</th><th>Jumlah Produk</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td style="font-weight:600;">{{ $cat->name }}</td>
                <td>{{ $cat->products_count }} produk</td>
                <td>
                    <a href="{{ route('categories.edit', $cat) }}" class="btn btn-xs btn-outline">Edit</a>
                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="empty-state">Belum ada kategori.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $categories->links() }}</div>
</div>
@endsection
