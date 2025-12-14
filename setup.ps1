# Saubhagya Group ERP - Quick Setup Script (Windows)
# This script automates the initial setup process

Write-Host "🚀 Starting Saubhagya Group ERP Setup..." -ForegroundColor Green
Write-Host ""

# Check if .env exists
if (-not (Test-Path .env)) {
    Write-Host "📄 Creating .env file from .env.example..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    Write-Host "✅ .env file created" -ForegroundColor Green
} else {
    Write-Host "✅ .env file already exists" -ForegroundColor Green
}

# Install PHP dependencies
Write-Host ""
Write-Host "📦 Installing PHP dependencies..." -ForegroundColor Yellow
composer install --no-interaction

# Install JavaScript dependencies
Write-Host ""
Write-Host "📦 Installing JavaScript dependencies..." -ForegroundColor Yellow
npm install

# Generate application key
Write-Host ""
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
php artisan key:generate

# Ask for database setup
Write-Host ""
Write-Host "📊 Database Setup" -ForegroundColor Cyan
Write-Host "Please ensure your database is created and .env is configured with correct credentials."
$migrate = Read-Host "Do you want to run migrations now? (y/n)"
if ($migrate -eq "y" -or $migrate -eq "Y") {
    php artisan migrate
    Write-Host "✅ Migrations completed" -ForegroundColor Green
    
    $seed = Read-Host "Do you want to seed the database with initial data? (y/n)"
    if ($seed -eq "y" -or $seed -eq "Y") {
        php artisan db:seed
        Write-Host "✅ Database seeded with initial data" -ForegroundColor Green
        Write-Host ""
        Write-Host "📧 Admin credentials:" -ForegroundColor Cyan
        Write-Host "   Email: admin@saubhagyagroup.com"
        Write-Host "   Password: password"
        Write-Host ""
        Write-Host "⚠️  IMPORTANT: Change the password after first login!" -ForegroundColor Red
    }
}

# Create storage link
Write-Host ""
Write-Host "🔗 Creating storage symlink..." -ForegroundColor Yellow
php artisan storage:link

# Install Laravel Breeze for authentication
Write-Host ""
$breeze = Read-Host "Do you want to install Laravel Breeze for authentication? (y/n)"
if ($breeze -eq "y" -or $breeze -eq "Y") {
    composer require laravel/breeze --dev
    php artisan breeze:install blade
    php artisan migrate
    npm install
    Write-Host "✅ Laravel Breeze installed" -ForegroundColor Green
}

# Compile assets
Write-Host ""
Write-Host "🎨 Compiling frontend assets..." -ForegroundColor Yellow
npm run build

# Clear all caches
Write-Host ""
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
php artisan optimize:clear

Write-Host ""
Write-Host "✅ Setup Complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Update .env with your database credentials if not done"
Write-Host "2. Visit http://localhost:8000 after running 'php artisan serve'"
Write-Host "3. Login at /login with the credentials above"
Write-Host "4. Access admin panel at /admin/dashboard"
Write-Host ""
Write-Host "🚀 To start the development server:" -ForegroundColor Cyan
Write-Host "   php artisan serve"
Write-Host ""
Write-Host "🎨 To watch for asset changes:" -ForegroundColor Cyan
Write-Host "   npm run dev"
Write-Host ""
