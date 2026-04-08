@extends('layouts.app')
@section('title', 'Diskon — Keshir')

@push('styles')
<style>
    .discount-form {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }
    .discount-form h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .discount-form .form-row {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .discount-form .form-group {
        flex: 1;
        min-width: 150px;
    }
    .discount-form .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 0.35rem;
    }
    
    .discount-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
    .discount-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .discount-card:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.1);
    }
    .discount-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary) 0%, #3b82f6 100%);
    }
    .discount-card.inactive::before { background: #cbd5e1; }
    
    .discount-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .discount-name { font-size: 1.1rem; font-weight: 700; color: var(--text); }
    .discount-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
        margin: 0.75rem 0;
    }
    .discount-type {
        font-size: 0.8rem;
        color: var(--muted);
    }
    .discount-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>🏷️ Manajemen Diskon</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Buat dan kelola promo diskon untuk pelanggan</p>
    </div>
</div>

<!-- Add Discount Form -->
<div class="discount-form">
    <h3><span>➕</span> Tambah Diskon Baru</h3>
    <form action="{{ route('discounts.store') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label>Nama Diskon</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Promo Akhir Tahun" required>
            </div>
            <div class="form-group">
                <label>Tipe Diskon</label>
                <select name="type" class="form-control" required>
                    <option value="percentage">📊 Persentase (%)</option>
                    <option value="nominal">💵 Nominal (Rp)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nilai</label>
                <input type="number" name="value" class="form-control" step="0.01" min="0" placeholder="10" required>
            </div>
            <input type="hidden" name="is_active" value="1">
            <button type="submit" class="btn btn-primary" style="height: fit-content;">+ Tambah Diskon</button>
        </div>
    </form>
</div>

<!-- Discount List -->
<div class="discount-grid">
    @forelse($discounts as $d)
    <div class="discount-card {{ !$d->is_active ? 'inactive' : '' }}">
        <div class="discount-header">
            <div class="discount-name">{{ $d->name }}</div>
            @if($d->is_active)
                <span class="badge badge-success">✅ Aktif</span>
            @else
                <span class="badge badge-danger">❌ Nonaktif</span>
            @endif
        </div>
        <div class="discount-value">
            @if($d->type === 'percentage')
                {{ $d->value }}%
            @else
                Rp {{ number_format($d->value, 0, ',', '.') }}
            @endif
        </div>
        <div class="discount-type">
            @if($d->type === 'percentage')
                📊 Potongan persentase dari total
            @else
                💵 Potongan nominal langsung
            @endif
        </div>
        <div class="discount-actions">
            <form action="{{ route('discounts.destroy', $d) }}" method="POST" onsubmit="return confirm('Hapus diskon {{ $d->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline" style="width: 100%; color: #ef4444; border-color: #fecaca;">
                    🗑️ Hapus Diskon
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: var(--card); border-radius: var(--radius); border: 1px solid var(--border);">
        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🏷️</div>
        <p style="color: var(--muted);">Belum ada diskon. Buat diskon pertama Anda di atas!</p>
    </div>
    @endforelse
</div>

@if($discounts->hasPages())
<div style="margin-top: 1.5rem;">
    {{ $discounts->links() }}
</div>
@endif
@endsection
