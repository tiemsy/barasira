<?php

// Dashboard admin

use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])->middleware('role:admin')->name('admin.dashboard');
