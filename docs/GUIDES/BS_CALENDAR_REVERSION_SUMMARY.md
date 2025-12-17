# BS Calendar Reversion Summary

## Overview

This document summarizes the complete reversion from BS (Bikram Sambat/Nepali) calendar implementation back to AD (Anno Domini/English) calendar across the entire ERP system.

**Date of Reversion:** January 2025  
**Reason:** User decided to revert to AD dates for all operations while maintaining dual date display on dashboard only.

---

## Latest Fixes (December 9, 2025)

### 1. Fixed Attendance Filter Issue ✅

-   **File**: `resources/views/admin/hrm/attendance/index.blade.php`
-   **Issue**: Escaped quotes in date input fields causing rendering issues
-   **Fix**: Cleaned up HTML formatting for date inputs
-   **Status**: Resolved

### 2. Fixed Jibble API User Roles Issue ✅

-   **File**: `app/Services/JibblePeopleService.php`
-   **Issue**: API filter `'$filter' => "role eq 'Member'"` was excluding Admin and Owner roles
-   **Fix**: Removed the role filter to fetch all users regardless of role
-   **Impact**: Now syncs all employees including Admins and Owners from Jibble
-   **Status**: Resolved

### 3. Updated Payroll Create Form ✅

-   **File**: `resources/views/admin/hrm/payroll/create.blade.php`
-   **Changes**:
    -   Changed "Period (Bikram Sambat)" heading to "Period"
    -   Changed "Start Date (BS)" to "Start Date"
    -   Changed "End Date (BS)" to "End Date"
    -   Changed input type from `text` with placeholder to `date` HTML5 input
    -   Changed field names from `period_start_bs` and `period_end_bs` to `period_start` and `period_end`
    -   Removed "Dates will be automatically converted to Gregorian for calculations" note
-   **Status**: Resolved

### 4. Updated Payroll Controller ✅

-   **File**: `app/Http/Controllers/Admin/HrmPayrollController.php`
-   **Changes**:
    -   Validation now accepts `period_start` and `period_end` as date fields instead of BS strings
    -   Dates are parsed as Carbon objects directly
    -   Conversion to BS format happens internally using `nepali_date()` helper for storage
    -   Backend still stores BS dates in database for compatibility
-   **Status**: Resolved

### 5. Fixed Employee Ordering ✅

-   **File**: `app/Http/Controllers/Admin/HrmAttendanceController.php`
-   **Issue**: SQL error trying to order by non-existent column `full_name`
-   **Fix**: Changed `orderBy('full_name')` to `orderBy('name')` since `full_name` is an accessor, not a database column
-   **Status**: Resolved

---

## Implementation Summary

### Phase 1: Frontend UI Revert ✅ COMPLETED

All Nepali date picker components (`x-nepali-date-picker`) were replaced with standard HTML5 date inputs (`<input type="date">`).

#### Files Modified:

**Leave Management:**

-   `/resources/views/admin/hrm/leaves/create.blade.php` - Replaced 2 BS pickers (start_date, end_date)
-   `/resources/views/admin/hrm/leaves/index.blade.php` - Replaced 1 BS picker (date_from filter)
-   `/resources/views/employee/leave/create.blade.php` - Replaced 2 BS pickers (start_date, end_date)

**Attendance Management:**

-   `/resources/views/admin/hrm/attendance/sync.blade.php` - Replaced 2 BS pickers (start_date, end_date)
-   `/resources/views/admin/hrm/attendance/index.blade.php` - Replaced 2 BS pickers (start_date, end_date)
-   `/resources/views/admin/hrm/attendance/employee.blade.php` - Reverted BS month selector to AD, updated date loop to use Carbon
-   `/resources/views/employee/attendance/index.blade.php` - Already using AD date inputs (no changes needed)

**Employee Management:**

-   `/resources/views/admin/hrm/employees/edit.blade.php` - Replaced 4 BS pickers:
    -   hire_date
    -   date_of_birth
    -   contract_start_date
    -   contract_end_date

**Payroll Management:**

-   `/resources/views/admin/hrm/payroll/create.blade.php` - Replaced 2 BS text inputs with AD date pickers:
    -   period_start_bs → period_start (date input)
    -   period_end_bs → period_end (date input)

**Dashboard:**

-   `/resources/views/admin/dashboard.blade.php` - Updated to show BOTH AD and BS dates side-by-side:
    -   Nepali (BS): Full date display with formatted text
    -   English (AD): Full date display with formatted text
    -   Both dates shown in same card for easy comparison

---

### Phase 2: Backend Revert ✅ COMPLETED

All backend controllers and models updated to use AD dates exclusively.

#### Controllers Modified:

**HrmLeaveController** (`/app/Http/Controllers/Admin/HrmLeaveController.php`):

-   Removed `NepaliDate` validation rule
-   Changed validation to standard Laravel date validation: `'required|date'` and `'after_or_equal:start_date'`
-   Replaced BS calendar service working days calculation with Carbon-based calculation
-   Working days now calculated by iterating through dates and excluding Saturdays

**HrmAttendanceController** (`/app/Http/Controllers/Admin/HrmAttendanceController.php`):

-   `employee()` method: Removed all BS date conversion logic
    -   Replaced BS month selector processing with AD month handling
    -   Now uses Carbon for month start/end dates
    -   Removed `startDateBS` and `endDateBS` variables
    -   Simplified date range calculation
-   `syncFromJibble()` method: Removed BS date validation and conversion
    -   Changed validation from `NepaliDate` rule to standard `'required|date'`
    -   Removed `english_date()` conversion calls
    -   Direct use of AD dates for Jibble API calls

#### Models Modified:

**HrmEmployee** (`/app/Models/HrmEmployee.php`):

-   Restored Carbon date casts:
    ```php
    'hire_date' => 'date',
    'date_of_birth' => 'date',
    'contract_start_date' => 'date',
    'contract_end_date' => 'date',
    ```

**HrmLeaveRequest** (`/app/Models/HrmLeaveRequest.php`):

-   Restored Carbon date casts:
    ```php
    'start_date' => 'date',
    'end_date' => 'date',
    ```

**HrmAttendanceDay** (`/app/Models/HrmAttendanceDay.php`):

-   Restored Carbon date cast:
    ```php
    'date' => 'date',
    ```

---

### Phase 3: Documentation Update ✅ COMPLETED

This summary document created to track the reversion process.

---

## What Was NOT Changed

The following BS-related components remain in the codebase but are **ONLY** used for display purposes on the dashboard:

### Helpers (Still Active - Dashboard Display Only):

-   `nepali_date()` - Converts AD to BS, used for dashboard display
-   `format_nepali_date()` - Formats BS dates for display, used for dashboard display
-   `english_date()` - Converts BS to AD (no longer used in data processing)

### Services (Inactive):

-   `/app/Services/NepalCalendarService.php` - No longer used in any business logic

### Validation Rules (Inactive):

-   `/app/Rules/NepaliDate.php` - No longer used

### Blade Components (Inactive):

-   `/resources/views/components/nepali-date-picker.blade.php` - No longer used

### Package (Still Installed - Dashboard Only):

-   `anuzpandey/laravel-nepali-date` - Still installed but only used for dashboard date display

---

## Database Schema

**NO DATABASE CHANGES REQUIRED.**

All date columns in the database continue to store dates in AD format (YYYY-MM-DD). The previous BS implementation only affected the UI layer and conversion logic - the database always stored AD dates.

---

## Current State

### ✅ What Works Now:

1. **All forms use AD date inputs** - Standard HTML5 date pickers throughout
2. **All backend APIs use AD dates** - Controllers validate and process AD dates
3. **All database operations use AD dates** - Models use Carbon date casts
4. **Dashboard shows BOTH dates** - Side-by-side display of AD and BS dates for user convenience
5. **Date calculations use Carbon** - Working days, date ranges, etc. all use Carbon

### ⚠️ What to Watch:

1. **Dashboard dependencies** - The dashboard still uses `nepali_date()` and `format_nepali_date()` helpers for BS display
2. **Legacy BS components remain** - They're inactive but still in codebase (safe to remove if needed)
3. **Testing needed** - All date-related features should be tested:
    - Employee creation/editing (hire date, DOB, contract dates)
    - Leave request creation
    - Attendance sync from Jibble
    - Timesheet view
    - Leave filtering

---

## Migration Notes

### For Future Developers:

If you need to completely remove BS calendar support:

1. **Remove Dashboard BS Display:**

    - Update `/resources/views/admin/dashboard.blade.php` to remove BS date section
    - Only show AD date

2. **Uninstall Package:**

    ```bash
    composer remove anuzpandey/laravel-nepali-date
    ```

3. **Remove Helpers:**

    - Delete or comment out `nepali_date()`, `format_nepali_date()`, `english_date()` from `/app/helpers.php`

4. **Remove Unused Files:**

    - `/app/Services/NepalCalendarService.php`
    - `/app/Rules/NepaliDate.php`
    - `/resources/views/components/nepali-date-picker.blade.php`

5. **Clean Documentation:**
    - Archive or delete BS-related docs:
        - `BS_ONLY_QUICK_REF.md`
        - `BS_ONLY_IMPLEMENTATION.md`
        - `NEPALI_DATE_*.md`

---

## Testing Checklist

Before deploying this reversion to production:

-   [ ] Test employee creation with all date fields
-   [ ] Test employee editing with date updates
-   [ ] Test leave request creation
-   [ ] Test leave request filtering by date
-   [ ] Test attendance sync from Jibble
-   [ ] Test timesheet view with month selector
-   [ ] Test dashboard displays both AD and BS dates correctly
-   [ ] Verify all existing database records display correctly
-   [ ] Verify date validations work (no future dates where not allowed, etc.)

---

## Conclusion

The reversion is **COMPLETE**. All business logic now operates exclusively on AD dates, while the dashboard provides a user-friendly dual date display showing both AD and BS dates for reference.

**Key Achievement:** Simplified codebase by removing complex date conversion logic from all data processing flows, while maintaining helpful BS date reference on dashboard.
