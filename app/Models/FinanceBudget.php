<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceBudget extends Model
{
    protected $fillable = [
        'company_id',
        'fiscal_year_bs',
        'category_id',
        'budget_type',
        'period',
        'budgeted_amount',
        'actual_amount',
        'variance',
        'variance_percentage',
        'notes',
        'created_by_user_id',
        'approved_by_user_id',
        'status',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
        'period' => 'integer',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_user_id');
    }

    // Scopes
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByFiscalYear($query, $fiscalYear)
    {
        return $query->where('fiscal_year_bs', $fiscalYear);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'active']);
    }

    public function scopeMonthly($query)
    {
        return $query->where('budget_type', 'monthly');
    }

    public function scopeQuarterly($query)
    {
        return $query->where('budget_type', 'quarterly');
    }

    public function scopeAnnual($query)
    {
        return $query->where('budget_type', 'annual');
    }

    // Helper Methods
    public function calculateVariance(): void
    {
        $this->variance = $this->actual_amount - $this->budgeted_amount;

        if ($this->budgeted_amount > 0) {
            $this->variance_percentage = ($this->variance / $this->budgeted_amount) * 100;
        } else {
            $this->variance_percentage = 0;
        }

        $this->save();
    }

    public function updateActualAmount(): void
    {
        // Calculate actual amount from transactions based on budget type and period
        $query = FinanceTransaction::where('company_id', $this->company_id)
            ->where('fiscal_year_bs', $this->fiscal_year_bs)
            ->where('category_id', $this->category_id)
            ->where('transaction_type', 'expense')
            ->where('status', 'completed');

        if ($this->budget_type === 'monthly') {
            $query->where('fiscal_month_bs', $this->period);
        } elseif ($this->budget_type === 'quarterly') {
            $startMonth = ($this->period - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $query->whereBetween('fiscal_month_bs', [$startMonth, $endMonth]);
        }

        $this->actual_amount = $query->sum('amount');
        $this->calculateVariance();
    }

    public function isOverBudget(): bool
    {
        return $this->variance > 0;
    }

    public function isUnderBudget(): bool
    {
        return $this->variance < 0;
    }
}
