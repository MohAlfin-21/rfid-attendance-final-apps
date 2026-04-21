<?php

namespace Database\Seeders;

use App\Domain\Absence\Enums\AbsenceRequestStatus;
use App\Domain\Absence\Enums\AbsenceRequestType;
use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Domain\Attendance\Enums\CheckMethod;
use App\Domain\Attendance\Enums\CheckOutType;
use App\Models\AbsenceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Device;
use App\Models\StudentFlag;
use App\Models\StudentProfile;
use App\Models\StudentStreak;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * PresentasiSeeder — Data dummy realistis untuk presentasi.
 *
 * Mengisi data 30 hari kebelakang untuk 35 siswa XI-RPL 1:
 * - Absensi harian (hadir, terlambat, izin, sakit, alpa)
 * - Surat izin (approved, rejected, pending)
 * - Student profiles (parent_phone, parent_name)
 * - Streak gamifikasi
 * - Anomaly flags contoh
 *
 * Run: php artisan db:seed --class=PresentasiSeeder
 */
class PresentasiSeeder extends Seeder
{
    // ──────────────────────────────────────────────
    // Parent phone data for WA demo (random Indonesian numbers)
    // ──────────────────────────────────────────────
    private array $parentData = [
        'Achmad Binu Caputra Fikri'           => ['Bapak Caputra Fikri', '0812-3456-7801'],
        'Achmad Saifudin'                      => ['Ibu Saifudin', '0813-4567-8912'],
        'Ahmad Rosyid Alfualdi'                => ['Bapak Alfualdi', '0814-5678-9023'],
        'Aisyafa Febriana Dewi'               => ['Ibu Febriana', '0815-6789-0134'],
        'Ajeng Natasya Nurlailly Istiqomah'   => ['Bapak Istiqomah', '0816-7890-1245'],
        'Akbar Aminurokhim'                    => ['Ibu Aminurokhim', '0817-8901-2356'],
        'Akmal Fadhlu Rahman Sasmito'         => ['Bapak Sasmito', '0818-9012-3467'],
        'Al Mira Cahaya Suci Tungga Dewi'     => ['Ibu Tungga', '0819-0123-4578'],
        'Aleifa Nofita Damayanti'              => ['Bapak Damayanti', '0821-1234-5689'],
        'Alindya Prajwalita'                   => ['Ibu Prajwalita', '0822-2345-6790'],
        'Amelia Nur Firzatullah'               => ['Bapak Firzatullah', '0823-3456-7801'],
        'Andhika Wahyu Sugiarto'              => ['Ibu Sugiarto', '0824-4567-8912'],
        'Arfansyah Adi Saputra'               => ['Bapak Saputra', '0825-5678-9023'],
        'Arina Karimatilail'                   => ['Ibu Karimatilail', '0826-6789-0134'],
        'Audy Muzaki'                          => ['Bapak Muzaki', '0827-7890-1245'],
        'Aujasena Risang Tauladani'           => ['Ibu Tauladani', '0828-8901-2356'],
        'Devi Zalfah Andiyanah'               => ['Bapak Andiyanah', '0829-9012-3467'],
        'Indra Syah Putra'                     => ['Ibu Syah', '0831-0123-4578'],
        'Iqbal Rizky Ramadhana'               => ['Bapak Ramadhana', '0832-1234-5689'],
        'Jesica Joansabrina Putri'             => ['Ibu Joansabrina', '0833-2345-6790'],
        'Kahfi Athohillah'                     => ['Bapak Athohillah', '0834-3456-7801'],
        'Kharis Fatur Rohman'                  => ['Ibu Rohman', '0835-4567-8912'],
        'Lailatul Rohmah'                      => ['Bapak Rohmah', '0836-5678-9023'],
        'Mohammad Alfin Dwi Prayetno'         => ['Ibu Prayetno', '0837-6789-0134'],
        'Muhammad Ridho Maulidiyanto'         => ['Bapak Maulidiyanto', '0838-7890-1245'],
        'Mukhamad Fadil Agustiar'              => ['Ibu Agustiar', '0839-8901-2356'],
        'Nadila Sandra Dewi'                   => ['Bapak Sandra', '0841-9012-3467'],
        'Nazril Gilang Ramadhan'               => ['Ibu Gilang', '0842-0123-4578'],
        'Septivea Elisa Rahmadhani'            => ['Bapak Rahmadhani', '0843-1234-5689'],
        'Teguh Dwi Santoso'                    => ['Ibu Santoso', '0844-2345-6790'],
        'Ummi Nur Fadhilah'                    => ['Bapak Fadhilah', '0845-3456-7801'],
        'Veronicha Gresta Haryanti'            => ['Ibu Haryanti', '0846-4567-8912'],
        'Widyo Krisna Yana Yahya'              => ['Bapak Yahya', '0847-5678-9023'],
        'Wildan Achmad Mubarok'               => ['Ibu Mubarok', '0848-6789-0134'],
        'Wisanggeni Cahya Manggalar'          => ['Bapak Manggalar', '0849-7890-1245'],
    ];

    // ──────────────────────────────────────────────
    // Siswa dengan pola khusus (untuk demo anomali & fitur)
    // ──────────────────────────────────────────────

    /** Siswa yang sering terlambat (demo anomaly #1) */
    private array $seringTerlambat = [
        'Audy Muzaki',
        'Indra Syah Putra',
        'Arfansyah Adi Saputra',
    ];

    /** Siswa yang akan punya streak tinggi (demo leaderboard) */
    private array $streakTinggi = [
        'Mohammad Alfin Dwi Prayetno', // sekretaris
        'Aisyafa Febriana Dewi',
        'Amelia Nur Firzatullah',
        'Alindya Prajwalita',
        'Lailatul Rohmah',
    ];

    /** Siswa yang akan punya beberapa alpa (demo consecutive absent) */
    private array $seringAlpa = [
        'Wisanggeni Cahya Manggalar',
        'Nazril Gilang Ramadhan',
    ];

    /** Siswa yang akan punya surat izin approved (demo observer) */
    private array $punyaIzin = [
        'Aleifa Nofita Damayanti',   // izin keluarga 2 hari
        'Iqbal Rizky Ramadhana',     // sakit 3 hari
        'Ummi Nur Fadhilah',          // izin 1 hari
        'Septivea Elisa Rahmadhani', // pending (belum diapprove)
    ];

    public function run(): void
    {
        $this->command?->info('🌱 Memulai PresentasiSeeder...');

        $classroom = Classroom::where('code', 'XI-RPL1')->first();
        $device = Device::where('code', 'READER-01')->first();
        $admin = User::where('username', 'admin')->first();

        if (! $classroom || ! $device || ! $admin) {
            $this->command?->error('Data dasar tidak ditemukan! Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $students = User::role('student')
            ->active()
            ->whereHas('classrooms', fn ($q) => $q->where('classrooms.id', $classroom->id))
            ->get();

        if ($students->isEmpty()) {
            $this->command?->error('35 siswa tidak ditemukan di kelas XI-RPL 1!');
            return;
        }

        $this->command?->info("  → {$students->count()} siswa ditemukan.");

        // ── 1. Buat student profiles (untuk demo WA notifikasi) ─────────────
        $this->seedStudentProfiles($students);

        // ── 2. Buat data absensi 30 hari ────────────────────────────────────
        $this->seedAttendances($students, $classroom, $device);

        // ── 3. Buat surat izin (beberapa approved, beberapa pending) ──────────
        $this->seedAbsenceRequests($students, $admin);

        // ── 4. Hitung ulang streaks berdasarkan data absensi ─────────────────
        $this->seedStreaks($students);

        // ── 5. Buat beberapa anomaly flags contoh ──────────────────────────
        $this->seedAnomalyFlags($students);

        $this->command?->info('✅ PresentasiSeeder selesai!');
        $this->command?->table(
            ['Tabel', 'Jumlah'],
            [
                ['student_profiles', StudentProfile::count()],
                ['attendances', Attendance::count()],
                ['absence_requests', AbsenceRequest::count()],
                ['student_streaks', StudentStreak::count()],
                ['student_flags', StudentFlag::count()],
            ]
        );
    }

    // ──────────────────────────────────────────────
    // Private seeders
    // ──────────────────────────────────────────────

    private function seedStudentProfiles($students): void
    {
        $this->command?->info('  Seeding student profiles...');

        foreach ($students as $student) {
            $data = $this->parentData[$student->name] ?? ['Orang Tua ' . $student->name, '0812-0000-0000'];

            StudentProfile::updateOrCreate(
                ['user_id' => $student->id],
                [
                    'parent_name'  => $data[0],
                    'parent_phone' => str_replace('-', '', $data[1]),
                ]
            );
        }
    }

    private function seedAttendances($students, $classroom, $device): void
    {
        $this->command?->info('  Seeding 30 hari absensi...');

        // Hapus absensi lama agar tidak duplikat
        $studentIds = $students->pluck('id');
        Attendance::whereIn('user_id', $studentIds)->delete();

        $startDate = Carbon::today()->subDays(29);
        $endDate   = Carbon::today();
        $period    = CarbonPeriod::create($startDate, $endDate);

        // Check-in windows
        $checkInStart  = '06:30';
        $lateThreshold = '07:10';
        $checkOutStart = '13:55';
        $checkOutEnd   = '14:15';

        foreach ($period as $date) {
            // Skip weekend
            if (in_array($date->dayOfWeek, [0, 6])) {
                continue;
            }

            $dateStr = $date->toDateString();

            foreach ($students as $student) {
                $status = $this->resolveStatusForDay($student->name, $date);

                if ($status === null) {
                    continue; // Tidak masuk tanpa keterangan (alpa murni — tidak ada record)
                }

                $record = [
                    'user_id'      => $student->id,
                    'classroom_id' => $classroom->id,
                    'date'         => $dateStr,
                    'status'       => $status->value,
                ];

                if (in_array($status->value, ['present', 'late'])) {
                    // Waktu check-in
                    $isLate = ($status->value === 'late');
                    if ($isLate) {
                        // Terlambat: 07:11 - 08:00
                        $checkInTime = $date->copy()->setTimeFromTimeString('07:11')
                            ->addMinutes(random_int(0, 49));
                    } else {
                        // Tepat waktu: 06:30 - 07:09
                        $checkInTime = $date->copy()->setTimeFromTimeString('06:30')
                            ->addMinutes(random_int(0, 39));
                    }

                    // Waktu check-out: 13:55 - 14:20
                    $checkOutTime = $date->copy()->setTimeFromTimeString('13:55')
                        ->addMinutes(random_int(0, 25));

                    // Khusus: beberapa siswa fast-checkout (untuk demo anomali titip kartu)
                    if ($status->value === 'present' && $this->isFastCheckoutDay($student->name, $date)) {
                        $checkOutTime = $checkInTime->copy()->addMinutes(random_int(30, 80));
                    }

                    $record['check_in_at']         = $checkInTime->toDateTimeString();
                    $record['check_in_method']      = CheckMethod::Rfid->value;
                    $record['check_in_device_id']   = $device->id;
                    $record['check_out_at']         = $checkOutTime->toDateTimeString();
                    $record['check_out_method']     = CheckMethod::Rfid->value;
                    $record['check_out_device_id']  = $device->id;
                    $record['check_out_type']       = CheckOutType::Normal->value;
                } elseif (in_array($status->value, ['excused', 'sick'])) {
                    // Izin/sakit tidak ada check-in — akan diisi observer saat surat disetujui
                    // Tapi untuk data existing kita isi langsung (override)
                    $record['override_note'] = 'Data demo presentasi';
                }

                Attendance::updateOrCreate(
                    ['user_id' => $student->id, 'date' => $dateStr],
                    $record
                );
            }
        }
    }

    private function seedAbsenceRequests($students, $admin): void
    {
        $this->command?->info('  Seeding surat izin...');

        // Hapus surat izin lama
        $studentIds = $students->pluck('id');
        AbsenceRequest::whereIn('user_id', $studentIds)->delete();

        $sampleRequests = [
            // Approved - izin keluarga
            [
                'name'       => 'Aleifa Nofita Damayanti',
                'type'       => AbsenceRequestType::Permission,
                'status'     => AbsenceRequestStatus::Approved,
                'date_start' => Carbon::today()->subDays(10)->toDateString(),
                'date_end'   => Carbon::today()->subDays(9)->toDateString(),
                'reason'     => 'Menghadiri acara pernikahan saudara di luar kota.',
                'review_note'=> 'Disetujui. Harap membawa surat dari orang tua.',
            ],
            // Approved - sakit
            [
                'name'       => 'Iqbal Rizky Ramadhana',
                'type'       => AbsenceRequestType::Sick,
                'status'     => AbsenceRequestStatus::Approved,
                'date_start' => Carbon::today()->subDays(7)->toDateString(),
                'date_end'   => Carbon::today()->subDays(5)->toDateString(),
                'reason'     => 'Demam tinggi dan dirawat di rumah sakit.',
                'review_note'=> 'Disetujui. Semoga cepat sembuh.',
            ],
            // Approved - izin 1 hari
            [
                'name'       => 'Ummi Nur Fadhilah',
                'type'       => AbsenceRequestType::Permission,
                'status'     => AbsenceRequestStatus::Approved,
                'date_start' => Carbon::today()->subDays(14)->toDateString(),
                'date_end'   => Carbon::today()->subDays(14)->toDateString(),
                'reason'     => 'Mengurus administrasi kepindahan tempat tinggal.',
                'review_note'=> 'Disetujui.',
            ],
            // Pending - menunggu review (untuk demo fitur pending)
            [
                'name'       => 'Septivea Elisa Rahmadhani',
                'type'       => AbsenceRequestType::Sick,
                'status'     => AbsenceRequestStatus::Pending,
                'date_start' => Carbon::today()->toDateString(),
                'date_end'   => Carbon::today()->addDay()->toDateString(),
                'reason'     => 'Batuk pilek dan kepala pusing sejak kemarin malam.',
                'review_note'=> null,
            ],
            // Pending - izin lomba
            [
                'name'       => 'Mohammad Alfin Dwi Prayetno',
                'type'       => AbsenceRequestType::Other,
                'status'     => AbsenceRequestStatus::Pending,
                'date_start' => Carbon::today()->addDays(2)->toDateString(),
                'date_end'   => Carbon::today()->addDays(3)->toDateString(),
                'reason'     => 'Mengikuti lomba LKS tingkat Kabupaten bidang IT.',
                'review_note'=> null,
            ],
            // Rejected
            [
                'name'       => 'Nazril Gilang Ramadhan',
                'type'       => AbsenceRequestType::Permission,
                'status'     => AbsenceRequestStatus::Rejected,
                'date_start' => Carbon::today()->subDays(5)->toDateString(),
                'date_end'   => Carbon::today()->subDays(5)->toDateString(),
                'reason'     => 'Ada keperluan mendadak di rumah.',
                'review_note'=> 'Ditolak. Alasan tidak cukup kuat. Harap hadir.',
            ],
            // Approved - extra (untuk demo auto-sync observer)
            [
                'name'       => 'Widyo Krisna Yana Yahya',
                'type'       => AbsenceRequestType::Sick,
                'status'     => AbsenceRequestStatus::Approved,
                'date_start' => Carbon::today()->subDays(3)->toDateString(),
                'date_end'   => Carbon::today()->subDays(3)->toDateString(),
                'reason'     => 'Sakit maag kambuh.',
                'review_note'=> 'Disetujui.',
            ],
            // Approved - extra
            [
                'name'       => 'Wildan Achmad Mubarok',
                'type'       => AbsenceRequestType::Permission,
                'status'     => AbsenceRequestStatus::Approved,
                'date_start' => Carbon::today()->subDays(20)->toDateString(),
                'date_end'   => Carbon::today()->subDays(19)->toDateString(),
                'reason'     => 'Membantu persiapan acara pernikahan kakak.',
                'review_note'=> 'Disetujui.',
            ],
        ];

        foreach ($sampleRequests as $req) {
            $student = $students->firstWhere('name', $req['name']);
            if (! $student) {
                continue;
            }

            $isReviewed = in_array($req['status'], [
                AbsenceRequestStatus::Approved,
                AbsenceRequestStatus::Rejected,
            ]);

            AbsenceRequest::create([
                'user_id'     => $student->id,
                'type'        => $req['type']->value,
                'status'      => $req['status']->value,
                'date_start'  => $req['date_start'],
                'date_end'    => $req['date_end'],
                'reason'      => $req['reason'],
                'reviewed_by' => $isReviewed ? $admin->id : null,
                'reviewed_at' => $isReviewed ? Carbon::now()->subDays(random_int(1, 3)) : null,
                'review_note' => $req['review_note'],
            ]);
        }
    }

    private function seedStreaks($students): void
    {
        $this->command?->info('  Seeding streaks & poin...');

        StudentStreak::whereIn('user_id', $students->pluck('id'))->delete();

        foreach ($students as $student) {
            // Hitung streak nyata dari data absensi yang sudah dibuat
            $attendances = Attendance::where('user_id', $student->id)
                ->orderByDesc('date')
                ->get();

            $currentStreak = 0;
            $longestStreak = 0;
            $totalPoints   = 0;
            $tempStreak    = 0;

            // Hitung streak dari terbaru ke terlama
            foreach ($attendances as $att) {
                $dayOfWeek = Carbon::parse($att->date)->dayOfWeek;
                if (in_array($dayOfWeek, [0, 6])) {
                    continue;
                }

                if (in_array($att->status->value, ['present', 'late'])) {
                    $tempStreak++;
                    $totalPoints += ($att->status->value === 'present') ? 10 : 5;
                    if ($tempStreak > $longestStreak) {
                        $longestStreak = $tempStreak;
                    }
                } elseif ($att->status->value === 'absent') {
                    if ($currentStreak === 0) {
                        $currentStreak = $tempStreak; // freeze on first break from recent
                    }
                    $tempStreak = 0;
                }
                // excused/sick = freeze, tidak reset
            }

            if ($currentStreak === 0) {
                $currentStreak = $tempStreak;
            }

            // Override untuk siswa streak tinggi: beri boost supaya terlihat di leaderboard
            if (in_array($student->name, $this->streakTinggi)) {
                $currentStreak = max($currentStreak, random_int(18, 28));
                $longestStreak = max($longestStreak, $currentStreak);
                $totalPoints   = max($totalPoints, $currentStreak * 10);
            }

            StudentStreak::create([
                'user_id'          => $student->id,
                'current_streak'   => $currentStreak,
                'longest_streak'   => $longestStreak,
                'total_points'     => $totalPoints,
                'last_streak_date' => Carbon::today()->subDays(in_array($student->name, $this->seringAlpa) ? 2 : 0)->toDateString(),
            ]);
        }
    }

    private function seedAnomalyFlags($students): void
    {
        $this->command?->info('  Seeding anomaly flags...');

        StudentFlag::whereIn('user_id', $students->pluck('id'))->delete();

        // Flag 1: Late pattern - sering terlambat hari Senin
        foreach ($this->seringTerlambat as $name) {
            $student = $students->firstWhere('name', $name);
            if (! $student) continue;

            StudentFlag::create([
                'user_id'     => $student->id,
                'flag_type'   => StudentFlag::TYPE_LATE_PATTERN,
                'details'     => [
                    'day_of_week' => 'Senin',
                    'count'       => random_int(3, 5),
                    'dates'       => [
                        Carbon::today()->previous(Carbon::MONDAY)->toDateString(),
                        Carbon::today()->previous(Carbon::MONDAY)->subWeek()->toDateString(),
                        Carbon::today()->previous(Carbon::MONDAY)->subWeeks(2)->toDateString(),
                    ],
                ],
                'flagged_date' => Carbon::today()->toDateString(),
            ]);
        }

        // Flag 2: Consecutive absent - 2 hari alpa berturut-turut
        foreach ($this->seringAlpa as $name) {
            $student = $students->firstWhere('name', $name);
            if (! $student) continue;

            $startAbsent = Carbon::today()->subDays(4)->toDateString();
            $endAbsent   = Carbon::today()->subDays(3)->toDateString();

            StudentFlag::create([
                'user_id'     => $student->id,
                'flag_type'   => StudentFlag::TYPE_CONSECUTIVE_ABSENT,
                'details'     => [
                    'streak_length' => 2,
                    'dates'         => [$startAbsent, $endAbsent],
                ],
                'flagged_date' => $endAbsent,
            ]);
        }

        // Flag 3: Fast checkout - diduga titip kartu
        $fastCheckoutStudents = ['Akbar Aminurokhim', 'Teguh Dwi Santoso'];
        foreach ($fastCheckoutStudents as $name) {
            $student = $students->firstWhere('name', $name);
            if (! $student) continue;

            StudentFlag::create([
                'user_id'     => $student->id,
                'flag_type'   => StudentFlag::TYPE_FAST_CHECKOUT,
                'details'     => [
                    'date'         => Carbon::today()->subDays(2)->toDateString(),
                    'check_in_at'  => '06:45:00',
                    'check_out_at' => '07:32:00',
                    'duration_min' => 47,
                ],
                'flagged_date' => Carbon::today()->subDays(2)->toDateString(),
            ]);
        }
    }

    // ──────────────────────────────────────────────
    // Helpers: logika distribusi status per siswa per hari
    // ──────────────────────────────────────────────

    /**
     * Tentukan status absensi seorang siswa pada hari tertentu.
     * Return null = alpa (tidak ada record di DB).
     */
    private function resolveStatusForDay(string $name, Carbon $date): ?AttendanceStatus
    {
        $dayOfWeek  = $date->dayOfWeek;    // 1=Mon...5=Fri
        $daysAgo    = Carbon::today()->diffInDays($date);

        // Siswa streak tinggi: hampir selalu hadir tepat waktu
        if (in_array($name, $this->streakTinggi)) {
            // 5% terlambat, 95% hadir
            return (random_int(1, 100) <= 5) ? AttendanceStatus::Late : AttendanceStatus::Present;
        }

        // Siswa sering terlambat: lebih sering late khususnya Senin
        if (in_array($name, $this->seringTerlambat)) {
            if ($dayOfWeek === 1) { // Senin
                // 70% terlambat di hari Senin
                return (random_int(1, 100) <= 70) ? AttendanceStatus::Late : AttendanceStatus::Present;
            }
            // Hari lain: 30% terlambat
            $roll = random_int(1, 100);
            if ($roll <= 30) return AttendanceStatus::Late;
            if ($roll <= 35) return null; // 5% alpa
            return AttendanceStatus::Present;
        }

        // Siswa sering alpa: alpa di beberapa hari tertentu
        if (in_array($name, $this->seringAlpa)) {
            if ($daysAgo >= 3 && $daysAgo <= 4) {
                return null; // Alpa 2 hari berturut-turut (untuk trigger flag consecutive_absent)
            }
            $roll = random_int(1, 100);
            if ($roll <= 15) return null;          // 15% alpa
            if ($roll <= 25) return AttendanceStatus::Late;
            return AttendanceStatus::Present;
        }

        // Siswa dengan izin: pada tanggal izin mereka sudah di-handle oleh Observer
        // Di sini kita skip tanggal-tanggal tertentu (akan diisi observer)
        $izinRanges = [
            'Aleifa Nofita Damayanti'   => [Carbon::today()->subDays(10), Carbon::today()->subDays(9)],
            'Iqbal Rizky Ramadhana'     => [Carbon::today()->subDays(7),  Carbon::today()->subDays(5)],
            'Ummi Nur Fadhilah'          => [Carbon::today()->subDays(14), Carbon::today()->subDays(14)],
            'Widyo Krisna Yana Yahya'   => [Carbon::today()->subDays(3),  Carbon::today()->subDays(3)],
            'Wildan Achmad Mubarok'     => [Carbon::today()->subDays(20), Carbon::today()->subDays(19)],
        ];

        if (isset($izinRanges[$name])) {
            [$start, $end] = $izinRanges[$name];
            if ($date->between($start, $end)) {
                // Izin resmi — sudah ada di absence_requests
                $isIzinType = in_array($name, ['Iqbal Rizky Ramadhana', 'Widyo Krisna Yana Yahya'])
                    ? AttendanceStatus::Sick
                    : AttendanceStatus::Excused;
                return $isIzinType;
            }
        }

        // Siswa normal: distribusi realistis
        // 78% hadir, 12% terlambat, 5% sakit, 3% izin, 2% alpa
        $roll = random_int(1, 100);
        if ($roll <= 78) return AttendanceStatus::Present;
        if ($roll <= 90) return AttendanceStatus::Late;
        if ($roll <= 95) return AttendanceStatus::Sick;
        if ($roll <= 98) return AttendanceStatus::Excused;
        return null; // 2% alpa
    }

    /**
     * True jika siswa ini akan punya fast-checkout pada hari ini (untuk demo anomali).
     */
    private function isFastCheckoutDay(string $name, Carbon $date): bool
    {
        $fastCheckoutStudents = ['Akbar Aminurokhim', 'Teguh Dwi Santoso'];
        if (! in_array($name, $fastCheckoutStudents)) {
            return false;
        }

        // Fast-checkout terjadi 2 hari yang lalu
        return Carbon::today()->diffInDays($date) === 2 && $date->dayOfWeek !== 0 && $date->dayOfWeek !== 6;
    }
}
