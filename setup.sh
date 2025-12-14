#!/bin/bash

# Saubhagya Group ERP - Quick Setup Script
# This script automates the initial setup process

echo "🚀 Starting Saubhagya Group ERP Setup..."
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "📄 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "✅ .env file already exists"
fi

# Install PHP dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install --no-interaction

# Install JavaScript dependencies
echo ""
echo "📦 Installing JavaScript dependencies..."
npm install

# Generate application key
echo ""
echo "🔑 Generating application key..."
php artisan key:generate

# Ask for database setup
echo ""
echo "📊 Database Setup"
echo "Please ensure your database is created and .env is configured with correct credentials."
read -p "Do you want to run migrations now? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate
    echo "✅ Migrations completed"
    
    read -p "Do you want to seed the database with initial data? (y/n) " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed
        echo "✅ Database seeded with initial data"
        echo ""
        echo "📧 Admin credentials:"
        echo "   Email: admin@saubhagyagroup.com"
        echo "   Password: password"
        echo ""
        echo "⚠️  IMPORTANT: Change the password after first login!"
    fi
fi

# Create storage link
echo ""
echo "🔗 Creating storage symlink..."
php artisan storage:link

# Install Laravel Breeze for authentication
echo ""
read -p "Do you want to install Laravel Breeze for authentication? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    composer require laravel/breeze --dev
    php artisan breeze:install blade
    php artisan migrate
    npm install
    echo "✅ Laravel Breeze installed"
fi

# Compile assets
echo ""
echo "🎨 Compiling frontend assets..."
npm run build

# Clear all caches
echo ""
echo "🧹 Clearing caches..."
php artisan optimize:clear

echo ""
echo "✅ Setup Complete!"
echo ""
echo "📝 Next Steps:"
echo "1. Update .env with your database credentials if not done"
echo "2. Visit http://localhost:8000 after running 'php artisan serve'"
echo "3. Login at /login with the credentials above"
echo "4. Access admin panel at /admin/dashboard"
echo ""
echo "🚀 To start the development server:"
echo "   php artisan serve"
echo ""
echo "🎨 To watch for asset changes:"
echo "   npm run dev"
echo ""
