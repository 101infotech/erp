# Design System Documentation

## Overview

This design system provides a unified set of design tokens, colors, spacing, typography, and components to ensure consistency across the entire ERP application.

---

## 🎨 Color Palette

### Primary Colors (Lime)
Used for CTAs, primary actions, and success states.

```css
--color-primary: #84cc16        /* lime-500 - Main brand color */
--color-primary-dark: #65a30d    /* lime-600 - Hover states */
--color-primary-light: #bef264   /* lime-400 - Subtle highlights */
```

**Usage:**
- Primary buttons
- Success messages
- Active navigation items
- Positive metrics/trends
- Form field focus states

**Tailwind Classes:**
```blade
bg-primary hover:bg-primary-dark
text-primary border-primary
```

---

### Secondary Colors (Blue)
Used for links, informational messages, and secondary actions.

```css
--color-secondary: #3b82f6       /* blue-500 */
--color-secondary-dark: #2563eb  /* blue-600 */
--color-secondary-light: #60a5fa /* blue-400 */
```

**Usage:**
- Links
- Info badges
- Secondary buttons
- Informational alerts

**Tailwind Classes:**
```blade
bg-secondary hover:bg-secondary-dark
text-secondary border-secondary
```

---

### Accent Color (Amber)
Used for warnings and highlights.

```css
--color-accent: #f59e0b         /* amber-500 */
```

**Usage:**
- Warning messages
- Pending states
- Important highlights

**Tailwind Classes:**
```blade
bg-accent text-accent border-accent
```

---

### Semantic Colors

#### Success (Green)
```css
--color-success: #10b981        /* green-500 */
```
**Usage:** Success messages, completed actions, positive indicators

#### Danger (Red)
```css
--color-danger: #ef4444         /* red-500 */
```
**Usage:** Error messages, destructive actions, critical alerts

#### Warning (Amber)
```css
--color-warning: #f59e0b        /* amber-500 */
```
**Usage:** Warning messages, caution states

#### Info (Blue)
```css
--color-info: #3b82f6           /* blue-500 */
```
**Usage:** Informational messages, help text

---

## 🎭 Background Colors

### Main Backgrounds
```css
--bg-primary: #020617           /* slate-950 - Main app background */
--bg-secondary: #0f172a         /* slate-900 - Secondary sections */
--bg-tertiary: #1e293b          /* slate-800 - Tertiary sections */
```

### Card Backgrounds
```css
--bg-card: rgba(30, 41, 59, 0.5)       /* slate-800/50 - Standard cards */
--bg-card-hover: rgba(30, 41, 59, 0.7) /* slate-800/70 - Card hover */
```

**Usage:**
```blade
<div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700">
    <!-- Card content -->
</div>
```

---

## 📝 Typography

### Font Family
```css
--font-sans: 'Inter', system-ui, sans-serif
```

**Usage:**
- All UI elements use Inter font by default
- System fonts as fallback for performance

### Font Sizes
```css
--font-size-xs: 0.75rem     /* 12px - Small labels, captions */
--font-size-sm: 0.875rem    /* 14px - Secondary text */
--font-size-base: 1rem      /* 16px - Body text */
--font-size-lg: 1.125rem    /* 18px - Large body */
--font-size-xl: 1.25rem     /* 20px - Subheadings */
--font-size-2xl: 1.5rem     /* 24px - Section headings */
--font-size-3xl: 1.875rem   /* 30px - Page headings */
```

**Tailwind Classes:**
```blade
text-xs   text-sm   text-base
text-lg   text-xl   text-2xl   text-3xl
```

### Font Weights
```css
--font-weight-normal: 400
--font-weight-medium: 500
--font-weight-semibold: 600
--font-weight-bold: 700
```

**Tailwind Classes:**
```blade
font-normal   font-medium   font-semibold   font-bold
```

### Text Colors
```css
--text-primary: #ffffff      /* white - Primary text */
--text-secondary: #94a3b8    /* slate-400 - Secondary text */
--text-tertiary: #64748b     /* slate-500 - Tertiary text */
```

**Tailwind Classes:**
```blade
text-white        /* Primary headings, important text */
text-slate-400    /* Secondary text, descriptions */
text-slate-500    /* Tertiary text, labels */
```

---

## 📏 Spacing Scale

```css
--spacing-xs: 0.25rem     /* 4px */
--spacing-sm: 0.5rem      /* 8px */
--spacing-md: 1rem        /* 16px */
--spacing-lg: 1.5rem      /* 24px */
--spacing-xl: 2rem        /* 32px */
--spacing-2xl: 3rem       /* 48px */
```

### Standard Spacing Patterns

**Card Padding:**
```blade
class="px-6 py-4"    <!-- 24px horizontal, 16px vertical -->
```

**Section Gaps:**
```blade
class="mb-6"         <!-- 24px bottom margin between sections -->
class="gap-4"        <!-- 16px gap in grid/flex -->
```

**Element Gaps:**
```blade
class="gap-2"        <!-- 8px gap between small elements -->
class="gap-3"        <!-- 12px gap between medium elements -->
class="gap-4"        <!-- 16px gap between large elements -->
```

---

## 🔲 Border Radius

```css
--radius-sm: 0.375rem     /* 6px - Small elements */
--radius-md: 0.5rem       /* 8px - Standard elements */
--radius-lg: 0.75rem      /* 12px - Large elements */
--radius-xl: 1rem         /* 16px - Cards */
```

**Tailwind Classes:**
```blade
rounded-lg       /* Buttons, inputs */
rounded-xl       /* Cards, containers */
rounded-full     /* Badges, avatars */
```

---

## 🎨 Borders

```css
--border-primary: #334155      /* slate-700 - Primary borders */
--border-secondary: #475569    /* slate-600 - Secondary borders */
```

**Standard Border Pattern:**
```blade
border border-slate-700              <!-- Default border -->
hover:border-primary-500/50          <!-- Hover state with accent -->
focus:border-primary ring-primary    <!-- Focus state -->
```

---

## 💫 Shadows

```css
--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1)
--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1)
--shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1)
--shadow-card: 0 4px 6px rgba(0, 0, 0, 0.1)
```

**Tailwind Classes:**
```blade
shadow-sm        <!-- Subtle shadow for raised elements -->
shadow-md        <!-- Standard shadow for cards -->
shadow-lg        <!-- Prominent shadow for modals -->
```

---

## ⚡ Transitions

```css
--transition-fast: 150ms
--transition-base: 200ms
--transition-slow: 300ms
```

**Standard Transition Pattern:**
```blade
transition-all duration-200          <!-- Standard transitions -->
transition-colors duration-150       <!-- Color transitions only -->
hover:scale-105 transition-transform <!-- Hover scale effect -->
```

---

## 📱 Responsive Breakpoints

```css
--breakpoint-sm: 640px
--breakpoint-md: 768px
--breakpoint-lg: 1024px
--breakpoint-xl: 1280px
--breakpoint-2xl: 1536px
```

**Tailwind Usage:**
```blade
grid-cols-1 md:grid-cols-2 lg:grid-cols-4
px-4 md:px-6 lg:px-8
text-sm md:text-base lg:text-lg
```

---

## 🎯 Component Patterns

### Card Pattern
```blade
<div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700 hover:border-primary/50 transition-colors">
    <!-- Content -->
</div>
```

### Button Pattern
```blade
<!-- Primary Button -->
<button class="px-4 py-2 bg-primary hover:bg-primary-dark text-slate-950 font-semibold rounded-lg transition-colors">
    Action
</button>

<!-- Secondary Button -->
<button class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition-colors">
    Cancel
</button>
```

### Input Pattern
```blade
<input type="text" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-colors">
```

### Badge Pattern
```blade
<!-- Success Badge -->
<span class="px-3 py-1 bg-success/20 text-success rounded-full text-xs font-semibold">
    Active
</span>

<!-- Danger Badge -->
<span class="px-3 py-1 bg-danger/20 text-danger rounded-full text-xs font-semibold">
    Inactive
</span>
```

---

## 🚀 Usage Guidelines

### DO's ✅
- Use design tokens consistently across all components
- Use Tailwind utility classes instead of custom CSS
- Follow spacing scale for margins and padding
- Use semantic color names (primary, success, danger)
- Maintain consistent hover and focus states

### DON'Ts ❌
- Don't use arbitrary color values
- Don't mix different spacing scales
- Don't create custom shadows without tokens
- Don't use inline styles
- Don't skip responsive breakpoints

---

## 📚 Quick Reference

### Most Common Patterns

**Container:**
```blade
class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
```

**Grid Layout:**
```blade
class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
```

**Flex Layout:**
```blade
class="flex items-center justify-between gap-4"
```

**Card:**
```blade
class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700"
```

**Text Hierarchy:**
```blade
<h1 class="text-3xl font-bold text-white mb-2">
<h2 class="text-2xl font-semibold text-white mb-2">
<h3 class="text-xl font-semibold text-white mb-2">
<p class="text-base text-slate-400">
<span class="text-xs text-slate-500">
```

---

## 🔗 Related Documentation

- [Component Library](COMPONENT_LIBRARY.md) - Reusable Blade components
- [Migration Guide](MIGRATION_GUIDE.md) - How to migrate existing pages
- [UI Consistency Plan](../UI_CONSISTENCY_PLAN.md) - Full implementation plan

---

**Last Updated:** January 15, 2026
**Version:** 1.0.0
