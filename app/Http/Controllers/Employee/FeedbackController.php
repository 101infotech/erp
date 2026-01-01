<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use App\Models\AiFeedbackSentimentAnalysis;
use App\Services\AI\AiFeedbackService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    protected $aiFeedbackService;

    public function __construct()
    {
        $this->aiFeedbackService = new AiFeedbackService();
    }

    /**
     * Dashboard - check if feedback already submitted this week
     */
    public function dashboard()
    {
        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());
        $isSubmitted = $weeklyFeedback && $weeklyFeedback->is_submitted;
        $daysUntilFriday = $this->getDaysUntilFriday();

        // Get latest sentiment analysis if available
        $latestSentiment = AiFeedbackSentimentAnalysis::getLatestForUser(auth()->id());

        return view('employee.feedback.dashboard', compact(
            'weeklyFeedback',
            'isSubmitted',
            'daysUntilFriday',
            'latestSentiment'
        ));
    }

    /**
     * Show form to submit or edit feedback
     */
    public function create()
    {
        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());

        if ($weeklyFeedback && $weeklyFeedback->is_submitted) {
            return redirect()->route('employee.feedback.dashboard')
                ->with('info', 'Your feedback has already been submitted this week.');
        }

        // Generate AI-powered questions
        $aiQuestions = $this->aiFeedbackService->generateFeedbackQuestions(auth()->id(), 3);

        return view('employee.feedback.create', compact(
            'weeklyFeedback',
            'aiQuestions'
        ));
    }

    /**
     * Store or update feedback
     */
    public function store(Request $request)
    {
        $request->validate([
            // Old format (optional for backward compatibility)
            'feelings' => 'nullable|string|min:10',
            'work_progress' => 'nullable|string|min:10',
            'self_improvements' => 'nullable|string|min:10',

            // New questionnaire format
            'stress_level' => 'required|integer|min:1|max:5',
            'workload_level' => 'required|integer|min:1|max:5',
            'work_satisfaction' => 'required|integer|min:1|max:5',
            'team_collaboration' => 'required|integer|min:1|max:5',
            'mental_wellbeing' => 'required|integer|min:1|max:5',
            'challenges_faced' => 'nullable|string',
            'achievements' => 'nullable|string',
            'support_needed' => 'nullable|string',
            'complaints' => 'nullable|string',
        ]);

        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());

        $feedbackData = [
            'user_id' => auth()->id(),
            'stress_level' => $request->stress_level,
            'workload_level' => $request->workload_level,
            'work_satisfaction' => $request->work_satisfaction,
            'team_collaboration' => $request->team_collaboration,
            'mental_wellbeing' => $request->mental_wellbeing,
            'challenges_faced' => $request->challenges_faced,
            'achievements' => $request->achievements,
            'support_needed' => $request->support_needed,
            'complaints' => $request->complaints,
            'is_submitted' => true,
            'submitted_at' => now(),
        ];

        if (!$weeklyFeedback) {
            $weeklyFeedback = EmployeeFeedback::create($feedbackData);
        } else {
            $weeklyFeedback->update($feedbackData);
        }

        // Perform AI sentiment analysis asynchronously
        try {
            $this->aiFeedbackService->analyzeFeedbackSentiment($weeklyFeedback);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Illuminate\Support\Facades\Log::warning('Sentiment analysis failed: ' . $e->getMessage());
        }

        return redirect()->route('employee.feedback.dashboard')
            ->with('success', 'Your weekly feedback has been submitted successfully!');
    }

    /**
     * View submitted feedback and admin notes
     */
    public function show(EmployeeFeedback $feedback)
    {
        // Ensure employee can only view their own feedback
        if ($feedback->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Get sentiment analysis if available
        $sentimentAnalysis = AiFeedbackSentimentAnalysis::where('feedback_id', $feedback->id)->first();

        return view('employee.feedback.show', compact('feedback', 'sentimentAnalysis'));
    }

    /**
     * Get previous week feedbacks
     */
    public function history()
    {
        $feedbacks = EmployeeFeedback::where('user_id', auth()->id())
            ->where('is_submitted', true)
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        // Get sentiment trend for the last 4 weeks
        $sentimentTrend = AiFeedbackSentimentAnalysis::getTrendForUser(auth()->id(), 4);

        return view('employee.feedback.history', compact(
            'feedbacks',
            'sentimentTrend'
        ));
    }

    /**
     * Calculate days until Friday (integer only, no decimal points)
     * Fixed to correctly calculate days remaining
     */
    private function getDaysUntilFriday()
    {
        $today = Carbon::now();
        $todayDayOfWeek = $today->dayOfWeek; // 0 = Sunday, 5 = Friday

        // If today is Friday (5), return 0
        if ($todayDayOfWeek === Carbon::FRIDAY) {
            return 0;
        }

        // If today is Saturday (6), next Friday is 6 days away
        if ($todayDayOfWeek === Carbon::SATURDAY) {
            return 6;
        }

        // If today is Sunday (0), next Friday is 5 days away
        if ($todayDayOfWeek === Carbon::SUNDAY) {
            return 5;
        }

        // For Monday-Thursday, calculate days until Friday
        // Monday (1) -> 4 days, Tuesday (2) -> 3 days, Wednesday (3) -> 2 days, Thursday (4) -> 1 day
        return Carbon::FRIDAY - $todayDayOfWeek;
    }
}
