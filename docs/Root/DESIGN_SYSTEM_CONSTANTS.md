# Font & Spacing Constants System

## Status
✅ **COMPLETE** - All three phases implemented and deployed

## Overview
This document outlines the unified font and spacing constants system implemented across the ERP project. The system ensures consistency from the staff dashboard to the admin dashboard using compact, efficient spacing and typography.

## Implementation Summary

### Phase 1 - Admin Dashboard Sidebar ✅
**Status:** Completed - File: `resources/views/admin/layouts/partials/sidebar.blade.php`
- All menu items (20+ items) updated with `Design::MENU_ITEM`
- Headings updated with `Design::TEXT_LG` and `Design::FONT_SEMIBOLD`
- Padding updated with `Design::SIDEBAR_SECTION_PADDING`
- Icon sizing with `Design::SIDEBAR_ICON_SIZE`

### Phase 2 - Admin Components ✅
**Status:** Completed - Components updated:
- `professional-modal`: Modal header, body, footer with constants
- `confirm-modal`: Titles, spacing, button padding
- `input-label`: Form label styling with `Design::FORM_LABEL`
- `input-error`: Error message styling
- `nav-link`: Navigation link text sizing
- `responsive-nav-link`: Responsive navigation  
- `dropdown-link`: Dropdown menu items
- `auth-session-status`: Status message styling

### Phase 3 - Staff Dashboard ✅
**Status:** Completed
- `employee/partials/sidebar.blade.php`: All navigation items and spacing
- `employee/partials/nav.blade.php`: Navigation bar layout and components

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

### Phase 1: Admin Dashboard Sidebar ✅ COMPLETED
Files updated:
- `resources/views/admin/layouts/partials/sidebar.blade.php`

Changes:
- Replaced 20+ hardcoded `px-4 py-2.5 text-sm` menu item classes
- Updated logo section with constants
- Updated section headers and typography
- All transitions now use consistent Design constants

### Phase 2: Admin Components ✅ COMPLETED
Files updated:
- `resources/views/components/professional-modal.blade.php`
- `resources/views/components/confirm-modal.blade.php`
- `resources/views/components/input-label.blade.php`
- `resources/views/components/input-error.blade.php`
- `resources/views/components/nav-link.blade.php`
- `resources/views/components/responsive-nav-link.blade.php`
- `resources/views/components/dropdown-link.blade.php`
- `resources/views/components/auth-session-status.blade.php`

Changes:
- Modal components use `Design::MODAL_*` constants
- Form components use `Design::FORM_*` and `Design::FONT_*` constants
- Navigation components use `Design::TEXT_*` and `Design::FONT_*` constants
- Consistent button padding with `Design::BTN_PADDING`

### Phase 3: Staff Dashboard ✅ COMPLETED
Files updated:
- `resources/views/employee/partials/sidebar.blade.php`
- `resources/views/employee/partials/nav.blade.php`

Changes:
- Employee sidebar uses `Design::NAV_ITEM_SPACING` for all menu items
- Logo section updated with `Design::SIDEBAR_*` constants
- Navigation bar uses `Design::GAP_*` and `Design::PAD_*` constants
- Text styling uses `Design::TEXT_*` and `Design::FONT_*` constants

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
