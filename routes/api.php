<?php

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ViralLoopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public V1 API - Accessible by storefront guest browsers
Route::middleware(['throttle:60,1'])->prefix('v1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
});

// Protected V1 API - Requires authentication (Sanctum)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->group(function () {
    // Viral Loop & Rewards
    Route::post('/viral/track-share', [ViralLoopController::class, 'trackShare']);
    Route::get('/viral/status', [ViralLoopController::class, 'getStatus']);

    Route::post('/orders', [OrderController::class, 'store']);
});
