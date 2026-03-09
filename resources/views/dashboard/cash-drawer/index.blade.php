@extends('layouts.app')
@section('title', 'Kas Laci — Shift')
@section('content')
<div class="page-header"><h2>💰 Kas Laci (Cash Drawer)</h2></div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

{{-- Active Shift --}}
@if($activeDrawer)
<div class="card" style="border-left:4px solid var(--success);">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h3 style="font-size:1rem;color:var(--success);margin-bottom:0.3rem;">🟢 Shift Aktif</h3>
            <div style="font-size:0.82rem;color:var(--muted);">Dibuka: {{ $activeDrawer->opened_at->format('d M Y H:i') }}</div>
            <div style="font-size:0.82rem;">Modal Awal: <strong>Rp {{ number_format($activeDrawer->starting_cash, 0, ',', '.') }}</strong></div>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <a href="{{ route('cash-drawer.show', $activeDrawer) }}" class="btn btn-sm btn-primary">📋 Detail</a>
            <button onclick="document.getElementById('close-modal').style.display='flex'" class="btn btn-sm btn-danger">🔒 Tutup Shift</button>
        </div>
    </div>
</div>

{{-- Close Shift Modal --}}
<div id="close-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center;">
    <div class="card" style="max-width:400px;width:90%;">
        <h3 style="font-size:1rem;margin-bottom:1rem;">🔒 Tutup Shift</h3>
        <form action="{{ route('cash-drawer.close', $activeDrawer) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Total Uang Fisik di Laci (Rp)</label>
                <input type="number" name="ending_cash" class="form-control" step="100" min="0" required placeholder="Hitung uang fisik...">
            </div>
            <div style="display:flex;gap:0.5rem;">
                <button type="submit" class="btn btn-danger" style="flex:1;justify-content:center;">Tutup Shift</button>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('close-modal').style.display='none'">Batal</button>
            </div>
        </form>
    </div>
</div>
@else
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📦 Buka Shift Baru</h3>
    <form action="{{ route('cash-drawer.open') }}" method="POST" class="form-inline">
        @csrf
        <div class="form-group">
            <label style="font-size:0.8rem;">Modal Awal (Rp)</label>
            <input type="number" name="starting_cash" class="form-control" step="100" min="0" required placeholder="Cth: 500000" style="width:200px;">
        </div>
        <button type="submit" class="btn btn-sm btn-success">🔓 Buka Shift</button>
    </form>
</div>
@endif

{{-- History --}}
<div class="card">
    <h3 style="font-size:1rem;margin-bottom:0.75rem;">📜 Riwayat Shift</h3>
    <table>
        <thead><tr><th>Tanggal</th><th>Modal Awal</th><th>Kas Fisik</th><th>Ekspektasi</th><th>Selisih</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($history as $h)
        @php $diff = $h->ending_cash - $h->expected_ending_cash; @endphp
        <tr>
            <td>{{ $h->opened_at->format('d M Y H:i') }}</td>
            <td>Rp {{ number_format($h->starting_cash, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($h->ending_cash, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($h->expected_ending_cash, 0, ',', '.') }}</td>
            <td style="color:{{ $diff == 0 ? 'var(--success)' : ($diff > 0 ? 'var(--success)' : 'var(--danger)') }};font-weight:600;">
                {{ $diff == 0 ? '✅ Sesuai' : ($diff > 0 ? '+Rp ' . number_format($diff, 0, ',', '.') : '-Rp ' . number_format(abs($diff), 0, ',', '.')) }}
            </td>
            <td><span class="badge badge-success">Closed</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state">Belum ada riwayat shift.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $history->links() }}</div>
</div>
@endsection
