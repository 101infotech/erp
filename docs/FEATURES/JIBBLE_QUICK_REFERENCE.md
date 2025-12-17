# Jibble Integration - Quick Reference Guide

**Last Updated:** December 2, 2024  
**Status:** Production Ready ✅

---

## 🚀 Quick Start

### 1. Sync Employees from Jibble

```bash
php artisan hrm:sync-jibble-employees
```

### 2. Sync Attendance Data

```bash
# Sync yesterday's attendance
php artisan hrm:sync-jibble-attendance

# Sync specific date range
php artisan hrm:sync-jibble-attendance --start=2024-12-01 --end=2024-12-02

# Sync last 7 days
php artisan hrm:sync-jibble-attendance --start=$(date -v-7d +%Y-%m-%d) --end=$(date +%Y-%m-%d)
```

### 3. Access Admin Interface

-   Employees: `http://localhost/admin/hrm/employees`
-   Attendance: `http://localhost/admin/hrm/attendance`
-   Companies: `http://localhost/admin/hrm/companies`
-   Departments: `http://localhost/admin/hrm/departments`

---

## 📋 Common Commands

### Database

```bash
# Check migration status
php artisan migrate:status | grep hrm

# View table counts
php artisan db:show --counts | grep hrm

# Fresh migration (⚠️ WARNING: Deletes all data)
php artisan migrate:fresh
```

### Testing

```bash
# Test authentication
php artisan tinker --execute="\$auth = app(\App\Services\JibbleAuthService::class); echo \$auth->getToken();"

# Test people fetch
php artisan tinker --execute="\$service = app(\App\Services\JibblePeopleService::class); var_dump(\$service->fetchAll(['id', 'fullName'], ['\$top' => 5]));"

# Check routes
php artisan route:list | grep hrm
```

### Debugging

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear

# Run in verbose mode
php artisan hrm:sync-jibble-employees -vvv
```

---

## 🔌 API Endpoints

### Employee API

```bash
# List all employees
curl http://localhost/api/v1/hrm/employees

# Filter by company
curl http://localhost/api/v1/hrm/employees?company_id=1

# Get specific employee
curl http://localhost/api/v1/hrm/employees/1
```

### Attendance API

```bash
# Get attendance for date range
curl "http://localhost/api/v1/hrm/attendance?start_date=2024-12-01&end_date=2024-12-02"

# Filter by employee
curl "http://localhost/api/v1/hrm/attendance?employee_id=5&start_date=2024-12-01&end_date=2024-12-02"
```

---

## 💻 Code Examples

### Fetch Employees in Code

```php
use App\Services\JibblePeopleService;

$service = app(JibblePeopleService::class);

// Fetch all people
$people = $service->fetchAll();

// Fetch with custom fields
$people = $service->fetchAll(['id', 'fullName', 'email']);

// Sync to database
$synced = $service->syncPeople();
echo "Synced {$synced} employees";
```

### Fetch Attendance in Code

```php
use App\Services\JibbleTimesheetService;
use Carbon\Carbon;

$service = app(JibbleTimesheetService::class);

// Sync daily summary
$synced = $service->syncDailySummary(
    Carbon::parse('2024-12-01'),
    Carbon::parse('2024-12-02')
);

// Sync for specific employee
$attendance = $service->syncAndFetchEmployeeAttendance(
    $employeeId,
    '2024-12-01',
    '2024-12-02'
);
```

### Query Database

```php
use App\Models\HrmEmployee;
use App\Models\HrmAttendanceDay;

// Get active employees
$employees = HrmEmployee::active()->get();

// Get employees by company
$employees = HrmEmployee::byCompany(1)->get();

// Get attendance for date range
$attendance = HrmAttendanceDay::dateRange('2024-12-01', '2024-12-02')
    ->forEmployee($employeeId)
    ->get();

// Get employee with attendance
$employee = HrmEmployee::with('attendanceDays')->find($id);
```

---

## ⚙️ Configuration

### Environment Variables (.env)

```env
JIBBLE_CLIENT_ID=your_client_id
JIBBLE_CLIENT_SECRET=your_client_secret
JIBBLE_WORKSPACE_ID=your_workspace_id
JIBBLE_BASE_URL=https://workspace.prod.jibble.io/v1
JIBBLE_PEOPLE_SELECT=id,fullName,email,department
JIBBLE_DEFAULT_COMPANY_ID=1
```

### Service Configuration (config/services.php)

```php
'jibble' => [
    'client_id' => env('JIBBLE_CLIENT_ID'),
    'client_secret' => env('JIBBLE_CLIENT_SECRET'),
    'workspace_id' => env('JIBBLE_WORKSPACE_ID'),
    'base_url' => env('JIBBLE_BASE_URL', 'https://workspace.prod.jibble.io/v1'),
    'people_select' => env('JIBBLE_PEOPLE_SELECT', 'id,fullName,email,department'),
    'default_company_id' => env('JIBBLE_DEFAULT_COMPANY_ID'),
],
```

---

## 📅 Scheduled Tasks

### Add to app/Console/Kernel.php

```php
protected function schedule(Schedule $schedule)
{
    // Sync employees daily at 2 AM
    $schedule->command('hrm:sync-jibble-employees')
        ->dailyAt('02:00')
        ->withoutOverlapping()
        ->onFailure(function () {
            // Send notification
        });

    // Sync yesterday's attendance daily at 3 AM
    $schedule->command('hrm:sync-jibble-attendance --start=yesterday --end=yesterday')
        ->dailyAt('03:00')
        ->withoutOverlapping()
        ->onFailure(function () {
            // Send notification
        });
}
```

### Set up Cron Job

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🐛 Troubleshooting

### Issue: "Unable to authenticate with Jibble"

**Solution:**

1. Verify credentials in `.env`
2. Run `php artisan config:clear`
3. Test token: `php artisan tinker --execute="\$auth = app(\App\Services\JibbleAuthService::class); echo \$auth->getToken();"`

### Issue: "No employees synced"

**Solution:**

1. Check Jibble workspace has employees
2. Verify `JIBBLE_WORKSPACE_ID` is correct
3. Check employees have role 'Member' in Jibble
4. Run with verbose: `php artisan hrm:sync-jibble-employees -vvv`

### Issue: "Duplicate attendance records"

**Solution:**

-   Database prevents duplicates via unique constraint
-   Check `storage/logs/laravel.log` for details
-   Verify `jibble_person_id` mapping is correct

### Issue: "Overtime hours not calculating"

**Solution:**

1. Check Jibble timesheet has overtime data
2. Verify ISO 8601 duration parsing
3. Test: `php artisan tinker --execute="\$service = app(\App\Services\JibbleTimesheetService::class); echo \$service->parseDuration('PT8H30M');"`

---

## 📊 Database Schema Reference

### hrm_companies

```sql
id, name, address, timestamps
```

### hrm_departments

```sql
id, company_id, name, timestamps
```

### hrm_employees

```sql
id, user_id, company_id, department_id, jibble_person_id,
full_name, email, phone, code, position, join_date, birth_date,
address, emergency_contact, emergency_phone, avatar, status, timestamps
```

### hrm_attendance_days

```sql
id, employee_id, date, tracked_hours, payroll_hours, overtime_hours,
source, jibble_data (JSON), notes, timestamps
```

---

## 🔗 Important URLs

### Jibble API Documentation

-   **Identity Server:** https://identity.prod.jibble.io
-   **Workspace API:** https://workspace.prod.jibble.io/v1
-   **Time Attendance API:** https://time-attendance.prod.jibble.io/v1
-   **Docs:** https://docs.jibble.io/

### Admin Panel

-   **Employees:** `/admin/hrm/employees`
-   **Attendance:** `/admin/hrm/attendance`
-   **Companies:** `/admin/hrm/companies`
-   **Departments:** `/admin/hrm/departments`

### API Endpoints

-   **Employees:** `/api/v1/hrm/employees`
-   **Attendance:** `/api/v1/hrm/attendance`

---

## 📚 Documentation Files

| File                                   | Description                   |
| -------------------------------------- | ----------------------------- |
| `docs/HRM_MODULE.md`                   | Complete module documentation |
| `docs/HRM_IMPLEMENTATION_COMPLETE.md`  | Implementation summary        |
| `docs/JIBBLE_IMPLEMENTATION_STATUS.md` | Current status report         |
| `docs/JIBBLE_LIVE_TEST_RESULTS.md`     | Test results                  |
| `docs/JIBBLE_QUICK_REFERENCE.md`       | This file                     |

---

## ✅ Pre-Deployment Checklist

-   [ ] Environment variables configured
-   [ ] Migrations run successfully
-   [ ] Initial employee sync completed
-   [ ] Initial attendance sync completed
-   [ ] Admin routes accessible
-   [ ] API endpoints working
-   [ ] Laravel scheduler configured
-   [ ] Cron job set up
-   [ ] Error logging working
-   [ ] Backup strategy in place

---

## 🎯 Performance Tips

1. **Cache Token:** Token is automatically cached for 60 minutes
2. **Batch Sync:** Use date ranges for attendance sync
3. **Index Database:** Foreign keys and dates are indexed
4. **Pagination:** API calls use pagination (100 records/page)
5. **Queue Jobs:** Consider queuing large syncs (future)

---

## 🔐 Security Best Practices

1. Never commit `.env` file
2. Use HTTPS for all API calls
3. Rotate credentials periodically
4. Monitor API usage and logs
5. Implement rate limiting on API endpoints
6. Use RBAC for admin access (future)

---

**Quick Reference Version:** 1.0  
**Last Updated:** December 2, 2024  
**Status:** Production Ready ✅
