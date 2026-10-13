<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\FcebookAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SettingsController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

    Route::get('/auth/facebook/redirect', [FcebookAuthController::class, 'redirect'])->name('facebook.redirect');
    Route::get('/auth/facebook/callback', [FcebookAuthController::class, 'callback'])->name('facebook.callback');

    Route::view("/login", "auth.login")->name("login");
    Route::view("/register", "auth.register")->name("register");
    Route::view("/forget-password", "auth.forget")->name("forget-password");
    Route::view('/verify-otp', 'guest.check-otp');

    Route::post("/register", [AuthController::class, "register"])->name("register.post");
    Route::post("/login", [AuthController::class, "login"])->name("login.post");
    Route::post("/send-otp", [ForgetPasswordController::class, "sendOtp"]);
    Route::post("/verify-otp", [ForgetPasswordController::class, "verifyOtp"]);
});

Route::post("/reset-password", [ForgetPasswordController::class, "resetPassword"]);
Route::get('/route-test-123', function () {
    return 'YES WEB.PHP IS LOADED';
});

Route::view('/reset-password', 'guest.resetpw');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/', [AuthController::class, 'welcome'])->name('welcome');
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/setting', [SettingsController::class, 'index'])->name('settings');
    Route::get('/create-customer', [CustomerController::class, 'create'])->name('create-customer');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/search-customers', [CustomerController::class, 'search'])->name('search-customers');
    Route::post('/create-customer', [CustomerController::class, 'store'])->name('create-customer.post');
    Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit-customer');
    Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update-customer');
    Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('delete-customer');

});
