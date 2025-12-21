#!/bin/bash

# Saubhagya Group Meeting API - Quick Test Script
# This script tests the public meeting submission endpoint

echo "🧪 Testing Saubhagya Group Meeting API..."
echo "----------------------------------------"
echo ""

# Configuration
BASE_URL="http://localhost:8000"
ENDPOINT="/api/v1/schedule-meeting"

# Generate a future date (tomorrow)
TOMORROW=$(date -v+1d +%Y-%m-%d 2>/dev/null || date -d "+1 day" +%Y-%m-%d)

echo "📝 Submitting a test meeting request..."
echo "Date: $TOMORROW"
echo ""

# Make the API request
response=$(curl -s -w "\n%{http_code}" -X POST "${BASE_URL}${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"full_name\": \"Test User\",
    \"email\": \"test@saubhagya.com\",
    \"phone\": \"+977-9821812699\",
    \"company\": \"Saubhagya Test Corp\",
    \"preferred_date\": \"${TOMORROW}\",
    \"preferred_time\": \"9:00 AM - 10:00 AM\",
    \"meeting_type\": \"Partnership Discussion\",
    \"message\": \"This is a test meeting request from the API test script.\"
  }")

# Extract HTTP status code (last line)
http_code=$(echo "$response" | tail -n1)

# Extract response body (all lines except last)
body=$(echo "$response" | sed '$d')

echo "📊 Response:"
echo "HTTP Status: $http_code"
echo ""
echo "Response Body:"
echo "$body" | python3 -m json.tool 2>/dev/null || echo "$body"
echo ""

# Check if request was successful
if [ "$http_code" -eq 201 ]; then
  echo "✅ SUCCESS! Meeting request submitted successfully."
  echo ""
  echo "Next steps:"
  echo "1. Check your database: SELECT * FROM schedule_meetings ORDER BY id DESC LIMIT 1;"
  echo "2. Test the protected endpoints with an auth token"
  echo "3. Import the Postman collection from docs/POSTMAN_SCHEDULE_MEETING.json"
else
  echo "❌ FAILED! HTTP Status: $http_code"
  echo ""
  echo "Troubleshooting:"
  echo "1. Make sure Laravel is running: php artisan serve"
  echo "2. Check if the database is connected"
  echo "3. Verify the API route exists: php artisan route:list --path=schedule-meeting"
fi

echo ""
echo "----------------------------------------"
echo "📚 Documentation: docs/API_SCHEDULE_MEETING.md"
echo "🧪 Postman Collection: docs/POSTMAN_SCHEDULE_MEETING.json"
