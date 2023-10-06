<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\JenisController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\Permissions\PermissionController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'master'], function () {
        Route::resource('jenis', JenisController::class);
        Route::resource('karyawan', KaryawanController::class);
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('user', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('user/{user}', [ProfileController::class, 'update'])->name('profile.update');

        Route::put('password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    //Roles & Permissions
    Route::prefix('roles')->group(function () {
        Route::get('', [RoleController::class, 'index'])->name('roles.index');
        Route::post('store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::prefix('permissions')->group(function () {
        Route::get('', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    });
});
