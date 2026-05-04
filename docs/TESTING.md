# Software Testing Specification

## 1. Tujuan & Scope

Dokumen ini mendefinisikan strategi uji untuk memastikan modul inti Keshir berjalan stabil:

1. Auth & attendance gate
2. Shift/cash drawer
3. Master data
4. POS workflow
5. Kitchen workflow
6. QR customer ordering
7. Booking
8. Midtrans payment flow
9. Refund + restock
10. Reporting
11. API attendance device
12. Chatbot API

---

## 2. Strategi Pengujian

## 2.1 Jenis Pengujian
- **Manual functional testing** (utama, karena coverage test otomatis masih minim).
- **Integration testing** untuk alur antar modul.
- **Regression testing** pada flow kritikal transaksi.
- **Smoke testing** setiap selesai perubahan besar.

## 2.2 Otomasi Saat Ini
- Saat ini project hanya memiliki test bawaan Laravel (`tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php`).
- Perlu ditambah test feature untuk modul bisnis utama.

## 2.3 Command Dasar

```bash
php artisan test
```

---

## 3. Environment Uji

| Komponen | Nilai Rekomendasi |
|---|---|
| APP_ENV | local/testing |
| DB | sqlite / mysql test db |
| Payment | Midtrans Sandbox |
| AI | Ollama lokal aktif |
| Browser | Chrome/Edge terbaru |

Data minimum:
- role + user seed default,
- table, category, product, ingredient, recipe,
- setting tax/service aktif.

---

## 4. Test Case Matrix

| ID | Modul | Skenario | Expected Result |
|---|---|---|---|
| AUTH-01 | Login | Login owner valid | Sukses, redirect dashboard |
| AUTH-02 | Login | Login staff tanpa check-in | Ditolak dengan pesan attendance |
| AUTH-03 | Login | Login staff setelah check-in | Sukses sesuai redirect role |
| AUTH-04 | Middleware | User sudah check-out lalu akses route auth | User di-logout & ditolak |
| ATT-01 | Attendance | Check-in manual pertama | Log attendance terbentuk |
| ATT-02 | Attendance | Check-in lagi saat belum check-out | Ditolak/info sudah check-in |
| ATT-03 | Attendance | Check-out tanpa check-in | Ditolak dengan error |
| ATT-04 | Attendance | Reset checkout oleh owner/manager | Check_out jadi null |
| ATT-05 | Attendance Device API | Tap UID unknown | Response `unknown_card` |
| ATT-06 | Attendance Device API | Tap check-in valid | Response `check_in` + status_in |
| ATT-07 | Attendance Device API | Tap check-out sebelum cooldown | Response `cooldown` |
| SHIFT-01 | Cash Drawer | Open shift pertama | Shift status `open` |
| SHIFT-02 | Cash Drawer | Open shift saat sudah ada active | Ditolak |
| SHIFT-03 | Cash Drawer | Close shift | status `closed`, expected cash tersimpan |
| POS-01 | POS | Cashier create bill tanpa shift | Ditolak |
| POS-02 | POS | Create open bill dengan table | Bill terbentuk, table occupied |
| POS-03 | POS | Add item + variant + addon | Detail item tersimpan benar |
| POS-04 | POS | Remove item | Detail item terhapus & total update |
| POS-05 | POS | Checkout cash amount kurang | Ditolak |
| POS-06 | POS | Checkout cash amount cukup | Paid + cash in log + receipt |
| POS-07 | POS | Checkout digital | Snap token/page muncul |
| POS-08 | POS | Void open bill | payment_status void + table release |
| KIT-01 | Kitchen | Pending -> in_progress | Deduksi FIFO berjalan |
| KIT-02 | Kitchen | in_progress -> done | Status done |
| KIT-03 | Kitchen | Mark all done | Semua pending/in_progress jadi done |
| INV-01 | Inventory | Add batch stock | Batch tersimpan + total_stock update |
| INV-02 | Inventory | Input mode pack conversion | Stock tersimpan sesuai konversi |
| INV-03 | Recipe | Simpan recipe produk | recipe + details terbentuk |
| QR-01 | Public Menu | Add to cart | Item muncul di cart session |
| QR-02 | Public Menu | Checkout dine-in saat toko tutup | Ditolak |
| QR-03 | Public Menu | Checkout booking saat toko tutup | Tetap bisa submit booking |
| QR-04 | Public Menu | Checkout cash (QR) | Payment pending, tunggu konfirmasi kasir |
| QR-05 | Public Menu | Checkout digital (QR) | Snap token didapat |
| QR-06 | Public Menu | Order status sync success | Payment paid + stok terdeduksi |
| QR-07 | Public Menu | Midtrans gagal/expire | Payment failed + transaksi di-void |
| BOOK-01 | Booking | Booking baru | Status pending |
| BOOK-02 | Booking | Kasir approve booking | Status approved + meja booked |
| BOOK-03 | Booking | Kasir reject booking unpaid | Status rejected + transaksi open di-void |
| REF-01 | Refund | Refund transaksi paid | Refund log + tx void + table release |
| REF-02 | Refund | Refund cash | Cash drawer log out terbentuk |
| REP-01 | Report | Daily summary | Semua metrik terhitung |
| REP-02 | Report | Best-selling period month | Data agregasi sesuai periode |
| API-CHAT-01 | Chatbot | Health endpoint | Success + info model/ollama status |
| API-CHAT-02 | Chatbot | Message endpoint valid | Success + response text |
| API-CHAT-03 | Chatbot | Menu endpoint | Data kategori + produk aktif |

---

## 5. Uji Integrasi Alur Kritis

## IT-01 POS End-to-End
1. Cashier check-in dan open shift.
2. Buat open bill + tambah item.
3. Kitchen ubah status ke in_progress.
4. Checkout cash.
5. Verifikasi:
   - status transaksi paid,
   - stok bahan berkurang,
   - cash drawer bertambah,
   - receipt tersedia.

## IT-02 QR Digital End-to-End
1. Customer checkout digital.
2. Midtrans callback success.
3. Verifikasi:
   - payment paid,
   - transaction paid,
   - stok terdeduksi,
   - order status ter-update.

## IT-03 Refund End-to-End
1. Ambil transaksi paid.
2. Proses refund.
3. Verifikasi:
   - transaction void,
   - stok kembali,
   - meja available,
   - cash out log (jika payment cash).

---

## 6. Regression Checklist (Setiap Rilis)

- [ ] Login role owner/manager/cashier/kitchen_staff
- [ ] Attendance gate berfungsi
- [ ] Shift open/close
- [ ] Create bill + add item + checkout cash
- [ ] Checkout digital + callback
- [ ] Kitchen status update
- [ ] Public QR checkout
- [ ] Booking approve/reject
- [ ] Refund
- [ ] Daily report
- [ ] Chatbot health/message/menu

---

## 7. Defect Severity

| Severity | Definisi |
|---|---|
| Critical | transaksi/stock/cash mismatch, payment flow rusak total |
| High | fitur utama tidak bisa dipakai role tertentu |
| Medium | fungsi tersedia tapi hasil tidak akurat/UX sangat terganggu |
| Low | isu minor visual/teks tanpa dampak data |

---

## 8. Catatan Peningkatan Testing

Prioritas automasi berikutnya:
1. Feature test untuk auth + attendance gate.
2. Feature test POS checkout cash/digital.
3. Feature test kitchen status + FIFO deduction.
4. Feature test checkout QR + webhook Midtrans.
5. Feature test refund + restock + cash drawer logs.
