# Professional Modal System Documentation

## Overview

A professional, reusable modal component has been implemented across the project to provide a consistent, modern UI for all dialogs and popups. This component uses a dark theme with proper styling, animations, and accessibility features.

## Component Location

The modal component is located at: `resources/views/components/professional-modal.blade.php`

## Features

✅ **Dark Theme**: Fully styled dark mode compatible with the project's design  
✅ **Flexible Icons**: Multiple icon options (trash, check, info, warning, question, exclamation)  
✅ **Color Variants**: Red, blue, green, yellow, purple icon colors  
✅ **Accessibility**: Proper focus management, keyboard support (Escape to close)  
✅ **Responsive**: Works on mobile and desktop  
✅ **Animations**: Smooth fade-in/fade-out transitions  
✅ **Customizable**: Footer slot for flexible button layouts

## Usage

### Basic Usage

```blade
<x-professional-modal
    id="myModal"
    title="Modal Title"
    icon="trash"
    iconColor="red"
    maxWidth="max-w-md">

    <!-- Your modal content goes here -->
    <p>This is the modal body content</p>

    <x-slot name="footer">
        <button type="button" onclick="closeModal('myModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel
        </button>
        <button type="submit"
            class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
            Delete
        </button>
    </x-slot>
</x-professional-modal>
```

### With Form

```blade
<x-professional-modal
    id="deleteModal"
    title="Delete Item"
    icon="trash"
    iconColor="red">

    <p>Are you sure you want to delete this item?</p>

    <x-slot name="footer">
        <button type="button" onclick="closeModal('deleteModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel
        </button>
        <form method="POST" action="{{ route('delete.item') }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
                Delete Item
            </button>
        </form>
    </x-slot>
</x-professional-modal>
```

## Component Props

| Prop        | Type    | Default       | Description                                                    |
| ----------- | ------- | ------------- | -------------------------------------------------------------- |
| `id`        | string  | Generated     | Unique identifier for the modal                                |
| `title`     | string  | ''            | Modal title/heading                                            |
| `subtitle`  | string  | ''            | Optional subtitle                                              |
| `icon`      | string  | 'exclamation' | Icon type (trash, check, info, warning, question, exclamation) |
| `iconColor` | string  | 'red'         | Icon color (red, blue, green, yellow, purple)                  |
| `maxWidth`  | string  | 'max-w-md'    | Max width (max-w-sm, max-w-md, max-w-lg, max-w-xl, max-w-2xl)  |
| `footer`    | boolean | true          | Show footer slot                                               |

## JavaScript Functions

### Open Modal

```javascript
openModal("modalId");
```

Opens a modal with the given ID and triggers show animation.

### Close Modal

```javascript
closeModal("modalId");
```

Closes a modal with the given ID.

## Icon Options

-   **trash**: Delete/removal icon
-   **check**: Confirmation/success icon
-   **info**: Information icon
-   **warning**: Warning/caution icon
-   **question**: Question/confirmation icon
-   **exclamation**: Alert/error icon

## Icon Colors

-   **red**: For destructive actions (delete)
-   **blue**: For information/neutral actions
-   **green**: For positive actions (confirm, approve)
-   **yellow**: For warnings
-   **purple**: For special actions

## Updated Files

The following files have been updated to use the professional modal component:

### 1. **Payroll Module**

-   `resources/views/admin/hrm/payroll/show.blade.php`

    -   Delete Draft Payroll modal
    -   Mark as Paid modal

-   `resources/views/admin/hrm/payroll/index.blade.php`
    -   Delete Confirmation modal

### 2. **Leave Management**

-   `resources/views/admin/hrm/leaves/show.blade.php`
    -   Reject Leave Request modal

### 3. **Complaint Management**

-   `resources/views/admin/complaints/show.blade.php`
    -   Delete Complaint modal

### 4. **Finance Module**

-   `resources/views/admin/finance/customers/show.blade.php`
    -   Upload Document modal

### 5. **User Management** (Already Well-Designed)

-   `resources/views/admin/users/index.blade.php`
    -   Set Password modal (maintained with custom styling)

## Implementation Best Practices

### 1. Naming Conventions

Use descriptive IDs that clearly indicate the action:

-   ✅ `deletePayrollModal`
-   ✅ `rejectLeaveModal`
-   ✅ `uploadDocumentModal`
-   ❌ `modal1`
-   ❌ `popup`

### 2. Icon Selection

-   Use **trash** for delete/remove actions (with red color)
-   Use **check** for confirmation/approval actions (with green color)
-   Use **question** for user confirmation needed (with red color)
-   Use **info** for information/neutral actions (with blue color)
-   Use **warning** for cautions (with yellow color)

### 3. Button Styling

Always include consistent button styling:

```blade
<!-- Cancel Button -->
<button type="button" onclick="closeModal('modalId')"
    class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
    Cancel
</button>

<!-- Destructive Action -->
<button type="submit"
    class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
    Delete
</button>

<!-- Positive Action -->
<button type="submit"
    class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
    Confirm
</button>
```

### 4. Form Integration

Always wrap form data in a form element with proper CSRF token:

```blade
<form method="POST" action="{{ route('item.destroy', $item) }}">
    @csrf
    @method('DELETE')
    <!-- Form fields and submit button -->
</form>
```

### 5. Content Organization

Structure modal content with proper spacing:

```blade
<!-- Information Section -->
<div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
    <p class="text-sm text-white"><span class="font-medium">Key:</span> Value</p>
</div>

<!-- Warning/Important Message -->
<p class="text-sm text-red-400 font-medium">⚠️ Important message</p>
```

## Accessibility

The component includes:

-   ✅ Keyboard support (Escape to close)
-   ✅ Click outside to close
-   ✅ Proper ARIA labels
-   ✅ Focus management
-   ✅ Semantic HTML
-   ✅ High contrast colors

## Migration Guide

### Old vs New

**Before:**

```blade
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50...">
    <div class="bg-slate-800...">
        <h3>Delete</h3>
        <!-- Content -->
    </div>
</div>
<script>
    function openDeleteModal() { ... }
    function closeDeleteModal() { ... }
</script>
```

**After:**

```blade
<x-professional-modal
    id="deleteModal"
    title="Delete"
    icon="trash"
    iconColor="red">
    <!-- Content -->
</x-professional-modal>
<!-- Use standard openModal/closeModal functions -->
```

## Common Patterns

### Confirmation Modal

```blade
<x-professional-modal
    id="confirmModal"
    title="Confirm Action"
    icon="question"
    iconColor="yellow">
    <p class="text-slate-300">Are you sure about this action?</p>
    <x-slot name="footer">
        <button onclick="closeModal('confirmModal')" class="...">Cancel</button>
        <button onclick="proceed()" class="...">Proceed</button>
    </x-slot>
</x-professional-modal>
```

### Success Modal

```blade
<x-professional-modal
    id="successModal"
    title="Success"
    icon="check"
    iconColor="green">
    <p class="text-slate-300">Action completed successfully!</p>
</x-professional-modal>
```

### Error Modal

```blade
<x-professional-modal
    id="errorModal"
    title="Error"
    icon="exclamation"
    iconColor="red">
    <p class="text-slate-300">{{ $errorMessage }}</p>
</x-professional-modal>
```

## Styling Customization

The modal uses Tailwind CSS classes. To customize:

1. Modify background colors: Change `bg-slate-800`, `bg-slate-700`, etc.
2. Modify border colors: Change `border-slate-700`
3. Modify text colors: Change `text-white`, `text-slate-300`, etc.
4. Modify rounded corners: Change `rounded-lg`

## Browser Support

✅ Chrome 90+  
✅ Firefox 88+  
✅ Safari 14+  
✅ Edge 90+

## Troubleshooting

### Modal not opening?

-   Verify the modal ID matches in `openModal()` call
-   Check browser console for JavaScript errors
-   Ensure the component is included in the Blade file

### Styling looks off?

-   Clear browser cache (Ctrl+Shift+Delete)
-   Verify Tailwind CSS is properly compiled
-   Check for CSS conflicts

### Form not submitting?

-   Verify form method (POST/DELETE) is correct
-   Check CSRF token is included
-   Verify route exists

## Future Enhancements

Potential improvements for the modal system:

-   [ ] Add loading state during form submission
-   [ ] Add toast notifications for success/error messages
-   [ ] Add animation options (fade, slide, scale)
-   [ ] Add modal stacking for multiple modals
-   [ ] Add accessibility improvements (ARIA live regions)
