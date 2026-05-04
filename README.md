# Keshir POS - Smart Cafe Management System

Keshir adalah sistem POS terintegrasi untuk coffee shop/resto berbasis **Laravel 12 + Blade** yang menggabungkan operasional kasir, dapur, inventory berbasis resep, QR ordering pelanggan, absensi staff, laporan, dan integrasi pembayaran.

## Ringkasan Fitur

1. **Role-based Staff Panel** (Owner, Manager, Cashier, Kitchen Staff)
2. **Attendance Gate** (staff wajib check-in sebelum bisa akses sistem)
3. **Shift & Cash Drawer** (open/close shift, cash in/out, reconciliation)
4. **POS Open Bill** (buat bill, tambah item, checkout cash/digital, receipt)
5. **Kitchen Display** (status item: pending -> in_progress -> done)
6. **Inventory + Recipe FIFO** (deduksi stok otomatis berdasarkan resep & batch expiry)
7. **Public QR Ordering** (menu, cart, checkout, order tracking, booking)
8. **Booking Workflow** (pending/approved/rejected + table locking)
9. **Refund & Restock** (refund transaksi paid + pengembalian stok)
10. **Laporan** (daily summary, best-selling)
11. **Chatbot API (Ollama Local)** untuk customer/cashier context
12. **RFID Attendance API** untuk integrasi perangkat ESP32

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Blade, Tailwind CSS v4, Vite |
| Database | MySQL / SQLite |
| Payment Gateway | Midtrans (Sandbox) |
| AI Chatbot | Ollama Local (`services.ollama`) |
| IoT Attendance | Endpoint API + contoh firmware `docs/esp32_attendance.ino` |

## Struktur Modul Utama

- `app/Http/Controllers` - orchestration per fitur (POS, Checkout, Kitchen, Attendance, Reports, dll)
- `app/Services/TransactionService.php` - core logic transaksi, kalkulasi total, FIFO deduction/restock
- `app/Services/CartService.php` - cart session public menu
- `app/Http/Middleware/CheckAttendance.php` - gate absensi
- `app/Http/Middleware/CheckRole.php` - gate role
- `resources/views` - seluruh UI (dashboard, POS, kitchen, public pages)
- `routes/web.php` - route web utama
- `routes/api.php` - webhook Midtrans, chatbot API, attendance device API

## Quick Start

## 1) Install dependency

```bash
composer install
npm install
```

## 2) Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Atur koneksi database + kredensial Midtrans/Ollama di `.env`.

## 3) Migrate dan seed data

```bash
php artisan migrate
php artisan db:seed
```

Opsional data coffee shop:

```bash
php artisan db:seed --class=CoffeeShopSeeder
```

## 4) Jalankan aplikasi

```bash
php artisan serve
npm run dev
```

## Akun Seeder Default

| Role | Username | Password |
|---|---|---|
| Owner | `owner` | `password` |
| Manager | `manager` | `password` |
| Cashier | `kasir` | `password` |
| Kitchen Staff | `dapur` | `password` |

> Ganti kredensial default sebelum production.

## Dokumentasi

- [SRS - Software Requirements Specification](docs/SRS.md)
- [SDS - Software Design Specification](docs/SDS.md)
- [Testing Specification](docs/TESTING.md)
- [User Manual](docs/USER_MANUAL.md)
- [API Reference](docs/API_REFERENCE.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Changelog](docs/CHANGELOG.md)

## Catatan Implementasi Penting

1. **Frontend saat ini adalah Blade/Tailwind** (bukan React).
2. **Order type internal** di DB untuk takeaway adalah `take_away`; request UI/checkout bisa mengirim `takeaway` lalu dipetakan.
3. **Status booking aktif dipakai di controller**: `pending`, `approved`, `rejected`.
4. **Cash drawer hanya mencatat arus kas fisik** (cash), bukan pembayaran digital.

## License

Project ini mengikuti lisensi default Laravel (MIT), kecuali jika ditentukan berbeda oleh pemilik proyek.
