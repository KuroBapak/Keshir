@extends('layouts.app')
@section('title', 'Refund Transaksi')
@section('content')
<div class="page-header">
    <h2>🔄 Refund — Bill #{{ $transaction->id }}</h2>
    <a href="{{ route('refunds.index') }}" class="btn btn-sm btn-outline">← Kembali</a>
</div>

@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

<div class="card" style="max-width:500px;">
    <div style="margin-bottom:1rem;">
        <div style="font-size:0.82rem;color:var(--muted);">Total Transaksi</div>
        <div style="font-size:1.2rem;font-weight:700;color:var(--primary);">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</div>
        <div style="font-size:0.75rem;color:var(--muted);">Metode: {{ $transaction->payment_method === 'cash' ? 'Cash' : 'Digital' }} · {{ $transaction->created_at->format('d M Y H:i') }}</div>
    </div>

    <form action="{{ route('refunds.store', $transaction) }}" method="POST" onsubmit="return confirm('Yakin refund? Stok bahan akan dikembalikan dan transaksi di-void.')">
        @csrf
        <div class="form-group">
            <label>Jumlah Refund (Rp)</label>
            <input type="number" name="amount" class="form-control" step="1" min="1" max="{{ $transaction->grand_total }}" value="{{ $transaction->grand_total }}" required>
        </div>
        <div class="form-group">
            <label>Alasan Refund</label>
            <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Makanan tidak bisa disajikan, bahan habis"></textarea>
        </div>
        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">🔄 Proses Refund</button>
    </form>
</div>
@endsection
