@extends('layouts.app')
@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<div class="page-header">
    <h2>{{ isset($product) ? '✏️ Edit Produk' : '➕ Tambah Produk' }}</h2>
</div>
<div class="card" style="max-width:600px;">
    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST">
        @csrf
        @if(isset($product)) @method('PUT') @endif
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Harga Dasar (Rp)</label>
                <input type="number" name="base_price" class="form-control" step="100" min="0" value="{{ old('base_price', $product->base_price ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $product->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Tags (pisahkan koma, contoh: spicy,vegetarian)</label>
            <input type="text" name="tags" class="form-control" value="{{ old('tags', $product->tags ?? '') }}">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}> Aktif</label>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
