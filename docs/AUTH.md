# Authentication Documentation

## Overview

The application uses **Laravel Sanctum** for API token authentication, replacing the legacy custom JWT implementation.

---

## Authentication Flow

### User Registration
1. User submits registration data (name, email, password)
2. Data is validated via `RegisterRequest`
3. User is created with hashed password
4. Access token (1 hour) and refresh token (30 days) are generated
5. Tokens returned to client

### User Login
1. User submits email and password
2. Credentials validated via `LoginRequest`
3. Email/password verified against database
4. Check if user is active
5. Update last_login timestamp
6. Generate access and refresh tokens
7. Return user data with tokens

### Token Refresh
1. Client sends refresh token
2. Token validated via Sanctum
3. New access token generated
4. Return new access token

### Logout
1. Client sends logout request with token
2. Current access token is deleted
3. Success response returned

---

## Token Configuration

### Access Token
- **Expiry:** 1 hour (60 minutes)
- **Abilities:** `['*']` (all abilities)
- **Usage:** API requests

### Refresh Token
- **Expiry:** 30 days
- **Abilities:** `['refresh']`
- **Usage:** Token refresh only

### Admin Tokens
- **Abilities:** `['admin']` or `['admin', 'refresh']`
- **Additional checks:** `is_admin` flag verified

---

## Middleware

### auth:sanctum
Laravel Sanctum's built-in authentication middleware. Applied to all protected routes.

```php
Route::middleware('auth:sanctum')->group(function () {
    // Protected routes
});
```

### admin
Custom middleware for admin-only routes. Checks:
1. User is authenticated
2. User has `is_admin = true`
3. User is active
4. (Optional) User has required admin role

```php
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin routes
});

// With role requirement
Route::middleware(['auth:sanctum', 'admin:super_admin'])->group(function () {
    // Super admin only routes
});
```

---

## Admin Roles

### Role Hierarchy
| Role | Level | Description |
|------|-------|-------------|
| super_admin | 3 | Full access, can delete users |
| admin | 2 | Most admin functions |
| moderator | 1 | Limited admin functions |

### Role Checking
```php
// In User model
public function hasAdminRole(string $role): bool
{
    $roleHierarchy = [
        'super_admin' => 3,
        'admin' => 2,
        'moderator' => 1,
    ];

    $userLevel = $roleHierarchy[$this->admin_role] ?? 0;
    $requiredLevel = $roleHierarchy[$role] ?? 0;

    return $userLevel >= $requiredLevel;
}
```

---

## API Headers

### Request Headers
```
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
```

### Response Format
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "access_token": "1|abc123...",
        "refresh_token": "2|def456...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

---

## Password Security

### Hashing
- Algorithm: bcrypt (via Laravel's `Hash` facade)
- Rounds: 12 (configurable in `.env`)

```php
// Hashing
$password = Hash::make($plainPassword);

// Verification
if (Hash::check($plainPassword, $hashedPassword)) {
    // Valid
}
```

### Password Requirements
- Minimum 6 characters
- No complexity requirements (can be added via validation rules)

---

## Session Management

### Token Storage
Tokens are stored in the `personal_access_tokens` table:
- `tokenable_type` - User model class
- `tokenable_id` - User ID
- `name` - Token name (access_token, refresh_token)
- `token` - Hashed token
- `abilities` - JSON array of abilities
- `expires_at` - Expiration timestamp

### Token Revocation
```php
// Revoke current token
$request->user()->currentAccessToken()->delete();

// Revoke all tokens
$user->tokens()->delete();

// Revoke specific token
$user->tokens()->where('name', 'access_token')->delete();
```

---

## Security Best Practices

1. **HTTPS Only** - Always use HTTPS in production
2. **Token Expiry** - Short-lived access tokens (1 hour)
3. **Secure Storage** - Store tokens securely on client
4. **CSRF Protection** - Enabled for web routes
5. **Rate Limiting** - Apply rate limiting to auth endpoints
6. **Password Hashing** - bcrypt with 12 rounds
7. **Input Validation** - All inputs validated via Form Requests

---

## Migration from Legacy JWT

### Changes Made
| Legacy | Laravel |
|--------|---------|
| Custom JWT class | Laravel Sanctum |
| Manual token generation | `$user->createToken()` |
| refresh_tokens table | personal_access_tokens table |
| Custom middleware | auth:sanctum + custom admin |
| Manual validation | Form Request classes |

### Backward Compatibility
The API response format remains the same to ensure frontend compatibility:
```json
{
    "success": true,
    "message": "...",
    "data": { ... }
}
```

---

*Document Created: January 2026*
