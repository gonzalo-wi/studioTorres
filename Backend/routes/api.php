<?php

use App\Http\Controllers\Api\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Services
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Gallery
Route::get('/gallery', [GalleryController::class, 'index']);

// Availability
Route::get('/availability', [AvailabilityController::class, 'index']);

// Appointments
Route::post('/appointments', [AppointmentController::class, 'store']);
Route::get('/appointments/{publicCode}', [AppointmentController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Admin API Routes (Protected with Sanctum)
|--------------------------------------------------------------------------
*/

// Admin Auth
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Dashboard stats
        Route::get('/dashboard/stats', [AdminAppointmentController::class, 'stats']);

        // Appointments
        Route::get('/appointments', [AdminAppointmentController::class, 'index']);
        Route::put('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus']);

        // Services
        Route::post('/services', [AdminServiceController::class, 'store']);
        Route::put('/services/{service}', [AdminServiceController::class, 'update']);
        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy']);

        // Gallery
        Route::post('/gallery', [AdminGalleryController::class, 'store']);
        Route::delete('/gallery/{galleryItem}', [AdminGalleryController::class, 'destroy']);
    });
});
