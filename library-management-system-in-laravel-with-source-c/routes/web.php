<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/counter', [CounterController::class, 'index'])->name('counter');
    Route::post('/counter/borrow', [CounterController::class, 'borrow'])->name('counter.borrow');
    Route::post('/counter/return', [CounterController::class, 'giveBack'])->name('counter.return');

    Route::get('/books/labels', [BookController::class, 'labels'])->name('books.labels');
    Route::resource('books', BookController::class)->except('show');
    Route::resource('members', MemberController::class)->except('show');
    Route::get('/members/{member}/card', [MemberController::class, 'card'])->name('members.card');

    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
});
