<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrmLeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'string',
        'end_date' => 'string',
        'total_days' => 'decimal:1',
        'approved_at' => 'datetime',
    ];

    /**
     * Get formatted start date in Nepali
     */
    public function getStartDateFormattedAttribute(): ?string
    {
        return $this->start_date ? format_nepali_date($this->start_date, 'j F Y') : null;
    }

    /**
     * Get formatted end date in Nepali
     */
    public function getEndDateFormattedAttribute(): ?string
    {
        return $this->end_date ? format_nepali_date($this->end_date, 'j F Y') : null;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
