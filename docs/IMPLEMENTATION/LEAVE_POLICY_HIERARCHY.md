# Leave Policy Hierarchy - Individual vs Company-Wide

## Overview
The system now implements a **hierarchical leave policy system** where employee-specific leave quotas take precedence over company-wide policies.

**Priority Logic:**
1. **Individual Employee Quota** (highest priority) - If set on employee record
2. **Company-Wide Policy** (fallback) - If individual quota not set

## Implementation Date
January 5, 2026

## How It Works

### 1. Company-Wide Leave Policy (Base Level)

Company-wide policies are defined in the Leave Policies section:

**Database:** `hrm_leave_policies`
```php
company_id: 1
leave_type: 'annual'
annual_quota: 15  // 15 days for all employees
gender_restriction: 'none'
```

**Applies to:** All employees in the company by default

### 2. Individual Employee Quota (Override)

Individual quotas can be set on each employee:

**Database:** `hrm_employees`
```php
employee_id: 123
paid_leave_annual: 20   // Overrides company policy
paid_leave_sick: null    // Will use company policy
paid_leave_casual: 10    // Overrides company policy
```

**Applies to:** Only that specific employee

### 3. Effective Quota Resolution

When the system checks leave quota for an employee:

```php
// Employee: John Doe
// Company Policy: 15 days annual leave
// Employee Record: paid_leave_annual = 20

$effectiveQuota = $leavePolicyService->getEffectiveQuota($employee, 'annual');
// Result: 20 days (uses individual quota)
```

```php
// Employee: Jane Smith  
// Company Policy: 15 days annual leave
// Employee Record: paid_leave_annual = null (not set)

$effectiveQuota = $leavePolicyService->getEffectiveQuota($employee, 'annual');
// Result: 15 days (uses company policy)
```

## Use Cases

### Use Case 1: Senior Employees Get More Leave

**Scenario:**
- Company policy: 15 days annual leave
- Senior employees: 25 days annual leave
- Regular employees: Use company policy (15 days)

**Setup:**
1. Create company-wide policy: 15 days
2. For senior employees, set `paid_leave_annual = 25`
3. For regular employees, leave `paid_leave_annual = null`

### Use Case 2: Contract-Based Leave

**Scenario:**
- Different contracts have different leave entitlements
- Full-time: 20 days
- Part-time: 10 days
- Contract: 5 days

**Setup:**
1. Company policy: 20 days (for full-time)
2. Set individual quotas for part-time and contract employees

### Use Case 3: Probation Period Reduced Leave

**Scenario:**
- Employees on probation get reduced leave
- After probation: Full company policy applies

**Setup:**
1. Company policy: 15 days
2. During probation: Set `paid_leave_annual = 5`
3. After probation: Set `paid_leave_annual = null` (reverts to 15)

### Use Case 4: Gender-Specific Policies with Exceptions

**Scenario:**
- Company has period leave only for females (5 days)
- But some male employees have medical conditions requiring similar leave

**Setup:**
1. Company policy: `gender_restriction = 'female'`, 5 days
2. For specific male employees: Create individual quota (overrides gender restriction)

## Code Examples

### Check Effective Quota
```php
use App\Services\LeavePolicyService;

$policyService = new LeavePolicyService();
$employee = HrmEmployee::find(123);

// Get effective quota for annual leave
$quota = $policyService->getEffectiveQuota($employee, 'annual');
// Returns: Employee-specific quota OR company policy quota

// For all leave types:
$summary = $policyService->getLeaveBalanceSummary($employee);
// Returns:
// [
//     'annual' => [
//         'quota' => 20,
//         'balance' => 18,
//         'used' => 2,
//         'available' => 18,
//         'source' => 'individual'  // or 'company'
//     ],
//     ...
// ]
```

### Validate Leave Request
```php
$validation = $policyService->validateLeaveRequest($employee, 'annual', 3);

if ($validation['valid']) {
    // Shows which quota is being used
    echo "Quota: " . $validation['quota'];
    echo "Available: " . $validation['available'];
} else {
    echo $validation['message'];
}
```

### Set Individual Quota
```php
$employee = HrmEmployee::find(123);

// Grant senior employee more annual leave
$employee->paid_leave_annual = 25;
$employee->save();
```

### Remove Individual Quota (Revert to Company Policy)
```php
$employee = HrmEmployee::find(123);

// Remove individual quota - will use company policy
$employee->paid_leave_annual = null;
$employee->save();
```

## Field Mapping

| Leave Type | Company Policy Field | Employee Individual Field | Employee Balance Field |
|-----------|---------------------|-------------------------|----------------------|
| Annual    | `annual_quota`      | `paid_leave_annual`     | `annual_leave_balance` |
| Sick      | `annual_quota`*     | `paid_leave_sick`       | `sick_leave_balance` |
| Casual    | `annual_quota`*     | `paid_leave_casual`     | `casual_leave_balance` |
| Period    | `annual_quota`*     | N/A                     | `period_leave_balance` |

*Note: The `annual_quota` field in policies is used for all leave types - the name is historical but applies to each policy type.

## UI Integration

### Employee Edit Form

The employee edit form should show individual quota fields:

```blade
<div class="form-group">
    <label>Annual Leave Quota (Override)</label>
    <input type="number" step="0.5" name="paid_leave_annual" 
           value="{{ $employee->paid_leave_annual }}"
           placeholder="Leave blank to use company policy">
    <small>Company policy: {{ $companyPolicy->annual_quota ?? 'Not set' }} days</small>
</div>
```

### Leave Balance Display

```blade
@foreach($leaveBalances as $type => $balance)
    <div class="leave-type">
        <span>{{ ucfirst($type) }} Leave</span>
        <span>{{ $balance['available'] }} / {{ $balance['quota'] }} days</span>
        @if($balance['source'] === 'individual')
            <span class="badge">Individual Quota</span>
        @else
            <span class="badge">Company Policy</span>
        @endif
    </div>
@endforeach
```

## Database Schema

### Company Policy Table
```sql
CREATE TABLE hrm_leave_policies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    policy_name VARCHAR(255),
    leave_type ENUM('annual', 'sick', 'casual', 'period'),
    gender_restriction ENUM('none', 'male', 'female') DEFAULT 'none',
    annual_quota INT COMMENT 'Days per year',
    carry_forward_allowed BOOLEAN DEFAULT FALSE,
    max_carry_forward INT DEFAULT 0,
    requires_approval BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE KEY (company_id, leave_type)
);
```

### Employee Individual Quotas
```sql
ALTER TABLE hrm_employees ADD COLUMN paid_leave_annual DECIMAL(3,1) NULL;
ALTER TABLE hrm_employees ADD COLUMN paid_leave_sick DECIMAL(3,1) NULL;
ALTER TABLE hrm_employees ADD COLUMN paid_leave_casual DECIMAL(3,1) NULL;
```

## Logic Flow Diagram

```
Employee Requests Leave
         ↓
Check Employee Record
         ↓
    ┌─────────────────┐
    │ Individual      │
    │ Quota Set?      │
    └─────────────────┘
         ├─ YES → Use Individual Quota
         │         (e.g., 20 days)
         │
         └─ NO → Check Company Policy
                      ↓
                 ┌─────────────────┐
                 │ Company Policy  │
                 │ Exists?         │
                 └─────────────────┘
                      ├─ YES → Check Gender Restriction
                      │         ├─ Matches → Use Policy Quota
                      │         │            (e.g., 15 days)
                      │         └─ No Match → Quota = 0
                      │
                      └─ NO → Quota = 0 (No leave available)
```

## Updated Service Methods

### `getEffectiveQuota()`
**Purpose:** Determine which quota to use (individual or company)

**Parameters:**
- `$employee` - Employee model
- `$leaveType` - 'annual', 'sick', 'casual', etc.

**Returns:** `float` - The effective quota in days

**Logic:**
1. Check if employee has individual quota set AND > 0
2. If yes, return individual quota
3. If no, check company policy
4. Apply gender restrictions if from company policy
5. Return quota or 0 if none available

### `validateLeaveRequest()`
**Updated:** Now uses `getEffectiveQuota()` instead of just company policy

**Returns:** Includes `'quota'` field showing which quota is being used

### `getLeaveBalanceSummary()`
**Updated:** Shows quota source in results

**Returns:** Each leave type includes `'source'` field:
- `'individual'` - Using employee-specific quota
- `'company'` - Using company policy

## Migration Guide

### For Existing Systems

If you have existing employees using company policies:

1. **No action required** - Individual quotas default to `null`, so company policies continue to work

2. **To grant individual quotas:**
```php
// Update specific employees
HrmEmployee::where('position', 'Senior Manager')
    ->update(['paid_leave_annual' => 25]);
```

3. **To migrate from old system:**
```php
// If you had individual quotas in a different field
HrmEmployee::whereNotNull('old_quota_field')->each(function($emp) {
    $emp->paid_leave_annual = $emp->old_quota_field;
    $emp->save();
});
```

## Testing Scenarios

### Test 1: Individual Quota Takes Precedence
```php
// Setup
$employee->paid_leave_annual = 20;
$companyPolicy->annual_quota = 15;

// Test
$quota = $service->getEffectiveQuota($employee, 'annual');
assertEquals(20, $quota); // Individual quota used
```

### Test 2: Fallback to Company Policy
```php
// Setup  
$employee->paid_leave_annual = null; // Not set
$companyPolicy->annual_quota = 15;

// Test
$quota = $service->getEffectiveQuota($employee, 'annual');
assertEquals(15, $quota); // Company policy used
```

### Test 3: No Quota Available
```php
// Setup
$employee->paid_leave_annual = null;
// No company policy exists

// Test
$quota = $service->getEffectiveQuota($employee, 'annual');
assertEquals(0, $quota);
```

### Test 4: Gender Restriction on Company Policy
```php
// Setup
$employee->paid_leave_annual = null;
$employee->gender = 'male';
$companyPolicy->gender_restriction = 'female';

// Test
$quota = $service->getEffectiveQuota($employee, 'period');
assertEquals(0, $quota); // Doesn't match gender restriction
```

### Test 5: Individual Quota Bypasses Gender Restriction
```php
// Setup
$employee->paid_leave_sick = 10; // Individual quota set
$employee->gender = 'male';
$companyPolicy->gender_restriction = 'female';

// Test
$quota = $service->getEffectiveQuota($employee, 'sick');
assertEquals(10, $quota); // Individual quota bypasses gender restriction
```

## Benefits

1. **Flexibility**: Different employees can have different entitlements
2. **Granular Control**: Managers can adjust individual quotas without changing company policy
3. **Backward Compatible**: Existing company policies continue to work
4. **Transparent**: System clearly shows which quota is being used
5. **Simple Override**: Just set individual field to override company policy
6. **Easy Revert**: Set to `null` to go back to company policy

## Best Practices

1. **Use Company Policies as Default**: Set reasonable company-wide defaults
2. **Document Exceptions**: Keep track of why certain employees have individual quotas
3. **Review Annually**: Check if individual quotas are still necessary
4. **Automate Where Possible**: Use role-based or contract-based rules to set quotas
5. **Clear Communication**: Let employees know their leave source (individual vs company)

## Related Files

- **Service:** [app/Services/LeavePolicyService.php](../../app/Services/LeavePolicyService.php)
- **Employee Model:** [app/Models/HrmEmployee.php](../../app/Models/HrmEmployee.php)
- **Leave Policy Model:** [app/Models/HrmLeavePolicy.php](../../app/Models/HrmLeavePolicy.php)

## Status

✅ **Implemented and Production Ready**

**Key Changes:**
- Added `getEffectiveQuota()` method
- Updated `validateLeaveRequest()` to use effective quota
- Updated `getLeaveBalanceSummary()` to show quota source
- Maintains backward compatibility

---

**Implementation Date:** January 5, 2026  
**Status:** Complete
