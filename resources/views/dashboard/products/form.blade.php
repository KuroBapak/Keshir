@extends('layouts.app')
@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')

@php
    $variantRows = old('variants');
    if ($variantRows === null) {
        $variantRows = isset($product) ? $product->variants->map(fn ($variant) => [
            'variant_name' => $variant->variant_name,
            'additional_price' => $variant->additional_price,
        ])->toArray() : [];
    }
    if (empty($variantRows)) {
        $variantRows = [['variant_name' => '', 'additional_price' => 0]];
    }

    $addonRows = old('addons');
    if ($addonRows === null) {
        $addonRows = isset($product) ? $product->addons->map(fn ($addon) => [
            'addon_name' => $addon->addon_name,
            'price' => $addon->price,
        ])->toArray() : [];
    }
    if (empty($addonRows)) {
        $addonRows = [['addon_name' => '', 'price' => 0]];
    }
@endphp

@push('styles')
<style>
    .form-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        max-width: 920px;
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
        overflow: visible;
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

    .option-help {
        font-size: 0.8rem;
        color: var(--muted);
        margin-bottom: 0.75rem;
    }

    .option-list {
        display: grid;
        gap: 0.75rem;
    }

    .option-row {
        display: grid;
        grid-template-columns: 1fr 180px auto;
        gap: 0.75rem;
        align-items: center;
        padding: 0.85rem;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
    }

    .option-row .form-control {
        width: 100%;
    }

    .option-actions {
        display: flex;
        justify-content: flex-start;
        margin-top: 0.75rem;
    }

    .option-add-btn {
        padding: 0.7rem 1rem;
    }

    .option-remove-btn {
        padding: 0.65rem 0.9rem;
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .option-row {
            grid-template-columns: 1fr;
        }
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
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 100px));
        gap: 0.75rem;
        margin-bottom: 1rem;
        width: 100%;
        justify-content: start;
        align-items: start;
    }
    .photo-item {
        position: relative;
        width: 100px;
        height: 100px;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.2s ease;
        background: #f8fafc;
        display: block;
    }
    .photo-item:hover { border-color: var(--primary); }
    .photo-item.removed {
        opacity: 0.45;
        filter: grayscale(0.7);
        border-color: #fca5a5;
    }
    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .photo-delete,
    .photo-delete-btn {
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
        border: none;
        width: 100%;
        min-height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        z-index: 1;
    }
    .photo-delete input { margin: 0; }
    .photo-delete-btn.undo {
        background: rgba(16, 185, 129, 0.95);
    }
    
    .file-upload {
        border: 2px dashed var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        width: 100%;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--bg);
        display: block;
    }
    .file-upload:hover {
        border-color: var(--primary);
        background: var(--primary-50);
    }
    .file-upload input { display: none; }
    .file-upload-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .file-upload-text { font-size: 0.9rem; color: var(--text-secondary); }
    .file-upload-hint { font-size: 0.8rem; color: var(--muted); margin-top: 0.35rem; }
    .upload-meta {
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: var(--muted);
    }
    .upload-errors {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #b91c1c;
        font-weight: 600;
    }
    
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
            </div>

            <!-- Variants -->
            <div class="form-section">
                <div class="form-section-title"><span>🔀</span> Varian Produk</div>
                <p class="option-help">Contoh: Hot, Ice, Small, Medium, Large. Jika menu tidak punya varian, biarkan kosong.</p>
                <div id="variantRows" class="option-list">
                    @foreach($variantRows as $index => $variantRow)
                        <div class="option-row" data-row="variant">
                            <input type="text" name="variants[{{ $index }}][variant_name]" class="form-control" placeholder="Nama varian" value="{{ $variantRow['variant_name'] ?? '' }}">
                            <input type="number" name="variants[{{ $index }}][additional_price]" class="form-control" step="100" min="0" placeholder="Tambahan harga" value="{{ $variantRow['additional_price'] ?? 0 }}">
                            <button type="button" class="btn btn-outline option-remove-btn" data-remove-row>Hapus</button>
                        </div>
                    @endforeach
                </div>
                <div class="option-actions">
                    <button type="button" class="btn btn-outline option-add-btn" data-add-variant>+ Tambah Varian</button>
                </div>
            </div>

            <!-- Add-ons -->
            <div class="form-section">
                <div class="form-section-title"><span>➕</span> Add-ons Produk</div>
                <p class="option-help">Contoh: Extra Shot, Keju, Saus, Whipped Cream. Jika tidak ada add-on, biarkan kosong.</p>
                <div id="addonRows" class="option-list">
                    @foreach($addonRows as $index => $addonRow)
                        <div class="option-row" data-row="addon">
                            <input type="text" name="addons[{{ $index }}][addon_name]" class="form-control" placeholder="Nama add-on" value="{{ $addonRow['addon_name'] ?? '' }}">
                            <input type="number" name="addons[{{ $index }}][price]" class="form-control" step="100" min="0" placeholder="Harga add-on" value="{{ $addonRow['price'] ?? 0 }}">
                            <button type="button" class="btn btn-outline option-remove-btn" data-remove-row>Hapus</button>
                        </div>
                    @endforeach
                </div>
                <div class="option-actions">
                    <button type="button" class="btn btn-outline option-add-btn" data-add-addon>+ Tambah Add-on</button>
                </div>
            </div>

            <div class="form-section">
                <label class="toggle-active">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    <span class="toggle-active-label">✅ Produk Aktif (tampil di POS & menu)</span>
                </label>
            </div>
            
            <!-- Photos -->
            <div class="form-section">
                <div class="form-section-title"><span>📷</span> Foto Produk (Maks 5)</div>
                
                @if(isset($product) && is_array($product->photos) && count($product->photos) > 0)
                    <div class="photo-preview" id="existingPhotoPreview">
                        @foreach($product->photos as $path)
                            <div class="photo-item" data-existing-photo="{{ $path }}">
                                <img src="{{ asset('storage/' . $path) }}" alt="Product photo">
                                <button type="button" class="photo-delete-btn" data-path="{{ $path }}">Hapus</button>
                            </div>
                        @endforeach
                    </div>
                    <div id="deletePhotosContainer"></div>
                    <p style="font-size: 0.8rem; color: var(--muted); margin-bottom: 1rem;">Klik tombol Hapus pada foto yang ingin dibuang, lalu Simpan Produk.</p>
                @endif
                
                <label class="file-upload" for="photosInput">
                    <input id="photosInput" type="file" name="photos[]" multiple accept="image/*">
                    <div class="file-upload-icon">📁</div>
                    <div class="file-upload-text">Klik untuk upload foto</div>
                    <div class="file-upload-hint">JPG, PNG, WEBP · Maks 2MB per file · Bisa pilih banyak</div>
                    <div class="upload-meta" id="uploadMeta">Belum ada file dipilih</div>
                    <div class="upload-errors" id="uploadErrors" style="display:none"></div>
                </label>

                <div id="newPhotoPreview" class="photo-preview" style="display:none"></div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">💾 Simpan Produk</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const photosInput = document.getElementById('photosInput');
        const newPreview = document.getElementById('newPhotoPreview');
        const uploadMeta = document.getElementById('uploadMeta');
        const uploadErrors = document.getElementById('uploadErrors');
        const deleteContainer = document.getElementById('deletePhotosContainer');
        const variantRows = document.getElementById('variantRows');
        const addonRows = document.getElementById('addonRows');
        const removedExisting = new Set();
        let selectedFiles = [];
        let variantRowIndex = variantRows ? variantRows.querySelectorAll('[data-row="variant"]').length : 0;
        let addonRowIndex = addonRows ? addonRows.querySelectorAll('[data-row="addon"]').length : 0;

        function createOptionRow(type, index, values = {}) {
            if (type === 'variant') {
                return `
                    <div class="option-row" data-row="variant">
                        <input type="text" name="variants[${index}][variant_name]" class="form-control" placeholder="Nama varian" value="${values.variant_name ?? ''}">
                        <input type="number" name="variants[${index}][additional_price]" class="form-control" step="100" min="0" placeholder="Tambahan harga" value="${values.additional_price ?? 0}">
                        <button type="button" class="btn btn-outline option-remove-btn" data-remove-row>Hapus</button>
                    </div>
                `;
            }

            return `
                <div class="option-row" data-row="addon">
                    <input type="text" name="addons[${index}][addon_name]" class="form-control" placeholder="Nama add-on" value="${values.addon_name ?? ''}">
                    <input type="number" name="addons[${index}][price]" class="form-control" step="100" min="0" placeholder="Harga add-on" value="${values.price ?? 0}">
                    <button type="button" class="btn btn-outline option-remove-btn" data-remove-row>Hapus</button>
                </div>
            `;
        }

        function addOptionRow(type) {
            const container = type === 'variant' ? variantRows : addonRows;
            if (!container) return;

            const index = type === 'variant' ? variantRowIndex++ : addonRowIndex++;
            container.insertAdjacentHTML('beforeend', createOptionRow(type, index));
        }

        if (variantRows) {
            variantRows.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                const row = button.closest('[data-row="variant"]');
                if (row) row.remove();
            });
        }

        if (addonRows) {
            addonRows.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                const row = button.closest('[data-row="addon"]');
                if (row) row.remove();
            });
        }

        document.querySelector('[data-add-variant]')?.addEventListener('click', () => addOptionRow('variant'));
        document.querySelector('[data-add-addon]')?.addEventListener('click', () => addOptionRow('addon'));

        function updateUploadMeta() {
            if (!uploadMeta) return;
            uploadMeta.innerText = selectedFiles.length > 0
                ? `${selectedFiles.length} file dipilih`
                : 'Belum ada file dipilih';
        }

        function syncInputFiles() {
            if (!photosInput) return;
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            photosInput.files = dt.files;
        }

        function renderNewPreview() {
            if (!newPreview) return;

            newPreview.innerHTML = '';
            if (selectedFiles.length === 0) {
                newPreview.style.display = 'none';
                updateUploadMeta();
                return;
            }

            selectedFiles.forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'photo-item';

                const img = document.createElement('img');
                img.alt = file.name;
                img.src = URL.createObjectURL(file);
                img.onload = () => URL.revokeObjectURL(img.src);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'photo-delete-btn';
                btn.innerText = 'Hapus';
                btn.addEventListener('click', () => {
                    selectedFiles.splice(index, 1);
                    syncInputFiles();
                    renderNewPreview();
                });

                item.appendChild(img);
                item.appendChild(btn);
                newPreview.appendChild(item);
            });

            newPreview.style.display = 'flex';
            updateUploadMeta();
        }

        function addDeleteInput(path) {
            if (!deleteContainer || removedExisting.has(path)) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_photos[]';
            input.value = path;
            input.dataset.path = path;
            deleteContainer.appendChild(input);
            removedExisting.add(path);
        }

        function removeDeleteInput(path) {
            if (!deleteContainer || !removedExisting.has(path)) return;
            const input = deleteContainer.querySelector(`input[data-path="${CSS.escape(path)}"]`);
            if (input) input.remove();
            removedExisting.delete(path);
        }

        document.querySelectorAll('.photo-delete-btn[data-path]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const path = btn.dataset.path;
                const card = btn.closest('.photo-item');
                if (!path || !card) return;

                const isRemoved = card.classList.contains('removed');
                if (isRemoved) {
                    card.classList.remove('removed');
                    btn.classList.remove('undo');
                    btn.innerText = 'Hapus';
                    removeDeleteInput(path);
                } else {
                    card.classList.add('removed');
                    btn.classList.add('undo');
                    btn.innerText = 'Batal';
                    addDeleteInput(path);
                }
            });
        });

        if (photosInput) {
            photosInput.addEventListener('change', () => {
                if (!uploadErrors) return;
                uploadErrors.style.display = 'none';
                uploadErrors.innerText = '';

                const incoming = Array.from(photosInput.files || []);
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                const maxBytes = 2 * 1024 * 1024;
                const errs = [];

                incoming.forEach((file) => {
                    if (!validTypes.includes(file.type)) {
                        errs.push(`${file.name} bukan format JPG/PNG/WEBP.`);
                        return;
                    }
                    if (file.size > maxBytes) {
                        errs.push(`${file.name} melebihi 2MB.`);
                        return;
                    }
                    selectedFiles.push(file);
                });

                if (selectedFiles.length > 5) {
                    errs.push('Maksimal 5 foto per produk.');
                    selectedFiles = selectedFiles.slice(0, 5);
                }

                if (errs.length > 0) {
                    uploadErrors.style.display = 'block';
                    uploadErrors.innerText = errs.join(' ');
                }

                syncInputFiles();
                renderNewPreview();
            });

            updateUploadMeta();
        }
    })();
</script>
@endpush
