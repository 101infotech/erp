<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hiring extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'position',
        'department',
        'type',
        'location',
        'description',
        'requirements',
        'responsibilities',
        'salary_range',
        'deadline',
        'vacancies',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'requirements' => 'array',
        'responsibilities' => 'array',
        'deadline' => 'date',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the site that owns the hiring
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
     * Scope for open positions
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for featured positions
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
