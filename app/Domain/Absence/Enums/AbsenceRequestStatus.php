<?php

namespace App\Domain\Absence\Enums;

enum AbsenceRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'badge-warning',
            self::Approved => 'badge-success',
            self::Rejected => 'badge-danger',
        };
    }
}
