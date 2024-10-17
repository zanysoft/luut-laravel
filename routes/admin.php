<?php

use App\Http\Controllers\Admin\ActionController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\BusinessTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PackagesController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->as('admin.')->group(function () {
    Route::auth();

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::any('logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::any('ajax-request/{table}/{field}', [\App\Http\Controllers\Admin\AjaxController::class, 'make']);
        Route::match(['GET', 'POST'], 'clear-cache', [ActionController::class, 'clearCache'])->name('clear-cache');

        Route::get('roles/reset-permissions', [RolesController::class, 'resetPermissions'])->name('roles.reset-permissions');

        Route::resource('roles', RolesController::class);
        Route::resource('users', UsersController::class);
        Route::resource('business-types', BusinessTypeController::class);
        Route::resource('packages', PackagesController::class);

        Route::prefix('/settings')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('settings.index');
            Route::get('/{key}', [SettingController::class, 'edit'])->name('settings.edit');
            Route::post('/update', [SettingController::class, 'update'])->name('settings.update');
            Route::post('/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');
        });
        Route::get('media', MediaController::class)->name('media');
    });
});
