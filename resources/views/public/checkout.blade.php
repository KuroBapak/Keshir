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
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
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
        
        /* Type Selector — 3 options */
        .type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
        }
        .type-option {
            border: 2px solid var(--border);
            border-radius: 1rem;
            padding: 1rem 0.75rem;
            text-align: center;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
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
            top: 0.4rem;
            right: 0.4rem;
            width: 18px;
            height: 18px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 700;
        }
        .type-icon { font-size: 1.75rem; }
        .type-label { font-size: 0.85rem; font-weight: 700; color: var(--text); }
        .type-desc { font-size: 0.7rem; color: var(--muted); line-height: 1.3; }

        /* Info Banner */
        .info-banner {
            background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
            border: 1px solid #bfdbfe;
            border-radius: 0.75rem;
            padding: 0.85rem 1rem;
            font-size: 0.8rem;
            color: #1e40af;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        .info-banner.warning {
            background: linear-gradient(135deg, #fffbeb, var(--warning-bg));
            border-color: #fde68a;
            color: #92400e;
        }
        .info-banner .icon { font-size: 1rem; flex-shrink: 0; margin-top: 0.05rem; }
        
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

        /* Booking date section */
        .booking-fields {
            display: none;
            background: #fefce8;
            border: 1px solid #fde68a;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.25rem;
        }
        .booking-fields.show { display: block; }
        .booking-fields .section-subtitle {
            font-weight: 700;
            font-size: 0.9rem;
            color: #92400e;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .date-time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
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
            border: none;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        .checkout-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .midtrans-badge {
            text-align: center;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: var(--muted);
        }

        @media (max-width: 400px) {
            .type-selector { grid-template-columns: 1fr; }
            .date-time-grid { grid-template-columns: 1fr; }
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
                        <div class="type-label">Dine In</div>
                        <div class="type-desc">Makan di tempat</div>
                    </label>
                    <label class="type-option" id="type-takeaway">
                        <input type="radio" name="order_type" value="takeaway" onchange="toggleType()">
                        <div class="type-icon">🛍️</div>
                        <div class="type-label">Takeaway</div>
                        <div class="type-desc">Bawa pulang</div>
                    </label>
                    <label class="type-option" id="type-booking">
                        <input type="radio" name="order_type" value="booking" onchange="toggleType()">
                        <div class="type-icon">📅</div>
                        <div class="type-label">Reservasi</div>
                        <div class="type-desc">Pesan meja & hari</div>
                    </label>
                </div>

                <!-- Info: Booking needs cashier approval -->
                <div class="info-banner warning" id="booking-info" style="display:none;">
                    <span class="icon">ℹ️</span>
                    <span>Reservasi akan divalidasi oleh kasir terlebih dahulu sebelum pesanan diproses ke dapur. Anda akan mendapat notifikasi di halaman status pesanan.</span>
                </div>
            </div>

            <div class="card">
                <div class="section-title">👤 Data Pemesan</div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="customer_name" class="form-control" required placeholder="Cth: Budi Santoso" value="{{ old('customer_name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">No. WhatsApp</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="Cth: 08123456789" value="{{ old('phone') }}">
                </div>
                
                <!-- Table Selection (hidden for takeaway) -->
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
                    <input type="number" name="people_count" id="inp-people" class="form-control" min="1" placeholder="Cth: 2" value="{{ old('people_count') }}">
                </div>

                <!-- Booking Date & Time Fields -->
                <div class="booking-fields" id="wrap-booking">
                    <div class="section-subtitle">📅 Detail Reservasi</div>
                    <div class="date-time-grid">
                        <div class="form-group" style="margin-bottom:0.5rem;">
                            <label class="form-label">Tanggal Kedatangan</label>
                            <input type="date" id="inp-booking-date" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom:0.5rem;">
                            <label class="form-label">Jam Kedatangan</label>
                            <input type="time" id="inp-booking-time" class="form-control">
                        </div>
                    </div>
                    <div style="font-size:0.75rem;color:#92400e;margin-top:0.5rem;">
                        ⏰ Minimal reservasi 1 jam dari sekarang
                    </div>
                    <!-- Hidden combined datetime field for backend -->
                    <input type="hidden" name="booking_time" id="inp-booking-combined">
                </div>
            </div>

            <!-- Payment Info -->
            <div class="info-banner">
                <span class="icon">💳</span>
                <span>Pembayaran dilakukan secara online melalui <strong>Midtrans</strong> (QRIS, Transfer Bank, E-Wallet, dll). Pembayaran tunai tidak tersedia untuk pemesanan online.</span>
            </div>

            <div class="checkout-bar">
                <button type="button" onclick="submitForm()" class="checkout-btn" id="btn-checkout">💳 Bayar via Midtrans</button>
                <div class="midtrans-badge">🔒 Pembayaran aman dengan Midtrans</div>
            </div>
        </form>
    </div>

    <script>
        // Set minimum date for booking (today, using local timezone)
        const today = new Date();
        const padD = n => String(n).padStart(2, '0');
        const todayStr = `${today.getFullYear()}-${padD(today.getMonth()+1)}-${padD(today.getDate())}`;
        document.getElementById('inp-booking-date').setAttribute('min', todayStr);

        function toggleType() {
            const type = document.querySelector('input[name="order_type"]:checked').value;
            
            // Reset all active states
            document.getElementById('type-dinein').classList.remove('active');
            document.getElementById('type-takeaway').classList.remove('active');
            document.getElementById('type-booking').classList.remove('active');
            
            // Set active
            if (type === 'dine_in') {
                document.getElementById('type-dinein').classList.add('active');
                document.getElementById('table-wrap').style.display = 'block';
                document.getElementById('wrap-people').style.display = 'block';
                document.getElementById('wrap-booking').classList.remove('show');
                document.getElementById('booking-info').style.display = 'none';
                document.getElementById('inp-table').required = true;
                document.getElementById('inp-people').required = true;
                document.getElementById('lbl-table').innerText = 'Pilih Meja';
            } else if (type === 'takeaway') {
                document.getElementById('type-takeaway').classList.add('active');
                document.getElementById('table-wrap').style.display = 'none';
                document.getElementById('wrap-people').style.display = 'none';
                document.getElementById('wrap-booking').classList.remove('show');
                document.getElementById('booking-info').style.display = 'none';
                document.getElementById('inp-table').required = false;
                document.getElementById('inp-people').required = false;
            } else if (type === 'booking') {
                document.getElementById('type-booking').classList.add('active');
                document.getElementById('table-wrap').style.display = 'block';
                document.getElementById('wrap-people').style.display = 'block';
                document.getElementById('wrap-booking').classList.add('show');
                document.getElementById('booking-info').style.display = 'flex';
                document.getElementById('inp-table').required = true;
                document.getElementById('inp-people').required = true;
                document.getElementById('lbl-table').innerText = 'Pilih Meja Booking';
            }
        }

        function combineBookingDateTime() {
            const date = document.getElementById('inp-booking-date').value;
            const time = document.getElementById('inp-booking-time').value;
            if (date && time) {
                document.getElementById('inp-booking-combined').value = date + 'T' + time;
            }
        }

        // Auto-combine on change
        document.getElementById('inp-booking-date').addEventListener('change', combineBookingDateTime);
        document.getElementById('inp-booking-time').addEventListener('change', combineBookingDateTime);

        async function submitForm() {
            const type = document.querySelector('input[name="order_type"]:checked').value;
            const form = document.getElementById('checkout-form');

            // Booking validation
            if (type === 'booking') {
                const bookingDate = document.getElementById('inp-booking-date').value;
                const bookingTime = document.getElementById('inp-booking-time').value;
                
                if (!bookingDate || !bookingTime) {
                    alert('⚠️ Silakan pilih tanggal dan jam kedatangan untuk reservasi.');
                    return;
                }

                // Validate minimum 1 hour from now
                const bookingDateTime = new Date(bookingDate + 'T' + bookingTime);
                const minTime = new Date(Date.now() + 60 * 60 * 1000); // 1 hour from now
                
                if (bookingDateTime < minTime) {
                    alert('⚠️ Waktu reservasi minimal 1 jam dari sekarang.');
                    return;
                }

                combineBookingDateTime();
            }

            // Dine-in validation: table required
            if (type === 'dine_in') {
                if (!document.getElementById('inp-table').value) {
                    alert('⚠️ Silakan pilih meja untuk Dine In.');
                    return;
                }
                if (!document.getElementById('inp-people').value) {
                    alert('⚠️ Silakan isi jumlah orang.');
                    return;
                }
            }
            
            if(form.reportValidity()) {
                const btn = document.getElementById('btn-checkout');
                const originalText = btn.innerHTML;
                btn.innerHTML = '⏳ Memproses pembayaran...';
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
