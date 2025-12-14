<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceFounder extends Model
{
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'citizenship_number',
        'pan_number',
        'ownership_percentage',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ownership_percentage' => 'decimal:2',
    ];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceFounderTransaction::class, 'founder_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getTotalInvestment(): float
    {
        return $this->transactions()
            ->where('transaction_type', 'investment')
            ->sum('amount');
    }

    public function getTotalWithdrawal(): float
    {
        return $this->transactions()
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');
    }

    public function getCurrentBalance(): float
    {
        return $this->getTotalInvestment() - $this->getTotalWithdrawal();
    }
}
