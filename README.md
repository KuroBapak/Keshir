# ☕ All-in-One Smart POS System for Cafes & Restaurants  
**Inventory Automation + Recipe-Based Deduction + AI Recommendation**

## 📌 Project Overview
This project is a **web-based integrated POS system** designed for cafes and restaurants.  
It combines multiple operational modules into one platform:

- POS / Cashier System  
- Inventory Management (Ingredients)  
- Recipe-Based Stock Deduction (Automation)  
- Unified QR Customer Ordering System (Menu + Booking + Payment)  
- Booking Approval Workflow  
- Attendance System (IoT-ready)  
- Payment Integration (Midtrans Simulation)  
- AI Recommendation System (Rule-Based)  
- Reporting & Analytics Dashboard  

The system is developed using:
- **Backend:** Laravel 12  
- **Frontend:** React  
- **Database:** MySQL  
- **Deployment:** Home Server via Coolify  
- **Payment Simulation:** Midtrans Sandbox  

---

## 👥 User Roles
- **Owner / Super Admin**
- **Manager / Admin / Leader**
- **Cashier**
- **Kitchen Staff**
- **Customer (Public User)**

📌 Note: All staff roles (except Owner) must check-in before login is allowed.

---

## 📂 Documentation Index (Requirements)
Click the documents below:

- 📌 [Software Requirements Specification (SRS)](docs/SRS.md)  
- 🏗️ [Software Design Specification (SDS)](docs/SDS.md)  
- 🧪 [Software Testing Specification](docs/TESTING.md)  
- 📖 [User Manual](docs/USER_MANUAL.md)  
- 🧾 [Patch Update / Update Log](docs/CHANGELOG.md)  

---

## 🔥 Key Feature Highlight
### Recipe-Based Inventory Automation
When a transaction is paid, the system automatically deducts ingredient stock based on the menu recipe.

### Unified Customer QR Ordering
Customer scans QR → chooses menu → cart checkout → selects **Dine Now** or **Booking** → payment → order saved only if paid.

### Attendance-Based Login Restriction
Staff cannot login unless they are officially checked-in (tap-in system).

---

## 🚀 Future Enhancements
- IoT Attendance using MQTT + RFID
- AI upgrade from rule-based → ML-based recommendation
- Landing page and cafe exploration (3D concept)

---

## 👨‍💻 Author
Developed by: **(Keshir Team XD)**

