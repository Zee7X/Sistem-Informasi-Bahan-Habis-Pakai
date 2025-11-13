<?php

use App\Http\Controllers\Admin\SatuanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BahanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/users', [AdminController::class, 'users'])->name('users');

        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::put('/satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');
    });

    Route::get('/bahan', [BahanController::class, 'index'])->name('bahan');
    Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
    Route::put('/bahan/{bahan}', [BahanController::class, 'update'])->name('bahan.update');
    Route::delete('/bahan/{bahan}', [BahanController::class, 'destroy'])->name('bahan.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
