# Complaint Box - Quick Start Guide

## 🎯 Quick Access

### For Employees

-   **URL**: `/employee/complaints`
-   **Navigation**: Employee Portal → Feedback
-   **Submit New**: Click "Submit New Feedback" button

### For Admins

-   **URL**: `/admin/complaints`
-   **Navigation**: Admin Panel → Staff Feedback (bottom of sidebar)
-   **View Details**: Click any complaint to manage

## 📝 Employee: How to Submit Feedback

1. Click **Feedback** in the top navigation
2. Click **Submit New Feedback**
3. Select a category (optional)
4. Enter subject line
5. Write detailed description
6. Click **Submit Feedback**
7. ✅ Done! Your feedback is submitted confidentially

## 👨‍💼 Admin: How to Manage Complaints

### Quick Actions on Detail Page

**Update Status:**

1. Scroll to "Update Status" panel
2. Select new status
3. Click "Update Status"

**Add Notes:**

1. Scroll to "Admin Notes" section
2. Type your notes
3. Click "Save Notes"

**View Submitter:**

1. Find "Information" panel on the right
2. Click "Show metadata" to reveal who submitted it

**Change Priority:**

1. Scroll to "Update Priority" panel
2. Select Low/Medium/High
3. Click "Update Priority"

## 🔍 Admin: Filtering & Search

Use the filter bar at the top:

-   **Search**: Type subject, description, or name
-   **Status**: Filter by Pending/Reviewing/Resolved/Dismissed
-   **Priority**: Filter by Low/Medium/High
-   **Category**: Filter by specific category
-   Click "Apply Filters"

## 📊 Status Meanings

| Status        | Meaning                          | When to Use                 |
| ------------- | -------------------------------- | --------------------------- |
| **Pending**   | Just submitted, not reviewed yet | Default state               |
| **Reviewing** | Being looked into                | Admin started investigation |
| **Resolved**  | Issue addressed and fixed        | Problem solved              |
| **Dismissed** | Reviewed but no action taken     | Not actionable              |

## 🎨 Priority Levels

| Priority   | Color  | When to Use                                |
| ---------- | ------ | ------------------------------------------ |
| **High**   | Red    | Urgent issues, safety concerns, harassment |
| **Medium** | Yellow | Important but not urgent                   |
| **Low**    | Green  | Minor suggestions, general feedback        |

## 🔐 Privacy Features

### Employee View

-   ✅ No user identification shown
-   ✅ Labeled as "Feedback" not "Complaints"
-   ✅ Privacy notice displayed
-   ✅ Can only see own submissions

### Admin View

-   🔒 Submitter name hidden by default
-   🔒 Click to reveal identity
-   🔒 Subtle UI placement
-   🔒 Professional language

## 🚀 Common Workflows

### Employee Workflow

```
Submit Feedback → Wait for Review → Check Status → View Response
```

### Admin Workflow

```
View New Complaint → Set Priority → Update to "Reviewing" →
Add Notes → Resolve Issue → Update to "Resolved" → Add Response
```

## ⚠️ Important Notes

-   Employees can only view their own complaints
-   Admins can see all complaints and submitter info
-   Admin notes are visible to employees (use for responses)
-   Delete is permanent (no soft delete)
-   Status automatically sets resolved_at timestamp when marked "Resolved"

## 📱 Mobile Access

All features are fully responsive and work on mobile devices:

-   Submit feedback from phone
-   View status updates
-   Admins can manage on the go

## 🎯 Tips for Employees

✅ **Do:**

-   Be specific with details
-   Include dates/times if relevant
-   Use professional language
-   Check back for updates

❌ **Don't:**

-   Submit duplicate complaints
-   Use offensive language
-   Share confidential information inappropriately

## 🎯 Tips for Admins

✅ **Best Practices:**

-   Review daily
-   Update status promptly
-   Add meaningful notes
-   Set appropriate priorities
-   Respond professionally
-   Track resolved issues

❌ **Avoid:**

-   Ignoring high-priority items
-   Dismissing without review
-   Using admin notes for personal comments
-   Leaving status as "Pending" indefinitely

## 🛠️ Troubleshooting

**Issue: Can't see Feedback menu**

-   Solution: Ensure you're logged in as employee

**Issue: Can't access admin panel**

-   Solution: Requires admin role

**Issue: Can't submit feedback**

-   Solution: Check subject and description are filled

**Issue: Submitter info not showing**

-   Solution: Click "Show metadata" toggle in info panel

## 📈 Dashboard Stats

Admin dashboard shows:

-   **Pending** complaints (amber)
-   **Reviewing** complaints (blue)
-   **Resolved** complaints (green)
-   **High Priority** complaints (red)

Click any stat card to filter by that category.

## 🔄 Quick Reference Routes

| Action          | Route                             | Method |
| --------------- | --------------------------------- | ------ |
| List (Employee) | `/employee/complaints`            | GET    |
| Create Form     | `/employee/complaints/create`     | GET    |
| Submit          | `/employee/complaints`            | POST   |
| View (Employee) | `/employee/complaints/{id}`       | GET    |
| List (Admin)    | `/admin/complaints`               | GET    |
| View (Admin)    | `/admin/complaints/{id}`          | GET    |
| Update Status   | `/admin/complaints/{id}/status`   | PATCH  |
| Update Priority | `/admin/complaints/{id}/priority` | PATCH  |
| Add Notes       | `/admin/complaints/{id}/notes`    | POST   |
| Delete          | `/admin/complaints/{id}`          | DELETE |

---

**Need More Help?** See full documentation in `COMPLAINT_BOX_MODULE.md`
