@extends('layouts.app')
@section('title', 'Status & Manajemen Meja')
@section('content')
<div class="page-header">
    <h2>🪑 Status & Manajemen Meja (Dine-In)</h2>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <p style="color:var(--muted);margin-bottom:0;">
        Di halaman ini, Kasir bisa melihat status seluruh meja secara *real-time*. Jika ada tamu Dine-In yang sudah selesai makan dan pulang, silakan klik tombol <strong>Kosongkan Meja</strong> agar meja tersebut bisa digunakan kembali oleh pelanggan lain.
    </p>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:1rem;">
    @foreach($tables as $table)
        @php
            $tx = $activeDineIn->get($table->id);
            $bgColor = '#fff';
            $borderColor = 'var(--border)';
            $badgeStr = '<span class="badge" style="background:var(--success);color:#fff;">Available</span>';
            
            if ($table->status === 'occupied') {
                $borderColor = 'var(--danger)';
                $bgColor = '#fef2f2'; // light red
                $badgeStr = '<span class="badge" style="background:var(--danger);color:#fff;">Occupied (Berisi)</span>';
            } else if ($table->status === 'booked') {
                $borderColor = 'var(--warning)';
                $bgColor = '#fffbeb'; // light yellow
                $badgeStr = '<span class="badge" style="background:var(--warning);color:#fff;">Booked (Dipesan)</span>';
            }
        @endphp

        <div class="card" style="border:2px solid {{ $borderColor }};background:{{ $bgColor }};display:flex;flex-direction:column;justify-content:space-between;min-height:160px;">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.8rem;">
                    <div style="font-size:1.5rem;font-weight:800;color:var(--text);">Meja {{ $table->table_number }}</div>
                    {!! $badgeStr !!}
                </div>
                
                <div style="font-size:0.85rem;color:var(--muted);margin-bottom:0.5rem;">👥 Kapasitas: {{ $table->capacity }} kursi</div>

                @if($table->status === 'occupied' && $tx)
                    <div style="font-size:0.85rem;color:var(--text);margin-top:0.5rem;">
                        <strong>Bill:</strong> #{{ str_pad($tx->bill_number ?? $tx->id, 5, '0', STR_PAD_LEFT) }}<br>
                        <strong>Atas Nama:</strong> {{ $tx->customer_name ?? '-' }}<br>
                        <strong>Status Bayar:</strong> {{ $tx->payment_status === 'paid' ? '✅ Lunas' : '⏳ Belum Lunas' }}
                    </div>
                @elseif($table->status === 'booked')
                    <div style="font-size:0.8rem;color:var(--warning);margin-top:0.5rem;">
                        Reserved / Menunggu tamu booking.
                    </div>
                @endif
            </div>

            <div style="margin-top:1rem;text-align:right;">
                @if($table->status === 'occupied')
                    <form action="{{ route('pos.clearTable', $table) }}" method="POST" onsubmit="return confirm('Kosongkan Meja {{ $table->table_number }}? Pastikan pelanggan benar-benar sudah pulang.')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline" style="color:var(--danger);border-color:var(--danger);">🧹 Kosongkan Meja</button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
