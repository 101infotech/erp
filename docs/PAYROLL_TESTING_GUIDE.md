# Payroll System - Testing Guide

## ✅ Implementation Status: COMPLETE

All backend logic and frontend views have been successfully implemented and are ready for testing.

## Test Case 1: Sagar's Partial Month Payroll (Dec 1-10)

### Setup

**Employee**: sagar@saubhagyagroup.com  
**Monthly Salary**: NPR 20,000  
**Period**: December 1-10, 2024 (2081-08-15 to 2081-08-24 BS)  
**Expected Outcome**: Correct pro-rated salary based on actual BS month days

### Steps to Test

1. **Navigate to Payroll Generation**

    - Go to Admin Panel → HRM → Payroll → Generate Payroll
    - URL: `/admin/hrm/payroll/create`

2. **Select Employee**

    - Find and check: Sagar Chhetri (sagar@saubhagyagroup.com)

3. **Set Period**

    - Start Date (AD): 2024-12-01
    - End Date (AD): 2024-12-10

4. **Configure Payroll Settings**

    - Month Total Days: Leave empty (auto-detect) OR enter 30 (Mangsir 2081)
    - Standard Working Hours: 8.00 (default)

5. **Generate Payroll**

    - Click "Generate Payroll"
    - System will create draft payroll

6. **Verify Calculations**

#### Expected Results:

```
Month: Mangsir 2081 (30 days)
Daily Rate: 20,000 ÷ 30 = NPR 666.67

Period: Dec 1-10 (10 calendar days)
Weekends (Saturdays): ~1-2 days
Working Days: ~7-8 days

If Sagar worked 7 days:
Basic Salary: 666.67 × 7 = NPR 4,666.69

If Sagar worked 8 days:
Basic Salary: 666.67 × 8 = NPR 5,333.36

Working Hours:
- Required: 7 days × 8 hrs = 56 hours (or 8 days × 8 = 64 hours)
- Actual: (from attendance records)
- Missing: Required - Actual
- Suggested Deduction: (666.67 ÷ 8) × Missing Hours
```

### Verification Checklist

-   [ ] Daily rate is NPR 666.67 (20,000 ÷ 30)
-   [ ] Basic salary is proportional to days worked
-   [ ] Working hours are tracked correctly
-   [ ] Missing hours calculation is accurate
-   [ ] Suggested hourly deduction is displayed
-   [ ] Paid leave days (if any) are counted as worked
-   [ ] Net salary calculation includes all components

## Test Case 2: Full Month with Paid Leave

### Setup

**Employee**: Any employee with paid leave policy  
**Monthly Salary**: NPR 30,000  
**Period**: Full month (e.g., Poush 2081 - 29 days)  
**Paid Leave**: 2 days

### Expected Results:

```
Daily Rate: 30,000 ÷ 29 = NPR 1,034.48
Total Work Days: 29 - 4 Saturdays = 25 days
Days Worked: 23 days (actual)
Paid Leave Used: 2 days

Days to Pay: 23 + 2 = 25 days
Basic Salary: 1,034.48 × 25 = NPR 25,862.00

(Employee should get full salary because paid leaves are counted)
```

### Verification:

-   [ ] Paid leave days are shown separately
-   [ ] Basic salary includes paid leave days
-   [ ] No deduction for paid leave days
-   [ ] Working hours requirement accounts for paid leaves

## Test Case 3: Missing Hours with Deduction

### Setup

**Employee**: Any monthly employee  
**Monthly Salary**: NPR 25,000  
**Period**: Full month (30 days)  
**Missing Hours**: 12 hours

### Expected Results:

```
Daily Rate: 25,000 ÷ 30 = NPR 833.33
Hourly Rate: 833.33 ÷ 8 = NPR 104.17
Suggested Deduction: 104.17 × 12 = NPR 1,250.04
```

### Test Admin Actions:

1. **Approve as-is**: Net salary reduced by NPR 1,250.04
2. **Edit amount**: Admin enters NPR 1,000.00 instead
3. **Waive completely**: Admin unchecks approval or sets to 0

### Verification:

-   [ ] System suggests correct deduction amount
-   [ ] Admin can edit the deduction amount
-   [ ] Admin can approve or waive the deduction
-   [ ] Net salary updates correctly based on admin decision

## Test Case 4: Advance Payment

### Setup

**Employee**: Any employee  
**Advance Amount**: NPR 5,000  
**Reason**: "Personal emergency"  
**Date**: 2024-12-05

### Steps:

1. Generate/Edit payroll
2. Go to "Advance Payment" section
3. Enter advance details
4. Save payroll

### Verification:

-   [ ] Advance amount is deducted from net salary
-   [ ] Reason and date are stored and displayed
-   [ ] Details appear in payslip view
-   [ ] Audit trail shows who recorded it and when

## Test Case 5: Working Hours Scenarios

### Scenario A: No Missing Hours

-   Required: 160 hours
-   Actual: 160 hours
-   Expected: No deduction, green checkmark message

### Scenario B: Small Shortage (< 4 hours)

-   Required: 160 hours
-   Actual: 157 hours
-   Missing: 3 hours
-   Expected: Small suggested deduction (~NPR 300-400)

### Scenario C: Large Shortage (> 10 hours)

-   Required: 160 hours
-   Actual: 145 hours
-   Missing: 15 hours
-   Expected: Significant suggested deduction (~NPR 1,500-2,000)

## Test Case 6: Different BS Month Days

### Test with different months:

-   **Poush 2081**: 29 days → Daily rate: Salary ÷ 29
-   **Mangsir 2081**: 30 days → Daily rate: Salary ÷ 30
-   **Baishakh 2082**: 31 days → Daily rate: Salary ÷ 31

### Verification:

-   [ ] System auto-detects correct month days
-   [ ] Manual entry (29-32) works correctly
-   [ ] Daily rate changes based on month days
-   [ ] Error shown if invalid value (< 29 or > 32)

## UI/UX Testing

### Create Form

-   [ ] Month total days field is optional with helpful text
-   [ ] Standard working hours defaults to 8.00
-   [ ] Info box explains the new features
-   [ ] Both BS and AD dates are mentioned
-   [ ] Form validation works correctly

### Edit Form

-   [ ] Working hours section shows clearly
-   [ ] Suggested deduction is highlighted
-   [ ] Admin can edit deduction amount
-   [ ] Approval checkbox works
-   [ ] Advance payment section is easy to use
-   [ ] All existing features still work (OT, allowances, deductions, tax)

### Show/Details Page

-   [ ] Working hours metrics are displayed
-   [ ] Paid leave days shown in attendance summary
-   [ ] Hourly deduction appears in salary breakdown
-   [ ] Advance payment shows with reason
-   [ ] All calculations add up correctly
-   [ ] BS dates are shown in readable format

## Edge Cases to Test

### 1. Zero Hours Worked

-   Employee didn't work at all
-   Expected: Basic salary should be minimal or zero

### 2. Overtime Only

-   Employee worked overtime but no regular hours
-   Expected: OT payment processed, hourly deduction calculated

### 3. All Paid Leave

-   Employee on full paid leave for entire period
-   Expected: Full salary paid, no deductions

### 4. Mixed Leave Types

-   Some paid leave, some unpaid leave
-   Expected: Paid leave counted, unpaid leave deducted

### 5. Partial Period in Different Months

-   Period spans two BS months (e.g., last week of Poush + first week of Magh)
-   Expected: Uses start date's month for calculation

## Performance Testing

### Large Batch Generation

1. Select 50+ employees
2. Generate payroll for all
3. Monitor processing time
4. Check all calculations are correct

### Expected:

-   [ ] Bulk generation completes successfully
-   [ ] All payrolls created in draft status
-   [ ] No timeouts or errors
-   [ ] Calculations consistent across all employees

## Data Integrity Testing

### 1. Verify Database Records

```sql
SELECT
    employee_id,
    month_total_days,
    standard_working_hours_per_day,
    total_working_hours_required,
    total_working_hours_missing,
    hourly_deduction_suggested,
    hourly_deduction_amount,
    hourly_deduction_approved,
    paid_leave_days_used,
    advance_payment,
    basic_salary,
    net_salary
FROM hrm_payroll_records
WHERE period_start_bs = '2081-08-15'
ORDER BY employee_id;
```

### 2. Verify Calculations

```sql
-- Check if net salary formula is correct
SELECT
    id,
    gross_salary,
    tax_amount,
    deductions_total,
    unpaid_leave_deduction,
    hourly_deduction_amount,
    advance_payment,
    net_salary,
    (gross_salary - tax_amount - deductions_total - unpaid_leave_deduction -
     CASE WHEN hourly_deduction_approved THEN hourly_deduction_amount ELSE 0 END -
     advance_payment) as calculated_net
FROM hrm_payroll_records
WHERE net_salary != (gross_salary - tax_amount - deductions_total - unpaid_leave_deduction -
                     CASE WHEN hourly_deduction_approved THEN hourly_deduction_amount ELSE 0 END -
                     advance_payment);
```

## Regression Testing

Verify existing features still work:

-   [ ] Tax calculation unchanged
-   [ ] Allowances work as before
-   [ ] Other deductions work as before
-   [ ] Overtime payment manual entry works
-   [ ] PDF generation works
-   [ ] Email sending works
-   [ ] Approval workflow works
-   [ ] Mark as paid works
-   [ ] Anomaly detection works

## Final Acceptance Criteria

### ✅ System must:

1. Calculate daily rate based on BS month days (29/30/31)
2. Pro-rate salary correctly for partial periods
3. Track working hours vs required hours
4. Suggest hourly deductions for missing hours
5. Allow admin to approve/edit/waive hourly deductions
6. Count paid leaves as worked days (no deduction)
7. Deduct unpaid leaves correctly
8. Support advance payment tracking
9. Display both BS and AD dates in UI
10. Maintain all existing functionality

### 📊 Success Metrics:

-   Sagar's Dec 1-10 payroll shows correct daily rate (666.67)
-   Partial period calculations are accurate
-   Paid leave integration works correctly
-   Hourly deduction approval workflow functions
-   Advance payments are tracked and deducted
-   No regression in existing features
-   All validations work correctly
-   UI is clear and easy to use

## Known Limitations

1. **Multi-month periods**: If period spans two BS months, uses start month's total days
2. **Leap year variations**: Rare BS calendar variations may need manual adjustment
3. **Public holidays**: Not automatically excluded from working days calculation
4. **Half-days**: System counts full days only, no half-day support yet

## Troubleshooting

### Issue: Wrong daily rate

**Check**: Month total days field - should be 29, 30, or 31 for the BS month

### Issue: Hourly deduction not showing

**Check**:

1. Is there actually missing hours?
2. Is employee on monthly salary type?
3. Check total_working_hours_required and total_working_hours_missing fields

### Issue: Paid leave not counted

**Check**:

1. Leave request is approved
2. Leave type is 'annual', 'sick', or 'casual' (not 'unpaid')
3. Leave dates overlap with payroll period

### Issue: Net salary seems wrong

**Check**:

1. All deduction fields in database
2. Whether hourly_deduction_approved is true
3. Advance payment amount
4. Review salary breakdown in UI

## Next Steps After Testing

1. **Documentation**: Update user manual with new features
2. **Training**: Train HR staff on new payroll workflow
3. **Monitoring**: Monitor first few payroll cycles closely
4. **Feedback**: Gather user feedback for improvements
5. **Optimization**: Optimize queries if performance issues arise

## Contact for Issues

If you encounter any issues during testing:

1. Check this testing guide first
2. Review documentation in `/docs/PAYROLL_*.md`
3. Check database for data integrity
4. Review logs for error messages
5. Test with simplified scenarios to isolate the issue
