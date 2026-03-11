<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja — Keshir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --bg:#f8fafc; --text:#1e293b; --muted:#64748b; --card:#fff; --border:#e2e8f0; --success:#16a34a; --danger:#dc2626; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); padding-bottom: 90px; }
        .header { background: #fff; border-bottom: 1px solid var(--border); padding: 1rem; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 1rem; }
        .header h1 { font-size: 1.15rem; font-weight: 700; margin: 0; flex: 1; }
        .back-btn { text-decoration: none; color: var(--muted); font-size: 1.2rem; }
        
        .container { padding: 1rem; max-width: 600px; margin: 0 auto; }
        
        .cart-item { background: var(--card); border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: flex-start; }
        .item-info { flex: 1; }
        .item-name { font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem; }
        .item-meta { font-size: 0.8rem; color: var(--muted); margin-bottom: 0.5rem; line-height: 1.4; }
        .item-price { font-weight: 700; color: var(--primary); }
        
        .del-btn { background: #fef2f2; color: var(--danger); border: none; width: 32px; height: 32px; border-radius: 0.4rem; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        .summary-card { background: var(--card); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-top: 1.5rem; }
        .summary-title { font-weight: 700; font-size: 1rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; color: var(--muted); }
        .summary-row.total { font-weight: 800; font-size: 1.15rem; color: var(--text); margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed var(--border); }
        
        .checkout-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 1rem; border-top: 1px solid var(--border); box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 20; max-width: 600px; margin: 0 auto; }
        .checkout-btn { display: block; width: 100%; text-align: center; background: var(--primary); color: #fff; padding: 0.8rem; border-radius: 0.5rem; font-weight: 700; text-decoration: none; font-size: 1rem; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('public.menu') }}" class="back-btn">←</a>
        <h1>Keranjang Belanja</h1>
    </header>

    <div class="container">
        @if(session('success'))
            <div style="background:#dcfce7;color:#166534;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;">
                {{ session('success') }}
            </div>
        @endif

        @forelse($cart as $id => $item)
            <div class="cart-item">
                <div class="item-info">
                    <div class="item-name">{{ $item['qty'] }}x {{ $item['product_name'] }}</div>
                    <div class="item-meta">
                        @if($item['variant_name']) <div>Varian: {{ $item['variant_name'] }}</div> @endif
                        @if(count($item['addons']) > 0)
                            <div>Ekstra: {{ implode(', ', array_column($item['addons'], 'name')) }}</div>
                        @endif
                        @if($item['notes']) <div>📝 {{ $item['notes'] }}</div> @endif
                    </div>
                    <div class="item-price">Rp {{ number_format($item['price'], 0, ',', '.') }} <span style="font-weight:400;font-size:0.8rem;color:var(--muted)">/item</span></div>
                </div>
                <div>
                    <div style="font-weight:800;text-align:right;margin-bottom:0.75rem;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                    <form action="{{ route('public.removeFromCart') }}" method="POST" style="text-align:right;">
                        @csrf
                        <input type="hidden" name="cart_item_id" value="{{ $id }}">
                        <button type="submit" class="del-btn" style="margin-left:auto;">✕</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size:3rem;margin-bottom:1rem;">🛒</div>
                <h3 style="margin-bottom:0.5rem;font-size:1.1rem;">Keranjang Anda Kosong</h3>
                <p style="font-size:0.9rem;margin-bottom:1.5rem;">Yuk, lihat menu lezat kami dan mulai pesan sekarang!</p>
                <a href="{{ route('public.menu') }}" style="display:inline-block;background:var(--primary);color:#fff;padding:0.6rem 1.5rem;border-radius:9999px;text-decoration:none;font-weight:600;font-size:0.9rem;">Lihat Menu</a>
            </div>
        @endforelse

        @if(count($cart) > 0)
            <div class="summary-card">
                <div class="summary-title">Ringkasan Pesanan</div>
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
        <a href="{{ route('public.checkout') }}" class="checkout-btn">Lanjut ke Pembayaran</a>
    </div>
    @endif
</body>
</html>
