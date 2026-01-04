<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_type',
        'reported_id',
        'reason',
        'description',
        'status',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function getReportedContent()
    {
        return match ($this->reported_type) {
            'ad' => Ad::find($this->reported_id),
            'user' => User::find($this->reported_id),
            'message' => Message::find($this->reported_id),
            default => null,
        };
    }

    public function resolve(): bool
    {
        $this->status = 'resolved';
        return $this->save();
    }

    public function dismiss(): bool
    {
        $this->status = 'dismissed';
        return $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('reported_type', $type);
    }
}
