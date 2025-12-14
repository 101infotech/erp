<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceJournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'entry_number',
        'reference_number',
        'entry_date_bs',
        'fiscal_year_bs',
        'fiscal_month_bs',
        'entry_type',
        'source_type',
        'source_id',
        'description',
        'notes',
        'total_debit',
        'total_credit',
        'status',
        'posted_by',
        'posted_at',
        'reversed_by',
        'reversed_at',
        'reversal_reason',
        'reversal_entry_id',
        'approved_by',
        'approved_at',
        'attachments',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
        'approved_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function lines(): HasMany
    {
        return $this->hasMany(FinanceJournalEntryLine::class, 'journal_entry_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reversalEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceJournalEntry::class, 'reversal_entry_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByFiscalYear($query, $fiscalYear)
    {
        return $query->where('fiscal_year_bs', $fiscalYear);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('entry_type', $type);
    }

    // Helper Methods
    public function isBalanced()
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function post($userId)
    {
        if (!$this->isBalanced()) {
            throw new \Exception('Journal entry is not balanced');
        }

        $this->status = 'posted';
        $this->posted_by = $userId;
        $this->posted_at = now();
        $this->save();

        // Update account balances
        foreach ($this->lines as $line) {
            $line->account->updateBalance($line->debit_amount, 'debit');
            $line->account->updateBalance($line->credit_amount, 'credit');
        }
    }

    public function reverse($userId, $reason)
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Only posted entries can be reversed');
        }

        $this->status = 'reversed';
        $this->reversed_by = $userId;
        $this->reversed_at = now();
        $this->reversal_reason = $reason;
        $this->save();

        // Create reversal entry
        $reversalEntry = $this->replicate();
        $reversalEntry->entry_type = 'reversal';
        $reversalEntry->status = 'draft';
        $reversalEntry->description = 'Reversal of ' . $this->entry_number . ': ' . $reason;
        $reversalEntry->reversal_entry_id = $this->id;
        $reversalEntry->save();

        // Create reverse lines (swap debit/credit)
        foreach ($this->lines as $line) {
            $reversalLine = $line->replicate();
            $reversalLine->journal_entry_id = $reversalEntry->id;
            $reversalLine->debit_amount = $line->credit_amount;
            $reversalLine->credit_amount = $line->debit_amount;
            $reversalLine->save();
        }

        return $reversalEntry;
    }
}
