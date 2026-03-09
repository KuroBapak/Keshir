@extends('layouts.app')
@section('title', 'Pengaturan — Keshir')
@section('content')
<div class="page-header"><h2>⚙️ Pengaturan Pajak & Service Charge</h2></div>
<div class="card" style="max-width:500px;">
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label><input type="checkbox" name="tax_enabled" value="1" {{ ($settings['tax_enabled']->value ?? '0') == '1' ? 'checked' : '' }}> Aktifkan Pajak (PPN)</label>
        </div>
        <div class="form-group">
            <label>Tarif Pajak (%)</label>
            <input type="number" name="tax_rate" class="form-control" step="0.1" min="0" max="100" value="{{ $settings['tax_rate']->value ?? '11' }}" style="width:150px;">
        </div>
        <hr style="margin:1rem 0;border:none;border-top:1px solid var(--border);">
        <div class="form-group">
            <label><input type="checkbox" name="service_charge_enabled" value="1" {{ ($settings['service_charge_enabled']->value ?? '0') == '1' ? 'checked' : '' }}> Aktifkan Service Charge</label>
        </div>
        <div class="form-group">
            <label>Tarif Service Charge (%)</label>
            <input type="number" name="service_charge_rate" class="form-control" step="0.1" min="0" max="100" value="{{ $settings['service_charge_rate']->value ?? '5' }}" style="width:150px;">
        </div>
        <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
    </form>
</div>
@endsection
