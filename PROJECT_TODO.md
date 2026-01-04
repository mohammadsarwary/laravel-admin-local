# Project TODO List

> **Project:** Market Local Admin Panel (Laravel 11)  
> **Analysis Date:** January 5, 2026  
> **Status:** Production-Ready with Improvements Needed

---

## 🔴 Critical (Must Fix)

### 1. CORS Configuration Too Permissive
- **Description:** `config/cors.php` allows all origins (`'allowed_origins' => ['*']`), which is a security vulnerability in production.
- **Priority:** High
- **Difficulty:** Easy
- **Location:** `config/cors.php:9`
- **Implementation:**
  ```php
  'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://bazarino.store,https://market.bazarino.store')),
  ```

### 2. Missing Rate Limiting on Authentication Endpoints
- **Description:** No rate limiting on `/api/auth/login` and `/api/admin/login` endpoints, vulnerable to brute force attacks.
- **Priority:** High
- **Difficulty:** Easy
- **Location:** `routes/api.php`
- **Implementation:**
  ```php
  // In bootstrap/app.php
  $middleware->throttleApi('60,1'); // 60 requests per minute
  
  // For login routes specifically
  Route::post('/auth/login', [AuthController::class, 'login'])
      ->middleware('throttle:5,1') // 5 attempts per minute
      ->name('auth.login');
  ```

### 3. APP_DEBUG=true in .env.example for Production
- **Description:** `.env.example` has `APP_DEBUG=true` with `APP_ENV=production`, which exposes sensitive error information.
- **Priority:** High
- **Difficulty:** Easy
- **Location:** `.env.example:4`
- **Implementation:** Change to `APP_DEBUG=false` for production template.

### 4. Missing Input Sanitization for Search Queries
- **Description:** Search queries in `Ad::scopeSearch()` and `UserController::index()` use raw LIKE queries without proper escaping, potential SQL injection via special characters.
- **Priority:** High
- **Difficulty:** Medium
- **Location:** `app/Models/Ad.php:178-183`, `app/Http/Controllers/Admin/UserController.php:28-32`
- **Implementation:**
  ```php
  // Escape special LIKE characters
  $term = str_replace(['%', '_'], ['\%', '\_'], $term);
  ```

### 5. No HTTPS Enforcement
- **Description:** No middleware to force HTTPS in production environment.
- **Priority:** High
- **Difficulty:** Easy
- **Location:** `bootstrap/app.php`
- **Implementation:**
  ```php
  if (app()->environment('production')) {
      URL::forceScheme('https');
  }
  ```

---

## 🟠 High Priority

### 6. Missing Password Reset Functionality
- **Description:** No password reset/forgot password feature implemented despite having `password_reset_tokens` table.
- **Priority:** High
- **Difficulty:** Medium
- **Location:** `app/Http/Controllers/Api/AuthController.php`
- **Implementation:** Add `forgotPassword()` and `resetPassword()` methods using Laravel's built-in password reset.

### 7. Export Function Returns JSON Instead of CSV
- **Description:** `UserController::export()` returns JSON with CSV headers, which is incorrect.
- **Priority:** High
- **Difficulty:** Easy
- **Location:** `app/Http/Controllers/Admin/UserController.php:231-243`
- **Implementation:**
  ```php
  public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
  {
      return response()->streamDownload(function () {
          $handle = fopen('php://output', 'w');
          fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Location', 'Rating', 'Active Listings', 'Verified', 'Active', 'Created At']);
          
          User::select([...])->chunk(1000, function ($users) use ($handle) {
              foreach ($users as $user) {
                  fputcsv($handle, $user->toArray());
              }
          });
          
          fclose($handle);
      }, 'users_export_' . date('Y-m-d') . '.csv');
  }
  ```

### 8. Missing Email Verification Flow
- **Description:** `email_verified_at` field exists but no verification flow is implemented.
- **Priority:** High
- **Difficulty:** Medium
- **Location:** `app/Http/Controllers/Api/AuthController.php`
- **Implementation:** Implement Laravel's `MustVerifyEmail` interface and verification routes.

### 9. No Logout Route for Web Sessions
- **Description:** Web admin panel has no proper logout functionality that clears the session.
- **Priority:** High
- **Difficulty:** Easy
- **Location:** `routes/web.php`
- **Implementation:**
  ```php
  Route::post('/logout', function (Request $request) {
      $request->session()->forget(['admin_token', 'admin_user_id']);
      return redirect()->route('login');
  })->name('auth.logout');
  ```

### 10. Missing Authorization Policies
- **Description:** No Laravel Policies for model authorization. Controllers manually check permissions.
- **Priority:** High
- **Difficulty:** Medium
- **Location:** `app/Policies/` (missing)
- **Implementation:** Create `AdPolicy`, `UserPolicy`, `ReportPolicy` for proper authorization.

---

## 🟡 Medium Priority

### 11. No Database Transactions for Critical Operations
- **Description:** User deletion, bulk actions, and ad operations don't use database transactions.
- **Priority:** Medium
- **Difficulty:** Easy
- **Location:** `app/Http/Controllers/Admin/UserController.php:160-175`
- **Implementation:**
  ```php
  DB::transaction(function () use ($user, $admin, $reason) {
      AdminLog::log($admin, 'delete_user', $user->id, 'user', $reason);
      $user->delete();
  });
  ```

### 12. Missing Soft Deletes on Critical Models
- **Description:** `User` and `Ad` models use hard deletes. Data recovery is impossible.
- **Priority:** Medium
- **Difficulty:** Medium
- **Location:** `app/Models/User.php`, `app/Models/Ad.php`
- **Implementation:**
  ```php
  use Illuminate\Database\Eloquent\SoftDeletes;
  
  class User extends Authenticatable
  {
      use SoftDeletes;
  }
  ```

### 13. No Caching Strategy
- **Description:** No caching for frequently accessed data (categories, stats, user counts).
- **Priority:** Medium
- **Difficulty:** Medium
- **Location:** `app/Http/Controllers/Admin/DashboardController.php`
- **Implementation:**
  ```php
  $stats = Cache::remember('dashboard_stats', 300, function () {
      return [
          'total_users' => User::count(),
          // ...
      ];
  });
  ```

### 14. Missing Request Validation for Admin Actions
- **Description:** Admin controllers use inline validation instead of Form Request classes.
- **Priority:** Medium
- **Difficulty:** Easy
- **Location:** `app/Http/Controllers/Admin/UserController.php:90-102`
- **Implementation:** Create `Admin/StoreUserRequest`, `Admin/BulkActionRequest` form request classes.

### 15. No API Versioning
- **Description:** API routes are not versioned, making breaking changes difficult.
- **Priority:** Medium
- **Difficulty:** Medium
- **Location:** `routes/api.php`
- **Implementation:**
  ```php
  Route::prefix('v1')->group(function () {
      // All current routes
  });
  ```

### 16. Missing Pagination Metadata in API Responses
- **Description:** Some endpoints return inconsistent pagination structure.
- **Priority:** Medium
- **Difficulty:** Easy
- **Location:** Various controllers
- **Implementation:** Create a standardized `PaginationResource` or trait.

### 17. No Image Validation for Dimensions/Size
- **Description:** Image upload only validates mime type and max size, not dimensions.
- **Priority:** Medium
- **Difficulty:** Easy
- **Location:** `app/Http/Controllers/Api/AdController.php:196-199`
- **Implementation:**
  ```php
  'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120|dimensions:min_width=200,min_height=200,max_width=4000,max_height=4000',
  ```

### 18. Missing Indexes on Foreign Keys
- **Description:** Some foreign key columns lack explicit indexes for query optimization.
- **Priority:** Medium
- **Difficulty:** Easy
- **Location:** `database/migrations/`
- **Implementation:** Add indexes on `reporter_id`, `reviewer_id`, `reviewed_user_id`, `sender_id`.

### 19. No Queue System for Heavy Operations
- **Description:** Export, bulk actions, and notifications run synchronously.
- **Priority:** Medium
- **Difficulty:** Medium
- **Location:** Various controllers
- **Implementation:** Create Jobs for `ExportUsers`, `BulkUserAction`, `SendNotification`.

### 20. Missing Model Factories
- **Description:** No factories for testing and seeding with realistic data.
- **Priority:** Medium
- **Difficulty:** Medium
- **Location:** `database/factories/` (missing)
- **Implementation:** Create factories for all models.

---

## 🟢 Low Priority / Enhancements

### 21. Add Full-Text Search with Scout
- **Description:** Current LIKE-based search is inefficient for large datasets.
- **Priority:** Low
- **Difficulty:** Hard
- **Implementation:** Integrate Laravel Scout with Meilisearch or Algolia.

### 22. Add Real-Time Notifications
- **Description:** No WebSocket/real-time notification system.
- **Priority:** Low
- **Difficulty:** Hard
- **Implementation:** Integrate Laravel Echo with Pusher or Soketi.

### 23. Add Two-Factor Authentication for Admin
- **Description:** Admin accounts lack 2FA protection.
- **Priority:** Low
- **Difficulty:** Medium
- **Implementation:** Use Laravel Fortify or custom TOTP implementation.

### 24. Add Activity Log Package
- **Description:** Current `AdminLog` is basic. Consider comprehensive activity logging.
- **Priority:** Low
- **Difficulty:** Easy
- **Implementation:** Install `spatie/laravel-activitylog` package.

### 25. Add API Documentation (OpenAPI/Swagger)
- **Description:** No interactive API documentation.
- **Priority:** Low
- **Difficulty:** Medium
- **Implementation:** Install `darkaonline/l5-swagger` and document all endpoints.

### 26. Add Health Check Endpoint
- **Description:** No endpoint for monitoring service health.
- **Priority:** Low
- **Difficulty:** Easy
- **Location:** Already has `/up` but needs expansion
- **Implementation:**
  ```php
  Route::get('/health', function () {
      return response()->json([
          'status' => 'healthy',
          'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
          'cache' => Cache::has('health_check') || Cache::put('health_check', true, 10),
      ]);
  });
  ```

### 27. Add Localization Support
- **Description:** All strings are hardcoded in English.
- **Priority:** Low
- **Difficulty:** Medium
- **Implementation:** Use Laravel's localization with `__()` helper and lang files.

### 28. Add Image Optimization on Upload
- **Description:** Images are stored as-is without optimization.
- **Priority:** Low
- **Difficulty:** Medium
- **Implementation:** Use `spatie/laravel-image-optimizer` or `intervention/image`.

### 29. Add Audit Trail for Data Changes
- **Description:** No tracking of who changed what and when.
- **Priority:** Low
- **Difficulty:** Medium
- **Implementation:** Use model observers or `owen-it/laravel-auditing`.

### 30. Add Dark Mode Toggle Persistence
- **Description:** Admin panel dark mode is hardcoded, no user preference.
- **Priority:** Low
- **Difficulty:** Easy
- **Implementation:** Store preference in localStorage and user settings.

---

## ⚡ Quick Wins

### 31. Add Missing `created_at` Index on AdminLog
- **Description:** `admin_logs` table queries by date but lacks index.
- **Difficulty:** Easy (5 min)
- **Implementation:**
  ```php
  $table->index('created_at');
  ```

### 32. Add Default Ordering to Models
- **Description:** Models lack default ordering, causing inconsistent results.
- **Difficulty:** Easy (5 min)
- **Implementation:**
  ```php
  protected static function booted()
  {
      static::addGlobalScope('order', function ($query) {
          $query->orderBy('created_at', 'desc');
      });
  }
  ```

### 33. Add `username` Field to User Model
- **Description:** Frontend expects `username` but model doesn't have it.
- **Difficulty:** Easy (10 min)
- **Implementation:** Add migration for `username` column or compute from email.

### 34. Fix Favorites Count Sync
- **Description:** `Ad::favorites` count is manually tracked but can get out of sync.
- **Difficulty:** Easy (15 min)
- **Implementation:** Use `withCount('favorites')` instead of manual counter.

### 35. Add `.gitkeep` to Empty Directories
- **Description:** `storage/` subdirectories may not exist after clone.
- **Difficulty:** Easy (5 min)
- **Implementation:** Add `.gitkeep` files to `storage/app/public/`, `storage/logs/`, etc.

### 36. Add Sanctum Stateful Domain for Production
- **Description:** `SANCTUM_STATEFUL_DOMAINS` only has one domain.
- **Difficulty:** Easy (5 min)
- **Location:** `.env.example:64`
- **Implementation:**
  ```
  SANCTUM_STATEFUL_DOMAINS=market.bazarino.store,bazarino.store,localhost
  ```

### 37. Add Response Compression
- **Description:** API responses are not compressed.
- **Difficulty:** Easy (5 min)
- **Implementation:** Enable gzip in nginx/Apache or add middleware.

### 38. Fix Admin Seeder Credentials Mismatch
- **Description:** `docs/TODO.md` shows old credentials, seeder has new ones.
- **Difficulty:** Easy (5 min)
- **Implementation:** Update documentation to match `admin@bazarino.store` / `password`.

---

## 🧱 Technical Debt

### 39. Inconsistent Response Format
- **Description:** Some controllers return raw data, others use `$this->success()`.
- **Location:** `app/Http/Controllers/Admin/UserController.php:87`
- **Implementation:** Standardize all responses to use base controller methods.

### 40. Duplicate Code in Controllers
- **Description:** Pagination logic is repeated across multiple controllers.
- **Implementation:** Create a `PaginationTrait` or base admin controller.

### 41. Missing Type Hints
- **Description:** Some methods lack return type declarations.
- **Location:** Various models and controllers
- **Implementation:** Add strict types and return type hints throughout.

### 42. Hardcoded Strings in Controllers
- **Description:** Error messages and success messages are hardcoded.
- **Implementation:** Move to language files or constants.

### 43. No Service Layer
- **Description:** Business logic is in controllers instead of service classes.
- **Implementation:** Create `UserService`, `AdService`, `ReportService` for complex operations.

### 44. Mixed Concerns in Models
- **Description:** Models contain business logic (e.g., `User::updateRating()`).
- **Implementation:** Move to service classes or dedicated action classes.

### 45. No Repository Pattern
- **Description:** Controllers directly query models, making testing harder.
- **Implementation:** Consider repositories for complex queries (optional, depends on team preference).

### 46. Unused Imports in Some Files
- **Description:** Some files have unused `use` statements.
- **Implementation:** Run PHP CS Fixer or IDE cleanup.

---

## 🚀 Future Improvements

### 47. Implement GraphQL API
- **Description:** REST API could be supplemented with GraphQL for flexible queries.
- **Difficulty:** Hard
- **Implementation:** Use `rebing/graphql-laravel` or `nuwave/lighthouse`.

### 48. Add Elasticsearch for Advanced Search
- **Description:** Full-text search with filters, facets, and suggestions.
- **Difficulty:** Hard
- **Implementation:** Integrate Elasticsearch with Laravel Scout.

### 49. Implement Microservices Architecture
- **Description:** Split into separate services (Auth, Ads, Notifications).
- **Difficulty:** Hard
- **Implementation:** Long-term architectural change.

### 50. Add Machine Learning for Content Moderation
- **Description:** Auto-flag suspicious ads using ML.
- **Difficulty:** Hard
- **Implementation:** Integrate with AWS Rekognition or custom ML model.

### 51. Implement CDN for Static Assets
- **Description:** Serve images and assets from CDN.
- **Difficulty:** Medium
- **Implementation:** Configure S3 + CloudFront or similar.

### 52. Add A/B Testing Framework
- **Description:** Test different UI/UX variations.
- **Difficulty:** Medium
- **Implementation:** Use feature flags with `laravel-pennant`.

### 53. Implement Event Sourcing for Audit
- **Description:** Full event history for compliance.
- **Difficulty:** Hard
- **Implementation:** Use `spatie/laravel-event-sourcing`.

### 54. Add Multi-Tenancy Support
- **Description:** Support multiple marketplaces from single codebase.
- **Difficulty:** Hard
- **Implementation:** Use `stancl/tenancy` package.

### 55. Implement PWA for Admin Panel
- **Description:** Make admin panel installable as PWA.
- **Difficulty:** Medium
- **Implementation:** Add service worker and manifest.

---

## 🧪 Testing (Missing)

### 56. Add Unit Tests for Models
- **Description:** No unit tests exist.
- **Priority:** High
- **Files Needed:**
  - `tests/Unit/Models/UserTest.php`
  - `tests/Unit/Models/AdTest.php`
  - `tests/Unit/Models/ReportTest.php`

### 57. Add Feature Tests for API Endpoints
- **Description:** No integration tests for API.
- **Priority:** High
- **Files Needed:**
  - `tests/Feature/Api/AuthTest.php`
  - `tests/Feature/Api/AdTest.php`
  - `tests/Feature/Api/UserTest.php`

### 58. Add Feature Tests for Admin Panel
- **Description:** No tests for admin functionality.
- **Priority:** Medium
- **Files Needed:**
  - `tests/Feature/Admin/UserManagementTest.php`
  - `tests/Feature/Admin/AdManagementTest.php`

### 59. Add Browser Tests (Dusk)
- **Description:** No E2E tests for admin UI.
- **Priority:** Low
- **Implementation:** Set up Laravel Dusk for browser testing.

### 60. Add Test Coverage Reporting
- **Description:** No code coverage metrics.
- **Priority:** Medium
- **Implementation:** Configure PHPUnit with coverage and integrate with CI.

---

## 🐳 DevOps (Missing)

### 61. Add Dockerfile
- **Description:** No containerization support.
- **Priority:** Medium
- **Implementation:** Create `Dockerfile` and `docker-compose.yml`.

### 62. Add CI/CD Pipeline
- **Description:** No automated testing/deployment.
- **Priority:** Medium
- **Implementation:** Create `.github/workflows/ci.yml` for GitHub Actions.

### 63. Add Production Deployment Script
- **Description:** No automated deployment process.
- **Priority:** Medium
- **Implementation:** Create `deploy.sh` or use Envoy/Deployer.

### 64. Add Database Backup Strategy
- **Description:** No automated backup configuration.
- **Priority:** High
- **Implementation:** Configure `spatie/laravel-backup` package.

### 65. Add Monitoring/APM Integration
- **Description:** No application performance monitoring.
- **Priority:** Medium
- **Implementation:** Integrate Sentry, New Relic, or Laravel Telescope.

---

## 📊 Summary

| Category | Count | Critical |
|----------|-------|----------|
| Critical Issues | 5 | ⚠️ |
| High Priority | 5 | - |
| Medium Priority | 10 | - |
| Low Priority | 10 | - |
| Quick Wins | 8 | - |
| Technical Debt | 8 | - |
| Future Improvements | 9 | - |
| Testing | 5 | - |
| DevOps | 5 | - |
| **Total** | **65** | **5** |

---

## 🎯 Recommended Action Plan

### Week 1: Critical Security Fixes
1. Fix CORS configuration
2. Add rate limiting
3. Fix APP_DEBUG
4. Add input sanitization
5. Enforce HTTPS

### Week 2: High Priority Features
1. Implement password reset
2. Fix export functionality
3. Add proper logout
4. Create authorization policies

### Week 3: Testing Foundation
1. Set up PHPUnit
2. Create model factories
3. Write unit tests for models
4. Write feature tests for auth

### Week 4: DevOps & Monitoring
1. Create Dockerfile
2. Set up CI/CD
3. Configure backups
4. Add error monitoring

---

*Generated by Project Analysis Tool*  
*Last Updated: January 5, 2026*
