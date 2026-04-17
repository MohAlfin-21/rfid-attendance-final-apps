<?php

use App\Http\Controllers\Api\V1\Devices\DeviceApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/devices')->middleware('device.token')->group(function (): void {
    Route::get('settings', [DeviceApiController::class, 'settings']);
    Route::post('heartbeat', [DeviceApiController::class, 'heartbeat']);
    Route::post('attendance/scan', [DeviceApiController::class, 'scan']);
});
