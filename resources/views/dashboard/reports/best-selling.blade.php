@extends('layouts.app')
@section('title', 'Produk Terlaris')

@push('styles')
<style>
    .period-tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--card);
        padding: 0.5rem;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }
    .period-tab {
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--muted);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .period-tab:hover { color: var(--primary); background: var(--primary-50); }
    .period-tab.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    
    .leaderboard {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .leaderboard-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .leaderboard-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .product-rank {
        display: flex;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }
    .product-rank:hover { background: var(--primary-50); }
    .product-rank:last-child { border-bottom: none; }
    
    .rank-badge {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .rank-badge.gold { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
    .rank-badge.silver { background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%); }
    .rank-badge.bronze { background: linear-gradient(135deg, #fdba74 0%, #ea580c 100%); }
    .rank-badge.normal {
        background: var(--bg);
        font-size: 1rem;
        font-weight: 800;
        color: var(--muted);
    }
    
    .product-info { flex: 1; }
    .product-name { font-weight: 700; font-size: 1rem; color: var(--text); margin-bottom: 0.25rem; }
    .product-category { font-size: 0.85rem; color: var(--muted); }
    
    .product-stats {
        text-align: right;
    }
    .product-qty {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }
    .product-revenue {
        font-size: 1.1rem;
        font-weight: 800;
        color: #10b981;
    }
    
    .empty-leaderboard {
        padding: 4rem 2rem;
        text-align: center;
    }
    .empty-leaderboard .icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>🏆 Produk Terlaris</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Lihat produk dengan penjualan tertinggi</p>
    </div>
    <form method="GET" class="period-tabs">
        <button type="submit" name="period" value="today" class="period-tab {{ $period === 'today' ? 'active' : '' }}">📅 Hari Ini</button>
        <button type="submit" name="period" value="week" class="period-tab {{ $period === 'week' ? 'active' : '' }}">📆 Minggu Ini</button>
        <button type="submit" name="period" value="month" class="period-tab {{ $period === 'month' ? 'active' : '' }}">🗓️ Bulan Ini</button>
        <button type="submit" name="period" value="all" class="period-tab {{ $period === 'all' ? 'active' : '' }}">📊 Semua</button>
    </form>
</div>

<div class="leaderboard">
    <div class="leaderboard-header">
        <h3><span>📈</span> Peringkat Penjualan</h3>
        <span class="badge badge-info">{{ $products->count() }} produk</span>
    </div>
    
    @forelse($products as $i => $p)
    <div class="product-rank">
        <div class="rank-badge {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : 'normal')) }}">
            @if($i === 0) 🥇
            @elseif($i === 1) 🥈
            @elseif($i === 2) 🥉
            @else {{ $i + 1 }}
            @endif
        </div>
        <div class="product-info">
            <div class="product-name">{{ $p->product->name ?? 'Produk dihapus' }}</div>
            <div class="product-category">{{ $p->product->category->name ?? '-' }}</div>
        </div>
        <div class="product-stats">
            <div class="product-qty">📦 {{ number_format($p->total_qty) }} terjual</div>
            <div class="product-revenue">Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</div>
        </div>
    </div>
    @empty
    <div class="empty-leaderboard">
        <div class="icon">🏆</div>
        <p style="color: var(--muted); font-size: 1rem;">Belum ada data penjualan untuk periode ini.</p>
    </div>
    @endforelse
</div>
@endsection
