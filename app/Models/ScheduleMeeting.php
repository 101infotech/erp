<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'preferred_date',
        'preferred_time',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime:H:i',
    ];

    /**
     * Get the site that owns the meeting request
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Scope to filter by site
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed requests
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
