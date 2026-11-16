<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('medicines', MedicineController::class)->except('show');

    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/pos/lookup/{barcode}', [PosController::class, 'lookup']);
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
});
