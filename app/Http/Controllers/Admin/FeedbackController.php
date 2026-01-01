<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeFeedback;
use App\Models\HrmEmployee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    /**
     * Display all employee feedback with filtering
     */
    public function index(Request $request)
    {
        $query = EmployeeFeedback::with('user');

        // Filter by week
        if ($request->filled('week')) {
            $weekStart = Carbon::parse($request->week)->startOfWeek();
            $weekEnd = Carbon::parse($request->week)->endOfWeek();
            $query->whereBetween('created_at', [$weekStart, $weekEnd]);
        } else {
            // Default: current week
            $query->thisWeek();
        }

        // Filter by submission status
        if ($request->filled('status')) {
            if ($request->status === 'submitted') {
                $query->submitted();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->latest('submitted_at')->paginate(15);

        // Get statistics
        $totalEmployees = HrmEmployee::count();
        $currentWeekSubmitted = EmployeeFeedback::thisWeek()->submitted()->count();
        $currentWeekPending = EmployeeFeedback::thisWeek()->pending()->count();

        return view('admin.feedback.index', compact(
            'feedbacks',
            'totalEmployees',
            'currentWeekSubmitted',
            'currentWeekPending'
        ));
    }

    /**
     * Display individual feedback
     */
    public function show(EmployeeFeedback $feedback)
    {
        $feedback->load('user');
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Add admin notes to feedback
     */
    public function addNotes(Request $request, EmployeeFeedback $feedback)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $feedback->update(['admin_notes' => $request->admin_notes]);

        return redirect()->route('admin.feedback.show', $feedback)
            ->with('success', 'Notes added successfully.');
    }

    /**
     * Analytics dashboard
     */
    public function analytics(Request $request)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Submission rate
        $totalEmployees = HrmEmployee::count();
        $submitted = EmployeeFeedback::thisWeek()->submitted()->count();
        $submissionRate = $totalEmployees > 0 ? round(($submitted / $totalEmployees) * 100) : 0;

        // Get feedbacks for analysis
        $feedbacks = EmployeeFeedback::thisWeek()->submitted()->with('user')->get();

        // Calculate average ratings
        $avgStress = $feedbacks->avg('stress_level') ?? 0;
        $avgWellbeing = $feedbacks->avg('mental_wellbeing') ?? 0;
        $avgWorkload = $feedbacks->avg('workload_level') ?? 0;
        $avgSatisfaction = $feedbacks->avg('work_satisfaction') ?? 0;
        $avgCollaboration = $feedbacks->avg('team_collaboration') ?? 0;

        // Get feedback summaries
        $allFeelings = $feedbacks->pluck('feelings')->filter()->implode(', ') ?: 'No specific feelings recorded';
        $allProgress = $feedbacks->pluck('achievements')->filter()->implode(', ') ?: 'No feedback provided';
        $allImprovements = $feedbacks->pluck('challenges_faced')->filter()->implode(', ') ?: 'No improvements noted';

        return view('admin.feedback.analytics', compact(
            'submissionRate',
            'submitted',
            'totalEmployees',
            'feedbacks',
            'allFeelings',
            'allProgress',
            'allImprovements',
            'avgStress',
            'avgWellbeing',
            'avgWorkload',
            'avgSatisfaction',
            'avgCollaboration'
        ));
    }
}
