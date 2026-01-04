# Project Overview - Market Local Admin Panel

## Executive Summary

This document provides a comprehensive analysis of the existing legacy PHP admin panel for the "Market Local" marketplace application. The goal is to migrate this codebase to a modern Laravel application while preserving all existing functionality.

---

## 1. Application Description

**Application Name:** Market Local API  
**Version:** 1.0.0  
**Type:** REST API Backend + Admin Panel  
**Purpose:** A local marketplace platform for buying and selling items

### Core Features:
- User registration and authentication (JWT-based)
- Ad/Listing management (CRUD operations)
- Category management
- Favorites system
- Messaging/Conversations
- Reviews and ratings
- Admin panel with full management capabilities
- Reporting system
- Analytics dashboard

---

## 2. Existing File Structure Analysis

### Entry Points
| File | Purpose |
|------|---------|
| `index.php` | Main API router for public endpoints |
| `admin_api.php` | Admin API router for admin endpoints |

### Controllers (5 files)
| Controller | Methods | Purpose |
|------------|---------|---------|
| `AuthController.php` | register, login, refresh, logout, me | User authentication |
| `UserController.php` | getProfile, updateProfile, updateAvatar, changePassword, deleteAccount | User profile management |
| `AdController.php` | getAll, getById, create, update, delete, markAsSold, uploadImages, getUserAds, toggleFavorite, getFavorites | Ad/Listing CRUD |
| `CategoryController.php` | getAll | Category listing |
| `AdminController.php` | 40+ methods | Full admin panel functionality |

### Models (2 files)
| Model | Table | Key Methods |
|-------|-------|-------------|
| `User.php` | users | create, findByEmail, findByPhone, findById, update, updatePassword, updateLastLogin, delete, incrementStat, updateRating |
| `Ad.php` | ads | create, getAll, getById, getUserAds, update, delete, markAsSold, incrementViews, addImage, getAdImages |

### Middleware (2 files)
| Middleware | Purpose |
|------------|---------|
| `AuthMiddleware.php` | JWT token validation for regular users |
| `AdminMiddleware.php` | JWT token validation + admin role verification |

### Utilities (3 files)
| Utility | Purpose |
|---------|---------|
| `JWT.php` | Custom JWT encode/decode implementation |
| `Response.php` | Standardized JSON API responses |
| `Validator.php` | Input validation helper |

### Configuration (3 files)
| Config | Purpose |
|--------|---------|
| `config.php` | App settings, CORS, upload config |
| `database.php` | Database connection (PDO) |
| `jwt.php` | JWT secret key and token expiry settings |

---

## 3. Database Schema Analysis

### Tables (12 total)

#### Core Tables
1. **users** - User accounts with admin fields
   - Fields: id, name, email, phone, password, avatar, bio, location, rating, review_count, active_listings, sold_items, followers, is_verified, is_active, is_admin, admin_role, created_at, updated_at, last_login
   - Admin roles: super_admin, admin, moderator

2. **categories** - Product categories (hierarchical)
   - Fields: id, name, slug, icon, parent_id, display_order, is_active, created_at, updated_at

3. **ads** - Listings/advertisements
   - Fields: id, user_id, category_id, title, description, price, condition, location, latitude, longitude, status, views, favorites, is_promoted, promoted_until, is_featured, created_at, updated_at, expires_at
   - Status: active, sold, expired, deleted
   - Condition: new, like_new, good, fair, poor

4. **ad_images** - Images for ads
   - Fields: id, ad_id, image_url, display_order, is_primary, created_at

5. **favorites** - User favorites
   - Fields: id, user_id, ad_id, created_at

6. **conversations** - Chat conversations
   - Fields: id, ad_id, buyer_id, seller_id, last_message, last_message_at, is_read_by_buyer, is_read_by_seller, created_at, updated_at

7. **messages** - Chat messages
   - Fields: id, conversation_id, sender_id, message, image_url, is_read, created_at

8. **reviews** - User reviews
   - Fields: id, reviewer_id, reviewed_user_id, ad_id, rating, comment, created_at, updated_at

9. **notifications** - User notifications
   - Fields: id, user_id, type, title, message, related_id, is_read, created_at
   - Types: message, favorite, review, ad_sold, ad_expired, system

10. **refresh_tokens** - JWT refresh tokens
    - Fields: id, user_id, token, expires_at, created_at

11. **reports** - Content reports
    - Fields: id, reporter_id, reported_type, reported_id, reason, description, status, created_at, updated_at
    - Types: ad, user, message
    - Status: pending, reviewed, resolved, dismissed

12. **admin_logs** - Admin action logs
    - Fields: id, admin_id, action, target_id, target_type, details, ip_address, user_agent, created_at

---

## 4. API Endpoints Analysis

### Public API (index.php)

#### Authentication Routes
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| POST | /api/auth/register | AuthController::register |
| POST | /api/auth/login | AuthController::login |
| POST | /api/auth/refresh | AuthController::refresh |
| POST | /api/auth/logout | AuthController::logout |
| GET | /api/auth/me | AuthController::me |

#### User Routes
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /api/users/{id} | UserController::getProfile |
| PUT | /api/users/profile | UserController::updateProfile |
| POST | /api/users/avatar | UserController::updateAvatar |
| POST | /api/users/change-password | UserController::changePassword |
| DELETE | /api/users/account | UserController::deleteAccount |
| GET | /api/users/favorites | AdController::getFavorites |
| GET | /api/users/{id}/ads | AdController::getUserAds |

#### Ad Routes
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /api/ads | AdController::getAll |
| POST | /api/ads | AdController::create |
| GET | /api/ads/{id} | AdController::getById |
| PUT | /api/ads/{id} | AdController::update |
| DELETE | /api/ads/{id} | AdController::delete |
| POST | /api/ads/{id}/sold | AdController::markAsSold |
| POST | /api/ads/{id}/images | AdController::uploadImages |
| POST | /api/ads/{id}/favorite | AdController::toggleFavorite |

#### Category Routes
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /api/categories | CategoryController::getAll |

### Admin API (admin_api.php)

#### Admin Authentication
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| POST | /admin/login | AdminController::login |
| GET | /admin/verify | AdminController::verify |

#### Dashboard
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /admin/stats | AdminController::getStats |
| GET | /admin/activity | AdminController::getRecentActivity |

#### User Management
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /admin/users | AdminController::listUsers |
| GET | /admin/users/export | AdminController::exportUsers |
| POST | /admin/users/create | AdminController::createUser |
| POST | /admin/users/bulk-action | AdminController::bulkActionUsers |
| GET | /admin/users/{id} | AdminController::getUserDetails |
| GET | /admin/users/{id}/activity | AdminController::getUserActivity |
| PUT | /admin/users/{id}/suspend | AdminController::suspendUser |
| PUT | /admin/users/{id}/activate | AdminController::activateUser |
| PUT | /admin/users/{id}/ban | AdminController::banUser |
| PUT | /admin/users/{id}/verify | AdminController::verifyUser |
| DELETE | /admin/users/{id} | AdminController::deleteUser |

#### Ad Management
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /admin/ads | AdminController::listAds |
| GET | /admin/ads/export | AdminController::exportAds |
| POST | /admin/ads/bulk-action | AdminController::bulkActionAds |
| GET | /admin/ads/{id} | AdminController::getAdDetails |
| PUT | /admin/ads/{id}/approve | AdminController::approveAd |
| PUT | /admin/ads/{id}/reject | AdminController::rejectAd |
| PUT | /admin/ads/{id}/feature | AdminController::featureAd |
| PUT | /admin/ads/{id}/promote | AdminController::promoteAd |
| DELETE | /admin/ads/{id} | AdminController::deleteAd |

#### Report Management
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /admin/reports | AdminController::listReports |
| GET | /admin/reports/stats | AdminController::getReportStats |
| GET | /admin/reports/{id} | AdminController::getReportDetails |
| PUT | /admin/reports/{id}/resolve | AdminController::resolveReport |
| PUT | /admin/reports/{id}/dismiss | AdminController::dismissReport |
| POST | /admin/reports/{id}/action | AdminController::takeReportAction |

#### Analytics
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | /admin/analytics/users | AdminController::getUserGrowthAnalytics |
| GET | /admin/analytics/ads | AdminController::getAdPostingAnalytics |
| GET | /admin/analytics/categories | AdminController::getCategoryAnalytics |
| GET | /admin/analytics/locations | AdminController::getLocationAnalytics |

---

## 5. Authentication System Analysis

### Current Implementation
- **Type:** Custom JWT implementation (no external library)
- **Access Token Expiry:** 1 hour (3600 seconds)
- **Refresh Token Expiry:** 30 days (2592000 seconds)
- **Algorithm:** HS256
- **Storage:** Refresh tokens stored in database

### Token Payload Structure
```json
{
  "user_id": 1,
  "is_admin": true,
  "admin_role": "super_admin",
  "iat": 1234567890,
  "exp": 1234571490,
  "iss": "market_local_api",
  "aud": "market_local_app"
}
```

### Admin Roles Hierarchy
1. **super_admin** (level 3) - Full access, can delete users
2. **admin** (level 2) - Most admin functions
3. **moderator** (level 1) - Limited admin functions

---

## 6. Security Analysis

### Current Security Measures
- ✅ Password hashing with bcrypt (PASSWORD_BCRYPT)
- ✅ Prepared statements for SQL queries (PDO)
- ✅ JWT token validation
- ✅ Admin role verification
- ✅ File type validation for uploads
- ✅ File size limits (5MB)
- ✅ Security headers in .htaccess

### Security Concerns to Address in Laravel
- ⚠️ No CSRF protection (API-only currently)
- ⚠️ Hardcoded JWT secret key
- ⚠️ Database credentials in code
- ⚠️ No rate limiting
- ⚠️ No input sanitization beyond validation

---

## 7. Business Logic Summary

### User Registration Flow
1. Validate input (name, email, password)
2. Check email/phone uniqueness
3. Hash password with bcrypt
4. Create user record
5. Generate access + refresh tokens
6. Return user data with tokens

### Ad Creation Flow
1. Authenticate user via JWT
2. Validate ad data
3. Create ad record
4. Increment user's active_listings count
5. Return created ad

### Admin Actions
- All admin endpoints require AdminMiddleware authentication
- Actions are logged to admin_logs table
- Role-based access control for sensitive operations

---

## 8. Migration Priority

### Phase 1 - Core Infrastructure
1. Laravel project setup
2. Database migrations
3. Eloquent models with relationships
4. Authentication system (Laravel Sanctum)

### Phase 2 - Public API
1. Auth routes
2. User routes
3. Ad routes
4. Category routes

### Phase 3 - Admin Panel
1. Admin authentication
2. Dashboard & stats
3. User management
4. Ad management
5. Report management
6. Analytics

### Phase 4 - Additional Features
1. File upload handling
2. Export functionality
3. Notifications
4. Messaging system

---

## 9. Technical Debt Identified

1. **Large Controller:** AdminController has 1300+ lines - needs splitting
2. **Missing Models:** No dedicated models for Category, Report, Notification, etc.
3. **Raw SQL in Controllers:** Some queries should be in models/repositories
4. **Duplicate Code:** Token handling duplicated between Auth and Admin controllers
5. **No Service Layer:** Business logic mixed with controller logic

---

## 10. Laravel Migration Benefits

- **Eloquent ORM:** Replace raw SQL with elegant model relationships
- **Form Requests:** Centralized validation
- **Middleware:** Clean authentication/authorization
- **Route Model Binding:** Automatic model resolution
- **Resource Controllers:** RESTful conventions
- **Blade Templates:** Secure view rendering
- **Artisan Commands:** Database migrations, seeding
- **Testing:** Built-in testing framework
- **Security:** CSRF, XSS protection out of the box

---

*Document Created: January 2026*  
*Last Updated: January 2026*
