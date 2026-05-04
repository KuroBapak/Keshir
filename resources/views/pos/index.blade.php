<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Kasir — Keshir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --primary-darker: #1e40af;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --accent: #06b6d4;
            --bg: #f0f4f8;
            --card: #ffffff;
            --text: #0f172a;
            --text-secondary: #475569;
            --muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --success-bg: #d1fae5;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background: var(--bg); 
            color: var(--text);
            font-size: 14px;
        }
        
        /* Modern Navigation */
        .pos-nav {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--primary) 50%, var(--accent) 100%);
            color: #fff;
            padding: 0 2rem;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 30px rgba(37, 99, 235, 0.3);
            position: relative;
            overflow: hidden;
        }
        .pos-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }
        .pos-nav .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }
        .pos-nav .brand-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.1) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        .pos-nav h1 { 
            font-size: 1.25rem; 
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .pos-nav h1 span {
            font-weight: 400;
            opacity: 0.8;
        }
        .pos-nav .info { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }
        .pos-nav .user-pill {
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        .pos-nav .user-avatar {
            width: 28px;
            height: 28px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--primary);
        }
        
        .btn {
            padding: 0.6rem 1.15rem;
            border: none;
            border-radius: 0.6rem;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); 
            color: #fff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        .btn-success { 
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%); 
            color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-warning { background: var(--warning); color: #fff; }
        .btn-outline { 
            background: transparent; 
            border: 1.5px solid var(--border); 
            color: var(--text-secondary);
        }
        .btn-outline:hover { 
            background: var(--primary-50); 
            border-color: var(--primary); 
            color: var(--primary);
        }
        .btn-ghost { 
            background: rgba(255,255,255,0.15); 
            color: #fff; 
            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
        }
        .btn-ghost:hover { 
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
        }
        .btn-sm { padding: 0.45rem 0.85rem; font-size: 0.8rem; }
        .btn-xs { padding: 0.35rem 0.65rem; font-size: 0.75rem; }
        .btn-lg { padding: 0.75rem 1.5rem; font-size: 0.95rem; }
        
        /* Language Switcher */
        .lang-switcher {
            position: relative;
        }
        .lang-btn {
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
            border: 1px solid rgba(255,255,255,0.25);
            padding: 0.5rem 0.85rem;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
        }
        .lang-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
        }
        .lang-dropdown {
            position: fixed;
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            min-width: 140px;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-5px);
            transition: all 0.2s ease;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .lang-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .lang-option {
            padding: 0.75rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
            color: var(--text);
            font-weight: 500;
            transition: all 0.15s ease;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        .lang-option:hover {
            background: var(--primary-50);
            color: var(--primary);
        }
        .lang-option.active {
            background: var(--primary-100);
            color: var(--primary);
            font-weight: 700;
        }
        
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.85rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 1px solid #6ee7b7; }
        .alert-error { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 1px solid #fca5a5; }
        .alert-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border: 1px solid #fcd34d; }
        
        /* Layout */
        .pos-layout { display: flex; height: calc(100vh - 70px); overflow: hidden; }
        
        .pos-left {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: var(--bg);
        }
        .pos-left::-webkit-scrollbar { width: 6px; }
        .pos-left::-webkit-scrollbar-track { background: transparent; }
        .pos-left::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        
        .pos-right {
            width: 400px;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-left: 1px solid var(--border);
            box-shadow: -8px 0 30px rgba(0,0,0,0.05);
        }
        
        .card {
            background: var(--card);
            border-radius: 1.15rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }
        .card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            letter-spacing: -0.01em;
        }
        .section-title .icon {
            width: 32px;
            height: 32px;
            background: var(--primary-100);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.65rem;
            font-size: 0.9rem;
            width: 100%;
            font-family: inherit;
            transition: all 0.2s ease;
            background: #fff;
        }
        .form-control:focus { 
            outline: none; 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px var(--primary-100);
        }
        select.form-control { appearance: auto; cursor: pointer; }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .badge-success { background: var(--success-bg); color: #065f46; }
        .badge-warning { background: var(--warning-bg); color: #92400e; }
        .badge-danger { background: var(--danger-bg); color: #991b1b; }
        .badge-primary { background: var(--primary-100); color: var(--primary-dark); }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        
        /* Bill Cards - Enhanced */
        .bills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .bill-card {
            border: 2px solid var(--border);
            border-radius: 1rem;
            padding: 1.25rem;
            transition: all 0.25s ease;
            cursor: pointer;
            background: var(--card);
            position: relative;
            overflow: hidden;
        }
        .bill-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .bill-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.15);
        }
        .bill-card:hover::before {
            opacity: 1;
        }
        .bill-card.paid {
            border-color: var(--success);
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }
        .bill-card.paid::before {
            background: linear-gradient(90deg, var(--success) 0%, #34d399 100%);
            opacity: 1;
        }
        .bill-card .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        .bill-card h4 { 
            font-size: 1.1rem; 
            font-weight: 800; 
            color: var(--text);
            letter-spacing: -0.02em;
        }
        .bill-card .badges {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }
        .bill-card .meta { 
            font-size: 0.8rem; 
            color: var(--muted); 
            margin-top: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .bill-card .meta span { 
            display: inline-flex; 
            align-items: center; 
            gap: 0.3rem;
            background: var(--bg);
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            font-size: 0.75rem;
        }
        .bill-card .amount {
            font-size: 1.35rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-top: 0.85rem;
        }
        
        /* Catalog - Right Panel */
        .catalog-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--primary-50) 0%, #fff 100%);
        }
        .catalog-header h3 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .catalog-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 1.5rem;
        }
        .catalog-body::-webkit-scrollbar { width: 4px; }
        .catalog-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        
        .catalog-category {
            margin-bottom: 1.5rem;
        }
        .catalog-category-title {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.65rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-100);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .catalog-category-title::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
        }
        .catalog-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 0.75rem;
            margin: 0 -0.75rem;
            border-radius: 0.65rem;
            font-size: 0.875rem;
            transition: all 0.15s ease;
        }
        .catalog-item:hover {
            background: var(--primary-50);
        }
        .catalog-item .name { 
            font-weight: 600; 
            color: var(--text);
        }
        .catalog-item .price { 
            font-weight: 800; 
            color: var(--primary);
            font-size: 0.9rem;
        }
        
        /* New Bill Form - Enhanced */
        .new-bill-card {
            background: linear-gradient(135deg, var(--primary-50) 0%, #ffffff 100%);
            border: 2px solid var(--primary-100);
        }
        .new-bill-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1.5fr auto;
            gap: 1rem;
            align-items: flex-end;
        }
        .new-bill-form .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--muted);
        }
        .empty-state .icon { 
            font-size: 3.5rem; 
            margin-bottom: 1rem; 
            opacity: 0.4;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .empty-state h3 {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        .empty-state p { font-size: 0.9rem; }
        
        /* Shift Warning - Enhanced */
        .shift-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fcd34d;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }
        .shift-warning .content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .shift-warning .icon-box {
            width: 48px;
            height: 48px;
            background: rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .shift-warning .text h4 {
            font-size: 0.95rem;
            color: #92400e;
            margin-bottom: 0.25rem;
        }
        .shift-warning .text p {
            font-size: 0.8rem;
            color: #a16207;
        }
        
        /* Stats Bar */
        .stats-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .stat-item {
            flex: 1;
            background: var(--card);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .stat-icon.blue { background: var(--primary-100); }
        .stat-icon.green { background: var(--success-bg); }
        .stat-icon.orange { background: var(--warning-bg); }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text); }
        .stat-label { font-size: 0.75rem; color: var(--muted); font-weight: 500; }

        @media (max-width: 1024px) {
            .pos-layout { flex-direction: column; }
            .pos-right { width: 100%; height: 50vh; }
            .bills-grid { grid-template-columns: 1fr; }
            .new-bill-form { grid-template-columns: 1fr; }
            .stats-bar { flex-direction: column; }
        }
    </style>
</head>
<body>
    <nav class="pos-nav">
        <div class="brand">
            <div class="brand-icon">🧾</div>
            <h1>POS <span>Kasir</span></h1>
        </div>
        <div class="info">
            <!-- Language Switcher -->
            <div class="lang-switcher">
                <button class="lang-btn" id="langToggle">
                    <span id="currentLangFlag">🇮🇩</span>
                    <span id="currentLangCode">ID</span>
                    <span style="font-size:0.65rem;">▼</span>
                </button>
                <div class="lang-dropdown" id="langDropdown">
                    <button class="lang-option" data-lang="id">
                        <span>🇮🇩</span>
                        <span>Indonesia</span>
                    </button>
                    <button class="lang-option" data-lang="en">
                        <span>🇺🇸</span>
                        <span>English</span>
                    </button>
                </div>
            </div>
            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <span>{{ Auth::user()->name }}</span>
            </div>
            @if(in_array(Auth::user()->role->name, ['owner', 'manager']))
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm" data-i18n="nav.dashboard">📊 Dashboard</a>
            @endif
            <a href="{{ route('pos.bookings') }}" class="btn btn-ghost btn-sm">📅 Reservasi</a>
            <a href="{{ route('cash-drawer.index') }}" class="btn btn-ghost btn-sm" data-i18n="nav.cash_drawer">💰 Kas Laci</a>
            <a href="{{ route('pos.tables') }}" class="btn btn-ghost btn-sm" data-i18n="nav.table_status">🪑 Status Meja</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" data-i18n="nav.logout">🚪 Logout</button>
            </form>
        </div>
    </nav>

    <div class="pos-layout">
        <!-- Left panel: Open Bills + New -->
        <div class="pos-left">
            @if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">❌ {{ session('error') }}</div>@endif
            @if(session('warning'))<div class="alert alert-warning">⚠️ {!! session('warning') !!}</div>@endif

            @if(!$hasActiveShift)
            <div class="shift-warning">
                <div class="content">
                    <div class="icon-box">⚠️</div>
                    <div class="text">
                        <h4 data-i18n="pos.shift_warning_title">Shift Belum Dibuka!</h4>
                        <p data-i18n="pos.shift_warning_desc">Anda tidak bisa membuat transaksi tanpa shift aktif.</p>
                    </div>
                </div>
                <a href="{{ route('cash-drawer.index') }}" class="btn btn-warning" data-i18n="pos.open_shift">🔓 Buka Shift</a>
            </div>
            @endif

            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-icon blue">📋</div>
                    <div>
                        <div class="stat-value">{{ $openBills->where('payment_status', 'open')->count() }}</div>
                        <div class="stat-label" data-i18n="pos.active_bills">Bills Aktif</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon green">✅</div>
                    <div>
                        <div class="stat-value">{{ $openBills->where('payment_status', 'paid')->count() }}</div>
                        <div class="stat-label" data-i18n="pos.paid">Sudah Bayar</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon orange">🍳</div>
                    <div>
                        <div class="stat-value">{{ $openBills->where('payment_status', 'paid')->count() }}</div>
                        <div class="stat-label" data-i18n="pos.cooking">Sedang Dimasak</div>
                    </div>
                </div>
            </div>

            <div class="section-title">
                <span class="icon">🆕</span>
                <span data-i18n="pos.new_bill">Buat Open Bill Baru</span>
            </div>
            <div class="card new-bill-card">
                <form action="{{ route('pos.createBill') }}" method="POST" class="new-bill-form">
                    @csrf
                    <div class="form-group">
                        <label data-i18n="pos.order_type">Tipe Pesanan</label>
                        <select name="order_type" class="form-control" required id="orderTypeSelect">
                            <option value="dine_in" data-i18n-opt="dine_in">🍽️ Dine In</option>
                            <option value="take_away" data-i18n-opt="takeaway">🛍️ Takeaway</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label data-i18n="pos.select_table_label">Pilih Meja</label>
                        <select name="table_id" class="form-control" id="tableSelect">
                            <option value="" data-i18n-opt="no_table">— Tanpa Meja —</option>
                            @foreach($tables as $t)
                                <option value="{{ $t->id }}">{{ $t->table_number }} ({{ $t->capacity }} kursi)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Pelanggan (Opsional)</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Contoh: Kevin">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" data-i18n="pos.create_bill" {{ !$hasActiveShift ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>
                        ➕ Buat Bill
                    </button>
                </form>
            </div>

            {{-- ============================================= --}}
            {{-- UPCOMING BOOKINGS — Always visible --}}
            {{-- ============================================= --}}
            <div class="section-title" style="margin-top:1.5rem;">
                <span class="icon">📅</span>
                <span>Reservasi Mendatang</span> ({{ $upcomingBookings->count() }})
            </div>

            @if($upcomingBookings->count() > 0)
            <div style="border:2px solid var(--warning);border-radius:1rem;overflow:hidden;margin-bottom:1.25rem;">
                <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                    <thead>
                        <tr style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Waktu</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Pemesan</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Meja</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Status</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Pembayaran</th>
                            <th style="padding:0.75rem 1rem;text-align:right;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Total</th>
                            <th style="padding:0.75rem 1rem;text-align:center;font-size:0.75rem;text-transform:uppercase;color:#92400e;letter-spacing:0.03em;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingBookings as $bk)
                        @php $bkBooking = $bk->booking; @endphp
                        <tr style="border-top:1px solid var(--border);background:{{ $bkBooking && $bkBooking->booking_time->isToday() ? '#fefce8' : '#fff' }};">
                            <td style="padding:0.75rem 1rem;">
                                <div style="font-weight:700;">{{ $bkBooking ? $bkBooking->booking_time->format('d M Y') : '-' }}</div>
                                <div style="font-size:1rem;color:var(--primary);font-weight:800;">{{ $bkBooking ? $bkBooking->booking_time->format('H:i') : '-' }}</div>
                                @if($bkBooking && $bkBooking->booking_time->isToday())
                                    <span class="badge badge-danger" style="margin-top:0.25rem;font-size:0.65rem;">HARI INI</span>
                                @elseif($bkBooking && $bkBooking->booking_time->isPast())
                                    <span class="badge badge-warning" style="margin-top:0.25rem;font-size:0.65rem;">LEWAT</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem 1rem;">
                                <div style="font-weight:600;">{{ $bk->customer_name }}</div>
                                <div style="font-size:0.8rem;color:var(--muted);">📱 {{ $bk->phone }}</div>
                            </td>
                            <td style="padding:0.75rem 1rem;">
                                <div style="font-weight:700;">{{ $bk->table ? 'Meja ' . $bk->table->table_number : '-' }}</div>
                                <div style="font-size:0.8rem;color:var(--muted);">👥 {{ $bk->people_count }} org</div>
                            </td>
                            <td style="padding:0.75rem 1rem;">
                                @if($bkBooking && $bkBooking->status === 'pending')
                                    <span class="badge badge-warning">⏳ Menunggu</span>
                                @elseif($bkBooking && $bkBooking->status === 'approved')
                                    <span class="badge badge-success">✅ Dikonfirmasi</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem 1rem;">
                                @if($bk->payment_status === 'paid')
                                    <span class="badge badge-success">💰 Lunas</span>
                                @elseif($bk->payment && $bk->payment->method === 'cash' && $bk->payment->status === 'pending')
                                    <span class="badge badge-danger">💵 Tunai (Blm Bayar)</span>
                                @else
                                    <span class="badge badge-warning">⏳ Belum Lunas</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem 1rem;text-align:right;font-weight:700;">Rp {{ number_format($bk->grand_total, 0, ',', '.') }}</td>
                            <td style="padding:0.75rem 1rem;text-align:center;">
                                <a href="{{ route('pos.bill', $bk) }}" class="btn btn-sm" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;display:block;margin-bottom:0.25rem;">Buka Bill</a>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card" style="text-align:center;color:var(--muted);padding:1.5rem;">
                Belum ada reservasi mendatang.
            </div>
            @endif


            <div class="section-title">
                <span class="icon">📋</span>
                <span data-i18n="pos.active_bills_title">Bills Aktif</span> ({{ $openBills->count() }})
            </div>
            
            @if($openBills->count() > 0)
            <div class="bills-grid">
                @foreach($openBills as $bill)
                    <a href="{{ route('pos.bill', $bill) }}" style="text-decoration:none;">
                            <div class="bill-card {{ in_array($bill->payment_status, ['paid', 'refunded']) ? 'paid' : '' }} {{ $bill->payment_status === 'void' ? 'void' : '' }}" style="{{ in_array($bill->payment_status, ['void', 'refunded']) ? 'opacity:0.7;' : '' }}">
                                <div class="bill-header">
                                    <h4 style="{{ in_array($bill->payment_status, ['void', 'refunded']) ? 'text-decoration:line-through;' : '' }}">{{ $bill->customer_name ?: 'Bill #' . ($bill->bill_number ?? $bill->id) }}</h4>
                                    <div class="badges">
                                        @if($bill->payment_status === 'paid')
                                            <span class="badge badge-success">✅ Paid</span>
                                        @elseif($bill->payment_status === 'void')
                                            <span class="badge badge-danger">🗑️ Void</span>
                                        @elseif($bill->payment_status === 'refunded')
                                            <span class="badge badge-danger">↩️ Refunded</span>
                                        @endif
                                        <span class="badge badge-{{ $bill->order_type === 'dine_in' ? 'primary' : ($bill->order_type === 'booking' ? 'warning' : 'info') }}">
                                            @if($bill->order_type === 'dine_in') 🍽️ Dine In
                                            @elseif($bill->order_type === 'booking') 📅 Booking
                                            @else 🛍️ Takeaway
                                            @endif
                                        </span>
                                        @if($bill->source === 'qr')
                                            <span class="badge badge-warning">📱 QR Menu</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="meta">
                                    <span>{{ $bill->table ? '🪑 ' . $bill->table->table_number : '🛍️ No Table' }}</span>
                                    <span>📦 {{ $bill->details->count() }} item</span>
                                    <span>⏰ {{ $bill->created_at->diffForHumans() }}</span>
                                </div>
                                @php
                                    $isCooking = $bill->details->contains(function ($detail) {
                                        return in_array($detail->status, ['pending', 'in_progress']);
                                    });
                                @endphp
                                @if(in_array($bill->payment_status, ['open', 'paid']) && $isCooking && $bill->details->count() > 0)
                                    <div style="margin-top: 0.5rem;">
                                        <span class="badge badge-warning">🔥 Sedang Dimasak</span>
                                    </div>
                                @elseif($bill->payment_status === 'paid' && !$isCooking && $bill->details->count() > 0)
                                    <div style="margin-top: 0.5rem;">
                                        <span class="badge badge-success">✅ Pesanan Selesai</span>
                                    </div>
                                @endif
                                @if($bill->source === 'qr' && $bill->payment_status === 'open' && (!$bill->booking || $bill->booking->status === 'approved'))
                                    <div style="margin-top: 0.5rem;">
                                        <span class="badge badge-danger">💰 Menunggu Bayar Cash</span>
                                    </div>
                                    <form action="{{ route('pos.confirmQrCash', $bill) }}" method="POST" style="margin-top: 0.75rem;" onclick="event.stopPropagation();">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" style="width:100%;" onclick="event.preventDefault(); if(confirm('Konfirmasi pembayaran tunai Rp {{ number_format($bill->grand_total, 0, ',', '.') }}?')) this.closest('form').submit();">
                                            ✅ Konfirmasi Bayar Cash
                                        </button>
                                    </form>
                                @endif
                                <div class="amount" style="{{ in_array($bill->payment_status, ['void', 'refunded']) ? 'text-decoration:line-through;color:var(--muted);background:none;-webkit-text-fill-color:var(--muted);' : '' }}">Rp {{ number_format($bill->grand_total, 0, ',', '.') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
            @else
            <div class="card">
                <div class="empty-state">
                    <div class="icon">📝</div>
                    <h3 data-i18n="pos.no_bills">Belum Ada Open Bill</h3>
                    <p data-i18n="pos.no_bills_desc">Buat bill baru menggunakan form di atas untuk memulai transaksi.</p>
                </div>
            </div>
            @endif

            
        </div>

        <!-- Right panel: Product Catalog -->
        <div class="pos-right">
            <div class="catalog-header">
                <h3 data-i18n="pos.catalog">🍽️ Katalog Produk</h3>
            </div>
            <div class="catalog-body">
                @foreach($categories as $cat)
                    <div class="catalog-category">
                        <div class="catalog-category-title">{{ $cat->name }}</div>
                        @foreach($products->where('category_id', $cat->id) as $p)
                            <div class="catalog-item">
                                <span class="name">{{ $p->name }}</span>
                                <span class="price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
    // Language Translations
    const translations = {
        id: {
            'nav.dashboard': '📊 Dashboard',
            'nav.cash_drawer': '💰 Kas Laci',
            'nav.table_status': '🪑 Status Meja',
            'nav.logout': '🚪 Logout',
            'pos.shift_warning_title': 'Shift Belum Dibuka!',
            'pos.shift_warning_desc': 'Anda tidak bisa membuat transaksi tanpa shift aktif.',
            'pos.open_shift': '🔓 Buka Shift',
            'pos.active_bills': 'Bills Aktif',
            'pos.active_bills_title': 'Bills Aktif',
            'pos.paid': 'Sudah Bayar',
            'pos.cooking': 'Sedang Dimasak',
            'pos.new_bill': 'Buat Open Bill Baru',
            'pos.order_type': 'Tipe Pesanan',
            'pos.select_table_label': 'Pilih Meja',
            'pos.create_bill': '➕ Buat Bill',
            'pos.no_bills': 'Belum Ada Open Bill',
            'pos.no_bills_desc': 'Buat bill baru menggunakan form di atas untuk memulai transaksi.',
            'pos.catalog': '🍽️ Katalog Produk',
            'pos.items': 'item'
        },
        en: {
            'nav.dashboard': '📊 Dashboard',
            'nav.cash_drawer': '💰 Cash Drawer',
            'nav.table_status': '🪑 Table Status',
            'nav.logout': '🚪 Logout',
            'pos.shift_warning_title': 'Shift Not Started!',
            'pos.shift_warning_desc': 'You cannot create transactions without an active shift.',
            'pos.open_shift': '🔓 Open Shift',
            'pos.active_bills': 'Active Bills',
            'pos.active_bills_title': 'Active Bills',
            'pos.paid': 'Paid',
            'pos.cooking': 'Cooking',
            'pos.new_bill': 'Create New Open Bill',
            'pos.order_type': 'Order Type',
            'pos.select_table_label': 'Select Table',
            'pos.create_bill': '➕ Create Bill',
            'pos.no_bills': 'No Open Bills Yet',
            'pos.no_bills_desc': 'Create a new bill using the form above to start a transaction.',
            'pos.catalog': '🍽️ Product Catalog',
            'pos.items': 'items'
        }
    };

    // Select options translations
    const selectTranslations = {
        id: {
            'dine_in': '🍽️ Dine In',
            'takeaway': '🛍️ Takeaway',
            'no_table': '— Tanpa Meja —'
        },
        en: {
            'dine_in': '🍽️ Dine In',
            'takeaway': '🛍️ Takeaway',
            'no_table': '— No Table —'
        }
    };

    // Language Switcher Logic
    document.addEventListener('DOMContentLoaded', function() {
        const langToggle = document.getElementById('langToggle');
        const langDropdown = document.getElementById('langDropdown');
        const currentLangFlag = document.getElementById('currentLangFlag');
        const currentLangCode = document.getElementById('currentLangCode');
        const langOptions = document.querySelectorAll('.lang-option');
        
        // Get saved language
        let currentLang = localStorage.getItem('keshir_lang') || 'id';
        
        // Apply saved language
        applyLanguage(currentLang);
        updateLangButton(currentLang);
        
        // Toggle dropdown
        langToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const rect = langToggle.getBoundingClientRect();
            langDropdown.style.top = (rect.bottom + 8) + 'px';
            langDropdown.style.right = (window.innerWidth - rect.right) + 'px';
            langDropdown.classList.toggle('show');
        });
        
        // Select language
        langOptions.forEach(option => {
            option.addEventListener('click', function() {
                const lang = this.dataset.lang;
                currentLang = lang;
                localStorage.setItem('keshir_lang', lang);
                applyLanguage(lang);
                updateLangButton(lang);
                langDropdown.classList.remove('show');
            });
        });
        
        // Close dropdown on outside click
        document.addEventListener('click', function() {
            langDropdown.classList.remove('show');
        });
        
        function updateLangButton(lang) {
            if (lang === 'id') {
                currentLangFlag.textContent = '🇮🇩';
                currentLangCode.textContent = 'ID';
            } else {
                currentLangFlag.textContent = '🇺🇸';
                currentLangCode.textContent = 'EN';
            }
            langOptions.forEach(opt => {
                opt.classList.toggle('active', opt.dataset.lang === lang);
            });
        }
        
        function applyLanguage(lang) {
            // Translate data-i18n elements
            const elements = document.querySelectorAll('[data-i18n]');
            elements.forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    el.textContent = translations[lang][key];
                }
            });
            
            // Translate select options
            const selectOpts = document.querySelectorAll('[data-i18n-opt]');
            selectOpts.forEach(opt => {
                const key = opt.getAttribute('data-i18n-opt');
                if (selectTranslations[lang] && selectTranslations[lang][key]) {
                    opt.textContent = selectTranslations[lang][key];
                }
            });
        }
        
        // Handle Table Selection toggle based on Order Type
        const orderTypeSelect = document.getElementById('orderTypeSelect');
        const tableSelect = document.getElementById('tableSelect');
        
        if (orderTypeSelect && tableSelect) {
            orderTypeSelect.addEventListener('change', function() {
                if (this.value === 'take_away') {
                    tableSelect.value = '';
                    tableSelect.disabled = true;
                    tableSelect.style.opacity = '0.5';
                } else {
                    tableSelect.disabled = false;
                    tableSelect.style.opacity = '1';
                }
            });
            // trigger on load
            orderTypeSelect.dispatchEvent(new Event('change'));
        }
    });
    </script>
</body>
</html>
