<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/notifications', [NotificationController::class, 'index']);
Route::put('/notifications/{id}', [NotificationController::class, 'update']); // TAMBAHAN: Untuk ubah status jadi read
