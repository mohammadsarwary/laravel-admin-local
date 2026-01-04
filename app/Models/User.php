<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'bio',
        'location',
        'rating',
        'review_count',
        'active_listings',
        'sold_items',
        'followers',
        'is_verified',
        'is_active',
        'is_admin',
        'admin_role',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'rating' => 'decimal:1',
        ];
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteAds()
    {
        return $this->belongsToMany(Ad::class, 'favorites')->withTimestamps();
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversationsAsBuyer(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function conversationsAsSeller(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'admin_id');
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_admin && $this->admin_role === 'super_admin';
    }

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

    public function incrementStat(string $field, int $amount = 1): bool
    {
        $allowedFields = ['active_listings', 'sold_items', 'followers'];

        if (!in_array($field, $allowedFields)) {
            return false;
        }

        $this->increment($field, $amount);
        return true;
    }

    public function updateRating(): void
    {
        $this->rating = $this->reviewsReceived()->avg('rating') ?? 0;
        $this->review_count = $this->reviewsReceived()->count();
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
