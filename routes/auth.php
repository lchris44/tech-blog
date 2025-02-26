<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/* Authentication routes */
/* ====================================================================== */
Route::middleware('guest')->group(function () {

    /* Login routes */
    /* ====================================================================== */
    Route::name('auth.login.')->group(function () {
        Route::get('login', [LoginController::class, 'index'])
            ->name('index');
        Route::post('login', [LoginController::class, 'store'])
            ->name('store');
    });

    /* Register routes */
    /* ====================================================================== */
    Route::name('auth.register.')->group(function () {
        Route::get('register', [RegisterController::class, 'index'])
            ->name('index');
        Route::post('register', [RegisterController::class, 'store'])
            ->name('store');
    });
});

/* Logout routes */
/* ====================================================================== */
Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'index'])
        ->name('auth.logout');
});
