<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15">
    <title>Dapur — Keshir POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --accent: #06b6d4;
            --bg: #f0f4f8;
            --bg-light: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --text-secondary: #475569;
            --muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --success-light: #d1fae5;
            --success-glow: rgba(16, 185, 129, 0.3);
            --danger: #ef4444;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --warning-glow: rgba(245, 158, 11, 0.3);
            --cooking: #f97316;
            --cooking-light: #ffedd5;
            --cooking-glow: rgba(249, 115, 22, 0.3);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background: linear-gradient(180deg, var(--bg) 0%, #e2e8f0 100%);
            color: var(--text); 
            min-height: 100vh;
            font-size: 14px;
        }
        
        /* Modern Navigation - Light */
        .kitchen-nav {
            background: linear-gradient(135deg, #1e3a8a 0%, var(--primary) 50%, var(--accent) 100%);
            color: #fff;
            padding: 0 2rem;
            height: 72px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 30px rgba(37, 99, 235, 0.25);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .kitchen-nav::before {
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
        .kitchen-nav .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }
        .kitchen-nav .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.1) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        .kitchen-nav h1 { 
            font-size: 1.35rem; 
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .kitchen-nav h1 span {
            font-weight: 400;
            opacity: 0.8;
        }
        .kitchen-nav .info { 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.6rem;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-success { 
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%); 
            color: #fff;
            box-shadow: 0 4px 15px var(--success-glow);
        }
        .btn-success:hover { 
            box-shadow: 0 6px 20px var(--success-glow);
            filter: brightness(1.05);
        }
        .btn-warning { 
            background: linear-gradient(135deg, var(--cooking) 0%, #ea580c 100%); 
            color: #fff;
            box-shadow: 0 4px 15px var(--cooking-glow);
        }
        .btn-warning:hover { 
            box-shadow: 0 6px 20px var(--cooking-glow);
            filter: brightness(1.05);
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
        .btn-sm { padding: 0.6rem 1.1rem; font-size: 1rem; }
        .btn-xs { padding: 0.55rem 1rem; font-size: 0.95rem; }
        
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
            background: #eff6ff;
            color: var(--primary);
        }
        .lang-option.active {
            background: #dbeafe;
            color: var(--primary);
            font-weight: 700;
        }
        
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.85rem;
            margin: 1rem 2rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success { 
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); 
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        /* Stats Bar - Light */
        .stats-bar {
            display: flex;
            gap: 1.25rem;
            padding: 1.5rem 2rem;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .stat-card {
            flex: 1;
            background: linear-gradient(135deg, #fff 0%, var(--bg-light) 100%);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.pending { 
            background: linear-gradient(135deg, var(--warning-light) 0%, #fde68a 100%);
        }
        .stat-icon.cooking { 
            background: linear-gradient(135deg, var(--cooking-light) 0%, #fed7aa 100%);
            animation: pulse 2s ease-in-out infinite;
        }
        .stat-icon.done { 
            background: linear-gradient(135deg, var(--success-light) 0%, #a7f3d0 100%);
        }
        .stat-value { 
            font-size: 2.75rem; 
            font-weight: 900; 
            color: var(--text);
            letter-spacing: -0.025em;
        }
        .stat-label { 
            font-size: 1rem; 
            color: var(--muted); 
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Ticket Grid */
        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem 2rem;
        }
        
        /* Ticket Card - Light Theme */
        .ticket {
            background: var(--card);
            color: var(--text);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .ticket:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .ticket-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
        }
        .ticket-header.pending {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: #fff;
        }
        .ticket-header.cooking {
            background: linear-gradient(135deg, var(--cooking) 0%, #ea580c 100%);
            color: #fff;
            animation: headerPulse 2s ease-in-out infinite;
        }
        @keyframes headerPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.92; }
        }
        
        .ticket-header .order-info {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .ticket-header .order-number {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: -0.02em;
        }
        .ticket-header .order-type {
            background: rgba(255,255,255,0.2);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }
        .ticket-header .order-type.takeaway {
            background: #fff;
            color: #ea580c;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.4);
            animation: pulse-bg 1.5s infinite;
        }
        @keyframes pulse-bg {
            0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.7); }
            70% { box-shadow: 0 0 0 8px rgba(255,255,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }
        .ticket-header .timestamp {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 600;
            background: rgba(0,0,0,0.1);
            padding: 0.4rem 1rem;
            border-radius: 50px;
        }
        
        .ticket-body { 
            padding: 1rem 1.5rem;
            background: #fff;
        }
        
        .ticket-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            border-radius: 0.5rem;
        }
        .ticket-item:last-child { border-bottom: none; }
        .ticket-item:hover {
            background: var(--bg-light);
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .item-info { flex: 1; }
        .item-info .name {
            font-weight: 800;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--text);
        }
        .item-info .qty {
            background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
            color: #fff;
            min-width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 900;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }
        .item-info .variant {
            color: var(--muted);
            font-weight: 600;
            font-size: 1.1rem;
        }
        .item-info .detail {
            font-size: 1.05rem;
            color: var(--muted);
            margin-top: 0.35rem;
            padding-left: 2.75rem;
            font-weight: 500;
        }
        .item-info .notes {
            font-size: 1.05rem;
            color: var(--cooking);
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            padding-left: 2.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 700;
            background: var(--cooking-light);
            border-radius: 50px;
            margin-left: -0.5rem;
        }
        
        .item-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.1rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .status-pending { 
            background: linear-gradient(135deg, var(--warning-light) 0%, #fde68a 100%); 
            color: #92400e;
        }
        .status-cooking { 
            background: linear-gradient(135deg, var(--cooking-light) 0%, #fed7aa 100%); 
            color: #c2410c;
            animation: pulse 2s ease-in-out infinite;
        }
        .status-done { 
            background: linear-gradient(135deg, var(--success-light) 0%, #a7f3d0 100%); 
            color: #065f46;
        }
        
        .ticket-footer {
            padding: 1rem 1.5rem;
            border-top: 2px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
        }
        .ticket-footer .item-count {
            font-size: 1.1rem;
            color: var(--muted);
            font-weight: 700;
        }
        
        /* Empty State - Light */
        .empty-kitchen {
            text-align: center;
            padding: 6rem 2rem;
            color: var(--muted);
        }
        .empty-kitchen .icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }
        .empty-kitchen .icon {
            font-size: 5rem;
            animation: float 4s ease-in-out infinite;
        }
        .empty-kitchen .icon-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 130px;
            height: 130px;
            border: 2px dashed var(--border);
            border-radius: 50%;
            animation: spin 20s linear infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }
        @keyframes spin {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .empty-kitchen h2 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: var(--text);
            letter-spacing: -0.025em;
        }
        .empty-kitchen p {
            font-size: 1rem;
            color: var(--muted);
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Live indicator */
        .live-indicator {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(16, 185, 129, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .live-dot {
            width: 10px;
            height: 10px;
            background: var(--success);
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
            box-shadow: 0 0 8px var(--success);
        }
        @keyframes blink {
            0%, 100% { opacity: 1; box-shadow: 0 0 8px var(--success); }
            50% { opacity: 0.5; box-shadow: 0 0 4px var(--success); }
        }
        
        .user-pill {
            background: rgba(255,255,255,0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.8rem;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .ticket-grid { 
                grid-template-columns: 1fr; 
                padding: 1rem;
            }
            .stats-bar { 
                flex-direction: column; 
                padding: 1rem;
            }
            .kitchen-nav { 
                padding: 0 1rem; 
                height: 64px;
            }
            .kitchen-nav h1 { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <nav class="kitchen-nav">
        <div class="brand">
            <div class="brand-icon">🍳</div>
            <h1>Kitchen <span>Display</span></h1>
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
            <div class="live-indicator">
                <span class="live-dot"></span>
                <span data-i18n="kitchen.live">Live • Auto-refresh 15s</span>
            </div>
            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <span>{{ Auth::user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" data-i18n="nav.logout">🚪 Logout</button>
            </form>
        </div>
    </nav>

    @if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon pending">⏳</div>
            <div>
                <div class="stat-value">{{ $tickets->sum(fn($t) => $t->details->where('status', 'pending')->count()) }}</div>
                <div class="stat-label" data-i18n="kitchen.pending">Menunggu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon cooking">🔥</div>
            <div>
                <div class="stat-value">{{ $tickets->sum(fn($t) => $t->details->where('status', 'in_progress')->count()) }}</div>
                <div class="stat-label" data-i18n="kitchen.cooking">Sedang Dimasak</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon done">✅</div>
            <div>
                <div class="stat-value">{{ $tickets->count() }}</div>
                <div class="stat-label" data-i18n="kitchen.total_orders">Total Order</div>
            </div>
        </div>
    </div>

    @if($tickets->count() > 0)
    <div class="ticket-grid">
        @foreach($tickets as $ticket)
            @php
                $hasPending = $ticket->details->where('status', 'pending')->count();
                $hasCooking = $ticket->details->where('status', 'in_progress')->count();
                $headerClass = $hasCooking > 0 ? 'cooking' : 'pending';
            @endphp
            <div class="ticket">
                <div class="ticket-header {{ $headerClass }}">
                    <div class="order-info">
                        <span class="order-number">🧾 {{ $ticket->customer_name ?: 'Order #' . ($ticket->bill_number ?? $ticket->id) }}</span>
                        <span class="order-type {{ !$ticket->table ? 'takeaway' : '' }}">
                            @if($ticket->table)
                                🍽️ DINE IN : Meja {{ $ticket->table->table_number }}
                            @else
                                🛍️ TAKEAWAY
                            @endif
                        </span>
                    </div>
                    <span class="timestamp">⏰ {{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                <div class="ticket-body">
                    @foreach($ticket->details as $d)
                        <div class="ticket-item">
                            <div class="item-info">
                                <div class="name">
                                    <span class="qty">{{ $d->qty }}×</span>
                                    {{ $d->product->name }}
                                    @if($d->variant)
                                        <span class="variant">({{ $d->variant->variant_name }})</span>
                                    @endif
                                </div>
                                @foreach($d->addons as $a)
                                    <div class="detail">+ {{ $a->addon->addon_name }}</div>
                                @endforeach
                                @if($d->notes)
                                    <div class="notes">📝 {{ $d->notes }}</div>
                                @endif
                            </div>
                            <div class="item-actions">
                                @if($d->status === 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                    <form action="{{ route('kitchen.updateStatus', $d) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-warning btn-xs">🔥 Masak</button>
                                    </form>
                                @elseif($d->status === 'in_progress')
                                    <span class="status-badge status-cooking">🔥 Cooking</span>
                                    <form action="{{ route('kitchen.updateStatus', $d) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="btn btn-success btn-xs">✅ Selesai</button>
                                    </form>
                                @else
                                    <span class="status-badge status-done">✅ Done</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="ticket-footer">
                    <span class="item-count">📦 {{ $ticket->details->count() }} item</span>
                    <form action="{{ route('kitchen.markAllDone', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">✅ Selesai Semua</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="empty-kitchen">
            <div class="icon-wrapper">
                <div class="icon-ring"></div>
                <div class="icon">🍳</div>
            </div>
            <h2 data-i18n="kitchen.no_orders">Tidak Ada Pesanan</h2>
            <p data-i18n="kitchen.no_orders_desc">Santai sejenak! Pesanan baru akan muncul secara otomatis di sini.</p>
        </div>
    @endif

    <script>
    // Language Translations
    const translations = {
        id: {
            'nav.logout': '🚪 Logout',
            'kitchen.live': 'Live • Auto-refresh 15s',
            'kitchen.pending': 'Menunggu',
            'kitchen.cooking': 'Sedang Dimasak',
            'kitchen.total_orders': 'Total Order',
            'kitchen.no_orders': 'Tidak Ada Pesanan',
            'kitchen.no_orders_desc': 'Santai sejenak! Pesanan baru akan muncul secara otomatis di sini.',
            'kitchen.cook': '🔥 Masak',
            'kitchen.done': '✅ Selesai',
            'kitchen.done_all': '✅ Selesai Semua',
            'kitchen.items': 'item'
        },
        en: {
            'nav.logout': '🚪 Logout',
            'kitchen.live': 'Live • Auto-refresh 15s',
            'kitchen.pending': 'Waiting',
            'kitchen.cooking': 'Cooking',
            'kitchen.total_orders': 'Total Orders',
            'kitchen.no_orders': 'No Orders',
            'kitchen.no_orders_desc': 'Take a break! New orders will appear here automatically.',
            'kitchen.cook': '🔥 Cook',
            'kitchen.done': '✅ Done',
            'kitchen.done_all': '✅ Complete All',
            'kitchen.items': 'items'
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
            const elements = document.querySelectorAll('[data-i18n]');
            elements.forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    el.textContent = translations[lang][key];
                }
            });
        }
    });
    </script>
</body>
</html>
