<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceAccount extends Model
{
    protected $fillable = [
        'company_id',
        'account_code',
        'account_name',
        'account_type',
        'parent_account_id',
        'description',
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

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'parent_account_id');
    }

    public function childAccounts(): HasMany
    {
        return $this->hasMany(FinanceAccount::class, 'parent_account_id');
    }

    public function debitTransactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class, 'debit_account_id');
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class, 'credit_account_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper Methods
    public function getBalance(): float
    {
        $debits = $this->debitTransactions()->where('status', 'completed')->sum('amount');
        $credits = $this->creditTransactions()->where('status', 'completed')->sum('amount');

        // Asset & Expense: Debit increases balance
        // Liability & Revenue & Equity: Credit increases balance
        if (in_array($this->account_type, ['asset', 'expense'])) {
            return $debits - $credits;
        }

        return $credits - $debits;
    }
}
