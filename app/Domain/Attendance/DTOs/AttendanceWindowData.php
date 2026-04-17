<?php

namespace App\Domain\Attendance\DTOs;

readonly class AttendanceWindowData
{
    public function __construct(
        public string $currentWindow,
        public bool $isLate,
        public string $timezone,
        public string $date,
    ) {}
}
