<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran — Keshir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --bg:#f8fafc; --text:#1e293b; --primary:#2563eb; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); padding: 2rem 1rem; text-align: center; }
        .card { background: #fff; max-width: 400px; margin: 0 auto; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .success-icon { font-size: 3rem; margin-bottom: 1rem; color: #16a34a; }
        h1 { font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; }
        p { color: #64748b; font-size: 0.9rem; margin-bottom: 2rem; line-height: 1.5; }
        .btn { display: inline-block; background: var(--primary); color: #fff; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 700; border: none; font-size: 1rem; cursor: pointer; width: 100%; margin-bottom: 1rem; }
        .btn-outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); }
    </style>
    <!-- Midtrans Snap.js -->
    @if(env('MIDTRANS_IS_PRODUCTION', config('midtrans.is_production')))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key')) }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key')) }}"></script>
    @endif
</head>
<body>
    <div class="card">
        <div class="success-icon">⏳</div>
        <h1>Menunggu Pembayaran</h1>
        <p>Pesanan Anda telah dicatat, silakan selesaikan pembayaran untuk memproses pesanan ke Dapur kami.</p>
        
        <div style="font-weight:700;font-size:1.5rem;margin-bottom:2rem;color:var(--text);">
            Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
        </div>

        <button id="pay-button" class="btn">💳 Bayar Sekarang</button>
        <a href="{{ route('public.order-status', $transaction) }}" class="btn btn-outline" style="box-sizing:border-box;">Cek Status Pesanan</a>
    </div>

    <script type="text/javascript">
      // For testing, Snap may already be triggered.
      document.getElementById('pay-button').onclick = function(){
        // SnapToken passed from Controller
        window.snap.pay('{{ $snapToken }}', {
          onSuccess: function(result){
            /* You may add your own implementation here */
            window.location.href = "{{ route('public.order-status', $transaction) }}";
          },
          onPending: function(result){
            /* You may add your own implementation here */
            alert("menunggu pembayaran Anda!"); console.log(result);
          },
          onError: function(result){
            /* You may add your own implementation here */
            alert("pembayaran gagal!"); console.log(result);
          },
          onClose: function(){
            /* You may add your own implementation here */
            alert('Anda menutup jendela pembayaran tanpa menyelesaikan transaksi.');
          }
        })
      };
      
      // Auto-trigger on popup load
      setTimeout(() => document.getElementById('pay-button').click(), 500);
    </script>
</body>
</html>
