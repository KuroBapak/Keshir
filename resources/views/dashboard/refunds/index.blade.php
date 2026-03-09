@extends('layouts.app')
@section('title', 'Log Refund')
@section('content')
<div class="page-header"><h2>🔄 Log Refund</h2></div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Transaksi</th><th>Jumlah</th><th>Alasan</th><th>Oleh</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($refunds as $r)
        <tr>
            <td>#{{ $r->id }}</td>
            <td>Bill #{{ $r->transaction_id }}</td>
            <td style="font-weight:600;color:var(--danger);">Rp {{ number_format($r->amount, 0, ',', '.') }}</td>
            <td>{{ $r->reason }}</td>
            <td>{{ $r->authorizedBy->name ?? '-' }}</td>
            <td>{{ $r->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state">Belum ada refund.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $refunds->links() }}</div>
</div>
@endsection
