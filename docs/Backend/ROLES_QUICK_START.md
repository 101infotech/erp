# Roles and Permissions - Quick Implementation Guide

## Setup Steps

### 1. Run Migration
```bash
php artisan migrate
```

This creates:
- `roles` table
- `permissions` table
- `role_permissions` table (junction table)
- `user_roles` table (junction table)
- `user_permissions` table (for direct permission assignment)

### 2. Seed Roles and Permissions
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This populates:
- 12 predefined roles (system + module-specific)
- 50+ permissions across all modules
- Default permission assignments for each role

## Using in Your Application

### In Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\AuthorizationTrait;
use App\Constants\PermissionConstants;

class LeadsController extends Controller
{
    use AuthorizationTrait;

    public function index()
    {
        // Check if user has permission
        $this->authorizePermission(PermissionConstants::VIEW_LEADS);
        
        return response()->json(Lead::all());
    }

    public function store(Request $request)
    {
        // Check permission before creating
        $this->authorizePermission(PermissionConstants::CREATE_LEADS);
        
        $lead = Lead::create($request->validated());
        return response()->json($lead, 201);
    }
}
```

### In Routes

Register middleware first in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class,
];
```

Then use in routes:

```php
// Check for specific role
Route::middleware('auth:sanctum', 'role:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Check for specific permission
Route::middleware('auth:sanctum', 'permission:manage_users')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Multiple roles (OR)
Route::middleware('auth:sanctum', 'role:admin,manager')->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});
```

### In Blade Templates

```blade
<!-- Check role -->
@if(auth()->user()->hasRole('admin'))
    <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
@endif

<!-- Check permission -->
@if(auth()->user()->hasPermission('manage_users'))
    <button class="btn btn-primary">Manage Users</button>
@endif

<!-- Check multiple permissions -->
@if(auth()->user()->hasAnyPermission(['edit_leads', 'view_leads']))
    <div>Leads Module</div>
@endif
```

### Assigning Roles to Users

```php
$user = User::find(1);

// Single role
$user->assignRole('leads_manager');

// Multiple roles
$user->syncRoles(['leads_manager', 'finance_accountant']);

// Remove role
$user->removeRole('user');

// Check roles
if ($user->hasRole('leads_manager')) {
    // ...
}
```

## Available Roles

### System Roles
- **super_admin** - Full system access
- **admin** - Administrative access
- **manager** - Team management
- **user** - Basic access

### Leads Module
- **leads_manager** - Manage leads and team
- **leads_executive** - Handle conversions and follow-ups

### Finance Module
- **finance_manager** - Manage finances
- **finance_accountant** - Handle accounting

### HRM Module
- **hr_manager** - Manage HR operations
- **hr_executive** - Process HR requests

### Projects Module
- **project_manager** - Manage projects
- **team_lead** - Lead teams

## Common Permissions

### Leads Module
- `view_leads` - View leads
- `create_leads` - Create leads
- `edit_leads` - Edit leads
- `delete_leads` - Delete leads
- `view_lead_documents` - View lead documents
- `manage_lead_stages` - Manage stages

### Finance Module
- `view_finances` - View financial data
- `create_finances` - Create records
- `edit_finances` - Edit records
- `view_transactions` - View transactions
- `create_transactions` - Create transactions
- `reconcile_accounts` - Reconcile accounts

### HRM Module
- `view_employees` - View employees
- `create_employees` - Create employees
- `manage_attendance` - Manage attendance
- `approve_leave_requests` - Approve leave
- `manage_payroll` - Manage payroll

### Admin Module
- `manage_users` - Manage users
- `manage_roles` - Manage roles
- `manage_permissions` - Manage permissions

## Advanced Usage

### Direct Permission Assignment

Assign permissions directly to users (not just through roles):

```php
$user = User::find(1);

// Give direct permission
$user->givePermissionTo('view_leads');

// Check permission (works through roles or direct)
if ($user->hasPermission('view_leads')) {
    // ...
}

// Revoke direct permission
$user->revokePermissionTo('view_leads');
```

### Check User Hierarchy

```php
$user = User::find(1);

if ($user->isSuperAdmin()) {
    // Full access
}

if ($user->isAdmin()) {
    // Admin or super admin
}
```

### Role Management

```php
use App\Models\Role;
use App\Constants\PermissionConstants;

// Get role
$role = Role::where('slug', 'leads_manager')->first();

// Get all permissions
$permissions = $role->permissions;

// Give permission to role
$role->givePermissionTo(PermissionConstants::DELETE_LEADS);

// Revoke permission from role
$role->revokePermissionTo(PermissionConstants::DELETE_LEADS);

// Get all users with role
$users = $role->users;
```

## Migration Guide (Existing Users)

If you have existing users with the old `role` string column:

```php
// In a migration or command
use App\Models\User, App\Models\Role;

$users = User::all();
foreach ($users as $user) {
    if ($user->role === 'admin') {
        $user->assignRole('admin');
    } elseif ($user->role === 'manager') {
        $user->assignRole('manager');
    } else {
        $user->assignRole('user');
    }
}
```

## Testing

```php
public function test_user_can_view_leads_with_permission()
{
    $user = User::factory()->create();
    $user->givePermissionTo('view_leads');

    $this->assertTrue($user->hasPermission('view_leads'));
}

public function test_user_with_role_has_permissions()
{
    $user = User::factory()->create();
    $user->assignRole('leads_manager');

    $this->assertTrue($user->hasPermission('view_leads'));
    $this->assertTrue($user->hasPermission('create_leads'));
}
```

## Troubleshooting

### Permissions not working
1. Ensure middleware is registered in `app/Http/Kernel.php`
2. Check that user is authenticated with `auth:sanctum`
3. Verify user has been assigned role/permission
4. Check permission slug matches exactly

### Role not assigned
```php
// Verify role exists
$role = Role::where('slug', 'admin')->exists();

// Verify user-role relationship
$user->roles()->exists();
```

### Performance Issues
- Cache role/permission relationships
- Use eager loading: `User::with('roles.permissions')->find($id)`
- Index frequently queried columns (already done in migration)

## Files Overview

| File | Purpose |
|------|---------|
| `app/Models/Role.php` | Role model |
| `app/Models/Permission.php` | Permission model |
| `app/Constants/RoleConstants.php` | Role constants |
| `app/Constants/PermissionConstants.php` | Permission constants |
| `app/Traits/AuthorizationTrait.php` | Authorization helper trait |
| `app/Http/Middleware/CheckRole.php` | Role verification middleware |
| `app/Http/Middleware/CheckPermission.php` | Permission verification middleware |
| `database/migrations/*roles_and_permissions*.php` | Database schema |
| `database/seeders/RolesAndPermissionsSeeder.php` | Seeder for roles/permissions |
