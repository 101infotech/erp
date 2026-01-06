<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmLeaveRequest;
use App\Models\HrmEmployee;
use App\Models\HrmLeavePolicy;
use App\Services\LeavePolicyService;
use App\Services\NotificationService;
use App\Mail\LeaveApproved;
use App\Mail\LeaveRejected;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HrmLeaveController extends Controller
{
    /**
     * Display list of leave requests
     */
    public function index(Request $request)
    {
        $query = HrmLeaveRequest::with('employee', 'approver', 'rejecter', 'canceller');

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = HrmEmployee::select('id', 'name')->get();

        return view('admin.hrm.leaves.index', compact('leaves', 'employees', 'status'));
    }

    /**
     * Show leave request form
     */
    public function create()
    {
        $employees = HrmEmployee::where('status', 'active')
            ->select('id', 'name', 'code', 'paid_leave_annual', 'paid_leave_sick', 'paid_leave_casual')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.leaves.create', compact('employees'));
    }

    /**
     * Store new leave request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'leave_type' => 'required|in:annual,sick,casual,unpaid,period',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $employee = HrmEmployee::findOrFail($validated['employee_id']);

        // Calculate working days using Carbon
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);

        $workDays = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isSaturday()) {
                $workDays++;
            }
        }

        // Check leave balance for paid leaves using policy service
        if ($validated['leave_type'] !== 'unpaid') {
            $policyService = new LeavePolicyService();
            $validation = $policyService->validateLeaveRequest($employee, $validated['leave_type'], $workDays);

            if (!$validation['valid']) {
                return back()->with('error', $validation['message'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $workDays) {
            HrmLeaveRequest::create([
                'employee_id' => $validated['employee_id'],
                'leave_type' => $validated['leave_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $workDays,
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);
        });

        return redirect()->route('admin.hrm.leaves.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display single leave request
     */
    public function show($id)
    {
        $leave = HrmLeaveRequest::with('employee', 'approver', 'rejecter', 'canceller')->findOrFail($id);
        return view('admin.hrm.leaves.show', compact('leave'));
    }

    /**
     * Approve leave request
     */
    public function approve($id)
    {
        $leave = HrmLeaveRequest::with('employee')->findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be approved.');
        }

        DB::transaction(function () use ($leave) {
            // Deduct leave balance
            if ($leave->leave_type !== 'unpaid') {
                $employee = $leave->employee;
                $balanceField = 'paid_leave_' . $leave->leave_type;
                $currentBalance = $employee->$balanceField ?? 0;
                $newBalance = max(0, $currentBalance - $leave->total_days);
                $employee->update([$balanceField => $newBalance]);
            }

            $leave->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        // Send email notification to employee
        $employee = $leave->employee;
        if ($employee->user && $employee->user->email && filter_var($employee->user->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($employee->user->email)->queue(new LeaveApproved($leave));
        }

        // Create in-app notification for employee
        if ($employee->user) {
            $notificationService = app(NotificationService::class);
            $notificationService->createNotification(
                $employee->user->id,
                'leave_approved',
                'Leave Request Approved',
                "Your {$leave->leave_type} leave request from " . \Carbon\Carbon::parse($leave->start_date)->format('M d') . " to " . \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') . " has been approved.",
                route('employee.leave.show', $leave->id),
                [
                    'leave_type' => $leave->leave_type,
                    'total_days' => $leave->total_days,
                ]
            );
        }

        return redirect()->route('admin.hrm.leaves.show', $id)
            ->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject leave request
     */
    public function reject(Request $request, $id)
    {
        $leave = HrmLeaveRequest::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Send email notification to employee
        $employee = $leave->employee;
        if ($employee->user && $employee->user->email && filter_var($employee->user->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($employee->user->email)->queue(new LeaveRejected($leave));
        }

        // Create in-app notification for employee
        if ($employee->user) {
            $notificationService = app(NotificationService::class);
            $notificationService->createNotification(
                $employee->user->id,
                'leave_rejected',
                'Leave Request Rejected',
                "Your {$leave->leave_type} leave request from " . \Carbon\Carbon::parse($leave->start_date)->format('M d') . " to " . \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') . " has been rejected.",
                route('employee.leave.show', $leave->id),
                [
                    'leave_type' => $leave->leave_type,
                    'rejection_reason' => $validated['rejection_reason'],
                ]
            );
        }

        return redirect()->route('admin.hrm.leaves.show', $id)
            ->with('success', 'Leave request rejected.');
    }

    /**
     * Cancel leave request
     */
    public function cancel($id)
    {
        $leave = HrmLeaveRequest::with('employee')->findOrFail($id);

        if ($leave->status === 'cancelled') {
            return back()->with('error', 'Leave request is already cancelled.');
        }

        DB::transaction(function () use ($leave) {
            // If leave was approved, restore balance
            if ($leave->status === 'approved' && $leave->leave_type !== 'unpaid') {
                $employee = $leave->employee;
                $balanceField = 'paid_leave_' . $leave->leave_type;
                $currentBalance = $employee->$balanceField ?? 0;
                $employee->update([$balanceField => $currentBalance + $leave->total_days]);
            }

            $leave->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
            ]);
        });

        return back()->with('success', 'Leave request cancelled successfully.');
    }
}
