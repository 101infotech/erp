<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'title',
        'slug',
        'department',
        'location',
        'job_type',
        'description',
        'responsibilities',
        'requirements',
        'salary_range',
        'posted_at',
        'deadline',
        'is_active',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
