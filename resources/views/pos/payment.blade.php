<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran Digital — Bill #{{ $transaction->bill_number ?? $transaction->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --bg: #f0f4ff;
            --card: #ffffff;
            --text: #0f172a;
            --text-secondary: #475569;
            --muted: #64748b;
            --success: #10b981;
            --success-bg: #d1fae5;
            --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .payment-container {
            background: var(--card);
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.15);
            max-width: 460px;
            width: 100%;
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .payment-header .icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            display: block;
        }
        .payment-header h1 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }
        .payment-header .bill-info {
            font-size: 0.85rem;
            opacity: 0.85;
        }
        .payment-body {
            padding: 2rem;
            text-align: center;
        }
        .amount-box {
            background: var(--primary-50);
            border: 2px solid var(--primary-100);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .amount-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }
        .amount-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        .order-summary {
            text-align: left;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .order-summary h3 {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 0.35rem;
            color: var(--text-secondary);
        }
        .order-item .name { font-weight: 500; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 0.75rem;
        }
        .btn-pay {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        }
        .btn-pay:active { transform: translateY(0); }
        .btn-back {
            background: transparent;
            border: 1.5px solid #e2e8f0;
            color: var(--muted);
        }
        .btn-back:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .status-msg {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 0.5rem;
        }
        /* Pulse animation for waiting state */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .waiting { animation: pulse 1.5s ease-in-out infinite; }
    </style>
    <!-- Midtrans Snap.js -->
    @if(config('midtrans.is_production') || env('MIDTRANS_IS_PRODUCTION'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key')) }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key')) }}"></script>
    @endif
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <span class="icon">💳</span>
            <h1>Pembayaran Digital</h1>
            <div class="bill-info">Bill #{{ $transaction->bill_number ?? $transaction->id }} · {{ $transaction->table ? 'Meja ' . $transaction->table->table_number : 'Takeaway' }}</div>
        </div>
        <div class="payment-body">
            <div class="amount-box">
                <div class="amount-label">Total Pembayaran</div>
                <div class="amount-value">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</div>
            </div>

            <div class="order-summary">
                <h3>📋 Ringkasan Pesanan</h3>
                @foreach($transaction->details as $d)
                    <div class="order-item">
                        <span class="name">{{ $d->qty }}x {{ $d->product->name }}@if($d->variant) ({{ $d->variant->variant_name }})@endif</span>
                        <span>Rp {{ number_format(($d->price + $d->addons->sum('price')) * $d->qty, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                @if($transaction->tax_total > 0)
                    <div class="order-item" style="margin-top:0.5rem;padding-top:0.5rem;border-top:1px dashed #e2e8f0;">
                        <span>Pajak</span>
                        <span>Rp {{ number_format($transaction->tax_total, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($transaction->service_total > 0)
                    <div class="order-item">
                        <span>Service</span>
                        <span>Rp {{ number_format($transaction->service_total, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <button id="pay-button" class="btn btn-pay">💳 Bayar Sekarang via Midtrans</button>
            <a href="{{ route('pos.bill', $transaction) }}" class="btn btn-back">← Kembali ke Bill</a>
            <p class="status-msg" id="status-msg">Klik tombol di atas untuk membuka halaman pembayaran</p>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', function() {
            const statusMsg = document.getElementById('status-msg');
            statusMsg.textContent = '⏳ Memproses pembayaran...';
            statusMsg.classList.add('waiting');

            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    statusMsg.textContent = '✅ Pembayaran berhasil! Mengalihkan ke nota...';
                    statusMsg.classList.remove('waiting');
                    statusMsg.style.color = '#10b981';

                    // Submit confirmation to server
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("pos.confirmDigital", $transaction) }}';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrf);

                    document.body.appendChild(form);
                    form.submit();
                },
                onPending: function(result) {
                    statusMsg.textContent = '⏳ Menunggu pembayaran... Silakan selesaikan di aplikasi bank Anda.';
                    statusMsg.style.color = '#f59e0b';
                },
                onError: function(result) {
                    statusMsg.textContent = '❌ Pembayaran gagal. Silakan coba lagi.';
                    statusMsg.classList.remove('waiting');
                    statusMsg.style.color = '#ef4444';
                },
                onClose: function() {
                    statusMsg.textContent = 'Jendela pembayaran ditutup. Klik tombol untuk mencoba lagi.';
                    statusMsg.classList.remove('waiting');
                    statusMsg.style.color = '#64748b';
                }
            });
        });

        // Auto-trigger payment popup
        setTimeout(() => document.getElementById('pay-button').click(), 600);
    </script>
</body>
</html>
