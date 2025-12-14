# Saubhagya Group - Admin Panel Architecture

## Project Overview

This is a centralized admin panel system for Saubhagya Group that manages content for 4 different websites:

1. **Saubhagya Group** - Corporate website
2. **Brand Bird Agency** - Marketing and branding agency
3. **Saubhagya Ghar** - Real estate and property
4. **Saubhagya Estimate** - Estimation and costing services

## Architecture Design

### Multi-Site Strategy

The system uses a **single database, multi-site** architecture:

-   One admin panel controls all sites
-   Each content item is tagged with a `site_id`
-   API endpoints filter content by `site_slug` parameter
-   External websites consume data via RESTful APIs

### Database Schema

#### Core Tables

**sites**

-   Stores all website configurations
-   Fields: id, name, slug, domain, description, is_active
-   Slug is used in API calls for filtering

**team_members**

-   Team member profiles for Saubhagya Group
-   Includes bio, image, social links
-   Custom ordering support

**news_media**

-   News articles and media content
-   Category-based organization
-   Publishing schedule support

**careers**

-   Job postings with application deadlines
-   Job type classification (full-time, part-time, contract)
-   Requirements and responsibilities

**case_studies**

-   Project showcases for Brand Bird Agency
-   Client information
-   Gallery support (JSON array)
-   Tags for categorization

**blogs**

-   Multi-site blog system
-   Category and tag support
-   Author attribution

**contact_forms**

-   Contact form submissions
-   Status tracking (new, read, responded)
-   IP address logging

**booking_forms**

-   Booking requests with detailed information
-   Service type tracking
-   Budget and project details

**media_files**

-   Centralized media library
-   File metadata storage
-   MIME type tracking

### Application Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Admin/              # Admin panel controllers
│       │   ├── DashboardController.php
│       │   ├── SiteController.php
│       │   ├── TeamMemberController.php
│       │   ├── NewsMediaController.php
│       │   ├── CareerController.php
│       │   ├── CaseStudyController.php
│       │   ├── BlogController.php
│       │   ├── ContactFormController.php
│       │   └── BookingFormController.php
│       └── Api/                # API controllers for external sites
│           ├── TeamMemberController.php
│           ├── NewsMediaController.php
│           ├── CareerController.php
│           ├── CaseStudyController.php
│           ├── BlogController.php
│           ├── ContactFormController.php
│           └── BookingFormController.php
└── Models/                     # Eloquent models
    ├── Site.php
    ├── TeamMember.php
    ├── NewsMedia.php
    ├── Career.php
    ├── CaseStudy.php
    ├── Blog.php
    ├── ContactForm.php
    ├── BookingForm.php
    └── MediaFile.php
```

### Routes Architecture

#### Admin Routes (Protected)

```
/admin/dashboard
/admin/sites
/admin/team-members
/admin/news-media
/admin/careers
/admin/case-studies
/admin/blogs
/admin/contact-forms
/admin/booking-forms
```

All admin routes are protected by the `auth` middleware.

#### API Routes (Public)

```
GET  /api/v1/team-members?site_slug={slug}
GET  /api/v1/news?site_slug={slug}
GET  /api/v1/careers?site_slug={slug}
GET  /api/v1/case-studies?site_slug={slug}
GET  /api/v1/blogs?site_slug={slug}
POST /api/v1/contact
POST /api/v1/booking
```

All API routes return JSON responses and support pagination.

### Frontend Architecture

#### Admin Panel UI

-   **Layout**: Single master layout (`resources/views/admin/layouts/app.blade.php`)
-   **Navigation**: Sidebar with active state indicators
-   **Color Scheme**: Red accent (#DC2626) matching Saubhagya brand
-   **Styling**: Tailwind CSS 4.0 utility classes
-   **Responsiveness**: Mobile-first responsive design

#### View Structure

```
resources/views/admin/
├── layouts/
│   └── app.blade.php           # Master layout with sidebar
├── dashboard.blade.php         # Dashboard with stats
├── sites/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── team-members/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── contact-forms/
│   ├── index.blade.php
│   └── show.blade.php
└── booking-forms/
    ├── index.blade.php
    └── show.blade.php
```

### File Upload Strategy

#### Storage Configuration

-   **Driver**: Local filesystem
-   **Disk**: `public` (accessible via URL)
-   **Base Path**: `storage/app/public/`
-   **Public URL**: `/storage/`

#### Upload Directories

```
storage/app/public/
├── team-members/       # Team member profile images
├── news/              # News featured images
├── case-studies/      # Case study galleries
└── blogs/             # Blog post featured images
```

#### File Handling

1. Upload validation (file type, size)
2. Storage using Laravel's `Storage` facade
3. Automatic cleanup of old files on update/delete
4. Public URL generation via `Storage::url()`

### API Integration Guide

#### For External Websites

**1. Fetching Team Members**

```javascript
fetch("http://your-admin.com/api/v1/team-members?site_slug=saubhagya-group")
    .then((res) => res.json())
    .then((data) => console.log(data));
```

**2. Fetching News**

```javascript
fetch(
    "http://your-admin.com/api/v1/news?site_slug=brand-bird-agency&category=press"
)
    .then((res) => res.json())
    .then((data) => console.log(data));
```

**3. Submitting Contact Form**

```javascript
fetch("http://your-admin.com/api/v1/contact", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        site_slug: "saubhagya-group",
        name: "John Doe",
        email: "john@example.com",
        message: "Hello!",
    }),
})
    .then((res) => res.json())
    .then((data) => console.log(data));
```

### Authentication Flow

```
1. User visits /login
2. Laravel authentication middleware validates credentials
3. Session created on successful login
4. User redirected to /admin/dashboard
5. All admin routes check auth middleware
6. Logout destroys session and redirects to login
```

### Data Flow Diagram

```
External Website (Frontend)
         ↓
    API Request (GET /api/v1/team-members?site_slug=X)
         ↓
    API Controller
         ↓
    Eloquent Model (with site filtering)
         ↓
    Database Query
         ↓
    JSON Response
         ↓
External Website (Display Data)


Admin Panel Flow:
         ↓
Admin Login → Dashboard → Manage Content
         ↓
    Controller
         ↓
    Eloquent Model
         ↓
    Database
         ↓
    Redirect with Success Message
```

### Security Considerations

1. **Admin Panel**

    - Protected by Laravel authentication
    - CSRF protection on all forms
    - Password hashing with bcrypt

2. **API Endpoints**

    - Rate limiting (configure in `RouteServiceProvider`)
    - Input validation on all POST requests
    - IP address logging for form submissions

3. **File Uploads**
    - File type validation
    - Size limits
    - Stored outside web root (except public disk)
    - No direct execution of uploaded files

### Performance Optimizations

1. **Database**

    - Indexed foreign keys
    - Indexed slug fields for filtering
    - Eager loading relationships to avoid N+1 queries

2. **API Responses**

    - Pagination (15 items per page)
    - Only return published/active content
    - Efficient filtering by site_slug

3. **Caching Strategy** (Recommended for Production)
    - Cache API responses (5-10 minutes)
    - Clear cache on content updates
    - Use Redis for session/cache storage

### Extensibility

#### Adding New Content Modules

1. Create migration: `php artisan make:migration create_new_table`
2. Create model: `php artisan make:model NewModule`
3. Add relationship to `Site` model
4. Create admin controller: `php artisan make:controller Admin/NewModuleController`
5. Create API controller: `php artisan make:controller Api/NewModuleController`
6. Add routes to `web.php` and `api.php`
7. Create Blade views in `resources/views/admin/new-module/`

#### Adding New Sites

Simply add via admin panel:

1. Go to Sites → Add New Site
2. Set name, slug, domain
3. Content for this site will be automatically available via API using the slug

### Testing Strategy

#### Manual Testing Checklist

-   [ ] Admin login/logout
-   [ ] CRUD operations for all modules
-   [ ] File uploads (team members, news, case studies, blogs)
-   [ ] Form submissions (contact, booking)
-   [ ] API endpoints with different site_slug values
-   [ ] Pagination on list views
-   [ ] Validation errors display correctly

#### Automated Testing (Future)

-   Unit tests for models
-   Feature tests for controllers
-   API endpoint tests
-   File upload tests

### Deployment Strategy

#### Development

```bash
php artisan serve
npm run dev
```

#### Production

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Server Requirements

-   PHP 8.2+
-   Composer
-   MySQL 8.0+ or PostgreSQL
-   Nginx/Apache with URL rewriting
-   SSL certificate for HTTPS

### Maintenance

#### Regular Tasks

1. Database backups (daily recommended)
2. Log file monitoring
3. Storage cleanup for orphaned files
4. Security updates for Laravel and dependencies

#### Monitoring

-   Error logs: `storage/logs/laravel.log`
-   Database queries (via Laravel Debugbar in development)
-   API response times
-   Storage disk usage

## Future Enhancements

### Planned Features

1. **Advanced Media Library**

    - Drag-and-drop uploads
    - Image cropping/resizing
    - CDN integration

2. **Email Notifications**

    - Lead submission confirmations
    - Admin notifications for new leads
    - Weekly lead summary emails

3. **Analytics Dashboard**

    - Page views per site
    - Form submission trends
    - Popular content tracking
    - Lead conversion metrics

4. **Content Versioning**

    - Revision history
    - Draft/publish workflow
    - Scheduled publishing

5. **Multi-Admin Roles**

    - Role-based access control
    - Site-specific permissions
    - Activity logging

6. **SEO Tools**

    - Meta tags management
    - Sitemap generation
    - Schema markup

7. **Enhanced API Features**
    - API authentication with Laravel Sanctum
    - Webhook support for real-time notifications
    - GraphQL endpoint option
    - API usage analytics

## API Implementation (v1.0)

### Overview

Production-ready RESTful API for frontend integration, specifically designed for Brand Bird Agency with extensibility for other Saubhagya Group brands.

### Endpoints

#### Lead Management

-   **POST** `/api/v1/leads` - Submit new lead (rate limited: 10/min)
    -   Auto-captures IP, user agent, timestamp
    -   Generates unique reference number
    -   Flexible meta field for custom data

#### Content Retrieval (GET only)

-   **GET** `/api/v1/services?brand=brand-bird-agency` - List services
-   **GET** `/api/v1/case-studies?brand=brand-bird-agency` - List case studies
-   **GET** `/api/v1/case-studies/{slug}` - Single case study details
-   **GET** `/api/v1/team?brand=brand-bird-agency` - List team members

### Response Format

All endpoints return consistent JSON structure:

```json
{
  "status": "success|error",
  "message": "Human-readable description",
  "data": { ... }
}
```

### Security Features

-   ✅ Rate limiting on lead submissions
-   ✅ Input validation and sanitization
-   ✅ IP tracking for abuse prevention
-   ✅ SQL injection protection (Eloquent ORM)
-   🔲 CORS configuration (requires setup)
-   🔲 reCAPTCHA integration (recommended)
-   🔲 Honeypot spam prevention (recommended)

### Documentation

-   Full API docs: `docs/API_Documentation.md`
-   Implementation summary: `docs/API_IMPLEMENTATION_SUMMARY.md`
-   Testing guide: `docs/API_TESTING.md`

### Database Tables

**leads** (new)

-   Stores all lead submissions across all brands
-   Fields: site_id, source, first_name, last_name, email, phone, message, meta (JSON), status, ip_address, user_agent
-   Indexes on: site_id, status, created_at
-   Status workflow: new → contacted → qualified → converted/rejected

## Troubleshooting

### Common Issues

**Issue**: Images not displaying

-   **Solution**: Run `php artisan storage:link`

**Issue**: 404 on admin routes

-   **Solution**: Clear route cache with `php artisan route:clear`

**Issue**: CSRF token mismatch

-   **Solution**: Clear config cache with `php artisan config:clear`

**Issue**: Database connection error

-   **Solution**: Check `.env` database credentials

## Contact & Support

For development questions or issues, refer to:

-   Laravel Documentation: https://laravel.com/docs
-   Tailwind CSS: https://tailwindcss.com/docs
-   Project Repository: [Add your repo URL]
