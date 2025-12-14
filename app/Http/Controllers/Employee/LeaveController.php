<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrmLeaveRequest;
use App\Models\HrmLeavePolicy;
use App\Services\LeavePolicyService;
use App\Services\NotificationService;
use App\Mail\LeaveRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LeaveController extends Controller
{
    /**
     * Display employee's leave requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return view('employee.leave.index', [
                'leaves' => collect(),
                'stats' => [],
                'message' => 'You are not linked to an employee record.'
            ]);
        }

        $employee = $user->hrmEmployee;

        // Get leave requests
        $leaves = HrmLeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate leave balance
        $stats = $this->calculateLeaveStats($employee->id);

        return view('employee.leave.index', compact('leaves', 'stats'));
    }

    /**
     * Show the form for creating a new leave request
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return redirect()->route('employee.leave.index')
                ->with('error', 'You are not linked to an employee record.');
        }

        $employee = $user->hrmEmployee;
        $stats = $this->calculateLeaveStats($employee->id);

        return view('employee.leave.create', compact('stats'));
    }

    /**
     * Store a newly created leave request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return redirect()->route('employee.leave.index')
                ->with('error', 'You are not linked to an employee record.');
        }

        $employee = $user->hrmEmployee;

        // Get available leave types from policies
        $policyService = new LeavePolicyService();
        $stats = $policyService->getLeaveBalanceSummary($employee);
        $availableLeaveTypes = array_keys($stats);
        $availableLeaveTypes[] = 'unpaid'; // Unpaid is always available

        $request->validate([
            'leave_type' => ['required', 'in:' . implode(',', $availableLeaveTypes)],
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Calculate total days
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Validate leave request using policy service
        $leaveType = $request->leave_type;

        if ($leaveType !== 'unpaid') {
            $validation = $policyService->validateLeaveRequest($employee, $leaveType, $totalDays);

            if (!$validation['valid']) {
                return back()->with('error', $validation['message']);
            }
        }

        // Create leave request
        $leave = HrmLeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => $leaveType,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Send email notification to employee
        if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($user->email)->queue(new LeaveRequestSubmitted($leave));
        }

        // Create in-app notification for admins
        $notificationService = app(NotificationService::class);
        $notificationService->notifyAdmins(
            'leave_request',
            'New Leave Request',
            "{$employee->name} has submitted a {$leaveType} leave request for {$totalDays} day(s).",
            route('admin.hrm.leaves.show', $leave->id),
            [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'leave_type' => $leaveType,
                'total_days' => $totalDays,
            ]
        );

        return redirect()->route('employee.leave.index')
            ->with('success', 'Leave request submitted successfully. Awaiting approval.');
    }

    /**
     * Display the specified leave request
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            abort(403, 'Not linked to employee record');
        }

        $employee = $user->hrmEmployee;

        $leave = HrmLeaveRequest::where('employee_id', $employee->id)
            ->findOrFail($id);

        return view('employee.leave.show', compact('leave'));
    }

    /**
     * Cancel a pending leave request
     */
    public function cancel($id)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return redirect()->route('employee.leave.index')
                ->with('error', 'Not linked to employee record');
        }

        $employee = $user->hrmEmployee;

        $leave = HrmLeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('employee.leave.index')
            ->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Get leave data as JSON for API
     */
    public function data(Request $request)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not linked to employee record'
            ], 404);
        }

        $employee = $user->hrmEmployee;

        $leaves = HrmLeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $this->calculateLeaveStats($employee->id);

        return response()->json([
            'status' => 'success',
            'data' => $leaves,
            'stats' => $stats
        ]);
    }

    /**
     * Calculate leave statistics using leave policies
     */
    protected function calculateLeaveStats($employeeId)
    {
        $employee = \App\Models\HrmEmployee::find($employeeId);

        if (!$employee) {
            return [];
        }

        $policyService = new \App\Services\LeavePolicyService();
        return $policyService->getLeaveBalanceSummary($employee);
    }
}
