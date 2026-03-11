<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Keshir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --bg:#f8fafc; --text:#1e293b; --muted:#64748b; --card:#fff; --border:#e2e8f0; --success:#16a34a; --danger:#dc2626; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); padding-bottom: 90px; margin: 0; }
        .header { background: #fff; border-bottom: 1px solid var(--border); padding: 1rem; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 1rem; }
        .header h1 { font-size: 1.15rem; font-weight: 700; margin: 0; flex: 1; }
        .back-btn { text-decoration: none; color: var(--muted); font-size: 1.2rem; }
        
        .container { padding: 1rem; max-width: 600px; margin: 0 auto; }
        
        .card { background: var(--card); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 1rem; }
        .section-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        
        .form-group { margin-bottom: 1.2rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; color: var(--text); }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-size: 0.95rem; font-family: inherit; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px rgba(37,99,235,0.2); }
        select.form-control { appearance: auto; background-color: #fff; }
        
        .type-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.5rem; }
        .type-option { border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem 0.5rem; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s; background: #fff; position: relative; }
        .type-option input { position: absolute; opacity: 0; }
        .type-option.active { border-color: var(--primary); background: #eff6ff; color: var(--primary); }
        .type-icon { font-size: 1.5rem; }
        .type-label { font-size: 0.9rem; font-weight: 600; }
        
        .checkout-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 1rem; border-top: 1px solid var(--border); box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 20; max-width: 600px; margin: 0 auto; }
        .checkout-btn { display: block; width: 100%; text-align: center; background: var(--primary); color: #fff; padding: 0.8rem; border-radius: 0.5rem; font-weight: 700; border: none; font-size: 1rem; cursor: pointer; }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('public.cart') }}" class="back-btn">←</a>
        <h1>Detail Pesanan</h1>
    </header>

    <div class="container">
        @if(session('error'))
            <div style="background:#fef2f2;color:#991b1b;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fef2f2;color:#991b1b;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;">
                <ul style="margin:0;padding-left:1rem;">
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
                    <label class="form-label" style="font-weight:400;font-size:0.8rem;">Jika dibawa pulang, pilih opsi takeaway.</label>
                    <select class="form-control" onchange="if(this.value==='takeaway'){document.getElementById('table-wrap').style.display='none';}else{document.getElementById('table-wrap').style.display='block';}">
                        <option value="dine">Makan di Tempat</option>
                        <option value="takeaway">Bawa Pulang (Takeaway)</option>
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
                                <option value="{{ $t->id }}">Meja {{ $t->table_number }} (Kapasitas: {{ $t->capacity }} orang)</option>
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
                <button type="button" onclick="submitForm()" class="checkout-btn">Bayar Sekarang</button>
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
            document.getElementById('table-wrap').style.display = 'block'; // Always show table for booking or dinein
            
            document.getElementById('lbl-table').innerText = isBooking ? 'Pilih Meja Booking' : 'Pilih Meja Sekarang';
            
            // Toggle required attrs
            document.getElementById('inp-booking').required = isBooking;
            document.getElementById('inp-table').required = true;
            document.getElementById('inp-people').required = true;
        }

        function submitForm() {
            // Check if takeaway mapped
            const type = document.querySelector('input[name="order_type"]:checked').value;
            if (type === 'dine_in') {
                const isTakeaway = document.querySelector('#dinein-toggle select').value === 'takeaway';
                if (isTakeaway) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'order_type';
                    hiddenInput.value = 'takeaway';
                    document.getElementById('checkout-form').appendChild(hiddenInput);
                    
                    document.getElementById('inp-table').required = false;
                    document.getElementById('inp-people').required = false;
                }
            }
            
            // Let HTML5 validation run
            if(document.getElementById('checkout-form').reportValidity()) {
                document.getElementById('checkout-form').submit();
            }
        }
    </script>
</body>
</html>
