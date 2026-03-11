<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Kasir — Keshir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --primary-dark:#1d4ed8; --bg:#f1f5f9; --card:#fff; --text:#1e293b; --muted:#64748b; --border:#e2e8f0; --success:#16a34a; --danger:#dc2626; --warning:#f59e0b; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); }
        .pos-nav { background:var(--primary); color:#fff; padding:0.6rem 1.25rem; display:flex; justify-content:space-between; align-items:center; }
        .pos-nav h1 { font-size:1.1rem; font-weight:700; }
        .pos-nav .info { display:flex; align-items:center; gap:1rem; font-size:0.82rem; }
        .btn { padding:0.45rem 0.85rem; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.8rem; font-weight:600; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem; }
        .btn-primary { background:var(--primary); color:#fff; } .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:#fff; }
        .btn-danger { background:var(--danger); color:#fff; }
        .btn-warning { background:var(--warning); color:#fff; }
        .btn-outline { background:transparent; border:1px solid var(--border); color:var(--text); }
        .btn-sm { padding:0.3rem 0.6rem; font-size:0.75rem; }
        .btn-xs { padding:0.2rem 0.45rem; font-size:0.7rem; }
        .alert { padding:0.6rem 0.85rem; border-radius:0.4rem; margin-bottom:0.75rem; font-size:0.8rem; }
        .alert-success { background:#dcfce7; color:#166534; }
        .alert-error { background:#fef2f2; color:#991b1b; }
        .pos-layout { display:flex; height:calc(100vh - 48px); overflow:hidden; }
        .pos-left { flex:1; overflow-y:auto; padding:1rem; border-right:1px solid var(--border); background:var(--bg); }
        .pos-right { width:380px; display:flex; flex-direction:column; background:var(--card); }
        .card { background:var(--card); border-radius:0.6rem; box-shadow:0 1px 3px rgba(0,0,0,0.06); padding:1rem; margin-bottom:0.75rem; }
        .section-title { font-size:0.9rem; font-weight:700; margin-bottom:0.75rem; }
        .form-control { padding:0.45rem 0.6rem; border:1px solid var(--border); border-radius:0.4rem; font-size:0.82rem; width:100%; }
        .form-control:focus { outline:none; border-color:var(--primary); }
        select.form-control { appearance:auto; }
        .badge { display:inline-block; padding:0.12rem 0.45rem; border-radius:9999px; font-size:0.68rem; font-weight:600; }
        .badge-success { background:#dcfce7; color:#166534; }
        .badge-warning { background:#fef3c7; color:#92400e; }
        .badge-danger { background:#fef2f2; color:#991b1b; }
        .bill-card { border:1px solid var(--border); border-radius:0.5rem; padding:0.75rem; margin-bottom:0.5rem; transition:all 0.15s; cursor:pointer; }
        .bill-card:hover { border-color:var(--primary); background:#f8faff; }
        .bill-card h4 { font-size:0.85rem; }
        .bill-card .meta { font-size:0.75rem; color:var(--muted); margin-top:0.25rem; }
        /* Modal */
        .modal-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center; }
        .modal-overlay.show { display:flex; }
        .modal { background:#fff; border-radius:0.75rem; padding:1.5rem; width:90%; max-width:450px; max-height:80vh; overflow-y:auto; }
        .modal h3 { margin-bottom:1rem; font-size:1rem; }
    </style>
</head>
<body>
    <nav class="pos-nav">
        <h1>🧾 POS Kasir</h1>
        <div class="info">
            <span>{{ Auth::user()->name }}</span>
            @if(in_array(Auth::user()->role->name, ['owner', 'manager']))
                <a href="{{ route('dashboard') }}" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">← Dashboard</a>
            @else
                <a href="{{ route('cash-drawer.index') }}" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">💰 Kas Laci</a>
            @endif
            <a href="{{ route('pos.tables') }}" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">🪑 Status Meja</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">@csrf<button type="submit" class="btn btn-xs btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.3);">Logout</button></form>
        </div>
    </nav>

    <div class="pos-layout">
        {{-- Left panel: Open Bills + New --}}
        <div class="pos-left">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
            @if(session('warning'))<div class="alert" style="background:#fff3cd;color:#856404;">{!! session('warning') !!}</div>@endif

            @if(!$hasActiveShift)
            <div class="alert" style="background:#fff3cd;color:#856404;border:1px solid #ffc107;display:flex;justify-content:space-between;align-items:center;">
                <span>⚠️ <strong>Shift belum dibuka!</strong> Anda tidak bisa membuat transaksi tanpa shift aktif.</span>
                <a href="{{ route('cash-drawer.index') }}" class="btn btn-sm btn-warning">🔓 Buka Shift</a>
            </div>
            @endif

            <div class="section-title">🆕 Buat Open Bill Baru</div>
            <div class="card">
                <form action="{{ route('pos.createBill') }}" method="POST" style="display:flex;gap:0.5rem;align-items:flex-end;flex-wrap:wrap;">
                    @csrf
                    <div style="flex:1;min-width:140px;">
                        <label style="font-size:0.75rem;font-weight:600;display:block;margin-bottom:0.2rem;">Tipe</label>
                        <select name="order_type" class="form-control" required>
                            <option value="dine_in">Dine In</option>
                            <option value="takeaway">Takeaway</option>
                        </select>
                    </div>
                    <div style="flex:1;min-width:140px;">
                        <label style="font-size:0.75rem;font-weight:600;display:block;margin-bottom:0.2rem;">Meja (opsional)</label>
                        <select name="table_id" class="form-control">
                            <option value="">— Tanpa Meja —</option>
                            @foreach($tables as $t)
                                <option value="{{ $t->id }}">{{ $t->table_number }} ({{ $t->capacity }} kursi)</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" {{ !$hasActiveShift ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>+ Buat Bill</button>
                </form>
            </div>

            <div class="section-title">📋 Bills Aktif ({{ $openBills->count() }})</div>
            @forelse($openBills as $bill)
                <a href="{{ $bill->payment_status === 'open' ? route('pos.bill', $bill) : '#' }}" style="text-decoration:none;color:inherit;{{ $bill->payment_status !== 'open' ? 'pointer-events:none;' : '' }}">
                    <div class="bill-card" style="{{ $bill->payment_status === 'paid' ? 'border-left:3px solid var(--success);background:#f0fdf4;' : '' }}">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <h4>Bill #{{ $bill->bill_number ?? $bill->id }}</h4>
                            <div style="display:flex;gap:0.25rem;">
                                @if($bill->payment_status === 'paid')
                                    <span class="badge badge-success">✅ Paid</span>
                                @endif
                                <span class="badge badge-warning">{{ ucfirst($bill->order_type) }}</span>
                            </div>
                        </div>
                        <div class="meta">
                            {{ $bill->table ? '🪑 ' . $bill->table->table_number : '🛍️ Takeaway' }}
                            · {{ $bill->details->count() }} item
                            · Rp {{ number_format($bill->grand_total, 0, ',', '.') }}
                            · {{ $bill->created_at->diffForHumans() }}
                            @if($bill->payment_status === 'paid')
                                · 🔥 Masih dimasak
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="card" style="text-align:center;color:var(--muted);font-size:0.85rem;">
                    Belum ada open bill. Buat baru di atas ↑
                </div>
            @endforelse
        </div>

        {{-- Right panel: Product Catalog (Quick Reference) --}}
        <div class="pos-right" style="padding:1rem;overflow-y:auto;">
            <div class="section-title">🍽️ Katalog Produk</div>
            @foreach($categories as $cat)
                <div style="margin-bottom:0.75rem;">
                    <div style="font-size:0.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:0.35rem;">{{ $cat->name }}</div>
                    @foreach($products->where('category_id', $cat->id) as $p)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:0.35rem 0;border-bottom:1px solid var(--border);font-size:0.82rem;">
                            <span>{{ $p->name }}</span>
                            <span style="font-weight:600;color:var(--primary);">Rp {{ number_format($p->base_price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
