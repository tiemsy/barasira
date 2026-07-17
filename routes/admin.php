<?php

// Dashboard admin

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])->middleware('role:admin')->name('admin.dashboard');
Route::get('/logs', [LogController::class, 'index'])->middleware('role:superadmin')->name('admin.logs.index');
Route::post('/users/{user}/impersonate', [ImpersonationController::class, 'start'])
    ->middleware('role:superadmin')
    ->name('admin.users.impersonate');
Route::resource('/users', UserController::class)
    ->except(['show'])
    ->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
Route::resource('/services', ServiceController::class)
    ->except(['show'])
    ->names([
        'index' => 'admin.services.index',
        'create' => 'admin.services.create',
        'store' => 'admin.services.store',
        'edit' => 'admin.services.edit',
        'update' => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ]);
