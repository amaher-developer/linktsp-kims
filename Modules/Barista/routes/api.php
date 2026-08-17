<?php

use Illuminate\Support\Facades\Route;
use Modules\Barista\Http\Controllers\OrderController;

Route::prefix('barista')
    ->middleware(['auth:sanctum', 'api.barista'])
    ->name('barista.')
    ->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    });
