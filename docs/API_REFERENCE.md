# API Reference

Dokumen ini merangkum endpoint API yang tersedia pada implementasi Keshir saat ini.

## 1. Konvensi Umum

- Base URL lokal default: `http://127.0.0.1:8000`
- Format payload: JSON (untuk endpoint API)
- Auth:
  - endpoint di bawah `/api` saat ini umumnya tidak memakai token auth (kecuali `/api/user`)
  - endpoint web menggunakan session auth Laravel

---

## 2. Endpoint API (`routes/api.php`)

## 2.1 Get Auth User

**GET** `/api/user`  
Middleware: `auth:sanctum`

### Response
```json
{
  "id": 1,
  "name": "Owner",
  "username": "owner"
}
```

---

## 2.2 Attendance Device - Register Card

**POST** `/api/attendance/register-card`

### Request Body
```json
{
  "user_id": 3,
  "rfid_uid": "A1B2C3D4"
}
```

### Success Response
```json
{
  "success": true,
  "message": "Kartu berhasil didaftarkan untuk Kasir",
  "data": {
    "id": 3,
    "name": "Kasir",
    "rfid_uid": "A1B2C3D4"
  }
}
```

---

## 2.3 Attendance Device - Tap

**POST** `/api/attendance/tap`

### Request Body
```json
{
  "uid": "A1B2C3D4",
  "device_id": "front_door"
}
```

### Kemungkinan Response `status`
- `check_in`
- `check_out`
- `cooldown`
- `already_done`
- `unknown_card`
- `error` (payload invalid)

---

## 2.4 Midtrans Webhook

**POST** `/api/midtrans/webhook`

Dipanggil oleh Midtrans untuk update status pembayaran digital.

### Behavior Ringkas
- `capture/settlement` -> payment success, transaction paid
- `pending` -> payment pending
- `deny/expire/cancel` -> payment failed
- QR open order gagal -> otomatis void

---

## 2.5 Chatbot - Message

**POST** `/api/v1/chatbot/message`

### Request Body
```json
{
  "message": "Rekomendasi kopi dingin yang enak apa?",
  "conversation_history": [],
  "role": "customer"
}
```

`role` yang didukung: `customer`, `cashier`

### Success Response
```json
{
  "success": true,
  "data": {
    "message": "Untuk cuaca panas, kamu bisa coba Iced Latte ...",
    "function_called": "get_weather_recommendation"
  }
}
```

---

## 2.6 Chatbot - Menu

**GET** `/api/v1/chatbot/menu`

Mengembalikan kategori dan menu aktif untuk konsumsi chatbot/client.

---

## 2.7 Chatbot - Health

**GET** `/api/v1/chatbot/health`

### Response Contoh
```json
{
  "success": true,
  "message": "Keshir Chatbot API is running",
  "version": "2.0.0",
  "ai_engine": "Ollama (Local)",
  "ollama_model": "llama3.1",
  "ollama_online": true
}
```

---

## 3. Endpoint Web Penting (Session-based)

## 3.1 Public Ordering
- `GET /menu`
- `POST /cart/add`
- `POST /cart/update`
- `POST /cart/remove`
- `POST /checkout/process`
- `GET /order/{transaction}`
- `POST /order/{transaction}/pay`

## 3.2 POS
- `GET /pos`
- `POST /pos/bill`
- `POST /pos/bill/{transaction}/item`
- `POST /pos/bill/{transaction}/checkout`
- `POST /pos/bill/{transaction}/confirm-digital`
- `POST /pos/bill/{transaction}/confirm-cash`

## 3.3 Kitchen
- `GET /kitchen`
- `PATCH /kitchen/item/{detail}/status`
- `POST /kitchen/order/{transaction}/done`

## 3.4 Cash Drawer
- `GET /cash-drawer`
- `POST /cash-drawer/open`
- `POST /cash-drawer/{cashDrawer}/close`
- `GET /cash-drawer/shift-sales`

---

## 4. Catatan Integrasi

1. Untuk pembayaran digital, pastikan env Midtrans valid:
   - `MIDTRANS_SERVER_KEY`
   - `MIDTRANS_CLIENT_KEY`
   - `MIDTRANS_IS_PRODUCTION`
2. Untuk chatbot:
   - `OLLAMA_URL`
   - `OLLAMA_MODEL`
3. Untuk perangkat attendance:
   - perangkat mengirim UID ke endpoint `/api/attendance/tap`.

---

## 5. Error Handling Umum

| HTTP Code | Makna |
|---|---|
| 200 | request sukses |
| 400 | payload invalid |
| 403 | unauthorized/forbidden |
| 404 | resource tidak ditemukan |
| 422 | validation error |
| 500/503 | error internal / service eksternal tidak tersedia |
