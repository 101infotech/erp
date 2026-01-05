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
        // Validate employee has basic salary configured
        if (!$employee->basic_salary_npr || $employee->basic_salary_npr <= 0) {
            throw new \Exception("Employee {$employee->name} (ID: {$employee->id}) does not have basic salary configured. Please set basic salary before generating payroll.");
        }

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
        $basicSalaryResult = $this->calculateBasicSalary(
            $employee,
            $attendanceData,
            $paidLeaveData,
            $monthTotalDays,
            $periodStart,
            $periodEnd
        );
        $basicSalary = $basicSalaryResult['basic_salary'];
        $perDayRate = $basicSalaryResult['per_day_rate'];
        $totalPayableDays = $basicSalaryResult['total_payable_days'];

        // Calculate working hours metrics
        $workingHoursMetrics = $this->calculateWorkingHoursMetrics(
            $employee,
            $attendanceData,
            $paidLeaveData,
            $periodStart,
            $periodEnd,
            $standardWorkingHours,
            $monthTotalDays,
            $totalPayableDays
        );

        // Get allowances
        $allowances = $employee->allowances ?? [];
        $allowancesTotal = $this->calculateAllowancesTotal($allowances);

        // Get approved expense claims for this period
        $expenseClaimsData = $this->getExpenseClaimsForPeriod($employee, $periodStart, $periodEnd);
        $expenseClaimsTotal = $expenseClaimsData['total_amount'];

        // Calculate gross salary (including expense claims)
        $grossSalary = $basicSalary + $overtimePayment + $allowancesTotal + $expenseClaimsTotal;

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
            'holiday_days' => $attendanceData['holiday_days'] ?? 0,
            'weekend_days' => $this->countWeekends($periodStart, $periodEnd),
            'suggested_hourly_deduction' => $workingHoursMetrics['suggested_deduction'],
            'hourly_deduction' => 0, // Admin will approve/edit
            'basic_salary' => round($basicSalary, 2),
            'per_day_rate' => round($perDayRate, 2),
            'total_payable_days' => $totalPayableDays,
            'overtime_payment' => round($overtimePayment, 2),
            'allowances' => $allowances,
            'allowances_total' => round($allowancesTotal, 2),
            'expense_claims' => $expenseClaimsData['claims'],
            'expense_claims_total' => round($expenseClaimsTotal, 2),
            'gross_salary' => round($grossSalary, 2),
            'tax_amount' => round($taxAmount, 2),
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

        // Calculate absent days (total work days - days worked - weekends - holidays)
        $totalDays = $periodStart->diffInDays($periodEnd) + 1;
        $weekends = $this->countWeekends($periodStart, $periodEnd);
        $holidays = $this->countHolidays($periodStart, $periodEnd);
        $expectedWorkDays = $totalDays - $weekends - $holidays;

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
            'holiday_days' => $holidays,
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

        // Use the total_days field directly - it already accounts for half days (0.5)
        $totalPaidLeaveDays = $paidLeaveRequests->sum('total_days');

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
        float $standardWorkingHours,
        int $monthTotalDays,
        int $totalPayableDays
    ): array {
        // IMPORTANT: Required hours should be based on PAYABLE DAYS, not expected work days
        // Payable days = days worked + paid leave + weekends (already calculated in salary)
        // This is what we're actually paying for, so this is what we should require hours for
        // Absent days (non-leave absences) are NOT included in payable days
        $requiredHours = $totalPayableDays * $standardWorkingHours;

        // Missing hours = required hours - actual hours worked
        // No need to subtract leaves again since payable days already accounts for them
        $missingHours = max(0, $requiredHours - $attendanceData['total_hours']);

        // Calculate suggested hourly deduction (only for monthly salary employees)
        $suggestedDeduction = 0;
        if ($employee->salary_type === 'monthly' && $missingHours > 0) {
            // Get daily rate first (this will be calculated properly in calculateBasicSalary)
            $monthlySalary = $employee->basic_salary_npr ?? 0;

            // We need month total days - but that's passed separately
            // For now, use a reasonable estimate (30 days)
            // This will be refined when we have the actual month_total_days
            $dailyRate = $monthlySalary / $monthTotalDays;
            $hourlyRate = $dailyRate / $standardWorkingHours;
            $suggestedDeduction = $hourlyRate * $missingHours;
        }

        return [
            'required_hours' => round($requiredHours, 2),
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
    ): array {
        $basicSalaryNpr = $employee->basic_salary_npr ?? 0;

        // Determine paid leave entitlement if available on employee
        $paidLeaveQuotaDays = $employee->paid_leave_quota_days ?? null; // optional field
        $paidLeaveUsed = $employee->paid_leave_used_days ?? null; // optional field
        $remainingPaidLeave = null;
        if ($paidLeaveQuotaDays !== null && $paidLeaveUsed !== null) {
            $remainingPaidLeave = max(0, (int)$paidLeaveQuotaDays - (int)$paidLeaveUsed);
        }

        // Apply policy: do not deduct assigned paid leaves; cap paid leaves to remaining entitlement if provided
        $paidLeaveDays = $paidLeaveData['paid_leave_days'];
        if ($remainingPaidLeave !== null) {
            $paidLeaveDays = min($paidLeaveDays, $remainingPaidLeave);
        }

        switch ($employee->salary_type) {
            case 'monthly':
                // Calculate daily rate based on BS month total days
                $dailyRate = $basicSalaryNpr / $monthTotalDays;

                // Calculate payable days:
                // Days worked + Paid leave days + Weekends/Saturdays (non-working paid days)
                // Formula: Days in month - Absent days - Unpaid leave days = Payable days
                $unpaidLeaveDays = $attendanceData['unpaid_leave_days'] ?? 0;
                $absentDays = $attendanceData['absent_days'] ?? 0;

                // Payable days = Month total days - Absent days - Unpaid leave days
                // This automatically includes weekends/Saturdays as paid days
                $daysToPay = max(0, $monthTotalDays - $absentDays);

                // Don't pay more than the days in the period
                $periodTotalDays = $periodStart->diffInDays($periodEnd) + 1;
                $daysToPay = min($daysToPay, $periodTotalDays);

                return [
                    'basic_salary' => round($dailyRate * $daysToPay, 2),
                    'per_day_rate' => round($dailyRate, 2),
                    'total_payable_days' => (int) $daysToPay,
                ];

            case 'daily':
                // For daily employees, pay only for days worked + paid leave days
                $daysToPay = $attendanceData['days_worked'] + $paidLeaveDays;
                return [
                    'basic_salary' => round($basicSalaryNpr * $daysToPay, 2),
                    'per_day_rate' => round($basicSalaryNpr, 2),
                    'total_payable_days' => (int) $daysToPay,
                ];

            case 'hourly':
                $hourlyRate = $employee->hourly_rate_npr ?? 0;
                // For hourly employees, paid leave should also be compensated
                $paidLeaveHours = $paidLeaveDays * 8; // Assume 8 hours per day
                // Derive equivalent payable days for display purposes
                $standardHours = 8;
                $totalHoursForDays = $attendanceData['total_hours'] + $paidLeaveHours;
                $daysToPay = floor($totalHoursForDays / $standardHours);
                $perDayRate = $hourlyRate * $standardHours;
                return [
                    'basic_salary' => round($hourlyRate * $totalHoursForDays, 2),
                    'per_day_rate' => round($perDayRate, 2),
                    'total_payable_days' => (int) $daysToPay,
                ];

            default:
                return [
                    'basic_salary' => $basicSalaryNpr,
                    'per_day_rate' => $monthTotalDays > 0 ? round($basicSalaryNpr / $monthTotalDays, 2) : $basicSalaryNpr,
                    'total_payable_days' => 0,
                ];
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
     * Get approved expense claims for the payroll period
     *
     * @param HrmEmployee $employee
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return array
     */
    protected function getExpenseClaimsForPeriod(HrmEmployee $employee, Carbon $periodStart, Carbon $periodEnd): array
    {
        $claims = \App\Models\HrmExpenseClaim::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('included_in_payroll', false)
            ->whereBetween('expense_date', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
            ->get();

        $claimsArray = $claims->map(function ($claim) {
            return [
                'id' => $claim->id,
                'claim_number' => $claim->claim_number,
                'expense_type' => $claim->expense_type,
                'title' => $claim->title,
                'amount' => $claim->amount,
                'expense_date' => $claim->expense_date->format('Y-m-d'),
            ];
        })->toArray();

        return [
            'claims' => $claimsArray,
            'total_amount' => $claims->sum('amount'),
        ];
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
     * Count company-wide active holidays in period
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return int
     */
    protected function countHolidays(Carbon $start, Carbon $end): int
    {
        return \App\Models\Holiday::where('is_active', true)
            ->where('is_company_wide', true)
            ->whereBetween('date', [$start, $end])
            ->count();
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

                // Link expense claims to this payroll record
                if (!empty($calculation['expense_claims'])) {
                    $claimIds = collect($calculation['expense_claims'])->pluck('id')->toArray();
                    \App\Models\HrmExpenseClaim::whereIn('id', $claimIds)->update([
                        'payroll_record_id' => $payroll->id,
                        'included_in_payroll' => true,
                        'payroll_period_start' => $periodStart->format('Y-m-d'),
                        'payroll_period_end' => $periodEnd->format('Y-m-d'),
                    ]);
                }

                // Save anomalies to database
                $anomalies = collect($calculation['anomalies']);
                if ($anomalies->isNotEmpty()) {
                    $attendances = HrmAttendanceDay::where('employee_id', $employee->id)
                        ->whereBetween('date', [$periodStart, $periodEnd])
                        ->get();

                    // Save day-level anomalies (attached to specific attendance records)
                    $dayLevelTypes = [
                        'missing_clock_out',
                        'excessive_hours',
                        'weekend_work_no_ot',
                        'location_inconsistency',
                        'duplicate_entry',
                        'negative_time'
                    ];
                    $dayLevelAnomalies = $anomalies->whereIn('type', $dayLevelTypes);

                    foreach ($attendances as $attendance) {
                        $dayAnomalies = $this->anomalyDetector->detectDayAnomalies($employee, $attendance);
                        $this->anomalyDetector->saveAnomalies($dayAnomalies, $employee, $attendance);
                    }

                    // Save period-level anomalies (not attached to specific days)
                    $periodLevelTypes = [
                        'excessive_absences',
                        'high_absences',
                        'low_work_hours',
                        'consecutive_absences',
                        'frequent_late_arrivals'
                    ];
                    $periodLevelAnomalies = $anomalies->whereIn('type', $periodLevelTypes);

                    if ($periodLevelAnomalies->isNotEmpty()) {
                        $this->anomalyDetector->savePeriodAnomalies($periodLevelAnomalies, $employee, $periodStart);
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
