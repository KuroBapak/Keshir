@extends('layouts.app')
@section('title', 'Detail Shift')
@section('content')
<div class="page-header">
    <h2>📋 Detail Shift — {{ $cashDrawer->opened_at->format('d M Y H:i') }}</h2>
    <a href="{{ route('cash-drawer.index') }}" class="btn btn-sm btn-outline">← Kembali</a>
</div>

<div class="grid-2">
    <div class="card">
        <div style="font-size:0.82rem;color:var(--muted);">Modal Awal</div>
        <div style="font-size:1.2rem;font-weight:700;">Rp {{ number_format($cashDrawer->starting_cash, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div style="font-size:0.82rem;color:var(--muted);">Status</div>
        <div style="font-size:1.2rem;font-weight:700;color:{{ $cashDrawer->status === 'open' ? 'var(--success)' : 'var(--muted)' }};">
            {{ $cashDrawer->status === 'open' ? '🟢 Aktif' : '🔒 Ditutup' }}
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card" style="border-left:3px solid var(--success);">
        <div style="font-size:0.82rem;color:var(--muted);">Total Cash IN</div>
        <div style="font-size:1.1rem;font-weight:700;color:var(--success);">+ Rp {{ number_format($totalIn, 0, ',', '.') }}</div>
    </div>
    <div class="card" style="border-left:3px solid var(--danger);">
        <div style="font-size:0.82rem;color:var(--muted);">Total Cash OUT</div>
        <div style="font-size:1.1rem;font-weight:700;color:var(--danger);">- Rp {{ number_format($totalOut, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card" style="border-left:3px solid var(--primary);">
    <div style="font-size:0.82rem;color:var(--muted);">Ekspektasi Kas Saat Ini</div>
    <div style="font-size:1.3rem;font-weight:700;color:var(--primary);">Rp {{ number_format($expectedCash, 0, ',', '.') }}</div>
    <div style="font-size:0.75rem;color:var(--muted);">Modal Awal + Cash IN - Cash OUT</div>
</div>

{{-- Transaction Logs --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📝 Log Transaksi Kas</h3>
    <table>
        <thead><tr><th>Waktu</th><th>Tipe</th><th>Jumlah</th><th>Keterangan</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
        <tr>
            <td>{{ $log->created_at->format('H:i:s') }}</td>
            <td>
                @if($log->type === 'in')
                    <span class="badge badge-success">💵 Cash IN</span>
                @else
                    <span class="badge badge-danger">💸 Cash OUT</span>
                @endif
            </td>
            <td style="font-weight:600;color:{{ $log->type === 'in' ? 'var(--success)' : 'var(--danger)' }};">
                {{ $log->type === 'in' ? '+' : '-' }} Rp {{ number_format($log->amount, 0, ',', '.') }}
            </td>
            <td>{{ $log->description ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="empty-state">Belum ada transaksi.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
