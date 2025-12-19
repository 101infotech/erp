<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFeedbackSentimentAnalysis extends Model
{
    use HasFactory;

    protected $table = 'ai_feedback_sentiment_analysis';

    protected $fillable = [
        'feedback_id',
        'user_id',
        'overall_sentiment',
        'feelings_sentiment',
        'progress_sentiment',
        'improvement_sentiment',
        'overall_classification',
        'trend_change',
        'trend_direction',
        'ai_response',
        'ai_model',
        'processing_time_ms',
        'needs_manager_attention',
        'alert_reason',
    ];

    protected $casts = [
        'overall_sentiment' => 'decimal:2',
        'feelings_sentiment' => 'decimal:2',
        'progress_sentiment' => 'decimal:2',
        'improvement_sentiment' => 'decimal:2',
        'trend_change' => 'decimal:2',
        'ai_response' => 'array',
        'needs_manager_attention' => 'boolean',
    ];

    /**
     * Get the feedback record
     */
    public function feedback(): BelongsTo
    {
        return $this->belongsTo(EmployeeFeedback::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if sentiment is positive
     */
    public function isPositive(): bool
    {
        return $this->overall_sentiment >= 0.5;
    }

    /**
     * Check if sentiment is negative
     */
    public function isNegative(): bool
    {
        return $this->overall_sentiment < 0.5;
    }

    /**
     * Get sentiment label
     */
    public function getSentimentLabel(): string
    {
        return match ($this->overall_classification) {
            'very_negative' => '😞 Very Negative',
            'negative' => '😟 Negative',
            'neutral' => '😐 Neutral',
            'positive' => '😊 Positive',
            'very_positive' => '😄 Very Positive',
            default => 'Unknown',
        };
    }

    /**
     * Get trend icon
     */
    public function getTrendIcon(): string
    {
        return match ($this->trend_direction) {
            'declining' => '📉',
            'stable' => '📊',
            'improving' => '📈',
            default => '•',
        };
    }

    /**
     * Get latest analysis for a user
     */
    public static function getLatestForUser($userId)
    {
        return static::where('user_id', $userId)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get analysis requiring attention
     */
    public static function getAlerts($limit = 10)
    {
        return static::where('needs_manager_attention', true)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trend over past N weeks for a user
     */
    public static function getTrendForUser($userId, $weeks = 4)
    {
        $startDate = now()->subWeeks($weeks);

        return static::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
