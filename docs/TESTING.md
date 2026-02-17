# 🧪 Software Testing Specification

## 1. Testing Scope
This document covers testing for:
- authentication and attendance restriction
- POS workflow
- customer QR ordering workflow
- payment simulation
- recipe-based stock deduction
- booking approval
- reporting functions

---

## 2. Test Cases

| Test ID | Feature | Steps | Expected Result |
|--------|---------|-------|----------------|
| TC-01 | Staff login without check-in | Login as cashier without check-in | Login denied, redirect to attendance |
| TC-02 | Owner login | Login as owner without check-in | Login success |
| TC-03 | Attendance check-in | Press check-in button | Timestamp stored |
| TC-04 | Attendance double check-in | Check-in twice same day | System prevents duplicate |
| TC-05 | Add menu to cart | Customer adds item to cart | Item appears in cart |
| TC-06 | Checkout dine now | Checkout → dine now → pay success | Transaction saved |
| TC-07 | Checkout booking | Checkout → booking → set time → pay success | Booking saved as pending approval |
| TC-08 | Payment canceled | Cancel payment | No transaction saved |
| TC-09 | Recipe stock deduction | Buy product with recipe → pay success | Ingredient stock reduced |
| TC-10 | Payment failed | Fail payment | No stock changes |
| TC-11 | Low stock alert | Deduct stock below threshold | Alert displayed |
| TC-12 | Booking approval | Cashier approves booking | Booking status updated |
