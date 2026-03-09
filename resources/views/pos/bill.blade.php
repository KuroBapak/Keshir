<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill #{{ $transaction->bill_number ?? $transaction->id }} — Keshir POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --primary-dark:#1d4ed8; --bg:#f1f5f9; --card:#fff; --text:#1e293b; --muted:#64748b; --border:#e2e8f0; --success:#16a34a; --danger:#dc2626; --warning:#f59e0b; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); }
        .pos-nav { background:var(--primary); color:#fff; padding:0.6rem 1.25rem; display:flex; justify-content:space-between; align-items:center; }
        .pos-nav h1 { font-size:1.1rem; font-weight:700; }
        .pos-nav .info { display:flex; align-items:center; gap:0.6rem; font-size:0.8rem; }
        .btn { padding:0.45rem 0.85rem; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.8rem; font-weight:600; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem; }
        .btn-primary { background:var(--primary); color:#fff; } .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:#fff; }
        .btn-danger { background:var(--danger); color:#fff; }
        .btn-outline { background:transparent; border:1px solid var(--border); color:var(--text); }
        .btn-sm { padding:0.3rem 0.6rem; font-size:0.75rem; }
        .btn-xs { padding:0.2rem 0.45rem; font-size:0.7rem; }
        .alert { padding:0.6rem 0.85rem; border-radius:0.4rem; margin-bottom:0.75rem; font-size:0.8rem; }
        .alert-success { background:#dcfce7; color:#166534; }
        .alert-error { background:#fef2f2; color:#991b1b; }
        .pos-layout { display:flex; height:calc(100vh - 48px); overflow:hidden; }
        .pos-left { flex:1; overflow-y:auto; padding:1rem; }
        .pos-right { width:380px; display:flex; flex-direction:column; background:var(--card); border-left:1px solid var(--border); }
        .cart-header { padding:0.75rem 1rem; border-bottom:1px solid var(--border); font-weight:700; font-size:0.9rem; }
        .cart-items { flex:1; overflow-y:auto; padding:0.75rem; }
        .cart-item { display:flex; justify-content:space-between; align-items:flex-start; padding:0.5rem 0; border-bottom:1px solid var(--border); font-size:0.82rem; }
        .cart-item:last-child { border-bottom:none; }
        .cart-footer { padding:0.75rem 1rem; border-top:1px solid var(--border); background:#f8fafc; }
        .total-row { display:flex; justify-content:space-between; font-size:0.82rem; margin-bottom:0.25rem; }
        .total-row.grand { font-size:1rem; font-weight:700; color:var(--primary); margin-top:0.5rem; padding-top:0.5rem; border-top:2px solid var(--primary); }
        .card { background:var(--card); border-radius:0.6rem; box-shadow:0 1px 3px rgba(0,0,0,0.06); padding:1rem; margin-bottom:0.75rem; }
        .product-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:0.6rem; }
        .product-card { border:1px solid var(--border); border-radius:0.5rem; padding:0.65rem; cursor:pointer; transition:all 0.15s; font-size:0.82rem; }
        .product-card:hover { border-color:var(--primary); background:#f8faff; box-shadow:0 2px 8px rgba(37,99,235,0.1); }
        .product-card .name { font-weight:600; margin-bottom:0.2rem; }
        .product-card .price { color:var(--primary); font-weight:700; font-size:0.85rem; }
        .form-control { padding:0.45rem 0.6rem; border:1px solid var(--border); border-radius:0.4rem; font-size:0.82rem; width:100%; }
        .form-control:focus { outline:none; border-color:var(--primary); }
        select.form-control { appearance:auto; }
        .badge { display:inline-block; padding:0.12rem 0.45rem; border-radius:9999px; font-size:0.68rem; font-weight:600; }
        .badge-warning { background:#fef3c7; color:#92400e; }
        .badge-success { background:#dcfce7; color:#166534; }
        .badge-info { background:#dbeafe; color:#1e40af; }
        .cat-tabs { display:flex; gap:0.3rem; flex-wrap:wrap; margin-bottom:0.75rem; }
        .cat-tab { padding:0.3rem 0.7rem; border-radius:9999px; font-size:0.75rem; font-weight:600; cursor:pointer; border:1px solid var(--border); background:#fff; color:var(--muted); transition:all 0.15s; }
        .cat-tab.active, .cat-tab:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
        .modal-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center; }
        .modal-overlay.show { display:flex; }
        .modal { background:#fff; border-radius:0.75rem; padding:1.5rem; width:90%; max-width:450px; max-height:80vh; overflow-y:auto; }
        .modal h3 { margin-bottom:1rem; font-size:1rem; }
    </style>
</head>
<body>
    <nav class="pos-nav">
        <h1>🧾 Bill #{{ $transaction->bill_number ?? $transaction->id }} · {{ $transaction->table ? $transaction->table->table_number : 'Takeaway' }}</h1>
        <div class="info">
            <a href="{{ route('pos.index') }}" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">← Kembali</a>
            <form action="{{ route('pos.voidBill', $transaction) }}" method="POST" style="display:inline;" onsubmit="return confirm('YAKIN void bill ini? Semua item akan dibatalkan.')">
                @csrf
                <button type="submit" class="btn btn-xs btn-danger">🗑️ Void Bill</button>
            </form>
        </div>
    </nav>

    <div class="pos-layout">
        {{-- Left: Product Catalog --}}
        <div class="pos-left">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

            <div class="cat-tabs">
                <span class="cat-tab active" onclick="filterCategory('all')">Semua</span>
                @foreach($categories as $cat)
                    <span class="cat-tab" data-cat="{{ $cat->id }}" onclick="filterCategory({{ $cat->id }})">{{ $cat->name }}</span>
                @endforeach
            </div>

            <div class="product-grid">
                @foreach($products as $p)
                    <div class="product-card" data-cat="{{ $p->category_id }}" onclick="openAddModal({{ $p->id }})">
                        <div class="name">{{ $p->name }}</div>
                        <div class="price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                        @if($p->variants->count())<div style="font-size:0.7rem;color:var(--muted);margin-top:0.15rem;">{{ $p->variants->count() }} varian</div>@endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right: Cart --}}
        <div class="pos-right">
            <div class="cart-header">🛒 Pesanan ({{ $transaction->details->count() }} item)</div>
            <div class="cart-items">
                @forelse($transaction->details as $d)
                    <div class="cart-item">
                        <div style="flex:1;">
                            <div style="font-weight:600;">{{ $d->product->name }}</div>
                            @if($d->variant)<div style="font-size:0.75rem;color:var(--muted);">Varian: {{ $d->variant->variant_name }}</div>@endif
                            @foreach($d->addons as $a)<div style="font-size:0.75rem;color:var(--muted);">+ {{ $a->addon->addon_name }}</div>@endforeach
                            @if($d->notes)<div style="font-size:0.72rem;color:var(--warning);font-style:italic;">📝 {{ $d->notes }}</div>@endif
                            <div style="display:flex;align-items:center;gap:0.35rem;margin-top:0.15rem;">
                                @if($d->status === 'pending')<span class="badge badge-warning">⏳ Pending</span>
                                @elseif($d->status === 'in_progress')<span class="badge" style="background:#ffedd5;color:#c2410c;">🔥 Dimasak</span>
                                @elseif($d->status === 'done')<span class="badge badge-success">✅ Selesai</span>
                                @endif
                            </div>
                            <div style="font-size:0.75rem;margin-top:0.2rem;">
                                {{ $d->qty }}x Rp {{ number_format($d->price + $d->addons->sum('price'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.25rem;">
                            <span style="font-weight:600;">Rp {{ number_format(($d->price + $d->addons->sum('price')) * $d->qty, 0, ',', '.') }}</span>
                            <form action="{{ route('pos.removeItem', [$transaction, $d->id]) }}" method="POST" onsubmit="return confirm('Hapus item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger">✕</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;color:var(--muted);padding:2rem;font-size:0.85rem;">Belum ada item. Klik produk untuk menambah.</div>
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

                @if($transaction->details->count() > 0)
                    <button class="btn btn-success" style="width:100%;margin-top:0.5rem;justify-content:center;" onclick="document.getElementById('checkout-modal').classList.add('show')">💰 Bayar</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Add Item Modal --}}
    <div class="modal-overlay" id="add-modal">
        <div class="modal">
            <h3 id="modal-product-name">Tambah Item</h3>
            <form action="" method="POST" id="add-item-form">
                @csrf
                <input type="hidden" name="product_id" id="modal-product-id">
                <div style="margin-bottom:0.75rem;" id="variant-section" style="display:none;">
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Varian</label>
                    <select name="product_variant_id" class="form-control" id="variant-select">
                        <option value="">— Standar —</option>
                    </select>
                </div>
                <div style="margin-bottom:0.75rem;" id="addon-section">
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Add-ons</label>
                    <div id="addon-checkboxes"></div>
                </div>
                <div style="display:flex;gap:0.5rem;margin-bottom:0.75rem;">
                    <div style="flex:1;">
                        <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Jumlah</label>
                        <input type="number" name="qty" value="1" min="1" class="form-control" required>
                    </div>
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Catatan</label>
                    <input type="text" name="notes" class="form-control" placeholder="Cth: Less sugar, no ice">
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">+ Tambah</button>
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('add-modal').classList.remove('show')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Checkout Modal --}}
    <div class="modal-overlay" id="checkout-modal">
        <div class="modal">
            <h3>💰 Pembayaran — Bill #{{ $transaction->bill_number ?? $transaction->id }}</h3>
            <div style="font-size:1.2rem;font-weight:700;color:var(--primary);text-align:center;margin-bottom:1rem;">
                Total: Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
            </div>
            <form action="{{ route('pos.checkout', $transaction) }}" method="POST">
                @csrf
                <div style="margin-bottom:0.75rem;">
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Diskon (opsional)</label>
                    <select name="discount_id" class="form-control">
                        <option value="">— Tanpa Diskon —</option>
                        @foreach($discounts as $disc)
                            <option value="{{ $disc->id }}">{{ $disc->name }} ({{ $disc->type === 'percentage' ? $disc->value.'%' : 'Rp '.number_format($disc->value,0,',','.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Metode Pembayaran</label>
                    <select name="method" class="form-control" id="payment-method" onchange="toggleCashInput()">
                        <option value="cash">💵 Cash</option>
                        <option value="digital">📱 Digital (Midtrans)</option>
                    </select>
                </div>
                <div id="cash-input" style="margin-bottom:0.75rem;">
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:0.2rem;">Uang Diterima (Rp)</label>
                    <input type="number" name="amount_paid" class="form-control" step="100" min="0" value="{{ ceil($transaction->grand_total / 1000) * 1000 }}">
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <button type="submit" class="btn btn-success" style="flex:1;justify-content:center;">✅ Proses Bayar</button>
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

            // Variants
            const vs = document.getElementById('variant-select');
            vs.innerHTML = '<option value="">— Standar —</option>';
            (p.variants || []).forEach(v => {
                vs.innerHTML += `<option value="${v.id}">${v.variant_name} (+Rp ${Number(v.additional_price).toLocaleString('id')})</option>`;
            });
            document.getElementById('variant-section').style.display = p.variants?.length ? '' : 'none';

            // Addons
            const ac = document.getElementById('addon-checkboxes');
            ac.innerHTML = '';
            (p.addons || []).forEach(a => {
                ac.innerHTML += `<label style="display:block;font-size:0.8rem;margin-bottom:0.2rem;"><input type="checkbox" name="addon_ids[]" value="${a.id}"> ${a.addon_name} (+Rp ${Number(a.price).toLocaleString('id')})</label>`;
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
