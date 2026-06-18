<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/posting-procedure', [DashboardController::class, 'postingProcedure'])->name('posting-procedure');
    Route::get('/data-gathering', [DashboardController::class, 'dataGathering'])->name('data-gathering');
    Route::get('/ecommerce-requirements', [DashboardController::class, 'ecommerceRequirements'])->name('ecommerce-requirements');
    Route::get('/price-calculator', [DashboardController::class, 'priceCalculator'])->name('price-calculator');
    Route::get('/end-of-day', [DashboardController::class, 'endOfDay'])->name('end-of-day');
    Route::get('/important-links', [DashboardController::class, 'importantLinks'])->name('important-links');

    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    });
});
