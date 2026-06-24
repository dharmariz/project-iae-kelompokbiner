<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraphQLController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Internal route untuk service lain (Reservation Service memanggil ini)
Route::get('/users/{id}', [AuthController::class, 'show']);

// Protected routes (butuh token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile',  [AuthController::class, 'profile']);
    Route::put('/profile',  [AuthController::class, 'updateProfile']);
    Route::post('/logout',  [AuthController::class, 'logout']);

// GraphQL endpoint
Route::post('/graphql', [GraphQLController::class, 'handle']);

});
