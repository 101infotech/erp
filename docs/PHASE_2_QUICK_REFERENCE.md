# Phase 2: Backend API - Quick Reference Guide

## 🎯 Quick Stats

- **Controllers**: 6 (ServiceLead, FollowUp, Payment, Document, Stage, Analytics)
- **Endpoints**: 48+
- **Request Classes**: 5 (Validation)
- **Status**: ✅ COMPLETE
- **Authentication**: Bearer Token (Sanctum)
- **Pagination**: 15 items default

## 📍 Base URL
```
/api/v2/leads/enhanced
```

## 🔑 Authentication Header (All Requests)
```
Authorization: Bearer {token}
```

## 🚀 Quick Endpoints Reference

### Lead CRUD (11 endpoints)
```bash
# List leads (with filters)
GET /?stage_id=1&priority=urgent&owner_id=5&per_page=20

# Create lead
POST / 
{
  "company_name": "Acme Corp",
  "contact_person": "John Doe",
  "email": "john@acme.com",
  "phone": "9876543210",
  "service_type": "Web Development",
  "service_description": "...",
  "priority": "high",
  "quoted_amount": 50000
}

# Get single lead (with all relationships)
GET /{lead}

# Update lead
PUT /{lead}
{
  "priority": "urgent",
  "notes": "Updated notes"
}

# Delete lead (soft delete)
DELETE /{lead}

# Move lead to another stage
POST /{lead}/transition
{
  "lead_stage_id": 3
}

# Get leads needing follow-up (overdue/today)
GET /special/needing-follow-up

# Get leads with pending payments
GET /special/pending-payment

# Get dashboard statistics
GET /special/statistics

# Bulk update multiple leads
POST /bulk/update
{
  "leads": [1, 2, 3],
  "data": {
    "priority": "high",
    "lead_owner_id": 5
  }
}

# Bulk delete multiple leads
POST /bulk/delete
{
  "leads": [1, 2, 3]
}
```

### Follow-ups (7 endpoints)
```bash
# List follow-ups for lead
GET /{lead}/follow-ups

# Create follow-up
POST /{lead}/follow-ups
{
  "follow_up_type": "call",
  "follow_up_date": "2026-01-20",
  "notes": "Discussed requirements with client"
}

# Get single follow-up
GET /{lead}/follow-ups/{followUp}

# Update follow-up
PUT /{lead}/follow-ups/{followUp}
{
  "outcome": "Client agreed to move forward"
}

# Delete follow-up
DELETE /{lead}/follow-ups/{followUp}

# Get all pending follow-ups (across all leads)
GET /follow-ups/pending/all

# Filter follow-ups by type
GET /follow-ups/by-type/call
```

### Payments (6 endpoints)
```bash
# List payments for lead
GET /{lead}/payments

# Record payment
POST /{lead}/payments
{
  "amount": 15000,
  "payment_method": "bank_transfer",
  "payment_date": "2026-01-15",
  "reference_number": "TXN123456"
}

# Get payment details
GET /{lead}/payments/{payment}

# Update payment
PUT /{lead}/payments/{payment}
{
  "amount": 16000
}

# Delete payment
DELETE /{lead}/payments/{payment}

# Get payment summary
GET /{lead}/payments/summary/view
```

### Documents (8 endpoints)
```bash
# List documents for lead
GET /{lead}/documents

# Upload document (multipart/form-data)
POST /{lead}/documents
{
  "document_type": "contract",
  "document_file": "file.pdf",
  "description": "Contract signed by client"
}

# Get document metadata
GET /{lead}/documents/{document}

# Update document info
PUT /{lead}/documents/{document}
{
  "document_type": "quotation"
}

# Delete document (soft delete)
DELETE /{lead}/documents/{document}

# Restore deleted document
POST /{lead}/documents/{document}/restore

# Filter documents by type
GET /documents/by-type/contract

# Download document
GET /{lead}/documents/{document}/download
```

### Stages (5 endpoints)
```bash
# Get all 9 stages
GET /stages/all

# Get stage with leads in it
GET /stages/{stage}

# Visual pipeline view
GET /stages/pipeline/view

# Get next/previous stage info
GET /stages/{stage}/transition-info

# Get stage metrics
GET /stages/{stage}/metrics
```

### Analytics (8 endpoints)
```bash
# Dashboard overview
GET /analytics/dashboard

# Pipeline analytics
GET /analytics/pipeline

# Sales team performance
GET /analytics/sales-team

# Payment analytics
GET /analytics/payments

# Follow-up statistics
GET /analytics/follow-ups

# Priority breakdown
GET /analytics/by-priority

# Source breakdown
GET /analytics/by-source

# Closure reasons
GET /analytics/closures
```

## 📋 Query Parameters

### Pagination
```
?per_page=25          # Items per page (default: 15, max: 100)
?page=2               # Page number
```

### Filtering
```
?stage_id=1           # Filter by stage
?owner_id=5           # Filter by lead owner (user ID)
?priority=urgent      # urgent, high, medium, low
?payment_status=pending  # pending, partial, full
?status=open          # open or closed
?search=Acme          # Search by company name, contact, email, phone
```

### Sorting
```
?sort_by=created_at   # Field to sort by
?sort_order=asc       # asc or desc
```

### Combined Example
```
GET /?stage_id=2&priority=high&owner_id=5&search=Acme&per_page=20&page=1
```

## 🎯 Follow-up Types
- `call` - Phone call
- `visit` - In-person visit
- `whatsapp` - WhatsApp message
- `email` - Email
- `sms` - SMS/Text message
- `meeting` - Scheduled meeting
- `other` - Other follow-up

## 💳 Payment Methods
- `cash`
- `cheque`
- `bank_transfer`
- `upi`
- `credit_card`
- `debit_card`
- `other`

## 📁 Document Types
- `photo` - Photographs
- `design` - Design files
- `contract` - Contracts
- `quotation` - Quotations
- `report` - Reports
- `invoice` - Invoices
- `proposal` - Proposals
- `other` - Other documents

## 🔴 Priority Levels
- `urgent` - Highest priority
- `high` - High priority
- `medium` - Medium priority
- `low` - Low priority

## 📊 Pipeline Stages (9 Total)
1. Lead Captured
2. Initial Contact
3. Requirement Analysis
4. Quotation Sent
5. Negotiation
6. Order Confirmation
7. Service Execution
8. Delivery & Completion
9. Follow-up & Feedback

## 📤 File Upload Rules
- **Max Size**: 5MB per file
- **Allowed Types**: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), Images (JPG/JPEG/PNG/GIF)
- **Auto-Storage**: `storage/app/public/leads/{lead_id}/{document_type}/`

## ✅ Validation Rules Summary

### Lead Creation
- Company name: required, max 255
- Contact person: required, max 255
- Email: required, valid email
- Phone: required, max 20
- Service type: required, max 100
- Service description: required
- Priority: optional (urgent/high/medium/low)
- Quoted amount: optional, numeric ≥ 0
- Lead owner: optional, must exist in users table

### Follow-up
- Type: required (call/visit/whatsapp/email/sms/meeting/other)
- Date: required, future date
- Notes: required, min 10 characters

### Payment
- Amount: required, numeric > 0
- Method: required (valid payment method)
- Date: required, today or past
- Reference: optional, unique

### Document
- Type: required (valid document type)
- File: required, 5MB max, valid MIME type
- Description: optional, max 500 chars

## 🔒 Auto-Updates (System Handles These)

When you create/update a lead:
- ✅ `last_activity_at` = current time
- ✅ `created_by` = logged-in user (on create)
- ✅ `lead_owner_id` = logged-in user (if not specified on create)

When you add a follow-up:
- ✅ `follow_up_count` += 1 (incremented)
- ✅ `last_activity_at` = current time

When you record payment:
- ✅ `paid_amount` = sum of all payments
- ✅ `payment_status` = auto-calculated (pending/partial/full)
- ✅ `payment_received_at` = date of last payment

## 📊 Response Examples

### Success (List)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "company_name": "Acme Corp",
            "contact_person": "John Doe",
            "email": "john@acme.com",
            "phone": "9876543210",
            "lead_stage_id": 2,
            "priority": "high",
            "quoted_amount": 50000,
            "paid_amount": 15000,
            "payment_status": "partial",
            "follow_up_count": 3,
            "leadStage": {...},
            "leadOwner": {...}
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 45,
        "last_page": 3,
        "from": 1,
        "to": 15
    }
}
```

### Success (Single)
```json
{
    "success": true,
    "message": "Lead retrieved successfully",
    "data": {
        "id": 1,
        "company_name": "Acme Corp",
        ...all fields...,
        "leadStage": {...},
        "leadOwner": {...},
        "followUps": [...],
        "payments": [...],
        "documents": [...]
    }
}
```

### Error (Validation)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["Email field is required"],
        "phone": ["Phone must be 10-20 characters"]
    }
}
```

### Error (Not Found)
```json
{
    "success": false,
    "message": "Lead not found",
    "error": "No query results for model [App\\Models\\ServiceLead] 1"
}
```

## 🧪 Testing the API

### Using cURL
```bash
# Get all leads
curl -H "Authorization: Bearer {token}" \
     "http://localhost:8000/api/v2/leads/enhanced"

# Create lead
curl -X POST -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     -d '{"company_name":"Test","contact_person":"John","email":"john@test.com","phone":"9876543210","service_type":"Web","service_description":"Website"}' \
     "http://localhost:8000/api/v2/leads/enhanced"
```

### Using Postman
1. Import collection from project
2. Set Bearer token in Authorization tab
3. Each request is pre-configured with proper method, URL, headers

## 🔗 Full Route List
```bash
php artisan route:list --path=leads
```

## 📖 Complete Documentation
See `PHASE_2_BACKEND_API.md` for detailed documentation of all endpoints, request/response formats, and examples.

## ⚠️ Common Issues & Solutions

### 401 Unauthorized
- ✅ Check Bearer token is correct
- ✅ Verify token hasn't expired
- ✅ Include Authorization header in all requests

### 404 Not Found
- ✅ Check resource ID exists
- ✅ Verify correct endpoint path
- ✅ Ensure lead_id matches in nested resources

### 422 Validation Error
- ✅ Check all required fields are provided
- ✅ Verify field values match expected format
- ✅ See error response for specific field issues

### 500 Server Error
- ✅ Check Laravel logs: `tail storage/logs/laravel.log`
- ✅ Verify database connection
- ✅ Run migrations: `php artisan migrate`

## 🚀 Next Steps (Phase 3)

1. Frontend API Integration (React components)
2. UI for Lead Management Dashboard
3. Follow-up Scheduling Interface
4. Payment Tracking UI
5. Document Upload/View Interface
6. Analytics Dashboard Charts
7. Mobile-responsive Design
8. Testing & Deployment

---

**Last Updated**: January 2026  
**Status**: ✅ Production Ready  
**Version**: 2.0
