# Leads Management API - Quick Reference

## Base URL
All endpoints are prefixed with `/admin/leads`

## Authentication
All endpoints require authentication via Laravel session/Sanctum.

---

## Endpoints

### 1. List All Leads
```
GET /admin/leads
```

**Query Parameters:**
- `search` - Search by client name, location, service type
- `status` - Filter by status key
- `assigned_to` - Filter by assigned user ID
- `date_from` - Filter from date (Y-m-d)
- `date_to` - Filter to date (Y-m-d)
- `page` - Page number (default: 1)

**Example:**
```bash
GET /admin/leads?search=John&status=Intake&page=1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "client_name": "John Doe",
        "email": "john@example.com",
        "phone_number": "(555) 123-4567",
        "service_requested": "Home Inspection",
        "location": "123 Main St, City",
        "inspection_date": "2024-01-15",
        "inspection_time": "10:00:00",
        "inspection_charge": 350.00,
        "status": "Inspection Booked",
        "assigned_to_name": "Jane Smith",
        "created_at": "2024-01-10T08:30:00Z"
      }
    ],
    "total": 150,
    "per_page": 20
  }
}
```

---

### 2. Get All Statuses
```
GET /admin/leads/statuses
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "status_key": "Intake",
      "display_name": "Intake",
      "color_class": "bg-blue-50 text-blue-700 border-blue-200",
      "priority": 1
    }
  ]
}
```

---

### 3. Create Lead
```
POST /admin/leads
```

**Request Body:**
```json
{
  "client_name": "John Doe",
  "service_requested": "Home Inspection",
  "location": "123 Main St, City",
  "phone_number": "(555) 123-4567",
  "email": "john@example.com",
  "inspection_date": "2024-01-15",
  "inspection_time": "10:00",
  "inspection_charge": 350.00,
  "inspection_report_date": null,
  "inspection_assigned_to": 5,
  "status": "Intake",
  "remarks": "Client wants afternoon appointment"
}
```

**Required Fields:**
- `client_name`
- `service_requested`
- `location`
- `phone_number`
- `email`
- `status`

**Response:**
```json
{
  "success": true,
  "message": "Lead created successfully",
  "data": {
    "id": 15,
    "client_name": "John Doe",
    ...
  }
}
```

---

### 4. View Single Lead
```
GET /admin/leads/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "client_name": "John Doe",
    "email": "john@example.com",
    "phone_number": "(555) 123-4567",
    "service_requested": "Home Inspection",
    "location": "123 Main St",
    "inspection_date": "2024-01-15",
    "inspection_time": "10:00:00",
    "inspection_charge": 350.00,
    "inspection_report_date": null,
    "status": "Inspection Booked",
    "remarks": "Note",
    "assigned_to": {
      "id": 5,
      "name": "Jane Smith"
    },
    "created_by": {
      "id": 1,
      "name": "Admin User"
    },
    "status_info": {
      "status_key": "Inspection Booked",
      "display_name": "Booked",
      "color_class": "bg-green-50 text-green-700"
    }
  }
}
```

---

### 5. Update Lead
```
PUT /admin/leads/{id}
```

**Request Body:** (Same as create, all fields optional)
```json
{
  "client_name": "John Updated",
  "inspection_charge": 400.00,
  "status": "Contacted"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Lead updated successfully",
  "data": { ... }
}
```

---

### 6. Update Status Only
```
PATCH /admin/leads/{id}/status
```

**Request Body:**
```json
{
  "status": "Contacted"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Status updated successfully",
  "data": { ... }
}
```

---

### 7. Assign Lead to User
```
PATCH /admin/leads/{id}/assign
```

**Request Body:**
```json
{
  "user_id": 5
}
```

**Response:**
```json
{
  "success": true,
  "message": "Lead assigned successfully",
  "data": { ... }
}
```

---

### 8. Delete Lead
```
DELETE /admin/leads/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "Lead deleted successfully"
}
```

---

### 9. Get Analytics
```
GET /admin/leads/analytics
```

**Query Parameters:**
- `date_from` - Start date (default: 30 days ago)
- `date_to` - End date (default: today)

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_leads": 150,
      "completed_services": 85,
      "positive_clients": 45,
      "pending_services": 45,
      "cancelled_leads": 20,
      "conversion_rate": 56.67,
      "positive_rate": 30.00,
      "cancellation_rate": 13.33,
      "total_revenue": 29750.00,
      "average_charge": 350.00,
      "potential_revenue": 15750.00
    },
    "status_distribution": [
      {
        "status": "Intake",
        "display_name": "Intake",
        "count": 15,
        "color": "bg-blue-50 text-blue-700"
      }
    ],
    "service_distribution": [
      {
        "service_requested": "Home Inspection",
        "count": 65
      }
    ],
    "monthly_trend": [
      {
        "month": "2024-01",
        "total_leads": 35,
        "completed": 20,
        "revenue": 7000.00
      }
    ],
    "location_distribution": [
      {
        "location": "Phoenix, AZ",
        "count": 25,
        "revenue": 8750.00
      }
    ],
    "staff_performance": [
      {
        "name": "Jane Smith",
        "total_assigned": 30,
        "completed": 18,
        "positive": 12,
        "pending": 8,
        "cancelled": 4,
        "total_revenue": 6300.00,
        "conversion_rate": 60.0,
        "avg_revenue": 210.00,
        "report_efficiency": 88.9
      }
    ],
    "date_range": {
      "from": "2024-01-01",
      "to": "2024-01-31"
    }
  }
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "errors": {
    "email": ["The email field must be a valid email address."],
    "phone_number": ["The phone number field is required."]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Lead not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to create lead",
  "error": "Database connection error"
}
```

---

## Service Types

Available values for `service_requested`:
1. Home Inspection
2. Pre-Purchase Inspection
3. Commercial Inspection
4. New Construction
5. Renovation Project
6. Kitchen Renovation
7. Bathroom Renovation
8. Home Addition
9. Roofing Services
10. Flooring Installation
11. Electrical Work
12. Plumbing Services
13. HVAC Installation
14. Structural Assessment
15. Property Consultation

---

## Status Keys

Available values for `status`:
- Intake
- Contacted
- Inspection Booked
- Inspection Rescheduled
- Office Visit Requested
- Reports Sent
- Bad Lead
- Out of Valley
- Cancelled
- Positive

---

## Testing with cURL

### Create a lead:
```bash
curl -X POST http://your-domain/admin/leads \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "client_name": "Test Client",
    "service_requested": "Home Inspection",
    "location": "123 Test St",
    "phone_number": "(555) 000-0000",
    "email": "test@example.com",
    "status": "Intake"
  }'
```

### Get all leads:
```bash
curl -X GET "http://your-domain/admin/leads?search=Test" \
  -H "Accept: application/json"
```

### Update status:
```bash
curl -X PATCH http://your-domain/admin/leads/1/status \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status": "Contacted"}'
```

---

## Notes

- All dates should be in `Y-m-d` format (e.g., 2024-01-15)
- Time should be in `H:i` format (e.g., 14:30)
- Inspection charge accepts decimal values with 2 decimal places
- The `created_by` field is automatically set to the authenticated user
- Deleted leads are soft-deleted and can be restored if needed
- All API endpoints support both JSON and HTML responses

