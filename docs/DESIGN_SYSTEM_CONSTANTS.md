# Font & Spacing Constants System

## Overview
This document outlines the unified font and spacing constants system implemented across the ERP project. The system ensures consistency from the staff dashboard to the admin dashboard using compact, efficient spacing and typography.

## Universal Spacing System

### Base Unit
All spacing is based on **4px base unit** from Tailwind CSS.

### Spacing Scale
```
XS  = 2px    (px-2/py-1)
SM  = 4px    (px-3/py-1.5)
MD  = 8px    (px-4/py-2)
LG  = 12px   (px-6/py-3)
XL  = 16px   (px-8/py-4)
2XL = 24px   (px-10/py-5)
```

## Font System

### Font Families
- `FONT_SANS`: Inter, Plus Jakarta Sans (default)
- `FONT_SERIF`: Default serif
- `FONT_MONO`: Default monospace

### Font Sizes (Tailwind classes)
```
TEXT_XS     = text-xs     (12px)
TEXT_SM     = text-sm     (14px)
TEXT_BASE   = text-base   (16px)
TEXT_LG     = text-lg     (18px)
TEXT_XL     = text-xl     (20px)
TEXT_2XL    = text-2xl    (24px)
TEXT_3XL    = text-3xl    (30px)
```

### Font Weights
```
FONT_LIGHT      = font-light      (300)
FONT_NORMAL     = font-normal     (400)
FONT_MEDIUM     = font-medium     (500)
FONT_SEMIBOLD   = font-semibold   (600)
FONT_BOLD       = font-bold       (700)
```

## Constants Usage

### In Blade Templates
```blade
<!-- Using Design constants in Blade -->
<div class="{{ Design::SPACE_MD }}">
    <h1 class="{{ Design::TEXT_LG }} {{ Design::FONT_SEMIBOLD }}">Title</h1>
</div>
```

### Combined Spacing
```blade
<!-- Predefined combinations -->
<nav class="{{ Design::MENU_ITEM }}">Menu Item</nav>
<label class="{{ Design::FORM_LABEL }}">Email</label>
```

## Component Specific Constants

### Navigation
- `NAV_ITEM_SPACING`: gap-3 px-4 py-2.5
- `NAV_ITEM_TEXT`: text-sm
- `NAV_LINK`: Combined navigation link styling

### Sidebar
- `SIDEBAR_SECTION_PADDING`: p-5
- `SIDEBAR_ITEM_SPACING`: space-y-2
- `SIDEBAR_ICON_SIZE`: w-10 h-10

### Modal/Dialog
- `MODAL_PADDING`: p-6
- `MODAL_HEADER_PADDING`: p-6
- `MODAL_BODY_SPACING`: space-y-4

### Buttons
- `BTN_PADDING`: px-4 py-2 (standard)
- `BTN_SMALL_PADDING`: px-3 py-1.5
- `BTN_LARGE_PADDING`: px-6 py-3

### Forms
- `INPUT_PADDING`: px-3 py-2
- `INPUT_SM_PADDING`: px-2 py-1
- `FORM_LABEL`: block font-medium text-sm text-slate-300

## Implementation Phases

### Phase 1: Admin Dashboard Sidebar
Files updated:
- `resources/views/admin/layouts/partials/sidebar.blade.php`

### Phase 2: Admin Components
Files to update:
- Admin navigation components
- Admin modal dialogs
- Admin form inputs
- Admin cards/panels

### Phase 3: Staff Dashboard
Files to update:
- `resources/views/employee/*`
- `resources/views/student/*`
- Staff navigation components

## Benefits

1. **Consistency**: Unified spacing and typography across entire application
2. **Maintainability**: Central location for design values
3. **Efficiency**: Reduce redundant class strings
4. **Scalability**: Easy to update design tokens globally
5. **Accessibility**: Proper font sizes and spacing for readability
6. **Compact**: Minimal class strings without custom CSS

## Migration Notes

- All hardcoded `px-*`, `py-*`, `p-*`, `text-*`, `font-*` classes should be replaced with constants
- Use combination helpers like `Design::text()` and `Design::spacing()` for flexibility
- New components should use these constants from the start
- Gradually migrate existing components component by component

## Future Enhancements

1. Add line-height constants (`LEADING_*`)
2. Add letter-spacing constants
3. Add responsive spacing variants
4. Create preset component classes
5. Document color + spacing combinations

## Support

For questions about the design system, refer to the Design class or documentation updates.
