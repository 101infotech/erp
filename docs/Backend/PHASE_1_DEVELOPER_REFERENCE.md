# Phase 1 - Developer Quick Reference

## 🔍 Database Tables

### lead_stages (9 records)
```sql
SELECT * FROM lead_stages ORDER BY stage_number;
```
| Stage | Name | Timeout | Action? |
|-------|------|---------|---------|
| 1 | Lead Capture | 30d | Yes |
| 2 | Lead Qualification | 15d | Yes |
| 3 | Site Visit Scheduled | 7d | Yes |
| 4 | Site Visit Completed | 10d | Yes |
| 5 | Design & Proposal | 14d | Yes |
| 6 | Booking Confirmed | 5d | No |
| 7 | Project Active | ∞ | No |
| 8 | Project Completion | 7d | Yes |
| 9 | Closed | - | No |

### service_leads (Enhanced)
Core lead table with 30+ new tracking columns:
- Lead tracking: lead_source, lead_owner_id, lead_stage_id, priority
- Site visit: site_visit_scheduled_at, site_visit_completed_at, site_visit_assigned_to_id
- Design: design_proposed_at, design_version, design_approved_at
- Booking: booking_confirmed_at, project_code
- Payment: quoted_amount, advance_amount, paid_amount, payment_status
- Closure: closure_reason, closed_at, closure_notes
- Activity: last_activity_at, next_follow_up_date, follow_up_count

### lead_follow_ups
Repeatable follow-up tracking:
- Types: call, visit, whatsapp, email, sms
- Tracks: follow_up_date, outcome, notes, next_follow_up_date
- Owner: follow_up_owner_id (user who performed the follow-up)

### lead_payments
Payment transaction logging:
- Types: advance, partial, full
- Modes: cash, bank_transfer, check, online, other
- Tracks: payment_amount, payment_date, reference_number, received_by_id

### lead_documents
Document/file storage with soft deletes:
- Types: photo, design, contract, quotation, report, other
- Stores: file_name, file_path, file_size, mime_type
- Tracks: document_type, description, uploaded_by_id

---

## 🔗 Model Relationships

### ServiceLead
```php
$lead->leadStage();              // BelongsTo LeadStage
$lead->leadOwner();              // BelongsTo User
$lead->siteVisitAssignedTo();    // BelongsTo User
$lead->followUps();              // HasMany LeadFollowUp
$lead->payments();               // HasMany LeadPayment
$lead->documents();              // HasMany LeadDocument
$lead->statusInfo();             // HasOne LeadStatus (existing)
$lead->assignedTo();             // BelongsTo User (existing)
$lead->createdBy();              // BelongsTo User (existing)
```

### LeadStage
```php
$stage->leads();                 // HasMany ServiceLead
```

### LeadFollowUp
```php
$followUp->lead();               // BelongsTo ServiceLead
$followUp->followUpOwner();      // BelongsTo User
```

### LeadPayment
```php
$payment->lead();                // BelongsTo ServiceLead
$payment->receivedBy();          // BelongsTo User
```

### LeadDocument
```php
$document->lead();               // BelongsTo ServiceLead
$document->uploadedBy();         // BelongsTo User
```

---

## 📝 Query Scopes

### ServiceLead Scopes
```php
// By stage
ServiceLead::byStage(1)->get();           // Stage ID
ServiceLead::byStage('Lead Capture')->get(); // Stage name

// By owner and priority
ServiceLead::byOwner($userId)->get();
ServiceLead::byPriority('high')->get();
ServiceLead::byOwner($userId)->byPriority('high')->get();

// Status-based
ServiceLead::open()->get();                // NOT closed
ServiceLead::closed()->get();              // Closed
ServiceLead::needingFollowUp()->get();     // Missing follow-ups
ServiceLead::pendingPayment()->get();      // Incomplete payments

// Date range
ServiceLead::createdBetween('2024-01-01', '2024-12-31')->get();

// Existing scopes (still work)
ServiceLead::active()->get();
ServiceLead::byStatus('Intake')->get();
ServiceLead::search('keyword')->get();
ServiceLead::assignedTo($userId)->get();
```

### LeadFollowUp Scopes
```php
LeadFollowUp::forLead($leadId)->get();
LeadFollowUp::byType('call')->get();
LeadFollowUp::byOwner($userId)->get();
LeadFollowUp::pending()->get();            // Overdue or today
```

### LeadPayment Scopes
```php
LeadPayment::forLead($leadId)->get();
LeadPayment::byType('advance')->get();
LeadPayment::byMode('bank_transfer')->get();
LeadPayment::betweenDates('2024-01-01', '2024-12-31')->get();
```

### LeadDocument Scopes
```php
LeadDocument::forLead($leadId)->get();
LeadDocument::byType('photo')->get();
LeadDocument::uploadedBy($userId)->get();
```

---

## 💾 Common Operations

### Create a Lead
```php
$lead = ServiceLead::create([
    'client_name' => 'John Doe',
    'email' => 'john@example.com',
    'phone_number' => '9841234567',
    'service_requested' => 'Home Renovation',
    'location' => 'Kathmandu',
    'lead_stage_id' => 1,           // Lead Capture
    'priority' => 'high',
    'lead_source' => 'website',
    'lead_owner_id' => 5,           // Sales person
    'created_by' => auth()->id(),
]);
```

### Transition Lead Stage
```php
$lead->update(['lead_stage_id' => 2]); // To Qualification

// Also update last activity
$lead->update([
    'lead_stage_id' => 3,              // To Site Visit Scheduled
    'last_activity_at' => now(),
    'site_visit_scheduled_at' => '2024-01-20 10:00:00',
]);
```

### Add Follow-up
```php
$lead->followUps()->create([
    'follow_up_date' => now()->addDays(3),
    'follow_up_type' => 'call',
    'follow_up_owner_id' => $userId,
    'follow_up_notes' => 'Initial contact call',
    'next_follow_up_date' => now()->addDays(7),
]);
```

### Record Payment
```php
$lead->payments()->create([
    'payment_amount' => 10000.00,
    'payment_date' => now(),
    'payment_mode' => 'bank_transfer',
    'payment_type' => 'advance',
    'reference_number' => 'TXN123456',
    'received_by_id' => auth()->id(),
    'notes' => 'Advance payment received',
]);

// Update lead payment tracking
$lead->update([
    'advance_amount' => 10000.00,
    'paid_amount' => 10000.00,
    'payment_status' => 'partial',  // or 'full' if complete
    'payment_received_at' => now(),
]);
```

### Upload Document
```php
$lead->documents()->create([
    'document_type' => 'photo',
    'file_name' => 'site-photo-1.jpg',
    'file_path' => 'storage/leads/photos/site-photo-1.jpg',
    'file_size' => '2048000',
    'mime_type' => 'image/jpeg',
    'description' => 'Living room photo',
    'uploaded_by_id' => auth()->id(),
]);
```

### Close a Lead
```php
$lead->update([
    'lead_stage_id' => 9,              // Closed
    'closed_at' => now(),
    'closure_reason' => 'converted',   // or 'won', 'lost', 'no_interest', etc.
    'closure_notes' => 'Project started',
]);
```

---

## 🧮 Aggregate Queries

### Get Lead Statistics
```php
// Payment summary
$lead->payments()->sum('payment_amount');           // Total paid
$lead->payments()->where('payment_type', 'advance')->sum('payment_amount');

// Follow-up count
$lead->followUps()->count();
$lead->followUps()->where('follow_up_type', 'call')->count();

// Document count
$lead->documents()->count();
$lead->documents()->where('document_type', 'photo')->count();
```

### Get User's Leads
```php
User::findOrFail($userId)->leads()  // via leadOwner relationship
    ->where('lead_stage_id', 2)
    ->orderBy('priority', 'desc')
    ->get();
```

### Get Stage Statistics
```php
LeadStage::with('leads')->each(function($stage) {
    echo $stage->stage_name . ': ' . $stage->leads()->count() . ' leads';
});
```

---

## 🔄 Workflow Examples

### Follow-up Workflow
```php
// Check leads needing follow-up
$overdueLeads = ServiceLead::needingFollowUp()->get();

foreach ($overdueLeads as $lead) {
    // Create new follow-up
    $lead->followUps()->create([
        'follow_up_date' => now(),
        'follow_up_type' => 'call',
        'follow_up_owner_id' => $lead->lead_owner_id,
        'follow_up_notes' => 'Automated follow-up call',
        'next_follow_up_date' => now()->addDays(3),
    ]);
    
    // Update lead
    $lead->update([
        'follow_up_count' => $lead->follow_up_count + 1,
        'next_follow_up_date' => now()->addDays(3),
        'last_activity_at' => now(),
    ]);
}
```

### Sales Pipeline View
```php
$pipeline = LeadStage::active()
    ->with(['leads' => function($q) {
        $q->where('closed_at', null)
          ->with('leadOwner')
          ->orderBy('priority', 'desc');
    }])
    ->get();

foreach ($pipeline as $stage) {
    echo $stage->stage_name . ' (' . $stage->leads->count() . ' leads)';
    foreach ($stage->leads as $lead) {
        echo "  - {$lead->client_name} ({$lead->leadOwner->name})";
    }
}
```

### Payment Tracking
```php
$lead = ServiceLead::findOrFail($leadId);
$totalPayments = $lead->payments()->sum('payment_amount');
$quotedAmount = $lead->quoted_amount;
$pendingAmount = $quotedAmount - $totalPayments;

if ($pendingAmount <= 0) {
    // All paid - transition to next stage
    $lead->update(['lead_stage_id' => 7, 'payment_status' => 'full']);
}
```

---

## 🚨 Important Notes

1. **Always update last_activity_at** when making changes to a lead
2. **Cascade deletes** are enabled for lead_id - deleting a lead deletes all related follow-ups, payments, documents
3. **Documents use soft deletes** - can be recovered if needed
4. **Payment status** should match actual payments recorded
5. **Follow-up count** helps track engagement level
6. **Priority field** enables sorting and filtering for workflows
7. **Closure reason** helps with analytics and reporting

---

## 📊 Testing Queries

```bash
# Open Tinker
php artisan tinker

# Test relationships
$lead = App\Models\ServiceLead::first();
$lead->followUps();
$lead->payments();
$lead->documents();

# Test scopes
App\Models\ServiceLead::byStage(2)->count();
App\Models\ServiceLead::needingFollowUp()->count();
App\Models\ServiceLead::pendingPayment()->count();

# Test stages
$stages = App\Models\LeadStage::all();
foreach ($stages as $s) { echo "{$s->stage_number}. {$s->stage_name}\n"; }
```

---

**Created:** January 15, 2026  
**Status:** Ready for Phase 2  
**Next:** Backend API Implementation
