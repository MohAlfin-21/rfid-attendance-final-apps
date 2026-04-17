<?php

return [
    'status' => [
        'present' => 'Hadir',
        'late' => 'Terlambat',
        'excused' => 'Izin',
        'sick' => 'Sakit',
        'absent' => 'Alpha',
    ],
    'action' => [
        'check_in' => 'Check-in',
        'check_out' => 'Check-out',
        'manual_mark' => 'Tandai manual',
        'early_checkout' => 'Pulang cepat',
        'rejected' => 'Ditolak',
    ],
    'rule_hit' => [
        'card_not_registered' => 'Kartu tidak terdaftar.',
        'card_inactive' => 'Kartu tidak aktif.',
        'card_lost' => 'Kartu ditandai hilang.',
        'device_inactive' => 'Device tidak aktif.',
        'user_inactive' => 'User tidak aktif.',
        'no_classroom_membership' => 'Membership kelas aktif tidak ditemukan.',
        'membership_conflict' => 'Terdeteksi lebih dari satu membership kelas aktif.',
        'duplicate_scan_cooldown' => 'Scan berulang dalam masa cooldown.',
        'already_excused' => 'Attendance sudah ditandai izin/sakit.',
        'attendance_locked' => 'Absensi hari ini sudah dikunci.',
        'already_checked_in' => 'Siswa sudah check-in.',
        'checkout_without_checkin' => 'Tidak bisa check-out tanpa check-in.',
        'already_checked_out' => 'Siswa sudah check-out.',
        'check_in_ok' => 'Check-in berhasil.',
        'check_in_late' => 'Check-in terlambat berhasil.',
        'check_out_ok' => 'Check-out berhasil.',
        'outside_window' => 'Scan di luar window absensi.',
    ],
];
