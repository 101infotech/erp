# Leave Policy Gender Restriction Implementation

## Overview

Successfully implemented gender-based leave policy restrictions in the HRM system, allowing administrators to create leave policies that apply only to specific genders (e.g., period leave for female employees only).

## Implementation Date

December 4, 2024

## Features Implemented

### 1. Database Changes

#### Migration: `add_period_leave_and_gender_restriction_to_hrm_leave_policies`

-   **Table**: `hrm_leave_policies`
-   **Changes**:
    -   Expanded `leave_type` ENUM to include `period` and `unpaid`
    -   Added `gender_restriction` column: ENUM('none', 'male', 'female') DEFAULT 'none'

#### Migration: `add_period_leave_balance_to_hrm_employees`

-   **Table**: `hrm_employees`
-   **Changes**:
    -   Added `period_leave_balance` column (DECIMAL 5,2)

### 2. Model Updates

#### HrmLeavePolicy

-   Added `gender_restriction` to `$fillable` array
-   Supports values: 'none', 'male', 'female'

#### HrmEmployee

-   Added `period_leave_balance` to `$fillable` array
-   Added `period_leave_balance` to `$casts` array with decimal:1 precision

### 3. Controller Updates

#### HrmLeavePolicyController

-   **index()**: Filtered to show only active policies (`is_active = true`)
-   **store()**: Updated validation to include:
    -   `leave_type`: 'annual', 'sick', 'casual', 'unpaid', 'period'
    -   `gender_restriction`: 'none', 'male', 'female'
-   **update()**: Same validation updates as store()

#### HrmLeaveController (Admin)

-   **store()**: Updated validation to accept 'period' leave type

#### LeaveController (Employee)

-   **store()**: Updated validation to accept 'period' leave type

### 4. Service Layer Updates

#### LeavePolicyService

**applyPoliciesToEmployee()**

-   Now checks gender restriction before applying policy
-   Skips policies if employee gender doesn't match restriction

**applyPoliciesToCompany()**

-   Checks gender restriction for each employee
-   Only applies policies to employees matching the restriction

**validateLeaveRequest()**

-   Validates employee gender against policy restriction
-   Returns error if gender doesn't match: "This leave type is not available for your gender."

**getLeaveBalanceSummary()**

-   Added 'period' to leave types array
-   Filters out policies that don't match employee gender
-   Only returns leave balances for applicable policies

**getBalanceField()**

-   Added mapping for 'period' → 'period_leave_balance'

### 5. View Updates

#### create.blade.php (Leave Policy)

-   Added 'Period Leave' option to leave_type dropdown
-   Added gender_restriction dropdown with three options:
    -   No Restriction (All)
    -   Male Only
    -   Female Only
-   Added helper text explaining gender restrictions

#### edit.blade.php (Leave Policy)

-   Same updates as create form
-   Pre-selects existing values for leave_type and gender_restriction

#### create.blade.php (Employee Leave Request)

-   Added conditional 'Period Leave' option
-   Only shows if $stats['period'] exists (employee is female and policy exists)

## How It Works

### Policy Creation Flow

1. Admin creates a leave policy
2. Sets leave type (e.g., 'period')
3. Sets gender restriction (e.g., 'female')
4. Sets annual quota and other settings
5. Policy is saved and automatically applied to matching employees

### Policy Application Flow

1. `LeavePolicyService::applyPoliciesToCompany()` is called
2. Fetches all active policies for the company
3. Fetches all active employees
4. For each employee and policy:
    - Checks if policy gender restriction matches employee gender
    - If match (or restriction is 'none'), applies policy quota to employee balance
    - If no match, skips the policy

### Leave Request Validation Flow

1. Employee submits leave request
2. `LeavePolicyService::validateLeaveRequest()` is called
3. Fetches the policy for that leave type
4. Checks if employee gender matches policy restriction
5. If no match, returns error message
6. If match, validates against balance and quota
7. Returns validation result

### UI Display Flow

1. Employee views leave request form
2. `getLeaveBalanceSummary()` retrieves available leave types
3. Only leave types matching employee gender are included
4. Form displays only applicable leave types with balances

## Example Use Cases

### Case 1: Period Leave for Females

**Policy Setup:**

```
Policy Name: Monthly Period Leave
Leave Type: period
Gender Restriction: female
Annual Quota: 12 days
Requires Approval: Yes
Is Active: Yes
```

**Result:**

-   Only female employees see this leave type
-   Only female employees get period_leave_balance credited
-   Male employees cannot request period leave
-   Validation prevents males from accessing period leave

### Case 2: Paternity Leave for Males

**Policy Setup:**

```
Policy Name: Paternity Leave
Leave Type: casual (or create new type)
Gender Restriction: male
Annual Quota: 15 days
Requires Approval: Yes
Is Active: Yes
```

**Result:**

-   Only male employees see this policy
-   Only male employees get the quota
-   Female employees cannot access this leave

## Files Modified

### Migrations

1. `2025_12_04_193941_add_period_leave_and_gender_restriction_to_hrm_leave_policies.php`
2. `2025_12_04_194423_add_period_leave_balance_to_hrm_employees.php`

### Models

1. `app/Models/HrmLeavePolicy.php`
2. `app/Models/HrmEmployee.php`

### Controllers

1. `app/Http/Controllers/Admin/HrmLeavePolicyController.php`
2. `app/Http/Controllers/Admin/HrmLeaveController.php`
3. `app/Http/Controllers/Employee/LeaveController.php`

### Services

1. `app/Services/LeavePolicyService.php`

### Views

1. `resources/views/admin/hrm/leave-policies/create.blade.php`
2. `resources/views/admin/hrm/leave-policies/edit.blade.php`
3. `resources/views/employee/leave/create.blade.php`

### Documentation

1. `docs/HRM_MODULE.md`

## Testing Checklist

-   [x] Create period leave policy with female restriction
-   [ ] Verify only female employees get period_leave_balance
-   [ ] Test female employee can request period leave
-   [ ] Test male employee cannot see period leave option
-   [ ] Test male employee receives error if trying to request period leave
-   [ ] Verify active-only policy filtering works
-   [ ] Test policy application to new employees
-   [ ] Test policy application when existing policy is updated
-   [ ] Verify leave balance summary only shows applicable leave types
-   [ ] Test carry forward functionality with period leave

## Benefits

1. **Compliance**: Supports gender-specific leave requirements (e.g., menstrual leave laws)
2. **Flexibility**: Can create different policies for different demographics
3. **Automatic**: Policies are automatically applied based on employee gender
4. **User-Friendly**: Employees only see leave types applicable to them
5. **Validation**: Prevents inappropriate leave requests at multiple levels
6. **Maintainable**: Centralized logic in LeavePolicyService

## Future Enhancements

1. Add more leave types (maternity, paternity, etc.)
2. Add age-based restrictions
3. Add tenure-based quotas
4. Add seasonal leave policies
5. Add leave accrual rates (monthly accumulation)
6. Add leave approval workflow with multiple levels

## Notes

-   Default gender_restriction is 'none' for backward compatibility
-   Existing policies without gender_restriction will apply to all employees
-   Period leave balance is optional and only populated if policy exists
-   Gender field must exist in hrm_employees table for this to work
-   All validation happens server-side for security
