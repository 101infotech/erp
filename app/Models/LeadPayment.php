<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadPayment extends Model
{
    protected $table = 'lead_payments';

    protected $fillable = [
        'lead_id',
        'payment_amount',
        'payment_date',
        'payment_mode',
        'reference_number',
        'received_by_id',
        'payment_type',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Get the lead this payment belongs to
     */
    public function lead()
    {
        return $this->belongsTo(ServiceLead::class, 'lead_id');
    }

    /**
     * Get the user who received this payment
     */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    /**
     * Scope to get payments for a specific lead
     */
    public function scopeForLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    /**
     * Scope to get payments by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Scope to get payments by mode
     */
    public function scopeByMode($query, $mode)
    {
        return $query->where('payment_mode', $mode);
    }

    /**
     * Scope to get payments between dates
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Get payment type label
     */
    public function getTypeLabel()
    {
        return match ($this->payment_type) {
            'advance' => 'Advance Payment',
            'partial' => 'Partial Payment',
            'full' => 'Full Payment',
            default => ucfirst($this->payment_type),
        };
    }

    /**
     * Get payment mode label
     */
    public function getModeLabel()
    {
        return match ($this->payment_mode) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'online' => 'Online',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->payment_mode)),
        };
    }
}
