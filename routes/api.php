<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [UserController::class, 'index']);
    Route::get('balances', [BalanceController::class, 'index']);

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('recent', [TransactionController::class, 'getRecent']);
    });
});
