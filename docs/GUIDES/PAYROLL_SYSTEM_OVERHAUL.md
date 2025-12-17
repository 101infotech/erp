# Payroll System Comprehensive Overhaul

## Current Issues

1. **Incorrect Calculation for Partial Months**: When generating payroll for Dec 1-10 for an employee with 20k NPR monthly salary, the system shows wrong data
2. **No Daily Pay Logic**: System doesn't properly calculate daily pay based on monthly salary
3. **No Variable Month Days**: Nepali calendar has 29, 30, or 31 days - system doesn't account for this
4. **No Working Hours Tracking**: System doesn't track required vs actual working hours (8 hrs/day standard)
5. **Missing Leave Policy Integration**: Paid leaves not properly considered in calculations
6. **No Advance Payment Support**: No way to record/deduct salary advances
7. **Date Display Issue**: Backend should work in AD (English dates) but UI should show both BS and AD

## New Payroll Calculation Logic

### 1. Daily Pay Calculation

For **monthly salary employees**:

```
Daily Rate = Monthly Salary / Total Days in Current Month (BS)
```

Example:

-   Monthly Salary: NPR 20,000
-   If Mangsir has 30 days: Daily Rate = 20,000 / 30 = NPR 666.67
-   If Poush has 29 days: Daily Rate = 20,000 / 29 = NPR 689.66
-   If Chaitra has 31 days: Daily Rate = 20,000 / 31 = NPR 645.16

### 2. Working Hours Logic

**Standard Configuration** (set by admin during payroll generation):

-   Standard Working Hours per Day: 8 hours
-   Total Working Days in Period: Calculated (excluding weekends)
-   Total Working Hours Required: Working Days × 8 hours

**Tracking**:

-   Total Working Hours Actual: Sum from attendance records
-   Total Working Hours Missing: Required - Actual

**Hourly Deduction**:

```
Hourly Rate = Daily Rate / Standard Working Hours per Day
Suggested Hourly Deduction = Hourly Rate × Missing Hours
```

This is **suggested** to admin who can:

-   Approve as-is
-   Edit the amount
-   Waive completely

### 3. Leave Policy Integration

**Paid Leave Days**:

-   Check employee's leave policy and remaining balance
-   If employee has paid leave balance and took approved paid leave
-   Count these days as "present" for salary calculation
-   Deduct from leave balance but NOT from salary

**Unpaid Leave Days**:

-   Deduct from salary calculation
-   Already handled in current system

### 4. Attendance-Based Calculation

**For Monthly Salary Employees** (Partial Period):

```
Period Days = Days between period_start and period_end
Days to Pay = Period Days - Absent Days (excluding paid leaves) - Weekends
Basic Salary = Daily Rate × Days to Pay
```

**Absent Day Deduction**:

```
Absent Day Deduction = Daily Rate × Absent Days (excluding paid leaves)
```

### 5. Advance Payment

-   Admin can record advance taken by employee
-   This is deducted from net salary
-   Format: { "amount": 5000, "date": "2081-08-15", "reason": "Personal emergency" }

### 6. Complete Salary Breakdown

```
1. Basic Salary (for period)
2. + Overtime Payment (manual entry by admin)
3. + Allowances Total
4. = Gross Salary

5. - Tax Amount
6. - Deductions Total
7. - Unpaid Leave Deduction
8. - Hourly Deduction (if approved by admin)
9. - Advance Payment
10. = Net Salary
```

## New Database Fields Required

| Field Name                       | Type          | Description                           |
| -------------------------------- | ------------- | ------------------------------------- |
| `month_total_days`               | integer       | Total days in the BS month (29/30/31) |
| `standard_working_hours_per_day` | decimal(4,2)  | Standard hours/day (usually 8.00)     |
| `total_working_hours_required`   | decimal(8,2)  | Expected hours for period             |
| `total_working_hours_missing`    | decimal(8,2)  | Hours short of requirement            |
| `hourly_deduction_suggested`     | decimal(10,2) | System calculated hourly deduction    |
| `hourly_deduction_amount`        | decimal(10,2) | Admin approved/edited amount          |
| `hourly_deduction_approved`      | boolean       | Whether admin approved deduction      |
| `advance_payment`                | decimal(10,2) | Advance salary taken                  |
| `advance_payment_details`        | json          | Details of advance                    |
| `paid_leave_days_used`           | integer       | Paid leave days in this period        |

## Date Handling Strategy

**Backend (Database & Logic)**:

-   All calculations use AD (English) dates
-   Store period_start and period_end in AD format (Carbon dates)
-   Keep period_start_bs and period_end_bs for display only

**Frontend (UI)**:

-   Display both BS and AD dates
-   Format: "15 Mangsir 2081 (1 Dec 2024)"
-   Date pickers show both calendars
-   Input can be either BS or AD (converted to AD before submission)

## API/Form Changes

### Payroll Generation Form

Admin must provide:

1. Employee selection (existing)
2. Period start and end dates in AD (existing)
3. **NEW**: Current month total days (29/30/31)
4. **NEW**: Standard working hours per day (default: 8)

### Payroll Edit Form

Admin can:

1. Edit overtime payment (existing)
2. Edit allowances and deductions (existing)
3. **NEW**: Approve/Edit hourly deduction
4. **NEW**: Add advance payment with details
5. **NEW**: Review paid leave usage

## Implementation Priority

1. **Phase 1 - Critical Fixes** (Immediate):

    - Fix daily rate calculation based on BS month days
    - Update basic salary calculation for partial periods
    - Add month_total_days field

2. **Phase 2 - Working Hours** (High Priority):

    - Add working hours tracking fields
    - Implement hourly deduction calculation
    - Add admin approval workflow

3. **Phase 3 - Leave Integration** (High Priority):

    - Integrate leave policy checks
    - Track paid leave usage
    - Adjust calculations accordingly

4. **Phase 4 - Advances & Polish** (Medium Priority):
    - Add advance payment functionality
    - Update all UI views
    - Improve date display (BS + AD)

## Testing Scenarios

### Test Case 1: Partial Month (Current Issue)

-   Employee: sagar@saubhagyagroup.com
-   Monthly Salary: NPR 20,000
-   Period: 1 Dec 2024 - 10 Dec 2024 (2081-08-15 to 2081-08-24 BS)
-   Mangsir 2081 has: 30 days
-   Expected Daily Rate: 20,000 / 30 = 666.67
-   Working Days: 10 days - weekends = ~7-8 days
-   Expected Basic Salary: ~666.67 × 7 = 4,666.69

### Test Case 2: Full Month with Paid Leave

-   Monthly Salary: NPR 30,000
-   Period: Full Poush 2081 (29 days)
-   Daily Rate: 30,000 / 29 = 1,034.48
-   Paid Leave: 2 days
-   Working Days: 29 - 4 Saturdays - 2 paid leave = 23 days
-   Should still get full salary (paid leave counted)

### Test Case 3: Missing Hours with Deduction

-   Monthly Salary: NPR 25,000
-   Period: Full month with 30 days
-   Daily Rate: 833.33
-   Hourly Rate: 833.33 / 8 = 104.17
-   Missing Hours: 12 hours
-   Suggested Deduction: 104.17 × 12 = 1,250.04
-   Admin can approve/edit/waive
