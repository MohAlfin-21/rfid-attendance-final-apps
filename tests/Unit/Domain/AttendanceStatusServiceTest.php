<?php

namespace Tests\Unit\Domain;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Domain\Attendance\Exceptions\InvalidAttendanceTransitionException;
use App\Domain\Attendance\Services\AttendanceStatusService;
use Tests\TestCase;

class AttendanceStatusServiceTest extends TestCase
{
    public function test_it_allows_the_planned_initial_transitions(): void
    {
        $service = new AttendanceStatusService;

        $this->assertTrue($service->canTransition(null, AttendanceStatus::Present));
        $this->assertTrue($service->canTransition(null, AttendanceStatus::Late));
        $this->assertTrue($service->canTransition(null, AttendanceStatus::Excused));
    }

    public function test_it_rejects_overwriting_a_valid_scan_with_excused_status(): void
    {
        $this->expectException(InvalidAttendanceTransitionException::class);

        $service = new AttendanceStatusService;

        $service->assertCanTransition(AttendanceStatus::Present, AttendanceStatus::Excused);
    }
}
