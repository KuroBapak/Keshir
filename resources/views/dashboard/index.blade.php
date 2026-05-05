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
    /* Tabs Navigation */
    .dashboard-tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--card);
        padding: 0.5rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-light);
    }
    .dashboard-tab {
        flex: 1;
        padding: 0.85rem 1rem;
        text-align: center;
        border-radius: var(--radius);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }
    .dashboard-tab:hover {
        background: var(--bg);
        color: var(--text);
    }
    .dashboard-tab.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    /* Chart Card */
    .chart-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    /* Best Selling List */
    .best-selling-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .bs-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-light);
    }
    .bs-item:last-child { border-bottom: none; padding-bottom: 0; }
    .bs-img {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
        background: var(--bg);
    }
    .bs-info { flex: 1; }
    .bs-name { font-weight: 600; color: var(--text); font-size: 0.95rem; }
    .bs-sold { font-size: 0.85rem; color: var(--muted); }
    .chart-tab { padding:.5rem 1rem;border-radius:var(--radius);border:1px solid var(--border);background:var(--card);color:var(--text-secondary);font-weight:600;font-size:.85rem;cursor:pointer;transition:all .2s ease; }
    .chart-tab:hover { background:var(--bg);border-color:var(--primary);color:var(--primary); }
    .chart-tab.active { background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);color:#fff;border-color:var(--primary);box-shadow:0 2px 8px rgba(37,99,235,.3); }
    

</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

@if($isOwner)
<!-- Dashboard Tabs -->
<div class="dashboard-tabs">
    <button class="dashboard-tab active" id="tab-analytics" onclick="switchTab('analytics')">📊 Analitik Utama</button>
    <button class="dashboard-tab" id="tab-manage" onclick="switchTab('manage')">⚙️ Manajemen Sistem</button>
</div>

<!-- ======================= -->
<!-- 1. ANALYTICS SECTION    -->
<!-- ======================= -->
<div id="analytics-section">
    <!-- Top Metrics -->
    <div class="grid-4 mb-3">
        <div class="stat-card green">
            <div class="icon-wrap">💰</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
            <div class="stat-change up">{{ $todayOrders }} Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="icon-wrap" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);">📅</div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">Rp {{ number_format($currentMonthSales, 0, ',', '.') }}</div>
            <div class="stat-change {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">{{ $revenueGrowth >= 0 ? '↗' : '↘' }} {{ number_format(abs($revenueGrowth), 1) }}% vs Lalu</div>
        </div>
        <div class="stat-card">
            <div class="icon-wrap" style="background: linear-gradient(135deg, #fef08a 0%, #fde047 100%);">🛍️</div>
            <div class="stat-label">AOV (Rata-rata/Pesanan)</div>
            <div class="stat-value">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
            <div class="stat-change up">{{ $currentMonthOrdersCount }} Pesanan</div>
        </div>
        <div class="stat-card blue" style="cursor:pointer" onclick="document.getElementById('staffModal').style.display='flex'">
            <div class="icon-wrap">👥</div>
            <div class="stat-label">Absensi Hari Ini (Klik Detail)</div>
            <div class="stat-value">{{ $todayAttendance }} <span style="font-size:.8rem;color:var(--muted)">orang</span></div>
            <div class="stat-change up">⏱️ {{ $totalWorkHoursThisMonth }} jam bulan ini</div>
        </div>
    </div>

    <!-- TABBED CHART AREA -->
    <div class="chart-card mb-3">
        <div style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap">
            <button class="chart-tab active" onclick="switchChart('dailyRev')">💵 Pendapatan Harian</button>
            <button class="chart-tab" onclick="switchChart('dailyOrd')">📦 Pesanan Harian</button>
            <button class="chart-tab" onclick="switchChart('monthlyRev')">📊 Pendapatan Bulanan</button>
            <button class="chart-tab" onclick="switchChart('monthlyOrd')">📈 Pesanan Bulanan</button>
        </div>
        <div style="position:relative;height:320px;width:100%">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    <div class="grid-2 mb-3" style="grid-template-columns:1fr 1fr">
        <!-- Best Selling -->
        <div class="chart-card">
            <div class="chart-header"><h3 class="card-title">🔥 Produk Terlaris</h3></div>
            <div class="best-selling-list">
                @forelse($bestSellingProducts as $item)
                <div class="bs-item">
                    @if($item->product && $item->product->photos && count($item->product->photos) > 0)
                        <img src="{{ asset('storage/' . $item->product->photos[0]) }}" class="bs-img" alt="">
                    @else
                        <div class="bs-img" style="display:flex;align-items:center;justify-content:center;font-size:1.5rem">☕</div>
                    @endif
                    <div class="bs-info">
                        <div class="bs-name">{{ $item->product->name ?? '-' }}</div>
                        <div class="bs-sold">{{ $item->total_sold }} terjual</div>
                    </div>
                </div>
                @empty
                <div class="text-muted text-center" style="padding:2rem 0">Belum ada data.</div>
                @endforelse
            </div>
        </div>
        <!-- Category Pie -->
        <div class="chart-card">
            <div class="chart-header"><h3 class="card-title">🍰 Kategori Terjual</h3></div>
            <div style="position:relative;height:280px;width:100%"><canvas id="categoryChart"></canvas></div>
        </div>
    </div>

    <div class="grid-2 mb-3">
        <!-- Order Types -->
        <div class="chart-card">
            <div class="chart-header"><h3 class="card-title">🛎️ Tipe Pesanan</h3></div>
            <div style="position:relative;height:280px;width:100%"><canvas id="orderTypeChart"></canvas></div>
        </div>
        <!-- Work Hours Chart -->
        <div class="chart-card">
            <div class="chart-header"><h3 class="card-title">⏱️ Jam Kerja per Bulan</h3></div>
            <div style="position:relative;height:280px;width:100%"><canvas id="workHoursChart"></canvas></div>
        </div>
    </div>
</div>

<!-- STAFF PERFORMANCE MODAL -->
<div id="staffModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center">
<div style="background:#fff;border-radius:1rem;width:90%;max-width:800px;max-height:85vh;overflow-y:auto;padding:2rem;position:relative">
    <button onclick="document.getElementById('staffModal').style.display='none'" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.5rem;cursor:pointer">✕</button>
    <h2 style="margin-bottom:1.5rem">👥 Performa Absensi Karyawan — {{ now()->translatedFormat('F Y') }}</h2>
    <table>
        <thead><tr><th>Nama</th><th>Role</th><th>Shift</th><th>Hari Masuk</th><th>Total Jam</th><th>Tepat Waktu</th><th>Terlambat</th></tr></thead>
        <tbody>
        @foreach($staffPerformance as $sp)
        <tr>
            <td style="font-weight:600">{{ $sp['name'] }}</td>
            <td><span class="badge badge-primary">{{ ucfirst($sp['role']) }}</span></td>
            <td>{{ $sp['shift'] }}</td>
            <td>{{ $sp['total_days'] }} hari</td>
            <td style="font-weight:600">{{ $sp['total_hours'] }} jam</td>
            <td><span class="badge badge-success">{{ $sp['on_time_days'] }}x</span></td>
            <td><span class="badge {{ $sp['late_days'] > 0 ? 'badge-danger' : 'badge-success' }}">{{ $sp['late_days'] }}x</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
</div>
@endif

<!-- ======================= -->
<!-- 2. MANAGE SECTION       -->
<!-- ======================= -->
<div id="manage-section" style="{{ $isOwner ? 'display: none;' : '' }}">
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
</div> <!-- End Manage Section -->

@push('scripts')
<script>
    @if($isOwner)
    function switchTab(mode) {
        document.getElementById('tab-analytics').classList.toggle('active', mode==='analytics');
        document.getElementById('tab-manage').classList.toggle('active', mode==='manage');
        document.getElementById('analytics-section').style.display = mode==='analytics' ? 'block' : 'none';
        document.getElementById('manage-section').style.display = mode==='manage' ? 'block' : 'none';
    }

    // === CHART DATA ===
    const chartData = {
        dailyRev: { labels: {!! json_encode($dailyRevenueLabels) !!}, data: {!! json_encode($dailyRevenueData) !!}, label: 'Pendapatan (Rp)', color: '#2563eb', isMoney: true },
        dailyOrd: { labels: {!! json_encode($dailyRevenueLabels) !!}, data: {!! json_encode($dailyOrdersData) !!}, label: 'Jumlah Pesanan', color: '#10b981', isMoney: false },
        monthlyRev: { labels: {!! json_encode($monthlyRevenueLabels) !!}, data: {!! json_encode($monthlyRevenueData) !!}, label: 'Pendapatan (Rp)', color: '#8b5cf6', isMoney: true },
        monthlyOrd: { labels: {!! json_encode($monthlyRevenueLabels) !!}, data: {!! json_encode($monthlyOrdersData) !!}, label: 'Jumlah Pesanan', color: '#f59e0b', isMoney: false }
    };

    let mainChartInstance = null;
    function switchChart(key) {
        document.querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
        const d = chartData[key];
        if (mainChartInstance) mainChartInstance.destroy();
        const ctx = document.getElementById('mainChart').getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 350);
        grad.addColorStop(0, d.color + '80');
        grad.addColorStop(1, d.color + '00');
        mainChartInstance = new Chart(ctx, {
            type: key.includes('monthly') ? 'bar' : 'line',
            data: { labels: d.labels, datasets: [{ label: d.label, data: d.data, borderColor: d.color, backgroundColor: key.includes('monthly') ? d.color + '99' : grad, borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: d.color, pointBorderWidth: 2, pointRadius: 4, fill: !key.includes('monthly'), tension: 0.4, borderRadius: 8 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,.9)', padding: 12, displayColors: false, callbacks: { label: function(c) { return d.isMoney ? 'Rp ' + c.parsed.y.toLocaleString('id-ID') : c.parsed.y + ' pesanan'; } } } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { callback: function(v) { if (!d.isMoney) return v; if(v>=1000000) return 'Rp '+(v/1000000).toFixed(1)+'M'; if(v>=1000) return 'Rp '+(v/1000)+'k'; return 'Rp '+v; } } }, x: { grid: { display: false } } } }
        });
    }
    // Init default chart
    document.addEventListener('DOMContentLoaded', () => { switchChart('dailyRev'); });

    // Category Pie
    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'pie',
        data: { labels: {!! json_encode($categoryChartLabels) !!}, datasets: [{ data: {!! json_encode($categoryChartData) !!}, backgroundColor: ['#2563eb','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { font: { family: 'Inter', size: 12 } } } } }
    });

    // Order Types Doughnut
    new Chart(document.getElementById('orderTypeChart').getContext('2d'), {
        type: 'doughnut',
        data: { labels: {!! json_encode($orderTypeLabels) !!}, datasets: [{ data: {!! json_encode($orderTypeData) !!}, backgroundColor: ['#3b82f6','#f97316','#a855f7'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right', labels: { font: { family: 'Inter', size: 12 } } } } }
    });

    // Work Hours Bar Chart
    const whCtx = document.getElementById('workHoursChart').getContext('2d');
    new Chart(whCtx, {
        type: 'bar',
        data: { labels: {!! json_encode($monthlyWorkHoursLabels) !!}, datasets: [{ label: 'Total Jam Kerja', data: {!! json_encode($monthlyWorkHoursData) !!}, backgroundColor: '#8b5cf699', borderColor: '#8b5cf6', borderWidth: 2, borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) { return c.parsed.y + ' jam'; } } } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { callback: v => v + ' jam' } }, x: { grid: { display: false } } } }
    });
    @endif
</script>
@endpush
@endsection

