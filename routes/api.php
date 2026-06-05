<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoomApplicationController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function (): void {
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('tenants', TenantController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('room-applications', RoomApplicationController::class);
});
