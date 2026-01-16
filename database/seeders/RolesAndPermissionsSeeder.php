<?php

namespace Database\Seeders;

use App\Constants\PermissionConstants;
use App\Constants\RoleConstants;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $this->createPermissions();

        // Create roles and assign permissions
        $this->createRoles();

        $this->command->info('Roles and permissions seeded successfully!');
    }

    /**
     * Create all permissions
     */
    private function createPermissions(): void
    {
        $permissionsByModule = PermissionConstants::getPermissionsByModule();

        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $slug => $name) {
                Permission::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'module' => $module,
                        'category' => $this->getCategory($slug),
                    ]
                );
            }
        }

        $this->command->info('Permissions created: ' . Permission::count());
    }

    /**
     * Create all roles and assign permissions
     */
    private function createRoles(): void
    {
        $rolesData = [
            RoleConstants::SUPER_ADMIN => [
                'name' => 'Super Admin',
                'description' => 'Full system access and control',
                'is_system_role' => true,
            ],
            RoleConstants::ADMIN => [
                'name' => 'Admin',
                'description' => 'Administrative access to most features',
                'is_system_role' => true,
            ],
            RoleConstants::MANAGER => [
                'name' => 'Manager',
                'description' => 'Team management and oversight',
                'is_system_role' => true,
            ],
            RoleConstants::USER => [
                'name' => 'User',
                'description' => 'Basic user access',
                'is_system_role' => true,
            ],
            RoleConstants::LEADS_MANAGER => [
                'name' => 'Leads Manager',
                'description' => 'Manage leads module and team members',
                'is_system_role' => false,
            ],
            RoleConstants::LEADS_EXECUTIVE => [
                'name' => 'Leads Executive',
                'description' => 'Handle lead conversion and follow-ups',
                'is_system_role' => false,
            ],
            RoleConstants::FINANCE_MANAGER => [
                'name' => 'Finance Manager',
                'description' => 'Manage finances and accounting',
                'is_system_role' => false,
            ],
            RoleConstants::FINANCE_ACCOUNTANT => [
                'name' => 'Finance Accountant',
                'description' => 'Handle accounting entries and reconciliation',
                'is_system_role' => false,
            ],
            RoleConstants::HR_MANAGER => [
                'name' => 'HR Manager',
                'description' => 'Manage HR operations and employees',
                'is_system_role' => false,
            ],
            RoleConstants::HR_EXECUTIVE => [
                'name' => 'HR Executive',
                'description' => 'Process HR requests and attendance',
                'is_system_role' => false,
            ],
            RoleConstants::PROJECT_MANAGER => [
                'name' => 'Project Manager',
                'description' => 'Manage projects and teams',
                'is_system_role' => false,
            ],
            RoleConstants::TEAM_LEAD => [
                'name' => 'Team Lead',
                'description' => 'Lead team members',
                'is_system_role' => false,
            ],
        ];

        $rolePermissions = PermissionConstants::getRolePermissions();

        foreach ($rolesData as $slug => $data) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                $data
            );

            // Assign permissions to role
            if (isset($rolePermissions[$slug])) {
                $permissionIds = Permission::whereIn('slug', $rolePermissions[$slug])->pluck('id');
                $role->permissions()->sync($permissionIds);
            }

            $this->command->info("Role '{$role->name}' created with " . $role->permissions()->count() . ' permissions');
        }

        $this->command->info('Roles created: ' . Role::count());
    }

    /**
     * Get category for a permission slug
     */
    private function getCategory(string $slug): string
    {
        if (str_starts_with($slug, 'view')) {
            return 'read';
        } elseif (str_starts_with($slug, 'create')) {
            return 'create';
        } elseif (str_starts_with($slug, 'edit')) {
            return 'update';
        } elseif (str_starts_with($slug, 'delete')) {
            return 'delete';
        } elseif (str_starts_with($slug, 'manage') || str_starts_with($slug, 'reconcile') || str_starts_with($slug, 'approve') || str_starts_with($slug, 'generate')) {
            return 'manage';
        }
        return 'other';
    }
}
