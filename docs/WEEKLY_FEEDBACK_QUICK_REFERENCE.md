# Weekly Feedback Module - Quick Reference

## Quick Start

### For Employees

1. Go to **Weekly Feedback** in main navigation
2. See dashboard with Friday deadline countdown
3. Click **Submit Feedback** button
4. Fill three fields (min 10 characters each):
    - How are you feeling?
    - What progress did you make?
    - What can you improve?
5. Click **Submit Feedback**
6. View past submissions in **Feedback History**

### For Admins

1. Go to **Weekly Feedback** in admin sidebar
2. See dashboard with submission stats
3. Filter by week, status, or search employee names
4. Click employee name to view detailed feedback
5. Add management notes/response
6. View **Analytics Dashboard** for team insights

## Key Routes

| Route                                 | Purpose                      |
| ------------------------------------- | ---------------------------- |
| `/employee/feedback`                  | Dashboard (main entry point) |
| `/employee/feedback/create`           | Submission form              |
| `/employee/feedback/history`          | Past submissions             |
| `/admin/feedback`                     | Admin dashboard with list    |
| `/admin/feedback/{id}`                | View feedback details        |
| `/admin/feedback/analytics/dashboard` | Team insights                |

## Database

**Table:** `employee_feedback`

-   Stores feedback submissions with user_id and timestamps
-   `is_submitted` flag tracks if feedback is complete
-   `submitted_at` records submission time
-   `admin_notes` for manager responses

## Models & Methods

### EmployeeFeedback Model

```php
// Scopes
$feedback = EmployeeFeedback::thisWeek()->get();      // Current week
$feedback = EmployeeFeedback::submitted()->get();     // Submitted only
$feedback = EmployeeFeedback::pending()->get();       // Not submitted

// Static method
$feedback = EmployeeFeedback::getWeeklyFeedback($userId); // Current week feedback
```

### Controllers

-   **Employee\FeedbackController**

    -   `dashboard()` - Show main dashboard
    -   `create()` - Show form
    -   `store()` - Save feedback
    -   `show($id)` - View submitted feedback
    -   `history()` - View past submissions

-   **Admin\FeedbackController**
    -   `index()` - Dashboard with filters
    -   `show($id)` - View details
    -   `addNotes()` - Save management notes
    -   `analytics()` - Generate insights

## Validation Rules

All three fields are required and need minimum 10 characters:

-   `feelings`
-   `work_progress`
-   `self_improvements`

## Color Coding

| Section      | Color  | Icon           |
| ------------ | ------ | -------------- |
| Feelings     | Blue   | Smiley Face    |
| Progress     | Green  | Lightning Bolt |
| Improvements | Purple | Gear           |
| Admin Notes  | Amber  | Chat Bubble    |

## Important Notes

1. **One feedback per week** - System checks if feedback exists for current week

    - If exists: updates existing record
    - If new: creates new record

2. **Friday Deadline** - Dashboard shows countdown to Friday

    - `getDaysUntilFriday()` calculates remaining days
    - Week runs Monday-Sunday

3. **Separate from Complaint Box** - Different tables, routes, and navigation items

4. **Two Navigation Items**:
    - Employee: "Weekly Feedback" + "Complaint Box"
    - Admin: "Weekly Feedback" + "Staff Feedback"

## Files Created/Modified

### New Files

-   `app/Models/EmployeeFeedback.php`
-   `app/Http/Controllers/Admin/FeedbackController.php`
-   `app/Http/Controllers/Employee/FeedbackController.php`
-   `resources/views/employee/feedback/` (4 views)
-   `resources/views/admin/feedback/` (3 views)
-   `database/migrations/2025_12_10_083627_create_employee_feedback_table.php`

### Modified Files

-   `routes/web.php` - Added feedback routes
-   `resources/views/admin/layouts/app.blade.php` - Added nav link
-   `resources/views/employee/partials/nav.blade.php` - Updated nav items

## Testing Routes

```bash
# View all feedback routes
php artisan route:list | grep feedback

# Check model/controller syntax
php artisan tinker
> \App\Models\EmployeeFeedback::class
> \App\Http\Controllers\Admin\FeedbackController::class
> \App\Http\Controllers\Employee\FeedbackController::class
```

## Troubleshooting

**Routes not showing?**

-   Run `php artisan route:clear`

**Model errors?**

-   Check `app/Models/EmployeeFeedback.php` for syntax
-   Verify `user_id` foreign key relationship

**Migration failed?**

-   Check if table `employee_feedback` already exists
-   Run `php artisan migrate:reset` then `php artisan migrate`

**Views not rendering?**

-   Verify file paths in controller redirects
-   Check blade template syntax
-   Ensure all variables passed to views exist

## Performance Tips

1. **Use pagination** on history view (paginate() handles it)
2. **Weekly scope optimization** - Uses Carbon date calculations
3. **Index on user_id and submitted_at** for faster queries
4. **Lazy load management notes** - Only fetch when viewing detail

## Future Enhancements

-   Email reminders for Friday submissions
-   Sentiment analysis on feelings
-   Export reports (PDF/Excel)
-   Response templates for admins
-   Trend comparison (week-to-week)
-   Anonymous mode option
