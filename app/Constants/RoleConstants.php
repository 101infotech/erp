<?php

namespace App\Constants;

/**
 * Role Constants
 * 
 * Define all available roles in the system.
 * These are hierarchical system roles that can be assigned to users.
 */
class RoleConstants
{
    // System Roles
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    const MANAGER = 'manager';
    const USER = 'user';

    // Module-Specific Roles
    const LEADS_MANAGER = 'leads_manager';
    const LEADS_EXECUTIVE = 'leads_executive';
    const FINANCE_MANAGER = 'finance_manager';
    const FINANCE_ACCOUNTANT = 'finance_accountant';
    const HR_MANAGER = 'hr_manager';
    const HR_EXECUTIVE = 'hr_executive';
    const PROJECT_MANAGER = 'project_manager';
    const TEAM_LEAD = 'team_lead';

    /**
     * Get all available roles
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::MANAGER,
            self::USER,
            self::LEADS_MANAGER,
            self::LEADS_EXECUTIVE,
            self::FINANCE_MANAGER,
            self::FINANCE_ACCOUNTANT,
            self::HR_MANAGER,
            self::HR_EXECUTIVE,
            self::PROJECT_MANAGER,
            self::TEAM_LEAD,
        ];
    }

    /**
     * Get role descriptions
     */
    public static function getDescriptions(): array
    {
        return [
            self::SUPER_ADMIN => 'Full system access and control',
            self::ADMIN => 'Administrative access to most features',
            self::MANAGER => 'Team management and oversight',
            self::USER => 'Basic user access',
            self::LEADS_MANAGER => 'Manage leads module and team members',
            self::LEADS_EXECUTIVE => 'Handle lead conversion and follow-ups',
            self::FINANCE_MANAGER => 'Manage finances and accounting',
            self::FINANCE_ACCOUNTANT => 'Handle accounting entries and reconciliation',
            self::HR_MANAGER => 'Manage HR operations and employees',
            self::HR_EXECUTIVE => 'Process HR requests and attendance',
            self::PROJECT_MANAGER => 'Manage projects and teams',
            self::TEAM_LEAD => 'Lead team members',
        ];
    }

    /**
     * Get hierarchy of roles (higher index = more permissions)
     */
    public static function getHierarchy(): array
    {
        return [
            self::USER => 0,
            self::LEADS_EXECUTIVE => 1,
            self::FINANCE_ACCOUNTANT => 1,
            self::HR_EXECUTIVE => 1,
            self::TEAM_LEAD => 2,
            self::LEADS_MANAGER => 3,
            self::FINANCE_MANAGER => 3,
            self::HR_MANAGER => 3,
            self::PROJECT_MANAGER => 3,
            self::MANAGER => 4,
            self::ADMIN => 5,
            self::SUPER_ADMIN => 6,
        ];
    }

    /**
     * Check if a role is a system role (not module-specific)
     */
    public static function isSystemRole(string $role): bool
    {
        return in_array($role, [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::MANAGER,
            self::USER,
        ]);
    }

    /**
     * Check if a role is module-specific
     */
    public static function isModuleRole(string $role): bool
    {
        return !self::isSystemRole($role);
    }

    /**
     * Get the module for a role
     */
    public static function getModule(string $role): ?string
    {
        $moduleMap = [
            self::LEADS_MANAGER => 'leads',
            self::LEADS_EXECUTIVE => 'leads',
            self::FINANCE_MANAGER => 'finance',
            self::FINANCE_ACCOUNTANT => 'finance',
            self::HR_MANAGER => 'hrm',
            self::HR_EXECUTIVE => 'hrm',
            self::PROJECT_MANAGER => 'projects',
            self::TEAM_LEAD => 'projects',
        ];

        return $moduleMap[$role] ?? null;
    }
}
