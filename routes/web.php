<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('static.about');
})->name('static.about');

Route::get('/contact', function () {
    return view('static.contact');
})->name('static.contact');

Route::get('/services', function () {
    return view('static.services');
})->name('static.services');
