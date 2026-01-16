<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

/**
 * RolePermissionService
 * 
 * Service class for managing roles and permissions
 */
class RolePermissionService
{
    /**
     * Assign multiple roles to a user
     */
    public function assignRolesToUser(User $user, array|string $roles): User
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            $user->assignRole($role);
        }

        return $user->fresh()->load('roles');
    }

    /**
     * Remove multiple roles from a user
     */
    public function removeRolesFromUser(User $user, array|string $roles): User
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            $user->removeRole($role);
        }

        return $user->fresh()->load('roles');
    }

    /**
     * Assign multiple permissions to a user
     */
    public function assignPermissionsToUser(User $user, array|string $permissions): User
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            $user->givePermissionTo($permission);
        }

        return $user->fresh()->load('permissions');
    }

    /**
     * Get all user roles with their permissions
     */
    public function getUserRolesWithPermissions(User $user): array
    {
        return $user->roles()
            ->with('permissions')
            ->get()
            ->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'permissions' => $role->permissions->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'module' => $p->module,
                ])->toArray(),
            ])
            ->toArray();
    }

    /**
     * Get all user permissions (from roles and direct)
     */
    public function getUserPermissions(User $user): array
    {
        $permissions = collect();

        // Get permissions from roles
        $user->roles()
            ->with('permissions')
            ->get()
            ->each(function ($role) use ($permissions) {
                $role->permissions->each(function ($permission) use ($permissions) {
                    $permissions->push($permission);
                });
            });

        // Get direct permissions
        $user->permissions()->get()->each(function ($permission) use ($permissions) {
            $permissions->push($permission);
        });

        // Remove duplicates and return
        return $permissions
            ->unique('id')
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'module' => $p->module,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Get all roles organized by type
     */
    public function getAllRoles(): array
    {
        $roles = Role::with('permissions')->get();

        return [
            'system_roles' => $roles->where('is_system_role', true)->values()->toArray(),
            'module_roles' => $roles->where('is_system_role', false)->values()->toArray(),
            'total' => $roles->count(),
        ];
    }

    /**
     * Get all permissions organized by module
     */
    public function getAllPermissions(): array
    {
        $permissions = Permission::all();

        return $permissions
            ->groupBy('module')
            ->map(fn($perms) => $perms->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'module' => $p->module,
                'category' => $p->category,
            ])->toArray())
            ->toArray();
    }

    /**
     * Check if user has access to module
     */
    public function userHasModuleAccess(User $user, string $module): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($query) use ($module) {
                $query->where('module', $module);
            })
            ->exists() || $user->permissions()
            ->where('module', $module)
            ->exists();
    }

    /**
     * Get user's accessible modules
     */
    public function getUserAccessibleModules(User $user): array
    {
        $modules = collect();

        // From roles
        $user->roles()
            ->with('permissions')
            ->get()
            ->each(function ($role) use ($modules) {
                $role->permissions->each(function ($permission) use ($modules) {
                    if ($permission->module) {
                        $modules->push($permission->module);
                    }
                });
            });

        // Direct permissions
        $user->permissions()
            ->whereNotNull('module')
            ->get()
            ->each(function ($permission) use ($modules) {
                $modules->push($permission->module);
            });

        return $modules->unique()->values()->toArray();
    }

    /**
     * Revoke all roles from user
     */
    public function revokeAllRolesFromUser(User $user): User
    {
        $user->roles()->detach();
        return $user->fresh();
    }

    /**
     * Revoke all direct permissions from user
     */
    public function revokeAllPermissionsFromUser(User $user): User
    {
        $user->permissions()->detach();
        return $user->fresh();
    }

    /**
     * Reset user to default role
     */
    public function resetUserToDefaultRole(User $user, string $defaultRole = 'user'): User
    {
        $this->revokeAllRolesFromUser($user);
        $user->assignRole($defaultRole);
        return $user->fresh()->load('roles');
    }

    /**
     * Clone permissions from one role to another
     */
    public function cloneRolePermissions(Role $sourceRole, Role $targetRole): Role
    {
        $permissions = $sourceRole->permissions()->pluck('id');
        $targetRole->permissions()->sync($permissions);
        return $targetRole->fresh()->load('permissions');
    }

    /**
     * Get role hierarchy comparison
     */
    public function compareUserHierarchy(User $user1, User $user2): array
    {
        $role1 = $user1->roles()->first();
        $role2 = $user2->roles()->first();

        return [
            'user1' => [
                'name' => $user1->name,
                'role' => $role1?->name,
                'permission_count' => $user1->roles()
                    ->with('permissions')
                    ->get()
                    ->sum(fn($r) => $r->permissions->count()),
            ],
            'user2' => [
                'name' => $user2->name,
                'role' => $role2?->name,
                'permission_count' => $user2->roles()
                    ->with('permissions')
                    ->get()
                    ->sum(fn($r) => $r->permissions->count()),
            ],
        ];
    }
}
