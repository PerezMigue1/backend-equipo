<?php

use App\Http\Controllers\Api\FacebookAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (OAuth only)
|--------------------------------------------------------------------------
|
| Estas rutas son necesarias para OAuth porque requieren sesiones.
| Todas las demás rutas están en api.php
|
*/

// Rutas para autenticación con Facebook
Route::get('auth/facebook', [FacebookAuthController::class, 'redirect'])->name('facebook.login');
Route::get('auth/facebook/callback', [FacebookAuthController::class, 'callback'])->name('facebook.callback');

// Health check endpoint (para Render.com)
Route::get('up', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toDateTimeString(),
    ], 200);
})->name('health.check');

// Páginas estáticas para Facebook Developer
Route::get('privacy', function () {
    return response()->json(['message' => 'Privacy Policy']);
})->name('privacy');

Route::get('delete-data', function () {
    return response()->json(['message' => 'Data Deletion']);
})->name('delete-data');

