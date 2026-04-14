<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Keshir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Midtrans Snap.js Configuration -->
    @if(filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @endif
    
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
        
        /* Error */
        .error-msg {
            background: linear-gradient(135deg, #fef2f2 0%, var(--danger-bg) 100%);
            color: #991b1b;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border: 1px solid #fecaca;
        }
        .error-msg ul { margin: 0; padding-left: 1.25rem; }
        
        /* Card */
        .card {
            background: var(--card);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 1rem;
            border: 1px solid var(--border);
        }
        .section-title {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text);
        }
        
        /* Type Selector */
        .type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .type-option {
            border: 2px solid var(--border);
            border-radius: 1rem;
            padding: 1.25rem 1rem;
            text-align: center;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            background: #fff;
            position: relative;
        }
        .type-option input { position: absolute; opacity: 0; }
        .type-option:hover { border-color: var(--primary-100); }
        .type-option.active {
            border-color: var(--primary);
            background: var(--primary-50);
        }
        .type-option.active::after {
            content: '✓';
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 20px;
            height: 20px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .type-icon { font-size: 2rem; }
        .type-label { font-size: 0.95rem; font-weight: 700; color: var(--text); }
        
        /* Form */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
        }
        .form-hint {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 400;
        }
        .form-control {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s ease;
            background: #fff;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-100);
        }
        .form-control::placeholder { color: #94a3b8; }
        select.form-control {
            appearance: auto;
            background-color: #fff;
            cursor: pointer;
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
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: #fff;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 700;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.2s ease;
        }
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('public.cart') }}" class="back-btn">←</a>
        <h1>📝 Detail Pesanan</h1>
    </header>

    <div class="container">
        @if(session('error'))
            <div class="error-msg">⚠️ {{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="error-msg">
                <strong>⚠️ Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('public.checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            
            <div class="card">
                <div class="section-title">📦 Tipe Pesanan</div>
                <div class="type-selector">
                    <label class="type-option active" id="type-dinein">
                        <input type="radio" name="order_type" value="dine_in" checked onchange="toggleType()">
                        <div class="type-icon">🍽️</div>
                        <div class="type-label">Dine In / Takeaway</div>
                    </label>
                    <label class="type-option" id="type-booking">
                        <input type="radio" name="order_type" value="booking" onchange="toggleType()">
                        <div class="type-icon">📅</div>
                        <div class="type-label">Booking Tempat</div>
                    </label>
                </div>

                <div class="form-group" id="dinein-toggle">
                    <label class="form-label">Pilih Opsi <span class="form-hint">(Makan di tempat atau bawa pulang)</span></label>
                    <select class="form-control" onchange="if(this.value==='takeaway'){document.getElementById('table-wrap').style.display='none';}else{document.getElementById('table-wrap').style.display='block';}">
                        <option value="dine">🍽️ Makan di Tempat</option>
                        <option value="takeaway">🛍️ Bawa Pulang (Takeaway)</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <div class="section-title">👤 Data Pemesan</div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="customer_name" class="form-control" required placeholder="Cth: Budi Santoso">
                </div>
                <div class="form-group">
                    <label class="form-label">No. WhatsApp</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="Cth: 08123456789">
                </div>
                
                <div id="table-wrap">
                    <div class="form-group">
                        <label class="form-label" id="lbl-table">Pilih Meja</label>
                        <select name="table_id" class="form-control" id="inp-table">
                            <option value="">— Silahkan Pilih —</option>
                            @foreach($tables as $t)
                                <option value="{{ $t->id }}">🪑 Meja {{ $t->table_number }} (Kapasitas: {{ $t->capacity }} orang)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group" id="wrap-people">
                    <label class="form-label">Jumlah Orang</label>
                    <input type="number" name="people_count" id="inp-people" class="form-control" min="1" placeholder="Cth: 2">
                </div>

                <div class="form-group" id="wrap-booking" style="display:none;">
                    <label class="form-label">Waktu Kedatangan</label>
                    <input type="datetime-local" name="booking_time" id="inp-booking" class="form-control">
                </div>
            </div>

            <div class="checkout-bar">
                <button type="button" onclick="submitForm()" class="checkout-btn">💳 Bayar Sekarang</button>
            </div>
        </form>
    </div>

    <script>
        function toggleType() {
            const isBooking = document.querySelector('input[name="order_type"]:checked').value === 'booking';
            
            document.getElementById('type-dinein').classList.toggle('active', !isBooking);
            document.getElementById('type-booking').classList.toggle('active', isBooking);
            
            document.getElementById('dinein-toggle').style.display = isBooking ? 'none' : 'block';
            document.getElementById('wrap-booking').style.display = isBooking ? 'block' : 'none';
            document.getElementById('table-wrap').style.display = 'block';
            
            document.getElementById('lbl-table').innerText = isBooking ? 'Pilih Meja Booking' : 'Pilih Meja Sekarang';
            
            document.getElementById('inp-booking').required = isBooking;
            document.getElementById('inp-table').required = true;
            document.getElementById('inp-people').required = true;
        }

        async function submitForm() {
            const type = document.querySelector('input[name="order_type"]:checked').value;
            const form = document.getElementById('checkout-form');
            if (type === 'dine_in') {
                const isTakeaway = document.querySelector('#dinein-toggle select').value === 'takeaway';
                if (isTakeaway) {
                    // Cek apakah sudah ada input hidden order_type=takeaway dari submit sebelumnya
                    let hiddenInput = document.querySelector('input[type="hidden"][name="order_type"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'order_type';
                        hiddenInput.value = 'takeaway';
                        form.appendChild(hiddenInput);
                    }
                    
                    document.getElementById('inp-table').required = false;
                    document.getElementById('inp-people').required = false;
                } else {
                    // Hapus kalau ganti dari takeaway ke dine in
                    const hiddenInput = document.querySelector('input[type="hidden"][name="order_type"]');
                    if (hiddenInput) hiddenInput.remove();
                }
            }
            
            if(form.reportValidity()) {
                const btn = document.querySelector('.checkout-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '⏳ Memproses...';
                btn.disabled = true;

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.status === 422) {
                        const res = await response.json();
                        let errors = "⚠️ Kesalahan input:\n";
                        for (const key in res.errors) {
                            errors += `- ${res.errors[key][0]}\n`;
                        }
                        alert(errors);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        return;
                    }

                    const result = await response.json();

                    if (result.status === 'success') {
                        // Trigger Midtrans Snap popup
                        window.snap.pay(result.snap_token, {
                            onSuccess: function(res) {
                                window.location.href = result.redirect_url;
                            },
                            onPending: function(res) {
                                window.location.href = result.redirect_url;
                            },
                            onError: function(res) {
                                alert("Pembayaran gagal!");
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                            },
                            onClose: function() {
                                alert('Anda menutup pop-up tanpa menyelesaikan pembayaran. Pesanan Anda tetap tersimpan.');
                                window.location.href = result.redirect_url;
                            }
                        });
                    } else {
                        alert(result.message || "Gagal menghubungi server Midtrans.");
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                } catch (e) {
                    console.error(e);
                    alert("Terjadi kesalahan jaringan.");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        }
    </script>
</body>
</html>
