# Production Deployment Guide

> **Saubhagya ERP System**  
> **Version:** 1.0.0  
> **Last Updated:** January 16, 2026

---

## 🚀 Pre-Deployment Checklist

### 1. Environment Configuration

#### ✅ Required `.env` Updates

```bash
# Application Settings
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# Database (Update with production credentials)
DB_CONNECTION=mysql
DB_HOST=your-production-db-host
DB_PORT=3306
DB_DATABASE=your_production_database
DB_USERNAME=your_production_user
DB_PASSWORD=your_secure_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-secure-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="support@saubhagyagroup.com"

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=redis  # Recommended for production

# Queue (Use Redis for production)
QUEUE_CONNECTION=redis

# AI Services (Set your actual keys)
OPENAI_API_KEY=your-actual-openai-key
BRAND_BIRD_API_KEY=your-actual-brandbird-key

# Jibble Integration
JIBBLE_CLIENT_ID=your-jibble-client-id
JIBBLE_CLIENT_SECRET=your-jibble-client-secret
JIBBLE_WORKSPACE_ID=your-workspace-id

# Monitoring
NIGHTWATCH_ENV=production
```

#### ⚠️ Security Checklist
- [ ] Generate new `APP_KEY` using `php artisan key:generate`
- [ ] Update all API keys with production values
- [ ] Remove any test/demo credentials
- [ ] Ensure database credentials are secure
- [ ] Enable HTTPS/SSL certificates
- [ ] Set up firewall rules

---

## 🔧 Server Requirements

### Minimum Server Specifications
- **PHP:** 8.1 or higher
- **MySQL:** 8.0 or higher
- **Node.js:** 18.x or higher
- **RAM:** 2GB minimum (4GB recommended)
- **Disk Space:** 10GB minimum

### Required PHP Extensions
```bash
php -m | grep -E 'bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml|curl|gd|zip'
```

Required extensions:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD
- Zip

---

## 📦 Deployment Steps

### Step 1: Clone Repository
```bash
cd /var/www
git clone https://github.com/101infotech/erp.git saubhagya-erp
cd saubhagya-erp
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build
```

### Step 3: Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Edit with production values
nano .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --class=FinanceDataSeeder
php artisan db:seed --class=HrmDemoDataSeeder

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Set Permissions
```bash
# Set ownership
chown -R www-data:www-data /var/www/saubhagya-erp

# Set permissions
chmod -R 755 /var/www/saubhagya-erp
chmod -R 775 /var/www/saubhagya-erp/storage
chmod -R 775 /var/www/saubhagya-erp/bootstrap/cache
```

### Step 6: Configure Web Server

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name erp.saubhagyagroup.com;
    root /var/www/saubhagya-erp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName erp.saubhagyagroup.com
    DocumentRoot /var/www/saubhagya-erp/public

    <Directory /var/www/saubhagya-erp/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/erp-error.log
    CustomLog ${APACHE_LOG_DIR}/erp-access.log combined
</VirtualHost>
```

### Step 7: SSL Certificate
```bash
# Using Certbot (Let's Encrypt)
certbot --nginx -d erp.saubhagyagroup.com
# or
certbot --apache -d erp.saubhagyagroup.com
```

### Step 8: Setup Queue Worker
```bash
# Create systemd service
nano /etc/systemd/system/saubhagya-queue.service
```

Service file content:
```ini
[Unit]
Description=Saubhagya ERP Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/saubhagya-erp/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
systemctl enable saubhagya-queue
systemctl start saubhagya-queue
```

### Step 9: Setup Cron Jobs
```bash
crontab -e -u www-data
```

Add:
```cron
* * * * * cd /var/www/saubhagya-erp && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Security Hardening

### 1. Disable Directory Listing
Add to your web server config or `.htaccess`:
```apache
Options -Indexes
```

### 2. Hide Laravel Version
Remove from headers and error pages.

### 3. Rate Limiting
Already configured in `app/Http/Kernel.php`

### 4. CSRF Protection
Already enabled by default in Laravel

### 5. SQL Injection Prevention
Using Eloquent ORM and prepared statements

---

## 🔍 Post-Deployment Verification

### 1. Health Checks
```bash
# Check application status
curl https://erp.saubhagyagroup.com/health

# Check database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Check queue worker
systemctl status saubhagya-queue

# Check cron jobs
tail -f /var/log/syslog | grep artisan
```

### 2. Test Critical Features
- [ ] User login/logout
- [ ] Dashboard loading
- [ ] Finance module operations
- [ ] HRM module operations
- [ ] Lead management
- [ ] Report generation
- [ ] Email notifications
- [ ] File uploads

### 3. Performance Testing
```bash
# Test response time
curl -w "@curl-format.txt" -o /dev/null -s https://erp.saubhagyagroup.com

# Check memory usage
php artisan tinker --execute="echo memory_get_usage(true) / 1024 / 1024 . ' MB';"
```

---

## 📊 Monitoring & Logging

### Application Logs
```bash
# View logs
tail -f storage/logs/laravel.log

# Clear old logs
php artisan log:clear
```

### Database Monitoring
- Set up slow query logging
- Monitor connection pool
- Regular backups (daily recommended)

### Performance Monitoring
- Use Laravel Telescope (development only)
- Implement APM tools (New Relic, DataDog)
- Monitor queue length
- Track response times

---

## 🔄 Backup Strategy

### Database Backup
```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p database_name > /backups/erp_$DATE.sql
gzip /backups/erp_$DATE.sql

# Keep only last 30 days
find /backups -name "erp_*.sql.gz" -mtime +30 -delete
```

### File Backup
```bash
# Backup storage and uploads
tar -czf /backups/erp_storage_$DATE.tar.gz /var/www/saubhagya-erp/storage/app
```

### Automated Backups
Add to crontab:
```cron
0 2 * * * /path/to/backup-script.sh
```

---

## 🚨 Troubleshooting

### Common Issues

#### 500 Internal Server Error
```bash
# Check logs
tail -100 storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### Permission Denied
```bash
# Reset permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Queue Not Processing
```bash
# Restart queue worker
systemctl restart saubhagya-queue

# Check status
systemctl status saubhagya-queue
```

#### Database Connection Failed
```bash
# Test connection
mysql -h hostname -u username -p database_name

# Check .env settings
grep DB_ .env
```

---

## 📞 Support

### Emergency Contacts
- **Technical Lead:** [Contact Info]
- **System Admin:** [Contact Info]
- **DevOps Team:** [Contact Info]

### Documentation
- [Project Documentation](../INDEX.md)
- [API Documentation](../Backend/)
- [Frontend Guide](../Frontend/)

---

## ✅ Deployment Completion Checklist

- [ ] All environment variables configured
- [ ] Database migrated successfully
- [ ] Assets compiled and optimized
- [ ] SSL certificate installed
- [ ] Queue worker running
- [ ] Cron jobs configured
- [ ] Permissions set correctly
- [ ] Backups configured
- [ ] Monitoring enabled
- [ ] All tests passing
- [ ] Documentation updated
- [ ] Team notified

---

**Deployed By:** ________________  
**Date:** ________________  
**Version:** 1.0.0
