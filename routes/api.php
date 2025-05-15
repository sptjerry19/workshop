<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return 'OK';
});
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Payment routes
    Route::post('/payment/create', [PaymentController::class, 'createPayment']);
});

// Payment webhook routes
Route::post('/payment/ipn', [PaymentController::class, 'handleIPN'])->name('payment.ipn');
Route::get('/payment/redirect', [PaymentController::class, 'handleRedirect'])->name('payment.redirect');