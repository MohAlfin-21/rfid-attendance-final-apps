<?php

namespace App\Domain\Attendance\Enums;

enum ScanRuleHit: string
{
    case CardNotRegistered = 'card_not_registered';
    case CardInactive = 'card_inactive';
    case CardLost = 'card_lost';
    case DeviceInactive = 'device_inactive';
    case UserInactive = 'user_inactive';
    case NoClassroomMembership = 'no_classroom_membership';
    case MembershipConflict = 'membership_conflict';
    case DuplicateScanCooldown = 'duplicate_scan_cooldown';
    case AlreadyExcused = 'already_excused';
    case AttendanceLocked = 'attendance_locked';
    case AlreadyCheckedIn = 'already_checked_in';
    case CheckoutWithoutCheckin = 'checkout_without_checkin';
    case AlreadyCheckedOut = 'already_checked_out';
    case CheckInOk = 'check_in_ok';
    case CheckInLate = 'check_in_late';
    case CheckOutOk = 'check_out_ok';
    case OutsideWindow = 'outside_window';

    public function label(): string
    {
        return __("attendance.rule_hit.{$this->value}");
    }

    public function resultType(): string
    {
        return match ($this) {
            self::CheckInOk, self::CheckInLate, self::CheckOutOk => 'success',
            self::DuplicateScanCooldown, self::AlreadyCheckedIn, self::AlreadyCheckedOut => 'warning',
            default => 'error',
        };
    }

    public function httpStatus(): int
    {
        return match ($this->resultType()) {
            'success', 'warning' => 200,
            default => 422,
        };
    }
}
