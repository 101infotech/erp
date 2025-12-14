<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceCompany extends Model
{
    protected $fillable = [
        'name',
        'type',
        'parent_company_id',
        'fiscal_year_start_month',
        'pan_number',
        'contact_email',
        'contact_phone',
        'address',
        'established_date_bs',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fiscal_year_start_month' => 'integer',
    ];

    // Relationships
    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'parent_company_id');
    }

    public function subsidiaries(): HasMany
    {
        return $this->hasMany(FinanceCompany::class, 'parent_company_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class, 'company_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(FinanceAccount::class, 'company_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(FinanceCategory::class, 'company_id');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(FinanceBankAccount::class, 'company_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(FinanceSale::class, 'company_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(FinancePurchase::class, 'company_id');
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(FinanceBudget::class, 'company_id');
    }

    public function hrmDepartments(): HasMany
    {
        return $this->hasMany(\App\Models\HrmDepartment::class, 'finance_company_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHolding($query)
    {
        return $query->where('type', 'holding');
    }

    public function scopeSister($query)
    {
        return $query->where('type', 'sister');
    }

    // Helper Methods
    public function getCurrentFiscalYear(): string
    {
        $calendar = app(\App\Services\NepalCalendarService::class);
        $currentBs = $calendar->getCurrentBsDate();
        [$year, $month] = explode('-', $currentBs);

        return (int)$month >= $this->fiscal_year_start_month
            ? $year
            : (string)((int)$year - 1);
    }

    public function getFiscalYearRange(string $fiscalYear): array
    {
        $startMonth = str_pad($this->fiscal_year_start_month, 2, '0', STR_PAD_LEFT);
        $endMonth = str_pad(($this->fiscal_year_start_month - 1) ?: 12, 2, '0', STR_PAD_LEFT);
        $endYear = $endMonth < $startMonth ? (string)((int)$fiscalYear + 1) : $fiscalYear;

        return [
            'start' => "{$fiscalYear}-{$startMonth}-01",
            'end' => "{$endYear}-{$endMonth}-32",
        ];
    }

    public function getTotalRevenue(string $fiscalYear = null): float
    {
        $fiscalYear = $fiscalYear ?? $this->getCurrentFiscalYear();

        return $this->transactions()
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('transaction_type', 'income')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTotalExpense(string $fiscalYear = null): float
    {
        $fiscalYear = $fiscalYear ?? $this->getCurrentFiscalYear();

        return $this->transactions()
            ->where('fiscal_year_bs', $fiscalYear)
            ->where('transaction_type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getNetProfit(string $fiscalYear = null): float
    {
        return $this->getTotalRevenue($fiscalYear) - $this->getTotalExpense($fiscalYear);
    }
}
