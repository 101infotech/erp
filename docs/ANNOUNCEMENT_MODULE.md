# Announcement Module - Quick Start Guide

## Overview

The Announcement Module allows administrators to create and send company-wide or targeted announcements to staff members. Announcements are displayed on the employee dashboard and can optionally trigger email notifications.

## Features

-   ✅ Admin can create, edit, and delete announcements
-   ✅ Send to all staff or specific employees
-   ✅ Priority levels (Low, Normal, High)
-   ✅ Optional email notifications
-   ✅ Publish/Draft status
-   ✅ Display recent announcements on employee dashboard
-   ✅ Full announcement viewing for employees
-   ✅ Beautiful, responsive UI with dark mode

## Quick Access

### Admin Panel

-   **Announcements List**: `/admin/announcements`
-   **Create Announcement**: `/admin/announcements/create`

### Employee Portal

-   **Announcements List**: `/employee/announcements`
-   **Dashboard**: Recent announcements shown on `/employee/dashboard`

## How to Use

### For Administrators

#### Creating an Announcement

1. Navigate to **Admin Panel → Announcements**
2. Click **"New Announcement"** button
3. Fill in the details:
    - **Title**: Brief, descriptive title
    - **Content**: Full announcement message
    - **Priority**: Low, Normal, or High
    - **Recipients**: All Staff or Specific Employees
    - **Send Email**: Check to send email notifications
    - **Publish**: Check to publish immediately (or save as draft)
4. Click **"Create Announcement"**

#### Managing Announcements

-   **View**: Click "View" to see full details
-   **Edit**: Click "Edit" to modify announcement
-   **Delete**: Click "Delete" to remove announcement

### For Employees

#### Viewing Announcements

1. **Dashboard**: See 3 most recent announcements
2. **Full List**: Navigate to **Announcements** in sidebar
3. **Read More**: Click on any announcement to view full details

## Priority Levels

| Priority   | Use Case                          | Color |
| ---------- | --------------------------------- | ----- |
| **Low**    | General information, FYI messages | Blue  |
| **Normal** | Standard company updates          | Green |
| **High**   | Urgent or critical information    | Red   |

## Email Notifications

When "Send Email" is enabled:

-   All recipients receive email notification
-   Email includes announcement title, content, and link
-   Email subject includes priority level
-   Beautiful HTML email template

## Database Schema

```sql
announcements
├── id (primary key)
├── title (string)
├── content (text)
├── priority (enum: low, normal, high)
├── recipient_type (enum: all, specific)
├── recipient_ids (json, nullable)
├── created_by (foreign key → users)
├── send_email (boolean)
├── is_published (boolean)
├── published_at (timestamp)
├── created_at
└── updated_at
```

## API Endpoints

### Admin Routes

```php
GET    /admin/announcements           // List all announcements
GET    /admin/announcements/create    // Show create form
POST   /admin/announcements           // Store new announcement
GET    /admin/announcements/{id}      // Show announcement details
GET    /admin/announcements/{id}/edit // Show edit form
PUT    /admin/announcements/{id}      // Update announcement
DELETE /admin/announcements/{id}      // Delete announcement
```

### Employee Routes

```php
GET /employee/announcements      // List user's announcements
GET /employee/announcements/{id} // View announcement details
```

## Code Examples

### Creating an Announcement Programmatically

```php
use App\Models\Announcement;

$announcement = Announcement::create([
    'title' => 'Company Holiday Announcement',
    'content' => 'Office will be closed on December 25th for Christmas.',
    'priority' => 'normal',
    'recipient_type' => 'all',
    'created_by' => auth()->id(),
    'send_email' => true,
    'is_published' => true,
    'published_at' => now(),
]);
```

### Fetching User-Specific Announcements

```php
$announcements = Announcement::published()
    ->forUser(auth()->id())
    ->recent(5)
    ->get();
```

## Files Created

### Database

-   `database/migrations/2025_12_10_091330_create_announcements_table.php`

### Models

-   `app/Models/Announcement.php`

### Controllers

-   `app/Http/Controllers/Admin/AnnouncementController.php`
-   `app/Http/Controllers/Employee/AnnouncementController.php`

### Mail

-   `app/Mail/AnnouncementMail.php`

### Views - Admin

-   `resources/views/admin/announcements/index.blade.php`
-   `resources/views/admin/announcements/create.blade.php`
-   `resources/views/admin/announcements/edit.blade.php`
-   `resources/views/admin/announcements/show.blade.php`

### Views - Employee

-   `resources/views/employee/announcements/index.blade.php`
-   `resources/views/employee/announcements/show.blade.php`

### Email Template

-   `resources/views/emails/announcement.blade.php`

### Routes

-   Updated `routes/web.php` with announcement routes

### Navigation

-   Updated `resources/views/admin/layouts/app.blade.php`
-   Updated `resources/views/employee/partials/sidebar.blade.php`
-   Updated `resources/views/employee/dashboard.blade.php`

## Customization

### Changing Priority Colors

Edit the `$priorityColors` array in views:

```php
$priorityColors = [
    'low' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    'normal' => 'bg-green-500/20 text-green-400 border-green-500/30',
    'high' => 'bg-red-500/20 text-red-400 border-red-500/30',
];
```

### Modifying Email Template

Edit `resources/views/emails/announcement.blade.php` to customize email design.

### Adding More Recipient Options

Update the `recipient_type` enum in the migration and add corresponding logic in the controller.

## Security Features

-   ✅ User authentication required
-   ✅ Admin-only access for management
-   ✅ Employee access control (can only view announcements for them)
-   ✅ CSRF protection on all forms
-   ✅ Authorization checks

## Best Practices

1. **Use Appropriate Priority**

    - Reserve "High" for truly urgent matters
    - Use "Normal" for most announcements
    - "Low" for optional information

2. **Keep Content Concise**

    - Write clear, scannable content
    - Use paragraphs for readability
    - Include actionable information

3. **Target Appropriately**

    - Use "All Staff" for general announcements
    - Target specific users for relevant updates

4. **Enable Email Selectively**
    - Enable email for important announcements
    - Avoid email fatigue by not emailing everything

## Troubleshooting

### Emails Not Sending

-   Check mail configuration in `.env`
-   Verify queue is running if using queues
-   Check logs in `storage/logs/laravel.log`

### Announcements Not Showing for Employee

-   Verify user has `role = 'user'`
-   Check `recipient_type` and `recipient_ids`
-   Ensure announcement is published

### Permission Denied

-   Ensure user has proper role (admin/user)
-   Check middleware configuration in routes

## Future Enhancements

-   [ ] Rich text editor for content
-   [ ] File attachments
-   [ ] Read/unread status tracking
-   [ ] Announcement categories
-   [ ] Scheduled publishing
-   [ ] Analytics and read receipts

---

**Implementation Date**: December 10, 2025  
**Status**: ✅ Complete and Ready for Use
