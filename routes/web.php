<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\User\UserTagihanController;

// Public Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// Routes that require authentication
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes accessible to both roles
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        // Users Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::post('/', [UserController::class, 'store'])->name('users.store');
            Route::patch('/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Payment Types Management
        Route::prefix('jenis-pembayaran')->group(function () {
            Route::get('/', [JenisPembayaranController::class, 'index'])->name('jenis-pembayaran.index');
            Route::post('/', [JenisPembayaranController::class, 'store'])->name('jenis-pembayaran.store');
            Route::put('/{jenisPembayaran}', [JenisPembayaranController::class, 'update'])->name('jenis-pembayaran.update');
            Route::delete('/{jenisPembayaran}', [JenisPembayaranController::class, 'destroy'])->name('jenis-pembayaran.destroy');
        });

        // Bills Management
        Route::prefix('tagihan')->name('tagihan.')->group(function () {
            Route::get('/', [TagihanController::class, 'index'])->name('index');
            Route::get('/{user}/detail', [TagihanController::class, 'getDetail'])->name('detail');
            Route::get('/jenis-pembayaran/{jenisPembayaran}', [TagihanController::class, 'getJenisPembayaran'])->name('jenis-pembayaran.detail');
            Route::get('/{user}/statistics', [TagihanController::class, 'getStatistics'])->name('statistics');
            Route::post('/{user}', [TagihanController::class, 'store'])->name('store');
            Route::put('/{user}/{tagihan}', [TagihanController::class, 'update'])->name('update');
            Route::delete('/{user}/{tagihan}', [TagihanController::class, 'destroy'])->name('destroy');
        });

        // Payments Management
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', [PembayaranController::class, 'index'])->name('index');
            Route::get('/search', [PembayaranController::class, 'search'])->name('search');
            Route::get('/{pembayaran}', [PembayaranController::class, 'show'])->name('show');
            Route::get('/bukti/{pembayaran}', [PembayaranController::class, 'showBukti'])->name('bukti');
            Route::post('/{pembayaran}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('verifikasi');
        });

        // Payment History
        Route::prefix('riwayat')->name('riwayat.')->group(function () {
            Route::get('/', [RiwayatController::class, 'index'])->name('index');
            Route::get('/{pembayaran}', [RiwayatController::class, 'show'])->name('show');
            Route::get('/bukti/{pembayaran}', [RiwayatController::class, 'showBukti'])->name('bukti');
        });
    });

    // Mahasiswa Routes
    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/tagihan', [UserTagihanController::class, 'index'])->name('tagihan.index');
            Route::post('/tagihan/{tagihan}/pembayaran', [UserTagihanController::class, 'bayar'])->name('tagihan.pembayaran');
            Route::put('/tagihan/{tagihan}/pembayaran/{pembayaran}', [UserTagihanController::class, 'updatePembayaran'])->name('tagihan.pembayaran.update');
        });
    });
});