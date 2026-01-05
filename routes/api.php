<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public API routes for the Market Local application
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::get('/ads', [AdController::class, 'index'])->name('ads.index');
Route::get('/ads/{ad}', [AdController::class, 'show'])->name('ads.show');

Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/ads', [UserController::class, 'ads'])->name('users.ads');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

    // User profile
    Route::put('/users/profile', [UserController::class, 'updateProfile'])->name('users.updateProfile');
    Route::post('/users/avatar', [UserController::class, 'updateAvatar'])->name('users.updateAvatar');
    Route::post('/users/change-password', [UserController::class, 'changePassword'])->name('users.changePassword');
    Route::delete('/users/account', [UserController::class, 'deleteAccount'])->name('users.deleteAccount');
    Route::get('/users/favorites', [UserController::class, 'favorites'])->name('users.favorites');

    // Ads CRUD
    Route::post('/ads', [AdController::class, 'store'])->name('ads.store');
    Route::put('/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
    Route::delete('/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');
    Route::post('/ads/{ad}/sold', [AdController::class, 'markAsSold'])->name('ads.markAsSold');
    Route::post('/ads/{ad}/images', [AdController::class, 'uploadImages'])->name('ads.uploadImages');
    Route::post('/ads/{ad}/favorite', [AdController::class, 'toggleFavorite'])->name('ads.toggleFavorite');
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('/login', [Admin\AuthController::class, 'login'])->name('login');

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        // Auth
        Route::get('/verify', [Admin\AuthController::class, 'verify'])->name('verify');

        // Dashboard
        Route::get('/stats', [Admin\DashboardController::class, 'stats'])->name('stats');
        Route::get('/activity', [Admin\DashboardController::class, 'activity'])->name('activity');

        // User Management
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/export', [Admin\UserController::class, 'export'])->name('users.export');
        Route::post('/users/create', [Admin\UserController::class, 'store'])->name('users.store');
        Route::post('/users/bulk-action', [Admin\UserController::class, 'bulkAction'])->name('users.bulkAction');
        Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/activity', [Admin\UserController::class, 'activity'])->name('users.activity');
        Route::put('/users/{user}/suspend', [Admin\UserController::class, 'suspend'])->name('users.suspend');
        Route::put('/users/{user}/activate', [Admin\UserController::class, 'activate'])->name('users.activate');
        Route::put('/users/{user}/ban', [Admin\UserController::class, 'ban'])->name('users.ban');
        Route::put('/users/{user}/verify', [Admin\UserController::class, 'verifyUser'])->name('users.verify');
        Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Ad Management
        Route::get('/ads', [Admin\AdController::class, 'index'])->name('ads.index');
        Route::get('/ads/export', [Admin\AdController::class, 'export'])->name('ads.export');
        Route::post('/ads/bulk-action', [Admin\AdController::class, 'bulkAction'])->name('ads.bulkAction');
        Route::get('/ads/{ad}', [Admin\AdController::class, 'show'])->name('ads.show');
        Route::put('/ads/{ad}/approve', [Admin\AdController::class, 'approve'])->name('ads.approve');
        Route::put('/ads/{ad}/reject', [Admin\AdController::class, 'reject'])->name('ads.reject');
        Route::put('/ads/{ad}/feature', [Admin\AdController::class, 'feature'])->name('ads.feature');
        Route::put('/ads/{ad}/promote', [Admin\AdController::class, 'promote'])->name('ads.promote');
        Route::delete('/ads/{ad}', [Admin\AdController::class, 'destroy'])->name('ads.destroy');

        // Report Management
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/stats', [Admin\ReportController::class, 'stats'])->name('reports.stats');
        Route::get('/reports/{report}', [Admin\ReportController::class, 'show'])->name('reports.show');
        Route::put('/reports/{report}/resolve', [Admin\ReportController::class, 'resolve'])->name('reports.resolve');
        Route::put('/reports/{report}/dismiss', [Admin\ReportController::class, 'dismiss'])->name('reports.dismiss');
        Route::post('/reports/{report}/action', [Admin\ReportController::class, 'takeAction'])->name('reports.takeAction');

        // Category Management
        Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/export', [Admin\CategoryController::class, 'export'])->name('categories.export');
        Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/bulk-action', [Admin\CategoryController::class, 'bulkAction'])->name('categories.bulkAction');
        Route::get('/categories/{category}', [Admin\CategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{category}', [Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::put('/categories/{category}/toggle-status', [Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggleStatus');
        Route::delete('/categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Analytics
        Route::get('/analytics/users', [Admin\AnalyticsController::class, 'users'])->name('analytics.users');
        Route::get('/analytics/ads', [Admin\AnalyticsController::class, 'ads'])->name('analytics.ads');
        Route::get('/analytics/categories', [Admin\AnalyticsController::class, 'categories'])->name('analytics.categories');
        Route::get('/analytics/locations', [Admin\AnalyticsController::class, 'locations'])->name('analytics.locations');
    });
});
