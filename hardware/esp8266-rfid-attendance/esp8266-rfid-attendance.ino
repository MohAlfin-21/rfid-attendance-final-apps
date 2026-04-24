#include <ArduinoJson.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <MFRC522.h>
#include <SPI.h>

#define API_USE_HTTPS 0

#if API_USE_HTTPS
#include <WiFiClientSecureBearSSL.h>
BearSSL::WiFiClientSecure networkClient;
#else
WiFiClient networkClient;
#endif

/*
 * RFID Attendance - ESP8266 + MFRC522
 *
 * Hardware:
 * - NodeMCU ESP8266
 * - MFRC522 RFID Reader
 * - LED Putih (D1), Hijau (D2), Merah (D0)
 * - Buzzer (D4)
 *
 * Wiring:
 * MFRC522 -> NodeMCU
 * - SDA (SS) -> D8
 * - SCK      -> D5
 * - MOSI     -> D7
 * - MISO     -> D6
 * - RST      -> D3
 * - 3.3V     -> 3.3V
 * - GND      -> GND
 */

// Credentials loaded from secrets.h (gitignored).
// Copy hardware/esp8266-rfid-attendance/secrets.h.example → secrets.h
// and fill in your real values before compiling.
#include "secrets.h"

const char *WIFI_SSID     = SECRETS_WIFI_SSID;
const char *WIFI_PASSWORD = SECRETS_WIFI_PASSWORD;
const char *API_BASE_URL  = SECRETS_API_BASE_URL;
const char *DEVICE_TOKEN  = SECRETS_DEVICE_TOKEN;
const char *FIRMWARE_VERSION = "esp8266-mfrc522-1.0.0";

constexpr uint8_t PIN_SS = D8;
constexpr uint8_t PIN_RST = D3;
constexpr uint8_t PIN_LED_WHITE = D1;
constexpr uint8_t PIN_LED_GREEN = D2;
constexpr uint8_t PIN_LED_RED = D0;
constexpr uint8_t PIN_BUZZER = D4;

constexpr bool LED_ACTIVE_HIGH = true;
constexpr bool BUZZER_ACTIVE_HIGH = true;
constexpr uint32_t SERIAL_BAUD_RATE = 115200;
constexpr uint32_t WIFI_CONNECT_TIMEOUT_MS = 20000;
constexpr uint32_t WIFI_RETRY_INTERVAL_MS = 10000;
constexpr uint32_t SETTINGS_REFRESH_INTERVAL_MS = 300000;
constexpr uint32_t HTTP_TIMEOUT_MS = 8000;
constexpr uint8_t HTTP_MAX_RETRIES = 3;
constexpr uint32_t LOCAL_SCAN_COOLDOWN_MS = 2500;

struct RuntimeSettings
{
  uint32_t heartbeatIntervalMs = 60000;
  uint32_t remoteDuplicateCooldownMs = 30000;
  String timezone = "Asia/Jakarta";
  String checkInStart = "05:45";
  String checkInEnd = "07:10";
  String lateAfter = "06:45";
  String checkOutStart = "15:00";
  String checkOutEnd = "16:45";
};

struct ScanFeedback
{
  bool ok = false;
  String result = "error";
  String code = "unknown";
  String message = "Unknown response";
  String studentName = "";
  String classroom = "";
  String status = "";
};

RuntimeSettings runtimeSettings;
MFRC522 mfrc522(PIN_SS, PIN_RST);

String lastScannedUid = "";
unsigned long lastScannedAt = 0;
unsigned long lastHeartbeatAt = 0;
unsigned long lastSettingsSyncAt = 0;
unsigned long lastWifiRetryAt = 0;
unsigned long lastReadyPulseAt = 0;

void setup()
{
  pinMode(PIN_LED_WHITE, OUTPUT);
  pinMode(PIN_LED_GREEN, OUTPUT);
  pinMode(PIN_LED_RED, OUTPUT);
  pinMode(PIN_BUZZER, OUTPUT);

  allIndicatorsOff();

  Serial.begin(SERIAL_BAUD_RATE);
  delay(50);
  Serial.println();
  Serial.println(F("=== RFID Attendance Scanner Boot ==="));

  SPI.begin();
  mfrc522.PCD_Init();

  bootSequence();

  WiFi.mode(WIFI_STA);
  WiFi.persistent(false);
  WiFi.setAutoReconnect(true);
  WiFi.setSleepMode(WIFI_NONE_SLEEP);

#if API_USE_HTTPS
  networkClient.setInsecure();
#endif

  connectWifiBlocking();

  if (WiFi.status() == WL_CONNECTED)
  {
    syncSettings();
    sendHeartbeat();
  }
}

void loop()
{
  maintainWifi();
  maintainReadyPulse();
  maintainSettings();
  maintainHeartbeat();
  handleCardScan();
}

void handleCardScan()
{
  // Pastikan MFRC522 aktif (anti-crash karena WiFi power drop pada ESP8266)
  byte v = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  if (v == 0x00 || v == 0xFF) {
    mfrc522.PCD_Init();
  }

  if (!mfrc522.PICC_IsNewCardPresent())
  {
    return;
  }

  if (!mfrc522.PICC_ReadCardSerial())
  {
    return;
  }

  String uid = readUid(mfrc522.uid);

  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();

  if (uid.length() == 0)
  {
    signalError("UID kosong atau gagal dibaca");
    return;
  }

  Serial.printf("[SCAN] UID=%s\n", uid.c_str());

  if (isLocalDuplicate(uid))
  {
    signalWarning("Scan lokal diabaikan, kartu masih terlalu dekat");
    return;
  }

  if (WiFi.status() != WL_CONNECTED)
  {
    signalError("WiFi belum terhubung");
    rememberScan(uid);
    return;
  }

  bool enrollmentActive = false;

  if (!fetchEnrollmentPending(enrollmentActive))
  {
    signalError("Gagal cek mode registrasi kartu");
    rememberScan(uid);
    return;
  }

  ScanFeedback feedback = enrollmentActive ? sendEnrollmentScan(uid) : sendScan(uid);
  rememberScan(uid);
  showScanFeedback(feedback);
}

void maintainWifi()
{
  if (WiFi.status() == WL_CONNECTED)
  {
    return;
  }

  unsigned long nowMs = millis();

  if (nowMs - lastWifiRetryAt < WIFI_RETRY_INTERVAL_MS)
  {
    return;
  }

  lastWifiRetryAt = nowMs;
  Serial.println(F("[WIFI] Memulai ulang koneksi..."));
  WiFi.disconnect();
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
}

void maintainHeartbeat()
{
  if (WiFi.status() != WL_CONNECTED)
  {
    return;
  }

  unsigned long nowMs = millis();

  if (nowMs - lastHeartbeatAt < runtimeSettings.heartbeatIntervalMs)
  {
    return;
  }

  sendHeartbeat();
}

void maintainSettings()
{
  if (WiFi.status() != WL_CONNECTED)
  {
    return;
  }

  unsigned long nowMs = millis();

  if (nowMs - lastSettingsSyncAt < SETTINGS_REFRESH_INTERVAL_MS)
  {
    return;
  }

  syncSettings();
}

void maintainReadyPulse()
{
  if (WiFi.status() != WL_CONNECTED)
  {
    setLed(PIN_LED_WHITE, (millis() / 200) % 2 == 0);
    setLed(PIN_LED_GREEN, false);
    setLed(PIN_LED_RED, false);
    return;
  }

  unsigned long nowMs = millis();

  if (nowMs - lastReadyPulseAt >= 3000)
  {
    lastReadyPulseAt = nowMs;
    setLed(PIN_LED_WHITE, true);
    delay(30);
    setLed(PIN_LED_WHITE, false);
  }
}

bool connectWifiBlocking()
{
  if (WiFi.status() == WL_CONNECTED)
  {
    return true;
  }

  Serial.printf("[WIFI] Connecting to %s\n", WIFI_SSID);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  unsigned long startedAt = millis();
  bool ledState = false;

  while (WiFi.status() != WL_CONNECTED && millis() - startedAt < WIFI_CONNECT_TIMEOUT_MS)
  {
    ledState = !ledState;
    setLed(PIN_LED_WHITE, ledState);
    setLed(PIN_LED_GREEN, false);
    setLed(PIN_LED_RED, false);
    delay(180);
    yield();
  }

  setLed(PIN_LED_WHITE, false);

  if (WiFi.status() == WL_CONNECTED)
  {
    Serial.printf("[WIFI] Connected. IP=%s RSSI=%d\n", WiFi.localIP().toString().c_str(), WiFi.RSSI());
    shortBeep(70);
    shortBeep(70);
    setLed(PIN_LED_GREEN, true);
    delay(100);
    setLed(PIN_LED_GREEN, false);
    return true;
  }

  Serial.println(F("[WIFI] Failed to connect"));
  setLed(PIN_LED_RED, true);
  delay(200);
  setLed(PIN_LED_RED, false);
  return false;
}

void syncSettings()
{
  String responseBody;
  int httpCode = 0;

  if (!sendGet("/settings", responseBody, httpCode))
  {
    Serial.println(F("[SETTINGS] Failed to sync settings"));
    return;
  }

  if (!isSuccessfulHttpStatus(httpCode))
  {
    logUnexpectedHttpResponse("SETTINGS", httpCode, responseBody);
    return;
  }

  DynamicJsonDocument responseDoc(1536);
  DeserializationError error = deserializeJson(responseDoc, responseBody);

  if (error)
  {
    Serial.printf("[SETTINGS] Invalid JSON: %s\n", error.c_str());
    return;
  }

  runtimeSettings.heartbeatIntervalMs =
      max(5000UL, (unsigned long)((responseDoc["heartbeat_interval_seconds"] | 60) * 1000UL));
  runtimeSettings.remoteDuplicateCooldownMs =
      max(1000UL, (unsigned long)((responseDoc["duplicate_scan_cooldown_seconds"] | 30) * 1000UL));
  runtimeSettings.timezone = (const char *)(responseDoc["attendance"]["timezone"] | "Asia/Jakarta");
  runtimeSettings.checkInStart = (const char *)(responseDoc["attendance"]["check_in_start"] | "05:45");
  runtimeSettings.checkInEnd = (const char *)(responseDoc["attendance"]["check_in_end"] | "07:10");
  runtimeSettings.lateAfter = (const char *)(responseDoc["attendance"]["late_after"] | "06:45");
  runtimeSettings.checkOutStart = (const char *)(responseDoc["attendance"]["check_out_start"] | "15:00");
  runtimeSettings.checkOutEnd = (const char *)(responseDoc["attendance"]["check_out_end"] | "16:45");
  lastSettingsSyncAt = millis();

  Serial.printf(
      "[SETTINGS] heartbeat=%lus, cooldown=%lus, timezone=%s\n",
      runtimeSettings.heartbeatIntervalMs / 1000UL,
      runtimeSettings.remoteDuplicateCooldownMs / 1000UL,
      runtimeSettings.timezone.c_str());
}

void sendHeartbeat()
{
  StaticJsonDocument<256> payloadDoc;
  payloadDoc["firmware_version"] = FIRMWARE_VERSION;
  payloadDoc["wifi_rssi"] = WiFi.RSSI();
  payloadDoc["free_heap"] = ESP.getFreeHeap();
  payloadDoc["reader_uptime_ms"] = millis();
  payloadDoc["ip_address"] = WiFi.localIP().toString();

  String payload;
  serializeJson(payloadDoc, payload);

  String responseBody;
  int httpCode = 0;

  if (!sendPost("/heartbeat", payload, responseBody, httpCode))
  {
    Serial.println(F("[HEARTBEAT] Failed"));
    return;
  }

  if (!isSuccessfulHttpStatus(httpCode))
  {
    logUnexpectedHttpResponse("HEARTBEAT", httpCode, responseBody);
    return;
  }

  DynamicJsonDocument responseDoc(768);
  DeserializationError error = deserializeJson(responseDoc, responseBody);

  if (!error)
  {
    runtimeSettings.heartbeatIntervalMs =
        max(5000UL, (unsigned long)((responseDoc["heartbeat_interval_seconds"] | 60) * 1000UL));
  }

  lastHeartbeatAt = millis();
  Serial.printf("[HEARTBEAT] OK http=%d next=%lus\n", httpCode, runtimeSettings.heartbeatIntervalMs / 1000UL);
}

bool fetchEnrollmentPending(bool &active)
{
  active = false;

  String responseBody;
  int httpCode = 0;

  if (!sendGet("/card-enrollment/pending", responseBody, httpCode))
  {
    Serial.println(F("[ENROLL] Gagal mengecek sesi registrasi"));
    return false;
  }

  if (!isSuccessfulHttpStatus(httpCode))
  {
    logUnexpectedHttpResponse("ENROLL", httpCode, responseBody);
    return false;
  }

  DynamicJsonDocument responseDoc(768);
  DeserializationError error = deserializeJson(responseDoc, responseBody);

  if (error)
  {
    Serial.printf("[ENROLL] Invalid JSON: %s\n", error.c_str());
    return false;
  }

  active = responseDoc["active"] | false;

  if (active)
  {
    const char *message = responseDoc["message"] | "Sesi registrasi kartu aktif";
    Serial.printf("[ENROLL] %s\n", message);
  }

  return true;
}

ScanFeedback sendScan(const String &uid)
{
  ScanFeedback feedback;

  StaticJsonDocument<256> payloadDoc;
  payloadDoc["uid"] = uid;
  payloadDoc["firmware_version"] = FIRMWARE_VERSION;
  payloadDoc["wifi_rssi"] = WiFi.RSSI();
  payloadDoc["free_heap"] = ESP.getFreeHeap();
  payloadDoc["reader_uptime_ms"] = millis();
  payloadDoc["ip_address"] = WiFi.localIP().toString();

  String payload;
  serializeJson(payloadDoc, payload);

  String responseBody;
  int httpCode = 0;

  if (!sendPost("/attendance/scan", payload, responseBody, httpCode))
  {
    feedback.code = "network_error";
    feedback.message = "Gagal menghubungi API";
    return feedback;
  }

  DynamicJsonDocument responseDoc(1536);
  DeserializationError error = deserializeJson(responseDoc, responseBody);

  if (error)
  {
    feedback.code = "invalid_json";
    feedback.message = "Respons server tidak valid";
    Serial.printf("[SCAN] Invalid JSON: %s\n", error.c_str());
    return feedback;
  }

  feedback.ok = responseDoc["ok"] | false;
  feedback.result = (const char *)(responseDoc["result"] | "error");
  feedback.code = (const char *)(responseDoc["code"] | "unknown");
  feedback.message = (const char *)(responseDoc["message"] | "Tanpa pesan");
  feedback.studentName = (const char *)(responseDoc["student"]["name"] | "");
  feedback.classroom = (const char *)(responseDoc["student"]["classroom"] | "");
  feedback.status = (const char *)(responseDoc["status"] | "");

  Serial.printf(
      "[SCAN] http=%d result=%s code=%s message=%s student=%s status=%s\n",
      httpCode,
      feedback.result.c_str(),
      feedback.code.c_str(),
      feedback.message.c_str(),
      feedback.studentName.c_str(),
      feedback.status.c_str());

  return feedback;
}

ScanFeedback sendEnrollmentScan(const String &uid)
{
  ScanFeedback feedback;

  StaticJsonDocument<256> payloadDoc;
  payloadDoc["uid"] = uid;
  payloadDoc["firmware_version"] = FIRMWARE_VERSION;
  payloadDoc["wifi_rssi"] = WiFi.RSSI();
  payloadDoc["free_heap"] = ESP.getFreeHeap();
  payloadDoc["reader_uptime_ms"] = millis();
  payloadDoc["ip_address"] = WiFi.localIP().toString();

  String payload;
  serializeJson(payloadDoc, payload);

  String responseBody;
  int httpCode = 0;

  if (!sendPost("/card-enrollment/scan", payload, responseBody, httpCode))
  {
    feedback.code = "network_error";
    feedback.message = "Gagal menghubungi API registrasi kartu";
    return feedback;
  }

  DynamicJsonDocument responseDoc(1536);
  DeserializationError error = deserializeJson(responseDoc, responseBody);

  if (error)
  {
    feedback.code = "invalid_json";
    feedback.message = "Respons server registrasi tidak valid";
    Serial.printf("[ENROLL] Invalid JSON: %s\n", error.c_str());
    return feedback;
  }

  feedback.ok = responseDoc["ok"] | false;
  feedback.result = (const char *)(responseDoc["result"] | "error");
  feedback.code = (const char *)(responseDoc["code"] | "unknown");
  feedback.message = (const char *)(responseDoc["message"] | "Tanpa pesan");
  feedback.studentName = (const char *)(responseDoc["user"]["name"] | "");

  Serial.printf(
      "[ENROLL] http=%d result=%s code=%s message=%s user=%s\n",
      httpCode,
      feedback.result.c_str(),
      feedback.code.c_str(),
      feedback.message.c_str(),
      feedback.studentName.c_str());

  return feedback;
}

bool sendGet(const String &path, String &responseBody, int &httpCode)
{
  return performHttpRequest("GET", path, "", responseBody, httpCode);
}

bool sendPost(const String &path, const String &payload, String &responseBody, int &httpCode)
{
  return performHttpRequest("POST", path, payload, responseBody, httpCode);
}

bool performHttpRequest(
    const String &method,
    const String &path,
    const String &payload,
    String &responseBody,
    int &httpCode)
{
  String url = String(API_BASE_URL) + path;

  for (uint8_t attempt = 1; attempt <= HTTP_MAX_RETRIES; attempt++)
  {
    if (WiFi.status() != WL_CONNECTED)
    {
      return false;
    }

    HTTPClient http;
    http.setTimeout(HTTP_TIMEOUT_MS);
    http.useHTTP10(true);

    if (!http.begin(networkClient, url))
    {
      Serial.printf("[HTTP] %s %s failed on attempt %u: unable to begin request\n",
                    method.c_str(),
                    url.c_str(),
                    attempt);
      delay(250 * attempt);
      yield();
      continue;
    }

    http.addHeader("Accept", "application/json");
    http.addHeader("X-Device-Token", normalizedDeviceToken());
    http.addHeader("X-Request-Id", buildRequestId());
    if (method == "POST")
    {
      http.addHeader("Content-Type", "application/json");
      httpCode = http.POST(payload);
    }
    else
    {
      httpCode = http.GET();
    }

    if (httpCode > 0)
    {
      responseBody = http.getString();
      http.end();
      return true;
    }

    Serial.printf("[HTTP] %s %s failed on attempt %u: %s\n",
                  method.c_str(),
                  url.c_str(),
                  attempt,
                  http.errorToString(httpCode).c_str());

    http.end();
    delay(250 * attempt);
    yield();
  }

  return false;
}

String readUid(const MFRC522::Uid &uid)
{
  String result;
  result.reserve(uid.size * 2);

  for (byte i = 0; i < uid.size; i++)
  {
    if (uid.uidByte[i] < 0x10)
    {
      result += '0';
    }

    result += String(uid.uidByte[i], HEX);
  }

  result.toUpperCase();
  return result;
}

bool isLocalDuplicate(const String &uid)
{
  if (uid != lastScannedUid)
  {
    return false;
  }

  return millis() - lastScannedAt < LOCAL_SCAN_COOLDOWN_MS;
}

void rememberScan(const String &uid)
{
  lastScannedUid = uid;
  lastScannedAt = millis();
}

String buildRequestId()
{
  String requestId = "esp8266-";
  requestId += String(ESP.getChipId(), HEX);
  requestId += '-';
  requestId += String(millis());
  return requestId;
}

String normalizedDeviceToken()
{
  String token = DEVICE_TOKEN;
  token.trim();
  return token;
}

bool isSuccessfulHttpStatus(int httpCode)
{
  return httpCode >= 200 && httpCode < 300;
}

void logUnexpectedHttpResponse(const char *scope, int httpCode, const String &responseBody)
{
  Serial.printf("[%s] HTTP %d\n", scope, httpCode);

  if (responseBody.length() == 0)
  {
    return;
  }

  Serial.printf("[%s] Body: %s\n", scope, responseBody.c_str());
}

void showScanFeedback(const ScanFeedback &feedback)
{
  String line = "[RESULT] " + feedback.message;

  if (feedback.studentName.length() > 0)
  {
    line += " | ";
    line += feedback.studentName;

    if (feedback.classroom.length() > 0)
    {
      line += " | ";
      line += feedback.classroom;
    }
  }

  if (feedback.status.length() > 0)
  {
    line += " | status=";
    line += feedback.status;
  }

  Serial.println(line);

  if (feedback.result == "success")
  {
    setLed(PIN_LED_GREEN, true);
    shortBeep(120);
    delay(120);
    setLed(PIN_LED_GREEN, false);
    return;
  }

  if (feedback.result == "warning")
  {
    signalWarning(feedback.message);
    return;
  }

  signalError(feedback.message);
}

void signalWarning(const String &message)
{
  Serial.printf("[WARN] %s\n", message.c_str());
  setLed(PIN_LED_WHITE, true);
  shortBeep(60);
  delay(80);
  shortBeep(60);
  delay(120);
  setLed(PIN_LED_WHITE, false);
}

void signalError(const String &message)
{
  Serial.printf("[ERROR] %s\n", message.c_str());
  setLed(PIN_LED_RED, true);
  longBeep(220);
  delay(120);
  setLed(PIN_LED_RED, false);
}

void bootSequence()
{
  setLed(PIN_LED_WHITE, true);
  delay(80);
  setLed(PIN_LED_WHITE, false);
  setLed(PIN_LED_GREEN, true);
  delay(80);
  setLed(PIN_LED_GREEN, false);
  setLed(PIN_LED_RED, true);
  delay(80);
  setLed(PIN_LED_RED, false);
}

void shortBeep(uint16_t durationMs)
{
  setBuzzer(true);
  delay(durationMs);
  setBuzzer(false);
}

void longBeep(uint16_t durationMs)
{
  setBuzzer(true);
  delay(durationMs);
  setBuzzer(false);
}

void setLed(uint8_t pin, bool on)
{
  digitalWrite(pin, normalizeLevel(on, LED_ACTIVE_HIGH));
}

void setBuzzer(bool on)
{
  digitalWrite(PIN_BUZZER, normalizeLevel(on, BUZZER_ACTIVE_HIGH));
}

void allIndicatorsOff()
{
  setLed(PIN_LED_WHITE, false);
  setLed(PIN_LED_GREEN, false);
  setLed(PIN_LED_RED, false);
  setBuzzer(false);
}

uint8_t normalizeLevel(bool on, bool activeHigh)
{
  if (activeHigh)
  {
    return on ? HIGH : LOW;
  }

  return on ? LOW : HIGH;
}
