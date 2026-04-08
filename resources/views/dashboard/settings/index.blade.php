@extends('layouts.app')
@section('title', 'Pengaturan — Keshir')

@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
    }
    .settings-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .settings-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
    }
    .settings-header h3 {
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .settings-header p {
        font-size: 0.85rem;
        color: var(--muted);
        margin: 0.35rem 0 0 0;
    }
    .settings-body {
        padding: 1.5rem;
    }
    
    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: var(--bg);
        border-radius: var(--radius);
        margin-bottom: 1rem;
    }
    .toggle-label {
        font-weight: 600;
        color: var(--text);
    }
    .toggle-desc {
        font-size: 0.85rem;
        color: var(--muted);
        margin-top: 0.25rem;
    }
    
    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 28px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked + .toggle-slider {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    
    .rate-input {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .rate-input label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .rate-input input {
        width: 100px;
        text-align: center;
        font-weight: 700;
    }
    .rate-input .suffix {
        font-weight: 600;
        color: var(--muted);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>⚙️ Pengaturan</h2>
        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem;">Konfigurasi pajak dan service charge</p>
    </div>
</div>

<form action="{{ route('settings.update') }}" method="POST">
    @csrf @method('PUT')
    
    <div class="settings-grid">
        <!-- Tax Settings -->
        <div class="settings-card">
            <div class="settings-header">
                <h3><span>🏛️</span> Pajak (PPN)</h3>
                <p>Pengaturan pajak pertambahan nilai</p>
            </div>
            <div class="settings-body">
                <div class="toggle-row">
                    <div>
                        <div class="toggle-label">Aktifkan Pajak</div>
                        <div class="toggle-desc">Pajak akan ditambahkan ke setiap transaksi</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="tax_enabled" value="1" {{ ($settings['tax_enabled']->value ?? '0') == '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="rate-input">
                    <label>Tarif Pajak:</label>
                    <input type="number" name="tax_rate" class="form-control" step="0.1" min="0" max="100" value="{{ $settings['tax_rate']->value ?? '11' }}">
                    <span class="suffix">%</span>
                </div>
            </div>
        </div>
        
        <!-- Service Charge Settings -->
        <div class="settings-card">
            <div class="settings-header">
                <h3><span>🍽️</span> Service Charge</h3>
                <p>Biaya layanan tambahan untuk pelanggan</p>
            </div>
            <div class="settings-body">
                <div class="toggle-row">
                    <div>
                        <div class="toggle-label">Aktifkan Service Charge</div>
                        <div class="toggle-desc">Biaya service akan ditambahkan ke setiap transaksi</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="service_charge_enabled" value="1" {{ ($settings['service_charge_enabled']->value ?? '0') == '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="rate-input">
                    <label>Tarif Service:</label>
                    <input type="number" name="service_charge_rate" class="form-control" step="0.1" min="0" max="100" value="{{ $settings['service_charge_rate']->value ?? '5' }}">
                    <span class="suffix">%</span>
                </div>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">
            💾 Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
