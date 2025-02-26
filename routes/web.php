<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

/* Public routes */
/* ====================================================================== */
Route::get('/', [BlogController::class, 'index'])
    ->name('index');

Route::get('/post/{post}', [BlogController::class, 'show'])
    ->name('show');

Route::post('/change-language', [LanguageController::class, 'changeLanguage'])
    ->name('language.change');

/* Authenticated Routes */
/* ====================================================================== */
Route::middleware(['auth'])->group(function () {

    /* Dashboard routes */
    /* ==================================================== */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /* Tags routes */
    /* ==================================================== */
    Route::resource('tags', TagController::class);

    /* Posts routes */
    /* ==================================================== */
    Route::resource('posts', PostController::class);

    /* Uploads routes */
    /* ==================================================== */
    Route::post('/posts/{post}/upload-cover', [PostController::class, 'uploadCover'])->name('posts.upload-cover');
    Route::delete('/posts/{post}/remove-cover', [PostController::class, 'removeCover'])->name('posts.removeCover');

    /* Users routes */
    /* ==================================================== */
    Route::resource('users', UserController::class);
});
