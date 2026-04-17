<?php

return [
    'status' => [
        'present' => 'Present',
        'late' => 'Late',
        'excused' => 'Excused',
        'sick' => 'Sick',
        'absent' => 'Absent',
    ],
    'action' => [
        'check_in' => 'Check-in',
        'check_out' => 'Check-out',
        'manual_mark' => 'Manual mark',
        'early_checkout' => 'Early checkout',
        'rejected' => 'Rejected',
    ],
    'rule_hit' => [
        'card_not_registered' => 'Card is not registered.',
        'card_inactive' => 'Card is inactive.',
        'card_lost' => 'Card is marked as lost.',
        'device_inactive' => 'Device is inactive.',
        'user_inactive' => 'User is inactive.',
        'no_classroom_membership' => 'No active classroom membership found.',
        'membership_conflict' => 'Multiple active classroom memberships detected.',
        'duplicate_scan_cooldown' => 'Duplicate scan detected during cooldown.',
        'already_excused' => 'Attendance is already marked as excused.',
        'attendance_locked' => 'Attendance for today is locked.',
        'already_checked_in' => 'Student has already checked in.',
        'checkout_without_checkin' => 'Cannot check out before check in.',
        'already_checked_out' => 'Student has already checked out.',
        'check_in_ok' => 'Check-in successful.',
        'check_in_late' => 'Late check-in successful.',
        'check_out_ok' => 'Check-out successful.',
        'outside_window' => 'Scan is outside the attendance window.',
    ],
];
