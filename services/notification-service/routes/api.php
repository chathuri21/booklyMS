<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Jobs\SendAppointmentNotification;

Route::post('/send', function (Request $request) {
    dispatch(new SendAppointmentNotification($request->all()));
});
