# Users vs Employees - System Clarification

## The Confusion

Currently, there are TWO pages that appear to show similar data:

1. **Team & Users** (`/admin/users`) - Located under "HRM MODULE" in sidebar
2. **Employees** (`/admin/hrm/employees`) - Located under "HRM MODULE" > "Organization"

## The Problem

Both pages are currently showing **HRM Employees** data, which creates confusion:

- `/admin/users` is using `HrmEmployee` model (not `User` model)
- `/admin/hrm/employees` is also using `HrmEmployee` model
- This creates duplicate functionality and confusing UX

## The Data Model

### User Model (`users` table)
- **Purpose**: System authentication & authorization
- **Fields**: email, password, role (admin/user), status
- **Represents**: Login accounts for the ERP system
- **Can exist without**: Employee record (system admin who is not staff)

### HrmEmployee Model (`hrm_employees` table)
- **Purpose**: HR staff records & management
- **Fields**: name, email, position, company_id, department_id, jibble_person_id, user_id
- **Represents**: Actual company employees with HR data
- **Can exist without**: User account (staff without system access)

### The Relationship
- **One-to-One**: A User can have ONE linked HrmEmployee (user_id foreign key)
- **Optional**: Users can exist without employees (system admins)
- **Optional**: Employees can exist without users (staff without system access)

## The Solution Options

### Option 1: Merge into Single "Team & Users" Page (RECOMMENDED)
- Keep only `/admin/users` route
- Remove `/admin/hrm/employees` page (it's redundant)
- Show ALL employees with their user account status
- Actions:
  - Link/Unlink user accounts
  - Manage Jibble integration
  - Edit employee HR data
  - View timesheets

### Option 2: Clear Separation by Purpose
- **Team & Users** (`/admin/users`): Focus on SYSTEM ACCOUNTS
  - Show only Users with login access
  - Manage passwords, roles, permissions
  - Link to employee records
  
- **Employees** (`/admin/hrm/employees`): Focus on HR RECORDS
  - Show all staff (regardless of system access)
  - Manage HR data (position, department, company)
  - Handle Jibble integration
  - Manage attendance, payroll, leaves

## Current Implementation Analysis

Looking at the code:

```php
// UserController@index - CURRENTLY showing HrmEmployee!
$query = \App\Models\HrmEmployee::with('user', 'company', 'department')->latest();
```

This is wrong! UserController should manage Users, not Employees.

## Recommendation: Option 1 (Simplified Approach)

Since most staff should have BOTH an employee record AND a user account:

1. **Rename** "Team & Users" to "Employees & Accounts"
2. **Show** HRM Employees as the main data
3. **Display** user account status (linked/not linked)
4. **Provide** actions to:
   - Create/link user accounts for employees
   - Manage Jibble sync
   - Edit employee info
   - View timesheets

5. **Remove** `/admin/hrm/employees` page entirely (keep the controller for show/edit actions)

## Implementation Steps

1. Update sidebar navigation to remove duplicate entry
2. Update UserController to properly show HRM employees with account status
3. Add clear badges showing:
   - "Has Login Access" (green) vs "No Account" (gray)
   - "Linked to Jibble" (orange) vs "Not Synced" (gray)
4. Improve action buttons to be clearer about what they do
5. Add tooltips/help text explaining the difference

## Date: 2026-01-05
