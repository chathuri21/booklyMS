<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;

// Identity is asserted by the API gateway via X-User-Id / X-User-Role headers
// after it validates the caller's token with the user service.
Route::middleware('auth.gateway')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
});
