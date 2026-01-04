# Controllers Documentation

## Overview

Controllers are organized into two namespaces:
- `App\Http\Controllers\Api` - Public API controllers
- `App\Http\Controllers\Admin` - Admin panel controllers

All controllers extend the base `Controller` class which provides helper methods for JSON responses.

---

## Base Controller

**Location:** `app/Http/Controllers/Controller.php`

### Helper Methods
```php
protected function success($data, string $message, int $statusCode): JsonResponse
protected function error(string $message, int $statusCode, $errors): JsonResponse
protected function notFound(string $message): JsonResponse
protected function unauthorized(string $message): JsonResponse
protected function forbidden(string $message): JsonResponse
```

---

## API Controllers

### AuthController
**Location:** `app/Http/Controllers/Api/AuthController.php`

| Method | Description | Request Class |
|--------|-------------|---------------|
| `register()` | Register new user | RegisterRequest |
| `login()` | User login | LoginRequest |
| `refresh()` | Refresh access token | - |
| `logout()` | Logout user | - |
| `me()` | Get current user | - |

### UserController
**Location:** `app/Http/Controllers/Api/UserController.php`

| Method | Description | Request Class |
|--------|-------------|---------------|
| `show(User $user)` | Get user profile | - |
| `updateProfile()` | Update profile | UpdateProfileRequest |
| `updateAvatar()` | Upload avatar | - |
| `changePassword()` | Change password | - |
| `deleteAccount()` | Delete account | - |
| `favorites()` | Get user favorites | - |
| `ads(User $user)` | Get user's ads | - |

### AdController
**Location:** `app/Http/Controllers/Api/AdController.php`

| Method | Description | Request Class |
|--------|-------------|---------------|
| `index()` | List ads with filters | - |
| `show(Ad $ad)` | Get ad details | - |
| `store()` | Create new ad | StoreAdRequest |
| `update(Ad $ad)` | Update ad | UpdateAdRequest |
| `destroy(Ad $ad)` | Delete ad | - |
| `markAsSold(Ad $ad)` | Mark as sold | - |
| `uploadImages(Ad $ad)` | Upload images | - |
| `toggleFavorite(Ad $ad)` | Toggle favorite | - |

### CategoryController
**Location:** `app/Http/Controllers/Api/CategoryController.php`

| Method | Description |
|--------|-------------|
| `index()` | List all active categories |

---

## Admin Controllers

### Admin\AuthController
**Location:** `app/Http/Controllers/Admin/AuthController.php`

| Method | Description |
|--------|-------------|
| `login()` | Admin login with role verification |
| `verify()` | Verify admin session |

### Admin\DashboardController
**Location:** `app/Http/Controllers/Admin/DashboardController.php`

| Method | Description |
|--------|-------------|
| `stats()` | Get dashboard statistics |
| `activity()` | Get recent activity feed |

### Admin\UserController
**Location:** `app/Http/Controllers/Admin/UserController.php`

| Method | Description |
|--------|-------------|
| `index()` | List users with filters |
| `show(User $user)` | Get user details |
| `store()` | Create new user |
| `suspend(User $user)` | Suspend user |
| `activate(User $user)` | Activate user |
| `ban(User $user)` | Ban user |
| `verifyUser(User $user)` | Verify user |
| `destroy(User $user)` | Delete user (super_admin only) |
| `bulkAction()` | Bulk user actions |
| `activity(User $user)` | Get user activity log |
| `export()` | Export users to CSV |

### Admin\AdController
**Location:** `app/Http/Controllers/Admin/AdController.php`

| Method | Description |
|--------|-------------|
| `index()` | List ads with filters |
| `show(Ad $ad)` | Get ad details |
| `approve(Ad $ad)` | Approve pending ad |
| `reject(Ad $ad)` | Reject ad with reason |
| `feature(Ad $ad)` | Toggle featured status |
| `promote(Ad $ad)` | Promote ad for X days |
| `destroy(Ad $ad)` | Delete ad |
| `bulkAction()` | Bulk ad actions |
| `export()` | Export ads to CSV |

### Admin\ReportController
**Location:** `app/Http/Controllers/Admin/ReportController.php`

| Method | Description |
|--------|-------------|
| `index()` | List reports with filters |
| `show(Report $report)` | Get report details |
| `stats()` | Get report statistics |
| `resolve(Report $report)` | Resolve report |
| `dismiss(Report $report)` | Dismiss report |
| `takeAction(Report $report)` | Take action on report |

### Admin\AnalyticsController
**Location:** `app/Http/Controllers/Admin/AnalyticsController.php`

| Method | Description |
|--------|-------------|
| `users()` | User growth analytics |
| `ads()` | Ad posting trends |
| `categories()` | Category distribution |
| `locations()` | Location distribution |

---

## Form Requests

### Auth Requests
- `App\Http\Requests\Auth\RegisterRequest` - User registration validation
- `App\Http\Requests\Auth\LoginRequest` - Login validation

### Ad Requests
- `App\Http\Requests\Ad\StoreAdRequest` - Create ad validation
- `App\Http\Requests\Ad\UpdateAdRequest` - Update ad validation (includes authorization)

### User Requests
- `App\Http\Requests\User\UpdateProfileRequest` - Profile update validation

---

*Document Created: January 2026*
