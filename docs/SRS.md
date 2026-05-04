# Software Requirements Specification (SRS)

## 1. Pendahuluan

## 1.1 Tujuan
Dokumen ini mendefinisikan kebutuhan sistem Keshir POS berdasarkan implementasi yang berjalan saat ini, sebagai acuan pengembangan, pengujian, dan maintenance.

## 1.2 Ruang Lingkup
Keshir POS adalah aplikasi manajemen operasional coffee shop yang mencakup:
- operasional kasir (POS),
- manajemen order dapur,
- inventory berbasis resep,
- pemesanan pelanggan via QR menu,
- booking meja,
- attendance staff (web + API device),
- manajemen shift kasir (cash drawer),
- laporan bisnis,
- chatbot AI berbasis Ollama lokal.

## 1.3 Definisi Singkat
- **Open Bill**: transaksi yang belum lunas (`payment_status=open`).
- **FIFO Batch**: deduksi stok dari batch ingredient dengan expiry paling dekat.
- **Shift**: sesi kerja kasir yang dibuka/ditutup melalui cash drawer.
- **QR Order**: transaksi dari public menu (`source=qr`).

## 1.4 Aktor Sistem
- **Owner**
- **Manager**
- **Cashier**
- **Kitchen Staff**
- **Customer (Public User via QR)**
- **Attendance Device (ESP32/RFID)**
- **Midtrans Webhook**

---

## 2. Gambaran Umum Sistem

## 2.1 Arsitektur Umum
- Backend: Laravel 12
- Frontend: Blade + Tailwind (server-rendered)
- DB: MySQL/SQLite
- Integrasi eksternal: Midtrans, Ollama

## 2.2 Batasan Sistem
- Otorisasi berbasis role + middleware.
- Staff non-owner wajib check-in sebelum bisa mengakses area auth.
- Cashier wajib membuka shift untuk proses transaksi POS.
- Public dine-in/takeaway bergantung pada status operasional.

---

## 3. Kebutuhan Fungsional

## FR-01 Autentikasi & Otorisasi
1. Sistem harus menyediakan login menggunakan `username` + `password`.
2. Sistem harus membatasi akses fitur berdasarkan role.
3. Redirect pasca login harus menyesuaikan role:
   - owner/manager -> `/dashboard`
   - cashier -> `/pos`
   - kitchen_staff -> `/kitchen`

## FR-02 Attendance Gate
1. Owner bypass attendance gate.
2. Staff selain owner harus memiliki log check-in hari ini untuk bisa login.
3. User yang sudah check-out hari ini harus diblokir dari akses (force logout oleh middleware attendance).

## FR-03 Attendance Management
1. Sistem harus menyediakan halaman absensi sementara (`/absencetemp`) untuk check-in/check-out manual.
2. Sistem harus mendukung attendance management (rekap, filter tanggal/user, statistik).
3. Manager/owner dapat reset checkout staff; owner dapat hapus log absensi.
4. Sistem harus mendukung assignment shift default per user dan opsi `allow_double_shift`.

## FR-04 Shift & Cash Drawer
1. Cashier harus membuka shift dengan `starting_cash`.
2. Sistem harus menolak multiple active shift untuk user yang sama.
3. Sistem harus menyimpan log kas (`cash_drawer_logs`) tipe `in/out`.
4. Saat tutup shift, sistem menghitung selisih `ending_cash` vs `expected_ending_cash`.
5. Sistem menyediakan tampilan detail shift sales untuk shift aktif.

## FR-05 Master Data
1. Owner/manager dapat CRUD:
   - category
   - product
   - ingredient + batch
   - recipe
   - table
   - discount
   - settings tax/service
   - shift
2. Product mendukung:
   - multi foto (max 5)
   - variant
   - addon
   - active/inactive state

## FR-06 Inventory & Recipe
1. Ingredient harus menyimpan unit, minimum stock, dan optional `content_per_pack`.
2. Stock in harus melalui batch dengan expiry date.
3. Recipe harus memetakan product -> ingredient -> quantity.
4. Sistem harus dapat menampilkan stok aktif batch (stock > 0) dan urut expiry.

## FR-07 POS Open Bill
1. Cashier/manager/owner dapat membuat bill POS.
2. Cashier tidak boleh membuat bill jika shift belum dibuka.
3. Bill mendukung add/remove item, variant, addon, notes.
4. Bill dapat di-void selama status masih open.

## FR-08 Checkout & Payment POS
1. Checkout metode `cash`:
   - validasi amount >= grand total,
   - payment langsung paid,
   - cash in otomatis tercatat ke cash drawer aktif.
2. Checkout metode `digital`:
   - generate Midtrans Snap token,
   - menunggu konfirmasi pembayaran.
3. Sistem harus menghasilkan receipt untuk transaksi berhasil.

## FR-09 Kitchen Workflow
1. Kitchen dashboard harus menampilkan tiket aktif sesuai aturan:
   - source POS: open/paid
   - source QR: hanya paid
   - booking QR: tampil jika booking approved untuk hari ini
2. Status item: `pending -> in_progress -> done`.
3. Ketika status pindah ke `in_progress`, sistem deduksi ingredient via FIFO.
4. Fitur `mark all done` harus menyelesaikan semua item pending/in_progress.

## FR-10 Public QR Ordering
1. Customer dapat melihat menu publik, cart, checkout, order status, dan riwayat order session.
2. Public menu harus menghitung subtotal, tax, service, grand total.
3. Public menu availability:
   - dine-in/takeaway: memerlukan shift open + ada staff yang belum checkout.
   - booking tetap dapat dibuat meski belum open.
4. Checkout publik mendukung:
   - digital (Midtrans),
   - tunai (status pending sampai dikonfirmasi kasir).

## FR-11 Booking
1. QR booking dibuat dengan status `pending`.
2. Kasir dapat update booking status (`pending`, `approved`, `rejected`).
3. Approved/pending booking (hari ini) harus lock meja jadi `booked`.
4. Rejected booking membuka kembali meja dan dapat mem-void transaksi open.

## FR-12 Midtrans Integration
1. Sistem harus membuat order id unik untuk transaksi digital (`QR-...` / `POS-...`).
2. Webhook Midtrans harus memperbarui payment & transaction status.
3. Jika pembayaran digital gagal/expire/cancel untuk order QR open, transaksi harus di-void.

## FR-13 Refund
1. Refund hanya untuk transaksi paid.
2. Refund harus menyimpan nominal, alasan, dan authorized user.
3. Refund harus mengembalikan stok untuk item in_progress/done.
4. Refund cash harus membuat cash out log.

## FR-14 Reporting
1. Daily summary report:
   - revenue, tax, service, discount, cash vs digital, void count.
2. Best-selling report:
   - aggregate qty & revenue per produk,
   - filter period (`today`, `week`, `month`, `all`).

## FR-15 API Attendance Device
1. Endpoint register kartu RFID ke user.
2. Endpoint tap attendance:
   - unknown card,
   - check-in/check-out,
   - cooldown,
   - already_done.

## FR-16 Chatbot API (Ollama)
1. Endpoint chat menerima message + optional history + role context.
2. Endpoint menu mengembalikan menu aktif per kategori.
3. Endpoint health mengembalikan status chatbot dan reachability Ollama.
4. Chatbot harus mendukung function/tool call untuk best seller, rekomendasi cuaca, detail menu, promo, meja, dan stok (khusus role tertentu).

## FR-17 Multi-language UI (Frontend)
1. Dashboard layout, POS, dan Kitchen memiliki language switcher ID/EN berbasis localStorage.
2. Perubahan bahasa tidak mengubah data backend.

---

## 4. Kebutuhan Non-Fungsional

## NFR-01 Keamanan
- Password harus hashed.
- Endpoint sensitif harus dibatasi middleware auth/role.
- Validasi request wajib untuk form dan endpoint API.

## NFR-02 Performa
- Halaman utama operasional (POS/Kitchen/Public Menu) harus tetap responsif untuk trafik harian kafe.
- Integrasi eksternal (Midtrans/Ollama) harus punya fallback/error handling.

## NFR-03 Reliabilitas
- Deduksi/restock stok harus konsisten pada transaksi, void, refund, dan perubahan status dapur.
- Shift reconciliation harus deterministik (starting + in - out).

## NFR-04 Maintainability
- Logika transaksi inti harus terpusat di `TransactionService`.
- Cart publik harus terisolasi di `CartService`.
- Dokumentasi harus sinkron dengan route/controller/migration.

## NFR-05 Usability
- UI modern, mobile-friendly untuk public ordering.
- Workflow kasir dan dapur harus minim klik dan jelas statusnya.

## NFR-06 Integrasi
- Midtrans sandbox/production dapat dikonfigurasi via env.
- Ollama URL/model dapat diganti via env.
- Attendance device dapat akses endpoint API dengan payload JSON.

---

## 5. Kebutuhan Data (Ringkas)

Entitas utama:
- `users`, `roles`, `shifts`, `attendance_logs`
- `categories`, `products`, `product_variants`, `product_addons`
- `ingredients`, `ingredient_batches`, `recipes`, `recipe_details`
- `tables`, `discounts`, `settings`
- `transactions`, `transaction_details`, `transaction_detail_addons`
- `payments`, `bookings`, `refunds`, `cash_drawers`, `cash_drawer_logs`

---

## 6. Batasan, Risiko, dan Catatan Teknis Saat Ini

1. **Perbedaan istilah takeaway**:
   - request/public menggunakan `takeaway`,
   - internal transaksi menggunakan enum `take_away`.
2. **Status booking**:
   - controller aktif memakai `pending/approved/rejected`.
   - ada migration legacy yang sempat memetakan ke `confirmed/cancelled`.
3. **Status payment webhook**:
   - migration payment enum: `pending/paid/failed/expired`.
   - webhook pernah mengisi `success` pada sebagian alur legacy.

---

## 7. Out of Scope (Versi Saat Ini)

- Integrasi MQTT attendance end-to-end di backend (saat ini masih endpoint HTTP + contoh firmware).
- Engine rekomendasi ML penuh (saat ini rule/tool-based).
- Integrasi multi-outlet dan multi-branch.

---

## 8. Kriteria Penerimaan Umum

1. Semua role dapat mengakses modul sesuai haknya.
2. Proses POS -> Kitchen -> Payment -> Report berjalan tanpa inkonsistensi status.
3. QR order dapat diproses dari menu sampai order status.
4. Refund dan void memulihkan stok sesuai aturan yang berlaku.
5. Dokumen ini konsisten dengan route, controller, service, dan skema DB saat ini.
