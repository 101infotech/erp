# 🚀 Enhanced Leads Module - Comprehensive ERP Implementation Plan

**Document Version:** 1.0  
**Date:** January 15, 2026  
**Status:** Planning Phase  
**Target:** Saubhagya Group (Design / Construction / Renovation Business)

---

## 📋 Executive Summary

Current state: Basic lead capture and service request system  
Target state: **Enterprise-grade lead lifecycle management** with:
- ✅ 9-stage lead journey (Capture → Conversion → Project)
- ✅ Role-based access control
- ✅ Workflow automation triggers
- ✅ Payment & follow-up tracking
- ✅ Comprehensive analytics & reporting

---

## 🎯 SECTION 1: LEAD LIFECYCLE - 9 STAGES

### Stage 1: Lead Capture
**Purpose:** Someone shows interest

| Aspect | Details |
|--------|---------|
| **Status Values** | New Lead, Inquiry Only, Reference Lead, Walk-in, Online Lead |
| **Entry Criteria** | Phone call / WhatsApp / Visit / Referral |
| **Mandatory Fields** | Lead Source, Contact Person, Phone, Address, Work Type |
| **Ownership** | Sales/Reception Team |
| **Duration** | 0-1 day |
| **Auto Action** | Send acknowledgment email |

---

### Stage 2: Lead Qualification
**Purpose:** Check if lead is serious & feasible

| Aspect | Details |
|--------|---------|
| **Status Values** | Contacted, Not Reachable, Interested, Not Interested, Budget Issue, Invalid Lead |
| **Decision Point** | ✅ Interested → Move to Stage 3 / ❌ Not Interested → Closed (Lost) |
| **Key Fields** | Expected Budget, Project Type, Location Feasibility, Qualification Remarks |
| **Ownership** | Sales Executive |
| **Duration** | 1-3 days |
| **Auto Action** | Reminder if not contacted in 48 hours |

---

### Stage 3: Site Visit Planning
**Purpose:** Arrange site inspection

| Aspect | Details |
|--------|---------|
| **Status Values** | Site Visit Scheduled, Client Rescheduled, Team Assigned, Visit Cancelled |
| **Mandatory Fields** | Site Visit Date, Assigned Team, Visit Type (Office/On-site), Visit Confirmation Status |
| **Ownership** | Site Coordinator / Sales Team |
| **Duration** | 1-7 days |
| **Auto Action** | SMS/Email reminder 1 day before visit |

---

### Stage 4: Site Visit Completed
**Purpose:** Technical understanding & measurements

| Aspect | Details |
|--------|---------|
| **Status Values** | Site Visit Done, Measurements Taken, Client Not Available, Follow-up Required |
| **Key Outputs** | Photos uploaded, Measurement completed, Notes added |
| **Ownership** | Inspection Team |
| **Duration** | 1 day (visit) + follow-up |
| **Auto Action** | Auto-move to Design Phase if measurements complete |

---

### Stage 5: Proposal / Design Phase
**Purpose:** Deliver design estimate

| Aspect | Details |
|--------|---------|
| **Status Values** | 2D In Progress, 2D Done, 3D In Progress, 3D Done, Cost Estimation Shared, Revision Requested |
| **Important Fields** | Design Version, No. of Revisions, Estimated Cost, Timeline Shared |
| **Ownership** | Design Team |
| **Duration** | 3-7 days (per revision) |
| **Auto Action** | Notify client when design ready |

---

### Stage 6: Negotiation & Approval
**Purpose:** Final decision

| Aspect | Details |
|--------|---------|
| **Status Values** | Proposal Sent, Negotiation Ongoing, Discount Requested, Approved, On Hold |
| **Decision Point** | ✅ Approved → Stage 7 / ❌ No Response → Follow-up loop / ❌ Rejected → Closed (Lost) |
| **Key Fields** | Final Quoted Amount, Discount %, Approval Date |
| **Ownership** | Sales Manager |
| **Duration** | 2-14 days |
| **Auto Action** | Follow-up email if no response in 5 days |

---

### Stage 7: Booking / Advance Payment
**Purpose:** Convert lead to client

| Aspect | Details |
|--------|---------|
| **Status Values** | Advance Pending, Partially Paid, Fully Paid, Booking Confirmed |
| **Mandatory Fields** | Total Payment, Payment Mode, Received By, Payment Date, Booking Date |
| **Ownership** | Accounts / Reception |
| **Duration** | 1-3 days |
| **Auto Action** | Auto-move to Stage 8 when full payment received |

---

### Stage 8: Converted to Project
**Purpose:** Move to execution

| Aspect | Details |
|--------|---------|
| **Status Values** | Converted to Project, Handed Over to Execution Team |
| **ERP Action** | Lead moves from **Leads Module → Projects Module** |
| **Ownership** | Project Manager |
| **Duration** | 1 day (administrative) |
| **Auto Action** | Create new Project record, Archive Lead |

---

### Stage 9: Closed (Lost / Cancelled)
**Purpose:** Clean closure

| Aspect | Details |
|--------|---------|
| **Status Values** | Lost – Budget Issue, Lost – No Response, Lost – Competitor, Cancelled by Client, Converted to Project |
| **Mandatory Field** | Loss Reason |
| **Ownership** | Sales Manager |
| **Duration** | Final |
| **Auto Action** | Archive lead, generate closure report |

---

## 📊 SECTION 2: DATABASE SCHEMA ENHANCEMENTS

### Current Tables
- ✅ `service_leads` - Basic lead info
- ✅ `lead_statuses` - Status management

### New Tables Required

#### 2.1 `lead_stages` Table
```sql
CREATE TABLE lead_stages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  stage_number INT NOT NULL UNIQUE,
  stage_name VARCHAR(100) NOT NULL,
  description TEXT,
  auto_timeout_days INT,
  requires_action BOOLEAN DEFAULT true,
  notification_template VARCHAR(255),
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  INDEX idx_stage_number (stage_number),
  INDEX idx_is_active (is_active)
);
```

#### 2.2 Enhanced `service_leads` Table (New Columns)
```sql
ALTER TABLE service_leads ADD COLUMN (
  -- Lead Hierarchy
  lead_source ENUM('phone', 'whatsapp', 'walk_in', 'referral', 'online', 'website', 'other') DEFAULT 'phone',
  lead_owner_id BIGINT UNSIGNED,
  lead_stage_id BIGINT UNSIGNED,
  lead_priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
  
  -- Client Enhanced
  secondary_phone VARCHAR(20),
  address_line_2 VARCHAR(255),
  location_map_link VARCHAR(255),
  property_type ENUM('house', 'commercial', 'apartment', 'other'),
  
  -- Site Visit
  site_visit_required BOOLEAN DEFAULT true,
  site_visit_scheduled_date DATE,
  site_visit_completed_at TIMESTAMP NULL,
  site_visit_type ENUM('office', 'on_site') DEFAULT 'on_site',
  site_visit_outcome VARCHAR(255),
  site_visit_remarks TEXT,
  next_action_date DATE,
  
  -- Design & Proposal
  design_type ENUM('2d', '3d', 'both') DEFAULT '2d',
  design_status VARCHAR(100),
  design_revision_count INT DEFAULT 0,
  estimated_budget DECIMAL(15,2),
  final_quoted_amount DECIMAL(15,2),
  proposal_sent_date DATE,
  proposal_version INT DEFAULT 1,
  
  -- Payment Tracking
  total_payment_required DECIMAL(15,2),
  advance_required DECIMAL(15,2),
  payment_received DECIMAL(15,2) DEFAULT 0,
  payment_mode ENUM('cash', 'bank_transfer', 'check', 'online', 'other'),
  payment_received_by_id BIGINT UNSIGNED,
  payment_received_date DATE,
  
  -- Follow-ups (structured)
  last_follow_up_date DATE,
  last_follow_up_type VARCHAR(50),
  next_follow_up_date DATE,
  follow_up_owner_id BIGINT UNSIGNED,
  
  -- Reports
  report_required BOOLEAN DEFAULT false,
  report_sent_date DATE,
  report_sent_by_id BIGINT UNSIGNED,
  client_feedback TEXT,
  
  -- Closure
  loss_reason VARCHAR(255),
  closure_date DATE,
  
  -- Metadata
  metadata JSON,
  
  FOREIGN KEY (lead_owner_id) REFERENCES users(id),
  FOREIGN KEY (lead_stage_id) REFERENCES lead_stages(id),
  FOREIGN KEY (payment_received_by_id) REFERENCES users(id),
  FOREIGN KEY (report_sent_by_id) REFERENCES users(id),
  FOREIGN KEY (follow_up_owner_id) REFERENCES users(id),
  
  INDEX idx_lead_stage (lead_stage_id),
  INDEX idx_lead_owner (lead_owner_id),
  INDEX idx_lead_priority (lead_priority),
  INDEX idx_site_visit_scheduled (site_visit_scheduled_date),
  INDEX idx_proposal_sent (proposal_sent_date),
  INDEX idx_next_action (next_action_date)
);
```

#### 2.3 `lead_follow_ups` Table (New - Repeatable)
```sql
CREATE TABLE lead_follow_ups (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lead_id BIGINT UNSIGNED NOT NULL,
  follow_up_date DATE NOT NULL,
  follow_up_type ENUM('call', 'visit', 'whatsapp', 'email', 'sms') NOT NULL,
  follow_up_outcome VARCHAR(255),
  follow_up_notes TEXT,
  next_follow_up_date DATE,
  follow_up_owner_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (lead_id) REFERENCES service_leads(id) ON DELETE CASCADE,
  FOREIGN KEY (follow_up_owner_id) REFERENCES users(id),
  
  INDEX idx_lead_id (lead_id),
  INDEX idx_follow_up_date (follow_up_date),
  INDEX idx_follow_up_owner (follow_up_owner_id)
);
```

#### 2.4 `lead_payments` Table (New - Repeatable)
```sql
CREATE TABLE lead_payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lead_id BIGINT UNSIGNED NOT NULL,
  payment_amount DECIMAL(15,2) NOT NULL,
  payment_date DATE NOT NULL,
  payment_mode ENUM('cash', 'bank_transfer', 'check', 'online', 'other') NOT NULL,
  reference_number VARCHAR(100),
  received_by_id BIGINT UNSIGNED NOT NULL,
  payment_type ENUM('advance', 'partial', 'full') NOT NULL,
  notes TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (lead_id) REFERENCES service_leads(id) ON DELETE CASCADE,
  FOREIGN KEY (received_by_id) REFERENCES users(id),
  
  INDEX idx_lead_id (lead_id),
  INDEX idx_payment_date (payment_date),
  INDEX idx_received_by (received_by_id)
);
```

#### 2.5 `lead_documents` Table (New)
```sql
CREATE TABLE lead_documents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lead_id BIGINT UNSIGNED NOT NULL,
  document_type ENUM('site_photo', 'design_2d', 'design_3d', 'cost_estimate', 'proposal', 'report', 'contract', 'other') NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_size INT,
  mime_type VARCHAR(50),
  uploaded_by_id BIGINT UNSIGNED NOT NULL,
  description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (lead_id) REFERENCES service_leads(id) ON DELETE CASCADE,
  FOREIGN KEY (uploaded_by_id) REFERENCES users(id),
  
  INDEX idx_lead_id (lead_id),
  INDEX idx_document_type (document_type)
);
```

---

## 🔑 SECTION 3: ENHANCED COLUMN STRUCTURE

### 🔹 Basic Lead Info
```
┌─ Lead ID (Auto)
├─ Lead Source (enum: phone, whatsapp, walk_in, referral, online, website)
├─ Lead Owner (FK: users)
├─ Lead Stage (FK: lead_stages)
├─ Lead Status (FK: lead_statuses) [7.1 NEW]
├─ Priority (enum: high, medium, low)
└─ Created At
```

### 🔹 Client Details
```
┌─ Client Name
├─ Primary Phone
├─ Secondary Phone [NEW]
├─ Email
├─ Address Line 1
├─ Address Line 2 [NEW]
├─ City
├─ State/Province
├─ Postal Code
├─ Country
├─ Location Map Link [NEW]
├─ Work Type
└─ Property Type [NEW] (House / Commercial / Apartment / Other)
```

### 🔹 Site Visit Management
```
┌─ Site Visit Required (Yes/No)
├─ Site Visit Scheduled Date [NEW]
├─ Site Visit Completed At [NEW]
├─ Assigned Team [FK: users]
├─ Visit Type [NEW] (Office / On-site)
├─ Visit Outcome [NEW]
├─ Visit Remarks [NEW]
└─ Next Action Date [NEW]
```

### 🔹 Design & Proposal
```
┌─ Design Type (2D / 3D / Both)
├─ Design Status [NEW]
├─ Revision Count [NEW]
├─ Estimated Budget
├─ Final Quoted Amount [NEW]
├─ Proposal Sent Date [NEW]
├─ Proposal Version [NEW]
└─ Timeline Shared (Yes/No)
```

### 🔹 Payment Tracking
```
┌─ Total Payment Required [NEW]
├─ Advance Required [NEW]
├─ Payment Received [NEW]
├─ Payment Mode [NEW]
├─ Received By [NEW] (FK: users)
├─ Payment Received Date [NEW]
└─ Payment Pending (Calculated)
```

### 🔹 Follow-ups (Repeatable - New Table)
```
┌─ Lead ID (FK)
├─ Follow-up Date
├─ Follow-up Type (Call / Visit / WhatsApp / Email / SMS)
├─ Follow-up Outcome
├─ Next Follow-up Date
├─ Follow-up Owner (FK: users)
└─ Follow-up Notes
```

### 🔹 Communication & Reports
```
┌─ Report Required (Yes/No)
├─ Report Send Date
├─ Report Sent By (FK: users)
├─ Client Feedback
└─ Internal Remarks
```

### 🔹 Closure (if Lost)
```
┌─ Loss Reason (enum: budget, no_response, competitor, client_cancelled)
├─ Closure Date
└─ Closure Notes
```

---

## 🎬 SECTION 4: IMPLEMENTATION PLAN - 4 PHASES

### PHASE 1: Database & Models (Week 1)
**Deliverables:**
- [ ] Create 5 new migrations
- [ ] Seed `lead_stages` with 9 stages
- [ ] Create models: `LeadStage`, `LeadFollowUp`, `LeadPayment`, `LeadDocument`
- [ ] Update `ServiceLead` model with new relationships
- [ ] Add scopes and query builders
- [ ] Database validation & testing

**Estimated Time:** 2-3 days

---

### PHASE 2: Backend API & Controllers (Week 2)
**Deliverables:**
- [ ] Create/Update Controllers:
  - `LeadController` - Main CRUD + stage management
  - `LeadFollowUpController` - Follow-up management
  - `LeadPaymentController` - Payment tracking
  - `LeadDocumentController` - File uploads
  - `LeadAnalyticsController` - Enhanced reporting
- [ ] Create Events & Listeners for stage transitions
- [ ] Add Queue jobs for auto-notifications
- [ ] API validation & testing
- [ ] Create 20+ API endpoints

**Estimated Time:** 4-5 days

---

### PHASE 3: Workflow Rules & Automation (Week 2-3)
**Deliverables:**
- [ ] Auto-stage transitions:
  - Payment received → Booking confirmed
  - Measurements complete → Design phase
  - Proposal approved → Booking
- [ ] Auto-reminders:
  - No follow-up in 3 days → Notification
  - Payment pending > 7 days → Reminder
  - Proposal awaiting response > 5 days → Reminder
- [ ] Email notifications:
  - Lead assignment
  - Stage transitions
  - Payment received
  - Site visit scheduled
  - Design ready
- [ ] Queue jobs for scheduled tasks

**Estimated Time:** 3-4 days

---

### PHASE 4: Frontend & Dashboard (Week 3-4)
**Deliverables:**
- [ ] Lead List View:
  - Kanban board (by stage)
  - Table view (filterable)
  - Quick filters (priority, source, owner)
  - Bulk actions
- [ ] Lead Detail View:
  - Stage timeline
  - Activity log
  - Follow-ups section
  - Payments section
  - Documents section
- [ ] Lead Creation Wizard:
  - Multi-step form
  - Smart field validation
  - Stage-based fields
- [ ] Dashboard:
  - Total leads by stage
  - Conversion funnel
  - Revenue pipeline
  - Staff performance
  - Lost reasons analysis
  - Site visits pending
  - Payments pending
- [ ] Reports:
  - Lead source analysis
  - Conversion rate by source
  - Average cycle time
  - Payment collection efficiency
  - Lost lead reasons

**Estimated Time:** 5-7 days

---

## 👥 SECTION 5: ROLE-BASED ACCESS CONTROL

### 1. **Admin** (Full Access)
```
✅ View all leads
✅ Create, edit, delete any lead
✅ Manage lead stages & statuses
✅ View full reports & analytics
✅ Access all payments
✅ Manage user permissions
✅ Configure automation rules
```

### 2. **Sales Manager**
```
✅ View all leads
✅ Create, edit leads
✅ Assign leads to team
✅ Move leads between stages
✅ View team performance
✅ Generate sales reports
❌ View payments (read-only)
❌ Delete leads
```

### 3. **Sales Executive**
```
✅ View assigned leads
✅ Create new leads
✅ Edit assigned leads
✅ Update follow-ups
✅ View own performance
❌ Move to financial stages (Payment onwards)
❌ View other team member leads
```

### 4. **Design Team**
```
✅ View leads in design stage
✅ Update design status
✅ Upload design files
✅ View client feedback
❌ Create leads
❌ Change lead status
```

### 5. **Accounts/Finance**
```
✅ View all payment info
✅ Record payments
✅ View payment reports
✅ Generate invoices
❌ View full lead details
❌ Change lead stages
```

### 6. **Site Coordinator**
```
✅ View site visit scheduled leads
✅ Schedule site visits
✅ Update visit outcome
✅ Upload site photos
❌ Move to other stages
❌ View payment info
```

---

## 📊 SECTION 6: DASHBOARD & REPORTS

### Main Dashboard Components
```
┌─────────────────────────────────────────────┐
│          LEADS DASHBOARD                    │
├─────────────────────────────────────────────┤
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │ KPI CARDS                            │  │
│  │ • Total Leads                        │  │
│  │ • Conversion Rate (%)                │  │
│  │ • Pending Payments                   │  │
│  │ • Site Visits Today                  │  │
│  └──────────────────────────────────────┘  │
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │ CONVERSION FUNNEL (Waterfall Chart)  │  │
│  │ Stage 1: 100 leads                   │  │
│  │ Stage 2: 85 leads (85%)              │  │
│  │ Stage 3: 70 leads (82%)              │  │
│  │ ...                                  │  │
│  │ Stage 8: 5 projects (25%)            │  │
│  └──────────────────────────────────────┘  │
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │ REVENUE PIPELINE                     │  │
│  │ Estimated Revenue by Stage           │  │
│  │ Chart: Column/Bar                    │  │
│  └──────────────────────────────────────┘  │
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │ LOST LEAD REASONS                    │  │
│  │ • Budget Issue: 45%                  │  │
│  │ • No Response: 35%                   │  │
│  │ • Competitor: 15%                    │  │
│  │ • Client Cancelled: 5%               │  │
│  └──────────────────────────────────────┘  │
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │ STAFF PERFORMANCE                    │  │
│  │ Sales Person A: 12 conversions       │  │
│  │ Sales Person B: 8 conversions        │  │
│  │ ...                                  │  │
│  └──────────────────────────────────────┘  │
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │ PENDING ACTIONS (Today)              │  │
│  │ • Site Visits: 3                     │  │
│  │ • Proposals Due: 2                   │  │
│  │ • Follow-ups: 5                      │  │
│  │ • Payments Due: 4                    │  │
│  └──────────────────────────────────────┘  │
│                                             │
└─────────────────────────────────────────────┘
```

### Report Templates
1. **Daily Sales Report** - Leads created, converted, lost
2. **Weekly Pipeline Report** - Stage distribution, revenue pipeline
3. **Monthly Performance** - Conversion rate, staff KPIs
4. **Quarterly Analysis** - Trends, lost reason analysis
5. **Custom Reports** - Flexible date ranges, filters

---

## ✅ SECTION 7: KEY FEATURES & BENEFITS

### Features
- ✅ **9-Stage Lead Pipeline** - Clear lead journey
- ✅ **Repeatable Follow-ups** - Structured communication tracking
- ✅ **Payment Management** - Track advance, partial, full payments
- ✅ **Document Storage** - Photos, designs, contracts, reports
- ✅ **Auto-Workflow** - Stage transitions, reminders, notifications
- ✅ **Role-Based Access** - 6 different user roles
- ✅ **Analytics Dashboard** - Conversion funnel, revenue pipeline, staff KPIs
- ✅ **Email Notifications** - Automated for key events
- ✅ **Mobile Friendly** - Responsive design for field teams
- ✅ **Bulk Operations** - Assign, update status, move stage

### Benefits for Saubhagya Group
- 🎯 **No Lead Missed** - Automatic reminders & follow-up tracking
- 👤 **Clear Accountability** - Each stage has an owner
- ⚡ **Faster Conversion** - Structured workflow reduces cycle time
- 💰 **Better Payment Control** - Track every rupee
- 📊 **Data-Driven Decisions** - Comprehensive analytics
- 🔄 **Process Standardization** - Everyone follows same workflow
- 📱 **Scalable Design** - Ready for mobile app & multiple locations
- 🤖 **Less Manual Work** - Automation handles routine tasks

---

## 📅 TIMELINE & MILESTONES

```
Week 1: Database & Models
├─ Days 1-2: Migrations & Database Setup
├─ Days 3: Models & Relationships
└─ Days 4-5: Testing & Documentation

Week 2: Backend API
├─ Days 1-2: Controllers & Routes
├─ Days 3: Validation & Error Handling
├─ Days 4-5: Events & Queue Jobs

Week 3: Automation & Frontend (Parallel)
├─ Backend: Auto-workflow & Email Notifications
└─ Frontend: List views, Detail pages, Wizard

Week 4: Dashboard & Reports
├─ Analytics Dashboard
├─ Report Generation
└─ Final Testing & Deployment

Total: 4 weeks
```

---

## 🎓 QUICK REFERENCE: STAGE TRANSITION LOGIC

```
Lead Capture (Stage 1)
    ↓
Lead Qualification (Stage 2) [DECISION: Interested?]
    ├─ YES → Site Visit Planning (Stage 3)
    └─ NO → Closed - Lost (Stage 9)
    ↓
Site Visit Completed (Stage 4)
    ↓
Design Phase (Stage 5)
    ↓
Negotiation (Stage 6) [DECISION: Approved?]
    ├─ YES → Booking/Payment (Stage 7)
    └─ NO → Closed - Lost (Stage 9)
    ↓
Advance Pending → Partially Paid → Fully Paid
    ↓
Booking Confirmed (Stage 7)
    ↓
Converted to Project (Stage 8)
    ↓
Project Execution (Projects Module)
```

---

## 🔧 TECHNICAL NOTES

### Technology Stack
- **Backend:** Laravel 11+
- **Frontend:** React + Vite (if React dashboard) or Blade Templates
- **Database:** MySQL 8+
- **Queue System:** Laravel Queue (for notifications)
- **Caching:** Redis (for analytics)
- **File Storage:** Local/S3

### Key Libraries to Use
- `laravel/sanctum` - API authentication
- `spatie/laravel-activitylog` - Activity logging
- `maatwebsite/laravel-excel` - Excel export
- `barryvdh/laravel-dompdf` - PDF generation
- `laravel/horizon` - Queue monitoring

### Performance Considerations
- Index on `(lead_stage_id, created_at)` for list views
- Cache lead statuses (60 min)
- Use eager loading for relationships
- Queue heavy operations (PDF generation, emails)
- Archive closed leads after 6 months

---

## 📝 NEXT STEPS

1. ✅ **Approval** - Review this plan with team
2. **Phase 1 Start** - Create database migrations
3. **Phase 2 Start** - Build API controllers
4. **Phase 3 Start** - Implement automation
5. **Phase 4 Start** - Build frontend
6. **Testing** - QA across all modules
7. **Deployment** - Production rollout
8. **Training** - Team onboarding

---

## 📞 QUESTIONS & CLARIFICATIONS

### Q1: Should we archive closed leads or keep them visible?
**A:** Recommended: Archive after 6 months (soft delete), searchable via archive view

### Q2: Can a lead skip stages?
**A:** Recommended: No auto-skip. Manual override available for special cases only.

### Q3: Multi-location support needed?
**A:** Add `location_id` (FK: sites/branches) to `service_leads` for multi-location tracking

### Q4: Export formats?
**A:** Support Excel (.xlsx) and PDF. CSV optional.

### Q5: Mobile app planned?
**A:** Yes. This API design supports iOS/Android apps (consider React Native)

---

**Document Owner:** System Administrator  
**Last Updated:** January 15, 2026  
**Version:** 1.0
