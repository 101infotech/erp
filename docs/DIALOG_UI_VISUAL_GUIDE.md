# Dialog UI System - Visual & Component Guide

## Dialog Types & Appearances

### 1. Default Dialog (Blue)

**Usage:** General information and neutral actions

```
┌─────────────────────────────────────┐
│ ℹ️  Dialog Title              ✕      │
│─────────────────────────────────────│
│                                     │
│ This is a general information       │
│ dialog with neutral styling.        │
│                                     │
│─────────────────────────────────────│
│                 [Cancel]  [Confirm] │
└─────────────────────────────────────┘
```

**Colors:** Blue border, light blue background, blue buttons

### 2. Success Dialog (Green)

**Usage:** Confirmations for successful operations, approvals

```
┌─────────────────────────────────────┐
│ ✓  Approve Action              ✕    │
│─────────────────────────────────────│
│                                     │
│ Are you sure you want to approve    │
│ this action? It cannot be undone.   │
│                                     │
│─────────────────────────────────────│
│                 [Cancel]  [Approve] │
└─────────────────────────────────────┘
```

**Colors:** Green border, light green background, green buttons

### 3. Danger Dialog (Red)

**Usage:** Destructive actions, deletions, critical operations

```
┌─────────────────────────────────────┐
│ ⚠️  Delete Item                ✕    │
│─────────────────────────────────────│
│                                     │
│ Are you sure you want to delete     │
│ this item? This cannot be undone.   │
│                                     │
│─────────────────────────────────────│
│                  [Cancel]  [Delete] │
└─────────────────────────────────────┘
```

**Colors:** Red border, light red background, red buttons

### 4. Warning Dialog (Yellow)

**Usage:** Important operations that need confirmation

```
┌─────────────────────────────────────┐
│ ⚠️  Warning              ✕           │
│─────────────────────────────────────│
│                                     │
│ This is an important operation.     │
│ Please confirm to proceed.          │
│                                     │
│─────────────────────────────────────│
│                 [Cancel]  [Proceed] │
└─────────────────────────────────────┘
```

**Colors:** Yellow border, light yellow background, yellow buttons

### 5. Info Dialog (Cyan)

**Usage:** Informational messages, tips, instructions

```
┌─────────────────────────────────────┐
│ ℹ️  Information                 ✕    │
│─────────────────────────────────────│
│                                     │
│ Here is some important information  │
│ you should know about.              │
│                                     │
│─────────────────────────────────────│
│                        [Close] [OK] │
└─────────────────────────────────────┘
```

**Colors:** Cyan border, light cyan background, cyan buttons

## Component Structure

### Confirm Dialog Structure

```
┌────────────────────────────────────────┐
│ [Header with icon] [Close Button] ✕    │
│────────────────────────────────────────│
│                                        │
│ [Message/Content Area]                 │
│                                        │
│────────────────────────────────────────│
│ [Cancel Button]       [Confirm Button] │
└────────────────────────────────────────┘
```

### Custom Dialog Structure

```
┌────────────────────────────────────────┐
│ [Title]                           ✕    │
│ [Optional Description]                 │
│────────────────────────────────────────│
│                                        │
│ [Custom Content/Form Area]             │
│                                        │
│────────────────────────────────────────│
│ [Footer Actions]                       │
└────────────────────────────────────────┘
```

## Size Variants

### Small (sm)

```
     Small Dialog
   ┌──────────────┐
   │ Content      │
   └──────────────┘
   Max Width: 384px
```

### Medium (md)

```
        Medium Dialog
   ┌────────────────────┐
   │ Content            │
   └────────────────────┘
   Max Width: 448px
```

### Large (lg)

```
           Large Dialog
   ┌─────────────────────────┐
   │ Content                 │
   └─────────────────────────┘
   Max Width: 512px
```

### Extra Large (xl)

```
              Extra Large Dialog
   ┌──────────────────────────────────┐
   │ Content                          │
   └──────────────────────────────────┘
   Max Width: 576px
```

### 2XL (2xl) - Default

```
                  2XL Dialog (Default)
   ┌────────────────────────────────────────────┐
   │ Content                                    │
   └────────────────────────────────────────────┘
   Max Width: 672px
```

## Styling Details

### Color Palette

#### Default (Blue)

-   Border: `border-blue-500`
-   Background: `bg-blue-50 dark:bg-blue-900/20`
-   Button: `bg-blue-600 hover:bg-blue-700`
-   Text: `text-blue-400`

#### Success (Green)

-   Border: `border-green-500`
-   Background: `bg-green-50 dark:bg-green-900/20`
-   Button: `bg-green-600 hover:bg-green-700`
-   Text: `text-green-400`

#### Danger (Red)

-   Border: `border-red-500`
-   Background: `bg-red-50 dark:bg-red-900/20`
-   Button: `bg-red-600 hover:bg-red-700`
-   Text: `text-red-400`

#### Warning (Yellow)

-   Border: `border-yellow-500`
-   Background: `bg-yellow-50 dark:bg-yellow-900/20`
-   Button: `bg-yellow-600 hover:bg-yellow-700`
-   Text: `text-yellow-400`

#### Info (Cyan)

-   Border: `border-cyan-500`
-   Background: `bg-cyan-50 dark:bg-cyan-900/20`
-   Button: `bg-cyan-600 hover:bg-cyan-700`
-   Text: `text-cyan-400`

### Dark Mode

All dialogs automatically adapt to dark mode:

```
Light Mode          Dark Mode
─────────────────────────────────
White background → Dark slate (800)
Light text      → Light gray text
Light border    → Dark border
```

**Dark Mode Colors:**

-   Background: `dark:bg-slate-800`
-   Border: `dark:border-slate-700`
-   Text: `dark:text-white / dark:text-gray-300`
-   Hover: `dark:hover:bg-slate-700`

## Animations

### Entrance Animation

-   Duration: 300ms
-   Effect: Fade in + Scale up (95% → 100%)
-   Easing: ease-out

```
Starts:  opacity-0, scale-95
Ends:    opacity-100, scale-100
```

### Exit Animation

-   Duration: 200ms
-   Effect: Fade out + Scale down (100% → 95%)
-   Easing: ease-in

```
Starts:  opacity-100, scale-100
Ends:    opacity-0, scale-95
```

### Backdrop Animation

-   Fade in: 300ms ease-out
-   Fade out: 200ms ease-in

## Accessibility Features

### Keyboard Navigation

```
Tab         → Move to next focusable element
Shift+Tab   → Move to previous focusable element
Escape      → Close dialog (unless persistent)
Enter       → Submit form / activate button
Space       → Toggle checkbox / activate button
```

### Focus Management

```
1. Dialog opens → Focus moved to first focusable element
2. Focus trap   → Tab cycles through focusable elements
3. Dialog closes → Focus returned to trigger element
```

### Semantic HTML

```
<div>
  ├── Backdrop (click to close)
  └── Dialog Container
      ├── Header (title, icon, close button)
      ├── Content (main message/form)
      └── Footer (action buttons)
```

### ARIA Labels

```
dialog      → role="dialog"
title       → aria-labelledby (heading id)
description → aria-describedby (description id)
buttons     → aria-label for icon-only buttons
```

## Visual Examples

### Delete Confirmation (Danger Type)

```
┌──────────────────────────────────────────┐
│ ⚠️  Delete Employee                    ✕  │
│──────────────────────────────────────────│
│                                          │
│ Are you sure you want to delete this     │
│ employee? This action cannot be undone.  │
│                                          │
│ Employee: John Doe                       │
│ ID: EMP-001                              │
│                                          │
│ This will:                               │
│ • Remove all employee records            │
│ • Delete payroll history                 │
│ • Remove from team assignments           │
│                                          │
│ ⚠️ This action is PERMANENT               │
│                                          │
│──────────────────────────────────────────│
│             [Cancel]    [Delete Employee]│
└──────────────────────────────────────────┘
```

### Form Dialog (Custom)

```
┌──────────────────────────────────────────┐
│ Edit Profile                           ✕  │
│ Make changes to your account              │
│──────────────────────────────────────────│
│                                          │
│ Full Name                                │
│ [__________________]                     │
│                                          │
│ Email                                    │
│ [__________________]                     │
│                                          │
│ Department                               │
│ [▼ Select Department]                    │
│                                          │
│ Bio                                      │
│ [________________________]               │
│                                          │
│──────────────────────────────────────────│
│             [Cancel]    [Save Changes]   │
└──────────────────────────────────────────┘
```

### Success Dialog

```
┌──────────────────────────────────────────┐
│ ✓  Payroll Approved                    ✕  │
│──────────────────────────────────────────│
│                                          │
│ The payroll for Q4 2025 has been         │
│ successfully approved.                   │
│                                          │
│ ✓ Status: Approved                       │
│ ✓ Amount: NPR 1,250,000                  │
│ ✓ Employees: 45                          │
│                                          │
│ You can now proceed to payment.          │
│                                          │
│──────────────────────────────────────────│
│             [Close]    [View Payroll]    │
└──────────────────────────────────────────┘
```

## Responsive Behavior

### Desktop (> 1024px)

-   Dialog centered on screen
-   Max width applied (default: 2xl)
-   Padding: 6px

### Tablet (768px - 1024px)

-   Dialog centered with responsive max width
-   Padding: 4px

### Mobile (< 768px)

-   Dialog takes up most of screen width
-   Max width: 100% with padding
-   Padding: 4px
-   Full viewport height available (scrollable if needed)

## State Examples

### Loading State

```
┌──────────────────────────────────────────┐
│ Processing...                          ✕  │
│──────────────────────────────────────────│
│                                          │
│        ⏳ Please wait...                  │
│                                          │
│        [Spinning animation]              │
│                                          │
│──────────────────────────────────────────│
│           [Cancel processing]            │
└──────────────────────────────────────────┘
```

### Error State

```
┌──────────────────────────────────────────┐
│ ✕  Error Occurred                      ✕  │
│──────────────────────────────────────────│
│                                          │
│ Failed to delete record.                 │
│                                          │
│ Error: This record is referenced by      │
│ other records and cannot be deleted.     │
│                                          │
│──────────────────────────────────────────│
│                           [Try Again]    │
└──────────────────────────────────────────┘
```

---

**For detailed usage, see [DIALOG_UI_QUICK_REF.md](DIALOG_UI_QUICK_REF.md)**
