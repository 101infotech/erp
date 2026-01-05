<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFinanceCategoryPattern extends Model
{
    protected $fillable = [
        'company_id',
        'pattern_type',
        'pattern_value',
        'category_id',
        'confidence_score',
        'usage_count',
        'success_rate',
        'last_used_at',
        'metadata',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:4',
        'success_rate' => 'decimal:4',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    /**
     * Increment usage count and update success rate
     */
    public function recordUsage(bool $wasSuccessful): void
    {
        $this->increment('usage_count');

        $newSuccessRate = ($this->success_rate * ($this->usage_count - 1) + ($wasSuccessful ? 1 : 0)) / $this->usage_count;

        $this->update([
            'success_rate' => $newSuccessRate,
            'last_used_at' => now(),
        ]);
    }
}
