@extends('layouts.app')
@section('title', 'Laporan Harian')

@push('styles')
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .date-picker {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--card);
        padding: 0.5rem 1rem;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }
    .date-picker input {
        border: none;
        background: transparent;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text);
        cursor: pointer;
    }
    .date-picker input:focus { outline: none; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.5rem;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .stat-card.primary::before { background: linear-gradient(180deg, var(--primary) 0%, #3b82f6 100%); }
    .stat-card.success::before { background: linear-gradient(180deg, #10b981 0%, #34d399 100%); }
    .stat-card.warning::before { background: linear-gradient(180deg, #f59e0b 0%, #fbbf24 100%); }
    .stat-card.purple::before { background: linear-gradient(180deg, #8b5cf6 0%, #a78bfa 100%); }
    .stat-card.danger::before { background: linear-gradient(180deg, #ef4444 0%, #f87171 100%); }
    
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
    .stat-card.primary .stat-icon { background: var(--primary-50); }
    .stat-card.success .stat-icon { background: #d1fae5; }
    .stat-card.warning .stat-icon { background: #fef3c7; }
    .stat-card.purple .stat-icon { background: #ede9fe; }
    .stat-card.danger .stat-icon { background: #fee2e2; }
    
    .stat-label { font-size: 0.85rem; color: var(--muted); margin-bottom: 0.25rem; }
    .stat-value { font-size: 1.35rem; font-weight: 800; color: var(--text); }
    .stat-value.success { color: #10b981; }
    .stat-value.danger { color: #ef4444; }
    
    .breakdown-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .breakdown-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        border: 1px solid var(--border);
    }
    .breakdown-label { font-size: 0.8rem; color: var(--muted); margin-bottom: 0.25rem; }
    .breakdown-value { font-size: 1.05rem; font-weight: 700; color: var(--text); }
    
    .transactions-card {
        background: var(--card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .transactions-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .transactions-header h3 {
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .trans-table {
        width: 100%;
        border-collapse: collapse;
    }
    .trans-table thead th {
        background: var(--bg);
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        text-align: left;
    }
    .trans-table tbody tr {
        transition: background 0.2s ease;
    }
    .trans-table tbody tr:hover { background: var(--primary-50); }
    .trans-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
    }
    .trans-id {
        font-weight: 700;
        color: var(--primary);
    }
    .trans-amount { font-weight: 700; }
</style>
@endpush

@section('content')
<div class="report-header">
    <div>
        <h2 style="margin-bottom: 0.25rem;">📊 Laporan Penjualan Harian</h2>
        <p style="color: var(--muted); font-size: 0.9rem;">Ringkasan penjualan untuk tanggal {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
    </div>
    <form method="GET" class="date-picker">
        <span>📅</span>
        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
    </form>
</div>

<!-- Main Stats -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon">🧾</div>
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ $stats['total_transactions'] }}</div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value success">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon">💵</div>
        <div class="stat-label">Pembayaran Cash</div>
        <div class="stat-value">Rp {{ number_format($stats['cash_revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">📱</div>
        <div class="stat-label">Pembayaran Digital</div>
        <div class="stat-value">Rp {{ number_format($stats['digital_revenue'], 0, ',', '.') }}</div>
    </div>
</div>

<!-- Breakdown -->
<div class="breakdown-grid">
    <div class="breakdown-card">
        <div class="breakdown-label">📦 Subtotal</div>
        <div class="breakdown-value">Rp {{ number_format($stats['total_subtotal'], 0, ',', '.') }}</div>
    </div>
    <div class="breakdown-card">
        <div class="breakdown-label">🏛️ Pajak</div>
        <div class="breakdown-value">Rp {{ number_format($stats['total_tax'], 0, ',', '.') }}</div>
    </div>
    <div class="breakdown-card">
        <div class="breakdown-label">🍽️ Service</div>
        <div class="breakdown-value">Rp {{ number_format($stats['total_service'], 0, ',', '.') }}</div>
    </div>
    <div class="breakdown-card">
        <div class="breakdown-label">🏷️ Diskon</div>
        <div class="breakdown-value" style="color: #ef4444;">- Rp {{ number_format($stats['total_discount'], 0, ',', '.') }}</div>
    </div>
</div>

<!-- Transaction List -->
<div class="transactions-card">
    <div class="transactions-header">
        <h3><span>📝</span> Daftar Transaksi</h3>
        <span class="badge badge-info">{{ $transactions->count() }} transaksi</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="trans-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Tipe</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $t)
            <tr>
                <td class="trans-id">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $t->created_at->format('H:i') }}</td>
                <td>{{ $t->cashier->name ?? '-' }}</td>
                <td>
                    @if($t->order_type === 'dine_in')
                        <span class="badge badge-info">🍽️ Dine In</span>
                    @else
                        <span class="badge badge-warning">🥡 Takeaway</span>
                    @endif
                </td>
                <td class="trans-amount">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                <td>
                    @if($t->payment_method === 'cash')
                        <span>💵 Cash</span>
                    @else
                        <span>📱 Digital</span>
                    @endif
                </td>
                <td>
                    @if($t->payment_status === 'paid')
                        <span class="badge badge-success">✅ Paid</span>
                    @elseif($t->payment_status === 'void')
                        <span class="badge badge-danger">❌ Void</span>
                    @endif
                </td>
                <td>
                    @if($t->payment_status === 'paid')
                        <a href="{{ route('refunds.create', $t) }}" class="btn btn-xs btn-outline" style="color: #ef4444;">↩️ Refund</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">📊</div>
                    <p style="color: var(--muted);">Tidak ada transaksi pada tanggal ini.</p>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
