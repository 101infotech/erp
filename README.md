# Saubhagya Group - Centralized Admin Panel

A centralized content management system for managing multiple websites under the Saubhagya Group umbrella.

## Overview

This Laravel application serves as a unified admin panel for managing content across 4 different websites:

-   **Saubhagya Group** - Corporate website
-   **Brand Bird Agency** - Marketing and branding agency
-   **Saubhagya Ghar** - Real estate and property
-   **Saubhagya Estimate** - Estimation and costing services

## Key Features

✅ **Multi-Site Management** - Single admin panel for all websites  
✅ **RESTful API** - JSON APIs for external sites to consume content  
✅ **File Upload Support** - Image and media file management  
✅ **Unified Authentication** - Single admin login for all sites  
✅ **Form Management** - Contact and booking form submissions  
✅ **Content Modules** - Team members, news, careers, case studies, blogs

## Tech Stack

-   **Backend**: Laravel 11
-   **Frontend**: Blade Templates + Tailwind CSS 4.0
-   **Build Tool**: Vite
-   **Database**: MySQL/PostgreSQL
-   **File Storage**: Local filesystem (public disk)

## Quick Start

### Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & npm
-   MySQL/PostgreSQL

### Installation

1. **Clone and setup**

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

2. **Configure database** in `.env`

```env
DB_CONNECTION=mysql
DB_DATABASE=saubhagya_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

3. **Run migrations and seed data**

```bash
php artisan migrate
php artisan db:seed
```

4. **Create storage link and compile assets**

```bash
php artisan storage:link
npm run dev
```

5. **Start server**

```bash
php artisan serve
```

### Default Credentials

-   **Email**: admin@saubhagyagroup.com
-   **Password**: password

⚠️ **Change password after first login!**

## Authentication Setup Required

Before using the admin panel, install Laravel Breeze:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run dev
```

## Project Structure

```
app/Http/Controllers/
├── Admin/          # Admin panel controllers (CRUD)
└── Api/            # API controllers (JSON responses)

resources/views/admin/
├── layouts/        # Master layout with sidebar
├── dashboard.blade.php
├── sites/
├── team-members/
├── news-media/
├── careers/
├── case-studies/
├── blogs/
├── contact-forms/
└── booking-forms/

database/migrations/
├── sites, team_members, news_media
├── careers, case_studies, blogs
└── contact_forms, booking_forms
```

## API Endpoints

All endpoints are under `/api/v1/` prefix:

### Read Endpoints

```
GET /api/v1/team-members?site_slug=saubhagya-group
GET /api/v1/news?site_slug=brand-bird-agency
GET /api/v1/careers?site_slug=saubhagya-group
GET /api/v1/case-studies?site_slug=brand-bird-agency
GET /api/v1/blogs?site_slug=saubhagya-group
```

### Form Submission

```
POST /api/v1/contact
POST /api/v1/booking
```

## Documentation

-   **[Setup Guide](SETUP.md)** - Complete installation and configuration
-   **[Architecture](docs/ARCHITECTURE.md)** - System design and structure
-   **[API Documentation](docs/API.md)** - Full API reference with examples

## Admin Panel Features

### Dashboard

-   Overview statistics for all sites
-   Recent contact and booking submissions
-   Quick access to all modules

### Content Management

-   **Sites** - Manage website configurations
-   **Team Members** - Profile, images, social links
-   **News & Media** - Articles with featured images
-   **Careers** - Job postings with deadlines
-   **Case Studies** - Project showcases with galleries
-   **Blogs** - Multi-site blog posts
-   **Forms** - Contact and booking submissions

### File Management

-   Image uploads for team members, news, case studies, blogs
-   Gallery support for case studies
-   Public storage with URL access

## Development

```bash
# Start development server
php artisan serve

# Compile assets (watch mode)
npm run dev

# Build for production
npm run build

# Clear all caches
php artisan optimize:clear
```

## Production Deployment

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## Site Slugs

Use these slugs in API calls to filter content:

-   `saubhagya-group` - Saubhagya Group
-   `brand-bird-agency` - Brand Bird Agency
-   `saubhagya-ghar` - Saubhagya Ghar
-   `saubhagya-estimate` - Saubhagya Estimate

## Testing API

### cURL Example

```bash
curl "http://localhost:8000/api/v1/team-members?site_slug=saubhagya-group"
```

### JavaScript Example

```javascript
fetch("http://localhost:8000/api/v1/news?site_slug=brand-bird-agency")
    .then((res) => res.json())
    .then((data) => console.log(data));
```

## Contributing

This is a private project for Saubhagya Group. For development questions, refer to:

-   [Laravel Documentation](https://laravel.com/docs)
-   [Tailwind CSS](https://tailwindcss.com/docs)

## License

Proprietary - © 2024 Saubhagya Group. All rights reserved.
