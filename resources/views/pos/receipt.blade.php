<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->bill_number ?? $transaction->id }} — Keshir POS</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Courier New', monospace; background:#f1f5f9; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .receipt { background:#fff; width:320px; padding:1.5rem; border-radius:0.5rem; box-shadow:0 4px 20px rgba(0,0,0,0.1); }
        .receipt h2 { text-align:center; font-size:1.1rem; margin-bottom:0.25rem; }
        .receipt .subtitle { text-align:center; font-size:0.75rem; color:#64748b; margin-bottom:0.75rem; }
        .divider { border:none; border-top:1px dashed #ccc; margin:0.5rem 0; }
        .row { display:flex; justify-content:space-between; font-size:0.78rem; margin-bottom:0.2rem; }
        .row.bold { font-weight:700; }
        .row.grand { font-size:0.95rem; font-weight:700; margin-top:0.25rem; }
        .item-name { font-weight:600; font-size:0.8rem; }
        .item-detail { font-size:0.72rem; color:#64748b; margin-left:0.5rem; }
        .center { text-align:center; }
        .btn { display:inline-block; padding:0.45rem 1rem; border:none; border-radius:0.4rem; font-size:0.8rem; font-weight:600; cursor:pointer; text-decoration:none; margin-top:1rem; }
        .btn-primary { background:#2563eb; color:#fff; }
        @media print { .no-print { display:none; } body { background:#fff; } .receipt { box-shadow:none; } }
    </style>
</head>
<body>
    <div class="receipt">
        <h2>☕ Keshir POS</h2>
        <div class="subtitle">Struk Pembayaran</div>
        <hr class="divider">
        <div class="row"><span>No. Bill</span><span>#{{ $transaction->bill_number ?? $transaction->id }}</span></div>
        <div class="row"><span>Tanggal</span><span>{{ $transaction->updated_at->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span>Kasir</span><span>{{ $transaction->cashier->name ?? '-' }}</span></div>
        @if($transaction->table)<div class="row"><span>Meja</span><span>{{ $transaction->table->table_number }}</span></div>@endif
        <div class="row"><span>Metode</span><span>{{ strtoupper($transaction->payment_method) }}</span></div>
        <hr class="divider">

        @foreach($transaction->details as $d)
            <div class="item-name">{{ $d->product->name }} @if($d->variant)({{ $d->variant->variant_name }})@endif</div>
            @foreach($d->addons as $a)<div class="item-detail">+ {{ $a->addon->addon_name }}</div>@endforeach
            @if($d->notes)<div class="item-detail">📝 {{ $d->notes }}</div>@endif
            <div class="row"><span style="margin-left:0.5rem;">{{ $d->qty }} x Rp {{ number_format($d->price + $d->addons->sum('price'), 0, ',', '.') }}</span><span>Rp {{ number_format(($d->price + $d->addons->sum('price')) * $d->qty, 0, ',', '.') }}</span></div>
        @endforeach

        <hr class="divider">
        <div class="row"><span>Subtotal</span><span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span></div>
        @if($transaction->discount_total > 0)
        <div class="row" style="color:#dc2626;"><span>Diskon{{ $transaction->discount ? ' ('.$transaction->discount->name.')' : '' }}</span><span>- Rp {{ number_format($transaction->discount_total, 0, ',', '.') }}</span></div>@endif
        @if($transaction->tax_total > 0)
        <div class="row"><span>Pajak</span><span>Rp {{ number_format($transaction->tax_total, 0, ',', '.') }}</span></div>@endif
        @if($transaction->service_total > 0)
        <div class="row"><span>Service</span><span>Rp {{ number_format($transaction->service_total, 0, ',', '.') }}</span></div>@endif
        <hr class="divider">
        <div class="row grand"><span>TOTAL</span><span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span></div>

        @if($transaction->payment)
        <div class="row"><span>Dibayar</span><span>Rp {{ number_format($transaction->payment->amount_paid, 0, ',', '.') }}</span></div>
        @if($transaction->payment->change_amount > 0)
        <div class="row bold"><span>Kembalian</span><span>Rp {{ number_format($transaction->payment->change_amount, 0, ',', '.') }}</span></div>@endif
        @endif

        <hr class="divider">
        <div class="center" style="font-size:0.72rem;color:#64748b;margin-top:0.25rem;">Terima kasih!<br>{{ now()->format('d/m/Y H:i:s') }}</div>

        <div class="center no-print">
            <button class="btn btn-primary" onclick="window.print()">🖨️ Print</button>
            <a href="{{ route('pos.index') }}" class="btn btn-primary" style="background:#64748b;">← POS</a>
        </div>
    </div>
</body>
</html>
