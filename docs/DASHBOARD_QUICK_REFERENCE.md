# Dashboard Structure - Quick Reference

**Last Updated**: January 7, 2026
**Status**: ✅ Complete

---

## Admin Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  Welcome back, [Name]!                                 System Status: ✓ OK  │
│  [Current Date] • Here's what's happening with your business today.         │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ KEY METRICS                                                                  │
├──────────────────┬──────────────────┬──────────────────┬──────────────────┤
│ Total Sites      │ Team Members     │ Total Blogs      │ New Contacts     │
│ [4]              │ [25 (20 active)] │ [12]             │ [3]              │
│ Active websites  │ Total employees  │ Published        │ Last 7 days      │
└──────────────────┴──────────────────┴──────────────────┴──────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ BUSINESS SUMMARY
├─────────────────────────────────────────────┬───────────────────────────────┤
│ Finance Snapshot                            │ HRM Health                    │
│ ├─ Revenue: NPR 0 (+0% vs month)            │ ├─ Active Employees: 25      │
│ ├─ Expenses: NPR 0 (+0% vs month)           │ ├─ Pending Leaves: 3         │
│ ├─ Net Profit: NPR 0 (0% margin)            │ ├─ Draft Payrolls: 2         │
│ └─ Pending Receivables: NPR 0 (0 invoices)  │ └─ Attendance Flags: 1       │
└─────────────────────────────────────────────┴───────────────────────────────┘

┌────────────────────────────────────────┬────────────────────────────────────┐
│ QUICK ACTIONS                          │ RECENT TRANSACTIONS / AI INSIGHTS   │
│ ├─ New Transaction (Cyan)              │ Recent Transactions:               │
│ ├─ View Reports (Purple)               │ • [Txn 1] - NPR +5000             │
│ ├─ New Sale (Green)                    │ • [Txn 2] - NPR -3000             │
│ └─ New Purchase (Orange)               │ • [Txn 3] - NPR +2000             │
│                                        │                                    │
│                                        │ AI Insights (Beta):                │
│                                        │ Analyzing finance and HRM signals. │
│                                        │ [Ask AI Input Box]                 │
└────────────────────────────────────────┴────────────────────────────────────┘

┌──────────────┬──────────────────────┬─────────────────┬──────────────────┐
│ QUICK ACCESS MODULES                                                      │
├──────────────┼──────────────────────┼─────────────────┼──────────────────┤
│ #Management  │ #HRM                 │ #Tracking       │ #Content         │
│ Sites        │ HR Management        │ Attendance      │ Content          │
│              │ Employees & payroll  │ Track employee  │ Blogs & media    │
│              │                      │ time            │                  │
└──────────────┴──────────────────────┴─────────────────┴──────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ PENDING ACTIONS (Conditional - shows if items exist)                        │
├──────────────────────────────┬───────────────────────┬──────────────────────┤
│ Pending Leave Requests (3)   │ Draft Payrolls (2)    │ Anomalies (1)        │
│ [Link to leaves section]     │ [Link to payroll]     │ [Link to attendance] │
└──────────────────────────────┴───────────────────────┴──────────────────────┘
```

---

## Employee Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  Welcome back, [Employee Name]!                    [View Profile Button]   │
│  [Current Date] • Last login: [Time]                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│  Employee Code: [CODE]  │  Department: [DEPT]  │  Designation: [TITLE]     │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ YOUR METRICS                                                                │
├──────────────────┬──────────────────┬──────────────────┬──────────────────┤
│ Attendance       │ Leave Balance    │ Last Payment     │ Average Hours    │
│ This Month: [10] │ Annual: [15]     │ NPR [15,000]     │ [8.5] hours      │
│ Days present     │ Days available   │ Last payment     │ Hours per day    │
└──────────────────┴──────────────────┴──────────────────┴──────────────────┘

┌──────────────────────────────────────────────────┬────────────────────────┐
│ RECENT ATTENDANCE (Last 3 Days)                  │ PENDING LEAVES         │
│                                                  │ ├─ [Leave Type 1]     │
│ • [Date 1]: [8.5]h - Full Day                   │ │  Mar 15 - Mar 20     │
│ • [Date 2]: [4.0]h - Half Day                   │ │  (5 days)            │
│ • [Date 3]: [0.0]h - Absent                     │ │  [Status: Pending]   │
│ [Link: View All]                                │ │                      │
│                                                  │ Quick Actions:         │
│                                                  │ ├─ Request Leave      │
│                                                  │ └─ Resources          │
└──────────────────────────────────────────────────┴────────────────────────┘

┌──────────────────────────────────────────────────┬────────────────────────┐
│ RECENT PAYROLL (Last 3 Records)                  │ ANNOUNCEMENTS          │
│                                                  │                        │
│ • [Month 1]: NPR [15,000] - Paid                 │ • [Title 1]           │
│ • [Month 2]: NPR [15,000] - Paid                 │   [Priority: Normal]   │
│ • [Month 3]: NPR [14,500] - Paid                 │   [Date posted]       │
│ [Link: View All]                                │   [Link: Read]        │
│                                                  │                        │
│                                                  │ • [Title 2]           │
│                                                  │   [Priority: High]     │
│                                                  │   [Date posted]       │
│                                                  │   [Link: Read]        │
└──────────────────────────────────────────────────┴────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ LEAVE BALANCE SUMMARY [Year]                                               │
├────────────┬────────────┬────────────┬────────────┬────────────────────────┤
│ Annual     │ Casual     │ Sick       │ Period     │ Unpaid                 │
│ Used: 5/20 │ Used: 2/10 │ Used: 1/10 │ Used: 0/5  │ Used: 0/5              │
│ [████━━]   │ [██━━━━━]  │ [█━━━━━━]  │ [━━━━━━━]  │ [━━━━━━━]              │
│ 15 avail   │ 8 avail    │ 9 avail    │ 5 avail    │ 5 avail                │
└────────────┴────────────┴────────────┴────────────┴────────────────────────┘
```

---

## Component Hierarchy

### Admin Dashboard Components Used
```
Dashboard Page
├── Header Section
│   ├── Welcome Message
│   └── System Status
├── KPI Cards (x4)
│   └── dashboard-stat-card
├── Business Summary
│   ├── Finance Section
│   ├── HRM Section
│   └── dashboard-section-header
├── Quick Actions & Analytics
│   ├── dashboard-card
│   └── dashboard-quick-action (x4)
├── Quick Access Modules
│   └── Module Cards (x4)
└── Pending Actions (Conditional)
    └── Status Cards
```

### Employee Dashboard Components Used
```
Dashboard Page
├── Header Section
│   ├── Welcome Message
│   ├── Profile Button
│   └── Info Card
├── Quick Stats (x4)
├── Main Content Grid
│   ├── Recent Attendance (dashboard-card)
│   ├── Pending Leaves (dashboard-card)
│   └── Quick Actions (x2)
├── Payroll & Announcements
│   ├── Recent Payroll (dashboard-card)
│   └── Announcements (dashboard-card)
└── Leave Balance Summary
    └── Progress Bars (x5)
```

---

## Responsive Behavior

### Mobile (< 768px)
- Single column layout
- Full-width cards
- Stacked sections
- Touch-friendly spacing

### Tablet (768px - 1024px)
- 2-column layout for grids
- Adjusted card sizes
- Optimized spacing

### Desktop (> 1024px)
- 3-4 column layouts
- Full feature display
- Optimized spacing and alignment

---

## Color Scheme

### Status Colors
| Status | Color | Usage |
|--------|-------|-------|
| Pending | Yellow/Amber | Leave requests, drafts |
| Approved | Green | Approved leaves, paid items |
| Rejected | Red | Rejected items, errors |
| Active | Lime | Active status, successful |
| Default | Slate | Neutral items |

### Section Colors
| Section | Color | Icon Color |
|---------|-------|-----------|
| Sites | Blue | Blue-400 |
| Team | Lime | Lime-400 |
| Blogs | Yellow | Yellow-400 |
| Contacts | Orange | Orange-400 |
| Finance | Multi | Various |
| HRM | Multi | Various |
| Attendance | Lime | Lime-400 |
| Leaves | Blue | Blue-400 |
| Payroll | Green | Green-400 |
| Announcements | Amber | Amber-400 |

---

## Key Metrics Tracked

### Admin Dashboard
- 📊 4 Key Stats Cards
- 📈 4 Finance Metrics
- 👥 4 HRM Metrics
- 🔄 4 Quick Actions
- 📋 5+ Pending Items (conditional)

### Employee Dashboard
- 📊 4 Quick Stats
- 📅 3 Recent Attendance Records
- 📋 3 Pending Leaves
- 💰 3 Recent Payrolls
- 📢 3 Recent Announcements
- 🎯 5 Leave Type Balances

---

## Navigation Links

### Admin Dashboard Links
- Finance Dashboard
- HRM Hub
- Transactions Index
- Reports
- Finance Transactions Create
- Finance Sales Create
- Finance Purchases Create
- Sites Management
- HRM Employees
- Attendance Tracking
- Leave Management
- Payroll Management

### Employee Dashboard Links
- Profile Show
- Attendance Index
- Leave Create
- Leave Index
- Payroll Index
- Announcements Index
- Announcements Show
- Resource Requests

---

## Design System Constants

### Spacing
- Gap between sections: gap-6, gap-8
- Card padding: p-4, p-6
- Section header margin: mb-4, mb-8
- Element spacing: space-y-3, space-y-4

### Typography
- H1: text-3xl font-bold
- H2: text-lg font-semibold
- H3: text-base font-semibold
- Label: text-xs, text-xs text-slate-400
- Value: text-2xl font-bold

### Borders
- Card border: border border-slate-700
- Divider: border-b border-slate-700
- Radius: rounded-xl, rounded-2xl

---

## Common Patterns

### Stat Display
```
┌──────────────────┐
│ [Icon] Label     │
│ [Large Value]    │
│ [Small Subtitle] │
│ [Optional Metric]│
└──────────────────┘
```

### List Item Display
```
┌───────────────────────────────────┐
│ [Icon] Title        [Status Badge]│
│        Subtitle / Details         │
└───────────────────────────────────┘
```

### Card Container Display
```
┌─────────────────────────────────────┐
│ [Icon] Title          [Action Link] │
├─────────────────────────────────────┤
│ Content (varies by section)         │
│ • List items, tables, etc.          │
│ • Multiple rows/items               │
└─────────────────────────────────────┘
```

---

## Usage Tips

1. **Admin Dashboard** - Best for overview and quick actions
2. **Employee Dashboard** - Best for personal attendance and leave status
3. **Mobile Friendly** - Both dashboards work on mobile
4. **Real-time Updates** - Finance data updates server-side
5. **Responsive** - Adapts to screen size automatically

---

**For complete details, see [COMPREHENSIVE_DASHBOARD_RESTRUCTURE.md](COMPREHENSIVE_DASHBOARD_RESTRUCTURE.md)**
