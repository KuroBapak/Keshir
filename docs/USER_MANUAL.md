# Keshir User Manual

Panduan ini menjelaskan cara menggunakan sistem Keshir untuk semua peran: Owner, Manager, Cashier, Kitchen Staff, dan Customer.

---

## 1. Akses & Login

## 1.1 URL Penting
- Login staff: `/login`
- Absensi sementara: `/absencetemp`
- Dashboard owner/manager: `/dashboard`
- POS kasir: `/pos`
- Kitchen display: `/kitchen`
- Public menu pelanggan: `/menu`

## 1.2 Aturan Login Staff
1. Owner bisa login langsung.
2. Staff non-owner harus check-in dulu di absensi.
3. Jika staff sudah check-out hari ini, akses akan ditolak sampai hari berikutnya.

---

## 2. Panduan Owner & Manager

## 2.1 Dashboard
Owner/Manager dapat mengakses:
- statistik ringkas,
- quick links ke master data,
- laporan penjualan.

## 2.2 Master Data

### Kategori
- Tambah/edit/hapus kategori.
- Kategori yang masih dipakai produk tidak bisa dihapus.

### Produk
- Tambah nama, harga dasar, kategori, deskripsi, tags.
- Upload foto produk (maks 5 file).
- Atur variant dan addon langsung dari form produk.
- Aktif/nonaktif produk via `is_active`.

### Resep
- Atur resep per produk.
- Tentukan komposisi ingredient + quantity per porsi.

### Ingredient & Batch
- Buat ingredient (unit, minimum stock, optional content per pack).
- Stock masuk melalui **batch** (wajib expiry date).
- Sistem menampilkan batch aktif dan total stock.

### Meja
- Tambah/edit/hapus meja.
- Status meja: available, occupied, booked.

### Discount
- Buat diskon nominal/persentase.
- Aktif/nonaktif promo.

### Settings
- Atur tax enabled/rate.
- Atur service charge enabled/rate.

### Shift
- CRUD shift (jam masuk/keluar, late threshold, warna).
- Assign default shift ke user.
- Atur allow double shift.

## 2.3 Attendance Management
- Lihat log absensi berdasarkan rentang tanggal.
- Lihat statistik hadir, selesai, alpha, rata-rata jam.
- Reset checkout staff jika dibutuhkan.
- Owner dapat menghapus log absensi.

## 2.4 Reports

### Daily Summary
- total transaksi paid,
- revenue,
- tax/service/discount,
- split cash vs digital,
- jumlah void.

### Best Selling
- ranking produk terlaris berdasarkan period:
  - today, week, month, all.

---

## 3. Panduan Cashier

## 3.1 Sebelum Mulai Jualan
1. Check-in absensi.
2. Buka menu **Kas Laci** (`/cash-drawer`).
3. Klik **Buka Shift** dan isi modal awal.

> Cashier tidak bisa membuat transaksi POS jika shift belum dibuka.

## 3.2 Operasional POS (`/pos`)

### Membuat Bill
1. Pilih order type (dine-in/takeaway).
2. Pilih meja (untuk dine-in).
3. Klik buat bill.

### Menambah Item
1. Buka bill.
2. Pilih produk dari katalog.
3. Isi qty, variant, addon, catatan.
4. Simpan item.

### Mengelola Bill
- Hapus item jika diperlukan.
- Void bill jika pesanan batal sebelum selesai.

### Checkout
- **Cash**: isi uang diterima, sistem hitung kembalian.
- **Digital**: lanjut ke halaman pembayaran Midtrans.

### Receipt
Setelah payment sukses, receipt dapat dilihat/print.

## 3.3 Booking Management
- Buka daftar booking di POS.
- Approve/reject booking.
- Approve booking (hari ini) akan lock meja ke status booked.

## 3.4 Konfirmasi Cash untuk QR Order
Untuk order QR dengan metode tunai:
1. Kasir menerima uang fisik.
2. Klik konfirmasi cash pada transaksi.
3. Sistem ubah status paid dan mencatat cash in.

## 3.5 Menutup Shift
1. Buka Kas Laci.
2. Klik tutup shift.
3. Isi uang fisik akhir.
4. Sistem tampilkan selisih kas.

---

## 4. Panduan Kitchen Staff

## 4.1 Kitchen Dashboard (`/kitchen`)
- Menampilkan tiket pesanan aktif.
- Menampilkan detail item, variant, addon, notes.

## 4.2 Update Status Item
1. `Pending` -> klik **Masak** (`in_progress`)
2. `In Progress` -> klik **Selesai** (`done`)

> Saat item masuk `in_progress`, stok ingredient otomatis berkurang (FIFO).

## 4.3 Selesai Semua
- Gunakan tombol **Selesai Semua** untuk menandai satu order selesai.

---

## 5. Panduan Customer (Public QR)

## 5.1 Akses Menu
1. Scan QR.
2. Buka `/menu`.
3. Pilih produk, variant, addon, catatan.

## 5.2 Cart & Checkout
1. Buka cart.
2. Pilih tipe pesanan:
   - dine-in
   - takeaway
   - booking
3. Isi nama, telepon, meja, jumlah orang, waktu booking (jika booking).

## 5.3 Payment
- Digital: lewat Midtrans.
- Tunai: status pending sampai kasir konfirmasi.

## 5.4 Order Status
- Lihat status order di halaman `/order/{id}`.
- Tracking tetap bisa diakses dari menu `my orders` pada session yang sama.

---

## 6. Language Switcher (ID/EN)

Halaman berikut mendukung penggantian bahasa frontend:
- dashboard layout,
- POS,
- kitchen.

Bahasa disimpan di browser (`localStorage`) dengan key `keshir_lang`.

---

## 7. Error Umum & Solusi Cepat

| Masalah | Penyebab Umum | Solusi |
|---|---|---|
| Tidak bisa login staff | belum check-in | check-in dulu di `/absencetemp` |
| Cashier tidak bisa buat bill | shift belum open | buka shift di kas laci |
| Order QR dine-in tidak bisa checkout | toko belum open | tunggu shift dibuka + staff aktif |
| Midtrans gagal | server key/env salah / sandbox error | cek `.env` MIDTRANS_* |
| Chatbot tidak merespon | Ollama mati/tidak reachable | nyalakan Ollama, cek `OLLAMA_URL` |

---

## 8. Best Practice Operasional

1. Buka shift hanya saat kas fisik siap.
2. Pastikan setiap produk memiliki resep untuk akurasi inventory.
3. Jangan approve booking jika meja tidak realistis tersedia.
4. Tutup shift tiap akhir operasional.
5. Jalankan audit sederhana harian: transaksi paid, void, refund, dan selisih kas.
