@extends('layouts.app')
@section('title', 'Manajemen Reservasi / Booking')
@section('content')
<div class="page-header">
    <h2>📅 Manajemen Reservasi / Booking</h2>
</div>

<div class="card">
    @if($bookings->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Waktu Kedatangan</th>
                <th>Pemesan</th>
                <th>Meja & Detail</th>
                <th>Menu yang Dipesan</th>
                <th>Status Pembayaran</th>
                <th>Status Booking</th>
                <th style="text-align:right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            @php 
                $tx = $booking->transaction; 
            @endphp
            <tr>
                <td>
                    <div style="font-weight:700;">{{ $booking->booking_time->format('d M Y') }}</div>
                    <div style="font-size:1.1rem;color:var(--primary);font-weight:800;">{{ $booking->booking_time->format('H:i') }}</div>
                </td>
                <td>
                    <div style="font-weight:600;">{{ $tx->customer_name }}</div>
                    <div style="font-size:0.85rem;color:var(--muted);">📱 {{ $tx->phone }}</div>
                </td>
                <td>
                    <div style="font-weight:700;color:var(--text);">Meja {{ $tx->table->table_number ?? '?' }}</div>
                    <div style="font-size:0.85rem;color:var(--muted);">👥 {{ $tx->people_count }} Orang</div>
                </td>
                <td>
                    <ul style="margin:0;padding-left:1.2rem;font-size:0.85rem;">
                        @foreach($tx->details as $d)
                            <li>{{ $d->qty }}x {{ $d->product->name }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    @if($tx->payment_status === 'paid')
                        <span class="badge badge-success">✅ Lunas</span>
                    @else
                        <span class="badge badge-warning">⏳ Belum Lunas</span>
                    @endif
                    <div style="font-size:0.8rem;margin-top:0.3rem;">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</div>
                </td>
                <td>
                    @if($booking->status === 'pending')
                        <span class="badge badge-warning">⏳ Menunggu Konfirmasi</span>
                    @elseif($booking->status === 'approved')
                        <span class="badge badge-primary">✅ Dikonfirmasi (Meja Disimpan)</span>
                    @else
                        <span class="badge badge-danger">❌ Ditolak</span>
                    @endif
                </td>
                <td style="text-align:right;">
                    @if($booking->status === 'pending')
                        <form action="{{ route('pos.updateBookingStatus', $booking) }}" method="POST" style="display:inline-block;margin-bottom:0.3rem;">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm" style="background:var(--primary);color:#fff;">Terima</button>
                        </form>
                        <form action="{{ route('pos.updateBookingStatus', $booking) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Tolak booking ini? Meja akan dilepas.')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-outline" style="color:var(--danger);border-color:var(--danger);">Tolak</button>
                        </form>
                    @elseif($booking->status === 'approved')
                        <form action="{{ route('pos.updateBookingStatus', $booking) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Batalkan booking ini? Meja akan dilepas.')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-outline" style="color:var(--danger);border-color:var(--danger);">Batalkan</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="empty-state">Belum ada data reservasi.</div>
    @endif
</div>
@endsection
