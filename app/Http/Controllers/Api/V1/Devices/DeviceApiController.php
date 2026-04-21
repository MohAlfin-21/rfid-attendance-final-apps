<?php

namespace App\Http\Controllers\Api\V1\Devices;

use App\Domain\Attendance\Services\DeviceAttendanceScanService;
use App\Domain\Devices\Services\CardEnrollmentSessionService;
use App\Domain\Devices\Services\RfidCardRegistrationService;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\RfidCard;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeviceApiController extends Controller
{
    public function settings(Request $request): JsonResponse
    {
        $device = $this->deviceFromRequest($request);

        $this->touchDevice($device);

        return response()->json([
            'ok' => true,
            'request_id' => $request->attributes->get('request_id'),
            'server_time' => now()->toIso8601String(),
            'device' => [
                'id' => $device->id,
                'code' => $device->code,
                'name' => $device->name,
                'location' => $device->location,
                'firmware_version' => $device->firmware_version,
            ],
            'heartbeat_interval_seconds' => (int) ($device->heartbeat_interval_seconds ?: config('devices.default_heartbeat_interval', 60)),
            'duplicate_scan_cooldown_seconds' => (int) SystemSetting::get(
                'devices.duplicate_scan_cooldown_seconds',
                config('devices.duplicate_scan_cooldown_seconds', 30),
            ),
            'attendance' => array_merge(
                SystemSetting::attendanceWindowSettings(),
                [
                    'academic_year' => (string) SystemSetting::get('attendance.academic_year', config('attendance.academic_year')),
                    'semester' => (int) SystemSetting::get('attendance.semester', config('attendance.semester')),
                ],
            ),
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $device = $this->deviceFromRequest($request);

        $validated = $request->validate([
            'firmware_version' => 'nullable|string|max:50',
            'wifi_rssi' => 'nullable|integer|min:-120|max:0',
            'free_heap' => 'nullable|integer|min:0',
            'reader_uptime_ms' => 'nullable|integer|min:0',
            'ip_address' => 'nullable|string|max:45',
        ]);

        $this->touchDevice($device, $validated['firmware_version'] ?? null);

        return response()->json([
            'ok' => true,
            'message' => 'Heartbeat received.',
            'request_id' => $request->attributes->get('request_id'),
            'server_time' => now()->toIso8601String(),
            'heartbeat_interval_seconds' => (int) ($device->heartbeat_interval_seconds ?: config('devices.default_heartbeat_interval', 60)),
        ]);
    }

    public function enrollmentPending(
        Request $request,
        CardEnrollmentSessionService $sessionService,
    ): JsonResponse {
        $device = $this->deviceFromRequest($request);

        $this->touchDevice($device);

        $session = $sessionService->pendingForDevice($device);

        return response()->json([
            'ok' => true,
            'active' => $session !== null,
            'request_id' => $request->attributes->get('request_id'),
            'device_id' => $device->id,
            'session_id' => $session['id'] ?? null,
            'message' => $session['message'] ?? null,
            'expires_at' => $session['expires_at'] ?? null,
        ]);
    }

    public function enrollmentScan(
        Request $request,
        CardEnrollmentSessionService $sessionService,
        RfidCardRegistrationService $registrationService,
    ): JsonResponse {
        $device = $this->deviceFromRequest($request);

        $validated = $request->validate([
            'uid' => 'required|string|min:4|max:64',
            'firmware_version' => 'nullable|string|max:50',
            'wifi_rssi' => 'nullable|integer|min:-120|max:0',
            'free_heap' => 'nullable|integer|min:0',
            'reader_uptime_ms' => 'nullable|integer|min:0',
            'ip_address' => 'nullable|string|max:45',
        ]);

        $this->touchDevice($device, $validated['firmware_version'] ?? null);

        $session = $sessionService->pendingForDevice($device);
        $uidNormalized = RfidCard::normalizeUid($validated['uid']);

        if (! $session) {
            return response()->json([
                'ok' => false,
                'result' => 'error',
                'code' => 'enrollment_not_active',
                'message' => 'Tidak ada sesi registrasi kartu yang aktif.',
                'uid' => $uidNormalized,
                'request_id' => $request->attributes->get('request_id'),
                'device_id' => $device->id,
            ], 409);
        }

        try {
            $user = User::query()->findOrFail($session['user_id']);
            $card = $registrationService->register($user, $validated['uid']);
        } catch (ValidationException $exception) {
            $message = $exception->errors()['uid'][0] ?? $exception->getMessage();

            $sessionService->fail($device, $session['id'], [
                'uid' => $uidNormalized,
                'message' => $message,
            ]);

            return response()->json([
                'ok' => false,
                'result' => 'error',
                'code' => 'card_registration_failed',
                'message' => $message,
                'uid' => $uidNormalized,
                'request_id' => $request->attributes->get('request_id'),
                'device_id' => $device->id,
            ], 422);
        } catch (Throwable $throwable) {
            $sessionService->fail($device, $session['id'], [
                'uid' => $uidNormalized,
                'message' => 'Terjadi kesalahan internal saat menyimpan kartu.',
            ]);

            $this->markDeviceError($device, $throwable->getMessage(), $validated['firmware_version'] ?? null);
            report($throwable);

            return response()->json([
                'ok' => false,
                'result' => 'error',
                'code' => 'internal_error',
                'message' => 'Internal server error.',
                'uid' => $uidNormalized,
                'request_id' => $request->attributes->get('request_id'),
                'device_id' => $device->id,
            ], 500);
        }

        $message = __('Kartu :uid berhasil didaftarkan untuk :name.', [
            'uid' => $card->uid,
            'name' => $session['user_name'] ?? __('pengguna terpilih'),
        ]);

        $sessionService->complete($device, $session['id'], [
            'uid' => $card->uid,
            'message' => $message,
            'card' => [
                'id' => $card->id,
                'uid' => $card->uid,
                'status' => $card->status->value,
                'status_label' => $card->status->label(),
                'registered_at' => $card->registered_at?->format('d M Y H:i'),
            ],
        ]);

        return response()->json([
            'ok' => true,
            'result' => 'success',
            'code' => 'card_registered',
            'message' => $message,
            'uid' => $card->uid,
            'request_id' => $request->attributes->get('request_id'),
            'device_id' => $device->id,
            'user' => [
                'id' => $session['user_id'] ?? null,
                'name' => $session['user_name'] ?? null,
            ],
            'card' => [
                'id' => $card->id,
                'uid' => $card->uid,
                'status' => $card->status->value,
            ],
        ]);
    }

    public function scan(Request $request, DeviceAttendanceScanService $scanService): JsonResponse
    {
        $device = $this->deviceFromRequest($request);

        $validated = $request->validate([
            'uid' => 'required|string|min:4|max:64',
            'scanned_at' => 'nullable|string|max:50',
            'firmware_version' => 'nullable|string|max:50',
            'wifi_rssi' => 'nullable|integer|min:-120|max:0',
            'free_heap' => 'nullable|integer|min:0',
            'reader_uptime_ms' => 'nullable|integer|min:0',
            'ip_address' => 'nullable|string|max:45',
        ]);

        $this->touchDevice($device, $validated['firmware_version'] ?? null);

        $startedAt = microtime(true);

        try {
            $result = $scanService->handle(
                device: $device,
                payload: $validated,
                requestId: (string) $request->attributes->get('request_id'),
            );
        } catch (Throwable $throwable) {
            $this->markDeviceError($device, $throwable->getMessage(), $validated['firmware_version'] ?? null);
            report($throwable);

            return response()->json([
                'ok' => false,
                'code' => 'internal_error',
                'result' => 'error',
                'message' => 'Internal server error.',
                'request_id' => $request->attributes->get('request_id'),
                'device_id' => $device->id,
            ], 500);
        }

        $latencyMs = max(1, (int) round((microtime(true) - $startedAt) * 1000));

        return response()->json(
            array_merge(
                $result->toApiArray(
                    requestId: (string) $request->attributes->get('request_id'),
                    deviceId: $device->id,
                    latencyMs: $latencyMs,
                ),
                [
                    'server_time' => now()->toIso8601String(),
                ],
            ),
            $result->ruleHit->httpStatus(),
        );
    }

    protected function deviceFromRequest(Request $request): Device
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        return $device;
    }

    protected function touchDevice(Device $device, ?string $firmwareVersion = null): void
    {
        $device->forceFill([
            'last_heartbeat_at' => now(),
            'firmware_version' => $firmwareVersion ?: $device->firmware_version,
            'last_error_at' => null,
            'last_error_message' => null,
            'error_count' => 0,
        ])->save();
    }

    protected function markDeviceError(Device $device, string $message, ?string $firmwareVersion = null): void
    {
        $device->forceFill([
            'last_heartbeat_at' => now(),
            'firmware_version' => $firmwareVersion ?: $device->firmware_version,
            'last_error_at' => now(),
            'last_error_message' => mb_strimwidth($message, 0, 255, '...'),
            'error_count' => (int) $device->error_count + 1,
        ])->save();
    }
}
