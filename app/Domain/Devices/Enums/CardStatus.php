<?php

namespace App\Domain\Devices\Enums;

enum CardStatus: string
{
    case Active = 'active';
    case Lost = 'lost';
    case Inactive = 'inactive';

    public function label(): string
    {
        return __("devices.card_status.{$this->value}");
    }

    public function badge(): string
    {
        return match ($this) {
            self::Active => 'badge-success',
            self::Lost => 'badge-danger',
            self::Inactive => 'badge-neutral',
        };
    }
}
