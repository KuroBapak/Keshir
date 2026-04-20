@extends('layouts.app')
@section('title', $ingredient->name . ' — Bahan Baku')
@section('content')
<div class="page-header">
    <h2>🧪 {{ $ingredient->name }}</h2>
    <div>
        <a href="{{ route('ingredients.edit', $ingredient) }}" class="btn btn-sm btn-outline">✏️ Edit</a>
        <a href="{{ route('ingredients.index') }}" class="btn btn-sm btn-outline">← Kembali</a>
    </div>
</div>

<div class="card">
    <div class="grid-2">
        <div><strong>Total Stok:</strong> {{ number_format($ingredient->total_stock, 2) }} {{ $ingredient->unit }}</div>
        <div><strong>Minimum Stok:</strong> {{ number_format($ingredient->minimum_stock, 2) }} {{ $ingredient->unit }}</div>
        <div><strong>Status:</strong> {!! $ingredient->isBelowMinimum() ? '<span class="badge badge-danger">⚠️ Stok Rendah</span>' : '<span class="badge badge-success">✅ OK</span>' !!}</div>
        @if($ingredient->content_per_pack)
        <div><strong>Isi per Pack:</strong> {{ number_format($ingredient->content_per_pack, 2) }} {{ $ingredient->unit }} / pack</div>
        @endif
    </div>
</div>

{{-- Add Batch --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📦 Tambah Stok Baru (FIFO Batch)</h3>
    <form action="{{ route('ingredients.batches.store', $ingredient) }}" method="POST" class="form-inline" style="flex-wrap:wrap;">
        @csrf
        @if($ingredient->content_per_pack)
        <div class="form-group">
            <label style="font-size:0.75rem;">Input sebagai</label>
            <select name="input_mode" class="form-control" style="width:100px;" onchange="togglePackLabel(this)">
                <option value="base">{{ $ingredient->unit }}</option>
                <option value="pack">Pack</option>
            </select>
        </div>
        @else
        <input type="hidden" name="input_mode" value="base">
        @endif
        <div class="form-group">
            <label style="font-size:0.75rem;" id="stock-label">Jumlah ({{ $ingredient->unit }})</label>
            <input type="number" name="stock" class="form-control" step="0.01" min="0.01" placeholder="Jumlah" required style="width:130px;">
        </div>
        <div class="form-group"><label style="font-size:0.75rem;">Tanggal Kedaluwarsa</label><input type="date" name="expiry_date" class="form-control" required style="width:160px;"></div>
        <div class="form-group"><label style="font-size:0.75rem;">Harga Beli (Rp)</label><input type="number" name="purchase_price" class="form-control" step="100" min="0" placeholder="Harga beli" required style="width:160px;"></div>
        <button type="submit" class="btn btn-sm btn-success" style="align-self:flex-end;">+ Tambah Batch</button>
    </form>
    @if($ingredient->content_per_pack)
    <small style="color:var(--muted);font-size:0.72rem;margin-top:0.35rem;display:block;">💡 Jika input sebagai Pack: {{ number_format($ingredient->content_per_pack, 0) }} {{ $ingredient->unit }} × jumlah pack = total stok yang ditambahkan</small>
    @endif
</div>

@push('scripts')
<script>
function togglePackLabel(select) {
    const label = document.getElementById('stock-label');
    label.textContent = select.value === 'pack' ? 'Jumlah (pack)' : 'Jumlah ({{ $ingredient->unit }})';
}
</script>
@endpush

{{-- Batch List --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📋 Daftar Batch (FIFO — expiry terdekat di atas)</h3>
    <table>
        <thead><tr><th>Sisa Stok</th><th>Exp. Date</th><th>Harga Beli</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($batches as $b)
            <tr>
                <td>{{ number_format($b->stock, 2) }} {{ $ingredient->unit }}</td>
                <td>{{ $b->expiry_date->format('d M Y') }}</td>
                <td>Rp {{ number_format($b->purchase_price, 0, ',', '.') }}</td>
                <td>
                    @php
                        $daysUntilExpiry = now()->startOfDay()->diffInDays($b->expiry_date->startOfDay(), false);
                    @endphp
                    @if($daysUntilExpiry < 0) <span class="badge badge-danger">❌ Expired ({{ abs($daysUntilExpiry) }} hari lalu)</span>
                    @elseif($daysUntilExpiry <= 7) <span class="badge badge-warning">⚠️ Hampir Exp ({{ $daysUntilExpiry }} hari lagi)</span>
                    @else <span class="badge badge-success">✅ OK ({{ $daysUntilExpiry }} hari lagi)</span> @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">Belum ada batch.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $batches->links() }}</div>
</div>
@endsection
