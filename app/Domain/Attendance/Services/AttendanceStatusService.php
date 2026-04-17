<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Domain\Attendance\Exceptions\InvalidAttendanceTransitionException;

class AttendanceStatusService
{
    public function canTransition(?AttendanceStatus $from, AttendanceStatus $to, bool $isOverride = false): bool
    {
        if ($isOverride) {
            return true;
        }

        if ($from === null) {
            return in_array($to, [
                AttendanceStatus::Present,
                AttendanceStatus::Late,
                AttendanceStatus::Excused,
                AttendanceStatus::Sick,
                AttendanceStatus::Absent,
            ], true);
        }

        return $from === $to;
    }

    public function assertCanTransition(?AttendanceStatus $from, AttendanceStatus $to, bool $isOverride = false): void
    {
        if ($this->canTransition($from, $to, $isOverride)) {
            return;
        }

        throw new InvalidAttendanceTransitionException(sprintf(
            'Illegal attendance transition from [%s] to [%s].',
            $from?->value ?? 'null',
            $to->value,
        ));
    }
}
