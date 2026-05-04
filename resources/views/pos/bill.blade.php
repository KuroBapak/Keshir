<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $transaction->customer_name ?: 'Bill #' . ($transaction->bill_number ?? $transaction->id) }} — Keshir POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --bg: #f8fafc;
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
        
        /* Navigation */
        .pos-nav {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.25);
        }
        .pos-nav .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .pos-nav .brand-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .pos-nav h1 { font-size: 1rem; font-weight: 700; }
        .pos-nav .info { display: flex; align-items: center; gap: 0.5rem; }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-success { 
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%); 
            color: #fff;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-secondary); }
        .btn-ghost { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.25); }
        .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.8rem; }
        .btn-xs { padding: 0.3rem 0.6rem; font-size: 0.75rem; }
        
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-success { background: var(--success-bg); color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: var(--danger-bg); color: #991b1b; border: 1px solid #fecaca; }
        
        /* Layout */
        .pos-layout { display: flex; height: calc(100vh - 60px); overflow: hidden; }
        
        .pos-left {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            background: var(--bg);
        }
        
        .pos-right {
            width: 400px;
            display: flex;
            flex-direction: column;
            background: var(--card);
            border-left: 1px solid var(--border);
            box-shadow: -4px 0 20px rgba(0,0,0,0.05);
        }
        
        /* Cart */
        .cart-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.85rem;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
            background: var(--card);
            transition: all 0.2s ease;
        }
        .cart-item:hover { border-color: var(--primary-100); }
        .cart-item .name { font-weight: 700; font-size: 0.95rem; color: var(--text); }
        .cart-item .meta { font-size: 0.8rem; color: var(--muted); margin-top: 0.25rem; }
        .cart-item .notes { font-size: 0.8rem; color: var(--warning); margin-top: 0.25rem; }
        
        .cart-footer {
            padding: 1.25rem;
            border-top: 1px solid var(--border);
            background: var(--bg);
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
            color: var(--text-secondary);
        }
        .total-row.grand {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary);
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 2px solid var(--primary);
        }
        
        /* Product Grid */
        .cat-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .cat-tab {
            padding: 0.45rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--muted);
            transition: all 0.2s ease;
        }
        .cat-tab:hover { border-color: var(--primary); color: var(--primary); }
        .cat-tab.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            border-color: transparent;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
        }
        .product-card {
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--card);
        }
        .product-card:hover {
            border-color: var(--primary);
            background: var(--primary-50);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }
        .product-card .name { font-weight: 700; font-size: 0.95rem; margin-bottom: 0.35rem; color: var(--text); }
        .product-card .price { color: var(--primary); font-weight: 800; font-size: 0.95rem; }
        .product-card .variants { font-size: 0.75rem; color: var(--muted); margin-top: 0.25rem; }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-warning { background: var(--warning-bg); color: #92400e; }
        .badge-success { background: var(--success-bg); color: #065f46; }
        .badge-cooking { background: #ffedd5; color: #c2410c; }
        .badge-info { background: var(--primary-100); color: var(--primary-dark); }
        
        .form-control {
            padding: 0.6rem 0.85rem;
            border: 1.5px solid var(--border);
            border-radius: 0.5rem;
            font-size: 0.9rem;
            width: 100%;
            font-family: inherit;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-100);
        }
        select.form-control { appearance: auto; cursor: pointer; }
        
        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 200;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.show { display: flex; }
        .modal {
            background: #fff;
            border-radius: 1rem;
            padding: 1.75rem;
            width: 90%;
            max-width: 480px;
            max-height: 85vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal h3 {
            margin-bottom: 1.25rem;
            font-size: 1.15rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <nav class="pos-nav">
        <div class="brand">
            <div class="brand-icon">🧾</div>
            <h1>
                {{ $transaction->customer_name ?: 'Bill #' . ($transaction->bill_number ?? $transaction->id) }} · 
                @if($transaction->table)
                    🪑 Meja {{ $transaction->table->table_number }}
                @else
                    <span style="background:#fff;color:var(--primary);padding:0.2rem 0.6rem;border-radius:0.3rem;font-weight:900;box-shadow:0 0 10px rgba(0,0,0,0.1);">🛍️ TAKEAWAY</span>
                @endif
            </h1>
        </div>
        <div class="info">
            <a href="{{ route('pos.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            @if($transaction->payment_status === 'paid')
                <span class="badge badge-success" style="font-size:0.85rem;padding:0.4rem 0.8rem;">✅ LUNAS</span>
            @elseif($transaction->payment_status === 'void')
                <span class="badge badge-danger" style="font-size:0.85rem;padding:0.4rem 0.8rem;">❌ VOID</span>
            @else
                <form action="{{ route('pos.voidBill', $transaction) }}" method="POST" style="display:inline;" onsubmit="return confirm('YAKIN void bill ini? Semua item akan dibatalkan.')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Void Bill</button>
                </form>
            @endif
        </div>
    </nav>

    <div class="pos-layout">
        <!-- Left: Product Catalog / Paid Summary -->
        <div class="pos-left">
            @if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">❌ {{ session('error') }}</div>@endif
            @if(session('info'))<div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;padding:0.85rem 1rem;border-radius:0.75rem;margin-bottom:1rem;font-size:0.85rem;">ℹ️ {{ session('info') }}</div>@endif
            @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

            @if($transaction->payment_status === 'open')
                {{-- OPEN BILL: Show product catalog --}}
                <div class="cat-tabs">
                    <span class="cat-tab active" onclick="filterCategory('all')">Semua</span>
                    @foreach($categories as $cat)
                        <span class="cat-tab" data-cat="{{ $cat->id }}" onclick="filterCategory({{ $cat->id }})">{{ $cat->name }}</span>
                    @endforeach
                </div>

                <div class="product-grid">
                    @foreach($products as $p)
                        @php
                            $isOutOfStock = $p->is_out_of_stock;
                        @endphp
                        <div class="product-card" data-cat="{{ $p->category_id }}" {!! $isOutOfStock ? 'style="opacity: 0.6; cursor: not-allowed; position: relative;"' : 'onclick="openAddModal('.$p->id.')"' !!}>
                            <div class="name">{{ $p->name }}</div>
                            <div class="price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                            @if($p->variants->count())<div class="variants">{{ $p->variants->count() }} varian tersedia</div>@endif
                            @if($isOutOfStock)
                                <div style="position:absolute; top:8px; right:8px; background:var(--danger); color:white; padding:2px 6px; border-radius:4px; font-size:0.65rem; font-weight:bold;">Habis</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif($transaction->payment_status === 'void')
                {{-- VOID BILL: Show void status --}}
                <div style="max-width:600px;margin:0 auto;">
                    <div style="background:var(--danger-bg);border:2px solid #fca5a5;border-radius:1rem;padding:2rem;text-align:center;margin-bottom:1.5rem;">
                        <div style="font-size:3rem;margin-bottom:0.5rem;">❌</div>
                        <h2 style="font-size:1.25rem;font-weight:800;color:#991b1b;margin-bottom:0.25rem;">Pesanan Dibatalkan (Void)</h2>
                        <p style="color:#b91c1c;font-size:0.9rem;">Transaksi ini telah dibatalkan.</p>
                    </div>
                </div>
            @else
                {{-- PAID BILL: Show summary & actions --}}
                <div style="max-width:600px;margin:0 auto;">
                    <div style="background:var(--success-bg);border:2px solid #a7f3d0;border-radius:1rem;padding:2rem;text-align:center;margin-bottom:1.5rem;">
                        <div style="font-size:3rem;margin-bottom:0.5rem;">✅</div>
                        <h2 style="font-size:1.25rem;font-weight:800;color:#065f46;margin-bottom:0.25rem;">Pembayaran Lunas</h2>
                        <p style="color:#047857;font-size:0.9rem;">Metode: {{ $transaction->payment_method === 'cash' ? '💵 Cash' : '📱 Digital' }}</p>
                    </div>

                    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">📋 Status Pesanan</h3>
                    @foreach($transaction->details as $d)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:0.85rem 1rem;background:var(--card);border:1px solid var(--border);border-radius:0.75rem;margin-bottom:0.5rem;">
                            <div>
                                <div style="font-weight:600;">{{ $d->product->name }}</div>
                                <div style="font-size:0.8rem;color:var(--muted);">{{ $d->qty }}x · Rp {{ number_format($d->price * $d->qty, 0, ',', '.') }}</div>
                            </div>
                            <div>
                                @if($d->status === 'pending')<span class="badge badge-warning">⏳ Pending</span>
                                @elseif($d->status === 'in_progress')<span class="badge badge-cooking">🔥 Dimasak</span>
                                @elseif($d->status === 'done')<span class="badge badge-success">✅ Selesai</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div style="display:flex;gap:0.75rem;margin-top:1.5rem;flex-wrap:wrap;">
                        <a href="{{ route('pos.receipt', $transaction) }}" class="btn btn-primary" style="flex:1;justify-content:center;">🧾 Lihat/Cetak Nota</a>
                        <a href="{{ route('refunds.create', $transaction) }}" class="btn btn-outline" style="flex:1;justify-content:center;border-color:var(--danger);color:var(--danger);">↩️ Refund</a>
                    </div>

                    @if($transaction->table_id && $transaction->table && $transaction->table->status === 'occupied')
                        <form action="{{ route('pos.clearTable', $transaction->table) }}" method="POST" style="margin-top:0.75rem;" onsubmit="return confirm('Kosongkan meja ini? Pastikan tamu sudah selesai.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;">🪑 Kosongkan Meja {{ $transaction->table->table_number }}</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right: Cart -->
        <div class="pos-right">
            <div class="cart-header">🛒 Pesanan ({{ $transaction->details->count() }} item)</div>
            <div class="cart-items">
                @forelse($transaction->details as $d)
                    <div class="cart-item">
                        <div style="flex:1;">
                            <div class="name">{{ $d->product->name }}</div>
                            @if($d->variant)<div class="meta">📦 {{ $d->variant->variant_name }}</div>@endif
                            @foreach($d->addons as $a)<div class="meta">➕ {{ $a->addon->addon_name }}</div>@endforeach
                            @if($d->notes)<div class="notes">📝 {{ $d->notes }}</div>@endif
                            <div style="display:flex;align-items:center;gap:0.35rem;margin-top:0.5rem;">
                                @if($d->status === 'pending')<span class="badge badge-warning">⏳ Pending</span>
                                @elseif($d->status === 'in_progress')<span class="badge badge-cooking">🔥 Dimasak</span>
                                @elseif($d->status === 'done')<span class="badge badge-success">✅ Selesai</span>
                                @endif
                            </div>
                            <div style="font-size:0.85rem;margin-top:0.35rem;color:var(--muted);">
                                {{ $d->qty }}x Rp {{ number_format($d->price + $d->addons->sum('price'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.5rem;">
                            <span style="font-weight:700;color:var(--text);">Rp {{ number_format(($d->price + $d->addons->sum('price')) * $d->qty, 0, ',', '.') }}</span>
                            @if($transaction->payment_status === 'open')
                                <form action="{{ route('pos.removeItem', [$transaction, $d->id]) }}" method="POST" onsubmit="return confirm('Hapus item?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">🗑️</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;color:var(--muted);padding:3rem 1rem;">
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;opacity:0.5;">🛒</div>
                        <p>Belum ada item. Klik produk untuk menambah.</p>
                    </div>
                @endforelse
            </div>
            <div class="cart-footer">
                <div class="total-row"><span>Subtotal</span><span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span></div>
                @if($transaction->discount_total > 0)
                <div class="total-row" style="color:var(--danger);"><span>Diskon</span><span>- Rp {{ number_format($transaction->discount_total, 0, ',', '.') }}</span></div>@endif
                @if($transaction->tax_total > 0)
                <div class="total-row"><span>Pajak</span><span>Rp {{ number_format($transaction->tax_total, 0, ',', '.') }}</span></div>@endif
                @if($transaction->service_total > 0)
                <div class="total-row"><span>Service</span><span>Rp {{ number_format($transaction->service_total, 0, ',', '.') }}</span></div>@endif
                <div class="total-row grand"><span>TOTAL</span><span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span></div>

                @if($transaction->payment_status === 'open' && $transaction->details->count() > 0)
                    @if($transaction->order_type === 'booking')
                        <div style="background:var(--warning); color:var(--white); padding:0.85rem; text-align:center; border-radius:8px; margin-top:0.75rem; font-weight:bold; font-size:0.9rem;">
                            ⏳ Menunggu Pelanggan Bayar via Midtrans
                        </div>
                    @else
                        <button class="btn btn-success" style="width:100%;margin-top:0.75rem;justify-content:center;padding:0.85rem;" onclick="document.getElementById('checkout-modal').classList.add('show')">💰 Proses Pembayaran</button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal-overlay" id="add-modal" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="modal">
            <h3 id="modal-product-name">➕ Tambah Item</h3>
            <form action="" method="POST" id="add-item-form">
                @csrf
                <input type="hidden" name="product_id" id="modal-product-id">
                <div style="margin-bottom:1rem;" id="variant-section">
                    <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Pilih Varian</label>
                    <select name="product_variant_id" class="form-control" id="variant-select">
                        <option value="">— Standar —</option>
                    </select>
                </div>
                <div style="margin-bottom:1rem;" id="addon-section">
                    <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Add-ons (Opsional)</label>
                    <div id="addon-checkboxes"></div>
                </div>
                <div style="display:flex;gap:0.75rem;margin-bottom:1rem;">
                    <div style="flex:1;">
                        <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Jumlah</label>
                        <input type="number" name="qty" value="1" min="1" class="form-control" required>
                    </div>
                </div>
                <div style="margin-bottom:1.25rem;">
                    <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Catatan</label>
                    <input type="text" name="notes" class="form-control" placeholder="Cth: Less sugar, no ice">
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">+ Tambah ke Pesanan</button>
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('add-modal').classList.remove('show')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal-overlay" id="checkout-modal" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="modal">
            <h3>💰 Pembayaran — {{ $transaction->customer_name ?: 'Bill #' . ($transaction->bill_number ?? $transaction->id) }}</h3>
            <div style="font-size:1.5rem;font-weight:800;color:var(--primary);text-align:center;margin-bottom:1.5rem;padding:1rem;background:var(--primary-50);border-radius:0.75rem;">
                Total: Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
            </div>
            <form action="{{ route('pos.checkout', $transaction) }}" method="POST">
                @csrf
                <div style="margin-bottom:1rem;">
                    <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Diskon (opsional)</label>
                    <select name="discount_id" class="form-control">
                        <option value="">— Tanpa Diskon —</option>
                        @foreach($discounts as $disc)
                            <option value="{{ $disc->id }}">{{ $disc->name }} ({{ $disc->type === 'percentage' ? $disc->value.'%' : 'Rp '.number_format($disc->value,0,',','.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Metode Pembayaran</label>
                    <select name="method" class="form-control" id="payment-method" onchange="toggleCashInput()">
                        <option value="cash">💵 Cash</option>
                        <option value="digital">📱 Digital (Midtrans)</option>
                    </select>
                </div>
                <div id="cash-input" style="margin-bottom:1rem;">
                    <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:0.4rem;">Uang Diterima (Rp)</label>
                    <input type="number" name="amount_paid" class="form-control" step="100" min="0" value="{{ ceil($transaction->grand_total / 1000) * 1000 }}">
                </div>
                <div style="display:flex;gap:0.75rem;">
                    <button type="submit" class="btn btn-success" style="flex:1;justify-content:center;padding:0.85rem;">✅ Proses Bayar</button>
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('checkout-modal').classList.remove('show')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const products = @json($products);
        const billId = {{ $transaction->id }};

        function filterCategory(catId) {
            document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
            if (catId === 'all') {
                document.querySelector('.cat-tab:first-child').classList.add('active');
                document.querySelectorAll('.product-card').forEach(c => c.style.display = '');
            } else {
                document.querySelector(`.cat-tab[data-cat="${catId}"]`).classList.add('active');
                document.querySelectorAll('.product-card').forEach(c => {
                    c.style.display = c.dataset.cat == catId ? '' : 'none';
                });
            }
        }

        function openAddModal(productId) {
            const p = products.find(x => x.id === productId);
            if (!p) return;
            document.getElementById('modal-product-name').textContent = '➕ ' + p.name;
            document.getElementById('modal-product-id').value = p.id;
            document.getElementById('add-item-form').action = `/pos/bill/${billId}/item`;

            const vs = document.getElementById('variant-select');
            vs.innerHTML = '<option value="">— Standar —</option>';
            (p.variants || []).forEach(v => {
                vs.innerHTML += `<option value="${v.id}">${v.variant_name} (+Rp ${Number(v.additional_price).toLocaleString('id')})</option>`;
            });
            document.getElementById('variant-section').style.display = p.variants?.length ? '' : 'none';

            const ac = document.getElementById('addon-checkboxes');
            ac.innerHTML = '';
            (p.addons || []).forEach(a => {
                ac.innerHTML += `<label style="display:flex;align-items:center;gap:0.5rem;padding:0.5rem;border:1px solid var(--border);border-radius:0.5rem;margin-bottom:0.35rem;cursor:pointer;"><input type="checkbox" name="addon_ids[]" value="${a.id}"> <span>${a.addon_name}</span> <span style="margin-left:auto;color:var(--primary);font-weight:600;">+Rp ${Number(a.price).toLocaleString('id')}</span></label>`;
            });
            document.getElementById('addon-section').style.display = p.addons?.length ? '' : 'none';

            document.getElementById('add-modal').classList.add('show');
        }

        function toggleCashInput() {
            document.getElementById('cash-input').style.display = document.getElementById('payment-method').value === 'cash' ? '' : 'none';
        }
    </script>
</body>
</html>
