<?php

use Illuminate\Support\Facades\Route;
use Modules\Staff\Http\Controllers\StaffAuthController;

Route::post('auth/staff/login', [StaffAuthController::class, 'login'])->name('auth.staff.login');
