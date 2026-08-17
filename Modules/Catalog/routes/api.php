<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\BranchController;
use Modules\Catalog\Http\Controllers\CategoryController;
use Modules\Catalog\Http\Controllers\ProductController;

Route::middleware(['auth:sanctum', 'api.customer'])
    ->group(function () {
        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('branches/{branch}', [BranchController::class, 'show'])->name('branches.show');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });
