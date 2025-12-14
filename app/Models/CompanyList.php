<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyList extends Model
{
    use HasFactory;

    protected $table = 'companies_list';

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'industry',
        'founded_year',
        'address',
        'phone',
        'email',
        'social_links',
        'order',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the site that owns the company
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
     * Scope for active companies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
