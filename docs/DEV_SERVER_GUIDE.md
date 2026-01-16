# Development Server Quick Start

## Servers Running ✅

### Laravel Dev Server
```
URL: http://127.0.0.1:8000
Port: 8000
Status: RUNNING ✓
```

### Vite Dev Server  
```
URL: http://localhost:5174
Port: 5174 (5173 was in use, auto-switched)
Status: RUNNING ✓
```

## Browser Access

**Dashboard**: http://127.0.0.1:8000/admin/dashboard

The page should now load properly with:
- ✅ CSS from Vite (auto-loaded from dev server)
- ✅ JavaScript from Vite (with hot module replacement)
- ✅ React components bundled
- ✅ Tailwind CSS styling active

## If Page Still Won't Load

### 1. Clear Browser Cache
- Open DevTools: F12 or Cmd+Option+I
- Go to Application → Clear Site Data
- Hard refresh: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)

### 2. Check Vite Dev Server in DevTools Console
- DevTools → Console
- Look for any 404 errors loading from `localhost:5174`

### 3. Verify Servers Running
```bash
# Check Laravel
ps aux | grep "php artisan serve"

# Check Vite
ps aux | grep "vite"

# If not running, start them:
php artisan serve &
npm run dev &
```

### 4. Environment Configuration
The Laravel Vite plugin automatically:
- Uses dev server (http://localhost:5174) during development
- Uses manifest file (public/build/manifest.json) in production
- This is handled by the Laravel Vite plugin plugin - no ENV file needed

## What's Being Loaded

### From Vite Dev Server (Development)
- `resources/css/app.css`
- `resources/js/app.js`
- Hot reload for both CSS and JavaScript

### JavaScript Entry Point
`resources/js/app.js`:
```javascript
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

This loads:
- Bootstrap configuration (Axios, CSRF token)
- Alpine.js for interactive components
- All React components (automatically bundled)

### Styling
- Tailwind CSS (via `resources/css/app.css`)
- Alpine.js dark mode support
- System fonts (Inter, Plus Jakarta Sans)

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Page keeps loading | Kill servers: `pkill -f vite && pkill -f "php artisan"` then restart |
| CSS not loading | Check browser console for 404 errors on localhost:5174 |
| JavaScript errors | Check DevTools Console for errors from app.js bundle |
| React components not working | Ensure React plugin is in vite.config.js |
| Port already in use | Vite will auto-pick another port (shown in terminal) |

## Dashboard URL

**Access the dashboard at:**
```
http://127.0.0.1:8000/admin/dashboard
```

If you're accessing from a different host, use:
```
http://erp.saubhagyagroup.com/admin/dashboard
```
(Requires /etc/hosts entry mapping to 127.0.0.1)

---

**Note**: The Vite dev server automatically detects when you edit files and:
- Recompiles CSS instantly
- Hot-reloads JavaScript without page refresh
- Rebuilds React components

Just save and the browser updates automatically! 🚀
