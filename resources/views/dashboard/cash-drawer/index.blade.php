@extends('layouts.app')
@section('title', 'Kas Laci — Shift')

@push('styles')
<style>
    .shift-active {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 2px solid #10b981;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .shift-active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
    }
    .shift-active-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .shift-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: #10b981;
        margin-bottom: 0.5rem;
    }
    .shift-status .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #10b981;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .shift-meta { font-size: 0.9rem; color: var(--muted); }
    .shift-meta strong { color: var(--text); }
    .shift-actions { display: flex; gap: 0.5rem; }
    
    .shift-open-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .shift-open-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--primary-50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem;
    }
    .shift-open-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
    }
    .shift-open-desc {
        font-size: 0.9rem;
        color: var(--muted);
        margin-bottom: 1.5rem;
    }
    .shift-open-form {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .shift-open-form .form-group {
        margin: 0;
    }
    
    .history-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .history-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .history-header h3 {
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .history-table {
        width: 100%;
        border-collapse: collapse;
    }
    .history-table thead th {
        background: var(--bg);
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        text-align: left;
    }
    .history-table tbody tr:hover { background: var(--primary-50); }
    .history-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
    }
    
    .diff-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.3rem 0.65rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .diff-badge.match { background: #d1fae5; color: #065f46; }
    .diff-badge.plus { background: #d1fae5; color: #065f46; }
    .diff-badge.minus { background: #fee2e2; color: #991b1b; }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 200;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal {
        background: var(--card);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
        width: 90%;
        max-width: 400px;
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>💰 Kas Laci (Cash Drawer)</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Kelola shift kasir dan rekonsiliasi kas</p>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">❌ {{ session('error') }}</div>@endif

<!-- Active Shift -->
@if($activeDrawer)
<div class="shift-active">
    <div class="shift-active-header">
        <div>
            <div class="shift-status">
                <span class="dot"></span>
                Shift Aktif
            </div>
            <div class="shift-meta">
                Dibuka: <strong>{{ $activeDrawer->opened_at->format('d M Y H:i') }}</strong>
                &nbsp;•&nbsp;
                Modal: <strong>Rp {{ number_format($activeDrawer->starting_cash, 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="shift-actions">
            <a href="{{ route('cash-drawer.show', $activeDrawer) }}" class="btn btn-primary">📋 Detail Shift</a>
            <button onclick="document.getElementById('close-modal').style.display='flex'" class="btn btn-danger">🔒 Tutup Shift</button>
        </div>
    </div>
</div>

<!-- Close Shift Modal -->
<div id="close-modal" class="modal-overlay" onclick="if(event.target===this) this.style.display='none'">
    <div class="modal">
        <h3><span>🔒</span> Tutup Shift</h3>
        <form action="{{ route('cash-drawer.close', $activeDrawer) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Total Uang Fisik di Laci (Rp)</label>
                <input type="number" name="ending_cash" class="form-control" step="1" min="0" required placeholder="Hitung uang fisik di laci...">
                <small style="color: var(--muted); font-size: 0.8rem; display: block; margin-top: 0.35rem;">
                    Hitung semua uang tunai yang ada di laci kas
                </small>
            </div>
            <div style="display:flex; gap:0.75rem; margin-top: 1.25rem;">
                <button type="submit" class="btn btn-danger" style="flex:1;">Tutup Shift</button>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('close-modal').style.display='none'">Batal</button>
            </div>
        </form>
    </div>
</div>
@else
<div class="shift-open-card">
    <div class="shift-open-icon">📦</div>
    <div class="shift-open-title">Buka Shift Baru</div>
    <div class="shift-open-desc">Masukkan modal awal untuk memulai shift kasir hari ini</div>
    <form action="{{ route('cash-drawer.open') }}" method="POST" class="shift-open-form">
        @csrf
        <div class="form-group">
            <input type="number" name="starting_cash" class="form-control" step="1" min="0" required placeholder="Modal awal (Rp)" style="width: 200px;">
        </div>
        <button type="submit" class="btn btn-success">🔓 Buka Shift</button>
    </form>
</div>
@endif

<!-- History -->
<div class="history-card">
    <div class="history-header">
        <h3><span>📜</span> Riwayat Shift</h3>
        <span class="badge badge-info">{{ $history->total() }} shift</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Modal Awal</th>
                    <th>Kas Fisik</th>
                    <th>Ekspektasi</th>
                    <th>Selisih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($history as $h)
            @php $diff = $h->ending_cash - $h->expected_ending_cash; @endphp
            <tr>
                <td>
                    <div style="font-weight: 600;">{{ $h->opened_at->format('d M Y') }}</div>
                    <div style="font-size: 0.8rem; color: var(--muted);">{{ $h->opened_at->format('H:i') }} - {{ $h->closed_at ? $h->closed_at->format('H:i') : '-' }}</div>
                </td>
                <td>Rp {{ number_format($h->starting_cash, 0, ',', '.') }}</td>
                <td style="font-weight: 600;">Rp {{ number_format($h->ending_cash, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($h->expected_ending_cash, 0, ',', '.') }}</td>
                <td>
                    @if($diff == 0)
                        <span class="diff-badge match">✅ Sesuai</span>
                    @elseif($diff > 0)
                        <span class="diff-badge plus">+Rp {{ number_format($diff, 0, ',', '.') }}</span>
                    @else
                        <span class="diff-badge minus">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                    @endif
                </td>
                <td><span class="badge badge-success">✓ Closed</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">📜</div>
                    <p style="color: var(--muted);">Belum ada riwayat shift.</p>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($history->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--border);">
        {{ $history->links() }}
    </div>
    @endif
</div>
@endsection
