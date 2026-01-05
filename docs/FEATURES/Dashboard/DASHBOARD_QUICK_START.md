# Dashboard Quick Start Guide

## Overview

A modern, dark-themed dashboard has been implemented featuring project cards, statistics, and data visualization.

## Prerequisites

-   Laravel application running
-   Database configured
-   Migrations executed
-   Sample data seeded

## Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Sample Data

```bash
php artisan db:seed --class=ProjectSeeder
```

### 3. Start Development Servers

**Terminal 1 - Laravel Server:**

```bash
php artisan serve
```

**Terminal 2 - Vite (Frontend Assets):**

```bash
npm run dev
```

### 4. Access Dashboard

Open your browser and navigate to:

```
http://localhost:8001/dashboard
```

(Or the port your Laravel server is running on)

## Dashboard Features

### 📊 Statistics Summary

-   **Total Budget**: Sum of all project budgets with weekly change indicator
-   **Completed Tasks**: Total completed tasks with today's count

### 🎨 Project Cards

Each project displays:

-   Category badge (color-coded)
-   Project name
-   Completed tasks count
-   Total budget
-   Team member avatars
-   Member count

**Project Categories & Colors:**

-   💙 Finance (Blue)
-   🧡 Education (Orange)
-   💚 Healthcare (Green)
-   ❤️ Travel (Red)
-   💙 Logistics (Cyan)

### 📈 Projects This Year

-   Average tasks value
-   Average tasks per project
-   New projects count

### 📊 Yearly Profit Chart

Bar chart showing monthly budget data

## API Endpoints

All endpoints are under `/api/v1/dashboard/`:

### Get Statistics

```bash
GET /api/v1/dashboard/stats

Response:
{
  "total_budget": 601545,
  "budget_change_week": 0,
  "total_completed_tasks": 1174,
  "tasks_completed_today": 1174,
  "average_tasks_value": 344.72,
  "average_tasks_per_project": 290.8,
  "new_projects_count": 6
}
```

### Get Projects

```bash
GET /api/v1/dashboard/projects

Response: Array of project objects with:
- id, name, category, category_color
- completed_tasks, total_tasks, budget
- progress_percentage
- team_members (array)
- member_count
```

### Get Calendar Events

```bash
GET /api/v1/dashboard/calendar?month=2025-12

Response: Object with date keys and event arrays
```

### Get Yearly Profit

```bash
GET /api/v1/dashboard/yearly-profit?year=2025

Response: Array of monthly data
[
  { "month": "Jan", "value": 0, "tasks": 0 },
  { "month": "Feb", "value": 0, "tasks": 0 },
  ...
]
```

## Testing APIs with cURL

```bash
# Get dashboard stats
curl http://localhost:8001/api/v1/dashboard/stats | python3 -m json.tool

# Get projects
curl http://localhost:8001/api/v1/dashboard/projects | python3 -m json.tool

# Get calendar events
curl "http://localhost:8001/api/v1/dashboard/calendar?month=2025-12" | python3 -m json.tool

# Get yearly profit
curl "http://localhost:8001/api/v1/dashboard/yearly-profit?year=2025" | python3 -m json.tool
```

## Adding New Projects

### Via Tinker

```bash
php artisan tinker

# Create a new project
$project = App\Models\Project::create([
    'name' => 'My New Project',
    'category' => 'Finance',
    'category_color' => '#4169E1',
    'completed_tasks' => 10,
    'total_tasks' => 100,
    'budget' => 50000,
    'description' => 'Project description',
    'status' => 'active',
]);

# Attach team members
$user = App\Models\User::first();
$project->members()->attach($user->id, ['role' => 'lead']);
```

### Via API (Future Enhancement)

Create POST endpoint for adding projects through the UI.

## Customization

### Change Theme Colors

Edit `resources/views/dashboard.blade.php`:

1. **Background**: Find `bg-slate-950` classes
2. **Accent Color**: Find `lime-400` classes
3. **Cards**: Find `slate-800` classes

### Add New Categories

When creating projects, specify any category name and color:

```php
'category' => 'Marketing',
'category_color' => '#FF6B9D', // Pink
```

### Modify Statistics

Edit `app/Http/Controllers/DashboardController.php` in the `stats()` method.

## Troubleshooting

### Dashboard shows no data

1. Ensure migrations have run: `php artisan migrate`
2. Seed sample data: `php artisan db:seed --class=ProjectSeeder`
3. Check API response: `curl http://localhost:8001/api/v1/dashboard/projects`

### Styles not loading

1. Ensure Vite is running: `npm run dev`
2. Check browser console for errors
3. Clear cache: `php artisan cache:clear`

### API returns 404

1. Check routes: `php artisan route:list | grep dashboard`
2. Ensure controller exists: `app/Http/Controllers/DashboardController.php`
3. Clear route cache: `php artisan route:clear`

### Team member avatars not showing

-   Avatars use https://ui-avatars.com API
-   Requires internet connection
-   Fallback: Add local avatar images

## Browser Support

-   Chrome/Edge ✅
-   Firefox ✅
-   Safari ✅
-   Modern browsers with ES6+ support required

## Documentation

-   Design Specs: `/docs/Dashboard_UIUX.md`
-   Implementation Details: `/docs/DASHBOARD_IMPLEMENTATION_SUMMARY.md`
-   This Guide: `/docs/DASHBOARD_QUICK_START.md`

## What's Next?

-   [ ] Implement calendar view component
-   [ ] Add project detail pages
-   [ ] Implement search & filters
-   [ ] Add export functionality
-   [ ] Create project management CRUD
-   [ ] Add real-time notifications
-   [ ] Implement dark/light mode toggle

---

**Need Help?** Check the full implementation documentation in `/docs/DASHBOARD_IMPLEMENTATION_SUMMARY.md`
