<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\FcebookAuthController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

    Route::get('/auth/facebook/redirect', [FcebookAuthController::class, 'redirect'])->name('facebook.redirect');
    Route::get('/auth/facebook/callback', [FcebookAuthController::class, 'callback'])->name('facebook.callback');

    Route::view("/login", "auth.login")->name("login");
    Route::view("/register", "auth.register")->name("register");
    Route::view("/forget-password", "auth.forget")->name("forget-password");

    Route::post("/register", [AuthController::class, "register"])->name("register.post");
    Route::post("/login", [AuthController::class, "login"])->name("login.post");
    Route::post("/forget-password", [AuthController::class, "resetPassword"])->name("reset-password.post");
    
    });
    
    
    Route::middleware('auth')->group(function () {
        Route::get('/', [AuthController::class, 'welcome'])->name('welcome');
        Route::post("/logout", [AuthController::class, "logout"])->name("logout");
});




