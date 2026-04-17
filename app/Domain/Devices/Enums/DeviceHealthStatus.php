<?php

namespace App\Domain\Devices\Enums;

enum DeviceHealthStatus: string
{
    case Healthy = 'healthy';
    case Warning = 'warning';
    case Offline = 'offline';

    public function label(): string
    {
        return __("devices.health.{$this->value}");
    }

    public function badge(): string
    {
        return match ($this) {
            self::Healthy => 'badge-success',
            self::Warning => 'badge-warning',
            self::Offline => 'badge-danger',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Healthy => '#16a34a',
            self::Warning => '#d97706',
            self::Offline => '#dc2626',
        };
    }
}
