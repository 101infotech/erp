# Admin Panel Access Guide

## Overview

The ERP system has two interfaces:

1. **User Dashboard** (`/dashboard`) - For regular users
2. **Admin Panel** (`/admin/dashboard`) - For administrators with full access to all modules

## Accessing the Admin Panel

### Method 1: Use the Existing Admin Account

The system already has an admin user:

-   **Email:** `admin@saubhagyagroup.com`
-   **Role:** admin

If you don't know the password, reset it using:

```bash
php artisan tinker
```

Then in tinker:

```php
$user = User::where('email', 'admin@saubhagyagroup.com')->first();
$user->password = bcrypt('your-new-password');
$user->save();
```

### Method 2: Make Your Current Account Admin

If you're logged in with a different email and want admin access:

```bash
php artisan user:make-admin your-email@example.com
```

Or interactively (shows all users):

```bash
php artisan user:make-admin
```

## Navigation

### Desktop

Once logged in as admin, you'll see **"Admin Panel"** link in the top navigation bar next to "Dashboard".

### Mobile

The Admin Panel link appears in the hamburger menu for admin users.

## Available Admin Modules

The admin panel provides access to:

### Site Management

-   **Sites** - Manage all website configurations
-   **Team Members** - Manage team member profiles
-   **News & Media** - Manage news articles and media
-   **Careers** - Manage job postings
-   **Case Studies** - Manage project case studies
-   **Blogs** - Manage blog posts

### HRM Module

-   **Employees** - Manage employee records, sync with Jibble
-   **Attendance** - View and sync attendance from Jibble
-   **Departments** - Manage department structure
-   **Companies** - Manage company entities

### Submissions

-   **Contact Forms** - View contact form submissions
-   **Booking Forms** - View booking form submissions

## Current Users

| ID   | Name    | Email                    | Role  |
| ---- | ------- | ------------------------ | ----- |
| 1    | Admin   | admin@saubhagyagroup.com | admin |
| 2-14 | Various | Various emails           | user  |

## Quick Access URLs

-   User Dashboard: `/dashboard`
-   Admin Dashboard: `/admin/dashboard`
-   HRM Employees: `/admin/hrm/employees`
-   HRM Attendance: `/admin/hrm/attendance`
-   Sites Management: `/admin/sites`

## Troubleshooting

**Problem:** Can't see Admin Panel link
**Solution:** Make sure you're logged in with an admin account (role = 'admin')

**Problem:** 403 Unauthorized error
**Solution:** Your account doesn't have admin role. Use `php artisan user:make-admin your-email@example.com`

**Problem:** Forgot admin password
**Solution:** Reset it using tinker (see Method 1 above)
