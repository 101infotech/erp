# Leads Management Module - Implementation Guide

## Overview
The Leads Management Module from BuildPro is a comprehensive system for tracking and managing service leads including inspections, construction, and renovation projects. This document outlines the analysis and implementation plan for integrating this module into the ERP system.

## Source Analysis (BuildPro)

### Technology Stack
- **Frontend**: React + TypeScript + Vite
- **Database**: Supabase (PostgreSQL)
- **UI Framework**: shadcn/ui + Tailwind CSS
- **State Management**: TanStack Query (React Query)
- **Forms**: React Hook Form

### Database Schema

#### 1. `inspection_leads` Table
```sql
CREATE TABLE inspection_leads (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  service_requested TEXT NOT NULL,
  location TEXT NOT NULL,
  client_name TEXT NOT NULL,
  phone_number TEXT NOT NULL,
  email TEXT NOT NULL,
  inspection_date DATE,
  inspection_time TIME,
  inspection_charge DECIMAL(10,2),
  inspection_report_date DATE,
  inspection_assigned_to UUID REFERENCES user_profiles(id),
  status TEXT NOT NULL DEFAULT 'Intake',
  remarks TEXT,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP DEFAULT now(),
  created_by UUID REFERENCES user_profiles(id)
);
```

#### 2. `lead_statuses` Table (Dynamic Status Management)
```sql
CREATE TABLE lead_statuses (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  status_key TEXT NOT NULL UNIQUE,
  display_name TEXT NOT NULL,
  color_class TEXT NOT NULL,
  priority INTEGER NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT true,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP DEFAULT now()
);
```

**Default Statuses:**
1. Intake (Priority: 1)
2. Contacted (Priority: 2)
3. Inspection Booked (Priority: 3)
4. Inspection Rescheduled (Priority: 4)
5. Office Visit Requested (Priority: 5)
6. Reports Sent (Priority: 6)
7. Bad Lead (Priority: 7)
8. Out of Valley (Priority: 8)
9. Cancelled (Priority: 9)
10. Positive (Priority: 10)

### Key Features

#### 1. Lead Management
- **Create/Edit Leads**: Full form with validation
- **Search & Filter**: By client name, location, service type, status
- **Status Tracking**: Dynamic status with color coding
- **Assignment**: Assign leads to team members
- **Email Notifications**: Auto-notify assigned members

#### 2. Service Types Supported
- Home Inspection
- Pre-Purchase Inspection
- Commercial Inspection
- New Construction
- Renovation Project
- Kitchen Renovation
- Bathroom Renovation
- Home Addition
- Roofing Services
- Flooring Installation
- Electrical Work
- Plumbing Services
- HVAC Installation
- Structural Assessment
- Property Consultation

#### 3. Analytics Dashboard
- Total leads count
- Completed services
- Positive clients rate
- Pending services
- Conversion rate
- Revenue metrics
- Staff performance
- Status distribution (Pie Chart)
- Monthly trends (Area Chart)
- Service type distribution
- Location analytics
- Response time metrics

#### 4. Components Structure

```
pages/
  InspectionLeads.tsx          - Main page with tabs (Leads, Analytics)

components/inspections/
  LeadsTable.tsx               - Table view with sorting
  LeadTableRow.tsx             - Individual row component
  LeadCard.tsx                 - Card view for mobile
  CompactLeadCard.tsx          - Compact card variant
  CreateLeadDialog.tsx         - Create new lead form
  EditLeadDialog.tsx           - Edit existing lead
  ViewLeadDialog.tsx           - View lead details
  LeadStatusBadge.tsx          - Status display component
  InspectionAnalytics.tsx      - Analytics dashboard
  leadStatusUtils.ts           - Status utilities (deprecated)

hooks/
  useLeadStatuses.tsx          - Dynamic status hook (fetches from DB)
```

## ERP Integration Plan

### Phase 1: Database Setup ✅
**Migration Files to Create:**

1. **`create_leads_table.php`**
   - inspection_leads table
   - Indexes on status, created_at, assigned_to
   - Foreign keys to users table

2. **`create_lead_statuses_table.php`**
   - lead_statuses table with seed data
   - Default statuses with priorities and colors

### Phase 2: Laravel Backend 🔄

#### Models
1. **`Lead.php`** (App\Models)
   - Fillable attributes
   - Relationships: belongsTo User (assigned, created)
   - Scopes: active, byStatus, recent
   - Accessors/Mutators for formatted data
   - Events for status changes

2. **`LeadStatus.php`** (App\Models)
   - Cache statuses for performance
   - Helper methods for colors and priorities

#### Controllers
1. **`LeadController.php`** (App\Http\Controllers)
   - index() - List leads with filters
   - store() - Create new lead
   - show() - View single lead
   - update() - Edit lead
   - destroy() - Delete lead
   - updateStatus() - Change status
   - assign() - Assign to team member

2. **`LeadAnalyticsController.php`**
   - dashboard() - Analytics data
   - export() - Export leads to Excel/PDF

#### API Routes
```php
Route::middleware(['auth'])->prefix('leads')->group(function () {
    Route::get('/', [LeadController::class, 'index']);
    Route::post('/', [LeadController::class, 'store']);
    Route::get('/statuses', [LeadController::class, 'statuses']);
    Route::get('/analytics', [LeadAnalyticsController::class, 'dashboard']);
    Route::get('/{lead}', [LeadController::class, 'show']);
    Route::put('/{lead}', [LeadController::class, 'update']);
    Route::delete('/{lead}', [LeadController::class, 'destroy']);
    Route::patch('/{lead}/status', [LeadController::class, 'updateStatus']);
    Route::patch('/{lead}/assign', [LeadController::class, 'assign']);
    Route::get('/export/excel', [LeadAnalyticsController::class, 'exportExcel']);
});
```

### Phase 3: Frontend Views 🎨

#### Blade Templates
1. **`resources/views/admin/leads/index.blade.php`**
   - Table view with DataTables
   - Search and filters
   - Quick status updates
   - Bulk actions

2. **`resources/views/admin/leads/create.blade.php`**
   - Create lead form
   - Client validation
   - Service type dropdown
   - Assignment selector

3. **`resources/views/admin/leads/edit.blade.php`**
   - Edit form (similar to create)
   - Status history
   - Activity log

4. **`resources/views/admin/leads/show.blade.php`**
   - Lead details
   - Timeline of changes
   - Related documents
   - Communication history

5. **`resources/views/admin/leads/analytics.blade.php`**
   - Charts using Chart.js
   - KPI cards
   - Staff performance
   - Export options

### Phase 4: Email Notifications 📧

1. **`LeadAssignedMail.php`** (App\Mail)
   - Email to assigned team member
   - Lead details
   - Action buttons

2. **`LeadStatusChangedMail.php`**
   - Notify relevant parties
   - Status change details

### Phase 5: Permissions & Roles 🔒

**Permissions to Add:**
- `leads.view` - View leads
- `leads.create` - Create new leads
- `leads.edit` - Edit leads
- `leads.delete` - Delete leads
- `leads.assign` - Assign leads to team
- `leads.analytics` - View analytics
- `leads.export` - Export data

### Phase 6: Integration Points 🔗

1. **User Management**: Link to existing users table
2. **Notifications**: Use existing notification system
3. **Activity Log**: Log all lead activities
4. **Email System**: Use existing mail configuration
5. **File Upload**: Store inspection reports/documents
6. **Calendar**: Integrate inspection dates with calendar

## Key Differences: BuildPro vs ERP

| Feature | BuildPro | ERP Implementation |
|---------|----------|-------------------|
| Database | Supabase (PostgreSQL) | MySQL via Laravel |
| Frontend | React + TypeScript | Blade + JavaScript |
| Auth | Supabase Auth | Laravel Sanctum/Session |
| Real-time | Supabase Realtime | Laravel Echo/Pusher |
| State | React Query | Server-side rendering |
| Forms | React Hook Form | Laravel Form Validation |
| Charts | Recharts | Chart.js / ApexCharts |

## Implementation Checklist

- [ ] Create database migrations
- [ ] Create Lead and LeadStatus models
- [ ] Create API controllers and routes
- [ ] Create blade templates
- [ ] Add navigation menu items
- [ ] Setup permissions and roles
- [ ] Create email templates
- [ ] Add activity logging
- [ ] Create analytics dashboard
- [ ] Write API documentation
- [ ] Add unit tests
- [ ] Add feature tests
- [ ] Create user guide

## API Response Examples

### GET /api/leads
```json
{
  "data": [
    {
      "id": 1,
      "client_name": "John Smith",
      "email": "john@example.com",
      "phone_number": "(555) 123-4567",
      "service_requested": "Home Inspection",
      "location": "123 Main St, Phoenix, AZ",
      "inspection_date": "2024-01-15",
      "inspection_time": "10:00:00",
      "inspection_charge": 350.00,
      "status": "Inspection Booked",
      "assigned_to": {
        "id": 5,
        "name": "Jane Doe"
      },
      "created_at": "2024-01-10T08:30:00Z"
    }
  ],
  "meta": {
    "total": 150,
    "per_page": 20,
    "current_page": 1
  }
}
```

### GET /api/leads/analytics
```json
{
  "summary": {
    "total_leads": 150,
    "completed": 85,
    "pending": 45,
    "cancelled": 20,
    "conversion_rate": 56.67,
    "total_revenue": 29750.00,
    "average_charge": 350.00
  },
  "by_status": {
    "Intake": 15,
    "Contacted": 12,
    "Inspection Booked": 18,
    "Positive": 45
  },
  "by_service": {
    "Home Inspection": 65,
    "Commercial Inspection": 25,
    "Pre-Purchase Inspection": 35
  },
  "monthly_trend": [
    {"month": "Jan", "leads": 35, "completed": 20},
    {"month": "Feb", "leads": 42, "completed": 28}
  ]
}
```

## Next Steps

1. Review and approve this implementation plan
2. Create database migrations
3. Build backend models and controllers
4. Create frontend views
5. Test thoroughly
6. Deploy to staging
7. User acceptance testing
8. Deploy to production

## Notes

- The module is production-ready in BuildPro
- All features are well-tested
- Responsive design for mobile/tablet
- Comprehensive analytics
- Email notifications working
- Dynamic status management allows easy customization
- Integration with existing ERP modules should be seamless

---
*Document created: January 5, 2026*
*Source: BuildPro Application Analysis*
