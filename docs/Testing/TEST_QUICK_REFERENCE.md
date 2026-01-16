# Test Quick Reference Guide

## Running Tests

### Run All Tests
```bash
php artisan test --colors
```

**Output**: 46 tests, 77 assertions, ~1.76 seconds

### Run Specific Test Suite

```bash
# Unit tests only
php artisan test tests/Unit/RolePermissionTest.php

# Dashboard feature tests
php artisan test tests/Feature/DashboardPermissionTest.php

# Middleware tests
php artisan test tests/Feature/MiddlewareTest.php
```

### Run Specific Test Method

```bash
php artisan test tests/Unit/RolePermissionTest.php --filter=test_roles_exist
```

### Filter by Test Name

```bash
# Run all permission tests
php artisan test --filter=permission

# Run all role tests  
php artisan test --filter=role

# Run all dashboard tests
php artisan test --filter=dashboard
```

## Test Results Expected

```
 PASS  Tests\Unit\RolePermissionTest
  ✓ roles exist
  ✓ permissions exist
  ✓ role has permissions
  ✓ user can be assigned role
  ✓ user can have multiple roles
  ✓ user inherits permissions from role
  ✓ user can have direct permission
  ✓ user without permission returns false
  ✓ user has all permissions
  ✓ user has all roles
  ✓ user is super admin
  ✓ user is admin
  ✓ permission can be revoked
  ✓ role can be removed
  ✓ all roles exist
  ✓ permissions count

 PASS  Tests\Feature\DashboardPermissionTest
  ✓ admin can view dashboard
  ✓ staff can view dashboard
  ✓ unauthenticated cannot view dashboard
  ✓ finance manager has finance permission
  ✓ hr manager has employee permission
  ✓ leads manager has leads permission
  ✓ non finance manager cannot view finances
  ✓ super admin has all permissions
  ✓ admin has all permissions
  ✓ manager has limited permissions
  ✓ user role has minimal permissions
  ✓ user with direct permission has access
  ✓ dashboard loads data for authorized user
  ✓ role hierarchy super admin is everything
  ✓ permission inheritance through roles
  ✓ direct permission grant

 PASS  Tests\Feature\MiddlewareTest
  ✓ authorized role passes middleware
  ✓ unauthorized role denied middleware
  ✓ authorized permission passes middleware
  ✓ unauthorized permission denied middleware
  ✓ user with one of multiple roles
  ✓ user with all required roles
  ✓ user with one of multiple permissions
  ✓ user with all required permissions
  ✓ super admin has all permissions and roles
  ✓ role based access control
  ✓ permission based access control
  ✓ admin role permissions
  ✓ guest cannot access protected routes
  ✓ permission caching

Tests: 46 passed
Duration: 1.76s
```

## Test Coverage

| Component | Tests | Status |
|-----------|-------|--------|
| Role Model | 5 | ✓ Pass |
| Permission Model | 3 | ✓ Pass |
| User Model | 8 | ✓ Pass |
| Role Assignment | 3 | ✓ Pass |
| Dashboard Integration | 5 | ✓ Pass |
| Permission Checks | 5 | ✓ Pass |
| Role Hierarchy | 4 | ✓ Pass |
| Middleware | 4 | ✓ Pass |
| Security | 2 | ✓ Pass |

## What Each Test Suite Validates

### Unit Tests (16 tests)
Tests the core models and their relationships:
- Role model and methods
- Permission model and methods
- User model authorization methods
- Database seeding
- Role/permission hierarchy

### Feature Tests - Dashboard (16 tests)
Tests the integration with the dashboard:
- Role assignment and verification
- Permission-based data loading
- View-level permission hiding
- Dashboard access by role
- Module-specific role access

### Feature Tests - Middleware (14 tests)
Tests the security and authorization layers:
- Role-based access control (RBAC)
- Permission-based access control (PBAC)
- Middleware protection
- Multiple role/permission logic
- Guest and unauthorized access restrictions

## Troubleshooting

### Tests Won't Run
```bash
# Clear cache first
php artisan cache:clear

# Try again
php artisan test --colors
```

### Database Issues
```bash
# Reset test database
php artisan migrate:refresh --env=testing --seed

# Then run tests
php artisan test --colors
```

### Slow Tests
```bash
# Run tests in parallel (Laravel 9+)
php artisan test --parallel
```

### See More Details
```bash
# Run with more verbosity
php artisan test --colors --cache
```

## Test Files

- `tests/Unit/RolePermissionTest.php` - 16 unit tests
- `tests/Feature/DashboardPermissionTest.php` - 16 feature tests  
- `tests/Feature/MiddlewareTest.php` - 14 middleware tests
- `tests/TestCase.php` - Base test class with seeding

## CI/CD Integration

### GitHub Actions Example
```yaml
- name: Run Tests
  run: php artisan test --colors
```

### GitLab CI Example
```yaml
test:
  script:
    - php artisan test --colors
```

### Local Pre-commit Hook
```bash
#!/bin/bash
php artisan test --colors
if [ $? -ne 0 ]; then
  echo "Tests failed. Commit aborted."
  exit 1
fi
```

## Key Assertions

Test assertions validate:
- ✓ Role/Permission exist in database
- ✓ Users can be assigned roles
- ✓ Permissions inherit through roles
- ✓ Direct permissions work
- ✓ Multiple roles work
- ✓ hasRole() method works
- ✓ hasPermission() method works
- ✓ hasAllRoles() method works
- ✓ hasAllPermissions() method works
- ✓ hasAnyRole() method works
- ✓ hasAnyPermission() method works
- ✓ isSuperAdmin() method works
- ✓ isAdmin() method works
- ✓ Permissions can be revoked
- ✓ Roles can be removed
- ✓ Unauthorized access is blocked

## More Information

See detailed reports:
- `docs/TEST_RESULTS_REPORT.md` - Comprehensive test results
- `docs/TESTING_COMPLETE.md` - Testing summary
- `docs/PHASE_3_COMPLETION_CHECKLIST.md` - Implementation checklist

## Last Test Run

```
Total Tests: 46
Passed: 46 (100%)
Failed: 0
Duration: 1.76 seconds
Status: ✅ ALL PASSING
```

---

**Last Updated**: 2026-01-19
**Test Framework**: PHPUnit 11.5.46
**Status**: Ready for Production
