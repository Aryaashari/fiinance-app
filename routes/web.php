<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BudgetAllocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

    // Budget Allocations
    Route::resource('budget-allocations', BudgetAllocationController::class)->except(['create', 'edit', 'show']);
    Route::post('/budget-allocations/bulk', [BudgetAllocationController::class, 'bulkUpdate'])->name('budget-allocations.bulk');

    // Transactions
    Route::resource('transactions', TransactionController::class)->except(['create', 'edit', 'show']);

    // Wallets
    Route::resource('wallets', WalletController::class)->except(['create', 'edit', 'show']);

    // Transfers
    Route::resource('transfers', TransferController::class)->only(['index', 'store', 'destroy']);

    // Savings Goals
    Route::resource('savings-goals', \App\Http\Controllers\SavingsGoalController::class)->only(['index', 'store', 'destroy']);

    // Investments
    Route::resource('investments', \App\Http\Controllers\InvestmentController::class)->only(['index', 'store', 'destroy']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{type}/{format}', [ReportController::class, 'export'])->name('reports.export');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
