# Half-Day Leave Support - January 5, 2026

## Overview
The system **fully supports half-day leave requests** for all leave types (annual, sick, casual, unpaid). This document explains how it works and the fix applied to ensure payroll calculations handle half-day leaves correctly.

## Database Structure

### Leave Request Table (`hrm_leave_requests`)
```php
$table->decimal('total_days', 3, 1)->comment('0.5 for half day');
```

**Field Details:**
- **Type**: `decimal(3, 1)` - Allows one decimal place
- **Examples**:
  - Full day: `1.0`
  - Half day: `0.5`
  - 2.5 days: `2.5` (2 full days + 1 half day)
  - Maximum: `99.9` days

## How Half-Day Leaves Work

### 1. Creating Half-Day Leave Request

When an employee or admin creates a leave request:

```php
HrmLeaveRequest::create([
    'employee_id' => $employeeId,
    'leave_type' => 'casual', // or 'annual', 'sick', 'unpaid'
    'start_date' => '2082-09-15',
    'end_date' => '2082-09-15', // Same day for half-day
    'total_days' => 0.5, // Half day
    'reason' => 'Doctor appointment',
    'status' => 'pending'
]);
```

### 2. Leave Balance Deduction

When a half-day leave is approved:
- **Annual leave balance**: Deducted by 0.5
- **Sick leave balance**: Deducted by 0.5
- **Casual leave balance**: Deducted by 0.5

### 3. Payroll Calculation

The `total_days` field is used directly in payroll calculations:

#### Paid Leave (Annual, Sick, Casual)
```php
// Correct implementation (after fix)
$totalPaidLeaveDays = $paidLeaveRequests->sum('total_days');
// Example: 2 full days + 1 half day = 2.0 + 0.5 = 2.5 days
```

#### Unpaid Leave
```php
// Already correct
$unpaidLeaveDays = HrmLeaveRequest::where('employee_id', $employee->id)
    ->where('leave_type', 'unpaid')
    ->where('status', 'approved')
    ->sum('total_days');
// Example: 1 full day + 1 half day = 1.0 + 0.5 = 1.5 days
```

## Bug Fix Applied

### Problem Found
The original `getPaidLeaveData()` method was manually counting calendar days instead of using the `total_days` field:

```php
// WRONG - Counted calendar days, ignored total_days field
foreach ($paidLeaveRequests as $leave) {
    $leaveStart = max($leave->start_date, $periodStart);
    $leaveEnd = min($leave->end_date, $periodEnd);
    // ... manual day counting loop ...
    $totalPaidLeaveDays += $paidDaysCount; // Always whole numbers
}
```

**Issue**: This approach couldn't handle half-day leaves because it counted calendar days (always integers).

### Solution Implemented
```php
// CORRECT - Uses the total_days field directly
$totalPaidLeaveDays = $paidLeaveRequests->sum('total_days');
```

**Benefits**:
- ✅ Automatically handles half-day leaves (0.5)
- ✅ Handles multi-day leaves with half days (2.5, 3.5, etc.)
- ✅ Simpler, more reliable code
- ✅ Uses the authoritative source (the `total_days` field set when leave was created)

## Payroll Impact Examples

### Example 1: Half-Day Sick Leave
**Scenario:**
- Period: 29 days
- Days worked: 20
- Paid leave: 0.5 days (half-day sick leave)
- Weekends: 4
- Holidays: 0

**Calculation:**
```
Total Payable Days = 20 (worked) + 0.5 (paid leave) + 4 (weekends) = 24.5 days
Base Salary = (Monthly Salary / 29) × 24.5
```

### Example 2: Multiple Half-Days
**Scenario:**
- Period: 30 days
- Days worked: 22
- Paid leave: 2.5 days (2 full days + 1 half day)
- Weekends: 4
- Holidays: 1

**Calculation:**
```
Total Payable Days = 22 (worked) + 2.5 (paid leave) + 4 (weekends) + 1 (holiday) = 29.5 days
Base Salary = (Monthly Salary / 30) × 29.5
```

### Example 3: Half-Day Unpaid Leave
**Scenario:**
- Period: 30 days
- Days worked: 23
- Unpaid leave: 0.5 days
- Weekends: 4
- Holidays: 0

**Calculation:**
```
Total Payable Days = 23 (worked) + 4 (weekends) = 27 days
Deduction for 0.5 unpaid leave = (Monthly Salary / 30) × 0.5
```

## UI Display

The payroll detail page shows leave days with decimal precision:

```blade
@if($paidLeaveDays > 0)
<div class="flex justify-between">
    <span class="text-slate-400">Paid Leave Days:</span>
    <span class="text-blue-400">{{ $paidLeaveDays }}</span>
</div>
@endif
```

**Display Examples:**
- `0.5` - Half day
- `1.0` - Full day
- `2.5` - Two and a half days

## Leave Request Form Guidelines

### For Half-Day Leaves

**Best Practice:**
1. Set `start_date` = `end_date` (same day)
2. Set `total_days` = `0.5`
3. Specify in reason: "Half day - morning" or "Half day - afternoon"

**Example:**
```
Leave Type: Casual
Start Date: 2082-09-15
End Date: 2082-09-15
Total Days: 0.5
Reason: Doctor appointment (afternoon half day)
```

### For Multi-Day with Half Days

**Example 1**: 2.5 days (Mon full, Tue full, Wed half)
```
Start Date: 2082-09-15 (Monday)
End Date: 2082-09-17 (Wednesday)
Total Days: 2.5
Reason: Medical treatment
```

## Validation Rules

### Recommended Validation
```php
// In LeaveRequest validation
'total_days' => [
    'required',
    'numeric',
    'min:0.5',
    'max:99.9',
    'regex:/^\d+(\.[05])?$/', // Only .0 or .5 decimals
]
```

This ensures:
- Minimum: 0.5 (half day)
- Maximum: 99.9 days
- Only .0 or .5 decimal values (no 0.3, 0.7, etc.)

## System Compatibility

### ✅ Features That Support Half-Days

1. **Leave Balance Tracking**
   - Deducts exact amount (0.5, 1.0, 2.5, etc.)
   
2. **Payroll Calculation**
   - Paid leave: Uses `total_days` directly
   - Unpaid leave: Uses `total_days` for deduction
   
3. **Leave Reports**
   - Shows decimal values accurately
   
4. **Leave Balance Display**
   - Can show "4.5 days remaining"

### ⚠️ Things to Consider

1. **Attendance Tracking**
   - If using Jibble/attendance system, ensure half-day leaves are marked appropriately
   - Half-day leave + half-day work should = 1 full day for attendance purposes

2. **Leave Policy Balance**
   - Ensure leave policies can handle decimal balances
   - Example: 10.5 days annual leave

3. **UI Forms**
   - Leave request forms should support decimal input
   - Consider dropdown or radio buttons: "Full Day" / "Half Day"

## Testing Scenarios

### Test Case 1: Single Half-Day Paid Leave
```php
// Create half-day casual leave
$leave = HrmLeaveRequest::create([
    'employee_id' => $employee->id,
    'leave_type' => 'casual',
    'start_date' => '2082-09-15',
    'end_date' => '2082-09-15',
    'total_days' => 0.5,
    'status' => 'approved'
]);

// Generate payroll
// Expected: paid_leave_days_used = 0.5
```

### Test Case 2: Mixed Full and Half Days
```php
// Create 2 leaves: 1 full day + 1 half day
$leave1 = HrmLeaveRequest::create([
    'total_days' => 1.0, // Full day
    'status' => 'approved'
]);
$leave2 = HrmLeaveRequest::create([
    'total_days' => 0.5, // Half day
    'status' => 'approved'
]);

// Generate payroll
// Expected: paid_leave_days_used = 1.5
```

### Test Case 3: Half-Day Unpaid Leave
```php
// Create half-day unpaid leave
$leave = HrmLeaveRequest::create([
    'leave_type' => 'unpaid',
    'total_days' => 0.5,
    'status' => 'approved'
]);

// Generate payroll
// Expected: unpaid_leave_days = 0.5
// Expected: Deduction = (monthly_salary / month_days) × 0.5
```

## Benefits of Half-Day Leave Support

1. **Flexibility**: Employees can take short leaves without using full days
2. **Accuracy**: Payroll reflects actual leave taken
3. **Leave Balance**: Preserves leave days for employees
4. **Fair Deduction**: Unpaid half-days deduct proportionally
5. **Professional**: Common feature in modern HR systems

## Related Files

- **Model**: [app/Models/HrmLeaveRequest.php](../../app/Models/HrmLeaveRequest.php)
- **Service**: [app/Services/PayrollCalculationService.php](../../app/Services/PayrollCalculationService.php)
- **Migration**: [database/migrations/2025_12_03_141443_create_hrm_leave_requests_table.php](../../database/migrations/2025_12_03_141443_create_hrm_leave_requests_table.php)

## Status

✅ **Fully Supported and Fixed**

- Database supports decimal days (0.5)
- Unpaid leave calculation already correct
- Paid leave calculation fixed to use `total_days`
- Payroll displays decimal values properly

---

**Fixed Date:** January 5, 2026  
**Status:** Production Ready
