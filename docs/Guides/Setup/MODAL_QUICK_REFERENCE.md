# Professional Modal System - Quick Reference

## Quick Start

### 1. Use the Component

```blade
<x-professional-modal id="myModal" title="My Title">
    Content here
</x-professional-modal>
```

### 2. Add Button to Open

```blade
<button onclick="openModal('myModal')">Open</button>
```

### 3. Done!

## Icon & Color Quick Reference

| Action   | Icon        | Color      | Example                                                     |
| -------- | ----------- | ---------- | ----------------------------------------------------------- |
| Delete   | trash       | red        | `<x-professional-modal icon="trash" iconColor="red">`       |
| Confirm  | check       | green      | `<x-professional-modal icon="check" iconColor="green">`     |
| Question | question    | red/yellow | `<x-professional-modal icon="question" iconColor="yellow">` |
| Info     | info        | blue       | `<x-professional-modal icon="info" iconColor="blue">`       |
| Warning  | warning     | yellow     | `<x-professional-modal icon="warning" iconColor="yellow">`  |
| Alert    | exclamation | red        | `<x-professional-modal icon="exclamation" iconColor="red">` |

## Size Options

```blade
<!-- Small (default) -->
<x-professional-modal maxWidth="max-w-sm">

<!-- Medium (default) -->
<x-professional-modal maxWidth="max-w-md">

<!-- Large -->
<x-professional-modal maxWidth="max-w-lg">

<!-- XL -->
<x-professional-modal maxWidth="max-w-xl">

<!-- 2XL -->
<x-professional-modal maxWidth="max-w-2xl">
```

## Common Patterns

### Delete Confirmation

```blade
<x-professional-modal id="deleteModal" title="Delete?" icon="trash" iconColor="red">
    <p>Are you sure? This cannot be undone.</p>
    <x-slot name="footer">
        <button onclick="closeModal('deleteModal')" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel
        </button>
        <form method="POST" action="{{ route('item.destroy') }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
                Delete
            </button>
        </form>
    </x-slot>
</x-professional-modal>
```

### Approval/Confirmation

```blade
<x-professional-modal id="approveModal" title="Approve Request?" icon="check" iconColor="green">
    <p>Approving this will notify the user.</p>
    <x-slot name="footer">
        <button onclick="closeModal('approveModal')" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel
        </button>
        <button onclick="approveAction()" class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
            Approve
        </button>
    </x-slot>
</x-professional-modal>
```

### Form Modal

```blade
<x-professional-modal id="formModal" title="Add New" icon="check" iconColor="green">
    <form method="POST" action="{{ route('store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Name</label>
            <input type="text" name="name" required class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2.5">
        </div>
        <x-slot name="footer">
            <button type="button" onclick="closeModal('formModal')" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
                Create
            </button>
        </x-slot>
    </form>
</x-professional-modal>
```

## All Props

```blade
<x-professional-modal
    id="myModal"              <!-- Unique ID (required) -->
    title="Title"             <!-- Modal title (required) -->
    subtitle="Subtitle"       <!-- Optional subtitle -->
    icon="trash"              <!-- Icon type: trash|check|info|warning|question|exclamation -->
    iconColor="red"           <!-- Icon color: red|blue|green|yellow|purple -->
    maxWidth="max-w-md"       <!-- Size: max-w-sm|md|lg|xl|2xl -->
    footer="true"             <!-- Show footer slot (default: true) -->
>
    Content here
    <x-slot name="footer">
        Buttons here
    </x-slot>
</x-professional-modal>
```

## JavaScript Functions

```javascript
// Open a modal
openModal("myModal");

// Close a modal
closeModal("myModal");

// Alternative close (from within footer button)
onclick = "closeModal('modalId')";
```

## Button Styling

### Cancel/Close Button

```blade
<button type="button" onclick="closeModal('id')"
    class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
    Cancel
</button>
```

### Delete Button

```blade
<button type="submit"
    class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
    Delete
</button>
```

### Approve/Confirm Button

```blade
<button type="submit"
    class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
    Confirm
</button>
```

### Info/Neutral Button

```blade
<button type="submit"
    class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition">
    Submit
</button>
```

## With Icon in Button

```blade
<button class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
    </svg>
    Delete
</button>
```

## Where to Find

-   **Component**: `resources/views/components/professional-modal.blade.php`
-   **Documentation**: `docs/MODAL_SYSTEM.md`
-   **Summary**: `MODAL_IMPLEMENTATION_COMPLETE.md`
-   **Used In**:
    -   `resources/views/admin/hrm/payroll/show.blade.php`
    -   `resources/views/admin/hrm/payroll/index.blade.php`
    -   `resources/views/admin/hrm/leaves/show.blade.php`
    -   `resources/views/admin/complaints/show.blade.php`
    -   `resources/views/admin/finance/customers/show.blade.php`

## Quick Tips

✅ Always use descriptive modal IDs  
✅ Match icon and color to the action  
✅ Keep titles short and clear  
✅ Use subtitle for additional context  
✅ Include warning messages for destructive actions  
✅ Use consistent button styling  
✅ Test on mobile  
✅ Verify form submissions work

## Don't Forget

```blade
<!-- Always include footer slot for buttons -->
<x-slot name="footer">
    <!-- Buttons go here -->
</x-slot>

<!-- Always use unique IDs -->
id="uniqueModalName"

<!-- Always include onclick for opening -->
onclick="openModal('uniqueModalName')"
```

## Need Help?

See full documentation at: `docs/MODAL_SYSTEM.md`
