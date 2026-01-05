# Jibble Employee Management Features

**Date:** January 5, 2026  
**Feature:** Link/Unlink User Accounts, Delete Users & Delete Jibble Employees  
**Status:** ✅ IMPLEMENTED

## Overview

Added comprehensive management features for Jibble-synced employees and user accounts in the Team & Users admin interface. Admins can now:

1. **Link** employees to user accounts (for login access)
2. **Unlink** employees from user accounts
3. **Delete** Jibble-synced employees and their data
4. **Delete** user accounts (preserves employee data)

## Features

### 1. Link Employee to User Account

**Purpose:** Connect a Jibble employee record to a user login account.

**Use Case:**
- Employee was synced from Jibble without email
- Want to give employee access to the system
- Need to manually create association between employee and user

**How to Use:**
1. Go to Team & Users page
2. Find employee with "Jibble" badge (orange)
3. Click "Jibble" dropdown → "Link to User"
4. Select user account from dropdown
5. Click "Link User Account"

**Features:**
- Only shows users without existing employee links
- Prevents duplicate associations
- Shows confirmation message
- Updates immediately

**Location:** `/admin/employees/{employee}/link-jibble`

---

### 2. Unlink Employee from User Account

**Purpose:** Remove the connection between employee and user account.

**Use Case:**
- Employee left but user account should remain
- Need to reassign user account to different employee
- Mistakenly linked wrong accounts

**How to Use:**
1. Go to Team & Users page
2. Find employee with "Jibble" badge
3. Click "Jibble" dropdown → "Unlink User"
4. Confirm the action
5. Employee remains but user link is removed

**Features:**
- Requires confirmation
- Employee data is preserved
- User account is preserved
- Only removes the association

**Location:** `/admin/employees/{employee}/unlink-jibble`

---

### 3. Delete Jibble Employee

**Purpose:** Completely remove a Jibble-synced employee and their data.

**Use Case:**
- Employee was synced by mistake
- Duplicate employee records
- Employee permanently left and data should be removed
- Clean up test data

**How to Use:**
1. Go to Team & Users page
2. Find employee with "Jibble" badge
3. Click "Jibble" dropdown → "Delete Employee"
4. Confirm the deletion (with warning)
5. Employee and attendance records are deleted

**Features:**
- Requires confirmation dialog
- Deletes all attendance records
- Unlinking user account (keeps user intact)
- Only works on Jibble-synced employees (has jibble_person_id)
- Cannot be undone

**Location:** `/admin/employees/{employee}/delete-jibble`

---

### 4. Delete User Account

**Purpose:** Remove a user's login account while preserving their employee data.

**Use Case:**
- User account no longer needed for login
- Want to revoke system access but keep employee records
- Clean up unused user accounts
- Remove test accounts

**How to Use:**
1. Go to Team & Users page
2. Find user with an account
3. Click red "Delete User" button
4. Confirm the deletion
5. User account is deleted, employee record is preserved

**Features:**
- Requires confirmation dialog
- Cannot delete your own account (safety)
- Automatically unlinks employee (preserves employee data)
- Shows clear confirmation message
- Employee remains visible with "No Account" badge

**Location:** `/admin/users/{user}`

---

## UI Implementation

### Visual Indicators

**Jibble Badge:**
- Orange badge with lightning icon
- Dropdown menu with actions
- Only appears for employees synced from Jibble

**Status Indicators:**
- "⚠️ No email" - Employee has no email address
- "No Account" - Employee has no user login
- Orange "Jibble" badge - Employee synced from Jibble

### Dropdown Menu

Located in the Actions column:

**For Jibble Employees:**
```
┌─────────────────────┐
│ 🗲 Jibble           │
├─────────────────────┤
│ 🔗 Link to User     │  (if no user linked)
│ 🔓 Unlink User      │  (if user is linked)
├─────────────────────┤
│ 🗑️  Delete Employee │
└─────────────────────┘
```

**For Users with Accounts:**
```
┌─────────────────────┐
│ [View] [Delete User]│
└─────────────────────┘
```

---

## Technical Implementation

### Routes Added

```php
// routes/web.php
Route::get('employees/{employee}/link-jibble', [UserController::class, 'linkJibbleForm'])
    ->name('employees.link-jibble-form');
    
Route::post('employees/{employee}/link-jibble', [UserController::class, 'linkJibble'])
    ->name('employees.link-jibble');
    
Route::post('employees/{employee}/unlink-jibble', [UserController::class, 'unlinkJibble'])
    ->name('employees.unlink-jibble');
    
Route::delete('employees/{employee}/delete-jibble', [UserController::class, 'deleteJibbleEmployee'])
    ->name('employees.delete-jibble');

Route::delete('users/{user}', [UserController::class, 'destroy'])
    ->name('users.destroy');
```

### Controller Methods

**File:** `app/Http/Controllers/Admin/UserController.php`

1. `linkJibbleForm($employeeId)` - Shows form to select user
2. `linkJibble(Request $request, $employeeId)` - Links employee to user
3. `unlinkJibble($employeeId)` - Removes user link
4. `deleteJibbleEmployee($employeeId)` - Deletes employee and data
5. `destroy(User $user)` - Deletes user account (preserves employee)

### Views

**Main View:** `resources/views/admin/users/index.blade.php`
- Added Jibble dropdown menu
- Alpine.js for dropdown interaction
- Visual indicators for employee status

**Link Form:** `resources/views/admin/users/link-jibble.blade.php`
- Employee info display
- User selection dropdown
- Helpful information box
- Confirmation buttons

---

## Safety Features

### Validations

1. **Link User:**
   - Checks if user already linked to another employee
   - Only shows unlinked users in dropdown
   - Validates user exists

2. **Unlink User:**
   - Checks if employee has user link
   - Preserves both employee and user records

3. **Delete Employee:**
   - Only allows deletion of Jibble-synced employees
   - Requires confirmation dialog
   - Shows warning about data loss

4. **Delete User:**
   - Cannot delete your own account
   - Requires confirmation
   - Automatically unlinks employee before deletion

### Data Integrity

- **On Link:** Creates association, updates user_id
- **On Unlink:** Removes association, preserves records
- **On Delete Employee:** 
  - Deletes attendance records
  - Unlinks user (keeps user account)
  - Deletes employee record
- **On Delete User:**
  - Unlinks employee (keeps employee record)
  - Deletes user account
  - Employee remains with "No Account" status

---

## Use Cases & Examples

### Example 1: Link Employee Without Email

**Scenario:** Bristi Maharjan was synced from Jibble without email.

**Steps:**
1. Create user account manually with email
2. Go to Team & Users
3. Find "Bristi Maharjan" 
4. Click "Jibble" → "Link to User"
5. Select the user account
6. Click "Link User Account"

**Result:** Bristi can now log in and access her employee profile.

---

### Example 2: Remove Duplicate Employee

**Scenario:** Employee synced twice due to data issue.

**Steps:**
1. Identify duplicate employee record
2. Click "Jibble" → "Delete Employee"
3. Confirm deletion
4. Record is removed

**Result:** Only one employee record remains.

---

### Example 3: Employee Reassignment

**Scenario:** User account needs to be linked to different employee.

**Steps:**
1. Find current employee
2. Click "Jibble" → "Unlink User"
3. Confirm
4. Find new employee
5. Click "Jibble" → "Link to User"
6. Select the freed user account

**Result:** User account now associated with different employee.

---

### Example 4: Remove User Account

**Scenario:** Employee no longer needs system access but records must be kept.

**Steps:**
1. Find user in Team & Users
2. Click red "Delete User" button
3. Confirm deletion
4. User account removed

**Result:** Employee record preserved, user cannot log in anymore.

---

## Files Modified

1. `/app/Http/Controllers/Admin/UserController.php` - Added 4 new methods
2. `/resources/views/admin/users/index.blade.php` - Added Jibble dropdown menu
3. `/resources/views/admin/users/link-jibble.blade.php` - New link form view
4. `/routes/web.php` - Added 4 new routes

---

## Current Stats

```
Total Employees: 17
Employees with Jibble ID: 17
Employees without user account: 2
  - Bristi Maharjan
  - Rajiv KC
```

---

## Security Considerations

1. **Admin Only:** All actions require admin authentication
2. **CSRF Protection:** All forms include CSRF tokens
3. **Confirmation Dialogs:** Delete requires explicit confirmation
4. **Validation:** All inputs validated before processing
5. **Preserved Data:** User accounts preserved even when unlinked

---

## Future Enhancements

1. Bulk link/unlink operations
2. Email notification when linked/unlinked
3. Audit log for link/unlink actions
4. Soft delete for employees (instead of hard delete)
5. Auto-suggest matching based on name/email
6. Import/export employee-user mappings

---

## Testing Checklist

- ✅ Link employee to user
- ✅ Unlink employee from user
- ✅ Delete Jibble employee
- ✅ Delete user account
- ✅ Prevent duplicate links
- ✅ Handle employees without email
- ✅ Preserve user accounts on unlink
- ✅ Preserve employee records when deleting user
- ✅ Delete attendance records on employee delete
- ✅ Prevent deleting own account
- ✅ Show proper error messages
- ✅ Display success confirmations
- ✅ Dropdown menu works correctly
- ✅ Delete button appears for users with accounts

---

## Support

If you encounter issues:

1. Check employee has `jibble_person_id` (synced from Jibble)
2. Verify user account exists and isn't already linked
3. Check logs: `storage/logs/laravel.log`
4. Ensure JavaScript/Alpine.js is enabled in browser
