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
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'inspection_report_date' => 'date',
        'inspection_time' => 'datetime:H:i',
        'inspection_charge' => 'decimal:2',
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
