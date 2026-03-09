@extends('layouts.app')
@section('title', isset($ingredient) ? 'Edit Bahan' : 'Tambah Bahan')
@section('content')
<div class="page-header"><h2>{{ isset($ingredient) ? '✏️ Edit Bahan' : '➕ Tambah Bahan Baku' }}</h2></div>
<div class="card" style="max-width:500px;">
    <form action="{{ isset($ingredient) ? route('ingredients.update', $ingredient) : route('ingredients.store') }}" method="POST">
        @csrf
        @if(isset($ingredient)) @method('PUT') @endif
        <div class="form-group">
            <label>Nama Bahan</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $ingredient->name ?? '') }}" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Satuan Dasar (unit terkecil)</label>
                <input type="text" name="unit" class="form-control" value="{{ old('unit', $ingredient->unit ?? '') }}" placeholder="gram, ml, pcs" required>
            </div>
            <div class="form-group">
                <label>Isi per Pack (opsional)</label>
                <input type="number" name="content_per_pack" class="form-control" step="0.01" min="0" value="{{ old('content_per_pack', $ingredient->content_per_pack ?? '') }}" placeholder="Cth: 500 (1 pack = 500 gram)">
                <small style="color:var(--muted);font-size:0.7rem;">Kosongkan jika beli satuan, isi jika beli per pack</small>
            </div>
        </div>
        <div class="form-group">
            <label>Minimum Stok (Alert) — dalam satuan dasar</label>
            <input type="number" name="minimum_stock" class="form-control" step="0.01" min="0" value="{{ old('minimum_stock', $ingredient->minimum_stock ?? 0) }}" required style="max-width:200px;">
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('ingredients.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
