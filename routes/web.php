<?php

use App\Http\Controllers\Admin\BahanMasukController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\ModulPraktikumController;
use App\Http\Controllers\Admin\SatuanController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\BahanApiController;
use App\Http\Controllers\Api\ModulApiController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KetuaJurusan\BahanMasukController as KjurBahanMasukController;
use App\Http\Controllers\KetuaJurusan\LaporanController as KjurLaporanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', fn () => redirect('/login'));


// ============================================================
// Authenticated Routes
// ============================================================

Route::middleware('auth')->group(function () {

    // Dashboard (unified, logic dibranch per role di controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ────────────────────────────────────────────────────────
    // ADMIN (Laboran)
    // ────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Master: Satuan
        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::put('/satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');

        // Master: Bahan
        Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');
        Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
        Route::put('/bahan/{bahan}', [BahanController::class, 'update'])->name('bahan.update');
        Route::delete('/bahan/{bahan}', [BahanController::class, 'destroy'])->name('bahan.destroy');

        // Master: Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Bahan Masuk (Stok Masuk)
        Route::get('/bahan-masuk', [BahanMasukController::class, 'index'])->name('bahan-masuk.index');
        Route::post('/bahan-masuk', [BahanMasukController::class, 'store'])->name('bahan-masuk.store');
        Route::delete('/bahan-masuk/{masuk}', [BahanMasukController::class, 'destroy'])->name('bahan-masuk.destroy');

        // Modul Praktikum
        Route::resource('/modul-praktikum', ModulPraktikumController::class)
             ->except(['show'])
             ->names([
                'index'   => 'modul-praktikum.index',
                'create'  => 'modul-praktikum.create',
                'store'   => 'modul-praktikum.store',
                'edit'    => 'modul-praktikum.edit',
                'update'  => 'modul-praktikum.update',
                'destroy' => 'modul-praktikum.destroy',
             ]);
        Route::post('/modul-praktikum/{modulPraktikum}/items', [ModulPraktikumController::class, 'storeItem'])
             ->name('modul-praktikum.items.store');
        Route::delete('/modul-praktikum/{modulPraktikum}/items/{item}', [ModulPraktikumController::class, 'destroyItem'])
             ->name('modul-praktikum.items.destroy');

        // Stock Opname
        Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
        Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');

        // Transaksi Pengajuan (admin view + state transitions)
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('pengajuan.approve');
        Route::post('/pengajuan/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('pengajuan.reject');
        Route::post('/pengajuan/{pengajuan}/complete', [PengajuanController::class, 'complete'])->name('pengajuan.complete');

        // Laporan
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    });

    // ────────────────────────────────────────────────────────
    // MAHASISWA
    // ────────────────────────────────────────────────────────
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        // Katalog (read only)
        Route::get('/katalog', [App\Http\Controllers\Mahasiswa\KatalogController::class, 'index'])->name('katalog.index');


        // Pengajuan saya
        Route::get('/pengajuan', [PengajuanController::class, 'myIndex'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'showMahasiswa'])->name('pengajuan.show');
    });

    // ────────────────────────────────────────────────────────
    // KETUA JURUSAN
    // ────────────────────────────────────────────────────────
    Route::middleware('role:ketua_jurusan')->prefix('kjur')->name('kjur.')->group(function () {

        // View-only: Transaksi & Bahan
        Route::get('/transaksi', [PengajuanController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{pengajuan}', [PengajuanController::class, 'show'])->name('transaksi.show');
        Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');

        // Approval Belanja
        Route::get('/bahan-masuk', [KjurBahanMasukController::class, 'index'])->name('bahan-masuk.index');
        Route::post('/bahan-masuk/{masuk}/approve', [KjurBahanMasukController::class, 'approve'])->name('bahan-masuk.approve');

        // Laporan Rekapitulasi
        Route::get('/laporan/rekap', [KjurLaporanController::class, 'rekap'])->name('laporan.rekap');
    });

    // ────────────────────────────────────────────────────────
    // Internal API (auth only, semua role)
    // ────────────────────────────────────────────────────────
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/bahan/search', [BahanApiController::class, 'search'])->name('bahan.search');
        Route::get('/modul/{modul}/items', [ModulApiController::class, 'items'])->name('modul.items');
    });
});
