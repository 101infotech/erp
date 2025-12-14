<?php

namespace App\Services;

use App\Models\HrmEmployee;
use App\Models\HrmAttendanceDay;
use App\Models\HrmLeaveRequest;
use App\Models\HrmPayrollRecord;
use Carbon\Carbon;

/**
 * Payroll Calculation Service
 * 
 * Handles payroll calculation for employees based on attendance data,
 * salary structure, allowances, deductions, and Nepal tax slabs.
 */
class PayrollCalculationService
{
    protected NepalTaxCalculationService $taxService;
    protected PayrollAnomalyDetector $anomalyDetector;

    public function __construct(
        NepalTaxCalculationService $taxService,
        PayrollAnomalyDetector $anomalyDetector
    ) {
        $this->taxService = $taxService;
        $this->anomalyDetector = $anomalyDetector;
    }

    /**
     * Calculate payroll for a single employee for a given period
     *
     * @param HrmEmployee $employee
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param string $periodStartBs BS date in YYYY-MM-DD format
     * @param string $periodEndBs BS date in YYYY-MM-DD format
     * @param float $overtimePayment Manually entered overtime payment
     * @param int|null $monthTotalDays Total days in the BS month (admin provided, null for auto-detect)
     * @param float $standardWorkingHours Standard working hours per day (default 8.00)
     * @return array Calculation details
     */
    public function calculatePayroll(
        HrmEmployee $employee,
        Carbon $periodStart,
        Carbon $periodEnd,
        string $periodStartBs,
        string $periodEndBs,
        float $overtimePayment = 0,
        ?int $monthTotalDays = null,
        float $standardWorkingHours = 8.00
    ): array {
        // Determine month total days
        if ($monthTotalDays === null) {
            // Auto-detect from BS start date
            [$year, $month] = explode('-', $periodStartBs);
            $monthTotalDays = app(NepalCalendarService::class)->getDaysInMonth((int)$year, (int)$month);
        }

        // Get attendance data for the period
        $attendanceData = $this->getAttendanceData($employee, $periodStart, $periodEnd);

        // Get paid leave data
        $paidLeaveData = $this->getPaidLeaveData($employee, $periodStart, $periodEnd);

        // Calculate basic salary for the period
        $basicSalary = $this->calculateBasicSalary(
            $employee,
            $attendanceData,
            $paidLeaveData,
            $monthTotalDays,
            $periodStart,
            $periodEnd
        );

        // Calculate working hours metrics
        $workingHoursMetrics = $this->calculateWorkingHoursMetrics(
            $employee,
            $attendanceData,
            $paidLeaveData,
            $periodStart,
            $periodEnd,
            $standardWorkingHours
        );

        // Get allowances
        $allowances = $employee->allowances ?? [];
        $allowancesTotal = $this->calculateAllowancesTotal($allowances);

        // Calculate gross salary
        $grossSalary = $basicSalary + $overtimePayment + $allowancesTotal;

        // Calculate tax
        $monthsInPeriod = $periodStart->diffInMonths($periodEnd) + 1;
        $taxAmount = $this->taxService->calculateTaxForPeriod($grossSalary, $monthsInPeriod);

        // Get deductions
        $deductions = $employee->deductions ?? [];

        // Calculate unpaid leave deduction
        $unpaidLeaveDeduction = $this->calculateUnpaidLeaveDeduction(
            $employee,
            $attendanceData,
            $monthTotalDays
        );

        $deductionsTotal = $this->calculateDeductionsTotal($deductions) + $unpaidLeaveDeduction;

        // Calculate net salary (hourly deduction and advance payment are added later by admin)
        $netSalary = $grossSalary - $taxAmount - $deductionsTotal;

        // Detect anomalies
        $anomalies = $this->anomalyDetector->detectAnomaliesForEmployee($employee, $periodStart, $periodEnd);

        return [
            'employee_id' => $employee->id,
            'period_start_bs' => $periodStartBs,
            'period_end_bs' => $periodEndBs,
            'period_start_ad' => $periodStart->format('Y-m-d'),
            'period_end_ad' => $periodEnd->format('Y-m-d'),
            'month_total_days' => $monthTotalDays,
            'standard_working_hours_per_day' => $standardWorkingHours,
            'total_hours_worked' => $attendanceData['total_hours'],
            'total_working_hours_required' => $workingHoursMetrics['required_hours'],
            'total_working_hours_missing' => $workingHoursMetrics['missing_hours'],
            'total_days_worked' => $attendanceData['days_worked'],
            'overtime_hours' => $attendanceData['overtime_hours'],
            'absent_days' => $attendanceData['absent_days'],
            'unpaid_leave_days' => $attendanceData['unpaid_leave_days'],
            'paid_leave_days_used' => $paidLeaveData['paid_leave_days'],
            'hourly_deduction_suggested' => $workingHoursMetrics['suggested_deduction'],
            'hourly_deduction_amount' => 0, // Admin will approve/edit
            'hourly_deduction_approved' => false,
            'basic_salary' => round($basicSalary, 2),
            'overtime_payment' => round($overtimePayment, 2),
            'allowances' => $allowances,
            'allowances_total' => round($allowancesTotal, 2),
            'gross_salary' => round($grossSalary, 2),
            'tax_amount' => round($taxAmount, 2),
            'tax_overridden' => false,
            'tax_override_reason' => null,
            'deductions' => $deductions,
            'deductions_total' => round($deductionsTotal, 2),
            'unpaid_leave_deduction' => round($unpaidLeaveDeduction, 2),
            'advance_payment' => 0, // Admin will add if applicable
            'advance_payment_details' => null,
            'net_salary' => round($netSalary, 2),
            'status' => 'draft',
            'anomalies' => $anomalies->map(fn($a) => $a)->toArray(),
            'anomalies_reviewed' => false,
        ];
    }

    /**
     * Get attendance data for employee in period
     *
     * @param HrmEmployee $employee
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return array
     */
    protected function getAttendanceData(HrmEmployee $employee, Carbon $periodStart, Carbon $periodEnd): array
    {
        $attendances = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();

        $totalHours = $attendances->sum('tracked_hours');
        $overtimeHours = $attendances->sum('overtime_hours');
        $daysWorked = $attendances->where('tracked_hours', '>', 0)->count();

        // Calculate absent days (total work days - days worked - weekends)
        $totalDays = $periodStart->diffInDays($periodEnd) + 1;
        $weekends = $this->countWeekends($periodStart, $periodEnd);
        $expectedWorkDays = $totalDays - $weekends;

        // Get unpaid leave days
        $unpaidLeaveDays = HrmLeaveRequest::where('employee_id', $employee->id)
            ->where('leave_type', 'unpaid')
            ->where('status', 'approved')
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->whereBetween('start_date', [$periodStart, $periodEnd])
                    ->orWhereBetween('end_date', [$periodStart, $periodEnd])
                    ->orWhere(function ($q) use ($periodStart, $periodEnd) {
                        $q->where('start_date', '<=', $periodStart)
                            ->where('end_date', '>=', $periodEnd);
                    });
            })
            ->sum('total_days');

        // Absent days = expected work days - days actually worked - unpaid leave days
        $absentDays = max(0, $expectedWorkDays - $daysWorked - $unpaidLeaveDays);

        return [
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'days_worked' => $daysWorked,
            'absent_days' => $absentDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'expected_work_days' => $expectedWorkDays,
        ];
    }

    /**
     * Get paid leave data for employee in period
     *
     * @param HrmEmployee $employee
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return array
     */
    protected function getPaidLeaveData(HrmEmployee $employee, Carbon $periodStart, Carbon $periodEnd): array
    {
        // Get approved paid leave requests in this period
        $paidLeaveRequests = HrmLeaveRequest::where('employee_id', $employee->id)
            ->whereIn('leave_type', ['annual', 'sick', 'casual']) // Paid leave types
            ->where('status', 'approved')
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->whereBetween('start_date', [$periodStart, $periodEnd])
                    ->orWhereBetween('end_date', [$periodStart, $periodEnd])
                    ->orWhere(function ($q) use ($periodStart, $periodEnd) {
                        $q->where('start_date', '<=', $periodStart)
                            ->where('end_date', '>=', $periodEnd);
                    });
            })
            ->get();

        $totalPaidLeaveDays = 0;
        foreach ($paidLeaveRequests as $leave) {
            $leaveStart = max($leave->start_date, $periodStart);
            $leaveEnd = min($leave->end_date, $periodEnd);
            $daysInPeriod = $leaveStart->diffInDays($leaveEnd) + 1;

            // Exclude weekends from paid leave count
            $current = $leaveStart->copy();
            $paidDaysCount = 0;
            while ($current->lte($leaveEnd)) {
                if (!$current->isSaturday()) {
                    $paidDaysCount++;
                }
                $current->addDay();
            }

            $totalPaidLeaveDays += $paidDaysCount;
        }

        return [
            'paid_leave_days' => $totalPaidLeaveDays,
            'requests' => $paidLeaveRequests,
        ];
    }

    /**
     * Calculate working hours metrics
     *
     * @param HrmEmployee $employee
     * @param array $attendanceData
     * @param array $paidLeaveData
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param float $standardWorkingHours
     * @return array
     */
    protected function calculateWorkingHoursMetrics(
        HrmEmployee $employee,
        array $attendanceData,
        array $paidLeaveData,
        Carbon $periodStart,
        Carbon $periodEnd,
        float $standardWorkingHours
    ): array {
        // Total expected working days (excluding weekends and paid leaves)
        $expectedWorkDays = $attendanceData['expected_work_days'];

        // Required hours = expected work days × standard hours
        $requiredHours = $expectedWorkDays * $standardWorkingHours;

        // Paid leave days count as "worked" for hour calculation purposes
        // So we reduce the required hours by paid leave hours
        $paidLeaveHours = $paidLeaveData['paid_leave_days'] * $standardWorkingHours;
        $adjustedRequiredHours = max(0, $requiredHours - $paidLeaveHours);

        // Missing hours
        $missingHours = max(0, $adjustedRequiredHours - $attendanceData['total_hours']);

        // Calculate suggested hourly deduction (only for monthly salary employees)
        $suggestedDeduction = 0;
        if ($employee->salary_type === 'monthly' && $missingHours > 0) {
            // Get daily rate first (this will be calculated properly in calculateBasicSalary)
            $monthlySalary = $employee->basic_salary_npr ?? 0;

            // We need month total days - but that's passed separately
            // For now, use a reasonable estimate (30 days)
            // This will be refined when we have the actual month_total_days
            $dailyRate = $monthlySalary / 30;
            $hourlyRate = $dailyRate / $standardWorkingHours;
            $suggestedDeduction = $hourlyRate * $missingHours;
        }

        return [
            'required_hours' => round($adjustedRequiredHours, 2),
            'missing_hours' => round($missingHours, 2),
            'suggested_deduction' => round($suggestedDeduction, 2),
        ];
    }

    /**
     * Calculate basic salary based on salary type
     *
     * @param HrmEmployee $employee
     * @param array $attendanceData
     * @param array $paidLeaveData
     * @param int $monthTotalDays Total days in the BS month
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return float
     */
    protected function calculateBasicSalary(
        HrmEmployee $employee,
        array $attendanceData,
        array $paidLeaveData,
        int $monthTotalDays,
        Carbon $periodStart,
        Carbon $periodEnd
    ): float {
        $basicSalaryNpr = $employee->basic_salary_npr ?? 0;

        switch ($employee->salary_type) {
            case 'monthly':
                // Calculate daily rate based on BS month total days
                $dailyRate = $basicSalaryNpr / $monthTotalDays;

                // Days to pay = days worked + paid leave days (they get paid for paid leaves)
                $daysToPay = $attendanceData['days_worked'] + $paidLeaveData['paid_leave_days'];

                // If it's a partial period, only pay for actual days in the period
                $periodTotalDays = $periodStart->diffInDays($periodEnd) + 1;
                $weekendsInPeriod = $this->countWeekends($periodStart, $periodEnd);
                $workDaysInPeriod = $periodTotalDays - $weekendsInPeriod;

                // Don't pay more than the work days in the period
                $daysToPay = min($daysToPay, $workDaysInPeriod);

                return $dailyRate * $daysToPay;

            case 'daily':
                // For daily employees, pay only for days worked + paid leave days
                $daysToPay = $attendanceData['days_worked'] + $paidLeaveData['paid_leave_days'];
                return $basicSalaryNpr * $daysToPay;

            case 'hourly':
                $hourlyRate = $employee->hourly_rate_npr ?? 0;
                // For hourly employees, paid leave should also be compensated
                $paidLeaveHours = $paidLeaveData['paid_leave_days'] * 8; // Assume 8 hours per day
                return $hourlyRate * ($attendanceData['total_hours'] + $paidLeaveHours);

            default:
                return $basicSalaryNpr;
        }
    }

    /**
     * Calculate total allowances
     *
     * @param array $allowances
     * @return float
     */
    protected function calculateAllowancesTotal(array $allowances): float
    {
        $total = 0;

        foreach ($allowances as $allowance) {
            if (isset($allowance['amount'])) {
                $total += (float) $allowance['amount'];
            }
        }

        return $total;
    }

    /**
     * Calculate total deductions
     *
     * @param array $deductions
     * @return float
     */
    protected function calculateDeductionsTotal(array $deductions): float
    {
        $total = 0;

        foreach ($deductions as $deduction) {
            if (isset($deduction['amount'])) {
                $total += (float) $deduction['amount'];
            }
        }

        return $total;
    }

    /**
     * Calculate unpaid leave deduction
     *
     * @param HrmEmployee $employee
     * @param array $attendanceData
     * @param int $monthTotalDays Total days in the BS month
     * @return float
     */
    protected function calculateUnpaidLeaveDeduction(
        HrmEmployee $employee,
        array $attendanceData,
        int $monthTotalDays
    ): float {
        if ($attendanceData['unpaid_leave_days'] == 0) {
            return 0;
        }

        // For monthly salary employees, deduct based on daily rate
        if ($employee->salary_type === 'monthly') {
            $dailyRate = ($employee->basic_salary_npr ?? 0) / $monthTotalDays;
            return $dailyRate * $attendanceData['unpaid_leave_days'];
        }

        // For daily/hourly employees, they're not paid for unpaid leave days anyway
        return 0;
    }

    /**
     * Count weekend days (only Saturdays) in period
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return int
     */
    protected function countWeekends(Carbon $start, Carbon $end): int
    {
        $weekends = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->isSaturday()) {
                $weekends++;
            }
            $current->addDay();
        }

        return $weekends;
    }

    /**
     * Generate payroll records for multiple employees
     *
     * @param array $employeeIds
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @param string $periodStartBs
     * @param string $periodEndBs
     * @param int|null $monthTotalDays Total days in BS month (admin provided)
     * @param float $standardWorkingHours Standard working hours per day
     * @return array ['success' => array, 'failed' => array]
     */
    public function generateBulkPayroll(
        array $employeeIds,
        Carbon $periodStart,
        Carbon $periodEnd,
        string $periodStartBs,
        string $periodEndBs,
        ?int $monthTotalDays = null,
        float $standardWorkingHours = 8.00
    ): array {
        $success = [];
        $failed = [];

        foreach ($employeeIds as $employeeId) {
            try {
                $employee = HrmEmployee::findOrFail($employeeId);

                // Check if payroll already exists for this period
                $existing = HrmPayrollRecord::where('employee_id', $employeeId)
                    ->where('period_start_bs', $periodStartBs)
                    ->where('period_end_bs', $periodEndBs)
                    ->first();

                if ($existing) {
                    $failed[] = [
                        'employee_id' => $employeeId,
                        'name' => $employee->full_name,
                        'reason' => 'Payroll already exists for this period',
                    ];
                    continue;
                }

                // Calculate payroll
                $calculation = $this->calculatePayroll(
                    $employee,
                    $periodStart,
                    $periodEnd,
                    $periodStartBs,
                    $periodEndBs,
                    0, // Default OT payment, can be edited later
                    $monthTotalDays,
                    $standardWorkingHours
                );

                // Create payroll record
                $payroll = HrmPayrollRecord::create($calculation);

                // Save anomalies to database
                $anomalies = collect($calculation['anomalies']);
                if ($anomalies->isNotEmpty()) {
                    $attendances = HrmAttendanceDay::where('employee_id', $employee->id)
                        ->whereBetween('date', [$periodStart, $periodEnd])
                        ->get();

                    foreach ($attendances as $attendance) {
                        $dayAnomalies = $this->anomalyDetector->detectDayAnomalies($employee, $attendance);
                        $this->anomalyDetector->saveAnomalies($dayAnomalies, $employee, $attendance);
                    }
                }

                $success[] = [
                    'employee_id' => $employeeId,
                    'name' => $employee->full_name,
                    'payroll_id' => $payroll->id,
                    'net_salary' => $payroll->net_salary,
                ];
            } catch (\Exception $e) {
                $failed[] = [
                    'employee_id' => $employeeId,
                    'name' => HrmEmployee::find($employeeId)->full_name ?? 'Unknown',
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }

    /**
     * Override tax amount with reason
     *
     * @param HrmPayrollRecord $payroll
     * @param float $newTaxAmount
     * @param string $reason
     * @return HrmPayrollRecord
     */
    public function overrideTax(HrmPayrollRecord $payroll, float $newTaxAmount, string $reason): HrmPayrollRecord
    {
        $oldTax = $payroll->tax_amount;
        $taxDifference = $oldTax - $newTaxAmount;

        $payroll->update([
            'tax_amount' => $newTaxAmount,
            'tax_overridden' => true,
            'tax_override_reason' => $reason,
            'net_salary' => $payroll->net_salary + $taxDifference, // Adjust net salary
        ]);

        return $payroll;
    }
}
