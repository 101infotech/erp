# Email Notification System - Complete Documentation

## 📧 Overview

The ERP system now includes a comprehensive email notification system that sends automated emails for:

-   Leave request submissions, approvals, and rejections
-   Complaint/Feedback submissions and status updates
-   Announcements to employees and staff

All emails are processed in the background using Laravel's queue system for optimal performance.

---

## 🎯 Features Implemented

### 1. Leave Management Emails

#### Leave Request Submitted

-   **Trigger**: When an employee submits a leave request
-   **Recipient**: The employee who submitted the request
-   **Content**: Leave details, status (pending), and next steps
-   **Email Class**: `App\Mail\LeaveRequestSubmitted`
-   **Template**: `resources/views/emails/leave-request-submitted.blade.php`

#### Leave Request Approved

-   **Trigger**: When admin/manager approves a leave request
-   **Recipient**: The employee whose leave was approved
-   **Content**: Approval confirmation, approver name, dates, and important notes
-   **Email Class**: `App\Mail\LeaveApproved`
-   **Template**: `resources/views/emails/leave-approved.blade.php`

#### Leave Request Rejected

-   **Trigger**: When admin/manager rejects a leave request
-   **Recipient**: The employee whose leave was rejected
-   **Content**: Rejection notification, reason, and next steps
-   **Email Class**: `App\Mail\LeaveRejected`
-   **Template**: `resources/views/emails/leave-rejected.blade.php`

### 2. Complaint/Feedback Emails

#### Feedback Submitted

-   **Trigger**: When an employee submits feedback through the complaint box
-   **Recipient**: The employee who submitted feedback
-   **Content**: Confirmation, reference ID, and what to expect next
-   **Email Class**: `App\Mail\ComplaintSubmitted`
-   **Template**: `resources/views/emails/complaint-submitted.blade.php`

#### Feedback Status Updated

-   **Trigger**: When admin updates the status of a complaint
-   **Recipient**: The employee who submitted the feedback
-   **Content**: New status, admin notes, and resolution details
-   **Email Class**: `App\Mail\ComplaintStatusUpdated`
-   **Template**: `resources/views/emails/complaint-status-updated.blade.php`

### 3. Announcement Emails

#### Announcement Notification

-   **Trigger**: When admin creates an announcement with "Send Email" enabled
-   **Recipient**: Selected users (all staff, specific users, or employees)
-   **Content**: Announcement title, content, priority, and link to view details
-   **Email Class**: `App\Mail\AnnouncementMail`
-   **Template**: `resources/views/emails/announcement.blade.php`

---

## ⚙️ Technical Implementation

### Queue Configuration

All emails implement `ShouldQueue` interface and are sent via background queue for performance:

```php
class LeaveRequestSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;      // Retry failed emails up to 3 times
    public $backoff = 60;   // Wait 60 seconds between retries
}
```

### Queue Setup

**Configuration**: `.env`

```env
QUEUE_CONNECTION=database
```

**Database Tables**: Already migrated

-   `jobs` - Pending queue jobs
-   `failed_jobs` - Failed jobs for debugging

### Email Configuration

**SMTP via Mailtrap** (Testing/Production)

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=6c20da601119174e4bb7269c2a3fb190
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@saubhagyagroup.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Controller Integration

#### Employee Leave Controller

```php
use App\Mail\LeaveRequestSubmitted;
use Illuminate\Support\Facades\Mail;

// In store() method
$leave = HrmLeaveRequest::create([...]);

if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
    Mail::to($user->email)->queue(new LeaveRequestSubmitted($leave));
}
```

#### Admin Leave Controller

```php
use App\Mail\LeaveApproved;
use App\Mail\LeaveRejected;

// In approve() method
$employee = $leave->employee;
if ($employee->user && $employee->user->email) {
    Mail::to($employee->user->email)->queue(new LeaveApproved($leave));
}

// In reject() method
if ($employee->user && $employee->user->email) {
    Mail::to($employee->user->email)->queue(new LeaveRejected($leave));
}
```

#### Employee Complaint Controller

```php
use App\Mail\ComplaintSubmitted;

// In store() method
$complaint = Complaint::create([...]);

$user = Auth::user();
if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
    Mail::to($user->email)->queue(new ComplaintSubmitted($complaint));
}
```

#### Admin Complaint Controller

```php
use App\Mail\ComplaintStatusUpdated;

// In updateStatus() method
$complaint->update([...]);

if ($complaint->user && $complaint->user->email) {
    Mail::to($complaint->user->email)->queue(new ComplaintStatusUpdated($complaint));
}
```

---

## 🚀 Running the Queue Worker

### Development

Process all queued jobs once:

```bash
php artisan queue:work --stop-when-empty
```

### Production

Run queue worker continuously:

```bash
php artisan queue:work --tries=3 --timeout=60
```

Keep queue worker running with supervisor or systemd:

```bash
# Run in background
nohup php artisan queue:work > /dev/null 2>&1 &
```

### Monitor Queue

Check pending jobs:

```bash
php artisan queue:work --once
```

Clear failed jobs:

```bash
php artisan queue:flush
php artisan queue:failed  # View failed jobs
php artisan queue:retry all  # Retry all failed jobs
```

---

## 📝 Email Templates

All email templates follow a consistent design:

### Common Features

-   **Responsive Design**: Works on desktop and mobile
-   **Professional Styling**: Gradient headers, clean layout
-   **Status Badges**: Color-coded for different statuses
-   **Call-to-Action Buttons**: Direct links to view details
-   **Information Boxes**: Highlighted sections for important data

### Template Structure

```html
<div class="container">
    <div class="header">
        <h1>📝 Email Title</h1>
    </div>

    <div class="content">
        <div class="greeting">Hello {{ $user->name }},</div>
        <div class="message">...</div>
        <div class="info-box">...</div>
        <a href="..." class="button">View Details</a>
    </div>

    <div class="footer">...</div>
</div>
```

---

## 🧪 Testing Email Notifications

### Test Leave Emails

```bash
# Test leave submission
php artisan tinker
$user = User::where('email', 'test@example.com')->first();
$leave = HrmLeaveRequest::create([
    'employee_id' => $user->hrmEmployee->id,
    'leave_type' => 'annual',
    'start_date' => '2025-12-15',
    'end_date' => '2025-12-17',
    'total_days' => 3,
    'reason' => 'Test',
    'status' => 'pending',
]);
Mail::to($user->email)->queue(new LeaveRequestSubmitted($leave));

# Test leave approval
$leave->update(['status' => 'approved', 'approved_by' => 1, 'approved_at' => now()]);
Mail::to($user->email)->queue(new LeaveApproved($leave));

# Test leave rejection
$leave->update(['status' => 'rejected', 'rejection_reason' => 'Test reason']);
Mail::to($user->email)->queue(new LeaveRejected($leave));
```

### Test Complaint Emails

```bash
php artisan tinker
$user = User::first();
$complaint = Complaint::create([
    'user_id' => $user->id,
    'subject' => 'Test',
    'description' => 'Test description',
    'status' => 'pending',
]);
Mail::to($user->email)->queue(new ComplaintSubmitted($complaint));

# Test status update
$complaint->update(['status' => 'resolved']);
Mail::to($user->email)->queue(new ComplaintStatusUpdated($complaint));
```

### Test via Mailtrap Command

```bash
php artisan mail:test sagar@saubhagyagroup.com
```

---

## 📊 Email Tracking

### View Queue Status

```bash
# Check jobs table
php artisan tinker
DB::table('jobs')->count()  # Pending jobs
DB::table('failed_jobs')->count()  # Failed jobs
```

### Mailtrap Dashboard

1. Login to Mailtrap: https://mailtrap.io/
2. Navigate to your inbox
3. View sent emails, spam scores, and HTML previews
4. Check delivery rates and troubleshoot issues

---

## 🔧 Troubleshooting

### Emails Not Sending

**Check Queue Worker**

```bash
php artisan queue:work --stop-when-empty
```

**Check Failed Jobs**

```bash
php artisan queue:failed
php artisan queue:retry {job-id}
```

**Verify Email Configuration**

```bash
php artisan config:clear
php artisan config:cache
```

### Email Validation Errors

Ensure email addresses are valid:

```php
if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
    Mail::to($user->email)->queue(new SomeMail($data));
}
```

### Queue Worker Not Processing

**Restart Queue Worker**

```bash
# Kill existing worker
ps aux | grep "queue:work"
kill {process-id}

# Start new worker
php artisan queue:work
```

---

## 🎨 Customization

### Modify Email Templates

Edit Blade files in `resources/views/emails/`:

-   `leave-request-submitted.blade.php`
-   `leave-approved.blade.php`
-   `leave-rejected.blade.php`
-   `complaint-submitted.blade.php`
-   `complaint-status-updated.blade.php`
-   `announcement.blade.php`

### Change Email Subject

Edit the `envelope()` method in mailable classes:

```php
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Your Custom Subject Here',
    );
}
```

### Add CC/BCC Recipients

```php
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Subject',
        cc: ['manager@company.com'],
        bcc: ['hr@company.com'],
    );
}
```

---

## 📋 Files Modified/Created

### Mailable Classes

-   `app/Mail/LeaveRequestSubmitted.php` ✅ Created
-   `app/Mail/LeaveApproved.php` ✅ Created
-   `app/Mail/LeaveRejected.php` ✅ Created
-   `app/Mail/ComplaintSubmitted.php` ✅ Created
-   `app/Mail/ComplaintStatusUpdated.php` ✅ Created
-   `app/Mail/AnnouncementMail.php` ✅ Updated (added ShouldQueue)

### Email Templates

-   `resources/views/emails/leave-request-submitted.blade.php` ✅ Created
-   `resources/views/emails/leave-approved.blade.php` ✅ Created
-   `resources/views/emails/leave-rejected.blade.php` ✅ Created
-   `resources/views/emails/complaint-submitted.blade.php` ✅ Created
-   `resources/views/emails/complaint-status-updated.blade.php` ✅ Created

### Controllers Updated

-   `app/Http/Controllers/Employee/LeaveController.php` ✅ Added email notification in store()
-   `app/Http/Controllers/Admin/HrmLeaveController.php` ✅ Added email in approve() and reject()
-   `app/Http/Controllers/Employee/ComplaintController.php` ✅ Added email in store()
-   `app/Http/Controllers/Admin/ComplaintController.php` ✅ Added email in updateStatus()

### Configuration

-   `.env` ✅ QUEUE_CONNECTION=database already set
-   `config/queue.php` ✅ Already configured
-   `database/migrations/*_create_jobs_table.php` ✅ Already migrated

### Models

-   `app/Models/HrmLeaveRequest.php` ✅ Fixed date casting to string format

---

## 🎯 Best Practices

1. **Always use queue for emails**: Never send emails synchronously in production
2. **Validate email addresses**: Check before sending to avoid bounces
3. **Monitor failed jobs**: Set up alerts for failed email jobs
4. **Test in Mailtrap first**: Verify emails before sending to real users
5. **Use retry logic**: Configure `$tries` and `$backoff` appropriately
6. **Keep templates responsive**: Ensure emails look good on all devices
7. **Include unsubscribe options**: For compliance (if applicable)
8. **Track email metrics**: Monitor open rates and delivery in Mailtrap

---

## 📈 Production Checklist

-   [x] Queue system configured (database)
-   [x] Jobs table migrated
-   [x] SMTP credentials set in .env
-   [x] All mailable classes implement ShouldQueue
-   [x] Email validation in controllers
-   [x] Queue worker running
-   [x] Failed jobs monitoring
-   [x] Email templates tested
-   [x] Mailtrap configured
-   [ ] Set up supervisor/systemd for queue worker (production)
-   [ ] Configure email rate limits (if needed)
-   [ ] Set up monitoring alerts for failed jobs

---

## 🔗 Quick Links

-   **Mailtrap Inbox**: https://mailtrap.io/inboxes
-   **Queue Documentation**: https://laravel.com/docs/11.x/queues
-   **Mail Documentation**: https://laravel.com/docs/11.x/mail
-   **Employee Leave Portal**: `/employee/leave`
-   **Admin Leave Management**: `/admin/hrm/leaves`
-   **Complaint Management**: `/admin/complaints`

---

## 📞 Support

For issues or questions:

1. Check Mailtrap inbox for delivery status
2. Review failed jobs: `php artisan queue:failed`
3. Check queue worker logs
4. Verify email configuration: `php artisan config:show mail`
5. Contact system administrator

---

**Last Updated**: December 10, 2025  
**Version**: 1.0  
**Author**: AI Assistant
