# Roles & Permissions Dashboard Integration

## Overview
The roles and permissions system has been successfully integrated into both the admin and staff dashboards. This document describes the implementation and how module visibility is controlled based on user permissions.

## Admin Dashboard Implementation

### Location
`resources/views/admin/dashboard.blade.php`

### Permission-Based Section Visibility

#### Finance Summary Section
- **Permission Required**: `VIEW_FINANCES`
- **Visible To**: Finance Manager, Finance Accountant, Super Admin
- **Shows**: Revenue, Expenses, Net Profit, Pending Receivables
- **Implementation**: Wrapped entire section with `@if($canViewFinance)`

#### HRM Quick Stats Section
- **Permission Required**: `VIEW_EMPLOYEES`
- **Visible To**: HR Manager, HR Executive, Super Admin
- **Shows**: Active Employees, Pending Leaves, Draft Payrolls, Attendance Issues
- **Implementation**: Wrapped entire section with `@if($canViewHRM)`

#### Pending Leave Requests
- **Permission Required**: `APPROVE_LEAVE_REQUESTS`
- **Visible To**: HR Manager, Super Admin
- **Shows**: Employee leave requests with review/approval options
- **Implementation**: Double-wrapped with `@if($canApproveLeaves)` and count check

#### Quick Actions Section
- **Total Members Card**: Conditional on `$canViewHRM` permission
- **Manage Employees**: Visible only to users with HRM access
- **Finance Dashboard**: Visible only to users with Finance access
- **Leave Requests**: Visible only to users with Leave approval access
- **User Accounts**: Always visible to all admin-level users

### Controller Implementation
`app/Http/Controllers/DashboardController.php`

```php
public function admin()
{
    $user = Auth::user();
    $moduleAccess = $this->getUserModuleAccess($user);
    
    // Finance data loaded only if user has permission
    $financeData = [];
    if ($user->hasPermission(PermissionConstants::VIEW_FINANCES)) {
        $financeData = $this->getFinanceData();
    }
    
    // HRM stats loaded only if user has permission
    $hrmStats = [];
    if ($user->hasPermission(PermissionConstants::VIEW_EMPLOYEES)) {
        $hrmStats = $this->getHrmStats();
    }
    
    // Additional methods called conditionally
}
```

## Staff/Employee Dashboard Implementation

### Location
`resources/views/dashboard.blade.php`

### Permission Checks Added

#### Navigation Menu Items
- **Projects**: Visible if user has `VIEW_PROJECTS` permission
- **Leads Base**: Visible if user has `VIEW_LEADS` permission
- **Team**: Visible if user has `VIEW_EMPLOYEES` permission
- **Finance**: Visible if user has `VIEW_FINANCES` permission
- **Dashboard**: Always visible (base navigation)

#### Admin Access Banner
- **Shows**: Only to users with super_admin or admin role
- **Updated**: Changed from string-based `role === 'admin'` to `hasRole(['super_admin', 'admin'])`

## Permission Constants Used

```php
PermissionConstants::VIEW_FINANCES          // Finance module access
PermissionConstants::VIEW_EMPLOYEES         // HRM module access
PermissionConstants::VIEW_LEADS             // Leads module access
PermissionConstants::VIEW_PROJECTS          // Projects module access
PermissionConstants::APPROVE_LEAVE_REQUESTS // Leave approval rights
```

## Middleware Registration

### Bootstrap Configuration
`bootstrap/app.php`

Middleware aliases have been registered:

```php
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class,
]);
```

### Usage in Routes
You can now protect routes using:

```php
// Check for specific role
Route::get('/admin', function() {
    //
})->middleware('role:super_admin,admin');

// Check for specific permission
Route::get('/finance', function() {
    //
})->middleware('permission:view_finances');

// Multiple permissions (all required)
Route::get('/sensitive', function() {
    //
})->middleware('permission:view_finances,manage_payroll');
```

## Testing the Implementation

### Test Scenarios

#### Test 1: Super Admin User
- Login as super_admin role
- **Expected**: All sections visible on both dashboards

#### Test 2: Finance Manager
- Login as finance_manager role
- **Expected**: 
  - Finance Summary visible on admin dashboard
  - Finance Dashboard link visible in staff navigation
  - HRM sections hidden

#### Test 3: HR Manager
- Login as hr_manager role
- **Expected**:
  - HRM sections visible on admin dashboard
  - Finance sections hidden
  - Leave management accessible

#### Test 4: Regular User/Employee
- Login as user role
- **Expected**:
  - Only staff dashboard accessible
  - Navigation shows only permitted modules
  - Admin panel not accessible

### Manual Testing Steps

1. **Access Admin Dashboard**
   ```
   - Navigate to /admin/dashboard
   - Verify sections appear based on role
   - Check that data is not loaded for hidden sections
   ```

2. **Access Staff Dashboard**
   ```
   - Navigate to /dashboard
   - Check navigation menu shows correct items
   - Verify admin banner appears only for admins
   ```

3. **Test Database Seeding**
   ```
   - Run: php artisan db:seed --class=RolesAndPermissionsSeeder
   - Verify 12 roles and 58 permissions created
   ```

## Database Schema

### Permission Checks in Views
All permission checks use the User model methods:

```php
// Check single permission
auth()->user()->hasPermission(PermissionConstants::VIEW_FINANCES)

// Check single role
auth()->user()->hasRole('admin')

// Check multiple roles (OR logic)
auth()->user()->hasRole(['super_admin', 'admin'])

// Check all roles (AND logic)
auth()->user()->hasAllRoles(['super_admin', 'finance_manager'])
```

## Future Enhancements

1. **Route Middleware**: Routes can be protected using the registered middleware
2. **API Permissions**: Extend middleware to API routes
3. **Permission Caching**: Cache permission checks for performance
4. **Audit Logging**: Log who accessed what sections
5. **Granular Controls**: Add more specific permissions for each dashboard section

## Troubleshooting

### Sections Not Appearing
- Verify user has the correct role assigned
- Check that role has correct permissions
- Clear view cache: `php artisan view:clear`

### Permissions Not Working
- Verify PermissionConstants are imported correctly
- Check that hasPermission() method exists on User model
- Verify database seeding completed successfully

### Middleware Errors
- Ensure middleware is registered in bootstrap/app.php
- Check route middleware names match exactly
- Verify middleware classes exist in app/Http/Middleware/

## Files Modified

1. `resources/views/admin/dashboard.blade.php` - Added permission checks
2. `resources/views/dashboard.blade.php` - Added permission checks and navigation
3. `app/Http/Controllers/DashboardController.php` - Added permission-aware data loading
4. `app/Http/Controllers/Employee/DashboardController.php` - Updated role checks
5. `bootstrap/app.php` - Registered role and permission middleware

## Completed Tasks ✅

- ✅ Admin dashboard sections wrapped with permission conditionals
- ✅ Staff dashboard navigation updated with permission checks
- ✅ Controllers updated to only load data for authorized users
- ✅ Middleware registered in bootstrap configuration
- ✅ Documentation created

## Status

**Implementation Complete** - Ready for testing with different user roles.
