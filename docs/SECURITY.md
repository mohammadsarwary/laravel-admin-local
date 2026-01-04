# Security Documentation

## Overview

This document outlines the security measures implemented in the Laravel admin panel and API.

---

## Authentication Security

### Password Hashing
- **Algorithm:** bcrypt
- **Rounds:** 12 (configurable via `BCRYPT_ROUNDS` in `.env`)
- **Implementation:** Laravel's `Hash` facade

```php
// Password is automatically hashed via User model cast
protected function casts(): array
{
    return [
        'password' => 'hashed',
    ];
}
```

### Token Security
- **Type:** Laravel Sanctum API tokens
- **Storage:** Hashed in database
- **Expiry:** Access tokens expire in 1 hour
- **Revocation:** Tokens can be revoked individually or all at once

### Session Security
- **Driver:** Database (configurable)
- **Encryption:** Sessions are encrypted
- **HTTPS:** Recommended for production

---

## Input Validation

### Form Requests
All user input is validated using Laravel Form Request classes:

```php
// Example: RegisterRequest
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'min:2', 'max:100'],
        'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
        'password' => ['required', 'string', 'min:6'],
    ];
}
```

### Validation Rules Applied
| Field | Validations |
|-------|-------------|
| Email | required, email format, unique, max length |
| Password | required, min 6 characters |
| Name | required, min 2, max 100 characters |
| Phone | regex pattern, unique |
| Price | numeric, min 0 |
| Title | required, min 5, max 200 |
| Description | required, min 20 |

---

## SQL Injection Prevention

### Eloquent ORM
All database queries use Eloquent ORM or Query Builder with parameter binding:

```php
// Safe - uses parameter binding
User::where('email', $email)->first();

// Safe - Query Builder with bindings
DB::table('users')->where('id', $id)->get();
```

### No Raw SQL
Raw SQL queries are avoided. When necessary, parameter binding is used:

```php
// If raw SQL is needed (avoided in this project)
DB::select('SELECT * FROM users WHERE id = ?', [$id]);
```

---

## Cross-Site Scripting (XSS) Prevention

### Blade Templates
All output is automatically escaped in Blade:

```blade
{{-- Escaped output (safe) --}}
{{ $user->name }}

{{-- Unescaped output (use with caution) --}}
{!! $trustedHtml !!}
```

### JSON Responses
API responses use `JSON_UNESCAPED_UNICODE` for proper encoding.

---

## Cross-Site Request Forgery (CSRF)

### Web Routes
CSRF protection is enabled for all web routes via middleware.

### API Routes
API routes use token-based authentication instead of CSRF tokens.

### Blade Forms
```blade
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

---

## Authorization

### Admin Middleware
Custom middleware verifies admin access:

```php
public function handle(Request $request, Closure $next, ?string $role = null): Response
{
    $user = $request->user();

    if (!$user || !$user->is_admin || !$user->is_active) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    if ($role && !$user->hasAdminRole($role)) {
        return response()->json(['success' => false, 'message' => 'Insufficient permissions'], 403);
    }

    return $next($request);
}
```

### Resource Authorization
Form Requests include authorization checks:

```php
// UpdateAdRequest
public function authorize(): bool
{
    $ad = $this->route('ad');
    return $ad && $this->user()->id === $ad->user_id;
}
```

---

## File Upload Security

### Validation
```php
$request->validate([
    'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
    'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
]);
```

### Restrictions
- **Allowed types:** JPEG, PNG, JPG, WebP
- **Max size:** 5MB (5120 KB)
- **Storage:** Public disk with unique filenames

### File Naming
Files are renamed to prevent path traversal:
```php
$path = $file->store('avatars', 'public');
```

---

## Rate Limiting

### Recommended Configuration
Add to `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->throttleApi('60,1'); // 60 requests per minute
})
```

### Auth Endpoints
Consider stricter limits for authentication endpoints:
```php
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

---

## Security Headers

### Recommended Headers
Configure in web server or middleware:

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self'
```

---

## Environment Security

### .env File
- Never commit `.env` to version control
- Use `.env.example` as template
- Set proper permissions (600)

### Sensitive Data
Store in `.env`:
- `APP_KEY` - Application encryption key
- `DB_PASSWORD` - Database password
- `MAIL_PASSWORD` - Mail server password
- API keys and secrets

### Production Settings
```env
APP_ENV=production
APP_DEBUG=false
```

---

## Logging & Monitoring

### Admin Action Logging
All admin actions are logged:

```php
AdminLog::log($admin, 'suspend_user', $userId, 'user', 'Reason for suspension');
```

### Logged Information
- Admin ID
- Action type
- Target ID and type
- Details/reason
- IP address
- User agent
- Timestamp

---

## Security Checklist

### Before Deployment
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Generate new `APP_KEY`
- [ ] Use strong database password
- [ ] Enable HTTPS
- [ ] Configure CORS properly
- [ ] Set up rate limiting
- [ ] Review file permissions
- [ ] Remove debug routes/endpoints

### Ongoing
- [ ] Keep Laravel and packages updated
- [ ] Monitor admin logs
- [ ] Review access patterns
- [ ] Rotate secrets periodically
- [ ] Backup database regularly

---

## Improvements from Legacy

| Security Aspect | Legacy | Laravel |
|-----------------|--------|---------|
| Password Hashing | bcrypt (manual) | bcrypt (automatic via cast) |
| SQL Injection | PDO prepared statements | Eloquent ORM |
| Input Validation | Custom Validator class | Form Request classes |
| CSRF | None (API only) | Built-in for web routes |
| XSS | Manual escaping | Automatic Blade escaping |
| Auth | Custom JWT | Laravel Sanctum |
| Secrets | Hardcoded | Environment variables |

---

*Document Created: January 2026*
