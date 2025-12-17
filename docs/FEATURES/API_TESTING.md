# API Quick Test Guide

## Prerequisites

1. Ensure database is migrated: `php artisan migrate`
2. Ensure you have at least one site in the database
3. Server must be running: `php artisan serve`

## Test 1: Submit a Lead

```bash
curl -X POST http://localhost:8000/api/v1/leads \
  -H "Content-Type: application/json" \
  -d '{
    "brand": "brand-bird-agency",
    "first_name": "Test",
    "last_name": "User",
    "email": "test@example.com",
    "phone": "+1234567890",
    "message": "Testing the API endpoint",
    "source": "api_test",
    "meta": {
      "test": true,
      "environment": "development"
    }
  }'
```

**Expected Response:**

```json
{
    "status": "success",
    "message": "Lead submitted successfully",
    "data": {
        "id": 1,
        "reference": "LEAD-000001"
    }
}
```

## Test 2: Get Services

```bash
curl http://localhost:8000/api/v1/services?brand=brand-bird-agency
```

**Expected Response:**

```json
{
    "status": "success",
    "message": "Services retrieved successfully",
    "data": []
}
```

## Test 3: Get Team Members

```bash
curl http://localhost:8000/api/v1/team?brand=brand-bird-agency
```

**Expected Response:**

```json
{
    "status": "success",
    "message": "Team members retrieved successfully",
    "data": []
}
```

## Test 4: Get Case Studies

```bash
curl http://localhost:8000/api/v1/case-studies?brand=brand-bird-agency
```

**Expected Response:**

```json
{
    "status": "success",
    "message": "Case studies retrieved successfully",
    "data": []
}
```

## Test 5: Rate Limiting Test

Run this command 11 times quickly to test rate limiting:

```bash
for i in {1..11}; do
  echo "Request $i:"
  curl -X POST http://localhost:8000/api/v1/leads \
    -H "Content-Type: application/json" \
    -d "{
      \"brand\": \"brand-bird-agency\",
      \"first_name\": \"Test$i\",
      \"email\": \"test$i@example.com\"
    }"
  echo -e "\n"
done
```

**Expected:** First 10 requests succeed, 11th request returns 429 (Too Many Requests)

## Test 6: Validation Test (Missing Required Field)

```bash
curl -X POST http://localhost:8000/api/v1/leads \
  -H "Content-Type: application/json" \
  -d '{
    "brand": "brand-bird-agency",
    "last_name": "User"
  }'
```

**Expected Response (422):**

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "first_name": ["The first name field is required."],
        "email": ["The email field is required."]
    }
}
```

## Test 7: Invalid Brand Test

```bash
curl -X POST http://localhost:8000/api/v1/leads \
  -H "Content-Type: application/json" \
  -d '{
    "brand": "invalid-brand",
    "first_name": "Test",
    "email": "test@example.com"
  }'
```

**Expected Response (422):**

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "brand": ["The selected brand is invalid."]
    }
}
```

## Browser-Based Testing

### Using Browser Console

Open your browser console (F12) and paste:

```javascript
// Test Lead Submission
fetch("http://localhost:8000/api/v1/leads", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        brand: "brand-bird-agency",
        first_name: "Browser",
        last_name: "Test",
        email: "browser@example.com",
        phone: "+1234567890",
        message: "Testing from browser console",
        meta: {
            browser: navigator.userAgent,
            timestamp: new Date().toISOString(),
        },
    }),
})
    .then((res) => res.json())
    .then((data) => console.log("Lead Response:", data))
    .catch((err) => console.error("Error:", err));

// Test Get Team
fetch("http://localhost:8000/api/v1/team?brand=brand-bird-agency")
    .then((res) => res.json())
    .then((data) => console.log("Team Response:", data))
    .catch((err) => console.error("Error:", err));

// Test Get Case Studies
fetch("http://localhost:8000/api/v1/case-studies?brand=brand-bird-agency")
    .then((res) => res.json())
    .then((data) => console.log("Case Studies Response:", data))
    .catch((err) => console.error("Error:", err));
```

## Verify Database Records

After submitting leads, check the database:

```bash
php artisan tinker
```

Then run:

```php
App\Models\Lead::all();
App\Models\Lead::latest()->first();
```

## Clean Up Test Data

To remove test leads:

```bash
php artisan tinker
```

```php
App\Models\Lead::where('source', 'api_test')->delete();
```

---

## Troubleshooting

### Issue: "Brand not found"

**Solution:** Ensure you have a site with slug "brand-bird-agency" in your sites table

```bash
php artisan tinker
```

```php
App\Models\Site::create([
    'name' => 'Brand Bird Agency',
    'slug' => 'brand-bird-agency',
    'url' => 'https://brandbird.com',
    'is_active' => true
]);
```

### Issue: CORS Error in Browser

**Solution:** Add to `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['*'], // For testing only! Use specific domains in production
```

### Issue: 404 on API Routes

**Solution:** Make sure `bootstrap/app.php` has:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php', // This line
    ...
)
```

### Issue: Rate Limit Not Working

**Solution:** Check Laravel cache is configured:

```bash
php artisan cache:clear
php artisan config:cache
```

---

**All tests passing?** ✅ Your API is production ready!
