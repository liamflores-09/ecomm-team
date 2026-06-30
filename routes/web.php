<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\BrandCatalogController;
use App\Http\Controllers\AdminBrandController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CalendarController;
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
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/posting-procedure', [DashboardController::class, 'postingProcedure'])->name('posting-procedure');
    Route::get('/data-gathering', [DashboardController::class, 'dataGathering'])->name('data-gathering');
    Route::get('/ecommerce-requirements', [DashboardController::class, 'ecommerceRequirements'])->name('ecommerce-requirements');
    Route::middleware(['not.analyst'])->group(function () {
        Route::get('/end-of-day', [DailyLogController::class, 'index'])->name('end-of-day');
        Route::post('/daily-logs', [DailyLogController::class, 'store'])->name('daily-logs.store');
        Route::put('/daily-logs/{dailyLog}', [DailyLogController::class, 'update'])->name('daily-logs.update');
        Route::delete('/daily-logs/{dailyLog}', [DailyLogController::class, 'destroy'])->name('daily-logs.destroy');
        Route::get('/daily-logs/history', [DailyLogController::class, 'history'])->name('daily-logs.history');
        Route::get('/important-links', [DashboardController::class, 'importantLinks'])->name('important-links');
        Route::get('/price-calculator', [DashboardController::class, 'priceCalculator'])->name('price-calculator');

        // Calendar
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
        Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.events.store');
        Route::put('/calendar/events/{event}', [CalendarController::class, 'update'])->name('calendar.events.update');
        Route::delete('/calendar/events/{event}', [CalendarController::class, 'destroy'])->name('calendar.events.destroy');
        Route::post('/calendar/categories', [CalendarController::class, 'storeCategory'])->name('calendar.categories.store');
        Route::delete('/calendar/categories/{category}', [CalendarController::class, 'destroyCategory'])->name('calendar.categories.destroy');
        Route::post('/calendar/tasks', [CalendarController::class, 'storeTask'])->name('calendar.tasks.store');
        Route::put('/calendar/tasks/{task}', [CalendarController::class, 'updateTask'])->name('calendar.tasks.update');
        Route::delete('/calendar/tasks/{task}', [CalendarController::class, 'destroyTask'])->name('calendar.tasks.destroy');
        Route::patch('/calendar/tasks/{task}/toggle', [CalendarController::class, 'toggleTask'])->name('calendar.tasks.toggle');
    });

    Route::get('/team', [DashboardController::class, 'team'])->name('team');

    // Announcements — all users can view
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');

    // Announcements CRUD — head, manager, analyst only
    Route::middleware(['announcement.poster'])->group(function () {
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::patch('/announcements/{announcement}/pin', [AnnouncementController::class, 'togglePin'])->name('announcements.pin');
    });

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
        Route::post('/preview-role', [AdminController::class, 'setPreviewRole'])->name('admin.preview-role.set');
        Route::delete('/preview-role', [AdminController::class, 'clearPreviewRole'])->name('admin.preview-role.clear');
    });
});
