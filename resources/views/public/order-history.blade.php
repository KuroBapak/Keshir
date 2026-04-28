<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya — Keshir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-bg: #eef2ff;
            --bg-color: #f3f4f6;
            --white: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: var(--bg-color);
            min-height: 100vh;
        }
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .brand-logo {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--primary);
            letter-spacing: -0.5px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1rem;
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.2s;
        }
        .back-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .page-title {
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }
        .page-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }
        .order-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: 0.2s;
        }
        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .order-id {
            font-weight: 800;
            font-size: 1rem;
            color: var(--text-dark);
        }
        .order-time {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-open { background: #fef3c7; color: #92400e; }
        .badge-void { background: #fee2e2; color: #991b1b; }
        .badge-booking { background: #dbeafe; color: #1e40af; }
        .order-items {
            margin: 0.75rem 0;
            padding: 0.75rem;
            background: var(--bg-color);
            border-radius: 10px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            padding: 0.25rem 0;
        }
        .order-item-name { color: var(--text-dark); font-weight: 500; }
        .order-item-price { color: var(--text-muted); font-weight: 600; }
        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px dashed var(--border-color);
        }
        .order-total {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary);
        }
        .order-type {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }
        .view-link {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
        }
        .view-link:hover {
            text-decoration: underline;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }
        .empty-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .empty-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="top-header">
        <div class="brand-logo">KESHIR</div>
        <a href="{{ route('public.menu') }}" class="back-btn">← Kembali ke Menu</a>
    </header>

    <div class="container">
        <h1 class="page-title">📋 Pesanan Saya</h1>
        <p class="page-subtitle">Riwayat pesanan Anda selama sesi ini ({{ $orders->count() }} pesanan)</p>

        @if($orders->count() > 0)
            @foreach($orders as $order)
            <a href="{{ route('public.order-status', $order) }}" style="text-decoration:none;color:inherit;">
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">#{{ str_pad($order->bill_number ?? $order->id, 5, '0', STR_PAD_LEFT) }}</span>
                            @if($order->order_type === 'booking')
                                <span class="badge badge-booking" style="margin-left:0.5rem;">📅 Booking</span>
                            @endif
                        </div>
                        <div style="text-align:right;">
                            @if($order->payment_status === 'paid')
                                <span class="badge badge-paid">✅ Lunas</span>
                            @elseif($order->payment_status === 'void')
                                <span class="badge badge-void">❌ Dibatalkan</span>
                            @else
                                <span class="badge badge-open">⏳ Proses</span>
                            @endif
                            <div class="order-time">{{ $order->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="order-items">
                        @foreach($order->details->take(5) as $d)
                        <div class="order-item">
                            <span class="order-item-name">{{ $d->qty }}x {{ $d->product->name ?? 'Item' }}
                                @if($d->variant) ({{ $d->variant->name }}) @endif
                            </span>
                            <span class="order-item-price">Rp {{ number_format($d->price * $d->qty, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @if($order->details->count() > 5)
                            <div style="font-size:0.8rem;color:var(--text-muted);text-align:center;padding-top:0.25rem;">
                                +{{ $order->details->count() - 5 }} item lainnya
                            </div>
                        @endif
                    </div>

                    <div class="order-footer">
                        <div>
                            <span class="order-type">
                                @if($order->order_type === 'dine_in')
                                    🍽️ Dine In {{ $order->table ? '• Meja ' . $order->table->table_number : '' }}
                                @elseif($order->order_type === 'takeaway')
                                    📦 Takeaway
                                @elseif($order->order_type === 'booking')
                                    📅 Booking {{ $order->booking ? '• ' . $order->booking->booking_time->format('d M Y H:i') : '' }}
                                @endif
                            </span>
                        </div>
                        <span class="order-total">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <div class="empty-title">Belum Ada Pesanan</div>
                <p>Anda belum membuat pesanan di sesi ini. Silakan pesan dari menu.</p>
                <a href="{{ route('public.menu') }}" style="display:inline-block;margin-top:1.5rem;padding:0.75rem 1.5rem;background:var(--primary);color:#fff;border-radius:50px;font-weight:700;text-decoration:none;font-size:0.9rem;">🍽️ Lihat Menu</a>
            </div>
        @endif
    </div>
</body>
</html>
