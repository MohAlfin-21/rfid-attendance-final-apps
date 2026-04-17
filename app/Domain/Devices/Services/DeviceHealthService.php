<?php

namespace App\Domain\Devices\Services;

use App\Domain\Devices\DTOs\DeviceHealthSnapshot;
use App\Domain\Devices\Enums\DeviceHealthStatus;
use App\Models\Device;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class DeviceHealthService
{
    /**
     * Build a health snapshot for the given device.
     *
     * @param  Device    $device            The device to check.
     * @param  int|null  $thresholdSeconds  Override the offline threshold (uses config default).
     * @return DeviceHealthSnapshot
     */
    public function snapshot(Device $device, ?int $thresholdSeconds = null): DeviceHealthSnapshot
    {
        $thresholdSeconds ??= (int) config('devices.offline_threshold_seconds', 120);

        return new DeviceHealthSnapshot(
            status: $this->resolveStatus(
                lastHeartbeatAt: $device->last_heartbeat_at,
                errorCount: (int) $device->error_count,
                lastErrorAt: $device->last_error_at,
                thresholdSeconds: $thresholdSeconds,
            ),
            lastHeartbeat: $device->last_heartbeat_at?->toIso8601String(),
            errorCount: (int) $device->error_count,
            lastError: $device->last_error_message,
        );
    }

    /**
     * Determine the health status based on heartbeat and error data.
     *
     * @param  CarbonInterface|null  $lastHeartbeatAt   Last heartbeat timestamp.
     * @param  int                   $errorCount        Cumulative error count.
     * @param  CarbonInterface|null  $lastErrorAt       Last error timestamp.
     * @param  int|null              $thresholdSeconds   Seconds before considered offline.
     * @return DeviceHealthStatus
     */
    public function resolveStatus(
        ?CarbonInterface $lastHeartbeatAt,
        int $errorCount = 0,
        ?CarbonInterface $lastErrorAt = null,
        ?int $thresholdSeconds = null,
    ): DeviceHealthStatus {
        $thresholdSeconds ??= (int) config('devices.offline_threshold_seconds', 120);

        if (! $lastHeartbeatAt || $lastHeartbeatAt->lessThan(CarbonImmutable::now()->subSeconds($thresholdSeconds))) {
            return DeviceHealthStatus::Offline;
        }

        if ($errorCount > 0 || $lastErrorAt !== null) {
            return DeviceHealthStatus::Warning;
        }

        return DeviceHealthStatus::Healthy;
    }
}
