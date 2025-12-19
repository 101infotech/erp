<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiWeeklyPrompt extends Model
{
    protected $table = 'ai_weekly_prompts';

    protected $fillable = [
        'employee_id',
        'week_number',
        'year',
        'prompt_text',
        'prompt_category',
        'ai_provider',
        'response_text',
        'response_sentiment',
        'response_submitted_at',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'response_submitted_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }
}
