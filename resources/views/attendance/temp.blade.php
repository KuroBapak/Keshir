@extends('layouts.attendance')

@section('title', 'Absensi Sementara — Keshir')

@push('styles')
<style>
    .attendance-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .attendance-header h2 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .attendance-header p {
        color: var(--muted);
        font-size: 0.9rem;
    }
    .attendance-date {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--primary-50);
        color: var(--primary-dark);
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.75rem;
        border: 1px solid var(--primary-100);
    }

    .attendance-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }

    .staff-row {
        display: flex;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }
    .staff-row:hover { background: var(--bg); }
    .staff-row:last-child { border-bottom: none; }
    .staff-row.completed {
        background: #f0fdf4;
    }

    .staff-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
        margin-right: 1rem;
        flex-shrink: 0;
        font-weight: 700;
    }
    .staff-row.completed .staff-avatar {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    }

    .staff-info { flex: 1; min-width: 0; }
    .staff-name { font-weight: 700; font-size: 1rem; color: var(--text); }
    .staff-role { font-size: 0.85rem; color: var(--muted); margin-top: 0.15rem; text-transform: capitalize; }

    .staff-status {
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.85rem;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-badge.checked-in { background: #d1fae5; color: #065f46; }
    .status-badge.done { background: var(--primary-100); color: var(--primary-dark); }
    .status-badge.absent { background: #fee2e2; color: #991b1b; }

    .staff-action { flex-shrink: 0; }
    .staff-action .btn {
        min-width: 110px;
    }
    .staff-action .btn-disabled {
        background: var(--bg-dark);
        color: var(--muted);
        cursor: not-allowed;
        pointer-events: none;
        padding: 0.45rem 0.85rem;
        font-size: 0.8rem;
        border-radius: var(--radius);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .attendance-footer {
        text-align: center;
        padding: 1.5rem;
        background: var(--bg);
        border-top: 1px solid var(--border);
    }

    .attendance-summary {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .summary-card {
        flex: 1;
        min-width: 140px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        text-align: center;
        box-shadow: var(--shadow);
    }
    .summary-card .sc-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
    }
    .summary-card .sc-label {
        font-size: 0.8rem;
        color: var(--muted);
        margin-top: 0.25rem;
    }
    .summary-card.present .sc-value { color: var(--success); }
    .summary-card.done .sc-value { color: var(--primary); }
    .summary-card.absent .sc-value { color: var(--danger); }

    @media (max-width: 640px) {
        .staff-row { flex-wrap: wrap; gap: 0.75rem; }
        .staff-status { width: 100%; margin-right: 0; padding-left: 64px; }
        .staff-action { width: 100%; padding-left: 64px; }
        .staff-action .btn, .staff-action .btn-disabled { width: 100%; }
        .attendance-summary { flex-direction: column; }
    }
</style>
@endpush

@section('content')
    <div class="attendance-header">
        <h2><span>📋</span> Absensi Staff</h2>
        <p>Halaman absensi sementara untuk fase development. Akan diganti IoT device di production.</p>
        <div class="attendance-date">
            📅 {{ now()->translatedFormat('l, d F Y') }} • ⏰ {{ now()->format('H:i') }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert" style="background: var(--primary-50); color: var(--primary-dark); border: 1px solid var(--primary-100); padding: 0.85rem 1rem; border-radius: 0.75rem;">
            ℹ️ {{ session('info') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    @php
        $presentCount = $todayLogs->filter(fn($l) => $l->check_in && !$l->check_out)->count();
        $doneCount = $todayLogs->filter(fn($l) => $l->check_out)->count();
        $absentCount = $users->count() - $todayLogs->count();
    @endphp
    <div class="attendance-summary">
        <div class="summary-card present">
            <div class="sc-value">{{ $presentCount }}</div>
            <div class="sc-label">🟢 Sedang Bekerja</div>
        </div>
        <div class="summary-card done">
            <div class="sc-value">{{ $doneCount }}</div>
            <div class="sc-label">✅ Selesai Shift</div>
        </div>
        <div class="summary-card absent">
            <div class="sc-value">{{ $absentCount }}</div>
            <div class="sc-label">⛔ Belum Absen</div>
        </div>
    </div>

    <div class="attendance-card">
        @foreach($users as $user)
            @php
                $log = $todayLogs->get($user->id);
                $isDone = $log && $log->check_out;
                $isPresent = $log && $log->check_in && !$log->check_out;
            @endphp
            <div class="staff-row {{ $isDone ? 'completed' : '' }}">
                <div class="staff-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="staff-info">
                    <div class="staff-name">{{ $user->name }}</div>
                    <div class="staff-role">{{ $user->role->name }}</div>
                </div>
                <div class="staff-status">
                    @if($isDone)
                        <span class="status-badge done">
                            ✅ Selesai ({{ $log->check_in->format('H:i') }} — {{ $log->check_out->format('H:i') }})
                        </span>
                    @elseif($isPresent)
                        <span class="status-badge checked-in">
                            🟢 Hadir sejak {{ $log->check_in->format('H:i') }}
                        </span>
                    @else
                        <span class="status-badge absent">
                            ⛔ Belum Absen
                        </span>
                    @endif
                </div>
                <div class="staff-action">
                    @if(!$log || !$log->check_in)
                        {{-- Belum check-in: Tampilkan tombol Check In --}}
                        <form action="{{ route('attendance.checkin') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-sm btn-success">✓ Check In</button>
                        </form>
                    @elseif(!$log->check_out)
                        {{-- Sudah check-in, belum checkout: Tampilkan tombol Check Out --}}
                        <form action="{{ route('attendance.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-sm btn-danger">✗ Check Out</button>
                        </form>
                    @else
                        {{-- Sudah check-out: Tidak bisa apa-apa lagi --}}
                        <span class="btn-disabled">🔒 Selesai</span>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="attendance-footer">
            <a href="{{ route('login') }}" class="btn btn-primary">🔑 Ke Halaman Login</a>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script>
    const mqttHost = '{{ config("services.mqtt.host") }}';
    const mqttWsPort = '{{ config("services.mqtt.ws_port") }}';
    const mqttUser = '{{ config("services.mqtt.username") }}';
    const mqttPass = '{{ config("services.mqtt.password") }}';
    
    const brokerUrl = mqttWsPort 
        ? `wss://${mqttHost}:${mqttWsPort}/mqtt`
        : `wss://${mqttHost}/mqtt`;
        
    const client = mqtt.connect(brokerUrl, {
        username: mqttUser,
        password: mqttPass,
        clientId: 'keshir_temp_' + Math.random().toString(16).substr(2, 8)
    });

    client.on('connect', function () {
        console.log('MQTT Connected for auto-refresh');
        // Listen to responses from API or commands to auto-refresh
        client.subscribe('keshir/attendance/+/down/response');
        client.subscribe('keshir/attendance/+/down/cmd');
    });

    client.on('message', function (topic, message) {
        try {
            const data = JSON.parse(message.toString());
            const topicType = topic.split('/').pop();
            
            if (topicType === 'response' && (data.status === 'check_in' || data.status === 'check_out')) {
                // Auto refresh if a valid tap was processed
                setTimeout(() => location.reload(), 2000);
            }
            if (topicType === 'cmd' && data.action === 'register_success') {
                // Auto refresh if a card was registered
                setTimeout(() => location.reload(), 2000);
            }
        } catch (e) {
            console.error('MQTT Parse Error', e);
        }
    });
</script>
@endpush
