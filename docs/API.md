# API Documentation - Saubhagya Group Admin Panel

## Base URL

```
http://your-domain.com/api/v1
```

## Authentication

Currently, all read endpoints are public. Form submission endpoints require CSRF protection when called from same domain, or can be called without CSRF from external domains.

## Common Parameters

### Pagination

All list endpoints support pagination:

-   `page` - Page number (default: 1)
-   Results per page: 15 (hardcoded, can be adjusted in controllers)

### Site Filtering

Most endpoints require the `site_slug` parameter to filter content by website:

-   `saubhagya-group`
-   `brand-bird-agency`
-   `saubhagya-ghar`
-   `saubhagya-estimate`

## Endpoints

### 1. Team Members

#### Get Team Members

```
GET /api/v1/team-members
```

**Query Parameters:**

-   `site_slug` (required) - Site identifier

**Response:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "position": "CEO",
            "bio": "Experienced leader...",
            "image_url": "http://domain.com/storage/team-members/image.jpg",
            "facebook_url": "https://facebook.com/johndoe",
            "twitter_url": "https://twitter.com/johndoe",
            "linkedin_url": "https://linkedin.com/in/johndoe",
            "order": 1
        }
    ],
    "first_page_url": "http://domain.com/api/v1/team-members?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://domain.com/api/v1/team-members?page=1",
    "next_page_url": null,
    "path": "http://domain.com/api/v1/team-members",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

#### Get Single Team Member

```
GET /api/v1/team-members/{id}
```

**Response:**

```json
{
    "id": 1,
    "name": "John Doe",
    "position": "CEO",
    "bio": "Experienced leader...",
    "image_url": "http://domain.com/storage/team-members/image.jpg",
    "facebook_url": "https://facebook.com/johndoe",
    "twitter_url": "https://twitter.com/johndoe",
    "linkedin_url": "https://linkedin.com/in/johndoe",
    "order": 1
}
```

---

### 2. News & Media

#### Get News Articles

```
GET /api/v1/news
```

**Query Parameters:**

-   `site_slug` (required) - Site identifier
-   `category` (optional) - Filter by category

**Response:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Company Launches New Service",
            "slug": "company-launches-new-service",
            "content": "Full article content...",
            "excerpt": "Short summary...",
            "featured_image_url": "http://domain.com/storage/news/image.jpg",
            "category": "press-release",
            "published_at": "2024-01-15T10:30:00.000000Z",
            "created_at": "2024-01-10T08:00:00.000000Z"
        }
    ],
    "per_page": 15,
    "total": 25
}
```

#### Get Single News Article

```
GET /api/v1/news/{slug}
```

**Response:**

```json
{
    "id": 1,
    "title": "Company Launches New Service",
    "slug": "company-launches-new-service",
    "content": "Full article content...",
    "excerpt": "Short summary...",
    "featured_image_url": "http://domain.com/storage/news/image.jpg",
    "category": "press-release",
    "published_at": "2024-01-15T10:30:00.000000Z",
    "created_at": "2024-01-10T08:00:00.000000Z"
}
```

---

### 3. Careers

#### Get Job Postings

```
GET /api/v1/careers
```

**Query Parameters:**

-   `site_slug` (required) - Site identifier
-   `job_type` (optional) - Filter by job type (full-time, part-time, contract)

**Response:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Senior Developer",
            "slug": "senior-developer",
            "description": "We are looking for...",
            "requirements": "5+ years experience...",
            "responsibilities": "Lead development team...",
            "job_type": "full-time",
            "location": "Kathmandu, Nepal",
            "application_deadline": "2024-12-31",
            "is_active": true
        }
    ],
    "per_page": 15,
    "total": 5
}
```

#### Get Single Job Posting

```
GET /api/v1/careers/{slug}
```

---

### 4. Case Studies

#### Get Case Studies

```
GET /api/v1/case-studies
```

**Query Parameters:**

-   `site_slug` (required) - Site identifier
-   `category` (optional) - Filter by category

**Response:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Brand Redesign for ABC Corp",
            "slug": "brand-redesign-abc-corp",
            "description": "Complete brand overhaul...",
            "client_name": "ABC Corporation",
            "project_date": "2024-01-15",
            "featured_image_url": "http://domain.com/storage/case-studies/featured.jpg",
            "gallery": [
                "http://domain.com/storage/case-studies/img1.jpg",
                "http://domain.com/storage/case-studies/img2.jpg"
            ],
            "tags": ["branding", "design", "corporate"],
            "category": "branding"
        }
    ],
    "per_page": 15,
    "total": 12
}
```

#### Get Single Case Study

```
GET /api/v1/case-studies/{slug}
```

---

### 5. Blogs

#### Get Blog Posts

```
GET /api/v1/blogs
```

**Query Parameters:**

-   `site_slug` (required) - Site identifier
-   `category` (optional) - Filter by category

**Response:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "10 Tips for Better Design",
            "slug": "10-tips-better-design",
            "content": "Full blog post content...",
            "excerpt": "Quick summary...",
            "featured_image_url": "http://domain.com/storage/blogs/image.jpg",
            "author": "Jane Smith",
            "category": "design",
            "tags": ["design", "tips", "guide"],
            "published_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "per_page": 15,
    "total": 45
}
```

#### Get Single Blog Post

```
GET /api/v1/blogs/{slug}
```

---

### 6. Contact Form Submission

#### Submit Contact Form

```
POST /api/v1/contact
```

**Request Body:**

```json
{
    "site_slug": "saubhagya-group",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+977-1234567890",
    "subject": "Inquiry about services",
    "message": "I would like to know more about..."
}
```

**Validation Rules:**

-   `site_slug` - required, must exist in sites table
-   `name` - required, max 255 characters
-   `email` - required, valid email format
-   `phone` - optional, max 20 characters
-   `subject` - optional, max 255 characters
-   `message` - required

**Success Response (201):**

```json
{
    "message": "Contact form submitted successfully",
    "data": {
        "id": 123,
        "name": "John Doe",
        "status": "new",
        "created_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

**Error Response (422):**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

---

### 7. Booking Form Submission

#### Submit Booking Form

```
POST /api/v1/booking
```

**Request Body:**

```json
{
    "site_slug": "saubhagya-estimate",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+977-1234567890",
    "company_name": "ABC Corp",
    "service_type": "Estimation Services",
    "preferred_date": "2024-02-15",
    "budget": "5000-10000",
    "project_details": "Need cost estimation for construction project..."
}
```

**Validation Rules:**

-   `site_slug` - required, must exist in sites table
-   `first_name` - required, max 100 characters
-   `last_name` - required, max 100 characters
-   `email` - required, valid email format
-   `phone` - required, max 20 characters
-   `company_name` - optional, max 255 characters
-   `service_type` - required, max 100 characters
-   `preferred_date` - optional, valid date
-   `budget` - optional, max 100 characters
-   `project_details` - optional

**Success Response (201):**

```json
{
    "message": "Booking form submitted successfully",
    "data": {
        "id": 456,
        "first_name": "John",
        "last_name": "Doe",
        "status": "new",
        "created_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

## Error Responses

### 404 Not Found

```json
{
    "message": "Resource not found"
}
```

### 422 Validation Error

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

### 500 Server Error

```json
{
    "message": "Server error occurred"
}
```

---

## Rate Limiting

Currently not implemented. Recommended to add rate limiting in production:

-   60 requests per minute per IP for read endpoints
-   10 requests per minute per IP for form submissions

---

## CORS Configuration

Configure CORS in `config/cors.php` to allow your external websites to access the API.

Example configuration:

```php
'paths' => ['api/*'],
'allowed_origins' => ['https://saubhagyagroup.com', 'https://brandbirdagency.com'],
'allowed_methods' => ['GET', 'POST'],
```

---

## Example Integration (JavaScript)

### Fetch Team Members

```javascript
async function getTeamMembers() {
    const response = await fetch(
        "http://admin.saubhagyagroup.com/api/v1/team-members?site_slug=saubhagya-group"
    );
    const data = await response.json();

    data.data.forEach((member) => {
        console.log(`${member.name} - ${member.position}`);
    });
}
```

### Submit Contact Form

```javascript
async function submitContact(formData) {
    const response = await fetch(
        "http://admin.saubhagyagroup.com/api/v1/contact",
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                site_slug: "saubhagya-group",
                name: formData.name,
                email: formData.email,
                message: formData.message,
            }),
        }
    );

    const result = await response.json();

    if (response.ok) {
        console.log("Form submitted successfully:", result);
    } else {
        console.error("Validation errors:", result.errors);
    }
}
```

### Fetch with Pagination

```javascript
async function getAllBlogs() {
    let page = 1;
    let allBlogs = [];

    while (true) {
        const response = await fetch(
            `http://admin.saubhagyagroup.com/api/v1/blogs?site_slug=saubhagya-group&page=${page}`
        );
        const data = await response.json();

        allBlogs = [...allBlogs, ...data.data];

        if (!data.next_page_url) break;
        page++;
    }

    return allBlogs;
}
```

---

## Testing the API

### Using cURL

**Get Team Members:**

```bash
curl -X GET "http://localhost:8000/api/v1/team-members?site_slug=saubhagya-group"
```

**Submit Contact Form:**

```bash
curl -X POST "http://localhost:8000/api/v1/contact" \
  -H "Content-Type: application/json" \
  -d '{
    "site_slug": "saubhagya-group",
    "name": "Test User",
    "email": "test@example.com",
    "message": "This is a test message"
  }'
```

### Using Postman

1. Import collection with base URL
2. Add endpoints listed above
3. Set environment variables for `base_url` and `site_slug`
4. Test each endpoint with sample data

---

## Future API Enhancements

1. **Authentication**

    - Add Laravel Sanctum for token-based auth
    - Protect endpoints with API keys

2. **Versioning**

    - Add v2 endpoints when breaking changes needed
    - Maintain backward compatibility

3. **Advanced Filtering**

    - Search functionality across content
    - Date range filters
    - Sorting options

4. **Response Optimization**

    - Field selection (sparse fieldsets)
    - Response compression
    - ETags for caching

5. **Webhooks**
    - Notify external sites of content updates
    - Real-time sync capabilities
