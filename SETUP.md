# Saubhagya Group - Admin Panel Setup

## Overview

Centralized admin panel for managing 4 websites: Saubhagya Group, Brand Bird Agency, Saubhagya Ghar, and Saubhagya Estimate.

## Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & npm
-   MySQL/PostgreSQL database

## Installation Steps

### 1. Environment Configuration

```bash
cp .env.example .env
```

Update `.env` with your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saubhagya_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password

APP_URL=http://localhost:8000
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install

# JavaScript dependencies
npm install
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Database Migrations

```bash
php artisan migrate
```

### 5. Seed Initial Data

```bash
php artisan db:seed
```

This will create:

-   Admin user (email: admin@saubhagyagroup.com, password: password)
-   4 sites: Saubhagya Group, Brand Bird Agency, Saubhagya Ghar, Saubhagya Estimate

### 6. Create Storage Symbolic Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for file uploads.

### 7. Compile Frontend Assets

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

The admin panel will be available at: `http://localhost:8000/admin`

## Default Login Credentials

-   Email: `admin@saubhagyagroup.com`
-   Password: `password`

**⚠️ IMPORTANT: Change the password immediately after first login in production!**

## API Endpoints

All API endpoints are under `/api/v1/` prefix:

### Read Endpoints

-   `GET /api/v1/team-members?site_slug=saubhagya-group` - Get team members
-   `GET /api/v1/news?site_slug=brand-bird-agency` - Get news/media
-   `GET /api/v1/careers?site_slug=saubhagya-group` - Get career postings
-   `GET /api/v1/case-studies?site_slug=brand-bird-agency` - Get case studies
-   `GET /api/v1/blogs?site_slug=saubhagya-group` - Get blog posts

### Form Submission Endpoints

-   `POST /api/v1/contact` - Submit contact form
-   `POST /api/v1/booking` - Submit booking form

## Site Slugs

-   `saubhagya-group` - Saubhagya Group
-   `brand-bird-agency` - Brand Bird Agency
-   `saubhagya-ghar` - Saubhagya Ghar
-   `saubhagya-estimate` - Saubhagya Estimate

## Admin Panel Features

### Dashboard

-   Overview statistics
-   Recent contact and booking form submissions

### Sites Management

-   Create, edit, delete sites
-   Toggle active/inactive status

### Team Members (Saubhagya Group)

-   Manage team member profiles
-   Upload profile images
-   Social media links (Facebook, Twitter, LinkedIn)
-   Custom ordering

### News & Media (Brand Bird Agency)

-   Create and publish news articles
-   Featured images
-   Categories
-   Publish date scheduling

### Careers (Saubhagya Group)

-   Job postings
-   Job types (full-time, part-time, contract)
-   Application deadlines
-   Requirements and responsibilities

### Case Studies (Brand Bird Agency)

-   Project showcases
-   Client information
-   Image galleries
-   Tags and categories

### Blogs (Multi-site)

-   Blog post management
-   Featured images
-   Categories and tags
-   Author attribution

### Contact Forms

-   View submissions
-   Mark as read
-   Filter by site

### Booking Forms

-   View booking requests
-   Mark as contacted
-   Service type tracking

## File Uploads

Uploaded files are stored in `storage/app/public/` and accessible via `/storage/` URL:

-   Team member images: `/storage/team-members/`
-   News images: `/storage/news/`
-   Case study images: `/storage/case-studies/`
-   Blog images: `/storage/blogs/`

## Production Deployment Checklist

-   [ ] Change admin password
-   [ ] Update `APP_ENV` to `production`
-   [ ] Set `APP_DEBUG` to `false`
-   [ ] Configure proper database credentials
-   [ ] Run `php artisan config:cache`
-   [ ] Run `php artisan route:cache`
-   [ ] Run `php artisan view:cache`
-   [ ] Run `npm run build`
-   [ ] Set up SSL certificate
-   [ ] Configure CORS for API if needed
-   [ ] Set up backup strategy for database and uploads

## Authentication Setup (Required)

The system uses Laravel's built-in authentication. You need to install Laravel Breeze or Jetstream:

### Option 1: Laravel Breeze (Recommended - Lightweight)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run dev
```

### Option 2: Laravel Jetstream (Full-featured)

```bash
composer require laravel/jetstream
php artisan jetstream:install livewire
php artisan migrate
npm install && npm run dev
```

After installing authentication, routes like `/login`, `/register`, `/logout` will be available.

## Technology Stack

-   **Backend**: Laravel 11
-   **Frontend**: Blade Templates with Tailwind CSS 4.0
-   **Build Tool**: Vite
-   **Database**: MySQL/PostgreSQL
-   **File Storage**: Local filesystem (public disk)

## Support

For issues or questions, contact the development team.
