# 🎉 Nepali Date Converter - Quick Start

## ✅ Installation Complete!

The AD to BS (Bikram Sambat) / Nepali date converter is now fully functional in your ERP system.

---

## 🚀 Quick Usage

### 1. Convert Current Date to Nepali

```php
$todayBS = nepali_date();
// Output: "2082-08-19"
```

### 2. Convert Any AD Date to BS

```php
$bs = toNepaliDate('2024-04-13');
// Output: "2081-01-01"
```

### 3. Convert BS Date to AD

```php
$ad = toEnglishDate('2081-01-01');
// Output: "2024-04-13"
```

### 4. Format Nepali Date

```php
$formatted = format_nepali_date('2081-08-15', 'j F Y');
// Output: "15 Mangsir 2081"
```

---

## 📝 In Your Code

### Controller

```php
use App\Services\NepalCalendarService;

$service = app(NepalCalendarService::class);

// User enters BS date
$bsDate = $request->input('date_bs');

// Convert to AD automatically
$adDate = $service->bsToAd($bsDate);

// Store both
$record->date_bs = $bsDate;
$record->date_ad = $adDate;
$record->save();
```

### Blade Template

```blade
{{-- Current date --}}
<p>Today: {{ nepali_date() }}</p>

{{-- Format date --}}
<p>{{ format_nepali_date($record->date_bs, 'j F Y') }}</p>

{{-- Convert on the fly --}}
<p>BS: {{ toNepaliDate('2024-04-13') }}</p>
```

---

## 🧪 Test It

```bash
php artisan nepali:test-dates
```

All tests should pass ✅

---

## 📚 Documentation

-   **Full Guide**: `docs/NEPALI_DATE_CONVERTER.md`
-   **Quick Reference**: `docs/NEPALI_DATE_QUICK_REF.md`
-   **Examples**: `app/Examples/NepaliDateExamples.php`
-   **Before/After**: `docs/NEPALI_DATE_BEFORE_AFTER.md`

---

## 🎯 Common Tasks

### Payroll Form

```php
// User enters BS period
$periodStartBS = '2081-08-01';

// Auto-convert to AD
$periodStartAD = $service->bsToAd($periodStartBS);

// Save both formats
$payroll->period_start_bs = $periodStartBS;
$payroll->period_start_ad = $periodStartAD;
```

### Display in Report

```blade
Period: {{ format_nepali_date($payroll->period_start_bs) }}
to {{ format_nepali_date($payroll->period_end_bs) }}
```

---

## ✨ Features

-   ✅ Accurate conversions (2000-2200 BS supported)
-   ✅ Multiple format options
-   ✅ English & Nepali localization
-   ✅ Error handling with logs
-   ✅ Helper functions
-   ✅ Full validation

---

## 🆘 Need Help?

Check the documentation:

```bash
# View in terminal
cat docs/NEPALI_DATE_QUICK_REF.md
```

Or open in VS Code:

-   `docs/NEPALI_DATE_CONVERTER.md` - Complete guide
-   `docs/NEPALI_DATE_QUICK_REF.md` - Cheat sheet

---

## 📦 Package Info

**Package**: anuzpandey/laravel-nepali-date v2.3.2  
**License**: MIT  
**GitHub**: https://github.com/anuzpandey/laravel-nepali-date

---

**Status**: Ready to use! 🎉  
**Accuracy**: Verified ✓  
**Integration**: HRM Payroll ✓
