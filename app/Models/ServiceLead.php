<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_leads';

    protected $fillable = [
        'service_requested',
        'location',
        'client_name',
        'phone_number',
        'email',
        'inspection_date',
        'inspection_time',
        'inspection_charge',
        'inspection_report_date',
        'inspection_assigned_to',
        'status',
        'remarks',
        'created_by',
        'lead_source',
        'lead_owner_id',
        'lead_stage_id',
        'priority',
        'last_activity_at',
        'site_visit_scheduled_at',
        'site_visit_completed_at',
        'site_visit_assigned_to_id',
        'site_visit_observations',
        'design_proposed_at',
        'design_version',
        'design_approved_at',
        'design_notes',
        'booking_confirmed_at',
        'project_code',
        'quoted_amount',
        'advance_amount',
        'paid_amount',
        'payment_status',
        'payment_received_at',
        'next_follow_up_date',
        'follow_up_count',
        'closure_reason',
        'closed_at',
        'closure_notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'inspection_report_date' => 'date',
        'inspection_time' => 'datetime:H:i',
        'inspection_charge' => 'decimal:2',
        'site_visit_scheduled_at' => 'datetime',
        'site_visit_completed_at' => 'datetime',
        'design_proposed_at' => 'datetime',
        'design_approved_at' => 'datetime',
        'booking_confirmed_at' => 'datetime',
        'quoted_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_received_at' => 'datetime',
        'next_follow_up_date' => 'date',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'design_version' => 'integer',
        'follow_up_count' => 'integer',
    ];

    protected $appends = [
        'assigned_to_name',
        'created_by_name',
        'status_badge',
    ];

    // Relationships
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'inspection_assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusInfo()
    {
        return $this->hasOne(LeadStatus::class, 'status_key', 'status');
    }

    /**
     * Get the lead stage
     */
    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    /**
     * Get the lead owner (sales executive)
     */
    public function leadOwner()
    {
        return $this->belongsTo(User::class, 'lead_owner_id');
    }

    /**
     * Get the site visit coordinator
     */
    public function siteVisitAssignedTo()
    {
        return $this->belongsTo(User::class, 'site_visit_assigned_to_id');
    }

    /**
     * Get all follow-ups for this lead
     */
    public function followUps()
    {
        return $this->hasMany(LeadFollowUp::class, 'lead_id');
    }

    /**
     * Get all payments for this lead
     */
    public function payments()
    {
        return $this->hasMany(LeadPayment::class, 'lead_id');
    }

    /**
     * Get all documents for this lead
     */
    public function documents()
    {
        return $this->hasMany(LeadDocument::class, 'lead_id');
    }

    // Accessors
    public function getAssignedToNameAttribute()
    {
        return $this->assignedTo ? $this->assignedTo->name : 'Unassigned';
    }

    public function getCreatedByNameAttribute()
    {
        return $this->createdBy ? $this->createdBy->name : 'System';
    }

    public function getStatusBadgeAttribute()
    {
        $status = LeadStatus::where('status_key', $this->status)->first();
        return $status ? [
            'key' => $status->status_key,
            'display' => $status->display_name,
            'color' => $status->color_class,
        ] : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Cancelled', 'Bad Lead']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['Intake', 'Contacted', 'Inspection Booked']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Positive', 'Reports Sent']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('inspection_assigned_to', $userId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('client_name', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('service_requested', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone_number', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to get leads by stage
     */
    public function scopeByStage($query, $stageId)
    {
        if (is_numeric($stageId)) {
            return $query->where('lead_stage_id', $stageId);
        }

        return $query->whereHas('leadStage', function ($q) use ($stageId) {
            $q->where('stage_name', $stageId);
        });
    }

    /**
     * Scope to get leads by owner
     */
    public function scopeByOwner($query, $userId)
    {
        return $query->where('lead_owner_id', $userId);
    }

    /**
     * Scope to get leads by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get leads needing follow-up
     */
    public function scopeNeedingFollowUp($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('next_follow_up_date')
                ->orWhere('next_follow_up_date', '<=', now()->format('Y-m-d'));
        })->where('closed_at', null);
    }

    /**
     * Scope to get leads with pending payments
     */
    public function scopePendingPayment($query)
    {
        return $query->whereIn('payment_status', ['pending', 'partial'])->where('closed_at', null);
    }

    /**
     * Scope to get leads that are open (not closed)
     */
    public function scopeOpen($query)
    {
        return $query->whereNull('closed_at');
    }

    /**
     * Scope to get leads that are closed
     */
    public function scopeClosed($query)
    {
        return $query->whereNotNull('closed_at');
    }

    /**
     * Scope to get leads by date range
     */
    public function scopeCreatedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($lead) {
            if (auth()->check() && !$lead->created_by) {
                $lead->created_by = auth()->id();
            }
        });

        static::updated(function ($lead) {
            // Log status changes
            if ($lead->isDirty('status')) {
                // You can add activity log here
            }
        });
    }
}
