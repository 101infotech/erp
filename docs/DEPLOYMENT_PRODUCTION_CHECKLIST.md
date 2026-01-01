# Production Deployment Checklist

**Document Version:** 1.0  
**Created:** 1 January 2026  
**For:** ERP System Production Deployment

---

## ⚠️ CRITICAL: Before Deployment

### 1. Environment Configuration (.env)

#### Update These Settings BEFORE Going Live

```env
# === CRITICAL CHANGES ===
APP_ENV=production          # Change from: local
APP_DEBUG=false            # Change from: true ⚠️ MUST BE FALSE
LOG_LEVEL=error            # Change from: debug

# === APP CONFIGURATION ===
APP_NAME="Your Company ERP"
APP_URL=https://yourdomain.com  # Update to production URL
APP_KEY=base64:...         # Run: php artisan key:generate

# === DATABASE (Production) ===
DB_CONNECTION=mysql
DB_HOST=your-production-db-host.com
DB_PORT=3306
DB_DATABASE=erp_production
DB_USERNAME=production_user
DB_PASSWORD=strong_production_password_here

# === SESSION & CACHE ===
SESSION_DRIVER=redis       # Better than database for production
SESSION_LIFETIME=120
CACHE_DRIVER=redis         # Better performance than file

# === QUEUE ===
QUEUE_CONNECTION=redis     # Better than database for production

# === REDIS CONFIGURATION ===
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# === MAIL CONFIGURATION (Production SMTP) ===
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailprovider.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# === SANCTUM (API) ===
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com

# === JIBBLE API (If using attendance tracking) ===
JIBBLE_CLIENT_ID=your_production_client_id
JIBBLE_CLIENT_SECRET=your_production_client_secret
JIBBLE_WORKSPACE_ID=your_workspace_id
JIBBLE_BASE_URL=https://workspace.prod.jibble.io/v1

# === AI INTEGRATION (If using) ===
OPENAI_API_KEY=your_production_api_key
AI_ENABLED=true
AI_PROVIDER=openai
```

---

## 🔐 Security Checklist

### Required Security Measures

- [ ] **APP_DEBUG=false** (CRITICAL - Hides error details)
- [ ] **APP_ENV=production** (Disables debug routes)
- [ ] **Strong APP_KEY** (Run `php artisan key:generate`)
- [ ] **HTTPS Enabled** (SSL certificate installed)
- [ ] **Database credentials** different from development
- [ ] **Mail credentials** using production SMTP
- [ ] **.env file** NOT in version control (.gitignore)
- [ ] **File permissions** correct (755 for directories, 644 for files)
- [ ] **Storage directory** writable (chmod 775 storage -R)

### Optional Security Enhancements

- [ ] **Security headers middleware** (See Quick Action Plan)
- [ ] **Rate limiting** on all routes
- [ ] **2FA for admins** (Future enhancement)
- [ ] **Activity logging** (Track admin actions)
- [ ] **Regular backups** automated

---

## 🚀 Pre-Deployment Commands

### On Production Server

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies (production only, optimized)
composer install --optimize-autoloader --no-dev

# 3. Generate application key (if first deployment)
php artisan key:generate

# 4. Run migrations
php artisan migrate --force

# 5. Create symbolic link for storage
php artisan storage:link

# 6. Cache configuration (PERFORMANCE BOOST)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 7. Restart queue workers (if using queues)
php artisan queue:restart

# 8. Set correct permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📊 Database Setup

### Production Database Best Practices

```sql
-- 1. Create production database
CREATE DATABASE erp_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Create production database user
CREATE USER 'erp_prod_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- 3. Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON erp_production.* TO 'erp_prod_user'@'localhost';
FLUSH PRIVILEGES;

-- 4. Never use root user in production!
```

### Backup Strategy

```bash
# Daily automated backup script
#!/bin/bash
BACKUP_DIR="/var/backups/erp"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u erp_backup_user -p erp_production > $BACKUP_DIR/erp_$DATE.sql
gzip $BACKUP_DIR/erp_$DATE.sql

# Delete backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete
```

---

## 🌐 Web Server Configuration

### Nginx Configuration (Recommended)

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/erp/public;
    index index.php index.html;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Laravel specific
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### Apache Configuration (.htaccess)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Redirect to index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

---

## 📧 Email Configuration

### Testing Email Setup

```bash
# Test email using tinker
php artisan tinker

# Send test email
\Mail::raw('Test email from production', function($msg) {
    $msg->to('admin@yourdomain.com')->subject('ERP Production Test');
});
```

### Common Email Providers

#### Gmail/Google Workspace
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=app-specific-password
MAIL_ENCRYPTION=tls
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Amazon SES
```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-ses-username
MAIL_PASSWORD=your-ses-password
MAIL_ENCRYPTION=tls
```

---

## 🔄 Queue & Scheduler Setup

### Queue Workers (Systemd Service)

Create `/etc/systemd/system/erp-worker.service`:

```ini
[Unit]
Description=ERP Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/erp/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable erp-worker
sudo systemctl start erp-worker
sudo systemctl status erp-worker
```

### Cron Job (Laravel Scheduler)

Add to crontab (`crontab -e`):
```bash
* * * * * cd /var/www/erp && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📈 Performance Optimization

### PHP Configuration (php.ini)

```ini
# Production optimizations
memory_limit = 256M
post_max_size = 20M
upload_max_filesize = 20M
max_execution_time = 60

# OPcache (CRITICAL for performance)
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=0
```

### Redis Installation

```bash
# Install Redis
sudo apt-get install redis-server

# Start Redis
sudo systemctl start redis
sudo systemctl enable redis

# Test Redis
redis-cli ping  # Should return "PONG"
```

### Database Optimization

```sql
-- Add indexes for performance (if not already added)
ALTER TABLE hrm_employees ADD INDEX idx_company_id (company_id);
ALTER TABLE hrm_employees ADD INDEX idx_department_id (department_id);
ALTER TABLE hrm_payroll_records ADD INDEX idx_employee_id (employee_id);
ALTER TABLE hrm_leave_requests ADD INDEX idx_employee_id (employee_id);
ALTER TABLE hrm_attendance_days ADD INDEX idx_employee_date (employee_id, date_bs);
```

---

## 🧪 Post-Deployment Testing

### Functional Tests

- [ ] **Login as admin** → Access dashboard
- [ ] **Login as employee** → Access employee portal
- [ ] **Create new employee** → Verify saved correctly
- [ ] **Generate payroll** → Verify calculations
- [ ] **Request leave** → Verify approval workflow
- [ ] **Submit expense claim** → Verify file upload
- [ ] **Check email notifications** → Verify emails sent
- [ ] **Test API endpoints** → Verify authentication

### Performance Tests

- [ ] **Page load times** < 1 second
- [ ] **Database queries** < 10 per page
- [ ] **Memory usage** < 128MB per request
- [ ] **Queue processing** working correctly
- [ ] **Cache working** (check with Redis CLI)

### Security Tests

- [ ] **HTTPS enforced** (HTTP redirects to HTTPS)
- [ ] **Security headers** present
- [ ] **Error pages** don't show sensitive info
- [ ] **File permissions** correct
- [ ] **Database credentials** secure

---

## 🚨 Monitoring & Logging

### Log Locations

```bash
# Application logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM logs
tail -f /var/log/php8.4-fpm.log
```

### Log Rotation

Create `/etc/logrotate.d/erp`:

```
/var/www/erp/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
    postrotate
        /usr/bin/php /var/www/erp/artisan cache:clear > /dev/null 2>&1
    endscript
}
```

---

## 🆘 Rollback Plan

### If Deployment Fails

```bash
# 1. Restore previous code version
git checkout previous-stable-tag

# 2. Restore database backup
mysql -u root -p erp_production < /var/backups/erp/erp_backup.sql

# 3. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
sudo systemctl restart erp-worker
```

---

## 📋 Final Checklist

### Before Going Live

- [ ] All `.env` variables updated for production
- [ ] APP_DEBUG=false confirmed
- [ ] Database backed up
- [ ] SSL certificate installed and working
- [ ] Email sending tested and working
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] File permissions set correctly
- [ ] All migrations run successfully
- [ ] Admin user created
- [ ] Test data removed (if any)
- [ ] Monitoring/logging configured
- [ ] Rollback plan documented and tested

### After Going Live

- [ ] Monitor logs for errors (first 24 hours)
- [ ] Check queue processing
- [ ] Verify email notifications
- [ ] Monitor database performance
- [ ] Check Redis cache hit rate
- [ ] Verify backup automation
- [ ] Test critical user workflows

---

**Document Maintained By:** DevOps Team  
**Last Updated:** 1 January 2026  
**Next Review:** After each major deployment
