# 📖 Keshir User Manual

Selamat datang di Panduan Pengguna (User Manual) Keshir POS. Panduan ini dirancang untuk membantu Staff dan Pelanggan dalam menggunakan seluruh fitur sistem secara optimal.

---

## 1. Panduan Staf (Staff Manual)

### 1.1 Absensi (Check-in / Check-out)
1. Buka halaman absensi (`/absencetemp` selama fase pengembangan).
2. Pilih nama Anda dan klik **Check-In**.
3. Sistem akan mencatat waktu kehadiran Anda dan membandingkannya dengan jadwal Shift Anda (Tepat Waktu atau Terlambat).
4. Setelah selesai bekerja, kembali ke halaman ini dan klik **Check-Out**.
> [!IMPORTANT]
> Sistem menerapkan aturan Anti-Cheating: Staf tidak akan bisa login ke Dashboard jika belum melakukan Check-In absensi untuk hari tersebut. Jika lupa Check-Out di hari sebelumnya, Manager memiliki akses untuk me-reset status Check-Out Anda.

### 1.2 Membuka Shift Kasir (Cash Drawer)
1. Setelah login, Kasir harus masuk ke menu **Kas Laci (Cash Drawer)**.
2. Klik tombol **Buka Shift**.
3. Masukkan jumlah uang fisik/modal awal (Starting Cash) yang ada di laci kasir, lalu klik konfirmasi.
> [!WARNING]
> Anda tidak dapat memproses pesanan di POS dan pelanggan tidak akan bisa membuka halaman QR Menu publik jika Shift Kasir belum dibuka.

### 1.3 Operasional POS (Kasir)
1. Masuk ke halaman **POS Dashboard**.
2. Untuk membuat pesanan baru, pilih tipe pesanan (Dine-in, Takeaway, atau Booking) dan pilih meja jika Dine-In.
3. Tambahkan menu ke keranjang. Anda bisa memilih varian (contoh: Panas/Dingin) dan Add-on (contoh: Ekstra Keju) jika tersedia.
4. Klik **Buat Bill**. Pesanan akan masuk ke daftar **Open Bill** (Belum Dibayar).
5. Buka tagihan tersebut, dan jika pelanggan ingin membayar, klik Checkout dan pilih metode pembayaran (Tunai atau Midtrans).
6. Jika Tunai, masukkan jumlah uang yang diterima, sistem akan menghitung kembalian.
7. Jika transaksi selesai, status akan berubah menjadi Paid (Lunas).

### 1.4 Manajemen Booking (Kasir)
1. Pesanan dengan tipe Booking yang dikirim pelanggan akan muncul di tab/halaman **Booking**.
2. Kasir dapat melihat rincian Booking (waktu kedatangan, meja, menu).
3. Klik **Setujui (Approve)** atau **Tolak (Reject)**.
4. Jika disetujui, meja yang dipilih pelanggan akan otomatis dikunci (berstatus *Booked*) dan pelanggan bisa melanjutkan ke proses pembayaran Digital.

### 1.5 Void & Refund
*   **Void (Batal):** Digunakan untuk membatalkan tagihan yang **Belum Lunas (Open Bill)**. Buka tagihan di POS, lalu klik tombol Void.
*   **Refund (Pengembalian Dana):** Digunakan untuk membatalkan transaksi yang **Sudah Lunas**. Masuk ke menu Refund, cari transaksi, dan setujui pengembalian dana.
> [!NOTE]
> Baik Void maupun Refund akan secara otomatis mengembalikan jumlah stok bahan baku ke dalam sistem Inventory berdasarkan resep yang terikat.

### 1.6 Dapur (Kitchen Dashboard)
1. Staff Dapur masuk ke menu **Kitchen**.
2. Semua pesanan yang masuk dan tervalidasi akan muncul sebagai tiket (Order Card).
3. Staff Dapur dapat mengubah status masakan dari `Pending` → `🔥 Sedang Dimasak (In Progress)` → `✅ Selesai (Done)`.
4. Perubahan status ini akan terlihat secara realtime di layar *Status Pesanan* pelanggan.

### 1.7 Inventory & Resep (Manager/Owner)
1. **Inventory:** Tambahkan bahan baku dan kelola stok masuk (Batch). Setiap stok masuk wajib memiliki **Tanggal Kadaluarsa (Expiry Date)**. Sistem menggunakan metode FIFO (First-In, First-Out) untuk otomatis memotong stok bahan baku yang paling dekat tanggal kadaluarsanya.
2. **Resep:** Masuk ke menu Resep, pilih produk, dan tentukan komponen bahan baku serta takarannya.

### 1.8 Menutup Shift Kasir
1. Di akhir hari kerja, Kasir harus masuk ke menu **Kas Laci**.
2. Pastikan semua *Open Bill* sudah ditutup (dibayar atau divoid).
3. Klik **Tutup Shift**.
4. Masukkan jumlah fisik uang tunai yang ada di laci. Sistem akan menghitung selisih (Discrepancy) antara uang fisik dan catatan sistem.

---

## 2. Panduan Pelanggan (QR Ordering)

### 2.1 Akses Menu
1. Scan QR code yang ada di meja menggunakan kamera smartphone.
2. Anda akan diarahkan ke halaman Menu Utama Keshir secara otomatis tanpa perlu login.
> [!NOTE]
> Jika restoran belum buka (Kasir belum membuka Shift), halaman akan memblokir akses pesanan dan menampilkan pesan "We're Not Open Yet".

### 2.2 Membuat Pesanan
1. Jelajahi menu menggunakan kategori atau fitur pencarian.
2. Pilih produk yang diinginkan. Anda bisa mengatur Varian, menambah Topping (Add-ons), atau menambahkan Catatan Khusus untuk dapur.
3. Klik **Tambah ke Keranjang**.

### 2.3 Checkout (Dine-In / Takeaway)
1. Buka Keranjang Anda dan periksa daftar pesanan.
2. Klik **Checkout**.
3. Pilih tipe **Dine In** atau **Take Away**.
4. Isi Nama, Nomor HP, dan pilih Meja (Sistem secara otomatis menyembunyikan meja yang sedang dipakai).
5. Anda dapat memilih metode pembayaran:
   *   **Digital (Midtrans):** Langsung membayar via QRIS/Transfer.
   *   **Tunai:** Anda harus berjalan ke Kasir untuk memberikan uang tunai.
6. Setelah selesai, Anda akan diarahkan ke layar pelacakan status pesanan.

### 2.4 Checkout (Booking)
1. Buka Keranjang Anda dan klik Checkout.
2. Pilih tipe pesanan **Booking**.
3. Isi detail identitas, Meja yang diinginkan, dan **Waktu Reservasi** (Tanggal & Jam).
4. Kirim pesanan. Anda akan masuk ke halaman *Menunggu Konfirmasi Kasir*.
5. Setelah Kasir menyetujui pesanan Anda, tombol pembayaran akan muncul. 
> [!IMPORTANT]
> Untuk pesanan Booking, Keshir **hanya menerima Pembayaran Digital (Midtrans)** untuk memastikan validitas reservasi dan mencegah pesanan fiktif.

### 2.5 Lacak Status Pesanan
Setelah memesan, jangan tutup halaman *Status Pesanan*. Halaman ini akan ter-update otomatis secara realtime untuk menampilkan apakah makanan Anda sedang Antre, Sedang Dimasak, atau Sudah Selesai!
