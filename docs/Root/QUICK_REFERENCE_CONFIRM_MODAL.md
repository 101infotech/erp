# Confirm Modal - Quick Reference Guide

## Purpose
Replace JavaScript `confirm()` dialogs with a styled, reusable Alpine.js modal component.

---

## ✅ Basic Usage

### 1. Include the Component
Add this line before `@endsection` in your Blade view:
```blade
@include('components.confirm-modal')
```

### 2. Add Form ID
Give your form a unique ID:
```blade
<form action="{{ route('your.route') }}" method="POST" id="my-form">
    @csrf
    @method('DELETE')
    <!-- form content -->
</form>
```

### 3. Replace Confirm Dialog
**Before:**
```blade
<button type="submit" onclick="return confirm('Are you sure?')">
    Delete
</button>
```

**After:**
```blade
<button type="button" 
    onclick="confirmAction({
        title: 'Delete Item?',
        message: 'Are you sure you want to delete this item?',
        type: 'danger',
        confirmText: 'Yes, Delete',
        onConfirm: () => document.getElementById('my-form').submit()
    })">
    Delete
</button>
```

---

## 🎨 Modal Types

### Danger (Red)
Use for: Delete, Remove, Permanent actions
```javascript
type: 'danger'
```

### Warning (Amber)
Use for: Reset, Generate, Replace actions
```javascript
type: 'warning'
```

### Info (Blue)
Use for: Send, Email, Notify actions
```javascript
type: 'info'
```

### Success (Green)
Use for: Approve, Accept, Confirm actions
```javascript
type: 'success'
```

---

## 📝 Full Configuration Options

```javascript
confirmAction({
    title: 'Modal Title',           // Required - Header text
    message: 'Modal message',       // Required - Body text (supports HTML)
    type: 'danger',                 // Required - danger|warning|info|success
    confirmText: 'Confirm',         // Optional - Default: 'Confirm'
    cancelText: 'Cancel',           // Optional - Default: 'Cancel'
    onConfirm: () => {              // Required - Callback function
        // Your action here
        document.getElementById('form-id').submit();
    }
})
```

---

## 💡 Common Examples

### Example 1: Delete User
```javascript
confirmAction({
    title: 'Delete User?',
    message: 'Are you sure you want to delete <strong>John Doe</strong>?<br><br>This action cannot be undone.',
    type: 'danger',
    confirmText: 'Yes, Delete',
    onConfirm: () => document.getElementById('delete-user-form-123').submit()
})
```

### Example 2: Send Email
```javascript
confirmAction({
    title: 'Send Reset Link?',
    message: 'A password reset link will be sent to <strong>user@example.com</strong>.',
    type: 'info',
    confirmText: 'Yes, Send',
    onConfirm: () => document.getElementById('send-email-form').submit()
})
```

### Example 3: Generate Password
```javascript
confirmAction({
    title: 'Generate Password?',
    message: 'This will replace the current password.<br><br><strong class="text-amber-400">Warning:</strong> User will be logged out.',
    type: 'warning',
    confirmText: 'Yes, Generate',
    onConfirm: () => document.getElementById('password-form').submit()
})
```

### Example 4: Approve Request
```javascript
confirmAction({
    title: 'Approve Request?',
    message: 'Are you sure you want to approve this resource request?',
    type: 'success',
    confirmText: 'Yes, Approve',
    onConfirm: () => document.getElementById('approve-form').submit()
})
```

---

## 🔧 Advanced Usage

### With AJAX
```javascript
confirmAction({
    title: 'Delete Item?',
    message: 'This will permanently delete the item.',
    type: 'danger',
    confirmText: 'Yes, Delete',
    onConfirm: async () => {
        try {
            const response = await fetch('/api/items/123', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            alert('Error deleting item');
        }
    }
})
```

### With Multiple Actions
```javascript
confirmAction({
    title: 'Process Request?',
    message: 'This will send emails and update the database.',
    type: 'warning',
    confirmText: 'Yes, Process',
    onConfirm: () => {
        // Multiple forms or actions
        document.getElementById('form-1').submit();
        document.getElementById('form-2').submit();
    }
})
```

### With Conditional Logic
```javascript
confirmAction({
    title: 'Save Changes?',
    message: `You have ${unsavedChanges} unsaved changes. Save before leaving?`,
    type: 'warning',
    confirmText: 'Yes, Save',
    onConfirm: () => {
        if (validateForm()) {
            document.getElementById('save-form').submit();
        } else {
            alert('Please fix validation errors');
        }
    }
})
```

---

## 🎯 Form ID Naming Convention

Use descriptive, unique IDs:

### Pattern
```
{action}-{entity}-form-{id}
```

### Examples
- `delete-user-form-123`
- `approve-request-form-456`
- `send-email-form`
- `generate-password-form`
- `unlink-account-form-789`

### For Mobile vs Desktop
- Desktop: `delete-user-form-123`
- Mobile: `delete-user-mobile-form-123`

---

## ✅ Checklist

When adding a new confirmation modal:

- [ ] Include `@include('components.confirm-modal')` in view
- [ ] Add unique `id` to form
- [ ] Change button `type="submit"` to `type="button"`
- [ ] Replace `onclick="return confirm(...)"` with `onclick="confirmAction(...)"`
- [ ] Choose appropriate modal type (danger/warning/info/success)
- [ ] Write clear title and message
- [ ] Use HTML formatting if needed (bold, line breaks)
- [ ] Test modal opens correctly
- [ ] Test form submits on confirm
- [ ] Test modal closes on cancel
- [ ] Test ESC key closes modal

---

## 🐛 Common Mistakes

### ❌ Mistake 1: Missing Form ID
```blade
<form action="...">  <!-- No ID! -->
    <button onclick="confirmAction({ ... onConfirm: () => document.getElementById('my-form').submit() })">
```
**Fix:** Add `id="my-form"` to the form

---

### ❌ Mistake 2: Using type="submit"
```blade
<button type="submit" onclick="confirmAction(...)">  <!-- Wrong! -->
```
**Fix:** Change to `type="button"`

---

### ❌ Mistake 3: Wrong Form ID in onConfirm
```blade
<form id="delete-user-form-123">
    <button onclick="confirmAction({ ... onConfirm: () => document.getElementById('delete-form').submit() })">
```
**Fix:** Match the form ID exactly

---

### ❌ Mistake 4: Forgetting to Include Component
```blade
<!-- No @include('components.confirm-modal') -->
```
**Fix:** Add before `@endsection`

---

### ❌ Mistake 5: HTML Not Escaped
```blade
message: 'Delete <strong>{{ $user->name }}</strong>?'  // Wrong in onclick
```
**Fix:** Use escaped quotes:
```blade
message: 'Delete <strong>{{ $user->name }}</strong>?'
```
Or better, use single quotes inside double quotes:
```blade
onclick="confirmAction({ message: 'Delete <strong>{{ $user->name }}</strong>?' })"
```

---

## 📚 Real-World Examples

### From Users Index Page

**Desktop Delete:**
```blade
<form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="delete-user-form-{{ $user->id }}">
    @csrf
    @method('DELETE')
    <button type="button" 
        onclick="confirmAction({
            title: 'Delete User & Employee?',
            message: 'Are you sure you want to delete <strong>{{ $user->name }}</strong>?<br><br>This will permanently delete:<br>• User login account<br>• Employee record<br>• All attendance data<br><br><strong class=\"text-red-400\">This action cannot be undone!</strong>',
            type: 'danger',
            confirmText: 'Yes, Delete Everything',
            onConfirm: () => document.getElementById('delete-user-form-{{ $user->id }}').submit()
        })"
        class="...">
        Delete
    </button>
</form>
```

**Password Reset:**
```blade
<form action="{{ route('admin.users.send-reset-link', $user) }}" method="POST" id="send-reset-link-form">
    @csrf
    <button type="button"
        onclick="confirmAction({
            title: 'Send Password Reset Link?',
            message: 'A password reset link will be sent to <strong>{{ $user->email }}</strong>.',
            type: 'info',
            confirmText: 'Yes, Send Link',
            onConfirm: () => document.getElementById('send-reset-link-form').submit()
        })"
        class="...">
        Send Reset Link
    </button>
</form>
```

---

## 🎨 Styling Reference

Modal automatically styles based on type:

| Type | Icon | Button Color | Border Color |
|------|------|--------------|--------------|
| danger | ⚠️ Triangle | Red gradient | Red |
| warning | ⚠️ Circle | Amber gradient | Amber |
| info | ℹ️ Circle | Blue gradient | Blue |
| success | ✓ Circle | Green gradient | Green |

---

## 🔍 Debugging

### Modal Not Opening?
1. Check browser console for errors
2. Verify `confirmAction()` function exists
3. Check `@include('components.confirm-modal')` is present
4. Verify Alpine.js is loaded

### Form Not Submitting?
1. Check form ID matches exactly
2. Verify `onConfirm` callback is correct
3. Check browser console for errors
4. Test form submission without modal first

### Styling Issues?
1. Check Tailwind CSS classes are loading
2. Verify Alpine.js directives are working
3. Check for CSS conflicts
4. Inspect element in DevTools

---

## 📖 Resources

- **Component File:** `/resources/views/components/confirm-modal.blade.php`
- **Full Documentation:** `/docs/Fixes/CONFIRM_MODAL_REPLACEMENT.md`
- **Examples:** `/resources/views/admin/users/index.blade.php`
- **Alpine.js Docs:** https://alpinejs.dev

---

_Last Updated: January 2025_
