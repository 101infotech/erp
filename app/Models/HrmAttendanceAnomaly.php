<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrmAttendanceAnomaly extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_day_id',
        'date',
        'anomaly_type',
        'description',
        'metadata',
        'severity',
        'reviewed',
        'reviewed_by',
        'reviewed_at',
        'resolution_notes',
    ];

    protected $casts = [
        // Removed date cast - date now stored as BS string
        'metadata' => 'array',
        'reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get formatted date in Nepali
     */
    public function getDateFormattedAttribute(): ?string
    {
        return $this->date ? format_nepali_date($this->date, 'j F Y') : null;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }

    public function attendanceDay(): BelongsTo
    {
        return $this->belongsTo(HrmAttendanceDay::class, 'attendance_day_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeUnreviewed($query)
    {
        return $query->where('reviewed', false);
    }
}
