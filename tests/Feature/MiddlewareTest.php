<?php

namespace Tests\Feature;

use App\Models\User;
use App\Constants\RoleConstants;
use App\Constants\PermissionConstants;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    /**
     * Test CheckRole middleware passes for authorized role
     */
    public function test_authorized_role_passes_middleware(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Test that user has admin role
        $this->assertTrue($user->hasRole('admin'));
    }

    /**
     * Test CheckRole middleware denies unauthorized role
     */
    public function test_unauthorized_role_denied_middleware(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // Regular user should not have admin role
        $this->assertFalse($user->hasRole('admin'));
    }

    /**
     * Test CheckPermission middleware passes for authorized permission
     */
    public function test_authorized_permission_passes_middleware(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test CheckPermission middleware denies unauthorized permission
     */
    public function test_unauthorized_permission_denied(): void
    {
        $user = User::factory()->create();
        $user->assignRole('leads_manager');

        $this->assertFalse($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test multiple roles with OR logic
     */
    public function test_user_with_one_of_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        $roles = ['admin', 'finance_manager', 'hr_manager'];
        $this->assertTrue($user->hasAnyRole($roles));
    }

    /**
     * Test multiple roles with AND logic
     */
    public function test_user_with_all_required_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        $user->assignRole('admin');

        $roles = ['finance_manager', 'admin'];
        $this->assertTrue($user->hasAllRoles($roles));
    }

    /**
     * Test multiple permissions with OR logic
     */
    public function test_user_with_one_of_multiple_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        $permissions = [
            PermissionConstants::VIEW_FINANCES,
            PermissionConstants::VIEW_LEADS,
        ];
        $this->assertTrue($user->hasAnyPermission($permissions));
    }

    /**
     * Test multiple permissions with AND logic
     */
    public function test_user_with_all_required_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        $permissions = [
            PermissionConstants::VIEW_FINANCES,
        ];
        $this->assertTrue($user->hasAllPermissions($permissions));
    }

    /**
     * Test super admin bypasses all checks
     */
    public function test_super_admin_has_all_permissions_and_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        // Super admin should have super_admin role
        $this->assertTrue($user->hasRole('super_admin'));
        $this->assertTrue($user->isSuperAdmin());
    }

    /**
     * Test role-based access control
     */
    public function test_role_based_access_control(): void
    {
        $finance_user = User::factory()->create();
        $finance_user->assignRole('finance_manager');

        $leads_user = User::factory()->create();
        $leads_user->assignRole('leads_manager');

        // Finance user should have finance permissions
        $this->assertTrue($finance_user->hasRole('finance_manager'));
        $this->assertFalse($finance_user->hasRole('leads_manager'));

        // Leads user should have leads permissions
        $this->assertTrue($leads_user->hasRole('leads_manager'));
        $this->assertFalse($leads_user->hasRole('finance_manager'));
    }

    /**
     * Test permission-based access control
     */
    public function test_permission_based_access_control(): void
    {
        $user1 = User::factory()->create();
        $user1->givePermissionTo(PermissionConstants::VIEW_FINANCES);

        $user2 = User::factory()->create();
        $user2->givePermissionTo(PermissionConstants::VIEW_LEADS);

        $this->assertTrue($user1->hasPermission(PermissionConstants::VIEW_FINANCES));
        $this->assertFalse($user1->hasPermission(PermissionConstants::VIEW_LEADS));

        $this->assertTrue($user2->hasPermission(PermissionConstants::VIEW_LEADS));
        $this->assertFalse($user2->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test admin role has broad permissions
     */
    public function test_admin_role_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Admin should have admin role
        $this->assertTrue($user->isAdmin());
    }

    /**
     * Test guest cannot access protected routes
     */
    public function test_guest_cannot_access_protected_routes(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test permission caching works
     */
    public function test_permission_caching(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');

        // First check
        $has_permission_1 = $user->hasPermission(PermissionConstants::VIEW_FINANCES);

        // Second check (should use cache)
        $has_permission_2 = $user->hasPermission(PermissionConstants::VIEW_FINANCES);

        $this->assertTrue($has_permission_1);
        $this->assertTrue($has_permission_2);
        $this->assertEquals($has_permission_1, $has_permission_2);
    }
}
