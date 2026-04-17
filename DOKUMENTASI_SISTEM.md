# Dokumentasi & Alur Lengkap RFID Attendance System

Sistem Manajemen Absensi RFID ini adalah aplikasi *full-stack* berbasis **Laravel 12** yang memanfaatkan pendekatan **Domain-Driven Design (DDD)**. Sistem ini menghubungkan perangkat keras pembaca RFID (IoT) dengan panel monitoring yang terbagi secara spesifik untuk masing-masing peran (Multi-Role Dashboard).

---

## 1. Arsitektur Role & Hak Akses (RBAC)

Sistem menggunakan `spatie/laravel-permission` untuk membatasi akses setiap pengguna secara ketat:

| Role | Tema Warna | Akses URL | Fungsionalitas Utama |
|---|---|---|---|
| **Admin** | Indigo (Gelap) | `/admin/*` | Manajemen penuh: User, Manajemen Kelas, Device IoT, Pengaturan Sistem, dan Hak Akses. |
| **Teacher** (Wali Kelas) | Emerald (Hijau) | `/teacher/*` | Hanya melihat data siswa yang berada di kelas binaannya. Dapat melihat rekap absensi kelas dan mereview serta menyetujui izin. |
| **Secretary** | Violet (Ungu) | `/secretary/*` | Hak akses administratif khusus untuk meninjau permohonan izin dari *seluruh* siswa di sekolah. |
| **Student** (Siswa) | Cyan (Biru) | `/student/*` | Melihat statistik absensi pribadi dan fitur utama untuk mengunggah pengajuan permohonan izin (upload file). |

> **Catatan Fleksibilitas:** Aplikasi mendukung role ganda. Contoh: Siswa dapat diberi tambahan role `secretary` sehingga mereka dapat mengakses panel siswa (`/student`) sekaligus panel sekretaris (`/secretary`) untuk membantu administrasi.

---

## 2. Alur Sistem (Workflows)

### A. Alur Absensi Otomatis (via Mesin RFID)
1. **Pendaftaran Kartu:** Admin via halaman backend menghubungkan UIDA (Nomor Kartu) RFID dengan akun Siswa.
2. **Scanning:** Siswa datang ke sekolah dan menempelkan kartu di Mesin RFID (contoh: ESP32).
3. **Validasi Perangkat (IoT):** Perangkat mengirim request API ke `/api/v1/devices/attendance/scan`. API memiliki lapisan *middleware* khusus yang memeriksa `Device Token` yang terenkripsi.
4. **Validasi Waktu (Business Logic):** Sistem memeriksa jam scan:
   - Apabila masuk pada rentang kelas buka → Siswa dihitung **Hadir**.
   - Apabila masuk pada rentang terlambat → Dihitung **Terlambat**.
5. **Perekaman:** Kehadiran langsung tersimpan di database dan memicu penambahan statistik secara instan di visualisasi Dashboard.

### B. Alur Permohonan Izin / Sakit (Absence Request)
Sistem ini menggunakan mekanisme otoritas (*approval*) untuk memvalidasi surat izin:
1. **Pengajuan (Siswa):** Siswa login dan masuk ke menu "Surat Izin". Mereka memilih tipe (Izin/Sakit), rentang tanggal, memberikan alasan, dan **mengunggah bukti foto dokumen** (Maksimal 2MB, format `.png`/`.jpg`).
2. **Status Awal:** Permohonan otomatis berstatus `Pending`. Ini akan memunculkan lencana *Badge Warning* di dasbor.
3. **Peninjauan (Review):** 
   - **Wali Kelas** (hanya melihat persetujuan siswa di kelas ampuannya) ATAU **Sekretaris** (melihat seluruh kelas di sekolah).
   - Wali kelas / Sekretaris memantau lampiran asli dan mengklik *Setujui* atau *Tolak*, opsional dengan menyertakan *Catatan Review*.
4. **Finalisasi:** Apabila disetujui, absensi siswa otomatis terkunci menjadi berstatus sesuai tipe suratnya (`Sick` / `Excused`).

---

## 3. Urutan Presentasi & Demo Web Lengkap

Untuk mempresentasikan aplikasi ini dengan maksimal (agar penguji atau *client wow*), ikuti skenario demo berjalan ini secara berurutan:

### **Langkah 1: Impresi Pertama (Sisi UI Teras Depan)**
- Masuk ke `http://127.0.0.1:8000/login`.
- Arahkan audiens pada **desain Dark-Glassmorphism** dan responsivitas. Penampilan luar menunjukkan produk sudah sangat disiapkan, *"bukan sekedar MVP"*.

### **Langkah 2: Skala Administratif (Admin Board)**
- **Login:** `admin@rfid-attendance.test` / `password`
- **Tunjukkan:** Pengelolaan User (bisa tambah siswa/guru) dan Pengaturan Kelas. Tunjukkan fleksibilitas mendaftarkan device tanpa perlu modifikasi kodifikasi (via Token).
- Logout via sidebar pojok kiri bawah.

### **Langkah 3: Pengalaman Murid (Student Portal)**
- **Login:** `ahmad.fauzi@rfid-attendance.test` / `password`
- **Tunjukkan:** Tema khas Cyan. Bagaimana murid secara ringkas melihat apakah hari itu mereka terekam *Hadir*, dan melihat rekap sebulan.
- **Demo Interaktif:** Buatlah pengajuan izin baru (misal karena acara keluarga). Upload foto asal, lalu berhasil disubmit. (Lihat badge kuning `Menunggu`).
- Logout.

### **Langkah 4: Validasi & Persetujuan Logis (Teacher / Secretary)**
- **Login:** Pilih login sebagai **Dewi Lestari** `dewi.lestari@rfid-attendance.test` / `password` (untuk demo Sekretaris sekolah) atau `budi@rfid-attendance.test` (demo Wali Kelas Ahmad Fauzi).
- **Tunjukkan:** Langsung arahkan ke halaman "Permohonan Izin". Rekues yang dibuat si Ahmad Fauzi tadi sudah muncul di sistem Guru/Sekretaris ini secara real-time. 
- **Demo Interaktif:** Buka lampiran dari Ahmad, tunjukkan sistem viewer, dan klik setujui. Kembali ke home dan jelaskan bahwa metrik absensi sekolah sudah terupdate secara atomik.

---

## 4. Daftar Akun Master (Default Seeder)

Semua kata sandi *(password)* default adalah: `password`

| Nama Pengguna | Surel Login | Role Tersemat | Status Fungsional |
|---|---|---|---|
| Administrator | `admin@rfid-attendance.test` | **Admin** | Superuser, akses `/admin` |
| Budi Santoso | `budi@rfid-attendance.test` | **Teacher** | Akses panel `/teacher`, Guru Wali Kelas dari *XII RPL 1*. |
| Ahmad Fauzi | `ahmad.fauzi@rfid-attendance.test`| **Student** | Siswa reguler, muridnya Budi Santoso. |
| Dewi Lestari | `dewi.lestari@rfid-attendance.test`| **Student + Secretary**| Model Dual-Role (Akses Panel `/student` & `/secretary`). |
| Rizky Pratama | `rizky.pratama@rfid-attendance.test`| **Student** | Cadangan login murid |

---

## 5. Cara Menjalankan Sistem (Local Development)

Untuk menjalankan sistem ini di komputer Anda (localhost), Anda harus membuka **2 terminal (command prompt/PowerShell)** terpisah secara bersamaan, arahkan ke folder proyek (`c:\IOT\RFID-attendance`), lalu jalankan:

### Terminal 1: Menjalankan Server Backend (Laravel)
Terminal ini bertugas menghidupkan core sistem, database, dan logika API utama.
```bash
php artisan serve
```
*Tunggu hingga muncul pesan `Server running on [http://127.0.0.1:8000]`.*

### Terminal 2: Menjalankan Server Frontend (Vite/Tailwind)
Terminal ini berfungsi merender mempercantik UI, CSS, serta *hot-reloading* halaman. Tanpa ini, tampilan website akan hancur/berantakan.
```bash
npm run dev
```

**Penting:** Biarkan kedua terminal tersebut tetap menyala terbuka.
Selanjutnya, Anda dapat membuka browser web (Chrome/Edge) dan mengakases tautan ini:
👉 **http://127.0.0.1:8000**

---

## 6. Integrasi Hardware Scanner ESP8266

Firmware scanner ada di [hardware/esp8266-rfid-attendance/esp8266-rfid-attendance.ino](/c:/IOT/RFID-attendance/hardware/esp8266-rfid-attendance/esp8266-rfid-attendance.ino) dan panduan singkatnya ada di [hardware/esp8266-rfid-attendance/README.md](/c:/IOT/RFID-attendance/hardware/esp8266-rfid-attendance/README.md).

### Endpoint Device

Perangkat RFID menggunakan token device pada header `X-Device-Token` dan mengakses endpoint berikut:

- `GET /api/v1/devices/settings`
- `POST /api/v1/devices/heartbeat`
- `POST /api/v1/devices/attendance/scan`

### Payload Scan

Contoh payload yang dikirim ESP8266:

```json
{
  "uid": "A1B2C3D4",
  "firmware_version": "esp8266-mfrc522-1.0.0",
  "wifi_rssi": -58,
  "free_heap": 40200,
  "reader_uptime_ms": 12500,
  "ip_address": "192.168.1.20"
}
```

### Penting Untuk Testing Lokal

- Pada firmware, `API_BASE_URL` harus memakai IP LAN komputer/server, bukan `localhost`.
- Device token diambil dari panel admin perangkat.
- Jika kartu belum terdaftar di database, API akan membalas `card_not_registered`.
- Server sekarang sudah memiliki proteksi `duplicate_scan_cooldown`, validasi window check-in/check-out, heartbeat, dan logging ke `attendance_logs`.
