<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceCategory extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'parent_category_id',
        'description',
        'color_code',
        'icon',
        'is_system',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'parent_category_id');
    }

    public function childCategories(): HasMany
    {
        return $this->hasMany(FinanceCategory::class, 'parent_category_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIncome($query)
    {
        return $query->whereIn('category_type', ['income', 'both']);
    }

    public function scopeExpense($query)
    {
        return $query->whereIn('category_type', ['expense', 'both']);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
