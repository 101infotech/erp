<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowUp extends Model
{
    protected $table = 'lead_follow_ups';

    protected $fillable = [
        'lead_id',
        'follow_up_date',
        'follow_up_type',
        'follow_up_outcome',
        'follow_up_notes',
        'next_follow_up_date',
        'follow_up_owner_id',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'next_follow_up_date' => 'date',
    ];

    /**
     * Get the lead this follow-up belongs to
     */
    public function lead()
    {
        return $this->belongsTo(ServiceLead::class, 'lead_id');
    }

    /**
     * Get the user who performed this follow-up
     */
    public function followUpOwner()
    {
        return $this->belongsTo(User::class, 'follow_up_owner_id');
    }

    /**
     * Scope to get follow-ups for a specific lead
     */
    public function scopeForLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    /**
     * Scope to get follow-ups by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('follow_up_type', $type);
    }

    /**
     * Scope to get pending follow-ups (next_follow_up_date is in past or today)
     */
    public function scopePending($query)
    {
        return $query->whereNotNull('next_follow_up_date')
            ->where('next_follow_up_date', '<=', now()->format('Y-m-d'));
    }

    /**
     * Scope to get follow-ups for a specific user
     */
    public function scopeByOwner($query, $userId)
    {
        return $query->where('follow_up_owner_id', $userId);
    }

    /**
     * Get follow-up type label
     */
    public function getTypeLabel()
    {
        return match ($this->follow_up_type) {
            'call' => 'Phone Call',
            'visit' => 'Site Visit',
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            'sms' => 'SMS',
            default => ucfirst($this->follow_up_type),
        };
    }
}
