# Leads Module - Verification & Testing Guide

## Quick Verification Steps

### 1. Check Routes
```bash
php artisan route:list --name=leads
```

**Expected Output:** 13 routes registered
- ✅ Verified: All 13 routes are registered correctly

### 2. Check Migrations
```bash
php artisan migrate:status
```

**Expected Tables:**
- ✅ `service_leads` - Created successfully
- ✅ `lead_statuses` - Created successfully with 10 seed statuses

### 3. Check Models
```bash
php artisan tinker
```

Then run:
```php
// Check if models are accessible
ServiceLead::count();
LeadStatus::count(); // Should return 10

// Check relationships
$lead = new ServiceLead();
$lead->client_name = "Test";
$lead->service_requested = "Home Inspection";
$lead->location = "Test Location";
$lead->phone_number = "555-0000";
$lead->email = "test@test.com";
$lead->status = "Intake";
$lead->save();

// Verify
ServiceLead::first();
```

### 4. Test API Endpoints

#### Create a Test Lead
```bash
curl -X POST http://localhost:8000/admin/leads \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_SESSION_COOKIE" \
  -d '{
    "client_name": "John Doe",
    "service_requested": "Home Inspection",
    "location": "123 Main St, Phoenix",
    "phone_number": "(555) 123-4567",
    "email": "john@example.com",
    "inspection_date": "2026-02-15",
    "inspection_time": "14:00",
    "inspection_charge": 350.00,
    "status": "Intake",
    "remarks": "Client prefers afternoon appointments"
  }'
```

#### Get All Statuses
```bash
curl http://localhost:8000/admin/leads/statuses \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_SESSION_COOKIE"
```

#### Get All Leads
```bash
curl http://localhost:8000/admin/leads \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_SESSION_COOKIE"
```

#### Get Analytics
```bash
curl http://localhost:8000/admin/leads/analytics \
  -H "Accept: application/json" \
  -H "Cookie: YOUR_SESSION_COOKIE"
```

## Browser Testing

### Using Postman/Insomnia

1. **Login** to get session cookie
2. **Import** these endpoints:

```json
{
  "name": "Leads Management API",
  "endpoints": [
    {
      "method": "GET",
      "url": "{{base_url}}/admin/leads",
      "description": "List all leads"
    },
    {
      "method": "POST",
      "url": "{{base_url}}/admin/leads",
      "body": {
        "client_name": "Test Client",
        "service_requested": "Home Inspection",
        "location": "Test Location",
        "phone_number": "555-0000",
        "email": "test@test.com",
        "status": "Intake"
      }
    },
    {
      "method": "GET",
      "url": "{{base_url}}/admin/leads/statuses"
    },
    {
      "method": "GET",
      "url": "{{base_url}}/admin/leads/analytics"
    }
  ]
}
```

## Manual Testing Checklist

### Database Layer
- [ ] `service_leads` table exists
- [ ] `lead_statuses` table exists
- [ ] 10 statuses are seeded
- [ ] Foreign keys are working
- [ ] Indexes are created

### Models
- [ ] ServiceLead model loads
- [ ] LeadStatus model loads
- [ ] Relationships work (assignedTo, createdBy)
- [ ] Scopes work (active, pending, completed)
- [ ] Accessors work (assigned_to_name, status_badge)

### Controllers
- [ ] ServiceLeadController responds
- [ ] LeadAnalyticsController responds
- [ ] Validation works on create/update
- [ ] JSON responses are correct
- [ ] Error handling works

### Routes
- [ ] All 13 routes are accessible
- [ ] Middleware is applied (admin only)
- [ ] Route model binding works

### Features to Test

#### Create Lead
1. Go to `/admin/leads/create` (when frontend is ready)
2. Fill in all required fields
3. Submit form
4. Check if lead is created in database

#### Edit Lead
1. Go to `/admin/leads/{id}/edit`
2. Modify some fields
3. Submit
4. Verify updates

#### Delete Lead
1. Delete a lead
2. Check if soft deleted (deleted_at is set)
3. Verify it doesn't appear in normal lists

#### Status Update
1. Change lead status
2. Verify status badge color changes
3. Check if updated_at timestamp changes

#### Search & Filter
1. Search by client name
2. Filter by status
3. Filter by assigned user
4. Filter by date range

#### Analytics
1. View analytics dashboard
2. Check summary statistics
3. Verify charts data
4. Test date range filters

#### Assignment
1. Assign lead to user
2. Verify user receives email (when implemented)
3. Check if assigned_to is updated

## Sample Test Data

### Create Multiple Test Leads

```sql
INSERT INTO service_leads (
  client_name, service_requested, location, phone_number, email,
  inspection_date, inspection_time, inspection_charge, status,
  created_at, updated_at
) VALUES
('John Smith', 'Home Inspection', '123 Main St, Phoenix, AZ', '(555) 111-1111', 'john@test.com', 
 '2026-02-15', '10:00:00', 350.00, 'Inspection Booked', NOW(), NOW()),
('Sarah Johnson', 'Kitchen Renovation', '456 Oak Ave, Scottsdale, AZ', '(555) 222-2222', 'sarah@test.com',
 '2026-02-20', '14:00:00', 5000.00, 'Contacted', NOW(), NOW()),
('Mike Wilson', 'Commercial Inspection', '789 Business Blvd, Tempe, AZ', '(555) 333-3333', 'mike@test.com',
 '2026-02-18', '09:00:00', 750.00, 'Intake', NOW(), NOW()),
('Emily Davis', 'Bathroom Renovation', '321 Desert Dr, Mesa, AZ', '(555) 444-4444', 'emily@test.com',
 '2026-03-01', '11:00:00', 3500.00, 'Positive', NOW(), NOW()),
('Robert Brown', 'HVAC Installation', '654 Valley Rd, Chandler, AZ', '(555) 555-5555', 'robert@test.com',
 NULL, NULL, 2500.00, 'Cancelled', NOW(), NOW());
```

## Performance Testing

### Load Testing
```bash
# Create 100 test leads
for i in {1..100}; do
  curl -X POST http://localhost:8000/admin/leads \
    -H "Content-Type: application/json" \
    -d "{
      \"client_name\": \"Test Client $i\",
      \"service_requested\": \"Home Inspection\",
      \"location\": \"Test Location $i\",
      \"phone_number\": \"555-000$i\",
      \"email\": \"test$i@test.com\",
      \"status\": \"Intake\"
    }"
done

# Test search performance
time curl "http://localhost:8000/admin/leads?search=Test"

# Test analytics with large dataset
time curl "http://localhost:8000/admin/leads/analytics"
```

## Expected Results

### Status Counts
After seeding 5 test leads:
- Intake: 1
- Contacted: 1
- Inspection Booked: 1
- Positive: 1
- Cancelled: 1

### Analytics Summary
- Total Leads: 5
- Completed Services: 1
- Pending Services: 3
- Cancelled: 1
- Conversion Rate: 20%
- Total Revenue: $12,100.00

## Troubleshooting

### Issue: Routes not found
**Solution:** Run `php artisan route:clear && php artisan route:cache`

### Issue: Models not found
**Solution:** Run `composer dump-autoload`

### Issue: Validation errors
**Solution:** Check [LEADS_API_REFERENCE.md](LEADS_API_REFERENCE.md) for required fields

### Issue: 403 Forbidden
**Solution:** Check admin middleware and user permissions

### Issue: Database connection
**Solution:** Check `.env` database credentials

## Next Steps After Verification

Once all tests pass:
1. Create frontend Blade templates
2. Add navigation menu item
3. Setup permissions
4. Implement email notifications
5. Add to admin sidebar
6. User acceptance testing
7. Deploy to production

---

**Testing Date:** January 5, 2026  
**Status:** Ready for Testing  
**Backend:** 100% Complete  
**Frontend:** Pending Implementation

