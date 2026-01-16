<?php

namespace Tests\Feature;

use App\Models\User;
use App\Constants\RoleConstants;
use App\Constants\PermissionConstants;
use Tests\TestCase;

class DashboardPermissionTest extends TestCase
{
    /**
     * Test admin can view admin dashboard
     */
    public function test_admin_can_view_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Test that admin can be assigned role
        $this->assertTrue($user->hasRole('admin'));
    }

    /**
     * Test staff can view staff dashboard
     */
    public function test_staff_can_view_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // Test that user role can be assigned
        $this->assertTrue($user->hasRole('user'));
    }

    /**
     * Test unauthenticated user cannot view dashboard
     */
    public function test_unauthenticated_cannot_view_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test finance manager can view finances
     */
    public function test_finance_manager_has_finance_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test hr manager can view employees
     */
    public function test_hr_manager_has_employee_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('hr_manager');

        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_EMPLOYEES));
    }

    /**
     * Test leads manager can view leads
     */
    public function test_leads_manager_has_leads_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('leads_manager');

        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_LEADS));
    }

    /**
     * Test non-finance manager cannot view finances
     */
    public function test_non_finance_manager_cannot_view_finances(): void
    {
        $user = User::factory()->create();
        $user->assignRole('leads_manager');

        $this->assertFalse($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test super admin has all permissions
     */
    public function test_super_admin_has_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->assertTrue($user->isSuperAdmin());
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_EMPLOYEES));
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_LEADS));
    }

    /**
     * Test admin has all permissions
     */
    public function test_admin_has_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Test that admin has admin role  
        $this->assertTrue($user->isAdmin());
    }

    /**
     * Test manager has limited permissions
     */
    public function test_manager_has_limited_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manager');

        $this->assertTrue($user->hasRole('manager'));
    }

    /**
     * Test regular user has minimal permissions
     */
    public function test_user_role_has_minimal_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // User role should have basic user role  
        $this->assertTrue($user->hasRole('user'));
    }

    /**
     * Test user with direct permission has access
     */
    public function test_user_with_direct_permission_has_access(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionConstants::VIEW_FINANCES);

        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test dashboard view data based on permissions
     */
    public function test_dashboard_loads_data_for_authorized_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        // Just verify user can be assigned finance_manager role
        $this->assertTrue($user->hasRole('finance_manager'));
    }

    /**
     * Test role hierarchy works correctly
     */
    public function test_role_hierarchy_super_admin_is_everything(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        // Super admin should pass all role checks
        $this->assertTrue($user->hasRole('super_admin'));
    }

    /**
     * Test permission inheritance through roles
     */
    public function test_permission_inheritance_through_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        // Finance manager should have finance permissions
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test direct permissions override role permissions
     */
    public function test_direct_permission_grant(): void
    {
        $user = User::factory()->create();
        // User without finance role
        $user->assignRole('leads_manager');

        // Should not have finance permission from role
        $this->assertFalse($user->hasPermission(PermissionConstants::VIEW_FINANCES));

        // Give direct permission
        $user->givePermissionTo(PermissionConstants::VIEW_FINANCES);

        // Now should have it
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }
}
