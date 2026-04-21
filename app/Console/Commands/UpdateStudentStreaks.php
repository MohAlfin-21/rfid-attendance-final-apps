<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\StudentStreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateStudentStreaks extends Command
{
    protected $signature   = 'attendance:update-streaks
                              {--date= : Override date for testing (Y-m-d). Defaults to yesterday.}';

    protected $description = 'Update student streaks based on yesterday\'s attendance. Run daily at 09:00.';

    // Points per on-time day. Late = half points.
    private const POINTS_PRESENT = 10;
    private const POINTS_LATE    = 5;

    public function handle(): int
    {
        // Use yesterday so that the school day is fully complete by the time this runs.
        // Scheduled at 09:00 (safer than 08:00) — yesterday's data is definitive.
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::yesterday()->toDateString();

        // Skip weekends (Saturday=6, Sunday=0)
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        if (in_array($dayOfWeek, [0, 6])) {
            $this->info("Skipping streak update for weekend ({$date}).");
            return self::SUCCESS;
        }

        $this->info("Updating streaks for {$date}...");

        $students = User::role('student')->active()->get();

        $updated = 0;
        foreach ($students as $student) {
            $attendance = Attendance::where('user_id', $student->id)
                ->whereDate('date', $date)
                ->first();

            $streak = StudentStreak::firstOrCreate(
                ['user_id' => $student->id],
                ['current_streak' => 0, 'longest_streak' => 0, 'total_points' => 0]
            );

            if ($this->shouldIncrementStreak($attendance)) {
                $points = ($attendance->status === AttendanceStatus::Present)
                    ? self::POINTS_PRESENT
                    : self::POINTS_LATE;

                $streak->current_streak  += 1;
                $streak->total_points    += $points;
                $streak->last_streak_date = $date;

                if ($streak->current_streak > $streak->longest_streak) {
                    $streak->longest_streak = $streak->current_streak;
                }
            } elseif ($this->shouldBreakStreak($attendance)) {
                // Break streak only if absent WITHOUT excuse
                $streak->current_streak = 0;
            }
            // Excused/sick: neither increment nor break — freeze the streak

            $streak->save();
            $updated++;
        }

        $this->info("Done. Streaks updated for {$updated} students.");
        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function shouldIncrementStreak(?Attendance $attendance): bool
    {
        if (! $attendance) {
            return false;
        }
        return in_array($attendance->status, [AttendanceStatus::Present, AttendanceStatus::Late], true);
    }

    private function shouldBreakStreak(?Attendance $attendance): bool
    {
        if (! $attendance) {
            // No attendance record at all = unexcused absence
            return true;
        }
        // Only unexcused absences break the streak; excused/sick freeze it
        return $attendance->status === AttendanceStatus::Absent;
    }
}
