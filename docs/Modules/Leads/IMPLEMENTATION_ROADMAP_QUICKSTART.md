# 🗺️ Enhanced Leads Module - Implementation Roadmap & Quick Start

**Quick Access Guide for Developers**  
**Version:** 1.0 | **Date:** January 15, 2026

---

## 📍 WHERE WE ARE NOW

### Current Implementation Status
```
✅ EXISTING              PLANNED                NICE-TO-HAVE
├─ Basic Lead Model     ├─ LeadStage Model     ├─ AI Lead Scoring
├─ Service Types        ├─ LeadFollowUp Model  ├─ WhatsApp Integration
├─ Simple Status        ├─ LeadPayment Model   ├─ Automated Proposal Gen
├─ Basic CRUD           ├─ 9-Stage Pipeline    ├─ Video Calls
└─ Simple UI            ├─ Auto-Workflows      └─ Mobile App
                        └─ Rich Analytics
```

### Existing Database Tables
```sql
-- Already exist:
service_leads (Table)
lead_statuses (Table)
users (Table)
```

### Existing Models
```
app/Models/ServiceLead.php
app/Models/LeadStatus.php
```

---

## 🎯 IMPLEMENTATION PHASES - DETAILED BREAKDOWN

### PHASE 1: Database Layer (Days 1-2)

#### Step 1.1: Create Lead Stages Migration
**File:** `database/migrations/2026_01_15_create_lead_stages_table.php`

```php
Schema::create('lead_stages', function (Blueprint $table) {
    $table->id();
    $table->integer('stage_number')->unique();
    $table->string('stage_name', 100);
    $table->text('description')->nullable();
    $table->integer('auto_timeout_days')->nullable();
    $table->boolean('requires_action')->default(true);
    $table->string('notification_template')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('stage_number');
    $table->index('is_active');
});
```

#### Step 1.2: Create Follow-ups Table Migration
**File:** `database/migrations/2026_01_15_create_lead_follow_ups_table.php`

```php
Schema::create('lead_follow_ups', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('lead_id');
    $table->date('follow_up_date');
    $table->enum('follow_up_type', ['call', 'visit', 'whatsapp', 'email', 'sms']);
    $table->string('follow_up_outcome')->nullable();
    $table->text('follow_up_notes')->nullable();
    $table->date('next_follow_up_date')->nullable();
    $table->unsignedBigInteger('follow_up_owner_id');
    $table->timestamps();
    
    $table->foreign('lead_id')->references('id')->on('service_leads')->onDelete('cascade');
    $table->foreign('follow_up_owner_id')->references('id')->on('users');
    $table->index(['lead_id', 'follow_up_date']);
});
```

#### Step 1.3: Create Payments Table Migration
**File:** `database/migrations/2026_01_15_create_lead_payments_table.php`

```php
Schema::create('lead_payments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('lead_id');
    $table->decimal('payment_amount', 15, 2);
    $table->date('payment_date');
    $table->enum('payment_mode', ['cash', 'bank_transfer', 'check', 'online', 'other']);
    $table->string('reference_number', 100)->nullable();
    $table->unsignedBigInteger('received_by_id');
    $table->enum('payment_type', ['advance', 'partial', 'full']);
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->foreign('lead_id')->references('id')->on('service_leads')->onDelete('cascade');
    $table->foreign('received_by_id')->references('id')->on('users');
    $table->index(['lead_id', 'payment_date']);
});
```

#### Step 1.4: Create Documents Table Migration
**File:** `database/migrations/2026_01_15_create_lead_documents_table.php`

```php
Schema::create('lead_documents', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('lead_id');
    $table->enum('document_type', ['site_photo', 'design_2d', 'design_3d', 'estimate', 'proposal', 'report', 'contract', 'other']);
    $table->string('file_path');
    $table->string('file_name');
    $table->integer('file_size')->nullable();
    $table->string('mime_type', 50)->nullable();
    $table->unsignedBigInteger('uploaded_by_id');
    $table->text('description')->nullable();
    $table->timestamps();
    
    $table->foreign('lead_id')->references('id')->on('service_leads')->onDelete('cascade');
    $table->foreign('uploaded_by_id')->references('id')->on('users');
    $table->index(['lead_id', 'document_type']);
});
```

#### Step 1.5: Alter Service Leads Table
**File:** `database/migrations/2026_01_15_alter_service_leads_add_enhanced_columns.php`

```php
Schema::table('service_leads', function (Blueprint $table) {
    // Lead Hierarchy
    $table->enum('lead_source', ['phone', 'whatsapp', 'walk_in', 'referral', 'online', 'website', 'other'])->default('phone')->after('client_name');
    $table->unsignedBigInteger('lead_owner_id')->nullable()->after('status');
    $table->unsignedBigInteger('lead_stage_id')->nullable()->after('lead_owner_id');
    $table->enum('lead_priority', ['high', 'medium', 'low'])->default('medium')->after('lead_stage_id');
    
    // Enhanced Client Details
    $table->string('secondary_phone', 20)->nullable()->after('phone_number');
    $table->string('address_line_2', 255)->nullable()->after('location');
    $table->string('location_map_link', 255)->nullable()->after('address_line_2');
    $table->enum('property_type', ['house', 'commercial', 'apartment', 'other'])->nullable()->after('location_map_link');
    
    // Site Visit Enhanced
    $table->boolean('site_visit_required')->default(true)->after('property_type');
    $table->date('site_visit_scheduled_date')->nullable()->after('site_visit_required');
    $table->timestamp('site_visit_completed_at')->nullable()->after('site_visit_scheduled_date');
    $table->enum('site_visit_type', ['office', 'on_site'])->default('on_site')->after('site_visit_completed_at');
    $table->string('site_visit_outcome', 255)->nullable()->after('site_visit_type');
    $table->text('site_visit_remarks')->nullable()->after('site_visit_outcome');
    $table->date('next_action_date')->nullable()->after('site_visit_remarks');
    
    // Design & Proposal Enhanced
    $table->enum('design_type', ['2d', '3d', 'both'])->default('2d')->after('next_action_date');
    $table->string('design_status', 100)->nullable()->after('design_type');
    $table->integer('design_revision_count')->default(0)->after('design_status');
    $table->decimal('estimated_budget', 15, 2)->nullable()->after('design_revision_count');
    $table->decimal('final_quoted_amount', 15, 2)->nullable()->after('estimated_budget');
    $table->date('proposal_sent_date')->nullable()->after('final_quoted_amount');
    $table->integer('proposal_version')->default(1)->after('proposal_sent_date');
    
    // Payment Enhanced
    $table->decimal('total_payment_required', 15, 2)->nullable()->after('proposal_version');
    $table->decimal('advance_required', 15, 2)->nullable()->after('total_payment_required');
    $table->decimal('payment_received', 15, 2)->default(0)->after('advance_required');
    $table->enum('payment_mode', ['cash', 'bank_transfer', 'check', 'online', 'other'])->nullable()->after('payment_received');
    $table->unsignedBigInteger('payment_received_by_id')->nullable()->after('payment_mode');
    $table->date('payment_received_date')->nullable()->after('payment_received_by_id');
    
    // Follow-ups
    $table->date('last_follow_up_date')->nullable()->after('payment_received_date');
    $table->string('last_follow_up_type', 50)->nullable()->after('last_follow_up_date');
    $table->date('next_follow_up_date')->nullable()->after('last_follow_up_type');
    $table->unsignedBigInteger('follow_up_owner_id')->nullable()->after('next_follow_up_date');
    
    // Reports
    $table->boolean('report_required')->default(false)->after('follow_up_owner_id');
    $table->date('report_sent_date')->nullable()->after('report_required');
    $table->unsignedBigInteger('report_sent_by_id')->nullable()->after('report_sent_date');
    $table->text('client_feedback')->nullable()->after('report_sent_by_id');
    
    // Closure
    $table->string('loss_reason', 255)->nullable()->after('client_feedback');
    $table->date('closure_date')->nullable()->after('loss_reason');
    
    // Metadata
    $table->json('metadata')->nullable()->after('closure_date');
    
    // Add foreign keys
    $table->foreign('lead_owner_id')->references('id')->on('users')->onDelete('set null');
    $table->foreign('lead_stage_id')->references('id')->on('lead_stages')->onDelete('set null');
    $table->foreign('payment_received_by_id')->references('id')->on('users')->onDelete('set null');
    $table->foreign('report_sent_by_id')->references('id')->on('users')->onDelete('set null');
    $table->foreign('follow_up_owner_id')->references('id')->on('users')->onDelete('set null');
    
    // Add indexes
    $table->index('lead_stage_id');
    $table->index('lead_owner_id');
    $table->index('lead_priority');
    $table->index('site_visit_scheduled_date');
    $table->index('proposal_sent_date');
    $table->index('next_action_date');
});
```

#### Step 1.6: Run Migrations & Seeders
```bash
# Create lead stages (9 stages)
php artisan make:seeder LeadStageSeeder

# Run all migrations
php artisan migrate

# Seed lead stages
php artisan db:seed --class=LeadStageSeeder
```

---

### PHASE 2: Models (Days 2-3)

#### Step 2.1: Create LeadStage Model
**File:** `app/Models/LeadStage.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    protected $fillable = [
        'stage_number',
        'stage_name',
        'description',
        'auto_timeout_days',
        'requires_action',
        'notification_template',
        'is_active',
    ];

    protected $casts = [
        'requires_action' => 'boolean',
        'is_active' => 'boolean',
        'stage_number' => 'integer',
    ];

    // Relationships
    public function leads()
    {
        return $this->hasMany(ServiceLead::class, 'lead_stage_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('stage_number');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('stage_number');
    }

    // Helper methods
    public function getProgressPercentage()
    {
        return round(($this->stage_number / 9) * 100);
    }

    public static function getStageByNumber($number)
    {
        return static::where('stage_number', $number)->first();
    }

    public function getNextStage()
    {
        return static::where('stage_number', '>', $this->stage_number)
            ->orderBy('stage_number')
            ->first();
    }
}
```

#### Step 2.2: Create LeadFollowUp Model
**File:** `app/Models/LeadFollowUp.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowUp extends Model
{
    protected $table = 'lead_follow_ups';

    protected $fillable = [
        'lead_id',
        'follow_up_date',
        'follow_up_type',
        'follow_up_outcome',
        'follow_up_notes',
        'next_follow_up_date',
        'follow_up_owner_id',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'next_follow_up_date' => 'date',
    ];

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(ServiceLead::class);
    }

    public function followUpOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_up_owner_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('follow_up_date', '<=', now())
            ->where('follow_up_outcome', null);
    }

    public function scopeScheduled($query)
    {
        return $query->where('follow_up_date', '>', now());
    }

    public function scopeByOwner($query, $userId)
    {
        return $query->where('follow_up_owner_id', $userId);
    }
}
```

#### Step 2.3: Create LeadPayment Model
**File:** `app/Models/LeadPayment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadPayment extends Model
{
    protected $table = 'lead_payments';

    protected $fillable = [
        'lead_id',
        'payment_amount',
        'payment_date',
        'payment_mode',
        'reference_number',
        'received_by_id',
        'payment_type',
        'notes',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(ServiceLead::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    // Accessors
    public function getPaymentStatusAttribute()
    {
        return match($this->payment_type) {
            'advance' => 'Advance Payment',
            'partial' => 'Partial Payment',
            'full' => 'Full Payment',
            default => 'Unknown'
        };
    }

    // Scopes
    public function scopeByLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    public function scopeByPaymentType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
```

#### Step 2.4: Create LeadDocument Model
**File:** `app/Models/LeadDocument.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadDocument extends Model
{
    protected $table = 'lead_documents';

    protected $fillable = [
        'lead_id',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by_id',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(ServiceLead::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    // Helper methods
    public function getFileSizeFormatted()
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    public function getDocumentTypeLabel()
    {
        return ucwords(str_replace('_', ' ', $this->document_type));
    }
}
```

#### Step 2.5: Update ServiceLead Model
**File:** `app/Models/ServiceLead.php`

```php
// Add these relationships and methods:

public function leadStage()
{
    return $this->belongsTo(LeadStage::class, 'lead_stage_id');
}

public function leadOwner()
{
    return $this->belongsTo(User::class, 'lead_owner_id');
}

public function followUps()
{
    return $this->hasMany(LeadFollowUp::class, 'lead_id');
}

public function payments()
{
    return $this->hasMany(LeadPayment::class, 'lead_id');
}

public function documents()
{
    return $this->hasMany(LeadDocument::class, 'lead_id');
}

public function paymentReceivedBy()
{
    return $this->belongsTo(User::class, 'payment_received_by_id');
}

public function reportSentBy()
{
    return $this->belongsTo(User::class, 'report_sent_by_id');
}

public function followUpOwner()
{
    return $this->belongsTo(User::class, 'follow_up_owner_id');
}

// Add scopes
public function scopeByStage($query, $stageId)
{
    return $query->where('lead_stage_id', $stageId);
}

public function scopeByOwner($query, $userId)
{
    return $query->where('lead_owner_id', $userId);
}

public function scopeByPriority($query, $priority)
{
    return $query->where('lead_priority', $priority);
}

public function scopeNeedingFollowUp($query)
{
    return $query->where('next_follow_up_date', '<=', now())
        ->whereNull('closure_date');
}

public function scopePendingPayment($query)
{
    return $query->where('payment_received', '<', DB::raw('total_payment_required'))
        ->whereNull('closure_date');
}

// Add helper methods
public function getRemainingPaymentAmount()
{
    return max(0, ($this->total_payment_required ?? 0) - ($this->payment_received ?? 0));
}

public function getPaymentPercentage()
{
    if (!$this->total_payment_required) return 0;
    return round(($this->payment_received / $this->total_payment_required) * 100);
}

public function moveToStage($stageNumber)
{
    $stage = LeadStage::where('stage_number', $stageNumber)->first();
    if ($stage) {
        $this->update(['lead_stage_id' => $stage->id]);
        event(new LeadStageChanged($this));
    }
}

public function getStageProgress()
{
    return $this->leadStage?->getProgressPercentage() ?? 0;
}
```

---

### PHASE 3: Seeders (Day 3)

#### Step 3.1: Create LeadStageSeeder
**File:** `database/seeders/LeadStageSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadStage;

class LeadStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'stage_number' => 1,
                'stage_name' => 'Lead Capture',
                'description' => 'Someone shows interest via phone, WhatsApp, visit, or referral',
                'auto_timeout_days' => 1,
                'requires_action' => true,
                'notification_template' => 'lead_created',
            ],
            [
                'stage_number' => 2,
                'stage_name' => 'Lead Qualification',
                'description' => 'Check if the lead is serious & feasible',
                'auto_timeout_days' => 3,
                'requires_action' => true,
                'notification_template' => 'lead_qualification_pending',
            ],
            [
                'stage_number' => 3,
                'stage_name' => 'Site Visit Planning',
                'description' => 'Arrange site inspection',
                'auto_timeout_days' => 7,
                'requires_action' => true,
                'notification_template' => 'site_visit_scheduled',
            ],
            [
                'stage_number' => 4,
                'stage_name' => 'Site Visit Completed',
                'description' => 'Technical understanding & measurements',
                'auto_timeout_days' => 1,
                'requires_action' => true,
                'notification_template' => 'site_visit_completed',
            ],
            [
                'stage_number' => 5,
                'stage_name' => 'Proposal / Design Phase',
                'description' => 'Deliver design estimate',
                'auto_timeout_days' => 7,
                'requires_action' => true,
                'notification_template' => 'design_in_progress',
            ],
            [
                'stage_number' => 6,
                'stage_name' => 'Negotiation & Approval',
                'description' => 'Final decision making',
                'auto_timeout_days' => 14,
                'requires_action' => true,
                'notification_template' => 'proposal_awaiting_response',
            ],
            [
                'stage_number' => 7,
                'stage_name' => 'Booking / Advance Payment',
                'description' => 'Convert lead to client',
                'auto_timeout_days' => 3,
                'requires_action' => true,
                'notification_template' => 'payment_pending',
            ],
            [
                'stage_number' => 8,
                'stage_name' => 'Converted to Project',
                'description' => 'Move to execution',
                'auto_timeout_days' => 1,
                'requires_action' => false,
                'notification_template' => 'project_created',
            ],
            [
                'stage_number' => 9,
                'stage_name' => 'Closed (Lost / Cancelled)',
                'description' => 'Clean closure',
                'auto_timeout_days' => null,
                'requires_action' => false,
                'notification_template' => 'lead_closed',
            ],
        ];

        foreach ($stages as $stage) {
            LeadStage::firstOrCreate(
                ['stage_number' => $stage['stage_number']],
                $stage
            );
        }
    }
}
```

---

## 🚦 EXECUTION CHECKLIST

### Week 1: Database & Models

**Day 1:**
- [ ] Create all 5 migrations
- [ ] Create LeadStage, LeadFollowUp, LeadPayment, LeadDocument models
- [ ] Run migrations
- [ ] Seed lead_stages
- [ ] Update ServiceLead model
- [ ] Database validation

**Day 2:**
- [ ] Create all indexes
- [ ] Foreign key validation
- [ ] Data integrity checks
- [ ] Performance testing with 1000 test leads

---

### Week 2: Controllers & API

**Day 1-2:**
- [ ] Create LeadController (CRUD + stage management)
- [ ] Create LeadFollowUpController
- [ ] Create LeadPaymentController
- [ ] Create LeadDocumentController

**Day 3-4:**
- [ ] Create validation request classes
- [ ] Create service classes
- [ ] Create event & listeners
- [ ] Write API tests

---

### Week 3: Automation & Frontend

**Day 1:**
- [ ] Create automation rules engine
- [ ] Create queue jobs
- [ ] Setup scheduled tasks
- [ ] Email templates

**Day 2-3:**
- [ ] Update blade templates
- [ ] Create new views
- [ ] Add JavaScript functionality
- [ ] Mobile responsiveness

---

### Week 4: Testing & Deployment

**Day 1:**
- [ ] Unit tests
- [ ] Integration tests
- [ ] Feature tests

**Day 2:**
- [ ] Manual testing
- [ ] User acceptance testing
- [ ] Performance testing

**Day 3-4:**
- [ ] Deployment prep
- [ ] Production migration
- [ ] Post-deployment validation
- [ ] Team training

---

## 📊 QUICK REFERENCE: KEY FILES TO CREATE

```
migrations/
├── 2026_01_15_create_lead_stages_table.php
├── 2026_01_15_create_lead_follow_ups_table.php
├── 2026_01_15_create_lead_payments_table.php
├── 2026_01_15_create_lead_documents_table.php
└── 2026_01_15_alter_service_leads_add_enhanced_columns.php

models/
├── LeadStage.php (NEW)
├── LeadFollowUp.php (NEW)
├── LeadPayment.php (NEW)
├── LeadDocument.php (NEW)
└── ServiceLead.php (UPDATE)

controllers/
├── Admin/LeadController.php (ENHANCED)
├── Admin/LeadFollowUpController.php (NEW)
├── Admin/LeadPaymentController.php (NEW)
├── Admin/LeadDocumentController.php (NEW)
└── Admin/LeadAnalyticsController.php (UPDATE)

services/
├── LeadService.php (NEW)
├── LeadStageService.php (NEW)
└── LeadAnalyticsService.php (NEW)

events/
├── LeadCreated.php (NEW)
├── LeadStageChanged.php (NEW)
└── PaymentReceived.php (NEW)

jobs/
├── SendLeadNotification.php (NEW)
├── CheckFollowUpReminders.php (NEW)
└── CheckPaymentReminders.php (NEW)

seeders/
└── LeadStageSeeder.php (NEW)

views/admin/leads/
├── index.blade.php (UPDATE)
├── create.blade.php (UPDATE)
├── edit.blade.php (UPDATE)
├── show.blade.php (UPDATE)
├── kanban.blade.php (NEW)
├── analytics.blade.php (UPDATE)
├── components/
│   ├── follow-up-form.blade.php (NEW)
│   ├── payment-form.blade.php (NEW)
│   ├── document-upload.blade.php (NEW)
│   └── stage-timeline.blade.php (NEW)
└── emails/
    ├── lead-created.blade.php (UPDATE)
    ├── stage-changed.blade.php (NEW)
    └── payment-received.blade.php (NEW)

docs/
├── ENHANCED_LEADS_MODULE_PLAN.md ✅
├── ENHANCED_LEADS_TECHNICAL_SPECS.md ✅
├── IMPLEMENTATION_ROADMAP.md ✅ (this file)
├── API_REFERENCE.md (TO CREATE)
├── USER_GUIDE.md (TO CREATE)
└── DATABASE_SCHEMA.md (TO CREATE)
```

---

## 🎯 SUCCESS INDICATORS

After each phase, verify:

**Phase 1 (Database)**
- [ ] All 5 migrations ran successfully
- [ ] All tables created with correct columns
- [ ] 9 lead stages seeded
- [ ] All foreign keys working
- [ ] No migration errors in logs

**Phase 2 (API)**
- [ ] All 43+ endpoints accessible
- [ ] CRUD operations working
- [ ] Validation working correctly
- [ ] Error handling working
- [ ] All tests passing

**Phase 3 (Automation)**
- [ ] Auto-transitions working
- [ ] Emails sending correctly
- [ ] Reminders triggering
- [ ] Queue jobs processing
- [ ] No task failures

**Phase 4 (Frontend)**
- [ ] All views loading
- [ ] Forms submitting
- [ ] Data displaying correctly
- [ ] Mobile responsive
- [ ] User acceptance passed

---

## 📞 TROUBLESHOOTING QUICK GUIDE

| Issue | Solution |
|-------|----------|
| Migrations fail | Clear cache: `php artisan cache:clear` |
| Foreign key errors | Check table order in migrations |
| Models not loading | Run: `composer dump-autoload` |
| Routes not found | Run: `php artisan route:clear` |
| Seeder not working | Run: `php artisan migrate:refresh --seed` |
| Tests failing | Run tests individually first |
| Performance slow | Check indexes, use eager loading |
| Emails not sending | Check queue worker: `php artisan queue:work` |

---

**Ready to start?**
👉 Begin with **Phase 1, Day 1** - Create the first migration!

**Questions?**
Check the comprehensive documentation:
- `ENHANCED_LEADS_MODULE_PLAN.md` - Full specifications
- `ENHANCED_LEADS_TECHNICAL_SPECS.md` - Technical details

---

**Last Updated:** January 15, 2026  
**Status:** Ready for Development
