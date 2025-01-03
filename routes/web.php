<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JurusanController;
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
        // Jurusan Management
        Route::prefix('jurusan')->group(function () {
            Route::get('/', [JurusanController::class, 'index'])->name('jurusan.index');
            Route::post('/', [JurusanController::class, 'store'])->name('jurusan.store');
            Route::get('/{jurusan}/edit', [JurusanController::class, 'edit'])->name('jurusan.edit');
            Route::put('/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
            Route::delete('/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');
        });

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
            Route::get('/check-affected-students', [TagihanController::class, 'checkAffectedStudents'])->name('check-affected-students');
            Route::post('/bulk-store', [TagihanController::class, 'bulkStore'])->name('bulk-store');

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
            Route::get('/export', [PembayaranController::class, 'export'])->name('export');
        });
    });

    // Mahasiswa Routes
    Route::middleware(['role:siswa'])->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/tagihan', [UserTagihanController::class, 'index'])->name('tagihan.index');
            Route::post('/tagihan/{tagihan}/bayar', [UserTagihanController::class, 'bayar'])->name('tagihan.bayar');
            Route::get('/tagihan/check-status/{kodeTransaksi}', [UserTagihanController::class, 'checkStatus'])->name('tagihan.check-status');
            Route::post('/tagihan/update-status/{kodeTransaksi}', [UserTagihanController::class, 'updateStatus'])->name('tagihan.update-status');
            Route::post('/tagihan/notification', [UserTagihanController::class, 'notification'])->name('tagihan.notification');
        });
    });
});