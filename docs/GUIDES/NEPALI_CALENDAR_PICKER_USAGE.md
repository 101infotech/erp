# 🗓️ Nepali Calendar Picker - Developer Quick Guide

## 🎯 Using the Component

### Basic Usage

```blade
<x-nepali-date-picker name="start_date" />
```

### With Value

```blade
<x-nepali-date-picker name="hire_date" :value="old('hire_date')" />
```

### With Required & Placeholder

```blade
<x-nepali-date-picker
    name="start_date"
    :value="old('start_date')"
    required
    placeholder="YYYY-MM-DD (BS)"
/>
```

### With Custom Class

```blade
<x-nepali-date-picker
    name="end_date"
    :value="old('end_date')"
    class="px-4 py-2 text-sm"
/>
```

---

## 🔧 Component Props

| Prop          | Type    | Default        | Description                  |
| ------------- | ------- | -------------- | ---------------------------- |
| `name`        | string  | `'date'`       | Input field name             |
| `value`       | string  | `''`           | Initial BS date (YYYY-MM-DD) |
| `required`    | boolean | `false`        | Mark as required field       |
| `id`          | string  | Auto-generated | Custom input ID              |
| `placeholder` | string  | `'YYYY-MM-DD'` | Placeholder text             |
| `class`       | string  | `''`           | Additional CSS classes       |

---

## 🎨 Styling

The component is pre-styled for dark mode:

-   ✅ Dark background (`bg-slate-900`)
-   ✅ Light text (`text-white`)
-   ✅ Lime green focus ring (`focus:ring-lime-500`)
-   ✅ Rounded borders (`rounded-lg`)

Calendar popup is also dark-themed with:

-   ✅ Dark background
-   ✅ Today highlighted in blue
-   ✅ Selected date in lime green
-   ✅ Hover effects

---

## ✅ Backend Validation

Always validate BS dates in controller:

```php
use App\Rules\NepaliDate;

$validated = $request->validate([
    'start_date' => ['required', new NepaliDate()],
    'end_date' => ['required', (new NepaliDate())->afterOrEqual($request->start_date)],
]);
```

---

## 📊 Common Patterns

### Leave Request Form

```blade
<div>
    <label class="block text-sm font-medium text-slate-300 mb-2">
        Start Date <span class="text-red-400">*</span>
    </label>
    <x-nepali-date-picker
        name="start_date"
        :value="old('start_date')"
        required
        placeholder="YYYY-MM-DD (BS)"
    />
    @error('start_date')
    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
```

### Filter Form

```blade
<x-nepali-date-picker
    name="date_from"
    :value="request('date_from')"
    placeholder="Start Date (BS)"
    class="px-3 py-2 text-sm"
/>
```

### With Default Current Date

```blade
<x-nepali-date-picker
    name="today"
    :value="old('today', nepali_date())"
    required
/>
```

---

## 🔄 Working with BS Dates in Controller

### Calculate Working Days

```php
$calendarService = new \App\Services\NepalCalendarService();
$workDays = $calendarService->calculateWorkingDays(
    $validated['start_date'],
    $validated['end_date']
);
```

### Convert BS to AD (for external APIs)

```php
$startDateAD = english_date($validated['start_date'])->format('Y-m-d');
$this->externalService->fetchData($startDateAD);
```

### Format for Display

```php
$formatted = format_nepali_date($bsDate, 'l, j F Y');
// Output: "Thursday, 21 Mangsir 2082"
```

---

## 🚨 Important Notes

1. **Always use the component** - Never use `<input type="date">`
2. **Dates are strings** - No Carbon casts in models
3. **BS format only** - UI never shows AD dates
4. **Validate properly** - Always use `NepaliDate` rule
5. **Convert for APIs** - Only convert to AD when calling external services

---

## 🎯 Examples in the App

Check these files for reference:

-   `/resources/views/admin/hrm/leaves/create.blade.php`
-   `/resources/views/admin/hrm/attendance/sync.blade.php`
-   `/resources/views/admin/hrm/leaves/index.blade.php`

---

## 📚 Related Documentation

-   `/BS_ONLY_QUICK_REF.md` - Complete BS system reference
-   `/docs/NEPALI_CALENDAR_PICKER_COMPLETE.md` - Full implementation details
-   `/app/Services/NepalCalendarService.php` - BS calculation methods
-   `/app/Rules/NepaliDate.php` - Validation rule source
