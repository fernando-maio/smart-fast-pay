<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/auth/me', [AuthController::class, 'me'])->name('me');

    Route::group(['prefix' => 'payments'], function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
    });
});
