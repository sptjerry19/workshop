<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Models\Product;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MomoPaymentController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\ToppingController;

Route::get('/', function () {
    return 'OK';
});



Route::get('/orders/histories', function () {
    return 'OK1111';
});


// Public routes
// auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

// product
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/all', [ProductController::class, 'indexAll']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/contact', [AuthController::class, 'contact']);

// news
Route::get('/news', [NewController::class, 'index']);
Route::get('/news/{id}', [NewController::class, 'show']);


// Protected routes
Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Payment routes
    // Route::post('/payment/create', [PaymentController::class, 'createPayment']);

    // Product routes
    // Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // update user
    Route::put('/user/update', [AuthController::class, 'update']);

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
});

// Payment routes
Route::post('/payment/create', [PaymentController::class, 'createPayment']);
Route::get('/payment/{paymentId}/qr', [PaymentController::class, 'getQRCode']);
Route::get('/payment/{paymentId}/status', [PaymentController::class, 'getPaymentStatus']);
// payment momo
// Momo Payment Routes
Route::prefix('payment/momo')->group(function () {
    Route::post('/create', [MomoPaymentController::class, 'createPayment'])->name('momo.payment.create');
    Route::get('/{paymentId}/qr', [MomoPaymentController::class, 'getQRCode'])->name('momo.payment.qr');
    Route::get('/{paymentId}/status', [MomoPaymentController::class, 'checkStatus'])->name('momo.payment.status');
    Route::get('/return', [MomoPaymentController::class, 'return'])->name('momo.payment.return');
    Route::post('/ipn', [MomoPaymentController::class, 'ipn'])->name('momo.payment.ipn');
});

// Payment webhook routes
Route::post('/payment/ipn', [PaymentController::class, 'handleIPN'])->name('payment.ipn');
Route::get('/payment/redirect', [PaymentController::class, 'handleRedirect'])->name('payment.redirect');

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

// Order Routes
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{order}', [OrderController::class, 'show']);
    Route::get('/histories', [OrderController::class, 'getHistories']);
    Route::post('/', [OrderController::class, 'store']);
    // Route::get('/{id}', [OrderController::class, 'show']);
    // Route::get('/generateQr', [OrderController::class, 'generateQr']);
    Route::post('/generate', [OrderController::class, 'generateQr']);
});

// Admin routes
// middleware(['auth:sanctum', 'admin'])->
Route::middleware(['jwt.auth'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // Orders
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus']);
    });

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // News
    Route::get('/news', [NewController::class, 'index']);
    Route::post('/news', [NewController::class, 'store']);
    Route::get('/news/{news}', [NewController::class, 'show']);
    Route::put('/news/{news}', [NewController::class, 'update']);
    Route::delete('/news/{news}', [NewController::class, 'destroy']);

    // Users
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::get('/users/{user}', [AdminController::class, 'showUser']);
    Route::put('/users/{user}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
});

// Google OAuth Routes
Route::middleware('web')->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

// Chat routes
Route::get('/messages', [MessageController::class, 'index']);
Route::get('/messages/users', [MessageController::class, 'getUsers']);
Route::post('/messages', [MessageController::class, 'store']);
Route::post('/messages/{from_sender_id}/reply', [MessageController::class, 'adminReply'])->middleware('jwt.auth');

// Options routes
Route::get('/options', [OptionController::class, 'index']);

// Toppings routes
Route::get('/toppings', [ToppingController::class, 'index']);