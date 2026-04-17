<?php

namespace App\Domain\Absence\Enums;

use App\Domain\Attendance\Enums\AttendanceStatus;

enum AbsenceRequestType: string
{
    case Permission = 'permission';
    case Sick = 'sick';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Permission => 'Izin',
            self::Sick => 'Sakit',
            self::Other => 'Lainnya',
        };
    }

    public function toAttendanceStatus(): AttendanceStatus
    {
        return match ($this) {
            self::Sick => AttendanceStatus::Sick,
            default => AttendanceStatus::Excused,
        };
    }
}
