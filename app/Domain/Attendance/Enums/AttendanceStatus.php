<?php

namespace App\Domain\Attendance\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Excused = 'excused';
    case Sick = 'sick';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Hadir',
            self::Late => 'Terlambat',
            self::Excused => 'Izin',
            self::Sick => 'Sakit',
            self::Absent => 'Alpa',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Present => 'badge-success',
            self::Late => 'badge-warning',
            self::Excused => 'badge-info',
            self::Sick => 'badge-purple',
            self::Absent => 'badge-danger',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Present => '#16a34a',
            self::Late => '#d97706',
            self::Excused => '#2563eb',
            self::Sick => '#7c3aed',
            self::Absent => '#dc2626',
        };
    }

    public function isPresent(): bool
    {
        return in_array($this, [self::Present, self::Late], true);
    }
}
