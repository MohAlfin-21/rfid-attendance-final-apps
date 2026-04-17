<?php

namespace App\Domain\Attendance\DTOs;

readonly class AttendanceSummaryData
{
    public function __construct(
        public int $totalStudents,
        public int $presentCount,
        public int $lateCount,
        public int $absentCount,
        public int $excusedCount,
        public int $sickCount,
        public int $checkedOutCount,
    ) {}
}
