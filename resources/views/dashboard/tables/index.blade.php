@extends('layouts.app')
@section('title', 'Meja — Keshir')

@push('styles')
<style>
    .table-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .table-stat {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        text-align: center;
    }
    .table-stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
    }
    .table-stat-label {
        font-size: 0.85rem;
        color: var(--muted);
        margin-top: 0.25rem;
    }
    .table-stat.available .table-stat-value { color: #10b981; }
    .table-stat.occupied .table-stat-value { color: #ef4444; }
    .table-stat.booked .table-stat-value { color: #f59e0b; }
    
    .add-table-form {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }
    .add-table-form h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .add-table-form .form-row {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .add-table-form .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 0.35rem;
    }
    
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .table-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 2px solid var(--border);
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .table-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .table-card.available { border-color: #10b981; }
    .table-card.occupied { border-color: #ef4444; }
    .table-card.booked { border-color: #f59e0b; }
    
    .table-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .table-card.available::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .table-card.occupied::before { background: linear-gradient(90deg, #ef4444, #f87171); }
    .table-card.booked::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    
    .table-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 0.5rem;
    }
    .table-capacity {
        font-size: 0.9rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-bottom: 0.75rem;
    }
    .table-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .table-status.available { background: #d1fae5; color: #065f46; }
    .table-status.occupied { background: #fee2e2; color: #991b1b; }
    .table-status.booked { background: #fef3c7; color: #92400e; }
    
    .table-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>🪑 Manajemen Meja</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Kelola meja dan kapasitas restoran</p>
    </div>
</div>

<!-- Stats -->
<div class="table-stats">
    <div class="table-stat">
        <div class="table-stat-value">{{ $tables->count() }}</div>
        <div class="table-stat-label">Total Meja</div>
    </div>
    <div class="table-stat available">
        <div class="table-stat-value">{{ $tables->where('status', 'available')->count() }}</div>
        <div class="table-stat-label">Tersedia</div>
    </div>
    <div class="table-stat occupied">
        <div class="table-stat-value">{{ $tables->where('status', 'occupied')->count() }}</div>
        <div class="table-stat-label">Terpakai</div>
    </div>
    <div class="table-stat booked">
        <div class="table-stat-value">{{ $tables->where('status', 'booked')->count() }}</div>
        <div class="table-stat-label">Booking</div>
    </div>
</div>

<!-- Add Table Form -->
<div class="add-table-form">
    <h3><span>➕</span> Tambah Meja Baru</h3>
    <form action="{{ route('tables.store') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label>Nomor Meja</label>
                <input type="text" name="table_number" class="form-control" placeholder="Cth: T11 atau Meja 11" required>
            </div>
            <div class="form-group" style="width: 150px;">
                <label>Kapasitas</label>
                <input type="number" name="capacity" class="form-control" placeholder="4" min="1" max="20" required>
            </div>
            <button type="submit" class="btn btn-primary">+ Tambah Meja</button>
        </div>
    </form>
</div>

<!-- Tables Grid -->
<div class="tables-grid">
    @forelse($tables as $t)
    <div class="table-card {{ $t->status }}">
        <div class="table-number">{{ $t->table_number }}</div>
        <div class="table-capacity">
            <span>👥</span>
            <span>{{ $t->capacity }} kursi</span>
        </div>
        <div class="table-status {{ $t->status }}">
            @if($t->status === 'available')
                ✅ Tersedia
            @elseif($t->status === 'occupied')
                🔴 Terpakai
            @else
                📅 Booking
            @endif
        </div>
        <div class="table-actions">
            <form action="{{ route('tables.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus meja {{ $t->table_number }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline" style="width: 100%; color: #ef4444; border-color: #fecaca;">
                    🗑️ Hapus Meja
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: var(--card); border-radius: var(--radius); border: 1px solid var(--border);">
        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🪑</div>
        <p style="color: var(--muted);">Belum ada meja. Tambahkan meja pertama Anda di atas!</p>
    </div>
    @endforelse
</div>

@if($tables->hasPages())
<div style="margin-top: 1.5rem;">
    {{ $tables->links() }}
</div>
@endif
@endsection
