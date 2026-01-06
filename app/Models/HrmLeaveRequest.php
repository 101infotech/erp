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
        'rejected_by',
        'rejected_at',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'start_date' => 'string',
        'end_date' => 'string',
        'total_days' => 'decimal:1',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
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
