# 🔧 Enhanced Leads Module - Technical Implementation Specifications

**Version:** 1.0  
**Date:** January 15, 2026  
**Status:** Ready for Development

---

## 📋 DELIVERABLES CHECKLIST

### ✅ Phase 1: Database & Models (2-3 Days)

#### Migrations to Create
```
✓ create_lead_stages_table.php
✓ alter_service_leads_add_enhanced_columns.php
✓ create_lead_follow_ups_table.php
✓ create_lead_payments_table.php
✓ create_lead_documents_table.php
```

#### Models to Create/Update
```
✓ app/Models/LeadStage.php (NEW)
✓ app/Models/ServiceLead.php (UPDATE)
✓ app/Models/LeadFollowUp.php (NEW)
✓ app/Models/LeadPayment.php (NEW)
✓ app/Models/LeadDocument.php (NEW)
```

#### Seeders
```
✓ database/seeders/LeadStageSeeder.php (9 stages)
✓ database/seeders/LeadStatusSeeder.php (UPDATE - add new statuses)
```

**Files Count:** 11 files
**Estimated LOC:** 800 lines

---

### ✅ Phase 2: Backend API (4-5 Days)

#### Controllers to Create/Update
```
✓ app/Http/Controllers/Admin/LeadController.php (NEW - replaces ServiceLeadController for extended functionality)
✓ app/Http/Controllers/Admin/LeadFollowUpController.php (NEW)
✓ app/Http/Controllers/Admin/LeadPaymentController.php (NEW)
✓ app/Http/Controllers/Admin/LeadDocumentController.php (NEW)
✓ app/Http/Controllers/Admin/LeadAnalyticsController.php (UPDATE)
✓ app/Http/Controllers/Admin/LeadWorkflowController.php (NEW - stage transitions)
```

#### Events & Listeners
```
✓ app/Events/LeadCreated.php
✓ app/Events/LeadStageChanged.php
✓ app/Events/PaymentReceived.php
✓ app/Listeners/SendLeadCreatedNotification.php
✓ app/Listeners/ProcessStageTransition.php
✓ app/Listeners/CheckAutomationRules.php
```

#### Queue Jobs
```
✓ app/Jobs/SendLeadNotification.php
✓ app/Jobs/CheckFollowUpReminders.php
✓ app/Jobs/CheckPaymentReminders.php
✓ app/Jobs/GenerateMonthlyReport.php
✓ app/Jobs/ArchiveClosedLeads.php
```

#### Requests (Validation)
```
✓ app/Http/Requests/StoreLeadRequest.php
✓ app/Http/Requests/UpdateLeadRequest.php
✓ app/Http/Requests/StoreFollowUpRequest.php
✓ app/Http/Requests/StorePaymentRequest.php
✓ app/Http/Requests/StoreDocumentRequest.php
```

#### Services
```
✓ app/Services/LeadService.php (Business logic)
✓ app/Services/LeadStageService.php (Stage management)
✓ app/Services/LeadAnalyticsService.php (Reporting)
```

#### Mail Classes
```
✓ app/Mail/LeadCreatedMail.php (Update)
✓ app/Mail/StageChangedMail.php (NEW)
✓ app/Mail/PaymentReceivedMail.php (NEW)
✓ app/Mail/SiteVisitReminderMail.php (NEW)
✓ app/Mail/FollowUpReminderMail.php (NEW)
```

#### Routes
```
✓ routes/web.php - Add 25+ routes for all controllers
OR
✓ routes/api.php - If building API-first
```

**Files Count:** 25+ files
**Estimated LOC:** 3,500+ lines
**API Endpoints:** 25+ endpoints

---

### ✅ Phase 3: Automation & Workflow (3-4 Days)

#### Configuration Files
```
✓ config/leads.php (NEW - automation rules, defaults)
```

#### Automation Rules Engine
```
✓ app/Services/AutomationRulesEngine.php (NEW)
✓ Database: Store automation rules as JSON/table
```

#### Scheduled Jobs
```
✓ app/Console/Kernel.php (UPDATE)
- Check follow-up reminders (Every 6 hours)
- Check payment reminders (Every 12 hours)
- Check proposal timeout (Every 24 hours)
- Auto-archive closed leads (Weekly)
- Generate reports (Monthly)
```

**Files Count:** 5 files
**Estimated LOC:** 500 lines

---

### ✅ Phase 4: Frontend Views (5-7 Days)

#### Blade Templates (Existing with enhancements)
```
✓ resources/views/admin/leads/index.blade.php (UPDATE - add kanban view option)
✓ resources/views/admin/leads/create.blade.php (UPDATE - enhanced form)
✓ resources/views/admin/leads/edit.blade.php (UPDATE)
✓ resources/views/admin/leads/show.blade.php (UPDATE - add tabs)
  - Details tab
  - Timeline tab
  - Follow-ups tab
  - Payments tab
  - Documents tab
  - Activity log tab

✓ resources/views/admin/leads/kanban.blade.php (NEW - stage board)
✓ resources/views/admin/leads/analytics.blade.php (UPDATE - enhanced)
```

#### Component Views
```
✓ resources/views/admin/leads/components/follow-up-form.blade.php (NEW)
✓ resources/views/admin/leads/components/follow-ups-list.blade.php (NEW)
✓ resources/views/admin/leads/components/payment-form.blade.php (NEW)
✓ resources/views/admin/leads/components/payments-list.blade.php (NEW)
✓ resources/views/admin/leads/components/document-upload.blade.php (NEW)
✓ resources/views/admin/leads/components/documents-list.blade.php (NEW)
✓ resources/views/admin/leads/components/stage-timeline.blade.php (NEW)
✓ resources/views/admin/leads/components/activity-log.blade.php (NEW)
```

#### Email Templates
```
✓ resources/views/emails/lead-created.blade.php (UPDATE)
✓ resources/views/emails/stage-changed.blade.php (NEW)
✓ resources/views/emails/payment-received.blade.php (NEW)
✓ resources/views/emails/site-visit-reminder.blade.php (NEW)
✓ resources/views/emails/follow-up-reminder.blade.php (NEW)
```

**Files Count:** 20+ files
**Estimated LOC:** 2,000+ lines

---

## 📊 API ENDPOINTS SPECIFICATION

### Lead Management Endpoints (15)
```
GET    /admin/leads                          → List all leads (with filters)
POST   /admin/leads                          → Create new lead
GET    /admin/leads/kanban                   → Kanban view
GET    /admin/leads/{id}                     → View lead detail
PUT    /admin/leads/{id}                     → Update lead
DELETE /admin/leads/{id}                     → Soft delete lead
GET    /admin/leads/{id}/timeline            → Get activity timeline
PATCH  /admin/leads/{id}/stage               → Move to stage
PATCH  /admin/leads/{id}/status              → Update status
PATCH  /admin/leads/{id}/assign              → Assign to user
GET    /admin/leads/{id}/export              → Export single lead
POST   /admin/leads/bulk-action              → Bulk operations
GET    /admin/leads/search                   → Advanced search
GET    /admin/leads/export-excel             → Export filtered list
```

### Follow-Up Endpoints (6)
```
GET    /admin/leads/{lead_id}/follow-ups     → List follow-ups
POST   /admin/leads/{lead_id}/follow-ups     → Create follow-up
GET    /admin/leads/{lead_id}/follow-ups/{id} → View follow-up
PUT    /admin/leads/{lead_id}/follow-ups/{id} → Update follow-up
DELETE /admin/leads/{lead_id}/follow-ups/{id} → Delete follow-up
GET    /admin/leads/{lead_id}/next-follow-up  → Get next action
```

### Payment Endpoints (6)
```
GET    /admin/leads/{lead_id}/payments       → List payments
POST   /admin/leads/{lead_id}/payments       → Record payment
GET    /admin/leads/{lead_id}/payments/{id}  → View payment
PUT    /admin/leads/{lead_id}/payments/{id}  → Update payment
DELETE /admin/leads/{lead_id}/payments/{id}  → Delete payment
GET    /admin/leads/{lead_id}/payment-status → Payment summary
```

### Document Endpoints (5)
```
GET    /admin/leads/{lead_id}/documents      → List documents
POST   /admin/leads/{lead_id}/documents      → Upload document
GET    /admin/leads/{lead_id}/documents/{id} → Download document
DELETE /admin/leads/{lead_id}/documents/{id} → Delete document
GET    /admin/leads/{lead_id}/documents/type/{type} → Filter by type
```

### Analytics Endpoints (8)
```
GET    /admin/leads/analytics/dashboard      → Main dashboard
GET    /admin/leads/analytics/funnel         → Conversion funnel
GET    /admin/leads/analytics/revenue        → Revenue pipeline
GET    /admin/leads/analytics/lost-reasons   → Lost lead analysis
GET    /admin/leads/analytics/staff          → Staff performance
GET    /admin/leads/analytics/source         → Lead source analysis
GET    /admin/leads/analytics/cycle-time     → Average cycle time
GET    /admin/leads/analytics/export         → Export analytics
```

### Workflow Endpoints (3)
```
GET    /admin/lead-stages                    → List all stages
POST   /admin/leads/{id}/auto-transition     → Test auto-transition
GET    /admin/automation-rules               → List automation rules
```

**Total API Endpoints:** 43+ endpoints

---

## 🗄️ DATABASE SCHEMA SUMMARY

### New Tables (5)
| Table | Rows | Indexes | Purpose |
|-------|------|---------|---------|
| `lead_stages` | 9 | 2 | Define 9 lead journey stages |
| `lead_follow_ups` | ∞ | 3 | Repeatable follow-up tracking |
| `lead_payments` | ∞ | 3 | Multiple payments per lead |
| `lead_documents` | ∞ | 2 | File storage & tracking |
| `automation_rules` | ~20 | 1 | Auto-workflow configurations |

### Modified Tables (1)
| Table | New Columns | Indexes | Purpose |
|-------|-------------|---------|---------|
| `service_leads` | 30+ | 10+ | Enhanced lead management |

### Total Schema Size
- **Tables:** 6 (5 new + 1 modified)
- **Columns:** 80+ total
- **Indexes:** 20+ for performance
- **Relations:** 15+ foreign keys
- **Storage:** ~100MB per 100K leads

---

## 🔐 PERMISSION MATRIX

| Action | Admin | Sales Mgr | Sales Exec | Design | Accounts | Site Coord |
|--------|-------|-----------|-----------|--------|----------|-----------|
| View All Leads | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| View Assigned | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Create Lead | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Edit Lead | ✅ | ✅ | Own | ❌ | ❌ | ❌ |
| Delete Lead | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Change Stage | ✅ | ✅ | Limited | ❌ | ❌ | Partial |
| Record Payment | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| View Payments | ✅ | ✅ | Own | ❌ | ✅ | ❌ |
| Add Follow-up | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Upload Documents | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ |
| View Analytics | ✅ | ✅ | Own | ❌ | ✅ | ❌ |

---

## 📦 DEPENDENCIES & LIBRARIES

### Already Available
```php
Laravel 11+
MySQL 8+
Blade Templates
Laravel Queue
Laravel Events
jQuery
DataTables
Chart.js
Flatpickr
```

### To Install
```bash
composer require spatie/laravel-activitylog
composer require barryvdh/laravel-excel
composer require barryvdh/laravel-dompdf
composer require intervention/image (for photo processing)
```

### Optional (For Enhanced Features)
```bash
npm install sweetalert2 (Better alerts)
npm install vue-draggable (Kanban drag-drop)
npm install apexcharts (Advanced charts)
```

---

## 🧪 TESTING STRATEGY

### Unit Tests (Must Have)
```
✓ LeadService tests
✓ LeadStageService tests
✓ Validation request tests
✓ Model relationship tests
```

### Integration Tests (Must Have)
```
✓ Lead creation flow
✓ Stage transition logic
✓ Payment processing
✓ Auto-notification triggers
✓ Follow-up reminders
```

### Feature Tests (Must Have)
```
✓ User can create lead
✓ User can move lead to stage
✓ Payment auto-updates status
✓ Email sent on stage change
```

### Manual Testing Checklist
```
✓ Create lead with all fields
✓ Move through all 9 stages
✓ Add follow-ups at each stage
✓ Record payments (advance, partial, full)
✓ Upload documents (photos, designs, contracts)
✓ Verify email notifications
✓ Check analytics dashboard
✓ Test permission restrictions
✓ Export to Excel
✓ Test on mobile browser
```

---

## 📋 MIGRATION CHECKLIST

### Pre-Migration
- [ ] Backup current database
- [ ] Test all migrations locally
- [ ] Create database snapshots
- [ ] Plan downtime window

### During Migration
- [ ] Run migrations in sequence
- [ ] Seed lead_stages
- [ ] Update lead_statuses table
- [ ] Migrate existing leads to new schema
- [ ] Validate data integrity

### Post-Migration
- [ ] Verify all tables created
- [ ] Check row counts
- [ ] Test all endpoints
- [ ] Verify permissions
- [ ] Clear caches
- [ ] Monitor for errors

---

## 📊 PERFORMANCE TARGETS

| Metric | Target | Strategy |
|--------|--------|----------|
| List 1000 leads | < 500ms | Pagination + index on stage_id |
| Create lead | < 200ms | No heavy validation |
| Get analytics | < 1000ms | Cache for 5 min |
| Export to Excel | < 5s | Queue job |
| Email sending | < 100ms | Queue jobs |
| Dashboard load | < 1000ms | API caching |

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-Deployment
- [ ] All tests passing
- [ ] Code review completed
- [ ] Database migrations tested
- [ ] Performance tested at scale

### Deployment
- [ ] Run migrations on production
- [ ] Seed lead_stages
- [ ] Publish new routes
- [ ] Update navigation menu
- [ ] Clear caches

### Post-Deployment
- [ ] Verify all endpoints working
- [ ] Check email notifications
- [ ] Monitor error logs
- [ ] Get user feedback
- [ ] Document issues found

---

## 📝 DOCUMENTATION TO CREATE

1. ✅ `ENHANCED_LEADS_MODULE_PLAN.md` - High-level plan (THIS FILE)
2. 📄 `API_REFERENCE.md` - Complete API documentation
3. 📄 `DATABASE_SCHEMA.md` - Detailed schema documentation
4. 📄 `WORKFLOW_RULES.md` - Automation rules documentation
5. 📄 `USER_GUIDE.md` - How to use the module
6. 📄 `ADMIN_GUIDE.md` - Configuration & setup guide
7. 📄 `DEVELOPER_GUIDE.md` - For developers extending the system

---

## 🎯 SUCCESS CRITERIA

### Functional
- ✅ All 9 stages working
- ✅ All 43+ endpoints working
- ✅ Auto-workflow triggers working
- ✅ All notifications sending
- ✅ Analytics dashboard functional

### Non-Functional
- ✅ > 95% uptime
- ✅ < 500ms response time (95th percentile)
- ✅ < 100ms email queue processing
- ✅ Zero data loss
- ✅ < 1% API error rate

### User Adoption
- ✅ All team members trained
- ✅ Zero critical bugs in first 2 weeks
- ✅ All permissions working correctly
- ✅ Dashboard being used daily
- ✅ 90% data entry accuracy

---

## 💰 RESOURCE ESTIMATION

| Resource | Effort | Duration |
|----------|--------|----------|
| Database Designer | 1 day | Day 1 |
| Backend Developer | 12 days | Days 1-12 |
| Frontend Developer | 8 days | Days 5-12 |
| QA Engineer | 4 days | Days 10-13 |
| DevOps/Deployment | 1 day | Day 14 |
| **Total** | **26 dev-days** | **2 weeks** |

---

**Last Updated:** January 15, 2026  
**Next Review:** After Phase 1 completion  
**Status:** Ready for approval & development
