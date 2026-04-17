<?php

namespace App\Domain\Attendance\Enums;

enum CheckOutType: string
{
    case Normal = 'normal';
    case Early = 'early';
}
