<h1 align="center">🏫 RFID Attendance System</h1>

<p align="center">
  Sistem Absensi Sekolah berbasis RFID yang menghubungkan perangkat IoT <strong>NodeMCU ESP8266 + MFRC522</strong> dengan backend <strong>Laravel 12</strong>.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/ESP8266-NodeMCU-00979D?style=for-the-badge&logo=arduino&logoColor=white" alt="ESP8266">
  <img src="https://img.shields.io/badge/PostgreSQL-Railway-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Tests-38%20passed-22C55E?style=for-the-badge" alt="Tests">
</p>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Struktur Proyek](#-struktur-proyek)
- [Prasyarat](#-prasyarat)
- [Instalasi & Setup Lokal](#-instalasi--setup-lokal)
- [Setup Hardware (ESP8266)](#-setup-hardware-esp8266)
- [Peran Pengguna & Hak Akses](#-peran-pengguna--hak-akses)
- [API Device](#-api-device)
- [Deploy ke Railway](#-deploy-ke-railway)
- [Menjalankan Test](#-menjalankan-test)
- [Keamanan](#-keamanan)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Proyek

**RFID Attendance** adalah sistem absensi digital untuk sekolah yang menggantikan absensi manual dengan pembacaan kartu RFID. Saat siswa menempelkan kartu pada reader, sistem secara otomatis:

1. Memvalidasi identitas kartu dan perangkat
2. Menentukan status kehadiran (tepat waktu / terlambat / keluar)
3. Mencatat rekap absensi dan log scan ke database
4. Menampilkan hasil real-time melalui LED dan buzzer di perangkat

Panel web menyediakan manajemen lengkap untuk Admin, Guru, Sekretaris, dan Siswa.

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                     RFID ATTENDANCE SYSTEM                   │
├──────────────────────┬──────────────────────────────────────┤
│   HARDWARE (IoT)     │         BACKEND (Laravel)            │
│                      │                                      │
│  ┌────────────────┐  │  ┌─────────────────────────────────┐ │
│  │ Kartu RFID     │  │  │  Middleware: DeviceTokenAuth    │ │
│  │ (UID)          │  │  │  • Validasi X-Device-Token      │ │
│  └───────┬────────┘  │  │  • IP Whitelist check           │ │
│          │ tempel    │  └──────────────┬──────────────────┘ │
│  ┌───────▼────────┐  │                 │                    │
│  │ MFRC522        │  │  ┌──────────────▼──────────────────┐ │
│  │ (Reader RFID)  │  │  │  Domain Services                │ │
│  └───────┬────────┘  │  │  • AttendanceWindowService      │ │
│          │ SPI       │  │  • AttendanceStatusService      │ │
│  ┌───────▼────────┐  │  │  • DeviceAttendanceScanService  │ │
│  │ NodeMCU        │─HTTP─▶  └──────────────┬──────────────────┘ │
│  │ ESP8266        │  │                 │                    │
│  │                │  │  ┌──────────────▼──────────────────┐ │
│  │ • LED x3       │◀─┤  │  Database (PostgreSQL)          │ │
│  │ • Buzzer       │  │  │  • attendances                  │ │
│  └────────────────┘  │  │  • attendance_logs              │ │
│                      │  │  • rfid_cards, devices, users   │ │
└──────────────────────┴──└─────────────────────────────────┘─┘
                                       │
                          ┌────────────▼──────────────┐
                          │    Dashboard Web           │
                          │  Admin / Guru / Sekretaris │
                          │  / Siswa (Multi-role UI)   │
                          └───────────────────────────┘
```

### Alur Absensi (End-to-End)

```
Siswa tap kartu → ESP8266 baca UID → HTTP POST /api/v1/devices/attendance/scan
  → Validasi token perangkat → Cek status kartu & user → Cek jendela waktu
  → Cek duplikasi → Simpan attendance + log → Response JSON
  → ESP8266 tampilkan hasil (LED hijau = sukses, merah = error)
```

---

## ✨ Fitur Utama

### 🔧 Perangkat RFID (ESP8266)
- ✅ Koneksi WiFi dengan auto-reconnect otomatis
- ✅ Sinkronisasi pengaturan jadwal dari server secara berkala
- ✅ Heartbeat berkala ke server (monitoring status perangkat)
- ✅ Anti double-tap lokal (cooldown 2.5 detik)
- ✅ Retry HTTP otomatis hingga 3 kali jika koneksi gagal
- ✅ Mode registrasi kartu baru (enrollment)
- ✅ Indikator LED 3 warna + buzzer

### 🌐 Aplikasi Web (Laravel)
- ✅ Dashboard multi-role yang berbeda tampilan per peran
- ✅ Manajemen pengguna, kelas, perangkat, dan kartu RFID
- ✅ Jam absensi dinamis — dapat diubah dari panel admin tanpa deploy ulang
- ✅ Override status absensi manual oleh admin
- ✅ Pengajuan & review surat izin/sakit dengan lampiran
- ✅ Rekap absensi bulanan per siswa
- ✅ Monitoring kesehatan perangkat (heartbeat, last seen, error)
- ✅ Token perangkat di-hash SHA-256 (tidak disimpan plain text)
- ✅ IP Whitelist per perangkat
- ✅ Auto-refresh dashboard setiap 3 detik
- ✅ Bilingual: Bahasa Indonesia & English

---

## 🛠️ Tech Stack

| Lapisan | Teknologi | Versi |
|---------|-----------|-------|
| **Backend** | PHP + Laravel | `^8.2` + `^12.0` |
| **Auth** | Laravel Breeze | `^2.4` |
| **Role & Permission** | Spatie Laravel Permission | `^6.25` |
| **Database** | PostgreSQL (production) / SQLite (dev) | — |
| **Frontend** | Blade + Tailwind CSS + Alpine.js + Vite | CSS `3.1`, Vite `6` |
| **Hardware** | NodeMCU ESP8266 + MFRC522 | — |
| **Firmware** | Arduino C++ | — |
| **Firmware Libraries** | ArduinoJson, ESP8266HTTPClient, MFRC522, SPI | — |

---

## 📁 Struktur Proyek

```
rfid-attendance/
├── app/
│   ├── Domain/                     # Logika bisnis inti (tidak terikat HTTP)
│   │   ├── Attendance/Services/    # AttendanceWindowService, StatusService, ScanService
│   │   └── Devices/                # DTOs, Enums, DeviceHealthService
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # CRUD user, kelas, device, absensi, setting
│   │   │   ├── Api/V1/Devices/     # Endpoint untuk ESP8266
│   │   │   ├── Teacher/            # Panel guru
│   │   │   ├── Secretary/          # Panel sekretaris
│   │   │   └── Student/            # Panel siswa
│   │   └── Middleware/
│   │       ├── DeviceTokenAuth.php # Validasi X-Device-Token + IP whitelist
│   │       └── RequestId.php       # Inject X-Request-Id pada setiap request
│   └── Models/                     # Eloquent models
├── hardware/
│   └── esp8266-rfid-attendance/
│       ├── esp8266-rfid-attendance.ino  # Firmware utama
│       ├── secrets.h.example            # Template konfigurasi (COMMIT ini)
│       └── secrets.h                    # Konfigurasi aktual (JANGAN commit!)
├── database/
│   ├── migrations/                 # 12 file migrasi skema
│   └── seeders/                    # Seeder role, setting, demo data
├── resources/views/                # 62 file Blade per role
├── tests/                          # 38 test (feature + unit)
├── .env.example                    # Template environment
├── DEPLOYMENT_POSTGRES_RAILWAY.md  # Panduan deploy Railway
└── LAPORAN_ANALISIS_RFID_ATTENDANCE.md
```

---

## 📦 Prasyarat

**Backend:**
- PHP `>= 8.2` dengan extension `pdo_pgsql`, `pgsql`, `mbstring`, `xml`
- Composer `>= 2.x`
- Node.js `>= 18.x` + npm
- PostgreSQL (atau SQLite untuk development lokal)

**Hardware:**
- Arduino IDE `>= 2.x`
- Board package: **ESP8266 by ESP8266 Community** (`http://arduino.esp8266.com/stable/package_esp8266com_index.json`)
- Library Arduino:
  - `MFRC522` by GithubCommunity (Miguel Balboa)
  - `ArduinoJson` by Benoit Blanchon

---

## 🚀 Instalasi & Setup Lokal

### 1. Clone Repositori

```bash
git clone https://github.com/username/rfid-attendance.git
cd rfid-attendance
```

### 2. Install Dependensi PHP

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai konfigurasi lokal Anda:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=rfid_attendance
DB_USERNAME=postgres
DB_PASSWORD=password_anda

# Token device awal untuk seeder (opsional)
SEED_DEMO_DATA=false
SEED_DEVICE_TOKEN=dev-token-anda
```

> **Catatan:** Untuk development menggunakan SQLite, ubah `DB_CONNECTION=sqlite` dan hapus baris `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

### 4. Migrasi Database & Seeder

```bash
php artisan migrate

# Isi data role & permission (wajib)
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SystemSettingSeeder

# Isi demo data (opsional, aktifkan SEED_DEMO_DATA=true di .env dulu)
# php artisan db:seed
```

### 5. Install Frontend & Build Asset

```bash
npm install
```

### 6. Jalankan Development Server

```bash
# Jalankan semua sekaligus (Laravel + Vite + Queue + Logs)
composer run dev
```

Atau secara terpisah:

```bash
php artisan serve   # Backend → http://127.0.0.1:8000
npm run dev         # Vite HMR
```

### 7. Buat Akun Admin Pertama

```bash
php artisan tinker
```

```php
use App\Models\User;
$user = User::create([
    'name'     => 'Admin Sekolah',
    'email'    => 'admin@sekolah.sch.id',
    'password' => bcrypt('password'),
]);
$user->assignRole('admin');
```

---

## 🔌 Setup Hardware (ESP8266)

### Wiring MFRC522 → NodeMCU ESP8266

| MFRC522 | NodeMCU | Keterangan |
|---------|---------|-----------|
| SDA (SS) | `D8` | Chip Select |
| SCK | `D5` | SPI Clock |
| MOSI | `D7` | SPI Data In |
| MISO | `D6` | SPI Data Out |
| RST | `D3` | Reset |
| 3.3V | `3.3V` | Power |
| GND | `GND` | Ground |

| Komponen | Pin NodeMCU |
|----------|------------|
| LED Putih (idle) | `D1` |
| LED Hijau (sukses) | `D2` |
| LED Merah (error) | `D0` |
| Buzzer | `D4` |

### Konfigurasi Firmware

**1. Buat file `secrets.h`** dari template yang sudah tersedia:

```bash
# Di dalam folder hardware/esp8266-rfid-attendance/
cp secrets.h.example secrets.h
```

**2. Edit `secrets.h`** dengan nilai asli Anda:

```cpp
#define SECRETS_WIFI_SSID     "nama-wifi-anda"
#define SECRETS_WIFI_PASSWORD "password-wifi-anda"
#define SECRETS_API_BASE_URL  "http://192.168.1.100:8000/api/v1/devices"
#define SECRETS_DEVICE_TOKEN  "salin-dari-dashboard-admin"
```

> ⚠️ **PENTING:** File `secrets.h` sudah otomatis masuk `.gitignore`. **Jangan pernah commit file ini ke Git.**

**3. Catatan penting `API_BASE_URL`:**

| Skenario | Format URL |
|----------|-----------|
| Jaringan lokal (LAN) | `http://192.168.1.100:8000/api/v1/devices` |
| Ngrok tunnel | `https://xxxx.ngrok-free.app/api/v1/devices` |
| Railway/production | `https://app.up.railway.app/api/v1/devices` |

> Jika menggunakan HTTPS (Ngrok/Railway), ubah `#define API_USE_HTTPS 1` di baris paling atas file `.ino`.

**4. Dapatkan Device Token:**
- Buka dashboard Admin → **Devices** → Tambah device baru
- Salin token yang ditampilkan → paste ke `SECRETS_DEVICE_TOKEN` di `secrets.h`

**5. Upload Firmware:**
- Buka `esp8266-rfid-attendance.ino` di Arduino IDE
- Board: `NodeMCU 1.0 (ESP-12E Module)`
- Upload speed: `115200`
- Buka **Serial Monitor** (115200 baud) untuk monitoring

### Indikator LED & Buzzer

| Kondisi | LED | Buzzer |
|---------|-----|--------|
| Connecting WiFi | Putih berkedip cepat | — |
| Idle / Siap | Putih pulse tiap 3 detik | — |
| Scan sukses (tepat waktu) | Hijau nyala sebentar | 1× beep pendek |
| Scan terlambat / warning | Putih nyala sebentar | 2× beep pendek |
| Error (kartu tidak terdaftar, server gagal) | Merah nyala sebentar | 1× beep panjang |
| Boot berhasil | Putih → Hijau → Merah | — |

---

## 👥 Peran Pengguna & Hak Akses

| Role | URL Panel | Kemampuan |
|------|-----------|-----------|
| **Admin** | `/admin/*` | CRUD user, kelas, device, absensi; override status; pengaturan sistem |
| **Teacher (Wali Kelas)** | `/teacher/*` | Lihat daftar siswa kelas wali, rekap absensi, review surat izin |
| **Secretary (Sekretaris)** | `/secretary/*` | Review & setujui/tolak semua surat izin; ringkasan administrasi |
| **Student (Siswa)** | `/student/*` | Lihat absensi pribadi; ajukan surat izin dengan lampiran |

---

## 📡 API Device

Semua endpoint memerlukan header:

```http
X-Device-Token: <token-perangkat>
Accept: application/json
```

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `GET` | `/api/v1/devices/settings` | Ambil pengaturan runtime (jadwal, cooldown, timezone) |
| `POST` | `/api/v1/devices/heartbeat` | Kirim status perangkat (IP, heap, RSSI, uptime) |
| `POST` | `/api/v1/devices/attendance/scan` | Kirim UID hasil scan RFID |
| `GET` | `/api/v1/devices/card-enrollment/pending` | Cek apakah ada sesi registrasi kartu aktif |
| `POST` | `/api/v1/devices/card-enrollment/scan` | Kirim UID untuk registrasi kartu baru |

### Contoh Request Scan

```bash
curl -X POST https://your-app.railway.app/api/v1/devices/attendance/scan \
  -H "X-Device-Token: dev-xxxxxxxxxxxx" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"uid":"A1B2C3D4","firmware_version":"esp8266-mfrc522-1.0.0"}'
```

### Contoh Response Sukses

```json
{
  "ok": true,
  "result": "success",
  "code": "check_in_ok",
  "message": "Check-in berhasil",
  "student": {
    "name": "Budi Santoso",
    "classroom": "XI RPL 1"
  },
  "status": "hadir"
}
```

---

## ☁️ Deploy ke Railway

Lihat panduan lengkap di [`DEPLOYMENT_POSTGRES_RAILWAY.md`](./DEPLOYMENT_POSTGRES_RAILWAY.md).

**Ringkasan cepat:**

```env
# Environment variables di Railway App Service
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...hasil php artisan key:generate...
APP_URL=https://your-app.up.railway.app
LOG_CHANNEL=stderr
DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}
DB_SSLMODE=prefer
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

---

## 🧪 Menjalankan Test

```bash
php artisan test
```

**Hasil saat ini:** ✅ 38 test lulus, 131 assertion

Area yang diuji:
- API endpoint perangkat (settings, heartbeat, scan, invalid token, duplicate tap)
- Unit test jendela waktu absensi (`AttendanceWindowService`)
- Unit test status kesehatan device (`DeviceHealthService`)
- Autentikasi dasar (login, logout, password)
- Locale switching

---

## 🔐 Keamanan

| Mekanisme | Implementasi |
|-----------|-------------|
| Token perangkat | Disimpan sebagai **SHA-256 hash** — token asli tidak pernah disimpan |
| IP Whitelist | Per perangkat, dapat dikonfigurasi dari dashboard admin |
| Kredensial firmware | Disimpan di `secrets.h` yang **di-ignore oleh Git** |
| File `.env` | Di-ignore Git, tidak pernah di-commit |
| Password user | Di-hash dengan **bcrypt** (12 rounds) |
| Session | Dapat dikonfigurasi secure cookie untuk HTTPS |

> 📁 Untuk setup awal perangkat baru, salin `hardware/esp8266-rfid-attendance/secrets.h.example` menjadi `secrets.h` dan isi dengan nilai asli.

---

## 📜 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  Dibuat sebagai proyek IoT berbasis Laravel 12 + ESP8266
</p>
