<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPerformanceInsight extends Model
{
    protected $table = 'ai_performance_insights';

    protected $fillable = [
        'employee_id',
        'period_type',
        'period_start',
        'period_end',
        'overall_sentiment_score',
        'engagement_score',
        'productivity_score',
        'culture_fit_score',
        'development_areas',
        'strengths',
        'recommendations',
        'ai_provider',
        'ai_model',
    ];

    protected $casts = [
        'development_areas' => 'array',
        'strengths' => 'array',
        'recommendations' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }
}
