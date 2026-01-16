# Dashboard Roles & Permissions - Testing Checklist

## 🧪 Pre-Testing Setup

### Prerequisites
- [ ] Database migrated: `php artisan migrate`
- [ ] Seeder run: `php artisan db:seed --class=RolesAndPermissionsSeeder`
- [ ] Cache cleared: `php artisan config:clear && php artisan view:clear`
- [ ] Dev server running: `php artisan serve`
- [ ] Test users created with different roles

### Test User Creation (via Tinker)
```bash
php artisan tinker
```

```php
// Create Super Admin
$superAdmin = User::create(['name' => 'Super Admin', 'email' => 'super@test.com', 'password' => bcrypt('password')]);
$superAdmin->assignRole('super_admin');

// Create Finance Manager
$financeManager = User::create(['name' => 'Finance Manager', 'email' => 'finance@test.com', 'password' => bcrypt('password')]);
$financeManager->assignRole('finance_manager');

// Create HR Manager
$hrManager = User::create(['name' => 'HR Manager', 'email' => 'hr@test.com', 'password' => bcrypt('password')]);
$hrManager->assignRole('hr_manager');

// Create Regular Employee
$employee = User::create(['name' => 'Employee', 'email' => 'employee@test.com', 'password' => bcrypt('password')]);
$employee->assignRole('user');
```

---

## ✅ Test Suite 1: Admin Dashboard Access

### Test 1.1: Super Admin Full Access
**User**: Super Admin  
**URL**: http://localhost:8000/admin/dashboard

- [ ] Page loads successfully
- [ ] No permission errors
- [ ] Finance Summary section visible
- [ ] HRM Quick Stats section visible
- [ ] Pending Leaves section visible
- [ ] All 4 quick action cards visible
- [ ] All stat cards in header visible
- [ ] No empty sections

**Expected Result**: ✅ All sections visible and populated

---

### Test 1.2: Finance Manager Limited Access
**User**: Finance Manager  
**URL**: http://localhost:8000/admin/dashboard

**Finance Module Checks**:
- [ ] Finance Summary section visible
- [ ] Revenue, Expenses, Net Profit displayed
- [ ] Pending Receivables shown
- [ ] "View More" link to finance dashboard present

**HRM Module Checks**:
- [ ] HRM Quick Stats section NOT visible
- [ ] Pending Leaves section NOT visible
- [ ] Team Members card NOT visible in stats

**Quick Actions Checks**:
- [ ] "Finance Dashboard" button visible
- [ ] "Manage Employees" button NOT visible
- [ ] "Leave Requests" button NOT visible
- [ ] "User Accounts" button visible

**Expected Result**: ✅ Only Finance section visible, HRM hidden

---

### Test 1.3: HR Manager Limited Access
**User**: HR Manager  
**URL**: http://localhost:8000/admin/dashboard

**HRM Module Checks**:
- [ ] HRM Quick Stats section visible
- [ ] Active Employees count shown
- [ ] Pending Leaves count shown with "Review" button
- [ ] Draft Payrolls shown with "Process" button
- [ ] Attendance Issues shown with "Check" button

**Finance Module Checks**:
- [ ] Finance Summary section NOT visible
- [ ] No revenue/expense data shown

**Quick Actions Checks**:
- [ ] "Manage Employees" button visible
- [ ] "Leave Requests" button visible
- [ ] "Finance Dashboard" button NOT visible
- [ ] "User Accounts" button visible

**Expected Result**: ✅ Only HRM section visible, Finance hidden

---

### Test 1.4: Regular Employee Admin Access Denied
**User**: Regular Employee  
**URL**: http://localhost:8000/admin/dashboard

- [ ] Page redirects to `/dashboard` (staff dashboard)
- [ ] Admin dashboard NOT accessible
- [ ] No error messages shown

**Expected Result**: ✅ Redirected to staff dashboard

---

## ✅ Test Suite 2: Staff Dashboard Navigation

### Test 2.1: Super Admin Staff Dashboard
**User**: Super Admin  
**URL**: http://localhost:8000/dashboard

**Navigation Menu**:
- [ ] Dashboard link visible
- [ ] Projects link visible
- [ ] Leads Base link visible
- [ ] Team link visible
- [ ] Finance link visible

**Admin Banner**:
- [ ] "Admin Access Enabled" banner visible
- [ ] "Open Admin Panel" button present and functional

**Expected Result**: ✅ All navigation items visible

---

### Test 2.2: Finance Manager Navigation
**User**: Finance Manager  
**URL**: http://localhost:8000/dashboard

**Navigation Menu**:
- [ ] Dashboard link visible
- [ ] Projects link NOT visible
- [ ] Leads Base link NOT visible
- [ ] Team link NOT visible
- [ ] Finance link visible

**Admin Banner**:
- [ ] Admin banner NOT visible
- [ ] No admin access indication

**Expected Result**: ✅ Only Finance link in navigation

---

### Test 2.3: HR Manager Navigation
**User**: HR Manager  
**URL**: http://localhost:8000/dashboard

**Navigation Menu**:
- [ ] Dashboard link visible
- [ ] Projects link NOT visible
- [ ] Leads Base link NOT visible
- [ ] Team link visible
- [ ] Finance link NOT visible

**Expected Result**: ✅ Only Team link visible

---

### Test 2.4: Regular Employee Navigation
**User**: Regular Employee (user role)  
**URL**: http://localhost:8000/dashboard

**Navigation Menu** (varies based on assigned permissions):
- [ ] Dashboard always visible
- [ ] Other links appear based on role permissions

**Admin Banner**:
- [ ] Admin banner NOT visible to regular employees

**Expected Result**: ✅ Minimal navigation, no admin access

---

## ✅ Test Suite 3: Data Loading Verification

### Test 3.1: Finance Data Only Loaded for Authorized Users
**Setup**: Create two test scenarios

**Scenario A: User with Finance Permission**
- Login as Finance Manager
- Open browser DevTools → Network tab
- Navigate to `/admin/dashboard`
- [ ] Check for API call to finance data endpoint
- [ ] Finance data successfully loaded

**Scenario B: User without Finance Permission**
- Login as HR Manager
- Open browser DevTools → Network tab
- Navigate to `/admin/dashboard`
- [ ] Verify NO API call to finance data endpoint
- [ ] Finance Summary section doesn't appear in DOM

**Expected Result**: ✅ Data loaded only for authorized users

---

### Test 3.2: HRM Data Only Loaded for Authorized Users

**Scenario A: User with HRM Permission**
- Login as HR Manager
- Open browser DevTools → Network tab
- Navigate to `/admin/dashboard`
- [ ] Check for API call to HRM data endpoint
- [ ] HRM data successfully loaded
- [ ] Pending leaves data loaded

**Scenario B: User without HRM Permission**
- Login as Finance Manager
- Open browser DevTools → Network tab
- Navigate to `/admin/dashboard`
- [ ] Verify NO API call to HRM data endpoint
- [ ] HRM section doesn't appear in DOM

**Expected Result**: ✅ HRM data loaded only for authorized users

---

## ✅ Test Suite 4: Permission Methods

### Test 4.1: hasPermission() Method
```php
php artisan tinker

$financeUser = User::where('email', 'finance@test.com')->first();
$financeUser->hasPermission('view_finances');  // Should return true
$financeUser->hasPermission('view_employees');  // Should return false
```

- [ ] Returns `true` for assigned permissions
- [ ] Returns `false` for unassigned permissions

---

### Test 4.2: hasRole() Method
```php
$financeUser = User::where('email', 'finance@test.com')->first();
$financeUser->hasRole('finance_manager');       // Should return true
$financeUser->hasRole('hr_manager');            // Should return false
$financeUser->hasRole(['finance_manager', 'admin']);  // Should return true (OR logic)
```

- [ ] Returns `true` for assigned roles
- [ ] Returns `false` for unassigned roles
- [ ] Supports array with OR logic

---

## ✅ Test Suite 5: Middleware Protection

### Test 5.1: Permission Middleware
**Setup**: Create test route with permission middleware
```php
Route::get('/test-finance', function() {
    return 'Finance Only';
})->middleware('permission:view_finances');
```

**Test 1: Authorized User**
- [ ] Finance Manager can access `/test-finance`
- [ ] See "Finance Only" message

**Test 2: Unauthorized User**
- [ ] HR Manager cannot access `/test-finance`
- [ ] See authorization error/redirect

---

### Test 5.2: Role Middleware
**Setup**: Create test route with role middleware
```php
Route::get('/test-admin', function() {
    return 'Admin Only';
})->middleware('role:super_admin,admin');
```

**Test 1: Admin User**
- [ ] Super Admin can access `/test-admin`
- [ ] Admin can access `/test-admin`

**Test 2: Non-Admin User**
- [ ] Finance Manager cannot access `/test-admin`
- [ ] Employee cannot access `/test-admin`

---

## ✅ Test Suite 6: Edge Cases

### Test 6.1: User with Multiple Roles
```php
$user = User::create(['name' => 'Multi Role', 'email' => 'multi@test.com', 'password' => bcrypt('password')]);
$user->assignRole(['super_admin', 'finance_manager']);
```

- [ ] User can access admin dashboard
- [ ] User can access finance module
- [ ] User can access all assigned modules

---

### Test 6.2: Direct Permission Assignment (Bypass Roles)
```php
$user = User::create(['name' => 'Direct Perm', 'email' => 'direct@test.com', 'password' => bcrypt('password')]);
$user->assignRole('user');  // Regular employee
$user->givePermissionTo('view_finances');  // Direct permission
```

- [ ] User can see Finance Summary on dashboard
- [ ] Permission works even without role

---

### Test 6.3: Permission Removal
```php
$user = User::where('email', 'finance@test.com')->first();
$user->revokePermissionFrom('view_finances');
```

- [ ] User loses access to Finance section
- [ ] Finance section hidden on next page load

---

## ✅ Test Suite 7: UI/UX Tests

### Test 7.1: No Empty Sections
**For Each User Type**:
- [ ] No empty grid items
- [ ] No placeholder "coming soon" sections
- [ ] Sections are completely removed (not hidden)

### Test 7.2: Quick Actions Responsiveness
- [ ] On mobile (< 768px): Grid is single column
- [ ] On tablet (768-1024px): Grid is 2 columns
- [ ] On desktop (> 1024px): Grid is 4 columns
- [ ] Hidden cards don't break layout

### Test 7.3: Navigation Menu Responsiveness
- [ ] Mobile menu works correctly
- [ ] Only permitted items show
- [ ] Menu is scrollable on small screens

---

## ✅ Test Suite 8: Database Verification

### Test 8.1: Roles Exist
```bash
php artisan tinker
App\Models\Role::all()->pluck('name')
```

Expected roles: super_admin, admin, manager, user, leads_manager, leads_executive, finance_manager, finance_accountant, hr_manager, hr_executive, project_manager, team_lead

- [ ] All 12 roles present

---

### Test 8.2: Permissions Exist
```bash
App\Models\Permission::count()  // Should be 58
```

- [ ] 58 permissions exist

---

### Test 8.3: Role-Permission Associations
```bash
$role = App\Models\Role::where('name', 'finance_manager')->first();
$role->permissions->pluck('name')
```

- [ ] Finance manager has finance permissions
- [ ] Finance manager doesn't have HRM permissions

---

## ✅ Test Suite 9: Performance Tests

### Test 9.1: Page Load Time
**Without Permission Checks** (if previously available):
- [ ] Measure baseline load time
- [ ] Record time

**With Permission Checks**:
- [ ] Measure new load time
- [ ] Verify no significant degradation (< 500ms increase)

### Test 9.2: Data Query Optimization
- [ ] No N+1 queries in permission checks
- [ ] Permissions loaded efficiently
- [ ] Roles loaded efficiently

---

## ✅ Test Suite 10: Error Handling

### Test 10.1: Invalid Permission Constant
```blade
@if(auth()->user()->hasPermission('INVALID_PERMISSION'))
```

- [ ] No errors thrown
- [ ] Returns false gracefully

### Test 10.2: User with No Role
```php
$user = User::create(['name' => 'No Role', 'email' => 'norole@test.com', 'password' => bcrypt('password')]);
```

- [ ] User can still login
- [ ] No permissions granted
- [ ] Can see public sections only

---

## 📋 Final Verification Checklist

- [ ] All 11 integration tests pass
- [ ] 10 test suites completed successfully
- [ ] No console errors
- [ ] No database errors
- [ ] All permission checks working
- [ ] All middleware protecting routes
- [ ] Dashboard displays correctly for all roles
- [ ] Navigation filters correctly
- [ ] Data not loaded for unauthorized users
- [ ] Performance acceptable
- [ ] UI clean with no empty sections
- [ ] Mobile responsive
- [ ] No security vulnerabilities

---

## 📊 Test Results Summary

| Test Suite | Status | Notes |
|-----------|--------|-------|
| Admin Dashboard Access | ⬜ | Pending |
| Staff Dashboard Navigation | ⬜ | Pending |
| Data Loading | ⬜ | Pending |
| Permission Methods | ⬜ | Pending |
| Middleware Protection | ⬜ | Pending |
| Edge Cases | ⬜ | Pending |
| UI/UX | ⬜ | Pending |
| Database | ⬜ | Pending |
| Performance | ⬜ | Pending |
| Error Handling | ⬜ | Pending |

---

## 🎯 Sign-Off

**Tested By**: _______________  
**Date**: _______________  
**Result**: ✅ PASSED / ❌ FAILED / 🟡 NEEDS WORK

**Issues Found**: None / List below
```
- Issue 1: ...
- Issue 2: ...
```

**Notes**: 
```
...
```

---

## 📞 Support

If tests fail, check:
1. Cache cleared: `php artisan config:clear && php artisan view:clear`
2. Database seeded: `php artisan db:seed --class=RolesAndPermissionsSeeder`
3. Test user roles assigned correctly in database
4. Check logs: `storage/logs/laravel.log`
5. Review documentation: `docs/ROLES_DASHBOARD_INTEGRATION.md`
