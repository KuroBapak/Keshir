@extends('layouts.app')
@section('title', 'Dashboard — Keshir POS')

@push('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
        border-radius: var(--radius-lg);
        padding: 2rem;
        color: #fff;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -30%;
        right: 10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .welcome-banner h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    .welcome-banner p {
        opacity: 0.9;
        font-size: 1rem;
        position: relative;
        z-index: 1;
    }
    .welcome-time {
        font-size: 0.875rem;
        opacity: 0.8;
        margin-top: 1rem;
        position: relative;
        z-index: 1;
    }

    .stat-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    .stat-card .icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .stat-card.blue .icon-wrap { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
    .stat-card.green .icon-wrap { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
    .stat-card.purple .icon-wrap { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); }
    .stat-card.orange .icon-wrap { background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%); }
    
    .stat-card .stat-label {
        font-size: 0.875rem;
        color: var(--muted);
        margin-bottom: 0.25rem;
    }
    .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1.2;
    }
    .stat-card .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.5rem;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
    }
    .stat-card .stat-change.up { background: #d1fae5; color: #059669; }
    .stat-card .stat-change.down { background: #fee2e2; color: #dc2626; }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: var(--card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius);
        text-decoration: none;
        color: var(--text);
        transition: all 0.2s ease;
        margin-bottom: 0.75rem;
    }
    .quick-action:hover {
        border-color: var(--primary);
        background: var(--primary-50);
        transform: translateX(4px);
    }
    .quick-action .qa-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--primary-100);
    }
    .quick-action .qa-text {
        flex: 1;
    }
    .quick-action .qa-title {
        font-weight: 600;
        font-size: 0.95rem;
    }
    .quick-action .qa-desc {
        font-size: 0.8rem;
        color: var(--muted);
    }
    .quick-action .qa-arrow {
        color: var(--muted);
        transition: transform 0.2s ease;
    }
    .quick-action:hover .qa-arrow {
        transform: translateX(4px);
        color: var(--primary);
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-light);
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-top: 0.35rem;
        flex-shrink: 0;
    }
    .activity-dot.blue { background: var(--primary); }
    .activity-dot.green { background: var(--success); }
    .activity-dot.orange { background: var(--warning); }
    .activity-content { flex: 1; }
    .activity-text { font-size: 0.875rem; color: var(--text); }
    .activity-time { font-size: 0.75rem; color: var(--muted); margin-top: 0.25rem; }
</style>
@endpush

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner">
    <h1>👋 Selamat Datang, {{ Auth::user()->name }}!</h1>
    <p>Kelola bisnis Keshir Coffee & Eatery dari dashboard ini.</p>
    <div class="welcome-time">
        📅 {{ now()->translatedFormat('l, d F Y') }} • ⏰ <span id="current-time">{{ now()->format('H:i') }}</span>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid-4 mb-3">
    <div class="stat-card blue">
        <div class="icon-wrap">📦</div>
        <div class="stat-label">Total Produk</div>
        <div class="stat-value">{{ \App\Models\Product::count() }}</div>
        <div class="stat-change up">Aktif</div>
    </div>
    <div class="stat-card green">
        <div class="icon-wrap">📂</div>
        <div class="stat-label">Kategori</div>
        <div class="stat-value">{{ \App\Models\Category::count() }}</div>
        <div class="stat-change up">Menu</div>
    </div>
    <div class="stat-card purple">
        <div class="icon-wrap">🪑</div>
        <div class="stat-label">Meja Tersedia</div>
        <div class="stat-value">{{ \App\Models\Table::where('status', 'available')->count() }}</div>
        <div class="stat-change up">Ready</div>
    </div>
    <div class="stat-card orange">
        <div class="icon-wrap">🧪</div>
        <div class="stat-label">Bahan Baku</div>
        <div class="stat-value">{{ \App\Models\Ingredient::count() }}</div>
        <div class="stat-change up">Items</div>
    </div>
</div>

<div class="grid-2">
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">⚡ Aksi Cepat</h3>
        </div>
        
        <a href="{{ route('pos.index') }}" class="quick-action">
            <div class="qa-icon">🧾</div>
            <div class="qa-text">
                <div class="qa-title">Buka POS Kasir</div>
                <div class="qa-desc">Mulai transaksi penjualan</div>
            </div>
            <div class="qa-arrow">→</div>
        </a>
        
        <a href="{{ route('products.create') }}" class="quick-action">
            <div class="qa-icon">➕</div>
            <div class="qa-text">
                <div class="qa-title">Tambah Produk Baru</div>
                <div class="qa-desc">Tambahkan menu ke katalog</div>
            </div>
            <div class="qa-arrow">→</div>
        </a>
        
        <a href="{{ route('reports.daily') }}" class="quick-action">
            <div class="qa-icon">📊</div>
            <div class="qa-text">
                <div class="qa-title">Lihat Laporan Harian</div>
                <div class="qa-desc">Cek penjualan hari ini</div>
            </div>
            <div class="qa-arrow">→</div>
        </a>
        
        <a href="{{ route('kitchen.index') }}" class="quick-action">
            <div class="qa-icon">👨‍🍳</div>
            <div class="qa-text">
                <div class="qa-title">Pantau Dapur</div>
                <div class="qa-desc">Lihat pesanan yang sedang dimasak</div>
            </div>
            <div class="qa-arrow">→</div>
        </a>
    </div>

    <!-- System Info & Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📋 Informasi Sistem</h3>
        </div>
        
        <div class="activity-item">
            <div class="activity-dot blue"></div>
            <div class="activity-content">
                <div class="activity-text">Anda login sebagai <strong>{{ ucfirst(Auth::user()->role->name) }}</strong></div>
                <div class="activity-time">Role: {{ Auth::user()->role->name }}</div>
            </div>
        </div>
        
        <div class="activity-item">
            <div class="activity-dot green"></div>
            <div class="activity-content">
                <div class="activity-text">Sistem POS aktif dan berjalan normal</div>
                <div class="activity-time">Status: Online</div>
            </div>
        </div>
        
        <div class="activity-item">
            <div class="activity-dot orange"></div>
            <div class="activity-content">
                <div class="activity-text">Database terkoneksi dengan baik</div>
                <div class="activity-time">Laravel v{{ app()->version() }}</div>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
            <h4 style="font-size: 0.85rem; color: var(--muted); margin-bottom: 0.75rem;">Pintasan Menu</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline">🍽️ Produk</a>
                <a href="{{ route('ingredients.index') }}" class="btn btn-sm btn-outline">🧪 Bahan</a>
                <a href="{{ route('tables.index') }}" class="btn btn-sm btn-outline">🪑 Meja</a>
                <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline">⚙️ Setting</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update time every second
    setInterval(() => {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('current-time').textContent = time;
    }, 1000);
</script>
@endpush
@endsection
