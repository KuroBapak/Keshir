<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kios Keshir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Load Midtrans Snap JS -->
    @php
        $isProd = env('MIDTRANS_IS_PRODUCTION', config('midtrans.is_production', false));
        $snapUrl = $isProd ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key'));
    @endphp
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-bg: #eef2ff;
            --bg-color: #f3f4f6;
            --white: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            z-index: 10;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .brand-logo {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .date-time {
            font-size: 0.9rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-bg);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.2s;
        }

        .icon-btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        /* Main Layout */
        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Left Side (Menu Content) */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            overflow-y: auto;
        }

        /* Search Bar */
        .search-bar {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem; /* increased */
            flex-shrink: 0; /* Important to prevent squashing */
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
            background: transparent;
        }

        /* Categories */
        .category-row {
            display: flex;
            gap: 1.25rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            flex-shrink: 0; /* Prevents overlap bug */
            min-height: 80px; /* guarantees space */
        }

        .category-card {
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.5rem 1.5rem 0.5rem 0.5rem;
            min-width: 180px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            height: 64px;
        }

        .category-card:hover, .category-card.active {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 4px 15px rgba(37,99,235,0.15);
        }

        .cat-img-box {
            width: 48px;
            height: 48px;
            background: var(--primary-bg); /* subtle gray/blue circle */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid var(--border-color);
        }

        .cat-img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        .cat-text {
            display: flex;
            flex-direction: column;
        }

        .cat-text .cat-name {
            font-weight: 900;
            font-size: 1.05rem;
            letter-spacing: -0.5px;
            color: var(--text-dark);
            margin-bottom: 0.1rem;
        }

        .cat-text .cat-items {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .category-card .cat-name {
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: -0.5px;
        }

        .category-card .cat-items {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .product-image {
            width: 100%;
            height: 120px;
            background: var(--bg-color);
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-name {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--text-dark);
        }

        .product-price {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .add-btn {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--text-dark);
            background: var(--white);
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-card:hover .add-btn {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        /* Right Side (Cart / Sidebar) */
        .sidebar {
            width: 400px;
            background: var(--white);
            border-left: 2px solid var(--primary);
            display: flex;
            flex-direction: column;
            margin: 1.5rem 1.5rem 1.5rem 0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px dashed var(--border-color);
        }

        .sidebar-header h2 {
            font-weight: 900;
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .order-toggle {
            display: flex;
            background: var(--bg-color);
            border-radius: 50px;
            padding: 0.25rem;
            margin: 1rem 1.5rem;
        }

        .order-type-btn {
            flex: 1;
            padding: 0.5rem;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.85rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .order-type-btn.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }

        .customer-form {
            padding: 0 1.5rem 1rem;
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 1rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.85rem;
            outline: none;
            background: var(--white);
        }

        /* Form Group Full Width for Phone */
        .form-group-full {
            grid-column: 1 / -1;
            margin-bottom: 0.5rem;
        }
        .form-group-full input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.85rem;
            outline: none;
            background: var(--white);
        }

        .cart-title {
            padding: 0 1.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 0 1.5rem;
        }

        .cart-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .cart-item-img {
            width: 50px;
            height: 50px;
            background: var(--bg-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .cart-item-price {
            font-size: 0.8rem;
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .cart-item-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            background: var(--bg-color);
            border-radius: 50px;
            padding: 0.25rem;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border: none;
            background: var(--white);
            border-radius: 50%;
            font-weight: 700;
            cursor: pointer;
            color: var(--text-dark);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .qty-input {
            width: 30px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .sidebar-footer {
            padding: 1.5rem;
            background: var(--white);
            border-top: 1px dashed var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .summary-row.total {
            font-size: 1.1rem;
            font-weight: 900;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border-color);
        }

        .place-order-btn {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(37,99,235,0.3);
        }

        .place-order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.4);
        }
        
        .place-order-btn:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* Product Modal */
        .product-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
        }
        .product-modal.show {
            display: flex;
        }
        .pm-content {
            background: var(--white);
            width: 450px;
            max-width: 90%;
            border-radius: 16px;
            padding: 1.5rem;
        }
        .pm-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .pm-header h3 { font-size: 1.25rem; font-weight: 800; }
        .pm-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); }
        .pm-group { margin-bottom: 1rem; }
        .pm-group label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; }
        .radio-grid { display: grid; gap: 0.5rem; }
        .radio-card {
            border: 1px solid var(--border-color); padding: 0.75rem; border-radius: 8px;
            display: flex; justify-content: space-between; cursor: pointer;
        }
        .radio-card input { margin-right: 0.5rem; }
        .pm-notes { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; }
        .pm-submit { width: 100%; padding: 1rem; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 800; cursor: pointer; margin-top: 1rem; }

        /* Payment Choice Modal */
        .payment-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .payment-modal.show {
            display: flex;
        }

        .payment-content {
            display: flex;
            gap: 1.5rem;
            position: relative;
        }

        .payment-btn {
            background: var(--white);
            width: 200px;
            height: 250px;
            border-radius: 16px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--text-dark);
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }

        .payment-btn:hover {
            transform: scale(1.05);
            color: var(--primary);
        }
        
        .d-none { display: none !important; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="top-header">
        <div class="header-left">
            <div class="brand-logo">KESHIR</div>
            <div class="date-time" id="datetime">Memuat waktu...</div>
        </div>
        <div class="header-right">
            <button class="icon-btn">🔔</button>
            <button class="icon-btn">👤</button>
        </div>
    </header>

    <div class="main-container">
        <!-- Left Area (Menu) -->
        <div class="content-area">
            <div class="search-bar">
                <span>🔍</span>
                <input type="text" id="searchInput" placeholder="Search menu...">
            </div>

            <!-- Categories -->
            <div class="category-row">
                <!-- Hardcoded KOPI -->
                <div class="category-card active" onclick="filterCategoryByText('kopi')" id="cat-tab-kopi">
                    <div class="cat-img-box">
                        <img src="{{ asset('images/Coffe.png') }}" class="cat-img" alt="Kopi">
                    </div>
                    <div class="cat-text">
                        <div class="cat-name">KOPI</div>
                        <div class="cat-items">{{ $products->filter(function($p) { return str_contains(strtolower($p->name), 'kopi') || str_contains(strtolower($p->name), 'latte') || str_contains(strtolower($p->name), 'americano') || str_contains(strtolower($p->name), 'espresso'); })->count() }} items</div>
                    </div>
                </div>

                <!-- Hardcoded TEH -->
                <div class="category-card" onclick="filterCategoryByText('teh')" id="cat-tab-teh">
                    <div class="cat-img-box">
                        <img src="{{ asset('images/Tea.png') }}" class="cat-img" alt="Teh">
                    </div>
                    <div class="cat-text">
                        <div class="cat-name">TEH</div>
                        <div class="cat-items">{{ $products->filter(function($p) { return str_contains(strtolower($p->name), 'teh') || str_contains(strtolower($p->name), 'tea') || str_contains(strtolower($p->name), 'matcha'); })->count() }} items</div>
                    </div>
                </div>

                <!-- Hardcoded SNACK -->
                <div class="category-card" onclick="filterCategoryByText('snack')" id="cat-tab-snack">
                    <div class="cat-img-box">
                        <img src="{{ asset('images/Snack.png') }}" class="cat-img" alt="Snack">
                    </div>
                    <div class="cat-text">
                        <div class="cat-name">SNACK</div>
                        <div class="cat-items">{{ $products->filter(function($p) { return str_contains(strtolower($p->name), 'snack') || str_contains(strtolower($p->name), 'kue') || str_contains(strtolower($p->name), 'roti'); })->count() }} items</div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="product-grid" id="productGrid">
                @foreach($products as $p)
                <div class="product-card" data-cat="{{ $p->category_id }}" data-name="{{ strtolower($p->name) }}" onclick='openProductModal(@json($p))'>
                    <div class="product-image">
                        @if(is_array($p->photos) && count($p->photos) > 0)
                            <img src="{{ asset('storage/' . $p->photos[0]) }}" alt="{{ $p->name }}">
                        @else
                            <span style="font-size:3rem; opacity:0.1">🍽️</span>
                        @endif
                    </div>
                    <div class="product-name">{{ $p->name }}</div>
                    <div class="product-price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                    <button class="add-btn">+</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right Area (Sidebar / Cart) -->
        <div class="sidebar">
            <form id="checkoutForm" onsubmit="event.preventDefault(); openPaymentModal();">
                <div class="sidebar-header">
                    <h2>Struk Belanja</h2>
                    <p>#{{ strtoupper(substr(uniqid(), -8)) }}</p>
                </div>

                <div class="order-toggle">
                    <input type="hidden" id="order_type" name="order_type" value="dine_in">
                    <button type="button" class="order-type-btn active" id="btnDineIn" onclick="setOrderType('dine_in')">Dine In</button>
                    <button type="button" class="order-type-btn" id="btnTakeAway" onclick="setOrderType('takeaway')">Take Away</button>
                </div>

                <div class="customer-form">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Nama..." required>
                    </div>
                    <div class="form-group" id="mejaGroup">
                        <label>Meja</label>
                        <select name="table_id" id="table_id" required>
                            <option value="">Pilih...</option>
                            @foreach($tables as $t)
                                <option value="{{ $t->id }}">{{ $t->table_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Required for Midtrans -->
                    <div class="form-group-full">
                        <input type="tel" name="phone" id="phone" placeholder="No HP Pelanggan" required>
                    </div>
                    <!-- Hidden fields for process -->
                    <input type="hidden" name="people_count" value="1">
                    <input type="hidden" name="payment_method" id="payment_method" value="">
                </div>

                <div class="cart-title">List Pesanan</div>
                <div class="cart-items" id="cartContainer">
                    @forelse($cart as $id => $item)
                    <div class="cart-item">
                        <div class="cart-item-img">
                            @if(isset($item['image']) && $item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="IMG">
                            @else
                                <span style="font-size:1.5rem">🍽️</span>
                            @endif
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">{{ $item['product_name'] ?? 'Unknown Product' }}</div>
                            <div class="cart-item-price">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</div>
                            @if(!empty($item['variant_name']))
                                <div class="cart-item-meta">☑ {{ $item['variant_name'] }}</div>
                            @endif
                            @if(!empty($item['addons']))
                                @foreach($item['addons'] as $addon)
                                    <div class="cart-item-meta">+ {{ $addon['name'] }}</div>
                                @endforeach
                            @endif
                        </div>
                        <div class="qty-control">
                            <button type="button" class="qty-btn" style="color:var(--danger)" onclick="updateCartQty('{{ $id }}', {{ $item['qty'] - 1 }})">−</button>
                            <input type="text" class="qty-input" value="{{ $item['qty'] }}" readonly>
                            <button type="button" class="qty-btn" style="color:var(--primary)" onclick="updateCartQty('{{ $id }}', {{ $item['qty'] + 1 }})">+</button>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem;">
                        Belum ada pesanan.<br>Pilih menu di sebelah kiri.
                    </div>
                    @endforelse
                </div>

                <div class="sidebar-footer">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($cartSummary['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Pajak - {{ $taxRate }}%</span>
                        <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <button type="submit" class="place-order-btn" {{ empty($cart) ? 'disabled' : '' }}>
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Choice Modal -->
    <div class="payment-modal" id="paymentModal" onclick="if(event.target===this) closePaymentModal()">
        <div style="position: absolute; top: 20px; right: 20px;">
            <button onclick="closePaymentModal()" style="background:var(--danger); color:white; border:none; padding: 0.5rem 1rem; border-radius:8px; cursor:pointer; font-weight:bold;">Tutup</button>
        </div>
        <div class="payment-content">
            <button class="payment-btn" onclick="submitCheckout('tunai')">TUNAI</button>
            <button class="payment-btn" onclick="submitCheckout('digital')" id="btnMidtrans">MIDTRANS</button>
        </div>
    </div>

    <!-- Product Add Modal -->
    <div class="product-modal" id="productModal" onclick="if(event.target===this) closeProductModal()">
        <div class="pm-content">
            <div class="pm-header">
                <h3 id="pm-title">Product</h3>
                <button class="pm-close" onclick="closeProductModal()">×</button>
            </div>
            
            <form id="addToCartForm" action="{{ route('public.addToCart') }}">
                <input type="hidden" name="product_id" id="pm-pid">
                
                <div class="pm-group" id="pm-v-group" style="display:none">
                    <label>Varian (Wajib)</label>
                    <div class="radio-grid" id="pm-variants"></div>
                </div>
                
                <div class="pm-group" id="pm-a-group" style="display:none">
                    <label>Addons (Opsional)</label>
                    <div class="radio-grid" id="pm-addons"></div>
                </div>

                <div class="pm-group">
                    <label>Catatan</label>
                    <input type="text" name="notes" class="pm-notes" placeholder="Contoh: Kurangi gula...">
                </div>

                <div class="pm-group" style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem;">
                    <div style="display:flex; align-items:center; gap:0.5rem; background:var(--bg-color); border-radius:50px; padding:0.25rem;">
                        <button type="button" style="width:36px;height:36px;border-radius:50%;border:none;font-weight:bold;cursor:pointer;" onclick="let q=document.getElementById('pm-qty'); if(q.value>1)q.value--">−</button>
                        <input type="number" name="qty" id="pm-qty" value="1" min="1" style="width:40px;text-align:center;border:none;background:transparent;font-weight:bold;" readonly>
                        <button type="button" style="width:36px;height:36px;border-radius:50%;border:none;font-weight:bold;cursor:pointer;" onclick="let q=document.getElementById('pm-qty'); q.value++">+</button>
                    </div>
                    <button type="button" class="pm-submit" style="width:auto; margin-top:0;" onclick="submitAddToCart()">Tambah ke Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit form handler & Scripts -->
    <script>
        // Update Time
        function updateTime() {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            document.getElementById('datetime').innerText = 
                `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Search Handlers
        document.getElementById('searchInput').addEventListener('input', function(e) {
            let term = e.target.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                if(card.getAttribute('data-name').includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Category Filter
        function filterCategory(catId) {
            document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
            if(catId === 'all') {
                document.querySelector('.category-card').classList.add('active'); // First child is 'all'
                document.querySelectorAll('.product-card').forEach(c => c.style.display = 'block');
            } else {
                document.getElementById('cat-tab-' + catId).classList.add('active');
                document.querySelectorAll('.product-card').forEach(c => {
                    c.style.display = c.getAttribute('data-cat') == catId ? 'block' : 'none';
                });
            }
        }

        // Hardcoded category filtering when database categories are not set up
        function filterCategoryByText(type) {
            document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
            document.getElementById('cat-tab-' + type).classList.add('active');
            
            document.querySelectorAll('.product-card').forEach(c => {
                let name = c.getAttribute('data-name').toLowerCase();
                let match = false;
                if (type === 'kopi' && (name.includes('kopi') || name.includes('coffee') || name.includes('latte') || name.includes('espresso') || name.includes('americano') || name.includes('cappuccino') || name.includes('mocha'))) {
                    match = true;
                } else if (type === 'teh' && (name.includes('teh') || name.includes('tea') || name.includes('matcha') || name.includes('oolong') || name.includes('chamomile'))) {
                    match = true;
                } else if (type === 'snack' && (name.includes('snack') || name.includes('kue') || name.includes('roti') || name.includes('fries') || name.includes('kentang') || name.includes('croissant') || name.includes('pastry') || name.includes('cake') || name.includes('tart'))) {
                    match = true;
                }
                c.style.display = match ? 'block' : 'none';
            });
        }

        // Toggle Dine In / Take Away
        function setOrderType(type) {
            document.getElementById('order_type').value = type;
            const btnDineIn = document.getElementById('btnDineIn');
            const btnTakeAway = document.getElementById('btnTakeAway');
            const mejaGroup = document.getElementById('mejaGroup');
            const tableId = document.getElementById('table_id');
            
            if (type === 'dine_in') {
                btnDineIn.classList.add('active');
                btnTakeAway.classList.remove('active');
                mejaGroup.style.display = 'block';
                tableId.required = true;
            } else {
                btnTakeAway.classList.add('active');
                btnDineIn.classList.remove('active');
                mejaGroup.style.display = 'none';
                tableId.required = false;
                tableId.value = '';
            }
        }

        // Add Product Modal
        function openProductModal(product) {
            document.getElementById('pm-title').innerText = product.name;
            document.getElementById('pm-pid').value = product.id;
            document.getElementById('pm-qty').value = 1;

            const vGroup = document.getElementById('pm-v-group');
            const vGrid = document.getElementById('pm-variants');
            if(product.variants && product.variants.length > 0) {
                vGroup.style.display = 'block';
                vGrid.innerHTML = product.variants.map((v, i) => `
                    <label class="radio-card">
                        <div><input type="radio" name="product_variant_id" value="${v.id}" ${i===0?'checked':''}> <span style="font-weight:600">${v.variant_name}</span></div>
                        <span style="color:var(--primary); font-weight:bold">+Rp ${new Intl.NumberFormat('id-ID').format(v.additional_price)}</span>
                    </label>
                `).join('');
            } else {
                vGroup.style.display = 'none';
                vGrid.innerHTML = '';
            }

            const aGroup = document.getElementById('pm-a-group');
            const aGrid = document.getElementById('pm-addons');
            if(product.addons && product.addons.length > 0) {
                aGroup.style.display = 'block';
                aGrid.innerHTML = product.addons.map(a => `
                    <label class="radio-card">
                        <div><input type="checkbox" name="addons[]" value="${a.id}"> <span style="font-weight:600">${a.addon_name}</span></div>
                        <span style="color:var(--primary); font-weight:bold">+Rp ${new Intl.NumberFormat('id-ID').format(a.price)}</span>
                    </label>
                `).join('');
            } else {
                aGroup.style.display = 'none';
                aGrid.innerHTML = '';
            }

            document.getElementById('productModal').classList.add('show');
        }

        function closeProductModal() { document.getElementById('productModal').classList.remove('show'); }

        function submitAddToCart() {
            const form = document.getElementById('addToCartForm');
            const formData = new FormData(form);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload(); // Simple reload to update cart UI instead of complex DOM diffing
                }
            })
            .catch(err => console.error(err));
        }

        // Handle quantity update (+/-)
        function updateCartQty(cartId, newQty) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            
            if (newQty <= 0) {
                // If qty becomes 0, remove item
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('cart_item_id', cartId);
                fetch("{{ route('public.removeFromCart') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                }).then(() => window.location.reload());
            } else {
                // Otherwise update item qty
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('cart_item_id', cartId);
                formData.append('qty', newQty);
                fetch("{{ route('public.updateCart') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                }).then(() => window.location.reload());
            }
        }

        function handleRemove(e, form) {
            // let normal submission happen
            return true;
        }

        // Checkout Modals
        function openPaymentModal() {
            // Check HTML5 validity first
            if (!document.getElementById('checkoutForm').checkValidity()) {
                document.getElementById('checkoutForm').reportValidity();
                return;
            }
            document.getElementById('paymentModal').classList.add('show');
        }
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('show');
        }

        function submitCheckout(paymentMethod) {
            document.getElementById('payment_method').value = paymentMethod;
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('payment_method', paymentMethod); // Custom field for future backend processing
            
            closePaymentModal();
            
            if (paymentMethod === 'tunai') {
                // To keep it simple without changing CheckoutController significantly, we just submit.
                // The CheckoutController currently requests midtrans. Since user requested Tunai logic 
                // in the image, we can just POST and catch the SnapToken, but since it's tunai, just redirect to order-status.
                formData.append('is_cash', 'true');
            }

            const btnMidtrans = document.getElementById('btnMidtrans');
            btnMidtrans.innerText = "Membuka...";
            btnMidtrans.disabled = true;

            fetch('{{ route("public.checkout.process") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (paymentMethod === 'tunai' && res.redirect_url) {
                     window.location.href = res.redirect_url;
                     return;
                }

                if(res.status === 'success' && res.snap_token) {
                    // Midtrans Mode
                    snap.pay(res.snap_token, {
                        onSuccess: function(result){ window.location.href = res.redirect_url; },
                        onPending: function(result){ window.location.href = res.redirect_url; },
                        onError: function(result){ alert('Pembayaran gagal.'); window.location.reload(); },
                        onClose: function(){
                            btnMidtrans.innerText = "MIDTRANS";
                            btnMidtrans.disabled = false;
                        }
                    });
                } else if(res.status === 'success' && res.redirect_url) {
                    // Bypass mode if backend implemented it
                    window.location.href = res.redirect_url;
                } else {
                    alert(res.message || 'Terjadi kesalahan.');
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan sistem.');
                window.location.reload();
            });
        }
    </script>
</body>
</html>
