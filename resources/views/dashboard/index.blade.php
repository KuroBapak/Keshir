@extends('layouts.app')
@section('title', 'Dashboard — Keshir POS')
@section('content')
<div class="page-header"><h2>📊 Dashboard</h2></div>
<div class="grid-2">
    <div class="card">
        <h3 style="font-size:0.9rem;color:var(--muted);margin-bottom:0.5rem;">Quick Links</h3>
        <div style="display:flex;flex-direction:column;gap:0.5rem;">
            <a href="{{ route('products.index') }}" class="btn btn-outline" style="justify-content:flex-start;">🍽️ Kelola Produk</a>
            <a href="{{ route('ingredients.index') }}" class="btn btn-outline" style="justify-content:flex-start;">🧪 Kelola Bahan Baku</a>
            <a href="{{ route('tables.index') }}" class="btn btn-outline" style="justify-content:flex-start;">🪑 Kelola Meja</a>
            <a href="{{ route('settings.index') }}" class="btn btn-outline" style="justify-content:flex-start;">⚙️ Pengaturan Tax & Service</a>
        </div>
    </div>
    <div class="card">
        <h3 style="font-size:0.9rem;color:var(--muted);margin-bottom:0.5rem;">Info Sistem</h3>
        <p style="font-size:0.85rem;">Selamat datang, <strong>{{ Auth::user()->name }}</strong>.</p>
        <p style="font-size:0.8rem;color:var(--muted);margin-top:0.5rem;">Halaman laporan dan analytics akan ditambahkan di fase berikutnya.</p>
    </div>
</div>
@endsection
