<?php

use App\Http\Controllers\Admin\SatuanController;
use App\Http\Controllers\Admin\BahanMasukController;
use App\Http\Controllers\Api\BahanApiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BahanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengajuanController;
 
require __DIR__ . '/auth.php';
 
Route::get('/', function () {
    return view('auth.login');
});
 
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
 
    // Fitur Pengajuan (Akses Berbeda-beda)
    Route::middleware('role:mahasiswa,admin,ketua_jurusan')->group(function () {
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    });

    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    });
    
    // Fitur Admin & Approval
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
 
        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::put('/satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');

        // Bahan Masuk Routes
        Route::get('/bahan-masuk', [BahanMasukController::class, 'index'])->name('bahan-masuk.index');
        Route::post('/bahan-masuk', [BahanMasukController::class, 'store'])->name('bahan-masuk.store');
        Route::delete('/bahan-masuk/{masuk}', [BahanMasukController::class, 'destroy'])->name('bahan-masuk.destroy');

        // Approval Routes
        Route::post('/pengajuan/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('pengajuan.approve');
        Route::post('/pengajuan/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('pengajuan.reject');
    });
 
    Route::get('/api/bahan/search', [BahanApiController::class, 'search'])->middleware('auth')->name('api.bahan.search');
    Route::get('/bahan', [BahanController::class, 'index'])->middleware('role:admin,mahasiswa,ketua_jurusan')->name('bahan');
    
    Route::middleware('role:admin')->group(function () {
        Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
        Route::put('/bahan/{bahan}', [BahanController::class, 'update'])->name('bahan.update');
        Route::delete('/bahan/{bahan}', [BahanController::class, 'destroy'])->name('bahan.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
