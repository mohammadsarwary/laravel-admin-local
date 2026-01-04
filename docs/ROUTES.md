# Routes Documentation

## Overview

All routes are defined in `routes/api.php` and `routes/web.php`. The API uses Laravel Sanctum for authentication.

---

## Public API Routes

### Authentication
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| POST | `/api/auth/register` | auth.register | Api\AuthController@register | Register new user |
| POST | `/api/auth/login` | auth.login | Api\AuthController@login | User login |

### Categories
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/categories` | categories.index | Api\CategoryController@index | List all categories |

### Ads (Public)
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/ads` | ads.index | Api\AdController@index | List ads with filters |
| GET | `/api/ads/{ad}` | ads.show | Api\AdController@show | Get ad details |

### Users (Public)
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/users/{user}` | users.show | Api\UserController@show | Get user profile |
| GET | `/api/users/{user}/ads` | users.ads | Api\UserController@ads | Get user's ads |

---

## Protected API Routes (Requires auth:sanctum)

### Authentication
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| POST | `/api/auth/refresh` | auth.refresh | Api\AuthController@refresh | Refresh token |
| POST | `/api/auth/logout` | auth.logout | Api\AuthController@logout | Logout user |
| GET | `/api/auth/me` | auth.me | Api\AuthController@me | Get current user |

### User Profile
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| PUT | `/api/users/profile` | users.updateProfile | Api\UserController@updateProfile | Update profile |
| POST | `/api/users/avatar` | users.updateAvatar | Api\UserController@updateAvatar | Upload avatar |
| POST | `/api/users/change-password` | users.changePassword | Api\UserController@changePassword | Change password |
| DELETE | `/api/users/account` | users.deleteAccount | Api\UserController@deleteAccount | Delete account |
| GET | `/api/users/favorites` | users.favorites | Api\UserController@favorites | Get favorites |

### Ads (Protected)
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| POST | `/api/ads` | ads.store | Api\AdController@store | Create ad |
| PUT | `/api/ads/{ad}` | ads.update | Api\AdController@update | Update ad |
| DELETE | `/api/ads/{ad}` | ads.destroy | Api\AdController@destroy | Delete ad |
| POST | `/api/ads/{ad}/sold` | ads.markAsSold | Api\AdController@markAsSold | Mark as sold |
| POST | `/api/ads/{ad}/images` | ads.uploadImages | Api\AdController@uploadImages | Upload images |
| POST | `/api/ads/{ad}/favorite` | ads.toggleFavorite | Api\AdController@toggleFavorite | Toggle favorite |

---

## Admin API Routes (Requires auth:sanctum + admin middleware)

### Admin Authentication
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| POST | `/api/admin/login` | admin.login | Admin\AuthController@login | Admin login |
| GET | `/api/admin/verify` | admin.verify | Admin\AuthController@verify | Verify session |

### Dashboard
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/admin/stats` | admin.stats | Admin\DashboardController@stats | Get statistics |
| GET | `/api/admin/activity` | admin.activity | Admin\DashboardController@activity | Recent activity |

### User Management
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/admin/users` | admin.users.index | Admin\UserController@index | List users |
| GET | `/api/admin/users/export` | admin.users.export | Admin\UserController@export | Export users |
| POST | `/api/admin/users/create` | admin.users.store | Admin\UserController@store | Create user |
| POST | `/api/admin/users/bulk-action` | admin.users.bulkAction | Admin\UserController@bulkAction | Bulk action |
| GET | `/api/admin/users/{user}` | admin.users.show | Admin\UserController@show | User details |
| GET | `/api/admin/users/{user}/activity` | admin.users.activity | Admin\UserController@activity | User activity |
| PUT | `/api/admin/users/{user}/suspend` | admin.users.suspend | Admin\UserController@suspend | Suspend user |
| PUT | `/api/admin/users/{user}/activate` | admin.users.activate | Admin\UserController@activate | Activate user |
| PUT | `/api/admin/users/{user}/ban` | admin.users.ban | Admin\UserController@ban | Ban user |
| PUT | `/api/admin/users/{user}/verify` | admin.users.verify | Admin\UserController@verifyUser | Verify user |
| DELETE | `/api/admin/users/{user}` | admin.users.destroy | Admin\UserController@destroy | Delete user |

### Ad Management
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/admin/ads` | admin.ads.index | Admin\AdController@index | List ads |
| GET | `/api/admin/ads/export` | admin.ads.export | Admin\AdController@export | Export ads |
| POST | `/api/admin/ads/bulk-action` | admin.ads.bulkAction | Admin\AdController@bulkAction | Bulk action |
| GET | `/api/admin/ads/{ad}` | admin.ads.show | Admin\AdController@show | Ad details |
| PUT | `/api/admin/ads/{ad}/approve` | admin.ads.approve | Admin\AdController@approve | Approve ad |
| PUT | `/api/admin/ads/{ad}/reject` | admin.ads.reject | Admin\AdController@reject | Reject ad |
| PUT | `/api/admin/ads/{ad}/feature` | admin.ads.feature | Admin\AdController@feature | Feature ad |
| PUT | `/api/admin/ads/{ad}/promote` | admin.ads.promote | Admin\AdController@promote | Promote ad |
| DELETE | `/api/admin/ads/{ad}` | admin.ads.destroy | Admin\AdController@destroy | Delete ad |

### Report Management
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/admin/reports` | admin.reports.index | Admin\ReportController@index | List reports |
| GET | `/api/admin/reports/stats` | admin.reports.stats | Admin\ReportController@stats | Report stats |
| GET | `/api/admin/reports/{report}` | admin.reports.show | Admin\ReportController@show | Report details |
| PUT | `/api/admin/reports/{report}/resolve` | admin.reports.resolve | Admin\ReportController@resolve | Resolve report |
| PUT | `/api/admin/reports/{report}/dismiss` | admin.reports.dismiss | Admin\ReportController@dismiss | Dismiss report |
| POST | `/api/admin/reports/{report}/action` | admin.reports.takeAction | Admin\ReportController@takeAction | Take action |

### Analytics
| Method | URI | Name | Controller | Description |
|--------|-----|------|------------|-------------|
| GET | `/api/admin/analytics/users` | admin.analytics.users | Admin\AnalyticsController@users | User growth |
| GET | `/api/admin/analytics/ads` | admin.analytics.ads | Admin\AnalyticsController@ads | Ad trends |
| GET | `/api/admin/analytics/categories` | admin.analytics.categories | Admin\AnalyticsController@categories | Category stats |
| GET | `/api/admin/analytics/locations` | admin.analytics.locations | Admin\AnalyticsController@locations | Location stats |

---

## Query Parameters

### Ads Listing (`GET /api/ads`)
- `category_id` - Filter by category
- `search` - Search in title/description
- `min_price` - Minimum price
- `max_price` - Maximum price
- `location` - Filter by location
- `condition` - Filter by condition (new, like_new, good, fair, poor)
- `sort` - Sort order (newest, oldest, price_asc, price_desc)
- `page` - Page number
- `limit` - Items per page (max 100)

### Admin User Listing (`GET /api/admin/users`)
- `search` - Search by name, email, phone
- `status` - Filter by status (active, inactive, verified, admin)
- `page` - Page number
- `limit` - Items per page

---

*Document Created: January 2026*
