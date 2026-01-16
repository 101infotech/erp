<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Constants\RoleConstants;
use App\Constants\PermissionConstants;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    /**
     * Test that roles can be created
     */
    public function test_roles_exist(): void
    {
        $super_admin = Role::where('slug', 'super_admin')->first();
        $this->assertNotNull($super_admin);
        $this->assertEquals('super_admin', $super_admin->slug);
    }

    /**
     * Test that permissions exist
     */
    public function test_permissions_exist(): void
    {
        $permission = Permission::where('slug', PermissionConstants::VIEW_FINANCES)->first();
        $this->assertNotNull($permission);
    }

    /**
     * Test role has permissions relationship
     */
    public function test_role_has_permissions(): void
    {
        $finance_manager = Role::where('slug', 'finance_manager')->first();
        $this->assertNotNull($finance_manager);
        $this->assertGreaterThan(0, $finance_manager->permissions->count());
    }

    /**
     * Test user can be assigned a role
     */
    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        
        $this->assertTrue($user->hasRole('finance_manager'));
    }

    /**
     * Test user can have multiple roles
     */
    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        $user->assignRole('hr_manager');
        
        $this->assertTrue($user->hasRole('finance_manager'));
        $this->assertTrue($user->hasRole('hr_manager'));
    }

    /**
     * Test user inherits permissions from role
     */
    public function test_user_inherits_permissions_from_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test user can be given direct permission
     */
    public function test_user_can_have_direct_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionConstants::VIEW_FINANCES);
        
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test user without permission returns false
     */
    public function test_user_without_permission_returns_false(): void
    {
        $user = User::factory()->create();
        
        $this->assertFalse($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test hasAllPermissions method
     */
    public function test_user_has_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        
        $permissions = [
            PermissionConstants::VIEW_FINANCES,
        ];
        
        $this->assertTrue($user->hasAllPermissions($permissions));
    }

    /**
     * Test hasAllRoles method
     */
    public function test_user_has_all_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        $user->assignRole('hr_manager');
        
        $this->assertTrue($user->hasAllRoles(['finance_manager', 'hr_manager']));
    }

    /**
     * Test isSuperAdmin method
     */
    public function test_user_is_super_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        
        $this->assertTrue($user->isSuperAdmin());
    }

    /**
     * Test isAdmin method
     */
    public function test_user_is_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $this->assertTrue($user->isAdmin());
    }

    /**
     * Test permission can be revoked
     */
    public function test_permission_can_be_revoked(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionConstants::VIEW_FINANCES);
        
        $this->assertTrue($user->hasPermission(PermissionConstants::VIEW_FINANCES));
        
        $user->revokePermissionTo(PermissionConstants::VIEW_FINANCES);
        
        $this->assertFalse($user->hasPermission(PermissionConstants::VIEW_FINANCES));
    }

    /**
     * Test role can be removed
     */
    public function test_role_can_be_removed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('finance_manager');
        
        $this->assertTrue($user->hasRole('finance_manager'));
        
        $user->removeRole('finance_manager');
        
        $this->assertFalse($user->hasRole('finance_manager'));
    }

    /**
     * Test all 12 roles exist
     */
    public function test_all_roles_exist(): void
    {
        $expected_roles = [
            'super_admin',
            'admin',
            'manager',
            'user',
            'leads_manager',
            'leads_executive',
            'finance_manager',
            'finance_accountant',
            'hr_manager',
            'hr_executive',
            'project_manager',
            'team_lead',
        ];
        
        foreach ($expected_roles as $role) {
            $exists = Role::where('slug', $role)->exists();
            $this->assertTrue($exists, "Role '{$role}' does not exist");
        }
    }

    /**
     * Test permission count
     */
    public function test_permissions_count(): void
    {
        $count = Permission::count();
        $this->assertEquals(58, $count);
    }
}
