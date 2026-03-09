<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15">
    <title>Dapur — Keshir POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary:#2563eb; --bg:#1e293b; --card:#fff; --text:#1e293b; --muted:#64748b; --border:#e2e8f0; --success:#16a34a; --danger:#dc2626; --warning:#f59e0b; --cooking:#f97316; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:#fff; min-height:100vh; }
        .kitchen-nav { background:#0f172a; padding:0.75rem 1.5rem; display:flex; justify-content:space-between; align-items:center; }
        .kitchen-nav h1 { font-size:1.15rem; font-weight:700; }
        .kitchen-nav .info { display:flex; align-items:center; gap:1rem; font-size:0.82rem; }
        .btn { padding:0.45rem 0.85rem; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.8rem; font-weight:600; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem; }
        .btn-success { background:var(--success); color:#fff; }
        .btn-warning { background:var(--cooking); color:#fff; }
        .btn-outline { background:transparent; border:1px solid rgba(255,255,255,0.2); color:#fff; }
        .btn-sm { padding:0.3rem 0.6rem; font-size:0.75rem; }
        .btn-xs { padding:0.2rem 0.45rem; font-size:0.7rem; }
        .alert { padding:0.6rem 0.85rem; border-radius:0.4rem; margin:0.75rem 1rem 0; font-size:0.8rem; }
        .alert-success { background:#dcfce7; color:#166534; }
        .ticket-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:1rem; padding:1rem; }
        .ticket { background:var(--card); color:var(--text); border-radius:0.75rem; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.3); }
        .ticket-header { padding:0.75rem 1rem; display:flex; justify-content:space-between; align-items:center; font-weight:700; }
        .ticket-header.pending { background:#fef3c7; color:#92400e; }
        .ticket-header.cooking { background:#ffedd5; color:#c2410c; }
        .ticket-body { padding:0.75rem 1rem; }
        .ticket-item { display:flex; justify-content:space-between; align-items:center; padding:0.4rem 0; border-bottom:1px solid var(--border); font-size:0.82rem; }
        .ticket-item:last-child { border-bottom:none; }
        .item-info .name { font-weight:600; }
        .item-info .detail { font-size:0.72rem; color:var(--muted); }
        .status-badge { display:inline-block; padding:0.15rem 0.5rem; border-radius:9999px; font-size:0.68rem; font-weight:700; }
        .status-pending { background:#fef3c7; color:#92400e; }
        .status-cooking { background:#ffedd5; color:#c2410c; }
        .status-done { background:#dcfce7; color:#166534; }
        .ticket-footer { padding:0.5rem 1rem; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:0.4rem; background:#f8fafc; }
        .empty-kitchen { text-align:center; padding:4rem; color:rgba(255,255,255,0.5); font-size:1.1rem; }
        .timestamp { font-size:0.7rem; color:var(--muted); }
    </style>
</head>
<body>
    <nav class="kitchen-nav">
        <h1>🍳 Kitchen Dashboard</h1>
        <div class="info">
            <span>{{ Auth::user()->name }}</span>
            <span style="opacity:0.5;">Auto-refresh 15s</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">@csrf<button type="submit" class="btn btn-xs btn-outline">Logout</button></form>
        </div>
    </nav>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    @if($tickets->count() > 0)
    <div class="ticket-grid">
        @foreach($tickets as $ticket)
            @php
                $hasPending = $ticket->details->where('status', 'pending')->count();
                $hasCooking = $ticket->details->where('status', 'in_progress')->count();
                $headerClass = $hasCooking > 0 ? 'cooking' : 'pending';
            @endphp
            <div class="ticket">
                <div class="ticket-header {{ $headerClass }}">
                    <div>
                        <span>🧾 #{{ $ticket->id }}</span>
                        @if($ticket->table)
                            <span style="margin-left:0.5rem;">🪑 {{ $ticket->table->table_number }}</span>
                        @else
                            <span style="margin-left:0.5rem;">🛍️ Takeaway</span>
                        @endif
                    </div>
                    <span class="timestamp">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                <div class="ticket-body">
                    @foreach($ticket->details as $d)
                        <div class="ticket-item">
                            <div class="item-info">
                                <div class="name">
                                    {{ $d->qty }}x {{ $d->product->name }}
                                    @if($d->variant) <span style="color:var(--muted);">({{ $d->variant->variant_name }})</span> @endif
                                </div>
                                @foreach($d->addons as $a)<div class="detail">+ {{ $a->addon->addon_name }}</div>@endforeach
                                @if($d->notes)<div class="detail" style="color:var(--warning);">📝 {{ $d->notes }}</div>@endif
                            </div>
                            <div style="display:flex;align-items:center;gap:0.35rem;">
                                @if($d->status === 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                    <form action="{{ route('kitchen.updateStatus', $d) }}" method="POST">@csrf @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-xs btn-warning">🔥 Masak</button>
                                    </form>
                                @elseif($d->status === 'in_progress')
                                    <span class="status-badge status-cooking">Cooking</span>
                                    <form action="{{ route('kitchen.updateStatus', $d) }}" method="POST">@csrf @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="btn btn-xs btn-success">✅ Done</button>
                                    </form>
                                @else
                                    <span class="status-badge status-done">Done</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="ticket-footer">
                    <form action="{{ route('kitchen.markAllDone', $ticket) }}" method="POST">@csrf
                        <button type="submit" class="btn btn-sm btn-success">✅ Semua Selesai</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="empty-kitchen">
            <div style="font-size:3rem;margin-bottom:0.5rem;">🍳</div>
            <div>Tidak ada pesanan aktif saat ini.</div>
            <div style="font-size:0.85rem;margin-top:0.25rem;">Pesanan baru akan muncul secara otomatis.</div>
        </div>
    @endif
</body>
</html>
