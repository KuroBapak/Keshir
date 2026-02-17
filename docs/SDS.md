# 🏗️ Software Design Specification (SDS)

## 1. System Architecture
The system is designed using a modular architecture:

- **Frontend (React):** UI for staff dashboard and customer QR ordering.
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
- Future integration via IoT MQTT device

### 2.3 POS Module
- Order creation
- Cart management
- Checkout process
- Receipt generation

### 2.4 Product/Menu Module
- CRUD menu items
- Tagging for filters (spicy, vegetarian, recommended)

### 2.5 Inventory Module
- Ingredient stock management
- Stock in/out logs
- Low stock alert system

### 2.6 Recipe Module (Core Feature)
- Recipe creation per menu item
- Recipe detail mapping product → ingredients → quantity

### 2.7 Booking Module
- Booking created only after payment confirmation
- Requires cashier approval

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

### products
- id, name, price, category, description, photo_url, tags

### ingredients
- id, name, stock, unit, minimum_stock

### recipes
- id, product_id

### recipe_details
- id, recipe_id, ingredient_id, quantity

### transactions
- id, order_type, customer_name, phone, table_no, people_count, booking_time, total, status

### transaction_details
- id, transaction_id, product_id, qty, price

### payments
- id, transaction_id, method, status, midtrans_reference

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
1. Customer scans QR
2. Customer browses menu and adds items
3. Customer proceeds to checkout
4. Customer selects dine now or booking
5. Customer fills required form
6. Customer completes payment via Midtrans simulation
7. If payment is confirmed → transaction saved

### 4.3 Inventory Deduction Workflow
1. Payment confirmed
2. System saves transaction
3. System loads recipe data
4. System deducts ingredient stock
5. System logs stock-out transaction

### 4.4 Booking Workflow
1. Booking created after payment success
2. Booking status set as pending approval
3. Cashier approves/rejects booking
4. Booking status updated
