<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * AuthorizationTrait
 * 
 * Provides authorization methods for checking user roles and permissions
 * throughout the application.
 */
trait AuthorizationTrait
{
    /**
     * Check if the authenticated user has a specific role
     */
    protected function userHasRole(string|array $roles): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasRole($roles);
    }

    /**
     * Check if the authenticated user has a specific permission
     */
    protected function userHasPermission(string $permission): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Check if the authenticated user has any of the given permissions
     */
    protected function userHasAnyPermission(array $permissions): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if the authenticated user has all given permissions
     */
    protected function userHasAllPermissions(array $permissions): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasAllPermissions($permissions);
    }

    /**
     * Authorize the user or throw an exception
     */
    protected function authorize(string|array $roles, ?string $message = null): void
    {
        if (!$this->userHasRole($roles)) {
            abort(403, $message ?? 'Unauthorized action.');
        }
    }

    /**
     * Authorize the user by permission or throw an exception
     */
    protected function authorizePermission(string $permission, ?string $message = null): void
    {
        if (!$this->userHasPermission($permission)) {
            abort(403, $message ?? 'Unauthorized action.');
        }
    }

    /**
     * Get the authenticated user
     */
    protected function getUser()
    {
        return Auth::user();
    }

    /**
     * Check if user is super admin
     */
    protected function isSuperAdmin(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->isSuperAdmin();
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->isAdmin();
    }
}
