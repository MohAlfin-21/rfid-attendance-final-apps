<?php

return [
    'boolean' => [
        'yes' => 'Ya',
        'no' => 'Tidak',
    ],
    'groups' => [
        'attendance' => 'Jam Absensi',
        'academic' => 'Tahun Ajaran',
        'device' => 'Perangkat',
        'general' => 'Informasi Sekolah',
    ],
    'labels' => [
        'attendance' => [
            'timezone' => 'Zona waktu untuk perhitungan jendela absensi',
            'check_in_start' => 'Waktu mulai check-in (JJ:MM)',
            'check_in_end' => 'Waktu selesai check-in (JJ:MM)',
            'late_after' => 'Batas keterlambatan check-in (JJ:MM)',
            'check_out_start' => 'Waktu mulai check-out (JJ:MM)',
            'check_out_end' => 'Waktu selesai check-out (JJ:MM)',
            'academic_year' => 'Tahun ajaran aktif',
            'semester' => 'Semester aktif (1 atau 2)',
        ],
        'devices' => [
            'offline_threshold_seconds' => 'Detik sebelum perangkat dianggap offline',
            'duplicate_scan_cooldown_seconds' => 'Detik untuk mengabaikan scan berulang dari kartu yang sama',
        ],
        'school' => [
            'name' => 'Nama sekolah yang tampil di antarmuka',
            'address' => 'Alamat sekolah',
        ],
    ],
];
