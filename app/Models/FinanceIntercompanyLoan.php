<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceIntercompanyLoan extends Model
{
    protected $fillable = [
        'loan_number',
        'lender_company_id',
        'borrower_company_id',
        'loan_date_bs',
        'loan_amount',
        'repaid_amount',
        'outstanding_balance',
        'interest_rate',
        'due_date_bs',
        'purpose',
        'status',
        'approved_by_user_id',
        'created_by_user_id',
        'fiscal_year_bs',
        'notes',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'repaid_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    // Relationships
    public function lenderCompany(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'lender_company_id');
    }

    public function borrowerCompany(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'borrower_company_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FinanceIntercompanyLoanPayment::class, 'loan_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByLender($query, $companyId)
    {
        return $query->where('lender_company_id', $companyId);
    }

    public function scopeByBorrower($query, $companyId)
    {
        return $query->where('borrower_company_id', $companyId);
    }

    public function scopeFullyRepaid($query)
    {
        return $query->where('status', 'fully_repaid');
    }

    // Helper Methods
    public function updateOutstandingBalance(): void
    {
        $this->outstanding_balance = $this->loan_amount - $this->repaid_amount;

        if ($this->outstanding_balance <= 0) {
            $this->status = 'fully_repaid';
        } elseif ($this->repaid_amount > 0) {
            $this->status = 'partially_repaid';
        }

        $this->save();
    }
}
