<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Device Offline Threshold
    |--------------------------------------------------------------------------
    |
    | Number of seconds after the last heartbeat before a device is
    | considered offline. The DeviceHealthService compares this value
    | against last_heartbeat_at to determine the device status.
    |
    */

    'offline_threshold_seconds' => (int) env('DEVICE_OFFLINE_THRESHOLD_SECONDS', 120),

    /*
    |--------------------------------------------------------------------------
    | Default Heartbeat Interval
    |--------------------------------------------------------------------------
    |
    | Default interval in seconds that devices should send heartbeats.
    | This value is sent to devices via the settings endpoint.
    |
    */

    'default_heartbeat_interval' => (int) env('DEVICE_HEARTBEAT_INTERVAL', 60),

    /*
    |--------------------------------------------------------------------------
    | Duplicate Scan Cooldown
    |--------------------------------------------------------------------------
    |
    | Number of seconds to ignore repeated scans from the same card.
    | Prevents accidental double-tap registrations.
    |
    */

    'duplicate_scan_cooldown_seconds' => (int) env('DEVICE_DUPLICATE_SCAN_COOLDOWN', 30),

];
