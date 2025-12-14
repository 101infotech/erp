# Leave Policy System Integration - Complete

## Overview

The leave policy system has been fully integrated into the ERP system. When admins create or update leave policies, they are automatically applied across all modules and UI.

## What Was Implemented

### 1. **LeavePolicyService** (`app/Services/LeavePolicyService.php`)

A centralized service that handles all leave policy operations:

#### Key Methods:

-   `applyPoliciesToEmployee()` - Applies company's active leave policies to a single employee
-   `applyPoliciesToCompany()` - Applies policies to all active employees in a company
-   `getPolicy()` - Retrieves active policy for a company and leave type
-   `validateLeaveRequest()` - Validates leave requests against policies and balances
-   `getLeaveBalanceSummary()` - Gets complete leave balance info for an employee
-   `resetAnnualLeaveBalances()` - Resets balances annually with carry-forward support

### 2. **Automatic Policy Application**

#### When Creating a Policy:

-   Policy is automatically applied to ALL active employees in the company
-   Employee leave balances are updated with the policy quota
-   Success message shows how many employees were updated

#### When Updating a Policy:

-   If policy is active, it's re-applied to all company employees
-   Balances are updated to match the new quota
-   Inactive policies don't affect employees

#### When Creating a New Employee:

-   Leave policies are automatically applied based on their company
-   Employee starts with correct leave balances from day one

### 3. **Leave Request Validation**

Now uses policy-based validation:

-   Checks if company has an active policy for the leave type
-   Validates requested days against available balance
-   Considers already used and pending leave days
-   Returns detailed error messages

### 4. **Employee Dashboard Integration**

Dashboard now shows policy-based leave balances:

-   **Quota**: From active leave policy
-   **Used**: Approved leaves this year
-   **Available**: Quota - Used
-   **Visual Progress Bars**: Show remaining balance percentage

### 5. **Data Flow**

```
Admin Creates/Updates Policy
         ↓
LeavePolicyService.applyPoliciesToCompany()
         ↓
Updates all employee leave balance fields:
  - annual_leave_balance
  - sick_leave_balance
  - casual_leave_balance
         ↓
Employee Dashboard reads these balances
         ↓
Leave requests validate against policy + balance
```

## Files Modified

1. **Controllers**:

    - `HrmLeavePolicyController.php` - Auto-applies policies on create/update
    - `HrmEmployeeController.php` - Applies policies to new employees
    - `HrmLeaveController.php` - Uses policy service for validation
    - `Employee/DashboardController.php` - Uses policy service for balances

2. **Services**:

    - `LeavePolicyService.php` - NEW - Central policy management

3. **Views**:

    - `employee/dashboard.blade.php` - Shows policy-based balances
    - `admin/hrm/leave-policies/*` - CRUD views for policies

4. **Routes**:
    - Added leave-policies resource routes

## How It Works

### Example Flow:

1. **Admin creates a policy**:

    ```
    Company: ABC Company
    Leave Type: Annual
    Quota: 15 days
    ```

2. **System automatically**:

    - Finds all active employees in ABC Company
    - Sets their `annual_leave_balance` = 15
    - Returns: "Policy created and applied to 25 employee leave balances"

3. **Employee sees on dashboard**:

    ```
    Annual Leave: 15/15 days
    (Full green progress bar)
    ```

4. **Employee requests 3 days leave**:

    - System validates: Has active policy? ✓
    - Has sufficient balance? 15 - 0 = 15 available ✓
    - Request approved

5. **After approval**:
    - Dashboard shows: 12/15 days (3 used)
    - Progress bar at 80%

## Policy Features

### Carry Forward

-   If enabled, unused leave carries to next year
-   Max carry forward limit enforced
-   Automatically calculated during annual reset

### Approval Requirements

-   Policy can specify if approval is needed
-   Flexible for different leave types

### Active/Inactive Status

-   Only active policies apply to employees
-   Deactivating a policy doesn't remove balances

## Benefits

✅ **Centralized Management** - Single source of truth for leave policies
✅ **Automatic Sync** - No manual balance updates needed
✅ **Validation** - Prevents over-booking of leaves
✅ **Scalability** - Easy to add new leave types
✅ **Audit Trail** - Policy changes tracked with applied counts
✅ **Employee Transparency** - Real-time balance visibility

## Future Enhancements (Optional)

-   Annual auto-reset scheduled job
-   Leave policy history/versioning
-   Department-specific policies
-   Probation period leave rules
-   Pro-rated leaves for mid-year joiners
