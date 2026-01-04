<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Admin panel web routes with Blade views
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Admin Login Route
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    return response()->json(['message' => 'Use /api/admin/login instead'], 400);
});

// Admin Panel Web Routes (if using Blade views)
Route::prefix('admin')->name('admin.web.')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users');

    Route::get('/ads', function () {
        return view('admin.ads.index');
    })->name('ads');

    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('reports');

    Route::get('/analytics', function () {
        return view('admin.analytics.index');
    })->name('analytics');

    Route::get('/moderation', function () {
        return view('admin.moderation');
    })->name('moderation');
});
