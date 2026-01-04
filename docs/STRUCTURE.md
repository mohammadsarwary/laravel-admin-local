# Structure Mapping - Legacy PHP to Laravel

## Overview

This document maps the existing legacy PHP files to their corresponding Laravel architecture components.

---

## 1. Directory Structure Comparison

### Legacy Structure
```
backend/
├── config/
│   ├── config.php
│   ├── database.php
│   └── jwt.php
├── controllers/
│   ├── AdminController.php
│   ├── AdController.php
│   ├── AuthController.php
│   ├── CategoryController.php
│   └── UserController.php
├── database/
│   ├── migrations/
│   └── schema.sql
├── middleware/
│   ├── AdminMiddleware.php
│   └── AuthMiddleware.php
├── models/
│   ├── Ad.php
│   └── User.php
├── utils/
│   ├── JWT.php
│   ├── Response.php
│   └── Validator.php
├── admin_api.php
├── index.php
└── .htaccess
```

### Laravel Structure
```
laravel-admin/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── AdController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── UserController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── UserController.php
│   │   │       ├── AdController.php
│   │   │       ├── ReportController.php
│   │   │       └── AnalyticsController.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   └── EnsureTokenIsValid.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── LoginRequest.php
│   │       │   └── RegisterRequest.php
│   │       ├── Ad/
│   │       │   ├── StoreAdRequest.php
│   │       │   └── UpdateAdRequest.php
│   │       └── User/
│   │           └── UpdateProfileRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Ad.php
│   │   ├── AdImage.php
│   │   ├── Category.php
│   │   ├── Favorite.php
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   ├── Review.php
│   │   ├── Notification.php
│   │   ├── RefreshToken.php
│   │   ├── Report.php
│   │   └── AdminLog.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── AdService.php
│   │   └── AnalyticsService.php
│   └── Enums/
│       ├── AdStatus.php
│       ├── AdCondition.php
│       ├── AdminRole.php
│       ├── ReportStatus.php
│       └── NotificationType.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── sanctum.php
│   └── filesystems.php
├── database/
│   ├── migrations/
│   │   ├── 2026_01_01_000001_create_users_table.php
│   │   ├── 2026_01_01_000002_create_categories_table.php
│   │   ├── 2026_01_01_000003_create_ads_table.php
│   │   └── ...
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php
│       └── AdminUserSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── admin.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── users/
│       │   ├── ads/
│       │   └── reports/
│       └── components/
├── routes/
│   ├── api.php
│   ├── web.php
│   └── admin.php
└── .env
```

---

## 2. File-by-File Mapping

### Configuration Files

| Legacy File | Laravel Equivalent | Notes |
|-------------|-------------------|-------|
| `config/config.php` | `.env` + `config/app.php` | Environment variables |
| `config/database.php` | `.env` + `config/database.php` | DB credentials in .env |
| `config/jwt.php` | `.env` + `config/sanctum.php` | Use Laravel Sanctum |

### Controllers

| Legacy Controller | Laravel Controller(s) | Notes |
|-------------------|----------------------|-------|
| `AuthController.php` | `App\Http\Controllers\Api\AuthController` | Use Sanctum for tokens |
| `UserController.php` | `App\Http\Controllers\Api\UserController` | Resource controller |
| `AdController.php` | `App\Http\Controllers\Api\AdController` | Resource controller |
| `CategoryController.php` | `App\Http\Controllers\Api\CategoryController` | Resource controller |
| `AdminController.php` | Split into multiple controllers: | |
| | - `Admin\DashboardController` | Stats, activity |
| | - `Admin\UserController` | User management |
| | - `Admin\AdController` | Ad management |
| | - `Admin\ReportController` | Report management |
| | - `Admin\AnalyticsController` | Analytics endpoints |

### Models

| Legacy Model | Laravel Model | Relationships |
|--------------|---------------|---------------|
| `User.php` | `App\Models\User` | hasMany: ads, favorites, reviews, notifications, messages |
| `Ad.php` | `App\Models\Ad` | belongsTo: user, category; hasMany: images, favorites |
| (none) | `App\Models\Category` | hasMany: ads; belongsTo: parent |
| (none) | `App\Models\AdImage` | belongsTo: ad |
| (none) | `App\Models\Favorite` | belongsTo: user, ad |
| (none) | `App\Models\Conversation` | belongsTo: ad, buyer, seller; hasMany: messages |
| (none) | `App\Models\Message` | belongsTo: conversation, sender |
| (none) | `App\Models\Review` | belongsTo: reviewer, reviewedUser, ad |
| (none) | `App\Models\Notification` | belongsTo: user |
| (none) | `App\Models\RefreshToken` | belongsTo: user |
| (none) | `App\Models\Report` | belongsTo: reporter |
| (none) | `App\Models\AdminLog` | belongsTo: admin |

### Middleware

| Legacy Middleware | Laravel Middleware | Notes |
|-------------------|-------------------|-------|
| `AuthMiddleware.php` | `auth:sanctum` | Built-in Sanctum middleware |
| `AdminMiddleware.php` | `App\Http\Middleware\AdminMiddleware` | Custom admin check |

### Utilities

| Legacy Utility | Laravel Equivalent | Notes |
|----------------|-------------------|-------|
| `JWT.php` | Laravel Sanctum | Built-in token management |
| `Response.php` | Laravel API Resources | `JsonResource` classes |
| `Validator.php` | Form Request classes | `App\Http\Requests\*` |

---

## 3. Route Mapping

### API Routes (routes/api.php)

```php
// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/ads', [AdController::class, 'index']);
Route::get('/ads/{ad}', [AdController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    Route::put('/users/profile', [UserController::class, 'updateProfile']);
    Route::post('/users/avatar', [UserController::class, 'updateAvatar']);
    Route::post('/users/change-password', [UserController::class, 'changePassword']);
    Route::delete('/users/account', [UserController::class, 'deleteAccount']);
    Route::get('/users/favorites', [UserController::class, 'favorites']);
    
    Route::apiResource('ads', AdController::class)->except(['index', 'show']);
    Route::post('/ads/{ad}/sold', [AdController::class, 'markAsSold']);
    Route::post('/ads/{ad}/images', [AdController::class, 'uploadImages']);
    Route::post('/ads/{ad}/favorite', [AdController::class, 'toggleFavorite']);
});

Route::get('/users/{user}', [UserController::class, 'show']);
Route::get('/users/{user}/ads', [UserController::class, 'ads']);
```

### Admin Routes (routes/admin.php)

```php
Route::prefix('admin')->group(function () {
    Route::post('/login', [Admin\AuthController::class, 'login']);
    
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/verify', [Admin\AuthController::class, 'verify']);
        Route::get('/stats', [Admin\DashboardController::class, 'stats']);
        Route::get('/activity', [Admin\DashboardController::class, 'activity']);
        
        // User Management
        Route::get('/users', [Admin\UserController::class, 'index']);
        Route::get('/users/export', [Admin\UserController::class, 'export']);
        Route::post('/users/create', [Admin\UserController::class, 'store']);
        Route::post('/users/bulk-action', [Admin\UserController::class, 'bulkAction']);
        Route::get('/users/{user}', [Admin\UserController::class, 'show']);
        Route::get('/users/{user}/activity', [Admin\UserController::class, 'activity']);
        Route::put('/users/{user}/suspend', [Admin\UserController::class, 'suspend']);
        Route::put('/users/{user}/activate', [Admin\UserController::class, 'activate']);
        Route::put('/users/{user}/ban', [Admin\UserController::class, 'ban']);
        Route::put('/users/{user}/verify', [Admin\UserController::class, 'verify']);
        Route::delete('/users/{user}', [Admin\UserController::class, 'destroy']);
        
        // Ad Management
        Route::get('/ads', [Admin\AdController::class, 'index']);
        Route::get('/ads/export', [Admin\AdController::class, 'export']);
        Route::post('/ads/bulk-action', [Admin\AdController::class, 'bulkAction']);
        Route::get('/ads/{ad}', [Admin\AdController::class, 'show']);
        Route::put('/ads/{ad}/approve', [Admin\AdController::class, 'approve']);
        Route::put('/ads/{ad}/reject', [Admin\AdController::class, 'reject']);
        Route::put('/ads/{ad}/feature', [Admin\AdController::class, 'feature']);
        Route::put('/ads/{ad}/promote', [Admin\AdController::class, 'promote']);
        Route::delete('/ads/{ad}', [Admin\AdController::class, 'destroy']);
        
        // Report Management
        Route::get('/reports', [Admin\ReportController::class, 'index']);
        Route::get('/reports/stats', [Admin\ReportController::class, 'stats']);
        Route::get('/reports/{report}', [Admin\ReportController::class, 'show']);
        Route::put('/reports/{report}/resolve', [Admin\ReportController::class, 'resolve']);
        Route::put('/reports/{report}/dismiss', [Admin\ReportController::class, 'dismiss']);
        Route::post('/reports/{report}/action', [Admin\ReportController::class, 'takeAction']);
        
        // Analytics
        Route::get('/analytics/users', [Admin\AnalyticsController::class, 'users']);
        Route::get('/analytics/ads', [Admin\AnalyticsController::class, 'ads']);
        Route::get('/analytics/categories', [Admin\AnalyticsController::class, 'categories']);
        Route::get('/analytics/locations', [Admin\AnalyticsController::class, 'locations']);
    });
});
```

---

## 4. Database Migration Order

1. `create_users_table` - Base users table
2. `create_categories_table` - Categories with self-reference
3. `create_ads_table` - Ads with FK to users, categories
4. `create_ad_images_table` - Images with FK to ads
5. `create_favorites_table` - Pivot table users-ads
6. `create_conversations_table` - Conversations with FK to ads, users
7. `create_messages_table` - Messages with FK to conversations, users
8. `create_reviews_table` - Reviews with FK to users, ads
9. `create_notifications_table` - Notifications with FK to users
10. `create_personal_access_tokens_table` - Sanctum tokens (built-in)
11. `create_reports_table` - Reports with FK to users
12. `create_admin_logs_table` - Admin logs with FK to users

---

## 5. Eloquent Relationship Summary

```
User
├── hasMany → Ad
├── hasMany → Favorite
├── hasMany → Review (as reviewer)
├── hasMany → Review (as reviewed)
├── hasMany → Notification
├── hasMany → Message
├── hasMany → Report
├── hasMany → AdminLog
├── hasMany → Conversation (as buyer)
└── hasMany → Conversation (as seller)

Ad
├── belongsTo → User
├── belongsTo → Category
├── hasMany → AdImage
├── hasMany → Favorite
├── hasMany → Conversation
└── hasMany → Review

Category
├── belongsTo → Category (parent)
├── hasMany → Category (children)
└── hasMany → Ad

Conversation
├── belongsTo → Ad
├── belongsTo → User (buyer)
├── belongsTo → User (seller)
└── hasMany → Message

Report
└── belongsTo → User (reporter)
```

---

## 6. Service Layer Design

### AuthService
- `register(array $data): User`
- `login(array $credentials): array`
- `logout(User $user): void`
- `refreshToken(string $token): array`

### AdService
- `getFilteredAds(array $filters, int $page, int $limit): LengthAwarePaginator`
- `createAd(User $user, array $data): Ad`
- `updateAd(Ad $ad, array $data): Ad`
- `markAsSold(Ad $ad): Ad`
- `uploadImages(Ad $ad, array $files): Collection`

### AnalyticsService
- `getDashboardStats(): array`
- `getUserGrowth(string $period): Collection`
- `getAdTrends(string $period): Collection`
- `getCategoryDistribution(): Collection`
- `getLocationDistribution(): Collection`

---

*Document Created: January 2026*
