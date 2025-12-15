# Dialog UI Quick Reference

## Using Dialogs in Your Code

### Simple Confirmation Dialog

```blade
<!-- Trigger Button -->
<button @click="$dispatch('open-confirm', 'confirm-action')" class="btn-danger">
    Delete
</button>

<!-- Dialog Component -->
<x-confirm-dialog
    name="confirm-action"
    title="Confirm Action"
    message="Are you sure?"
    type="danger"
    confirmText="Delete"
    form="actionForm"
/>

<!-- Hidden Form -->
<form id="actionForm" method="POST" action="/your-route" style="display: none;">
    @csrf
</form>
```

### Dialog with JavaScript Callback

```blade
<button @click="$dispatch('open-confirm', 'approve')" class="btn-success">
    Approve
</button>

<x-confirm-dialog
    name="approve"
    title="Approve?"
    message="Approve this item?"
    type="success"
    confirmText="Yes, Approve"
    onConfirm="handleApprove()"
/>

<script>
function handleApprove() {
    // Your JavaScript code here
    console.log('Approved!');
}
</script>
```

### Dialog with Dynamic Form (List Page)

```blade
<!-- Trigger Button with URL -->
<button type="button" onclick="deleteItem('{{ route('items.destroy', $item->id) }}')">
    Delete
</button>

<!-- Dialog (at bottom of page) -->
<x-confirm-dialog
    name="delete-item"
    title="Delete Item"
    message="Are you sure you want to delete this?"
    type="danger"
    confirmText="Delete"
    form="deleteForm"
/>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteItem(url) {
    document.getElementById('deleteForm').action = url;
    window.dispatchEvent(new CustomEvent('open-confirm', { detail: 'delete-item' }));
}
</script>
```

### Custom Dialog Component

```blade
<x-dialog name="edit-profile" title="Edit Profile" maxWidth="md">
    <form @submit.prevent="submitForm" class="space-y-4">
        <input type="text" placeholder="Name" class="w-full px-3 py-2 rounded border">
        <textarea placeholder="Bio" class="w-full px-3 py-2 rounded border"></textarea>

        <x-slot name="footer">
            <button type="button" @click="show = false" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
        </x-slot>
    </form>
</x-dialog>

<!-- Open dialog -->
<button @click="$dispatch('open-dialog', 'edit-profile')">Edit</button>
```

## Component Props

### Confirm Dialog Props

```blade
<x-confirm-dialog
    name="dialog-name"                    <!-- Required: unique identifier -->
    title="Title"                         <!-- Dialog title -->
    message="Your message here"           <!-- Confirmation message -->
    type="danger"                         <!-- default|success|danger|warning|info -->
    confirmText="Confirm"                 <!-- Confirm button text -->
    cancelText="Cancel"                   <!-- Cancel button text -->
    onConfirm="javascript code"           <!-- JavaScript callback (optional) -->
    form="formId"                         <!-- Form ID to submit (optional) -->
/>
```

### Dialog Props

```blade
<x-dialog
    name="dialog-name"                    <!-- Required: unique identifier -->
    title="Title"                         <!-- Dialog title -->
    description="Description"             <!-- Optional description -->
    type="default"                        <!-- default|success|danger|warning|info -->
    show="false"                          <!-- Show on load -->
    maxWidth="2xl"                        <!-- sm|md|lg|xl|2xl -->
    closeButton="true"                    <!-- Show close button -->
    backdrop="true"                       <!-- Show backdrop -->
    persistent="false"                    <!-- Prevent closing -->
>
    <!-- Your content here -->

    <x-slot name="footer">
        <!-- Footer buttons -->
    </x-slot>
</x-dialog>
```

## Dialog Types & Colors

| Type      | Color  | Use Case                             |
| --------- | ------ | ------------------------------------ |
| `default` | Blue   | General actions                      |
| `success` | Green  | Successful operations                |
| `danger`  | Red    | Destructive actions (delete, remove) |
| `warning` | Yellow | Important operations                 |
| `info`    | Cyan   | Information dialogs                  |

## Events

### Opening Dialogs

```javascript
// Confirm dialog
window.dispatchEvent(
    new CustomEvent("open-confirm", { detail: "dialog-name" })
);

// Regular dialog
window.dispatchEvent(new CustomEvent("open-dialog", { detail: "dialog-name" }));
```

### Closing Dialogs

```javascript
// Confirm dialog
window.dispatchEvent(
    new CustomEvent("close-confirm", { detail: "dialog-name" })
);

// Regular dialog
window.dispatchEvent(
    new CustomEvent("close-dialog", { detail: "dialog-name" })
);
```

## Accessibility

All dialogs include:

-   ✅ Keyboard navigation (Tab, Shift+Tab, Escape)
-   ✅ Focus management
-   ✅ ARIA labels
-   ✅ Semantic HTML
-   ✅ Screen reader support
-   ✅ Color contrast compliance

## Examples

### Delete with Confirmation

```blade
<button type="button" onclick="deleteRecord('{{ route('records.destroy', $record->id) }}')">
    Delete
</button>

<x-confirm-dialog
    name="delete-record"
    title="Delete Record"
    message="This action cannot be undone."
    type="danger"
    confirmText="Delete"
    form="deleteForm"
/>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteRecord(url) {
    document.getElementById('deleteForm').action = url;
    window.dispatchEvent(new CustomEvent('open-confirm', { detail: 'delete-record' }));
}
</script>
```

### Success Action

```blade
<button @click="$dispatch('open-confirm', 'publish-post')" class="btn-success">
    Publish
</button>

<x-confirm-dialog
    name="publish-post"
    title="Publish Post"
    message="Make this post visible to everyone?"
    type="success"
    confirmText="Publish"
    form="publishForm"
/>

<form id="publishForm" method="POST" action="{{ route('posts.publish', $post) }}" style="display: none;">
    @csrf
</form>
```

### Custom Form Dialog

```blade
<button @click="$dispatch('open-dialog', 'export-data')">Export</button>

<x-dialog name="export-data" title="Export Data" maxWidth="md">
    <form method="POST" action="{{ route('export') }}" class="space-y-4">
        @csrf

        <div>
            <label>Format</label>
            <select name="format" class="w-full border rounded px-3 py-2">
                <option>CSV</option>
                <option>Excel</option>
                <option>PDF</option>
            </select>
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" name="include_archived">
                <span class="ml-2">Include Archived</span>
            </label>
        </div>

        <x-slot name="footer">
            <button type="button" @click="show = false" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Export</button>
        </x-slot>
    </form>
</x-dialog>
```

## Best Practices

1. **Use Appropriate Types** - Match dialog type to action (danger for delete, success for approval)
2. **Clear Messages** - Keep confirmation messages brief and clear
3. **Consistent Naming** - Use descriptive dialog names (e.g., 'delete-user', 'approve-request')
4. **Test Keyboard Navigation** - Always test Tab, Shift+Tab, and Escape
5. **Mobile Testing** - Test dialogs on mobile devices
6. **Accessibility** - Ensure dialogs meet WCAG AA standards

## Migration Checklist

When replacing old `confirm()` dialogs:

-   [ ] Create confirmation dialog component
-   [ ] Update trigger button (remove onclick="return confirm()")
-   [ ] Add event dispatcher: `@click="$dispatch('open-confirm', 'name')"`
-   [ ] Create hidden form (if using form submission)
-   [ ] Test opening/closing
-   [ ] Test form submission
-   [ ] Test keyboard navigation
-   [ ] Test mobile responsiveness
-   [ ] Test accessibility with screen reader

---

**Need more help? See [DIALOG_UI_SYSTEM.md](DIALOG_UI_SYSTEM.md) for complete documentation.**
