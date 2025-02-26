<?php

use App\Http\Controllers\api\v1\PostController;
use Illuminate\Support\Facades\Route;

/* API V1 routes */
/* ====================================================================== */
Route::prefix('v1')->group(function () {
    Route::apiResource('posts', PostController::class)->only(['index'])->middleware('throttle:60,1');
});
