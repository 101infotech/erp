<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFinancePrediction extends Model
{
    protected $fillable = [
        'company_id',
        'prediction_type',
        'target_date',
        'predicted_amount',
        'actual_amount',
        'confidence_level',
        'accuracy_score',
        'prediction_metadata',
        'model_version',
        'generated_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'predicted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'confidence_level' => 'decimal:4',
        'accuracy_score' => 'decimal:4',
        'prediction_metadata' => 'array',
        'generated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    /**
     * Update with actual value and calculate accuracy
     */
    public function updateWithActual(float $actualAmount): void
    {
        $predicted = (float) $this->predicted_amount;
        $accuracy = $predicted > 0 ? (1 - abs($predicted - $actualAmount) / $predicted) : 0;

        $this->update([
            'actual_amount' => $actualAmount,
            'accuracy_score' => max(0, min(1, $accuracy)), // Clamp between 0 and 1
        ]);
    }
}
