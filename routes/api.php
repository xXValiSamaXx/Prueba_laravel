<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);

Route::get('/orders/report/pdf', [OrderReportController::class, 'pdf']);
Route::get('/orders/report/excel', [OrderReportController::class, 'excel']);
Route::apiResource('orders', OrderController::class);
