# Market Local Admin Panel - Laravel

A modern Laravel admin panel for the Market Local marketplace application, migrated from legacy PHP.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (for frontend assets, optional)

## Installation

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=market_local
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations

```bash
php artisan migrate --seed
```

### 5. Create Storage Link

```bash
php artisan storage:link
```

### 6. Start Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Default Admin Credentials

| Email | Password | Role |
|-------|----------|------|
| admin@marketlocal.com | admin123 | super_admin |
| moderator@marketlocal.com | moderator123 | moderator |

## Project Structure

```
laravel-admin/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # Public API controllers
│   │   │   └── Admin/        # Admin API controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form request validation
│   └── Models/               # Eloquent models
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/views/          # Blade templates
├── routes/
│   ├── api.php              # API routes
│   └── web.php              # Web routes
└── docs/                     # Documentation
```

## API Endpoints

### Public Endpoints
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/categories` - List categories
- `GET /api/ads` - List ads
- `GET /api/ads/{id}` - Get ad details

### Protected Endpoints (requires authentication)
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Get current user
- `POST /api/ads` - Create ad
- `PUT /api/ads/{id}` - Update ad
- `DELETE /api/ads/{id}` - Delete ad

### Admin Endpoints (requires admin role)
- `POST /api/admin/login` - Admin login
- `GET /api/admin/stats` - Dashboard statistics
- `GET /api/admin/users` - List users
- `GET /api/admin/ads` - List ads
- `GET /api/admin/reports` - List reports

See [ROUTES.md](ROUTES.md) for complete API documentation.

## Documentation

- [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) - Project analysis and overview
- [STRUCTURE.md](STRUCTURE.md) - File structure mapping
- [ROUTES.md](ROUTES.md) - API routes documentation
- [CONTROLLERS.md](CONTROLLERS.md) - Controllers documentation
- [MODELS.md](MODELS.md) - Eloquent models documentation
- [MIGRATIONS.md](MIGRATIONS.md) - Database migrations
- [AUTH.md](AUTH.md) - Authentication system
- [SECURITY.md](SECURITY.md) - Security measures
- [TODO.md](TODO.md) - Progress and pending tasks

## Features

### User Features
- User registration and authentication
- Profile management
- Ad creation and management
- Favorites system
- Image uploads

### Admin Features
- Dashboard with statistics
- User management (CRUD, suspend, ban, verify)
- Ad management (approve, reject, feature, promote)
- Report management
- Analytics and insights
- Export functionality
- Activity logging

## Security

- Laravel Sanctum for API authentication
- bcrypt password hashing
- Form Request validation
- Eloquent ORM (SQL injection prevention)
- CSRF protection for web routes
- Admin role-based access control

## Testing

```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## Production Deployment

1. Set environment variables:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Optimize:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Set proper file permissions
4. Configure web server (Apache/Nginx)
5. Enable HTTPS

## License

MIT License

## Credits

Migrated from legacy PHP to Laravel - January 2026
