# ESP8266 RFID Attendance Scanner

Firmware untuk **NodeMCU ESP8266 + MFRC522** yang terhubung ke backend Laravel proyek ini.

---

## Prasyarat

Install library berikut di Arduino IDE melalui **Library Manager** (`Ctrl+Shift+I`):

| Library | Author |
|---------|--------|
| `MFRC522` | GithubCommunity / Miguel Balboa |
| `ArduinoJson` | Benoit Blanchon |

Board package yang diperlukan:

- Tambahkan URL berikut di **File → Preferences → Additional Board Manager URLs:**
  ```
  http://arduino.esp8266.com/stable/package_esp8266com_index.json
  ```
- Kemudian install **ESP8266 by ESP8266 Community** melalui **Board Manager**

---

## Setup Konfigurasi (Wajib)

Firmware menggunakan file `secrets.h` untuk menyimpan kredensial. File ini **tidak akan pernah masuk ke Git**.

### Langkah 1 — Buat `secrets.h` dari template

```bash
# Di dalam folder ini (hardware/esp8266-rfid-attendance/)
cp secrets.h.example secrets.h
```

### Langkah 2 — Edit `secrets.h` dengan nilai asli Anda

```cpp
#define SECRETS_WIFI_SSID     "nama-wifi-anda"
#define SECRETS_WIFI_PASSWORD "password-wifi-anda"
#define SECRETS_API_BASE_URL  "http://192.168.1.100:8000/api/v1/devices"
#define SECRETS_DEVICE_TOKEN  "salin-dari-dashboard-admin"
```

> ⚠️ **JANGAN commit `secrets.h`!** File ini sudah otomatis di-ignore oleh `.gitignore`.

### Catatan penting `API_BASE_URL`

| Skenario | Format URL |
|----------|-----------|
| **LAN lokal** | `http://192.168.1.100:8000/api/v1/devices` |
| **Ngrok** | `https://xxxx.ngrok-free.app/api/v1/devices` |
| **Railway/production** | `https://app.up.railway.app/api/v1/devices` |

> Jangan gunakan `localhost` atau `127.0.0.1` — ESP8266 akan menganggap itu IP dirinya sendiri.

> Jika menggunakan **HTTPS** (Ngrok/Railway), ubah baris berikut di `.ino`:
> ```cpp
> #define API_USE_HTTPS 1
> ```

### Mendapatkan Device Token

1. Buka dashboard Admin → **Devices**
2. Tambahkan device baru atau buka device yang sudah ada
3. Salin token yang ditampilkan
4. Paste ke `SECRETS_DEVICE_TOKEN` di `secrets.h`

---

## Wiring

### MFRC522 → NodeMCU

| MFRC522 | NodeMCU | Keterangan |
|---------|---------|-----------|
| SDA (SS) | `D8` | Chip Select |
| SCK | `D5` | SPI Clock |
| MOSI | `D7` | SPI Data In |
| MISO | `D6` | SPI Data Out |
| RST | `D3` | Reset |
| 3.3V | `3.3V` | Power |
| GND | `GND` | Ground |

### Komponen Tambahan

| Komponen | Pin NodeMCU |
|----------|------------|
| LED Putih (idle) | `D1` |
| LED Hijau (sukses) | `D2` |
| LED Merah (error) | `D0` |
| Buzzer | `D4` |

---

## Upload ke Board

1. Pastikan backend Laravel sudah berjalan dan dapat diakses dari ESP8266
2. Buat device di panel admin dan copy token ke `secrets.h`
3. Pastikan kartu RFID siswa sudah terdaftar di database
4. Buka `esp8266-rfid-attendance.ino` di Arduino IDE
5. Pilih board: **NodeMCU 1.0 (ESP-12E Module)**
6. Pilih port COM yang sesuai
7. Upload sketch
8. Buka **Serial Monitor** dengan baud rate `115200`

---

## Alur Firmware

```
Boot → Inisialisasi SPI + MFRC522
     → Konek WiFi (timeout 20 detik)
     → Sinkron settings dari server
     → Kirim heartbeat pertama
     → Loop:
         ├─ Maintain WiFi (reconnect jika putus)
         ├─ Maintain heartbeat (interval dari server)
         ├─ Maintain sinkron settings (tiap 5 menit)
         └─ Baca kartu RFID
              ├─ Cek duplicate lokal (cooldown 2.5 detik)
              ├─ Cek mode enrollment aktif?
              │    ├─ Ya → POST /card-enrollment/scan
              │    └─ Tidak → POST /attendance/scan
              └─ Tampilkan hasil via LED + buzzer
```

---

## Endpoint yang Digunakan

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `GET` | `/settings` | Ambil pengaturan runtime (jadwal, cooldown) |
| `POST` | `/heartbeat` | Kirim status perangkat berkala |
| `POST` | `/attendance/scan` | Proses scan absensi |
| `GET` | `/card-enrollment/pending` | Cek sesi registrasi aktif |
| `POST` | `/card-enrollment/scan` | Daftarkan kartu baru |

**Header wajib di setiap request:**
```
X-Device-Token: <token-perangkat>
Accept: application/json
```

---

## Indikator LED & Buzzer

| Kondisi | LED | Buzzer |
|---------|-----|--------|
| Connecting WiFi | Putih berkedip cepat | — |
| Idle / Siap scan | Putih pulse tiap 3 detik | — |
| Scan **sukses** | Hijau nyala sebentar | 1× beep pendek |
| **Warning** (scan ganda, dll) | Putih nyala sebentar | 2× beep pendek |
| **Error** (kartu tidak terdaftar, server gagal) | Merah nyala sebentar | 1× beep panjang |
| Boot sequence | Putih → Hijau → Merah | — |
