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

// Admin Session Setup (for web routes)
Route::post('/admin/set-session', function (\Illuminate\Http\Request $request) {
    $token = $request->input('token');
    
    if (!$token) {
        return response()->json(['success' => false, 'message' => 'Token required'], 400);
    }
    
    // Verify token and get user
    try {
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
        
        if (!$user || !$user->is_admin || !$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }
        
        // Store token in session for web routes
        $request->session()->put('admin_token', $token);
        $request->session()->put('admin_user_id', $user->id);
        
        return response()->json(['success' => true, 'message' => 'Session created']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Session creation failed'], 401);
    }
})->middleware('web');

// Admin Panel Web Routes (if using Blade views)
Route::prefix('admin')->name('admin.web.')->middleware(['web', 'admin.session'])->group(function () {
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

    Route::get('/advertisements', function () {
        return view('admin.advertisements.index');
    })->name('advertisements');
});
