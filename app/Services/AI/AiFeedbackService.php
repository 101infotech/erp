<?php

namespace App\Services\AI;

use App\Models\AiFeedbackPrompt;
use App\Models\AiFeedbackSentimentAnalysis;
use App\Models\EmployeeFeedback;
use App\Models\User;
use App\Models\HrmEmployee;
use Illuminate\Support\Facades\Log;
use Exception;

class AiFeedbackService
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = AiServiceFactory::make();
    }

    /**
     * Generate AI-powered feedback questions for a user
     */
    public function generateFeedbackQuestions(int $userId, int $count = 3): array
    {
        if (!$this->aiService) {
            Log::warning('AI service not available for feedback question generation');
            return $this->getDefaultQuestions();
        }

        try {
            $user = User::findOrFail($userId);
            $employee = HrmEmployee::where('user_id', $userId)->first();

            // Build context for question generation
            $context = $this->buildFeedbackContext($user, $employee);

            $questions = [];

            // Generate questions for different categories
            $categories = ['feelings', 'work_progress', 'improvements'];

            foreach ($categories as $index => $category) {
                $prompt = $this->buildQuestionPrompt($category, $context, $index + 1);

                try {
                    $question = $this->aiService->generateText($prompt);

                    // Store the generated prompt
                    $aiPrompt = AiFeedbackPrompt::create([
                        'user_id' => $userId,
                        'prompt' => $question,
                        'context' => $context,
                        'category' => $category,
                        'sequence_number' => $index + 1,
                        'ai_metadata' => [
                            'model' => $this->aiService->getModel(),
                            'provider' => $this->aiService->getProvider(),
                            'generated_at' => now()->toIso8601String(),
                        ],
                        'is_used' => false,
                    ]);

                    $questions[] = [
                        'id' => $aiPrompt->id,
                        'category' => $category,
                        'question' => $question,
                        'sequence' => $index + 1,
                    ];
                } catch (Exception $e) {
                    Log::error("Failed to generate {$category} question: " . $e->getMessage());
                    // Add fallback question
                    $questions[] = $this->getDefaultQuestion($category, $index + 1);
                }
            }

            return $questions;
        } catch (Exception $e) {
            Log::error('Error generating AI feedback questions: ' . $e->getMessage());
            return $this->getDefaultQuestions();
        }
    }

    /**
     * Analyze sentiment of submitted feedback
     */
    public function analyzeFeedbackSentiment(EmployeeFeedback $feedback): ?AiFeedbackSentimentAnalysis
    {
        if (!$this->aiService || !config('services.ai.features.sentiment_analysis')) {
            Log::warning('Sentiment analysis not available');
            return null;
        }

        try {
            $startTime = microtime(true);

            // Analyze each field
            $feelingsSentiment = $this->aiService->analyzeSentiment($feedback->feelings);
            $progressSentiment = $this->aiService->analyzeSentiment($feedback->work_progress);
            $improvementSentiment = $this->aiService->analyzeSentiment($feedback->self_improvements);

            // Calculate overall sentiment
            $overallScore = (
                $feelingsSentiment['score'] +
                $progressSentiment['score'] +
                $improvementSentiment['score']
            ) / 3;

            // Determine classification
            if ($overallScore >= 0.7) {
                $classification = 'very_positive';
            } elseif ($overallScore >= 0.55) {
                $classification = 'positive';
            } elseif ($overallScore >= 0.45) {
                $classification = 'neutral';
            } elseif ($overallScore >= 0.3) {
                $classification = 'negative';
            } else {
                $classification = 'very_negative';
            }

            // Check for trend
            $previousAnalysis = AiFeedbackSentimentAnalysis::where('user_id', $feedback->user_id)
                ->latest('created_at')
                ->first();

            $trendChange = null;
            $trendDirection = 'stable';

            if ($previousAnalysis) {
                $trendChange = $overallScore - $previousAnalysis->overall_sentiment;

                if ($trendChange > 0.1) {
                    $trendDirection = 'improving';
                } elseif ($trendChange < -0.1) {
                    $trendDirection = 'declining';
                }
            }

            // Determine if needs attention
            $needsAttention = $classification === 'very_negative' || $classification === 'negative';
            $alertReason = null;

            if ($needsAttention) {
                if ($trendDirection === 'declining') {
                    $alertReason = 'Employee sentiment is declining. Immediate follow-up recommended.';
                } elseif ($trendDirection === 'improving' && $classification === 'negative') {
                    $alertReason = 'Employee sentiment shows improvement but remains negative.';
                } else {
                    $alertReason = 'Negative sentiment detected. Manager review recommended.';
                }
            }

            $processingTime = round((microtime(true) - $startTime) * 1000);

            // Store analysis
            $analysis = AiFeedbackSentimentAnalysis::create([
                'feedback_id' => $feedback->id,
                'user_id' => $feedback->user_id,
                'overall_sentiment' => $overallScore,
                'feelings_sentiment' => $feelingsSentiment['score'],
                'progress_sentiment' => $progressSentiment['score'],
                'improvement_sentiment' => $improvementSentiment['score'],
                'overall_classification' => $classification,
                'trend_change' => $trendChange,
                'trend_direction' => $trendDirection,
                'ai_response' => [
                    'feelings' => $feelingsSentiment,
                    'progress' => $progressSentiment,
                    'improvement' => $improvementSentiment,
                ],
                'ai_model' => $this->aiService->getModel(),
                'processing_time_ms' => $processingTime,
                'needs_manager_attention' => $needsAttention,
                'alert_reason' => $alertReason,
            ]);

            // Mark prompts as used
            AiFeedbackPrompt::where('user_id', $feedback->user_id)
                ->where('is_used', false)
                ->update(['is_used' => true, 'feedback_id' => $feedback->id]);

            Log::info("Sentiment analysis completed for feedback {$feedback->id}");

            return $analysis;
        } catch (Exception $e) {
            Log::error('Error analyzing feedback sentiment: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get alerts for managers (negative sentiment feedback)
     */
    public function getManagerAlerts(int $limit = 10): array
    {
        return AiFeedbackSentimentAnalysis::getAlerts($limit)
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'employee_name' => $alert->user->name,
                    'employee_id' => $alert->user_id,
                    'sentiment' => $alert->getSentimentLabel(),
                    'trend' => $alert->getTrendIcon() . ' ' . ucfirst($alert->trend_direction),
                    'alert_reason' => $alert->alert_reason,
                    'feedback_date' => $alert->feedback->submitted_at->format('M d, Y'),
                    'action_url' => route('admin.employees.show', $alert->user_id),
                ];
            })
            ->toArray();
    }

    /**
     * Build context for feedback question generation
     */
    protected function buildFeedbackContext(User $user, ?HrmEmployee $employee): array
    {
        $context = [
            'employee_name' => $user->name,
            'department' => $employee?->department_id ?? 'General',
            'position' => $employee?->position ?? 'N/A',
            'week_number' => now()->weekOfYear,
            'timestamp' => now()->toIso8601String(),
        ];

        // Get previous feedback for trending
        $previousFeedback = EmployeeFeedback::where('user_id', $user->id)
            ->where('is_submitted', true)
            ->latest('submitted_at')
            ->first();

        if ($previousFeedback) {
            $context['previous_feedback_date'] = $previousFeedback->submitted_at->toIso8601String();
            $context['has_previous_feedback'] = true;
        }

        return $context;
    }

    /**
     * Build the prompt for AI question generation
     */
    protected function buildQuestionPrompt(string $category, array $context, int $sequence): string
    {
        $employee = $context['employee_name'];
        $department = $context['department'];

        return match ($category) {
            'feelings' => <<<PROMPT
            Generate a specific, professional question for an employee feedback form that asks about their feelings toward the company this week.
            
            Employee: $employee
            Department: $department
            Sequence: $sequence/3
            
            Requirements:
            - Question should be 1-2 sentences
            - Be specific about "this week"
            - Encourage detailed, thoughtful response
            - Show genuine interest in their experience
            - Should feel personalized, not generic
            
            Generate ONLY the question, nothing else.
            PROMPT,

            'work_progress' => <<<PROMPT
            Generate a specific, professional question for an employee feedback form about their work progress this week.
            
            Employee: $employee
            Department: $department
            Sequence: $sequence/3
            
            Requirements:
            - Question should be 1-2 sentences
            - Ask about specific accomplishments and challenges
            - Encourage reflection on productivity
            - Should feel like a supportive manager asking
            - Avoid generic productivity questions
            
            Generate ONLY the question, nothing else.
            PROMPT,

            'improvements' => <<<PROMPT
            Generate a specific, professional question for an employee feedback form about areas for improvement.
            
            Employee: $employee
            Department: $department
            Sequence: $sequence/3
            
            Requirements:
            - Question should be 1-2 sentences
            - Frame as growth opportunity, not criticism
            - Ask about skills development or learning goals
            - Encourage self-reflection and positive attitude
            - Should feel constructive and supportive
            
            Generate ONLY the question, nothing else.
            PROMPT,

            default => 'What are your thoughts on this week?',
        };
    }

    /**
     * Get default questions (fallback when AI is unavailable)
     */
    protected function getDefaultQuestions(): array
    {
        return [
            [
                'id' => 0,
                'category' => 'feelings',
                'question' => 'How are you feeling about the company this week?',
                'sequence' => 1,
                'is_default' => true,
            ],
            [
                'id' => 0,
                'category' => 'work_progress',
                'question' => 'What progress did you make on your tasks this week?',
                'sequence' => 2,
                'is_default' => true,
            ],
            [
                'id' => 0,
                'category' => 'improvements',
                'question' => 'What areas would you like to improve or develop?',
                'sequence' => 3,
                'is_default' => true,
            ],
        ];
    }

    /**
     * Get a single default question
     */
    protected function getDefaultQuestion(string $category, int $sequence): array
    {
        $defaults = [
            'feelings' => 'How are you feeling about the company this week?',
            'work_progress' => 'What progress did you make on your tasks this week?',
            'improvements' => 'What areas would you like to improve or develop?',
        ];

        return [
            'id' => 0,
            'category' => $category,
            'question' => $defaults[$category] ?? 'Please share your thoughts.',
            'sequence' => $sequence,
            'is_default' => true,
        ];
    }
}
