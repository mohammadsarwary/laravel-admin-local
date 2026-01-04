# Admin Panel Setup Guide

## Quick Start (Local Development)

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Create Database
```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

### 4. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000/login`

**Login Credentials:**
- Email: `admin@bazarino.store`
- Password: `password`

---

## cPanel Deployment (Production)

### Step 1: Prepare Local Project
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 2: Upload to cPanel

**Option A: Using FTP/SFTP**
1. Connect via FileZilla or similar
2. Upload entire project to `public_html/`
3. Or create subdomain and upload there

**Option B: Using Git (if available)**
```bash
cd /home/username/public_html
git clone your-repo-url laravel-admin
cd laravel-admin
```

### Step 3: Configure Document Root

In **cPanel → Addon Domains** or **Subdomains**:
- Point document root to: `/public_html/laravel-admin/public`

### Step 4: Set Permissions

Via SSH:
```bash
cd /home/username/public_html/laravel-admin

# Set directory permissions
chmod 755 bootstrap/cache
chmod 755 storage
chmod -R 755 storage/*
chmod -R 755 public

# Set ownership
chown -R username:username .
```

### Step 5: Configure .env

Edit `.env` with cPanel credentials:
```
APP_URL=https://market.bazarino.store
APP_DEBUG=false
APP_ENV=production

DB_HOST=localhost
DB_DATABASE=bazarino_marketplace
DB_USERNAME=bazarino_user
DB_PASSWORD=your_secure_password
```

### Step 6: Create Database in cPanel

1. Go to **cPanel → MySQL Databases**
2. Create database: `bazarino_marketplace`
3. Create user: `bazarino_user` with strong password
4. Add user to database with ALL privileges

### Step 7: Run Migrations via SSH

```bash
cd /home/username/public_html/laravel-admin

php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan key:generate
```

### Step 8: Enable HTTPS

1. Go to **cPanel → AutoSSL** or **Let's Encrypt**
2. Install SSL certificate
3. Update `.env`: `APP_URL=https://market.bazarino.store`

### Step 9: Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 10: Test Installation

Visit: `https://market.bazarino.store/login`

Login with:
- Email: `admin@bazarino.store`
- Password: `password`

---

## Admin Routes

### Web Routes (Blade Views)
- `/login` - Admin login page
- `/admin/dashboard` - Dashboard
- `/admin/users` - User management
- `/admin/ads` - Listings management
- `/admin/reports` - Reports
- `/admin/analytics` - Analytics
- `/admin/moderation` - Moderation queue

### API Routes (JSON)
- `POST /api/admin/login` - Admin authentication
- `GET /api/admin/stats` - Dashboard stats
- `GET /api/admin/users` - List users
- `GET /api/admin/ads` - List ads
- `GET /api/admin/reports` - List reports
- `GET /api/admin/moderation` - Moderation queue

---

## Troubleshooting

### 500 Error / Route Not Found
```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Permission Denied
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

### Database Connection Error
1. Verify credentials in `.env`
2. Test connection: `php artisan tinker`
3. Run: `DB::connection()->getPdo()`

### Composer Issues on cPanel
```bash
php composer.phar install --no-dev --optimize-autoloader
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

## Security Checklist

- [ ] Change admin password after first login
- [ ] Enable HTTPS/SSL
- [ ] Set strong database password
- [ ] Hide `.env` file (cPanel usually does this)
- [ ] Disable directory listing in `.htaccess`
- [ ] Keep Laravel updated: `composer update`
- [ ] Regular backups of database and files
- [ ] Monitor logs: `storage/logs/laravel.log`

---

## Cron Jobs (Optional)

In **cPanel → Cron Jobs**, add:
```
* * * * * /usr/bin/php /home/username/public_html/laravel-admin/artisan schedule:run >> /dev/null 2>&1
```

---

## Admin Features

✅ Dark theme with red accents  
✅ Dashboard with stats and charts  
✅ User management (view, edit, delete)  
✅ Listings/Ads management  
✅ Moderation queue  
✅ Reports management  
✅ Analytics  
✅ Responsive design  
✅ Alpine.js for interactivity  

---

## Support

For issues, check:
1. `storage/logs/laravel.log`
2. Browser console (F12)
3. cPanel error logs
4. Database connection

---

**Last Updated:** January 5, 2026
