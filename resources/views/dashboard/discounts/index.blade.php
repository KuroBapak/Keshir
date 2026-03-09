@extends('layouts.app')
@section('title', 'Diskon — Keshir')
@section('content')
<div class="page-header"><h2>🏷️ Manajemen Diskon</h2></div>
<div class="card">
    <form action="{{ route('discounts.store') }}" method="POST" class="form-inline mb-2">
        @csrf
        <div class="form-group"><input type="text" name="name" class="form-control" placeholder="Nama diskon" required style="width:180px;"></div>
        <div class="form-group">
            <select name="type" class="form-control" required style="width:140px;">
                <option value="percentage">Persentase (%)</option>
                <option value="nominal">Nominal (Rp)</option>
            </select>
        </div>
        <div class="form-group"><input type="number" name="value" class="form-control" step="0.01" min="0" placeholder="Nilai" required style="width:120px;"></div>
        <input type="hidden" name="is_active" value="1">
        <button type="submit" class="btn btn-sm btn-success">+ Tambah</button>
    </form>
    <table>
        <thead><tr><th>Nama</th><th>Tipe</th><th>Nilai</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($discounts as $d)
            <tr>
                <td style="font-weight:600;">{{ $d->name }}</td>
                <td><span class="badge badge-info">{{ $d->type }}</span></td>
                <td>{{ $d->type === 'percentage' ? $d->value . '%' : 'Rp ' . number_format($d->value, 0, ',', '.') }}</td>
                <td>{!! $d->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' !!}</td>
                <td>
                    <form action="{{ route('discounts.destroy', $d) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus diskon?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="empty-state">Belum ada diskon.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">{{ $discounts->links() }}</div>
</div>
@endsection
