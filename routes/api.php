<?php

use App\Http\Controllers\Api\V1\Devices\DeviceApiController;
use Illuminate\Support\Facades\Route;

// Device API: throttle 60 requests/minute per IP to prevent token abuse.
// IP resolution is reliable because TrustProxies is set in AppServiceProvider.
Route::prefix('v1/devices')->middleware(['device.token', 'throttle:60,1'])->group(function (): void {
    Route::get('settings', [DeviceApiController::class, 'settings']);
    Route::post('heartbeat', [DeviceApiController::class, 'heartbeat']);
    Route::get('card-enrollment/pending', [DeviceApiController::class, 'enrollmentPending']);
    Route::post('card-enrollment/scan', [DeviceApiController::class, 'enrollmentScan']);
    Route::post('attendance/scan', [DeviceApiController::class, 'scan']);
});
