<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFinanceCategoryPrediction extends Model
{
    protected $fillable = [
        'transaction_id',
        'suggested_category_id',
        'confidence_score',
        'reasoning',
        'was_accepted',
        'user_corrected_category_id',
        'correction_feedback',
        'pattern_matched',
        'ai_model_version',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:4',
        'was_accepted' => 'boolean',
        'pattern_matched' => 'boolean',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinanceTransaction::class, 'transaction_id');
    }

    public function suggestedCategory(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'suggested_category_id');
    }

    public function correctedCategory(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'user_corrected_category_id');
    }
}
