# Jibble Active Users Feature - Implementation Guide

## Overview

This feature allows you to see which employees are currently logged in (clocked in) on Jibble in real-time.

## API Endpoint Used

```
GET https://time-tracking.prod.jibble.io/v1/People?$filter=isActive eq true&$select=id,fullName,email,isActive
```

## Features Implemented

### 1. **Active Users Service** (`JibbleActiveUsersService`)

Located in: `app/Services/JibbleActiveUsersService.php`

**Methods:**

-   `fetchActivePeople()` - Fetches currently active people from Jibble API
-   `getActiveEmployees()` - Maps Jibble active people to local employee records
-   `getCachedActiveEmployees()` - Returns cached active employees (2-minute cache)
-   `clearCache()` - Clears the active employees cache

**Caching:**

-   Active users data is cached for 2 minutes to reduce API calls
-   Cache is automatically refreshed when expired
-   Can be manually refreshed by reloading the page

### 2. **Controller Methods**

Located in: `app/Http/Controllers/Admin/HrmAttendanceController.php`

**Routes:**

-   `GET /admin/hrm/attendance/active-users` - View page showing active employees
-   `GET /admin/hrm/attendance/active-users/json` - JSON API endpoint for AJAX calls

**Methods:**

-   `activeUsers()` - Displays the active users page
-   `activeUsersJson()` - Returns active users as JSON

### 3. **User Interface**

Located in: `resources/views/admin/hrm/attendance/active-users.blade.php`

**Features:**

-   Shows list of currently active (logged in) employees
-   Displays employee name, email, department/group
-   Visual status indicator (green pulsing dot)
-   Link to view employee timesheet
-   Refresh button to update data
-   Empty state when no one is logged in

**Access:**

-   From Attendance Index page, click "Who's Logged In" button

## Usage

### Viewing Active Employees

1. Go to **Attendance** > **Attendance Records**
2. Click the **"Who's Logged In"** button (lime green)
3. See list of currently active employees
4. Click **"View Timesheet"** to see individual employee details

### JSON API Usage

For AJAX or programmatic access:

```javascript
fetch("/admin/hrm/attendance/active-users/json")
    .then((response) => response.json())
    .then((data) => {
        console.log("Active employees:", data.data);
        console.log("Count:", data.count);
    });
```

Response format:

```json
{
    "success": true,
    "data": [
        {
            "jibble_person_id": "985305b3-f5f1-4a54-9268-9d052252c7d7",
            "full_name": "Sagar Chhetri",
            "email": "sagar@example.com",
            "group": "Saubhagya Group",
            "is_active": true,
            "employee_id": 16,
            "employee_code": "EMP001",
            "employee": { ... }
        }
    ],
    "count": 1
}
```

## Technical Details

### Jibble API Query Parameters

-   `$filter=isActive eq true` - Only fetch active (clocked in) users
-   `$select=id,fullName,email,isActive` - Select specific fields
-   `$expand=group($select=id,name)` - Include group/department info

### Performance

-   Uses 60-second HTTP timeout for API requests
-   Implements 2-minute cache to reduce API load
-   Fetches only necessary fields to minimize data transfer

### Error Handling

-   Graceful fallback if API fails
-   Error messages displayed to user
-   Detailed error logging for debugging

## Troubleshooting

### No Active Users Showing

**Possible causes:**

1. No employees are currently clocked in on Jibble
2. Jibble API connection issue
3. Cache needs to be refreshed

**Solutions:**

-   Click "Refresh" button to reload data
-   Check if employees are actually clocked in on Jibble
-   Check logs: `storage/logs/laravel.log`

### Employee Shows as "Not Linked"

**Cause:** Employee exists in Jibble but not in local database or `jibble_person_id` not set

**Solution:**

-   Run employee sync: Click "Sync All" on Attendance page
-   This will sync all employees from Jibble and link them

## Related Documentation

-   See `/docs/JIBBLE_SYNC_GUIDE.md` for general Jibble integration
-   See `/docs/JIBBLE_QUICK_REFERENCE.md` for API details
-   See `/docs/HRM_IMPLEMENTATION_COMPLETE.md` for HRM module overview
