# Changelog

Semua perubahan penting pada proyek Keshir didokumentasikan di sini.

## v0.6 - 2026-05-04

### Documentation Overhaul (SRS/SDS/dll)
- Revisi total **README** agar sinkron dengan implementasi aktual (Laravel + Blade).
- Revisi total **SRS** dengan kebutuhan fungsional/non-fungsional berbasis kode saat ini.
- Revisi total **SDS** mencakup arsitektur, modul, alur sistem, desain data, integrasi.
- Revisi total **TESTING** dengan test matrix lengkap lintas modul.
- Revisi total **USER_MANUAL** untuk owner/manager/cashier/kitchen/customer.
- Tambah **API_REFERENCE.md**.
- Tambah **DEPLOYMENT.md**.
- Tambah catatan technical debt/inconsistency yang masih ada agar tim mudah follow-up.

## v0.5 - 2026-03-09
- Bill number reset per shift (`bill_number` + `cash_drawer_id`).
- Shift enforcement: cashier harus open shift sebelum transaksi.
- Warning shift saat login/logout cashier.
- Sidebar role-based.
- Cashier dapat akses refund.
- Perbaikan link dashboard cashier, warna selisih, null auth crash public pages.

## v0.4 - 2026-03-09
- Implementasi cash drawer/shift management (open/close/reconciliation).
- Refund log + restock.
- Daily sales report + best-selling report.
- Penambahan menu sidebar untuk fitur baru.

## v0.3 - 2026-03-09
- Perbaikan timing FIFO (POS saat cooking, QR saat payment).
- Penambahan konversi pack ingredient (`content_per_pack`).
- Hidden depleted batches.
- Badge status cooking pada POS bill.
- Perbaikan mismatch enum (`unpaid` -> `open`, `cooking` -> `in_progress`).
- Perbaikan null auth public pages.

## v0.2 - 2026-03-09
- Implementasi POS cashier (open bill, catalog, cart, variant/addon, checkout, receipt).
- Implementasi kitchen dashboard (tickets, status update, auto-refresh).
- Pembuatan `TransactionService` untuk FIFO deduction & restock.

## v0.1 - 2026-03-09
- Finalisasi awal dokumen SRS/SDS.
- Penyesuaian frontend stack ke Blade/HTML/Tailwind.
- Pondasi fitur Open Bill POS, FIFO Inventory, Kitchen Dashboard, Cash Drawer.
