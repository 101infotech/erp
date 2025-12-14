<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrmTimeEntry extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_day_id',
        'jibble_entry_id',
        'type',
        'time',
        'local_time',
        'belongs_to_date',
        'project_id',
        'project_name',
        'activity_id',
        'activity_name',
        'location_id',
        'note',
        'address',
        'latitude',
        'longitude',
        'raw_data',
    ];

    protected $casts = [
        'time' => 'datetime',
        'local_time' => 'datetime',
        // Removed date cast - belongs_to_date now stored as BS string
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'raw_data' => 'array',
    ];

    /**
     * Get formatted belongs_to_date in Nepali
     */
    public function getBelongsToDateFormattedAttribute(): ?string
    {
        return $this->belongs_to_date ? format_nepali_date($this->belongs_to_date, 'j F Y') : null;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class);
    }

    public function attendanceDay(): BelongsTo
    {
        return $this->belongsTo(HrmAttendanceDay::class);
    }

    /**
     * Scope for clock in entries
     */
    public function scopeClockIn($query)
    {
        return $query->where('type', 'In');
    }

    /**
     * Scope for clock out entries
     */
    public function scopeClockOut($query)
    {
        return $query->where('type', 'Out');
    }

    /**
     * Scope for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('belongs_to_date', $date);
    }
}
