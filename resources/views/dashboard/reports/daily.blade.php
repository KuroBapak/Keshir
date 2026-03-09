@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('content')
<div class="page-header">
    <h2>📊 Laporan Penjualan Harian</h2>
    <form method="GET" class="form-inline">
        <input type="date" name="date" value="{{ $date }}" class="form-control" style="width:180px;" onchange="this.form.submit()">
    </form>
</div>

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1rem;">
    <div class="card" style="border-left:3px solid var(--primary);">
        <div style="font-size:0.75rem;color:var(--muted);">Total Transaksi</div>
        <div style="font-size:1.3rem;font-weight:700;">{{ $stats['total_transactions'] }}</div>
    </div>
    <div class="card" style="border-left:3px solid var(--success);">
        <div style="font-size:0.75rem;color:var(--muted);">Total Pendapatan</div>
        <div style="font-size:1.1rem;font-weight:700;color:var(--success);">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="card" style="border-left:3px solid var(--warning);">
        <div style="font-size:0.75rem;color:var(--muted);">💵 Cash</div>
        <div style="font-size:1rem;font-weight:700;">Rp {{ number_format($stats['cash_revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="card" style="border-left:3px solid #8b5cf6;">
        <div style="font-size:0.75rem;color:var(--muted);">📱 Digital</div>
        <div style="font-size:1rem;font-weight:700;">Rp {{ number_format($stats['digital_revenue'], 0, ',', '.') }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1rem;">
    <div class="card">
        <div style="font-size:0.75rem;color:var(--muted);">Subtotal</div>
        <div style="font-weight:600;">Rp {{ number_format($stats['total_subtotal'], 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div style="font-size:0.75rem;color:var(--muted);">Pajak</div>
        <div style="font-weight:600;">Rp {{ number_format($stats['total_tax'], 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div style="font-size:0.75rem;color:var(--muted);">Service</div>
        <div style="font-weight:600;">Rp {{ number_format($stats['total_service'], 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div style="font-size:0.75rem;color:var(--muted);">Diskon</div>
        <div style="font-weight:600;color:var(--danger);">- Rp {{ number_format($stats['total_discount'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Transaction List --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📝 Daftar Transaksi</h3>
    <table>
        <thead><tr><th>ID</th><th>Waktu</th><th>Kasir</th><th>Tipe</th><th>Grand Total</th><th>Metode</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($transactions as $t)
        <tr>
            <td>#{{ $t->id }}</td>
            <td>{{ $t->created_at->format('H:i') }}</td>
            <td>{{ $t->cashier->name ?? '-' }}</td>
            <td>{{ $t->order_type === 'dine_in' ? 'Dine In' : 'Takeaway' }}</td>
            <td style="font-weight:600;">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
            <td>{{ $t->payment_method === 'cash' ? '💵 Cash' : '📱 Digital' }}</td>
            <td>
                @if($t->payment_status === 'paid')<span class="badge badge-success">Paid</span>
                @elseif($t->payment_status === 'void')<span class="badge badge-danger">Void</span>
                @endif
            </td>
            <td>
                @if($t->payment_status === 'paid')
                    <a href="{{ route('refunds.create', $t) }}" class="btn btn-xs btn-outline" style="color:var(--danger);">Refund</a>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="empty-state">Tidak ada transaksi pada tanggal ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
