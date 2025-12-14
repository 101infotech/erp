# Password Reset Quick Reference

## Quick Actions

### Reset User Password - 3 Methods

#### Method 1: Manual Password (Recommended for Onboarding)

1. Go to Users page or User profile
2. Click "Password" → "Set New Password"
3. Enter password twice
4. ✓ Check "Send notification"
5. Click "Set Password"

#### Method 2: Email Reset Link (Most Secure)

1. Go to Users page or User profile
2. Click "Password" → "Send Reset Link"
3. Confirm action
4. ✅ User receives email with secure link
5. User creates their own password

#### Method 3: Auto-Generate (Fastest)

1. Go to Users page or User profile
2. Click "Password" → "Generate & Email Password"
3. Confirm action
4. ✅ Random password emailed to user

## When to Use Each Method

| Scenario             | Best Method     | Why                                       |
| -------------------- | --------------- | ----------------------------------------- |
| User forgot password | Send Reset Link | Most secure, user controls password       |
| New employee         | Manual Password | Set temporary password, notify to change  |
| Urgent access needed | Auto-Generate   | Fast, secure, admin doesn't know password |
| Security breach      | Manual Password | Immediate change, contact user separately |

## Quick Keyboard Shortcuts

-   **Esc** - Close modal
-   **Enter** - Submit form (when in modal)

## URLs

-   User Management: `/admin/users`
-   User Profile: `/admin/users/{id}`

## Email Templates

All three methods send email notifications:

-   ✉️ Reset Link - Standard Laravel reset
-   ✉️ Password Changed - Notification of manual change
-   ✉️ New Password - Contains auto-generated password

## Validation Rules

Password must have:

-   ✓ Minimum 8 characters
-   ✓ Letters recommended
-   ✓ Numbers recommended
-   ✓ Symbols recommended

## Common Issues

| Problem              | Solution                                     |
| -------------------- | -------------------------------------------- |
| Email not received   | Check spam, verify email address             |
| Reset link expired   | Links expire after 60 minutes - send new one |
| Can't set password   | Check password meets requirements            |
| Email failed to send | Check mail settings in `.env`                |

## Password Requirements

**Manual Password:**

-   You choose the password
-   Must meet validation rules
-   Optional email notification

**Auto-Generated Password:**

-   16 characters long
-   Contains: letters, numbers, symbols
-   Automatically emailed
-   Very secure

**Reset Link:**

-   User creates their password
-   Link valid for 60 minutes
-   Throttled (1 request per minute)

## Tips

✅ **DO:**

-   Use "Send Reset Link" for regular password resets
-   Check "Notify user" when setting passwords manually
-   Verify user identity before resetting
-   Use strong passwords

❌ **DON'T:**

-   Share passwords over unencrypted channels
-   Use simple/common passwords
-   Reset without user's knowledge (unless security breach)
-   Disable email notifications without reason

## Emergency Access

If email is not working:

1. Use "Set New Password"
2. **Uncheck** "Send notification"
3. Contact user through alternative channel
4. Provide password securely (phone, in-person, secure chat)

---

**Quick Help:**

-   Documentation: `/docs/PASSWORD_RESET_SYSTEM.md`
-   User Guide: This file
-   Technical Support: Contact system administrator
