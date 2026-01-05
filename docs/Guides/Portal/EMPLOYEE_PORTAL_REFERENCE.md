# Quick Reference - Employee Portal Components

## 🎯 Dashboard Components

### 1. Welcome Section

**Location:** Top of dashboard
**Content:**

-   Greeting message
-   Employee code, department
-   Current date and last login
-   Quick profile link button

**Styling:** Gradient background with lime/emerald accents

### 2. Quick Stats (4 Cards)

**Row 1-4 Dashboard:**

| Card          | Icon     | Color  | Data                 |
| ------------- | -------- | ------ | -------------------- |
| Attendance    | Calendar | Lime   | Days present + hours |
| Leave Balance | Calendar | Blue   | Annual available     |
| Last Payment  | Money    | Green  | Net salary received  |
| Average Hours | Chart    | Purple | Hours per day        |

**Features:** Hover effects, smooth transitions, responsive layout

### 3. Main Content Grid (3 Sections)

**Left (2/3 width):**

-   Recent Attendance (7 days max)
-   Status badges (Full Day/Partial/Absent)

**Right (1/3 width):**

-   Leave Requests (3 max)
-   Quick Action Buttons
    -   Request Leave (Blue button)
    -   Resources (Purple button)

### 4. Bottom Sections (Full Width)

**Left (1/2):**

-   Recent Payroll (3 records)
-   Shows period and net salary

**Right (1/2):**

-   Announcements (3-5)
-   Priority indicators
-   "Read More" links

### 5. Leave Balance (Full Width)

-   Responsive grid of leave types
-   Progress bars (color-coded)
-   Used/Available/Quota display

---

## 👤 Profile Page Structure

### Header Section

**Content:**

-   Employee avatar (large, circular)
-   Name and employee code
-   Position, department, company
-   Hire date with "time ago"
-   Status badge (Active/Inactive)
-   Reports to (manager info)

**Layout:** Flex with avatar on left, info grid on right

### Tabs (5 Total)

#### Tab 1: Personal Information

-   Date of Birth
-   Gender
-   Blood Group
-   Marital Status
-   Address
-   Emergency Contact (Name, Relationship, Phone)

#### Tab 2: Employment Details

-   Employment Type
-   Working Hours Per Day
-   Working Days Per Week
-   Reporting Manager
-   Contract Start Date
-   Contract End Date
-   Probation Period
-   Hire Date

#### Tab 3: Compensation & Tax

-   Salary Type
-   Base Salary
-   Allowances
-   PAN Number
-   Tax Regime
-   Member Since

#### Tab 4: Contact Information

-   Email
-   Phone Number
-   Bank Name
-   Account Number
-   Bank Branch

#### Tab 5: (Additional - Future)

-   Documents
-   Performance reviews
-   Training records
-   Certifications

---

## 🎨 Color Coding System

### Quick Stats

```
Attendance:   Lime green   (#84cc16)
Leave:        Blue         (#3b82f6)
Payroll:      Green        (#22c55e)
Average:      Purple       (#a855f7)
```

### Profile Sections

```
Personal:     Lime         (#84cc16)
Employment:   Blue         (#3b82f6)
Compensation: Green        (#22c55e)
Contact:      Purple       (#a855f7)
```

### Announcement Priorities

```
High:         Red          (#ef4444)
Normal:       Green        (#22c55e)
Low:          Blue         (#3b82f6)
```

### Status Badges

```
Active:       Green        (#22c55e)
Inactive:     Red          (#ef4444)
Pending:      Amber        (#f59e0b)
Paid:         Green        (#22c55e)
Full Day:     Green        (#22c55e)
Partial:      Amber        (#f59e0b)
Absent:       Red          (#ef4444)
```

---

## 📱 Responsive Breakpoints

### Mobile (<768px)

-   Dashboard: Single column
-   Profile: Stacked sections
-   Cards: Full width
-   Tabs: Vertical scroll

### Tablet (768px-1024px)

-   Dashboard: 2 columns
-   Profile: 2 columns
-   Cards: Medium width
-   Balanced spacing

### Desktop (>1024px)

-   Dashboard: 3-4 columns
-   Profile: 2 columns
-   Cards: Optimal sizing
-   Full information density

---

## 🔄 Data Flow

### Dashboard Data Sources

```
Current Month Attendance
└─ HrmAttendanceDay (whereBetween current month)

Recent Attendance (7 days)
└─ HrmAttendanceDay (orderBy date, limit 7)

Recent Payroll (3 records)
└─ HrmPayrollRecord (orderBy period_start_bs, limit 3)

Leave Requests
└─ HrmLeaveRequest (where status = pending)

Announcements
└─ Announcement (published, forUser, recent 3)

Leave Balance
└─ LeavePolicyService::getLeaveBalanceSummary()
```

### Profile Data Sources

```
Employee Information
└─ HrmEmployee model with:
   - department relationship
   - company relationship
   - manager relationship
   - user relationship

All displayed as read-only
```

---

## 🎯 Navigation Routes

### Dashboard

```
Route: /employee/dashboard
Named: employee.dashboard
Controller: EmployeeController@dashboard
```

### Profile (View)

```
Route: /employee/profile
Named: employee.profile.show
Controller: Employee\ProfileController@show
```

### Profile (Edit) - Hidden from sidebar

```
Route: /employee/profile/edit
Named: employee.profile.edit
Controller: Employee\ProfileController@edit
```

### Quick Links from Dashboard

-   Attendance → `/employee/attendance` (employee.attendance.index)
-   Leave Request → `/employee/leave/create` (employee.leave.create)
-   Resource → `/employee/resource-requests` (employee.resource-requests.index)
-   Payroll → `/employee/payroll` (employee.payroll.index)
-   Announcements → `/employee/announcements` (employee.announcements.index)

---

## 🚀 Performance Tips

### Dashboard

-   Pre-loads last 7 days attendance
-   Limits payroll to 3 records
-   Uses eager loading
-   Calculates leave balance once
-   Caches announcements query

### Profile

-   Single model fetch
-   Includes all relationships
-   No pagination needed
-   Lightweight rendering

---

## 🔍 Customization Points

### Easy to Customize

**Colors:**

-   Edit Tailwind classes in view files
-   Change badge colors
-   Modify icon colors

**Data Limits:**

-   Change `->limit(7)` for attendance
-   Change `->limit(3)` for payroll/announcements
-   Modify date ranges

**Layout:**

-   Adjust grid columns (grid-cols-\*)
-   Change card sizing
-   Modify spacing (gap-_, p-_)

**Content:**

-   Add new tabs to profile
-   Add new quick stats
-   Customize badges

---

## 📋 Maintenance Checklist

**Weekly:**

-   [ ] Check dashboard loads without errors
-   [ ] Verify profile displays correctly
-   [ ] Test on mobile devices
-   [ ] Monitor console for warnings

**Monthly:**

-   [ ] Review dashboard data accuracy
-   [ ] Check profile field completeness
-   [ ] Update documentation if changes made
-   [ ] Performance test with large datasets

**Quarterly:**

-   [ ] Consider UI/UX improvements
-   [ ] Update color scheme if needed
-   [ ] Add new components if requested
-   [ ] Refactor complex sections

---

## 📚 Related Files

-   Dashboard View: [resources/views/employee/dashboard.blade.php](../resources/views/employee/dashboard.blade.php)
-   Profile View: [resources/views/employee/profile/show.blade.php](../resources/views/employee/profile/show.blade.php)
-   Dashboard Controller: [app/Http/Controllers/Employee/DashboardController.php](../app/Http/Controllers/Employee/DashboardController.php)
-   Profile Controller: [app/Http/Controllers/Employee/ProfileController.php](../app/Http/Controllers/Employee/ProfileController.php)
-   Sidebar: [resources/views/employee/partials/sidebar.blade.php](../resources/views/employee/partials/sidebar.blade.php)

---

**Last Updated:** December 18, 2025
**Version:** 1.0
**Status:** Production Ready
