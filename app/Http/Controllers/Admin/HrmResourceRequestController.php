<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmResourceRequest;
use App\Models\HrmEmployee;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HrmResourceRequestController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display list of resource requests
     */
    public function index(Request $request)
    {
        $query = HrmResourceRequest::with(['employee', 'approver', 'fulfiller']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // If not admin, show only own requests
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            $employee = HrmEmployee::where('user_id', $user->id)->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        $requests = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => HrmResourceRequest::pending()->count(),
            'approved' => HrmResourceRequest::approved()->count(),
            'fulfilled' => HrmResourceRequest::fulfilled()->count(),
            'rejected' => HrmResourceRequest::rejected()->count(),
        ];

        return view('admin.hrm.resource-requests.index', compact('requests', 'stats'));
    }

    /**
     * Show the form for creating a new resource request
     */
    public function create()
    {
        $employees = HrmEmployee::where('status', 'active')
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.resource-requests.create', compact('employees'));
    }

    /**
     * Store a newly created resource request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:office_supplies,equipment,pantry,furniture,technology,other',
            'reason' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $resourceRequest = HrmResourceRequest::create($validated);

            // Send notification to admins
            $employee = HrmEmployee::find($validated['employee_id']);
            $this->notificationService->notifyAdmins(
                'New Resource Request',
                "{$employee->name} has requested {$validated['item_name']} (Priority: {$validated['priority']})",
                'resource_request',
                route('admin.hrm.resource-requests.show', $resourceRequest->id)
            );

            DB::commit();

            return redirect()->route('admin.hrm.resource-requests.index')
                ->with('success', 'Resource request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resource request creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create resource request. Please try again.');
        }
    }

    /**
     * Display the specified resource request
     */
    public function show($id)
    {
        $request = HrmResourceRequest::with(['employee', 'approver', 'fulfiller'])->findOrFail($id);
        return view('admin.hrm.resource-requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified resource request
     */
    public function edit($id)
    {
        $resourceRequest = HrmResourceRequest::findOrFail($id);

        // Only allow editing if pending
        if (!$resourceRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $employees = HrmEmployee::where('status', 'active')
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.resource-requests.edit', compact('resourceRequest', 'employees'));
    }

    /**
     * Update the specified resource request
     */
    public function update(Request $request, $id)
    {
        $resourceRequest = HrmResourceRequest::findOrFail($id);

        // Only allow updating if pending
        if (!$resourceRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be updated.');
        }

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:office_supplies,equipment,pantry,furniture,technology,other',
            'reason' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            $resourceRequest->update($validated);

            return redirect()->route('admin.hrm.resource-requests.show', $resourceRequest->id)
                ->with('success', 'Resource request updated successfully.');
        } catch (\Exception $e) {
            Log::error('Resource request update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update resource request. Please try again.');
        }
    }

    /**
     * Approve a resource request
     */
    public function approve(Request $request, $id)
    {
        $resourceRequest = HrmResourceRequest::findOrFail($id);

        if (!$resourceRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $resourceRequest->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_by_name' => $user->name,
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
            ]);

            // Notify the employee
            $employee = $resourceRequest->employee;
            if ($employee->user_id) {
                $this->notificationService->createNotification(
                    $employee->user_id,
                    'Resource Request Approved',
                    "Your request for {$resourceRequest->item_name} has been approved.",
                    'resource_request',
                    route('admin.hrm.resource-requests.show', $resourceRequest->id)
                );
            }

            DB::commit();

            return back()->with('success', 'Resource request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resource request approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve resource request. Please try again.');
        }
    }

    /**
     * Reject a resource request
     */
    public function reject(Request $request, $id)
    {
        $resourceRequest = HrmResourceRequest::findOrFail($id);

        if (!$resourceRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $resourceRequest->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_by_name' => $user->name,
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Notify the employee
            $employee = $resourceRequest->employee;
            if ($employee->user_id) {
                $this->notificationService->createNotification(
                    $employee->user_id,
                    'Resource Request Rejected',
                    "Your request for {$resourceRequest->item_name} has been rejected.",
                    'resource_request',
                    route('admin.hrm.resource-requests.show', $resourceRequest->id)
                );
            }

            DB::commit();

            return back()->with('success', 'Resource request rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resource request rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject resource request. Please try again.');
        }
    }

    /**
     * Mark a resource request as fulfilled
     */
    public function fulfill(Request $request, $id)
    {
        $resourceRequest = HrmResourceRequest::findOrFail($id);

        if (!$resourceRequest->isApproved()) {
            return back()->with('error', 'Only approved requests can be marked as fulfilled.');
        }

        $request->validate([
            'fulfillment_notes' => 'nullable|string',
            'actual_cost' => 'nullable|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $resourceRequest->update([
                'status' => 'fulfilled',
                'fulfilled_by' => $user->id,
                'fulfilled_by_name' => $user->name,
                'fulfilled_at' => now(),
                'fulfillment_notes' => $request->fulfillment_notes,
                'actual_cost' => $request->actual_cost,
                'vendor' => $request->vendor,
            ]);

            // Notify the employee
            $employee = $resourceRequest->employee;
            if ($employee->user_id) {
                $this->notificationService->createNotification(
                    $employee->user_id,
                    'Resource Request Fulfilled',
                    "Your request for {$resourceRequest->item_name} has been fulfilled.",
                    'resource_request',
                    route('admin.hrm.resource-requests.show', $resourceRequest->id)
                );
            }

            DB::commit();

            return back()->with('success', 'Resource request marked as fulfilled.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resource request fulfillment failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to fulfill resource request. Please try again.');
        }
    }

    /**
     * Remove the specified resource request
     */
    public function destroy($id)
    {
        $resourceRequest = HrmResourceRequest::findOrFail($id);

        // Only allow deleting if pending
        if (!$resourceRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be deleted.');
        }

        try {
            $resourceRequest->delete();
            return redirect()->route('admin.hrm.resource-requests.index')
                ->with('success', 'Resource request deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Resource request deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete resource request. Please try again.');
        }
    }
}
