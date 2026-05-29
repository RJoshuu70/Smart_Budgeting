<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Guest routes (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/',          [AuthController::class, 'showLogin']);
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

// Authenticated routes (sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/transactions',        [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions',       [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{id}',   [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}',[TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/budgets',       [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets',      [BudgetController::class, 'store'])->name('budgets.store');
    Route::put('/budgets/{id}',  [BudgetController::class, 'update'])->name('budgets.update');

    Route::get('/reports',      [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/data', [ReportController::class, 'data'])->name('reports.data');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});