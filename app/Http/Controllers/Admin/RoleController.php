<?php

namespace App\Http\Controllers\Admin;

use App\Constants\PermissionConstants;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    private function authorizeManageRoles(): void
    {
        $user = Auth::user();

        // Allow super_admin and admin roles to manage roles
        if (!$user || (!$user->hasRole(['super_admin', 'admin']) && !$user->hasPermission(PermissionConstants::MANAGE_ROLES))) {
            abort(403, 'You do not have permission to manage roles.');
        }
    }

    public function index()
    {
        $this->authorizeManageRoles();

        $roles = Role::withCount(['users', 'permissions'])->orderBy('slug')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $this->authorizeManageRoles();

        $permissionsByModule = PermissionConstants::getPermissionsByModule();
        $rolePermissions = $role->permissions()->pluck('slug')->toArray();

        return view('admin.roles.edit', compact('role', 'permissionsByModule', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorizeManageRoles();

        $data = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        $slugs = $data['permissions'] ?? [];
        $permissionIds = Permission::whereIn('slug', $slugs)->pluck('id')->toArray();
        $role->permissions()->sync($permissionIds);

        return redirect()->route('admin.roles.index')->with('status', 'Role permissions updated.');
    }

    public function users(Role $role)
    {
        $this->authorizeManageRoles();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $assigned = $role->users()->pluck('users.id')->toArray();

        return view('admin.roles.users', compact('role', 'users', 'assigned'));
    }

    public function syncUsers(Request $request, Role $role)
    {
        $this->authorizeManageRoles();

        $data = $request->validate([
            'users' => ['array'],
            'users.*' => ['integer', 'exists:users,id'],
        ]);

        $userIds = $data['users'] ?? [];
        $role->users()->sync($userIds);

        return redirect()->route('admin.roles.index')->with('status', 'Assigned users updated.');
    }
}
