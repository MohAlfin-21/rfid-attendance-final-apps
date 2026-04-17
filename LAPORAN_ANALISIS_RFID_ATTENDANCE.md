# LAPORAN ANALISIS PROYEK
## Sistem Internet of Things (SIoT)
### "ANALISIS SISTEM ABSENSI RFID BERBASIS LARAVEL 12 DAN NODEMCU ESP8266"

Disusun berdasarkan analisis file proyek pada:

- Tanggal analisis: 12 April 2026
- Lokasi proyek: `c:\IOT\RFID-attendance`
- Metode: analisis struktur file, pembacaan source code, pembacaan dokumentasi internal, pemeriksaan route, dan eksekusi test tanpa mengubah source code

---

## KATA PENGANTAR

Puji syukur penulis panjatkan kepada Allah SWT karena laporan analisis proyek ini dapat disusun dengan baik. Laporan ini dibuat untuk mendokumentasikan hasil analisis menyeluruh terhadap proyek `RFID Attendance`, yaitu sistem absensi sekolah berbasis RFID yang menggabungkan aplikasi web Laravel dengan perangkat IoT NodeMCU ESP8266 dan pembaca kartu MFRC522.

Analisis dilakukan tanpa mengubah kode program inti. Fokus utama laporan ini adalah memahami arsitektur sistem, alur kerja aplikasi, integrasi perangkat keras, struktur database, pembagian hak akses pengguna, pengujian yang tersedia, serta menemukan kesesuaian dan ketidaksesuaian antara dokumentasi dengan implementasi aktual di dalam file proyek.

Laporan ini diharapkan dapat menjadi dasar evaluasi teknis, bahan presentasi, serta referensi pengembangan sistem di tahap berikutnya.

---

## ABSTRAK

Proyek `RFID Attendance` merupakan sistem absensi sekolah berbasis Internet of Things yang terdiri dari dua komponen utama, yaitu aplikasi web berbasis Laravel 12 dan firmware perangkat RFID berbasis NodeMCU ESP8266. Aplikasi web berfungsi sebagai pusat administrasi, pengelolaan data siswa, kelas, perangkat, absensi, dan surat izin. Perangkat ESP8266 berfungsi membaca UID kartu RFID lalu mengirimkan data scan ke API backend untuk diproses.

Berdasarkan hasil analisis file, sistem ini sudah memiliki fondasi arsitektur yang baik, yaitu pemisahan domain bisnis, middleware autentikasi device, role-based access control, pengaturan jam absensi dinamis, pencatatan log scan, dashboard multi-peran, dan pengujian otomatis yang cukup solid pada layer inti. Hasil verifikasi menunjukkan terdapat 60 route terdaftar dan 38 test otomatis yang seluruhnya lulus.

Namun demikian, ditemukan beberapa catatan penting. Dokumentasi internal belum sepenuhnya sinkron dengan implementasi saat ini, terutama pada data akun default. Selain itu, alur persetujuan surat izin belum ditemukan sinkronisasi otomatis ke tabel absensi meskipun dokumentasi menyebutkan hal tersebut. Terdapat pula ketidakkonsistenan antara pengaturan dinamis di `system_settings` dengan sebagian logika runtime yang masih membaca file konfigurasi statis.

**Kata kunci:** RFID, Laravel 12, ESP8266, MFRC522, absensi sekolah, IoT, RBAC, NodeMCU, API device, monitoring perangkat.

---

## DAFTAR ISI

1. BAB I Pendahuluan
2. BAB II Gambaran Umum Proyek
3. BAB III Analisis Implementasi Sistem
4. BAB IV Temuan Analisis dan Evaluasi
5. BAB V Penutup
6. Lampiran

---

# BAB I
## PENDAHULUAN

### 1.1 Latar Belakang

Digitalisasi absensi sekolah menjadi kebutuhan penting untuk meningkatkan ketepatan pencatatan kehadiran, mengurangi manipulasi data, dan mempercepat monitoring oleh pihak sekolah. Salah satu pendekatan yang efektif adalah penggunaan RFID sebagai identitas siswa yang dipadukan dengan sistem backend berbasis web.

Proyek `RFID Attendance` dibangun sebagai sistem absensi sekolah yang tidak hanya mencatat scan kartu, tetapi juga menyediakan panel manajemen untuk admin, guru, sekretaris, dan siswa. Dengan demikian, sistem ini tidak berhenti pada fungsi pembacaan RFID, tetapi berkembang menjadi sistem informasi absensi yang terintegrasi.

### 1.2 Rumusan Masalah

Rumusan masalah dalam analisis ini adalah sebagai berikut:

1. Bagaimana arsitektur umum sistem `RFID Attendance` berdasarkan file proyek yang tersedia?
2. Bagaimana alur kerja absensi RFID dari perangkat sampai penyimpanan data?
3. Bagaimana pembagian hak akses pengguna dalam aplikasi?
4. Apa saja komponen inti perangkat lunak dan perangkat keras yang digunakan?
5. Apa saja temuan penting dari perbandingan antara dokumentasi dan implementasi aktual?

### 1.3 Tujuan Analisis

Tujuan analisis ini adalah:

1. Memahami struktur dan fungsi tiap komponen utama dalam proyek.
2. Menjelaskan alur data dari kartu RFID hingga dashboard web.
3. Mengidentifikasi kekuatan teknis sistem yang sudah dibangun.
4. Menemukan gap, risiko, dan ketidaksesuaian implementasi.
5. Menyusun laporan formal berbasis kondisi file aktual proyek.

### 1.4 Metode Analisis

Metode yang digunakan pada laporan ini adalah:

1. Analisis statis terhadap file konfigurasi, route, controller, model, domain service, migration, view, firmware, dan dokumentasi.
2. Verifikasi perilaku sistem melalui perintah `php artisan route:list --except-vendor`.
3. Verifikasi kualitas dasar proyek melalui `php artisan test`.
4. Penyusunan kesimpulan berdasarkan kode yang benar-benar ada, bukan asumsi.

---

# BAB II
## GAMBARAN UMUM PROYEK

### 2.1 Deskripsi Singkat Sistem

Sistem `RFID Attendance` adalah aplikasi full-stack yang menghubungkan perangkat pembaca RFID dengan backend sekolah. Saat siswa menempelkan kartu RFID pada reader, perangkat akan mengirim UID ke endpoint API. Backend kemudian memvalidasi:

1. validitas token perangkat,
2. status perangkat,
3. status kartu RFID,
4. status pengguna,
5. keanggotaan kelas aktif,
6. jendela waktu absensi,
7. duplikasi scan.

Jika semua validasi lolos, sistem akan mencatat check-in atau check-out dan menyimpan log scan ke database.

### 2.2 Tumpukan Teknologi

| Lapisan | Teknologi | Keterangan |
| --- | --- | --- |
| Backend | PHP 8.2 | Bahasa utama aplikasi web |
| Framework | Laravel 12 | Framework inti aplikasi |
| Auth UI | Laravel Breeze 2.4 | Dasar autentikasi web |
| Role & Permission | Spatie Laravel Permission 6.25 | RBAC multi-peran |
| Frontend bundler | Vite 6 | Build asset frontend |
| Styling | Tailwind CSS 3.1 | Utility CSS |
| Frontend interaction | Alpine.js | Interaksi ringan di sisi klien |
| HTTP client frontend | Axios | Request AJAX/browser |
| Database | SQLite | Terlihat dari file `database/database.sqlite`; test memakai SQLite memory |
| Firmware | Arduino C++ | Kode perangkat ESP8266 |
| Hardware IoT | NodeMCU ESP8266 + MFRC522 | Reader RFID |
| Firmware libraries | ArduinoJson, ESP8266HTTPClient, MFRC522, SPI | Komunikasi HTTP dan pembacaan kartu |

### 2.3 Statistik Struktur Proyek

Berikut ringkasan jumlah komponen inti yang ditemukan:

| Komponen | Jumlah |
| --- | ---: |
| File controller | 22 |
| File model | 8 |
| File domain (DTO, enum, service, exception) | 19 |
| File Blade view | 62 |
| File migration | 12 |
| File test | 15 |
| File bahasa/terjemahan | 16 |
| File hardware | 2 |
| Route terdaftar | 60 |
| Test lulus | 38 |

### 2.4 Peran Pengguna Sistem

| Role | Prefix URL | Fungsi utama |
| --- | --- | --- |
| `admin` | `/admin/*` | Kelola user, kelas, device, absensi, surat izin, dan setting sistem |
| `teacher` | `/teacher/*` | Melihat data kelas wali, rekap absensi, dan mereview surat izin siswanya |
| `secretary` | `/secretary/*` | Mereview surat izin seluruh sekolah dan melihat ringkasan administrasi |
| `student` | `/student/*` | Melihat absensi pribadi dan mengajukan surat izin |

### 2.5 Struktur Modul Utama

Struktur inti proyek terbagi menjadi:

1. `app/Http/Controllers`: pengelolaan request web dan API.
2. `app/Domain`: logika bisnis inti seperti window absensi, status absensi, dan scan device.
3. `app/Models`: representasi tabel database.
4. `database/migrations`: pembentukan skema database.
5. `resources/views`: UI Blade untuk tiap role.
6. `hardware/esp8266-rfid-attendance`: firmware reader RFID.
7. `tests`: pengujian feature dan unit.

---

# BAB III
## ANALISIS IMPLEMENTASI SISTEM

### 3.1 Arsitektur Umum Sistem

Secara umum, arsitektur proyek dapat dijelaskan sebagai berikut:

1. **Perangkat RFID** membaca UID kartu melalui MFRC522.
2. **ESP8266** mengirim request HTTP ke backend dengan header `X-Device-Token`.
3. **Middleware API** memvalidasi token perangkat dan menambahkan `request_id`.
4. **Domain service** memproses aturan absensi.
5. **Database** menyimpan data absensi, log scan, status perangkat, dan pengaturan sistem.
6. **Dashboard web** menampilkan data sesuai role pengguna.

### 3.2 Analisis Routing dan Hak Akses

Hasil `route:list` menunjukkan 60 route aktif. Pemisahan route dilakukan dengan cukup rapi:

- `routes/web.php` untuk seluruh panel web dan profile.
- `routes/api.php` khusus device API.
- `routes/auth.php` untuk login, logout, password reset, dan confirm password.

Hal penting yang terlihat:

1. Registrasi publik dinonaktifkan. Ini sesuai dengan pola sistem sekolah yang seharusnya dikelola admin.
2. Root `/` langsung diarahkan ke halaman login.
3. Redirect dashboard dilakukan berdasarkan role pertama yang cocok.
4. Middleware `role` digunakan untuk membatasi panel per-peran.
5. Middleware `device.token` digunakan khusus API perangkat.

### 3.3 Analisis Backend Web

#### 3.3.1 Modul Admin

Panel admin mencakup:

1. dashboard ringkasan siswa, kehadiran, perangkat, dan surat izin,
2. CRUD pengguna,
3. CRUD kelas dan pengelolaan anggota kelas,
4. CRUD perangkat RFID,
5. rekap absensi dan override status,
6. review surat izin,
7. pengaturan sistem.

Kekuatan modul admin:

1. token perangkat dibuat otomatis dan disimpan dalam bentuk hash serta versi terenkripsi,
2. admin terakhir tidak bisa dihapus atau dinonaktifkan,
3. perangkat memiliki monitoring heartbeat dan status kesehatan,
4. absensi dapat di-override manual dengan catatan.

#### 3.3.2 Modul Guru

Panel guru fokus pada kelas yang diampu sebagai wali kelas:

1. melihat daftar siswa,
2. melihat absensi kelas,
3. melihat dan mereview surat izin siswanya.

Otorisasi review juga dicek ulang di controller dengan memverifikasi bahwa siswa benar-benar termasuk dalam kelas guru tersebut.

#### 3.3.3 Modul Sekretaris

Panel sekretaris lebih sempit tetapi bersifat administratif:

1. melihat ringkasan sekolah,
2. melihat seluruh surat izin,
3. menyetujui atau menolak surat izin.

#### 3.3.4 Modul Siswa

Panel siswa berfungsi untuk:

1. melihat status absensi hari ini,
2. melihat statistik absensi bulanan,
3. melihat riwayat absensi,
4. mengajukan surat izin dengan lampiran gambar.

### 3.4 Analisis Domain Bisnis Absensi

Logika inti absensi berada pada `DeviceAttendanceScanService`, `AttendanceWindowService`, dan `AttendanceStatusService`.

#### 3.4.1 Aturan Jendela Waktu

Default jam yang ditemukan:

| Pengaturan | Nilai default |
| --- | --- |
| Timezone | `Asia/Jakarta` |
| Check-in mulai | `05:45` |
| Check-in selesai | `07:10` |
| Batas terlambat | `06:45` |
| Check-out mulai | `15:00` |
| Check-out selesai | `16:45` |

`AttendanceWindowService` membagi waktu menjadi tiga kondisi:

1. `check_in`
2. `check_out`
3. `outside`

#### 3.4.2 Aturan Scan

Urutan validasi scan adalah:

1. cek device aktif,
2. cek kartu RFID terdaftar,
3. cek status kartu aktif/tidak hilang,
4. cek user aktif,
5. cek keanggotaan kelas aktif,
6. cek apakah scan berada dalam jendela waktu yang valid,
7. cek duplicate cooldown,
8. proses check-in atau check-out.

Hasil scan diklasifikasikan ke dalam `ScanRuleHit`, misalnya:

- `check_in_ok`
- `check_in_late`
- `check_out_ok`
- `duplicate_scan_cooldown`
- `card_not_registered`
- `outside_window`
- `already_checked_in`
- `already_checked_out`

Semua hasil scan akan tercatat pada `attendance_logs`, termasuk scan yang ditolak.

### 3.5 Analisis Integrasi Device API

Endpoint device yang tersedia:

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| `GET` | `/api/v1/devices/settings` | Mengambil pengaturan runtime device |
| `POST` | `/api/v1/devices/heartbeat` | Mengirim heartbeat dan status perangkat |
| `POST` | `/api/v1/devices/attendance/scan` | Mengirim UID hasil scan |

Pengamanan API:

1. setiap request harus membawa `X-Device-Token`,
2. request mendapatkan `X-Request-Id`,
3. token device disimpan sebagai hash SHA-256,
4. data firmware dan kondisi perangkat ikut tercatat.

### 3.6 Analisis Firmware ESP8266

Firmware `hardware/esp8266-rfid-attendance/esp8266-rfid-attendance.ino` menunjukkan perangkat sudah lebih dari sekadar proof of concept. Fitur yang terpasang:

1. koneksi WiFi dengan reconnect otomatis,
2. sinkronisasi setting dari server,
3. heartbeat berkala,
4. pembacaan UID RFID,
5. anti double-tap lokal 2.5 detik,
6. retry HTTP sampai 3 kali,
7. indikator LED dan buzzer untuk sukses, warning, dan error.

#### 3.6.1 Wiring Perangkat

| Komponen | NodeMCU ESP8266 |
| --- | --- |
| SDA / SS MFRC522 | `D8` |
| SCK MFRC522 | `D5` |
| MOSI MFRC522 | `D7` |
| MISO MFRC522 | `D6` |
| RST MFRC522 | `D3` |
| LED putih | `D1` |
| LED hijau | `D2` |
| LED merah | `D0` |
| Buzzer | `D4` |

### 3.7 Analisis Database

Tabel utama yang membentuk sistem:

| Tabel | Fungsi |
| --- | --- |
| `users` | Data akun sistem |
| `roles`, `permissions`, tabel pivot permission | Hak akses pengguna |
| `classrooms` | Data kelas |
| `classroom_students` | Relasi siswa dan kelas per tahun ajaran/semester |
| `devices` | Data reader RFID |
| `rfid_cards` | UID kartu yang terhubung ke siswa |
| `attendances` | Rekap absensi harian |
| `attendance_logs` | Audit log semua scan |
| `absence_requests` | Surat izin/sakit |
| `system_settings` | Konfigurasi dinamis sistem |

Desain databasenya sudah cukup baik karena:

1. keanggotaan kelas mempertimbangkan tahun ajaran dan semester,
2. log scan dipisahkan dari data absensi utama,
3. tabel device menyimpan heartbeat dan error,
4. tabel absensi mendukung check-in dan check-out terpisah.

### 3.8 Analisis Frontend

Frontend dibangun dengan Blade, Tailwind, Alpine.js, dan Vite. Ciri yang terlihat:

1. layout dashboard dibedakan per role,
2. sidebar mendukung mini sidebar dan state disimpan di `localStorage`,
3. beberapa dashboard memakai auto-refresh setiap 3 detik,
4. mendukung switch bahasa Indonesia dan Inggris,
5. halaman login sudah dibuat khusus dan tidak lagi memakai tampilan Breeze default.

Catatan frontend:

1. UI utama sudah cukup rapi dan konsisten,
2. masih ada beberapa file tampilan cadangan atau duplikat,
3. root `README.md` masih bawaan Laravel sehingga dokumentasi utama justru berada di `DOKUMENTASI_SISTEM.md`.

### 3.9 Analisis Pengujian

Test suite yang dijalankan menghasilkan:

- **38 test lulus**
- **131 assertion**
- **durasi 18.33 detik**

Area yang sudah diuji:

1. API device settings, heartbeat, check-in, duplicate tap, check-out, dan invalid token,
2. unit test jendela absensi,
3. unit test status kesehatan device,
4. auth dasar,
5. locale,
6. profile.

Area yang belum terlihat diuji secara spesifik:

1. CRUD admin untuk user, kelas, dan device,
2. review surat izin oleh teacher dan secretary,
3. sinkronisasi approved absence request ke attendance,
4. pengujian firmware secara otomatis.

---

# BAB IV
## TEMUAN ANALISIS DAN EVALUASI

### 4.1 Kekuatan Sistem

Berdasarkan file proyek, kekuatan utama sistem ini adalah:

1. **Arsitektur backend cukup matang.** Logika bisnis penting dipisahkan ke domain service sehingga controller tidak terlalu berat.
2. **Integrasi perangkat sudah realistis.** Firmware mendukung heartbeat, retry, sync settings, dan feedback perangkat.
3. **RBAC jelas.** Admin, guru, sekretaris, dan siswa memiliki panel dan tanggung jawab yang berbeda.
4. **Audit trail tersedia.** Semua scan masuk ke `attendance_logs`, termasuk yang ditolak.
5. **Sistem memiliki test otomatis yang relevan.** Ini menambah kepercayaan terhadap logika inti API device.

### 4.2 Ketidaksesuaian Dokumentasi dengan Implementasi

Beberapa ketidaksesuaian penting ditemukan:

#### 4.2.1 Akun default di dokumentasi tidak sesuai seeder aktual

`DOKUMENTASI_SISTEM.md` menyebut beberapa akun seperti `admin@rfid-attendance.test`, `budi@...`, `ahmad.fauzi@...`, dan lain-lain. Namun `DatabaseSeeder.php` yang ada saat ini hanya membuat:

- satu akun admin: `Admin@smk.id`
- password: `admin`
- satu sample device: `READER-01`

Artinya, dokumentasi akun default saat ini **sudah tidak sinkron** dengan implementasi seeder aktual.

#### 4.2.2 Dokumen menyebut surat izin memfinalisasi absensi, tetapi implementasinya belum ditemukan

Dokumentasi internal menjelaskan bahwa jika surat izin disetujui maka absensi siswa otomatis dikunci sesuai status izin/sakit. Setelah ditelusuri pada controller review surat izin dan model terkait, implementasi yang ada saat ini hanya mengubah kolom:

- `absence_requests.status`
- `reviewed_by`
- `reviewed_at`
- `review_note`

Belum ditemukan kode yang membuat atau memperbarui record di tabel `attendances` saat surat izin disetujui. Ini berarti ada **gap antara deskripsi sistem dan implementasi aktual**.

### 4.3 Risiko dan Catatan Teknis

#### 4.3.1 Potensi mismatch tahun ajaran dan semester

Saat admin menambahkan siswa ke kelas, `ClassroomController` memakai `config('attendance.academic_year')` dan `config('attendance.semester')`. Namun `DeviceAttendanceScanService` memvalidasi kelas aktif memakai `SystemSetting::get('attendance.academic_year')` dan `SystemSetting::get('attendance.semester')`.

Risikonya:

1. admin mengubah tahun ajaran/semester lewat menu setting,
2. data kelas baru masih tersimpan memakai nilai config lama,
3. scan RFID bisa gagal karena siswa dianggap tidak punya keanggotaan kelas aktif pada semester berjalan.

#### 4.3.2 Pengaturan offline threshold device belum sepenuhnya dinamis

`SystemSettingSeeder` menyediakan `devices.offline_threshold_seconds`, dan admin dapat mengubahnya dari UI setting. Namun `DeviceHealthService` masih membaca `config('devices.offline_threshold_seconds')`, bukan `SystemSetting`.

Akibatnya, pengaturan yang diubah admin berpotensi **tidak berpengaruh** pada status kesehatan perangkat di dashboard.

#### 4.3.3 Redirect login untuk user tanpa role

`AuthenticatedSessionController` akan mengarahkan user yang tidak punya role `admin`, `teacher`, atau `secretary` ke dashboard siswa. Sementara route siswa dilindungi middleware `role:student`.

Dalam skenario data user tanpa role, user bisa login tetapi diarahkan ke area yang mungkin tidak bisa diakses. Ini bukan bug yang selalu muncul, tetapi merupakan **risiko konsistensi data akun**.

#### 4.3.4 Kebersihan dokumentasi dan scaffold

Masih ada beberapa sisa scaffold atau file yang tidak mewakili sistem utama, misalnya:

1. `README.md` root masih bawaan Laravel,
2. `welcome.blade.php` masih tampilan default Laravel padahal root diarahkan ke login,
3. terdapat file tampilan alternatif seperti `dashboard-localized.blade.php` dan `settings/index.blade.php` yang tidak terlihat menjadi jalur utama route aktif,
4. ada script utilitas `update-ui.cjs` yang tampaknya dipakai untuk modifikasi massal layout dan bukan bagian runtime inti aplikasi.

### 4.4 Rekomendasi Pengembangan

Berdasarkan temuan di atas, rekomendasi yang disarankan adalah:

1. **Sinkronkan dokumentasi dengan implementasi aktual.** Perbarui `DOKUMENTASI_SISTEM.md` agar akun default, alur demo, dan behavior sistem sesuai codebase saat ini.
2. **Implementasikan finalisasi absensi dari surat izin.** Jika surat izin disetujui, sistem sebaiknya membuat atau memperbarui `attendances` sesuai rentang tanggal.
3. **Samakan sumber pengaturan dinamis.** Gunakan `SystemSetting` secara konsisten untuk tahun ajaran, semester, dan threshold device.
4. **Tambahkan validasi role saat login atau pembuatan akun.** Ini mencegah akun tanpa role masuk ke jalur yang tidak sesuai.
5. **Rapikan file sisa scaffold.** Pisahkan dokumentasi resmi dari file bawaan framework agar codebase lebih mudah dipelihara.
6. **Perluas coverage test.** Utamakan test untuk approval surat izin, admin CRUD, dan sinkronisasi settings.

---

# BAB V
## PENUTUP

### 5.1 Kesimpulan

Berdasarkan analisis keseluruhan file proyek, dapat disimpulkan bahwa `RFID Attendance` adalah sistem absensi sekolah berbasis IoT yang sudah memiliki fondasi teknis yang kuat. Integrasi antara backend Laravel dan perangkat ESP8266 sudah dirancang dengan cukup matang, terutama pada aspek autentikasi device, window absensi, audit log, dan dashboard multi-peran.

Struktur proyek menunjukkan adanya pemisahan tanggung jawab yang baik antara controller, model, domain service, migration, view, dan firmware. Ini menandakan proyek tidak lagi berada pada tahap eksperimen awal, tetapi sudah mengarah ke sistem yang dapat dikembangkan lebih lanjut secara serius.

Walaupun demikian, masih ada sejumlah gap penting yang perlu diperbaiki, terutama sinkronisasi dokumentasi dengan implementasi, finalisasi absensi dari surat izin, serta konsistensi penggunaan pengaturan dinamis. Dengan perbaikan pada area tersebut, sistem ini akan menjadi lebih stabil, lebih mudah dipahami, dan lebih siap untuk dipresentasikan maupun dikembangkan.

### 5.2 Saran

Saran yang dapat diberikan dari hasil analisis ini adalah:

1. Jadikan `DOKUMENTASI_SISTEM.md` dan seeder sebagai satu sumber kebenaran yang selalu diperbarui bersamaan.
2. Buat alur surat izin yang benar-benar terhubung dengan rekap absensi harian.
3. Konsolidasikan semua setting runtime agar tidak bercampur antara config file dan database setting.
4. Tambahkan test untuk skenario operasional sekolah yang lebih nyata.
5. Rapikan file dokumentasi dan file scaffold agar struktur proyek lebih profesional dan mudah dipelajari oleh pengembang berikutnya.

---

## LAMPIRAN

### Lampiran A - Hasil Verifikasi Teknis

Perintah yang dijalankan dalam analisis:

```bash
php artisan route:list --except-vendor
php artisan test
```

Hasil verifikasi:

1. 60 route aktif terdaftar.
2. 38 test lulus.
3. Tidak ada perubahan source code aplikasi selama analisis.

### Lampiran B - File Inti yang Dianalisis

File penting yang menjadi dasar laporan ini antara lain:

- `DOKUMENTASI_SISTEM.md`
- `composer.json`
- `package.json`
- `routes/web.php`
- `routes/api.php`
- `bootstrap/app.php`
- `app/Http/Controllers/...`
- `app/Domain/...`
- `app/Models/...`
- `database/migrations/...`
- `database/seeders/...`
- `hardware/esp8266-rfid-attendance/esp8266-rfid-attendance.ino`
- `tests/...`

### Lampiran C - Ringkasan Status Aktual Proyek

| Aspek | Status hasil analisis |
| --- | --- |
| Arsitektur backend | Baik |
| Integrasi device RFID | Baik |
| Role-based access | Baik |
| Dokumentasi vs implementasi | Perlu sinkronisasi |
| Approval surat izin ke absensi | Belum lengkap |
| Konsistensi settings dinamis | Perlu perapihan |
| Pengujian inti API | Baik |
| Kebersihan scaffold proyek | Perlu dirapikan |

