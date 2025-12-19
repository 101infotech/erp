<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use App\Models\AiFeedbackSentimentAnalysis;
use App\Models\AiWeeklyPrompt;
use App\Models\AiPerformanceInsight;
use App\Services\AI\AiFeedbackService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AiFeedbackController extends Controller
{
    protected $aiFeedbackService;

    public function __construct()
    {
        $this->aiFeedbackService = new AiFeedbackService();
        $this->middleware('auth:sanctum');
    }

    /**
     * Generate AI-powered feedback questions
     * GET /api/v1/ai/feedback/questions
     */
    public function generateQuestions(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'count' => 'integer|min:1|max:10',
                'category' => 'string|in:wellbeing,productivity,culture,engagement,development,general',
                'adaptive' => 'boolean',
            ]);

            $count = $validated['count'] ?? 3;
            $category = $validated['category'] ?? null;
            $adaptive = $validated['adaptive'] ?? true;

            // Generate questions
            $questions = $this->aiFeedbackService->generateFeedbackQuestions(
                auth()->id(),
                $count,
                $category,
                $adaptive
            );

            return response()->json([
                'success' => true,
                'data' => $questions,
                'count' => count($questions),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze sentiment of feedback
     * POST /api/v1/ai/feedback/analyze-sentiment
     */
    public function analyzeSentiment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'feedback_id' => 'required|exists:employee_feedback,id',
            ]);

            $feedback = EmployeeFeedback::findOrFail($validated['feedback_id']);

            // Check authorization
            if ($feedback->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            // Analyze sentiment
            $analysis = $this->aiFeedbackService->analyzeFeedbackSentiment($feedback);

            if (!$analysis) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sentiment analysis feature not available',
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'sentiment' => $analysis->overall_classification,
                    'score' => $analysis->overall_sentiment,
                    'trends' => [
                        'feelings' => $analysis->feelings_sentiment,
                        'progress' => $analysis->progress_sentiment,
                        'improvement' => $analysis->improvement_sentiment,
                    ],
                    'analysis' => $analysis->ai_response,
                    'manager_attention_required' => $analysis->needs_manager_attention,
                    'alert_reason' => $analysis->alert_reason,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get weekly feedback prompt for employee
     * GET /api/v1/ai/feedback/weekly-prompt
     */
    public function getWeeklyPrompt(Request $request): JsonResponse
    {
        try {
            $weekNumber = $request->get('week_number', now()->weekOfYear);
            $year = $request->get('year', now()->year);

            $prompt = AiWeeklyPrompt::where('employee_id', auth()->user()->employee->id ?? null)
                ->where('week_number', $weekNumber)
                ->where('year', $year)
                ->first();

            if (!$prompt) {
                // Generate new prompt if doesn't exist
                $prompt = $this->aiFeedbackService->generateWeeklyPrompt(
                    auth()->user()->employee,
                    $weekNumber,
                    $year
                );
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $prompt->id,
                    'title' => $prompt->title,
                    'prompt' => $prompt->prompt,
                    'category' => $prompt->category,
                    'follow_up_questions' => $prompt->follow_up_questions,
                    'answered' => $prompt->answered,
                    'answered_at' => $prompt->answered_at,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit answer to weekly prompt
     * POST /api/v1/ai/feedback/submit-answer
     */
    public function submitAnswer(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prompt_id' => 'required|exists:ai_weekly_prompts,id',
                'answer' => 'required|string|min:10',
            ]);

            $prompt = AiWeeklyPrompt::findOrFail($validated['prompt_id']);

            // Check authorization
            if ($prompt->employee_id !== auth()->user()->employee->id ?? null) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            // Update prompt with answer
            $prompt->update([
                'answer' => $validated['answer'],
                'answered' => true,
                'answered_at' => now(),
            ]);

            // Analyze sentiment of the answer
            $sentiment = $this->aiFeedbackService->analyzeSentiment($validated['answer']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $prompt->id,
                    'status' => 'submitted',
                    'sentiment' => $sentiment,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get employee sentiment trends
     * GET /api/v1/ai/feedback/sentiment-trends
     */
    public function getSentimentTrends(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'period' => 'string|in:weekly,monthly,quarterly,yearly',
                'days' => 'integer|min:7|max:365',
            ]);

            $period = $validated['period'] ?? 'monthly';
            $days = $validated['days'] ?? 30;

            // Get sentiment analysis records
            $trends = AiFeedbackSentimentAnalysis::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subDays($days))
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->format($this->getPeriodFormat($period));
                })
                ->map(function ($group) {
                    return [
                        'avg_sentiment' => $group->avg('overall_sentiment'),
                        'count' => $group->count(),
                        'sentiments' => [
                            'positive' => $group->where('overall_classification', 'positive')->count(),
                            'neutral' => $group->where('overall_classification', 'neutral')->count(),
                            'negative' => $group->where('overall_classification', 'negative')->count(),
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $trends,
                'period' => $period,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get performance insights for employee
     * GET /api/v1/ai/feedback/performance-insights
     */
    public function getPerformanceInsights(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'period_type' => 'string|in:weekly,monthly,quarterly',
            ]);

            $periodType = $validated['period_type'] ?? 'weekly';

            $employee = auth()->user()->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'error' => 'Employee record not found',
                ], 404);
            }

            $insights = AiPerformanceInsight::where('employee_id', $employee->id)
                ->where('period_type', $periodType)
                ->latest('analysis_date')
                ->first();

            if (!$insights) {
                return response()->json([
                    'success' => false,
                    'error' => 'No insights available yet',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'employee_mood' => $insights->employee_mood,
                    'engagement_score' => $insights->engagement_score,
                    'sentiment_score' => $insights->avg_sentiment_score,
                    'burnout_risk' => $insights->burnout_risk,
                    'retention_risk' => $insights->retention_risk,
                    'retention_probability' => $insights->retention_probability,
                    'positive_themes' => $insights->positive_themes,
                    'improvement_areas' => $insights->improvement_areas,
                    'hr_recommendations' => $insights->hr_recommendations,
                    'manager_recommendations' => $insights->manager_recommendations,
                    'feedback_count' => $insights->total_feedback_count,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to get period format for grouping
     */
    private function getPeriodFormat(string $period): string
    {
        return match ($period) {
            'weekly' => 'Y-W',
            'monthly' => 'Y-m',
            'quarterly' => 'Y-Q',
            'yearly' => 'Y',
            default => 'Y-m',
        };
    }
}
