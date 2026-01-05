# Service Leads - Permissions & Access Control Guide

## Overview

The Service Leads Management module implements a **role-based access control** system that differentiates permissions between Administrators and Employees. This guide details the permission structure, implementation, and access patterns.

---

## Permission Model

### System Architecture

The ERP uses a **simple role-based authentication** system with **individual access control**:
- User model has a `role` field (values: `admin`, `employee`)
- User model has a `can_access_leads` boolean field for granular employee access control
- Custom middleware enforces granular action-based permissions
- No external permission packages (Spatie, etc.) required

### Individual Staff Access Control

**New Feature**: Admins can enable/disable leads module access for individual employees.

- **Default Behavior**: All employees have `can_access_leads = false` by default
- **Admin Control**: Only admins can toggle leads access for employees
- **Per-Employee Basis**: Access is controlled individually, not role-wide
- **Database Field**: `users.can_access_leads` (boolean, default: false)

**Benefits**:
- Fine-grained control over who can access the leads module
- Not all employees need access to leads (e.g., HR staff, accountants)
- Admin can grant/revoke access without changing user roles
- Clear audit trail of who has access

### Access Control Components

1. **Database Field**: `users.can_access_leads` - Boolean flag per user
2. **Middleware**: `EnsureCanManageLeads` - Located at `app/Http/Middleware/EnsureCanManageLeads.php`
3. **Middleware Alias**: `can.manage.leads` - Registered in `bootstrap/app.php`
4. **Route Protection**: Applied to all lead routes in `routes/web.php`
5. **Toggle Endpoint**: `POST /admin/users/{user}/toggle-leads-access` - Admin-only route

---

## Role-Based Permissions Matrix

### Administrator Access

Admins have **full unrestricted access** to all lead management functions:

| Action | Permitted | Description |
|--------|-----------|-------------|
| **View** | ✅ Yes | View all leads (any status, any assignee) |
| **Create** | ✅ Yes | Create new leads |
| **Edit** | ✅ Yes | Edit any lead's details |
| **Delete** | ✅ Yes | Permanently delete any lead |
| **Assign** | ✅ Yes | Assign/reassign leads to any user |
| **Status Update** | ✅ Yes | Change status of any lead |
| **Analytics** | ✅ Yes | View analytics dashboard, export reports |
| **Export** | ✅ Yes | Export leads to Excel/PDF |

### Employee Access

Employees have **limited access** restricted to their assigned leads **AND must be granted access**:

| Action | Permitted | Description |
|--------|-----------|-------------|
| **Module Access** | ⚠️ Required | Must have `can_access_leads = true` (set by admin) |
| **View** | ⚠️ Limited | Only view leads assigned to them (if access granted) |
| **Create** | ❌ No | Cannot create new leads |
| **Edit** | ⚠️ Limited | Can update status of assigned leads only (if access granted) |
| **Delete** | ❌ No | Cannot delete leads |
| **Assign** | ❌ No | Cannot assign leads to others |
| **Status Update** | ⚠️ Limited | Update status of assigned leads only (if access granted) |
| **Analytics** | ❌ No | Cannot access analytics dashboard |
| **Export** | ❌ No | Cannot export reports |

**Important**: Even if an employee role user is assigned leads, they cannot access the module unless an admin enables `can_access_leads` for their account.

---

## Implementation Details

### Middleware: EnsureCanManageLeads

**File**: `app/Http/Middleware/EnsureCanManageLeads.php`

```php
public function handle(Request $request, Closure $next, string $action = 'view'): Response
{
    $user = $request->user();

    // Admins have full access
    if ($user->role === 'admin') {
        return $next($request);
    }

    // Employees have limited access
    if ($user->role === 'employee') {
        // Check if employee has access to leads module
        if (!$user->can_access_leads) {
            abort(403, 'You do not have access to the leads management module. Please contact your administrator.');
        }

        // Allow view and edit (status updates) for assigned leads
        if (in_array($action, ['view', 'edit'])) {
            $lead = $request->route('lead');
            
            if (!$lead) {
                // Viewing list - controller filters to assigned leads
                return $next($request);
            }
            
            // Check if lead is assigned to this employee
            if ($lead->assigned_to === $user->id) {
                return $next($request);
            }
            
            abort(403, 'You can only access leads assigned to you.');
        }
        
        abort(403, 'You do not have permission to perform this action.');
    }

    abort(403, 'You do not have permission to access this module.');
}
```

### Action Parameters

The middleware accepts the following action parameters:

- **`view`** - Read-only access to leads
- **`create`** - Permission to create new leads
- **`edit`** - Permission to modify lead details
- **`delete`** - Permission to delete leads
- **`assign`** - Permission to assign leads to users
- **`analytics`** - Permission to view analytics & reports

### Route Protection Examples

**View Routes** (Admin + Employee with assigned leads):
```php
Route::get('/', [ServiceLeadController::class, 'index'])
    ->middleware('can.manage.leads:view')
    ->name('index');

Route::get('/{lead}', [ServiceLeadController::class, 'show'])
    ->middleware('can.manage.leads:view')
    ->name('show');
```

**Create Routes** (Admin only):
```php
Route::get('/create', [ServiceLeadController::class, 'create'])
    ->middleware('can.manage.leads:create')
    ->name('create');

Route::post('/', [ServiceLeadController::class, 'store'])
    ->middleware('can.manage.leads:create')
    ->name('store');
```

**Edit Routes** (Admin only):
```php
Route::get('/{lead}/edit', [ServiceLeadController::class, 'edit'])
    ->middleware('can.manage.leads:edit')
    ->name('edit');

Route::put('/{lead}', [ServiceLeadController::class, 'update'])
    ->middleware('can.manage.leads:edit')
    ->name('update');
```

**Status Update** (Admin + Employee for assigned leads):
```php
Route::patch('/{lead}/status', [ServiceLeadController::class, 'updateStatus'])
    ->middleware('can.manage.leads:edit')
    ->name('update-status');
```

**Assign Routes** (Admin only):
```php
Route::patch('/{lead}/assign', [ServiceLeadController::class, 'assign'])
    ->middleware('can.manage.leads:assign')
    ->name('assign');
```

**Delete Routes** (Admin only):
```php
Route::delete('/{lead}', [ServiceLeadController::class, 'destroy'])
    ->middleware('can.manage.leads:delete')
    ->name('destroy');
```

**Analytics Routes** (Admin only):
```php
Route::get('/analytics', [LeadAnalyticsController::class, 'index'])
    ->middleware('can.manage.leads:analytics')
    ->name('analytics');

Route::get('/export/excel', [LeadAnalyticsController::class, 'exportExcel'])
    ->middleware('can.manage.leads:analytics')
    ->name('export.excel');
```

---

## Controller-Level Filtering

### Employee Lead Filtering

The `ServiceLeadController::index()` method automatically filters leads for employees:

```php
public function index(Request $request)
{
    $query = ServiceLead::with(['assignedTo', 'createdBy'])
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc');

    // If employee, show only their assigned leads
    if ($request->user()->role === 'employee') {
        $query->where('assigned_to', $request->user()->id);
    }

    // ... rest of filters and pagination
}
```

### Assigned To Filter Restriction

The assigned_to filter is restricted to admins only:

```php
// Assigned to filter (admin only)
if ($request->filled('assigned_to') && $request->user()->role === 'admin') {
    $query->assignedTo($request->assigned_to);
}
```

This prevents employees from bypassing the security by manipulating filter parameters.

---

## Managing Staff Access to Leads

### Enabling/Disabling Leads Access

**Admin-Only Feature**: Grant or revoke leads module access for individual employees.

### How to Toggle Access

**Via User Profile Page**:

1. Navigate to **Admin Panel** → **Users**
2. Click on any employee user to view their profile
3. Click the **"Enable Leads Access"** or **"Disable Leads Access"** button
4. Confirmation message will appear
5. Employee can now access (or cannot access) the leads module

**Visual Indicators**:
- **Profile Card**: Shows "Leads Module Access: Enabled ✓" or "Disabled ✗"
- **Toggle Button**: Green (Enable) when disabled, Orange (Disable) when enabled
- **Button Location**: Top-right action area of user profile page

### Via Code (Programmatic)

```php
// Enable leads access for an employee
$employee = User::find($employeeId);
$employee->can_access_leads = true;
$employee->save();

// Disable leads access
$employee->can_access_leads = false;
$employee->save();
```

### Route Endpoint

```php
POST /admin/users/{user}/toggle-leads-access
```

**Controller Method**: `UserController@toggleLeadsAccess`

**Implementation**:
```php
public function toggleLeadsAccess(User $user)
{
    // Only employees can have their leads access toggled
    if ($user->role !== 'employee') {
        return redirect()->back()
            ->with('error', 'Leads access can only be toggled for employees.');
    }

    $user->can_access_leads = !$user->can_access_leads;
    $user->save();

    $status = $user->can_access_leads ? 'enabled' : 'disabled';
    
    return redirect()->back()
        ->with('success', "Leads module access has been {$status} for {$user->name}.");
}
```

### Access Control Flow

```
Employee attempts to access leads module
              ↓
Middleware checks: user.can_access_leads == true?
              ↓
         ┌────┴────┐
         NO       YES
         ↓         ↓
    403 Error    Proceed to action check
                      ↓
              Check if action is 'view' or 'edit'
                      ↓
                 ┌────┴────┐
                 NO       YES
                 ↓         ↓
            403 Error   Check lead ownership
                            ↓
                       ┌────┴────┐
                       NO       YES
                       ↓         ↓
                  403 Error   Allow access
```

### Default Behavior

- **New Employees**: `can_access_leads = false` (access disabled by default)
- **Existing Employees**: Must be manually granted access by admin
- **Admin Users**: Always have full access (field ignored)

### Use Cases

**Scenario 1**: Sales Team Only
- Enable leads access for sales employees
- Disable for HR, accounting, and other departments
- Only sales team sees "Service Leads" in navigation

**Scenario 2**: Gradual Rollout
- Start with a few test employees
- Enable access as they complete training
- Disable if employee changes departments

**Scenario 3**: Temporary Access
- Enable for a contractor during a project
- Disable when project completes
- No need to change role or delete account

---

## Controller-Level Filtering

### Conditional UI Elements

Views should conditionally display action buttons based on user role:

**Example - Index Page (index.blade.php)**:
```blade
@if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Lead
    </a>
@endif
```

**Example - Show Page (show.blade.php)**:
```blade
{{-- Quick Actions Sidebar - Admin Only --}}
@if(auth()->user()->role === 'admin')
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-100 mb-4">Quick Actions</h3>
        
        {{-- Assign Lead Form --}}
        <form action="{{ route('admin.leads.assign', $lead) }}" method="POST">
            @csrf
            @method('PATCH')
            <!-- ... form fields -->
        </form>
        
        {{-- Delete Button --}}
        <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete Lead</button>
        </form>
    </div>
@endif

{{-- Status Update - Both Admin and Assigned Employee --}}
@if(auth()->user()->role === 'admin' || $lead->assigned_to === auth()->id())
    <form action="{{ route('admin.leads.update-status', $lead) }}" method="POST">
        @csrf
        @method('PATCH')
        <!-- ... status update form -->
    </form>
@endif
```

### Navigation Menu Access

The sidebar navigation item should be visible to all authenticated users (admin + employee):

```blade
{{-- resources/views/admin/layouts/app.blade.php --}}
<li>
    <a href="{{ route('admin.leads.index') }}" 
       class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
        <i class="fas fa-clipboard"></i>
        <span>Service Leads</span>
    </a>
</li>
```

Both admins and employees can see the menu item, but employees will only see their assigned leads.

---

## Security Considerations

### 1. Multi-Layer Protection

The system implements **defense in depth**:
- **Route Middleware**: First line of defense - blocks unauthorized routes
- **Controller Filtering**: Second layer - filters data at query level
- **View Conditionals**: Third layer - hides UI elements from unauthorized users

### 2. Route Parameter Binding

Laravel's implicit route model binding is leveraged for security:
```php
Route::get('/{lead}', [ServiceLeadController::class, 'show'])
```

The `{lead}` parameter automatically fetches the `ServiceLead` model, allowing the middleware to check ownership:
```php
$lead = $request->route('lead');
if ($lead->assigned_to === $user->id) {
    return $next($request);
}
```

### 3. Mass Assignment Protection

The `ServiceLead` model uses `$fillable` to prevent mass assignment vulnerabilities:
```php
protected $fillable = [
    'client_name',
    'client_email',
    // ... other fields
    'assigned_to',
    'status',
];
```

### 4. XSS Protection

All user inputs displayed in views use Blade's `{{ }}` syntax for automatic escaping:
```blade
<td>{{ $lead->client_name }}</td>
<td>{{ $lead->service_type }}</td>
```

---

## Testing Checklist

### Admin User Tests

- [ ] Can view all leads (including those assigned to others)
- [ ] Can create new leads
- [ ] Can edit any lead's details
- [ ] Can delete any lead
- [ ] Can assign/reassign leads to any user
- [ ] Can update status of any lead
- [ ] Can access analytics dashboard
- [ ] Can export leads to Excel/PDF
- [ ] Sees all filter options (including "Assigned To" filter)

### Employee User Tests

- [ ] Can ONLY view leads assigned to them
- [ ] Cannot see leads assigned to others
- [ ] Cannot create new leads (button hidden, route blocked)
- [ ] Cannot edit lead details of assigned leads
- [ ] Can update status of assigned leads
- [ ] Cannot delete any leads (button hidden, route blocked)
- [ ] Cannot assign/reassign leads (form hidden, route blocked)
- [ ] Cannot access analytics dashboard (route blocked)
- [ ] Cannot export reports (buttons hidden, routes blocked)
- [ ] "Assigned To" filter not visible/functional
- [ ] Attempting to access unauthorized routes returns 403 error

### Security Tests

- [ ] Employee accessing `/admin/leads/create` via URL → 403 Forbidden
- [ ] Employee accessing `/admin/leads/analytics` via URL → 403 Forbidden
- [ ] Employee accessing another user's lead via URL → 403 Forbidden
- [ ] Employee attempting to assign via POST request → 403 Forbidden
- [ ] Employee attempting to delete via DELETE request → 403 Forbidden
- [ ] Unauthenticated user accessing any lead route → Redirect to login

---

## Common Use Cases

### Use Case 1: Employee Views Their Assigned Leads

**Scenario**: Sarah (Employee) logs in and wants to view her assigned leads.

**Flow**:
1. Sarah clicks "Service Leads" in navigation
2. Route: `GET /admin/leads` with middleware `can.manage.leads:view`
3. Middleware checks: Sarah is employee → allows `view` action
4. Controller filters: `where('assigned_to', Sarah->id)`
5. View displays: Only Sarah's assigned leads

### Use Case 2: Employee Updates Lead Status

**Scenario**: Sarah wants to mark her lead as "Inspection Booked".

**Flow**:
1. Sarah views her assigned lead detail page
2. Sarah sees status update form (conditional: assigned to her)
3. Sarah submits form: `PATCH /admin/leads/{lead}/status`
4. Middleware checks: Sarah is employee, action is `edit`, lead is assigned to her → allows
5. Controller updates status → sends email notification
6. Success message displayed

### Use Case 3: Employee Attempts Unauthorized Action

**Scenario**: Sarah tries to delete a lead or create a new one.

**Flow**:
1. Sarah doesn't see "Delete" or "Create" buttons (view conditional)
2. If Sarah manually crafts URL: `DELETE /admin/leads/{lead}`
3. Middleware checks: Sarah is employee, action is `delete` → 403 Forbidden
4. Error page displayed: "You do not have permission to perform this action."

### Use Case 4: Admin Assigns Lead to Employee

**Scenario**: Admin John assigns a lead to Employee Sarah.

**Flow**:
1. John views lead detail page
2. John sees "Assign Lead" form (admin only)
3. John selects Sarah from dropdown
4. Form submits: `PATCH /admin/leads/{lead}/assign`
5. Middleware checks: John is admin → allows `assign` action
6. Controller updates `assigned_to` → sends email to Sarah
7. Sarah receives "Lead Assigned" email notification

### Use Case 5: Admin Views Analytics

**Scenario**: Admin John wants to review sales performance.

**Flow**:
1. John clicks "Analytics" button in leads index page
2. Route: `GET /admin/leads/analytics` with middleware `can.manage.leads:analytics`
3. Middleware checks: John is admin → allows
4. Analytics dashboard displays with charts and metrics
5. John can export to Excel/PDF

---

## Error Messages

### 403 Forbidden Errors

Different messages are shown based on the violation:

| Scenario | Error Message |
|----------|---------------|
| Employee accessing admin-only action | "You do not have permission to perform this action." |
| Employee accessing another user's lead | "You can only access leads assigned to you." |
| Unauthenticated user | "Unauthorized. Authentication required." |
| User with invalid role | "You do not have permission to access this module." |

---

## Extending Permissions

### Adding a New Action

To add a new permission action (e.g., "export"):

**Step 1**: Update Middleware
```php
// app/Http/Middleware/EnsureCanManageLeads.php
if ($user->role === 'employee') {
    if (in_array($action, ['view', 'edit', 'export'])) {
        // ... existing logic
    }
}
```

**Step 2**: Apply to Routes
```php
// routes/web.php
Route::get('/leads/export', [ServiceLeadController::class, 'export'])
    ->middleware('can.manage.leads:export')
    ->name('export');
```

**Step 3**: Update Controller
```php
// app/Http/Controllers/Admin/ServiceLeadController.php
public function export(Request $request)
{
    // Filter to assigned leads if employee
    if ($request->user()->role === 'employee') {
        $leads = ServiceLead::where('assigned_to', $request->user()->id)->get();
    } else {
        $leads = ServiceLead::all();
    }
    
    // ... export logic
}
```

### Adding a New Role

To add a new role (e.g., "manager"):

**Step 1**: Update User Migration (if needed)
```php
// Ensure role column allows new value
$table->enum('role', ['admin', 'manager', 'employee'])->default('employee');
```

**Step 2**: Update Middleware
```php
// app/Http/Middleware/EnsureCanManageLeads.php
public function handle(Request $request, Closure $next, string $action = 'view'): Response
{
    $user = $request->user();

    // Admins and Managers have full access
    if (in_array($user->role, ['admin', 'manager'])) {
        return $next($request);
    }

    // ... employee logic
}
```

**Step 3**: Update Controller Filters
```php
// app/Http/Controllers/Admin/ServiceLeadController.php
public function index(Request $request)
{
    // Employees see only assigned leads; admins and managers see all
    if ($request->user()->role === 'employee') {
        $query->where('assigned_to', $request->user()->id);
    }
    
    // ... rest of method
}
```

---

## Troubleshooting

### Issue: Employee can access all leads

**Cause**: Controller filtering not implemented
**Solution**: Ensure `index()` method filters by `assigned_to` for employees

```php
if ($request->user()->role === 'employee') {
    $query->where('assigned_to', $request->user()->id);
}
```

### Issue: 403 error on legitimate employee access

**Cause**: Middleware action parameter mismatch
**Solution**: Check route middleware matches controller action:
- Viewing leads → `can.manage.leads:view`
- Updating status → `can.manage.leads:edit`

### Issue: Employee sees admin buttons

**Cause**: Missing Blade conditionals
**Solution**: Wrap admin-only elements in role checks:

```blade
@if(auth()->user()->role === 'admin')
    <!-- Admin only content -->
@endif
```

### Issue: Middleware not being applied

**Cause**: Middleware alias not registered
**Solution**: Verify `bootstrap/app.php` has:

```php
$middleware->alias([
    'can.manage.leads' => \App\Http\Middleware\EnsureCanManageLeads::class,
]);
```

Then run:
```bash
php artisan optimize:clear
```

---

## Best Practices

### 1. Always Use Middleware

Never rely solely on view conditionals for security:
```php
// ❌ BAD - View-only protection
@if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.leads.create') }}">Create</a>
@endif

// ✅ GOOD - Middleware + View protection
Route::get('/create', [ServiceLeadController::class, 'create'])
    ->middleware('can.manage.leads:create');  // Route protected

@if(auth()->user()->role === 'admin')  // UI hidden
    <a href="{{ route('admin.leads.create') }}">Create</a>
@endif
```

### 2. Filter at Query Level

Apply role-based filters in the query, not after fetching:
```php
// ❌ BAD - Fetching all then filtering
$allLeads = ServiceLead::all();
$leads = $allLeads->filter(function($lead) use ($user) {
    return $lead->assigned_to === $user->id;
});

// ✅ GOOD - Filter in query
if ($user->role === 'employee') {
    $leads = ServiceLead::where('assigned_to', $user->id)->get();
}
```

### 3. Use Descriptive Action Names

Make action parameters self-documenting:
```php
// ❌ BAD
->middleware('can.manage.leads:a')

// ✅ GOOD
->middleware('can.manage.leads:analytics')
```

### 4. Centralize Permission Logic

Keep all permission logic in the middleware:
```php
// ❌ BAD - Permission logic scattered
public function index(Request $request) {
    if ($request->user()->role !== 'admin') {
        abort(403);
    }
}

// ✅ GOOD - Middleware handles it
Route::get('/', [ServiceLeadController::class, 'index'])
    ->middleware('can.manage.leads:view');
```

### 5. Test with Actual User Accounts

Create test accounts for each role:
```bash
php artisan tinker
>>> User::create(['name' => 'Admin Test', 'email' => 'admin@test.com', 'role' => 'admin', 'password' => Hash::make('password')]);
>>> User::create(['name' => 'Employee Test', 'email' => 'employee@test.com', 'role' => 'employee', 'password' => Hash::make('password')]);
```

Then manually test each role's access patterns.

---

## Related Documentation

- [LEADS_IMPLEMENTATION_SUMMARY.md](./LEADS_IMPLEMENTATION_SUMMARY.md) - Full implementation overview
- [LEADS_API_REFERENCE.md](./LEADS_API_REFERENCE.md) - API endpoints and usage
- [LEADS_TESTING_GUIDE.md](./LEADS_TESTING_GUIDE.md) - Testing procedures
- [LEADS_EMAIL_NOTIFICATIONS.md](./LEADS_EMAIL_NOTIFICATIONS.md) - Email notification system

---

**Last Updated**: January 2026  
**Module Version**: 1.0.0  
**Author**: ERP Development Team
