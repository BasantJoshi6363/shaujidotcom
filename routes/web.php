<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationOtpController;
use App\Http\Controllers\Auth\FcebookAuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SettingsController;
use App\Http\Middleware\EnsureHasBusiness;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Guests only (not logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

    Route::get('/auth/facebook/redirect', [FcebookAuthController::class, 'redirect'])->name('facebook.redirect');
    Route::get('/auth/facebook/callback', [FcebookAuthController::class, 'callback'])->name('facebook.callback');

    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

    // Forgot password
    Route::view('/forget-password', 'auth.forget')->name('forget-password');
    Route::post('/send-otp', [ForgetPasswordController::class, 'sendOtp'])->middleware('throttle:5,1');
    Route::view('/verify-otp', 'guest.check-otp');
    Route::post('/verify-otp', [ForgetPasswordController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::get('/reset-password', [ForgetPasswordController::class, 'showReset']);
    Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| 2. Logged in, email NOT verified yet
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [EmailVerificationOtpController::class, 'show'])->name('verification.notice');
    Route::post('/email/verify/send', [EmailVerificationOtpController::class, 'send'])
        ->middleware('throttle:3,1')->name('verification.send');
    Route::post('/email/verify', [EmailVerificationOtpController::class, 'verify'])
        ->middleware('throttle:10,1')->name('verification.verify');
});

/*
|--------------------------------------------------------------------------
| 3. Verified, but no shop yet: store setup wizard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

/*
|--------------------------------------------------------------------------
| 4. The app: verified AND has a shop
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', EnsureHasBusiness::class])->group(function () {
    Route::get('/', [AuthController::class, 'welcome'])->name('welcome');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/setting', [SettingsController::class, 'index'])->name('settings');

    Route::get('/create-customer', [CustomerController::class, 'create'])->name('create-customer');
    Route::post('/create-customer', [CustomerController::class, 'store'])->name('create-customer.post');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/search-customers', [CustomerController::class, 'search'])->name('search-customers');
    Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit-customer');
    Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update-customer');
    Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('delete-customer');
    Route::get('/customers/{id}', [LedgerController::class, 'show'])->name('customers.show');
Route::post('/customers/{id}/credit', [LedgerController::class, 'credit'])->middleware('throttle:30,1')->name('customers.credit');
Route::post('/customers/{id}/payments', [LedgerController::class, 'payment'])->middleware('throttle:30,1')->name('customers.payments');
Route::post('/transactions/{id}/reverse', [LedgerController::class, 'reverse'])->middleware('throttle:30,1')->name('transactions.reverse');
Route::get('/', [DashboardController::class, 'index'])->name('welcome');
Route::get('/reports/outstanding', [ReportController::class, 'outstanding'])->name('reports.outstanding');
Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
});