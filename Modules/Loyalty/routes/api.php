<?php

use Illuminate\Support\Facades\Route;
use Modules\Loyalty\Http\Controllers\LoyaltyController;

Route::middleware(['auth:sanctum', 'api.customer'])
    ->group(function () {
        Route::get('loyalty', [LoyaltyController::class, 'show'])->name('loyalty.show');
        Route::get('loyalty/transactions', [LoyaltyController::class, 'transactions'])->name('loyalty.transactions');
    });
