# Migrations Documentation

## Overview

All migrations are located in `database/migrations/`. They are designed to run in order based on their timestamp prefix.

---

## Migration Files

### 1. Users Table
**File:** `2026_01_01_000001_create_users_table.php`

Creates the main users table with admin fields, plus password_reset_tokens and sessions tables.

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('email', 150)->unique();
    $table->string('phone', 20)->unique()->nullable();
    $table->string('password', 255);
    $table->string('avatar', 255)->default('default-avatar.png');
    $table->text('bio')->nullable();
    $table->string('location', 100)->nullable();
    $table->decimal('rating', 2, 1)->default(0.0);
    $table->integer('review_count')->default(0);
    $table->integer('active_listings')->default(0);
    $table->integer('sold_items')->default(0);
    $table->integer('followers')->default(0);
    $table->boolean('is_verified')->default(false);
    $table->boolean('is_active')->default(true);
    $table->boolean('is_admin')->default(false);
    $table->enum('admin_role', ['super_admin', 'admin', 'moderator'])->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('last_login')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

**Indexes:** email, phone, location, is_admin

---

### 2. Categories Table
**File:** `2026_01_01_000002_create_categories_table.php`

Hierarchical categories with self-referencing parent_id.

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('slug', 100)->unique();
    $table->string('icon', 50)->nullable();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->integer('display_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Indexes:** slug, parent_id

---

### 3. Ads Table
**File:** `2026_01_01_000003_create_ads_table.php`

Main listings/advertisements table.

```php
Schema::create('ads', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->constrained()->restrictOnDelete();
    $table->string('title', 200);
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->enum('condition', ['new', 'like_new', 'good', 'fair', 'poor'])->default('good');
    $table->string('location', 100);
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->enum('status', ['active', 'sold', 'expired', 'deleted', 'pending', 'rejected'])->default('active');
    $table->string('rejection_reason')->nullable();
    $table->integer('views')->default(0);
    $table->integer('favorites')->default(0);
    $table->boolean('is_promoted')->default(false);
    $table->timestamp('promoted_until')->nullable();
    $table->boolean('is_featured')->default(false);
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
    $table->fullText(['title', 'description']);
});
```

**Indexes:** user_id, category_id, status, location, price, created_at
**Full-text:** title, description

---

### 4. Ad Images Table
**File:** `2026_01_01_000004_create_ad_images_table.php`

```php
Schema::create('ad_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
    $table->string('image_url', 255);
    $table->integer('display_order')->default(0);
    $table->boolean('is_primary')->default(false);
    $table->timestamp('created_at')->useCurrent();
});
```

---

### 5. Favorites Table
**File:** `2026_01_01_000005_create_favorites_table.php`

Pivot table for user favorites.

```php
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
    $table->timestamp('created_at')->useCurrent();
    $table->unique(['user_id', 'ad_id']);
});
```

---

### 6. Conversations Table
**File:** `2026_01_01_000006_create_conversations_table.php`

```php
Schema::create('conversations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
    $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
    $table->text('last_message')->nullable();
    $table->timestamp('last_message_at')->nullable();
    $table->boolean('is_read_by_buyer')->default(true);
    $table->boolean('is_read_by_seller')->default(false);
    $table->timestamps();
    $table->unique(['ad_id', 'buyer_id', 'seller_id']);
});
```

---

### 7. Messages Table
**File:** `2026_01_01_000007_create_messages_table.php`

```php
Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
    $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
    $table->text('message');
    $table->string('image_url', 255)->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('created_at')->useCurrent();
});
```

---

### 8. Reviews Table
**File:** `2026_01_01_000008_create_reviews_table.php`

```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('reviewed_user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('ad_id')->nullable()->constrained()->nullOnDelete();
    $table->tinyInteger('rating')->unsigned();
    $table->text('comment')->nullable();
    $table->timestamps();
    $table->unique(['reviewer_id', 'reviewed_user_id', 'ad_id']);
});
```

---

### 9. Notifications Table
**File:** `2026_01_01_000009_create_notifications_table.php`

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['message', 'favorite', 'review', 'ad_sold', 'ad_expired', 'system']);
    $table->string('title', 200);
    $table->text('message');
    $table->unsignedBigInteger('related_id')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('created_at')->useCurrent();
});
```

---

### 10. Reports Table
**File:** `2026_01_01_000010_create_reports_table.php`

```php
Schema::create('reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
    $table->enum('reported_type', ['ad', 'user', 'message']);
    $table->unsignedBigInteger('reported_id');
    $table->string('reason', 200);
    $table->text('description')->nullable();
    $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
    $table->timestamps();
});
```

---

### 11. Admin Logs Table
**File:** `2026_01_01_000011_create_admin_logs_table.php`

```php
Schema::create('admin_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
    $table->string('action', 100);
    $table->unsignedBigInteger('target_id')->nullable();
    $table->string('target_type', 50)->nullable();
    $table->text('details')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent', 255)->nullable();
    $table->timestamp('created_at')->useCurrent();
});
```

---

### 12. Personal Access Tokens Table
**File:** `2026_01_01_000012_create_personal_access_tokens_table.php`

Laravel Sanctum tokens table.

```php
Schema::create('personal_access_tokens', function (Blueprint $table) {
    $table->id();
    $table->morphs('tokenable');
    $table->string('name');
    $table->string('token', 64)->unique();
    $table->text('abilities')->nullable();
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
});
```

---

## Running Migrations

```bash
# Run all migrations
php artisan migrate

# Run with seeding
php artisan migrate --seed

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset
```

---

## Seeders

### DatabaseSeeder
Runs CategorySeeder and AdminUserSeeder.

### CategorySeeder
Creates 10 default categories:
- Electronics, Vehicles, Property, Fashion, Home & Garden
- Sports, Books, Pets, Jobs, Services

### AdminUserSeeder
Creates default admin users:
- `admin@marketlocal.com` (super_admin) - Password: admin123
- `moderator@marketlocal.com` (moderator) - Password: moderator123

---

*Document Created: January 2026*
