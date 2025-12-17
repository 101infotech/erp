# 🗓️ Nepali Calendar (BS-Only) Quick Reference

## ✅ COMPLETE: Full Nepali Calendar Implementation with UI

**All dates in the system now operate exclusively in Bikram Sambat (BS) format with a Nepali calendar picker for all date inputs.**

---

## 🎨 **NEW: Nepali Calendar Picker UI**

### In Blade Forms

All date inputs now use the custom Nepali date picker component:

```blade
<x-nepali-date-picker
    name="start_date"
    :value="old('start_date')"
    required
    placeholder="YYYY-MM-DD (BS)"
/>
```

### Features:

-   ✅ **Visual BS Calendar Selector** - Click to pick dates from Nepali calendar
-   ✅ **No AD Dates in UI** - Only BS dates visible to users
-   ✅ **Dark Mode Styled** - Matches your app's dark theme
-   ✅ **Today Highlight** - Current BS date highlighted
-   ✅ **Month/Year Navigation** - Easy navigation through BS calendar

### Forms Updated:

-   ✅ Leave Request Form (create)
-   ✅ Attendance Sync Form
-   ✅ Attendance Index Filter
-   ✅ Leave Index Filter
-   ✅ Employee Attendance Portal

---

## 📋 Quick Usage

### Current BS Date

```php
nepali_date()  // "2082-08-19"
```

### Format BS Date

```php
format_nepali_date('2081-08-15', 'j F Y')  // "15 Mangsir 2081"
```

### Convert BS to AD (for external APIs)

```php
$adCarbon = english_date('2081-08-15');
```

### In Blade Views

```blade
<!-- Display -->
{{ format_nepali_date($employee->hire_date, 'j F Y') }}

<!-- Input -->
<x-nepali-date-picker name="start_date" :value="old('start_date')" required />
```

### Validation

```php
use App\Rules\NepaliDate;

'start_date' => ['required', new NepaliDate()],
'end_date' => ['required', (new NepaliDate())->afterOrEqual($request->start_date)],
```

### Calculate Working Days

```php
$service = app(NepalCalendarService::class);
$days = $service->calculateWorkingDays('2081-01-01', '2081-01-15');  // 13
```

---

## 📁 Key File Locations

| Component       | Location                                                                     |
| --------------- | ---------------------------------------------------------------------------- |
| **Service**     | `app/Services/NepalCalendarService.php`                                      |
| **Helpers**     | `app/helpers.php`                                                            |
| **Validation**  | `app/Rules/NepaliDate.php`                                                   |
| **Date Picker** | `resources/views/components/nepali-date-picker.blade.php`                    |
| **Migration**   | `database/migrations/2025_12_05_*_convert_all_dates_to_nepali_bs_format.php` |
| **Full Docs**   | `docs/NEPALI_BS_ONLY_COMPLETE_GUIDE.md`                                      |

---

## 🔧 Common Format Codes

```php
'j F Y'      // 15 Mangsir 2081
'D, j F Y'   // Thu, 15 Mangsir 2081
'Y-m-d'      // 2081-08-15
'l, j F Y'   // Thursday, 15 Mangsir 2081
```

---

## ⚡ Migration Commands

```bash
# Run migration (convert AD to BS)
php artisan migrate

# Test Nepali dates
php artisan nepali:test-dates

# Rollback if needed
php artisan migrate:rollback
```

---

## 🎯 Key Points

1. ✅ **All dates stored as BS strings** in `YYYY-MM-DD` format
2. ✅ **No dual calendar** - BS only throughout
3. ✅ **Nepali calendar picker** for all date inputs (no AD calendar in UI)
4. ✅ **Format helper** for all date displays
5. ✅ **Custom validation** for BS dates
6. ✅ **Service methods** for date calculations
7. ✅ **Backend converts BS to AD** when calling external APIs (Jibble, etc.)

---

## 🔄 Backend Date Handling

### Controllers Automatically Handle BS Dates

When BS dates come from forms, controllers:

1. **Validate** using `NepaliDate` rule
2. **Calculate** using `NepalCalendarService` methods
3. **Convert to AD** only when calling external APIs (Jibble)
4. **Store as BS** in database

Example:

```php
// Validation
$validated = $request->validate([
    'start_date' => ['required', new \App\Rules\NepaliDate()],
    'end_date' => ['required', (new \App\Rules\NepaliDate())->afterOrEqual($request->start_date)],
]);

// Calculate working days (BS dates)
$calendarService = new \App\Services\NepalCalendarService();
$workDays = $calendarService->calculateWorkingDays($validated['start_date'], $validated['end_date']);

// Convert to AD only for external API
$startDateAD = english_date($validated['start_date'])->format('Y-m-d');
$this->jibbleService->sync($startDateAD, ...);
```

---

## 🚨 Remember

-   Always use `nepali_date()` not `now()` or `date()`
-   Always use `<x-nepali-date-picker>` not `<input type="date">`
-   Always use `format_nepali_date()` for display
-   Always use `new NepaliDate()` rule for validation
-   Dates are strings - no Carbon casts in models
-   UI shows only BS dates - AD conversion is backend-only

---

---

## Display Formats

### Raw

```blade
{{ $payroll->period_start_bs }}
```

Output: `2081-08-01`

### Formatted

```blade
{{ format_nepali_date($payroll->period_start_bs, 'j M Y') }}
```

Output: `1 Mangsir 2081`

### Full Format

```blade
{{ format_nepali_date($payroll->period_start_bs, 'l, j F Y') }}
```

Output: `Thursday, 1 Mangsir 2081`

### Date Range

```blade
{{ format_nepali_date($payroll->period_start_bs, 'j M Y') }} -
{{ format_nepali_date($payroll->period_end_bs, 'j M Y') }}
```

Output: `1 Mangsir 2081 - 30 Mangsir 2081`

---

## Database

### Payroll Table

```sql
period_start_bs VARCHAR(20) -- "2081-08-01"
period_end_bs VARCHAR(20)   -- "2081-08-30"
```

### Indexes

```sql
UNIQUE (employee_id, period_start_bs, period_end_bs)
INDEX (period_start_bs, period_end_bs)
INDEX (status, period_start_bs)
```

---

## Controller Validation

### Old ❌

```php
'period_start_ad' => 'required|date',
'period_end_ad' => 'required|date',
'period_start_bs' => 'required|string',
'period_end_bs' => 'required|string',
```

### New ✅

```php
'period_start_bs' => 'required|string',
'period_end_bs' => 'required|string',
```

---

## Date Conversion

### BS to AD

```php
$ad = english_date('2081-08-01');
// Returns: Carbon instance for 2024-11-14

$adString = english_date('2081-08-01', 'Y-m-d');
// Returns: "2024-11-14"
```

### AD to BS

```php
$bs = nepali_date('2024-11-14');
// Returns: "2081-08-01"

$formatted = nepali_date('2024-11-14', 'j F Y');
// Returns: "1 Mangsir 2081"
```

---

## Common Patterns

### Get Current Month Period (BS)

```php
$todayBs = nepali_date();
[$year, $month] = explode('-', $todayBs);

$periodStart = "{$year}-{$month}-01";
$periodEnd = "{$year}-{$month}-30"; // Adjust for month
```

### Calculate Days in Period

```php
$startAd = english_date($payroll->period_start_bs);
$endAd = english_date($payroll->period_end_bs);

$days = $startAd->diffInDays($endAd) + 1;
```

### Query Attendance for Payroll Period

```php
$startAd = english_date($payroll->period_start_bs);
$endAd = english_date($payroll->period_end_bs);

$attendance = Attendance::where('employee_id', $payroll->employee_id)
    ->whereBetween('date', [$startAd, $endAd])
    ->get();
```

---

## Files Changed

### Database

-   ✅ `2025_12_03_141443_create_hrm_payroll_records_table.php`

### Backend

-   ✅ `app/Models/HrmPayrollRecord.php`
-   ✅ `app/Http/Controllers/Admin/HrmPayrollController.php`

### Frontend

-   ✅ `resources/views/admin/hrm/payroll/create.blade.php`
-   ✅ `resources/views/admin/hrm/payroll/index.blade.php`
-   ✅ `resources/views/admin/hrm/payroll/show.blade.php`

---

## Testing

```bash
# Check date conversion
php artisan tinker
>>> english_date('2081-08-01')->format('Y-m-d');
// "2024-11-14"

>>> nepali_date('2024-11-14');
// "2081-08-01"

# Test payroll creation
# Navigate to: /admin/hrm/payroll/create
# Enter BS dates only
# Verify it saves correctly
```

---

## Migration

```bash
# Fresh start (if no data)
php artisan migrate:fresh

# With data (backup first!)
# 1. Export existing payroll records
# 2. Convert AD dates to BS
# 3. Run migration
# 4. Import converted data
```

---

## Status

-   ✅ Database migrated
-   ✅ Model updated
-   ✅ Controller updated
-   ✅ Views updated
-   ✅ No errors
-   ✅ Ready for testing

---

**Date**: December 5, 2025  
**System**: BS (Bikram Sambat) Only  
**AD Support**: Internal conversion only
