<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FinanceFounderTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'founder_id',
        'transaction_number',
        'transaction_date_bs',
        'transaction_type',
        'amount',
        'running_balance',
        'payment_method',
        'description',
        'document_path',
        'is_settled',
        'fiscal_year_bs',
        'created_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'is_settled' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function founder(): BelongsTo
    {
        return $this->belongsTo(FinanceFounder::class, 'founder_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(FinanceTransaction::class, 'reference');
    }

    // Scopes
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByFounder($query, $founderId)
    {
        return $query->where('founder_id', $founderId);
    }

    public function scopeInvestments($query)
    {
        return $query->where('transaction_type', 'investment');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    public function scopeSettled($query)
    {
        return $query->where('is_settled', true);
    }

    public function scopeUnsettled($query)
    {
        return $query->where('is_settled', false);
    }

    // Helper Methods
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Calculate running balance
            $lastTransaction = self::where('founder_id', $transaction->founder_id)
                ->where('company_id', $transaction->company_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $previousBalance = $lastTransaction ? $lastTransaction->running_balance : 0;

            if ($transaction->transaction_type === 'investment') {
                $transaction->running_balance = $previousBalance + $transaction->amount;
            } else {
                $transaction->running_balance = $previousBalance - $transaction->amount;
            }
        });
    }
}
