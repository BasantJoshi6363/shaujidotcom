<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PasswordApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Middleware\EnsureApiHasBusiness;
use App\Http\Middleware\EnsureApiEmailVerified;
use Illuminate\Support\Facades\Route;

// Guest
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login',    [AuthApiController::class, 'login'])->middleware('throttle:10,1');

Route::post('/send-otp',   [PasswordApiController::class, 'sendOtp'])->middleware('throttle:5,1');
Route::post('/verify-otp', [PasswordApiController::class, 'verifyOtp'])->middleware('throttle:10,1');
Route::post('/reset-password', [PasswordApiController::class, 'resetPassword']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // Email verification
    Route::post('/email/verify/send', [AuthApiController::class, 'resendVerification'])
        ->middleware('throttle:3,1');
    Route::post('/email/verify',      [AuthApiController::class, 'verifyEmail'])
        ->middleware('throttle:10,1');

    // Onboarding
    Route::post('/onboarding', [AuthApiController::class, 'onboard']);

    // Fully unlocked app
    Route::middleware([EnsureApiEmailVerified::class, EnsureApiHasBusiness::class])->group(function () {
        Route::get('/dashboard',  [DashboardApiController::class, 'index']);
        Route::get('/customers',  [CustomerApiController::class, 'index']);
        Route::get('/customers/{id}', [CustomerApiController::class, 'show']);
        Route::post('/customers/{id}/credit',   [CustomerApiController::class, 'credit']);
        Route::post('/customers/{id}/payments', [CustomerApiController::class, 'payment']);
        Route::get('/customers/{id}/ledger',    [CustomerApiController::class, 'ledger']);
    });
});