<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Models\Product;

Route::get('/', function () {
    return 'OK';
});
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

//test
Route::post('/products', [ProductController::class, 'store']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Payment routes
    Route::post('/payment/create', [PaymentController::class, 'createPayment']);

    // Product routes
    // Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});

// Payment webhook routes
Route::post('/payment/ipn', [PaymentController::class, 'handleIPN'])->name('payment.ipn');
Route::get('/payment/redirect', [PaymentController::class, 'handleRedirect'])->name('payment.redirect');

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
