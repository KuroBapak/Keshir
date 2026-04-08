<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja — Keshir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --bg: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --card: #ffffff;
            --border: #e2e8f0;
            --success: #10b981;
            --success-bg: #d1fae5;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            padding-bottom: 100px;
            font-size: 14px;
        }
        
        /* Header */
        .header {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .back-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            border-radius: 12px;
            text-decoration: none;
            color: var(--muted);
            font-size: 1.25rem;
            transition: all 0.2s ease;
        }
        .back-btn:hover {
            background: var(--primary-100);
            color: var(--primary);
        }
        .header h1 {
            font-size: 1.15rem;
            font-weight: 700;
            flex: 1;
        }
        
        .container { padding: 1rem; max-width: 640px; margin: 0 auto; }
        
        /* Alert */
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-success { background: var(--success-bg); color: #065f46; border: 1px solid #a7f3d0; }
        
        /* Cart Item */
        .cart-item {
            background: var(--card);
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 0.85rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }
        .cart-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .item-info { flex: 1; }
        .item-name {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.35rem;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .item-qty {
            background: var(--primary);
            color: #fff;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .item-meta {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.5;
        }
        .item-meta div { margin-bottom: 0.15rem; }
        .item-price {
            font-weight: 700;
            color: var(--primary);
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
        .item-price span {
            font-weight: 400;
            font-size: 0.8rem;
            color: var(--muted);
        }
        
        .item-right {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.75rem;
        }
        .item-subtotal {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text);
        }
        .del-btn {
            background: var(--danger-bg);
            color: var(--danger);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        .del-btn:hover {
            background: var(--danger);
            color: #fff;
            transform: scale(1.05);
        }
        
        /* Summary Card */
        .summary-card {
            background: var(--card);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-top: 1.5rem;
            border: 1px solid var(--border);
        }
        .summary-title {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.65rem;
            font-size: 0.95rem;
            color: var(--muted);
        }
        .summary-row.total {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--text);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed var(--border);
        }
        
        /* Checkout Bar */
        .checkout-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            z-index: 20;
            max-width: 640px;
            margin: 0 auto;
        }
        .checkout-btn {
            display: block;
            width: 100%;
            text-align: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 1.5rem;
            color: var(--muted);
        }
        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .empty-state h3 {
            font-size: 1.25rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .empty-state p {
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        .empty-state a {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }
        .empty-state a:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('public.menu') }}" class="back-btn">←</a>
        <h1>🛒 Keranjang Belanja</h1>
    </header>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif

        @forelse($cart as $id => $item)
            <div class="cart-item">
                <div class="item-info">
                    <div class="item-name">
                        <span class="item-qty">{{ $item['qty'] }}x</span>
                        {{ $item['product_name'] }}
                    </div>
                    <div class="item-meta">
                        @if($item['variant_name']) <div>📦 Varian: {{ $item['variant_name'] }}</div> @endif
                        @if(count($item['addons']) > 0)
                            <div>➕ Ekstra: {{ implode(', ', array_column($item['addons'], 'name')) }}</div>
                        @endif
                        @if($item['notes']) <div>📝 {{ $item['notes'] }}</div> @endif
                    </div>
                    <div class="item-price">
                        Rp {{ number_format($item['price'], 0, ',', '.') }} <span>/item</span>
                    </div>
                </div>
                <div class="item-right">
                    <div class="item-subtotal">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                    <form action="{{ route('public.removeFromCart') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cart_item_id" value="{{ $id }}">
                        <button type="submit" class="del-btn">🗑️</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="icon">🛒</div>
                <h3>Keranjang Anda Kosong</h3>
                <p>Yuk, lihat menu lezat kami dan mulai pesan sekarang!</p>
                <a href="{{ route('public.menu') }}">Lihat Menu</a>
            </div>
        @endforelse

        @if(count($cart) > 0)
            <div class="summary-card">
                <div class="summary-title">📋 Ringkasan Pesanan</div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($cartSummary['subtotal'], 0, ',', '.') }}</span>
                </div>
                @if($taxRate > 0)
                <div class="summary-row">
                    <span>Pajak ({{ $taxRate }}%)</span>
                    <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($serviceRate > 0)
                <div class="summary-row">
                    <span>Service ({{ $serviceRate }}%)</span>
                    <span>Rp {{ number_format($serviceAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span>Total Bayar</span>
                    <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif
    </div>

    @if(count($cart) > 0)
    <div class="checkout-bar">
        <a href="{{ route('public.checkout') }}" class="checkout-btn">Lanjut ke Pembayaran →</a>
    </div>
    @endif
</body>
</html>
