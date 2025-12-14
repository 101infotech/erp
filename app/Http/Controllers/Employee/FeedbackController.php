<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    /**
     * Dashboard - check if feedback already submitted this week
     */
    public function dashboard()
    {
        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());
        $isSubmitted = $weeklyFeedback && $weeklyFeedback->is_submitted;
        $daysUntilFriday = $this->getDaysUntilFriday();

        return view('employee.feedback.dashboard', compact('weeklyFeedback', 'isSubmitted', 'daysUntilFriday'));
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

        return view('employee.feedback.create', compact('weeklyFeedback'));
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

        return view('employee.feedback.show', compact('feedback'));
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

        return view('employee.feedback.history', compact('feedbacks'));
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
