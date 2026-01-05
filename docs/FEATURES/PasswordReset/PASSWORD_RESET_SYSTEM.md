# Password Reset System - Admin Guide

## Overview

The admin panel now includes a comprehensive password reset system with three different methods to manage user passwords. This provides flexibility for different scenarios and security requirements.

## Features

### Three Password Reset Methods

#### 1. **Set New Password** (Manual)

-   Admin directly sets a new password for the user
-   Option to notify user via email about the password change
-   Use when: User needs immediate access or prefers a specific password format

#### 2. **Send Reset Link** (Self-Service)

-   Sends a standard password reset link to user's email
-   User clicks the link and sets their own password
-   Link expires after 60 minutes (configurable)
-   Use when: User should choose their own password for security

#### 3. **Generate & Email Password** (Automatic)

-   Generates a secure random 16-character password
-   Automatically emails the password to the user
-   Password includes letters, numbers, and symbols
-   Use when: User needs immediate access but admin doesn't want to know the password

## How to Use

### From User Management Page (`/admin/users`)

1. Locate the user in the table
2. Click on **"Password"** in the Actions column
3. A dropdown menu will appear with three options:
    - **Set New Password**: Opens a modal to manually set password
    - **Send Reset Link**: Sends email with reset link
    - **Generate & Email Password**: Auto-generates and emails password

### From User Profile Page (`/admin/users/{id}`)

1. Navigate to the user's profile page
2. Click on **"Password Options"** button (yellow button in header)
3. Select one of the three options from the dropdown

## Password Requirements

-   **Minimum Length**: 8 characters
-   **Recommended**: Include letters, numbers, and symbols
-   **Auto-Generated**: 16 characters with letters, numbers, symbols

## Email Notifications

### Password Reset Email (Method 3: Auto-Generated)

The user receives:

-   The new temporary password
-   Instructions to log in
-   Reminder to change password after first login
-   Security notice about the change

### Password Changed Notification (Method 1: Optional)

When admin manually sets a password with "notify user" checked:

-   User receives notification that password was changed
-   Security recommendations
-   Link to log in
-   Contact information for questions

### Password Reset Link Email (Method 2)

Standard Laravel password reset email:

-   Secure reset link (expires in 60 minutes)
-   Instructions to create new password
-   Security notice

## Security Features

1. **Email Verification**: All methods send notifications to user's registered email
2. **Secure Passwords**: Auto-generated passwords are cryptographically secure
3. **Password Hashing**: All passwords are hashed using bcrypt before storage
4. **Expiring Links**: Reset links expire after 60 minutes
5. **Audit Trail**: All password changes are logged in system logs
6. **Admin Protection**: Admins cannot delete their own accounts

## Common Scenarios

### Scenario 1: User Forgot Password

**Recommended Method**: Send Reset Link (Method 2)

-   User maintains control of their password
-   Most secure option
-   Standard industry practice

### Scenario 2: New Employee Onboarding

**Recommended Method**: Set New Password (Method 1)

-   Set a simple temporary password
-   Notify user via email
-   Ask user to change on first login

### Scenario 3: Locked Out User (Urgent)

**Recommended Method**: Generate & Email Password (Method 3)

-   Fastest method
-   User gets immediate access
-   Admin doesn't know the password
-   Strong random password

### Scenario 4: Security Breach Suspected

**Recommended Method**: Set New Password (Method 1)

-   Immediate password change
-   Don't notify via email (uncheck option)
-   Contact user through alternative channel
-   Provide new password securely (phone, in-person)

## Admin Interface

### User Index Page

```
Actions Column:
- View
- Edit
- Password ▼
  ├── Set New Password
  ├── Send Reset Link
  └── Generate & Email Password
- Delete (if not self)
```

### User Profile Page

```
Header Actions:
- Edit User (Blue Button)
- Password Options ▼ (Yellow Button)
  ├── Set New Password
  ├── Send Reset Link
  └── Generate & Email Password
```

### Set Password Modal

```
- User Information Display
- Password Input Field
- Confirm Password Field
- "Notify User" Checkbox
- Cancel / Set Password Buttons
```

## Technical Details

### Routes

-   `POST /admin/users/{user}/set-password` - Manual password setting
-   `POST /admin/users/{user}/send-reset-link` - Send reset link
-   `POST /admin/users/{user}/reset-password` - Generate & email password

### Controllers

-   `UserController@setPassword` - Handles manual password updates
-   `UserController@sendPasswordResetLink` - Sends reset links
-   `UserController@resetPassword` - Generates random passwords

### Notifications

-   `PasswordResetNotification` - Sent when auto-generating password
-   `PasswordChangedNotification` - Sent when manually setting password
-   `ResetPassword` (Laravel Default) - Sent with reset link

## Error Handling

The system includes comprehensive error handling:

1. **Email Failures**: If email fails to send, admin sees error message with details
2. **Validation Errors**: Password requirements are validated
3. **Logging**: All errors are logged to `storage/logs/laravel.log`
4. **User Feedback**: Success/error messages displayed to admin

## Configuration

### Password Expiry Time (Reset Links)

Located in: `config/auth.php`

```php
'passwords' => [
    'users' => [
        'expire' => 60, // minutes
        'throttle' => 60, // seconds between requests
    ],
],
```

### Password Requirements

Located in: `app/Http/Controllers/Admin/UserController.php`

```php
Rules\Password::defaults()
```

To customize, modify in `app/Providers/AppServiceProvider.php`:

```php
Password::defaults(function () {
    return Password::min(8)
        ->letters()
        ->numbers()
        ->symbols()
        ->mixedCase();
});
```

## Best Practices

1. **Use Send Reset Link** when possible - most secure
2. **Check "Notify User"** when setting passwords manually
3. **Use strong passwords** (8+ characters, mixed types)
4. **Log password resets** for security audits
5. **Verify user identity** before resetting passwords
6. **Use HTTPS** in production (required for secure password transmission)
7. **Regular security training** for users about password best practices

## Troubleshooting

### Email Not Sending

1. Check `.env` mail configuration:
    ```
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your-username
    MAIL_PASSWORD=your-password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=noreply@yourapp.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```
2. Check logs: `storage/logs/laravel.log`
3. Test mail configuration: `php artisan tinker` then `Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));`

### Password Not Accepted

-   Verify minimum length (8 characters)
-   Check password complexity requirements
-   Ensure passwords match in confirmation field

### Reset Link Expired

-   Links expire after 60 minutes
-   Request a new reset link
-   Check system time is synchronized

### User Not Receiving Email

1. Check spam/junk folder
2. Verify email address is correct
3. Check mail server logs
4. Try alternative email address

## Security Considerations

-   Never share passwords over unencrypted channels
-   Always use HTTPS in production
-   Regularly audit password reset logs
-   Implement rate limiting for password reset requests
-   Consider implementing 2FA for admin accounts
-   Regular security training for administrators

## Future Enhancements

Potential improvements to consider:

-   Two-factor authentication requirement for admins
-   Password history (prevent reuse)
-   Bulk password reset operations
-   Password expiry policies
-   Login attempt monitoring
-   Account lockout after failed attempts
-   SMS notifications for password changes
-   Activity logs visible to users

---

**Last Updated**: December 5, 2025
**Version**: 1.0
