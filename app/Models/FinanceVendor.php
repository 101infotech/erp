<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FinanceVendor extends Model
{
    protected $fillable = [
        'company_id',
        'vendor_code',
        'vendor_name',
        'vendor_type',
        'contact_person',
        'contact_number',
        'email',
        'pan_number',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(FinancePurchase::class, 'vendor_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(FinanceDocument::class, 'documentable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }

    // Helper Methods
    public function getTotalPurchases(): float
    {
        return $this->purchases()->sum('net_amount');
    }

    public function getOutstandingBalance(): float
    {
        return $this->purchases()
            ->whereIn('payment_status', ['pending', 'partial'])
            ->sum('net_amount');
    }
}
