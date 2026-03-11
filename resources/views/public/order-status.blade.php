<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan — Keshir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --bg:#f8fafc; --text:#1e293b; --muted:#64748b; --card:#fff; --border:#e2e8f0; --success:#16a34a; --warning:#eab308; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); padding-bottom: 2rem; margin: 0; }
        .header { background: #fff; padding: 1.25rem 1rem; border-bottom: 1px solid var(--border); text-align: center; position: sticky; top:0; z-index:10; }
        .header h1 { font-size: 1.15rem; font-weight: 800; margin: 0; }
        
        .container { padding: 1rem; max-width: 600px; margin: 0 auto; }
        
        .status-card { background: var(--card); border-radius: 1rem; padding: 1.5rem; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1rem; }
        .badge { display: inline-block; padding: 0.4rem 1rem; border-radius: 9999px; font-weight: 700; font-size: 0.85rem; margin-bottom: 1rem; }
        .badge-pending { background: #fef08a; color: #854d0e; }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-void { background: #fef2f2; color: #991b1b; }
        
        .r-title { font-size: 0.9rem; color: var(--muted); margin-bottom: 0.2rem; }
        .r-value { font-size: 1.1rem; font-weight: 700; color: var(--text); }
        
        .receipt-card { background: center/cover url('data:image/svg+xml;utf8,<svg width="100" height="10" viewBox="0 0 100 10" xmlns="http://www.w3.org/2000/svg"><path d="M0 10 L5 0 L10 10" stroke="none" fill="%23fff"/></svg>') repeat-x bottom; background-color: var(--card); border-radius: 0.75rem 0.75rem 0 0; padding: 1.5rem; margin-top: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding-bottom: 2rem; }
        
        .r-item { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9rem; }
        .r-item-name { font-weight: 600; }
        .r-item-meta { font-size: 0.75rem; color: var(--muted); }
        
        .r-total-row { display: flex; justify-content: space-between; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--border); font-size: 0.9rem; }
        .r-grand { font-weight: 800; font-size: 1.1rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 2px dashed var(--border); }
        
        .cooking-status { font-size: 0.75rem; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600; margin-left: 0.5rem; }
        .cs-pending { background: #f1f5f9; color: var(--muted); }
        .cs-progress { background: #fef08a; color: #854d0e; }
        .cs-done { background: #dcfce7; color: #166534; }
    </style>
    @if($transaction->payment_status === 'open')
        <meta http-equiv="refresh" content="10"> {{-- Auto refresh every 10s if waiting for payment --}}
    @elseif(in_array('pending', $transaction->details->pluck('status')->toArray()) || in_array('in_progress', $transaction->details->pluck('status')->toArray()))
        <meta http-equiv="refresh" content="30"> {{-- Auto refresh every 30s if cooking --}}
    @endif
</head>
<body>
    <header class="header">
        <h1>Status Pesanan</h1>
    </header>

    <div class="container">
        <div class="status-card">
            @if($transaction->payment_status === 'paid')
                <div class="badge badge-paid">✅ Pembayaran Berhasil</div>
                @if(in_array('in_progress', $transaction->details->pluck('status')->toArray()))
                    <h2 class="r-value">🔥 Makanan Sedang Dimasak</h2>
                    <p style="font-size:0.85rem;color:var(--muted);margin-top:0.5rem;">Koki kami sedang menyiapkan pesanan Anda.</p>
                @elseif(in_array('pending', $transaction->details->pluck('status')->toArray()))
                    <h2 class="r-value">👨‍🍳 Dalam Antrean Dapur</h2>
                    <p style="font-size:0.85rem;color:var(--muted);margin-top:0.5rem;">Pesanan sedang menunggu giliran disiapkan.</p>
                @else
                    <h2 class="r-value" style="color:var(--success);">🍽️ Pesanan Selesai</h2>
                    <p style="font-size:0.85rem;color:var(--muted);margin-top:0.5rem;">Terima kasih, silakan nikmati hidangan Anda!</p>
                @endif
            @elseif($transaction->payment_status === 'void')
                <div class="badge badge-void">❌ Dibatalkan</div>
                <h2 class="r-value">Struk Kadaluarsa / Dibatalkan</h2>
            @else
                <div class="badge badge-pending">⏳ Menunggu Pembayaran</div>
                <h2 class="r-value">Midtrans Processing...</h2>
                <div style="font-size:0.85rem;color:var(--muted);margin-top:1rem;">Halaman ini akan refresh otomatis.</div>
            @endif
        </div>

        <div style="display:flex;gap:1rem;">
            <div style="flex:1;background:var(--card);padding:1rem;border-radius:0.75rem;border:1px solid var(--border);">
                <div class="r-title">Tipe Pesanan</div>
                <div class="r-value">
                    @if($transaction->order_type==='dine_in') Dine In (Meja {{ $transaction->table->table_number ?? '?' }})
                    @elseif($transaction->order_type==='take_away') Takeaway
                    @else Booking Meja
                    @endif
                </div>
            </div>
            <div style="flex:1;background:var(--card);padding:1rem;border-radius:0.75rem;border:1px solid var(--border);">
                <div class="r-title">Atas Nama</div>
                <div class="r-value">{{ $transaction->customer_name }}</div>
            </div>
        </div>

        <div class="receipt-card">
            <div style="text-align:center;margin-bottom:1.5rem;">
                <h3 style="margin:0;font-size:1.1rem;">Keshir Coffee & Eatery</h3>
                <div style="font-size:0.85rem;color:var(--muted);">ID: #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size:0.85rem;color:var(--muted);">{{ $transaction->created_at->format('d M Y H:i') }}</div>
            </div>

            @foreach($transaction->details as $d)
                <div class="r-item">
                    <div>
                        <div class="r-item-name">
                            {{ $d->qty }}x {{ $d->product->name }}
                            @if($transaction->payment_status === 'paid')
                                @if($d->status === 'done') <span class="cooking-status cs-done">Selesai</span>
                                @elseif($d->status === 'in_progress') <span class="cooking-status cs-progress">Dimasak</span>
                                @else <span class="cooking-status cs-pending">Antre</span>
                                @endif
                            @endif
                        </div>
                        <div class="r-item-meta">
                            @if($d->variant) <div>var: {{ $d->variant->variant_name }}</div> @endif
                            @if($d->addons->count() > 0) <div>+ {{ implode(', ', $d->addons->map(fn($a) => $a->addon->addon_name ?? '')->toArray()) }}</div> @endif
                            @if($d->notes) <div>📝 "{{ $d->notes }}"</div> @endif
                        </div>
                    </div>
                    <div style="font-weight:600;">{{ number_format($d->price * $d->qty, 0, ',', '.') }}</div>
                </div>
            @endforeach

            <div style="margin-top:1.5rem;">
                <div class="r-total-row">
                    <span>Subtotal</span>
                    <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="r-total-row">
                    <span>Pajak</span>
                    <span>{{ number_format($transaction->tax_total, 0, ',', '.') }}</span>
                </div>
                <div class="r-total-row">
                    <span>Service</span>
                    <span>{{ number_format($transaction->service_total, 0, ',', '.') }}</span>
                </div>
                <div class="r-total-row r-grand">
                    <span>Total Bayar</span>
                    <span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
            
            @if($transaction->payment_status === 'open')
                <div style="margin-top:2rem;text-align:center;">
                    <button onclick="window.location.reload()" style="background:#f1f5f9;border:1px solid var(--border);padding:0.5rem 1rem;border-radius:0.4rem;font-size:0.8rem;color:var(--text);font-weight:600;">🔄 Refresh Cek Pembayaran</button>
                    <div style="font-size:0.75rem;color:var(--muted);margin-top:0.5rem;">Jika gagal bayar, pesanan ini akan dibatalkan (Meja dilepas otomatis).</div>
                </div>
            @endif
        </div>
        
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('public.menu') }}" style="color:var(--primary);text-decoration:none;font-size:0.9rem;font-weight:600;">← Kembali ke Menu</a>
        </div>
    </div>
</body>
</html>
