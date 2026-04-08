<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Menu — Keshir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --bg: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --card: #ffffff;
            --border: #e2e8f0;
            --success: #10b981;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 1.25rem 1rem;
            position: sticky;
            top: 0;
            z-index: 10;
            text-align: center;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.25);
        }
        .header .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .header .brand-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .header h1 { 
            font-size: 1.25rem; 
            font-weight: 700; 
            letter-spacing: -0.025em;
        }
        
        /* Category Navigation */
        .cat-nav {
            display: flex;
            overflow-x: auto;
            padding: 0.85rem 1rem;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 68px;
            z-index: 9;
            gap: 0.5rem;
            scrollbar-width: none;
        }
        .cat-nav::-webkit-scrollbar { display: none; }
        .cat-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            background: var(--bg);
            border: 1.5px solid var(--border);
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
            text-decoration: none;
            color: var(--muted);
            transition: all 0.2s ease;
        }
        .cat-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .cat-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        
        .container { padding: 1rem; max-width: 640px; margin: 0 auto; }
        
        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1rem;
            margin-top: 1.5rem;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title:first-child { margin-top: 0.5rem; }
        
        /* Product Card */
        .product-card {
            background: var(--card);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1rem;
            display: flex;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: var(--primary-100);
        }
        .product-card:active {
            transform: translateY(0);
        }
        
        .product-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary-100) 0%, var(--primary-50) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-img .placeholder {
            font-size: 2rem;
            opacity: 0.5;
        }
        
        .product-info {
            padding: 0.85rem 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--text);
        }
        .product-desc {
            font-size: 0.8rem;
            color: var(--muted);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
        }
        .product-price {
            font-weight: 800;
            color: var(--primary);
            font-size: 1rem;
        }
        .add-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.45rem 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        .add-btn:hover {
            transform: scale(1.05);
        }
        
        /* Cart Bar */
        .cart-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid var(--border);
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            z-index: 20;
            max-width: 640px;
            margin: 0 auto;
        }
        .cart-info { display: flex; flex-direction: column; }
        .cart-qty {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 600;
        }
        .cart-total {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text);
        }
        .cart-btn {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: #fff;
            padding: 0.75rem 1.75rem;
            border-radius: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.2s ease;
        }
        .cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 100;
            align-items: flex-end;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal.show { display: flex; }
        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 640px;
            border-radius: 1.5rem 1.5rem 0 0;
            padding: 1.75rem 1.5rem 2rem;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.25rem;
        }
        .modal-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text);
        }
        .modal-close {
            background: var(--bg);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            transition: all 0.2s ease;
        }
        .modal-close:hover {
            background: var(--border);
            color: var(--text);
        }
        
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
            color: var(--text);
        }
        
        .variant-grid, .addon-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.6rem;
        }
        .radio-card {
            border: 1.5px solid var(--border);
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }
        .radio-card:hover {
            border-color: var(--primary);
            background: var(--primary-50);
        }
        .radio-card input { margin-right: 0.75rem; accent-color: var(--primary); }
        .radio-card .label-text { font-weight: 600; font-size: 0.95rem; }
        .radio-card .price-text { color: var(--primary); font-weight: 700; }
        
        .qty-controls {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            width: fit-content;
            background: var(--bg);
        }
        .qty-btn {
            background: transparent;
            border: none;
            width: 44px;
            height: 44px;
            font-size: 1.35rem;
            cursor: pointer;
            color: var(--primary);
            font-weight: 600;
            transition: background 0.2s ease;
        }
        .qty-btn:hover { background: var(--primary-100); }
        .qty-input {
            width: 50px;
            text-align: center;
            border: none;
            font-weight: 800;
            font-size: 1.1rem;
            background: transparent;
            color: var(--text);
        }
        
        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .submit-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 0.85rem 2rem;
            border-radius: 0.75rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        
        .notes-input {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }
        .notes-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-100);
        }
        
        /* Image Slider */
        .modal-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            gap: 0;
            margin-bottom: 1.5rem;
            border-radius: 1rem;
            background: var(--primary-50);
        }
        .modal-slider::-webkit-scrollbar { display: none; }
        .modal-slider img {
            flex: 0 0 100%;
            width: 100%;
            height: 220px;
            object-fit: cover;
            scroll-snap-align: start;
        }
        
        /* Error Message */
        .error-msg {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="brand">
            <div class="brand-icon">☕</div>
            <h1>Keshir Coffee & Eatery</h1>
        </div>
    </header>

    <div class="cat-nav">
        <a href="#" class="cat-btn active">Semua</a>
        @foreach($categories as $cat)
            <a href="#cat-{{ $cat->id }}" class="cat-btn">{{ $cat->name }}</a>
        @endforeach
    </div>

    <div class="container">
        @if(session('error'))
            <div class="error-msg">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @foreach($categories as $cat)
            @php $catProducts = $products->where('category_id', $cat->id); @endphp
            @if($catProducts->count() > 0)
                <h2 class="section-title" id="cat-{{ $cat->id }}">{{ $cat->name }}</h2>
                @foreach($catProducts as $p)
                    <div class="product-card" onclick='openModal(@json($p))'>
                        <div class="product-img">
                            @if(is_array($p->photos) && count($p->photos) > 0)
                                <img src="{{ asset('storage/' . $p->photos[0]) }}" alt="{{ $p->name }}">
                            @else
                                <span class="placeholder">🍽️</span>
                            @endif
                        </div>
                        <div class="product-info">
                            <div>
                                <div class="product-title">{{ $p->name }}</div>
                                <div class="product-desc">{{ $p->description ?? 'Nikmati hidangan spesial ini.' }}</div>
                            </div>
                            <div class="product-footer">
                                <div class="product-price">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                                <button class="add-btn">+ Tambah</button>
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
            <span class="cart-qty">🛒 {{ $cartSummary['item_count'] }} Item</span>
            <span class="cart-total">Rp {{ number_format($cartSummary['subtotal'], 0, ',', '.') }}</span>
        </div>
        <a href="{{ route('public.cart') }}" class="cart-btn">
            Lihat Keranjang →
        </a>
    </div>
    @endif

    <!-- Add Item Modal -->
    <div class="modal" id="itemModal" onclick="if(event.target===this) closeModal()">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="m-title"></h3>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            
            <div id="m-slider" class="modal-slider" style="display:none;"></div>
            
            <form id="add-form" action="{{ route('public.addToCart') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="m-pid">
                
                <div id="m-variants" class="form-group" style="display:none;">
                    <label>Pilih Varian <span style="color:#ef4444">*</span></label>
                    <div class="variant-grid" id="m-var-list"></div>
                </div>

                <div id="m-addons" class="form-group" style="display:none;">
                    <label>Tambahan Ekstra</label>
                    <div class="addon-grid" id="m-add-list"></div>
                </div>

                <div class="form-group">
                    <label>Catatan (opsional)</label>
                    <input type="text" name="notes" class="notes-input" placeholder="Contoh: Jangan terlalu pedas, gula sedikit">
                </div>

                <div class="modal-footer">
                    <div class="qty-controls">
                        <button type="button" class="qty-btn" onclick="let q=document.getElementById('m-qty'); if(q.value>1)q.value--">−</button>
                        <input type="number" name="qty" id="m-qty" value="1" min="1" class="qty-input" readonly>
                        <button type="button" class="qty-btn" onclick="let q=document.getElementById('m-qty'); q.value++">+</button>
                    </div>
                    <button type="button" onclick="submitForm()" class="submit-btn">+ Tambah ke Keranjang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(product) {
            document.getElementById('m-pid').value = product.id;
            document.getElementById('m-title').innerText = product.name;
            document.getElementById('m-qty').value = 1;
            
            // Render Photos
            const sliderDiv = document.getElementById('m-slider');
            if(product.photos && product.photos.length > 0) {
                sliderDiv.style.display = 'flex';
                sliderDiv.innerHTML = product.photos.map(p => `
                    <img src="/storage/${p}" alt="${product.name}">
                `).join('');
            } else {
                sliderDiv.style.display = 'none';
                sliderDiv.innerHTML = '';
            }
            
            // Variants
            const vDiv = document.getElementById('m-variants');
            const vList = document.getElementById('m-var-list');
            if(product.variants && product.variants.length > 0) {
                vDiv.style.display = 'block';
                vList.innerHTML = product.variants.map((v, i) => `
                    <label class="radio-card">
                        <div style="display:flex;align-items:center;">
                            <input type="radio" name="product_variant_id" value="${v.id}" ${i===0?'checked':''} required>
                            <span class="label-text">${v.variant_name}</span>
                        </div>
                        <span class="price-text">+Rp ${new Intl.NumberFormat('id-ID').format(v.additional_price)}</span>
                    </label>
                `).join('');
            } else {
                vDiv.style.display = 'none';
                vList.innerHTML = '';
            }

            // Addons
            const aDiv = document.getElementById('m-addons');
            const aList = document.getElementById('m-add-list');
            if(product.addons && product.addons.length > 0) {
                aDiv.style.display = 'block';
                aList.innerHTML = product.addons.map(a => `
                    <label class="radio-card">
                        <div style="display:flex;align-items:center;">
                            <input type="checkbox" name="addons[]" value="${a.id}">
                            <span class="label-text">${a.addon_name}</span>
                        </div>
                        <span class="price-text">+Rp ${new Intl.NumberFormat('id-ID').format(a.price)}</span>
                    </label>
                `).join('');
            } else {
                aDiv.style.display = 'none';
                aList.innerHTML = '';
            }

            document.getElementById('itemModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('itemModal').classList.remove('show');
            document.body.style.overflow = '';
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
        
        // Category scroll highlight
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
