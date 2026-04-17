<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\DTOs\ScanResult;
use App\Domain\Attendance\DTOs\StudentIdentityData;
use App\Domain\Attendance\Enums\AttendanceAction;
use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Domain\Attendance\Enums\CheckMethod;
use App\Domain\Attendance\Enums\CheckOutType;
use App\Domain\Attendance\Enums\ScanRuleHit;
use App\Domain\Devices\Enums\CardStatus;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Classroom;
use App\Models\Device;
use App\Models\RfidCard;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DeviceAttendanceScanService
{
    public function __construct(
        protected AttendanceWindowService $windowService,
        protected AttendanceStatusService $statusService,
    ) {}

    public function handle(Device $device, array $payload, string $requestId): ScanResult
    {
        $serverNow = CarbonImmutable::now();
        $uidNormalized = RfidCard::normalizeUid($payload['uid']);

        if (! $device->is_active) {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::DeviceInactive,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
            );
        }

        $card = RfidCard::query()
            ->with('user')
            ->byUid($uidNormalized)
            ->first();

        if (! $card) {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::CardNotRegistered,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
            );
        }

        if ($card->status === CardStatus::Inactive) {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::CardInactive,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                student: StudentIdentityData::fromUser($card->user),
            );
        }

        if ($card->status === CardStatus::Lost) {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::CardLost,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                student: StudentIdentityData::fromUser($card->user),
            );
        }

        $student = $card->user;

        if (! $student || ! $student->is_active) {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::UserInactive,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                student: $student ? StudentIdentityData::fromUser($student) : null,
            );
        }

        [$membershipRuleHit, $classroom] = $this->resolveActiveClassroom($student);

        if ($membershipRuleHit !== null) {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: $membershipRuleHit,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                student: StudentIdentityData::fromUser($student),
            );
        }

        $studentIdentity = StudentIdentityData::fromUser($student, $classroom);
        $window = $this->windowService->resolveFromArray(
            $serverNow,
            SystemSetting::attendanceWindowSettings(),
        );

        if ($window->currentWindow === 'outside') {
            return $this->finalizeStandalone(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::OutsideWindow,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                student: $studentIdentity,
            );
        }

        return DB::transaction(function () use (
            $device,
            $payload,
            $requestId,
            $serverNow,
            $uidNormalized,
            $student,
            $studentIdentity,
            $classroom,
            $window,
        ): ScanResult {
            if ($this->hasRecentScan($uidNormalized, $serverNow)) {
                return $this->persistResult(
                    device: $device,
                    uidNormalized: $uidNormalized,
                    ruleHit: ScanRuleHit::DuplicateScanCooldown,
                    action: AttendanceAction::Rejected,
                    requestId: $requestId,
                    payload: $payload,
                    serverNow: $serverNow,
                    student: $studentIdentity,
                );
            }

            $attendance = Attendance::query()
                ->where('user_id', $student->id)
                ->whereDate('date', $window->date)
                ->lockForUpdate()
                ->first();

            if ($window->currentWindow === 'check_in') {
                return $this->processCheckIn(
                    device: $device,
                    payload: $payload,
                    requestId: $requestId,
                    serverNow: $serverNow,
                    uidNormalized: $uidNormalized,
                    student: $student,
                    studentIdentity: $studentIdentity,
                    classroom: $classroom,
                    attendance: $attendance,
                    isLate: $window->isLate,
                    attendanceDate: $window->date,
                );
            }

            return $this->processCheckOut(
                device: $device,
                payload: $payload,
                requestId: $requestId,
                serverNow: $serverNow,
                uidNormalized: $uidNormalized,
                studentIdentity: $studentIdentity,
                attendance: $attendance,
            );
        });
    }

    protected function processCheckIn(
        Device $device,
        array $payload,
        string $requestId,
        CarbonImmutable $serverNow,
        string $uidNormalized,
        User $student,
        StudentIdentityData $studentIdentity,
        Classroom $classroom,
        ?Attendance $attendance,
        bool $isLate,
        string $attendanceDate,
    ): ScanResult {
        $targetStatus = $isLate ? AttendanceStatus::Late : AttendanceStatus::Present;

        if ($attendance && in_array($attendance->status, [AttendanceStatus::Excused, AttendanceStatus::Sick], true)) {
            return $this->persistResult(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::AlreadyExcused,
                action: AttendanceAction::Rejected,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                attendance: $attendance,
                student: $studentIdentity,
                status: $attendance->status,
            );
        }

        if ($attendance && $attendance->hasCheckedIn()) {
            return $this->persistResult(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::AlreadyCheckedIn,
                action: AttendanceAction::Rejected,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                attendance: $attendance,
                student: $studentIdentity,
                status: $attendance->status,
            );
        }

        if ($attendance && ! $this->statusService->canTransition($attendance->status, $targetStatus)) {
            return $this->persistResult(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::AttendanceLocked,
                action: AttendanceAction::Rejected,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                attendance: $attendance,
                student: $studentIdentity,
                status: $attendance->status,
            );
        }

        if (! $attendance) {
            $attendance = new Attendance([
                'user_id' => $student->id,
                'classroom_id' => $classroom->id,
                'date' => $attendanceDate,
            ]);
        }

        $attendance->fill([
            'classroom_id' => $attendance->classroom_id ?: $classroom->id,
            'status' => $targetStatus,
            'check_in_at' => $serverNow,
            'check_in_method' => CheckMethod::Rfid,
            'check_in_device_id' => $device->id,
        ]);
        $attendance->save();

        return $this->persistResult(
            device: $device,
            uidNormalized: $uidNormalized,
            ruleHit: $isLate ? ScanRuleHit::CheckInLate : ScanRuleHit::CheckInOk,
            action: AttendanceAction::CheckIn,
            requestId: $requestId,
            payload: $payload,
            serverNow: $serverNow,
            attendance: $attendance,
            student: $studentIdentity,
            status: $targetStatus,
        );
    }

    protected function processCheckOut(
        Device $device,
        array $payload,
        string $requestId,
        CarbonImmutable $serverNow,
        string $uidNormalized,
        StudentIdentityData $studentIdentity,
        ?Attendance $attendance,
    ): ScanResult {
        if (! $attendance || ! $attendance->hasCheckedIn()) {
            return $this->persistResult(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::CheckoutWithoutCheckin,
                action: AttendanceAction::Rejected,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                attendance: $attendance,
                student: $studentIdentity,
                status: $attendance?->status,
            );
        }

        if (in_array($attendance->status, [AttendanceStatus::Excused, AttendanceStatus::Sick], true)) {
            return $this->persistResult(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::AlreadyExcused,
                action: AttendanceAction::Rejected,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                attendance: $attendance,
                student: $studentIdentity,
                status: $attendance->status,
            );
        }

        if ($attendance->hasCheckedOut()) {
            return $this->persistResult(
                device: $device,
                uidNormalized: $uidNormalized,
                ruleHit: ScanRuleHit::AlreadyCheckedOut,
                action: AttendanceAction::Rejected,
                requestId: $requestId,
                payload: $payload,
                serverNow: $serverNow,
                attendance: $attendance,
                student: $studentIdentity,
                status: $attendance->status,
            );
        }

        $attendance->fill([
            'check_out_at' => $serverNow,
            'check_out_method' => CheckMethod::Rfid,
            'check_out_device_id' => $device->id,
            'check_out_type' => CheckOutType::Normal,
        ]);
        $attendance->save();

        return $this->persistResult(
            device: $device,
            uidNormalized: $uidNormalized,
            ruleHit: ScanRuleHit::CheckOutOk,
            action: AttendanceAction::CheckOut,
            requestId: $requestId,
            payload: $payload,
            serverNow: $serverNow,
            attendance: $attendance,
            student: $studentIdentity,
            status: $attendance->status,
        );
    }

    protected function resolveActiveClassroom(User $student): array
    {
        $academicYear = (string) SystemSetting::get('attendance.academic_year', config('attendance.academic_year'));
        $semester = (int) SystemSetting::get('attendance.semester', config('attendance.semester'));

        $classrooms = $student->classrooms()
            ->where('classrooms.is_active', true)
            ->wherePivot('is_active', true)
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->get();

        if ($classrooms->isEmpty()) {
            return [ScanRuleHit::NoClassroomMembership, null];
        }

        if ($classrooms->count() > 1) {
            return [ScanRuleHit::MembershipConflict, null];
        }

        return [null, $classrooms->first()];
    }

    protected function hasRecentScan(string $uidNormalized, CarbonImmutable $serverNow): bool
    {
        $cooldownSeconds = (int) SystemSetting::get(
            'devices.duplicate_scan_cooldown_seconds',
            config('devices.duplicate_scan_cooldown_seconds', 30),
        );

        if ($cooldownSeconds <= 0) {
            return false;
        }

        return AttendanceLog::query()
            ->where('rfid_uid', $uidNormalized)
            ->where('created_at', '>=', $serverNow->subSeconds($cooldownSeconds))
            ->lockForUpdate()
            ->exists();
    }

    protected function finalizeStandalone(
        Device $device,
        string $uidNormalized,
        ScanRuleHit $ruleHit,
        string $requestId,
        array $payload,
        CarbonImmutable $serverNow,
        ?StudentIdentityData $student = null,
    ): ScanResult {
        return $this->persistResult(
            device: $device,
            uidNormalized: $uidNormalized,
            ruleHit: $ruleHit,
            action: AttendanceAction::Rejected,
            requestId: $requestId,
            payload: $payload,
            serverNow: $serverNow,
            student: $student,
        );
    }

    protected function persistResult(
        Device $device,
        string $uidNormalized,
        ScanRuleHit $ruleHit,
        AttendanceAction $action,
        string $requestId,
        array $payload,
        CarbonImmutable $serverNow,
        ?Attendance $attendance = null,
        ?StudentIdentityData $student = null,
        ?AttendanceStatus $status = null,
    ): ScanResult {
        AttendanceLog::query()->create([
            'attendance_id' => $attendance?->id,
            'action' => $action,
            'rfid_uid' => $uidNormalized,
            'device_id' => $device->id,
            'rule_hit' => $ruleHit,
            'metadata' => $this->buildMetadata($payload, $serverNow),
            'request_id' => $requestId,
        ]);

        return new ScanResult(
            ok: $ruleHit->resultType() === 'success',
            ruleHit: $ruleHit,
            action: $action,
            message: $ruleHit->label(),
            uidNormalized: $uidNormalized,
            student: $student,
            status: $status,
            attendance: $attendance,
        );
    }

    protected function buildMetadata(array $payload, CarbonImmutable $serverNow): array
    {
        return array_filter([
            'server_time' => $serverNow->toIso8601String(),
            'firmware_version' => $payload['firmware_version'] ?? null,
            'scanned_at' => $payload['scanned_at'] ?? null,
            'wifi_rssi' => $payload['wifi_rssi'] ?? null,
            'free_heap' => $payload['free_heap'] ?? null,
            'reader_uptime_ms' => $payload['reader_uptime_ms'] ?? null,
            'ip_address' => $payload['ip_address'] ?? null,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
