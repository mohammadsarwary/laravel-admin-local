# API Documentation

Complete list of all API endpoints in the Market Local application.

---

## 📋 Public API Routes (No Authentication Required)

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | User registration |
| POST | `/api/auth/login` | User login |

### Public Data
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all active categories |
| GET | `/api/ads` | List all ads (public) |
| GET | `/api/ads/{ad}` | Get single ad details |
| GET | `/api/users/{user}` | Get user profile |
| GET | `/api/users/{user}/ads` | Get user's ads |

---

## 🔐 Protected API Routes (Requires User Authentication - `auth:sanctum`)

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/refresh` | Refresh authentication token |
| POST | `/api/auth/logout` | User logout |
| GET | `/api/auth/me` | Get current authenticated user |

### User Profile Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| PUT | `/api/users/profile` | Update user profile |
| POST | `/api/users/avatar` | Update user avatar |
| POST | `/api/users/change-password` | Change password |
| DELETE | `/api/users/account` | Delete user account |
| GET | `/api/users/favorites` | Get user's favorite ads |

### Ads CRUD
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/ads` | Create new ad |
| PUT | `/api/ads/{ad}` | Update ad |
| DELETE | `/api/ads/{ad}` | Delete ad |
| POST | `/api/ads/{ad}/sold` | Mark ad as sold |
| POST | `/api/ads/{ad}/images` | Upload ad images |
| POST | `/api/ads/{ad}/favorite` | Toggle ad favorite status |

---

## 👑 Admin API Routes (Requires Admin Authentication - `auth:sanctum` + `admin` middleware)

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/admin/login` | Admin login |
| GET | `/api/admin/verify` | Verify admin authentication |

### Dashboard
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/stats` | Get dashboard statistics |
| GET | `/api/admin/activity` | Get recent activity log |

### User Management (Full CRUD)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/users` | List all users with pagination |
| GET | `/api/admin/users/export` | Export users to CSV |
| POST | `/api/admin/users/create` | Create new user |
| POST | `/api/admin/users/bulk-action` | Bulk actions (activate, deactivate, delete) |
| GET | `/api/admin/users/{user}` | Get user details |
| GET | `/api/admin/users/{user}/activity` | Get user activity log |
| PUT | `/api/admin/users/{user}/suspend` | Suspend user |
| PUT | `/api/admin/users/{user}/activate` | Activate user |
| PUT | `/api/admin/users/{user}/ban` | Ban user |
| PUT | `/api/admin/users/{user}/verify` | Verify user |
| DELETE | `/api/admin/users/{user}` | Delete user |

### Ad Management (Full CRUD + Actions)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/ads` | List all ads with pagination |
| GET | `/api/admin/ads/export` | Export ads to CSV |
| POST | `/api/admin/ads/bulk-action` | Bulk actions (approve, reject, feature, promote, delete) |
| GET | `/api/admin/ads/{ad}` | Get ad details |
| PUT | `/api/admin/ads/{ad}/approve` | Approve ad |
| PUT | `/api/admin/ads/{ad}/reject` | Reject ad |
| PUT | `/api/admin/ads/{ad}/feature` | Feature ad |
| PUT | `/api/admin/ads/{ad}/promote` | Promote ad |
| DELETE | `/api/admin/ads/{ad}` | Delete ad |

### Report Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/reports` | List all reports |
| GET | `/api/admin/reports/stats` | Get report statistics |
| GET | `/api/admin/reports/{report}` | Get report details |
| PUT | `/api/admin/reports/{report}/resolve` | Resolve report |
| PUT | `/api/admin/reports/{report}/dismiss` | Dismiss report |
| POST | `/api/admin/reports/{report}/action` | Take action on report |

### Category Management (Full CRUD)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/categories` | List all categories with pagination |
| GET | `/api/admin/categories/export` | Export categories to CSV |
| POST | `/api/admin/categories` | Create new category |
| POST | `/api/admin/categories/bulk-action` | Bulk actions (activate, deactivate, delete) |
| GET | `/api/admin/categories/{category}` | Get category details |
| PUT | `/api/admin/categories/{category}` | Update category |
| PUT | `/api/admin/categories/{category}/toggle-status` | Toggle category active status |
| DELETE | `/api/admin/categories/{category}` | Delete category |

### Analytics
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/analytics/users` | User analytics data |
| GET | `/api/admin/analytics/ads` | Ads analytics data |
| GET | `/api/admin/analytics/categories` | Categories analytics data |
| GET | `/api/admin/analytics/locations` | Location analytics data |

---

## 🔑 Authentication

All protected and admin routes require a Bearer token in the Authorization header:

```http
Authorization: Bearer {token}
```

### Admin Login Credentials (Demo)
- **Email:** `admin@bazarino.store`
- **Password:** `password`

---

## 📊 Response Format

All API responses follow this format:

### Success Response
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

---

## 📄 Pagination

List endpoints support pagination with these parameters:

- `page` - Page number (default: 1)
- `limit` - Items per page (default: 20, max: 100)

Response includes pagination metadata:
```json
{
    "success": true,
    "data": {
        "items": [ ... ],
        "pagination": {
            "page": 1,
            "limit": 20,
            "total": 100,
            "pages": 5
        }
    }
}
```

---

## 🔍 Filtering & Search

Most list endpoints support filtering:

- `search` - Search query
- `status` - Filter by status
- `category` - Filter by category ID
- `date_from` - Start date filter
- `date_to` - End date filter
- `sort_by` - Sort field
- `sort_order` - Sort direction (asc/desc)

---

## 📝 Notes

- All timestamps are in ISO 8601 format
- All monetary values are in USD
- File uploads support: JPG, PNG, WebP (max 5MB)
- Rate limiting may apply to public endpoints
- Admin routes require both authentication and admin role verification

---

*Last Updated: January 5, 2026*
