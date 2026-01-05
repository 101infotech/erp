# Payroll Regeneration & Holiday Handling - January 5, 2026

## Overview
This update adds support for company holidays in payroll calculations and provides a "Regenerate Payroll" feature to recalculate draft payrolls when holidays are added after initial generation.

## Problem Statement

### Issue 1: Holidays Not Affecting Payroll
When company holidays were added AFTER payroll generation, the existing payroll records still counted those days as expected work days, leading to incorrect:
- Missing hours calculations
- Hourly deductions
- Absent day counts

### Issue 2: No Way to Recalculate
There was no mechanism to regenerate payroll for draft records when:
- New holidays were added
- Attendance data was corrected
- Leave requests were approved late

### Issue 3: Hours Calculation Logic
The system needed review to ensure anomaly detection properly accounts for holidays and doesn't flag legitimate absences on holidays.

## Solution Implemented

### 1. Holiday-Aware Payroll Calculation

#### Files Modified
- [app/Services/PayrollCalculationService.php](../app/Services/PayrollCalculationService.php)

#### Changes Made

**Added `countHolidays()` Method:**
```php
protected function countHolidays(Carbon $start, Carbon $end): int
{
    return \App\Models\Holiday::where('is_active', true)
        ->where('is_company_wide', true)
        ->whereBetween('date', [$start, $end])
        ->count();
}
```

**Updated `getAttendanceData()` Method:**
```php
// OLD: Only weekends excluded
$expectedWorkDays = $totalDays - $weekends;

// NEW: Weekends AND holidays excluded
$holidays = $this->countHolidays($periodStart, $periodEnd);
$expectedWorkDays = $totalDays - $weekends - $holidays;
```

**Added to Return Data:**
```php
return [
    // ...existing fields
    'holiday_days' => $holidays,
    'weekend_days' => $this->countWeekends($periodStart, $periodEnd),
];
```

### 2. Regenerate Payroll Feature

#### Controller Method Added
**File:** [app/Http/Controllers/Admin/HrmPayrollController.php](../app/Http/Controllers/Admin/HrmPayrollController.php)

**New Method:** `regenerate($id)`

**Features:**
- Only works on DRAFT payrolls (approved/paid are locked)
- Recalculates with latest data:
  - Updated attendance records
  - New holidays
  - Recent leave approvals
- Preserves manual edits:
  - Overtime payment
  - Hourly deduction amount
  - Advance payment
- Resets anomaly review status
- Deletes old PDF (will be regenerated on download)

**Process:**
1. Validates payroll is in draft status
2. Converts BS dates back to AD
3. Calls `PayrollCalculationService::calculatePayroll()`
4. Updates payroll record with recalculated values
5. Preserves manual admin edits
6. Recalculates net salary
7. Logs the regeneration

### 3. UI Changes

#### Payroll Show Page
**File:** [resources/views/admin/hrm/payroll/show.blade.php](../resources/views/admin/hrm/payroll/show.blade.php)

**Added:**
- "Regenerate" button (purple) for draft payrolls
- Regeneration modal with detailed explanation
- Icon: Refresh/reload symbol

**Button Placement:**
```
Draft Status: [Edit] [Regenerate] [Approve] [Delete]
```

**Modal Information:**
- What will be recalculated
- What will be preserved
- Warning that anomaly review resets
- Confirmation required

### 4. Routing

**File:** [routes/web.php](../routes/web.php)

**Added Route:**
```php
Route::post('payroll/{payroll}/regenerate', [HrmPayrollController::class, 'regenerate'])
    ->name('payroll.regenerate');
```

## How It Works

### Scenario 1: Holiday Added After Payroll Generation

**Before:**
1. Admin generates payroll for Jan 10 - Feb 9
2. Expected work days: 29 - 4 weekends = 25 days
3. Employee worked 23 days
4. System marks 2 days as absent

**Then:**
- HR adds a company holiday on Jan 26 (Republic Day)

**After Regeneration:**
1. Admin clicks "Regenerate" on draft payroll
2. Expected work days: 29 - 4 weekends - 1 holiday = 24 days  
3. Employee worked 23 days
4. System marks 1 day as absent (correct!)

### Scenario 2: Preserving Manual Edits

**Workflow:**
1. Admin generates payroll (draft)
2. Reviews and edits overtime payment from NPR 0 to NPR 5,000
3. Adds NPR 10,000 advance payment deduction
4. Later adds a holiday
5. Clicks "Regenerate"
6. **Result:** New calculations apply, BUT:
   - Overtime payment remains NPR 5,000
   - Advance payment remains NPR 10,000
   - Only base salary, hours, and auto-calculations update

## Anomaly Detection Enhancement

The existing anomaly detection system (`PayrollAnomalyDetector`) already has sophisticated logic to detect:

### Current Anomalies Tracked:
1. **Day-Level Anomalies:**
   - Missing clock-out (HIGH severity)
   - Excessive hours >16hrs (HIGH severity)  
   - Weekend work without OT (MEDIUM severity)
   - Location inconsistencies (LOW severity)
   - Duplicate entries (MEDIUM severity)
   - Negative time (HIGH severity)

2. **Period-Level Anomalies:**
   - Excessive absences >40% (HIGH severity)
   - High absences >25% (MEDIUM severity)
   - Low work hours <50% (HIGH severity)
   - Consecutive absences ≥5 days (HIGH severity)
   - Frequent late arrivals ≥8 times (MEDIUM severity)

### Holiday Impact on Anomaly Detection

**Already Handled:**
- Anomaly detector uses attendance records directly
- Doesn't flag missing attendance on holidays (no attendance record exists)
- Won't create "consecutive absence" anomalies spanning holidays

**Why It Works:**
```php
// Anomaly detector looks at actual attendance records
$attendanceRecords = HrmAttendanceDay::where('employee_id', $employee->id)
    ->whereBetween('date', [$startDate, $endDate])
    ->get();

// Holidays have NO attendance record, so they're not counted as:
// - Absent days
// - Missing hours
// - Consecutive absences (breaks the streak)
```

## Testing

### Test Case 1: Generate Payroll Without Holiday
```
Period: 2082-09-21 to 2082-10-20 (30 days)
Weekends: 4 Saturdays
Holidays: 0
Expected Work Days: 30 - 4 = 26 days
```

### Test Case 2: Add Holiday and Regenerate
```
1. Add holiday on 2082-09-26
2. Click "Regenerate" on draft payroll
3. Expected Work Days: 30 - 4 - 1 = 25 days ✓
4. Hours calculation updated ✓
5. Manual OT payment preserved ✓
```

### Test Case 3: Cannot Regenerate Approved Payroll
```
1. Approve a payroll
2. Try to click "Regenerate"
3. Button not visible ✓
4. Direct API call returns error ✓
```

## Database Fields

### Updated Fields in `hrm_payroll_records`:

**Fields That Get Recalculated:**
- `total_hours_worked`
- `total_working_hours_required`
- `total_working_hours_missing`
- `total_days_worked`
- `overtime_hours`
- `absent_days`
- `unpaid_leave_days`
- `paid_leave_days_used`
- `total_payable_days`
- `weekend_days`
- `holiday_days` ← NEW
- `basic_salary`
- `gross_salary`
- `tax_amount`
- `deductions_total`
- `unpaid_leave_deduction`
- `suggested_hourly_deduction`
- `net_salary` (recalculated with preserved manual edits)
- `anomalies`
- `anomalies_reviewed` (reset to false)

**Fields That Are Preserved:**
- `overtime_payment` (manual edit)
- `hourly_deduction` (manual edit)
- `advance_payment` (manual edit)
- `advance_payment_details` (manual edit)

## User Guide

### For HR Admins

**When to Regenerate:**
- After adding/editing company holidays
- When attendance data is corrected in Jibble
- When leave requests are approved late
- To get latest calculated values

**How to Regenerate:**
1. Go to Payroll Management
2. Click on a DRAFT payroll
3. Click purple "Regenerate" button
4. Review the modal explanation
5. Click "Regenerate Payroll"
6. Review updated values
7. Proceed with edit/approve as normal

**What Gets Updated:**
- Attendance hours
- Holiday count
- Expected work days
- Missing hours
- Deductions
- Anomalies

**What Stays the Same:**
- Your manual OT payments
- Your approved hourly deductions
- Your advance payments

### For Employees

**Impact:**
- More accurate payroll when holidays are properly recorded
- Hours not deducted for company holidays
- Anomalies won't flag holiday absences

## Benefits

1. **Accuracy:** Payroll now correctly accounts for company holidays
2. **Flexibility:** Can recalculate draft payrolls as needed
3. **Safety:** Approved/paid payrolls remain locked
4. **Preservation:** Manual admin decisions are kept during regeneration
5. **Transparency:** Clear modal explains what happens
6. **Audit Trail:** Regeneration is logged

## API Changes

### New Endpoint

**POST** `/admin/hrm/payroll/{payroll}/regenerate`

**Authorization:** Admin only

**Request:** None (just payroll ID in URL)

**Response:**
```json
{
  "success": true,
  "message": "Payroll regenerated successfully with latest data including holidays. Please review before approval."
}
```

**Errors:**
- 403: Only draft payrolls can be regenerated
- 500: Calculation error

## Future Enhancements

### Potential Improvements:
1. **Bulk Regenerate:** Allow regenerating multiple draft payrolls at once
2. **Holiday Templates:** Predefined holiday calendars by year
3. **Department Holidays:** Support for department-specific holidays
4. **Notification:** Alert admins when holidays affect existing drafts
5. **Comparison View:** Show before/after values during regeneration
6. **Version History:** Track all regenerations with timestamps

## Related Documentation

- [Payroll Calculation Logic](PAYROLL_CALCULATION_FIX.md)
- [Hourly Deduction Fix](HOURLY_DEDUCTION_FIX.md)
- [HRM Implementation Guide](../GUIDES/HRM_IMPLEMENTATION_FINAL_SUMMARY.md)
- [Anomaly Detection System](../app/Services/PayrollAnomalyDetector.php)

## Status

✅ **Complete and Tested**

**Date:** January 5, 2026  
**Author:** AI Assistant  
**Version:** 1.0
