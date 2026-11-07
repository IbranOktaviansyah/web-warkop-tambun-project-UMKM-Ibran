<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokHistoryController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ResepController;

// === DEFAULT ===
Route::get('/', fn() => redirect()->route('dashboard'));

// === LOGIN ===
Route::middleware(['guest', 'prevent-back-history'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// === FITUR SETELAH LOGIN ===
Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    // === DASHBOARD (semua role) ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === PEMESANAN (semua role) ===
    Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::get('/pemesanan/create', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan/store', [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::get('/pemesanan/show/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');
    Route::get('/pemesanan/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');
    Route::post('/pemesanan/check-stok', [PemesananController::class, 'checkStok'])->name('pemesanan.checkStok');

    // === STOK (semua bisa lihat, tapi CRUD hanya admin) ===
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/stok/create', [StokController::class, 'create'])->name('stok.create');
        Route::post('/stok/baru', [StokController::class, 'storeBaru'])->name('stok.store.baru');
        Route::post('/stok/lama', [StokController::class, 'storeLama'])->name('stok.store.lama');
        Route::get('/stok/{id}/edit', [StokController::class, 'edit'])->name('stok.edit');
        Route::put('/stok/{id}', [StokController::class, 'update'])->name('stok.update');
        Route::delete('/stok/{id}', [StokController::class, 'destroy'])->name('stok.destroy');
        Route::get('/stok/history', [StokHistoryController::class, 'index'])->name('stok.history');
    });

    // === MENU ===
    // Kasir & Admin boleh melihat daftar menu
    Route::get('/menu', [MenuController::class, 'index'])
        ->middleware('role:admin,kasir')
        ->name('menu.index');

    // Hanya Admin yang boleh CRUD menu
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu/store', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/edit/{id_produk}', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/update/{id_produk}', [MenuController::class, 'update'])->name('menu.update');
        Route::get('/menu/delete/{id_produk}', [MenuController::class, 'destroy'])->name('menu.delete');
    });

    // === RESEP (hanya ADMIN) ===
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/resep', [ResepController::class, 'index'])->name('resep.index');
        Route::get('/resep/create', [ResepController::class, 'create'])->name('resep.create');
        Route::post('/resep/store', [ResepController::class, 'store'])->name('resep.store');
        Route::delete('/resep/{id}', [ResepController::class, 'destroy'])->name('resep.destroy');
    });

    // === LAPORAN KEUANGAN (hanya ADMIN) ===
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPDF'])->name('laporan.exportPDF');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
    });

    // === STAFF (hanya ADMIN) ===
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::post('/staff/{id}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggleStatus');
    });

    // === LOGOUT (semua role) ===
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
