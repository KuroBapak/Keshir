@extends('layouts.app')
@section('title', 'Log Refund')

@push('styles')
<style>
    .refund-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .refund-stat {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .refund-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .refund-stat-icon.danger { background: #fee2e2; }
    .refund-stat-icon.warning { background: #fef3c7; }
    .refund-stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text); }
    .refund-stat-label { font-size: 0.85rem; color: var(--muted); }
    
    .refund-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .refund-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .refund-header h3 {
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .refund-table {
        width: 100%;
        border-collapse: collapse;
    }
    .refund-table thead th {
        background: var(--bg);
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        text-align: left;
    }
    .refund-table tbody tr:hover { background: var(--primary-50); }
    .refund-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    
    .refund-id { 
        font-weight: 700;
        color: var(--primary);
    }
    .refund-amount {
        font-weight: 800;
        color: #ef4444;
        font-size: 1rem;
    }
    .refund-reason {
        background: #fef3c7;
        padding: 0.35rem 0.65rem;
        border-radius: 0.35rem;
        font-size: 0.85rem;
        color: #92400e;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>🔄 Log Refund</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Riwayat pengembalian dana transaksi</p>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">❌ {{ session('error') }}</div>@endif

<!-- Stats -->
<div class="refund-stats">
    <div class="refund-stat">
        <div class="refund-stat-icon danger">🔄</div>
        <div>
            <div class="refund-stat-value">{{ $refunds->total() }}</div>
            <div class="refund-stat-label">Total Refund</div>
        </div>
    </div>
    <div class="refund-stat">
        <div class="refund-stat-icon warning">💸</div>
        <div>
            <div class="refund-stat-value" style="color: #ef4444;">Rp {{ number_format($refunds->sum('amount'), 0, ',', '.') }}</div>
            <div class="refund-stat-label">Total Nilai Refund</div>
        </div>
    </div>
</div>

<div class="refund-card">
    <div class="refund-header">
        <h3><span>📋</span> Daftar Refund</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="refund-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Transaksi</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                    <th>Otorisasi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
            @forelse($refunds as $r)
            <tr>
                <td class="refund-id">#{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <span class="badge badge-info">Bill #{{ $r->transaction_id }}</span>
                </td>
                <td class="refund-amount">- Rp {{ number_format($r->amount, 0, ',', '.') }}</td>
                <td>
                    <span class="refund-reason">{{ $r->reason }}</span>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-50); display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                            {{ strtoupper(substr($r->authorizedBy->name ?? '-', 0, 1)) }}
                        </div>
                        <span style="font-weight: 600;">{{ $r->authorizedBy->name ?? '-' }}</span>
                    </div>
                </td>
                <td>
                    <div style="font-weight: 600;">{{ $r->created_at->format('d M Y') }}</div>
                    <div style="font-size: 0.8rem; color: var(--muted);">{{ $r->created_at->format('H:i') }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">🔄</div>
                    <p style="color: var(--muted);">Belum ada refund. Bagus! 🎉</p>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($refunds->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--border);">
        {{ $refunds->links() }}
    </div>
    @endif
</div>
@endsection
