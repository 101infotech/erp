# Password Reset System - Testing Guide

## Pre-Testing Setup

### 1. Configure Email Settings

Edit `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # Use Mailtrap for testing
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

For production, use a real SMTP service (Gmail, SendGrid, Mailgun, etc.)

### 2. Create Test User

Run in terminal or tinker:

```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Test User';
$user->email = 'testuser@example.com';
$user->password = bcrypt('password');
$user->role = 'user';
$user->save();
```

## Test Cases

### Test 1: Set New Password (With Notification)

**Steps:**

1. Login as admin
2. Navigate to `/admin/users`
3. Find the test user
4. Click "Password" dropdown → "Set New Password"
5. Enter password: `NewPass123!`
6. Confirm password: `NewPass123!`
7. ✓ Check "Send notification email to user"
8. Click "Set Password"

**Expected Results:**

-   ✅ Success message appears
-   ✅ Password changed in database
-   ✅ Email sent to user
-   ✅ Can login with new password
-   ✅ Old password no longer works

**Verify Email Contains:**

-   Subject: "Password Changed - ERP System"
-   User's name
-   Security recommendations
-   Login link

---

### Test 2: Set New Password (Without Notification)

**Steps:**

1. Click "Password" → "Set New Password"
2. Enter password: `AnotherPass456!`
3. Confirm password: `AnotherPass456!`
4. ✗ Uncheck "Send notification email to user"
5. Click "Set Password"

**Expected Results:**

-   ✅ Success message appears
-   ✅ Password changed in database
-   ✅ NO email sent
-   ✅ Can login with new password

---

### Test 3: Send Reset Link

**Steps:**

1. Click "Password" → "Send Reset Link"
2. Confirm the action

**Expected Results:**

-   ✅ Success message: "Password reset link has been sent to..."
-   ✅ Email sent to user
-   ✅ Email contains reset link
-   ✅ Link valid for 60 minutes

**Verify Email Contains:**

-   Subject: "Reset Password Notification"
-   Reset password button/link
-   Expiry notice (60 minutes)

**Test Reset Link:**

1. Open email
2. Click reset link
3. Enter new password
4. Confirm password
5. Submit form

**Expected:**

-   ✅ Redirected to login page
-   ✅ Success message displayed
-   ✅ Can login with new password

---

### Test 4: Generate & Email Password

**Steps:**

1. Click "Password" → "Generate & Email Password"
2. Confirm the action

**Expected Results:**

-   ✅ Success message appears
-   ✅ Password auto-generated (16 characters)
-   ✅ Email sent to user
-   ✅ Can login with emailed password

**Verify Email Contains:**

-   Subject: "Password Reset - Action Required"
-   User's name
-   The new temporary password (visible in email)
-   Instructions to change password
-   Login link

**Test Login:**

1. Copy password from email
2. Go to login page
3. Login with emailed password

**Expected:**

-   ✅ Successfully logged in

---

### Test 5: Password Validation

**Test 5.1: Password Too Short**

1. Open "Set New Password" modal
2. Enter password: `123` (too short)
3. Confirm: `123`
4. Click "Set Password"

**Expected:**

-   ❌ Validation error
-   ❌ Password not saved

**Test 5.2: Passwords Don't Match**

1. Open "Set New Password" modal
2. Enter password: `ValidPass123!`
3. Confirm: `DifferentPass456!`
4. Click "Set Password"

**Expected:**

-   ❌ Validation error
-   ❌ Password not saved

---

### Test 6: UI/UX Testing

**Test 6.1: Dropdown Menu**

1. Click "Password" button
2. Verify menu opens
3. Click outside menu
4. Verify menu closes

**Test 6.2: Modal**

1. Click "Set New Password"
2. Verify modal opens
3. Press ESC key
4. Verify modal closes
5. Open modal again
6. Click "Cancel" button
7. Verify modal closes

**Test 6.3: Mobile View**

1. Resize browser to mobile width (< 768px)
2. Navigate to users page
3. Verify card layout displays
4. Test password dropdown on mobile
5. Test modal on mobile

---

### Test 7: User Profile Page

**Steps:**

1. Navigate to `/admin/users/{user-id}`
2. Click "Password Options" button
3. Test all three methods from profile page

**Expected:**

-   ✅ All methods work identically to users index page
-   ✅ Modal displays correctly
-   ✅ Success messages appear

---

### Test 8: Error Handling

**Test 8.1: Email Failure**

1. Temporarily break email config in `.env`
2. Try "Generate & Email Password"

**Expected:**

-   ✅ Error message displayed
-   ✅ Error logged to `storage/logs/laravel.log`
-   ✅ Password still changed in database

**Test 8.2: Invalid User Email**

1. Update user email to invalid format
2. Try sending reset link

**Expected:**

-   ✅ Error message displayed
-   ✅ Process fails gracefully

---

### Test 9: Security Testing

**Test 9.1: CSRF Protection**

1. Open browser console
2. Try submitting form without CSRF token
3. Should fail

**Test 9.2: Authentication Check**

1. Logout
2. Try accessing `/admin/users/{user}/set-password` directly
3. Should redirect to login

**Test 9.3: Password Hashing**

1. Set a password
2. Check database: `select password from users where id=X;`
3. Verify password is hashed (bcrypt format: starts with `$2y$`)

---

### Test 10: Multiple Browsers

Test across different browsers:

-   ✓ Chrome
-   ✓ Firefox
-   ✓ Safari
-   ✓ Edge

Check:

-   Dropdown menus work
-   Modals display correctly
-   Forms submit properly
-   Responsive design works

---

## Performance Testing

### Test 11: Concurrent Requests

1. Open multiple tabs
2. Reset passwords for different users simultaneously
3. Verify no conflicts

---

## Regression Testing

After any code changes, verify:

-   [ ] All three methods still work
-   [ ] Emails still send
-   [ ] Validation still works
-   [ ] UI still displays correctly
-   [ ] Mobile view still works
-   [ ] Error handling still works

---

## Production Checklist

Before deploying to production:

### Email Configuration

-   [ ] Update MAIL_HOST to production SMTP server
-   [ ] Update MAIL_FROM_ADDRESS to company email
-   [ ] Test email delivery in production environment
-   [ ] Verify emails don't go to spam

### Security

-   [ ] HTTPS enabled
-   [ ] APP_DEBUG=false in .env
-   [ ] APP_ENV=production in .env
-   [ ] Strong APP_KEY generated
-   [ ] CSRF protection working
-   [ ] Authentication middleware protecting routes

### Database

-   [ ] Password reset tokens table exists
-   [ ] Users table has password column
-   [ ] Email column exists and is indexed

### Permissions

-   [ ] Only admins can access password reset features
-   [ ] Users cannot reset their own passwords via admin panel
-   [ ] Proper role-based access control

### Logging

-   [ ] Error logging configured
-   [ ] Log rotation set up
-   [ ] Monitoring for email failures

---

## Test Data Cleanup

After testing, clean up test data:

```bash
php artisan tinker
```

```php
// Delete test user
User::where('email', 'testuser@example.com')->delete();

// Clear password reset tokens
DB::table('password_reset_tokens')->truncate();

// Clear failed jobs (if any)
DB::table('failed_jobs')->truncate();
```

---

## Bug Report Template

If you find bugs, report using this format:

```
**Bug Title:** [Brief description]

**Steps to Reproduce:**
1.
2.
3.

**Expected Behavior:**
[What should happen]

**Actual Behavior:**
[What actually happened]

**Environment:**
- Browser:
- OS:
- Laravel Version:
- PHP Version:

**Screenshots:**
[Attach if applicable]

**Error Logs:**
[From storage/logs/laravel.log]
```

---

## Success Criteria

All tests pass when:

-   ✅ All three password reset methods work
-   ✅ Emails are sent and received correctly
-   ✅ Password validation works
-   ✅ UI/UX is smooth and responsive
-   ✅ Error handling is graceful
-   ✅ Security measures are in place
-   ✅ Works on desktop and mobile
-   ✅ Works across all major browsers
-   ✅ No PHP/JavaScript errors in logs
-   ✅ Performance is acceptable

---

**Testing Guide Version:** 1.0
**Last Updated:** December 5, 2025
