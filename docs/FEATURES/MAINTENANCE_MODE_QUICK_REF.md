# Maintenance Mode - Quick Reference

## Quick Commands

### Enable Maintenance Mode
```bash
# Via UI: Admin Panel -> Settings -> Maintenance Mode -> Enable
# Or via Tinker:
php artisan tinker
>>> Cache::forever('system_maintenance_mode', true);
>>> Cache::forever('system_maintenance_message', 'Custom message');
```

### Disable Maintenance Mode
```bash
# Via UI: Admin Panel -> Settings -> Maintenance Mode -> Disable
# Or via Tinker:
php artisan tinker
>>> Cache::forget('system_maintenance_mode');
>>> Cache::forget('system_maintenance_message');
```

### Check Status
```bash
php artisan tinker
>>> Cache::get('system_maintenance_mode')
# Returns: true or false
```

## Access Levels

| User Type | Access During Maintenance |
|-----------|---------------------------|
| Super Admin | ✅ Full Access |
| Admin | ✅ Full Access |
| Whitelisted User | ✅ Full Access |
| Regular User | ❌ Maintenance Page |
| Guest | ❌ Maintenance Page |

## Whitelist Configuration

Edit `/config/maintenance.php`:

```php
'allowed_user_ids' => [
    1,   // User ID 1
    5,   // User ID 5
    10,  // User ID 10
],
```

## API Endpoints

```
GET  /api/maintenance/status   - Get current status
POST /api/maintenance/enable   - Enable maintenance (Admin only)
POST /api/maintenance/disable  - Disable maintenance (Admin only)
POST /api/maintenance/message  - Update message (Admin only)
```

## UI Access

**Admin Panel:**
1. Log in as admin
2. Click **Settings** (gear icon) in sidebar
3. Click **Maintenance Mode**
4. Toggle on/off or update message

## Testing Checklist

- [ ] Enable maintenance mode as admin
- [ ] Verify admin can still access all pages
- [ ] Verify regular users see maintenance page
- [ ] Test whitelisted user access
- [ ] Update maintenance message
- [ ] Disable maintenance mode
- [ ] Verify all users can access again

## Troubleshooting

**Issue:** Can't toggle maintenance mode
- **Solution:** Clear cache: `php artisan cache:clear`

**Issue:** Changes not taking effect
- **Solution:** 
  1. Clear config cache: `php artisan config:clear`
  2. Clear route cache: `php artisan route:clear`
  3. Refresh browser

**Issue:** Admin seeing maintenance page
- **Solution:** 
  1. Verify role: Check user has 'admin' or 'super_admin' role
  2. Clear session: Log out and log back in
  3. Check cache driver is working

## Emergency Disable

If locked out, use tinker:

```bash
php artisan tinker
>>> Cache::forget('system_maintenance_mode');
>>> exit
```

Or directly in database (if using database cache):

```sql
DELETE FROM cache WHERE key LIKE '%maintenance%';
```
