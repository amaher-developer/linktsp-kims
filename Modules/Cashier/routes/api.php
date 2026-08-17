<?php

use Illuminate\Support\Facades\Route;
use Modules\Cashier\Http\Controllers\CustomerLookupController;
use Modules\Cashier\Http\Controllers\InvoiceController;
use Modules\Cashier\Http\Controllers\LoyaltyAwardController;

Route::prefix('cashier')
    ->middleware(['auth:sanctum', 'api.cashier'])
    ->name('cashier.')
    ->group(function () {
        Route::post('identify-customer', [CustomerLookupController::class, 'store'])->name('identify-customer');
        Route::get('invoices/{invoiceNumber}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('loyalty/award', [LoyaltyAwardController::class, 'store'])->name('loyalty.award');
    });
