# Models Documentation

## Overview

All Eloquent models are located in `app/Models/`. Each model includes:
- Fillable attributes
- Casts for type conversion
- Relationships
- Scopes for common queries
- Helper methods

---

## User Model

**Location:** `app/Models/User.php`

### Fillable Attributes
```php
'name', 'email', 'phone', 'password', 'avatar', 'bio', 'location',
'rating', 'review_count', 'active_listings', 'sold_items', 'followers',
'is_verified', 'is_active', 'is_admin', 'admin_role', 'last_login'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `ads()` | HasMany | Ad |
| `favorites()` | HasMany | Favorite |
| `favoriteAds()` | BelongsToMany | Ad (through favorites) |
| `reviewsGiven()` | HasMany | Review (as reviewer) |
| `reviewsReceived()` | HasMany | Review (as reviewed) |
| `notifications()` | HasMany | Notification |
| `messages()` | HasMany | Message |
| `conversationsAsBuyer()` | HasMany | Conversation |
| `conversationsAsSeller()` | HasMany | Conversation |
| `reports()` | HasMany | Report |
| `adminLogs()` | HasMany | AdminLog |

### Scopes
- `scopeActive($query)` - Active users only
- `scopeAdmins($query)` - Admin users only
- `scopeVerified($query)` - Verified users only

### Helper Methods
- `isAdmin(): bool`
- `isSuperAdmin(): bool`
- `hasAdminRole(string $role): bool`
- `incrementStat(string $field, int $amount): bool`
- `updateRating(): void`

---

## Ad Model

**Location:** `app/Models/Ad.php`

### Fillable Attributes
```php
'user_id', 'category_id', 'title', 'description', 'price', 'condition',
'location', 'latitude', 'longitude', 'status', 'rejection_reason',
'views', 'favorites', 'is_promoted', 'promoted_until', 'is_featured',
'approved_at', 'expires_at'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `user()` | BelongsTo | User |
| `category()` | BelongsTo | Category |
| `images()` | HasMany | AdImage |
| `primaryImage()` | HasOne | AdImage (where is_primary) |
| `favoritedBy()` | BelongsToMany | User (through favorites) |
| `favorites()` | HasMany | Favorite |
| `conversations()` | HasMany | Conversation |
| `reviews()` | HasMany | Review |

### Scopes
- `scopeActive($query)` - Active ads only
- `scopePending($query)` - Pending ads only
- `scopeFeatured($query)` - Featured ads only
- `scopePromoted($query)` - Currently promoted ads
- `scopeByCategory($query, $categoryId)`
- `scopeByLocation($query, $location)`
- `scopePriceRange($query, $min, $max)`
- `scopeSearch($query, $term)`

### Helper Methods
- `incrementViews(): void`
- `markAsSold(): bool`
- `approve(): bool`
- `reject(string $reason): bool`
- `feature(bool $featured): bool`
- `promote(int $days): bool`
- `isFavoritedBy(?User $user): bool`

---

## Category Model

**Location:** `app/Models/Category.php`

### Fillable Attributes
```php
'name', 'slug', 'icon', 'parent_id', 'display_order', 'is_active'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `parent()` | BelongsTo | Category (self) |
| `children()` | HasMany | Category (self) |
| `ads()` | HasMany | Ad |

### Scopes
- `scopeActive($query)`
- `scopeOrdered($query)`
- `scopeParentCategories($query)`

---

## AdImage Model

**Location:** `app/Models/AdImage.php`

### Fillable Attributes
```php
'ad_id', 'image_url', 'display_order', 'is_primary'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `ad()` | BelongsTo | Ad |

### Helper Methods
- `makePrimary(): void`

---

## Favorite Model

**Location:** `app/Models/Favorite.php`

### Fillable Attributes
```php
'user_id', 'ad_id'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `user()` | BelongsTo | User |
| `ad()` | BelongsTo | Ad |

---

## Conversation Model

**Location:** `app/Models/Conversation.php`

### Fillable Attributes
```php
'ad_id', 'buyer_id', 'seller_id', 'last_message', 'last_message_at',
'is_read_by_buyer', 'is_read_by_seller'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `ad()` | BelongsTo | Ad |
| `buyer()` | BelongsTo | User |
| `seller()` | BelongsTo | User |
| `messages()` | HasMany | Message |
| `latestMessage()` | HasOne | Message (latest) |

### Helper Methods
- `markAsReadByBuyer(): void`
- `markAsReadBySeller(): void`

---

## Message Model

**Location:** `app/Models/Message.php`

### Fillable Attributes
```php
'conversation_id', 'sender_id', 'message', 'image_url', 'is_read'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `conversation()` | BelongsTo | Conversation |
| `sender()` | BelongsTo | User |

### Helper Methods
- `markAsRead(): void`

---

## Review Model

**Location:** `app/Models/Review.php`

### Fillable Attributes
```php
'reviewer_id', 'reviewed_user_id', 'ad_id', 'rating', 'comment'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `reviewer()` | BelongsTo | User |
| `reviewedUser()` | BelongsTo | User |
| `ad()` | BelongsTo | Ad |

### Model Events
- `created` - Updates reviewed user's rating
- `updated` - Updates reviewed user's rating
- `deleted` - Updates reviewed user's rating

---

## Notification Model

**Location:** `app/Models/Notification.php`

### Fillable Attributes
```php
'user_id', 'type', 'title', 'message', 'related_id', 'is_read'
```

### Notification Types
- `message`, `favorite`, `review`, `ad_sold`, `ad_expired`, `system`

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `user()` | BelongsTo | User |

### Scopes
- `scopeUnread($query)`
- `scopeOfType($query, string $type)`

---

## Report Model

**Location:** `app/Models/Report.php`

### Fillable Attributes
```php
'reporter_id', 'reported_type', 'reported_id', 'reason', 'description', 'status'
```

### Report Types
- `ad`, `user`, `message`

### Report Statuses
- `pending`, `reviewed`, `resolved`, `dismissed`

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `reporter()` | BelongsTo | User |

### Scopes
- `scopePending($query)`
- `scopeResolved($query)`
- `scopeDismissed($query)`
- `scopeOfType($query, string $type)`

### Helper Methods
- `getReportedContent()` - Returns the reported entity
- `resolve(): bool`
- `dismiss(): bool`

---

## AdminLog Model

**Location:** `app/Models/AdminLog.php`

### Fillable Attributes
```php
'admin_id', 'action', 'target_id', 'target_type', 'details', 'ip_address', 'user_agent'
```

### Relationships
| Relationship | Type | Related Model |
|--------------|------|---------------|
| `admin()` | BelongsTo | User |

### Static Methods
- `log(User $admin, string $action, ?int $targetId, ?string $targetType, ?string $details): self`

### Scopes
- `scopeByAdmin($query, int $adminId)`
- `scopeByAction($query, string $action)`

---

*Document Created: January 2026*
