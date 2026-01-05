# Notification System - Complete Guide

## Overview

The notification system provides real-time in-app notifications for both admin and employee users. Notifications are created automatically for key events like leave requests, approvals/rejections, complaint submissions, and status updates.

## Features

✅ Real-time in-app notifications  
✅ Email + in-app notification combination  
✅ Notification bell with unread count badge  
✅ Auto-polling every 30 seconds  
✅ Mark as read functionality  
✅ Mark all as read  
✅ Direct links to related resources  
✅ Different notification types with icons  
✅ Works for both admin and employee portals

## Database Structure

### Notifications Table

```sql
- id (primary key)
- user_id (foreign key to users table)
- type (leave_request, leave_approved, leave_rejected, complaint_submitted, complaint_status_updated)
- title (notification heading)
- message (detailed message)
- link (URL to related resource)
- is_read (boolean, default false)
- read_at (timestamp, nullable)
- data (JSON, additional metadata)
- created_at, updated_at
```

## Notification Types

### 1. Leave Request (leave_request)

**Triggered:** When employee submits leave request  
**Sent To:** All admins  
**Email:** ✅ Employee receives confirmation  
**In-App:** ✅ Admins receive notification

### 2. Leave Approved (leave_approved)

**Triggered:** When admin approves leave request  
**Sent To:** Employee who requested leave  
**Email:** ✅  
**In-App:** ✅

### 3. Leave Rejected (leave_rejected)

**Triggered:** When admin rejects leave request  
**Sent To:** Employee who requested leave  
**Email:** ✅  
**In-App:** ✅

### 4. Complaint Submitted (complaint_submitted)

**Triggered:** When employee submits complaint  
**Sent To:** All admins  
**Email:** ✅ Employee receives confirmation  
**In-App:** ✅ Admins receive notification

### 5. Complaint Status Updated (complaint_status_updated)

**Triggered:** When admin updates complaint status  
**Sent To:** Employee who submitted complaint  
**Email:** ✅  
**In-App:** ✅

## Architecture

### 1. Model: `app/Models/Notification.php`

-   Eloquent model for notifications
-   Relationships: belongs to User
-   Scopes: `unread()`, `forUser($userId)`
-   Methods: `markAsRead()`

### 2. Service: `app/Services/NotificationService.php`

Central service for all notification operations:

-   `createNotification()` - Create single notification
-   `notifyAdmins()` - Create notifications for all admins
-   `getUnreadCount()` - Get unread count for user
-   `getRecentNotifications()` - Get recent notifications
-   `markAsRead()` - Mark single notification as read
-   `markAllAsRead()` - Mark all notifications as read
-   `deleteNotification()` - Delete notification

### 3. Controller: `app/Http/Controllers/NotificationController.php`

API endpoints for notification operations:

-   `GET /notifications` - Get all notifications for current user
-   `GET /notifications/unread-count` - Get unread count
-   `POST /notifications/{id}/mark-as-read` - Mark as read
-   `POST /notifications/mark-all-as-read` - Mark all as read
-   `DELETE /notifications/{id}` - Delete notification

### 4. UI Component: `resources/views/components/notification-bell.blade.php`

Alpine.js-powered notification bell with:

-   Bell icon with unread count badge
-   Dropdown showing recent notifications
-   Auto-refresh every 30 seconds
-   Click to open/close
-   Click outside to close
-   Different icons for different notification types

## Integration Points

### Controllers Updated

1. **Employee/LeaveController.php** - Notifies admins on leave request
2. **Admin/HrmLeaveController.php** - Notifies employee on approve/reject
3. **Employee/ComplaintController.php** - Notifies admins on complaint submission
4. **Admin/ComplaintController.php** - Notifies employee on status update

### Layouts Updated

1. **resources/views/layouts/navigation.blade.php** - Admin notification bell
2. **resources/views/employee/partials/nav.blade.php** - Employee notification bell

## Usage Examples

### Creating a Notification Programmatically

```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);

// Single user notification
$notificationService->createNotification(
    $userId,
    'leave_approved',
    'Leave Request Approved',
    'Your annual leave request has been approved.',
    route('employee.leave.show', $leaveId),
    ['leave_type' => 'annual', 'total_days' => 5]
);

// Notify all admins
$notificationService->notifyAdmins(
    'leave_request',
    'New Leave Request',
    'John Doe has submitted a leave request.',
    route('admin.hrm.leaves.show', $leaveId)
);
```

### Frontend JavaScript

The notification bell uses Alpine.js for reactivity:

```javascript
// Data properties
isOpen: false,
notifications: [],
unreadCount: 0,
pollInterval: null

// Methods
toggleDropdown() - Open/close dropdown
loadNotifications() - Fetch notifications
loadUnreadCount() - Fetch unread count only
markAsRead(id) - Mark notification as read
markAllAsRead() - Mark all as read
startPolling() - Auto-refresh every 30s
formatDate(dateString) - Format relative time
```

## API Responses

### GET /notifications

```json
{
    "notifications": [
        {
            "id": 1,
            "user_id": 5,
            "type": "leave_approved",
            "title": "Leave Request Approved",
            "message": "Your annual leave request has been approved.",
            "link": "/employee/leave/12",
            "is_read": false,
            "read_at": null,
            "data": {
                "leave_type": "annual",
                "total_days": 5
            },
            "created_at": "2025-12-10T10:30:00.000000Z",
            "updated_at": "2025-12-10T10:30:00.000000Z"
        }
    ],
    "unread_count": 3
}
```

### GET /notifications/unread-count

```json
{
    "count": 3
}
```

## Testing

### Manual Testing Steps

1. **Test Leave Request Notification:**

    - Login as employee
    - Submit a leave request
    - Login as admin
    - Check notification bell (should show 1 unread)
    - Click notification to view leave request
    - Notification should mark as read

2. **Test Leave Approval Notification:**

    - Login as admin
    - Approve a leave request
    - Login as that employee
    - Check notification bell (should show 1 unread)
    - Click notification to view approved leave

3. **Test Complaint Notification:**

    - Login as employee
    - Submit a complaint
    - Login as admin
    - Check notification bell for new complaint notification

4. **Test Auto-Polling:**
    - Open notification dropdown
    - Wait 30 seconds
    - Verify unread count updates automatically

## Customization

### Adding New Notification Types

1. Update controllers to call `NotificationService`
2. Define new type string (e.g., 'payroll_generated')
3. Add icon/color in notification-bell.blade.php

### Changing Polling Interval

Edit `notification-bell.blade.php`:

```javascript
// Change from 30000 (30s) to desired milliseconds
this.pollInterval = setInterval(() => {
    if (!this.isOpen) {
        this.loadUnreadCount();
    }
}, 60000); // 60 seconds
```

### Customizing Notification Appearance

Edit `notification-bell.blade.php`:

-   Update SVG icons
-   Change colors (bg-blue-500, text-yellow-400, etc.)
-   Modify dropdown width (w-96)
-   Change max height (max-h-96)

## Security Considerations

✅ CSRF protection on all POST/DELETE requests  
✅ User can only see their own notifications (forUser scope)  
✅ Authorization in NotificationController  
✅ SQL injection prevention via Eloquent ORM

## Performance Optimization

-   Polling uses lightweight `/unread-count` endpoint when dropdown closed
-   Full notifications loaded only when dropdown opened
-   Database indexes on `user_id`, `is_read`, `created_at`
-   Limited to 50 recent notifications by default

## Troubleshooting

### Notifications not appearing

1. Check database: `SELECT * FROM notifications WHERE user_id = X;`
2. Verify routes: `php artisan route:list | grep notification`
3. Clear cache: `php artisan optimize:clear`
4. Check browser console for JavaScript errors

### Unread count not updating

1. Verify polling is running (check Network tab in DevTools)
2. Check CSRF token is present in page meta tags
3. Ensure user is authenticated

### Notification bell not showing

1. Verify component is included in layout
2. Check Alpine.js is loaded
3. Clear view cache: `php artisan view:clear`

## Future Enhancements

-   [ ] Push notifications using WebSockets
-   [ ] Email digest (daily summary)
-   [ ] Notification preferences per user
-   [ ] Notification categories/filters
-   [ ] Sound/desktop notifications
-   [ ] Notification history page

## Files Reference

```
app/
├── Models/Notification.php
├── Services/NotificationService.php
├── Http/Controllers/NotificationController.php
├── Http/Controllers/Employee/
│   ├── LeaveController.php (updated)
│   └── ComplaintController.php (updated)
└── Http/Controllers/Admin/
    ├── HrmLeaveController.php (updated)
    └── ComplaintController.php (updated)

database/migrations/
└── 2025_12_10_103134_create_notifications_table.php

resources/views/
├── components/notification-bell.blade.php
├── layouts/navigation.blade.php (updated)
└── employee/partials/nav.blade.php (updated)

routes/
└── web.php (updated with notification routes)
```

## Support

For issues or questions, refer to:

-   Laravel Documentation: https://laravel.com/docs
-   Alpine.js Documentation: https://alpinejs.dev/
-   Project Documentation: /docs folder
