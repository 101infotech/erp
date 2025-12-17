# Weekly Feedback Table Layout - Visual Guide

## Table Structure

### Column Layout (7 columns)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│ WEEK PERIOD      │ FEELINGS      │ PROGRESS      │ IMPROVEMENTS  │ SUBMITTED │ STATUS │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ Dec 02 - Dec 8   │ Feeling ener- │ Completed UI  │ Time mgmt &   │ Dec 10    │ ✓      │
│ 2025              │ gized... ✂     │ design for... │ comms skills  │ 2:30 PM   │ Subm.  │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ Dec 09 - Dec 15  │ Bit stressed  │ Resolved 5    │ Want to learn │ Dec 11    │ Subm.  │
│ 2025              │ about deadli... │ customer i... │ new tech...   │ 1:15 PM   │ (✓ Rev)│
└─────────────────────────────────────────────────────────────────────────────────────┘
```

### Desktop View (Full Resolution)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ MY FEEDBACK HISTORY                                          [Submit New Feedback Button]    │
│ Track your submitted weekly feedback and responses                                           │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────────────────────┐   │
│  │ WEEK        │ FEELINGS      │ PROGRESS      │ IMPROVEMENTS  │ SUBMITTED │ STATUS │    │  │
│  │ PERIOD      │               │               │               │           │        │    │  │
│  ├──────────────────────────────────────────────────────────────────────────────────────┤   │
│  │ Dec 02 -    │ Feeling ener- │ Completed UI  │ Time mgmt &   │ Dec 10    │ ✓      │    │  │
│  │ Dec 08,     │ gized, great  │ design for... │ comms skills  │ 2:30 PM   │ Subm.  │    │  │
│  │ 2025        │ collabor...   │               │               │           │        │    │  │
│  │             │               │               │               │           │        │    │  │
│  ├──────────────────────────────────────────────────────────────────────────────────────┤   │
│  │ Dec 09 -    │ Bit stressed  │ Resolved 5    │ Want to learn │ Dec 11    │ ✓      │    │  │
│  │ Dec 15,     │ about deadli- │ customer is-  │ new tech...   │ 1:15 PM   │ Revew  │    │  │
│  │ 2025        │ nes, team good│ sues, attend  │               │           │ (✓)    │    │  │
│  │             │               │ sync mtgs...  │               │           │        │    │  │
│  ├──────────────────────────────────────────────────────────────────────────────────────┤   │
│  │ [More rows...]                                                                      │   │
│  └──────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                              │
│  Pagination: [< Previous] 1 [Next >]                                                       │
│                                                                                              │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Cell Content & Styling

### Week Period Column

```
Content:  Date range (M d - M d, Y format)
Width:    Flexible, whitespace-nowrap
Example:  "Dec 02 - Dec 08, 2025"
Styling:  text-sm font-medium text-white
```

### Feelings Column

```
Content:  First 50 characters of feelings
Width:    max-w-xs with truncate
Truncate: Yes (if longer than 50 chars)
Styling:  text-sm text-slate-300
Example:  "Feeling energized, great collabor..."
```

### Progress Column

```
Content:  First 50 characters of progress
Width:    max-w-xs with truncate
Truncate: Yes (if longer than 50 chars)
Styling:  text-sm text-slate-300
Example:  "Completed UI design for feat..."
```

### Improvements Column

```
Content:  First 50 characters of improvements
Width:    max-w-xs with truncate
Truncate: Yes (if longer than 50 chars)
Styling:  text-sm text-slate-300
Example:  "Time management & communication..."
```

### Submitted Column

```
Content:  Submission date (M d, Y format)
Width:    Fixed, whitespace-nowrap
Example:  "Dec 10, 2025"
Styling:  text-xs text-slate-400
```

### Status Column

```
Content:  Badge (Submitted or Reviewed)
Width:    Flexible

Submitted Badge (Green):
  Background: bg-green-500/20
  Text:       text-green-300
  Border:     border-green-500/30
  Content:    "✓ Submitted"

Reviewed Badge (Amber):
  Background: bg-amber-500/20
  Text:       text-amber-300
  Border:     border-amber-500/30
  Content:    "✓ Reviewed"

Styling:  px-3 py-1 text-xs font-medium rounded-full
```

### Actions Column

```
Content:  View Details link
Width:    Right-aligned, whitespace-nowrap
Link:     "View Details"
Styling:  text-lime-400 hover:text-lime-300 transition
Action:   Links to employee.feedback.show page
```

## Row Styling

### Normal State

```
Background:  bg-slate-800/50
Border-top:  None
Border-bottom: border-slate-700
Padding:     py-4 (cells)
Text color:  white/slate-300
```

### Hover State

```
Background:  hover:bg-slate-700/50
Transition:  transition (smooth)
Effect:      Slight highlight for interactivity
```

### Row Structure

```blade
<tr class="hover:bg-slate-700/50 transition">
    <td class="px-6 py-4 ...">Content</td>
    <td class="px-6 py-4 ...">Content</td>
    ...
</tr>
```

## Table Container

### Outer Container

```
Background:  bg-slate-800/50 with backdrop blur
Border:      border border-slate-700
Corner:      rounded-lg
Overflow:    overflow-hidden
```

### Inner Scroll Container

```
Overflow-X:  overflow-x-auto (for mobile)
Allows:      Horizontal scrolling on small screens
```

### Table Element

```
Width:       min-w-full
Border:      divide-y divide-slate-700
Structure:   thead + tbody
```

## Responsive Breakpoints

### Desktop (lg: 1024px+)

```
Container:   px-8 (generous padding)
Table width: 100% - 32px (16px each side)
Columns:     All visible, no scroll needed
Font size:   text-sm (readable)
```

### Tablet (sm: 640px - 1023px)

```
Container:   px-6 (medium padding)
Table width: 100% - 24px (12px each side)
Scroll:      Horizontal scroll if columns overflow
Font size:   text-sm (readable)
```

### Mobile (< 640px)

```
Container:   px-4 (minimal padding)
Table width: 100% - 16px (8px each side)
Scroll:      Horizontal scroll enabled
Font size:   text-xs for headers, text-sm for content
```

## Empty State

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│                     📋 Icon (slate-400)                     │
│                                                              │
│            No Feedback Submitted Yet                         │
│                                                              │
│      You haven't submitted any weekly feedback yet.          │
│      Start by submitting your first feedback!               │
│                                                              │
│        [Submit Feedback Now Button - Green/Lime]            │
│                                                              │
└──────────────────────────────────────────────────────────────┘

Styling:
- Container: bg-slate-800/50, border border-slate-700, rounded-lg, p-12
- Icon: w-8 h-8, text-slate-400
- Title: text-lg font-semibold text-white
- Text: text-slate-400
- Button: bg-gradient-to-r from-lime-500 to-green-600 hover:from-lime-600 hover:to-green-700
```

## Header Section

```
┌──────────────────────────────────────────────────────────────────┐
│ My Feedback History                [Submit New Feedback Button]   │
│ Track your submitted weekly feedback and responses               │
└──────────────────────────────────────────────────────────────────┘

Title:      h1 class="text-3xl font-bold text-white"
Subtitle:   p class="text-slate-400 text-sm mt-1"
Button:     px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-950
            font-semibold rounded-lg transition
            With icon and text: "Submit New Feedback"
```

## Pagination

```
┌──────────────────────────────────────────────────────────────────┐
│ [< Previous] 1 2 3 [Next >]                                      │
│ Showing 10 items per page                                        │
└──────────────────────────────────────────────────────────────────┘

Location:   Below table in px-6 py-4 section
Border:     border-t border-slate-700
Styling:    Default Tailwind pagination styling
Provider:   Laravel's links() method for pagination
```

## Full-Width Layout

```
Browser Window
├─ Sidebar (w-64, fixed)
│
└─ Main Content (flex-1, ml-64)
   ├─ Header (optional, full width)
   │
   └─ Container (px-4 sm:px-6 lg:px-8)
      └─ Table (100% width)
```

## Color Reference

### Backgrounds

-   Container: `bg-slate-800/50`
-   Header: `bg-slate-900`
-   Rows: `bg-slate-800/50`
-   Hover: `hover:bg-slate-700/50`

### Text

-   Primary: `text-white`
-   Secondary: `text-slate-300`
-   Tertiary: `text-slate-400`
-   Link: `text-lime-400` → `hover:text-lime-300`

### Badges (Submitted)

-   Background: `bg-green-500/20`
-   Text: `text-green-300`
-   Border: `border-green-500/30`

### Badges (Reviewed)

-   Background: `bg-amber-500/20`
-   Text: `text-amber-300`
-   Border: `border-amber-500/30`

### Borders

-   Container: `border-slate-700`
-   Dividers: `divide-slate-700`

## Accessibility

-   ✅ Semantic table markup
-   ✅ Proper th headers
-   ✅ Proper td cells
-   ✅ Color contrast (WCAG AA)
-   ✅ Focusable links
-   ✅ Keyboard navigation
-   ✅ Screen reader friendly
-   ✅ Clear row separators

## Animation & Transitions

### Hover Effects

```
- Row hover:  bg-slate-700/50 (200ms)
- Link hover: text-lime-300 (default transition)
- Button:     opacity/background change
```

### Transitions

```
- Duration:   transition (default 150ms)
- Easing:     ease (default cubic-bezier)
- Properties: all (applies to all properties)
```

---

## Summary

The table provides:

-   ✅ 7 columns of feedback information
-   ✅ Professional, scannable layout
-   ✅ Responsive design for all devices
-   ✅ Color-coded status badges
-   ✅ Truncated text with proper ellipsis
-   ✅ Interactive row hover effects
-   ✅ Quick-access "View Details" links
-   ✅ Pagination support
-   ✅ Empty state handling
-   ✅ Full-width responsive padding
