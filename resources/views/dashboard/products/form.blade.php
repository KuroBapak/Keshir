@extends('layouts.app')
@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<div class="page-header">
    <h2>{{ isset($product) ? '✏️ Edit Produk' : '➕ Tambah Produk' }}</h2>
</div>
<div class="card" style="max-width:600px;">
    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data">
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
        
        <hr style="margin:1.5rem 0; border:none; border-top:1px solid #e2e8f0;">
        <h3 style="font-size:1rem; margin-bottom:1rem;">📷 Foto Produk (Maks 5)</h3>
        
        @if(isset($product) && is_array($product->photos) && count($product->photos) > 0)
            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
                @foreach($product->photos as $path)
                    <div style="position:relative; width:100px; height:100px; border:1px solid #e2e8f0; border-radius:0.5rem; overflow:hidden;">
                        <img src="{{ asset('storage/' . $path) }}" style="width:100%; height:100%; object-fit:cover;">
                        <label style="position:absolute; bottom:0; left:0; right:0; background:rgba(220,38,38,0.9); color:#fff; font-size:0.7rem; padding:0.2rem; text-align:center; cursor:pointer; margin:0;">
                            <input type="checkbox" name="delete_photos[]" value="{{ $path }}"> Hapus
                        </label>
                    </div>
                @endforeach
            </div>
            <div style="font-size:0.8rem; color:#64748b; margin-bottom:1rem;">Centang "Hapus" pada foto yang ingin dibuang.</div>
        @endif
        
        <div class="form-group">
            <label>Upload Foto Baru</label>
            <input type="file" name="photos[]" class="form-control" multiple accept="image/*" style="padding:0.5rem;">
            <div style="font-size:0.8rem; color:#64748b; margin-top:0.4rem;">Format: JPG, PNG, WEBP. Maks 2MB/file. Bisa pilih banyak (Max total 5).</div>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
