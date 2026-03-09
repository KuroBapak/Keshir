@extends('layouts.app')
@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')
<div class="page-header">
    <h2>{{ isset($category) ? '✏️ Edit Kategori' : '➕ Tambah Kategori' }}</h2>
</div>
<div class="card" style="max-width:500px;">
    <form action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" method="POST">
        @csrf
        @if(isset($category)) @method('PUT') @endif
        <div class="form-group">
            <label for="name">Nama Kategori</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required autofocus>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
