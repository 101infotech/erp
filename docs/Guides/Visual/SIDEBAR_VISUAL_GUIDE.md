# Employee Sidebar Navigation - Visual Guide

## Layout Structure

```
┌─────────────────────────────────────────────────────────────────┐
│                          Browser Window                          │
├─────────────┬───────────────────────────────────────────────────┤
│             │                                                   │
│   SIDEBAR   │              MAIN CONTENT AREA                   │
│   w-64      │              ml-64 (margin-left)                 │
│ (256px)     │                                                   │
│             │  ┌─────────────────────────────────┐             │
│  Fixed      │  │      Header/Page Title          │             │
│  Position   │  ├─────────────────────────────────┤             │
│  z-40       │  │                                 │             │
│             │  │      Page Content Area          │             │
│  overflow   │  │      Scrollable if needed       │             │
│  y-auto     │  │                                 │             │
│             │  │                                 │             │
│             │  │                                 │             │
│             │  │                                 │             │
│             │  │                                 │             │
└─────────────┴───────────────────────────────────────────────────┘
```

## Sidebar Components

### 1. Logo Section (Header)

```
┌───────────────────────────────────┐
│  ┌────────┐                       │
│  │   S    │  Saubhagya ERP       │
│  │ Logo   │  Employee Portal     │
│  └────────┘                       │
└───────────────────────────────────┘
  Background: slate-900
  Border: slate-800 (bottom)
  Colors: Logo lime-500/to-lime-600
          Text white/slate-400
```

### 2. Navigation Items

#### Main Section

```
Dashboard         [house icon]     Active: lime-green highlight
Attendance        [calendar icon]
Payroll           [money icon]
Leave Requests    [date icon]
```

#### Self-Service Section (Divider)

```
───────────────────────────────────
SELF-SERVICE (small text, slate-500)
───────────────────────────────────

Weekly Feedback   [checkmark icon]
Complaint Box     [message icon]
My Profile        [user icon]
```

### 3. Footer Section (Logout)

```
───────────────────────────────────
Logout            [exit icon]
```

## Color Scheme

### Default States

```
Background:  bg-slate-900
Borders:     border-slate-800
Text:        text-slate-300
Icons:       inherit text color
Hover:       hover:bg-slate-800 + transition
```

### Active State

```
Background:  bg-lime-500/10 (10% opacity)
Text:        text-lime-400
Border:      none
Icon:        matches text color
Effect:      Stands out from inactive items
```

### Section Divider

```
Border-top:  border-slate-800
Text:        text-slate-500 (uppercase, small)
Gap:         pt-4 mt-4
```

## Dimensions & Spacing

### Sidebar Dimensions

```
Width:        w-64 (256px fixed)
Height:       h-screen (full viewport height)
Position:     fixed left-0 top-0
Z-Index:      z-40
Overflow:     overflow-y-auto
Main Content: ml-64
```

### Internal Spacing

```
Logo Section:    p-4, border-b border-slate-800
Navigation:      px-3 py-4 space-y-1
Nav Items:       px-3 py-2
Section Gaps:    pt-4 mt-4
Footer:          p-4, border-t border-slate-800
Icons:           w-5 h-5, mr-3
```

## Visual Examples

### Navigation Item - Inactive

```
┌─────────────────────────────────┐
│ 🏠 Dashboard                    │  ← hover:bg-slate-800
│                                 │  ← text-slate-300
└─────────────────────────────────┘
```

### Navigation Item - Active (Current Page)

```
┌─────────────────────────────────┐
│ 🏠 Dashboard                    │  ← bg-lime-500/10
│                                 │  ← text-lime-400
└─────────────────────────────────┘
```

### Sidebar Full View

```
┌───────────────────────────────────┐
│          [S]  Saubhagya           │  ← Logo
│               Employee Portal     │
├───────────────────────────────────┤  ← Divider
│ 🏠 Dashboard                      │  ← Active (highlighted)
│ 📅 Attendance                     │
│ 💰 Payroll                        │
│ 📆 Leave Requests                 │
├───────────────────────────────────┤  ← Section divider
│ SELF-SERVICE                      │  ← Small text label
├───────────────────────────────────┤
│ ✓ Weekly Feedback                 │
│ 💬 Complaint Box                  │
│ 👤 My Profile                     │
├───────────────────────────────────┤  ← Footer divider
│ 🚪 Logout                         │
└───────────────────────────────────┘
```

## Responsive Behavior

### Desktop (Current Implementation)

-   Sidebar always visible (fixed position)
-   Main content has ml-64 margin
-   Full height scrollable sidebar
-   No mobile menu

### Future Mobile Enhancement

```
Collapsed View:
├─────┐
│ [S] │  Logo only (icon)
│ 🏠  │  Nav icons only
│ 📅  │
│ 💰  │
│ ...  │
└─────┘

Expanded Menu:
├─────────────────────────────────┐
│  Sidebar Menu                   │
│  (Mobile hamburger toggle)      │
│  Close on outside click         │
└─────────────────────────────────┘
```

## Icon Set

Used in Sidebar:

-   Dashboard: `M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6` (house)
-   Attendance: `M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4` (checkmark)
-   Payroll: `M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z` (money)
-   Leave: `M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z` (calendar)
-   Feedback: `M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z` (checkmark circle)
-   Complaint: `M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z` (message)
-   Profile: `M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z` (user)
-   Logout: `M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1` (exit)

## Animation Effects

### Hover Transition

```
transition: all 0.2s ease
- Background color smoothly changes
- Text color smoothly changes
- Icons follow text color
```

### Active State

```
No animation on page load
Instantly highlights current page
Smooth transition on navigation
```

## Accessibility Features

### Current Implementation

-   Semantic HTML (nav, a, button)
-   Proper link href attributes
-   Form method POST for logout
-   Color contrast meets WCAG standards
-   Icon + text labels (not icon-only)

### Future Enhancements

-   ARIA labels for nav sections
-   Keyboard navigation support
-   Skip to content link
-   Focus indicators
-   Screen reader optimization

## Browser Compatibility

✅ Tested & Working:

-   Chrome/Chromium (latest)
-   Firefox (latest)
-   Safari (latest)
-   Edge (latest)

❌ Not Supported:

-   Internet Explorer (flex/grid not supported)
-   Very old browsers (CSS Grid required)

## Performance Notes

-   Static HTML rendering (no JavaScript needed)
-   Minimal CSS (Tailwind)
-   Fixed positioning doesn't trigger layout thrashing
-   Overflow-y-auto only when needed
-   No animations on scroll (performance friendly)

## Customization Options

To modify the sidebar, edit:
`resources/views/employee/partials/sidebar.blade.php`

Change:

-   `w-64` → Different sidebar width
-   `bg-slate-900` → Different colors
-   SVG icons → Different icons
-   Navigation routes → Different links
-   Spacing values → Different padding/margins
-   Z-index value → Different stacking order

Example: Dark blue sidebar

```blade
class="fixed left-0 top-0 h-screen w-64 bg-blue-900 border-r border-blue-800..."
```
