# Jibble Sync Multi-Day Fix - December 9, 2025

## Problem Summary

Jibble attendance sync was only syncing December 1st data (10 employee records) when requesting full date range (Dec 1-9), despite API returning all 9 days of data. Expected ~70+ records across 9 days, but only got 10 records from one day.

## Root Cause

**Broken unique key constraint** on `hrm_attendance_days` table:

-   The unique key `hrm_attendance_days_employee_id_date_unique` only contained `employee_id` column
-   Should have contained both `(employee_id, date)` columns
-   This meant each employee could only have ONE attendance record total, not one per day
-   Every attempt to insert a second date for the same employee failed with "Duplicate entry" error

## Investigation Steps

1. Initial hypothesis: Date column varchar incompatibility → **Ruled out**
2. Checked API response: Confirmed API returns all 9 days → **API working correctly**
3. Checked sync logic: Loop processes all dates correctly → **Logic working correctly**
4. Checked duration parsing: PT8H30M format converts to 8.5h correctly → **Parsing working correctly**
5. Examined database errors: "Duplicate entry '2' for key hrm_attendance_days_employee_id_date_unique"
    - Error showed only ONE value ('2' for employee_id) not two values
    - This revealed the unique key was only on employee_id, not (employee_id, date)

## Fixes Applied

### 1. Changed date column from varchar to proper DATE type

**Migration:** `2025_12_09_130134_change_date_column_to_date_type_in_hrm_attendance_days.php`

```php
Schema::table('hrm_attendance_days', function (Blueprint $table) {
    $table->date('date')->change();
});
```

### 2. Restored date cast in model

**File:** `app/Models/HrmAttendanceDay.php`

```php
protected $casts = [
    'date' => 'date',  // Added back
    'tracked_hours' => 'decimal:2',
    'payroll_hours' => 'decimal:2',
    'overtime_hours' => 'decimal:2',
    'jibble_data' => 'array',
];
```

### 3. Fixed unique key constraint

**Migration:** `2025_12_09_130410_fix_unique_key_on_hrm_attendance_days.php`

```php
Schema::table('hrm_attendance_days', function (Blueprint $table) {
    // Drop the broken unique key (only has employee_id)
    $table->dropUnique('hrm_attendance_days_employee_id_date_unique');

    // Add the correct composite unique key
    $table->unique(['employee_id', 'date'], 'hrm_attendance_days_employee_id_date_unique');
});
```

### 4. Improved sync reliability with transactions

**File:** `app/Services/JibbleTimesheetService.php`

```php
// Use DB transaction to avoid race conditions
DB::transaction(function () use ($employee, $dateString, $trackedHours, $regularHours, $overtimeHours, $day) {
    $existing = HrmAttendanceDay::where('employee_id', $employee->id)
        ->where('date', $dateString)
        ->lockForUpdate()
        ->first();

    if ($existing) {
        $existing->update([...]);
    } else {
        HrmAttendanceDay::create([...]);
    }
});
```

## Verification

After fixes, syncing Dec 1-9:

```
Synced: 70 records
2025-12-01: 10
2025-12-02: 9
2025-12-03: 10
2025-12-04: 8
2025-12-05: 8
2025-12-07: 8  (Dec 6 skipped - weekend/no attendance)
2025-12-08: 7
2025-12-09: 10
Total: 70 records ✅
```

## Key Learnings

1. **Always verify database constraints match migration intentions** - The migration SAID it was creating a composite unique key, but the actual database only had the first column
2. **Database schema errors can masquerade as application logic errors** - We spent time debugging sync logic when the real problem was database schema
3. **MySQL error messages provide clues** - "Duplicate entry '2'" (single value) vs "Duplicate entry '2-2025-12-02'" (composite) would have revealed the issue sooner
4. **Use proper data types** - Changed from varchar(10) to DATE type for better type safety and query performance

## Files Modified

-   `app/Services/JibbleTimesheetService.php` - Improved transaction handling, removed debug logs
-   `app/Models/HrmAttendanceDay.php` - Restored date cast
-   `database/migrations/2025_12_09_130134_change_date_column_to_date_type_in_hrm_attendance_days.php` - Changed column type
-   `database/migrations/2025_12_09_130410_fix_unique_key_on_hrm_attendance_days.php` - Fixed unique constraint

## Status

✅ **RESOLVED** - Full date range sync now working correctly
✅ Monthly filter already exists in UI
✅ All December 1-9 attendance data syncs successfully
✅ View errors fixed (Carbon::parse wrapper applied earlier)
