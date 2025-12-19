<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFeedbackAnalysis extends Model
{
    protected $table = 'ai_feedback_analysis';

    protected $fillable = [
        'feedback_id',
        'sentiment_score',
        'sentiment_classification',
        'ai_provider',
        'ai_model',
        'recommendations',
        'key_insights',
        'metadata',
    ];

    protected $casts = [
        'recommendations' => 'array',
        'key_insights' => 'array',
        'metadata' => 'array',
    ];

    public function feedback()
    {
        return $this->belongsTo(EmployeeFeedback::class, 'feedback_id');
    }
}
