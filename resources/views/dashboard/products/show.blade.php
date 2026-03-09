@extends('layouts.app')
@section('title', $product->name . ' — Keshir')
@section('content')
<div class="page-header">
    <h2>{{ $product->name }}</h2>
    <div>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline">✏️ Edit</a>
        <a href="{{ route('products.recipe.edit', $product) }}" class="btn btn-sm btn-primary">📋 Resep</a>
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
</div>

{{-- Info --}}
<div class="card">
    <div class="grid-2">
        <div><strong>Kategori:</strong> {{ $product->category->name }}</div>
        <div><strong>Harga Dasar:</strong> Rp {{ number_format($product->base_price, 0, ',', '.') }}</div>
        <div><strong>Status:</strong> {!! $product->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' !!}</div>
        <div><strong>Tags:</strong> {{ $product->tags ?: '-' }}</div>
    </div>
    @if($product->description)<p class="mt-1 text-muted" style="font-size:0.85rem;">{{ $product->description }}</p>@endif
</div>

{{-- Variants --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">🔀 Varian</h3>
    <form action="{{ route('products.variants.store', $product) }}" method="POST" class="form-inline mb-1">
        @csrf
        <div class="form-group"><input type="text" name="variant_name" class="form-control" placeholder="Nama varian (cth: Large)" required></div>
        <div class="form-group"><input type="number" name="additional_price" class="form-control" placeholder="Tambahan harga" step="100" min="0" required style="width:150px;"></div>
        <button type="submit" class="btn btn-sm btn-success">+ Tambah</button>
    </form>
    @if($product->variants->count())
    <table>
        <thead><tr><th>Nama Varian</th><th>Tambahan Harga</th><th></th></tr></thead>
        <tbody>
            @foreach($product->variants as $v)
            <tr>
                <td>{{ $v->variant_name }}</td>
                <td>+ Rp {{ number_format($v->additional_price, 0, ',', '.') }}</td>
                <td class="text-right">
                    <form action="{{ route('products.variants.destroy', [$product, $v]) }}" method="POST" onsubmit="return confirm('Hapus varian?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p class="empty-state">Belum ada varian.</p> @endif
</div>

{{-- Add-ons --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">➕ Add-ons</h3>
    <form action="{{ route('products.addons.store', $product) }}" method="POST" class="form-inline mb-1">
        @csrf
        <div class="form-group"><input type="text" name="addon_name" class="form-control" placeholder="Nama add-on (cth: Extra Shot)" required></div>
        <div class="form-group"><input type="number" name="price" class="form-control" placeholder="Harga add-on" step="100" min="0" required style="width:150px;"></div>
        <button type="submit" class="btn btn-sm btn-success">+ Tambah</button>
    </form>
    @if($product->addons->count())
    <table>
        <thead><tr><th>Nama Add-on</th><th>Harga</th><th></th></tr></thead>
        <tbody>
            @foreach($product->addons as $a)
            <tr>
                <td>{{ $a->addon_name }}</td>
                <td>+ Rp {{ number_format($a->price, 0, ',', '.') }}</td>
                <td class="text-right">
                    <form action="{{ route('products.addons.destroy', [$product, $a]) }}" method="POST" onsubmit="return confirm('Hapus add-on?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p class="empty-state">Belum ada add-on.</p> @endif
</div>

{{-- Recipe --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📋 Resep (Bill of Materials)</h3>
    @if($product->recipe && $product->recipe->details->count())
    <table>
        <thead><tr><th>Bahan</th><th>Jumlah</th><th>Satuan</th></tr></thead>
        <tbody>
            @foreach($product->recipe->details as $d)
            <tr>
                <td>{{ $d->ingredient->name }}</td>
                <td>{{ $d->quantity }}</td>
                <td>{{ $d->ingredient->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-state">Belum ada resep. <a href="{{ route('products.recipe.edit', $product) }}">Buat resep →</a></p>
    @endif
</div>
@endsection
