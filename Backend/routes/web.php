<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Sanctum CSRF Cookie route
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])
    ->middleware('web');

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/up', function () {
    return response()->json(['status' => 'ok']);
});
