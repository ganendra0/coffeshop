<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReviewController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Group untuk API v1 misalnya
Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class); // apiResource untuk API
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('deliveries', DeliveryController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('reviews', ReviewController::class);

    // Contoh route custom jika diperlukan, misal untuk Order
    // Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus']);
});