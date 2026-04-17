<?php

namespace App\Domain\Devices\DTOs;

use App\Domain\Devices\Enums\DeviceHealthStatus;

/**
 * Immutable snapshot of a device's health at a point in time.
 */
readonly class DeviceHealthSnapshot
{
    public function __construct(
        public DeviceHealthStatus $status,
        public ?string $lastHeartbeat,
        public int $errorCount,
        public ?string $lastError,
    ) {}

    /**
     * Convert the snapshot to an array suitable for API responses.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'last_heartbeat' => $this->lastHeartbeat,
            'error_count' => $this->errorCount,
            'last_error' => $this->lastError,
        ];
    }
}
