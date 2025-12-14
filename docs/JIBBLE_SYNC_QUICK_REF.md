# Jibble Sync Quick Reference

## Quick Start

### One-Click Sync (Web Interface)

1. Go to **Admin → HRM → Attendance**
2. Click **"Sync All (30 days)"** button
3. Wait for success message

### Command Line Sync

```bash
# Full sync (recommended for first time)
php artisan jibble:sync --all --days=30

# Quick employee update
php artisan jibble:sync --employees

# Sync last 7 days attendance
php artisan jibble:sync --attendance --days=7
```

## Common Commands

| Command                                          | What it does                          |
| ------------------------------------------------ | ------------------------------------- |
| `php artisan jibble:sync --all`                  | Sync employees + last 7 days          |
| `php artisan jibble:sync --employees`            | Update employee list only             |
| `php artisan jibble:sync --attendance --days=30` | Sync last 30 days attendance          |
| `php artisan schedule:list`                      | Check scheduled tasks                 |
| `php artisan schedule:run`                       | Run scheduled tasks now (for testing) |

## Automatic Sync

-   **When**: Runs automatically at 8 AM and 6 PM daily
-   **What**: Syncs all employees + last 7 days attendance
-   **Setup**: Add cron job (one time only):
    ```bash
    crontab -e
    # Add this line:
    * * * * * cd /path/to/erp && php artisan schedule:run >> /dev/null 2>&1
    ```

## Troubleshooting

| Problem                      | Solution                                                                                                                     |
| ---------------------------- | ---------------------------------------------------------------------------------------------------------------------------- |
| "Method not found" error     | Clear cache: `php artisan cache:clear`                                                                                       |
| "Duplicate constraint" error | Already fixed! If still happening, check logs                                                                                |
| No records synced            | 1. Check internet connection<br>2. Verify Jibble credentials in `.env`<br>3. Run `php artisan jibble:sync --employees` first |
| Scheduled task not running   | 1. Check cron is set up<br>2. Run `php artisan schedule:list`                                                                |

## Where to Find Things

-   **Sync Buttons**: Admin → HRM → Attendance (top right)
-   **Employee Timesheet**: Admin → HRM → Attendance → Click employee name
-   **Logs**: `storage/logs/laravel.log`
-   **Full Guide**: `docs/JIBBLE_SYNC_GUIDE.md`
-   **Implementation Details**: `docs/JIBBLE_SYNC_IMPLEMENTATION.md`

## What Gets Synced

### Employees (`--employees`)

-   Name, email, phone
-   Company and department
-   Employee code
-   Status (active/inactive)

### Attendance (`--attendance`)

-   Daily tracked hours
-   Payroll hours
-   Overtime hours
-   Individual clock in/out entries
-   Project/activity information
-   Location and notes

## Tips

✅ **Do this:**

-   Always sync employees first when setting up
-   Use `--all` for regular syncs
-   Check logs if something seems wrong
-   Start with 7 days when testing

❌ **Avoid this:**

-   Don't sync 90 days on first try (start small)
-   Don't run multiple syncs at the same time
-   Don't modify database directly (use sync instead)

## Support

If you encounter issues:

1. Check `storage/logs/laravel.log`
2. Review `docs/JIBBLE_SYNC_GUIDE.md` (troubleshooting section)
3. Test with: `php artisan jibble:sync --employees`

## Configuration

Required in `.env`:

```env
JIBBLE_CLIENT_ID=your-client-id
JIBBLE_CLIENT_SECRET=your-client-secret
JIBBLE_BASE_URL=https://workspace.prod.jibble.io/v1
```

---

**Last Updated**: December 3, 2025  
**Version**: 1.0  
**Status**: Production Ready ✅
