<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'condition',
        'location',
        'latitude',
        'longitude',
        'status',
        'rejection_reason',
        'views',
        'favorites',
        'is_promoted',
        'promoted_until',
        'is_featured',
        'approved_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_promoted' => 'boolean',
            'is_featured' => 'boolean',
            'promoted_until' => 'datetime',
            'approved_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(AdImage::class)->orderBy('display_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(AdImage::class)->where('is_primary', true);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function markAsSold(): bool
    {
        $this->status = 'sold';
        return $this->save();
    }

    public function approve(): bool
    {
        $this->status = 'active';
        $this->approved_at = now();
        return $this->save();
    }

    public function reject(string $reason = null): bool
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        return $this->save();
    }

    public function feature(bool $featured = true): bool
    {
        $this->is_featured = $featured;
        return $this->save();
    }

    public function promote(int $days = 7): bool
    {
        $this->is_promoted = true;
        $this->promoted_until = now()->addDays($days);
        return $this->save();
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePromoted($query)
    {
        return $query->where('is_promoted', true)
                     ->where('promoted_until', '>', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
