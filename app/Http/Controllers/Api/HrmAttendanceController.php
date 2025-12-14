<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HrmAttendanceDay;
use Illuminate\Http\Request;

class HrmAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = HrmAttendanceDay::with('employee');

        // Filter by employee ID (required)
        if (!$request->filled('employee_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee_id parameter is required'
            ], 400);
        }

        $query->where('employee_id', $request->employee_id);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $summary = [
            'total_tracked_hours' => $attendances->sum('tracked_hours'),
            'total_payroll_hours' => $attendances->sum('payroll_hours'),
            'total_overtime_hours' => $attendances->sum('overtime_hours'),
            'days_count' => $attendances->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $attendances,
            'summary' => $summary
        ]);
    }
}
