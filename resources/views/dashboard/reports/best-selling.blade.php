@extends('layouts.app')
@section('title', 'Produk Terlaris')
@section('content')
<div class="page-header">
    <h2>🏆 Produk Terlaris</h2>
    <form method="GET" class="form-inline">
        <select name="period" class="form-control" style="width:150px;" onchange="this.form.submit()">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua</option>
        </select>
    </form>
</div>

<div class="card">
    <table>
        <thead><tr><th>#</th><th>Produk</th><th>Terjual</th><th>Pendapatan</th></tr></thead>
        <tbody>
        @forelse($products as $i => $p)
        <tr>
            <td style="font-weight:700;color:var(--primary);">{{ $i + 1 }}</td>
            <td style="font-weight:600;">
                @if($i === 0) 🥇
                @elseif($i === 1) 🥈
                @elseif($i === 2) 🥉
                @endif
                {{ $p->product->name ?? 'Produk dihapus' }}
            </td>
            <td>{{ $p->total_qty }} pcs</td>
            <td style="font-weight:600;color:var(--success);">Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="empty-state">Belum ada data penjualan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
