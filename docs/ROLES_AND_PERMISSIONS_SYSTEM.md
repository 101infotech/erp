# Roles and Permissions System Documentation

## Overview

A comprehensive, scalable roles and permissions system has been implemented for the ERP application to support multiple modules (Leads, Finance, HRM, Projects) with fine-grained access control.

## Architecture

### Models

#### Role Model
Located at: `app/Models/Role.php`

A role is a collection of permissions that can be assigned to users. Roles can be:
- **System Roles**: Global roles that apply across the system (Super Admin, Admin, Manager, User)
- **Module-Specific Roles**: Roles specific to particular modules (Leads Manager, Finance Accountant, etc.)

**Key Methods:**
- `permissions()` - Get all permissions for this role
- `users()` - Get all users with this role
- `hasPermission(string)` - Check if role has a permission
- `givePermissionTo(Permission|string)` - Assign a permission
- `revokePermissionTo(Permission|string)` - Remove a permission
- `syncPermissions($permissions)` - Replace all permissions

#### Permission Model
Located at: `app/Models/Permission.php`

A permission represents an action that can be performed on a resource. Each permission has:
- `name` - Human-readable name
- `slug` - Unique identifier for the permission
- `module` - Module the permission belongs to (leads, finance, hrm, projects, admin)
- `category` - Category of the permission (read, create, update, delete, manage, other)

#### User Model Enhancements
Updated `app/Models/User.php` with role and permission methods:

**Role Methods:**
- `roles()` - Get all roles assigned to user
- `assignRole(Role|string)` - Assign a role
- `removeRole(Role|string)` - Remove a role
- `hasRole(string|array)` - Check if user has a role
- `hasAnyRole(array)` - Check if user has any of the roles
- `hasAllRoles(array)` - Check if user has all roles
- `syncRoles($roles)` - Replace all roles
- `isSuperAdmin()` - Check if user is super admin
- `isAdmin()` - Check if user is admin

**Permission Methods:**
- `permissions()` - Get all direct permissions
- `hasPermission(string)` - Check if user has permission (through role or directly)
- `hasAnyPermission(array)` - Check if user has any permission
- `hasAllPermissions(array)` - Check if user has all permissions
- `givePermissionTo(Permission|string)` - Assign a direct permission
- `revokePermissionTo(Permission|string)` - Revoke a direct permission

### Constants

#### RoleConstants
Located at: `app/Constants/RoleConstants.php`

Defines all available roles:

**System Roles:**
- `SUPER_ADMIN` - Full system access
- `ADMIN` - Administrative access
- `MANAGER` - Team management
- `USER` - Basic access

**Module-Specific Roles:**
- **Leads**: `LEADS_MANAGER`, `LEADS_EXECUTIVE`
- **Finance**: `FINANCE_MANAGER`, `FINANCE_ACCOUNTANT`
- **HRM**: `HR_MANAGER`, `HR_EXECUTIVE`
- **Projects**: `PROJECT_MANAGER`, `TEAM_LEAD`

**Key Methods:**
- `all()` - Get all roles
- `getDescriptions()` - Get role descriptions
- `getHierarchy()` - Get role hierarchy levels
- `isSystemRole(string)` - Check if role is system role
- `getModule(string)` - Get module for a role

#### PermissionConstants
Located at: `app/Constants/PermissionConstants.php`

Defines all available permissions organized by module:

**Modules:**
- `MODULE_LEADS` - Leads management
- `MODULE_FINANCE` - Finance and accounting
- `MODULE_HRM` - Human resources
- `MODULE_PROJECTS` - Project management
- `MODULE_ADMIN` - System administration

**Key Methods:**
- `getPermissionsByModule()` - Get all permissions organized by module
- `getRolePermissions()` - Get default permissions for each role
- `all()` - Get all permissions

### Database Tables

Four new tables are created:

1. **roles**
   - `id` - Primary key
   - `name` - Role name
   - `slug` - Unique slug
   - `description` - Role description
   - `is_system_role` - Boolean flag
   - `timestamps` - Created/updated at

2. **permissions**
   - `id` - Primary key
   - `name` - Permission name
   - `slug` - Unique slug
   - `description` - Permission description
   - `module` - Module name
   - `category` - Permission category
   - `timestamps` - Created/updated at

3. **role_permissions**
   - `id` - Primary key
   - `role_id` - Foreign key to roles
   - `permission_id` - Foreign key to permissions
   - `timestamps` - Created/updated at
   - Unique constraint on (role_id, permission_id)

4. **user_roles**
   - `id` - Primary key
   - `user_id` - Foreign key to users
   - `role_id` - Foreign key to roles
   - `timestamps` - Created/updated at
   - Unique constraint on (user_id, role_id)

5. **user_permissions**
   - `id` - Primary key
   - `user_id` - Foreign key to users
   - `permission_id` - Foreign key to permissions
   - `timestamps` - Created/updated at
   - Unique constraint on (user_id, permission_id)

### Middleware

#### CheckRole Middleware
Located at: `app/Http/Middleware/CheckRole.php`

Use in routes to check if user has specific roles:

```php
Route::middleware('auth:sanctum', 'role:admin,super_admin')->group(function () {
    // Route group for admins
});
```

#### CheckPermission Middleware
Located at: `app/Http/Middleware/CheckPermission.php`

Use in routes to check if user has specific permission:

```php
Route::middleware('auth:sanctum', 'permission:manage_users')->group(function () {
    // Route group for users with permission
});
```

### Authorization Trait
Located at: `app/Traits/AuthorizationTrait.php`

Provides helper methods for authorization checks in controllers:

```php
use App\Traits\AuthorizationTrait;

class SomeController extends Controller
{
    use AuthorizationTrait;

    public function someMethod()
    {
        // Check role
        if ($this->userHasRole('admin')) {
            // ...
        }

        // Check permission
        if ($this->userHasPermission('manage_users')) {
            // ...
        }

        // Authorize or throw exception
        $this->authorize('admin');
        $this->authorizePermission('manage_users');
    }
}
```

## Usage Examples

### Assigning Roles to Users

```php
$user = User::find(1);

// Assign a single role
$user->assignRole('admin');
$user->assignRole(Role::where('slug', 'finance_manager')->first());

// Assign multiple roles
$user->syncRoles(['leads_manager', 'finance_accountant']);

// Remove a role
$user->removeRole('user');
```

### Checking User Roles

```php
$user = User::find(1);

// Check single role
if ($user->hasRole('admin')) {
    // User is admin
}

// Check multiple roles
if ($user->hasRole(['admin', 'manager'])) {
    // User is admin or manager
}

// Check all roles
if ($user->hasAllRoles(['admin', 'manager'])) {
    // User is both admin and manager
}

// Check system role
if ($user->isAdmin()) {
    // User is admin or super admin
}
```

### Checking Permissions

```php
$user = User::find(1);

// Check single permission
if ($user->hasPermission('view_leads')) {
    // User can view leads
}

// Check any permission
if ($user->hasAnyPermission(['view_leads', 'edit_leads'])) {
    // User can view or edit leads
}

// Check all permissions
if ($user->hasAllPermissions(['view_leads', 'edit_leads'])) {
    // User can view and edit leads
}
```

### In Controllers

```php
use App\Traits\AuthorizationTrait;
use App\Constants\PermissionConstants;

class LeadsController extends Controller
{
    use AuthorizationTrait;

    public function index()
    {
        // Check permission
        $this->authorizePermission(PermissionConstants::VIEW_LEADS);

        // Get leads
        $leads = Lead::all();
        return response()->json($leads);
    }

    public function store(Request $request)
    {
        // Check permission
        $this->authorizePermission(PermissionConstants::CREATE_LEADS);

        // Create lead
        $lead = Lead::create($request->validated());
        return response()->json($lead, 201);
    }
}
```

### In Blade Templates

```blade
@if(auth()->user()->hasRole('admin'))
    <a href="{{ route('admin.users') }}">Users</a>
@endif

@if(auth()->user()->hasPermission('manage_users'))
    <button>Manage Users</button>
@endif
```

### In Routes

```php
// Register middleware in app/Http/Middleware/Kernel.php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class,
];

// Use in routes
Route::middleware('auth:sanctum', 'role:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

Route::middleware('auth:sanctum', 'permission:manage_users')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

## Seeding

Run the seeder to populate all roles and permissions:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This will create:
- 12 roles (4 system + 8 module-specific)
- 50+ permissions across all modules
- Default permission assignments for each role

## Scalability & Future Modules

The system is designed to be easily extensible:

### Adding a New Module

1. **Add new role constants** in `RoleConstants`:
```php
const NEW_MODULE_MANAGER = 'new_module_manager';
const NEW_MODULE_EXECUTIVE = 'new_module_executive';
```

2. **Add new permissions** in `PermissionConstants`:
```php
const VIEW_NEW_MODULE = 'view_new_module';
const CREATE_NEW_MODULE = 'create_new_module';
// ... more permissions
```

3. **Add module to permissions array** in `PermissionConstants::getPermissionsByModule()`:
```php
self::MODULE_NEW_MODULE => [
    self::VIEW_NEW_MODULE => 'View New Module',
    self::CREATE_NEW_MODULE => 'Create New Module',
    // ... more permissions
]
```

4. **Add role permissions** in `PermissionConstants::getRolePermissions()`:
```php
RoleConstants::NEW_MODULE_MANAGER => [
    self::VIEW_NEW_MODULE,
    self::CREATE_NEW_MODULE,
    // ... relevant permissions
]
```

5. **Run seeder** to create roles and permissions in the database:
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

## Migration Path

The system is backward compatible with the existing `role` column on the users table. To migrate:

1. Run the migration:
```bash
php artisan migrate
```

2. Run the seeder:
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

3. Optionally migrate existing roles:
```php
// In a command or script
$users = User::whereNotNull('role')->get();
foreach ($users as $user) {
    // Map old role format to new role
    if ($user->role === 'admin') {
        $user->assignRole('admin');
    } elseif ($user->role === 'user') {
        $user->assignRole('user');
    }
}
```

## Best Practices

1. **Use Constants**: Always use `RoleConstants` and `PermissionConstants` instead of hardcoded strings
2. **Check Permissions**: Use permission checks rather than role checks for better granularity
3. **Cache Permissions**: Consider caching roles and permissions for performance
4. **Audit Trail**: Log role and permission assignments for security
5. **Regular Reviews**: Periodically review user roles and permissions
6. **Principle of Least Privilege**: Assign only necessary roles/permissions to users
7. **Module Separation**: Keep module-specific permissions separate from system-wide permissions

## Performance Considerations

The system uses:
- Foreign key constraints with cascade delete
- Indexes on frequently queried columns (slug, role_id, permission_id, user_id)
- Unique constraints to prevent duplicate assignments
- Eager loading relationships to minimize database queries

For high-scale deployments, consider:
- Caching role and permission relationships
- Using materialized views for complex permission checks
- Implementing permission caching layer
