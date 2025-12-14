<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\NotificationService;
use App\Mail\ComplaintStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $query = Complaint::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $complaints = $query->latest()->paginate(15);

        // Get unique categories for filter
        $categories = Complaint::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        $complaint->load('user');
        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,resolved,dismissed',
            'admin_notes' => 'nullable|string',
        ]);

        $complaint->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes ?? $complaint->admin_notes,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        // Send email notification to user
        if ($complaint->user && $complaint->user->email && filter_var($complaint->user->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($complaint->user->email)->queue(new ComplaintStatusUpdated($complaint));
        }

        // Create in-app notification for user
        if ($complaint->user) {
            $notificationService = app(NotificationService::class);
            $statusText = ucfirst($request->status);
            $notificationService->createNotification(
                $complaint->user->id,
                'complaint_status_updated',
                'Complaint Status Updated',
                "Your complaint '{$complaint->subject}' status has been updated to {$statusText}.",
                route('employee.complaints.show', $complaint->id),
                [
                    'complaint_id' => $complaint->id,
                    'new_status' => $request->status,
                ]
            );
        }

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Complaint status updated successfully.');
    }

    /**
     * Update complaint priority
     */
    public function updatePriority(Request $request, Complaint $complaint)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high',
        ]);

        $complaint->update(['priority' => $request->priority]);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Complaint priority updated successfully.');
    }

    /**
     * Add admin notes
     */
    public function addNotes(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $complaint->update(['admin_notes' => $request->admin_notes]);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Notes added successfully.');
    }

    /**
     * Delete a complaint
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint deleted successfully.');
    }
}
