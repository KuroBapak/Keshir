# ☕ Keshir POS Smart Chatbot Module

Selamat datang di **Keshir POS Chatbot**, sebuah sub-modul kecerdasan buatan (*standalone*) yang dirancang khusus untuk mendampingi aplikasi utama Keshir Point of Sales. Chatbot ini ditenagai oleh **Ollama (Local AI)** dengan model **Llama 3.1** yang berjalan sepenuhnya di mesin lokal Anda — **tanpa limit API, tanpa biaya, tanpa koneksi internet!**

Modul ini memiliki **Role-Based Access**, yang memungkinkannya bertindak sebagai "Pelayan Virtual" untuk customer, atau berubah cerdas menjadi "Asisten Dapur/Inventaris" bagi staf kasir Anda!

---

## 🚀 Fitur Utama

1. **100% Lokal & Tanpa Limit**
   Menggunakan Ollama sebagai engine AI lokal. Tidak ada rate-limit, tidak ada API key, tidak ada biaya langganan.
2. **Function Calling (Tool Use)**
   AI mampu memanggil fungsi database secara otomatis: `get_best_sellers`, `get_weather_recommendation`, `get_menu_details`, dan `check_stock_status`.
3. **Role-Based AI Persona**
   - `role="customer"`: Bertindak sebagai pelayan ramah; merekomendasikan menu, memberi tahu harga/deskripsi produk. Otoritas akses *stok/gudang* sepenuhnya diblokir (*secure*).
   - `role="cashier"`: Bertindak sebagai koordinator kasir/dapur; mampu langsung mengakses dan melaporkan sisa stok bahan dari *database*.
4. **Gambar Menu via Database & Markdown**
   Gambar menu disisipkan otomatis dari kolom `image_url` database lewat format Markdown `![Nama](url)`.

---

## 🛠️ Tech Stack

**Backend (API & AI Logic):**
- **Laravel 12** (Framework v13.4.0) via PHP 8.3
- **SQLite** (Database dummy ringan)
- **Ollama** (Local LLM Server) + **Llama 3.1** (Default Model)
- **Service Layer Pattern**: `OllamaChatService.php`

**Frontend (Widget UI):**
- **React.js** (Vite Scaffold v5)
- **Lucide React** (Icon library)
- **React-Markdown** (Render teks & gambar dari AI)
- **Vanilla CSS Scoped** (Micro-animations & Glassmorphism)

---

## 📊 Application Flow

```
┌─────────────┐     POST /api/v1/chatbot/message      ┌──────────────────┐
│  React UI   │ ──────────────────────────────────────► │  Laravel API     │
│  (Widget)   │     { message, role, history }          │  Controller      │
└─────────────┘                                        └────────┬─────────┘
       ▲                                                        │
       │                                                        ▼
       │                                               ┌──────────────────┐
       │                                               │ OllamaChatService│
       │                                               │  - System Prompt │
       │                                               │  - Tool Filtering│
       │                                               └────────┬─────────┘
       │                                                        │
       │                                                        ▼
       │                                               ┌──────────────────┐
       │         Final text response                    │  Ollama Server   │
       │◄─────────────────────────────────────────────  │  (localhost:11434│
       │                                               │   llama3.1)      │
       │                                               └────────┬─────────┘
       │                                                        │
       │                          If tool_call detected:        │
       │                                                        ▼
       │                                               ┌──────────────────┐
       │                                               │  SQLite Database │
       │                                               │  (products, etc) │
       │                                               └──────────────────┘
```

**Langkah detail:**
1. User mengetik pesan di widget React → dikirim via `POST` bersama `role` dan `conversation_history`.
2. `OllamaChatService` meng-assemble pesan + system prompt + tools berdasarkan role.
3. Request dikirim ke Ollama lokal (`POST http://127.0.0.1:11434/api/chat`).
4. Jika Ollama mengenali intent yang memerlukan data → AI me-return `tool_calls`.
5. Laravel mengeksekusi query SQLite → hasilnya dikirim balik ke Ollama sebagai pesan `tool`.
6. Ollama mengemas data mentah menjadi respons bahasa Indonesia yang ramah + Markdown gambar.
7. Frontend merender hasilnya di bubble chat.

---

## 📋 Prerequisites

Sebelum menjalankan modul ini, pastikan Anda sudah menginstal:

| Software | Versi Minimum | Cara Cek |
|----------|--------------|----------|
| PHP | 8.3+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 20.x+ | `node -v` |
| NPM | 10.x+ | `npm -v` |
| Ollama | 0.20.7+ | `ollama --version` |

### Instalasi Ollama & Model
```bash
# 1. Install Ollama (download dari https://ollama.com)

# 2. Pull model llama3.1
ollama pull llama3.1

# 3. Pastikan Ollama berjalan (biasanya otomatis setelah install)
ollama serve
```

---

## 💻 Cara Menjalankan

### Terminal 1: Backend (Laravel)
```bash
cd Chatbot/backend

# Install dependencies (pertama kali saja)
composer install

# Setup database
php artisan migrate:fresh --seed

# Jalankan server
php artisan serve --port=8000
```

### Terminal 2: Frontend (React)
```bash
cd Chatbot/frontend

# Install dependencies (pertama kali saja)
npm install

# Jalankan dev server
npm run dev -- --port=5173
```

### Terminal 3: Ollama (jika belum berjalan)
```bash
ollama serve
```

Buka browser ke **http://localhost:5173** dan klik widget chat di pojok kanan bawah! 🎉

---

## ⚙️ Konfigurasi Environment

File `.env` di folder `backend/`:

| Variable | Default | Keterangan |
|----------|---------|------------|
| `OLLAMA_URL` | `http://127.0.0.1:11434` | Alamat server Ollama lokal |
| `OLLAMA_MODEL` | `llama3.1` | Model AI yang digunakan |
| `DB_CONNECTION` | `sqlite` | Tipe database |

### Mengganti Model AI
Anda bisa mengganti model Ollama kapan saja:
```bash
# Pull model lain
ollama pull mistral
ollama pull gemma2

# Ubah di .env
OLLAMA_MODEL=mistral
```

---

## 🔀 Mengganti Role (Customer ↔ Cashier)

Edit file `Chatbot/frontend/src/App.jsx`:

```jsx
// Mode Pelanggan (tanpa akses stok)
<ChatbotWidget role="customer" />

// Mode Kasir/Staff (akses penuh termasuk stok)
<ChatbotWidget role="cashier" />
```

---

## 🔗 Migrasi ke Main App Keshir

1. Copy `backend/app/Services/Chatbot/OllamaChatService.php` ke proyek utama.
2. Copy routes dari `routes/api.php` ke API routes utama.
3. Tempel `<ChatbotWidget role="..." />` di layout frontend Dashboard Keshir.
4. Pastikan Ollama terinstall di server production.
