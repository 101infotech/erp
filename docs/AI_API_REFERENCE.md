# AI Feedback API Reference

## Base URL

```
https://your-erp.com/api/v1/ai/feedback
```

## Authentication

All endpoints require Bearer token authentication:

```http
Authorization: Bearer {access_token}
```

---

## Endpoints

### 1. Generate AI Questions

Generate intelligent feedback questions for an employee.

#### Request

```http
GET /questions?count=3&category=general&adaptive=true
Authorization: Bearer {token}
```

#### Query Parameters

| Parameter  | Type    | Default | Description                                                        |
| ---------- | ------- | ------- | ------------------------------------------------------------------ |
| `count`    | integer | 3       | Number of questions (1-10)                                         |
| `category` | string  | general | wellbeing, productivity, culture, engagement, development, general |
| `adaptive` | boolean | true    | Use previous responses to adapt questions                          |

#### Response (200 OK)

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "category": "wellbeing",
            "question": "How has your work-life balance been this week?",
            "sequence": 1
        },
        {
            "id": 2,
            "category": "productivity",
            "question": "What accomplishments are you most proud of this week?",
            "sequence": 2
        },
        {
            "id": 3,
            "category": "culture",
            "question": "How well did you connect with your team this week?",
            "sequence": 3
        }
    ],
    "count": 3
}
```

#### Response (500 Error)

```json
{
    "success": false,
    "error": "AI service temporarily unavailable"
}
```

#### Example cURL

```bash
curl -X GET "https://your-erp.com/api/v1/ai/feedback/questions?count=3" \
  -H "Authorization: Bearer your_token_here"
```

---

### 2. Analyze Feedback Sentiment

Analyze the sentiment of a submitted feedback.

#### Request

```http
POST /analyze-sentiment
Authorization: Bearer {token}
Content-Type: application/json

{
    "feedback_id": 123
}
```

#### Request Body

| Field         | Type    | Required | Description                   |
| ------------- | ------- | -------- | ----------------------------- |
| `feedback_id` | integer | Yes      | ID of the feedback to analyze |

#### Response (200 OK)

```json
{
    "success": true,
    "data": {
        "sentiment": "positive",
        "score": 0.78,
        "trends": {
            "feelings": 0.75,
            "progress": 0.8,
            "improvement": 0.78
        },
        "analysis": {
            "overall_classification": "positive",
            "trend_change": 0.05,
            "trend_direction": "improving"
        },
        "manager_attention_required": false,
        "alert_reason": null
    }
}
```

#### Response (403 Forbidden)

```json
{
    "success": false,
    "error": "Unauthorized"
}
```

#### Response (404 Not Found)

```json
{
    "success": false,
    "error": "Feedback not found"
}
```

#### Example cURL

```bash
curl -X POST "https://your-erp.com/api/v1/ai/feedback/analyze-sentiment" \
  -H "Authorization: Bearer your_token_here" \
  -H "Content-Type: application/json" \
  -d '{"feedback_id": 123}'
```

---

### 3. Get Weekly Prompt

Retrieve the weekly prompt for current or specified week.

#### Request

```http
GET /weekly-prompt?week_number=51&year=2025
Authorization: Bearer {token}
```

#### Query Parameters

| Parameter     | Type    | Default | Description        |
| ------------- | ------- | ------- | ------------------ |
| `week_number` | integer | current | Week number (1-52) |
| `year`        | integer | current | Year (e.g., 2025)  |

#### Response (200 OK)

```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Weekly Check-in",
        "prompt": "This week we're focusing on work-life balance. How have you maintained yours?",
        "category": "wellbeing",
        "follow_up_questions": [
            "What's one thing you did to recharge this week?",
            "Any areas where you felt overwhelmed?"
        ],
        "answered": false,
        "answered_at": null
    }
}
```

#### Example cURL

```bash
curl -X GET "https://your-erp.com/api/v1/ai/feedback/weekly-prompt?week_number=51" \
  -H "Authorization: Bearer your_token_here"
```

---

### 4. Submit Weekly Prompt Answer

Submit your answer to the weekly prompt.

#### Request

```http
POST /submit-answer
Authorization: Bearer {token}
Content-Type: application/json

{
    "prompt_id": 1,
    "answer": "This week was challenging but rewarding. I managed to maintain work-life balance by..."
}
```

#### Request Body

| Field       | Type    | Required | Min Length | Description               |
| ----------- | ------- | -------- | ---------- | ------------------------- |
| `prompt_id` | integer | Yes      | -          | ID of the prompt          |
| `answer`    | string  | Yes      | 10         | Your answer to the prompt |

#### Response (200 OK)

```json
{
    "success": true,
    "data": {
        "id": 1,
        "status": "submitted",
        "sentiment": "positive"
    }
}
```

#### Response (422 Unprocessable Entity)

```json
{
    "success": false,
    "error": "Validation failed",
    "errors": {
        "answer": ["The answer must be at least 10 characters"]
    }
}
```

#### Example cURL

```bash
curl -X POST "https://your-erp.com/api/v1/ai/feedback/submit-answer" \
  -H "Authorization: Bearer your_token_here" \
  -H "Content-Type: application/json" \
  -d '{
    "prompt_id": 1,
    "answer": "This week was productive and engaging. I completed..."
  }'
```

---

### 5. Get Sentiment Trends

Retrieve sentiment trends over a specified period.

#### Request

```http
GET /sentiment-trends?period=monthly&days=90
Authorization: Bearer {token}
```

#### Query Parameters

| Parameter | Type    | Default | Description                        |
| --------- | ------- | ------- | ---------------------------------- |
| `period`  | string  | monthly | weekly, monthly, quarterly, yearly |
| `days`    | integer | 30      | Number of days to analyze (7-365)  |

#### Response (200 OK)

```json
{
    "success": true,
    "data": {
        "2025-11": {
            "avg_sentiment": 0.72,
            "count": 4,
            "sentiments": {
                "positive": 3,
                "neutral": 1,
                "negative": 0
            }
        },
        "2025-12": {
            "avg_sentiment": 0.75,
            "count": 2,
            "sentiments": {
                "positive": 2,
                "neutral": 0,
                "negative": 0
            }
        }
    },
    "period": "monthly"
}
```

#### Response Data Format

**For Weekly Period**:

```json
{
    "2025-51": {
        /* Week 51 data */
    },
    "2025-52": {
        /* Week 52 data */
    }
}
```

**For Quarterly Period**:

```json
{
    "2025-Q3": {
        /* Q3 data */
    },
    "2025-Q4": {
        /* Q4 data */
    }
}
```

#### Example cURL

```bash
curl -X GET "https://your-erp.com/api/v1/ai/feedback/sentiment-trends?period=monthly&days=90" \
  -H "Authorization: Bearer your_token_here"
```

---

### 6. Get Performance Insights

Retrieve AI-generated performance insights.

#### Request

```http
GET /performance-insights?period_type=monthly
Authorization: Bearer {token}
```

#### Query Parameters

| Parameter     | Type   | Default | Description                |
| ------------- | ------ | ------- | -------------------------- |
| `period_type` | string | monthly | weekly, monthly, quarterly |

#### Response (200 OK)

```json
{
    "success": true,
    "data": {
        "employee_mood": "good",
        "engagement_score": 0.78,
        "sentiment_score": 0.75,
        "burnout_risk": false,
        "retention_risk": false,
        "retention_probability": 0.92,
        "positive_themes": ["motivated", "productive", "collaborative"],
        "improvement_areas": ["work-life balance", "time management"],
        "hr_recommendations": [
            "Consider additional flexible working options",
            "Schedule quarterly career development check-in"
        ],
        "manager_recommendations": [
            "Recognize recent achievements in team meeting",
            "Discuss future growth opportunities"
        ],
        "feedback_count": 12
    }
}
```

#### Response (404 Not Found)

```json
{
    "success": false,
    "error": "No insights available yet"
}
```

#### Employee Mood Values

-   `excellent`: Outstanding performance and satisfaction
-   `good`: Good engagement and positive sentiment
-   `neutral`: Average engagement and neutral sentiment
-   `concerning`: Declining performance or negative sentiment
-   `critical`: Critical issues requiring immediate attention

#### Example cURL

```bash
curl -X GET "https://your-erp.com/api/v1/ai/feedback/performance-insights?period_type=monthly" \
  -H "Authorization: Bearer your_token_here"
```

---

## Sentiment Score Reference

| Score       | Classification | Meaning                              |
| ----------- | -------------- | ------------------------------------ |
| 0.00 - 0.33 | Negative       | Very unhappy, concerns, issues       |
| 0.33 - 0.67 | Neutral        | Mixed feelings, neither good nor bad |
| 0.67 - 1.00 | Positive       | Happy, satisfied, engaged            |

---

## Error Codes

| Code | Description                             |
| ---- | --------------------------------------- |
| 200  | Success                                 |
| 400  | Bad Request - Invalid parameters        |
| 401  | Unauthorized - Missing or invalid token |
| 403  | Forbidden - Access denied               |
| 404  | Not Found - Resource not found          |
| 422  | Unprocessable Entity - Validation error |
| 503  | Service Unavailable - AI service down   |
| 500  | Internal Server Error                   |

---

## Rate Limiting

API requests are rate limited to prevent abuse:

-   **Default**: 60 requests per minute per user
-   **Burst**: 100 requests per minute
-   **Response Header**: `X-RateLimit-Remaining`

Example:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1640000000
```

---

## Best Practices

### 1. Error Handling

```javascript
try {
    const response = await fetch("/api/v1/ai/feedback/questions", {
        headers: { Authorization: `Bearer ${token}` },
    });

    if (!response.ok) {
        const error = await response.json();
        console.error("API Error:", error.error);
    }

    const data = await response.json();
} catch (e) {
    console.error("Network Error:", e);
}
```

### 2. Implementing Retries

```javascript
async function fetchWithRetry(url, options, retries = 3) {
    for (let i = 0; i < retries; i++) {
        try {
            const response = await fetch(url, options);
            if (response.ok) return response.json();
        } catch (e) {
            if (i === retries - 1) throw e;
            await new Promise((r) => setTimeout(r, 1000 * Math.pow(2, i)));
        }
    }
}
```

### 3. Caching Responses

```javascript
const cache = new Map();

async function getInsights(token) {
    const cacheKey = `insights_${Date.now().getDate()}`;

    if (cache.has(cacheKey)) {
        return cache.get(cacheKey);
    }

    const response = await fetch("/api/v1/ai/feedback/performance-insights", {
        headers: { Authorization: `Bearer ${token}` },
    });

    const data = await response.json();
    cache.set(cacheKey, data);
    return data;
}
```

---

## Webhooks (Planned)

Future versions will support webhooks for real-time alerts:

```json
{
    "event": "sentiment.alert",
    "data": {
        "employee_id": 123,
        "sentiment": "negative",
        "alert_reason": "Critical sentiment detected"
    }
}
```

---

**Last Updated**: December 18, 2025
**Version**: 1.0
**API Version**: v1
