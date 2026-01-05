# Nepali Date (Bikram Sambat) Converter - Implementation Guide

## Overview

We have successfully integrated a robust AD (Gregorian) to BS (Bikram Sambat) / Nepali date conversion system into the Laravel ERP application using the `anuzpandey/laravel-nepali-date` package.

## Package Information

-   **Package**: `anuzpandey/laravel-nepali-date` v2.3.2
-   **GitHub**: https://github.com/anuzpandey/laravel-nepali-date
-   **Installation Date**: December 5, 2025
-   **Dependencies**: spatie/laravel-package-tools

## Features Implemented

### 1. Core Service - `NepalCalendarService`

Located at: `app/Services/NepalCalendarService.php`

#### Methods Available:

-   ✅ `bsToAd(string $bsDate): ?Carbon` - Convert BS to AD
-   ✅ `adToBs($adDate): ?string` - Convert AD to BS
-   ✅ `formatBsDate(string $bsDate, string $format = 'j F Y', string $locale = 'en'): string` - Format BS dates
-   ✅ `getCurrentBsDate(): string` - Get current date in BS
-   ✅ `isValidBsDateFormat(string $bsDate): bool` - Validate BS date format
-   ✅ `getMonthName(int $month): string` - Get Nepali month name
-   ✅ `parseManualDates(string $bsDate, string $adDate): array` - Parse manual date entry

### 2. Helper Functions

Located at: `app/helpers.php`

Three convenient helper functions for global use:

```php
// Convert AD to BS or get current BS date
nepali_date($date = null, ?string $format = null, string $locale = 'en'): string

// Convert BS to AD
english_date(string $bsDate, ?string $format = null): Carbon|string|null

// Format a BS date
format_nepali_date(string $bsDate, string $format = 'j F Y', string $locale = 'en'): string
```

### 3. Package Built-in Helpers

The package also provides these global functions:

```php
// Convert English date to Nepali date
toNepaliDate(string $date): string

// Convert Nepali date to English date
toEnglishDate(string $date): string
```

## Usage Examples

### Basic Conversions

```php
// Convert AD to BS
$bsDate = $calendarService->adToBs('2024-04-13');
// Returns: "2081-01-01"

$bsDate = $calendarService->adToBs(Carbon::now());
// Returns current date in BS format

// Convert BS to AD
$adDate = $calendarService->bsToAd('2081-01-01');
// Returns: Carbon instance for 2024-04-13

$adDateString = $adDate->format('Y-m-d');
// Returns: "2024-04-13"
```

### Using Helper Functions

```php
// Get current date in BS
$today = nepali_date(); // "2081-08-20"

// Convert specific AD date to BS
$bs = nepali_date('2024-11-28'); // "2081-08-13"

// Convert and format in one go
$formatted = nepali_date('2024-11-28', 'j F Y', 'en');
// "13 Mangsir 2081"

$formattedNp = nepali_date('2024-11-28', 'j F Y', 'np');
// "१३ मंसिर २०८१"

// Convert BS to AD
$ad = english_date('2081-08-13'); // Carbon instance
$adString = english_date('2081-08-13', 'Y-m-d'); // "2024-11-28"

// Format BS date
$pretty = format_nepali_date('2081-08-13', 'D, j F Y', 'en');
// "Thu, 13 Mangsir 2081"
```

### Using Package Built-in Helpers

```php
// Simple conversions
$bs = toNepaliDate('2024-04-13'); // "2081-01-01"
$ad = toEnglishDate('2081-01-01'); // "2024-04-13"

// With formatting
$formatted = LaravelNepaliDate::from('2024-04-13')
    ->toNepaliDate(format: 'D, j F Y', locale: 'en');
// "Sat, 1 Baisakh 2081"

$nepali = LaravelNepaliDate::from('2081-01-01')
    ->toEnglishDate(format: 'l, j F Y', locale: 'np');
// "शनिबार, १३ अप्रिल २०२४"
```

### In Blade Templates

```blade
{{-- Display BS date --}}
<p>Nepali Date: {{ nepali_date(now()) }}</p>
<p>Formatted: {{ nepali_date(now(), 'j F Y', 'en') }}</p>

{{-- Convert and display --}}
<p>BS Date: {{ toNepaliDate('2024-04-13') }}</p>
<p>AD Date: {{ toEnglishDate('2081-01-01') }}</p>

{{-- In payroll context --}}
<p>Period: {{ format_nepali_date($payroll->period_start_bs) }} to
          {{ format_nepali_date($payroll->period_end_bs) }}</p>
```

### In Controllers (Payroll Example)

```php
use App\Services\NepalCalendarService;

class HrmPayrollController extends Controller
{
    protected NepalCalendarService $calendarService;

    public function store(Request $request)
    {
        // User enters BS dates
        $periodStartBs = $request->input('period_start_bs'); // "2081-08-01"
        $periodEndBs = $request->input('period_end_bs');     // "2081-08-30"

        // Convert to AD for database storage
        $periodStartAd = $this->calendarService->bsToAd($periodStartBs);
        $periodEndAd = $this->calendarService->bsToAd($periodEndBs);

        // Store both formats
        HrmPayrollRecord::create([
            'period_start_bs' => $periodStartBs,
            'period_end_bs' => $periodEndBs,
            'period_start_ad' => $periodStartAd,
            'period_end_ad' => $periodEndAd,
            // ... other fields
        ]);
    }

    public function show($id)
    {
        $payroll = HrmPayrollRecord::findOrFail($id);

        // Display formatted BS dates
        $payroll->formatted_period = sprintf(
            '%s to %s',
            $this->calendarService->formatBsDate($payroll->period_start_bs, 'j F Y'),
            $this->calendarService->formatBsDate($payroll->period_end_bs, 'j F Y')
        );

        return view('admin.hrm.payroll.show', compact('payroll'));
    }
}
```

## Format Specifiers

The package supports the following format specifiers:

| Specifier | Description          | Example (EN)   | Example (NP) |
| --------- | -------------------- | -------------- | ------------ |
| `Y`       | Year (4 digits)      | 2081           | २०८१         |
| `y`       | Year (2 digits)      | 81             | ८१           |
| `m`       | Month (2 digits)     | 08             | ०८           |
| `n`       | Month (1-2 digits)   | 8              | ८            |
| `M`       | Month (3 letters)    | Man            | मंसि         |
| `F`       | Month (full name)    | Mangsir        | मंसिर        |
| `d`       | Day (2 digits)       | 15             | १५           |
| `j`       | Day (1-2 digits)     | 15             | १५           |
| `D`       | Day name (3 letters) | Thu            | बिही         |
| `l`       | Day name (full)      | Thursday       | बिहीबार      |
| `S`       | Ordinal suffix       | th, st, nd, rd | -            |

## Nepali Months

| Number | English Name | Nepali Name | Days  |
| ------ | ------------ | ----------- | ----- |
| 1      | Baisakh      | बैशाख       | 30-31 |
| 2      | Jestha       | जेष्ठ       | 31-32 |
| 3      | Ashadh       | असार        | 31-32 |
| 4      | Shrawan      | श्रावण      | 31-32 |
| 5      | Bhadra       | भाद्र       | 31-32 |
| 6      | Ashwin       | आश्विन      | 30-31 |
| 7      | Kartik       | कार्तिक     | 29-30 |
| 8      | Mangsir      | मंसिर       | 29-30 |
| 9      | Poush        | पौष         | 29-30 |
| 10     | Magh         | माघ         | 29-30 |
| 11     | Falgun       | फाल्गुन     | 29-30 |
| 12     | Chaitra      | चैत्र       | 30-31 |

## Testing

Comprehensive test suite available at: `tests/Feature/NepaliDateConversionTest.php`

Run tests with:

```bash
php artisan test --filter NepaliDateConversionTest
```

### Test Coverage:

-   ✅ AD to BS conversion
-   ✅ BS to AD conversion
-   ✅ Carbon instance handling
-   ✅ Date formatting (English & Nepali)
-   ✅ Current BS date retrieval
-   ✅ Date format validation
-   ✅ Month name retrieval
-   ✅ Invalid date handling
-   ✅ Round-trip conversion accuracy

All tests passing: 10/10 ✓

## Integration Points in the ERP System

### 1. Payroll Module

-   Located: `app/Http/Controllers/Admin/HrmPayrollController.php`
-   Uses: `NepalCalendarService` for period date conversions
-   Database stores both BS and AD dates for accuracy

### 2. Attendance Module

-   Can be integrated for BS date display in attendance records

### 3. Leave Management

-   Can display leave dates in BS format for Nepali users

### 4. Reports & PDF Generation

-   Use formatted BS dates in payslips and official documents

## Database Schema Recommendations

When storing dates that need both BS and AD:

```php
Schema::create('hrm_payroll_records', function (Blueprint $table) {
    // Store both formats
    $table->string('period_start_bs'); // "2081-08-01"
    $table->string('period_end_bs');   // "2081-08-30"
    $table->date('period_start_ad');   // 2024-11-14
    $table->date('period_end_ad');     // 2024-12-13

    // Use AD for date comparisons and queries
    // Use BS for display to users
});
```

## Best Practices

1. **Always store AD dates in database** - For accurate date arithmetic and queries
2. **Store BS dates for display** - Show BS to users, especially in Nepal
3. **Use both formats in payroll** - Ensures accuracy and user-friendliness
4. **Validate BS dates** - Use `isValidBsDateFormat()` before conversion
5. **Handle errors gracefully** - All conversion methods return null on error
6. **Log conversion errors** - Service logs all errors automatically

## Error Handling

```php
// Example of safe conversion
$bsDate = $request->input('bs_date');

if (!$calendarService->isValidBsDateFormat($bsDate)) {
    return back()->withErrors(['bs_date' => 'Invalid Nepali date format']);
}

$adDate = $calendarService->bsToAd($bsDate);

if ($adDate === null) {
    return back()->withErrors(['bs_date' => 'Cannot convert date']);
}

// Proceed with valid date
```

## Common Use Cases

### Payroll Period Selection

```php
// Display current BS date as default
$currentBs = nepali_date();
// User selects BS dates for period
// Convert to AD for calculations
// Store both formats
```

### Report Generation

```php
// Convert report date range to BS for display
$reportTitle = sprintf(
    'Payroll Report: %s to %s',
    format_nepali_date($startBs, 'j F Y'),
    format_nepali_date($endBs, 'j F Y')
);
```

### Date Input Forms

```blade
{{-- Allow users to input BS dates --}}
<input type="text"
       name="period_start_bs"
       placeholder="YYYY-MM-DD (e.g., 2081-08-01)"
       value="{{ old('period_start_bs', nepali_date()) }}">

{{-- Display corresponding AD date --}}
@if($periodStartBs)
    <small>AD: {{ english_date($periodStartBs, 'd M Y') }}</small>
@endif
```

## Troubleshooting

### Package not found

```bash
composer require anuzpandey/laravel-nepali-date
composer dump-autoload
```

### Helpers not working

Ensure `app/helpers.php` is in composer.json autoload files section.

### Conversion returns null

-   Check date format (must be YYYY-MM-DD)
-   Verify date is within supported range
-   Check logs for specific error

## Additional Resources

-   Package Documentation: https://github.com/anuzpandey/laravel-nepali-date
-   Nepali Calendar Info: https://en.wikipedia.org/wiki/Bikram_Sambat
-   Date Range: Package supports BS years 2000-2200

## Version History

-   **v1.0** (Dec 5, 2025) - Initial implementation
    -   Installed anuzpandey/laravel-nepali-date package
    -   Updated NepalCalendarService with full functionality
    -   Created helper functions
    -   Added comprehensive test suite
    -   Documentation completed

## Future Enhancements

-   [ ] Add Blade components for date pickers
-   [ ] Create Vue/React components for frontend
-   [ ] Add API endpoints for date conversion
-   [ ] Implement date range validation helpers
-   [ ] Add more localization options
