<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceJournalEntryLine extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'line_number',
        'description',
        'debit_amount',
        'credit_amount',
        'category_id',
        'department',
        'project',
        'cost_center',
        'reference',
        'metadata',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'metadata' => 'array',
        'line_number' => 'integer',
    ];

    // Relationships
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceJournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceChartOfAccount::class, 'account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    // Helper Methods
    public function getNetAmount()
    {
        return $this->debit_amount - $this->credit_amount;
    }

    public function isDebit()
    {
        return $this->debit_amount > 0;
    }

    public function isCredit()
    {
        return $this->credit_amount > 0;
    }
}
