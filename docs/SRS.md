# 📌 Software Requirements Specification (SRS)

## 1. Purpose
The purpose of this system is to provide an **all-in-one web-based POS platform** for cafes and restaurants.  
It integrates POS transactions, inventory automation, recipe-based stock deduction, customer QR ordering, booking, attendance, payment simulation, reporting, and AI recommendation.

---

## 2. Scope
This system consists of:
- Staff Panel (Owner, Manager, Cashier, Kitchen Staff)
- Customer QR Ordering Module
- POS System
- Inventory Management
- Recipe Management (Core Feature)
- Booking Management
- Attendance Module (IoT-ready)
- Payment Simulation (Midtrans)
- AI Recommendation (Rule-Based)

---

## 3. Intended Users
- Owner / Super Admin
- Manager / Admin / Leader
- Cashier
- Kitchen Staff
- Customer (Public User)

---

## 4. System Environment
| Component | Technology |
|----------|------------|
| Backend | Laravel 12 |
| Frontend | React |
| Database | MySQL |
| Hosting | Home Server (Coolify) |
| Payment | Midtrans Sandbox Simulation |
| AI Recommendation | Rule-Based |
| Future Attendance | IoT MQTT Integration |

---

## 5. Functional Requirements (FR)

### FR-01 Authentication & Authorization
- The system shall provide login using username/password.
- The system shall restrict features based on role-based access control (RBAC).

### FR-02 Attendance-Based Login Restriction (Anti-Cheating)
- The system shall deny login access to staff users who have not checked-in for the day.
- This rule applies to all roles except Owner.

### FR-03 Attendance System (Web vs IoT)
- The system shall allow staff to check-in and check-out.
- For the development phase, the system provides a temporary web route (`/absencetemp`) containing a dropdown of seeded users to perform manual check-in/out.
- The web attendance is designed to be easily replaced by the future IoT MQTT device integration.
- The system shall record timestamps for each attendance action and support configurable work schedules.
### FR-03B Cash Drawer & Shift Management
- The system shall require the Cashier to input the **Starting Cash** (modal awal) when opening a shift.
- The system shall track all cash transactions (Cash IN from sales, Cash OUT from refunds or petty cash).
- The system shall require the Cashier to input the **Ending Cash** (total fisik uang) when closing the shift, and log any discrepancies.
### FR-04 POS Order Creation
- The system shall allow cashier to create orders (dine-in/take-away) and assign a table/seat.
- The system shall prevent selection of a table if its status is currently occupied or booked.
- Orders created by the cashier shall immediately be stored as an **Open Bill** (unpaid).
- The system shall allow the Cashier to add **Notes/Add-ons** (e.g., "Less sugar") to specific items.

### FR-05 Cart, Checkout, and Payment Flow (Cashier)
- The system shall allow adding/removing items and modifying quantity.
- The system shall calculate subtotal, **Tax (e.g., PPN), Service Charge, and Discounts**, yielding the final Grand Total.
- The Master Admin shall have the ability to enable/disable Tax and Service Charge calculations.
- Upon checkout, the cashier must choose the payment method (Cash or Digital).
- **Cash Payment:** The system shall display a modal to input the received cash amount and automatically calculate the change.
- **Digital Payment:** The system shall process the payment (e.g., via QRIS/Transfer).
- Transactions can be marked as Paid, or left as Open Bill to be paid later.
- If an Open Bill is canceled, the Cashier can **Void** it without restrictions, and the system shall automatically restock the ingredients.
- If a **Paid Bill** is canceled (e.g., failed to serve), the system shall record a **Refund Log** (stating the amount, reason, and authorized user) and restock the ingredients.

### FR-06 Transaction Storage
- The system shall store transaction data and transaction details.

### FR-07 Receipt/Invoice Generation
- The system shall generate a digital receipt after successful checkout.
- The system shall support printing/generating billing statements for Open Bills.

### FR-08 Product/Menu Management
- The system shall support CRUD for menu items.
- Each product shall have name, base price, category, photo, tags.
- **Product Variants & Add-ons:** The system shall allow products to have variants (e.g., Size, Hot/Ice) and Add-ons (e.g., Extra shot, Extra cheese) that **modify the final price**.

### FR-08B Discount & Promo Management
- The Manager/Admin shall be able to create Discounts (nominal or percentage).
- Discounts can be applied to specific items or the entire bill.

### FR-09 Inventory Management (Ingredients)
- The system shall support CRUD for ingredients.
- The system shall support stock in/out logs.
- The system shall require an **Expiry Date** input during stock-in.

### FR-10 Expiry & Low Stock Alerts
- The system shall alert admin/manager if stock is below the minimum threshold.
- The system shall alert admin/manager of ingredients approaching their Expiry Date.

### FR-11 Recipe Management
- The system shall allow creating recipes linked to menu items.
- Recipes shall define ingredient quantity per product.

### FR-12 Automatic Recipe-Based Stock Deduction (FIFO & Voiding)
- The system shall automatically deduct ingredient stock upon order creation or payment (configurable depending on workflow, default scenario: at order creation).
- The system shall apply a **First-In, First-Out (FIFO)** method, prioritizing the deduction of stock batches with the closest Expiry Date.
- If an order is **Voided or Canceled** (such as unpaid Midtrans timeouts), the deducted ingredients must be returned to inventory.

### FR-13 Customer QR Ordering Module
- Customers shall access menu via QR without login.
- Customers shall browse menu, filter items, and add to cart.

### FR-14 Unified Checkout (Dine Now / Booking) from QR
- Customer checkout shall provide options: Dine Now & Booking.
- Both options shall request: customer name, phone number, table/seat, and number of people.
- The Table selection dropdown shall only display available tables (hiding occupied/booked tables based on current POS/Booking data).
- The system shall allow Customers to select **Variants/Add-ons** (affecting price) and add **Notes** to their order.
- The system shall calculate and display the Subtotal, Tax, Service Charge, and Grand Total to the customer.
- Booking shall additionally require booking time.

### FR-15 Booking and Kitchen Dashboard Flow
- **Kitchen Dashboard:** The Kitchen Staff shall have access to a dedicated Kitchen View, displaying active incoming orders, variants, and notes. Kitchen Staff can update the status of individual items (e.g., "In Progress", "Done").
- **Dine Now (Direct Order):** Validated orders shall immediately route to the Kitchen Dashboard as active tickets.
- **Booking:** Booking requests shall route to the Cashier's Booking View for approval. Approved bookings will enter the kitchen queue at the appropriate time based on the scheduled booking.

### FR-16 Payment Simulation (Midtrans)
- The system shall integrate Midtrans sandbox for payment simulation.
- Payment statuses supported:
  - pending
  - paid
  - failed

### FR-17 Payment Validation Rule (Customer QR)
- Customer QR orders shall only be saved upon payment confirmation.
- If payment is canceled, failed, or timed out in Midtrans, the QR order shall not be stored, and the system must cleanup related data.
- *Note: This restriction does not apply to Cashier POS orders (which use the Open Bill system).*

### FR-18 End-of-Day (Closing) & Shift Procedures
- The system shall enforce a daily closing / shift closing verification by the cashier/manager.
- The system shall check for any remaining **Open Bills** and require them to be closed (paid or voided).
- The system shall require confirmation/reconciliation of the daily physical vs. system stock.
- The system shall reconcile the **Cash Drawer** (System Cash vs Physical Cash).

### FR-18 Reporting & Analytics
- The system shall provide sales reports daily/weekly/monthly.
- The system shall provide best-selling product analytics.

### FR-19 AI Recommendation (Rule-Based)
- The system shall recommend products based on weather and sales trends.

### FR-20 User Management
- Owner shall manage staff accounts (CRUD).
- Owner shall assign roles to staff.

---

## 6. Non-Functional Requirements (NFR)

### NFR-01 Security
- Passwords must be hashed.
- Role-based access control must be enforced.

### NFR-02 Performance
- System should handle multiple customers browsing menu simultaneously.

### NFR-03 Reliability
- Inventory deduction must only occur after payment confirmation.

### NFR-04 Scalability
- System must be designed for future IoT attendance integration.

### NFR-05 Usability
- Customer QR menu must be mobile-friendly.
- POS dashboard must support fast cashier operations.

### NFR-06 Maintainability
- Backend must implement modular service-based architecture.
