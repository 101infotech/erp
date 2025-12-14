# HRM Module - Quick Start Guide

## 🚀 Getting Started

The HRM module is now **85% complete** and ready for testing. Here's how to use it:

---

## 📍 Navigation

After logging in as admin, you'll see the updated sidebar with 3 HRM items:

1. **Team Management** - Employee list and attendance tracking
2. **Organization** - Companies and departments management
3. **Payroll & Leaves** - Payroll generation and leave requests

---

## 💰 Payroll Management

### Generating Payroll

1. Click **Payroll & Leaves** in sidebar
2. Click **Payroll** tab (default)
3. Click **Generate Payroll** button
4. **Select Employees:**
    - Check individual employees OR
    - Click "Select All" to choose everyone
5. **Enter Period Dates:**
    - **Bikram Sambat:** Enter BS dates (e.g., 2081-08-01 to 2081-08-30)
    - **Gregorian:** Select AD dates using date picker
    - _Both must represent the same period_
6. Click **Generate Payroll**
7. You'll be redirected to the payroll list with success message

### Reviewing Payroll (Draft Status)

1. Click **View** on any draft payroll
2. Review the payslip details:
    - ✅ Employee information
    - ✅ Attendance summary (hours, days, OT, absents)
    - ✅ Salary breakdown (basic + OT + allowances - tax - deductions)
    - ✅ Anomalies panel (right side)

### Editing Payroll

**Only draft payrolls can be edited**

1. Click **Edit** button on payslip
2. **Update Fields:**
    - **Overtime Payment:** Enter manual OT amount (defaults to 0)
    - **Allowances:** Click "Add Allowance" to add items (e.g., "Transport - 5000")
    - **Deductions:** Click "Add Deduction" to add items (e.g., "Advance - 10000")
    - **Tax Override:** Enter new tax amount + reason (optional)
3. Click **Update Payroll**
4. Net salary will recalculate automatically

### Reviewing Anomalies

If the **⚠ Anomalies** badge appears:

1. Open the payslip
2. Scroll to **Attendance Anomalies** panel (right side)
3. Review each anomaly:
    - **High Severity (Red):** Missing clock-out, excessive hours, negative time
    - **Medium Severity (Yellow):** Weekend work without OT, duplicate entries
    - **Low Severity (Blue):** Location inconsistencies
4. Click **Mark as Reviewed** button
5. Anomalies must be reviewed before approval

### Approving Payroll

**Prerequisites:**

-   Payroll status is "Draft"
-   Anomalies are reviewed (if any)

1. Open the payslip
2. Click **Approve** button
3. Confirm in dialog
4. Status changes to "Approved"
5. Approval timestamp and approver recorded

### Marking as Paid

**Prerequisites:**

-   Payroll status is "Approved"

1. Open the approved payslip
2. Click **Mark as Paid** button
3. **Select Payment Method:**
    - Bank Transfer
    - Cash
    - Cheque
4. **Enter Transaction Reference** (optional but recommended)
5. Click **Confirm Payment**
6. Status changes to "Paid"
7. Payment timestamp recorded

---

## 🏖️ Leave Management

### Submitting Leave Request

1. Click **Payroll & Leaves** in sidebar
2. Click **Leaves** dropdown → **Leave Requests**
3. Click **New Leave Request** button
4. **Fill Form:**
    - **Employee:** Select from dropdown (balance displays automatically)
    - **Leave Type:** Annual / Sick / Casual / Unpaid
    - **Start Date:** Select using date picker
    - **End Date:** Select using date picker
    - **Reason:** Enter reason (max 500 characters)
5. Click **Submit Leave Request**
6. System calculates work days (excludes Saturdays automatically)
7. For paid leaves, balance is checked before submission

### Viewing Leave Balance

When you select an employee in the leave request form:

-   Available balance displays automatically
-   Shows Annual, Sick, and Casual leave balances
-   Real-time validation prevents over-requesting

### Approving Leave Request

1. Go to **Leaves** page
2. Click **Pending** tab
3. Click **View** on a request
4. Review details:
    - Employee name and code
    - Leave type and duration
    - Reason provided
    - Requested date
5. Click **Approve Leave Request**
6. Confirm in dialog
7. Balance is deducted automatically
8. Status changes to "Approved"

### Rejecting Leave Request

1. Open a pending leave request
2. Click **Reject Leave Request** button
3. **Enter Rejection Reason** (required)
4. Click **Confirm Rejection**
5. Status changes to "Rejected"
6. Reason is stored and visible to employee

### Cancelling Leave

**Can cancel pending or approved leaves**

1. Open the leave request
2. Scroll to bottom
3. Click **Cancel Leave Request** button
4. Confirm in dialog
5. If leave was approved, balance is restored automatically
6. Status changes to "Cancelled"

---

## 🏢 Organization Management

### Viewing Companies

1. Click **Organization** in sidebar
2. **Companies** tab is default
3. View company cards with:
    - Company name
    - Employee count
    - Address (if available)
    - Jibble ID (if synced)
4. Click **View Details** to see full company page

### Viewing Departments

1. Click **Organization** in sidebar
2. Click **Departments** tab
3. View departments table with:
    - Department name
    - Parent company
    - Employee count
    - Jibble ID (if synced)
4. Click **View** or **Edit** to manage department

### Adding Company/Department

1. Click **Add Company** or **Add Department** button
2. Fill in required details
3. Click **Save**
4. Company/department appears in list immediately

---

## 🔍 Using Filters

### Payroll Filters

Available on payroll index page:

-   **Status:** Draft / Approved / Paid
-   **Employee:** Select specific employee
-   **Company:** Filter by company
-   **Period From:** Filter by start date
-   Click **Apply Filters** to refresh list

### Leave Filters

Available on leaves index page:

-   **Status Tabs:** Pending / Approved / Rejected / All
-   **Employee:** Select specific employee
-   **Leave Type:** Annual / Sick / Casual / Unpaid
-   **Date From:** Filter by start date
-   Click **Apply Filters** to refresh list

---

## 🎨 UI Elements Guide

### Status Badges

**Payroll:**

-   🟡 **Yellow "Draft"** - Editable, needs review
-   🔵 **Blue "Approved"** - Ready for payment
-   🟢 **Green "Paid"** - Payment completed

**Leaves:**

-   🟡 **Yellow "Pending"** - Awaiting approval
-   🟢 **Green "Approved"** - Leave granted
-   🔴 **Red "Rejected"** - Leave denied
-   ⚫ **Gray "Cancelled"** - Request cancelled

**Anomalies:**

-   🔴 **Red Badge** - Unreviewed anomalies present
-   No badge - No anomalies or all reviewed

**Leave Types:**

-   🔵 **Blue** - Annual Leave
-   🔴 **Red** - Sick Leave
-   🟢 **Green** - Casual Leave
-   ⚫ **Gray** - Unpaid Leave

### Action Buttons

**Primary Actions (Green/Lime):**

-   Generate Payroll
-   New Leave Request
-   Add Company/Department
-   Submit/Save buttons

**Secondary Actions (Blue):**

-   Edit buttons
-   View Details

**Destructive Actions (Red):**

-   Reject buttons
-   Delete/Remove

**Neutral Actions (Gray):**

-   Cancel buttons
-   Back buttons

---

## ⚠️ Important Notes

### Payroll

-   ✅ Overtime payment is **manual entry only** (not auto-calculated)
-   ✅ Tax is auto-calculated using Nepal tax slabs but can be overridden
-   ✅ Allowances and deductions are dynamic (add as many as needed)
-   ✅ Anomalies must be reviewed before approval
-   ✅ Only draft payrolls can be edited
-   ✅ BS and AD dates must represent the same period

### Leaves

-   ✅ Weekends (Saturdays) are automatically excluded from calculation
-   ✅ Balance is checked only for paid leaves (annual, sick, casual)
-   ✅ Unpaid leaves don't require available balance
-   ✅ Balance is deducted only on approval
-   ✅ Cancelled leaves restore balance if already approved

### Organization

-   ✅ Companies and departments can be managed independently
-   ✅ Employees must be assigned to a company/department
-   ✅ Jibble sync is separate (doesn't auto-create)

---

## 🐛 Troubleshooting

### "Insufficient leave balance"

**Cause:** Employee doesn't have enough leave days  
**Solution:**

-   Check employee's leave balance in employee profile
-   Use unpaid leave instead
-   Or increase employee's leave balance in employee edit page

### "Anomalies must be reviewed before approval"

**Cause:** Payroll has unreviewed attendance anomalies  
**Solution:**

-   Open the payslip
-   Review anomalies in right panel
-   Click "Mark as Reviewed" button
-   Then approve

### "Only draft payrolls can be edited"

**Cause:** Trying to edit approved or paid payroll  
**Solution:**

-   Cannot edit after approval (by design for audit trail)
-   If correction needed, generate new payroll for correction period

### Tax calculation seems wrong

**Cause:** Nepal tax slabs or calculation issue  
**Solution:**

-   Check NepalTaxCalculationService TAX_SLABS constant
-   Use tax override with reason if needed
-   Verify annual income calculation

### BS date validation fails

**Cause:** Invalid BS date format  
**Solution:**

-   Use format: YYYY-MM-DD (e.g., 2081-08-15)
-   Ensure valid BS month (1-12)
-   Ensure valid BS day for that month

---

## 📊 Data Examples

### Sample Payroll Generation

```
Employees: Select 3-5 employees
Period BS: 2081-08-01 to 2081-08-30
Period AD: 2024-11-16 to 2024-12-15
Result: 5 draft payroll records created
```

### Sample Leave Request

```
Employee: John Doe
Leave Type: Annual
Start Date: 2024-12-20
End Date: 2024-12-24
Work Days: 4 days (excludes Saturday)
Balance Before: 10 days
Balance After Approval: 6 days
```

### Sample Tax Calculation

```
Monthly Salary: NPR 100,000
Annual Income: NPR 1,200,000
Tax Slabs Applied:
- First 500,000 @ 1% = 5,000
- Next 200,000 @ 10% = 20,000
- Next 300,000 @ 20% = 60,000
- Next 200,000 @ 30% = 60,000
Total Annual Tax: 145,000
Monthly Tax: 12,083.33
```

---

## 🎯 Best Practices

### Payroll

1. Generate payroll at month end after all attendance is recorded
2. Review all anomalies before approving
3. Keep overtime payment records separate for audit
4. Use tax override sparingly and always document reason
5. Mark as paid only after actual payment is made

### Leaves

1. Approve leaves promptly to avoid confusion
2. Always provide clear rejection reasons
3. Cancel leaves if employee doesn't actually take them
4. Monitor leave balances to prevent over-requests
5. Set up leave policies at company level first

### Organization

1. Create companies before departments
2. Assign all employees to proper company/department
3. Don't create duplicate entries during Jibble sync
4. Keep Jibble IDs for reference but manage locally

---

## 📞 Need Help?

Check the comprehensive documentation:

-   `/docs/HRM_IMPLEMENTATION_FINAL_SUMMARY.md` - Complete feature list
-   `/docs/IMPLEMENTATION_PROGRESS.md` - Current status
-   `/docs/HRM_MODULE.md` - Original requirements

---

**Happy HRM Managing! 🎉**
