# Payroll Hourly Calculation Logic Fix

## Issue Identified

The hourly deduction calculation was using incorrect logic for calculating required working hours:

-   **OLD LOGIC**: Required Hours = (Expected Work Days - Leave Days) × 8
-   **PROBLEM**: This approach was confusing and double-counted absences

## Root Cause

The calculation was based on "expected work days minus leaves" instead of using the already-calculated "Total Payable Days". This caused:

1. Incorrect required hours (e.g., showing 200 hrs instead of 104 hrs)
2. Confusion about how leaves are handled
3. Double-subtraction of absences

## Solution Implemented

Changed calculation basis to use **Total Payable Days**:

-   **NEW LOGIC**: Required Hours = Total Payable Days × 8
-   **RATIONALE**: If we're paying for X days, we should expect X × 8 hours of work

### Why This Makes Sense

-   Total Payable Days already accounts for:
    -   Days worked
    -   Paid leaves
    -   Weekends (Saturdays)
    -   Verbal/informal leaves (when added)
-   Total Payable Days excludes:
    -   Absent days
    -   Unpaid leave days
-   Therefore: **Payment aligns with expectation**

## Example

For Sagar Chhetri (period 2025-01-10 to 2025-02-09):

-   Total Payable Days: 13
-   **Required Hours: 13 × 8 = 104 hrs** ✓
-   Worked Hours: 69.03 hrs
-   Missing Hours: 34.97 hrs

### Old vs New Comparison

| Metric            | Old Logic                    | New Logic                |
| ----------------- | ---------------------------- | ------------------------ |
| Calculation Basis | Expected Work Days - Leaves  | Total Payable Days       |
| Required Hours    | 200 hrs (WRONG)              | 104 hrs (CORRECT)        |
| Formula           | (25 work days - leaves) × 8  | 13 payable days × 8      |
| Complexity        | High (multiple subtractions) | Low (one multiplication) |

## Code Changes

### 1. PayrollCalculationService.php

#### Method: `calculateWorkingHoursMetrics()`

**Before:**

```php
protected function calculateWorkingHoursMetrics(
    $attendanceData, $paidLeaveData, $standardWorkingHours, $dailyRate
): array {
    $expectedWorkDays = $attendanceData['expected_work_days'];
    $paidLeaveHours = $paidLeaveData['paid_leave_days'] * $standardWorkingHours;
    $unpaidLeaveHours = $attendanceData['unpaid_leave_days'] * $standardWorkingHours;
    $totalLeaveHours = $paidLeaveHours + $unpaidLeaveHours;
    $requiredHours = $expectedWorkDays * $standardWorkingHours;
    $adjustedRequiredHours = max(0, $requiredHours - $totalLeaveHours);
    // ... complex calculation
}
```

**After:**

```php
protected function calculateWorkingHoursMetrics(
    $attendanceData,
    $paidLeaveData,
    $standardWorkingHours,
    $dailyRate,
    int $totalPayableDays  // NEW PARAMETER
): array {
    // IMPORTANT: Required hours = Payable Days × Standard Hours
    // Payable days already account for work days, paid leave, and weekends
    $requiredHours = $totalPayableDays * $standardWorkingHours;
    $missingHours = max(0, $requiredHours - $attendanceData['total_hours']);
    // ... simple deduction calculation
}
```

**Key Changes:**

-   Added `$totalPayableDays` parameter
-   Removed complex leave subtraction logic
-   Simplified to single multiplication: `$totalPayableDays * $standardWorkingHours`

### 2. HrmPayrollController.php

#### Method: `update()` - Verbal Leave Recalculation

**Before:**

```php
// Calculate expected work days and subtract leaves
$expectedWorkDays = $totalDays - $weekends;
$requiredHours = $expectedWorkDays * $standardWorkingHours;
$adjustedRequiredHours = max(0, $requiredHours - $totalLeaveHours);
```

**After:**

```php
// Recalculate total payable days including verbal leave
$totalPayableDays = $payroll->total_days_worked
    + ($payroll->paid_leave_days_used ?? 0)
    + $verbalLeaveDays
    + $weekends;

$payroll->total_payable_days = $totalPayableDays;

// Required hours = Payable Days × 8
$requiredHours = $totalPayableDays * $payroll->standard_working_hours_per_day;
```

**Key Changes:**

-   Recalculate `total_payable_days` when verbal leave changes
-   Update database field for consistency
-   Use payable days for required hours calculation

## Benefits

1. **Accuracy**: Calculation now matches payment logic
2. **Simplicity**: One multiplication instead of multiple subtractions
3. **Clarity**: Easy to understand: "Paying for 13 days? Expect 104 hours."
4. **Consistency**: Same logic used throughout the system
5. **Maintainability**: Less code, fewer edge cases

## Verbal Leave Integration

When verbal/informal leave days are added:

1. Total Payable Days increases
2. Required Hours increases proportionally
3. Missing Hours recalculates automatically
4. Hourly deduction adjusts accordingly

**Example:**

-   Original: 13 payable days → 104 required hrs
-   Add 2 verbal leave days: 15 payable days → 120 required hrs
-   More days paid = more hours expected ✓

## Testing

To verify the fix works:

```bash
php artisan tinker
```

```php
$employee = \App\Models\HrmEmployee::where('name', 'LIKE', '%Sagar%')->first();
$periodStart = \Carbon\Carbon::parse('2025-01-10');
$periodEnd = \Carbon\Carbon::parse('2025-02-09');
$calendarService = app(\App\Services\NepalCalendarService::class);
$periodStartBs = $calendarService->adToBs($periodStart);
$periodEndBs = $calendarService->adToBs($periodEnd);
$service = app(\App\Services\PayrollCalculationService::class);
$data = $service->calculatePayroll($employee, $periodStart, $periodEnd, $periodStartBs, $periodEndBs, 0, null);

echo "Payable Days: {$data['total_payable_days']}\n";
echo "Required Hours: {$data['total_working_hours_required']}\n";
echo "Verification: " . ($data['total_payable_days'] * 8) . " hrs\n";
// Should match!
```

## Status

✅ **FIXED** - Calculation now correctly uses Total Payable Days × 8

## Related Files

-   [app/Services/PayrollCalculationService.php](../app/Services/PayrollCalculationService.php)
-   [app/Http/Controllers/Admin/HrmPayrollController.php](../app/Http/Controllers/Admin/HrmPayrollController.php)
-   [resources/views/admin/hrm/payroll/show.blade.php](../resources/views/admin/hrm/payroll/show.blade.php)
-   [resources/views/admin/hrm/payroll/edit.blade.php](../resources/views/admin/hrm/payroll/edit.blade.php)

## Notes

-   Jibble attendance system provides the base data for payable days calculation
-   Leaves are accounted for when staff doesn't clock in
-   Weekends (Saturdays) are always included in payable days
-   The formula is now aligned: **Payment = Expectation**
