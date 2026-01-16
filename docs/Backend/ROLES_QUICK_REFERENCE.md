# Roles & Permissions - Developer Quick Reference

## Quick Commands

### Initial Setup
```bash
# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RolesAndPermissionsSeeder
```

## Assign Roles to Users

```php
$user = User::find(1);

// Single role
$user->assignRole('admin');

// Multiple roles
$user->syncRoles(['leads_manager', 'finance_accountant']);

// Remove role
$user->removeRole('user');
```

## Check User Roles

```php
$user = User::find(1);

// Has role?
$user->hasRole('admin')                    // true/false
$user->hasRole(['admin', 'manager'])       // true/false (OR)

// Has all roles?
$user->hasAllRoles(['admin', 'manager'])   // true/false (AND)

// Is admin?
$user->isAdmin()       // admin or super_admin
$user->isSuperAdmin()  // super_admin only
```

## Check Permissions

```php
$user = User::find(1);

// Has permission?
$user->hasPermission('view_leads')                          // true/false

// Has any permission?
$user->hasAnyPermission(['view_leads', 'edit_leads'])       // true/false (OR)

// Has all permissions?
$user->hasAllPermissions(['view_leads', 'edit_leads'])      // true/false (AND)
```

## Direct Permissions (Not Through Roles)

```php
$user = User::find(1);

// Give direct permission
$user->givePermissionTo('view_leads');

// Revoke direct permission
$user->revokePermissionTo('view_leads');

// Sync permissions
$user->syncPermissions(['view_leads', 'create_leads']);
```

## In Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Traits\AuthorizationTrait;
use App\Constants\PermissionConstants;

class YourController extends Controller
{
    use AuthorizationTrait;

    public function index()
    {
        // Check permission
        $this->authorizePermission(PermissionConstants::VIEW_LEADS);

        // Or check role
        $this->authorize('admin');

        // Your logic here
    }
}
```

## In Routes

```php
// Register middleware (in app/Http/Kernel.php)
protected $routeMiddleware = [
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class,
];

// Use in routes
Route::middleware('role:admin')->group(function () {
    // Admin routes
});

Route::middleware('permission:manage_users')->group(function () {
    // Routes requiring permission
});
```

## In Blade Templates

```blade
@if(auth()->user()->hasRole('admin'))
    <admin-panel />
@endif

@if(auth()->user()->hasPermission('manage_users'))
    <button>Manage Users</button>
@endif

@unless(auth()->user()->hasPermission('view_leads'))
    <p>No access to leads</p>
@endunless
```

## Using RolePermissionService

```php
use App\Services\RolePermissionService;

$service = app(RolePermissionService::class);

// Get user's roles with permissions
$roles = $service->getUserRolesWithPermissions($user);

// Get all user permissions
$permissions = $service->getUserPermissions($user);

// Get accessible modules
$modules = $service->getUserAccessibleModules($user);

// Check module access
if ($service->userHasModuleAccess($user, 'leads')) {
    // Show leads module
}

// Reset to default role
$service->resetUserToDefaultRole($user, 'user');

// Assign multiple roles
$service->assignRolesToUser($user, ['leads_manager', 'finance_accountant']);
```

## Available Roles Reference

```
System Roles:
├── super_admin      (Full access)
├── admin            (Most features)
├── manager          (Team oversight)
└── user             (Basic access)

Module Roles:
├── Leads
│   ├── leads_manager
│   └── leads_executive
├── Finance
│   ├── finance_manager
│   └── finance_accountant
├── HRM
│   ├── hr_manager
│   └── hr_executive
└── Projects
    ├── project_manager
    └── team_lead
```

## Permission Categories

**By Action Type:**
- `view_*` - Read/View permissions
- `create_*` - Create permissions
- `edit_*` - Update permissions
- `delete_*` - Delete permissions
- `manage_*` - Administrative permissions

**By Module:**
- `leads` - Lead management
- `finance` - Financial operations
- `hrm` - Human resources
- `projects` - Project management
- `admin` - System administration

## Common Permission Strings

```
Leads:
  view_leads, create_leads, edit_leads, delete_leads
  view_lead_documents, create_lead_documents
  view_lead_followups, create_lead_followups
  manage_lead_stages, manage_lead_status

Finance:
  view_finances, create_finances, edit_finances
  view_accounts, create_accounts, manage_chart_of_accounts
  view_transactions, create_transactions, edit_transactions
  view_budgets, create_budgets, reconcile_accounts
  generate_financial_reports

HRM:
  view_employees, create_employees, edit_employees
  manage_attendance, approve_leave_requests
  manage_payroll, manage_departments

Projects:
  view_projects, create_projects, edit_projects
  view_tasks, create_tasks, edit_tasks
  manage_project_members

Admin:
  manage_users, manage_roles, manage_permissions
```

## Troubleshooting

### User doesn't have expected permission
```php
// Check what roles user has
$user->roles()->pluck('slug');

// Check what permissions user has
$user->permissions()->pluck('slug');

// Check permissions from roles
$user->roles()->with('permissions')->get();
```

### Reset user's roles completely
```php
$user->removeRole($user->roles->pluck('slug')->toArray());
$user->assignRole('user');  // Set to default
```

### Sync multiple roles and permissions
```php
$user->syncRoles(['leads_manager', 'finance_accountant']);
$user->permissions()->sync([]);  // Clear direct permissions
```

## Performance Tips

1. **Use eager loading in queries:**
```php
User::with('roles.permissions')->find(1);
```

2. **Cache role permissions if needed:**
```php
cache()->remember("user:{$user->id}:permissions", 3600, function () {
    return $user->permissions()->pluck('slug');
});
```

3. **Check permissions in queries:**
```php
$user->roles()
    ->whereHas('permissions', fn($q) => $q->where('slug', 'view_leads'))
    ->exists();
```

## Testing Examples

```php
public function test_user_has_permission()
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

public function test_middleware_blocks_unauthorized()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->get('/api/users');
    
    $response->assertStatus(403);
}
```

## Files Reference

| Purpose | File |
|---------|------|
| Role Model | `app/Models/Role.php` |
| Permission Model | `app/Models/Permission.php` |
| User Model | `app/Models/User.php` (updated) |
| Role Constants | `app/Constants/RoleConstants.php` |
| Permission Constants | `app/Constants/PermissionConstants.php` |
| Check Role Middleware | `app/Http/Middleware/CheckRole.php` |
| Check Permission Middleware | `app/Http/Middleware/CheckPermission.php` |
| Authorization Trait | `app/Traits/AuthorizationTrait.php` |
| Role Permission Service | `app/Services/RolePermissionService.php` |
| Migration | `database/migrations/2026_01_16_000001_create_roles_and_permissions_tables.php` |
| Seeder | `database/seeders/RolesAndPermissionsSeeder.php` |

## See Also

- Full documentation: `docs/ROLES_AND_PERMISSIONS_SYSTEM.md`
- Quick start guide: `docs/ROLES_QUICK_START.md`
- Implementation summary: `docs/ROLES_PERMISSIONS_IMPLEMENTATION_SUMMARY.md`
