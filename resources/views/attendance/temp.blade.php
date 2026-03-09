@extends('layouts.app')

@section('title', 'Absensi Sementara — Keshir')

@section('content')
<div class="container" style="max-width:700px;">
    <div class="card" style="margin-top:2rem;">
        <h2 style="margin-bottom:0.25rem;">📋 Absensi Staff (Sementara)</h2>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:1.5rem;">Halaman ini untuk fase development. Akan diganti IoT device di production.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Status Hari Ini</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @php
                        $log = $todayLogs->get($user->id);
                    @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $user->name }}</td>
                        <td><span class="badge badge-warning">{{ $user->role->name }}</span></td>
                        <td>
                            @if($log && $log->check_out)
                                <span class="badge badge-success">✅ Selesai ({{ $log->check_in->format('H:i') }} — {{ $log->check_out->format('H:i') }})</span>
                            @elseif($log && $log->check_in)
                                <span class="badge badge-success">🟢 Checked In ({{ $log->check_in->format('H:i') }})</span>
                            @else
                                <span class="badge badge-danger">⛔ Belum Absen</span>
                            @endif
                        </td>
                        <td>
                            @if(!$log || !$log->check_in)
                                <form action="{{ route('attendance.checkin') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-sm btn-success">Check In</button>
                                </form>
                            @elseif(!$log->check_out)
                                <form action="{{ route('attendance.checkout') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger">Check Out</button>
                                </form>
                            @else
                                <span style="color:var(--text-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:1.5rem;text-align:center;">
            <a href="{{ route('login') }}" class="btn btn-primary">← Ke Halaman Login</a>
        </div>
    </div>
</div>
@endsection
