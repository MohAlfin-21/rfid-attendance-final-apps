<?php

namespace App\Domain\Attendance\Enums;

enum AttendanceAction: string
{
    case CheckIn = 'check_in';
    case CheckOut = 'check_out';
    case ManualMark = 'manual_mark';
    case EarlyCheckout = 'early_checkout';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::CheckIn => __('attendance.action.check_in'),
            self::CheckOut => __('attendance.action.check_out'),
            self::ManualMark => __('attendance.action.manual_mark'),
            self::EarlyCheckout => __('attendance.action.early_checkout'),
            self::Rejected => __('attendance.action.rejected'),
        };
    }
}
