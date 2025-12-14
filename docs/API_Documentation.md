# API Documentation - Saubhagya Group Admin Panel

## Base URL

```
Production: https://yourdomain.com/api/v1
Development: http://localhost:8000/api/v1
```

## Authentication

All endpoints listed below are **public** and do not require authentication.

## Rate Limiting

-   **Lead Submission**: 10 requests per minute per IP
-   **All Other Endpoints**: Standard Laravel throttle limits apply

---

## Endpoints

### 1. Submit Lead (POST)

**Endpoint:** `POST /api/v1/leads`

**Description:** Submit a new lead for any brand in the Saubhagya Group.

**Rate Limit:** 10 requests/minute

**Request Body:**

```json
{
    "brand": "brand-bird-agency",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "message": "I'm interested in your services",
    "source": "website_contact_form",
    "meta": {
        "utm_source": "google",
        "utm_campaign": "spring_2024",
        "page_url": "https://brandbird.com/contact"
    }
}
```

**Required Fields:**

-   `brand` (string) - Brand slug (must exist in sites table)
-   `first_name` (string, max 255)
-   `email` (valid email, max 255)

**Optional Fields:**

-   `last_name` (string, max 255)
-   `phone` (string, max 20)
-   `message` (text)
-   `source` (string, max 100) - defaults to "api"
-   `meta` (JSON object) - flexible metadata

**Success Response (201):**

```json
{
    "status": "success",
    "message": "Lead submitted successfully",
    "data": {
        "id": 42,
        "reference": "LEAD-000042"
    }
}
```

**Error Responses:**

_Validation Error (422):_

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "brand": ["The selected brand is invalid."]
    }
}
```

_Server Error (500):_

```json
{
    "status": "error",
    "message": "Failed to submit lead"
}
```

---

### 2. Get Services (GET)

**Endpoint:** `GET /api/v1/services?brand=brand-bird-agency`

**Description:** Retrieve all published services (case studies) for a specific brand.

**Query Parameters:**

-   `brand` (required) - Brand slug

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Services retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Brand Strategy & Identity Design",
            "slug": "brand-strategy-identity",
            "description": "Complete brand overhaul for tech startup",
            "featured_image": "https://example.com/storage/images/service1.jpg",
            "category": "Branding",
            "created_at": "2024-01-15T10:30:00.000000Z"
        }
    ]
}
```

**Error Response (400):**

```json
{
    "status": "error",
    "message": "Brand parameter is required"
}
```

**Error Response (404):**

```json
{
    "status": "error",
    "message": "Brand not found"
}
```

---

### 3. Get Case Studies (GET)

**Endpoint:** `GET /api/v1/case-studies?brand=brand-bird-agency`

**Description:** Retrieve all published case studies for a specific brand.

**Query Parameters:**

-   `brand` (required) - Brand slug

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Case studies retrieved successfully",
    "data": [
        {
            "id": 5,
            "title": "Digital Transformation for Fortune 500",
            "slug": "digital-transformation-fortune-500",
            "description": "How we helped a major corporation modernize their digital presence",
            "featured_image": "https://example.com/storage/images/case5.jpg",
            "category": "Digital Strategy",
            "client_name": "Acme Corp",
            "created_at": "2024-02-20T14:45:00.000000Z"
        }
    ]
}
```

---

### 4. Get Single Case Study (GET)

**Endpoint:** `GET /api/v1/case-studies/{slug}`

**Description:** Retrieve a single published case study by slug.

**Path Parameters:**

-   `slug` (required) - Case study slug

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Case study retrieved successfully",
    "data": {
        "id": 5,
        "site_id": 1,
        "title": "Digital Transformation for Fortune 500",
        "slug": "digital-transformation-fortune-500",
        "description": "How we helped a major corporation modernize their digital presence",
        "content": "<p>Full HTML content here...</p>",
        "featured_image": "https://example.com/storage/images/case5.jpg",
        "category": "Digital Strategy",
        "client_name": "Acme Corp",
        "project_url": "https://acmecorp.com",
        "completion_date": "2024-01-31",
        "status": "published",
        "created_at": "2024-02-20T14:45:00.000000Z",
        "updated_at": "2024-02-20T14:45:00.000000Z"
    }
}
```

**Error Response (404):**

```json
{
    "status": "error",
    "message": "Case study not found"
}
```

---

### 5. Get Team Members (GET)

**Endpoint:** `GET /api/v1/team?brand=brand-bird-agency`

**Description:** Retrieve all active team members for a specific brand, ordered by display order.

**Query Parameters:**

-   `brand` (required) - Brand slug

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Team members retrieved successfully",
    "data": [
        {
            "id": 3,
            "name": "Jane Smith",
            "position": "Creative Director",
            "bio": "Award-winning designer with 10+ years experience",
            "photo": "https://example.com/storage/team/jane.jpg",
            "email": "jane@brandbird.com",
            "linkedin_url": "https://linkedin.com/in/janesmith",
            "order": 1,
            "created_at": "2024-01-10T09:00:00.000000Z"
        }
    ]
}
```

---

## Error Handling

All API endpoints follow a consistent error response format:

```json
{
    "status": "error",
    "message": "Human-readable error message"
}
```

### HTTP Status Codes

-   `200` - Success (GET requests)
-   `201` - Created (POST requests)
-   `400` - Bad Request (missing required parameters)
-   `404` - Not Found (resource doesn't exist)
-   `422` - Validation Error (invalid data)
-   `429` - Too Many Requests (rate limit exceeded)
-   `500` - Server Error

---

## CORS Configuration

CORS should be configured to allow requests from your frontend domains:

```php
// config/cors.php
'paths' => ['api/*'],
'allowed_origins' => [
    'https://brandbird.com',
    'https://saubhagyagroup.com',
    // Add other domains
],
```

---

## Security Features

### Implemented

-   ✅ Rate limiting on lead submissions (10/min)
-   ✅ IP address tracking for leads
-   ✅ User agent tracking
-   ✅ Input validation on all POST requests
-   ✅ SQL injection protection (Eloquent ORM)

### Recommended

-   🔲 Honeypot fields for spam prevention
-   🔲 reCAPTCHA integration
-   🔲 Email verification for leads
-   🔲 CSRF protection for form submissions
-   🔲 API key authentication for sensitive endpoints

---

## Testing Examples

### cURL Examples

**Submit Lead:**

```bash
curl -X POST https://yourdomain.com/api/v1/leads \
  -H "Content-Type: application/json" \
  -d '{
    "brand": "brand-bird-agency",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "message": "Interested in your services"
  }'
```

**Get Services:**

```bash
curl -X GET "https://yourdomain.com/api/v1/services?brand=brand-bird-agency"
```

**Get Team:**

```bash
curl -X GET "https://yourdomain.com/api/v1/team?brand=brand-bird-agency"
```

### JavaScript/Fetch Examples

**Submit Lead:**

```javascript
fetch("https://yourdomain.com/api/v1/leads", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        brand: "brand-bird-agency",
        first_name: "John",
        last_name: "Doe",
        email: "john@example.com",
        phone: "+1234567890",
        message: "Interested in your services",
        meta: {
            utm_source: "google",
            page_url: window.location.href,
        },
    }),
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

**Get Case Studies:**

```javascript
fetch("https://yourdomain.com/api/v1/case-studies?brand=brand-bird-agency")
    .then((response) => response.json())
    .then((data) => console.log(data.data));
```

---

## Database Schema

### Leads Table

```sql
CREATE TABLE leads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  site_id BIGINT UNSIGNED NOT NULL,
  source VARCHAR(255),
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255),
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  message TEXT,
  meta JSON,
  status ENUM('new', 'contacted', 'qualified', 'converted', 'rejected') DEFAULT 'new',
  ip_address VARCHAR(255),
  user_agent VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  INDEX idx_site_id (site_id),
  INDEX idx_status (status),
  INDEX idx_created_at (created_at),
  FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
);
```

---

## Changelog

### v1.0.0 (2024-11-30)

-   Initial API release
-   POST /api/v1/leads endpoint with rate limiting
-   GET /api/v1/services endpoint
-   GET /api/v1/case-studies endpoint (list & single)
-   GET /api/v1/team endpoint
-   Consistent JSON response format
-   Brand-based filtering for all GET endpoints
