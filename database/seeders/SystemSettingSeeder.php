<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    /**
     * Seed default system settings.
     */
    public function run(): void
    {
        $settings = [
            // Attendance window settings
            [
                'key' => 'attendance.timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Timezone for attendance window calculations',
            ],
            [
                'key' => 'attendance.check_in_start',
                'value' => '05:45',
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Check-in window start time (HH:MM)',
            ],
            [
                'key' => 'attendance.check_in_end',
                'value' => '07:10',
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Check-in window end time (HH:MM)',
            ],
            [
                'key' => 'attendance.late_after',
                'value' => '06:45',
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Time after which check-in is considered late (HH:MM)',
            ],
            [
                'key' => 'attendance.check_out_start',
                'value' => '15:00',
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Check-out window start time (HH:MM)',
            ],
            [
                'key' => 'attendance.check_out_end',
                'value' => '16:45',
                'type' => 'string',
                'group' => 'attendance',
                'description' => 'Check-out window end time (HH:MM)',
            ],

            // Academic settings
            [
                'key' => 'attendance.academic_year',
                'value' => '2025/2026',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Current academic year',
            ],
            [
                'key' => 'attendance.semester',
                'value' => '2',
                'type' => 'integer',
                'group' => 'academic',
                'description' => 'Current semester (1 or 2)',
            ],

            // Device settings
            [
                'key' => 'devices.offline_threshold_seconds',
                'value' => '120',
                'type' => 'integer',
                'group' => 'device',
                'description' => 'Seconds before device is considered offline',
            ],
            [
                'key' => 'devices.duplicate_scan_cooldown_seconds',
                'value' => '30',
                'type' => 'integer',
                'group' => 'device',
                'description' => 'Seconds to ignore repeated scans from the same card',
            ],

            // School info
            [
                'key' => 'school.name',
                'value' => 'SMK Negeri 1',
                'type' => 'string',
                'group' => 'general',
                'description' => 'School name displayed in the UI',
            ],
            [
                'key' => 'school.address',
                'value' => 'Jl. Pendidikan No. 1',
                'type' => 'string',
                'group' => 'general',
                'description' => 'School address',
            ],
        ];

        $now = now();

        foreach ($settings as $setting) {
            DB::table('system_settings')->insertOrIgnore(
                array_merge($setting, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]),
            );
        }
    }
}
