<?php

return [
    'boolean' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],
    'groups' => [
        'attendance' => 'Attendance Schedule',
        'academic' => 'Academic Period',
        'device' => 'RFID Devices',
        'general' => 'School Identity',
        'other' => 'Other Settings',
    ],
    'group_descriptions' => [
        'attendance' => 'Control when students can tap in, when they are marked late, and when check-out becomes available.',
        'academic' => 'This period is used to determine the active classroom roster and current semester reports.',
        'device' => 'These rules affect when a reader is marked offline and how repeat scans are ignored.',
        'general' => 'This information is shown in the admin interface and may also appear in public or report views.',
        'other' => 'Additional technical settings that are not yet mapped to a simpler layout.',
    ],
    'summary' => [
        'eyebrow' => 'Quick Summary',
        'title' => 'Currently Active Attendance Rules',
        'description' => 'Use this summary to confirm the operational schedule before editing the detailed fields below.',
        'note' => 'RFID readers will follow these rules on their next sync.',
        'check_in' => 'Students can check in',
        'late_after' => 'Late after',
        'check_out' => 'Students can check out',
        'timezone' => 'Timezone',
        'status_on_time' => 'If a card is tapped before the late threshold, the student is marked on time.',
        'status_late' => 'If a card is tapped after the late threshold but before check-in closes, the student is marked late.',
        'status_check_out' => 'Check-out is only accepted during the dismissal window defined below.',
    ],
    'labels' => [
        'attendance' => [
            'timezone' => 'School timezone',
            'check_in_start' => 'Check-in starts',
            'check_in_end' => 'Check-in ends',
            'late_after' => 'Late threshold',
            'check_out_start' => 'Check-out starts',
            'check_out_end' => 'Check-out ends',
            'academic_year' => 'Active academic year',
            'semester' => 'Active semester',
        ],
        'devices' => [
            'offline_threshold_seconds' => 'Offline threshold',
            'duplicate_scan_cooldown_seconds' => 'Repeat scan cooldown',
        ],
        'school' => [
            'name' => 'School name',
            'address' => 'School address',
        ],
    ],
    'help' => [
        'attendance' => [
            'timezone' => 'Use the school local timezone so scans are evaluated against the correct local time.',
            'check_in_start' => 'The earliest time students are allowed to tap for arrival.',
            'check_in_end' => 'After this time, arrival scans are no longer treated as check-in.',
            'late_after' => 'Starting from this time, valid arrival scans are still accepted but marked late.',
            'check_out_start' => 'The earliest time students are allowed to tap for dismissal.',
            'check_out_end' => 'After this time, dismissal scans are no longer treated as check-out.',
            'academic_year' => 'Recommended format: 2025/2026.',
            'semester' => 'Select the semester that is currently active.',
        ],
        'devices' => [
            'offline_threshold_seconds' => 'If a reader stops sending heartbeats for this duration, it is marked offline.',
            'duplicate_scan_cooldown_seconds' => 'Repeated scans from the same card during this duration are ignored.',
        ],
        'school' => [
            'name' => 'This name is displayed in the system interface.',
            'address' => 'School address shown in general information areas.',
        ],
    ],
    'cards' => [
        'check_in' => 'Arrival Window',
        'check_out' => 'Dismissal Window',
        'lateness' => 'Late Rule',
        'device' => 'Reader Behavior',
    ],
    'field_hint' => [
        'time_format' => 'Use 24-hour format.',
        'seconds' => 'Value is in seconds.',
    ],
    'semester' => [
        '1' => 'Semester 1',
        '2' => 'Semester 2',
    ],
    'actions' => [
        'save' => 'Save Settings',
        'save_help' => 'Save after you finish adjusting the rules in each section.',
    ],
];
