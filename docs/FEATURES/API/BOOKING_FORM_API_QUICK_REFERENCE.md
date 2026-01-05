# 🎯 Brand Bird Agency Booking Form API - Quick Reference

## API Endpoint
```
POST /api/v1/booking
```

## 📋 Required Fields
```json
{
  "site_slug": "brandbirdagency",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "+1-234-567-8900"
}
```

## 📝 All Available Fields

### Step 1: Your Details (Required)
```json
{
  "first_name": "John",          // Required
  "last_name": "Doe",            // Required
  "email": "john@example.com",   // Required
  "phone": "+1-234-567-8900"     // Required
}
```

### Step 2: Business & Services (All Optional)
```json
{
  "company_name": "Tech Innovations Inc.",
  "website_url": "https://example.com",
  "industry": "Technology & SaaS",
  "services_interested": [
    "Brand Strategy & Identity Development",
    "Digital Marketing Campaign Creation"
  ],
  "investment_range": "$10,000 - $25,000",
  "flight_timeline": "3-6 months",
  "current_marketing_efforts": "Currently running campaigns..."
}
```

### Step 3: Goals & Vision (All Optional)
```json
{
  "marketing_goals": "Increase brand awareness by 50%",
  "current_challenges": "Limited budget and resources"
}
```

## 🏭 Industry Options
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

## 🛠️ Service Options
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

## 🚀 Quick Test

### Using cURL
```bash
curl -X POST "http://localhost:8000/api/v1/booking" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "site_slug": "brandbirdagency",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1-234-567-8900",
    "company_name": "Tech Inc.",
    "industry": "Technology & SaaS",
    "services_interested": ["Brand Strategy & Identity Development"],
    "marketing_goals": "Increase brand awareness"
  }'
```

### Using JavaScript (Fetch)
```javascript
fetch('http://localhost:8000/api/v1/booking', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    site_slug: "brandbirdagency",
    first_name: "John",
    last_name: "Doe",
    email: "john@example.com",
    phone: "+1-234-567-8900",
    company_name: "Tech Inc.",
    industry: "Technology & SaaS",
    services_interested: ["Brand Strategy & Identity Development"],
    marketing_goals: "Increase brand awareness"
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

## 📊 API Endpoints Summary

### Public
- `POST /api/v1/booking` - Submit form (Rate: 10/min)

### Protected (Auth Required)
- `GET /api/v1/booking` - List all bookings
- `GET /api/v1/booking/{id}` - Get single booking
- `PATCH /api/v1/booking/{id}/status` - Update status
- `DELETE /api/v1/booking/{id}` - Delete booking

## ✅ Success Response
```json
{
  "success": true,
  "message": "Thank you for your interest! Our team will review your information and reach out within 24 hours to schedule your complimentary Brand Flight Consultation.",
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "company_name": "Tech Inc.",
    "status": "new",
    "created_at": "2025-12-21T11:30:00.000000Z"
  }
}
```

## ❌ Error Response
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## 🔄 Status Values
- `new` - Just submitted
- `contacted` - Team reached out
- `scheduled` - Consultation scheduled
- `completed` - Consultation done
- `cancelled` - Booking cancelled

## 📚 Full Documentation
- **Complete API Docs**: [API_BOOKING_FORM.md](API_BOOKING_FORM.md)
- **Postman Collection**: [POSTMAN_BOOKING_FORM.json](POSTMAN_BOOKING_FORM.json)

## 🔒 Security
- Rate limited: 10 requests/minute
- Protected endpoints use Laravel Sanctum
- IP address tracking
- Input validation on all fields

---

**Status**: ✅ Production Ready  
**Last Updated**: December 21, 2025
