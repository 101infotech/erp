# Jibble Sync System Guide

## Overview

The Jibble Sync System automatically synchronizes employee data, attendance records, and time entries from Jibble Time Tracking platform to the local ERP database.

## Features

-   **Automated Daily Sync**: Runs twice daily at 8 AM and 6 PM (Asia/Kathmandu timezone)
-   **Manual Sync Options**: Web interface buttons and CLI commands
-   **Selective Sync**: Choose to sync employees only, attendance only, or all data
-   **Duplicate Prevention**: Smart upsert logic prevents duplicate records
-   **Error Handling**: Continues syncing even if individual records fail

## Components

### 1. Services

#### JibblePeopleService

-   **Method**: `syncEmployees()`
-   **Purpose**: Fetches employees from Jibble People API and creates/updates local `hrm_employees` records
-   **Returns**: Count of employees synced
-   **Features**:
    -   Links Jibble person ID to local employee
    -   Creates user accounts for employees with email
    -   Auto-assigns to default company/department if not specified
    -   Handles missing data gracefully

#### JibbleTimesheetService

-   **Method**: `syncDailySummary($startDate, $endDate)`
-   **Purpose**: Fetches daily attendance summaries (tracked hours, payroll hours, overtime)
-   **Returns**: Count of attendance records synced
-   **API Endpoint**: `https://time-attendance.prod.jibble.io/v1/TimesheetsSummary`
-   **Features**:
    -   Parses ISO 8601 duration format (PT8H30M)
    -   Updates existing records instead of duplicating
    -   Skips days with zero tracked time
    -   Stores raw Jibble data as JSON for reference

#### JibbleTimeTrackingService

-   **Method**: `syncTimeEntries($startDate, $endDate)`
-   **Purpose**: Fetches individual clock in/out entries
-   **Returns**: Count of time entries synced
-   **API Endpoint**: `https://time-tracking.prod.jibble.io/v1/TimeEntries`
-   **Features**:
    -   Links entries to attendance days
    -   Stores project/activity information
    -   Captures location and notes
    -   Handles both IN and OUT entries

### 2. Console Command

#### Command: `php artisan jibble:sync`

**Options:**

-   `--all` - Sync employees and attendance (default if no option specified)
-   `--employees` - Sync only employee data
-   `--attendance` - Sync only attendance data
-   `--days=N` - Number of days to sync (default: 7, max: 90)

**Examples:**

```bash
# Sync everything for the last 7 days
php artisan jibble:sync --all

# Sync only employees
php artisan jibble:sync --employees

# Sync attendance for the last 30 days
php artisan jibble:sync --attendance --days=30

# Full sync (employees + 30 days attendance)
php artisan jibble:sync --all --days=30
```

### 3. Scheduled Task

**Schedule**: Runs automatically twice daily at 8:00 AM and 6:00 PM

**Configuration**: `routes/console.php`

```php
Schedule::command('jibble:sync --all')
    ->twiceDaily(8, 18)
    ->timezone('Asia/Kathmandu')
    ->withoutOverlapping()
    ->runInBackground();
```

**To activate scheduled tasks**, add this cron entry to your server:

```bash
* * * * * cd /path/to/erp && php artisan schedule:run >> /dev/null 2>&1
```

**Verify scheduled tasks:**

```bash
php artisan schedule:list
```

### 4. Web Interface

#### Sync All Button (Attendance Index)

-   **Location**: Admin → HRM → Attendance → "Sync All (30 days)" button
-   **Action**: Syncs employees + last 30 days of attendance/time entries
-   **Route**: `POST /admin/hrm/attendance/sync-all`

#### Custom Sync Form

-   **Location**: Admin → HRM → Attendance → "Custom Sync" button
-   **Features**: Select specific date range to sync
-   **Route**: `/admin/hrm/attendance/sync`

#### Employee Timesheet Sync

-   **Location**: Admin → HRM → Attendance → Employee Timesheet → "Sync from Jibble" button
-   **Action**: Syncs selected month for that employee
-   **Redirects**: Back to employee timesheet (not attendance index)

## Database Schema

### hrm_employees

Stores employee data synced from Jibble.

**Key Fields:**

-   `jibble_person_id` - Unique identifier from Jibble (links to Jibble API)
-   `full_name`, `email`, `phone` - Personal information
-   `company_id`, `department_id` - Organization structure
-   `code` - Employee code/ID
-   `status` - active/inactive

### hrm_attendance_days

Stores daily attendance summaries.

**Key Fields:**

-   `employee_id` - Links to hrm_employees
-   `date` - Date of attendance
-   `tracked_hours` - Total time tracked
-   `payroll_hours` - Billable/payroll time
-   `overtime_hours` - Overtime hours
-   `source` - Always 'jibble'
-   `jibble_data` - Raw JSON from Jibble API

**Unique Constraint:** `(employee_id, date)` prevents duplicates

### hrm_time_entries

Stores individual clock in/out records.

**Key Fields:**

-   `employee_id` - Links to hrm_employees
-   `attendance_day_id` - Links to hrm_attendance_days (nullable)
-   `jibble_entry_id` - Unique identifier from Jibble
-   `type` - 'In' or 'Out'
-   `time` - UTC timestamp
-   `local_time` - Local timezone timestamp
-   `project_id`, `activity_id` - Work classification
-   `note`, `address`, `coordinates` - Additional context

## Configuration

### Environment Variables

Required in `.env`:

```env
JIBBLE_CLIENT_ID=your-client-id
JIBBLE_CLIENT_SECRET=your-client-secret
JIBBLE_BASE_URL=https://workspace.prod.jibble.io/v1
```

### Config File

`config/services.php`:

```php
'jibble' => [
    'client_id' => env('JIBBLE_CLIENT_ID'),
    'client_secret' => env('JIBBLE_CLIENT_SECRET'),
    'base_url' => env('JIBBLE_BASE_URL', 'https://workspace.prod.jibble.io/v1'),
    'default_company_id' => env('JIBBLE_DEFAULT_COMPANY_ID', null),
],
```

## Troubleshooting

### Issue: "Call to undefined method syncEmployees()"

**Cause**: Old version of `JibblePeopleService` with `syncPeople()` method instead of `syncEmployees()`

**Solution**: Update service to use `syncEmployees()` method name

### Issue: "Integrity constraint violation: 19 UNIQUE constraint failed"

**Cause**: Trying to insert duplicate attendance records for same employee and date

**Solution**:

-   Ensure `updateOrCreate()` is used instead of `create()`
-   Cast employee_id to int: `'employee_id' => (int) $employee->id`
-   Round hours to 2 decimals: `round($hours, 2)`

### Issue: "Employee not found for Jibble person"

**Cause**: Employee exists in Jibble but not in local database

**Solution**: Run `php artisan jibble:sync --employees` first to sync all employees

### Issue: No attendance records synced

**Possible Causes:**

1. No time tracked in Jibble for the date range
2. Employees not linked (missing jibble_person_id)
3. API authentication failure

**Debugging:**

```bash
# Check logs
tail -f storage/logs/laravel.log

# Test sync with verbose output
php artisan jibble:sync --all --days=7
```

### Issue: Scheduled task not running

**Check:**

1. Cron entry exists: `crontab -l`
2. Schedule is registered: `php artisan schedule:list`
3. Timezone matches: `config('app.timezone')`

**Manual test:**

```bash
php artisan schedule:run
```

## Data Flow

```
Jibble API → Services → Database
     ↓           ↓          ↓
  People    syncEmployees  hrm_employees
  Timesheets syncDailySummary hrm_attendance_days
  TimeEntries syncTimeEntries hrm_time_entries
```

## Best Practices

1. **Always sync employees first** when setting up for the first time
2. **Start with small date ranges** (7 days) when testing
3. **Monitor logs** during initial sync to catch configuration issues
4. **Use --all option** for regular syncs to keep everything updated
5. **Verify scheduled tasks** are running using `php artisan schedule:list`
6. **Check duplicate constraints** before running migrations in production

## API Rate Limits

Jibble API has rate limits. The sync system handles this by:

-   Using pagination (100 records per request)
-   Adding small delays between requests
-   Catching and logging API errors
-   Continuing sync even if individual records fail

## Security

-   API credentials stored in `.env` (never commit to git)
-   Access tokens cached and auto-refreshed
-   Admin-only routes for sync operations
-   CSRF protection on all POST routes

## Support

For issues or questions:

1. Check logs: `storage/logs/laravel.log`
2. Review Jibble API docs: `public/docs.txt`
3. Test authentication: `php artisan jibble:sync --employees`
4. Verify database schema: `php artisan migrate:status`
