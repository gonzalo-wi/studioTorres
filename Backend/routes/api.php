<?php

use App\Http\Controllers\Api\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\BarberController;
use App\Http\Controllers\Api\Admin\ClientController;
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

// Barbers (Public)
Route::get('/barbers', [\App\Http\Controllers\Api\BarberController::class, 'index']);
Route::get('/barbers/{barber}', [\App\Http\Controllers\Api\BarberController::class, 'show']);

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
        Route::get('/dashboard/monthly-stats', [AdminAppointmentController::class, 'monthlyStats']);

        // Appointments
        Route::get('/appointments', [AdminAppointmentController::class, 'index']);
        Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show']);
        Route::put('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus']);

        // Barbers
        Route::get('/barbers', [BarberController::class, 'index']);
        Route::get('/barbers/{barber}', [BarberController::class, 'show']);
        Route::post('/barbers', [BarberController::class, 'store']);
        Route::put('/barbers/{barber}', [BarberController::class, 'update']);
        Route::delete('/barbers/{barber}', [BarberController::class, 'destroy']);
        Route::post('/barbers/{barber}/schedules', [BarberController::class, 'updateSchedules']);
        Route::get('/barbers/{barber}/appointments', [BarberController::class, 'appointments']);
        Route::get('/barbers/{barber}/earnings', [BarberController::class, 'earnings']);

        // Clients
        Route::get('/clients', [ClientController::class, 'index']);
        Route::get('/clients/{client}', [ClientController::class, 'show']);
        Route::get('/clients/search/appointments', [ClientController::class, 'searchByAppointments']);

        // Services
        Route::get('/services', [AdminServiceController::class, 'index']);
        Route::post('/services', [AdminServiceController::class, 'store']);
        Route::put('/services/{service}', [AdminServiceController::class, 'update']);
        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy']);

        // Gallery
        Route::post('/gallery', [AdminGalleryController::class, 'store']);
        Route::delete('/gallery/{galleryItem}', [AdminGalleryController::class, 'destroy']);

        // Reports
        Route::get('/reports', [AdminAppointmentController::class, 'reports']);
        Route::get('/reports/export', [AdminAppointmentController::class, 'exportReports']);
    });
});
