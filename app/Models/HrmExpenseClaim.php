<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrmExpenseClaim extends Model
{
    protected $fillable = [
        'employee_id',
        'claim_number',
        'expense_type',
        'title',
        'description',
        'amount',
        'currency',
        'expense_date',
        'receipt_path',
        'attachments',
        'status',
        'approved_by',
        'approved_by_name',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'payroll_record_id',
        'included_in_payroll',
        'payroll_period_start',
        'payroll_period_end',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'project_code',
        'cost_center',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'attachments' => 'array',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'included_in_payroll' => 'boolean',
        'payroll_period_start' => 'date',
        'payroll_period_end' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate claim number on creation
        static::creating(function ($claim) {
            if (empty($claim->claim_number)) {
                $claim->claim_number = static::generateClaimNumber();
            }
        });
    }

    /**
     * Generate a unique claim number
     */
    public static function generateClaimNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "EXP-{$year}{$month}-";

        $lastClaim = static::where('claim_number', 'like', $prefix . '%')
            ->orderBy('claim_number', 'desc')
            ->first();

        if ($lastClaim) {
            $lastNumber = intval(substr($lastClaim->claim_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Get the employee who made the claim
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }

    /**
     * Get the user who approved the claim
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the payroll record this claim is included in
     */
    public function payrollRecord(): BelongsTo
    {
        return $this->belongsTo(HrmPayrollRecord::class, 'payroll_record_id');
    }

    /**
     * Scope to get pending claims
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved claims
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get paid claims
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get rejected claims
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get claims not yet included in payroll
     */
    public function scopeNotInPayroll($query)
    {
        return $query->where('included_in_payroll', false);
    }

    /**
     * Scope to get approved claims ready for payroll
     */
    public function scopeReadyForPayroll($query)
    {
        return $query->where('status', 'approved')
            ->where('included_in_payroll', false);
    }

    /**
     * Check if claim is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if claim is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if claim is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if claim is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'paid' => 'info',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
