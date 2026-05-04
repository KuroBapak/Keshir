<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan — Keshir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { 
            --primary:#2563eb; 
            --primary-dark:#1d4ed8;
            --primary-50:#eff6ff;
            --primary-100:#dbeafe;
            --bg:#f8fafc; 
            --text:#0f172a; 
            --text-secondary:#475569;
            --muted:#64748b; 
            --card:#fff; 
            --border:#e2e8f0; 
            --success:#10b981; 
            --success-bg:#d1fae5;
            --warning:#f59e0b; 
            --warning-bg:#fef3c7;
            --danger:#ef4444;
            --danger-bg:#fee2e2;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            min-height: 100vh;
        }
        
        /* Header */
        .header { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 1.5rem 1rem; 
            text-align: center; 
            position: sticky; 
            top: 0; 
            z-index: 10;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.25);
        }
        .header .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-bottom: 0.35rem;
        }
        .header .logo-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .header h1 { font-size: 1.25rem; font-weight: 800; }
        .header .subtitle { font-size: 0.85rem; opacity: 0.9; margin-top: 0.25rem; }
        
        .container { padding: 1.25rem; max-width: 600px; margin: 0 auto; }
        
        /* Status Card */
        .status-card { 
            background: var(--card); 
            border-radius: 1.25rem; 
            padding: 2rem 1.5rem; 
            text-align: center; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            margin-bottom: 1rem;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, #3b82f6 100%);
        }
        .status-card.paid::before { background: linear-gradient(90deg, var(--success) 0%, #34d399 100%); }
        .status-card.void::before { background: linear-gradient(90deg, var(--danger) 0%, #f87171 100%); }
        .status-card.pending::before { background: linear-gradient(90deg, var(--warning) 0%, #fcd34d 100%); }
        
        .status-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin: 0 auto 1rem;
        }
        .status-icon.paid { background: var(--success-bg); }
        .status-icon.void { background: var(--danger-bg); }
        .status-icon.pending { background: var(--warning-bg); }
        .status-icon.cooking { background: #ffedd5; }
        
        .badge { 
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 1.1rem; 
            border-radius: 50px; 
            font-weight: 700; 
            font-size: 0.8rem; 
            margin-bottom: 1rem; 
        }
        .badge-pending { background: var(--warning-bg); color: #92400e; }
        .badge-paid { background: var(--success-bg); color: #065f46; }
        .badge-void { background: var(--danger-bg); color: #991b1b; }
        
        .status-title { font-size: 1.25rem; font-weight: 800; color: var(--text); }
        .status-desc { font-size: 0.9rem; color: var(--muted); margin-top: 0.5rem; line-height: 1.5; }
        
        /* Progress Bar */
        .progress-steps {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
            flex: 1;
            max-width: 100px;
        }
        .progress-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: var(--muted);
            transition: all 0.3s ease;
        }
        .progress-dot.active { background: var(--primary); color: #fff; }
        .progress-dot.done { background: var(--success); color: #fff; }
        .progress-label { font-size: 0.7rem; color: var(--muted); text-align: center; font-weight: 600; }
        .progress-label.active { color: var(--primary); }
        .progress-label.done { color: var(--success); }
        
        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .info-card {
            background: var(--card);
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
        }
        .info-label { font-size: 0.75rem; color: var(--muted); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .info-value { font-size: 1rem; font-weight: 700; color: var(--text); }
        
        /* Receipt Card */
        .receipt-card { 
            background: var(--card); 
            border-radius: 1rem; 
            padding: 1.5rem; 
            margin-top: 1rem; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
        }
        .receipt-header {
            text-align: center;
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
            border-bottom: 2px dashed var(--border);
        }
        .receipt-header h3 { 
            font-size: 1.1rem; 
            font-weight: 800; 
            color: var(--primary);
            margin-bottom: 0.35rem;
        }
        .receipt-header .meta { font-size: 0.8rem; color: var(--muted); }
        
        .r-item { 
            display: flex; 
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.85rem 0; 
            border-bottom: 1px solid var(--border);
        }
        .r-item:last-child { border-bottom: none; }
        .r-item-name { font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
        .r-item-meta { font-size: 0.8rem; color: var(--muted); margin-top: 0.35rem; }
        .r-item-price { font-weight: 700; font-size: 0.95rem; color: var(--text); }
        
        .cooking-status { 
            font-size: 0.65rem; 
            padding: 0.2rem 0.55rem; 
            border-radius: 50px; 
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .cs-pending { background: #f1f5f9; color: var(--muted); }
        .cs-progress { background: #fef08a; color: #854d0e; }
        .cs-done { background: var(--success-bg); color: #065f46; }
        
        /* Totals */
        .totals-section { 
            margin-top: 1.25rem; 
            padding-top: 1.25rem; 
            border-top: 2px dashed var(--border); 
        }
        .r-total-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 0.35rem 0;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        .r-grand { 
            font-weight: 800; 
            font-size: 1.15rem; 
            color: var(--primary);
            margin-top: 0.75rem; 
            padding-top: 0.75rem; 
            border-top: 2px solid var(--primary); 
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        .btn-ghost {
            background: var(--bg);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { background: #fff; border-color: var(--primary); color: var(--primary); }
        
        .action-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        .action-footer .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.2s ease;
        }
        .action-footer .back-link:hover { gap: 0.6rem; }
        
        /* Pulse animation */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .pulse { animation: pulse 2s ease-in-out infinite; }
    </style>
    @if($transaction->payment_status === 'open')
        <meta http-equiv="refresh" content="10">
    @elseif($transaction->order_type === 'booking' && $transaction->booking && $transaction->booking->status === 'pending')
        {{-- Auto-refresh while waiting for cashier confirmation --}}
        <meta http-equiv="refresh" content="15">
    @elseif(in_array('pending', $transaction->details->pluck('status')->toArray()) || in_array('in_progress', $transaction->details->pluck('status')->toArray()))
        <meta http-equiv="refresh" content="30">
    @endif
</head>
<body>
    <header class="header">
        <div class="logo">
            <div class="logo-icon">☕</div>
            <h1>Keshir Coffee</h1>
        </div>
        <div class="subtitle">Status Pesanan</div>
    </header>

    <div class="container">
        @php
            $hasInProgress = in_array('in_progress', $transaction->details->pluck('status')->toArray());
            $hasPending = in_array('pending', $transaction->details->pluck('status')->toArray());
            $allDone = !$hasInProgress && !$hasPending;
            $isBooking = $transaction->order_type === 'booking';
            $bookingStatus = $isBooking && $transaction->booking ? $transaction->booking->status : null;
            $bookingPendingApproval = $isBooking && $bookingStatus === 'pending';
            $bookingConfirmed = $isBooking && $bookingStatus === 'approved';
            $bookingCancelled = $isBooking && $bookingStatus === 'rejected';
        @endphp
        
        <div class="status-card {{ $transaction->payment_status === 'paid' ? 'paid' : ($transaction->payment_status === 'void' ? 'void' : 'pending') }}">
            {{-- ============ VOID / CANCELLED ============ --}}
            @if($transaction->payment_status === 'void')
                <div class="status-icon void">❌</div>
                @if($bookingCancelled)
                    <div class="badge badge-void">Reservasi Ditolak</div>
                    <h2 class="status-title">Reservasi Dibatalkan</h2>
                    <p class="status-desc">Maaf, reservasi Anda tidak dapat dikonfirmasi oleh kasir. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                @else
                    <div class="badge badge-void">Dibatalkan</div>
                    <h2 class="status-title">Pesanan Dibatalkan</h2>
                    <p class="status-desc">Struk ini sudah kadaluarsa atau telah dibatalkan.</p>
                @endif

            {{-- ============ PAID ============ --}}
            @elseif($transaction->payment_status === 'paid')
                {{-- BOOKING: Waiting for cashier approval --}}
                @if($bookingPendingApproval)
                    <div class="status-icon pending pulse">📅</div>
                    <div class="badge badge-paid">✅ Pembayaran Berhasil</div>
                    <h2 class="status-title">Menunggu Konfirmasi Kasir</h2>
                    <p class="status-desc">Reservasi Anda sudah dibayar. Kasir akan mengonfirmasi reservasi Anda untuk tanggal <strong>{{ $transaction->booking->booking_time->translatedFormat('d M Y, H:i') }}</strong>. Halaman ini akan update otomatis.</p>
                @elseif($bookingCancelled)
                    <div class="status-icon void">❌</div>
                    <div class="badge badge-void">Reservasi Ditolak</div>
                    <h2 class="status-title">Reservasi Dibatalkan</h2>
                    <p class="status-desc">Maaf, reservasi Anda tidak dapat dikonfirmasi oleh kasir. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                @elseif($hasInProgress)
                    <div class="status-icon cooking pulse">🔥</div>
                    <div class="badge badge-paid">✅ Pembayaran Berhasil</div>
                    <h2 class="status-title">Makanan Sedang Dimasak</h2>
                    <p class="status-desc">Koki kami sedang menyiapkan pesanan Anda dengan penuh cinta ❤️</p>
                @elseif($hasPending)
                    <div class="status-icon paid">👨‍🍳</div>
                    <div class="badge badge-paid">✅ Pembayaran Berhasil</div>
                    @if($bookingConfirmed)
                        <h2 class="status-title">Reservasi Dikonfirmasi! 🎉</h2>
                        <p class="status-desc">Kasir sudah mengonfirmasi reservasi Anda. Pesanan sedang menunggu giliran untuk disiapkan.</p>
                    @else
                        <h2 class="status-title">Dalam Antrean Dapur</h2>
                        <p class="status-desc">Pesanan sedang menunggu giliran untuk disiapkan.</p>
                    @endif
                @else
                    <div class="status-icon paid">🍽️</div>
                    <div class="badge badge-paid">✅ Pesanan Selesai</div>
                    <h2 class="status-title" style="color: var(--success);">Siap Dinikmati!</h2>
                    <p class="status-desc">Terima kasih telah memesan di Keshir. Selamat menikmati! 🎉</p>
                @endif
                
                <!-- Progress Steps -->
                @if($isBooking)
                    {{-- Booking progress: Bayar → Kasir Konfirmasi → Dimasak → Selesai --}}
                    <div class="progress-steps">
                        <div class="progress-step">
                            <div class="progress-dot done">✓</div>
                            <span class="progress-label done">Bayar</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot {{ $bookingPendingApproval ? 'active' : ($bookingConfirmed || $allDone ? 'done' : ($bookingCancelled ? '' : '')) }}">{{ $bookingConfirmed || $allDone ? '✓' : ($bookingPendingApproval ? '⏳' : ($bookingCancelled ? '✗' : '2')) }}</div>
                            <span class="progress-label {{ $bookingPendingApproval ? 'active' : ($bookingConfirmed || $allDone ? 'done' : '') }}">Kasir</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot {{ $hasInProgress ? 'active' : ($allDone ? 'done' : '') }}">{{ $allDone ? '✓' : ($hasInProgress ? '🔥' : '3') }}</div>
                            <span class="progress-label {{ $hasInProgress ? 'active' : ($allDone ? 'done' : '') }}">Dimasak</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot {{ $allDone && !$bookingPendingApproval ? 'done' : '' }}">{{ $allDone && !$bookingPendingApproval ? '✓' : '4' }}</div>
                            <span class="progress-label {{ $allDone && !$bookingPendingApproval ? 'done' : '' }}">Selesai</span>
                        </div>
                    </div>
                @else
                    {{-- Non-booking progress: Bayar → Antre → Dimasak → Selesai --}}
                    <div class="progress-steps">
                        <div class="progress-step">
                            <div class="progress-dot done">✓</div>
                            <span class="progress-label done">Bayar</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot {{ $hasPending || $hasInProgress || $allDone ? 'done' : '' }}">{{ $hasPending || $hasInProgress || $allDone ? '✓' : '2' }}</div>
                            <span class="progress-label {{ $hasPending || $hasInProgress || $allDone ? 'done' : '' }}">Antre</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot {{ $hasInProgress ? 'active' : ($allDone ? 'done' : '') }}">{{ $allDone ? '✓' : ($hasInProgress ? '🔥' : '3') }}</div>
                            <span class="progress-label {{ $hasInProgress ? 'active' : ($allDone ? 'done' : '') }}">Dimasak</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot {{ $allDone ? 'done' : '' }}">{{ $allDone ? '✓' : '4' }}</div>
                            <span class="progress-label {{ $allDone ? 'done' : '' }}">Selesai</span>
                        </div>
                    </div>
                @endif

            {{-- ============ OPEN / PENDING ============ --}}
            @else
                @if($bookingPendingApproval)
                    <div class="status-icon pending pulse">📅</div>
                    <div class="badge badge-pending">Reservasi Terkirim</div>
                    <h2 class="status-title">Menunggu Konfirmasi Kasir</h2>
                    <p class="status-desc">Reservasi Anda untuk tanggal <strong>{{ $transaction->booking->booking_time->translatedFormat('d M Y, H:i') }}</strong> sudah terkirim. Kasir akan mengonfirmasi reservasi Anda. Setelah dikonfirmasi, Anda bisa melakukan pembayaran. Halaman ini akan update otomatis.</p>
                    <div class="progress-steps">
                        <div class="progress-step">
                            <div class="progress-dot active">⏳</div>
                            <span class="progress-label active">Reservasi</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">2</div>
                            <span class="progress-label">Konfirmasi</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">3</div>
                            <span class="progress-label">Bayar</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">4</div>
                            <span class="progress-label">Selesai</span>
                        </div>
                    </div>
                @elseif($bookingCancelled)
                    <div class="status-icon void">❌</div>
                    <div class="badge badge-void">Reservasi Ditolak</div>
                    <h2 class="status-title">Reservasi Dibatalkan</h2>
                    <p class="status-desc">Maaf, reservasi Anda tidak dapat dikonfirmasi oleh kasir. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                @elseif($bookingConfirmed)
                    <div class="status-icon paid">🎉</div>
                    <div class="badge badge-paid">✅ Reservasi Dikonfirmasi</div>
                    <h2 class="status-title">Silakan Pilih Metode Pembayaran</h2>
                    <p class="status-desc">Reservasi Anda untuk <strong>{{ $transaction->booking->booking_time->translatedFormat('d M Y, H:i') }}</strong> sudah dikonfirmasi kasir. Silakan pilih cara pembayaran di bawah.</p>
                    
                    <div style="display:flex; gap:0.75rem; margin-top:1.25rem; justify-content:center; flex-wrap:wrap;">
                        <form action="{{ route('public.booking-pay', $transaction) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="digital">
                            <button type="submit" class="btn btn-primary" style="padding:0.85rem 1.5rem; font-size:0.95rem; background:linear-gradient(135deg, #10b981, #059669);">
                                💳 Bayar Digital (Midtrans)
                            </button>
                        </form>
                    </div>

                    <div class="progress-steps">
                        <div class="progress-step">
                            <div class="progress-dot done">✓</div>
                            <span class="progress-label done">Reservasi</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot done">✓</div>
                            <span class="progress-label done">Konfirmasi</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot active">⏳</div>
                            <span class="progress-label active">Bayar</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">4</div>
                            <span class="progress-label">Selesai</span>
                        </div>
                    </div>
                @elseif($transaction->payment_method === 'cash')
                    <div class="status-icon pending pulse">💰</div>
                    <div class="badge badge-pending">Menunggu Pembayaran</div>
                    <h2 class="status-title">Silakan Bayar di Kasir</h2>
                    <p class="status-desc">Pesanan Anda sudah tercatat. Silakan menuju kasir untuk melakukan pembayaran tunai sebesar <strong>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</strong>. Halaman ini akan update otomatis setelah kasir mengonfirmasi.</p>
                    @if(session('cash_booking_submitted'))
                        <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:0.75rem;padding:0.85rem 1rem;margin-top:1rem;font-size:0.85rem;color:#065f46;text-align:left;">
                            ✅ <strong>Pembayaran tunai sudah terdaftar!</strong> Silakan menuju kasir untuk membayar. Halaman ini akan otomatis update setelah kasir mengonfirmasi pembayaran Anda.
                        </div>
                    @endif
                    <div class="progress-steps">
                        <div class="progress-step">
                            <div class="progress-dot active">⏳</div>
                            <span class="progress-label active">Bayar</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">2</div>
                            <span class="progress-label">Antre</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">3</div>
                            <span class="progress-label">Dimasak</span>
                        </div>
                        <div class="progress-step">
                            <div class="progress-dot">4</div>
                            <span class="progress-label">Selesai</span>
                        </div>
                    </div>
                @else
                    <div class="status-icon pending pulse">⏳</div>
                    <div class="badge badge-pending">Menunggu Pembayaran</div>
                    <h2 class="status-title">Processing...</h2>
                    <p class="status-desc">Pembayaran via Midtrans sedang diproses. Halaman ini akan refresh otomatis.</p>
                @endif
            @endif
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Tipe Pesanan</div>
                <div class="info-value">
                    @if($transaction->order_type === 'dine_in') 
                        🍽️ Dine In
                    @elseif($transaction->order_type === 'take_away') 
                        🥡 Takeaway
                    @else 
                        📅 Booking
                    @endif
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">Atas Nama</div>
                <div class="info-value">{{ $transaction->customer_name }}</div>
            </div>
            @if(($transaction->order_type === 'dine_in' || $transaction->order_type === 'booking') && $transaction->table)
            <div class="info-card">
                <div class="info-label">Meja</div>
                <div class="info-value">{{ $transaction->table->table_number }}</div>
            </div>
            @endif
            @if($transaction->order_type === 'booking' && $transaction->booking)
            <div class="info-card">
                <div class="info-label">Waktu Reservasi</div>
                <div class="info-value">{{ $transaction->booking->booking_time->translatedFormat('d M Y, H:i') }}</div>
            </div>
            @endif
        </div>

        <div class="receipt-card">
            <div class="receipt-header">
                <h3>☕ Keshir Coffee & Eatery</h3>
                <div class="meta">Order #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="meta">{{ $transaction->created_at->format('d M Y • H:i') }}</div>
            </div>

            @foreach($transaction->details as $d)
                <div class="r-item">
                    <div style="flex:1;">
                        <div class="r-item-name">
                            {{ $d->qty }}x {{ $d->product->name }}
                            @if($transaction->payment_status === 'paid')
                                @if($d->status === 'done') 
                                    <span class="cooking-status cs-done">✓ Selesai</span>
                                @elseif($d->status === 'in_progress') 
                                    <span class="cooking-status cs-progress">🔥 Dimasak</span>
                                @else 
                                    <span class="cooking-status cs-pending">⏳ Antre</span>
                                @endif
                            @endif
                        </div>
                        <div class="r-item-meta">
                            @if($d->variant) <div>📦 {{ $d->variant->variant_name }}</div> @endif
                            @if($d->addons->count() > 0) <div>➕ {{ implode(', ', $d->addons->map(fn($a) => $a->addon->addon_name ?? '')->toArray()) }}</div> @endif
                            @if($d->notes) <div>📝 "{{ $d->notes }}"</div> @endif
                        </div>
                    </div>
                    <div class="r-item-price">Rp {{ number_format($d->price * $d->qty, 0, ',', '.') }}</div>
                </div>
            @endforeach

            <div class="totals-section">
                <div class="r-total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($transaction->tax_total > 0)
                <div class="r-total-row">
                    <span>Pajak</span>
                    <span>Rp {{ number_format($transaction->tax_total, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($transaction->service_total > 0)
                <div class="r-total-row">
                    <span>Service</span>
                    <span>Rp {{ number_format($transaction->service_total, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="r-total-row r-grand">
                    <span>Total Bayar</span>
                    <span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
            
            @if($transaction->payment_status === 'open')
                <div style="margin-top:1.5rem;text-align:center;">
                    <button onclick="window.location.reload()" class="btn btn-ghost" style="width:100%;">
                        🔄 Refresh Cek Pembayaran
                    </button>
                    <p style="font-size:0.8rem;color:var(--muted);margin-top:0.75rem;">
                        Jika gagal bayar, pesanan akan dibatalkan otomatis.
                    </p>
                </div>
            @endif
        </div>
        
        <div class="action-footer">
            <a href="{{ route('public.menu') }}" class="back-link">← Kembali ke Menu</a>
        </div>
    </div>
</body>
</html>
