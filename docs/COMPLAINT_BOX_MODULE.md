# Complaint Box Module Implementation

## Overview

The Complaint Box module provides a confidential feedback system for staff members to submit concerns, suggestions, or complaints. While staff members experience the submission as anonymous, administrators can view submitter information to properly address issues.

## Features

### For Employees

-   **Anonymous Submission Experience**: Staff can submit feedback without their identity being prominently displayed
-   **Category Selection**: Choose from predefined categories (Workplace, Management, Harassment, Safety, etc.)
-   **Status Tracking**: View the status of submitted feedback (Pending, Reviewing, Resolved, Dismissed)
-   **Response Viewing**: See management responses to their feedback
-   **Privacy Assurance**: Clear messaging about confidentiality

### For Administrators

-   **Full Visibility**: View all complaints with submitter information (subtly displayed)
-   **Status Management**: Update complaint status (Pending → Reviewing → Resolved/Dismissed)
-   **Priority Assignment**: Set priority levels (Low, Medium, High)
-   **Internal Notes**: Add confidential notes visible only to administrators
-   **Filtering & Search**: Filter by status, priority, category, or search by keywords
-   **Stats Dashboard**: Quick overview of pending, reviewing, resolved, and high-priority complaints

## Database Structure

### Complaints Table

```sql
- id (primary key)
- user_id (foreign key to users table)
- subject (string)
- description (text)
- category (string, nullable)
- status (enum: pending, reviewing, resolved, dismissed)
- priority (enum: low, medium, high)
- admin_notes (text, nullable)
- resolved_at (timestamp, nullable)
- created_at, updated_at
```

## File Structure

### Models

-   `app/Models/Complaint.php` - Main complaint model with relationships and scopes

### Controllers

-   `app/Http/Controllers/Admin/ComplaintController.php` - Admin management functions
-   `app/Http/Controllers/Employee/ComplaintController.php` - Employee submission and viewing

### Views

#### Admin Views

-   `resources/views/admin/complaints/index.blade.php` - List all complaints with filters and stats
-   `resources/views/admin/complaints/show.blade.php` - Detailed complaint view with actions

#### Employee Views

-   `resources/views/employee/complaints/index.blade.php` - Employee's submitted feedback list
-   `resources/views/employee/complaints/create.blade.php` - Submit new feedback form
-   `resources/views/employee/complaints/show.blade.php` - View individual feedback details

### Routes

#### Employee Routes (Prefix: `/employee`)

```php
GET    /employee/complaints                  - List my feedback
GET    /employee/complaints/create           - Submit new feedback form
POST   /employee/complaints                  - Store new feedback
GET    /employee/complaints/{complaint}      - View feedback details
```

#### Admin Routes (Prefix: `/admin`)

```php
GET    /admin/complaints                     - List all complaints
GET    /admin/complaints/{complaint}         - View complaint details
PATCH  /admin/complaints/{complaint}/status  - Update status
PATCH  /admin/complaints/{complaint}/priority - Update priority
POST   /admin/complaints/{complaint}/notes   - Add admin notes
DELETE /admin/complaints/{complaint}         - Delete complaint
```

## Usage Guide

### For Employees

#### Submitting Feedback

1. Navigate to **Employee Portal** → **Feedback**
2. Click **Submit New Feedback**
3. Select a category (optional)
4. Enter subject and detailed description
5. Click **Submit Feedback**
6. Confirmation message appears assuring anonymity

#### Viewing My Feedback

1. Go to **Employee Portal** → **Feedback**
2. View list of all submitted feedback
3. Filter by status if needed
4. Click **View Details** to see full feedback and any responses

### For Administrators

#### Accessing Complaints

1. Login as admin
2. Navigate to **Admin Panel** → **Staff Feedback** (located at bottom of HRM section)
3. View dashboard with stats for pending, reviewing, resolved, and high-priority items

#### Managing a Complaint

1. Click on any complaint to view details
2. **Submitter Information**: Click "Show metadata" to reveal who submitted it
3. **Update Status**: Choose from Pending, Reviewing, Resolved, or Dismissed
4. **Set Priority**: Assign Low, Medium, or High priority
5. **Add Notes**: Write internal notes that only admins can see
6. **Add Response**: Write admin notes that employees can view
7. **Delete**: Remove complaint if necessary

#### Filtering & Search

-   **Status Filter**: Show only pending, reviewing, resolved, or dismissed complaints
-   **Priority Filter**: Filter by low, medium, or high priority
-   **Category Filter**: Filter by specific categories
-   **Search**: Search by subject, description, or submitter name

## Privacy Features

### Employee Experience

-   No user identification shown in UI
-   Labeled as "Feedback Box" to encourage participation
-   Privacy notice displayed prominently
-   Anonymous appearance maintained throughout

### Admin Experience

-   Submitter info hidden by default behind "Show metadata" toggle
-   Subtle placement in navigation (bottom of list)
-   Labeled as "Staff Feedback" rather than "Complaints"
-   Professional, neutral language throughout

## Security

-   Only employees can submit complaints (enforced by middleware)
-   Employees can only view their own complaints
-   Admins have full access with proper authorization
-   User authentication required for all operations
-   CSRF protection on all forms

## Design Highlights

### UI/UX

-   Dark theme consistent with ERP system
-   Color-coded status badges (Amber=Pending, Blue=Reviewing, Green=Resolved, Red=Dismissed)
-   Priority indicators (Red=High, Yellow=Medium, Green=Low)
-   Responsive design for mobile and desktop
-   Alpine.js for interactive elements
-   Success notifications with auto-dismiss

### Accessibility

-   Clear visual hierarchy
-   Descriptive icons and labels
-   Guidelines for effective feedback
-   Status explanations for employees

## Categories Available

-   Workplace Environment
-   Management
-   Harassment/Discrimination
-   Safety Concerns
-   Compensation/Benefits
-   Workload
-   Other

## Status Workflow

1. **Pending**: Initial state when feedback is submitted
2. **Reviewing**: Admin has started looking into the issue
3. **Resolved**: Issue has been addressed and resolved
4. **Dismissed**: Issue reviewed but no action taken

## Best Practices

### For Administrators

1. Review complaints regularly
2. Update status to keep employees informed
3. Use admin notes for internal tracking
4. Respond professionally in admin notes that employees can see
5. Set appropriate priorities based on severity
6. Handle sensitive issues promptly

### For Employees

1. Be specific and provide details
2. Remain professional and constructive
3. Include relevant dates, times, or situations
4. Use appropriate categories
5. Check back for updates and responses

## Technical Notes

-   **Model Relationships**: Complaint belongs to User
-   **Scopes**: `pending()`, `resolved()` for quick filtering
-   **Soft Deletes**: Not implemented (hard delete)
-   **Validation**: Required fields - subject, description
-   **Pagination**: 15 items per page (admin), 10 items per page (employee)

## Future Enhancements

Potential improvements:

-   Email notifications for new complaints and status updates
-   File attachment support
-   Anonymous option (completely hide submitter even from admin)
-   Department-specific routing
-   Auto-escalation for high-priority items
-   Analytics dashboard for trends
-   Export functionality for reporting
-   Bulk actions for admins

## Integration Points

-   Uses existing authentication system
-   Integrates with employee/admin middleware
-   Follows existing UI/UX patterns
-   Compatible with HRM module structure
-   Uses shared layout components

## Deployment Checklist

-   ✅ Migration created and run
-   ✅ Model created with relationships
-   ✅ Controllers created for admin and employee
-   ✅ Views created for all operations
-   ✅ Routes registered
-   ✅ Navigation updated
-   ✅ Middleware applied
-   ✅ Validation implemented
-   ✅ Privacy features implemented

## Support

For issues or questions about the Complaint Box module, refer to:

-   This documentation
-   Code comments in controllers
-   Laravel documentation for framework features
