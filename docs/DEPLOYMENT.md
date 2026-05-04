# Deployment Guide

Panduan setup dan deployment Keshir untuk environment lokal maupun server.

## 1. Prasyarat

## 1.1 Software
- PHP 8.2+
- Composer 2+
- Node.js 18+
- NPM 9+
- MySQL (atau SQLite untuk dev)

## 1.2 Service Eksternal (opsional tetapi direkomendasikan)
- Midtrans Sandbox/Production
- Ollama local/server untuk chatbot

---

## 2. Setup Project

## 2.1 Clone & Install

```bash
git clone <repo-url>
cd keshir
composer install
npm install
```

## 2.2 Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` minimal:

```env
APP_NAME=Keshir
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keshir
DB_USERNAME=root
DB_PASSWORD=secret
```

## 2.3 Midtrans

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
MIDTRANS_MERCHANT_ID=Gxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

## 2.4 Ollama (Chatbot)

```env
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_MODEL=llama3.1
```

> Pastikan service Ollama aktif sebelum menggunakan endpoint chatbot.

## 2.5 MQTT Attendance (jika dipakai)

```env
MQTT_HOST=your-broker-host
MQTT_PORT=1883
MQTT_WS_PORT=443
MQTT_AUTH_USERNAME=your-user
MQTT_AUTH_PASSWORD=your-password
```

---

## 3. Database Initialization

```bash
php artisan migrate
php artisan db:seed
```

Opsional seed data coffee shop:

```bash
php artisan db:seed --class=CoffeeShopSeeder
```

---

## 4. Build & Run

## 4.1 Development

```bash
php artisan serve
npm run dev
```

Atau gunakan script composer:

```bash
composer run dev
```

## 4.2 Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Storage & File Upload

Produk menggunakan upload foto ke disk `public`.

```bash
php artisan storage:link
```

Pastikan folder berikut writable:
- `storage/`
- `bootstrap/cache/`

---

## 6. Midtrans Webhook Setup

Set webhook URL di dashboard Midtrans:

```text
https://your-domain.com/api/midtrans/webhook
```

Pastikan endpoint bisa diakses dari internet pada production.

---

## 7. Post-Deployment Checklist

- [ ] Login owner berhasil
- [ ] Staff check-in/check-out berhasil
- [ ] Cash drawer open/close berhasil
- [ ] POS create bill + checkout cash berhasil
- [ ] Digital payment callback Midtrans diterima
- [ ] Kitchen status update berjalan
- [ ] Public QR order flow berjalan
- [ ] Reports tampil benar
- [ ] Chatbot health endpoint OK

---

## 8. Troubleshooting

## 8.1 APP_KEY missing
```bash
php artisan key:generate
```

## 8.2 Migration error
- Cek kredensial DB di `.env`
- Cek apakah database sudah dibuat

## 8.3 Midtrans gagal generate Snap token
- Cek `MIDTRANS_SERVER_KEY`
- Cek mode production/sandbox
- Cek koneksi internet server

## 8.4 Chatbot timeout/fail
- Cek service Ollama aktif
- Cek `OLLAMA_URL`
- Cek model tersedia (`ollama list`)

## 8.5 Asset tidak tampil
```bash
npm run build
php artisan optimize:clear
```

---

## 9. Security Minimum untuk Production

1. Set `APP_DEBUG=false`.
2. Gunakan HTTPS.
3. Ganti semua password default seeder.
4. Jangan commit file `.env`.
5. Rotasi key/token integrasi (Midtrans, MQTT) secara berkala.
