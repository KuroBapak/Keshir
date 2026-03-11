<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Keshir POS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --primary-dark:#1d4ed8; --bg:#f1f5f9; --card:#fff; --text:#1e293b; --muted:#64748b; --border:#e2e8f0; --success:#16a34a; --danger:#dc2626; --warning:#f59e0b; --sidebar-w:240px; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
        .top-nav { background:var(--primary); color:#fff; padding:0.75rem 1.5rem; display:flex; justify-content:space-between; align-items:center; position:fixed; top:0; left:0; right:0; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
        .top-nav h1 { font-size:1.15rem; font-weight:700; }
        .top-nav .right { display:flex; align-items:center; gap:1rem; font-size:0.85rem; }
        .layout { display:flex; margin-top:52px; min-height:calc(100vh - 52px); }
        .sidebar { width:var(--sidebar-w); background:var(--card); border-right:1px solid var(--border); padding:1rem 0; position:fixed; top:52px; bottom:0; overflow-y:auto; }
        .sidebar a { display:block; padding:0.6rem 1.25rem; font-size:0.85rem; color:var(--muted); text-decoration:none; transition:all 0.15s; border-left:3px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background:#eff6ff; color:var(--primary); border-left-color:var(--primary); font-weight:600; }
        .sidebar .section-title { padding:0.5rem 1.25rem; font-size:0.7rem; text-transform:uppercase; color:var(--muted); font-weight:700; letter-spacing:0.5px; margin-top:0.5rem; }
        .main-content { margin-left:var(--sidebar-w); flex:1; padding:1.5rem; }
        .card { background:var(--card); border-radius:0.75rem; box-shadow:0 1px 3px rgba(0,0,0,0.06); padding:1.5rem; margin-bottom:1rem; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
        .page-header h2 { font-size:1.25rem; }
        .btn { padding:0.5rem 1rem; border:none; border-radius:0.5rem; cursor:pointer; font-size:0.8rem; font-weight:600; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem; }
        .btn-primary { background:var(--primary); color:#fff; } .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:#fff; } .btn-success:hover { background:#15803d; }
        .btn-danger { background:var(--danger); color:#fff; } .btn-danger:hover { background:#b91c1c; }
        .btn-outline { background:transparent; border:1px solid var(--border); color:var(--text); } .btn-outline:hover { background:var(--bg); }
        .btn-sm { padding:0.35rem 0.65rem; font-size:0.75rem; }
        .btn-xs { padding:0.25rem 0.5rem; font-size:0.7rem; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:0.65rem 0.75rem; border-bottom:1px solid var(--border); font-size:0.85rem; }
        th { font-weight:600; font-size:0.75rem; text-transform:uppercase; color:var(--muted); }
        .form-group { margin-bottom:1rem; }
        .form-group label { display:block; margin-bottom:0.3rem; font-weight:600; font-size:0.8rem; }
        .form-control { width:100%; padding:0.55rem 0.75rem; border:1px solid var(--border); border-radius:0.5rem; font-size:0.85rem; }
        .form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
        select.form-control { appearance:auto; }
        .form-inline { display:flex; gap:0.5rem; align-items:flex-end; }
        .form-inline .form-group { margin-bottom:0; }
        .alert { padding:0.65rem 1rem; border-radius:0.5rem; margin-bottom:1rem; font-size:0.8rem; }
        .alert-success { background:#dcfce7; color:#166534; }
        .alert-error { background:#fef2f2; color:#991b1b; }
        .alert-info { background:#dbeafe; color:#1e40af; }
        .alert-warning { background:#fff3cd; color:#856404; }
        .badge { display:inline-block; padding:0.15rem 0.5rem; border-radius:9999px; font-size:0.7rem; font-weight:600; }
        .badge-success { background:#dcfce7; color:#166534; }
        .badge-warning { background:#fef3c7; color:#92400e; }
        .badge-danger { background:#fef2f2; color:#991b1b; }
        .badge-info { background:#dbeafe; color:#1e40af; }
        .empty-state { text-align:center; padding:2rem; color:var(--muted); font-size:0.85rem; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .text-right { text-align:right; }
        .text-muted { color:var(--muted); }
        .mt-1 { margin-top:0.5rem; } .mt-2 { margin-top:1rem; } .mb-1 { margin-bottom:0.5rem; } .mb-2 { margin-bottom:1rem; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="top-nav">
        <h1>☕ Keshir POS</h1>
        <div class="right">
            @auth
                <span>{{ Auth::user()->name }} ({{ Auth::user()->role->name }})</span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">Login</a>
            @endauth
        </div>
    </nav>

    <div class="layout">
        <aside class="sidebar">
            @auth
            @php $role = auth()->user()->role->name; @endphp

            @if(in_array($role, ['owner', 'manager']))
            <div class="section-title">Menu</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>

            <div class="section-title">Master Data</div>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">📂 Kategori</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">🍽️ Produk</a>
            <a href="{{ route('ingredients.index') }}" class="{{ request()->routeIs('ingredients.*') ? 'active' : '' }}">🧪 Bahan Baku</a>
            <a href="{{ route('recipes.index') }}" class="{{ request()->routeIs('recipes.*') || request()->routeIs('products.recipe.*') ? 'active' : '' }}">📋 Resep</a>

            <div class="section-title">Operasional</div>
            <a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.*') ? 'active' : '' }}">🪑 Meja</a>
            <a href="{{ route('discounts.index') }}" class="{{ request()->routeIs('discounts.*') ? 'active' : '' }}">🏷️ Diskon</a>
            <a href="{{ route('cash-drawer.index') }}" class="{{ request()->routeIs('cash-drawer.*') ? 'active' : '' }}">💰 Kas Laci</a>
            <a href="{{ route('refunds.index') }}" class="{{ request()->routeIs('refunds.*') ? 'active' : '' }}">🔄 Refund</a>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">⚙️ Pengaturan</a>
            <a href="{{ route('attendance.temp') }}">📋 Absensi</a>

            <div class="section-title">Laporan</div>
            <a href="{{ route('reports.daily') }}" class="{{ request()->routeIs('reports.daily') ? 'active' : '' }}">📊 Laporan Harian</a>
            <a href="{{ route('reports.best-selling') }}" class="{{ request()->routeIs('reports.best-selling') ? 'active' : '' }}">🏆 Produk Terlaris</a>

            <div class="section-title">POS</div>
            <a href="{{ route('pos.index') }}">🧾 Kasir POS</a>
            <a href="{{ route('kitchen.index') }}">👨‍🍳 Dapur</a>
            @endif

            @if($role === 'cashier')
            <div class="section-title">Kasir</div>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') && !request()->routeIs('pos.bookings') ? 'active' : '' }}">🧾 POS Kasir</a>
            <a href="{{ route('pos.bookings') }}" class="{{ request()->routeIs('pos.bookings') ? 'active' : '' }}">📅 Reservasi / Booking</a>
            <a href="{{ route('cash-drawer.index') }}" class="{{ request()->routeIs('cash-drawer.*') && !request()->routeIs('cash-drawer.shift-sales') ? 'active' : '' }}">💰 Kas Laci</a>
            <a href="{{ route('cash-drawer.shift-sales') }}" class="{{ request()->routeIs('cash-drawer.shift-sales') ? 'active' : '' }}">📊 Penjualan Shift</a>
            <a href="{{ route('refunds.index') }}" class="{{ request()->routeIs('refunds.*') ? 'active' : '' }}">🔄 Refund</a>
            @endif
            @endauth
        </aside>

        <div class="main-content">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
            @if(session('warning'))<div class="alert alert-warning">{!! session('warning') !!}</div>@endif
            @if(session('info'))<div class="alert alert-info">{{ session('info') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
