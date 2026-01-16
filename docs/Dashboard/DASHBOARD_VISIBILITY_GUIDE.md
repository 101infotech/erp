# Dashboard Visibility Guide - What Users See

## 🎯 Dashboard Display by Role

### 1. Super Admin
**Access Level**: Full System Access

#### Admin Dashboard (`/admin/dashboard`)
✅ **Header & Overview Section**
- Total Leads count
- Total Sites count  
- Team Members count (all visible)
- Total Blogs count
- New Contacts count

✅ **Finance Summary Section** - VISIBLE
- Revenue display
- Expenses display
- Net Profit calculation
- Pending Receivables

✅ **HRM Quick Stats Section** - VISIBLE
- Active Employees count
- Pending Leaves with review button
- Draft Payrolls with process button
- Attendance Issues with check button

✅ **Pending Leave Requests** - VISIBLE
- List of all pending leave requests
- Employee names and leave types
- Review action buttons

✅ **Quick Actions Section** - ALL VISIBLE
- Manage Employees
- Finance Dashboard
- Leave Requests
- User Accounts

#### Staff Dashboard (`/dashboard`)
✅ **Admin Banner** - VISIBLE
- "Admin Access Enabled" message
- "Open Admin Panel" button

✅ **Navigation Menu** - ALL ITEMS VISIBLE
- Dashboard
- Projects
- Leads Base
- Team
- Finance
- Analytics

---

### 2. Admin
**Access Level**: Admin Panel + Staff Features

#### Admin Dashboard (`/admin/dashboard`)
✅ **Same as Super Admin** (full access)
- All sections visible
- All data loaded
- All quick actions available

#### Staff Dashboard (`/dashboard`)
✅ **Redirected to Admin Dashboard**
- Does not see regular staff dashboard
- Redirected to `/admin/dashboard` automatically

---

### 3. Finance Manager / Finance Accountant
**Access Level**: Finance Module Only

#### Admin Dashboard (`/admin/dashboard`)
✅ **Finance Summary Section** - VISIBLE
- Revenue: NPR [amount]
- Expenses: NPR [amount]
- Net Profit: NPR [amount]
- Pending Receivables: NPR [amount]
- View More link to Finance Dashboard

❌ **HRM Quick Stats** - HIDDEN
- Section completely removed from view
- No data loaded

❌ **Team Members Card** - HIDDEN
- Not visible in overview stats

❌ **Pending Leaves** - HIDDEN
- Section completely removed

✅ **Quick Actions** - PARTIAL
- ❌ Manage Employees (hidden)
- ✅ Finance Dashboard (visible)
- ❌ Leave Requests (hidden)
- ✅ User Accounts (always visible)

#### Staff Dashboard (`/dashboard`)
✅ **Navigation Menu**
- Dashboard (always visible)
- Projects (❌ hidden)
- Leads Base (❌ hidden)
- Team (❌ hidden)
- Finance (✅ visible)
- Analytics (❌ hidden)

✅ **Can Access**
- /dashboard (staff dashboard)
- Finance module pages

❌ **Cannot Access**
- /admin/dashboard (no redirect)
- HRM features
- Projects features

---

### 4. HR Manager / HR Executive
**Access Level**: HRM Module Only

#### Admin Dashboard (`/admin/dashboard`)
❌ **Finance Summary Section** - HIDDEN
- Section completely removed
- No finance data loaded

✅ **HRM Quick Stats Section** - VISIBLE
- Active Employees: [count]
- Pending Leaves: [count] (with Review button)
- Draft Payrolls: [count] (with Process button)
- Attendance Issues: [count] (with Check button)

✅ **Team Members Card** - VISIBLE
- Shows team count in overview stats

✅ **Pending Leaves** - VISIBLE
- List of leave requests
- Review buttons for each leave
- Leave type, dates, and employee info

✅ **Quick Actions** - PARTIAL
- ✅ Manage Employees (visible)
- ❌ Finance Dashboard (hidden)
- ✅ Leave Requests (visible)
- ✅ User Accounts (always visible)

#### Staff Dashboard (`/dashboard`)
✅ **Navigation Menu**
- Dashboard (always visible)
- Projects (❌ hidden)
- Leads Base (❌ hidden)
- Team (✅ visible)
- Finance (❌ hidden)
- Analytics (❌ hidden)

✅ **Can Access**
- /dashboard (staff dashboard)
- HRM module pages
- Employee management
- Leave approval

❌ **Cannot Access**
- /admin/dashboard
- Finance features
- Leads features

---

### 5. Leads Manager / Leads Executive
**Access Level**: Leads Module Only

#### Admin Dashboard (`/admin/dashboard`)
❌ **Finance Summary** - HIDDEN
❌ **HRM Sections** - HIDDEN
❌ **Pending Leaves** - HIDDEN

✅ **Quick Actions** - MINIMAL
- Only User Accounts visible
- All module-specific actions hidden

#### Staff Dashboard (`/dashboard`)
✅ **Navigation Menu**
- Dashboard (visible)
- Projects (❌ hidden)
- Leads Base (✅ visible)
- Team (❌ hidden)
- Finance (❌ hidden)

✅ **Can Access**
- /dashboard (staff dashboard)
- Leads module pages

---

### 6. Project Manager / Team Lead
**Access Level**: Projects Module Only

#### Admin Dashboard (`/admin/dashboard`)
❌ **Finance Summary** - HIDDEN
❌ **HRM Sections** - HIDDEN
❌ **Pending Leaves** - HIDDEN

✅ **Quick Actions** - MINIMAL
- Only User Accounts visible

#### Staff Dashboard (`/dashboard`)
✅ **Navigation Menu**
- Dashboard (visible)
- Projects (✅ visible)
- Leads Base (❌ hidden)
- Team (❌ hidden)
- Finance (❌ hidden)

---

### 7. Regular User/Employee
**Access Level**: Staff Dashboard + Assigned Modules

#### Admin Dashboard (`/admin/dashboard`)
🚫 **CANNOT ACCESS**
- Route redirects to `/dashboard`
- No admin panel access

#### Staff Dashboard (`/dashboard`)
✅ **Full Access**
- See own assigned modules
- Navigation shows only permitted items
- No Admin Banner

✅ **Navigation Menu** - VARIES by permissions
- Dashboard (always visible)
- Projects (visible if assigned)
- Leads Base (visible if assigned)
- Team (visible if assigned)
- Finance (visible if assigned)

✅ **Can Access**
- Only assigned modules
- Own employee records
- Projects they're part of
- Leads assigned to them

---

## 📊 Permission Matrix

| Section | Super Admin | Admin | Finance | HR | Leads | Projects | Employee |
|---------|------------|-------|---------|----|----|---------|----------|
| **Admin Dashboard Access** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Finance Summary** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **HRM Quick Stats** | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Pending Leaves** | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Finance Nav Link** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Team Nav Link** | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Projects Nav Link** | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Leads Nav Link** | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Admin Banner** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 🔄 What Happens Behind The Scenes

### When Super Admin Views Dashboard
1. ✅ All permission checks pass
2. ✅ Finance data is loaded and displayed
3. ✅ HRM data is loaded and displayed
4. ✅ Leave data is loaded and displayed
5. ✅ All sections render in the template

### When Finance Manager Views Dashboard
1. ❌ Finance permission check passes → DATA LOADED
2. ❌ HRM permission check fails → NO DATA LOADED
3. ❌ Leave permission check fails → NO DATA LOADED
4. ✅ Finance Summary section renders
5. ❌ HRM and Leave sections are completely hidden
6. 🎯 Result: Only Finance Summary visible

### When Regular Employee Views Dashboard
1. Route check: Not admin → Redirected to `/dashboard`
2. Dashboard loads with permission variables
3. Navigation menu filters based on assigned permissions
4. Only assigned module links show
5. Admin banner is completely hidden

---

## 📍 Key Insight

**The system doesn't hide or gray out sections** - it **completely removes them from the view**.

This means:
- ✅ Cleaner UI (no empty sections)
- ✅ Better performance (no unnecessary data loaded)
- ✅ Enhanced security (no data sent to unauthorized users)
- ✅ Simplified navigation (only relevant options shown)

---

## 🎨 UI Changes by Role

### Admin Dashboard Stats Row
```
Super Admin/Admin:       4 stat cards visible
├─ Leads
├─ Sites
├─ Team Members
└─ Blogs

Finance Manager:         3 stat cards visible
├─ Leads
├─ Sites
└─ Blogs
(Team Members hidden - no HRM permission)

HR Manager:             3 stat cards visible
├─ Leads
├─ Sites
└─ Blogs
(No change - Team Members only shows in HRM section)
```

### Finance & HRM Section Grid
```
Super Admin:            2 columns visible
├─ Finance Summary (left, wider)
└─ HRM Quick Stats (right)

Finance Manager:        1 column visible
└─ Finance Summary (left, wider)
   HRM section removed

HR Manager:             1 column visible
├─ Finance Summary hidden
└─ HRM Quick Stats (right)
```

### Quick Actions Grid
```
Super Admin:            4 cards visible
├─ Manage Employees
├─ Finance Dashboard
├─ Leave Requests
└─ User Accounts

Finance Manager:        2 cards visible
├─ Finance Dashboard
└─ User Accounts

HR Manager:             3 cards visible
├─ Manage Employees
├─ Leave Requests
└─ User Accounts
```

---

## ✨ User Experience Benefits

1. **Focused Interface**: Users only see tools they need
2. **Faster Load Times**: No unnecessary data requests
3. **Reduced Confusion**: No restricted features to click
4. **Better Security**: No permission bypass through UI inspection
5. **Professional Appearance**: Clean, organized dashboard

---

## 🧪 Verification Checklist

- [ ] Super Admin sees all sections
- [ ] Finance Manager sees only Finance section
- [ ] HR Manager sees only HRM section
- [ ] Employee sees filtered navigation
- [ ] Admin Banner only shows to admins
- [ ] Data is not loaded for hidden sections
- [ ] Navigation links appear/disappear correctly
- [ ] No empty placeholder sections
