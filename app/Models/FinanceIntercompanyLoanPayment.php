<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceIntercompanyLoanPayment extends Model
{
    protected $fillable = [
        'loan_id',
        'payment_number',
        'payment_date_bs',
        'payment_amount',
        'payment_method',
        'description',
        'fiscal_year_bs',
        'handled_by_user_id',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
    ];

    // Relationships
    public function loan(): BelongsTo
    {
        return $this->belongsTo(FinanceIntercompanyLoan::class, 'loan_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'handled_by_user_id');
    }

    // Scopes
    public function scopeByLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    // Helper Methods
    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            // Update loan repaid amount and outstanding balance
            $loan = $payment->loan;
            $loan->repaid_amount += $payment->payment_amount;
            $loan->updateOutstandingBalance();
        });
    }
}
