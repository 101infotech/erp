<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFinanceAnomalyDetection extends Model
{
    protected $fillable = [
        'transaction_id',
        'anomaly_type',
        'severity',
        'risk_score',
        'description',
        'suggested_action',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'ai_confidence',
        'detection_details',
    ];

    protected $casts = [
        'risk_score' => 'integer',
        'ai_confidence' => 'decimal:4',
        'reviewed_at' => 'datetime',
        'detection_details' => 'array',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinanceTransaction::class, 'transaction_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Mark anomaly as reviewed
     */
    public function markAsReviewed(int $userId, string $notes, string $newStatus): void
    {
        $this->update([
            'status' => $newStatus,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }
}
