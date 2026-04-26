# Implementasi Global Language Switch & Dark Mode

Dokumen ini merangkum rencana teknis untuk mengimplementasikan dua fitur besar: **Sistem Lokalisasi Global (Multibahasa)** dan **Dark/Light Mode Toggle**, agar dapat diaplikasikan ke seluruh halaman sistem tanpa terkecuali.

## User Review Required

Menerjemahkan *seluruh* aplikasi (kategori, produk, kasir, laporan, refund, dll) akan membutuhkan modifikasi pada puluhan file tampilan (`.blade.php`).
**Mohon Konfirmasi:** Apakah Anda setuju saya langsung menerapkannya pada **semua halaman utama** (Dashboard, Master Data, Operasional, dsb) secara sekaligus? Atau halaman utama (Dashboard & Setting) saja dulu?

## Open Questions

1. **Pilihan Bahasa**: Saat ini bahasa utama adalah Indonesia (id). Terjemahan tambahannya adalah English (en). Apakah ada bahasa lain yang ingin ditambahkan sekalian (misal: Mandarin/Jepang)?
2. **Warna Dark Mode**: Secara default, *Dark Mode* akan mengubah latar belakang aplikasi menjadi warna abu gelap elegan (`#0f172a` atau `slate-900` dari Tailwind) dan teks menjadi putih. Apakah Anda setuju menggunakan warna ini?

## Proposed Changes

### 1. Dark/Light Mode System

- **[MODIFY] `resources/views/layouts/app.blade.php`**: Menambahkan `<script>` di `<head>` untuk membaca preferensi dari `localStorage` (mencegah kedipan halaman / FOUC).
- **[CSS Update]**: Menambahkan gaya `[data-theme="dark"]` yang menimpa variabel `--bg`, `--card`, `--text`, dll dengan warna gelap.
- **[UI]**: Menambahkan tombol *Toggle Theme* (☀️/🌙) di *header*.

### 2. Localization System (Language Switch)

- **[NEW] `app/Http/Middleware/SetLocale.php`**: *Middleware* baru untuk mengubah bahasa aktif via `App::setLocale()` berdasarkan sesi pengguna.
- **[MODIFY] `bootstrap/app.php`**: Mendaftarkan `SetLocale` middleware.
- **[NEW] `lang/en.json`**: File kamus baru untuk menerjemahkan teks dari bahasa Indonesia ke Inggris.
- **[MODIFY] `routes/web.php`**: Endpoint POST `/locale` untuk memproses ganti bahasa.
- **[MODIFY] `resources/views/**/*.blade.php`**: Mengganti teks statis (contoh: "Total Penjualan") menjadi `{{ __('Total Penjualan') }}`.
