# 📊 Enhanced Leads Module - Visual Reference & Architecture

**Quick Visual Guide for Understanding the System**  
**Version:** 1.0 | **Date:** January 15, 2026

---

## 🏗️ SYSTEM ARCHITECTURE

### High-Level Overview
```
┌─────────────────────────────────────────────────────────────────┐
│                        LEADS MODULE SYSTEM                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ FRONTEND LAYER                                            │   │
│  │ ├─ Dashboard                                              │   │
│  │ ├─ Lead List Views (Table, Kanban, Grid)                │   │
│  │ ├─ Lead Details                                           │   │
│  │ ├─ Forms (Create, Edit)                                  │   │
│  │ └─ Analytics & Reports                                   │   │
│  └──────────────────────────────────────────────────────────┘   │
│                            ↓↑                                     │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ API LAYER (43+ Endpoints)                               │   │
│  │ ├─ LeadController (15 endpoints)                        │   │
│  │ ├─ LeadFollowUpController (6 endpoints)                │   │
│  │ ├─ LeadPaymentController (6 endpoints)                 │   │
│  │ ├─ LeadDocumentController (5 endpoints)                │   │
│  │ └─ LeadAnalyticsController (8+ endpoints)              │   │
│  └──────────────────────────────────────────────────────────┘   │
│                            ↓↑                                     │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ BUSINESS LOGIC LAYER                                     │   │
│  │ ├─ LeadService                                            │   │
│  │ ├─ LeadStageService                                       │   │
│  │ ├─ LeadAnalyticsService                                   │   │
│  │ ├─ AutomationRulesEngine                                 │   │
│  │ ├─ Events & Listeners                                     │   │
│  │ └─ Queue Jobs                                             │   │
│  └──────────────────────────────────────────────────────────┘   │
│                            ↓↑                                     │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ DATA LAYER (6 Tables, 80+ Columns)                       │   │
│  │ ├─ service_leads (Lead master)                           │   │
│  │ ├─ lead_stages (9 journey stages)                        │   │
│  │ ├─ lead_statuses (Status options)                        │   │
│  │ ├─ lead_follow_ups (Follow-up history)                  │   │
│  │ ├─ lead_payments (Payment records)                       │   │
│  │ └─ lead_documents (Uploaded files)                       │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 LEAD JOURNEY VISUALIZATION

### 9-Stage Pipeline Flow

```
                    STAGE 1: CAPTURE
                         ↓
                   WHO:  Reception/Sales
                   HOW:  Phone, WhatsApp, Walk-in, Referral
                   TIME: Day 1
                   ┌──────────────────┐
                   │ Lead Captured    │ → Email acknowledgment
                   │ Auto-reminder: 1d│
                   └──────────────────┘
                         ↓
                    STAGE 2: QUALIFICATION
                         ↓
                   WHO:  Sales Executive
                   HOW:  Call, WhatsApp, Email
                   TIME: 1-3 days
                   ┌──────────────────┐
                   │ Lead Qualified?  │
                   └──────────────────┘
                      /            \
                    YES             NO
                    /                 \
                   ↓                   ↓
            [Continue]          [Stage 9: CLOSED]
                   ↓                   ↓
            STAGE 3: SITE          Lost Reason
            VISIT PLANNING         Recorded
                   ↓
                   WHO:  Site Coordinator
                   HOW:  Phone call + SMS reminder 1d before
                   TIME: 1-7 days
                   ┌──────────────────────┐
                   │ Visit Scheduled      │
                   │ Team Assigned        │
                   │ Confirmation Sent    │
                   └──────────────────────┘
                         ↓
            STAGE 4: SITE VISIT
                COMPLETED
                   ↓
                   WHO:  Inspection Team
                   HOW:  On-site visit
                   TIME: 1 day + follow-up
                   ┌──────────────────────┐
                   │ Photos Uploaded      │
                   │ Measurements Taken   │
                   │ Notes Added          │
                   │ Auto-move to Stage 5 │
                   └──────────────────────┘
                         ↓
            STAGE 5: PROPOSAL / 
               DESIGN PHASE
                   ↓
                   WHO:  Design Team
                   HOW:  2D/3D design, costing
                   TIME: 3-7 days per revision
                   ┌──────────────────────┐
                   │ Design In Progress   │
                   │ (2D → 3D → Cost)     │
                   │ Notify when ready    │
                   └──────────────────────┘
                         ↓
            STAGE 6: NEGOTIATION
               & APPROVAL
                   ↓
                   WHO:  Sales Manager
                   HOW:  Email, Call, Meeting
                   TIME: 2-14 days
                   ┌──────────────────────┐
                   │ Proposal Sent        │
                   │ Awaiting Response    │
                   │ Auto-follow after 5d │
                   └──────────────────────┘
                         ↓
                   APPROVED?
                    /      \
                  YES       NO
                  /          \
                 ↓           [Closed]
            STAGE 7: BOOKING &
            ADVANCE PAYMENT
                   ↓
                   WHO:  Accounts/Reception
                   HOW:  Booking form + Payment
                   TIME: 1-3 days
                   ┌──────────────────────┐
                   │ Advance Pending      │
                   │ Payment Received     │
                   │ Booking Confirmed    │
                   │ Auto-move to Stage 8 │
                   └──────────────────────┘
                         ↓
            STAGE 8: CONVERTED
               TO PROJECT
                   ↓
                   WHO:  Project Manager
                   HOW:  System auto-creates project
                   TIME: 1 day (admin)
                   ┌──────────────────────┐
                   │ Project Created      │
                   │ Team Assigned        │
                   │ Lead Archived        │
                   └──────────────────────┘
                         ↓
            PROJECT EXECUTION
            (Projects Module)
```

---

## 📊 STAGE DURATION & METRICS

```
┌──────┬─────────────────────────┬────────────┬─────────────┬──────────────┐
│Stage │ Name                    │ Duration   │ Ownership   │ Auto-Timeout │
├──────┼─────────────────────────┼────────────┼─────────────┼──────────────┤
│  1   │ Lead Capture            │ 0-1 days   │ Reception   │ 1 day        │
│  2   │ Qualification           │ 1-3 days   │ Sales Exec  │ 3 days       │
│  3   │ Site Visit Planning     │ 1-7 days   │ Coordinator │ 7 days       │
│  4   │ Site Visit Completed    │ 1 day      │ Team        │ 1 day        │
│  5   │ Design Phase            │ 3-7 days   │ Design Team │ 7 days       │
│  6   │ Negotiation & Approval  │ 2-14 days  │ Sales Mgr   │ 14 days      │
│  7   │ Booking & Payment       │ 1-3 days   │ Accounts    │ 3 days       │
│  8   │ Converted to Project    │ 1 day      │ Project Mgr │ N/A          │
│  9   │ Closed (Lost)           │ Final      │ Sales Mgr   │ N/A          │
└──────┴─────────────────────────┴────────────┴─────────────┴──────────────┘

Total Lead-to-Project Conversion Time: 10-40 days (typical)
Target: 21 days (3 weeks) for standard project
```

---

## 🗄️ DATABASE SCHEMA DIAGRAM

### Table Relationships

```
                            USERS TABLE
                                 ↑
                    ┌────────────┼────────────┐
                    │            │            │
                    │            │            │
              lead_owner_id  created_by  inspection_assigned_to
                    │            │            │
                    ↓            ↓            ↓
        ┌─────────────────────────────────────────────┐
        │  SERVICE_LEADS (Master Table)              │
        │  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │
        │  • id (Primary Key)                        │
        │  • lead_source, lead_owner_id, stage_id   │
        │  • client_name, phone, email, address     │
        │  • site_visit fields, design fields       │
        │  • payment_received, remarks               │
        │  • created_at, updated_at, deleted_at      │
        └─────────────────────────────────────────────┘
                    ↑        ↑        ↑
                    │        │        │
            lead_id (FK) lead_id (FK) lead_id (FK)
                    │        │        │
        ┌───────────┴──┐  ┌──┴───────────┐  ┌──────────────────┐
        │               │  │               │  │                  │
    LEAD_STAGES    LEAD_FOLLOW_UPS    LEAD_PAYMENTS      LEAD_DOCUMENTS
    ──────────     ──────────────     ──────────────     ──────────────
    • id           • id               • id               • id
    • stage_number • follow_up_date   • payment_amount   • document_type
    • stage_name   • follow_up_type   • payment_date     • file_path
    • is_active    • follow_up_owner  • received_by      • file_size
                   • next_follow_up   • payment_mode     • uploaded_by

                            LEAD_STATUSES
                            ──────────────
                            • status_key
                            • display_name
                            • color_class
                            • priority
                            • is_active
```

---

## 🎬 DATA FLOW DIAGRAM

### Creating a New Lead

```
FRONTEND                          BACKEND                         DATABASE
  │                                 │                                 │
  ├─ User fills form                │                                 │
  ├─ Clicks "Create Lead"           │                                 │
  │                                 │                                 │
  ├─────── POST /leads ────────────>│                                 │
  │                                 ├─ Validate input                │
  │                                 ├─ Create ServiceLead            │
  │                                 ├─ Set stage = 1                 │
  │                                 ├─ Set status = "New Lead"       │
  │                                 │                                 │
  │                                 ├──────── INSERT ───────────────>│
  │                                 │                                 │
  │                                 │<───── Lead created ────────────│
  │                                 │                                 │
  │                                 ├─ Fire LeadCreated event        │
  │                                 ├─ Send email notification       │
  │                                 ├─ Queue notification job        │
  │                                 │                                 │
  │<──── 201 Created ───────────────┤                                 │
  │      {lead_id: 123}             │                                 │
  │                                 │                                 │
  ├─ Show success message           │                                 │
  └─ Redirect to lead detail
```

### Moving Lead to Next Stage

```
FRONTEND                          BACKEND                         DATABASE
  │                                 │                                 │
  ├─ User clicks "Move to Stage 5"  │                                 │
  │                                 │                                 │
  ├───── PATCH /leads/{id}/stage ──>│                                 │
  │      {stage_number: 5}          │                                 │
  │                                 ├─ Validate stage transition     │
  │                                 ├─ Check permissions             │
  │                                 │                                 │
  │                                 ├──────── UPDATE ───────────────>│
  │                                 │  stage_id = 5                  │
  │                                 │  status = "Design In Progress" │
  │                                 │                                 │
  │                                 │<── Record updated ─────────────│
  │                                 │                                 │
  │                                 ├─ Fire LeadStageChanged event   │
  │                                 ├─ Check automation rules        │
  │                                 ├─ Send notifications            │
  │                                 │                                 │
  │<──── 200 OK ───────────────────┤                                 │
  │      {stage: 5, ...}            │                                 │
  │                                 │                                 │
  └─ Update UI, show new stage      │
```

### Recording a Payment

```
FRONTEND                          BACKEND                         DATABASE
  │                                 │                                 │
  ├─ User fills payment form        │                                 │
  ├─ Clicks "Record Payment"        │                                 │
  │                                 │                                 │
  ├─ POST /leads/{id}/payments ────>│                                 │
  │   {amount, date, mode}          │                                 │
  │                                 ├─ Validate payment              │
  │                                 ├─ Create LeadPayment record     │
  │                                 │                                 │
  │                                 ├──────── INSERT ───────────────>│
  │                                 │  lead_payments table           │
  │                                 │                                 │
  │                                 │<── Payment recorded ───────────│
  │                                 │                                 │
  │                                 ├─ Update ServiceLead.payment_received
  │                                 │                                 │
  │                                 ├──────── UPDATE ───────────────>│
  │                                 │  service_leads                 │
  │                                 │                                 │
  │                                 │<── Updated ────────────────────│
  │                                 │                                 │
  │                                 ├─ Fire PaymentReceived event    │
  │                                 ├─ Check if full payment         │
  │                                 ├─ If yes: auto-move to Stage 8  │
  │                                 │                                 │
  │<──── 201 Created ───────────────┤                                 │
  │      {payment_id: 456}          │                                 │
  │                                 │                                 │
  └─ Update payment status display   │
```

---

## 📈 CONVERSION FUNNEL VISUALIZATION

### Typical Lead-to-Project Conversion

```
100 Leads Captured (Stage 1)
  │
  ├─> 85 Qualified (Stage 2) ........................ 85%
  │     │
  │     ├─> 70 Site Visit Booked (Stage 3) ....... 82%
  │     │     │
  │     │     ├─> 68 Site Visit Completed (Stage 4) 97%
  │     │     │     │
  │     │     │     ├─> 65 Design Phase (Stage 5) ... 96%
  │     │     │     │     │
  │     │     │     │     ├─> 60 Negotiation (Stage 6) 92%
  │     │     │     │     │     │
  │     │     │     │     │     ├─> 55 Booked (Stage 7) 92%
  │     │     │     │     │     │     │
  │     │     │     │     │     │     ├─> 50 Projects (Stage 8) 91%
  │     │     │     │     │     │     │
  │     │     │     │     │     │     └─> 5 Not Converted
  │     │     │     │     │     │
  │     │     │     │     │     └─> 5 Lost in Negotiation
  │     │     │     │     │
  │     │     │     │     └─> 3 Design Issues
  │     │     │     │
  │     │     │     └─> 2 No Show
  │     │     │
  │     │     └─> 2 Cancelled
  │     │
  │     └─> 15 Not Interested
  │
  └─> 15 Not Reachable

CONVERSION RATE: 50% (50 projects from 100 leads)
AVERAGE CYCLE TIME: 21 days
LOST REASON BREAKDOWN:
  • No Response: 35%
  • Budget Issue: 45%
  • Competitor: 15%
  • Other: 5%
```

---

## 🔐 PERMISSION HIERARCHY

### Access Control Matrix

```
                    ADMIN  SALES_MGR  SALES_EXEC  DESIGN  ACCOUNTS  SITE_COORD
                    ─────  ─────────  ──────────  ──────  ────────  ──────────
View All Leads        ✓        ✓          ✗          ✗       ✗         ✗
View Assigned         ✓        ✓          ✓          ✓       ✓         ✓
Create Lead           ✓        ✓          ✓          ✗       ✗         ✗
Edit Lead             ✓        ✓         Own         ✗       ✗         ✗
Delete Lead           ✓        ✗          ✗          ✗       ✗         ✗
Move Stage            ✓        ✓        Limited      ✗       ✗       Partial
Record Payment        ✓        ✓          ✗          ✗       ✓         ✗
View Payments         ✓        ✓         Own         ✗       ✓         ✗
Add Follow-up         ✓        ✓          ✓          ✗       ✗         ✓
Upload Documents      ✓        ✓          ✓          ✓       ✗         ✓
View Analytics        ✓        ✓         Own         ✗       ✓         ✗
```

**Legend:**
- ✓ = Full access
- Own = Only their own records
- Partial = Limited access to specific stages
- ✗ = No access

---

## 📊 DASHBOARD MOCKUP

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                           │
│  LEADS DASHBOARD                                                         │
│  ════════════════════════════════════════════════════════════════════   │
│                                                                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐   │
│  │ Total Leads │  │ Conversion  │  │  Pending    │  │   Site      │   │
│  │   287       │  │   Rate      │  │  Payments   │  │   Visits    │   │
│  │             │  │   48%       │  │   $245,000  │  │   Today: 3  │   │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘   │
│                                                                           │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ CONVERSION FUNNEL (Last 30 Days)                                   │ │
│  │                                                                    │ │
│  │  Captured    Qualified   Site Booked  Design Phase  Negotiation  │ │
│  │     100         85          70           65           55        │ │
│  │      ▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇                               │ │
│  │      100%        85%         82%         93%          85%        │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                           │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ REVENUE PIPELINE by Stage                                          │ │
│  │                                                                    │ │
│  │  Stage 5: Design Phase              ████████████ $125,000 (25%)  │ │
│  │  Stage 6: Negotiation & Approval    ███████████████ $165,000     │ │
│  │  Stage 7: Booking                   ████████████████ $200,000    │ │
│  │  Stage 8: Converted (Projects)      ██████████████ $180,000      │ │
│  │                                                                    │ │
│  │  Total Pipeline: $670,000                                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                           │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ LOST REASON ANALYSIS                                               │ │
│  │                                                                    │ │
│  │  Budget Issue        ████████████████████ 45% (12 leads)         │ │
│  │  No Response         ███████████████ 35% (9 leads)               │ │
│  │  Competitor          ████ 15% (4 leads)                          │ │
│  │  Other               ██ 5% (1 lead)                              │ │
│  │                                                                    │ │
│  │  Total Lost: 26 leads                                             │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                           │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ STAFF PERFORMANCE (Top 5)                                          │ │
│  │                                                                    │ │
│  │  Raj Kumar            ██████████████████████ 15 conversions       │ │
│  │  Priya Singh          ███████████████████ 12 conversions         │ │
│  │  Amit Sharma          ████████████████ 10 conversions           │ │
│  │  Sneha Patel          ██████████████ 8 conversions             │ │
│  │  Vikas Gupta          ████████████ 6 conversions             │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                           │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 📋 AUTOMATION RULES ENGINE

### What Triggers Automatically?

```
┌─────────────────────────────────┬──────────────────────┬──────────────┐
│ EVENT                           │ TRIGGER              │ ACTION       │
├─────────────────────────────────┼──────────────────────┼──────────────┤
│ Lead Created                    │ New lead entered     │ Send email   │
│                                 │                      │ Set reminder │
│                                 │                      │ (1 day)      │
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ Payment Received                │ Payment recorded     │ Update       │
│                                 │ = Full Amount        │ status       │
│                                 │                      │ Auto-move to │
│                                 │                      │ Stage 8      │
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ Measurements Complete           │ Photos + Measure     │ Auto-move to │
│                                 │ fields filled        │ Stage 5      │
│                                 │                      │ (Design)     │
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ No Follow-up in 3 Days          │ next_follow_up_date  │ Email        │
│                                 │ <= today AND         │ reminder to  │
│                                 │ no outcome recorded  │ sales team   │
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ Payment Due for 7+ Days         │ payment_pending >    │ Email        │
│                                 │ 7 days               │ follow-up    │
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ Proposal Not Responded (5+ Days)│ proposal_sent_date   │ Email        │
│                                 │ + 5 days <= today    │ reminder to  │
│                                 │ AND no approval      │ client       │
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ Site Visit - 1 Day Before       │ Scheduled visit date │ SMS/Email    │
│                                 │ = tomorrow           │ reminder to  │
│                                 │                      │ team & client│
│─────────────────────────────────┼──────────────────────┼──────────────┤
│ Closed Leads (Auto-Archive)     │ Monthly cron job     │ Soft delete  │
│                                 │ loss_reason set      │ Archive old  │
│                                 │ + 6 months old       │ closed leads │
└─────────────────────────────────┴──────────────────────┴──────────────┘
```

---

## 🔔 NOTIFICATION FLOW

### When Notifications Are Sent

```
Lead Created (Stage 1)
    ├─ Sales Team: "New lead assigned to you"
    ├─ Manager: "New lead created"
    └─ Email: Welcome + next steps

Stage 2 - No contact for 48 hours
    ├─ Sales Exec: "You have pending leads to contact"
    └─ Notification: Overdue action reminder

Site Visit Scheduled
    ├─ Team: "Site visit scheduled on {date}"
    ├─ Client: "We're visiting on {date}"
    └─ 1 Day Before: SMS reminder to both

Payment Received
    ├─ Finance: "Payment received - ₹{amount}"
    ├─ Manager: "Lead moving to Stage 7"
    └─ Client: "Payment confirmation"

Design Complete
    ├─ Sales: "Design ready, send to client"
    ├─ Client: "Your design is ready"
    └─ Manager: Daily summary

Proposal Awaiting Response (5+ days)
    ├─ Sales: "Follow-up proposal with client"
    ├─ Client: "Awaiting your approval"
    └─ Manager: Escalation needed

Lead Closed
    ├─ Team: "Lead closed - {loss_reason}"
    └─ Manager: Daily summary

Monthly Reports
    ├─ Manager: Conversion metrics, KPIs
    ├─ Finance: Payment summary
    └─ CEO: Executive dashboard
```

---

## 💾 BACKUP & ARCHIVAL STRATEGY

```
ACTIVE LEADS
├─ Stage 1-7 (Open Leads)
├─ Last activity within 3 months
└─ Visible in main UI

COMPLETED LEADS  
├─ Stage 8 (Converted to Projects)
├─ Last activity within 6 months
└─ Visible in reports + archive

ARCHIVED LEADS
├─ Stage 9 (Closed/Lost)
├─ Older than 6 months
└─ Soft deleted (searchable if needed)

Retention Policy:
├─ Keep all lead data permanently
├─ Archive closed leads after 6 months
├─ Monthly backups
└─ Query optimization on archive table
```

---

## 🚀 DEPLOYMENT STAGES

### Rollout Strategy

```
Week 1: Internal Testing
├─ QA team tests all features
├─ Database validation
├─ Permission testing
└─ Performance testing

Week 2: Pilot with Sales Team
├─ 3-5 sales reps use new system
├─ Provide live feedback
├─ Bug fixes & tweaks
└─ Train on new features

Week 3: Department Rollout
├─ All sales team (20+ people)
├─ Daily stand-ups
├─ Issue resolution
└─ Process adjustments

Week 4: Full Company Deployment
├─ All departments
├─ Comprehensive training
├─ 24/7 support
└─ Monitor for issues
```

---

## 📞 QUICK REFERENCE CARD

### For Sales Team
```
Lead Flow:
1. Create → 2. Qualify → 3. Schedule Visit → 4. Visit → 
5. Design → 6. Negotiate → 7. Book → 8. Project

Key Actions:
- Add follow-ups daily
- Update status before leaving
- Attach documents (photos, designs)
- Check pending approvals
```

### For Finance Team
```
Payment Tracking:
1. Lead moves to Stage 7
2. Record payment (Advance/Partial/Full)
3. System auto-updates lead status
4. Generate payment receipt
5. Archive after closure

Key Reports:
- Pending Payments
- Payment Collection Rate
- Outstanding Invoices
- Monthly Revenue
```

### For Management
```
Monitoring:
- Daily: Pending actions, site visits, critical alerts
- Weekly: Team performance, conversion metrics
- Monthly: Revenue pipeline, lost reason analysis
- Quarterly: Trends, improvements, forecasting
```

---

**Document Status:** Ready for Reference  
**Last Updated:** January 15, 2026  
**Version:** 1.0

---

For more details, see:
- **ENHANCED_LEADS_MODULE_PLAN.md** - Full specifications
- **ENHANCED_LEADS_TECHNICAL_SPECS.md** - Technical details
- **IMPLEMENTATION_ROADMAP_QUICKSTART.md** - Step-by-step guide
