<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Timezone
    |--------------------------------------------------------------------------
    |
    | The timezone used for attendance window calculations.
    | Should match the school's physical location.
    |
    */

    'timezone' => env('ATTENDANCE_TIMEZONE', 'Asia/Jakarta'),

    /*
    |--------------------------------------------------------------------------
    | Check-in Window
    |--------------------------------------------------------------------------
    |
    | Start and end times for the check-in window (HH:MM format).
    | Students scanning within this window are marked as checked in.
    |
    */

    'check_in_start' => env('ATTENDANCE_CHECK_IN_START', '05:45'),
    'check_in_end' => env('ATTENDANCE_CHECK_IN_END', '07:10'),

    /*
    |--------------------------------------------------------------------------
    | Late Threshold
    |--------------------------------------------------------------------------
    |
    | Time after which a check-in is considered late (HH:MM format).
    | Set to null to disable late tracking.
    |
    */

    'late_after' => env('ATTENDANCE_LATE_AFTER', '06:45'),

    /*
    |--------------------------------------------------------------------------
    | Check-out Window
    |--------------------------------------------------------------------------
    |
    | Start and end times for the check-out window (HH:MM format).
    | Students scanning within this window are marked as checked out.
    |
    */

    'check_out_start' => env('ATTENDANCE_CHECK_OUT_START', '15:00'),
    'check_out_end' => env('ATTENDANCE_CHECK_OUT_END', '16:45'),

    /*
    |--------------------------------------------------------------------------
    | Academic Year & Semester
    |--------------------------------------------------------------------------
    |
    | Current academic year and semester. Used as defaults when creating
    | classroom memberships and filtering attendance records.
    |
    */

    'academic_year' => env('ATTENDANCE_ACADEMIC_YEAR', '2025/2026'),
    'semester' => (int) env('ATTENDANCE_SEMESTER', 2),

];
