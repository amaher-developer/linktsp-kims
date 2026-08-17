<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AuthenticatedSessionController;
use Modules\Admin\Http\Controllers\BranchController;
use Modules\Admin\Http\Controllers\CategoryController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\IntegrationController;
use Modules\Admin\Http\Controllers\LoyaltyRuleController;
use Modules\Admin\Http\Controllers\OptionController;
use Modules\Admin\Http\Controllers\OptionGroupController;
use Modules\Admin\Http\Controllers\OrderController;
use Modules\Admin\Http\Controllers\PasswordController;
use Modules\Admin\Http\Controllers\ProductController;
use Modules\Admin\Http\Controllers\ProfileController;
use Modules\Admin\Http\Controllers\RewardController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\StaffController;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::post('locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED, true), 404);

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

Route::prefix('admin')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware('auth:staff')->group(function () {
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });

    Route::middleware(['auth:staff', 'staff.manager'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::name('admin.')->group(function () {
            Route::resource('branches', BranchController::class)->except('show');
            Route::resource('categories', CategoryController::class)->except('show');

            Route::resource('products', ProductController::class)->except('show');

            Route::resource('option-groups', OptionGroupController::class)->except('show');
            Route::post('option-groups/{optionGroup}/options', [OptionController::class, 'store'])->name('option-groups.options.store');
            Route::put('option-groups/{optionGroup}/options/{option}', [OptionController::class, 'update'])->name('option-groups.options.update');
            Route::delete('option-groups/{optionGroup}/options/{option}', [OptionController::class, 'destroy'])->name('option-groups.options.destroy');

            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

            Route::resource('loyalty-rules', LoyaltyRuleController::class)->except('show');
            Route::resource('rewards', RewardController::class)->except('show');

            Route::resource('staff', StaffController::class)->except('show')->parameters(['staff' => 'staffMember']);
            Route::resource('roles', RoleController::class)->except('show');

            Route::get('integrations', [IntegrationController::class, 'edit'])->name('integrations.edit');
            Route::put('integrations', [IntegrationController::class, 'update'])->name('integrations.update');
        });
    });
});
