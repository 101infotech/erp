<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'parent_account_id',
        'account_code',
        'account_name',
        'description',
        'account_type',
        'account_subtype',
        'normal_balance',
        'is_control_account',
        'is_contra_account',
        'allow_manual_entry',
        'is_active',
        'is_system_account',
        'opening_balance',
        'current_balance',
        'fiscal_year_bs',
        'level',
        'display_order',
        'is_taxable',
        'tax_category',
        'show_in_bs',
        'show_in_pl',
        'external_code',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_control_account' => 'boolean',
        'is_contra_account' => 'boolean',
        'allow_manual_entry' => 'boolean',
        'is_active' => 'boolean',
        'is_system_account' => 'boolean',
        'is_taxable' => 'boolean',
        'show_in_bs' => 'boolean',
        'show_in_pl' => 'boolean',
        'metadata' => 'array',
        'level' => 'integer',
        'display_order' => 'integer',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceChartOfAccount::class, 'parent_account_id');
    }

    public function childAccounts(): HasMany
    {
        return $this->hasMany(FinanceChartOfAccount::class, 'parent_account_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(FinanceJournalEntryLine::class, 'account_id');
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeManualEntry($query)
    {
        return $query->where('allow_manual_entry', true);
    }

    public function scopeParentAccounts($query)
    {
        return $query->whereNull('parent_account_id');
    }

    // Helper Methods
    public function updateBalance($amount, $type = 'debit')
    {
        if ($type === $this->normal_balance) {
            $this->current_balance += $amount;
        } else {
            $this->current_balance -= $amount;
        }
        $this->save();
    }

    public function getFullAccountPath()
    {
        $path = [$this->account_name];
        $parent = $this->parentAccount;

        while ($parent) {
            array_unshift($path, $parent->account_name);
            $parent = $parent->parentAccount;
        }

        return implode(' > ', $path);
    }
}
