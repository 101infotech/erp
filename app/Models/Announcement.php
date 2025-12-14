<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'priority',
        'recipient_type',
        'recipient_ids',
        'created_by',
        'send_email',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'send_email' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user who created the announcement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get announcements for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('recipient_type', 'all')
                ->orWhere(function ($q) use ($userId) {
                    $q->where('recipient_type', 'specific')
                        ->whereJsonContains('recipient_ids', $userId);
                });
        });
    }

    /**
     * Scope to get recent announcements
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'blue',
            'normal' => 'green',
            'high' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get recipients list
     */
    public function getRecipientsList()
    {
        if ($this->recipient_type === 'all') {
            return 'All Staff';
        }

        if (empty($this->recipient_ids)) {
            return 'No recipients';
        }

        return User::whereIn('id', $this->recipient_ids)->pluck('name')->join(', ');
    }
}
