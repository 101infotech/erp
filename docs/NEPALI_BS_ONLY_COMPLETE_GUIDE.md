# Nepali Calendar (BS) Complete Implementation Guide

## Overview

The entire ERP system now operates exclusively in the Nepali Calendar (Bikram Sambat - BS). All dates are stored, processed, and displayed in BS format, eliminating the need for dual calendar support.

**Implementation Date**: December 5, 2025  
**Status**: ✅ Complete

---

## Architecture

### Database Storage

All date fields are stored as **strings in YYYY-MM-DD format (BS)**:

```sql
-- Example: hrm_employees table
hire_date VARCHAR(10)           -- "2081-04-15"
date_of_birth VARCHAR(10)       -- "2055-12-10"
contract_start_date VARCHAR(10) -- "2081-01-01"
contract_end_date VARCHAR(10)   -- "2082-12-30"
```

### Benefits

-   ✅ Native BS date storage and operations
-   ✅ No conversion overhead for display
-   ✅ Simplified application logic
-   ✅ Better performance for BS date queries
-   ✅ Accurate BS date calculations

---

## Core Components

### 1. Nepal Calendar Service

**Location**: `app/Services/NepalCalendarService.php`

**Key Methods**:

```php
// Convert AD to BS
$bsDate = $service->adToBs('2024-04-13'); // "2081-01-01"

// Convert BS to AD (for external integrations)
$adCarbon = $service->bsToAd('2081-01-01'); // Carbon instance

// Format BS date
$formatted = $service->formatBsDate('2081-08-15', 'j F Y'); // "15 Mangsir 2081"

// Get current BS date
$today = $service->getCurrentBsDate(); // "2082-08-19"

// Calculate working days
$days = $service->calculateWorkingDays('2081-01-01', '2081-01-15'); // 13

// Compare dates
$isAfter = $service->isAfter('2081-05-10', '2081-05-01'); // true
```

### 2. Helper Functions

**Location**: `app/helpers.php`

```php
// Get current BS date
nepali_date(); // "2082-08-19"

// Convert and format
nepali_date('2024-11-28', 'j F Y', 'en'); // "13 Mangsir 2081"

// Convert BS to AD
english_date('2081-08-13'); // Carbon instance
english_date('2081-08-13', 'Y-m-d'); // "2024-11-28"

// Format BS date
format_nepali_date('2081-08-13', 'j F Y'); // "13 Mangsir 2081"
```

### 3. Validation Rule

**Location**: `app/Rules/NepaliDate.php`

```php
use App\Rules\NepaliDate;

// Basic validation
'start_date' => ['required', new NepaliDate()],

// With comparison
'start_date' => ['required', (new NepaliDate())->afterOrEqual(nepali_date())],
'end_date' => ['required', (new NepaliDate())->afterOrEqual($request->start_date)],
```

### 4. Blade Component

**Location**: `resources/views/components/nepali-date-picker.blade.php`

```blade
<!-- Basic usage -->
<x-nepali-date-picker name="hire_date" />

<!-- With value -->
<x-nepali-date-picker
    name="start_date"
    :value="old('start_date', $employee->hire_date)"
    required
/>

<!-- With custom class -->
<x-nepali-date-picker
    name="date_of_birth"
    :value="$employee->date_of_birth"
    class="bg-slate-900 text-white"
    placeholder="YYYY-MM-DD (BS)"
/>
```

**Features**:

-   ✅ Beautiful Nepali calendar picker
-   ✅ Click to select dates
-   ✅ Dark mode support
-   ✅ BS date validation
-   ✅ Keyboard accessible
-   ✅ Mobile responsive

---

## Database Migration

### Migration File

**Location**: `database/migrations/2025_12_05_181515_convert_all_dates_to_nepali_bs_format.php`

**What it does**:

1. Creates new BS date columns (string type)
2. Converts all existing AD dates to BS
3. Drops old AD date columns
4. Renames BS columns to original names

**Tables Updated**:

-   `hrm_employees` - hire_date, date_of_birth, contract dates, probation_end_date
-   `hrm_leave_requests` - start_date, end_date
-   `hrm_attendance_days` - date
-   `hrm_time_entries` - belongs_to_date
-   `hrm_attendance_anomalies` - date
-   `hrm_payroll_records` - already uses period_start_bs, period_end_bs

**Run Migration**:

```bash
php artisan migrate
```

**Rollback** (if needed):

```bash
php artisan migrate:rollback
```

---

## Model Updates

All date casts have been removed from models. Dates are now plain strings.

### HrmEmployee Model

```php
// REMOVED
protected $casts = [
    'hire_date' => 'date',  // ❌
    'date_of_birth' => 'date',  // ❌
];

// ADDED
public function getHireDateFormattedAttribute(): ?string
{
    return $this->hire_date ? format_nepali_date($this->hire_date, 'j F Y') : null;
}
```

**Access formatted dates**:

```blade
{{ $employee->hire_date }}  <!-- 2081-04-15 -->
{{ $employee->hire_date_formatted }}  <!-- 15 Shrawan 2081 -->
```

---

## Controller Updates

### Validation

**Before** (AD dates):

```php
'start_date' => 'required|date|after_or_equal:today',
```

**After** (BS dates):

```php
use App\Rules\NepaliDate;

'start_date' => ['required', (new NepaliDate())->afterOrEqual(nepali_date())],
'end_date' => ['required', (new NepaliDate())->afterOrEqual($request->start_date)],
```

### Date Calculations

**Before**:

```php
$startDate = Carbon::parse($request->start_date);
$endDate = Carbon::parse($request->end_date);
$totalDays = $startDate->diffInDays($endDate) + 1;
```

**After**:

```php
$calendarService = app(NepalCalendarService::class);
$workDays = $calendarService->calculateWorkingDays(
    $request->start_date,
    $request->end_date
);
```

---

## View Updates

### Date Display

**Before**:

```blade
{{ $employee->hire_date->format('M d, Y') }}
```

**After**:

```blade
{{ format_nepali_date($employee->hire_date, 'j F Y') }}
<!-- Output: 15 Shrawan 2081 -->
```

### Date Input

**Before**:

```blade
<input type="date" name="start_date" value="{{ old('start_date') }}">
```

**After**:

```blade
<x-nepali-date-picker
    name="start_date"
    :value="old('start_date')"
    required
/>
```

---

## Format Codes

| Code | Output                   | Example           |
| ---- | ------------------------ | ----------------- |
| `j`  | Day without leading zero | 1, 15, 30         |
| `d`  | Day with leading zero    | 01, 15, 30        |
| `F`  | Full month name          | Baishakh, Mangsir |
| `M`  | Short month name         | Bai, Man          |
| `m`  | Month with leading zero  | 01, 08, 12        |
| `Y`  | 4-digit year             | 2081, 2082        |
| `y`  | 2-digit year             | 81, 82            |
| `l`  | Full day name            | Thursday          |
| `D`  | Short day name           | Thu               |

**Common Formats**:

```php
format_nepali_date($date, 'j F Y')      // 15 Mangsir 2081
format_nepali_date($date, 'D, j F Y')   // Thu, 15 Mangsir 2081
format_nepali_date($date, 'Y-m-d')      // 2081-08-15
```

---

## Testing

### Test Command

```bash
php artisan nepali:test-dates
```

This validates:

-   ✅ Current date conversion
-   ✅ Known date accuracy
-   ✅ Reverse conversion
-   ✅ Month names
-   ✅ Date validation
-   ✅ Format options
-   ✅ Localization

### Manual Testing Checklist

-   [ ] Employee creation with hire date
-   [ ] Leave request submission
-   [ ] Date range filtering
-   [ ] Payroll period selection
-   [ ] Contract date management
-   [ ] Attendance date display
-   [ ] PDF generation with BS dates
-   [ ] API responses with BS dates

---

## External Integrations

### Jibble Sync

Jibble provides dates in AD format. Convert on sync:

```php
use App\Services\NepalCalendarService;

$calendarService = app(NepalCalendarService::class);

// Jibble provides AD date
$jibbleDate = '2024-12-05'; // AD

// Convert to BS for storage
$bsDate = $calendarService->adToBs($jibbleDate);

HrmAttendanceDay::create([
    'date' => $bsDate, // Store as BS
    'employee_id' => $employeeId,
    // ...
]);
```

---

## Migration Checklist

-   [x] Install `anuzpandey/laravel-nepali-date` package
-   [x] Create NepalCalendarService
-   [x] Create helper functions
-   [x] Create NepaliDate validation rule
-   [x] Create Nepali date picker component
-   [x] Create database migration
-   [x] Update all models (remove date casts)
-   [x] Update controllers (validation & calculations)
-   [ ] Update all Blade views (inputs & displays)
-   [ ] Run migration
-   [ ] Test all date features
-   [ ] Update documentation

---

## Troubleshooting

### Date Picker Not Working

**Issue**: Nepali date picker doesn't appear

**Solution**:

1. Ensure the component is used correctly:

```blade
<x-nepali-date-picker name="date" />
```

2. Check browser console for JavaScript errors
3. Verify CDN is accessible:
    - https://cdn.jsdelivr.net/npm/nepali-date-picker@2.0.3/dist/nepaliDatePicker.min.js
    - https://cdn.jsdelivr.net/npm/nepali-date-picker@2.0.3/dist/nepaliDatePicker.min.css

### Date Validation Failing

**Issue**: BS date fails validation

**Solution**:

```php
use App\Rules\NepaliDate;

// Ensure rule is used
'date' => ['required', new NepaliDate()],

// Check date format is YYYY-MM-DD
'2081-08-15' // ✅ Valid
'2081/08/15' // ❌ Invalid
'08-15-2081' // ❌ Invalid
```

### Date Display Issues

**Issue**: Dates show as strings instead of formatted

**Solution**:

```blade
<!-- Use format helper -->
{{ format_nepali_date($date, 'j F Y') }}

<!-- Not just the raw value -->
{{ $date }} <!-- Shows: 2081-08-15 -->
```

---

## Best Practices

### 1. Always Use Helper Functions

```php
// ✅ Good
$today = nepali_date();

// ❌ Bad
$today = date('Y-m-d'); // This is AD date!
```

### 2. Store Only BS Dates

```php
// ✅ Good
Employee::create([
    'hire_date' => '2081-04-15', // BS
]);

// ❌ Bad
Employee::create([
    'hire_date' => now(), // Carbon AD date
]);
```

### 3. Use Service for Calculations

```php
// ✅ Good
$service = app(NepalCalendarService::class);
$days = $service->calculateWorkingDays($start, $end);

// ❌ Bad
$start = Carbon::parse($startBs); // Won't work with BS string
```

### 4. Consistent Formatting

```php
// ✅ Good - readable format for display
format_nepali_date($date, 'j F Y') // 15 Mangsir 2081

// ✅ Good - standard format for storage/API
$date = '2081-08-15'
```

---

## Package Information

**Package**: `anuzpandey/laravel-nepali-date`  
**Version**: ^2.3  
**GitHub**: https://github.com/anuzpandey/laravel-nepali-date  
**License**: MIT

---

## Support

For issues or questions:

1. Check this documentation
2. Review `docs/NEPALI_DATE_QUICK_REF.md` for quick examples
3. Test with `php artisan nepali:test-dates`
4. Check package documentation: https://github.com/anuzpandey/laravel-nepali-date

---

**Last Updated**: December 5, 2025  
**Maintained By**: Development Team
