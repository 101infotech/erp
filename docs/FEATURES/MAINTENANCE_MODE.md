# Maintenance Mode Feature

## Overview
The system now includes a comprehensive maintenance mode feature that allows administrators to put the system under maintenance while still providing access to specific users for testing purposes.

## Key Features

### 1. **Selective Access Control**
- **Admins & Super Admins**: Always have full access during maintenance mode
- **Whitelisted Users**: Specific user IDs can be configured to bypass maintenance mode
- **Regular Users**: See a maintenance page with a custom message

### 2. **Easy Management**
- Toggle maintenance mode on/off from the admin panel
- Customize the maintenance message shown to users
- Real-time status updates

### 3. **Automatic Bypass**
- Admin users can test all features including staff functionalities
- No need to disable maintenance mode for testing
- Transparent bypass with headers for debugging

## How to Use

### Access the Maintenance Mode Panel

1. Log in as an admin or super admin
2. Navigate to **Settings** (gear icon) in the sidebar
3. Click on **Maintenance Mode**

### Enable Maintenance Mode

1. Click the **"Enable Maintenance"** button
2. Optionally customize the maintenance message
3. Click **"Update Message"** to save the custom message

### Disable Maintenance Mode

1. Click the **"Disable Maintenance"** button
2. System will be immediately accessible to all users

### Whitelist Specific Users

To allow specific users to bypass maintenance mode:

1. Open `/config/maintenance.php`
2. Add user IDs to the `allowed_user_ids` array:

```php
'allowed_user_ids' => [
    1,  // User ID 1
    5,  // User ID 5
    10, // User ID 10
],
```

3. Save the file
4. No restart required - changes take effect immediately

## Technical Implementation

### Files Created

1. **Middleware**: `/app/Http/Middleware/CheckMaintenanceMode.php`
   - Checks if maintenance mode is enabled
   - Verifies user roles and whitelist
   - Redirects to maintenance page or allows access

2. **Controller**: `/app/Http/Controllers/MaintenanceModeController.php`
   - Manages maintenance mode state
   - Provides API endpoints for toggling and updating

3. **Configuration**: `/config/maintenance.php`
   - Stores allowed user IDs
   - Default maintenance message
   - Allowed roles configuration

4. **View**: `/resources/views/errors/maintenance.blade.php`
   - Beautiful maintenance page shown to users
   - Auto-refreshes every 30 seconds
   - Customizable message display

5. **Admin Panel**: `/resources/views/admin/maintenance/index.blade.php`
   - UI for managing maintenance mode
   - Toggle on/off
   - Update custom messages

### API Endpoints

All endpoints require authentication:

- `GET /api/maintenance/status` - Get current maintenance status
- `POST /api/maintenance/enable` - Enable maintenance mode
- `POST /api/maintenance/disable` - Disable maintenance mode
- `POST /api/maintenance/message` - Update maintenance message

### Middleware Registration

The middleware is automatically applied to all web and API routes:

```php
// bootstrap/app.php
$middleware->web(append: [
    \App\Http\Middleware\CheckMaintenanceMode::class,
]);

$middleware->api(append: [
    \App\Http\Middleware\CheckMaintenanceMode::class,
]);
```

## Usage Scenarios

### Scenario 1: System Updates
1. Enable maintenance mode before deploying updates
2. Test changes as an admin while users see maintenance page
3. Disable maintenance mode once updates are verified

### Scenario 2: Testing Staff Features
1. Enable maintenance mode
2. Admin can access and test all staff features
3. Regular users cannot access the system
4. No need to create separate test environments

### Scenario 3: Beta Testing
1. Enable maintenance mode
2. Add beta testers' user IDs to whitelist
3. Beta testers can access the system
4. All other users see maintenance page

## Configuration Options

### Cache Driver
Maintenance mode state is stored in the cache. Make sure your cache is configured properly in `.env`:

```env
CACHE_STORE=database
```

### Allowed Roles
By default, these roles can bypass maintenance mode:
- `super_admin`
- `admin`

### Custom Maintenance Message
The default message can be changed in `/config/maintenance.php`:

```php
'default_message' => 'Your custom default message here',
```

## Debugging

### Check if Maintenance Mode is Active
Admins will see a header in the response:
- `X-Maintenance-Mode: bypassed-admin` (for admins)
- `X-Maintenance-Mode: bypassed-whitelist` (for whitelisted users)

### View Current Status
```bash
php artisan tinker
>>> Cache::get('system_maintenance_mode')
>>> Cache::get('system_maintenance_message')
```

### Clear Maintenance Mode (Emergency)
```bash
php artisan tinker
>>> Cache::forget('system_maintenance_mode')
>>> Cache::forget('system_maintenance_message')
```

## Security Considerations

1. **Admin Only**: Only admins and super admins can toggle maintenance mode
2. **Cache-Based**: State is stored in cache for fast access
3. **No Database**: No database queries on every request
4. **Role-Based**: Uses existing role system for authorization

## Best Practices

1. **Notify Users**: Announce maintenance windows in advance
2. **Custom Messages**: Use clear, informative maintenance messages
3. **Test First**: Always test as an admin before enabling for production
4. **Short Duration**: Keep maintenance windows as short as possible
5. **Monitor Logs**: Check logs after maintenance to catch any issues

## Troubleshooting

### Maintenance Mode Not Working
1. Check if cache is working: `php artisan cache:clear`
2. Verify middleware is registered in `bootstrap/app.php`
3. Check user role permissions

### Cannot Access as Admin
1. Verify you have `admin` or `super_admin` role
2. Check if you're logged in
3. Clear browser cache and cookies

### Whitelist Not Working
1. Verify user IDs are correct
2. Check `/config/maintenance.php` syntax
3. Ensure config cache is cleared: `php artisan config:clear`

## Future Enhancements

Possible improvements for future versions:

1. Schedule maintenance windows
2. Email notifications to users
3. Countdown timer on maintenance page
4. IP-based whitelisting
5. Maintenance mode for specific modules
6. Maintenance history/logs
