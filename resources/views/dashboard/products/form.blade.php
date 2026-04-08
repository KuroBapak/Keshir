@extends('layouts.app')
@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')

@push('styles')
<style>
    .form-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        max-width: 700px;
    }
    .form-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    }
    .form-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-body {
        padding: 1.5rem;
    }
    .form-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    .form-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .form-section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
    }
    
    .photo-preview {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .photo-item {
        position: relative;
        width: 100px;
        height: 100px;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .photo-item:hover { border-color: var(--primary); }
    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-delete {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(239, 68, 68, 0.95);
        color: #fff;
        font-size: 0.7rem;
        padding: 0.35rem;
        text-align: center;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }
    .photo-delete input { margin: 0; }
    
    .file-upload {
        border: 2px dashed var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--bg);
    }
    .file-upload:hover {
        border-color: var(--primary);
        background: var(--primary-50);
    }
    .file-upload input { display: none; }
    .file-upload-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .file-upload-text { font-size: 0.9rem; color: var(--text-secondary); }
    .file-upload-hint { font-size: 0.8rem; color: var(--muted); margin-top: 0.35rem; }
    
    .toggle-active {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--bg);
        border-radius: var(--radius);
        cursor: pointer;
    }
    .toggle-active input { width: 18px; height: 18px; cursor: pointer; }
    .toggle-active-label { font-weight: 600; color: var(--text); }
    
    .form-actions {
        display: flex;
        gap: 0.75rem;
        padding-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>{{ isset($product) ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' }}</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">
            {{ isset($product) ? 'Ubah informasi produk ' . $product->name : 'Isi detail produk baru untuk menu Anda' }}
        </p>
    </div>
</div>

<div class="form-card">
    <div class="form-header">
        <h3><span>🍽️</span> Informasi Produk</h3>
    </div>
    <div class="form-body">
        <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($product)) @method('PUT') @endif
            
            <!-- Basic Info -->
            <div class="form-section">
                <div class="form-section-title"><span>📝</span> Informasi Dasar</div>
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" placeholder="Contoh: Cappuccino, Nasi Goreng Spesial" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Harga Dasar (Rp)</label>
                        <input type="number" name="base_price" class="form-control" step="100" min="0" value="{{ old('base_price', $product->base_price ?? '') }}" placeholder="25000" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id', $product->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi (Opsional)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat produk...">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Tags (Opsional)</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags', $product->tags ?? '') }}" placeholder="spicy, vegetarian, best-seller (pisahkan dengan koma)">
                </div>
                <label class="toggle-active">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    <span class="toggle-active-label">✅ Produk Aktif (tampil di POS & menu)</span>
                </label>
            </div>
            
            <!-- Photos -->
            <div class="form-section">
                <div class="form-section-title"><span>📷</span> Foto Produk (Maks 5)</div>
                
                @if(isset($product) && is_array($product->photos) && count($product->photos) > 0)
                    <div class="photo-preview">
                        @foreach($product->photos as $path)
                            <div class="photo-item">
                                <img src="{{ asset('storage/' . $path) }}" alt="Product photo">
                                <label class="photo-delete">
                                    <input type="checkbox" name="delete_photos[]" value="{{ $path }}"> Hapus
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p style="font-size: 0.8rem; color: var(--muted); margin-bottom: 1rem;">Centang "Hapus" pada foto yang ingin dibuang.</p>
                @endif
                
                <label class="file-upload">
                    <input type="file" name="photos[]" multiple accept="image/*">
                    <div class="file-upload-icon">📁</div>
                    <div class="file-upload-text">Klik untuk upload foto</div>
                    <div class="file-upload-hint">JPG, PNG, WEBP · Maks 2MB per file · Bisa pilih banyak</div>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">💾 Simpan Produk</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
