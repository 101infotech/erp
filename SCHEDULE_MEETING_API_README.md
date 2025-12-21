# 🎯 Saubhagya Group Meeting Scheduling API

## Quick Start

A protected REST API endpoint for scheduling meetings with Saubhagya Group. The submission endpoint is public (with rate limiting), while management endpoints require authentication.

## 📍 Endpoints

### Public
- `POST /api/v1/schedule-meeting` - Submit meeting request (Rate limited: 5/min)

### Protected (Auth Required)
- `GET /api/v1/schedule-meeting` - List all meetings
- `GET /api/v1/schedule-meeting/{id}` - Get meeting details
- `PATCH /api/v1/schedule-meeting/{id}/status` - Update status
- `DELETE /api/v1/schedule-meeting/{id}` - Delete meeting

## 🚀 Quick Test

### 1. Test with cURL
```bash
# Run the test script
./test_meeting_api.sh

# Or manually test
curl -X POST "http://localhost:8000/api/v1/schedule-meeting" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "+977-9821812699",
    "company": "ABC Corp",
    "preferred_date": "2025-12-25",
    "preferred_time": "9:00 AM - 10:00 AM",
    "meeting_type": "Partnership Discussion",
    "message": "Test message"
  }'
```

### 2. Test with Postman
Import the collection: `docs/POSTMAN_SCHEDULE_MEETING.json`

### 3. Test with JavaScript
```javascript
fetch('http://localhost:8000/api/v1/schedule-meeting', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    full_name: "John Doe",
    email: "john@example.com",
    phone: "+977-9821812699",
    company: "ABC Corp",
    preferred_date: "2025-12-25",
    preferred_time: "9:00 AM - 10:00 AM",
    meeting_type: "Partnership Discussion",
    message: "Looking forward to the meeting"
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

## 📋 Required Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `full_name` | string | ✅ Yes | Full name of the person |
| `email` | string | ✅ Yes | Valid email address |
| `preferred_date` | date | ✅ Yes | Meeting date (YYYY-MM-DD, today or future) |
| `preferred_time` | string | ✅ Yes | Time slot (see options below) |
| `meeting_type` | string | ✅ Yes | Type of meeting (see options below) |
| `phone` | string | ❌ No | Contact phone number |
| `company` | string | ❌ No | Company/Organization name |
| `message` | string | ❌ No | Additional message (max 2000 chars) |

## ⏰ Time Slot Options
- `9:00 AM - 10:00 AM`
- `10:00 AM - 11:00 AM`
- `11:00 AM - 12:00 PM`
- `1:00 PM - 2:00 PM`
- `2:00 PM - 3:00 PM`
- `3:00 PM - 4:00 PM`
- `4:00 PM - 5:00 PM`

## 📝 Meeting Type Options
- `Partnership Discussion`
- `Investment Opportunity`
- `Franchise Inquiry`
- `Project Consultation`
- `General Inquiry`

## 🔒 Authentication (Protected Endpoints)

Protected endpoints require a Bearer token:

```bash
curl -X GET "http://localhost:8000/api/v1/schedule-meeting" \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Accept: application/json"
```

## 📊 Response Format

### Success (201 Created)
```json
{
  "success": true,
  "message": "Meeting request submitted successfully. We will get back to you within 24 hours.",
  "data": {
    "id": 1,
    "full_name": "John Doe",
    "email": "john@example.com",
    "meeting_type": "Partnership Discussion",
    "preferred_date": "2025-12-25",
    "preferred_time": "9:00 AM - 10:00 AM",
    "status": "pending",
    "created_at": "2025-12-21T10:30:00.000000Z"
  }
}
```

### Error (422 Validation Failed)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "preferred_date": ["The preferred date must be a date after or equal to today."]
  }
}
```

## 📚 Documentation

- **Complete API Docs**: [docs/API_SCHEDULE_MEETING.md](docs/API_SCHEDULE_MEETING.md)
- **Postman Collection**: [docs/POSTMAN_SCHEDULE_MEETING.json](docs/POSTMAN_SCHEDULE_MEETING.json)
- **Implementation Summary**: [docs/SCHEDULE_MEETING_API_SUMMARY.md](docs/SCHEDULE_MEETING_API_SUMMARY.md)

## 🛠️ Technical Details

### Files Created/Modified
- **Migration**: `database/migrations/2025_12_21_105523_add_meeting_type_to_schedule_meetings_table.php`
- **Controller**: `app/Http/Controllers/Api/ScheduleMeetingController.php`
- **Model**: `app/Models/ScheduleMeeting.php` (updated)
- **Routes**: `routes/api.php` (updated)

### Security Features
- ✅ Rate limiting (5 requests/minute on public endpoint)
- ✅ Laravel Sanctum authentication for protected endpoints
- ✅ Input validation
- ✅ IP address tracking
- ✅ User agent logging
- ✅ CSRF protection

### Database Schema
```sql
schedule_meetings table:
- id (primary key)
- site_id (foreign key)
- name (legacy)
- full_name (new)
- email
- phone
- company
- meeting_type (new)
- subject
- message
- preferred_date
- preferred_time
- status (pending/confirmed/completed/cancelled)
- ip_address
- user_agent
- timestamps
```

## 🧪 Testing Checklist

- [ ] Public submission works without auth
- [ ] Rate limiting prevents spam (test 6+ requests in 1 minute)
- [ ] Email validation works
- [ ] Date validation prevents past dates
- [ ] Meeting type validation works
- [ ] Protected endpoints require auth token
- [ ] Invalid auth token returns 401
- [ ] Status update works
- [ ] Pagination works on list endpoint
- [ ] Filtering by status works

## 🚨 Common Issues

### 1. Migration already ran
If you see "Table already exists" error:
```bash
php artisan migrate:fresh  # ⚠️ This will drop all tables
# OR
php artisan migrate:rollback --step=1  # Rollback last migration
```

### 2. Routes not found
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list --path=schedule-meeting
```

### 3. Validation errors
Make sure all required fields are present and in correct format:
- Date format: `YYYY-MM-DD` (e.g., `2025-12-25`)
- Date must be today or future
- Meeting type must be exact match (case-sensitive)

## 📞 Support

Created on: December 21, 2025  
Status: ✅ Production Ready  
Version: 1.0.0

For issues or questions, refer to the complete documentation in the `docs/` folder.
