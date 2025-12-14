# Dashboard UI/UX Implementation

## Overview

Modern, dark-themed dashboard with project cards, statistics, calendar view, and navigation. Inspired by contemporary project management interfaces with vibrant accent colors and clean typography.

## Design Specifications

### Color Scheme

-   **Background**: Dark navy/black (`#0A1628`, `#0F1B2E`)
-   **Cards**: Slightly lighter dark (`#1A2332`)
-   **Accent Colors**:
    -   Finance (Blue): `#4169E1`, `#5B7FE8`
    -   Education (Orange): `#FF7F3F`, `#FF9D66`
    -   Healthcare (Green): `#3EAF7C`, `#4DC290`
    -   Travel (Red): `#E74C3C`, `#ED6F61`
    -   Logistics (Cyan): `#17A2B8`, `#2CB5CC`
    -   Primary Button (Lime): `#C8FF00`, `#D4FF33`
-   **Text**:
    -   Primary: `#FFFFFF`
    -   Secondary: `#A0AEC0`
    -   Muted: `#718096`

### Typography

-   **Headings**: Bold, large (32-48px for main headings)
-   **Card Titles**: Semi-bold, medium (18-20px)
-   **Body Text**: Regular (14-16px)
-   **Stats**: Bold, extra-large (28-36px)

### Layout Components

#### 1. Navigation Bar (Top)

-   Logo (left)
-   Navigation items: Dashboard, Projects, Knowledge base, Users, Analytics
-   Actions (right): Invite user, Notifications, Profile avatar
-   Background: Dark with slight transparency
-   Active state: Lime green pill background

#### 2. Main Dashboard Title

-   Large "Dashboard" heading
-   Left sidebar with icons for quick access

#### 3. Statistics Summary (Top Row)

-   **Total budget**: `$21,339` (+14% week)
-   **Total completed tasks**: `21,339` (+178 today)
-   Circular icon backgrounds
-   Growth indicators

#### 4. Projects Section

-   Grid layout (3 columns on desktop, responsive)
-   Each card contains:
    -   Category tag with color-coded background
    -   Project name
    -   Completed tasks count
    -   Budget amount (large, bold)
    -   Team member avatars (circular, overlapping)
    -   Member count indicator (+N)
    -   Arrow icon (top right) for navigation
-   Card styles: Rounded corners, gradient backgrounds matching category
-   Hover effect: Slight elevation/glow

**Sample Projects**:

-   Decem App (Finance, Blue) - 988 tasks, $391,991
-   SkyLux (Education, Orange) - 12 tasks, $51,792
-   DushMash (Finance, Magenta) - 32 tasks, $31,955
-   Biofarm (Healthcare, Green) - 19 tasks, $11,538
-   PAD move (Travel, Red) - 35 tasks, $21,688
-   Getstats (Logistics, Cyan) - 88 tasks, $92,581

#### 5. Calendar View (Right Side)

-   Month name "Calendar (April)"
-   Grid layout showing days
-   Each day cell:
    -   Day number
    -   Team member avatars for scheduled tasks
    -   Color-coded borders for different projects/priority
-   Current day highlighted
-   Hover states for days
-   Compact, visual representation

#### 6. Projects This Year Section

-   Statistics display:
    -   Average tasks value: `$568,338` (comparison to last year)
    -   Average tasks per project: `89.3`
    -   New projects: `76`
-   Clean, minimalist design

#### 7. Yearly Profit Chart

-   Bar chart showing monthly data (Mar - Oct visible)
-   Highlight for peak month (July: `$21,339`)
-   Dark background, green accent for bars
-   Simple, readable design

### Data Structure

#### Project Model

```php
- id
- name
- category (Finance, Education, Healthcare, Travel, Logistics)
- category_color (hex color)
- completed_tasks
- total_tasks
- budget
- team_members (relation)
- created_at
- updated_at
```

#### Dashboard Stats

```php
- total_budget
- budget_change_week (percentage)
- total_completed_tasks
- tasks_completed_today
- average_tasks_value
- average_tasks_per_project
- new_projects_count
```

#### Calendar Events

```php
- date
- project_id
- team_members (array of user IDs)
- priority (affects border color)
```

## Component Breakdown

### Alpine.js Components

#### 1. `dashboard-stats`

-   Displays top-level statistics
-   Fetches data from `/api/dashboard/stats`
-   Auto-updates on interval (optional)

#### 2. `project-grid`

-   Renders project cards
-   Handles hover effects
-   Click to navigate to project details
-   Fetches from `/api/dashboard/projects`

#### 3. `calendar-view`

-   Month navigation
-   Day cells with events
-   Click handlers for day selection
-   Fetches from `/api/dashboard/calendar`

#### 4. `yearly-chart`

-   Bar chart visualization
-   Uses Chart.js or similar
-   Fetches from `/api/dashboard/yearly-profit`

#### 5. `project-card` (reusable)

-   Props: project data
-   Displays all project information
-   Handles click navigation
-   Avatar group component

### API Endpoints Required

```
GET /api/dashboard/stats
Response: {
  total_budget: 21339,
  budget_change_week: 14,
  total_completed_tasks: 21339,
  tasks_completed_today: 178,
  average_tasks_value: 568338,
  average_tasks_per_project: 89.3,
  new_projects_count: 76
}

GET /api/dashboard/projects
Response: [{
  id: 1,
  name: "Decem App",
  category: "Finance",
  category_color: "#4169E1",
  completed_tasks: 988,
  total_tasks: 1200,
  budget: 391991,
  team_members: [{id, name, avatar}],
  member_count: 14
}]

GET /api/dashboard/calendar?month=2025-04
Response: [{
  date: "2025-04-01",
  events: [{
    project_id: 1,
    team_members: [{id, avatar}]
  }]
}]

GET /api/dashboard/yearly-profit
Response: [{
  month: "Jan",
  value: 15234
}, ...]
```

## Implementation Notes

1. **Technology Stack**: Laravel (backend) + Alpine.js (frontend) + Tailwind CSS (styling)
2. **Database**: Extend existing models or create new Project model
3. **Responsive Design**: Mobile-first approach, stack cards on smaller screens
4. **Performance**: Lazy load images, optimize API calls
5. **Accessibility**: Proper ARIA labels, keyboard navigation
6. **Testing**: Component tests, API endpoint tests

## Next Steps

1. Create database migrations for Project model
2. Create ProjectController with dashboard methods
3. Build blade components and Alpine.js logic
4. Style with Tailwind CSS
5. Add Chart.js for yearly profit visualization
6. Test and refine
