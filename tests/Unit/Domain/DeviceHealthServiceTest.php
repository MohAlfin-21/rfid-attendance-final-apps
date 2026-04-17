<?php

namespace Tests\Unit\Domain;

use App\Domain\Devices\Enums\DeviceHealthStatus;
use App\Domain\Devices\Services\DeviceHealthService;
use App\Models\Device;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class DeviceHealthServiceTest extends TestCase
{
    public function test_it_marks_devices_without_recent_heartbeat_as_offline(): void
    {
        $service = new DeviceHealthService;

        $status = $service->resolveStatus(
            lastHeartbeatAt: CarbonImmutable::parse('2000-01-01 00:00:00'),
            thresholdSeconds: 120,
        );

        $this->assertSame(DeviceHealthStatus::Offline, $status);
    }

    public function test_it_marks_online_devices_with_errors_as_warning(): void
    {
        $service = new DeviceHealthService;

        $status = $service->resolveStatus(
            lastHeartbeatAt: CarbonImmutable::now()->subSeconds(30),
            errorCount: 1,
            thresholdSeconds: 120,
        );

        $this->assertSame(DeviceHealthStatus::Warning, $status);
    }

    public function test_it_builds_a_snapshot_for_a_healthy_device(): void
    {
        $device = new Device([
            'name' => 'Reader Utama',
            'is_active' => true,
            'error_count' => 0,
            'last_heartbeat_at' => CarbonImmutable::now()->subSeconds(30),
        ]);

        $service = new DeviceHealthService;
        $snapshot = $service->snapshot($device, 120);

        $this->assertSame(DeviceHealthStatus::Healthy, $snapshot->status);
        $this->assertSame(0, $snapshot->errorCount);
        $this->assertNotNull($snapshot->lastHeartbeat);
    }
}
