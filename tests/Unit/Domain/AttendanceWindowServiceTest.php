<?php

namespace Tests\Unit\Domain;

use App\Domain\Attendance\Services\AttendanceWindowService;
use Tests\TestCase;

class AttendanceWindowServiceTest extends TestCase
{
    public function test_it_marks_check_in_as_late_after_the_late_threshold(): void
    {
        $service = new AttendanceWindowService;

        $window = $service->resolve(
            timestamp: '2026-04-13 06:51:00',
            timezone: 'Asia/Jakarta',
            checkInStart: '05:45',
            checkInEnd: '07:10',
            lateAfter: '06:45',
            checkOutStart: '15:00',
            checkOutEnd: '16:45',
        );

        $this->assertSame('check_in', $window->currentWindow);
        $this->assertTrue($window->isLate);
        $this->assertSame('2026-04-13', $window->date);
    }

    public function test_it_resolves_the_checkout_window(): void
    {
        $service = new AttendanceWindowService;

        $window = $service->resolve(
            timestamp: '2026-04-13 15:20:00',
            timezone: 'Asia/Jakarta',
            checkInStart: '05:45',
            checkInEnd: '07:10',
            lateAfter: '06:45',
            checkOutStart: '15:00',
            checkOutEnd: '16:45',
        );

        $this->assertSame('check_out', $window->currentWindow);
        $this->assertFalse($window->isLate);
    }
}
