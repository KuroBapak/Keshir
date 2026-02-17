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

### FR-03 Attendance System
- The system shall allow staff to check-in and check-out.
- The system shall record timestamp for each attendance action.
- The system shall support configurable work schedule (start/end shift).

### FR-04 POS Order Creation
- The system shall allow cashier to create orders (dine-in/take-away).
- The system shall allow assigning a table/seat for dine-in.

### FR-05 Cart and Checkout
- The system shall allow adding/removing items in cart.
- The system shall allow modifying quantity.
- The system shall calculate subtotal and total automatically.

### FR-06 Transaction Storage
- The system shall store transaction data and transaction details.

### FR-07 Receipt/Invoice Generation
- The system shall generate digital receipt after successful checkout.

### FR-08 Product/Menu Management
- The system shall support CRUD for menu items.
- Each product shall have name, price, category, photo, tags.

### FR-09 Inventory Management (Ingredients)
- The system shall support CRUD for ingredients.
- The system shall support stock in/out logs.

### FR-10 Low Stock Alert
- The system shall alert admin/manager if stock is below minimum threshold.

### FR-11 Recipe Management
- The system shall allow creating recipes linked to menu items.
- Recipes shall define ingredient quantity per product.

### FR-12 Automatic Recipe-Based Stock Deduction
- The system shall automatically deduct ingredient stock after payment is confirmed.

### FR-13 Customer QR Ordering Module
- Customers shall access menu via QR without login.
- Customers shall browse menu, filter items, and add to cart.

### FR-14 Unified Checkout (Dine Now / Booking)
- Customer checkout shall provide options:
  - Dine Now
  - Booking
- Both options shall request:
  - customer name
  - phone number
  - table/seat
  - number of people
- Booking shall additionally require booking time.

### FR-15 Booking Approval
- Booking requests shall require cashier approval before confirmation.

### FR-16 Payment Simulation (Midtrans)
- The system shall integrate Midtrans sandbox for payment simulation.
- Payment statuses supported:
  - pending
  - paid
  - failed

### FR-17 Payment Validation Rule
- Transactions shall only be saved if payment is confirmed.
- If payment is canceled/failed, the order shall not be stored.

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
