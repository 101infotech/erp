<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrmAttendanceDay;
use App\Models\HrmEmployee;
use App\Models\Holiday;
use App\Services\JibblePeopleService;
use App\Services\JibbleTimesheetService;
use App\Services\JibbleTimeTrackingService;
use App\Services\JibbleActiveUsersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class HrmAttendanceController extends Controller
{
    public function __construct(
        private readonly JibblePeopleService $peopleService,
        private readonly JibbleTimesheetService $timesheetService,
        private readonly JibbleTimeTrackingService $timeTrackingService,
        private readonly JibbleActiveUsersService $activeUsersService
    ) {}

    public function index(Request $request)
    {
        $query = HrmAttendanceDay::with('employee');

        $defaultStart = now()->startOfMonth()->toDateString();
        $defaultEnd = now()->endOfMonth()->toDateString();

        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', $defaultEnd);

        if (Carbon::parse($endDate)->lt(Carbon::parse($startDate))) {
            $endDate = $startDate;
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $query->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(30);
        $employees = HrmEmployee::active()->orderBy('name')->get();

        return view('admin.hrm.attendance.index', compact('attendances', 'employees', 'startDate', 'endDate'));
    }

    public function show(HrmAttendanceDay $attendance)
    {
        $attendance->load(['employee', 'timeEntries' => function ($query) {
            $query->orderBy('time', 'asc');
        }]);

        return view('admin.hrm.attendance.show', compact('attendance'));
    }

    public function employee(Request $request, HrmEmployee $employee)
    {
        // Handle month parameter (format: YYYY-MM in AD)
        if ($request->filled('month')) {
            $month = $request->input('month');
            $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            // Default to current month
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $attendances = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->get();

        $totalTracked = $attendances->sum('tracked_hours');
        $totalPayroll = $attendances->sum('payroll_hours');
        $totalOvertime = $attendances->sum('overtime_hours');

        return view('admin.hrm.attendance.employee', compact(
            'employee',
            'attendances',
            'startDate',
            'endDate',
            'totalTracked',
            'totalPayroll',
            'totalOvertime'
        ));
    }

    public function calendar(Request $request)
    {
        $monthInput = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::parse($monthInput . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $month = (int) $startDate->format('m');
        $year = (int) $startDate->format('Y');

        $query = HrmAttendanceDay::with('employee')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get()->groupBy('date');
        $employees = HrmEmployee::active()->orderBy('name')->get();

        $holidays = Holiday::where('is_active', true)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->get()
            ->groupBy(fn($holiday) => $holiday->date->toDateString());

        return view('admin.hrm.attendance.calendar', compact(
            'attendances',
            'employees',
            'month',
            'year',
            'startDate',
            'endDate',
            'holidays'
        ));
    }

    /**
     * Sync attendance from Jibble
     */
    public function syncFromJibble(Request $request)
    {
        // Increase execution time and memory for large syncs
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'employee_id' => 'nullable|exists:hrm_employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'month' => 'nullable|date_format:Y-m',
        ]);

        try {
            $startDateAD = $validated['start_date'];
            $endDateAD = $validated['end_date'];

            Log::info('Starting Jibble sync', [
                'start_date' => $startDateAD,
                'end_date' => $endDateAD,
                'employee_id' => $validated['employee_id'] ?? null
            ]);

            // Sync daily summary (tracked hours, payroll, overtime)
            $summaryCount = $this->timesheetService->syncDailySummary(
                $startDateAD,
                $endDateAD
            );

            Log::info('Timesheet summary synced', ['count' => $summaryCount]);

            // Sync time entries (clock in/out details)
            $entriesCount = $this->timeTrackingService->syncTimeEntries(
                $startDateAD,
                $endDateAD
            );

            Log::info('Time entries synced', ['count' => $entriesCount]);

            $successMessage = "Successfully synced {$summaryCount} attendance records and {$entriesCount} time entries from Jibble.";

            // Redirect back to employee timesheet if employee_id is provided
            if ($request->filled('employee_id')) {
                $employee = HrmEmployee::findOrFail($validated['employee_id']);
                return redirect()
                    ->route('admin.hrm.attendance.employee', $employee)
                    ->with('success', $successMessage);
            }

            return redirect()
                ->route('admin.hrm.attendance.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to sync from Jibble: ' . $e->getMessage();

            // Redirect back to employee timesheet if employee_id is provided
            if ($request->filled('employee_id')) {
                $employee = HrmEmployee::findOrFail($validated['employee_id']);
                return redirect()
                    ->route('admin.hrm.attendance.employee', $employee)
                    ->with('error', $errorMessage);
            }

            return redirect()
                ->route('admin.hrm.attendance.index')
                ->with('error', $errorMessage);
        }
    }

    /**
     * Show sync form
     */
    public function syncForm()
    {
        return view('admin.hrm.attendance.sync');
    }

    /**
     * Sync employees from Jibble
     */
    public function syncEmployees()
    {
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', 300);

        try {
            $employeeCount = $this->peopleService->syncEmployees();

            return redirect()
                ->route('admin.users.index')
                ->with('success', "Successfully synced {$employeeCount} employees from Jibble.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Failed to sync employees from Jibble: ' . $e->getMessage());
        }
    }

    /**
     * Sync all data from Jibble (employees + attendance for last 30 days)
     */
    public function syncAll(Request $request)
    {
        // Increase execution time and memory for large syncs
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        $days = $validated['days'] ?? 30;

        try {
            // Sync employees first
            $employeeCount = $this->peopleService->syncEmployees();

            // Calculate date range
            $endDate = Carbon::today()->toDateString();
            $startDate = Carbon::today()->subDays($days - 1)->toDateString();

            // Sync attendance summaries
            $summaryCount = $this->timesheetService->syncDailySummary($startDate, $endDate);

            // Sync time entries
            $entriesCount = $this->timeTrackingService->syncTimeEntries($startDate, $endDate);

            return redirect()
                ->route('admin.hrm.attendance.index')
                ->with('success', "Successfully synced {$employeeCount} employees, {$summaryCount} attendance records, and {$entriesCount} time entries from Jibble (last {$days} days).");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.hrm.attendance.index')
                ->with('error', 'Failed to sync from Jibble: ' . $e->getMessage());
        }
    }

    /**
     * Show currently active (logged in) employees from Jibble
     */
    public function activeUsers()
    {
        $error = null;
        $activeEmployees = [];
        try {
            $activeEmployees = $this->activeUsersService->getCachedActiveEmployees();
        } catch (\Exception $e) {
            $error = 'Failed to fetch active users: ' . $e->getMessage();
        }
        return view('admin.hrm.attendance.active-users', compact('activeEmployees', 'error'));
    }

    /**
     * Get active users as JSON (for AJAX calls)
     */
    public function activeUsersJson()
    {
        try {
            $activeEmployees = $this->activeUsersService->getCachedActiveEmployees();

            return response()->json([
                'success' => true,
                'data' => $activeEmployees,
                'count' => count($activeEmployees)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active users: ' . $e->getMessage()
            ], 500);
        }
    }
}
