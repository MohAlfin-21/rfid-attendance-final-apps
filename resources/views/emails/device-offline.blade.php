<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 24px; }
  .card { background: #fff; border-radius: 12px; max-width: 520px; margin: 0 auto; padding: 32px; border: 1px solid #e4e4e7; }
  .badge { display: inline-block; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 6px; font-size: 12px; font-weight: 600; padding: 4px 10px; letter-spacing: 0.5px; text-transform: uppercase; }
  h2 { font-size: 20px; color: #18181b; margin: 16px 0 8px; }
  p { font-size: 14px; color: #52525b; line-height: 1.6; margin: 0 0 16px; }
  .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
  .info-table td { font-size: 13px; padding: 8px 0; border-bottom: 1px solid #f4f4f5; color: #3f3f46; }
  .info-table td:first-child { font-weight: 500; color: #71717a; width: 120px; }
  .footer { font-size: 12px; color: #a1a1aa; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f4f4f5; }
</style></head>
<body>
<div class="card">
  <span class="badge">⚠ Device Offline</span>
  <h2>Perangkat Tidak Merespons</h2>
  <p>Perangkat RFID berikut tidak terdeteksi aktif dalam <strong>{{ $minutesOffline }} menit</strong> terakhir. Segera periksa koneksi jaringan atau daya perangkat.</p>
  <table class="info-table">
    <tr><td>Nama Perangkat</td><td><strong>{{ $device->name }}</strong></td></tr>
    <tr><td>Kode</td><td>{{ $device->code }}</td></tr>
    <tr><td>Lokasi</td><td>{{ $device->location ?? '—' }}</td></tr>
    <tr><td>Heartbeat Terakhir</td><td>{{ $device->last_heartbeat_at?->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') ?? 'Belum pernah' }} WIB</td></tr>
    <tr><td>Status Error</td><td>{{ $device->error_count > 0 ? "Ada ({$device->error_count} error)" : 'Tidak ada' }}</td></tr>
    <tr><td>Waktu Alert</td><td>{{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB</td></tr>
  </table>
  <p>Jika perangkat sudah kembali online, email ini dapat diabaikan. Alert berikutnya akan dikirim hanya jika perangkat masih offline pada pengecekan interval berikutnya.</p>
  <div class="footer">Email otomatis dari RFID Attendance System &mdash; Jangan balas email ini.</div>
</div>
</body>
</html>
