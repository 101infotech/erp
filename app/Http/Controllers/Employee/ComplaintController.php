<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\NotificationService;
use App\Mail\ComplaintSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ComplaintController extends Controller
{
    /**
     * Display a listing of employee's own complaints
     */
    public function index(Request $request)
    {
        $query = Complaint::where('user_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->latest()->paginate(10);

        return view('employee.complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        return view('employee.complaints.create');
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:255',
        ]);

        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        // Send confirmation email to user
        $user = Auth::user();
        if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($user->email)->queue(new ComplaintSubmitted($complaint));
        }

        // Create in-app notification for admins
        $notificationService = app(NotificationService::class);
        $notificationService->notifyAdmins(
            'complaint_submitted',
            'New Complaint Submitted',
            "A new complaint has been submitted: {$complaint->subject}",
            route('admin.complaints.show', $complaint->id),
            [
                'complaint_id' => $complaint->id,
                'subject' => $complaint->subject,
                'category' => $complaint->category,
            ]
        );

        return redirect()->route('employee.complaints.index')
            ->with('success', 'Your feedback has been submitted anonymously. Thank you!');
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        // Ensure employee can only view their own complaints
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('employee.complaints.show', compact('complaint'));
    }
}
