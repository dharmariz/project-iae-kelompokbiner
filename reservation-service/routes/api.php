<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\GraphQLController;

Route::get('/reservations',              [ReservationController::class, 'index']);
Route::get('/reservations/{id}',         [ReservationController::class, 'show']);
Route::post('/reservations',             [ReservationController::class, 'store']);
Route::put('/reservations/{id}',         [ReservationController::class, 'update']);
Route::delete('/reservations/{id}',      [ReservationController::class, 'destroy']);
Route::put('/reservations/{id}/approve', [ReservationController::class, 'approve']);
Route::put('/reservations/{id}/reject',  [ReservationController::class, 'reject']);
Route::post('/graphql', [GraphQLController::class, 'handle']);
