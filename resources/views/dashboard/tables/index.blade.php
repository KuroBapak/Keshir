@extends('layouts.app')
@section('title', 'Meja — Keshir')
@section('content')
<div class="page-header"><h2>🪑 Manajemen Meja</h2></div>
<div class="card">
    <form action="{{ route('tables.store') }}" method="POST" class="form-inline mb-2">
        @csrf
        <div class="form-group"><input type="text" name="table_number" class="form-control" placeholder="Nomor Meja (cth: T11)" required style="width:150px;"></div>
        <div class="form-group"><input type="number" name="capacity" class="form-control" placeholder="Kapasitas" min="1" max="20" required style="width:120px;"></div>
        <button type="submit" class="btn btn-sm btn-success">+ Tambah Meja</button>
    </form>
    <table>
        <thead><tr><th>No. Meja</th><th>Kapasitas</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($tables as $t)
            <tr>
                <td style="font-weight:600;">{{ $t->table_number }}</td>
                <td>{{ $t->capacity }} kursi</td>
                <td>
                    @if($t->status === 'available') <span class="badge badge-success">Tersedia</span>
                    @elseif($t->status === 'occupied') <span class="badge badge-danger">Terpakai</span>
                    @else <span class="badge badge-warning">Booking</span> @endif
                </td>
                <td>
                    <form action="{{ route('tables.destroy', $t) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus meja?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">Belum ada meja.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $tables->links() }}</div>
</div>
@endsection
