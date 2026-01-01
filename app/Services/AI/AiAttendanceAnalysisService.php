<?php

namespace App\Services\AI;

use App\Models\HrmAttendanceDay;
use App\Models\HrmTimeEntry;
use App\Models\AiAttendanceInsight;
use App\Models\HrmEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAttendanceAnalysisService
{
    protected $openaiApiKey;
    protected $openaiModel;

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
        $this->openaiModel = config('services.openai.model', 'gpt-4');
    }

    /**
     * Analyze attendance behavior for an employee
     */
    public function analyzeAttendanceBehavior(HrmEmployee $employee, int $daysToAnalyze = 30)
    {
        // Get attendance data for the period
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($daysToAnalyze);

        $attendanceRecords = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('timeEntries')
            ->orderBy('date', 'asc')
            ->get();

        if ($attendanceRecords->isEmpty()) {
            return null;
        }

        // Calculate basic metrics
        $metrics = $this->calculateAttendanceMetrics($attendanceRecords);

        // Get AI insights
        $aiInsights = $this->generateAiInsights($employee, $attendanceRecords, $metrics);

        // Calculate scores
        $scores = $this->calculateScores($metrics);

        // Determine trend
        $trend = $this->determineTrend($employee, $metrics);

        // Save the insight
        $insight = AiAttendanceInsight::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'analysis_date' => Carbon::now()->toDateString(),
            ],
            array_merge($metrics, $scores, $trend, $aiInsights)
        );

        return $insight;
    }

    /**
     * Calculate attendance metrics
     */
    protected function calculateAttendanceMetrics($attendanceRecords)
    {
        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('payroll_hours', '>', 0)->count();
        $absentDays = $totalDays - $presentDays;

        // Calculate average clock-in time
        $clockInTimes = [];
        $lateArrivals = 0;
        $earlyDepartures = 0;
        $weeklyPattern = [];

        foreach ($attendanceRecords as $record) {
            $dayOfWeek = Carbon::parse($record->date)->format('l');

            if (!isset($weeklyPattern[$dayOfWeek])) {
                $weeklyPattern[$dayOfWeek] = [
                    'count' => 0,
                    'total_hours' => 0,
                    'avg_clock_in' => null,
                ];
            }

            $weeklyPattern[$dayOfWeek]['count']++;
            $weeklyPattern[$dayOfWeek]['total_hours'] += $record->payroll_hours;

            // Get first time entry (clock-in)
            $firstEntry = $record->timeEntries->sortBy('clock_in')->first();
            if ($firstEntry && $firstEntry->clock_in) {
                $clockInTime = Carbon::parse($firstEntry->clock_in);
                $clockInHour = $clockInTime->hour + ($clockInTime->minute / 60);
                $clockInTimes[] = $clockInHour;

                // Add to weekly pattern
                if (!$weeklyPattern[$dayOfWeek]['avg_clock_in']) {
                    $weeklyPattern[$dayOfWeek]['avg_clock_in'] = [];
                }
                $weeklyPattern[$dayOfWeek]['avg_clock_in'][] = $clockInHour;

                // Check if late (after 9:30 AM)
                if ($clockInHour > 9.5) {
                    $lateArrivals++;
                }
            }

            // Get last time entry (clock-out)
            $lastEntry = $record->timeEntries->sortByDesc('clock_out')->first();
            if ($lastEntry && $lastEntry->clock_out) {
                $clockOutTime = Carbon::parse($lastEntry->clock_out);
                $clockOutHour = $clockOutTime->hour + ($clockOutTime->minute / 60);

                // Check if early departure (before 6:00 PM)
                if ($clockOutHour < 18.0 && $record->payroll_hours < 8) {
                    $earlyDepartures++;
                }
            }
        }

        // Calculate weekly pattern averages
        foreach ($weeklyPattern as $day => &$pattern) {
            if ($pattern['count'] > 0) {
                $pattern['avg_hours'] = round($pattern['total_hours'] / $pattern['count'], 2);
                if (!empty($pattern['avg_clock_in'])) {
                    $pattern['avg_clock_in'] = round(array_sum($pattern['avg_clock_in']) / count($pattern['avg_clock_in']), 2);
                }
            }
        }

        // Find most and least productive days
        $productivityByDay = array_map(fn($p) => $p['avg_hours'] ?? 0, $weeklyPattern);
        arsort($productivityByDay);
        $mostProductiveDay = array_key_first($productivityByDay);
        $leastProductiveDay = array_key_last($productivityByDay);

        return [
            'avg_clock_in_time' => !empty($clockInTimes) ? round(array_sum($clockInTimes) / count($clockInTimes), 2) : null,
            'late_arrivals_count' => $lateArrivals,
            'early_departures_count' => $earlyDepartures,
            'avg_daily_hours' => $presentDays > 0 ? round($attendanceRecords->sum('payroll_hours') / $presentDays, 2) : 0,
            'absent_days_count' => $absentDays,
            'weekly_pattern' => $weeklyPattern,
            'most_productive_day' => $mostProductiveDay,
            'least_productive_day' => $leastProductiveDay,
        ];
    }

    /**
     * Calculate scores
     */
    protected function calculateScores($metrics)
    {
        // Punctuality score (0-100)
        $totalWorkDays = max(1, 30 - $metrics['absent_days_count']);
        $punctualityScore = max(0, 100 - ($metrics['late_arrivals_count'] / $totalWorkDays * 100));

        // Clock-in consistency score
        $consistencyScore = 100;
        if ($metrics['late_arrivals_count'] > 5) {
            $consistencyScore -= 30;
        } elseif ($metrics['late_arrivals_count'] > 2) {
            $consistencyScore -= 15;
        }

        // Regularity score (based on attendance)
        $regularityScore = max(0, 100 - ($metrics['absent_days_count'] / 30 * 100));

        // Overall score (weighted average)
        $overallScore = round(
            ($punctualityScore * 0.4) +
                ($consistencyScore * 0.3) +
                ($regularityScore * 0.3),
            2
        );

        return [
            'punctuality_score' => round($punctualityScore, 2),
            'clock_in_consistency_score' => round($consistencyScore, 2),
            'regularity_score' => round($regularityScore, 2),
            'overall_score' => $overallScore,
        ];
    }

    /**
     * Determine trend
     */
    protected function determineTrend($employee, $currentMetrics)
    {
        $previousInsight = AiAttendanceInsight::where('employee_id', $employee->id)
            ->where('analysis_date', '<', Carbon::now()->toDateString())
            ->orderBy('analysis_date', 'desc')
            ->first();

        if (!$previousInsight) {
            return [
                'trend' => 'stable',
                'trend_change' => 0,
            ];
        }

        $currentScore = $currentMetrics['avg_daily_hours'] ?? 0;
        $previousScore = $previousInsight->avg_daily_hours ?? 0;

        if ($previousScore == 0) {
            return [
                'trend' => 'stable',
                'trend_change' => 0,
            ];
        }

        $change = (($currentScore - $previousScore) / $previousScore) * 100;

        $trend = 'stable';
        if ($change > 5) {
            $trend = 'improving';
        } elseif ($change < -5) {
            $trend = 'declining';
        }

        return [
            'trend' => $trend,
            'trend_change' => round($change, 2),
        ];
    }

    /**
     * Generate AI insights using OpenAI
     */
    protected function generateAiInsights($employee, $attendanceRecords, $metrics)
    {
        if (!$this->openaiApiKey) {
            return $this->generateFallbackInsights($metrics);
        }

        try {
            $prompt = $this->buildAnalysisPrompt($employee, $metrics);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->openaiModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an HR analytics expert analyzing employee attendance patterns. Provide constructive, actionable insights.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');

                return [
                    'ai_summary' => $this->extractSummary($content),
                    'ai_suggestions' => $this->extractSuggestions($content),
                    'ai_metadata' => [
                        'model' => $this->openaiModel,
                        'generated_at' => now()->toIso8601String(),
                    ],
                ];
            }
        } catch (\Exception $e) {
            Log::error('AI Attendance Analysis failed: ' . $e->getMessage());
        }

        return $this->generateFallbackInsights($metrics);
    }

    /**
     * Build analysis prompt
     */
    protected function buildAnalysisPrompt($employee, $metrics)
    {
        return "Analyze this employee's attendance pattern and provide insights:\n\n" .
            "Employee: {$employee->name}\n" .
            "Position: {$employee->position}\n\n" .
            "Metrics (Last 30 days):\n" .
            "- Average Clock-in Time: " . ($metrics['avg_clock_in_time'] ? sprintf('%02d:%02d', floor($metrics['avg_clock_in_time']), round(($metrics['avg_clock_in_time'] - floor($metrics['avg_clock_in_time'])) * 60)) : 'N/A') . "\n" .
            "- Late Arrivals: {$metrics['late_arrivals_count']}\n" .
            "- Early Departures: {$metrics['early_departures_count']}\n" .
            "- Average Daily Hours: {$metrics['avg_daily_hours']}\n" .
            "- Absent Days: {$metrics['absent_days_count']}\n" .
            "- Most Productive Day: {$metrics['most_productive_day']}\n" .
            "- Least Productive Day: {$metrics['least_productive_day']}\n\n" .
            "Provide:\n" .
            "1. SUMMARY: A brief 2-3 sentence summary of attendance patterns\n" .
            "2. SUGGESTIONS: 3-5 specific, actionable suggestions to improve clock-in behavior and attendance";
    }

    /**
     * Extract summary from AI response
     */
    protected function extractSummary($content)
    {
        if (preg_match('/SUMMARY:?\s*(.+?)(?=SUGGESTIONS:|$)/s', $content, $matches)) {
            return trim($matches[1]);
        }

        // Fallback: take first paragraph
        $paragraphs = explode("\n\n", $content);
        return trim($paragraphs[0]);
    }

    /**
     * Extract suggestions from AI response
     */
    protected function extractSuggestions($content)
    {
        if (preg_match('/SUGGESTIONS:?\s*(.+)$/s', $content, $matches)) {
            return trim($matches[1]);
        }

        return $content;
    }

    /**
     * Generate fallback insights when AI is unavailable
     */
    protected function generateFallbackInsights($metrics)
    {
        $summary = "Based on the last 30 days, ";

        if ($metrics['late_arrivals_count'] > 10) {
            $summary .= "there's a significant pattern of late arrivals ({$metrics['late_arrivals_count']} times). ";
        } elseif ($metrics['late_arrivals_count'] > 5) {
            $summary .= "there are some instances of late arrivals ({$metrics['late_arrivals_count']} times). ";
        } else {
            $summary .= "attendance punctuality has been good with only {$metrics['late_arrivals_count']} late arrivals. ";
        }

        $summary .= "Average daily working hours: {$metrics['avg_daily_hours']} hours.";

        $suggestions = "To improve your attendance and clock-in behavior:\n\n";

        if ($metrics['late_arrivals_count'] > 5) {
            $suggestions .= "1. Set multiple alarms 30 minutes before your start time\n";
            $suggestions .= "2. Prepare your work materials the night before\n";
            $suggestions .= "3. Plan your commute with 15-minute buffer time\n";
        } else {
            $suggestions .= "1. Maintain your consistent morning routine\n";
            $suggestions .= "2. Continue arriving before your scheduled start time\n";
            $suggestions .= "3. Use calendar reminders for important meetings\n";
        }

        if ($metrics['most_productive_day'] && $metrics['most_productive_day'] != $metrics['least_productive_day']) {
            $suggestions .= "4. Apply your {$metrics['most_productive_day']} work patterns to other days\n";
        }

        if ($metrics['absent_days_count'] > 3) {
            $suggestions .= "5. Consider reviewing your work-life balance and wellness\n";
        } else {
            $suggestions .= "5. Keep up the excellent attendance record\n";
        }

        return [
            'ai_summary' => $summary,
            'ai_suggestions' => $suggestions,
            'ai_metadata' => [
                'fallback' => true,
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
