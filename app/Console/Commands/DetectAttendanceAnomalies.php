<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\StudentFlag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class DetectAttendanceAnomalies extends Command
{
    protected $signature   = 'attendance:detect-anomalies
                              {--months=1 : Number of months to look back}';

    protected $description = 'Detect attendance anomaly patterns and insert flags for admin review.';

    // Threshold: minimum 90 minutes between check-in and check-out.
    // Using 90 min to reduce false positives for legit early departures/events.
    private const FAST_CHECKOUT_MINUTES = 90;

    public function handle(): int
    {
        $monthsBack = (int) $this->option('months');
        $since      = Carbon::now()->subMonths($monthsBack)->startOfMonth()->toDateString();
        $today      = Carbon::today()->toDateString();

        $this->info("Detecting anomalies from {$since} to {$today}...");

        $students = User::role('student')->active()->get();

        $flagged = 0;
        foreach ($students as $student) {
            $attendances = Attendance::where('user_id', $student->id)
                ->whereBetween('date', [$since, $today])
                ->get();

            $flagged += $this->detectLatePattern($student, $attendances, $today);
            $flagged += $this->detectConsecutiveAbsent($student, $attendances);
            $flagged += $this->detectFastCheckout($student, $attendances, $today);
        }

        $this->info("Done. Total new flags inserted: {$flagged}.");
        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    // Detection handlers
    // ──────────────────────────────────────────────

    /**
     * Pattern 1: Late 3+ times on the same weekday in the current period.
     * E.g. late every Monday → suspicious pattern.
     */
    private function detectLatePattern(User $student, Collection $attendances, string $today): int
    {
        $lateRecords = $attendances->where('status', AttendanceStatus::Late);
        $byDayOfWeek = $lateRecords->groupBy(fn ($a) => $a->date->dayOfWeek);

        $flagged = 0;
        foreach ($byDayOfWeek as $dayOfWeek => $records) {
            if ($records->count() < 3) {
                continue;
            }

            $days    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $dayName = $days[$dayOfWeek] ?? "Hari-{$dayOfWeek}";
            $existed = StudentFlag::where('user_id', $student->id)
                ->where('flag_type', StudentFlag::TYPE_LATE_PATTERN)
                ->whereNull('resolved_at')
                ->whereDate('flagged_date', $today)
                ->exists();

            if ($existed) {
                continue;
            }

            StudentFlag::create([
                'user_id'     => $student->id,
                'flag_type'   => StudentFlag::TYPE_LATE_PATTERN,
                'details'     => [
                    'day_of_week' => $dayName,
                    'count'       => $records->count(),
                    'dates'       => $records->pluck('date')->map->toDateString()->values()->toArray(),
                ],
                'flagged_date' => $today,
            ]);
            $flagged++;
        }

        return $flagged;
    }

    /**
     * Pattern 2: Absent (without permission) 2+ consecutive days.
     */
    private function detectConsecutiveAbsent(User $student, Collection $attendances): int
    {
        $absentDates = $attendances
            ->where('status', AttendanceStatus::Absent)
            ->pluck('date')
            ->sort()
            ->values();

        if ($absentDates->count() < 2) {
            return 0;
        }

        $flagged = 0;
        $streak  = [$absentDates->first()];

        for ($i = 1; $i < $absentDates->count(); $i++) {
            $prev = $absentDates[$i - 1];
            $curr = $absentDates[$i];

            if ($prev->diffInDays($curr) === 1) {
                $streak[] = $curr;
            } else {
                if (count($streak) >= 2) {
                    $flagged += $this->insertConsecutiveAbsentFlag($student, $streak);
                }
                $streak = [$curr];
            }
        }

        // Check remaining streak
        if (count($streak) >= 2) {
            $flagged += $this->insertConsecutiveAbsentFlag($student, $streak);
        }

        return $flagged;
    }

    private function insertConsecutiveAbsentFlag(User $student, array $streak): int
    {
        $lastDate = end($streak)->toDateString();

        $existed = StudentFlag::where('user_id', $student->id)
            ->where('flag_type', StudentFlag::TYPE_CONSECUTIVE_ABSENT)
            ->whereNull('resolved_at')
            ->whereDate('flagged_date', $lastDate)
            ->exists();

        if ($existed) {
            return 0;
        }

        StudentFlag::create([
            'user_id'     => $student->id,
            'flag_type'   => StudentFlag::TYPE_CONSECUTIVE_ABSENT,
            'details'     => [
                'streak_length' => count($streak),
                'dates'         => collect($streak)->map->toDateString()->values()->toArray(),
            ],
            'flagged_date' => $lastDate,
        ]);

        return 1;
    }

    /**
     * Pattern 3: Check-out happened less than 90 minutes after check-in.
     * Excludes days where an approved absence request exists (early departure excused).
     * 90-minute threshold is intentionally lenient to reduce false positives.
     */
    private function detectFastCheckout(User $student, Collection $attendances, string $today): int
    {
        $suspicious = $attendances->filter(function (Attendance $a) use ($student) {
            if (! $a->check_in_at || ! $a->check_out_at) {
                return false;
            }

            $minutes = $a->check_in_at->diffInMinutes($a->check_out_at);
            if ($minutes >= self::FAST_CHECKOUT_MINUTES) {
                return false;
            }

            // Exclude if there's an approved absence request for this date
            $hasApprovedAbsence = $student->absenceRequests()
                ->where('status', 'approved')
                ->where('date_start', '<=', $a->date->toDateString())
                ->where('date_end', '>=', $a->date->toDateString())
                ->exists();

            return ! $hasApprovedAbsence;
        });

        $flagged = 0;
        foreach ($suspicious as $attendance) {
            $dateStr = $attendance->date->toDateString();

            $existed = StudentFlag::where('user_id', $student->id)
                ->where('flag_type', StudentFlag::TYPE_FAST_CHECKOUT)
                ->whereNull('resolved_at')
                ->whereDate('flagged_date', $dateStr)
                ->exists();

            if ($existed) {
                continue;
            }

            StudentFlag::create([
                'user_id'     => $student->id,
                'flag_type'   => StudentFlag::TYPE_FAST_CHECKOUT,
                'details'     => [
                    'date'          => $dateStr,
                    'check_in_at'  => $attendance->check_in_at->toTimeString(),
                    'check_out_at' => $attendance->check_out_at->toTimeString(),
                    'duration_min' => $attendance->check_in_at->diffInMinutes($attendance->check_out_at),
                ],
                'flagged_date' => $dateStr,
            ]);
            $flagged++;
        }

        return $flagged;
    }
}
