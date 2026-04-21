@extends('layouts.app')
@section('title', 'Manajemen Absensi — Keshir POS')

@push('styles')
<style>
    /* ========== HEADER ========== */
    .att-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .att-header h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .att-header .subtitle {
        color: var(--muted);
        font-size: 0.9rem;
    }

    /* ========== TODAY STATUS BANNER ========== */
    .today-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, var(--primary) 50%, #06b6d4 100%);
        border-radius: var(--radius-lg);
        padding: 1.75rem 2rem;
        color: #fff;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .today-banner::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .today-banner::after {
        content: '';
        position: absolute;
        bottom: -40%;
        right: 15%;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .today-banner .tb-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .today-banner .tb-date {
        opacity: 0.8;
        font-weight: 400;
        font-size: 0.9rem;
        margin-left: 0.5rem;
    }
    .today-stats {
        display: flex;
        gap: 1.5rem;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
    }
    .today-stat {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        min-width: 140px;
        flex: 1;
    }
    .today-stat .ts-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .today-stat .ts-label {
        font-size: 0.8rem;
        opacity: 0.85;
        margin-top: 0.25rem;
    }

    /* ========== FILTER BAR ========== */
    .filter-bar {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
        box-shadow: var(--shadow);
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .filter-group label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
    }
    .filter-group input,
    .filter-group select {
        padding: 0.6rem 0.85rem;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-family: inherit;
        background: var(--bg);
        color: var(--text);
        transition: all 0.2s ease;
        min-width: 160px;
    }
    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-100);
    }
    .filter-actions {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }

    /* ========== STATS GRID ========== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .stat-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .stat-card.primary::before { background: linear-gradient(180deg, var(--primary) 0%, #3b82f6 100%); }
    .stat-card.success::before { background: linear-gradient(180deg, #10b981 0%, #34d399 100%); }
    .stat-card.warning::before { background: linear-gradient(180deg, #f59e0b 0%, #fbbf24 100%); }
    .stat-card.purple::before { background: linear-gradient(180deg, #8b5cf6 0%, #a78bfa 100%); }
    .stat-card.danger::before { background: linear-gradient(180deg, #ef4444 0%, #f87171 100%); }
    .stat-card.cyan::before { background: linear-gradient(180deg, #06b6d4 0%, #22d3ee 100%); }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }
    .stat-card.primary .stat-icon { background: var(--primary-50); }
    .stat-card.success .stat-icon { background: #d1fae5; }
    .stat-card.warning .stat-icon { background: #fef3c7; }
    .stat-card.purple .stat-icon { background: #ede9fe; }
    .stat-card.danger .stat-icon { background: #fee2e2; }
    .stat-card.cyan .stat-icon { background: #cffafe; }

    .stat-label { font-size: 0.8rem; color: var(--muted); margin-bottom: 0.2rem; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text); }

    /* ========== TABS ========== */
    .att-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 0;
        border-bottom: 2px solid var(--border);
    }
    .att-tab {
        padding: 0.85rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
        font-family: inherit;
    }
    .att-tab:hover { color: var(--primary); }
    .att-tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ========== TABLE ========== */
    .att-table-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    .att-table-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .att-table-header h3 {
        font-size: 1.05rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .att-table {
        width: 100%;
        border-collapse: collapse;
    }
    .att-table thead th {
        background: var(--bg);
        padding: 0.85rem 1rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        text-align: left;
        white-space: nowrap;
    }
    .att-table tbody tr { transition: background 0.2s ease; }
    .att-table tbody tr:hover { background: var(--primary-50); }
    .att-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border-light);
        font-size: 0.875rem;
    }
    .att-table tbody tr:last-child td { border-bottom: none; }

    /* Staff identity cell */
    .staff-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .staff-cell .avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .staff-cell .name { font-weight: 600; color: var(--text); }
    .staff-cell .role { font-size: 0.78rem; color: var(--muted); text-transform: capitalize; }

    /* Duration badge */
    .duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.65rem;
        background: var(--primary-50);
        color: var(--primary-dark);
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* Recap table specifics */
    .recap-bar {
        height: 6px;
        border-radius: 3px;
        background: var(--bg-dark);
        overflow: hidden;
        width: 100%;
        max-width: 120px;
    }
    .recap-bar-fill {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        transition: width 0.5s ease;
    }
    .recap-bar-fill.danger {
        background: linear-gradient(90deg, #ef4444, #f87171);
    }
    .recap-bar-fill.warning {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }
    .recap-bar-fill.success {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    /* Rate circle */
    .rate-circle {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }
    .rate-value {
        font-size: 1rem;
        font-weight: 800;
    }
    .rate-value.success { color: #10b981; }
    .rate-value.warning { color: #f59e0b; }
    .rate-value.danger { color: #ef4444; }

    /* Empty state */
    .empty-state-att {
        text-align: center;
        padding: 3rem 2rem;
    }
    .empty-state-att .icon { font-size: 3rem; margin-bottom: 0.75rem; opacity: 0.4; }
    .empty-state-att p { color: var(--muted); font-size: 0.9rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .att-header { flex-direction: column; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-group input,
        .filter-group select { min-width: 100%; }
        .today-stats { flex-direction: column; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="att-header">
    <div>
        <h2><span>📋</span> Manajemen Absensi</h2>
        <p class="subtitle">Kelola dan pantau kehadiran seluruh staff</p>
    </div>
    <a href="{{ route('attendance.temp') }}" class="btn btn-primary" target="_blank">
        📱 Halaman Absensi
    </a>
</div>

{{-- Today's Status Banner --}}
<div class="today-banner">
    <div class="tb-title">
        🔴 Status Hari Ini
        <span class="tb-date">— {{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="today-stats">
        <div class="today-stat">
            <div class="ts-value">{{ $stats['today_present'] }}</div>
            <div class="ts-label">🟢 Sedang Bekerja</div>
        </div>
        <div class="today-stat">
            <div class="ts-value">{{ $stats['today_done'] }}</div>
            <div class="ts-label">✅ Selesai Shift</div>
        </div>
        <div class="today-stat">
            <div class="ts-value">{{ $stats['today_absent'] }}</div>
            <div class="ts-label">⛔ Belum Absen</div>
        </div>
        <div class="today-stat">
            <div class="ts-value">{{ $staffUsers->count() }}</div>
            <div class="ts-label">👥 Total Staff</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('attendance.management') }}" class="filter-bar" id="filterForm">
    <div class="filter-group">
        <label>📅 Dari Tanggal</label>
        <input type="date" name="start_date" value="{{ $startDate }}">
    </div>
    <div class="filter-group">
        <label>📅 Sampai Tanggal</label>
        <input type="date" name="end_date" value="{{ $endDate }}">
    </div>
    <div class="filter-group">
        <label>👤 Karyawan</label>
        <select name="user_id">
            <option value="">Semua Staff</option>
            @foreach($staffUsers as $user)
                <option value="{{ $user->id }}" {{ $filterUser == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ ucfirst($user->role->name) }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary btn-sm">🔍 Filter</button>
        <a href="{{ route('attendance.management') }}" class="btn btn-outline btn-sm">↻ Reset</a>
    </div>
</form>

{{-- Period Stats --}}
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon">📊</div>
        <div class="stat-label">Total Kehadiran</div>
        <div class="stat-value">{{ $stats['total_present'] }}</div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Shift Lengkap</div>
        <div class="stat-value">{{ $stats['total_completed'] }}</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon">⛔</div>
        <div class="stat-label">Total Alpha</div>
        <div class="stat-value">{{ $stats['total_alpha'] }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">⏱️</div>
        <div class="stat-label">Rata-rata Jam Kerja</div>
        <div class="stat-value">{{ $stats['avg_hours'] }}j {{ $stats['avg_mins'] }}m</div>
    </div>
    <div class="stat-card cyan">
        <div class="stat-icon">📈</div>
        <div class="stat-label">Tingkat Kehadiran</div>
        <div class="stat-value">{{ $stats['attendance_rate'] }}%</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon">📅</div>
        <div class="stat-label">Hari Kerja (Periode)</div>
        <div class="stat-value">{{ $stats['work_days'] }}</div>
    </div>
</div>

{{-- Tabs --}}
<div class="att-tabs">
    <button class="att-tab active" onclick="switchTab('history', this)">📝 Histori Absensi</button>
    <button class="att-tab" onclick="switchTab('recap', this)">📊 Rekap per Karyawan</button>
</div>

{{-- Tab 1: History --}}
<div class="tab-panel active" id="tab-history">
    <div class="att-table-card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
        <div class="att-table-header">
            <h3><span>📝</span> Riwayat Absensi</h3>
            <span class="badge badge-info">{{ $logs->count() }} record</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Karyawan</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Durasi</th>
                        <th>Sumber</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    @php
                        $duration = null;
                        if ($log->check_in && $log->check_out) {
                            $diffMinutes = $log->check_in->diffInMinutes($log->check_out);
                            $hours = floor($diffMinutes / 60);
                            $mins = $diffMinutes % 60;
                            $duration = "{$hours}j {$mins}m";
                        }
                    @endphp
                    <tr>
                        <td style="font-weight: 600;">
                            {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}
                            <div style="font-size: 0.75rem; color: var(--muted); font-weight: 400;">
                                {{ \Carbon\Carbon::parse($log->date)->translatedFormat('l') }}
                            </div>
                        </td>
                        <td>
                            <div class="staff-cell">
                                <div class="avatar">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</div>
                                <div>
                                    <div class="name">{{ $log->user->name ?? '-' }}</div>
                                    <div class="role">{{ $log->user->role->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($log->check_in)
                                <span style="font-weight: 600; color: #10b981;">{{ $log->check_in->format('H:i') }}</span>
                            @else
                                <span style="color: var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log->check_out)
                                <span style="font-weight: 600; color: #ef4444;">{{ $log->check_out->format('H:i') }}</span>
                            @else
                                <span style="color: var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($duration)
                                <span class="duration-badge">⏱️ {{ $duration }}</span>
                            @else
                                <span style="color: var(--muted); font-size: 0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log->source === 'iot')
                                <span class="badge badge-info">📡 IoT</span>
                            @else
                                <span class="badge badge-gray">🌐 Web</span>
                            @endif
                        </td>
                        <td>
                            @if($log->check_in && $log->check_out)
                                <span class="badge badge-success">✅ Selesai</span>
                            @elseif($log->check_in)
                                <span class="badge badge-warning">🟡 Sedang Kerja</span>
                            @else
                                <span class="badge badge-danger">❌ Tidak Lengkap</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state-att">
                                <div class="icon">📋</div>
                                <p>Tidak ada data absensi untuk periode ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tab 2: Staff Recap --}}
<div class="tab-panel" id="tab-recap">
    <div class="att-table-card" style="border-top-left-radius: 0; border-top-right-radius: 0;">
        <div class="att-table-header">
            <h3><span>📊</span> Rekap Kehadiran per Karyawan</h3>
            <span class="badge badge-primary">
                {{ \Carbon\Carbon::parse($startDate)->format('d M') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </span>
        </div>
        <div style="overflow-x: auto;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th style="text-align:center;">Hadir</th>
                        <th style="text-align:center;">Alpha</th>
                        <th style="text-align:center;">Shift Lengkap</th>
                        <th style="text-align:center;">Rata-rata / Hari</th>
                        <th style="text-align:center;">Total Jam</th>
                        <th>Tingkat Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($staffRecap as $recap)
                    @php
                        $rate = $stats['work_days'] > 0
                            ? round(($recap['total_present'] / $stats['work_days']) * 100, 1)
                            : 0;
                        $rateClass = $rate >= 90 ? 'success' : ($rate >= 70 ? 'warning' : 'danger');
                        $barClass = $rate >= 90 ? 'success' : ($rate >= 70 ? 'warning' : 'danger');
                    @endphp
                    <tr>
                        <td>
                            <div class="staff-cell">
                                <div class="avatar">{{ strtoupper(substr($recap['user']->name, 0, 1)) }}</div>
                                <div>
                                    <div class="name">{{ $recap['user']->name }}</div>
                                    <div class="role">{{ $recap['user']->role->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-weight: 700; color: #10b981; font-size: 1.1rem;">{{ $recap['total_present'] }}</span>
                            <span style="color: var(--muted); font-size: 0.78rem;"> / {{ $stats['work_days'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-weight: 700; color: {{ $recap['total_alpha'] > 0 ? '#ef4444' : 'var(--muted)' }}; font-size: 1.1rem;">
                                {{ $recap['total_alpha'] }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-weight: 700; font-size: 1.1rem;">{{ $recap['total_completed'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="duration-badge">{{ $recap['avg_hours'] }} jam</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-weight: 700;">{{ $recap['total_hours'] }}j</span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="recap-bar">
                                    <div class="recap-bar-fill {{ $barClass }}" style="width: {{ min($rate, 100) }}%;"></div>
                                </div>
                                <span class="rate-value {{ $rateClass }}">{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state-att">
                                <div class="icon">👥</div>
                                <p>Tidak ada data karyawan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabName, btnElement) {
        // Deactivate all tabs
        document.querySelectorAll('.att-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

        // Activate selected
        btnElement.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
    }
</script>
@endpush
