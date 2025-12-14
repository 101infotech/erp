# Weekly Feedback Module - User Workflows

## 🔵 Employee Workflow

### Step 1: Access Dashboard

```
Dashboard → Click "Weekly Feedback" in main navigation
```

-   URL: `/employee/feedback`
-   See: Mandatory submission reminder
-   Display: Days until Friday deadline

### Step 2: Review Status

```
Dashboard shows:
├── ✓ Already submitted this week?
│   └─ Display: Submitted badge + form preview
└── ✗ Not submitted yet?
    └─ Display: Submission form ready
```

### Step 3: Submit Feedback

```
Fill three sections (min 10 characters each):

1️⃣ FEELINGS
   "How are you feeling?"
   Example: "I've been feeling motivated and energized this week,
   though a bit tired by Friday"

2️⃣ PROGRESS
   "What progress did you make?"
   Example: "Completed the dashboard redesign, fixed 5 bugs,
   attended 3 client meetings"

3️⃣ IMPROVEMENTS
   "What can you improve?"
   Example: "Want to improve my presentation skills and learn
   more about API optimization"
```

### Step 4: Submit

```
Click "Submit Feedback"
→ Confirmation: Submitted ✓
→ Show: Submission timestamp
→ Options: View other weeks or return to dashboard
```

### Step 5: View History

```
Click "Feedback History" in navigation
→ See: All past weeks of feedback
→ Each week shows:
   ├─ Week date range (Mon-Sun)
   ├─ Quick preview cards (3 sections)
   ├─ Admin notes indicator (if available)
   └─ "View Full Feedback" link
```

### Step 6: View Management Feedback

```
Click "View Full Feedback" on any week
→ See all three sections in detail
→ If manager added notes:
   ├─ See: "Management Feedback" section
   └─ Read: Their response/comments
```

---

## 🔴 Admin Workflow

### Step 1: Access Feedback Dashboard

```
Admin Sidebar → Click "Weekly Feedback"
URL: /admin/feedback
```

### Step 2: View Statistics

```
Dashboard shows three stat cards:
├─ Total Employees: 45
├─ Current Week Submitted: 38 (84%)
└─ Pending Submissions: 7 (16%)
```

### Step 3: Filter & Search

```
Options available:
├─ Week Selector Dropdown
│  └─ Default: Current week
│  └─ Change to: Any past/future week
├─ Status Filter Tabs
│  ├─ Submitted (38)
│  └─ Pending (7)
└─ Employee Search Box
   └─ Search by name
```

### Step 4: View Employee List

```
Table shows:
├─ Employee Name (clickable)
├─ Status Badge (✓ Submitted or ⏳ Pending)
├─ Submitted Date/Time
└─ View Link

Click employee name → Go to detail view
```

### Step 5: Review Feedback Detail

```
URL: /admin/feedback/{id}

Display sections:
├─ 🔵 How They're Feeling
│  └─ Show: Full text of feelings/emotions
├─ 🟢 Work Progress
│  └─ Show: Full text of accomplishments
├─ 🟣 Self-Improvements
│  └─ Show: Full text of development goals
└─ Add Management Feedback
   └─ Textarea: Type your response
   └─ Button: Save Notes
```

### Step 6: Add Management Notes

```
1. Scroll to "Management Feedback" section
2. Click in textarea (shows "Enter your feedback...")
3. Type your response/comments (no character limit)
4. Click "Save Notes"
5. Confirmation: Notes saved ✓
6. Employee will see your notes when viewing their feedback
```

### Step 7: View Analytics

```
Click "Analytics Dashboard" (or /admin/feedback/analytics/dashboard)

View insights:
├─ 📊 Submission Rate
│  └─ "84% of team submitted this week"
├─ 😊 Team Sentiments
│  └─ Aggregated feelings summary
│  └─ Common emotions/themes
├─ 📈 Work Progress Overview
│  └─ Common accomplishments
│  └─ Areas of focus
├─ 🎯 Self-Improvement Focus Areas
│  └─ Most common skill development goals
│  └─ Growth trends
└─ Individual Feedback Cards
   └─ Sample from each employee (recent)
```

---

## 📱 Mobile Flow

### Employee (Mobile)

```
1. Tap "Weekly Feedback" (hamburger menu)
   └─ View dashboard
2. See Friday countdown
   └─ Tap "Submit Feedback"
3. Scroll through form
   └─ Fill each section
4. Tap "Submit Feedback" button
5. Confirmation screen
```

### Admin (Mobile)

```
1. Tap "Weekly Feedback" (hamburger menu)
   └─ View dashboard
2. Tap week selector for filters
3. Scroll employee list
   └─ Tap employee name
4. View feedback sections
5. Scroll to add notes
   └─ Tap textarea + type
```

---

## 🔄 Typical Weekly Cycle

### Monday Morning

-   Admin: Check "Analytics" from last Friday's submissions
-   Employees: Can start submitting (optional, not due until Friday)

### Wednesday

-   Employees: May have submitted, can view/edit
-   Admin: Can review early submissions and add notes

### Friday Morning

-   Employees: Last day to submit!
-   Dashboard shows: "Submit today" (0 days left)
-   Employees: Rushing to submit

### Friday Afternoon

-   All submissions in
-   Admin: Reviews all feedback
-   Admin: Adds management notes and responses

### Next Monday

-   Admin: Reviews "Analytics" dashboard
-   Cycle repeats

---

## ⚙️ Key Actions Summary

| User     | Action         | Screen                                | Result                        |
| -------- | -------------- | ------------------------------------- | ----------------------------- |
| Employee | View Dashboard | `/employee/feedback`                  | See status & countdown        |
| Employee | Submit Form    | `/employee/feedback/create`           | Store new feedback            |
| Employee | View History   | `/employee/feedback/history`          | See all past weeks            |
| Employee | View Details   | `/employee/feedback/{id}`             | See feedback + manager notes  |
| Admin    | View List      | `/admin/feedback`                     | See all submissions + filters |
| Admin    | View Details   | `/admin/feedback/{id}`                | Read feedback, add notes      |
| Admin    | View Analytics | `/admin/feedback/analytics/dashboard` | Team insights                 |

---

## 📝 Data Flow

```
Employee Submits
    ↓
Store in database (is_submitted = true)
    ↓
Admin Notified (optional future feature)
    ↓
Admin Views Dashboard
    ↓
Admin Clicks Employee → View Details
    ↓
Admin Reads Feedback
    ↓
Admin Adds Management Notes
    ↓
Employee Views Their Feedback
    ↓
Employee Sees Management Notes
    ↓
Next Week (cycle repeats)
```

---

## ✅ Validation Rules Applied

### Employee Input

```
feelings:          required | min:10 characters
work_progress:     required | min:10 characters
self_improvements: required | min:10 characters
```

All three fields are **mandatory** and must be at least **10 characters long**.

### Error Handling

```
If validation fails:
├─ Stay on form
├─ Show error message in red
├─ Highlight invalid field
└─ Keep existing data in form
```

---

## 🎯 Navigation Paths

### Employee Navigation Menu

```
Dashboard
├─ Attendance
├─ Payroll
├─ Leave Requests
├─ 🆕 Weekly Feedback ← Main entry point
└─ Complaint Box ← Separate system
```

### Admin Sidebar

```
HRM Module
├─ Who is Clocked In
├─ Team Management
├─ Organization
├─ Payroll
├─ Leave Requests
├─ Leave Policies
├─ Users
├─ 🆕 Weekly Feedback ← New section
└─ Staff Feedback (Complaints) ← Separate
```

---

## 🔐 Access Control

| User Type    | Can Access                                 | Cannot Access                              |
| ------------ | ------------------------------------------ | ------------------------------------------ |
| **Employee** | Their own dashboard, create, history, show | All admin views, other employees' feedback |
| **Admin**    | All admin views, all employee feedback     | Employee-only views                        |
| **Guest**    | None                                       | All features                               |

Access is controlled via `employee` and `admin` middleware on routes.

---

## 🚀 Best Practices for Users

### For Employees

1. ✅ Submit early in the week
2. ✅ Be honest and specific
3. ✅ Use at least 10 characters per field
4. ✅ Review manager feedback when available
5. ✅ Keep track of accomplishments during the week

### For Admins

1. ✅ Review analytics on Monday morning
2. ✅ Add personal notes to team members
3. ✅ Use feedback for 1-on-1 conversations
4. ✅ Track trends over weeks
5. ✅ Use insights for team development plans

---

## 📞 Common Questions

**Q: Can I edit my feedback after submitting?**  
A: Yes! Visiting the create form again will show your current week's feedback for editing.

**Q: What if I submit after Friday?**  
A: You can still submit anytime. The Friday deadline is a guideline, not a hard block.

**Q: Can I see other employees' feedback?**  
A: Employees can only see their own. Admins can see all.

**Q: Are responses from managers required?**  
A: No, manager notes are optional. They can respond if needed.

**Q: How far back can I see my feedback?**  
A: All history is kept. The history view shows paginated results (15 per page).

---

**Module Ready for Use!** ✅
