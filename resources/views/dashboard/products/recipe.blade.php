@extends('layouts.app')
@section('title', 'Resep ' . $product->name)
@section('content')
<div class="page-header">
    <h2>📋 Resep: {{ $product->name }}</h2>
    <a href="{{ route('recipes.index') }}" class="btn btn-sm btn-outline">← Kembali ke Daftar Resep</a>
</div>
<div class="card" style="max-width:750px;">
    <form action="{{ route('products.recipe.update', $product) }}" method="POST" id="recipe-form">
        @csrf @method('PUT')
        <div id="recipe-rows">
            @if($product->recipe && $product->recipe->details->count())
                @foreach($product->recipe->details as $i => $d)
                <div class="form-inline mb-1 recipe-row">
                    <div class="form-group" style="flex:2;">
                        <select name="ingredients[{{ $i }}][ingredient_id]" class="form-control ingredient-select" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}" data-unit="{{ $ing->unit }}" {{ $d->ingredient_id == $ing->id ? 'selected' : '' }}>{{ $ing->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <input type="number" name="ingredients[{{ $i }}][quantity]" class="form-control" step="0.01" min="0.01" value="{{ $d->quantity }}" placeholder="Jumlah" required>
                    </div>
                    <div class="form-group unit-label" style="min-width:60px;font-size:0.8rem;color:var(--muted);font-weight:600;">{{ $d->ingredient->unit }}</div>
                    <button type="button" class="btn btn-xs btn-danger" onclick="this.closest('.recipe-row').remove()">✕</button>
                </div>
                @endforeach
            @else
                <div class="form-inline mb-1 recipe-row">
                    <div class="form-group" style="flex:2;">
                        <select name="ingredients[0][ingredient_id]" class="form-control ingredient-select" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}" data-unit="{{ $ing->unit }}">{{ $ing->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <input type="number" name="ingredients[0][quantity]" class="form-control" step="0.01" min="0.01" placeholder="Jumlah" required>
                    </div>
                    <div class="form-group unit-label" style="min-width:60px;font-size:0.8rem;color:var(--muted);font-weight:600;">—</div>
                    <button type="button" class="btn btn-xs btn-danger" onclick="this.closest('.recipe-row').remove()">✕</button>
                </div>
            @endif
        </div>
        <div class="mt-1" style="display:flex;gap:0.5rem;">
            <button type="button" class="btn btn-sm btn-outline" id="add-row">+ Tambah Bahan</button>
            <button type="submit" class="btn btn-primary">💾 Simpan Resep</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let idx = {{ ($product->recipe ? $product->recipe->details->count() : 1) }};
const ingredientData = @json($ingredients->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit]));

// Update unit label when ingredient is selected
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('ingredient-select')) {
        const selected = e.target.options[e.target.selectedIndex];
        const unitLabel = e.target.closest('.recipe-row').querySelector('.unit-label');
        unitLabel.textContent = selected.dataset.unit || '—';
    }
});

document.getElementById('add-row').addEventListener('click', function() {
    let optHtml = '<option value="">-- Pilih Bahan --</option>';
    ingredientData.forEach(o => optHtml += `<option value="${o.id}" data-unit="${o.unit}">${o.name}</option>`);
    const row = document.createElement('div');
    row.className = 'form-inline mb-1 recipe-row';
    row.innerHTML = `
        <div class="form-group" style="flex:2;"><select name="ingredients[${idx}][ingredient_id]" class="form-control ingredient-select" required>${optHtml}</select></div>
        <div class="form-group" style="flex:1;"><input type="number" name="ingredients[${idx}][quantity]" class="form-control" step="0.01" min="0.01" placeholder="Jumlah" required></div>
        <div class="form-group unit-label" style="min-width:60px;font-size:0.8rem;color:var(--muted);font-weight:600;">—</div>
        <button type="button" class="btn btn-xs btn-danger" onclick="this.closest('.recipe-row').remove()">✕</button>
    `;
    document.getElementById('recipe-rows').appendChild(row);
    idx++;
});
</script>
@endpush
@endsection
