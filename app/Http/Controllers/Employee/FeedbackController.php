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
            'feelings' => 'required|string|min:10',
            'work_progress' => 'required|string|min:10',
            'self_improvements' => 'required|string|min:10',
        ]);

        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());

        if (!$weeklyFeedback) {
            $weeklyFeedback = EmployeeFeedback::create([
                'user_id' => auth()->id(),
                'feelings' => $request->feelings,
                'work_progress' => $request->work_progress,
                'self_improvements' => $request->self_improvements,
                'is_submitted' => true,
                'submitted_at' => now(),
            ]);
        } else {
            $weeklyFeedback->update([
                'feelings' => $request->feelings,
                'work_progress' => $request->work_progress,
                'self_improvements' => $request->self_improvements,
                'is_submitted' => true,
                'submitted_at' => now(),
            ]);
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
     */
    private function getDaysUntilFriday()
    {
        $today = Carbon::now();
        $friday = $today->copy()->endOfWeek(); // Friday is endOfWeek

        // If today is after Friday, get next week's Friday
        if ($today->dayOfWeek > Carbon::FRIDAY) {
            $friday = $friday->addWeek();
        }
        // If today IS Friday, return 0
        elseif ($today->dayOfWeek === Carbon::FRIDAY) {
            return 0;
        }

        return (int) $today->diffInDays($friday);
    }
}
