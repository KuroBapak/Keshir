@extends('layouts.app')
@section('title', 'Pengaturan Shift — Keshir POS')

@push('styles')
<style>
    .shift-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .shift-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .shift-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .shift-color-bar {
        width: 8px;
        height: 60px;
        border-radius: 4px;
        flex-shrink: 0;
    }
    .shift-info {
        flex: 1;
    }
    .shift-name {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .shift-time {
        color: var(--muted);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .shift-meta {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
        font-size: 0.8rem;
    }
    .shift-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        background: var(--bg-dark);
        color: var(--text);
        font-weight: 600;
    }
    .shift-actions {
        display: flex;
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="shift-header">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
            <span>⏱️</span> Pengaturan Shift
        </h2>
        <p style="color: var(--muted); font-size: 0.9rem;">Kelola jam kerja, batas telat, dan auto-checkout</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">+ Tambah Shift</button>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1rem;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom: 1rem;">❌ {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error" style="margin-bottom: 1rem;">
        <ul>
            @foreach($errors->all() as $err)
                <li>❌ {{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="shifts-list">
    @forelse($shifts as $shift)
        <div class="shift-card">
            <div class="shift-color-bar" style="background-color: {{ $shift->color }};"></div>
            <div class="shift-info">
                <div class="shift-name">{{ $shift->name }}</div>
                <div class="shift-time">
                    <span>🕒 {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</span>
                </div>
                <div class="shift-meta">
                    <span class="shift-badge">⏳ Telat: +{{ $shift->late_threshold }} menit</span>
                    <span class="shift-badge">👥 {{ $shift->users_count }} Karyawan</span>
                </div>
            </div>
            <div class="shift-actions">
                <button class="btn btn-outline btn-sm" onclick="openEditModal({{ $shift->toJson() }})">✏️ Edit</button>
                <form action="{{ route('shifts.destroy', $shift) }}" method="POST" onsubmit="return confirm('Hapus shift ini?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" style="background: #fee2e2; color: #991b1b; border: none;">🗑️ Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 3rem; background: var(--card); border-radius: var(--radius-lg); border: 1px dashed var(--border);">
            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">⏱️</div>
            <p style="color: var(--muted);">Belum ada shift yang dibuat.</p>
        </div>
    @endforelse
</div>

<div class="shift-header" style="margin-top: 3rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
            <span>👥</span> Penugasan Shift Karyawan
        </h2>
        <p style="color: var(--muted); font-size: 0.9rem;">Atur default shift dan izin double shift untuk masing-masing karyawan</p>
    </div>
</div>

<div style="background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow-x: auto; box-shadow: var(--shadow);">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 1rem; text-align: left; background: var(--bg); border-bottom: 1px solid var(--border-light); color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: 0.75rem;">Karyawan</th>
                <th style="padding: 1rem; text-align: left; background: var(--bg); border-bottom: 1px solid var(--border-light); color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: 0.75rem;">Peran</th>
                <th style="padding: 1rem; text-align: left; background: var(--bg); border-bottom: 1px solid var(--border-light); color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: 0.75rem;">Shift & Pengaturan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffUsers as $user)
            <tr style="border-bottom: 1px solid var(--border-light);">
                <td style="padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div style="font-weight: 600;">{{ $user->name }}</div>
                    </div>
                </td>
                <td style="padding: 1rem;">
                    <span style="background: var(--bg-dark); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; color: var(--muted);">
                        {{ $user->role->name }}
                    </span>
                </td>
                <td style="padding: 1rem;">
                    <form action="{{ route('shifts.assign-staff', $user) }}" method="POST" style="display: flex; align-items: center; gap: 1rem;">
                        @csrf
                        @method('PATCH')
                        <select name="default_shift_id" style="padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--bg); font-size: 0.85rem; min-width: 200px;" onchange="this.form.submit()">
                            <option value="">Tanpa Default Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ $user->default_shift_id == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                        <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary);">
                            <input type="checkbox" name="allow_double_shift" value="1" {{ $user->allow_double_shift ? 'checked' : '' }} onchange="this.form.submit()" style="width: 16px; height: 16px; accent-color: var(--primary);">
                            Izinkan Double Shift
                        </label>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="padding: 2rem; text-align: center; color: var(--muted);">Tidak ada data karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Form --}}
<div id="shiftModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: var(--card); padding: 2rem; border-radius: var(--radius-lg); width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 id="modalTitle" style="margin-bottom: 1.5rem; font-size: 1.25rem;">Tambah Shift</h3>
        <form id="shiftForm" method="POST" action="{{ route('shifts.store') }}">
            @csrf
            <div id="methodField"></div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Nama Shift</label>
                <input type="text" name="name" id="shiftName" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" required placeholder="Misal: Shift Pagi">
            </div>

            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Jam Masuk</label>
                    <input type="time" name="start_time" id="shiftStartTime" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Jam Pulang (Auto-Checkout)</label>
                    <input type="time" name="end_time" id="shiftEndTime" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Batas Telat (Menit)</label>
                <input type="number" name="late_threshold" id="shiftLate" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" required value="15" min="0">
                <small style="color: var(--muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Waktu toleransi sebelum dihitung terlambat.</small>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">Warna Label</label>
                <input type="color" name="color" id="shiftColor" value="#3b82f6" style="width: 100%; height: 40px; border: 1px solid var(--border); border-radius: 0.5rem; cursor: pointer;">
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Shift</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('shiftModal');
    const form = document.getElementById('shiftForm');
    const methodField = document.getElementById('methodField');
    const title = document.getElementById('modalTitle');

    function openCreateModal() {
        title.innerText = 'Tambah Shift';
        form.action = '{{ route("shifts.store") }}';
        methodField.innerHTML = '';
        document.getElementById('shiftName').value = '';
        document.getElementById('shiftStartTime').value = '08:00';
        document.getElementById('shiftEndTime').value = '17:00';
        document.getElementById('shiftLate').value = '15';
        document.getElementById('shiftColor').value = '#3b82f6';
        modal.style.display = 'flex';
    }

    function openEditModal(shift) {
        title.innerText = 'Edit Shift';
        form.action = `/shifts/${shift.id}`;
        methodField.innerHTML = '@method("PUT")';
        document.getElementById('shiftName').value = shift.name;
        document.getElementById('shiftStartTime').value = shift.start_time.substring(0, 5);
        document.getElementById('shiftEndTime').value = shift.end_time.substring(0, 5);
        document.getElementById('shiftLate').value = shift.late_threshold;
        document.getElementById('shiftColor').value = shift.color;
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
    }
</script>
@endpush
