<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = trim((string) $request->header('X-Device-Token'));

        if ($plainToken === '') {
            return $this->unauthorizedResponse('Missing device token.', $request);
        }

        $device = Device::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $device) {
            return $this->unauthorizedResponse('Invalid device token.', $request);
        }

        // ── IP Whitelist check ───────────────────────
        // $request->ip() is already reliable because TrustProxies is configured
        // in AppServiceProvider to forward X-Forwarded-For correctly.
        if (! $this->isIpAllowed($device, $request)) {
            return response()->json([
                'message'    => 'Request IP is not whitelisted for this device.',
                'request_id' => $request->attributes->get('request_id'),
            ], 403);
        }

        $routeDevice = $request->route('device');

        if ($routeDevice instanceof Device && ! $routeDevice->is($device)) {
            return response()->json([
                'message' => 'Device mismatch.',
                'request_id' => $request->attributes->get('request_id'),
            ], 403);
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Return true if the incoming IP is allowed for this device.
     * If `allowed_ip` is null, any IP is allowed (backward-compatible).
     */
    protected function isIpAllowed(Device $device, Request $request): bool
    {
        if (empty($device->allowed_ip)) {
            return true;
        }

        $incoming = $request->ip();

        // Exact match
        if ($incoming === $device->allowed_ip) {
            return true;
        }

        // Allow comma-separated list of IPs (e.g. "192.168.1.0,10.0.0.1")
        $whitelist = array_map('trim', explode(',', $device->allowed_ip));

        return in_array($incoming, $whitelist, true);
    }

    protected function unauthorizedResponse(string $message, Request $request): JsonResponse
    {
        return response()->json([
            'message'    => $message,
            'request_id' => $request->attributes->get('request_id'),
        ], 401);
    }
}

