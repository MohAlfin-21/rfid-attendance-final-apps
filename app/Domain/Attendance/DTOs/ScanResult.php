<?php

namespace App\Domain\Attendance\DTOs;

use App\Domain\Attendance\Enums\AttendanceAction;
use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Domain\Attendance\Enums\ScanRuleHit;
use App\Models\Attendance;

readonly class ScanResult
{
    public function __construct(
        public bool $ok,
        public ScanRuleHit $ruleHit,
        public AttendanceAction $action,
        public string $message,
        public string $uidNormalized,
        public ?StudentIdentityData $student = null,
        public ?AttendanceStatus $status = null,
        public ?Attendance $attendance = null,
    ) {}

    public function resultType(): string
    {
        return $this->ruleHit->resultType();
    }

    public function toApiArray(string $requestId, int $deviceId, int $latencyMs): array
    {
        return [
            'ok' => $this->ok,
            'code' => $this->ruleHit->value,
            'action' => $this->action->value,
            'result' => $this->resultType(),
            'message' => $this->message,
            'uid' => $this->uidNormalized,
            'student' => $this->student?->toArray(),
            'status' => $this->status?->value,
            'attendance' => $this->attendance ? [
                'check_in_at' => $this->attendance->check_in_at?->toIso8601String(),
                'check_out_at' => $this->attendance->check_out_at?->toIso8601String(),
            ] : null,
            'request_id' => $requestId,
            'device_id' => $deviceId,
            'latency_ms' => $latencyMs,
        ];
    }
}
