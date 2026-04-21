<?php

namespace App\Domain\Devices\Services;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CardEnrollmentSessionService
{
    protected const SESSION_TTL_SECONDS = 120;

    public function start(Device $device, User $user, int $startedBy): array
    {
        $expiresAt = now()->addSeconds(self::SESSION_TTL_SECONDS);

        $session = [
            'id' => (string) Str::uuid(),
            'device_id' => $device->id,
            'device_name' => $device->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'started_by' => $startedBy,
            'status' => 'waiting',
            'message' => __('Tempelkan kartu ke reader untuk mendaftarkan ke :name.', [
                'name' => $user->name,
            ]),
            'uid' => null,
            'card' => null,
            'started_at' => now()->toIso8601String(),
            'completed_at' => null,
            'expires_at' => $expiresAt->toIso8601String(),
        ];

        Cache::put($this->cacheKey($device->id), $session, $expiresAt);

        return $session;
    }

    public function pendingForDevice(Device $device): ?array
    {
        $session = $this->get($device);

        if (! $session || ($session['status'] ?? null) !== 'waiting') {
            return null;
        }

        return $session;
    }

    public function get(Device $device): ?array
    {
        $session = Cache::get($this->cacheKey($device->id));

        return is_array($session) ? $session : null;
    }

    public function getMatchingSession(Device $device, string $sessionId): ?array
    {
        $session = $this->get($device);

        if (! $session || ($session['id'] ?? null) !== $sessionId) {
            return null;
        }

        return $session;
    }

    public function complete(Device $device, string $sessionId, array $updates = []): ?array
    {
        return $this->replace($device, $sessionId, 'completed', $updates);
    }

    public function fail(Device $device, string $sessionId, array $updates = []): ?array
    {
        return $this->replace($device, $sessionId, 'failed', $updates);
    }

    public function cancel(Device $device, ?string $sessionId = null): void
    {
        if ($sessionId !== null && ! $this->getMatchingSession($device, $sessionId)) {
            return;
        }

        Cache::forget($this->cacheKey($device->id));
    }

    protected function replace(Device $device, string $sessionId, string $status, array $updates = []): ?array
    {
        $session = $this->getMatchingSession($device, $sessionId);

        if (! $session) {
            return null;
        }

        $expiresAt = now()->addSeconds(self::SESSION_TTL_SECONDS);

        $session = array_merge($session, $updates, [
            'status' => $status,
            'completed_at' => now()->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
        ]);

        Cache::put($this->cacheKey($device->id), $session, $expiresAt);

        return $session;
    }

    protected function cacheKey(int $deviceId): string
    {
        return "devices.card-enrollment.{$deviceId}";
    }
}
