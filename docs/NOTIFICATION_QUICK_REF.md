# Notification System - Quick Reference

## ✅ What's Implemented

### 1. Database

-   **Table:** `notifications`
-   **Migration:** `2025_12_10_103134_create_notifications_table.php`
-   **Fields:** user_id, type, title, message, link, is_read, read_at, data

### 2. Backend

-   **Model:** `app/Models/Notification.php`
-   **Service:** `app/Services/NotificationService.php`
-   **Controller:** `app/Http/Controllers/NotificationController.php`

### 3. Frontend

-   **Component:** `resources/views/components/notification-bell.blade.php`
-   **Admin Nav:** `resources/views/layouts/navigation.blade.php`
-   **Employee Nav:** `resources/views/employee/partials/nav.blade.php`

### 4. Routes (all under /notifications)

```
GET    /notifications               - Get all notifications
GET    /notifications/unread-count  - Get unread count
POST   /notifications/{id}/mark-as-read - Mark as read
POST   /notifications/mark-all-as-read  - Mark all as read
DELETE /notifications/{id}          - Delete notification
```

## 🔔 Notification Types

| Type                       | Triggered When                 | Recipient  | Email    | In-App |
| -------------------------- | ------------------------------ | ---------- | -------- | ------ |
| `leave_request`            | Employee submits leave         | All admins | Employee | Admins |
| `leave_approved`           | Admin approves leave           | Employee   | ✅       | ✅     |
| `leave_rejected`           | Admin rejects leave            | Employee   | ✅       | ✅     |
| `complaint_submitted`      | Employee submits complaint     | All admins | Employee | Admins |
| `complaint_status_updated` | Admin updates complaint status | Employee   | ✅       | ✅     |

## 📝 Code Examples

### Create Notification for Single User

```php
use App\Services\NotificationService;

$service = app(NotificationService::class);
$service->createNotification(
    $userId,
    'leave_approved',
    'Leave Request Approved',
    'Your annual leave has been approved.',
    route('employee.leave.show', $leaveId)
);
```

### Notify All Admins

```php
$service->notifyAdmins(
    'leave_request',
    'New Leave Request',
    'John Doe requested 3 days of annual leave.',
    route('admin.hrm.leaves.show', $leaveId)
);
```

### Get Unread Count

```php
$count = $service->getUnreadCount($userId);
```

### Mark as Read

```php
$service->markAsRead($notificationId);
```

### Mark All as Read

```php
$service->markAllAsRead($userId);
```

## 🎨 UI Features

-   **Bell Icon:** Shows in navigation bar
-   **Badge:** Red circle with unread count (shows 9+ if >9)
-   **Dropdown:** Opens on click, closes on click away
-   **Auto-Polling:** Updates count every 30 seconds
-   **Icons:** Different colors/icons for leave vs complaint
-   **Timestamps:** Relative time (Just now, 5m ago, 2h ago, etc.)
-   **Read Status:** Blue dot for unread notifications
-   **Links:** Clicking notification navigates to related page

## 🧪 Testing Steps

1. **Test Leave Request Notification:**

    ```
    Employee → Submit Leave → Admin Bell Shows 1 Unread
    Admin → Click Bell → See Leave Request
    Admin → Click Notification → Redirects to Leave Details
    ```

2. **Test Leave Approval:**

    ```
    Admin → Approve Leave → Employee Bell Shows 1 Unread
    Employee → Click Bell → See "Leave Approved"
    ```

3. **Test Auto-Polling:**

    ```
    Open App → Wait 30s → Unread Count Updates Automatically
    ```

4. **Test Mark All as Read:**
    ```
    Multiple Unread → Click "Mark all as read" → All Turn Grey
    ```

## 🔧 Customization

### Change Polling Interval

Edit `notification-bell.blade.php`:

```javascript
// Line ~160
setInterval(() => {...}, 30000); // Change 30000 to desired ms
```

### Add New Notification Type

1. Update controller to call `NotificationService`
2. Add new type string (e.g., 'payroll_generated')
3. Add icon in `notification-bell.blade.php`:

```html
<template x-if="notification.type.includes('payroll')">
    <div class="w-8 h-8 rounded-full bg-green-500/20...">
        <!-- Your icon SVG -->
    </div>
</template>
```

### Change Notification Limit

Edit `NotificationService.php`:

```php
public function getRecentNotifications(int $userId, int $limit = 10)
// Change default limit from 10 to your desired number
```

## 📱 Browser Compatibility

-   ✅ Chrome/Edge (Latest)
-   ✅ Firefox (Latest)
-   ✅ Safari (Latest)
-   ✅ Mobile Browsers

## 🚀 Performance

-   Lightweight polling (unread count only when dropdown closed)
-   Full notifications loaded on dropdown open
-   Database indexes on user_id, is_read, created_at
-   Auto-cleanup possible via scheduled task (future enhancement)

## 📄 Documentation

Full documentation: `docs/NOTIFICATION_SYSTEM.md`

## ✨ Key Benefits

1. **Dual Channel:** Email + in-app notifications
2. **Real-time:** Auto-updates every 30 seconds
3. **User-Friendly:** Clear visual indicators
4. **Organized:** Categorized by type with icons
5. **Accessible:** Works for both admin and employee
6. **Scalable:** Easy to add new notification types

## 🎯 Integration Summary

**4 Controllers Updated:**

-   Employee/LeaveController.php
-   Admin/HrmLeaveController.php
-   Employee/ComplaintController.php
-   Admin/ComplaintController.php

**2 Layouts Updated:**

-   layouts/navigation.blade.php (Admin)
-   employee/partials/nav.blade.php (Employee)

**All Emails Preserved:** Existing email notifications still work!

---

**Status:** ✅ Production Ready  
**Version:** 1.0  
**Last Updated:** December 10, 2025
