<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceBankAccount extends Model
{
    protected $fillable = [
        'company_id',
        'bank_name',
        'branch_name',
        'account_number',
        'account_holder_name',
        'account_type',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_balance' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
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
}
