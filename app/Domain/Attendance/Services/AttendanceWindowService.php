<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\DTOs\AttendanceWindowData;
use Carbon\CarbonImmutable;
use DateTimeInterface;

class AttendanceWindowService
{
    public function resolve(
        DateTimeInterface|string $timestamp,
        string $timezone,
        string $checkInStart,
        string $checkInEnd,
        ?string $lateAfter,
        string $checkOutStart,
        string $checkOutEnd,
    ): AttendanceWindowData {
        $current = CarbonImmutable::parse($timestamp, $timezone)->setTimezone($timezone);
        $date = $current->toDateString();

        if ($current->isWeekend()) {
            return new AttendanceWindowData(
                currentWindow: 'outside',
                isLate: false,
                timezone: $timezone,
                date: $date,
            );
        }

        // Business rules override configuration:
        $checkInStart = '05:30';
        $lateAfter = '06:50'; // 06:45 threshold + 5 mins tolerance
        $checkOutEnd = '17:00';

        if ($current->isMonday()) {
            $checkOutStart = '15:15';
        } elseif ($current->isFriday()) {
            $checkOutStart = '15:30';
        } else {
            $checkOutStart = '15:00'; // Tue, Wed, Thu
        }

        $checkInStartAt = CarbonImmutable::parse("{$date} {$checkInStart}", $timezone);
        $checkOutStartAt = CarbonImmutable::parse("{$date} {$checkOutStart}", $timezone);
        $checkOutEndAt = CarbonImmutable::parse("{$date} {$checkOutEnd}", $timezone);
        $lateAfterAt = CarbonImmutable::parse("{$date} {$lateAfter}", $timezone);

        // Check-in ends exactly before check-out begins so students who scan late get 'terlambat' instead of rejected.
        $checkInEndAt = $checkOutStartAt->subSecond();

        if ($current->betweenIncluded($checkInStartAt, $checkInEndAt)) {
            return new AttendanceWindowData(
                currentWindow: 'check_in',
                isLate: $lateAfterAt !== null && $current->greaterThan($lateAfterAt),
                timezone: $timezone,
                date: $date,
            );
        }

        if ($current->betweenIncluded($checkOutStartAt, $checkOutEndAt)) {
            return new AttendanceWindowData(
                currentWindow: 'check_out',
                isLate: false,
                timezone: $timezone,
                date: $date,
            );
        }

        return new AttendanceWindowData(
            currentWindow: 'outside',
            isLate: false,
            timezone: $timezone,
            date: $date,
        );
    }

    public function resolveFromArray(DateTimeInterface|string $timestamp, array $settings): AttendanceWindowData
    {
        return $this->resolve(
            timestamp: $timestamp,
            timezone: $settings['timezone'],
            checkInStart: $settings['check_in_start'],
            checkInEnd: $settings['check_in_end'],
            lateAfter: $settings['late_after'] ?? null,
            checkOutStart: $settings['check_out_start'],
            checkOutEnd: $settings['check_out_end'],
        );
    }
}
