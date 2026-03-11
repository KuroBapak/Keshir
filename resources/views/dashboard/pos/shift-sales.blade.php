@extends('layouts.app')
@section('title', 'Detail Penjualan Shift')
@section('content')
<div class="page-header">
    <h2>📊 Detail Penjualan Shift Saat Ini</h2>
</div>

@if(!$activeDrawer)
<div class="card" style="text-align:center;padding:3rem 1rem;">
    <h3 style="color:var(--danger);margin-bottom:0.5rem;">Shift Belum Dibuka</h3>
    <p style="color:var(--muted);margin-bottom:1rem;">Anda harus membuka Kas Laci (Shift) terlebih dahulu untuk mulai mencatat penjualan.</p>
    <a href="{{ route('cash-drawer.index') }}" class="btn" style="background:var(--primary);color:#fff;">Buka Shift Kasir</a>
</div>
@else
<div class="grid-2" style="margin-bottom:1.5rem;">
    <div class="card" style="border-left:3px solid var(--success);">
        <div style="font-size:0.85rem;color:var(--muted);">Total Transaksi</div>
        <div style="font-size:1.5rem;font-weight:800;">{{ $transactions->count() }} <span style="font-size:1rem;color:var(--muted);font-weight:400;">Struk</span></div>
    </div>
    <div class="card" style="border-left:3px solid var(--primary);">
        <div style="font-size:0.85rem;color:var(--muted);">Total Pemasukan Shift Ini</div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--primary);">Rp {{ number_format($transactions->where('payment_status', 'paid')->sum('grand_total'), 0, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom:1rem;font-size:1.1rem;">Daftar Transaksi: Shift Mulai {{ $activeDrawer->opened_at->format('d/m/Y H:i') }}</h3>
    @if($transactions->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Order ID</th>
                <th>Tipe Pesanan / Meja</th>
                <th>Pelanggan</th>
                <th>Status Pembayaran</th>
                <th style="text-align:right;">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->created_at->format('H:i') }}</td>
                <td style="font-weight:700;">#{{ str_pad($tx->bill_number ?? $tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>
                    @if($tx->order_type === 'dine_in')
                        <span class="badge badge-primary">Dine In (Meja {{ $tx->table->table_number ?? '-' }})</span>
                    @elseif($tx->order_type === 'takeaway')
                        <span class="badge badge-warning">Takeaway</span>
                    @else
                        <span class="badge badge-success">Booking (Meja {{ $tx->table->table_number ?? '-' }})</span>
                    @endif
                </td>
                <td>{{ $tx->customer_name ?: '-' }}</td>
                <td>
                    @if($tx->payment_status === 'paid')
                        <span class="badge badge-success">{{ ucfirst($tx->payment_method ?? 'Lunas') }}</span>
                    @elseif($tx->payment_status === 'open')
                        <span class="badge badge-warning">Belum Lunas</span>
                    @elseif($tx->payment_status === 'void')
                        <span class="badge badge-danger">Dibatalkan (Void)</span>
                    @elseif($tx->payment_status === 'refunded')
                        <span class="badge badge-danger">Refund</span>
                    @else
                        <span class="badge" style="background:var(--muted);color:#fff;">{{ ucfirst($tx->payment_status) }}</span>
                    @endif
                </td>
                <td style="text-align:right;font-weight:700;color:{{ $tx->payment_status === 'void' || $tx->payment_status === 'refunded' ? 'var(--danger);text-decoration:line-through;' : 'var(--text)' }};">
                    Rp {{ number_format($tx->grand_total, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="empty-state">Belum ada penjualan pada shift ini.</div>
    @endif
</div>
@endif
@endsection
