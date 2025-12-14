<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrmAttendanceDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'tracked_hours',
        'payroll_hours',
        'overtime_hours',
        'source',
        'jibble_data',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'tracked_hours' => 'decimal:2',
        'payroll_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'jibble_data' => 'array',
    ];

    /**
     * Get formatted date in Nepali
     */
    public function getDateFormattedAttribute(): ?string
    {
        return $this->date ? format_nepali_date($this->date, 'j F Y') : null;
    }

    /**
     * Get the employee that owns the attendance record
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }

    /**
     * Get the time entries for this attendance day
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(HrmTimeEntry::class, 'attendance_day_id');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to filter by source
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Check if attendance has overtime
     */
    public function hasOvertime(): bool
    {
        return $this->overtime_hours > 0;
    }

    /**
     * Get total hours worked (tracked + overtime)
     */
    public function getTotalHoursAttribute(): float
    {
        return $this->tracked_hours + $this->overtime_hours;
    }
}
