#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  Roles & Permissions Dashboard Integration Test${NC}"
echo -e "${BLUE}================================================${NC}\n"

cd /Users/sagarchhetri/Downloads/Coding/erp

# Test 1: Check admin dashboard has permission checks
echo -e "${YELLOW}Test 1: Admin Dashboard Permission Checks${NC}"
if grep -q '@if($canViewFinance)' resources/views/admin/dashboard.blade.php && \
   grep -q '@if($canViewHRM)' resources/views/admin/dashboard.blade.php && \
   grep -q '@if($canApproveLeaves)' resources/views/admin/dashboard.blade.php; then
    echo -e "${GREEN}✓ Admin dashboard has permission conditionals${NC}"
else
    echo -e "${RED}✗ Admin dashboard missing permission checks${NC}"
fi

# Test 2: Check staff dashboard has permission checks
echo -e "${YELLOW}Test 2: Staff Dashboard Permission Checks${NC}"
if grep -q '$canViewProjects' resources/views/dashboard.blade.php && \
   grep -q '$canViewLeads' resources/views/dashboard.blade.php && \
   grep -q '@if(Auth::user()->hasRole' resources/views/dashboard.blade.php; then
    echo -e "${GREEN}✓ Staff dashboard has permission conditionals${NC}"
else
    echo -e "${RED}✗ Staff dashboard missing permission checks${NC}"
fi

# Test 3: Check middleware is registered
echo -e "${YELLOW}Test 3: Middleware Registration${NC}"
if grep -q "CheckRole::class" bootstrap/app.php && \
   grep -q "CheckPermission::class" bootstrap/app.php; then
    echo -e "${GREEN}✓ Role and Permission middleware registered${NC}"
else
    echo -e "${RED}✗ Middleware not properly registered${NC}"
fi

# Test 4: Check DashboardController has permission checks
echo -e "${YELLOW}Test 4: DashboardController Permission Logic${NC}"
if grep -q 'hasPermission.*VIEW_FINANCES' app/Http/Controllers/DashboardController.php && \
   grep -q 'hasPermission.*VIEW_EMPLOYEES' app/Http/Controllers/DashboardController.php; then
    echo -e "${GREEN}✓ Controller has permission-aware data loading${NC}"
else
    echo -e "${RED}✗ Controller missing permission checks${NC}"
fi

# Test 5: Check Employee Dashboard has role checks
echo -e "${YELLOW}Test 5: Employee Dashboard Role Checks${NC}"
if grep -q 'hasRole.*super_admin.*admin' app/Http/Controllers/Employee/DashboardController.php || \
   grep -q "hasRole\(\['super_admin'" app/Http/Controllers/Employee/DashboardController.php; then
    echo -e "${GREEN}✓ Employee dashboard uses hasRole method${NC}"
else
    echo -e "${RED}✗ Employee dashboard missing role checks${NC}"
fi

# Test 6: Check roles and permissions exist in database
echo -e "${YELLOW}Test 6: Database Models${NC}"
if [ -f "app/Models/Role.php" ] && [ -f "app/Models/Permission.php" ]; then
    echo -e "${GREEN}✓ Role and Permission models exist${NC}"
else
    echo -e "${RED}✗ Models missing${NC}"
fi

# Test 7: Check migration exists
echo -e "${YELLOW}Test 7: Migration Files${NC}"
if ls database/migrations/*create_roles_and_permissions_tables.php &>/dev/null; then
    echo -e "${GREEN}✓ Migration file exists${NC}"
else
    echo -e "${RED}✗ Migration file not found${NC}"
fi

# Test 8: Check seeder exists
echo -e "${YELLOW}Test 8: Seeder File${NC}"
if [ -f "database/seeders/RolesAndPermissionsSeeder.php" ]; then
    echo -e "${GREEN}✓ Seeder file exists${NC}"
else
    echo -e "${RED}✗ Seeder file not found${NC}"
fi

# Test 9: Check User model has permission methods
echo -e "${YELLOW}Test 9: User Model Methods${NC}"
if grep -q 'hasPermission' app/Models/User.php && \
   grep -q 'hasRole' app/Models/User.php; then
    echo -e "${GREEN}✓ User model has permission methods${NC}"
else
    echo -e "${RED}✗ User model missing permission methods${NC}"
fi

# Test 10: Check middleware files exist
echo -e "${YELLOW}Test 10: Middleware Files${NC}"
if [ -f "app/Http/Middleware/CheckRole.php" ] && [ -f "app/Http/Middleware/CheckPermission.php" ]; then
    echo -e "${GREEN}✓ Middleware classes exist${NC}"
else
    echo -e "${RED}✗ Middleware classes not found${NC}"
fi

# Test 11: Check documentation
echo -e "${YELLOW}Test 11: Documentation${NC}"
if [ -f "docs/ROLES_DASHBOARD_INTEGRATION.md" ] && \
   [ -f "docs/ROLES_AND_PERMISSIONS_SYSTEM.md" ] && \
   [ -f "docs/ROLES_QUICK_START.md" ]; then
    echo -e "${GREEN}✓ Documentation files exist${NC}"
else
    echo -e "${RED}✗ Some documentation files missing${NC}"
fi

echo ""
echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}          Test Summary Complete${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Run migrations: php artisan migrate"
echo "2. Seed database: php artisan db:seed --class=RolesAndPermissionsSeeder"
echo "3. Test dashboards with different user roles"
echo "4. Clear cache: php artisan config:clear && php artisan view:clear"
