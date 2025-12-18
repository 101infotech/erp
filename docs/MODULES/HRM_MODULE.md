# HRM Module - Human Resource Management

## Overview

The HRM (Human Resource Management) module is a comprehensive employee management system integrated into the Saubhagya Group ERP. It provides employee data management, attendance tracking, and seamless integration with Jibble time tracking platform.

## Features

### 1. Employee Management

-   Complete employee profile management
-   Department and company assignment
-   Employee status tracking (active, inactive, terminated)
-   Jibble person ID linking for sync
-   User account linking for employee self-service portal

### 2. Attendance Tracking

-   Daily attendance records
-   Integration with Jibble timesheets
-   Tracked hours, payroll hours, and overtime calculation
-   Historical attendance data
-   **Employee self-service**: View personal attendance records with filtering

### 3. Payroll Management

-   Payroll record generation with period tracking
-   Gross salary calculation with bonuses and overtime
-   Automatic tax calculation using Nepal Tax Calculation Service
-   SSF (Social Security Fund) deductions
-   Net salary computation
-   PDF payslip generation
-   **Employee self-service**: View salary history and download payslips

### 4. Leave Management

-   Leave request creation and tracking
-   Multiple leave types (sick, casual, annual, unpaid, period)
-   **Leave Policy System** - Admin-defined policies with company-specific quotas
-   **Gender-based Restrictions** - Policies can be restricted by gender (e.g., period leave for females only)
-   Leave balance tracking per employee with automatic policy application
-   Approval workflow with admin controls
-   Leave history and status tracking
-   **Employee self-service**: Request leave and view leave balance
-   **Admin panel**:
    -   Create and manage leave policies
    -   Set gender restrictions for specific leave types
    -   Filter to show only active policies
    -   Approve/reject leave requests with reasons
    -   Automatic policy application to employees

### 5. Jibble Integration

-   **OAuth2 Authentication** - Secure token-based API access
-   **Employee Sync** - Bi-directional sync between ERP and Jibble
-   **Timesheet Sync** - Automatic attendance data import
-   **Real-time Updates** - Scheduled sync via Laravel commands

### 6. Department & Company Management

-   Multi-company support
-   Department hierarchy
-   Employee assignment to departments

### 7. Employee Self-Service Portal

-   Personal dashboard with attendance, leave, and payroll overview
-   Attendance tracking with monthly/custom date range views
-   Payroll history with detailed breakdown and tax information
-   Leave request submission and balance tracking
-   PDF payslip downloads
-   Responsive UI with dark theme

## Architecture

### Database Schema

#### companies

-   id (primary key)
-   name
-   address
-   timestamps

#### departments

-   id (primary key)
-   company_id (foreign key)
-   name
-   timestamps

#### employees

-   id (primary key)
-   user_id (foreign key to users, nullable)
-   company_id (foreign key)
-   department_id (foreign key, nullable)
-   jibble_person_id (unique, nullable)
-   full_name
-   email (unique, nullable)
-   phone (nullable)
-   code (unique - employee code)
-   status (enum: active, inactive, terminated)
-   timestamps

#### attendance_days

-   id (primary key)
-   employee_id (foreign key)
-   date (date)
-   tracked_hours (decimal)
-   payroll_hours (decimal)
-   overtime_hours (decimal)
-   source (string - 'jibble', 'manual')
-   jibble_data (json - raw data from Jibble)
-   notes (text, nullable)
-   timestamps
-   unique constraint on (employee_id, date)

#### hrm_leave_policies

-   id (primary key)
-   company_id (foreign key)
-   policy_name (string)
-   leave_type (enum: annual, sick, casual, unpaid, period)
-   gender_restriction (enum: none, male, female) - Default: 'none'
-   annual_quota (decimal - days per year)
-   carry_forward_allowed (boolean)
-   max_carry_forward (decimal - maximum days to carry forward)
-   requires_approval (boolean)
-   is_active (boolean)
-   timestamps

#### hrm_leave_requests

-   id (primary key)
-   employee_id (foreign key)
-   leave_type (enum: annual, sick, casual, unpaid, period)
-   start_date (date)
-   end_date (date)
-   total_days (decimal)
-   reason (text)
-   status (enum: pending, approved, rejected)
-   admin_notes (text, nullable)
-   approved_by (foreign key to users, nullable)
-   approved_at (timestamp, nullable)
-   timestamps

#### employees (leave-related fields)

-   annual_leave_balance (decimal)
-   sick_leave_balance (decimal)
-   casual_leave_balance (decimal)
-   period_leave_balance (decimal)

### Leave Policy System

The leave policy system provides centralized management of leave policies with gender-based restrictions.

#### Key Features

1. **Company-Specific Policies** - Each policy is linked to a specific company
2. **Gender Restrictions** - Policies can be restricted to:
    - None (all employees)
    - Male only
    - Female only (e.g., for period/menstrual leave)
3. **Automatic Application** - Policies are automatically applied to:
    - All existing employees when policy is created/updated
    - New employees when they are added
4. **Active Policy Filtering** - Only active policies are shown and applied
5. **Carry Forward Support** - Configurable carry forward of unused leave days

#### LeavePolicyService

Centralized service for leave policy management and validation.

**Methods:**

-   `applyPoliciesToEmployee(HrmEmployee $employee)` - Apply all active company policies to an employee (respects gender restrictions)
-   `applyPoliciesToCompany(int $companyId)` - Apply all active policies to all employees in a company
-   `getPolicy(int $companyId, string $leaveType)` - Get active policy for specific leave type
-   `validateLeaveRequest(HrmEmployee $employee, string $leaveType, float $days)` - Validate leave request against policy and balance (checks gender restrictions)
-   `getLeaveBalanceSummary(HrmEmployee $employee)` - Get leave balance summary for employee (only includes policies available for employee's gender)
-   `resetAnnualLeaveBalances(int $companyId)` - Reset leave balances based on policies

#### Gender-Based Leave Policies

Period/menstrual leave is typically restricted to female employees only. The system enforces this through:

1. **Policy Creation** - Admin sets gender_restriction to 'female' when creating period leave policy
2. **Policy Application** - LeavePolicyService only applies policies to employees matching the gender restriction
3. **Leave Validation** - validateLeaveRequest() checks if employee's gender matches policy restriction
4. **UI Filtering** - Leave request forms only show leave types available to the employee based on gender

Example: Creating a female-only period leave policy

```php
HrmLeavePolicy::create([
    'company_id' => 1,
    'policy_name' => 'Period Leave Policy',
    'leave_type' => 'period',
    'gender_restriction' => 'female',
    'annual_quota' => 12,
    'requires_approval' => true,
    'is_active' => true
]);
```

### Service Layer

#### JibbleAuthService

Handles authentication with Jibble API using OAuth2 client credentials flow.

**Methods:**

-   `getToken()` - Get access token (cached for performance)

**Configuration:**

-   JIBBLE_CLIENT_ID
-   JIBBLE_CLIENT_SECRET
-   JIBBLE_BASE_URL

#### JibblePeopleService

Manages employee/people synchronization with Jibble.

**Methods:**

-   `fetchAll()` - Fetch all people from Jibble with pagination
-   `syncPeople()` - Sync Jibble people to local employees table
-   `createPerson()` - Create new person in Jibble
-   `updatePerson()` - Update person in Jibble
-   `deletePerson()` - Remove person from Jibble

**Features:**

-   Automatic company and department resolution
-   User account linking for employees with email
-   Employee code generation

#### JibbleTimesheetService

Handles attendance/timesheet synchronization.

**Methods:**

-   `syncDailySummary()` - Sync daily attendance summary
-   `fetchDetailedTimesheets()` - Get detailed timesheet data
-   `syncAndFetchEmployeeAttendance()` - Sync and return employee attendance
-   `parseDuration()` - Parse ISO 8601 duration to decimal hours

**Features:**

-   ISO 8601 duration parsing (PT8H30M format)
-   Automatic overtime calculation
-   Duplicate prevention (unique constraint)
-   Raw data storage for audit trail

### Holidays

-   Holidays are stored in the `holidays` table with `name`, `date`, `description`, `is_company_wide`, and `is_active` fields.
-   Admin UI: Manage at Admin → HRM → Holidays (`admin.hrm.holidays.*`). Supports add/edit/delete.
-   Employee UI: View-only list at Employee → Holidays (`employee.holidays.index`).
-   Attendance calendar overlays holidays for the selected month and highlights them in amber.

### Console Commands

#### sync:jibble-employees

Sync all employees from Jibble to local database.

```bash
php artisan sync:jibble-employees
```

**Schedule:** Daily at 2:00 AM

#### sync:jibble-attendance

Sync attendance data from Jibble.

```bash
php artisan sync:jibble-attendance --start=2024-12-01 --end=2024-12-02
```

**Options:**

-   `--start` - Start date (default: yesterday)
-   `--end` - End date (default: yesterday)

**Schedule:** Daily at 3:00 AM

### Admin Panel Features

#### Employees Module

-   **List View** - All employees with search and filters
-   **Create/Edit** - Full employee profile management
-   **Jibble Sync** - Manual sync button to pull from Jibble
-   **Status Management** - Activate/deactivate employees
-   **User Linking** - Link employees to user accounts for portal access

#### Attendance Module

-   **Calendar View** - Monthly attendance overview
-   **Daily View** - Detailed daily attendance records
-   **Employee View** - Individual employee attendance history
-   **Sync Interface** - Manual sync with date range selection
-   **Reports** - Attendance reports with export

#### Payroll Module

-   **Payroll Generation** - Create payroll records for employees
-   **Tax Calculation** - Automatic Nepal tax calculation integration
-   **Deductions Management** - SSF, tax, and other deductions
-   **Payslip PDF** - Generate and store payslip PDFs
-   **Payment Tracking** - Mark payrolls as paid with payment dates
-   **Anomaly Detection** - Flag attendance anomalies for review

#### Leave Management Module

-   **Leave Request List** - View all leave requests with status filters
-   **Approval Workflow** - Approve or reject leave requests
-   **Balance Management** - Track and update leave balances
-   **Leave Policies** - Configure leave types and allowances
-   **Rejection Reasons** - Provide feedback for rejected requests
-   **Leave Calendar** - Visual overview of team leave schedules

#### Departments Module

-   **CRUD Operations** - Create, read, update, delete departments
-   **Employee Assignment** - Assign employees to departments
-   **Company Grouping** - Departments organized by company

#### Companies Module

-   **CRUD Operations** - Manage multiple companies
-   **Department Management** - View departments per company
-   **Employee Count** - Track employees per company

## API Endpoints

### Public API (JSON responses)

```
GET  /api/v1/hrm/employees?company=<slug>         # List employees
GET  /api/v1/hrm/employees/{id}                   # Get employee details
GET  /api/v1/hrm/attendance?employee_id={id}&start={date}&end={date}
```

### Employee Self-Service API (Protected - requires authentication)

```
GET    /employee/dashboard                        # Employee dashboard
GET    /employee/attendance                       # View attendance records
GET    /employee/attendance/data                  # Get attendance data as JSON
GET    /employee/payroll                          # View payroll history
GET    /employee/payroll/{id}                     # View specific payslip
GET    /employee/payroll/{id}/download            # Download payslip PDF
GET    /employee/payroll-data                     # Get payroll data as JSON
GET    /employee/leave                            # View leave requests
GET    /employee/leave/create                     # Show leave request form
POST   /employee/leave                            # Submit leave request
GET    /employee/leave/{id}                       # View leave request details
POST   /employee/leave/{id}/cancel                # Cancel pending leave request
GET    /employee/leave-data                       # Get leave data as JSON
```

### Admin API (Protected)

```
GET    /admin/hrm/employees                       # List employees
GET    /admin/hrm/employees/create                # Create form
POST   /admin/hrm/employees                       # Store employee
GET    /admin/hrm/employees/{id}                  # Show employee
GET    /admin/hrm/employees/{id}/edit             # Edit form
PUT    /admin/hrm/employees/{id}                  # Update employee
DELETE /admin/hrm/employees/{id}                  # Delete employee

POST   /admin/hrm/sync/employees                  # Manual Jibble sync
POST   /admin/hrm/sync/attendance                 # Manual attendance sync

GET    /admin/hrm/attendance                      # Attendance list
GET    /admin/hrm/attendance/calendar             # Calendar view
GET    /admin/hrm/attendance/{id}                 # Attendance details

GET    /admin/hrm/payroll                         # Payroll list
POST   /admin/hrm/payroll                         # Create payroll
GET    /admin/hrm/payroll/{id}                    # View payroll
PUT    /admin/hrm/payroll/{id}                    # Update payroll
POST   /admin/hrm/payroll/{id}/approve            # Approve payroll
POST   /admin/hrm/payroll/{id}/mark-as-paid       # Mark as paid
GET    /admin/hrm/payroll/{id}/download-pdf       # Download payslip

GET    /admin/hrm/leaves                          # Leave requests list
GET    /admin/hrm/leaves/create                   # Create leave request form
POST   /admin/hrm/leaves                          # Store leave request
GET    /admin/hrm/leaves/{id}                     # View leave request
POST   /admin/hrm/leaves/{id}/approve             # Approve leave
POST   /admin/hrm/leaves/{id}/reject              # Reject leave
POST   /admin/hrm/leaves/{id}/cancel              # Cancel leave

GET    /admin/hrm/departments                     # List departments
POST   /admin/hrm/departments                     # Create department
PUT    /admin/hrm/departments/{id}                # Update department
DELETE /admin/hrm/departments/{id}                # Delete department

GET    /admin/hrm/companies                       # List companies
POST   /admin/hrm/companies                       # Create company
PUT    /admin/hrm/companies/{id}                  # Update company
DELETE /admin/hrm/companies/{id}                  # Delete company
```

## Blade Templates Structure

```
resources/views/admin/hrm/
├── layouts/
│   └── hrm-nav.blade.php                # HRM sub-navigation
├── employees/
│   ├── index.blade.php                  # Employee list with search
│   ├── create.blade.php                 # Create employee form
│   ├── edit.blade.php                   # Edit employee form
│   ├── show.blade.php                   # Employee details
│   └── sync.blade.php                   # Jibble sync interface
├── attendance/
│   ├── index.blade.php                  # Attendance list
│   ├── calendar.blade.php               # Calendar view
│   ├── show.blade.php                   # Daily attendance details
│   └── employee.blade.php               # Employee attendance history
├── departments/
│   ├── index.blade.php                  # Department list
│   ├── create.blade.php                 # Create department
│   └── edit.blade.php                   # Edit department
└── companies/
    ├── index.blade.php                  # Company list
    ├── create.blade.php                 # Create company
    └── edit.blade.php                   # Edit company
```

## Configuration

### Environment Variables

Add to `.env`:

```env
# Jibble API Configuration
JIBBLE_CLIENT_ID=your_client_id
JIBBLE_CLIENT_SECRET=your_client_secret
JIBBLE_WORKSPACE_ID=your_workspace_id
JIBBLE_BASE_URL=https://time-attendance.prod.jibble.io
JIBBLE_DEFAULT_COMPANY_ID=1

# Sync Settings
JIBBLE_SYNC_ENABLED=true
JIBBLE_SYNC_DAYS=7
JIBBLE_BATCH_SIZE=100
```

### Service Configuration

In `config/services.php`:

```php
'jibble' => [
    'client_id' => env('JIBBLE_CLIENT_ID'),
    'client_secret' => env('JIBBLE_CLIENT_SECRET'),
    'workspace_id' => env('JIBBLE_WORKSPACE_ID'),
    'base_url' => env('JIBBLE_BASE_URL', 'https://time-attendance.prod.jibble.io'),
    'people_select' => env('JIBBLE_PEOPLE_SELECT', 'id,fullName,email,department'),
    'default_company_id' => env('JIBBLE_DEFAULT_COMPANY_ID'),
],
```

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Configure Jibble API

Add credentials to `.env` file (see Configuration section above).

### 3. Initial Sync

```bash
# Sync employees from Jibble
php artisan sync:jibble-employees

# Sync last 7 days of attendance
php artisan sync:jibble-attendance --start=$(date -d '7 days ago' +%Y-%m-%d) --end=$(date +%Y-%m-%d)
```

### 4. Schedule Tasks

The following tasks are automatically scheduled:

-   Employee sync: Daily at 2:00 AM
-   Attendance sync: Daily at 3:00 AM

Ensure Laravel scheduler is running:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Usage Guide

### For Employees

#### Accessing the Employee Portal

1. Navigate to the login page
2. Enter your email and password
3. After login, you'll be redirected to your employee dashboard

#### Viewing Attendance

1. Click **Attendance** in the navigation
2. View your monthly attendance records
3. Use date filters to see specific periods
4. Check hours worked, payroll hours, and overtime

#### Checking Payroll

1. Click **Payroll** in the navigation
2. View your salary history
3. Click on any payroll record to see detailed breakdown
4. Download payslip PDFs for your records
5. Review tax calculations and deductions

#### Requesting Leave

1. Click **Leave** in the navigation
2. Check your leave balance for each type
3. Click **Request Leave** button
4. Fill in:
    - Leave type (sick, casual, annual, or unpaid)
    - Start and end dates
    - Reason for leave
5. Submit the request
6. Track status (pending, approved, rejected)
7. Cancel pending requests if needed

### For Administrators

#### Managing Leave Requests

1. Navigate to **HRM → Leave Management**
2. View requests by status (Pending, Approved, Rejected, All)
3. **To Approve**: Click on request → Click "Approve" button
4. **To Reject**: Click on request → Click "Reject" → Enter reason
5. View employee leave balance before approving
6. System automatically deducts approved leave from balance

#### Generating Payroll

1. Navigate to **HRM → Payroll**
2. Click **Create Payroll**
3. Select employee and pay period
4. System automatically:
    - Fetches attendance hours
    - Calculates gross salary
    - Applies Nepal tax calculation
    - Adds SSF deductions
    - Computes net salary
5. Review and approve
6. Mark as paid when payment is processed
7. Download PDF payslip for employee

#### Adding New Employee

1. Navigate to **HRM → Employees → Add New**
2. Fill in employee details (name, email, phone, department)
3. Set base salary and leave balances
4. **Important**: Link to user account if employee needs portal access
    - If email matches a user account, it will be auto-linked
    - Or create a new user account from the Users menu
5. Click **Save**
6. System will attempt to sync with Jibble if employee has email

#### Syncing with Jibble

**Automatic Sync:**

-   Runs daily at scheduled times
-   No manual intervention needed

**Manual Sync:**

1. Navigate to **HRM → Employees → Sync from Jibble**
2. Select date range (for attendance) or click "Sync Employees"
3. System displays sync results

#### Viewing Attendance

1. Navigate to **HRM → Attendance**
2. Choose view type:
    - **List View** - Table format with filters
    - **Calendar View** - Monthly calendar
    - **Employee View** - Individual employee history
3. Use filters to refine results

### For API Consumers

#### Get Employee List

```javascript
fetch("/api/v1/hrm/employees?company=saubhagya-group")
    .then((res) => res.json())
    .then((data) => console.log(data));
```

#### Get Employee Attendance

```javascript
fetch("/api/v1/hrm/attendance?employee_id=5&start=2024-12-01&end=2024-12-31")
    .then((res) => res.json())
    .then((data) => console.log(data));
```

## Security Considerations

1. **API Authentication**

    - Admin routes protected by auth middleware
    - Public API endpoints rate-limited
    - Jibble credentials stored securely in .env

2. **Data Privacy**

    - Employee personal data encrypted at rest
    - Access logs for sensitive operations
    - Role-based access control (future enhancement)

3. **Jibble Integration**
    - OAuth2 token cached with expiration
    - HTTPS required for API calls
    - Token refresh handling

## Performance Optimization

1. **Caching**

    - Jibble access token cached (expires in ~55 minutes)
    - Employee list cached for dashboard
    - Attendance summary cached per day

2. **Batch Processing**

    - Jibble API pagination (100 records per page)
    - Bulk insert for attendance data
    - Queue jobs for large syncs (future)

3. **Database Indexing**
    - Indexed: employee_id, date, company_id, department_id
    - Unique constraints prevent duplicates
    - Foreign key indexes for joins

## Troubleshooting

### Common Issues

**Issue:** Jibble sync fails with "Unable to authenticate"

-   **Solution:** Verify JIBBLE_CLIENT_ID and JIBBLE_CLIENT_SECRET in .env
-   Run `php artisan config:clear` to refresh configuration

**Issue:** Duplicate attendance records

-   **Solution:** Database has unique constraint. Check jibble_person_id mapping.

**Issue:** Employee not syncing from Jibble

-   **Solution:** Check employee has valid email in Jibble
-   Verify employee role is 'Member' in Jibble

**Issue:** Overtime hours not calculating

-   **Solution:** Ensure Jibble timesheet has overtime data
-   Check `parseDuration()` method for ISO 8601 parsing

### Debug Mode

Enable logging in sync commands:

```bash
php artisan sync:jibble-employees -vvv
php artisan sync:jibble-attendance --start=2024-12-01 --end=2024-12-01 -vvv
```

Check logs:

```bash
tail -f storage/logs/laravel.log
```

## Future Enhancements

### Planned Features

1. **Advanced Leave Management**

    - ✅ Leave request workflow (IMPLEMENTED)
    - ✅ Leave balance tracking (IMPLEMENTED)
    - Integration with Jibble time-off
    - Leave calendar view for teams
    - Leave policy configuration

2. **Enhanced Payroll Integration**

    - ✅ Automatic payroll calculation from attendance (IMPLEMENTED)
    - ✅ Overtime and bonus tracking (IMPLEMENTED)
    - ✅ Salary slip generation (IMPLEMENTED)
    - ✅ Nepal tax calculation integration (IMPLEMENTED)
    - Bank transfer integration
    - Bulk payroll processing

3. **Employee Self-Service Portal**

    - ✅ View own attendance (IMPLEMENTED)
    - ✅ Request leave (IMPLEMENTED)
    - ✅ View payroll and download payslips (IMPLEMENTED)
    - ✅ Personal dashboard (IMPLEMENTED)
    - Update profile information
    - Submit expense claims
    - View company announcements

4. **Performance Reviews**

    - Employee performance tracking
    - Goal setting and KPIs
    - Review cycle management
    - 360-degree feedback

5. **Advanced Reporting**

    - Custom attendance reports
    - Department-wise analytics
    - Payroll cost analysis
    - Leave utilization reports
    - Export to Excel/PDF

6. **Mobile App Integration**
    - Mobile attendance marking
    - Push notifications for leave approvals
    - GPS-based check-in
    - Quick access to payslips

## Recent Updates (December 2024)

### Employee Self-Service Portal (NEW)

-   Full-featured employee portal with authentication
-   Personal dashboard showing attendance, leave, and payroll overview
-   Attendance tracking with custom date filters
-   Payroll history with detailed tax breakdown
-   Leave request system with balance tracking
-   Responsive dark-themed UI

### Payroll Management (NEW)

-   Comprehensive payroll record management
-   Integration with Nepal Tax Calculation Service
-   Automatic SSF deduction calculation
-   PDF payslip generation and storage
-   Employee access to payroll history
-   Admin approval workflow

### Leave Management System (NEW)

-   Employee leave request submission
-   Multi-type leave support (sick, casual, annual, unpaid)
-   Real-time leave balance tracking
-   Admin approval/rejection workflow
-   Rejection reason tracking
-   Leave history and status monitoring

### Authentication & Access Control

-   Email/password authentication
-   Role-based access (admin vs employee)
-   Separate portals for admin and employees
-   Secure password handling
-   Session management

## Technical Implementation Notes

### Employee Portal Authentication

-   Uses Laravel's built-in authentication system
-   Email and password login only (no social auth)
-   User accounts linked to HRM employee records via `user_id`
-   Middleware protection for all employee routes
-   Automatic redirect based on user role

### Tax Calculation

-   Implements Nepal's progressive tax slabs for FY 2081/82 (2024/25)
-   Automatic tax calculation based on annual income
-   Monthly tax computation via annualization
-   Detailed tax breakdown display for employees
-   Integrated with payroll record generation

### Leave Balance Management

-   Default annual allocations: 12 sick, 10 casual, 15 annual days
-   Balance tracked per employee per year
-   Automatic deduction on leave approval
-   Restoration on leave cancellation
-   Validation before request submission

### PDF Payslip Generation

-   Uses DomPDF for payslip generation
-   Stored in `storage/app/payslips/`
-   Includes company details, employee info, earnings, and deductions
-   Secure download for employees
-   Linked to payroll records

## API Reference

See separate API documentation for detailed endpoint specifications.

## License

Proprietary - © 2024 Saubhagya Group. All rights reserved.
