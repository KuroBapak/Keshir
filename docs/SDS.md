# 🏗️ Software Design Specification (SDS)

## 1. System Architecture
The system is designed using a modular architecture:

- **Frontend:** Server-rendered views (Blade, simple HTML/Tailwind) for faster development.
- **Backend (Laravel 12):** REST API and business logic processing.
- **Database (MySQL):** persistent storage for transactions, stock, recipes, and attendance.
- **Payment Integration:** Midtrans Sandbox Simulation.
- **Future IoT Attendance:** MQTT-based integration.

---

## 2. Module Design

### 2.1 Authentication Module
- Handles login validation
- Role-based authorization

### 2.2 Attendance Module
- Check-in/check-out functions
- Shift schedule validation
- **Dev Phase:** Temporary web route (`/absencetemp`) with user dropdown
- **Future Phase:** Integration via IoT MQTT device

### 2.3 POS Module
- Order creation & Table Assignment
- Cart management
- Checkout process (Cash Modal / Digital)
- Supports **Open Bill** (Save unpaid transactions)
- Receipt & Billing statement generation
- **Cash Drawer & Shift Management**

### 2.4 Product/Menu Module
- CRUD menu items
- Tagging for filters (spicy, vegetarian, recommended)

### 2.5 Inventory Module
- Ingredient stock management (Batch-based)
- Stock in/out logs with **Expiry Date** tracking
- Low stock & Expiry alert system

### 2.6 Recipe Module (Core Feature)
- Recipe creation per menu item (and per Variant if applicable)
- Recipe detail mapping product → ingredients → quantity

### 2.6B Kitchen Dashboard Module (NEW)
- Dedicated UI for Kitchen Staff
- Displays active tickets (Dine Now & Approved Bookings)
- Shows item Variants, Add-ons, and Notes
- Ability to update item status (e.g., Pending, In Progress, Done)
- Sends status updates back to the POS/Cashier view

### 2.7 Booking & Kitchen Flow Module
- **Dine Now:** Orders go directly to Kitchen Display.
- **Booking:** Booking created after payment confirmation (QR) or saved as Open Bill (POS).
- Bookings require cashier approval before entering the kitchen queue.
- Validates Table availability (prevents double booking).

### 2.8 Payment Module
- Midtrans sandbox payment simulation
- Payment status update handler

### 2.9 AI Recommendation Module (Rule-Based)
- Rule-based engine using:
  - weather conditions
  - best-selling menu data
- Output recommendations shown on customer menu page and admin dashboard

---

## 3. Database Design (High-Level)

### users
- id, name, username, password_hash, role_id

### roles
- id, role_name

### tables (NEW)
- id, table_number, capacity, status (available/occupied/booked)

### products
- id, name, base_price, category, description, photo_url, tags

### product_variants (NEW)
- id, product_id, variant_name (e.g., "Large", "Hot"), additional_price

### product_addons (NEW)
- id, product_id, addon_name (e.g., "Extra Shot"), price

### discounts (NEW)
- id, name, type (percentage/nominal), value, is_active

### settings (NEW - for Tax & Service Charge)
- id, setting_key (e.g., 'tax_rate', 'service_charge_rate', 'tax_enabled'), setting_value

### ingredients
- id, name, total_stock, unit, minimum_stock

### ingredient_batches (NEW for FIFO & Expiry)
- id, ingredient_id, stock, expiry_date, purchase_price, created_at

### recipes
- id, product_id

### recipe_details
- id, recipe_id, ingredient_id, quantity

### transactions
- id, order_type, customer_name, phone, table_no, people_count, booking_time, subtotal, discount_total, tax_total, service_total, grand_total, payment_status (open/paid/void), payment_method

### transaction_details
- id, transaction_id, product_id, product_variant_id (nullable), qty, price, notes (Add-ons/Special requests), status (pending/in_progress/done)

### transaction_detail_addons (NEW)
- id, transaction_detail_id, product_addon_id, price

### payments
- id, transaction_id, method, status, midtrans_reference

### refunds (NEW)
- id, transaction_id, amount, reason, authorized_by, created_at

### cash_drawers (NEW)
- id, user_id, opened_at, closed_at, starting_cash, ending_cash, expected_ending_cash, status (open/closed)

### cash_drawer_logs (NEW)
- id, cash_drawer_id, type (in/out), amount, description, transaction_id (nullable)

### bookings
- id, transaction_id, booking_time, status, approved_by_cashier

### attendance_logs
- id, user_id, date, check_in, check_out, source

---

## 4. System Workflow Design

### 4.1 Staff Login Workflow
1. Staff submits login credentials
2. System checks role
3. If role != Owner:
   - verify attendance check-in for current day
   - deny login if not checked-in

### 4.2 Customer QR Ordering Workflow
1. Customer scans QR & browses menu
2. Customer selects items, chooses Variants/Add-ons, adds Notes
3. Customer adds items to cart & proceeds to checkout
4. Customer selects Dine Now or Booking and fills the form
5. System displays Subtotal + Tax + Service Charge = Grand Total
6. Customer completes payment via Midtrans
7. If payment is confirmed → transaction saved, order routed to Kitchen Dashboard

### 4.3 Cashier POS Workflow (Open Bill)
1. Cashier creates order, selects Variants/Add-ons, inputs customer name & table number
2. Cashier saves order as **Open Bill**
3. Order immediately routes to Kitchen Display
4. Staff updates ticket status in Kitchen Dashboard
5. Later, Customer pays → System calculates Tax/Service/Discount → Cashier processes payment (Cash modal / Digital)
6. Transaction status updated from Open to Paid

### 4.4 Inventory Deduction Workflow (FIFO)
1. Order confirmed (or paid, depending on config)
2. System loads recipe data
3. System finds oldest `ingredient_batches` (closest expiry date)
4. System deducts stock from batch(es) until required quantity is met
5. System logs stock-out transaction

### 4.5 Booking & Kitchen Workflow
1. Direct Order/Dine Now gets pushed to Kitchen Display instantly
2. Booking order gets pushed to Cashier Booking View (Pending)
3. Cashier approves booking
4. When booking time nears, order gets pushed to Kitchen Display

### 4.6 Cash Drawer & Daily Closing Workflow
1. **Shift Start:** Cashier logs in and opens Cash Drawer by inputting Starting Cash.
2. **During Shift:** Cash payments automatically log as "Cash IN".
3. **Refund/Void:** If a paid cash transaction is refunded, it logs as "Cash OUT".
4. **Shift End:** Cashier initiates End of Day closing.
5. System checks for unresolved **Open Bills** (must be paid or voided).
6. Cashier inputs physical Ending Cash; system compares with expected cash and logs discrepancy.
7. Staff reconciles system ingredient stock vs physical stock.
8. System generates daily summary report (Sales, Taxes, Discounts, Refunds, Cash Drawer status).
