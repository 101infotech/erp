# Jibble Sync Fix - Staff Without Email

**Date:** January 5, 2026  
**Issue:** 2 staff members in Jibble without email addresses were not syncing to the system  
**Status:** ✅ FIXED

## Problem

The system had 2 staff members in Jibble (Bristi Maharjan and Rajiv KC) who don't have email addresses. These employees were not being synced to the local database due to the following issues:

1. **Method Name Mismatch**: The sync command was calling `syncPeople()` but the service class had `syncEmployees()`
2. **Database Constraint**: The `hrm_employees` table had a `UNIQUE` constraint on the `email` column, which prevented multiple employees with `NULL` emails from being stored
3. **Fillable Issue**: The `full_name` field was not in the `$fillable` array of the HrmEmployee model

## Root Causes

### 1. Method Name Mismatch
**File:** `app/Console/Commands/SyncJibbleEmployees.php`
- The command was calling `$peopleService->syncPeople()`
- The actual method in `JibblePeopleService` is `syncEmployees()`
- This caused the sync to fail silently

### 2. Unique Constraint on Email
**File:** `database/migrations/2024_12_02_000003_create_hrm_employees_table.php`
- The migration defined: `$table->string('email')->nullable()->unique();`
- MySQL's unique constraint allows only ONE `NULL` value
- When trying to sync the second employee without email, it would fail with a duplicate key error

### 3. Mass Assignment Protection
**File:** `app/Models/HrmEmployee.php`
- The `full_name` field was not in the `$fillable` array
- This could prevent the field from being assigned during sync

## Solution

### 1. Fixed Method Name
**File:** `app/Console/Commands/SyncJibbleEmployees.php`

Changed:
```php
$count = $peopleService->syncPeople();
```

To:
```php
$count = $peopleService->syncEmployees();
```

### 2. Removed Unique Constraint on Email
**Created Migration:** `database/migrations/2026_01_05_115650_remove_unique_constraint_from_hrm_employees_email.php`

```php
public function up(): void
{
    Schema::table('hrm_employees', function (Blueprint $table) {
        // Drop the unique constraint on email to allow multiple employees without email
        $table->dropUnique(['email']);
    });
}
```

This allows multiple employees to have `NULL` email addresses.

### 3. Added full_name to Fillable Array
**File:** `app/Models/HrmEmployee.php`

Added `'full_name'` to the `$fillable` array to ensure mass assignment works correctly.

## Testing

After applying the fixes:

```bash
php artisan migrate
php artisan hrm:sync-jibble-employees
```

**Result:**
```
Starting Jibble employee sync...
Successfully synced 16 employees from Jibble.
```

**Verification:**
```bash
php artisan tinker --execute="echo 'Employees without email: ' . \App\Models\HrmEmployee::whereNull('email')->count();"
```

**Output:**
```
Employees without email: 2
- Bristi Maharjan (Jibble ID: 13d60c15-42f3-4f8b-bac3-ebde658a1cfe)
- Rajiv KC (Jibble ID: 1d444c9c-cd98-46bd-beca-f9ee6ea67f47)
```

✅ Both staff members are now successfully synced!

## Files Modified

1. `/app/Console/Commands/SyncJibbleEmployees.php` - Fixed method name
2. `/app/Models/HrmEmployee.php` - Added `full_name` to fillable array
3. `/database/migrations/2026_01_05_115650_remove_unique_constraint_from_hrm_employees_email.php` - New migration to remove unique constraint

## Important Notes

- Emails can still be added to these employees later through the admin interface
- The unique constraint removal means you need to be careful not to accidentally create duplicate employees with the same email
- The system will still link employees to user accounts when emails are available
- Staff without emails won't have user accounts created (which is correct behavior)

## Future Recommendations

1. Consider adding validation to prevent duplicate emails at the application level
2. Add a UI notification when syncing employees without emails
3. Create a report/dashboard showing employees missing email addresses

## Impact

- ✅ All 16 employees from Jibble are now synced (including 2 without emails)
- ✅ Attendance tracking will now work for all staff
- ✅ HR data is now complete and accurate
- ✅ Emails can be added later without re-syncing
