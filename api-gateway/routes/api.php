<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserGatewayController;
use App\Http\Controllers\AppointmentGatewayController;

// Public: proxied to user-service, which does its own validation
Route::post('/register', [UserGatewayController::class, 'register']);
Route::post('/login', [UserGatewayController::class, 'login']);

// Authenticated: gateway verifies the JWT signature locally, then
// forwards trusted X-User-Id / X-User-Role headers downstream
Route::middleware('auth.jwt')->group(function () {
    Route::get('/me', [UserGatewayController::class, 'me']);

    Route::get('/appointments', [AppointmentGatewayController::class, 'index']);
    Route::post('/appointments', [AppointmentGatewayController::class, 'store']);
    Route::put('/appointments/{id}', [AppointmentGatewayController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentGatewayController::class, 'destroy']);
});
