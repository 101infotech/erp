# 🚀 Quick Start - Test Mailtrap Now!

## 1️⃣ Test Email Command (Fastest Way)

Open your terminal and run:

```bash
cd /Users/sagarchhetri/Downloads/Coding/erp
php artisan mail:test test@example.com
```

**Expected Output:**

```
Sending test email to: test@example.com
✅ Test email sent successfully!
Check your Mailtrap inbox at: https://mailtrap.io/inboxes
```

---

## 2️⃣ Test with Announcement Module

### Step-by-Step:

1. **Start Laravel Server** (if not running):

    ```bash
    php artisan serve
    ```

2. **Login as Admin**:

    - Go to: `http://localhost:8000/login`
    - Login with admin credentials

3. **Create Test Announcement**:
    - Navigate to: **Admin Panel → Announcements**
    - Click **"New Announcement"**
4. **Fill in the Form**:

    ```
    Title: "Test Email from Mailtrap"
    Content: "This is a test announcement to verify Mailtrap is working!"
    Priority: High
    Send To: All Staff (or select specific user)
    ✅ Send email notification (CHECK THIS!)
    ✅ Publish immediately
    ```

5. **Click "Create Announcement"**

6. **Check Mailtrap**:
    - Go to: https://mailtrap.io/inboxes
    - You should see the email arrive!

---

## 3️⃣ View Email in Mailtrap

### What to Check:

✅ **Email Received** - Should appear in inbox within seconds  
✅ **Subject Line** - Shows priority: `[HIGH] Test Email from Mailtrap`  
✅ **HTML Preview** - Beautiful formatted email with gradients  
✅ **Spam Score** - Should be good (no spam triggers)  
✅ **Headers** - Verify sender, recipient, etc.

### Mailtrap Features to Try:

1. **HTML & Plain Text** - Switch between views
2. **Check Spam** - See spam score analysis
3. **Forward** - Forward to real email for testing
4. **Preview** - See how it looks on different devices
5. **Raw** - View raw email source

---

## 4️⃣ Test Different Scenarios

### Test Priority Levels:

-   Create announcements with **Low**, **Normal**, **High** priorities
-   Each has different color badges in email

### Test Recipients:

-   **All Staff** - Sends to everyone
-   **Specific Employees** - Select individual users

### Test Draft Mode:

-   Uncheck "Publish immediately"
-   Email should NOT be sent
-   Only sent when published

---

## 🎨 Expected Email Appearance

```
┌─────────────────────────────────────────┐
│  📢 Company Announcement                │
│  [HIGH PRIORITY] Badge                  │
│─────────────────────────────────────────│
│                                         │
│  Test Email from Mailtrap              │
│                                         │
│  This is a test announcement to verify  │
│  Mailtrap is working!                   │
│                                         │
│  [View Full Announcement] Button        │
│                                         │
│─────────────────────────────────────────│
│  📅 Posted on Dec 10, 2025              │
│  👤 From Admin Name                     │
│─────────────────────────────────────────│
│  This is an automated email...          │
└─────────────────────────────────────────┘
```

---

## 🔍 Verify Configuration

**Check .env file:**

```bash
cat .env | grep MAIL_
```

**Should show:**

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=6c20da601119174e4bb7269c2a3fb190
MAIL_ENCRYPTION=tls
```

---

## ❌ Troubleshooting

### Email Not Appearing in Mailtrap?

**1. Check Laravel Logs:**

```bash
tail -f storage/logs/laravel.log
```

**2. Clear All Caches:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**3. Verify .env:**

-   Ensure `MAIL_MAILER=smtp` (not `log`)
-   Check API token is correct
-   No extra spaces in credentials

**4. Test Raw Connection:**

```bash
php artisan tinker

# In tinker:
Mail::raw('Test', function($m) { $m->to('test@test.com')->subject('Test'); });
exit
```

### Common Issues:

| Issue                       | Solution                                |
| --------------------------- | --------------------------------------- |
| "Connection timeout"        | Check firewall, port 587 open           |
| "Authentication failed"     | Verify API token in .env                |
| Email goes to `laravel.log` | Change MAIL_MAILER to `smtp`            |
| No error but no email       | Check Mailtrap inbox in correct account |

---

## ✅ Success Indicators

You'll know it's working when:

1. ✅ Test command shows "sent successfully"
2. ✅ No errors in `laravel.log`
3. ✅ Email appears in Mailtrap inbox
4. ✅ HTML preview looks good
5. ✅ Spam score is good
6. ✅ All links work correctly

---

## 🎉 Ready to Use!

Once tested successfully:

-   ✅ All announcement emails will go through Mailtrap
-   ✅ Safe to test without sending real emails
-   ✅ Perfect for development and staging
-   ✅ Ready for production when needed

---

**Need Help?**

-   Check `docs/MAILTRAP_SETUP.md` for detailed guide
-   Check `docs/ANNOUNCEMENT_MODULE.md` for module guide
-   View logs: `tail -f storage/logs/laravel.log`

**Mailtrap Dashboard**: https://mailtrap.io/inboxes

---

Run this now:

```bash
php artisan mail:test your-email@example.com
```

Then check Mailtrap! 🚀
