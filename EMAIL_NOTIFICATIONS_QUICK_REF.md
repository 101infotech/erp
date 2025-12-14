# Email Notifications - Quick Reference

## 🚀 Quick Start

### Start Queue Worker

```bash
php artisan queue:work
```

### Test Email System

```bash
php artisan mail:test sagar@saubhagyagroup.com
```

---

## 📧 Email Types

| Event              | Email Class              | Recipient      | Trigger                        |
| ------------------ | ------------------------ | -------------- | ------------------------------ |
| Leave Submitted    | `LeaveRequestSubmitted`  | Employee       | Employee submits leave request |
| Leave Approved     | `LeaveApproved`          | Employee       | Admin approves leave           |
| Leave Rejected     | `LeaveRejected`          | Employee       | Admin rejects leave            |
| Feedback Submitted | `ComplaintSubmitted`     | Employee       | Employee submits feedback      |
| Feedback Updated   | `ComplaintStatusUpdated` | Employee       | Admin updates feedback status  |
| Announcement       | `AnnouncementMail`       | Selected Users | Admin creates announcement     |

---

## ⚡ Common Commands

```bash
# Process all queued emails
php artisan queue:work --stop-when-empty

# Run queue worker continuously
php artisan queue:work --tries=3 --timeout=60

# Check queue status
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear all jobs
php artisan queue:flush

# Restart queue worker
php artisan queue:restart
```

---

## 🧪 Testing Emails

### Via Tinker

```php
// Test leave submission email
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
```

### Via UI

1. **Leave Submission**

    - Go to `/employee/leave/create`
    - Submit a leave request
    - Check email inbox

2. **Leave Approval**

    - Go to `/admin/hrm/leaves`
    - Approve a pending leave
    - Employee receives approval email

3. **Feedback Submission**

    - Go to `/employee/complaints/create`
    - Submit feedback
    - Check email inbox

4. **Announcement**
    - Go to `/admin/announcements/create`
    - Check "Send email notification"
    - Create announcement
    - Recipients receive email

---

## 🔧 Troubleshooting

### Emails Not Sending?

1. **Check Queue Worker**

    ```bash
    php artisan queue:work --stop-when-empty
    ```

2. **Check Failed Jobs**

    ```bash
    php artisan queue:failed
    ```

3. **Verify Config**

    ```bash
    php artisan config:clear
    php artisan config:cache
    ```

4. **Check Mailtrap**
    - Visit: https://mailtrap.io/inboxes
    - Verify emails are received

### Common Issues

| Issue                 | Solution                       |
| --------------------- | ------------------------------ |
| Queue not processing  | Run `php artisan queue:work`   |
| Invalid email address | Check user email in database   |
| Config not loading    | Run `php artisan config:cache` |
| Emails stuck in queue | Restart queue worker           |

---

## 📊 Monitoring

### Check Queue Status

```bash
# Pending jobs
php artisan tinker
DB::table('jobs')->count()

# Failed jobs
DB::table('failed_jobs')->count()
```

### View Mailtrap Dashboard

1. Login: https://mailtrap.io/
2. Select inbox
3. View sent emails
4. Check spam scores
5. Preview HTML

---

## 📝 Email Templates

All templates in `resources/views/emails/`:

-   `leave-request-submitted.blade.php`
-   `leave-approved.blade.php`
-   `leave-rejected.blade.php`
-   `complaint-submitted.blade.php`
-   `complaint-status-updated.blade.php`
-   `announcement.blade.php`

---

## ⚙️ Configuration

### .env Settings

```env
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=6c20da601119174e4bb7269c2a3fb190
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@saubhagyagroup.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🎯 Quick Tips

✅ **Always run queue worker in production**  
✅ **Test in Mailtrap before going live**  
✅ **Monitor failed jobs regularly**  
✅ **Validate email addresses before sending**  
✅ **Use supervisor/systemd for queue worker**

❌ **Don't send emails synchronously**  
❌ **Don't skip email validation**  
❌ **Don't forget to restart queue worker after changes**

---

## 📞 Support

-   **Documentation**: `docs/EMAIL_NOTIFICATION_SYSTEM.md`
-   **Mailtrap**: https://mailtrap.io/
-   **Queue Docs**: https://laravel.com/docs/11.x/queues

---

**Last Updated**: December 10, 2025
