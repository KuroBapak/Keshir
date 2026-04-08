@extends('layouts.app')

@section('title', 'Absensi Sementara — Keshir')

@push('styles')
<style>
    .attendance-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .attendance-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .attendance-header h2 {
        font-size: 1.5rem;
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
    
    .attendance-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow);
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
    }
    
    .staff-info { flex: 1; }
    .staff-name { font-weight: 700; font-size: 1rem; color: var(--text); }
    .staff-role { font-size: 0.85rem; color: var(--muted); margin-top: 0.15rem; }
    
    .staff-status {
        margin-right: 1rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.85rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .status-badge.checked-in { background: #d1fae5; color: #065f46; }
    .status-badge.done { background: var(--primary-100); color: var(--primary-dark); }
    .status-badge.absent { background: #fee2e2; color: #991b1b; }
    
    .staff-action { flex-shrink: 0; }
    .staff-action .btn {
        min-width: 110px;
    }
    
    .attendance-footer {
        text-align: center;
        padding: 1.5rem;
        background: var(--bg);
        border-top: 1px solid var(--border);
    }
    
    .alert {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="attendance-container">
    <div class="attendance-header">
        <h2><span>📋</span> Absensi Staff</h2>
        <p>Halaman absensi sementara untuk fase development. Akan diganti IoT device di production.</p>
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

    <div class="attendance-card">
        @foreach($users as $user)
            @php
                $log = $todayLogs->get($user->id);
            @endphp
            <div class="staff-row">
                <div class="staff-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="staff-info">
                    <div class="staff-name">{{ $user->name }}</div>
                    <div class="staff-role">{{ $user->role->name }}</div>
                </div>
                <div class="staff-status">
                    @if($log && $log->check_out)
                        <span class="status-badge done">
                            ✅ Selesai ({{ $log->check_in->format('H:i') }} — {{ $log->check_out->format('H:i') }})
                        </span>
                    @elseif($log && $log->check_in)
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
                        <form action="{{ route('attendance.checkin') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-sm btn-success">✓ Check In</button>
                        </form>
                    @elseif(!$log->check_out)
                        <form action="{{ route('attendance.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-sm btn-danger">✗ Check Out</button>
                        </form>
                    @else
                        <span style="color: var(--muted); font-size: 0.85rem;">—</span>
                    @endif
                </div>
            </div>
        @endforeach
        
        <div class="attendance-footer">
            <a href="{{ route('login') }}" class="btn btn-primary">← Ke Halaman Login</a>
        </div>
    </div>
</div>
@endsection
