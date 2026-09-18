<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Device Communication API
Route::prefix('devices')->group(function () {
    Route::post('{device}/heartbeat', [DeviceApiController::class, 'heartbeat']);
    Route::post('{device}/biometric/logs', [DeviceApiController::class, 'pushBiometricLogs']);
    Route::post('{device}/smart-lock/events', [DeviceApiController::class, 'pushSmartLockEvent']);
    Route::get('{device}/smart-lock/access-codes', [DeviceApiController::class, 'getSmartLockAccessCodes']);
    Route::post('{device}/printer/jobs', [DeviceApiController::class, 'submitPrintJob']);
});
