<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordRecoveryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SecretQuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/preguntas-secretas', [SecretQuestionController::class, 'index']);

// Password recovery routes
Route::post('/password/verify-email', [PasswordRecoveryController::class, 'verifyEmail']);
Route::post('/password/verify-answer', [PasswordRecoveryController::class, 'verifyAnswer']);
Route::post('/password/update', [PasswordRecoveryController::class, 'updatePassword']);

// Protected routes (require authentication)
// Note: OAuth routes are in web.php because they need session handling
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

