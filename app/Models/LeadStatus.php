<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LeadStatus extends Model
{
    protected $fillable = [
        'status_key',
        'display_name',
        'color_class',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // Relationships
    public function leads()
    {
        return $this->hasMany(ServiceLead::class, 'status', 'status_key');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority');
    }

    // Static methods for caching
    public static function getAllActive()
    {
        return Cache::remember('lead_statuses_active', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    public static function getStatusColor($statusKey)
    {
        $status = static::getAllActive()->firstWhere('status_key', $statusKey);
        return $status ? $status->color_class : 'bg-gray-50 text-gray-700 border-gray-200';
    }

    public static function getDisplayName($statusKey)
    {
        $status = static::getAllActive()->firstWhere('status_key', $statusKey);
        return $status ? $status->display_name : $statusKey;
    }

    public static function clearCache()
    {
        Cache::forget('lead_statuses_active');
    }

    // Events
    protected static function booted()
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
