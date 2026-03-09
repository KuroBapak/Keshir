@extends('layouts.app')
@section('title', 'Bahan Baku — Keshir')
@section('content')
<div class="page-header">
    <h2>🧪 Bahan Baku</h2>
    <a href="{{ route('ingredients.create') }}" class="btn btn-primary">+ Tambah Bahan</a>
</div>
<div class="card">
    <form method="GET" class="form-inline mb-2">
        <div class="form-group"><input type="text" name="search" class="form-control" placeholder="Cari bahan..." value="{{ request('search') }}"></div>
        <button type="submit" class="btn btn-outline btn-sm">🔍 Cari</button>
    </form>
    <table>
        <thead><tr><th>Nama</th><th>Stok</th><th>Min. Stok</th><th>Satuan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($ingredients as $ing)
            <tr>
                <td style="font-weight:600;">{{ $ing->name }}</td>
                <td>{{ number_format($ing->total_stock, 2) }}</td>
                <td>{{ number_format($ing->minimum_stock, 2) }}</td>
                <td>{{ $ing->unit }}</td>
                <td>
                    @if($ing->isBelowMinimum()) <span class="badge badge-danger">⚠️ Stok Rendah</span>
                    @else <span class="badge badge-success">✅ OK</span> @endif
                </td>
                <td>
                    <a href="{{ route('ingredients.show', $ing) }}" class="btn btn-xs btn-outline">Detail</a>
                    <a href="{{ route('ingredients.edit', $ing) }}" class="btn btn-xs btn-outline">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state">Belum ada bahan baku.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $ingredients->links() }}</div>
</div>
@endsection
