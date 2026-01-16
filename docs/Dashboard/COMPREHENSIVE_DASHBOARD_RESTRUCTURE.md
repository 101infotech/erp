# Comprehensive Dashboard Restructure

**Status**: In Progress
**Last Updated**: January 7, 2026
**Version**: 2.0

---

## Overview

This document outlines the complete restructuring of both Admin and Employee dashboards to create comprehensive, role-based information displays with improved organization, visual hierarchy, and data presentation.

## Dashboard Architecture

### 1. Admin Dashboard Structure

#### **1.1 Header Section**
```
- Welcome message with user name
- Current date and time
- Quick stat summary badge (Total sites, Employees, etc.)
```

#### **1.2 Key Performance Indicators (KPIs) - Top Row**
Four main stat cards with:
- **Total Sites** (Blue)
  - Icon: Globe/Website
  - Data: Count of active websites
  - Subtitle: "Active websites"

- **Team Members** (Lime)
  - Icon: People
  - Data: Total employees + active count
  - Subtitle: "Total employees (X active)"

- **Total Blogs** (Yellow)
  - Icon: Document/Article
  - Data: Published articles count
  - Subtitle: "Published articles"

- **New Contacts** (Orange)
  - Icon: Mail/Envelope
  - Data: New contact forms (last 7 days)
  - Subtitle: "Last 7 days"

#### **1.3 Business Summary Section**
Two-column layout:

**Left Side (2/3 width):**
- **Finance Dashboard Snapshot**
  - Revenue (Green) - Total for fiscal year
  - Expenses (Red) - Total spending
  - Net Profit (Blue) - Calculated profit
  - Pending Receivables (Yellow) - Outstanding payments
  - Growth indicators for each metric (% vs last month)
  - Links to full Finance Dashboard

**Right Side (1/3 width):**
- **HRM Health Dashboard**
  - Active Employees count
  - Pending Leave Requests (Yellow)
  - Draft Payrolls (Blue)
  - Attendance Flags/Anomalies (Red)
  - Direct action links to HRM sections

#### **1.4 Finance & HRM Quick Actions**
Three-column grid:

**Column 1: Finance Quick Actions**
- New Transaction
- View Reports
- New Sale
- New Purchase

**Column 2: Recent Transactions**
- List of latest 5 transactions
- Transaction type (Income/Expense)
- Amount and date
- Link to view all

**Column 3: AI Insights (Beta)**
- Real-time analysis of finance & HRM data
- Key insights and recommendations
- AI suggestion box (Ask AI)
- Auto-refresh from live data

#### **1.5 Quick Access Modules**
Four module cards:
- **#Management - Sites**: Manage websites
- **#HRM - HR Management**: Employees & payroll
- **#Tracking - Attendance**: Track employee time
- **#Content - Content**: Blogs & media

#### **1.6 Pending Actions (Conditional)**
Displays only if there are pending items:
- **Pending Leave Requests** (Yellow)
- **Draft Payrolls** (Blue)
- **Attendance Anomalies** (Red)

### 2. Employee Dashboard Structure

#### **2.1 Welcome Section**
Two-column layout:

**Left (2/3):**
- Large welcome message with employee name
- Employee Code badge
- Department display
- Current date and last login info

**Right (1/3):**
- Profile Card with link to full profile

#### **2.2 Quick Stats Row**
Four stat cards:
- **Attendance This Month** (Lime)
  - Days present + total hours
  - Monthly tracking

- **Leave Balance** (Blue)
  - Annual leave available
  - Days available counter

- **Last Payment** (Green)
  - Latest salary paid
  - Last payment amount

- **Average Hours** (Purple)
  - Daily average hours worked
  - Per day metric

#### **2.3 Main Content Area**
Three-column layout:

**Left (2/3):**
- **Recent Attendance** (Last 3 days)
  - Date
  - Hours worked
  - Status badge (Full Day/Half Day/Absent)
  - Link to full attendance

**Right (1/3):**
- **Leave Requests** (Pending)
  - Leave type
  - Start and end dates
  - Days count
  - Status badge
  - Request new leave button

- **Quick Actions** (2-column grid)
  - Request Leave button
  - Resource Requests button

#### **2.4 Payroll & Announcements**
Two-column section:

**Left: Recent Payroll**
- Last 3 payroll records
- Month/Year display
- Net salary
- Status badge (Paid)
- Link to view all

**Right: Announcements**
- Latest 3 announcements
- Title and priority badge
- Content preview
- Time posted (relative)
- Read more link
- Priority-based left border color

#### **2.5 Leave Balance Summary**
Full-width section with:
- Leave types (Annual, Casual, Sick, Period, Unpaid)
- Progress bars showing usage
- Used/Quota ratio
- Available days count
- Color-coded by type

---

## Design System Implementation

### Colors
```
Primary: Lime-400/500 (#84cc16)
Accent Blue: #3b82f6
Dark Background: slate-950, slate-900
Card Background: slate-800/50
Border: slate-700
Text Primary: white
Text Secondary: slate-400
```

### Typography
```
H1: text-2xl/3xl font-semibold
H2: text-lg/xl font-semibold
H3: text-base font-semibold
Body: text-sm/base
Small: text-xs
```

### Spacing
```
Section Gaps: gap-6, gap-8
Card Padding: p-4, p-6
Element Spacing: space-y-3, space-y-4
Border Radius: rounded-xl, rounded-2xl
```

### Component Classes
```
Stat Card: bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700
Section Header: text-lg font-semibold text-white
Status Badge: px-3 py-1 rounded-full text-xs font-semibold
Icon Container: w-10 h-10 rounded-xl flex items-center justify-center
```

---

## Data Requirements

### Admin Dashboard Data
```
From Controller:
- stats: {total_sites, total_team_members, total_blogs, new_contact_forms}
- hrmStats: {total_employees, active_employees, pending_leaves, draft_payrolls, approved_payrolls, paid_payrolls, unreviewed_anomalies}
- financeData: {kpis, recent_transactions, pending_payments}
- recentContacts: Collection[ContactForm]
- recentBookings: Collection[BookingForm]
- pendingLeaves: Collection[HrmLeaveRequest]
```

### Employee Dashboard Data
```
From Controller:
- employee: HrmEmployee with department and user
- stats: {attendance, leaves, payroll}
- recentAttendance: Collection[HrmAttendanceDay] (last 3)
- recentPayrolls: Collection[HrmPayrollRecord] (last 3)
- pendingLeaves: Collection[HrmLeaveRequest]
- recentAnnouncements: Collection[Announcement]
```

---

## Component Library

### StatCard
```php
<x-stat-card
    :title="'Total Sites'"
    :value="4"
    :subtitle="'Active websites'"
    :color="'blue'"
/>
```

### SectionHeader
```php
<x-section-header
    :title="'Business Summary'"
    :subtitle="'Financial overview'"
/>
```

### QuickActionCard
```php
<x-quick-action-card
    :title="'New Transaction'"
    :subtitle="'Add entry'"
    :icon="'plus'"
    :color="'cyan'"
    :href="route('admin.finance.transactions.create')"
/>
```

### StatusBadge
```php
<x-status-badge
    :status="'pending'"
    :label="'Pending'"
/>
```

---

## Responsive Breakpoints

### Mobile (< 768px)
- Single column layout for all sections
- Full-width cards
- Stacked grid items

### Tablet (768px - 1024px)
- 2-column layouts where applicable
- Adjusted card sizes
- Compact spacing

### Desktop (> 1024px)
- 3-4 column layouts
- Optimized grid spacing
- Full feature display

---

## Implementation Roadmap

### Phase 1: Documentation & Planning ✅
- [x] Define dashboard structure
- [x] Document data flows
- [x] Create design specifications

### Phase 2: Component Library (Current)
- [ ] Create reusable Blade components
- [ ] Build stat cards
- [ ] Create section headers
- [ ] Build status badges

### Phase 3: Admin Dashboard Restructure
- [ ] Refactor main dashboard.blade.php
- [ ] Organize sections by importance
- [ ] Improve visual hierarchy
- [ ] Add missing sections

### Phase 4: Employee Dashboard Restructure
- [ ] Reorganize welcome section
- [ ] Improve quick stats display
- [ ] Better attendance section
- [ ] Enhanced leave balance display

### Phase 5: Testing & Optimization
- [ ] Responsive testing
- [ ] Data accuracy verification
- [ ] Performance optimization
- [ ] Cross-browser testing

---

## Key Features

### ✅ Implemented
- Current layout structure
- Finance data integration
- HRM data display
- Recent items sections
- Quick action cards
- Status badges

### 🔄 To Implement
- Component library for reusability
- Enhanced visual hierarchy
- Better data organization
- Improved mobile responsiveness
- Advanced analytics
- Custom dashboard widgets
- Real-time data updates

---

## Notes

- Both dashboards follow the same design system for consistency
- Dark theme with accent colors for better visibility
- Role-based information display
- Data flows from controllers to views
- Integration with existing modules (Finance, HRM, Sites, etc.)

---

## Related Files

- **Admin Dashboard**: [resources/views/admin/dashboard.blade.php](resources/views/admin/dashboard.blade.php)
- **Employee Dashboard**: [resources/views/employee/dashboard.blade.php](resources/views/employee/dashboard.blade.php)
- **Admin Controller**: [app/Http/Controllers/Admin/DashboardController.php](app/Http/Controllers/Admin/DashboardController.php)
- **Employee Controller**: [app/Http/Controllers/Employee/DashboardController.php](app/Http/Controllers/Employee/DashboardController.php)
