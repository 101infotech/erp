<?php

namespace App\Services;

use App\Models\HrmEmployee;
use App\Models\HrmLeavePolicy;
use App\Models\HrmLeaveRequest;
use Illuminate\Support\Facades\DB;

class LeavePolicyService
{
    /**
     * Apply leave policies to an employee
     */
    public function applyPoliciesToEmployee(HrmEmployee $employee): void
    {
        $policies = HrmLeavePolicy::where('company_id', $employee->company_id)
            ->where('is_active', true)
            ->get();

        foreach ($policies as $policy) {
            // Check gender restriction
            if ($policy->gender_restriction !== 'none') {
                if ($employee->gender !== $policy->gender_restriction) {
                    continue; // Skip this policy if gender doesn't match
                }
            }

            $balanceField = $this->getBalanceField($policy->leave_type);

            if ($balanceField && $employee->{$balanceField} === null) {
                $employee->{$balanceField} = $policy->annual_quota;
            }
        }

        $employee->save();
    }

    /**
     * Apply leave policies to all employees in a company
     */
    public function applyPoliciesToCompany(int $companyId): int
    {
        $policies = HrmLeavePolicy::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        if ($policies->isEmpty()) {
            return 0;
        }

        $employees = HrmEmployee::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $updated = 0;

        foreach ($employees as $employee) {
            foreach ($policies as $policy) {
                // Check gender restriction
                if ($policy->gender_restriction !== 'none') {
                    if ($employee->gender !== $policy->gender_restriction) {
                        continue; // Skip this policy if gender doesn't match
                    }
                }

                $balanceField = $this->getBalanceField($policy->leave_type);

                if ($balanceField) {
                    $employee->{$balanceField} = $policy->annual_quota;
                    $updated++;
                }
            }
            $employee->save();
        }

        return $updated;
    }

    /**
     * Get policy for a specific leave type and company
     */
    public function getPolicy(int $companyId, string $leaveType): ?HrmLeavePolicy
    {
        return HrmLeavePolicy::where('company_id', $companyId)
            ->where('leave_type', $leaveType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get effective quota for employee
     * Uses individual employee quota if set, otherwise falls back to company policy
     * 
     * @param HrmEmployee $employee
     * @param string $leaveType
     * @return float The effective quota (employee-specific or company-wide)
     */
    public function getEffectiveQuota(HrmEmployee $employee, string $leaveType): float
    {
        // Map leave type to employee quota field
        $quotaFields = [
            'annual' => 'paid_leave_annual',
            'sick' => 'paid_leave_sick',
            'casual' => 'paid_leave_casual',
        ];

        $quotaField = $quotaFields[$leaveType] ?? null;

        // Check if employee has individual quota set
        if ($quotaField && $employee->{$quotaField} !== null && $employee->{$quotaField} > 0) {
            return (float) $employee->{$quotaField};
        }

        // Fall back to company policy
        $policy = $this->getPolicy($employee->company_id, $leaveType);

        if (!$policy) {
            return 0;
        }

        // Check gender restriction on company policy
        if ($policy->gender_restriction !== 'none' && $employee->gender !== $policy->gender_restriction) {
            return 0;
        }

        return (float) $policy->annual_quota;
    }

    /**
     * Validate leave request against policy
     * Uses employee-specific quota if defined, otherwise company policy
     */
    public function validateLeaveRequest(HrmEmployee $employee, string $leaveType, float $days): array
    {
        // Get effective quota (employee-specific or company-wide)
        $effectiveQuota = $this->getEffectiveQuota($employee, $leaveType);

        if ($effectiveQuota <= 0) {
            return [
                'valid' => false,
                'message' => 'No leave quota available for this leave type.'
            ];
        }

        $balanceField = $this->getBalanceField($leaveType);
        $balance = $balanceField ? ($employee->{$balanceField} ?? 0) : 0;

        // Get used days this year
        $usedDays = HrmLeaveRequest::where('employee_id', $employee->id)
            ->where('leave_type', $leaveType)
            ->whereIn('status', ['approved', 'pending'])
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        $availableDays = $balance - $usedDays;

        if ($days > $availableDays) {
            return [
                'valid' => false,
                'message' => "Insufficient leave balance. Available: {$availableDays} days, Requested: {$days} days."
            ];
        }

        $policy = $this->getPolicy($employee->company_id, $leaveType);

        return [
            'valid' => true,
            'policy' => $policy,
            'quota' => $effectiveQuota,
            'balance' => $balance,
            'used' => $usedDays,
            'available' => $availableDays
        ];
    }

    /**
     * Get leave balance summary for an employee
     * Uses employee-specific quota if defined, otherwise company policy
     */
    public function getLeaveBalanceSummary(HrmEmployee $employee): array
    {
        $summary = [];
        $leaveTypes = ['annual', 'sick', 'casual', 'period'];

        foreach ($leaveTypes as $type) {
            // Get effective quota (employee-specific or company-wide)
            $effectiveQuota = $this->getEffectiveQuota($employee, $type);

            // Skip if no quota available
            if ($effectiveQuota <= 0) {
                continue;
            }

            $balanceField = $this->getBalanceField($type);
            $balance = $balanceField ? ($employee->{$balanceField} ?? 0) : 0;

            $usedDays = HrmLeaveRequest::where('employee_id', $employee->id)
                ->where('leave_type', $type)
                ->where('status', 'approved')
                ->whereYear('start_date', now()->year)
                ->sum('total_days');

            $policy = $this->getPolicy($employee->company_id, $type);

            // Check if using individual quota
            $quotaFields = [
                'annual' => 'paid_leave_annual',
                'sick' => 'paid_leave_sick',
                'casual' => 'paid_leave_casual',
            ];
            $quotaField = $quotaFields[$type] ?? null;
            $hasIndividualQuota = $quotaField && $employee->{$quotaField} !== null && $employee->{$quotaField} > 0;

            $summary[$type] = [
                'quota' => $effectiveQuota,
                'balance' => $balance,
                'used' => $usedDays,
                'available' => $balance - $usedDays,
                'policy' => $policy,
                'source' => $hasIndividualQuota ? 'individual' : 'company'
            ];
        }

        return $summary;
    }

    /**
     * Get balance field name for leave type
     */
    private function getBalanceField(string $leaveType): ?string
    {
        $fields = [
            'annual' => 'annual_leave_balance',
            'sick' => 'sick_leave_balance',
            'casual' => 'casual_leave_balance',
            'period' => 'period_leave_balance',
        ];

        return $fields[$leaveType] ?? null;
    }

    /**
     * Reset annual leave balances based on policies
     */
    public function resetAnnualLeaveBalances(int $companyId): int
    {
        $policies = HrmLeavePolicy::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $employees = HrmEmployee::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $updated = 0;

        foreach ($employees as $employee) {
            foreach ($policies as $policy) {
                $balanceField = $this->getBalanceField($policy->leave_type);

                if ($balanceField) {
                    $carryForward = 0;

                    // Calculate carry forward if allowed
                    if ($policy->carry_forward_allowed) {
                        $usedDays = HrmLeaveRequest::where('employee_id', $employee->id)
                            ->where('leave_type', $policy->leave_type)
                            ->where('status', 'approved')
                            ->whereYear('start_date', now()->subYear()->year)
                            ->sum('total_days');

                        $remaining = ($employee->{$balanceField} ?? 0) - $usedDays;
                        $carryForward = min($remaining, $policy->max_carry_forward);
                    }

                    $employee->{$balanceField} = $policy->annual_quota + $carryForward;
                    $updated++;
                }
            }
            $employee->save();
        }

        return $updated;
    }
}
