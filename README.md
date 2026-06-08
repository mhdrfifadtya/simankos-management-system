# SiManKos — Sistem Informasi Manajemen Kos

> **"Kelola Kos, Lebih Mudah."**
> Tema Visual: **SKYLINE** — Cakrawala gedung kota di malam hari.

---

## Deskripsi

SiManKos adalah aplikasi manajemen kos berbasis **Web Admin (Laravel 12)** dan **Mobile App (React Native Expo)** dengan fitur utama algoritma penjadwalan notifikasi pembayaran otomatis via **WhatsApp Gateway API (Fonnte)**.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.2, MySQL 8.0 |
| Auth | Laravel Sanctum (JWT Mobile), Spatie Permission (RBAC) |
| Scheduler | Laravel Task Scheduling (Cron Job) |
| WhatsApp | Fonnte API |
| PDF Export | Barryvdh DomPDF |
| Web Frontend | Blade + Livewire 3, Alpine.js, Chart.js |
| Mobile | React Native 0.81, Expo SDK 51, TypeScript |
| State Mobile | Zustand, Axios, Expo Notifications |

---

## Aktor Sistem

| Role | Deskripsi |
|---|---|
| `admin` | Pemilik/pengelola kos — akses penuh ke semua fitur |
| `penghuni` | Penyewa kamar — akses terbatas via mobile app |

---

## Tema Visual SKYLINE

| Elemen | Nilai |
|---|---|
| Deep Navy | `#0F172A` |
| Slate Blue | `#1E293B` |
| Sky Accent | `#38BDF8` |
| Amber | `#F59E0B` |
| White Smoke | `#F1F5F9` |
| Tipografi | Inter / Plus Jakarta Sans |

---

## Fitur Utama

### Web Admin
- Dashboard statistik (kamar, penyewa, tagihan, pendapatan)
- Manajemen kamar (foto, fasilitas, status)
- Manajemen penyewa (data lengkap, foto KTP)
- Manajemen kontrak & tagihan (invoice otomatis)
- Manajemen pembayaran (verifikasi bukti transfer)
- Notifikasi & scheduler (WhatsApp H-7, H-3, H-1, overdue)
- Laporan & export PDF
- Pengaturan sistem & Fonnte API

### Mobile App (Penyewa)
- Lihat tagihan & riwayat pembayaran
- Upload bukti pembayaran
- Notifikasi push lokal
- Request maintenance
- Kotak saran

---

## Algoritma Cron Job — Notifikasi Otomatis

```
Jadwal: Setiap hari 08:00 WIB (Asia/Jakarta)
Command: php artisan notify:payment-reminder

Logika:
1. Ambil semua invoice status != 'paid'
2. Hitung diff = Carbon::today()->diffInDays(due_date, false)
3. Kondisi:
   - diff == 7  → H-7 reminder
   - diff == 3  → H-3 reminder
   - diff == 1  → H-1 reminder
   - diff < 0   → Overdue reminder
4. Cek reminder_logs (hindari duplikasi hari yang sama)
5. Kirim via Fonnte API
6. Simpan ke reminder_logs + notification_logs
7. Log ke scheduler_job_logs
```

---

## API Endpoints Mobile (prefix: /api/v1)

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | /auth/login | Login penyewa |
| POST | /auth/logout | Logout |
| GET | /auth/profile | Profil user |
| GET | /invoices | List tagihan |
| GET | /invoices/{id} | Detail tagihan |
| GET | /invoices/summary | Ringkasan tagihan |
| POST | /payments | Upload bukti bayar |
| GET | /payments | List pembayaran |
| GET | /payments/{id} | Detail pembayaran |
| GET | /room | Info kamar aktif |
| GET | /notifications | List notifikasi |
| PATCH | /notifications/{id}/read | Tandai dibaca |
| POST | /maintenance | Buat request maintenance |
| GET | /maintenance | List maintenance |
| POST | /suggestions | Kirim saran |

---

## Instalasi

### Prasyarat
- PHP 8.2+
- Composer 2.x
- MySQL 8.0
- Node.js 18+
- XAMPP / Laragon

### Backend Setup

```bash
cd c:/xampp/htdocs/Simankos/simankos

composer install

cp .env.example .env

php artisan key:generate

# Edit .env (database, Fonnte)

php artisan migrate

php artisan db:seed

php artisan storage:link

php artisan serve
```

### Environment Variables

```env
APP_NAME=SiManKos
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simankos
DB_USERNAME=root
DB_PASSWORD=

FONNTE_TOKEN=your_fonnte_token_here

QUEUE_CONNECTION=database
```

### Cron Job (Production)

```cron
* * * * * cd /path/to/simankos && php artisan schedule:run >> /dev/null 2>&1
```

### Mobile Setup

```bash
cd mobile
npm install
npx expo start
```

---

## Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@simanakos.com | Admin@123 |
| Penyewa | penyewa1@simanakos.com | Penyewa@123 |

---

## Lisensi

MIT License — SiManKos © 2026
# project_Capstone
