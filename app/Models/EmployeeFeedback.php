<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmployeeFeedback extends Model
{
    use HasFactory;

    protected $table = 'employee_feedback';

    protected $fillable = [
        'user_id',
        'feelings',
        'work_progress',
        'self_improvements',
        'admin_notes',
        'is_submitted',
        'submitted_at',
        // Questionnaire fields
        'stress_level',
        'workload_level',
        'work_satisfaction',
        'team_collaboration',
        'mental_wellbeing',
        'challenges_faced',
        'achievements',
        'support_needed',
        'complaints',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is the current week's feedback
     */
    public function isCurrentWeek(): bool
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        return $this->created_at->between($weekStart, $weekEnd);
    }

    /**
     * Scope for submitted feedback
     */
    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    /**
     * Scope for pending feedback
     */
    public function scopePending($query)
    {
        return $query->where('is_submitted', false);
    }

    /**
     * Scope for this week
     */
    public function scopeThisWeek($query)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        return $query->whereBetween('created_at', [$weekStart, $weekEnd]);
    }

    /**
     * Get feedback from a specific week
     */
    public static function getWeeklyFeedback($user_id, $date = null)
    {
        $date = $date ?? now();
        $weekStart = $date->copy()->startOfWeek();
        $weekEnd = $date->copy()->endOfWeek();

        return self::where('user_id', $user_id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->first();
    }
}
