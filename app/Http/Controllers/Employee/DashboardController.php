<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrmAttendanceDay;
use App\Models\HrmLeaveRequest;
use App\Models\HrmPayrollRecord;
use App\Models\Announcement;
use App\Services\LeavePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display employee dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Redirect admin users to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (!$user->hrmEmployee) {
            return view('employee.dashboard', [
                'employee' => null,
                'stats' => [],
                'recentAttendance' => collect(),
                'recentPayrolls' => collect(),
                'pendingLeaves' => collect(),
                'recentAnnouncements' => collect(),
                'message' => 'You are not linked to an employee record. Please contact HR.'
            ]);
        }

        $employee = $user->hrmEmployee;

        // Get current month attendance
        $currentMonthAttendance = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();

        // Get recent attendance (last 7 days)
        $recentAttendance = HrmAttendanceDay::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Get recent payrolls (last 3)
        $recentPayrolls = HrmPayrollRecord::where('employee_id', $employee->id)
            ->orderBy('period_start_bs', 'desc')
            ->limit(3)
            ->get();

        // Get pending leave requests
        $pendingLeaves = HrmLeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent announcements
        $recentAnnouncements = Announcement::published()
            ->forUser($user->id)
            ->recent(3)
            ->with('creator')
            ->get();

        // Calculate leave balance
        $leaveStats = $this->calculateLeaveStats($employee->id);

        // Calculate stats
        $stats = [
            'attendance' => [
                'days_present' => $currentMonthAttendance->where('payroll_hours', '>', 0)->count(),
                'total_hours' => round($currentMonthAttendance->sum('payroll_hours'), 2),
                'average_hours' => $currentMonthAttendance->count() > 0
                    ? round($currentMonthAttendance->avg('payroll_hours'), 2)
                    : 0,
            ],
            'leaves' => $leaveStats,
            'payroll' => [
                'base_salary' => $employee->base_salary ?? 0,
                'last_payment' => $recentPayrolls->first()->net_salary ?? 0,
            ]
        ];

        return view('employee.dashboard', compact(
            'employee',
            'stats',
            'recentAttendance',
            'recentPayrolls',
            'pendingLeaves',
            'recentAnnouncements'
        ));
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

        $policyService = new LeavePolicyService();
        return $policyService->getLeaveBalanceSummary($employee);
    }
}
