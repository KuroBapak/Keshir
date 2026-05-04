# Software Design Specification (SDS)

## 1. Arsitektur Sistem

## 1.1 Gaya Arsitektur
Keshir menggunakan arsitektur **Laravel MVC + Service Layer**:

- **Presentation Layer**: Blade views (`resources/views`)
- **Application Layer**: Controllers (`app/Http/Controllers`)
- **Domain/Business Layer**: Services (`app/Services`)
- **Persistence Layer**: Eloquent Models + Migrations
- **Integration Layer**: Midtrans, Ollama, Attendance Device API

## 1.2 Komponen Inti

| Komponen | Lokasi | Tanggung Jawab |
|---|---|---|
| Routing | `routes/web.php`, `routes/api.php` | Mapping endpoint ke controller |
| Auth + Gate | middleware `attendance`, `role` | Validasi akses user |
| Transaksi inti | `TransactionService` | open bill, add/remove item, checkout, FIFO, restock |
| Cart publik | `CartService` | cart session publik (QR customer) |
| Payment gateway | `MidtransWebhookController`, `PosController`, `CheckoutController` | snap token + webhook sync |
| AI chatbot | `ChatbotController`, `OllamaChatService` | chat, tools, menu data, health |

---

## 2. Desain Modul

## 2.1 Authentication & Authorization
- `Auth\LoginController`:
  - login username/password,
  - attendance check non-owner,
  - role-based redirect,
  - warning shift cashier.
- `CheckRole` middleware:
  - validasi user role dari relasi `user->role->name`.

## 2.2 Attendance Module
- `AttendanceController`:
  - `index()` halaman absensi sementara,
  - `checkIn()` dan `checkOut()` manual,
  - `management()` statistik dan history,
  - `resetCheckout()` dan `destroy()`.
- `CheckAttendance` middleware:
  - block user yang belum check-in / sudah check-out hari ini.

## 2.3 Shift & Cash Drawer Module
- `ShiftController`:
  - CRUD shift, assign default shift user.
- `CashDrawerController`:
  - open shift,
  - close shift (reconciliation),
  - shift detail logs,
  - shift sales list.

## 2.4 Master Data Module
- Category, Product, Variant, Addon, Ingredient, Batch, Recipe, Table, Discount, Setting.
- Product support:
  - foto multiple (JSON),
  - variant/addon sync replace mode,
  - active flag.
- Ingredient stock:
  - base unit + optional pack conversion (`content_per_pack`).

## 2.5 POS Module
- `PosController`:
  - POS dashboard & open bill list,
  - create bill, add/remove item,
  - void bill,
  - checkout cash/digital,
  - digital payment page + confirm,
  - receipt,
  - booking review,
  - table clearance,
  - confirm cash order dari QR.

## 2.6 Public QR Ordering Module
- `PublicMenuController`:
  - menu, cart, checkout form, order history.
- `CheckoutController`:
  - proses checkout publik (dine_in/takeaway/booking),
  - generate Midtrans Snap,
  - handle cash pending,
  - booking payment flow,
  - order status sync.

## 2.7 Kitchen Module
- `KitchenController`:
  - active ticket feed,
  - update item status,
  - mark all done.
- Integrasi FIFO:
  - deduksi stok dipicu saat status item masuk `in_progress` (POS),
  - atau saat pembayaran sukses untuk alur QR.

## 2.8 Refund & Reporting Module
- `RefundController`:
  - create/store refund pada transaksi paid,
  - restock + table release + cash out log.
- `ReportController`:
  - daily summary,
  - best-selling analytics.

## 2.9 AI Chatbot Module
- `ChatbotController`:
  - `/api/v1/chatbot/message`
  - `/api/v1/chatbot/menu`
  - `/api/v1/chatbot/health`
- `OllamaChatService`:
  - call Ollama API,
  - role-aware prompt,
  - function/tool call execution (best seller, weather recommendation, menu detail, discount, table, stock).

## 2.10 Attendance Device API Module
- `AttendanceDeviceController`:
  - register RFID card,
  - tap endpoint untuk check-in/check-out/cooldown.

---

## 3. Desain Data

## 3.1 Entitas & Relasi Kunci

| Entitas | Relasi Utama |
|---|---|
| `roles` | 1..* ke `users` |
| `users` | *..1 ke `roles`, *..1 ke `shifts` (default), 1..* ke attendance/cash_drawers |
| `transactions` | *..1 ke table/cashier/discount/cash_drawer, 1..* ke details, 1..1 ke payment/booking |
| `transaction_details` | *..1 ke transaction/product/variant, 1..* ke detail_addons |
| `products` | *..1 ke categories, 1..* ke variants/addons, 1..1 ke recipe |
| `recipes` | 1..* ke recipe_details |
| `ingredients` | 1..* ke ingredient_batches |
| `cash_drawers` | 1..* ke cash_drawer_logs |
| `bookings` | *..1 ke transactions |
| `refunds` | *..1 ke transactions |

## 3.2 Kolom Penting

- `transactions.order_type`: `dine_in`, `take_away`, `booking`
- `transactions.source`: `pos`, `qr`
- `transactions.payment_status`: `open`, `paid`, `void`
- `transaction_details.status`: `pending`, `in_progress`, `done`, `cancelled`
- `tables.status`: `available`, `occupied`, `booked`
- `cash_drawers.status`: `open`, `closed`
- `attendance_logs.source`: `web`, `iot`
- `bookings.status`: saat ini aktif dipakai controller `pending`, `approved`, `rejected`

---

## 4. Desain Alur Sistem (Sequence Ringkas)

## 4.1 Login Staff
1. User kirim username/password.
2. Auth sukses.
3. Jika bukan owner -> cek attendance hari ini.
4. Jika belum check-in, login ditolak.
5. Middleware attendance menjaga akses selama sesi.

## 4.2 POS Open Bill
1. Cashier membuat bill (`createOpenBill`).
2. Item ditambahkan (`addItemToBill`).
3. Sistem hitung subtotal/discount/tax/service/grand total.
4. Dapur menerima item status pending.

## 4.3 Kitchen Processing
1. Kitchen ubah pending -> in_progress.
2. `TransactionService::deductIngredients()` menjalankan FIFO deduction.
3. Kitchen ubah in_progress -> done.

## 4.4 Checkout Cash POS
1. Kasir pilih cash + amount paid.
2. Validasi amount >= total.
3. Payment paid.
4. Transaction paid.
5. Cash drawer log `in` dibuat.

## 4.5 Checkout Digital (POS/QR)
1. Sistem generate Midtrans order id + snap token.
2. Payment status pending.
3. Webhook/status sync update payment & transaction.
4. Jika success -> paid (+ deduksi stok untuk flow QR).
5. Jika gagal/expired -> gagal, QR open order dapat di-void.

## 4.6 Booking
1. Customer submit booking (status pending).
2. Kasir approve/reject di POS booking view.
3. Table lock mengikuti status booking.
4. Booking approved dapat lanjut pembayaran.

## 4.7 Refund
1. Refund hanya transaksi paid.
2. Sistem simpan refund log.
3. Restock ingredient untuk item in_progress/done.
4. Mark transaction void.
5. Jika payment method cash -> cash drawer log `out`.

---

## 5. Route Design

## 5.1 Web Routes (high-level)
- Public: `/menu`, `/cart`, `/checkout`, `/order/{transaction}`, `/my-orders`
- Auth: `/login`, `/logout`
- Attendance temp: `/absencetemp`
- Dashboard owner/manager: master data, attendance management, shifts, reports
- POS: `/pos/*`
- Kitchen: `/kitchen/*`
- Cash drawer: `/cash-drawer/*`
- Refund: `/refunds/*`

## 5.2 API Routes
- `POST /api/attendance/register-card`
- `POST /api/attendance/tap`
- `POST /api/midtrans/webhook`
- `POST /api/v1/chatbot/message`
- `GET /api/v1/chatbot/menu`
- `GET /api/v1/chatbot/health`

---

## 6. Middleware & Keamanan

## 6.1 Middleware Chain
- `auth` -> memastikan user login.
- `attendance` -> memastikan status attendance valid.
- `role:*` -> role-based access.

## 6.2 Security Controls
- Password hashing via cast `hashed`.
- Server-side request validation di controller.
- CSRF protection default Laravel untuk web forms.
- Payment webhook diproses via Midtrans Notification.

---

## 7. Integrasi Eksternal

## 7.1 Midtrans
- Config: `config/midtrans.php`, env `MIDTRANS_*`
- Generate snap token di POS/Checkout controller
- Status callback via webhook controller

## 7.2 Ollama
- Config: `services.ollama.url`, `services.ollama.model`
- API call: `POST {OLLAMA_URL}/api/chat`
- Timeout dan fallback mode tanpa tools tersedia.

## 7.3 Attendance Device
- API HTTP untuk register/tap.
- Contoh firmware: `docs/esp32_attendance.ino`.

---

## 8. Desain Frontend

- Layout modern biru-putih untuk dashboard/POS/kitchen/public pages.
- Language switcher ID/EN frontend-only via localStorage (`keshir_lang`).
- Kitchen display auto-refresh periodik.
- Komponen UI utama:
  - stat cards,
  - bill cards,
  - ticket cards,
  - custom pagination,
  - responsive public menu/cart/checkout.

---

## 9. Known Design Debt / Konsistensi yang Perlu Dijaga

1. **Enum/Status Booking**:
   - migration legacy pernah mengubah label, controller masih memakai `approved/rejected`.
2. **Enum/Status Payment**:
   - webhook legacy sempat menulis `success`, sementara migration enum mendefinisikan `paid`.
3. **Terminologi Takeaway**:
   - input `takeaway` di publik dipetakan ke `take_away` internal.

Dokumentasi ini menyesuaikan implementasi aktual agar tim dapat mengelola technical debt secara terarah.
