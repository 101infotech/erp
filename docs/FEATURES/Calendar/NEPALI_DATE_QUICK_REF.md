# Nepali Date Converter - Quick Reference

## Installation ✅ COMPLETE

```bash
composer require anuzpandey/laravel-nepali-date
```

## Quick Examples

### Convert Dates

```php
// AD to BS
$bs = toNepaliDate('2024-04-13'); // "2081-01-01"

// BS to AD
$ad = toEnglishDate('2081-01-01'); // "2024-04-13"

// Using service
$calendarService->adToBs('2024-04-13'); // "2081-01-01"
$calendarService->bsToAd('2081-01-01'); // Carbon instance
```

### Helper Functions

```php
// Current BS date
nepali_date(); // "2081-08-20"

// Convert and format
nepali_date('2024-11-28', 'j F Y', 'en'); // "13 Mangsir 2081"

// BS to AD
english_date('2081-08-13'); // Carbon instance
english_date('2081-08-13', 'Y-m-d'); // "2024-11-28"

// Format BS date
format_nepali_date('2081-08-13', 'j F Y'); // "13 Mangsir 2081"
```

### In Blade

```blade
{{ nepali_date(now()) }}
{{ toNepaliDate('2024-04-13') }}
{{ format_nepali_date($bsDate, 'j F Y', 'en') }}
```

### Format Codes

| Code       | Output                    | Example         |
| ---------- | ------------------------- | --------------- |
| `j F Y`    | 15 Mangsir 2081           | Most common     |
| `D, j F Y` | Thu, 15 Mangsir 2081      | With day        |
| `l, j F Y` | Thursday, 15 Mangsir 2081 | Full day        |
| `Y-m-d`    | 2081-08-15                | Database format |

### Locales

-   `'en'` - English (Mangsir, Thursday)
-   `'np'` - Nepali (मंसिर, बिहीबार)

## Service Methods

```php
use App\Services\NepalCalendarService;

$service = app(NepalCalendarService::class);

$service->adToBs($date);                    // AD → BS
$service->bsToAd($bsDate);                  // BS → AD
$service->formatBsDate($bs, $format);       // Format BS
$service->getCurrentBsDate();               // Current BS
$service->isValidBsDateFormat($bs);         // Validate
$service->getMonthName($monthNum);          // Month name
```

## Common Patterns

### Payroll Dates

```php
// Store both formats
$periodStartBs = '2081-08-01';
$periodStartAd = $service->bsToAd($periodStartBs);

HrmPayrollRecord::create([
    'period_start_bs' => $periodStartBs,
    'period_start_ad' => $periodStartAd,
]);
```

### Display in Views

```blade
<p>Period: {{ format_nepali_date($payroll->period_start_bs) }}</p>
<small>({{ $payroll->period_start_ad->format('d M Y') }})</small>
```

### Form Input

```blade
<input type="text"
       name="date_bs"
       placeholder="YYYY-MM-DD"
       value="{{ nepali_date() }}">
```

## Testing

```bash
php artisan test --filter NepaliDateConversionTest
```

All tests passing ✅

## Files Modified/Created

-   ✅ `app/Services/NepalCalendarService.php` - Updated with full implementation
-   ✅ `app/helpers.php` - Created helper functions
-   ✅ `tests/Feature/NepaliDateConversionTest.php` - Comprehensive tests
-   ✅ `docs/NEPALI_DATE_CONVERTER.md` - Full documentation
-   ✅ `composer.json` - Added package and autoload

## Package Info

**Package**: anuzpandey/laravel-nepali-date v2.3.2  
**Docs**: https://github.com/anuzpandey/laravel-nepali-date

---

**Status**: ✅ Fully implemented and tested  
**Date**: December 5, 2025
