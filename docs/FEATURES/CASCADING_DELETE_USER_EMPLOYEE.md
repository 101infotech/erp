# Cascading Delete - User & Employee Records

## Overview
Implemented cascading delete functionality to ensure that when a user account is deleted, all related employee and attendance records are also deleted to maintain database integrity and prevent orphaned records.

## Date
2024

## Problem Statement
Previously, deleting a user account only removed the user record from the `users` table. This left:
- Orphaned employee records in `hrm_employees` table
- Orphaned attendance data in `hrm_attendance_days` and `hrm_attendance_anomalies` tables
- Broken relationships and data inconsistencies

## Solution

### Updated UserController Destroy Method
**File:** `/app/Http/Controllers/Admin/UserController.php`

**Method:** `destroy(User $user)`

**Implementation:**
```php
public function destroy(User $user)
{
    try {
        // Get the related employee record before deleting the user
        $employee = $user->hrmEmployee;
        
        // Delete user account first
        $user->delete();
        
        // Delete the employee record if it exists
        if ($employee) {
            // Delete attendance records first
            HrmAttendanceDay::where('employee_id', $employee->id)->delete();
            HrmAttendanceAnomaly::where('employee_id', $employee->id)->delete();
            
            // Then delete the employee
            $employee->delete();
            
            activity()
                ->performedOn($employee)
                ->causedBy(Auth::user())
                ->log('Employee record deleted along with user account');
        }
        
        activity()
            ->causedBy(Auth::user())
            ->log("Deleted user: {$user->name}");
        
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User and related employee records deleted successfully!');
            
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Error deleting user: ' . $e->getMessage());
    }
}
```

## Deletion Order (Cascade Chain)

The deletion follows this specific order to respect foreign key constraints:

1. **User Account** (`users` table)
   - Deleted first as it's the primary record

2. **Attendance Records** (if employee exists)
   - `hrm_attendance_days` - Daily attendance records
   - `hrm_attendance_anomalies` - Attendance issues/exceptions

3. **Employee Record** (`hrm_employees` table)
   - Deleted last as other records depend on it

## Database Relationships

```
User (users)
  └── hasOne → HrmEmployee (hrm_employees)
       ├── hasMany → HrmAttendanceDay (hrm_attendance_days)
       └── hasMany → HrmAttendanceAnomaly (hrm_attendance_anomalies)
```

## User Interface Updates

### Confirmation Messages
Updated all delete user confirmations to clearly communicate what will be deleted:

**Desktop & Mobile Views:**
```
Title: "Delete User & Employee?"

Message: "Are you sure you want to delete [User Name]?

This will permanently delete:
• User login account
• Employee record
• All attendance data

This action cannot be undone!"
```

**Modal Type:** `danger` (red theme)
**Button Text:** "Yes, Delete Everything"

## Activity Logging

The system logs two activities for audit trail:

1. **Employee Deletion Log:**
   ```php
   activity()
       ->performedOn($employee)
       ->causedBy(Auth::user())
       ->log('Employee record deleted along with user account');
   ```

2. **User Deletion Log:**
   ```php
   activity()
       ->causedBy(Auth::user())
       ->log("Deleted user: {$user->name}");
   ```

## Error Handling

- Wrapped in try-catch block
- Returns error message if deletion fails
- Uses database transactions implicitly through Eloquent
- Prevents partial deletions

## Impact on Jibble Integration

### If Employee Has Jibble Integration
- `jibble_person_id` field on employee record is deleted with the employee
- Jibble employee can be manually deleted first using "Delete Jibble Employee" action
- Or it gets deleted automatically when user is deleted

### Deletion Scenarios

**Scenario 1: Delete User with Jibble Employee**
1. User clicks "Delete User & Employee"
2. System deletes user account
3. System deletes all attendance records
4. System deletes employee record (including jibble_person_id)
5. Success message shown

**Scenario 2: Delete Jibble Employee First, Then User**
1. User clicks "Delete Jibble Employee" (in Jibble dropdown)
2. Jibble employee deleted via API
3. `jibble_person_id` set to null on employee record
4. Later, user clicks "Delete User & Employee"
5. System proceeds with normal cascading delete

**Scenario 3: Unlink User Account**
1. User clicks "Unlink User Account"
2. User record deleted
3. Employee record remains (employee_id = null on employee)
4. Employee can still be viewed in HR records
5. Employee can still sync with Jibble

## Testing Scenarios

### Test Case 1: User with Employee and Attendance
- ✅ Create user with employee record
- ✅ Add attendance data
- ✅ Delete user
- ✅ Verify all records deleted

### Test Case 2: User with Employee (No Attendance)
- ✅ Create user with employee record
- ✅ Delete user
- ✅ Verify user and employee deleted

### Test Case 3: User Only (No Employee)
- ✅ Create user without employee
- ✅ Delete user
- ✅ Verify user deleted successfully

### Test Case 4: User with Jibble Integration
- ✅ Create user linked to Jibble employee
- ✅ Sync attendance from Jibble
- ✅ Delete user
- ✅ Verify all records deleted (including attendance)

### Test Case 5: Cannot Delete Own Account
- ✅ Admin cannot delete their own account
- ✅ Delete button not shown for current user
- ✅ Direct POST request blocked

## Security Considerations

1. **Authorization**
   - Only admins can delete users
   - Users cannot delete their own accounts
   - Checked in UI (button hidden) and controller

2. **Confirmation Required**
   - All deletes require explicit confirmation
   - Modal clearly shows what will be deleted
   - No accidental deletions

3. **Audit Trail**
   - All deletions logged with actor
   - Activity log preserved even after deletion
   - Can track who deleted what and when

## Files Modified

1. `/app/Http/Controllers/Admin/UserController.php` - Updated `destroy()` method
2. `/resources/views/admin/users/index.blade.php` - Updated delete confirmations (desktop & mobile)
3. `/resources/views/admin/users/show.blade.php` - Updated delete confirmation

## Database Tables Affected

1. `users` - User accounts
2. `hrm_employees` - Employee records
3. `hrm_attendance_days` - Daily attendance
4. `hrm_attendance_anomalies` - Attendance exceptions
5. `activity_log` - Audit trail (preserves deletion history)

## Success Messages

**After Successful Deletion:**
```
"User and related employee records deleted successfully!"
```

**After Error:**
```
"Error deleting user: [error message]"
```

## Related Features

- [Create User Account for Employee](../Features/CREATE_USER_ACCOUNT_FOR_EMPLOYEE.md)
- [Confirm Modal Replacement](./CONFIRM_MODAL_REPLACEMENT.md)
- [Users vs Employees Clarification](../USERS_VS_EMPLOYEES_CLARIFICATION.md)

## Future Enhancements

Potential improvements for future iterations:

1. **Soft Deletes**
   - Implement soft deletes for recovery
   - Add "Restore User" functionality
   - Keep deleted records for audit period

2. **Batch Deletion**
   - Allow multiple users to be deleted at once
   - Show summary of what will be deleted

3. **Export Before Delete**
   - Option to export user data before deletion
   - Include all attendance records
   - Generate PDF report

4. **Advanced Notifications**
   - Notify HR when employees are deleted
   - Send email to admin with deletion summary
   - Weekly digest of all deletions
