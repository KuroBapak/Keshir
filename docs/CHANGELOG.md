# 🧾 Patch Update / Update Log

This log records system updates, including what changed, who updated it, and when.

| Version | Date | Updated By | Description |
|--------|------|------------|-------------|
| v0.5 | 2026-03-09 | AI Agent | Bill number now resets per shift (`bill_number` + `cash_drawer_id` on transactions). Shift enforcement: cashier must open shift before creating bills (owner/manager bypass). Login warns cashier if no active shift; logout warns if shift still open. Role-based sidebar (owner/manager=full, cashier=POS+Kas Laci+Refund). Cashier can now refund per SRS FR-05. Fixed POS "← Dashboard" link for cashier (→ Kas Laci). Fixed selisih color (green for +, red for −). Fixed null auth crash on public pages. |
| v0.4 | 2026-03-09 | AI Agent | Phase 5: Implemented Cash Drawer/Shift management (open/close, reconciliation, auto cash-in on sale, cash-out on refund). Refund Log system (paid bill refund with restock). Daily Sales Report (revenue breakdown, cash/digital split). Best-Selling Products analytics (today/week/month). Added sidebar links for all new features. |
| v0.3 | 2026-03-09 | AI Agent | Fixed FIFO timing (POS=deduct at cooking, QR=deduct at payment). Added ingredient pack conversion (`content_per_pack`). Hidden depleted batches. Added item cooking status badges in POS bill view. Fixed ENUM mismatches (`unpaid`→`open`, `cooking`→`in_progress`). Fixed null auth on public pages. |
| v0.2 | 2026-03-09 | AI Agent | Phase 3: Implemented POS Cashier (Open Bill, catalog, cart, variant/addon selection, checkout, receipt) and Kitchen Dashboard (ticket cards, per-item status updates, auto-refresh, dark theme). Created `TransactionService` with FIFO ingredient deduction and restock logic. |
| v0.1 | 2026-03-09 | AI Agent | Finalized SRS & SDS. Realigned Frontend stack to Blade/HTML/Tailwind as per project_context. Added Open Bill POS, FIFO Inventory, Kitchen Dashboard, and Cash Drawer mechanics |
