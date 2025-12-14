<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceRecurringExpense extends Model
{
    protected $fillable = [
        'company_id',
        'expense_name',
        'category_id',
        'amount',
        'frequency',
        'start_date_bs',
        'end_date_bs',
        'payment_method',
        'account_id',
        'auto_create_transaction',
        'last_generated_date_bs',
        'next_due_date_bs',
        'is_active',
        'created_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'auto_create_transaction' => 'boolean',
        'is_active' => 'boolean',
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
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

    public function scopeAutoCreate($query)
    {
        return $query->where('auto_create_transaction', true);
    }

    public function scopeDueForGeneration($query)
    {
        $calendar = app(\App\Services\NepalCalendarService::class);
        $today = $calendar->getCurrentBsDate();

        return $query->active()
            ->autoCreate()
            ->where(function ($q) use ($today) {
                $q->whereNull('next_due_date_bs')
                    ->orWhere('next_due_date_bs', '<=', $today);
            });
    }

    // Helper Methods
    public function calculateNextDueDate(): string
    {
        $calendar = app(\App\Services\NepalCalendarService::class);
        $lastDate = $this->last_generated_date_bs ?? $this->start_date_bs;

        [$year, $month, $day] = explode('-', $lastDate);

        switch ($this->frequency) {
            case 'monthly':
                $month = (int)$month + 1;
                if ($month > 12) {
                    $month = 1;
                    $year = (int)$year + 1;
                }
                break;
            case 'quarterly':
                $month = (int)$month + 3;
                if ($month > 12) {
                    $month = $month - 12;
                    $year = (int)$year + 1;
                }
                break;
            case 'annually':
                $year = (int)$year + 1;
                break;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
