# Saubhagya Group Meeting Booking API

## Overview
This API endpoint allows users to schedule meetings with Saubhagya Group. The submission endpoint is public (with rate limiting), while management endpoints require authentication.

## Base URL
```
/api/v1/schedule-meeting
```

## Endpoints

### 1. Submit Meeting Request (Public)
**POST** `/api/v1/schedule-meeting`

Submit a new meeting request for Saubhagya Group.

#### Rate Limiting
- 5 requests per minute per IP address

#### Request Body
```json
{
  "site_slug": "saubhagya-group",  // Optional - defaults to saubhagya-group
  "full_name": "John Doe",          // Required
  "email": "john@example.com",      // Required - valid email
  "phone": "+977-9821812699",       // Optional
  "company": "Your Company",        // Optional
  "preferred_date": "2025-12-25",   // Required - format: YYYY-MM-DD, must be today or future
  "preferred_time": "9:00 AM - 10:00 AM",  // Required
  "meeting_type": "Partnership Discussion",  // Required - see allowed values below
  "message": "Additional details..."  // Optional - max 2000 characters
}
```

#### Meeting Type Values (Required)
- `Partnership Discussion`
- `Investment Opportunity`
- `Franchise Inquiry`
- `Project Consultation`
- `General Inquiry`

#### Preferred Time Options
- `9:00 AM - 10:00 AM`
- `10:00 AM - 11:00 AM`
- `11:00 AM - 12:00 PM`
- `1:00 PM - 2:00 PM`
- `2:00 PM - 3:00 PM`
- `3:00 PM - 4:00 PM`
- `4:00 PM - 5:00 PM`

#### Success Response (201 Created)
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

#### Error Response (422 Validation Failed)
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

---

### 2. Get All Meeting Requests (Protected)
**GET** `/api/v1/schedule-meeting`

Retrieve all meeting requests with pagination.

#### Authentication
Requires Bearer token via Sanctum authentication.

#### Headers
```
Authorization: Bearer YOUR_AUTH_TOKEN
```

#### Query Parameters
- `status` (optional) - Filter by status: `pending`, `confirmed`, `completed`, `cancelled`
- `site_id` (optional) - Filter by site ID
- `per_page` (optional) - Items per page (default: 15)

#### Example Request
```
GET /api/v1/schedule-meeting?status=pending&per_page=20
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "site_id": 1,
        "full_name": "John Doe",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+977-9821812699",
        "company": "Your Company",
        "meeting_type": "Partnership Discussion",
        "subject": "Partnership Discussion",
        "message": "Additional details...",
        "preferred_date": "2025-12-25",
        "preferred_time": "9:00 AM - 10:00 AM",
        "status": "pending",
        "ip_address": "192.168.1.1",
        "user_agent": "Mozilla/5.0...",
        "created_at": "2025-12-21T10:30:00.000000Z",
        "updated_at": "2025-12-21T10:30:00.000000Z",
        "site": {
          "id": 1,
          "name": "Saubhagya Group",
          "slug": "saubhagya-group"
        }
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

---

### 3. Get Single Meeting Request (Protected)
**GET** `/api/v1/schedule-meeting/{id}`

Retrieve details of a specific meeting request.

#### Authentication
Requires Bearer token via Sanctum authentication.

#### Headers
```
Authorization: Bearer YOUR_AUTH_TOKEN
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "site_id": 1,
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "+977-9821812699",
    "company": "Your Company",
    "meeting_type": "Partnership Discussion",
    "preferred_date": "2025-12-25",
    "preferred_time": "9:00 AM - 10:00 AM",
    "status": "pending",
    "message": "Additional details...",
    "created_at": "2025-12-21T10:30:00.000000Z",
    "updated_at": "2025-12-21T10:30:00.000000Z",
    "site": {
      "id": 1,
      "name": "Saubhagya Group",
      "slug": "saubhagya-group"
    }
  }
}
```

#### Error Response (404 Not Found)
```json
{
  "success": false,
  "message": "Meeting request not found"
}
```

---

### 4. Update Meeting Status (Protected)
**PATCH** `/api/v1/schedule-meeting/{id}/status`

Update the status of a meeting request.

#### Authentication
Requires Bearer token via Sanctum authentication.

#### Headers
```
Authorization: Bearer YOUR_AUTH_TOKEN
Content-Type: application/json
```

#### Request Body
```json
{
  "status": "confirmed"  // Required: pending, confirmed, completed, cancelled
}
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Meeting status updated successfully",
  "data": {
    "id": 1,
    "full_name": "John Doe",
    "status": "confirmed",
    "updated_at": "2025-12-21T11:00:00.000000Z"
  }
}
```

---

### 5. Delete Meeting Request (Protected)
**DELETE** `/api/v1/schedule-meeting/{id}`

Delete a meeting request.

#### Authentication
Requires Bearer token via Sanctum authentication.

#### Headers
```
Authorization: Bearer YOUR_AUTH_TOKEN
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Meeting request deleted successfully"
}
```

---

## Authentication

### Getting an Auth Token
To access protected endpoints, you need to authenticate using Laravel Sanctum:

1. **Login or Register** to get an authentication token
2. **Include the token** in the Authorization header for all protected requests

```bash
# Example with curl
curl -X GET "https://your-domain.com/api/v1/schedule-meeting" \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Accept: application/json"
```

---

## Example Usage

### JavaScript/Fetch Example (Public Submission)
```javascript
const submitMeeting = async () => {
  const data = {
    full_name: "John Doe",
    email: "john@example.com",
    phone: "+977-9821812699",
    company: "ABC Corporation",
    preferred_date: "2025-12-25",
    preferred_time: "9:00 AM - 10:00 AM",
    meeting_type: "Partnership Discussion",
    message: "Looking forward to discussing partnership opportunities."
  };

  try {
    const response = await fetch('https://your-domain.com/api/v1/schedule-meeting', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();
    
    if (result.success) {
      console.log('Meeting scheduled:', result.data);
    } else {
      console.error('Error:', result.message);
    }
  } catch (error) {
    console.error('Request failed:', error);
  }
};
```

### JavaScript/Axios Example (Protected Endpoint)
```javascript
import axios from 'axios';

const getMeetings = async (authToken) => {
  try {
    const response = await axios.get('https://your-domain.com/api/v1/schedule-meeting', {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      },
      params: {
        status: 'pending',
        per_page: 20
      }
    });

    console.log('Meetings:', response.data.data);
  } catch (error) {
    console.error('Error fetching meetings:', error.response?.data);
  }
};
```

### cURL Example (Public Submission)
```bash
curl -X POST "https://your-domain.com/api/v1/schedule-meeting" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "+977-9821812699",
    "company": "ABC Corporation",
    "preferred_date": "2025-12-25",
    "preferred_time": "9:00 AM - 10:00 AM",
    "meeting_type": "Partnership Discussion",
    "message": "Looking forward to discussing partnership opportunities."
  }'
```

### cURL Example (Protected - Get Meetings)
```bash
curl -X GET "https://your-domain.com/api/v1/schedule-meeting?status=pending" \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Accept: application/json"
```

---

## Status Workflow

1. **pending** - Initial status when meeting is submitted
2. **confirmed** - Admin has confirmed the meeting
3. **completed** - Meeting has been completed
4. **cancelled** - Meeting has been cancelled

---

## Error Codes

| Code | Description |
|------|-------------|
| 200  | Success |
| 201  | Created successfully |
| 401  | Unauthorized (invalid or missing token) |
| 404  | Resource not found |
| 422  | Validation failed |
| 429  | Too many requests (rate limit exceeded) |

---

## Database Schema

### schedule_meetings Table
```sql
- id (bigint, primary key)
- site_id (foreign key to sites table)
- name (string) - legacy field
- full_name (string) - new field
- email (string)
- phone (string, nullable)
- company (string, nullable)
- meeting_type (string, nullable)
- subject (string)
- message (text, nullable)
- preferred_date (date, nullable)
- preferred_time (time, nullable)
- status (enum: pending, confirmed, completed, cancelled)
- ip_address (string, nullable)
- user_agent (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## Notes

1. **Rate Limiting**: The public submission endpoint is rate-limited to prevent abuse (5 requests per minute per IP)
2. **Date Validation**: Preferred date must be today or a future date
3. **Email Notifications**: Consider setting up email notifications when new meetings are submitted
4. **Site Integration**: The API supports multi-site architecture. If no site_slug is provided, it defaults to 'saubhagya-group'
5. **IP Tracking**: The system automatically captures the submitter's IP address and user agent for security purposes

---

## Support

For API support or questions, please contact your system administrator.
