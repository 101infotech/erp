# Dialog & Popup UI System

## Overview

This document describes the unified dialog and popup system for the ERP application. All dialogs, modals, and confirmation popups now use modern, styled components with proper dark mode support and accessibility features.

## Components

### 1. Dialog Component

The base dialog component for displaying content in a modal overlay.

**Location:** `resources/views/components/dialog.blade.php`

**Props:**

-   `name` (string, required) - Unique identifier for the dialog
-   `title` (string) - Dialog title
-   `description` (string) - Optional description text
-   `type` (string) - Dialog type: `default`, `success`, `danger`, `warning`, `info`
-   `show` (boolean) - Whether to show dialog on load
-   `maxWidth` (string) - Max width: `sm`, `md`, `lg`, `xl`, `2xl`
-   `closeButton` (boolean) - Show close button
-   `backdrop` (boolean) - Show backdrop
-   `persistent` (boolean) - Prevent closing on backdrop click/escape

**Usage Example:**

```blade
<x-dialog name="delete-item" type="danger" title="Delete Item" description="This action cannot be undone.">
    Are you sure you want to delete this item?

    <x-slot name="footer">
        <button @click="show = false" class="btn-secondary">Cancel</button>
        <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Delete</button>
        </form>
    </x-slot>
</x-dialog>
```

### 2. Confirm Dialog Component

Specialized component for confirmation dialogs with action handling.

**Location:** `resources/views/components/confirm-dialog.blade.php`

**Props:**

-   `name` (string, required) - Unique identifier
-   `title` (string) - Dialog title
-   `message` (string) - Confirmation message
-   `confirmText` (string) - Confirm button text
-   `cancelText` (string) - Cancel button text
-   `type` (string) - Dialog type: `default`, `danger`, `warning`, `success`
-   `onConfirm` (string) - JavaScript callback or code to execute
-   `form` (string) - Optional form ID to submit on confirmation

**Usage Example - With Callback:**

```blade
<x-confirm-dialog
    name="approve-payroll"
    title="Approve Payroll"
    message="Are you sure you want to approve this payroll?"
    type="success"
    confirmText="Approve"
    onConfirm="handleApprovePayroll()"
/>
```

**Usage Example - With Form:**

```blade
<x-confirm-dialog
    name="delete-employee"
    title="Delete Employee"
    message="This will permanently remove the employee from the system."
    type="danger"
    confirmText="Delete"
    form="delete-employee-form"
/>

<form id="delete-employee-form" method="POST" action="{{ route('employees.destroy', $employee) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
```

## JavaScript API

### DialogManager

Manages general dialogs.

```javascript
import { DialogManager } from "./dialog-manager.js";

// Open a dialog
DialogManager.open("dialog-name");

// Close a dialog
DialogManager.close("dialog-name");

// Close all dialogs
DialogManager.closeAll();
```

### ConfirmDialog

Manages confirmation dialogs.

```javascript
import { ConfirmDialog } from "./dialog-manager.js";

// Show confirmation dialog
ConfirmDialog.show("confirm-dialog-name");

// Close confirmation dialog
ConfirmDialog.close("confirm-dialog-name");
```

### Notify

Shows notifications/toasts.

```javascript
import { Notify } from "./dialog-manager.js";

Notify.success("Operation successful!");
Notify.error("An error occurred");
Notify.warning("Please be careful");
Notify.info("Here is some information");
```

## Migration from Old System

### From `confirm()` to New Dialog:

**Before:**

```blade
<button onclick="return confirm('Are you sure?')">Delete</button>
```

**After:**

```blade
<button @click="$dispatch('open-confirm', 'confirm-delete')">Delete</button>

<x-confirm-dialog
    name="confirm-delete"
    message="Are you sure you want to delete this?"
    onConfirm="document.getElementById('deleteForm').submit()"
/>

<form id="deleteForm" method="POST" action="/delete" style="display: none;">
    @csrf
</form>
```

### From `alert()` to Dialog:

**Before:**

```javascript
alert("Operation failed!");
```

**After:**

```javascript
import { Notify } from "./dialog-manager.js";

Notify.error("Operation failed!");
```

## Events

### Dialog Events

-   `open-dialog` - Open a dialog by name
-   `close-dialog` - Close a dialog by name

### Confirm Dialog Events

-   `open-confirm` - Show confirmation dialog
-   `close-confirm` - Close confirmation dialog

## Styling

All components support:

-   **Dark Mode** - Automatic dark mode support with Tailwind dark classes
-   **Accessibility** - ARIA labels, keyboard navigation (Tab, Shift+Tab, Escape)
-   **Animations** - Smooth transitions for open/close
-   **Responsive** - Works on all screen sizes

## Types & Colors

| Type      | Color  | Use Case                    |
| --------- | ------ | --------------------------- |
| `default` | Blue   | General information dialogs |
| `success` | Green  | Success confirmations       |
| `danger`  | Red    | Destructive actions         |
| `warning` | Yellow | Warning messages            |
| `info`    | Cyan   | Informational dialogs       |

## Keyboard Navigation

-   **Tab** - Move to next focusable element
-   **Shift+Tab** - Move to previous focusable element
-   **Escape** - Close dialog (if not persistent)
-   **Enter** - Submit form or activate button

## Examples

### Delete Confirmation

```blade
<x-confirm-dialog
    name="delete-service"
    title="Delete Service"
    message="Are you sure you want to delete this service? This action cannot be undone."
    type="danger"
    confirmText="Delete Service"
    cancelText="Keep Service"
    form="deleteServiceForm"
/>

<form id="deleteServiceForm" method="POST" action="{{ route('services.destroy', $service) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<button @click="$dispatch('open-confirm', 'delete-service')" class="btn-danger">
    Delete Service
</button>
```

### Success Notification

```blade
<x-dialog name="success" type="success" title="Success">
    <div class="space-y-2">
        <p>✓ Your operation was completed successfully!</p>
        <button @click="show = false" class="btn-primary">Close</button>
    </div>
</x-dialog>
```

### Custom Form Dialog

```blade
<x-dialog name="edit-profile" title="Edit Profile" maxWidth="md">
    <form @submit.prevent="submitForm">
        <input type="text" placeholder="Name" class="input w-full">
        <textarea placeholder="Bio" class="input w-full mt-4"></textarea>

        <x-slot name="footer">
            <button @click="show = false" type="button" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Save Changes</button>
        </x-slot>
    </form>
</x-dialog>
```

## Accessibility Features

-   **Screen Reader Support** - Proper ARIA labels and roles
-   **Focus Management** - Automatic focus management with trap
-   **Keyboard Navigation** - Full keyboard support
-   **Semantic HTML** - Proper heading hierarchy
-   **Color Contrast** - WCAG AA compliant colors
-   **Reduced Motion** - Respects user's motion preferences

## Best Practices

1. **Always provide a title** for accessibility
2. **Use appropriate type** for visual context
3. **Keep messages concise** - Brief, clear language
4. **Confirm destructive actions** - Always confirm deletions
5. **Use `persistent: true`** for important operations
6. **Test keyboard navigation** - Ensure Tab key works
7. **Mobile friendly** - Test on mobile devices

## Migration Status

See the project documentation for tracking which pages have been migrated to the new dialog system.
