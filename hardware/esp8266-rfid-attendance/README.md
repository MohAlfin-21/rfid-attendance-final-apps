# ESP8266 RFID Attendance Scanner

Folder ini berisi sketch Arduino IDE untuk `NodeMCU ESP8266 + MFRC522` yang terhubung ke backend Laravel di proyek ini.

## Library Arduino IDE

Install library berikut:

1. `MFRC522` by GithubCommunity / miguelbalboa
2. `ArduinoJson`
3. Board package `ESP8266 by ESP8266 Community`

## Konfigurasi Yang Wajib Diubah

Edit file [esp8266-rfid-attendance.ino](/c:/IOT/RFID-attendance/hardware/esp8266-rfid-attendance/esp8266-rfid-attendance.ino) pada bagian ini:

```cpp
const char *WIFI_SSID = "GANTI_WIFI";
const char *WIFI_PASSWORD = "GANTI_PASSWORD_WIFI";
const char *API_BASE_URL = "https://contoh-subdomain.ngrok-free.app/api/v1/devices";
const char *DEVICE_TOKEN = "GANTI_DEVICE_TOKEN_DARI_PANEL_ADMIN";
```

Catatan penting:

- Jangan gunakan `localhost` atau `127.0.0.1` pada `API_BASE_URL` karena ESP8266 akan menganggap itu sebagai dirinya sendiri.
- Jika memakai `ngrok-free.app`, aktifkan `#define API_USE_HTTPS 1` dan gunakan URL `https://...`, bukan `http://...`.
- Gunakan IP LAN komputer/server Laravel untuk jaringan lokal, contoh `http://192.168.1.10:8000/api/v1/devices`.
- `DEVICE_TOKEN` harus sama dengan token device di halaman admin perangkat.

## Endpoint Yang Dipakai Scanner

- `GET /api/v1/devices/settings`
- `POST /api/v1/devices/heartbeat`
- `POST /api/v1/devices/attendance/scan`

Header wajib:

- `X-Device-Token: <token perangkat>`
- `Accept: application/json`

## Alur Firmware

- Boot lalu konek WiFi
- Sinkron settings dari server
- Kirim heartbeat berkala
- Baca UID kartu dari MFRC522
- Tolak double-tap lokal selama `2.5` detik
- Kirim UID ke API scan
- Tampilkan hasil lewat Serial Monitor, LED, dan buzzer

## Indikator

- LED putih berkedip: scanner idle / sedang konek WiFi
- LED hijau + beep pendek: scan sukses
- LED putih + 2 beep pendek: warning, misalnya scan ganda
- LED merah + beep panjang: error, misalnya kartu tidak terdaftar atau server gagal dihubungi

## Sebelum Upload Ke Board

1. Jalankan backend Laravel.
2. Pastikan device sudah dibuat di panel admin dan token sudah dicopy.
3. Pastikan kartu RFID sudah terdaftar ke siswa di database.
4. Buka Serial Monitor `115200`.
5. Upload sketch ke board `NodeMCU 1.0 (ESP-12E Module)`.
