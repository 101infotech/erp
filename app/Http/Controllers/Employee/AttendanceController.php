<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrmAttendanceDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display employee's attendance records
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return view('employee.attendance.index', [
                'attendances' => collect(),
                'stats' => [
                    'total_days' => 0,
                    'present_days' => 0,
                    'total_hours' => 0,
                    'average_hours' => 0,
                ],
                'message' => 'You are not linked to an employee record.'
            ]);
        }

        $employee = $user->hrmEmployee;

        // Get date range (default to current month)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Fetch attendance records
        $attendances = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate stats
        $stats = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('payroll_hours', '>', 0)->count(),
            'total_hours' => $attendances->sum('payroll_hours'),
            'average_hours' => $attendances->count() > 0 ? round($attendances->avg('payroll_hours'), 2) : 0,
        ];

        return view('employee.attendance.index', compact('attendances', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Get attendance data as JSON for API
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

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendances = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $attendances,
            'stats' => [
                'total_days' => $attendances->count(),
                'present_days' => $attendances->where('payroll_hours', '>', 0)->count(),
                'total_hours' => $attendances->sum('payroll_hours'),
                'average_hours' => $attendances->count() > 0 ? round($attendances->avg('payroll_hours'), 2) : 0,
            ]
        ]);
    }
}
