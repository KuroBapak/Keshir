<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Menu — Keshir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --bg:#f8fafc; --text:#1e293b; --muted:#64748b; --card:#fff; --border:#e2e8f0; --success:#16a34a; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); padding-bottom: 80px; }
        .header { background: var(--primary); color: #fff; padding: 1rem; position: sticky; top: 0; z-index: 10; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { font-size: 1.25rem; font-weight: 700; margin: 0; }
        
        .cat-nav { display: flex; overflow-x: auto; padding: 0.75rem 1rem; background: var(--card); border-bottom: 1px solid var(--border); position: sticky; top: 56px; z-index: 9; gap: 0.5rem; scrollbar-width: none; }
        .cat-nav::-webkit-scrollbar { display: none; }
        .cat-btn { padding: 0.4rem 1rem; border-radius: 9999px; background: var(--bg); border: 1px solid var(--border); font-size: 0.85rem; font-weight: 600; white-space: nowrap; text-decoration: none; color: var(--muted); }
        .cat-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        
        .container { padding: 1rem; max-width: 600px; margin: 0 auto; }
        .section-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; margin-top: 1.5rem; }
        
        .product-card { background: var(--card); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1rem; display: flex; border: 1px solid var(--border); }
        .product-img { width: 100px; height: 100px; object-fit: cover; background: #eee; }
        .product-info { padding: 0.75rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .product-title { font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem; }
        .product-desc { font-size: 0.75rem; color: var(--muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 0.5rem; }
        .product-price { font-weight: 700; color: var(--primary); font-size: 0.9rem; }
        .add-btn { background: var(--primary); color: #fff; border: none; border-radius: 0.4rem; padding: 0.35rem 0.75rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; }
        
        .cart-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid var(--border); padding: 1rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 20; max-width: 600px; margin: 0 auto; }
        .cart-info { display: flex; flex-direction: column; }
        .cart-qty { font-size: 0.8rem; color: var(--muted); font-weight: 600; }
        .cart-total { font-weight: 800; font-size: 1.1rem; color: var(--text); }
        .cart-btn { background: var(--success); color: #fff; padding: 0.6rem 1.5rem; border-radius: 0.5rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        
        /* Modal Add Item */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: flex-end; justify-content: center; }
        .modal.show { display: flex; }
        .modal-content { background: #fff; width: 100%; max-width: 600px; border-radius: 1rem 1rem 0 0; padding: 1.5rem; max-height: 85vh; overflow-y: auto; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; }
        .variant-grid, .addon-grid { display: grid; grid-template-columns: 1fr; gap: 0.5rem; }
        .radio-card { border: 1px solid var(--border); padding: 0.75rem; border-radius: 0.5rem; display: flex; justify-content: space-between; cursor: pointer; }
        .radio-card input { margin-right: 0.5rem; }
        .qty-controls { display: flex; align-items: center; border: 1px solid var(--border); border-radius: 0.5rem; width: fit-content; }
        .qty-btn { background: transparent; border: none; width: 36px; height: 36px; font-size: 1.2rem; cursor: pointer; color: var(--primary); }
        .qty-input { width: 40px; text-align: center; border: none; font-weight: 700; }
    </style>
</head>
<body>
    <header class="header">
        <h1>Keshir Coffee & Eatery</h1>
    </header>

    <div class="cat-nav">
        <a href="#" class="cat-btn active">Semua</a>
        @foreach($categories as $cat)
            <a href="#cat-{{ $cat->id }}" class="cat-btn">{{ $cat->name }}</a>
        @endforeach
    </div>

    <div class="container">
        @if(session('error'))
            <div style="background:#fef2f2;color:#991b1b;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;">
                {{ session('error') }}
            </div>
        @endif

        @foreach($categories as $cat)
            @php $catProducts = $products->where('category_id', $cat->id); @endphp
            @if($catProducts->count() > 0)
                <h2 class="section-title" id="cat-{{ $cat->id }}">{{ $cat->name }}</h2>
                @foreach($catProducts as $p)
                    <div class="product-card" onclick='openModal(@json($p))'>
                        <div class="product-img" style="display:flex; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; width:100px; height:100px;">
                            @if(is_array($p->photos) && count($p->photos) > 0)
                                @foreach($p->photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" style="flex:0 0 100px; height:100px; object-fit:cover; scroll-snap-align:start;" alt="{{ $p->name }}">
                                @endforeach
                            @endif
                        </div>
                        <div class="product-info">
                            <div>
                                <div class="product-title">{{ $p->name }}</div>
                                <div class="product-desc">{{ $p->description ?? 'Nikmati hidangan spesial ini.' }}</div>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div class="product-price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                                <button class="add-btn">Tambah</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>

    @if($cartSummary['item_count'] > 0)
    <div class="cart-bar">
        <div class="cart-info">
            <span class="cart-qty">{{ $cartSummary['item_count'] }} Item</span>
            <span class="cart-total">Rp {{ number_format($cartSummary['subtotal'], 0, ',', '.') }}</span>
        </div>
        <a href="{{ route('public.cart') }}" class="cart-btn">
            Keranjang ➔
        </a>
    </div>
    @endif

    {{-- Add. Item Modal --}}
    <div class="modal" id="itemModal" onclick="if(event.target===this) closeModal()">
        <div class="modal-content">
            <div style="display:flex;justify-content:space-between;margin-bottom:1rem;">
                <h3 id="m-title" style="font-size:1.2rem;font-weight:800;"></h3>
                <button onclick="closeModal()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;">&times;</button>
            </div>
            
            {{-- Photo Slider Placeholder --}}
            <div id="m-slider" style="display:none; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; gap:0.5rem; margin-bottom:1.5rem; border-radius:0.75rem;">
            </div>
            
            <form id="add-form" action="{{ route('public.addToCart') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="m-pid">
                
                <div id="m-variants" class="form-group" style="display:none;">
                    <label>Pilih Varian <span style="color:var(--danger)">*</span></label>
                    <div class="variant-grid" id="m-var-list"></div>
                </div>

                <div id="m-addons" class="form-group" style="display:none;">
                    <label>Tambahan Ekstra</label>
                    <div class="addon-grid" id="m-add-list"></div>
                </div>

                <div class="form-group">
                    <label>Catatan (opsional)</label>
                    <input type="text" name="notes" class="qty-input" style="width:100%;border:1px solid var(--border);border-radius:0.4rem;padding:0.6rem;text-align:left;font-weight:400;" placeholder="Contoh: Jangan terlalu pedas">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2rem;">
                    <div class="qty-controls">
                        <button type="button" class="qty-btn" onclick="let q=document.getElementById('m-qty'); if(q.value>1)q.value--">-</button>
                        <input type="number" name="qty" id="m-qty" value="1" min="1" class="qty-input" readonly>
                        <button type="button" class="qty-btn" onclick="let q=document.getElementById('m-qty'); q.value++">+</button>
                    </div>
                    <button type="button" onclick="submitForm()" class="cart-btn" style="border:none;cursor:pointer;font-size:1rem;">+ Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(product) {
            document.getElementById('m-pid').value = product.id;
            document.getElementById('m-title').innerText = product.name;
            document.getElementById('m-qty').value = 1;
            
            // Render Photos inside modal
            const sliderDiv = document.getElementById('m-slider');
            if(product.photos && product.photos.length > 0) {
                sliderDiv.style.display = 'flex';
                sliderDiv.innerHTML = product.photos.map(p => `
                    <img src="/storage/${p}" style="flex:0 0 100%; width:100%; height:200px; object-fit:cover; scroll-snap-align:start;">
                `).join('');
            } else {
                sliderDiv.style.display = 'none';
                sliderDiv.innerHTML = '';
            }
            
            const vDiv = document.getElementById('m-variants');
            const vList = document.getElementById('m-var-list');
            if(product.variants && product.variants.length > 0) {
                vDiv.style.display = 'block';
                vList.innerHTML = product.variants.map((v, i) => `
                    <label class="radio-card">
                        <div>
                            <input type="radio" name="product_variant_id" value="${v.id}" ${i===0?'checked':''} required>
                            <span style="font-weight:600;font-size:0.9rem;">${v.variant_name}</span>
                        </div>
                        <span style="color:var(--primary);font-weight:700;">Rp ${new Intl.NumberFormat('id-ID').format(v.additional_price)}</span>
                    </label>
                `).join('');
            } else {
                vDiv.style.display = 'none';
                vList.innerHTML = '';
            }

            const aDiv = document.getElementById('m-addons');
            const aList = document.getElementById('m-add-list');
            if(product.addons && product.addons.length > 0) {
                aDiv.style.display = 'block';
                aList.innerHTML = product.addons.map(a => `
                    <label class="radio-card">
                        <div>
                            <input type="checkbox" name="addons[]" value="${a.id}">
                            <span style="font-weight:600;font-size:0.9rem;">${a.addon_name}</span>
                        </div>
                        <span style="color:var(--primary);font-weight:700;">+Rp ${new Intl.NumberFormat('id-ID').format(a.price)}</span>
                    </label>
                `).join('');
            } else {
                aDiv.style.display = 'none';
                aList.innerHTML = '';
            }

            document.getElementById('itemModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('itemModal').classList.remove('show');
        }

        function submitForm() {
            const form = document.getElementById('add-form');
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).then(r => r.json()).then(res => {
                if(res.success) window.location.reload();
            }).catch(e => console.error(e));
        }
    </script>
</body>
</html>
