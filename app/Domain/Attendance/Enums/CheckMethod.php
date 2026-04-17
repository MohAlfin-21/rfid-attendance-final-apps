<?php

namespace App\Domain\Attendance\Enums;

enum CheckMethod: string
{
    case Rfid = 'rfid';
    case Manual = 'manual';
}
