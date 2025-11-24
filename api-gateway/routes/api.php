<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserGatewayController;
use App\Http\Controllers\AppointmentGatewayController;

Route::post('/register', [UserGatewayController::class, 'register']);
Route::post('/login', [UserGatewayController::class, 'login']);
Route::apiResource('/appointments', AppointmentGatewayController::class);

