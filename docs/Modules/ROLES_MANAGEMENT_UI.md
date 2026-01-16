# Roles Management UI

## Overview
A dedicated Roles & Permissions management UI is available in the admin panel for managing role permissions and assigning roles to users.

- Entry point: `/admin/roles`
- Access control: Requires `manage_roles` permission (enforced server-side)

## Features
- View all roles with user/permission counts
- Edit a role's permissions (grouped by module)
- Assign/unassign users to a role

## Files
- Controller: `app/Http/Controllers/Admin/RoleController.php`
- Views:
  - `resources/views/admin/roles/index.blade.php`
  - `resources/views/admin/roles/edit.blade.php`
  - `resources/views/admin/roles/users.blade.php`
- Routes: defined under the admin group in `routes/web.php`

## Usage
- Navigate to `Admin → Roles & Permissions` (URL: `/admin/roles`)
- Click `Edit` to toggle a role's permissions
- Click `Users` to manage which users are assigned the role

## Notes
- Role and permission slugs map to `App\Constants\PermissionConstants`
- Super Admin retains all permissions regardless of UI assignments
- All actions are protected and return 403 for users lacking `manage_roles`
