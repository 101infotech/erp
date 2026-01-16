# Dashboard Roles & Permissions - Developer Quick Reference

## Quick Commands

### Clear cache and view cache (required after updates)
```bash
php artisan config:clear
php artisan view:clear
```

### Test the integration
```bash
bash test_roles_integration.sh
```

---

## Permission Check Syntax

### In Blade Templates
```blade
{{-- Check single permission --}}
@if(auth()->user()->hasPermission(PermissionConstants::VIEW_FINANCES))
  <!-- Content -->
@endif

{{-- Check single role --}}
@if(auth()->user()->hasRole('admin'))
  <!-- Content -->
@endif

{{-- Check multiple roles (OR logic) --}}
@if(auth()->user()->hasRole(['super_admin', 'admin']))
  <!-- Content -->
@endif
```

### In Controllers
```php
use App\Constants\PermissionConstants;

// Single permission check
if ($user->hasPermission(PermissionConstants::VIEW_FINANCES)) {
    // User has permission
}

// Multiple permissions (AND logic)
if ($user->hasAllPermissions([
    PermissionConstants::VIEW_FINANCES,
    PermissionConstants::MANAGE_PAYROLL
])) {
    // User has all permissions
}

// Single role check
if ($user->hasRole('admin')) {
    // User is admin
}

// Multiple roles (OR logic)
if ($user->hasRole(['super_admin', 'admin'])) {
    // User is super admin or admin
}
```

### Route Protection
```php
// Protect route with permission
Route::get('/finance', function () {
    //
})->middleware('permission:view_finances');

// Protect route with role
Route::get('/admin', function () {
    //
})->middleware('role:super_admin,admin');
```

---

## Common Permissions

### Finance Module
```php
PermissionConstants::VIEW_FINANCES           // View finance data
PermissionConstants::MANAGE_FINANCES         // Create/edit finances
PermissionConstants::VIEW_PAYMENTS           // View payments
PermissionConstants::MANAGE_PAYROLL          // Manage payroll
PermissionConstants::APPROVE_EXPENSES        // Approve expenses
```

### HRM Module
```php
PermissionConstants::VIEW_EMPLOYEES          // View employee data
PermissionConstants::MANAGE_EMPLOYEES        // Create/edit employees
PermissionConstants::APPROVE_LEAVE_REQUESTS  // Approve leaves
PermissionConstants::MANAGE_PAYROLL          // Manage payroll
PermissionConstants::VIEW_ATTENDANCE         // View attendance
```

### Leads Module
```php
PermissionConstants::VIEW_LEADS              // View leads
PermissionConstants::MANAGE_LEADS            // Create/edit leads
PermissionConstants::MANAGE_LEAD_STAGES      // Manage lead stages
```

### Projects Module
```php
PermissionConstants::VIEW_PROJECTS           // View projects
PermissionConstants::MANAGE_PROJECTS         // Create/edit projects
PermissionConstants::MANAGE_TEAM_MEMBERS     // Manage team
```

---

## Common Roles

### System Roles
```php
RoleConstants::SUPER_ADMIN   // Full system access
RoleConstants::ADMIN         // Admin panel access
RoleConstants::MANAGER       // Department manager
RoleConstants::USER          // Regular employee
```

### Module Roles
```php
RoleConstants::LEADS_MANAGER         // Leads department head
RoleConstants::LEADS_EXECUTIVE       // Leads staff
RoleConstants::FINANCE_MANAGER       // Finance department head
RoleConstants::FINANCE_ACCOUNTANT    // Finance staff
RoleConstants::HR_MANAGER            // HR department head
RoleConstants::HR_EXECUTIVE          // HR staff
RoleConstants::PROJECT_MANAGER       // Project management
RoleConstants::TEAM_LEAD             // Team leadership
```

---

## Dashboard Display Logic

### Admin Dashboard
| Section | Condition | File |
|---------|-----------|------|
| Finance Summary | `@if($canViewFinance)` | admin/dashboard.blade.php:119 |
| HRM Quick Stats | `@if($canViewHRM)` | admin/dashboard.blade.php:164 |
| Pending Leaves | `@if($canApproveLeaves)` | admin/dashboard.blade.php:278 |
| Quick Actions | Conditional cards | admin/dashboard.blade.php:307+ |

### Staff Dashboard
| Section | Condition | File |
|---------|-----------|------|
| Projects Link | `@if($canViewProjects)` | dashboard.blade.php:27 |
| Leads Link | `@if($canViewLeads)` | dashboard.blade.php:31 |
| Team Link | `@if($canViewHRM)` | dashboard.blade.php:35 |
| Finance Link | `@if($canViewFinance)` | dashboard.blade.php:39 |
| Admin Banner | `@if(Auth::user()->hasRole(...))` | dashboard.blade.php:65 |

---

## Adding New Permission-Based Section

### Step 1: Add Permission to PermissionConstants
```php
// app/Constants/PermissionConstants.php
const VIEW_REPORTS = 'view_reports';
const MANAGE_REPORTS = 'manage_reports';
```

### Step 2: Create Blade Variable
```blade
@php
$canViewReports = auth()->user()->hasPermission(PermissionConstants::VIEW_REPORTS);
@endphp
```

### Step 3: Wrap Section
```blade
@if($canViewReports)
  <div class="reports-section">
    <!-- Reports content -->
  </div>
@endif
```

### Step 4: Update Controller (if loading data)
```php
$reports = [];
if ($user->hasPermission(PermissionConstants::VIEW_REPORTS)) {
    $reports = $this->getReports();
}
```

### Step 5: Pass to View
```php
return view('dashboard', compact('reports', ...));
```

---

## Testing Tips

### Test Scenario 1: Super Admin
- Login as user with `super_admin` role
- Check `/admin/dashboard` - all sections should be visible
- Check `/dashboard` - admin banner should appear

### Test Scenario 2: Finance Manager
- Login as user with `finance_manager` role
- Check `/admin/dashboard` - only Finance Summary should appear
- HRM and Leave sections should be hidden
- Check `/dashboard` - Finance link in nav should appear

### Test Scenario 3: HR Manager
- Login as user with `hr_manager` role
- Check `/admin/dashboard` - only HRM sections should appear
- Finance and other sections should be hidden
- Check `/dashboard` - Team and Leave links should appear

### Test Scenario 4: Regular Employee
- Login as user with `user` role
- Check `/admin/dashboard` - should redirect to `/dashboard`
- Check `/dashboard` - should show available modules based on permissions

---

## Common Issues & Solutions

### Sections Still Showing
**Problem**: A section is still visible even though user doesn't have permission
**Solution**: 
1. Clear view cache: `php artisan view:clear`
2. Check user's roles and permissions in database
3. Verify PermissionConstants are imported correctly

### Permission Check Not Working
**Problem**: Permission check returns false even though user should have access
**Solution**:
1. Verify database seeding: `php artisan db:seed --class=RolesAndPermissionsSeeder`
2. Check user role assignment in database
3. Verify role has correct permissions assigned

### Middleware Not Protecting Routes
**Problem**: Routes are still accessible without permission
**Solution**:
1. Verify middleware is registered in `bootstrap/app.php`
2. Check route middleware syntax is correct
3. Clear config cache: `php artisan config:clear`

---

## File Locations Quick Reference

| Component | Location |
|-----------|----------|
| Permission Constants | `app/Constants/PermissionConstants.php` |
| Role Constants | `app/Constants/RoleConstants.php` |
| Role Model | `app/Models/Role.php` |
| Permission Model | `app/Models/Permission.php` |
| User Model | `app/Models/User.php` |
| CheckRole Middleware | `app/Http/Middleware/CheckRole.php` |
| CheckPermission Middleware | `app/Http/Middleware/CheckPermission.php` |
| Admin Dashboard | `resources/views/admin/dashboard.blade.php` |
| Staff Dashboard | `resources/views/dashboard.blade.php` |
| Dashboard Controller | `app/Http/Controllers/DashboardController.php` |
| Employee Dashboard Controller | `app/Http/Controllers/Employee/DashboardController.php` |
| Middleware Config | `bootstrap/app.php` |
| Migration | `database/migrations/*create_roles_and_permissions_tables.php` |
| Seeder | `database/seeders/RolesAndPermissionsSeeder.php` |

---

## Key Methods on User Model

```php
// Permission checking
auth()->user()->hasPermission($permission)
auth()->user()->hasAllPermissions($permissions)
auth()->user()->hasAnyPermission($permissions)

// Role checking
auth()->user()->hasRole($role)
auth()->user()->hasAllRoles($roles)
auth()->user()->hasAnyRole($roles)

// Role/permission assignment
auth()->user()->assignRole($role)
auth()->user()->removeRole($role)
auth()->user()->givePermissionTo($permission)
auth()->user()->revokePermissionFrom($permission)

// Check if super admin
auth()->user()->isSuperAdmin()
auth()->user()->isAdmin()
```

---

## Performance Tips

1. **Use Permission Constants** - Don't hardcode permission strings
2. **Check in Controllers** - Avoid loading unnecessary data
3. **Cache Permissions** - Consider caching permission checks for high-traffic sites
4. **Combine Checks** - Group multiple checks together when possible
5. **Test Permissions** - Always test with different user roles before deployment

---

## Support References

- **Roles & Permissions System**: `docs/ROLES_AND_PERMISSIONS_SYSTEM.md`
- **Quick Start Guide**: `docs/ROLES_QUICK_START.md`
- **Dashboard Integration**: `docs/ROLES_DASHBOARD_INTEGRATION.md`
- **Complete Summary**: `docs/IMPLEMENTATION_COMPLETE.md`
- **Test Script**: `test_roles_integration.sh`
