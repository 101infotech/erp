<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'transaction_number',
        'transaction_date_bs',
        'transaction_type',
        'category_id',
        'amount',
        'debit_account_id',
        'credit_account_id',
        'payment_method',
        'reference_type',
        'reference_id',
        'description',
        'document_path',
        'is_from_holding_company',
        'status',
        'fiscal_year_bs',
        'fiscal_month_bs',
        'approved_by_user_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_from_holding_company' => 'boolean',
        'amount' => 'decimal:2',
        'fiscal_month_bs' => 'integer',
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

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'credit_account_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    // Polymorphic relationship - can reference Sale, Purchase, FounderTransaction, etc.
    public function reference(): MorphTo
    {
        return $this->morphTo();
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

    public function scopeByMonth($query, $month)
    {
        return $query->where('fiscal_month_bs', $month);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeIncome($query)
    {
        return $query->where('transaction_type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('transaction_type', 'expense');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFromHolding($query)
    {
        return $query->where('is_from_holding_company', true);
    }

    // Helper Methods
    public static function generateTransactionNumber(int $companyId): string
    {
        $prefix = 'TXN';
        $date = now()->format('Ymd');
        $lastTransaction = self::where('company_id', $companyId)
            ->whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransaction
            ? ((int) substr($lastTransaction->transaction_number, -4)) + 1
            : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
