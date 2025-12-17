# Mailtrap Email Configuration

## ✅ Setup Complete

Mailtrap has been successfully installed and configured for your ERP system.

## Configuration Details

### Environment Variables (`.env`)

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=6c20da601119174e4bb7269c2a3fb190
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@saubhagyagroup.com"
MAIL_FROM_NAME="Laravel"

MAILTRAP_API_TOKEN=6c20da601119174e4bb7269c2a3fb190
```

### Packages Installed

-   `railsware/mailtrap-php` (v3.9.3) - Mailtrap PHP SDK
-   `symfony/http-client` (v8.0.1) - HTTP client for API requests
-   `nyholm/psr7` (v1.8.2) - PSR-7 implementation

## Testing Email Configuration

### Method 1: Using Test Command

```bash
# Interactive mode (will prompt for email)
php artisan mail:test

# Direct mode (provide email as argument)
php artisan mail:test your-email@example.com
```

### Method 2: Test with Announcement Module

1. Go to `/admin/announcements/create`
2. Create a test announcement
3. Enable "Send email notification"
4. Select recipients
5. Check Mailtrap inbox for the email

### Method 3: Manual Test in Tinker

```bash
php artisan tinker

# In tinker shell:
Mail::raw('Test email content', function ($message) {
    $message->to('test@example.com')
        ->subject('Test Email');
});
```

## Mailtrap Dashboard Access

-   **Login**: https://mailtrap.io/signin
-   **Inboxes**: https://mailtrap.io/inboxes
-   **API Tokens**: https://mailtrap.io/api-tokens

## Features

### Email Testing

✅ All emails sent from the application go to Mailtrap inbox  
✅ No real emails sent to actual recipients during development  
✅ View HTML and text versions  
✅ Check spam score  
✅ Validate email headers  
✅ Test email rendering across devices

### Production Ready

When ready for production, Mailtrap supports:

-   Actual email delivery (Mailtrap Send)
-   Email analytics
-   Deliverability insights
-   Bulk sending

## Email Configuration Files

### Modified Files

1. `.env` - Environment variables
2. `config/mail.php` - Added encryption parameter

### Created Files

1. `app/Console/Commands/TestMailtrap.php` - Test command

## How Emails Are Sent

### From Announcement Module

When creating an announcement with "Send Email" enabled:

```php
// In AnnouncementController.php
Mail::to($recipient->email)->send(new AnnouncementMail($announcement, $recipient));
```

### Email Template

Location: `resources/views/emails/announcement.blade.php`

-   Beautiful HTML design
-   Priority badges
-   Responsive layout
-   Company branding

## Troubleshooting

### Email Not Sending

1. **Check configuration:**

    ```bash
    php artisan config:clear
    php artisan cache:clear
    ```

2. **Verify credentials:**

    - Check `.env` file has correct API token
    - Ensure `MAIL_MAILER=smtp` (not `log`)

3. **Check logs:**
    ```bash
    tail -f storage/logs/laravel.log
    ```

### Common Issues

**Issue**: Emails go to log instead of Mailtrap  
**Solution**: Ensure `MAIL_MAILER=smtp` in `.env`

**Issue**: Authentication failed  
**Solution**: Verify API token is correct in `.env`

**Issue**: Connection timeout  
**Solution**: Check firewall/network allows port 587

## Queue Configuration (Optional)

For better performance, enable queue for emails:

1. **Update `.env`:**

    ```env
    QUEUE_CONNECTION=database
    ```

2. **Create jobs table:**

    ```bash
    php artisan queue:table
    php artisan migrate
    ```

3. **Run queue worker:**

    ```bash
    php artisan queue:work
    ```

4. **Update Mailable** (already configured):
    ```php
    class AnnouncementMail extends Mailable implements ShouldQueue
    {
        use Queueable;
        // ...
    }
    ```

## Email Limits

### Mailtrap Free Plan

-   500 emails/month (Testing)
-   1,000 emails/month (Sending)

### Recommendations

-   Use queues for bulk announcements
-   Monitor usage in Mailtrap dashboard
-   Upgrade plan if needed for production

## Best Practices

1. **Test Before Production**

    - Always test announcements in Mailtrap first
    - Check email rendering on different devices
    - Verify links work correctly

2. **Email Content**

    - Keep subject lines under 50 characters
    - Use clear call-to-action
    - Test spam score in Mailtrap

3. **Performance**
    - Enable queue for bulk emails
    - Use rate limiting for large recipient lists
    - Monitor Mailtrap analytics

## Next Steps

1. ✅ Test email sending with test command
2. ✅ Create a test announcement
3. ✅ Verify emails arrive in Mailtrap
4. 🔄 Configure queue workers (optional)
5. 🔄 Set up production email sending when ready

## Support

-   **Mailtrap Docs**: https://help.mailtrap.io/
-   **Laravel Mail Docs**: https://laravel.com/docs/mail
-   **Support**: Contact Mailtrap support for account issues

---

**Configuration Date**: December 10, 2025  
**Status**: ✅ Ready for Testing  
**Next Action**: Run `php artisan mail:test your-email@example.com`
