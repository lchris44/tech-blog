<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/* Auth routes */
/* ====================================================================== */
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'index'])
        ->name('login');

    Route::post('login', [LoginController::class, 'store'])
        ->name('login.store');

    Route::get('register', [RegisterController::class, 'index'])
        ->name('login.register');

    Route::post('register', [RegisterController::class, 'store'])
        ->name('login.register');
});

/* Logout routes */
/* ====================================================================== */
Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'index'])
        ->name('logout');
});