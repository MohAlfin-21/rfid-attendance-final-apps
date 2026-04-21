<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Absensi — {{ $classroom }}</title>
<style>
  @media print { @page { size: A4 landscape; margin: 15mm; } .no-print { display: none !important; } }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #111; padding: 16px; }
  .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #111; padding-bottom: 10px; }
  .header h1 { font-size: 16px; font-weight: bold; }
  .header p { font-size: 11px; margin-top: 4px; color: #444; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  th { background: #18181b; color: #fff; font-size: 10px; padding: 6px 8px; text-align: left; }
  td { padding: 5px 8px; border-bottom: 1px solid #e4e4e7; font-size: 10px; }
  tr:nth-child(even) td { background: #f4f4f5; }
  .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
  .badge-present  { background: #dcfce7; color: #166534; }
  .badge-late     { background: #fef9c3; color: #854d0e; }
  .badge-excused  { background: #dbeafe; color: #1e40af; }
  .badge-sick     { background: #ede9fe; color: #5b21b6; }
  .badge-absent   { background: #fee2e2; color: #991b1b; }
  .summary { display: flex; gap: 16px; margin: 12px 0; flex-wrap: wrap; }
  .stat { background: #f4f4f5; border-radius: 6px; padding: 8px 16px; text-align: center; }
  .stat .num { font-size: 18px; font-weight: bold; }
  .stat .lbl { font-size: 9px; color: #71717a; }
  .print-btn { background: #18181b; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-size: 13px; margin-bottom: 16px; }
</style>
</head>
<body>
<button class="print-btn no-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>

<div class="header">
  <h1>REKAP ABSENSI SISWA</h1>
  <p>Kelas: <strong>{{ $classroom }}</strong> &nbsp;|&nbsp;
     Periode: <strong>{{ \Carbon\Carbon::parse($params['date_from'])->format('d M Y') }}</strong>
     s.d. <strong>{{ \Carbon\Carbon::parse($params['date_to'])->format('d M Y') }}</strong>
  </p>
  <p>Dicetak: {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
</div>

@php
  $counts = [
    'Hadir'    => $attendances->where('status.value', 'present')->count(),
    'Terlambat'=> $attendances->where('status.value', 'late')->count(),
    'Izin'     => $attendances->where('status.value', 'excused')->count(),
    'Sakit'    => $attendances->where('status.value', 'sick')->count(),
    'Alpa'     => $attendances->where('status.value', 'absent')->count(),
  ];
  $statusMap = [
    'present' => 'present', 'late' => 'late',
    'excused' => 'excused', 'sick' => 'sick', 'absent' => 'absent',
  ];
@endphp

<div class="summary">
  @foreach($counts as $lbl => $num)
    <div class="stat"><div class="num">{{ $num }}</div><div class="lbl">{{ $lbl }}</div></div>
  @endforeach
  <div class="stat"><div class="num">{{ $attendances->count() }}</div><div class="lbl">Total Records</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>No</th><th>Tanggal</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th>
      <th>Status</th><th>Check-in</th><th>Check-out</th><th>Metode</th><th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    @forelse($attendances as $i => $a)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $a->date->format('d/m/Y') }}</td>
      <td>{{ $a->user->nis ?? '—' }}</td>
      <td>{{ $a->user->name ?? '—' }}</td>
      <td>{{ $a->classroom->name ?? '—' }}</td>
      <td>
        <span class="badge badge-{{ $a->status->value }}">
          {{ $a->status->label() }}
        </span>
      </td>
      <td>{{ $a->check_in_at?->setTimezone('Asia/Jakarta')->format('H:i') ?? '—' }}</td>
      <td>{{ $a->check_out_at?->setTimezone('Asia/Jakarta')->format('H:i') ?? '—' }}</td>
      <td>{{ $a->check_in_method?->value ?? '—' }}</td>
      <td>{{ $a->override_note ?? '' }}</td>
    </tr>
    @empty
    <tr><td colspan="10" style="text-align:center;padding:20px;color:#71717a;">Tidak ada data absensi untuk periode ini.</td></tr>
    @endforelse
  </tbody>
</table>
</body>
</html>
