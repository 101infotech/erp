# Content Management System Refactor - Complete Guide

## Overview

Refactored the multi-site content management system to remove workspace switching and implement a simpler, card-based interface with site-specific dashboards.

## What Changed

### 1. Navigation Simplification ✅

**Before:**

-   Workspace switcher dropdown in sidebar
-   Content Management dropdown with workspace-based links
-   Team Members link in sidebar
-   Submissions section with Contact Forms and Booking Forms

**After:**

-   Simple logo header
-   Clean navigation: Dashboard → Sites → HRM Module → User Management
-   No workspace switching
-   All content management accessible through site cards

### 2. Sites Page Redesign ✅

**Before:**

-   Table layout showing name, slug, domain, status
-   Simple Edit/Delete buttons

**After:**

-   **Card Grid Layout** (3 columns on desktop)
-   Each card shows:
    -   Site name, slug, domain with external link
    -   Active/Inactive status badge
    -   Description (if available)
    -   **Content Summary Statistics** in grid:
        -   Services count
        -   Blogs count
        -   Case Studies count
        -   Companies count
        -   Media count
        -   Hirings count
        -   Team count
        -   Contact Forms count
        -   Booking Forms count
        -   Meeting Requests count
    -   **"Manage Content" button** → Links to site dashboard
    -   Edit Site and Delete buttons

### 3. Site-Specific Dashboard ✅

**New Route:** `/admin/sites/{site}/dashboard`

Each site now has its own dedicated content management dashboard with color-coded cards:

-   **Services** (Blue) - For Saubhagya Ghar, Brand Bird
-   **Blogs** (Purple) - For Saubhagya Ghar
-   **Case Studies** (Orange) - For Brand Bird
-   **Companies** (Indigo) - For Saubhagya Group
-   **News & Media** (Pink) - For Saubhagya Group
-   **Job Openings** (Teal) - For Saubhagya Group
-   **Team Members** (Cyan) - All sites
-   **Contact Submissions** (Green) - All sites
-   **Booking Submissions** (Yellow) - All sites except Saubhagya Group
-   **Meeting Requests** (Red) - Saubhagya Group only

Each card displays:

-   Icon in colored background
-   Current count
-   Content type name and description
-   "Manage [Type]" button

### 4. Routes Architecture ✅

**Before:**

```php
Route::prefix('{workspace}')->middleware('workspace')->group(function () {
    Route::resource('services', ServiceController::class);
    // ... other routes
});
```

**After:**

```php
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // Site dashboard
    Route::get('sites/{site}/dashboard', [SiteController::class, 'dashboard'])->name('sites.dashboard');

    // Content routes with site filtering
    Route::resource('services', ServiceController::class);
    Route::resource('hirings', HiringController::class);
    // ... other content routes
});
```

**URL Pattern Change:**

-   Before: `/{workspace}/services` (e.g., `/saubhagya-group/services`)
-   After: `/admin/services?site={id}` (e.g., `/admin/services?site=1`)

### 5. Controller Updates ✅ (ServiceController completed)

**Before:**

```php
public function index()
{
    $siteId = session('selected_site_id');
    $services = Service::when($siteId, fn($q) => $q->where('site_id', $siteId))->get();
    return view('admin.services.index', compact('services', 'sites'));
}
```

**After:**

```php
public function index(Request $request)
{
    $siteId = $request->query('site');
    $query = Service::with('site');

    if ($siteId) {
        $query->where('site_id', $siteId);
    }

    $services = $query->orderBy('order')->latest()->paginate(15);
    $sites = Site::where('is_active', true)->get();
    $selectedSite = $siteId ? Site::find($siteId) : null;

    return view('admin.services.index', compact('services', 'sites', 'selectedSite'));
}
```

**Key Changes:**

-   Use `$request->query('site')` instead of `session('selected_site_id')`
-   Pass `$selectedSite` to view for context
-   Redirect after create/update/delete with `['site' => $siteId]` parameter

## Controllers to Update

### Completed:

-   ✅ SiteController - Added `dashboard()` method
-   ✅ ServiceController - Fully refactored

### Pending:

-   ⏳ HiringController
-   ⏳ CompanyListController
-   ⏳ ScheduleMeetingController
-   ⏳ TeamMemberController
-   ⏳ NewsMediaController
-   ⏳ CareerController
-   ⏳ CaseStudyController
-   ⏳ BlogController
-   ⏳ ContactFormController
-   ⏳ BookingFormController

## Database Changes

No database migrations needed - using existing structure with `site_id` foreign keys.

## Middleware Changes

-   Removed dependency on `SetWorkspace` middleware
-   All routes now under `admin` middleware only
-   Site filtering done via query parameter instead of session

## Implementation Checklist

### Phase 1: Navigation & UI ✅

-   [x] Remove workspace switcher from sidebar
-   [x] Simplify sidebar navigation
-   [x] Convert sites index to card layout
-   [x] Add summary statistics to site cards
-   [x] Create site dashboard view
-   [x] Update routes configuration

### Phase 2: Backend Refactoring (In Progress)

-   [x] Add `dashboard()` method to SiteController
-   [x] Update ServiceController with site query parameter
-   [ ] Update HiringController
-   [ ] Update CompanyListController
-   [ ] Update ScheduleMeetingController
-   [ ] Update remaining content controllers

### Phase 3: View Updates (Not Started)

-   [ ] Update services index/create/edit views
-   [ ] Update hirings views
-   [ ] Update companies-list views
-   [ ] Update schedule-meetings views
-   [ ] Update existing content views (blogs, case-studies, etc.)

## Benefits of This Approach

1. **Simpler User Experience**

    - No workspace switching confusion
    - Direct access to site content via cards
    - Visual content summary at a glance

2. **Cleaner Architecture**

    - No session-based filtering
    - Query parameters are stateless and shareable
    - Easier to bookmark specific filtered views

3. **Better Navigation**

    - Less cluttered sidebar
    - Site-specific dashboards provide focused context
    - Color-coded content types for quick recognition

4. **Scalability**
    - Easy to add new content types per site
    - Conditional rendering based on site slug
    - Flexible routing structure

## Usage Examples

### Accessing Site Dashboard

```
/admin/sites/1/dashboard  (Saubhagya Group)
/admin/sites/2/dashboard  (Brand Bird Agency)
/admin/sites/3/dashboard  (Saubhagya Ghar)
/admin/sites/4/dashboard  (Saubhagya Estimate)
```

### Managing Content

From site dashboard, click "Manage Services" → redirects to:

```
/admin/services?site=1
```

### Creating New Content

When creating from filtered view, site_id is pre-selected:

```
/admin/services/create?site=1
```

## Next Steps

1. Complete controller updates for all content types
2. Update existing Blade views with new routing
3. Create missing CRUD views (services create/edit, hirings, etc.)
4. Test all CRUD operations with site filtering
5. Remove SetWorkspace middleware if no longer needed
6. Update documentation with final implementation details

## Files Modified

-   `resources/views/admin/layouts/app.blade.php` - Sidebar navigation
-   `resources/views/admin/sites/index.blade.php` - Card layout
-   `resources/views/admin/sites/dashboard.blade.php` - New file
-   `app/Http/Controllers/Admin/SiteController.php` - Added dashboard method
-   `app/Http/Controllers/Admin/ServiceController.php` - Query parameter filtering
-   `routes/web.php` - Route structure refactoring
