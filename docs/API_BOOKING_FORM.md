# Brand Bird Agency Booking Form API

## Overview
This API endpoint allows users to book a complimentary Brand Flight Consultation with Brand Bird Agency. It's a multi-step form that collects user details, business information, and goals. The submission endpoint is public (with rate limiting), while management endpoints require authentication.

## Base URL
```
/api/v1/booking
```

## Endpoints

### 1. Submit Booking Form (Public)
**POST** `/api/v1/booking`

Submit a brand flight consultation booking request.

#### Rate Limiting
- 10 requests per minute per IP address

#### Request Body
```json
{
  "site_slug": "brandbirdagency",  // Required - site identifier
  "first_name": "John",             // Required
  "last_name": "Doe",               // Required
  "email": "john@example.com",      // Required - valid email
  "phone": "+1-234-567-8900",       // Required
  "company_name": "Tech Innovations Inc.",  // Optional
  "website_url": "https://example.com",     // Optional - valid URL
  "industry": "Technology & SaaS",           // Optional
  "services_interested": [                   // Optional - array of services
    "Brand Strategy & Identity Development",
    "Digital Marketing Campaign Creation",
    "Social Media Flight Management"
  ],
  "investment_range": "$10,000 - $25,000",  // Optional
  "flight_timeline": "3-6 months",           // Optional
  "current_marketing_efforts": "Currently running social media campaigns and email marketing",  // Optional
  "marketing_goals": "Increase brand awareness and customer acquisition by 50% in next quarter",  // Optional
  "current_challenges": "Limited budget and struggling with consistent brand messaging"  // Optional
}
```

#### Industry Options
- Technology & SaaS
- E-commerce & Retail
- Healthcare & Wellness
- Financial Services
- Real Estate
- Education & Training
- Food & Beverage
- Fashion & Beauty
- Professional Services
- Non-profit & NGO
- Other

#### Services Interested Options
- Brand Strategy & Identity Development
- Digital Marketing Campaign Creation
- Social Media Flight Management
- Content Creation & Wing Crafting
- SEO & Website Sky Optimization
- Email Marketing Flight Automation
- Paid Advertising (PPC/Social Soaring)
- Marketing Analytics & Flight Tracking
- Brand Repositioning & Market Migration
- Influencer Partnership Networks

#### Success Response (201 Created)
```json
{
  "success": true,
  "message": "Thank you for your interest! Our team will review your information and reach out within 24 hours to schedule your complimentary Brand Flight Consultation.",
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "company_name": "Tech Innovations Inc.",
    "status": "new",
    "created_at": "2025-12-21T11:30:00.000000Z"
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
    "first_name": ["The first name field is required."]
  }
}
```

---

### 2. Get All Bookings (Protected)
**GET** `/api/v1/booking`

Retrieve all booking submissions with filtering and pagination.

#### Authentication
Requires Bearer token via Sanctum authentication.

#### Headers
```
Authorization: Bearer YOUR_AUTH_TOKEN
```

#### Query Parameters
- `status` (optional) - Filter by status: `new`, `contacted`, `scheduled`, `completed`, `cancelled`
- `site_id` (optional) - Filter by site ID
- `search` (optional) - Search by name, email, or company
- `per_page` (optional) - Items per page (default: 15)

#### Example Request
```
GET /api/v1/booking?status=new&search=john&per_page=20
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
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone": "+1-234-567-8900",
        "company_name": "Tech Innovations Inc.",
        "website_url": "https://example.com",
        "industry": "Technology & SaaS",
        "services_interested": [
          "Brand Strategy & Identity Development",
          "Digital Marketing Campaign Creation"
        ],
        "investment_range": "$10,000 - $25,000",
        "flight_timeline": "3-6 months",
        "current_marketing_efforts": "Currently running social media campaigns",
        "marketing_goals": "Increase brand awareness by 50%",
        "current_challenges": "Limited budget",
        "status": "new",
        "ip_address": "192.168.1.1",
        "created_at": "2025-12-21T11:30:00.000000Z",
        "updated_at": "2025-12-21T11:30:00.000000Z",
        "site": {
          "id": 1,
          "name": "Brand Bird Agency",
          "slug": "brandbirdagency"
        }
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

---

### 3. Get Single Booking (Protected)
**GET** `/api/v1/booking/{id}`

Retrieve details of a specific booking submission.

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
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1-234-567-8900",
    "company_name": "Tech Innovations Inc.",
    "website_url": "https://example.com",
    "industry": "Technology & SaaS",
    "services_interested": [
      "Brand Strategy & Identity Development",
      "Digital Marketing Campaign Creation"
    ],
    "investment_range": "$10,000 - $25,000",
    "flight_timeline": "3-6 months",
    "current_marketing_efforts": "Currently running social media campaigns",
    "marketing_goals": "Increase brand awareness by 50%",
    "current_challenges": "Limited budget",
    "status": "new",
    "created_at": "2025-12-21T11:30:00.000000Z",
    "updated_at": "2025-12-21T11:30:00.000000Z",
    "site": {
      "id": 1,
      "name": "Brand Bird Agency",
      "slug": "brandbirdagency"
    }
  }
}
```

#### Error Response (404 Not Found)
```json
{
  "success": false,
  "message": "Booking not found"
}
```

---

### 4. Update Booking Status (Protected)
**PATCH** `/api/v1/booking/{id}/status`

Update the status of a booking submission.

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
  "status": "contacted"  // Required: new, contacted, scheduled, completed, cancelled
}
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Booking status updated successfully",
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "status": "contacted",
    "updated_at": "2025-12-21T12:00:00.000000Z"
  }
}
```

---

### 5. Delete Booking (Protected)
**DELETE** `/api/v1/booking/{id}`

Delete a booking submission.

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
  "message": "Booking deleted successfully"
}
```

---

## Form Structure (Multi-Step)

### Step 1: Your Details
**Required Fields:**
- `first_name` - First name
- `last_name` - Last name
- `email` - Email address
- `phone` - Phone number

### Step 2: Business & Services
**Required Fields:**
- None (all optional)

**Optional Fields:**
- `company_name` - Company name
- `website_url` - Website URL
- `industry` - Industry selection (dropdown)
- `services_interested` - Array of selected services (checkboxes)
- `investment_range` - Budget range (dropdown)
- `flight_timeline` - Project timeline (dropdown)
- `current_marketing_efforts` - Text area for current efforts

### Step 3: Goals & Vision
**Optional Fields:**
- `marketing_goals` - Main marketing goals (textarea)
- `current_challenges` - Current challenges (textarea)

---

## Status Workflow

1. **new** - Initial status when booking is submitted
2. **contacted** - Team has reached out to the client
3. **scheduled** - Consultation is scheduled
4. **completed** - Consultation completed
5. **cancelled** - Booking cancelled

---

## Example Usage

### JavaScript/Fetch Example (Public Submission)
```javascript
const submitBooking = async (formData) => {
  const data = {
    site_slug: "brandbirdagency",
    first_name: formData.firstName,
    last_name: formData.lastName,
    email: formData.email,
    phone: formData.phone,
    company_name: formData.companyName,
    website_url: formData.websiteUrl,
    industry: formData.industry,
    services_interested: formData.selectedServices, // Array
    investment_range: formData.investmentRange,
    flight_timeline: formData.flightTimeline,
    current_marketing_efforts: formData.currentEfforts,
    marketing_goals: formData.marketingGoals,
    current_challenges: formData.currentChallenges
  };

  try {
    const response = await fetch('https://your-domain.com/api/v1/booking', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();
    
    if (result.success) {
      console.log('Booking submitted:', result.data);
      // Show success message
    } else {
      console.error('Validation errors:', result.errors);
      // Show error messages
    }
  } catch (error) {
    console.error('Request failed:', error);
  }
};
```

### JavaScript/Axios Example (Protected Endpoint)
```javascript
import axios from 'axios';

const getBookings = async (authToken, filters = {}) => {
  try {
    const response = await axios.get('https://your-domain.com/api/v1/booking', {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      },
      params: {
        status: filters.status || 'new',
        search: filters.search || '',
        per_page: filters.perPage || 20
      }
    });

    console.log('Bookings:', response.data.data);
  } catch (error) {
    console.error('Error fetching bookings:', error.response?.data);
  }
};
```

### cURL Example (Public Submission)
```bash
curl -X POST "https://your-domain.com/api/v1/booking" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "site_slug": "brandbirdagency",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1-234-567-8900",
    "company_name": "Tech Innovations Inc.",
    "website_url": "https://example.com",
    "industry": "Technology & SaaS",
    "services_interested": [
      "Brand Strategy & Identity Development",
      "Digital Marketing Campaign Creation"
    ],
    "investment_range": "$10,000 - $25,000",
    "flight_timeline": "3-6 months",
    "current_marketing_efforts": "Running social media campaigns",
    "marketing_goals": "Increase brand awareness by 50%",
    "current_challenges": "Limited budget"
  }'
```

### cURL Example (Protected - Get Bookings)
```bash
curl -X GET "https://your-domain.com/api/v1/booking?status=new" \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Accept: application/json"
```

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

### booking_forms Table
```sql
- id (bigint, primary key)
- site_id (foreign key to sites table)
- first_name (string)
- last_name (string)
- email (string)
- phone (string)
- company_name (string, nullable)
- website_url (string, nullable)
- industry (string, nullable)
- services_interested (json, nullable)
- investment_range (string, nullable)
- flight_timeline (string, nullable)
- current_marketing_efforts (text, nullable)
- marketing_goals (text, nullable)
- current_challenges (text, nullable)
- ip_address (string, nullable)
- status (enum: new, contacted, scheduled, completed, cancelled)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## Notes

1. **Rate Limiting**: Public submission is rate-limited to 10 requests per minute per IP
2. **Multi-step Form**: Frontend should handle the 3-step progression
3. **Services Array**: Services interested is stored as JSON array
4. **Email Notifications**: Consider setting up email notifications when new bookings are submitted
5. **Site Integration**: Multi-site architecture - ensure correct site_slug is provided

---

## Support

For API support or questions, please contact your system administrator.
