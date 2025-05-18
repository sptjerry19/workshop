<?php

use Illuminate\Support\Facades\Route;

// Tất cả các routes sẽ được xử lý bởi Vue Router
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
