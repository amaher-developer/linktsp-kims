<?php

use Illuminate\Support\Facades\Route;
use Modules\Ordering\Http\Controllers\AuthController;
use Modules\Ordering\Http\Controllers\CartController;
use Modules\Ordering\Http\Controllers\CartItemController;
use Modules\Ordering\Http\Controllers\CheckoutController;
use Modules\Ordering\Http\Controllers\CustomerAuthController;
use Modules\Ordering\Http\Controllers\OrderController;
use Modules\Ordering\Http\Controllers\ProfileController;

Route::post('auth/customer/login', [CustomerAuthController::class, 'login'])->name('auth.customer.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::middleware('api.customer')->group(function () {
        Route::get('me', [ProfileController::class, 'show'])->name('me');

        Route::get('cart', [CartController::class, 'show'])->name('cart.show');
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::post('cart/items', [CartItemController::class, 'store'])->name('cart.items.store');
        Route::put('cart/items/{cartItem}', [CartItemController::class, 'update'])->name('cart.items.update');
        Route::delete('cart/items/{cartItem}', [CartItemController::class, 'destroy'])->name('cart.items.destroy');
        Route::post('cart/checkout', [CheckoutController::class, 'store'])->name('cart.checkout');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
});
