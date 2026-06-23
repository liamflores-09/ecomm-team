<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\BrandCatalogController;
use App\Http\Controllers\AdminBrandController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/posting-procedure', [DashboardController::class, 'postingProcedure'])->name('posting-procedure');
    Route::get('/data-gathering', [DashboardController::class, 'dataGathering'])->name('data-gathering');
    Route::get('/ecommerce-requirements', [DashboardController::class, 'ecommerceRequirements'])->name('ecommerce-requirements');
    Route::get('/price-calculator', [DashboardController::class, 'priceCalculator'])->name('price-calculator');
    Route::get('/end-of-day', [DailyLogController::class, 'index'])->name('end-of-day');
    Route::post('/daily-logs', [DailyLogController::class, 'store'])->name('daily-logs.store');
    Route::put('/daily-logs/{dailyLog}', [DailyLogController::class, 'update'])->name('daily-logs.update');
    Route::delete('/daily-logs/{dailyLog}', [DailyLogController::class, 'destroy'])->name('daily-logs.destroy');
    Route::get('/daily-logs/history', [DailyLogController::class, 'history'])->name('daily-logs.history');
    Route::get('/important-links', [DashboardController::class, 'importantLinks'])->name('important-links');
    Route::get('/team', [DashboardController::class, 'team'])->name('team');

    // Brand Catalogs — all authenticated users can browse
    Route::get('/brand-catalogs', [BrandCatalogController::class, 'index'])->name('brand-catalogs');

    // Brand Catalogs CRUD — admin (manager) and researcher only
    Route::middleware(['catalog.manager'])->group(function () {
        Route::post('/brand-catalogs', [BrandCatalogController::class, 'store'])->name('brand-catalogs.store');
        Route::put('/brand-catalogs/{catalog}', [BrandCatalogController::class, 'update'])->name('brand-catalogs.update');
        Route::delete('/brand-catalogs/{catalog}', [BrandCatalogController::class, 'destroy'])->name('brand-catalogs.destroy');
    });

    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::get('/daily-logs', [AdminController::class, 'dailyLogs'])->name('admin.daily-logs');
        Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/brands', [AdminBrandController::class, 'index'])->name('admin.brands');
        Route::post('/brands', [AdminBrandController::class, 'store'])->name('admin.brands.store');
        Route::put('/brands/{brand}', [AdminBrandController::class, 'update'])->name('admin.brands.update');
        Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy'])->name('admin.brands.destroy');
    });
});
