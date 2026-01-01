<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AiAttendanceInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'analysis_date',
        'avg_clock_in_time',
        'clock_in_consistency_score',
        'late_arrivals_count',
        'early_departures_count',
        'avg_daily_hours',
        'absent_days_count',
        'weekly_pattern',
        'most_productive_day',
        'least_productive_day',
        'ai_summary',
        'ai_suggestions',
        'ai_metadata',
        'punctuality_score',
        'regularity_score',
        'overall_score',
        'trend',
        'trend_change',
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'avg_clock_in_time' => 'decimal:2',
        'clock_in_consistency_score' => 'decimal:2',
        'avg_daily_hours' => 'decimal:2',
        'weekly_pattern' => 'array',
        'ai_metadata' => 'array',
        'punctuality_score' => 'decimal:2',
        'regularity_score' => 'decimal:2',
        'overall_score' => 'decimal:2',
        'trend_change' => 'decimal:2',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class);
    }

    /**
     * Get the latest insight for an employee
     */
    public static function getLatestForEmployee($employeeId)
    {
        return self::where('employee_id', $employeeId)
            ->orderBy('analysis_date', 'desc')
            ->first();
    }

    /**
     * Get trend for the last N periods
     */
    public static function getTrendForEmployee($employeeId, $periods = 4)
    {
        return self::where('employee_id', $employeeId)
            ->orderBy('analysis_date', 'desc')
            ->limit($periods)
            ->get();
    }

    /**
     * Get formatted clock-in time
     */
    public function getFormattedClockInTimeAttribute()
    {
        if (!$this->avg_clock_in_time) {
            return 'N/A';
        }

        $hours = floor($this->avg_clock_in_time);
        $minutes = round(($this->avg_clock_in_time - $hours) * 60);

        return sprintf('%02d:%02d AM', $hours, $minutes);
    }

    /**
     * Get average clock-in time as string
     */
    public function getAvgClockInDisplayAttribute()
    {
        if (!$this->avg_clock_in_time) {
            return 'No data';
        }

        $hours = floor($this->avg_clock_in_time);
        $minutes = round(($this->avg_clock_in_time - $hours) * 60);

        $ampm = $hours >= 12 ? 'PM' : 'AM';
        $displayHour = $hours > 12 ? $hours - 12 : ($hours == 0 ? 12 : $hours);

        return sprintf('%d:%02d %s', $displayHour, $minutes, $ampm);
    }

    /**
     * Get trend icon
     */
    public function getTrendIcon()
    {
        return match ($this->trend) {
            'improving' => '📈',
            'declining' => '📉',
            default => '➡️',
        };
    }

    /**
     * Get performance badge
     */
    public function getPerformanceBadge()
    {
        if ($this->overall_score >= 90) {
            return 'Excellent';
        } elseif ($this->overall_score >= 75) {
            return 'Good';
        } elseif ($this->overall_score >= 60) {
            return 'Average';
        } elseif ($this->overall_score >= 40) {
            return 'Below Average';
        } else {
            return 'Needs Improvement';
        }
    }
}
