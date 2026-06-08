<?php

use Illuminate\Support\Facades\Route;

use App\Jobs\ReservationCreatedJob;

Route::get('/test-rabbitmq', function () {

    ReservationCreatedJob::dispatch([
        'user_id' => 1
    ]);

    return 'Message Sent!';
});
Route::get('/', function () {
    return view('welcome');
});
