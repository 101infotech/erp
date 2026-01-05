# UI Redesign Quick Reference

## 🎨 New Color System

### Primary Colors (Indigo)
```php
bg-primary-50    // Very light indigo background
bg-primary-100   // Light indigo background
text-primary-600 // Indigo text
text-primary-700 // Darker indigo text
```

### Neutral Colors (Slate)
```php
bg-slate-50      // Page background (#F8FAFC)
bg-slate-100     // Hover background
border-slate-200 // Borders (#E2E8F0)
text-neutral-500 // Muted text
text-neutral-900 // Primary text
```

### Status Colors
```php
// Success (Green)
bg-success-50 text-success-600

// Warning (Amber)  
bg-warning-50 text-warning-600

// Danger (Red)
bg-danger-50 text-danger-600

// Info (Blue)
bg-info-50 text-info-600
```

---

## 📏 Spacing Scale

### Card Spacing
```php
p-6              // Card padding (24px)
gap-6            // Grid gap between cards
mb-8             // Section margin bottom
```

### Component Sizing
```php
w-12 h-12        // Icon containers (48px)
w-10 h-10        // Small icon containers (40px)
w-6 h-6          // Icons (24px)
w-5 h-5          // Small icons (20px)
```

---

## 🔤 Typography

### Font Sizes
```php
text-xs          // 12px - Supporting text
text-sm          // 13px - Labels
text-base        // 14px - Body text
text-xl          // 18px - Subheadings
text-2xl         // 20px - Section headings
text-3xl         // 28px - Large numbers
```

### Font Weights
```php
font-medium      // 500 - Labels, subtitles
font-semibold    // 600 - Buttons
font-bold        // 700 - Headings
font-extrabold   // 800 - Large numbers
```

---

## 🎯 Common Patterns

### Stat Card
```html
<div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-neutral-500 text-sm font-medium mb-2">Label</p>
            <h2 class="text-3xl font-extrabold text-neutral-900">123</h2>
            <p class="text-xs text-neutral-500 mt-2">Supporting text</p>
        </div>
        <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center">
            <!-- Icon here -->
        </div>
    </div>
</div>
```

### Icon Container
```html
<div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center">
    <svg class="w-6 h-6 text-primary-600" ...>
</div>
```

### Status Badge
```html
<!-- Success -->
<span class="px-3 py-1 bg-success-100 text-success-700 text-xs font-medium rounded-lg">
    Completed
</span>

<!-- Warning -->
<span class="px-3 py-1 bg-warning-100 text-warning-700 text-xs font-medium rounded-lg">
    Pending
</span>

<!-- Danger -->
<span class="px-3 py-1 bg-danger-100 text-danger-700 text-xs font-medium rounded-lg">
    Cancelled
</span>

<!-- Info -->
<span class="px-3 py-1 bg-info-100 text-info-700 text-xs font-medium rounded-lg">
    Confirmed
</span>
```

### Primary Button
```html
<button class="px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-primary-700 hover:shadow-md transition-all duration-200">
    Click Me
</button>
```

### Secondary Button
```html
<button class="px-4 py-2.5 bg-white text-neutral-700 text-sm font-semibold rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors">
    Cancel
</button>
```

### Navigation Link
```html
<!-- Active -->
<a class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors bg-primary-50 text-primary-700">
    Dashboard
</a>

<!-- Inactive -->
<a class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors text-slate-700 hover:bg-slate-100">
    Settings
</a>
```

---

## 🎭 Shadows

```php
shadow-sm        // Subtle card shadow
hover:shadow-md  // Elevated hover state
shadow-lg        // Dropdown/modal shadow
```

---

## 🔄 Transitions

```php
transition-colors        // For color changes (buttons, links)
transition-all          // For multiple properties
duration-200            // 200ms animation
```

---

## 📐 Border Radius

```php
rounded-lg      // 10px - Buttons, small elements
rounded-xl      // 12px - Cards, containers
rounded-2xl     // 16px - Large containers
rounded-full    // Circular - Avatars, pills
```

---

## ✅ Best Practices

### DO ✓
- Use `slate-*` for neutral colors
- Use `primary-*` for interactive elements
- Use `rounded-xl` for cards
- Use `p-6` for card padding
- Use `gap-6` for grid gaps
- Use `shadow-sm` as default
- Use `transition-colors` for smooth effects

### DON'T ✗
- Don't use `blue-*` for backgrounds/borders
- Don't mix border colors on same element
- Don't use large padding (p-8, p-10)
- Don't use large gaps (gap-8, gap-10)
- Don't use heavy shadows
- Don't use `transition` alone (specify type)

---

## 🎯 Quick Conversions

### Old → New
```
bg-blue-50          → bg-slate-50
border-blue-100     → border-slate-200
text-neutral-600    → text-neutral-500
p-8                 → p-6
gap-8               → gap-6
mb-10               → mb-8
w-14 h-14           → w-12 h-12
w-7 h-7             → w-6 h-6
text-4xl            → text-3xl
rounded-lg (icons)  → rounded-xl
transition          → transition-colors
bg-lime-600         → bg-primary-600
```

---

## 🔍 Finding Elements

### Search & Replace Patterns
```bash
# Update backgrounds
bg-blue-50 → bg-slate-50

# Update borders  
border-blue-100 → border-slate-200

# Update text
text-neutral-600 → text-neutral-500

# Update padding
p-8 border → p-6 border

# Update icons
w-14 h-14 → w-12 h-12
w-7 h-7 → w-6 h-6

# Update transitions
transition → transition-colors
```

---

## 📱 Responsive Grid

```html
<!-- 1 col (mobile) → 2 cols (tablet) → 4 cols (desktop) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Cards -->
</div>
```

---

## 💡 Tips

1. **Consistency**: Always use the same patterns across pages
2. **Spacing**: Stick to gap-6, p-6, mb-8 for uniformity
3. **Colors**: Use semantic colors (success, warning, danger, info)
4. **Shadows**: Keep subtle - shadow-sm is your friend
5. **Transitions**: Always specify type (transition-colors)
6. **Typography**: text-xs for support, text-sm for labels, text-3xl for numbers
7. **Icons**: Always center in colored containers with rounded-xl

---

## 🚀 Ready-to-Use Components

Copy these directly into your blade files:

### Section Header
```html
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-neutral-900">Section Title</h2>
    <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-semibold flex items-center gap-2 transition-colors">
        View All
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </a>
</div>
```

### Empty State
```html
<div class="bg-white rounded-xl p-12 border border-slate-200 shadow-sm text-center">
    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <!-- Icon -->
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-neutral-900 mb-2">No data found</h3>
    <p class="text-sm text-neutral-500 mb-6">Get started by creating your first item.</p>
    <button class="px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-primary-700 transition-colors">
        Create New
    </button>
</div>
```
