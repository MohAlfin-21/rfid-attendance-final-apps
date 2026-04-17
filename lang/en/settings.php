<?php

return [
    'boolean' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],
    'groups' => [
        'attendance' => 'Attendance Window',
        'academic' => 'Academic Term',
        'device' => 'Device',
        'general' => 'School Information',
    ],
    'labels' => [
        'attendance' => [
            'timezone' => 'Timezone for attendance window calculations',
            'check_in_start' => 'Check-in window start time (HH:MM)',
            'check_in_end' => 'Check-in window end time (HH:MM)',
            'late_after' => 'Time after which check-in is considered late (HH:MM)',
            'check_out_start' => 'Check-out window start time (HH:MM)',
            'check_out_end' => 'Check-out window end time (HH:MM)',
            'academic_year' => 'Current academic year',
            'semester' => 'Current semester (1 or 2)',
        ],
        'devices' => [
            'offline_threshold_seconds' => 'Seconds before device is considered offline',
            'duplicate_scan_cooldown_seconds' => 'Seconds to ignore repeated scans from the same card',
        ],
        'school' => [
            'name' => 'School name displayed in the UI',
            'address' => 'School address',
        ],
    ],
];
