<?php

// Dashboard admin

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])->middleware('role:admin')->name('admin.dashboard');
Route::get('/logs', [LogController::class, 'index'])->middleware('role:superadmin')->name('admin.logs.index');
Route::delete('/logs', [LogController::class, 'purge'])->middleware('role:superadmin')->name('admin.logs.purge');
Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents.index');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('admin.documents.download');
Route::patch('/documents/{document}/review', [DocumentController::class, 'review'])->name('admin.documents.review');
Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('admin.documents.destroy');
Route::prefix('exports')->controller(ExportController::class)->group(function () {
    Route::get('/users', 'users')->name('admin.exports.users');
    Route::get('/services', 'services')->name('admin.exports.services');
    Route::get('/missions', 'missions')->name('admin.exports.missions');
    Route::get('/partners', 'partners')->name('admin.exports.partners');
});
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
Route::resource('/partners', PartnerController::class)
    ->except(['show'])
    ->names([
        'index' => 'admin.partners.index', 'create' => 'admin.partners.create',
        'store' => 'admin.partners.store', 'edit' => 'admin.partners.edit',
        'update' => 'admin.partners.update', 'destroy' => 'admin.partners.destroy',
    ]);
