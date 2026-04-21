<?php

return [
    'boolean' => [
        'yes' => 'Ya',
        'no' => 'Tidak',
    ],
    'groups' => [
        'attendance' => 'Jam Absensi',
        'academic' => 'Periode Akademik',
        'device' => 'Perangkat RFID',
        'general' => 'Identitas Sekolah',
        'other' => 'Pengaturan Lainnya',
    ],
    'group_descriptions' => [
        'attendance' => 'Atur kapan siswa boleh tap kartu untuk masuk, kapan dianggap terlambat, dan kapan boleh check-out.',
        'academic' => 'Periode ini dipakai sistem untuk menentukan kelas aktif dan rekap semester berjalan.',
        'device' => 'Aturan ini memengaruhi kapan reader dianggap offline dan kapan scan ganda diabaikan.',
        'general' => 'Informasi ini tampil di antarmuka admin dan bisa dipakai di halaman publik atau laporan.',
        'other' => 'Pengaturan teknis tambahan yang belum dipetakan ke tampilan yang lebih ramah.',
    ],
    'summary' => [
        'eyebrow' => 'Ringkasan Cepat',
        'title' => 'Aturan Absensi Yang Sedang Aktif',
        'description' => 'Lihat ringkasan ini untuk memastikan aturan operasional sudah benar sebelum mengubah detail form di bawah.',
        'note' => 'Reader RFID akan mengikuti aturan ini saat sinkronisasi berikutnya.',
        'check_in' => 'Siswa bisa check-in',
        'late_after' => 'Terlambat setelah',
        'check_out' => 'Siswa bisa check-out',
        'timezone' => 'Zona waktu',
        'status_on_time' => 'Jika tap sebelum batas terlambat, siswa tercatat hadir tepat waktu.',
        'status_late' => 'Jika tap setelah batas terlambat tapi masih dalam jendela check-in, status menjadi terlambat.',
        'status_check_out' => 'Check-out hanya dihitung pada jam pulang yang Anda tetapkan di bawah.',
    ],
    'labels' => [
        'attendance' => [
            'timezone' => 'Zona waktu sekolah',
            'check_in_start' => 'Mulai check-in',
            'check_in_end' => 'Akhir check-in',
            'late_after' => 'Batas mulai terlambat',
            'check_out_start' => 'Mulai check-out',
            'check_out_end' => 'Akhir check-out',
            'academic_year' => 'Tahun ajaran aktif',
            'semester' => 'Semester aktif',
        ],
        'devices' => [
            'offline_threshold_seconds' => 'Batas perangkat dianggap offline',
            'duplicate_scan_cooldown_seconds' => 'Jeda abaikan scan kartu yang sama',
        ],
        'school' => [
            'name' => 'Nama sekolah',
            'address' => 'Alamat sekolah',
        ],
    ],
    'help' => [
        'attendance' => [
            'timezone' => 'Gunakan zona waktu lokal sekolah agar sistem membaca jam scan dengan benar.',
            'check_in_start' => 'Jam pertama siswa boleh tap kartu untuk absen masuk.',
            'check_in_end' => 'Setelah jam ini, scan masuk tidak lagi dihitung sebagai check-in.',
            'late_after' => 'Mulai dari jam ini, scan masuk tetap diterima tetapi statusnya terlambat.',
            'check_out_start' => 'Jam pertama siswa boleh tap kartu untuk absen pulang.',
            'check_out_end' => 'Setelah jam ini, scan pulang tidak lagi dihitung sebagai check-out.',
            'academic_year' => 'Contoh format yang disarankan: 2025/2026.',
            'semester' => 'Pilih semester yang sedang berjalan untuk kelas aktif saat ini.',
        ],
        'devices' => [
            'offline_threshold_seconds' => 'Jika reader tidak kirim heartbeat selama durasi ini, statusnya berubah menjadi offline.',
            'duplicate_scan_cooldown_seconds' => 'Scan kartu yang sama dalam rentang ini akan diabaikan agar tidak tercatat dobel.',
        ],
        'school' => [
            'name' => 'Nama ini ditampilkan di antarmuka sistem.',
            'address' => 'Alamat sekolah untuk tampilan informasi umum.',
        ],
    ],
    'cards' => [
        'check_in' => 'Jam Masuk',
        'check_out' => 'Jam Pulang',
        'lateness' => 'Aturan Terlambat',
        'device' => 'Perilaku Reader',
    ],
    'field_hint' => [
        'time_format' => 'Gunakan format 24 jam.',
        'seconds' => 'Satuan detik.',
    ],
    'semester' => [
        '1' => 'Semester 1',
        '2' => 'Semester 2',
    ],
    'actions' => [
        'save' => 'Simpan Pengaturan',
        'save_help' => 'Simpan setelah Anda selesai menyesuaikan aturan di setiap bagian.',
    ],
];
