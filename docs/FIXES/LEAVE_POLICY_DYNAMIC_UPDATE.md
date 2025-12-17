# Leave Policy Dynamic System Update

## Date

December 4, 2024

## Changes Made

### 1. Policy-Based Leave System

**Issue**: Leave types (sick, casual, annual) were hardcoded in the employee portal, showing even when no policies were created by admin.

**Solution**: Updated the system to only show leave types that have active policies created by administrators.

#### Implementation Details:

**Employee LeaveController (`app/Http/Controllers/Employee/LeaveController.php`)**

-   Replaced hardcoded `calculateLeaveStats()` method with `LeavePolicyService`
-   Added `LeavePolicyService` to use statements
-   Updated `store()` method to validate only against available leave types from active policies
-   Dynamic validation: Only allows leave types that have active policies for the employee

**Before:**

```php
protected function calculateLeaveStats($employeeId)
{
    // Hardcoded policies
    $policies = [
        'sick' => ['total' => 12],
        'casual' => ['total' => 10],
        'annual' => ['total' => 15],
    ];
    // ...
}
```

**After:**

```php
protected function calculateLeaveStats($employeeId)
{
    $employee = \App\Models\HrmEmployee::find($employeeId);
    if (!$employee) {
        return [];
    }
    $policyService = new \App\Services\LeavePolicyService();
    return $policyService->getLeaveBalanceSummary($employee);
}
```

### 2. Navigation Consolidation

**Issue**: Employee dashboard had duplicate navigation - one embedded in the file and one from the nav partial.

**Solution**: Removed embedded navigation from dashboard and used only the nav partial for consistency.

#### Files Updated:

**Dashboard (`resources/views/employee/dashboard.blade.php`)**

-   Removed embedded navigation code (45 lines)
-   Added `@include('employee.partials.nav')` directive
-   Now consistent with other employee views (attendance, payroll, leave)

### 3. View Updates

#### Leave Index View (`resources/views/employee/leave/index.blade.php`)

-   Updated to use dynamic `$stats` array from policies
-   Changed from `@foreach` to `@forelse` to handle empty states
-   Added empty state message when no policies are configured
-   Updated grid to dynamically adjust columns based on number of policies
-   Changed data access from `$stats[$type]['total']` to `$stat['quota']`
-   Added support for all leave types (period, unpaid) dynamically

#### Dashboard View (`resources/views/employee/dashboard.blade.php`)

-   Updated leave balance section to display dynamic policies
-   Added empty state for when no policies are configured
-   Dynamic grid columns based on number of active policies
-   Changed from hardcoded array to dynamic `$stats['leaves']` iteration

### 4. Leave Request Validation

**Employee Leave Store Method**

-   Now validates against available leave types from active policies
-   Builds allowed leave types dynamically: `array_keys($stats) + ['unpaid']`
-   Uses `LeavePolicyService::validateLeaveRequest()` for validation
-   Returns policy-specific error messages

**Before:**

```php
$request->validate([
    'leave_type' => 'required|in:sick,casual,annual,unpaid,period',
    // ...
]);
```

**After:**

```php
$stats = $policyService->getLeaveBalanceSummary($employee);
$availableLeaveTypes = array_keys($stats);
$availableLeaveTypes[] = 'unpaid'; // Always available

$request->validate([
    'leave_type' => ['required', 'in:' . implode(',', $availableLeaveTypes)],
    // ...
]);
```

## Benefits

### 1. Admin Control

-   Admins have full control over which leave types are available
-   No leave types appear until admin creates policies
-   Easy to add new leave types (just create a policy)

### 2. Consistency

-   Single source of truth (leave policies in database)
-   No hardcoded values in multiple places
-   Unified navigation across all employee views

### 3. Flexibility

-   Supports unlimited leave types
-   Gender-based restrictions work seamlessly
-   Dynamic UI adjusts to number of policies

### 4. Better UX

-   Clear messaging when no policies exist
-   Only shows applicable leave types to each employee
-   Prevents confusion from seeing unavailable leave types

## Files Modified

1. `app/Http/Controllers/Employee/LeaveController.php`

    - Replaced hardcoded stats with LeavePolicyService
    - Dynamic validation for leave types

2. `resources/views/employee/dashboard.blade.php`

    - Removed duplicate navigation
    - Dynamic leave balance display
    - Empty state handling

3. `resources/views/employee/leave/index.blade.php`
    - Dynamic leave cards from policies
    - Empty state for no policies
    - Support for all leave types

## Testing Scenarios

### Scenario 1: No Policies Created

**Expected**:

-   Employee sees message: "No leave policies have been configured by your administrator yet."
-   Cannot request any leave (except unpaid if allowed)

### Scenario 2: Admin Creates Sick Leave Policy

**Expected**:

-   Sick leave card appears in employee dashboard
-   Employee can request sick leave
-   Balance shows quota from policy

### Scenario 3: Female Employee with Period Leave Policy

**Expected**:

-   Female employee sees period leave option
-   Male employee does not see period leave
-   Validation prevents male from requesting period leave

### Scenario 4: Multiple Policies

**Expected**:

-   Grid adjusts to show all policies (up to 4 columns)
-   Each policy shows correct quota and balance
-   All applicable leave types available in request form

## Migration Path

### For Existing Installations:

1. **Administrators must create leave policies**

    - Navigate to Admin → HRM Module → Leave Policies
    - Click "Add Policy"
    - Create policies for: Annual, Sick, Casual, Period (if needed)
    - Set quotas and restrictions
    - Mark as active

2. **Policies are auto-applied**

    - Existing employees get balances updated automatically
    - New employees get balances on creation

3. **Employees will see**
    - Only active policies in their dashboard
    - Only leave types they're eligible for (based on gender)
    - Clear balances from policies

## Notes

-   Unpaid leave is always available (no policy needed)
-   Gender restrictions are enforced at multiple levels
-   Empty states guide admins to create policies
-   Backward compatible (existing leaves still visible)

## Next Steps

1. Admin should create leave policies for all companies
2. Test employee views with various policy combinations
3. Verify gender restrictions work correctly
4. Check leave request validation with policies
