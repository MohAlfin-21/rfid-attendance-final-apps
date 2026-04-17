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

    protected function unauthorizedResponse(string $message, Request $request): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'request_id' => $request->attributes->get('request_id'),
        ], 401);
    }
}
