<?php

namespace App\Constants;

/**
 * Permission Constants
 * 
 * Define all available permissions in the system.
 * Permissions follow the pattern: {action}_{module} or {action}_{module}_{resource}
 */
class PermissionConstants
{
    // Action types
    const ACTION_VIEW = 'view';
    const ACTION_CREATE = 'create';
    const ACTION_EDIT = 'edit';
    const ACTION_DELETE = 'delete';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_MANAGE = 'manage';

    // Module definitions
    const MODULE_LEADS = 'leads';
    const MODULE_FINANCE = 'finance';
    const MODULE_HRM = 'hrm';
    const MODULE_PROJECTS = 'projects';
    const MODULE_ADMIN = 'admin';
    const MODULE_USERS = 'users';
    const MODULE_ROLES = 'roles';

    // Leads Module Permissions
    const VIEW_LEADS = 'view_leads';
    const CREATE_LEADS = 'create_leads';
    const EDIT_LEADS = 'edit_leads';
    const DELETE_LEADS = 'delete_leads';
    const EXPORT_LEADS = 'export_leads';
    const VIEW_LEAD_DOCUMENTS = 'view_lead_documents';
    const CREATE_LEAD_DOCUMENTS = 'create_lead_documents';
    const VIEW_LEAD_FOLLOWUPS = 'view_lead_followups';
    const CREATE_LEAD_FOLLOWUPS = 'create_lead_followups';
    const MANAGE_LEAD_STAGES = 'manage_lead_stages';
    const MANAGE_LEAD_STATUS = 'manage_lead_status';

    // Finance Module Permissions
    const VIEW_FINANCES = 'view_finances';
    const CREATE_FINANCES = 'create_finances';
    const EDIT_FINANCES = 'edit_finances';
    const DELETE_FINANCES = 'delete_finances';
    const VIEW_ACCOUNTS = 'view_accounts';
    const CREATE_ACCOUNTS = 'create_accounts';
    const EDIT_ACCOUNTS = 'edit_accounts';
    const MANAGE_CHART_OF_ACCOUNTS = 'manage_chart_of_accounts';
    const VIEW_TRANSACTIONS = 'view_transactions';
    const CREATE_TRANSACTIONS = 'create_transactions';
    const EDIT_TRANSACTIONS = 'edit_transactions';
    const DELETE_TRANSACTIONS = 'delete_transactions';
    const VIEW_BUDGETS = 'view_budgets';
    const CREATE_BUDGETS = 'create_budgets';
    const EDIT_BUDGETS = 'edit_budgets';
    const VIEW_PURCHASES = 'view_purchases';
    const CREATE_PURCHASES = 'create_purchases';
    const VIEW_SALES = 'view_sales';
    const CREATE_SALES = 'create_sales';
    const RECONCILE_ACCOUNTS = 'reconcile_accounts';
    const GENERATE_FINANCIAL_REPORTS = 'generate_financial_reports';

    // HRM Module Permissions
    const VIEW_EMPLOYEES = 'view_employees';
    const CREATE_EMPLOYEES = 'create_employees';
    const EDIT_EMPLOYEES = 'edit_employees';
    const DELETE_EMPLOYEES = 'delete_employees';
    const VIEW_ATTENDANCE = 'view_attendance';
    const MANAGE_ATTENDANCE = 'manage_attendance';
    const VIEW_LEAVE_REQUESTS = 'view_leave_requests';
    const CREATE_LEAVE_REQUESTS = 'create_leave_requests';
    const APPROVE_LEAVE_REQUESTS = 'approve_leave_requests';
    const VIEW_PAYROLL = 'view_payroll';
    const MANAGE_PAYROLL = 'manage_payroll';
    const VIEW_DEPARTMENTS = 'view_departments';
    const MANAGE_DEPARTMENTS = 'manage_departments';

    // Projects Module Permissions
    const VIEW_PROJECTS = 'view_projects';
    const CREATE_PROJECTS = 'create_projects';
    const EDIT_PROJECTS = 'edit_projects';
    const DELETE_PROJECTS = 'delete_projects';
    const MANAGE_PROJECT_MEMBERS = 'manage_project_members';
    const VIEW_TASKS = 'view_tasks';
    const CREATE_TASKS = 'create_tasks';
    const EDIT_TASKS = 'edit_tasks';

    // Admin Panel Permissions
    const MANAGE_USERS = 'manage_users';
    const MANAGE_ROLES = 'manage_roles';
    const MANAGE_PERMISSIONS = 'manage_permissions';
    const VIEW_ACTIVITY_LOGS = 'view_activity_logs';
    const MANAGE_SYSTEM_SETTINGS = 'manage_system_settings';

    /**
     * Get all permissions organized by module
     */
    public static function getPermissionsByModule(): array
    {
        return [
            self::MODULE_LEADS => [
                self::VIEW_LEADS => 'View Leads',
                self::CREATE_LEADS => 'Create Leads',
                self::EDIT_LEADS => 'Edit Leads',
                self::DELETE_LEADS => 'Delete Leads',
                self::EXPORT_LEADS => 'Export Leads',
                self::VIEW_LEAD_DOCUMENTS => 'View Lead Documents',
                self::CREATE_LEAD_DOCUMENTS => 'Create Lead Documents',
                self::VIEW_LEAD_FOLLOWUPS => 'View Lead Follow-ups',
                self::CREATE_LEAD_FOLLOWUPS => 'Create Lead Follow-ups',
                self::MANAGE_LEAD_STAGES => 'Manage Lead Stages',
                self::MANAGE_LEAD_STATUS => 'Manage Lead Status',
            ],
            self::MODULE_FINANCE => [
                self::VIEW_FINANCES => 'View Finances',
                self::CREATE_FINANCES => 'Create Financial Records',
                self::EDIT_FINANCES => 'Edit Financial Records',
                self::DELETE_FINANCES => 'Delete Financial Records',
                self::VIEW_ACCOUNTS => 'View Accounts',
                self::CREATE_ACCOUNTS => 'Create Accounts',
                self::EDIT_ACCOUNTS => 'Edit Accounts',
                self::MANAGE_CHART_OF_ACCOUNTS => 'Manage Chart of Accounts',
                self::VIEW_TRANSACTIONS => 'View Transactions',
                self::CREATE_TRANSACTIONS => 'Create Transactions',
                self::EDIT_TRANSACTIONS => 'Edit Transactions',
                self::DELETE_TRANSACTIONS => 'Delete Transactions',
                self::VIEW_BUDGETS => 'View Budgets',
                self::CREATE_BUDGETS => 'Create Budgets',
                self::EDIT_BUDGETS => 'Edit Budgets',
                self::VIEW_PURCHASES => 'View Purchases',
                self::CREATE_PURCHASES => 'Create Purchases',
                self::VIEW_SALES => 'View Sales',
                self::CREATE_SALES => 'Create Sales',
                self::RECONCILE_ACCOUNTS => 'Reconcile Accounts',
                self::GENERATE_FINANCIAL_REPORTS => 'Generate Financial Reports',
            ],
            self::MODULE_HRM => [
                self::VIEW_EMPLOYEES => 'View Employees',
                self::CREATE_EMPLOYEES => 'Create Employees',
                self::EDIT_EMPLOYEES => 'Edit Employees',
                self::DELETE_EMPLOYEES => 'Delete Employees',
                self::VIEW_ATTENDANCE => 'View Attendance',
                self::MANAGE_ATTENDANCE => 'Manage Attendance',
                self::VIEW_LEAVE_REQUESTS => 'View Leave Requests',
                self::CREATE_LEAVE_REQUESTS => 'Create Leave Requests',
                self::APPROVE_LEAVE_REQUESTS => 'Approve Leave Requests',
                self::VIEW_PAYROLL => 'View Payroll',
                self::MANAGE_PAYROLL => 'Manage Payroll',
                self::VIEW_DEPARTMENTS => 'View Departments',
                self::MANAGE_DEPARTMENTS => 'Manage Departments',
            ],
            self::MODULE_PROJECTS => [
                self::VIEW_PROJECTS => 'View Projects',
                self::CREATE_PROJECTS => 'Create Projects',
                self::EDIT_PROJECTS => 'Edit Projects',
                self::DELETE_PROJECTS => 'Delete Projects',
                self::MANAGE_PROJECT_MEMBERS => 'Manage Project Members',
                self::VIEW_TASKS => 'View Tasks',
                self::CREATE_TASKS => 'Create Tasks',
                self::EDIT_TASKS => 'Edit Tasks',
            ],
            self::MODULE_ADMIN => [
                self::MANAGE_USERS => 'Manage Users',
                self::MANAGE_ROLES => 'Manage Roles',
                self::MANAGE_PERMISSIONS => 'Manage Permissions',
                self::VIEW_ACTIVITY_LOGS => 'View Activity Logs',
                self::MANAGE_SYSTEM_SETTINGS => 'Manage System Settings',
            ],
        ];
    }

    /**
     * Get default permissions for each role
     */
    public static function getRolePermissions(): array
    {
        return [
            RoleConstants::SUPER_ADMIN => self::all(),
            RoleConstants::ADMIN => array_merge(
                self::getPermissionsByModule()[self::MODULE_LEADS],
                self::getPermissionsByModule()[self::MODULE_FINANCE],
                self::getPermissionsByModule()[self::MODULE_HRM],
                self::getPermissionsByModule()[self::MODULE_PROJECTS],
                self::getPermissionsByModule()[self::MODULE_ADMIN],
            ),
            RoleConstants::MANAGER => [
                self::VIEW_LEADS,
                self::CREATE_LEADS,
                self::EDIT_LEADS,
                self::VIEW_FINANCES,
                self::VIEW_TRANSACTIONS,
                self::VIEW_EMPLOYEES,
                self::VIEW_PROJECTS,
                self::MANAGE_PROJECT_MEMBERS,
            ],
            RoleConstants::USER => [
                self::VIEW_LEADS,
                self::VIEW_FINANCES,
                self::VIEW_EMPLOYEES,
                self::VIEW_PROJECTS,
            ],
            RoleConstants::LEADS_MANAGER => [
                self::VIEW_LEADS,
                self::CREATE_LEADS,
                self::EDIT_LEADS,
                self::DELETE_LEADS,
                self::EXPORT_LEADS,
                self::VIEW_LEAD_DOCUMENTS,
                self::CREATE_LEAD_DOCUMENTS,
                self::VIEW_LEAD_FOLLOWUPS,
                self::CREATE_LEAD_FOLLOWUPS,
                self::MANAGE_LEAD_STAGES,
                self::MANAGE_LEAD_STATUS,
            ],
            RoleConstants::LEADS_EXECUTIVE => [
                self::VIEW_LEADS,
                self::CREATE_LEADS,
                self::EDIT_LEADS,
                self::VIEW_LEAD_DOCUMENTS,
                self::CREATE_LEAD_DOCUMENTS,
                self::VIEW_LEAD_FOLLOWUPS,
                self::CREATE_LEAD_FOLLOWUPS,
            ],
            RoleConstants::FINANCE_MANAGER => [
                self::VIEW_FINANCES,
                self::CREATE_FINANCES,
                self::EDIT_FINANCES,
                self::DELETE_FINANCES,
                self::VIEW_ACCOUNTS,
                self::CREATE_ACCOUNTS,
                self::EDIT_ACCOUNTS,
                self::MANAGE_CHART_OF_ACCOUNTS,
                self::VIEW_TRANSACTIONS,
                self::CREATE_TRANSACTIONS,
                self::EDIT_TRANSACTIONS,
                self::VIEW_BUDGETS,
                self::CREATE_BUDGETS,
                self::EDIT_BUDGETS,
                self::VIEW_PURCHASES,
                self::CREATE_PURCHASES,
                self::VIEW_SALES,
                self::CREATE_SALES,
                self::RECONCILE_ACCOUNTS,
                self::GENERATE_FINANCIAL_REPORTS,
            ],
            RoleConstants::FINANCE_ACCOUNTANT => [
                self::VIEW_FINANCES,
                self::VIEW_ACCOUNTS,
                self::VIEW_TRANSACTIONS,
                self::CREATE_TRANSACTIONS,
                self::EDIT_TRANSACTIONS,
                self::VIEW_BUDGETS,
                self::RECONCILE_ACCOUNTS,
            ],
            RoleConstants::HR_MANAGER => [
                self::VIEW_EMPLOYEES,
                self::CREATE_EMPLOYEES,
                self::EDIT_EMPLOYEES,
                self::DELETE_EMPLOYEES,
                self::VIEW_ATTENDANCE,
                self::MANAGE_ATTENDANCE,
                self::VIEW_LEAVE_REQUESTS,
                self::APPROVE_LEAVE_REQUESTS,
                self::VIEW_PAYROLL,
                self::MANAGE_PAYROLL,
                self::VIEW_DEPARTMENTS,
                self::MANAGE_DEPARTMENTS,
            ],
            RoleConstants::HR_EXECUTIVE => [
                self::VIEW_EMPLOYEES,
                self::VIEW_ATTENDANCE,
                self::MANAGE_ATTENDANCE,
                self::VIEW_LEAVE_REQUESTS,
                self::CREATE_LEAVE_REQUESTS,
            ],
            RoleConstants::PROJECT_MANAGER => [
                self::VIEW_PROJECTS,
                self::CREATE_PROJECTS,
                self::EDIT_PROJECTS,
                self::DELETE_PROJECTS,
                self::MANAGE_PROJECT_MEMBERS,
                self::VIEW_TASKS,
                self::CREATE_TASKS,
                self::EDIT_TASKS,
            ],
            RoleConstants::TEAM_LEAD => [
                self::VIEW_PROJECTS,
                self::VIEW_TASKS,
                self::CREATE_TASKS,
                self::EDIT_TASKS,
                self::MANAGE_PROJECT_MEMBERS,
            ],
        ];
    }

    /**
     * Get all permissions
     */
    public static function all(): array
    {
        $permissions = [];
        foreach (self::getPermissionsByModule() as $module => $modulePermissions) {
            $permissions = array_merge($permissions, array_keys($modulePermissions));
        }
        return $permissions;
    }
}
