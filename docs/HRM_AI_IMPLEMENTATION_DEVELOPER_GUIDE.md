# HRM AI Integration - Implementation Guide

## Table of Contents

1. [Quick Setup](#quick-setup)
2. [Architecture Overview](#architecture-overview)
3. [Database Schema](#database-schema)
4. [Service Layer](#service-layer)
5. [Controller Integration](#controller-integration)
6. [Frontend Integration](#frontend-integration)
7. [API Usage](#api-usage)
8. [Customization](#customization)
9. [Troubleshooting](#troubleshooting)

---

## Quick Setup

### Prerequisites

-   PHP 8.1+
-   Laravel 10+
-   MySQL 8.0+ or SQLite
-   One of: OpenAI, HuggingFace, or Anthropic API account

### Installation Steps

1. **Clone/Update Repository**

```bash
git pull origin main
```

2. **Install Dependencies**

```bash
composer install
npm install
```

3. **Configure Environment**

```bash
cp .env.example .env
```

4. **Set API Key**
   Edit `.env`:

```env
AI_ENABLED=true
OPENAI_API_KEY=sk-your-api-key-here
AI_PROVIDER=openai
```

5. **Run Migrations**

```bash
php artisan migrate

# Rollback if needed
php artisan migrate:rollback
php artisan migrate
```

6. **Clear Cache**

```bash
php artisan config:clear
php artisan cache:clear
```

7. **Test Installation**

```bash
php artisan tinker
# Inside Tinker:
> App\Services\AI\AiServiceFactory::make()->testConnection()
```

---

## Architecture Overview

### System Flow

```
┌─────────────────┐
│    Employee     │
│  (Web/Mobile)   │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│   FeedbackController    │
│                         │
│ • Create feedback       │
│ • Submit feedback       │
│ • View dashboard        │
└────────┬────────────────┘
         │
         ▼
┌──────────────────────────────┐
│   AiFeedbackService          │
│                              │
│ • Generate Questions         │
│ • Analyze Sentiment          │
│ • Generate Insights          │
└────────┬─────────────────────┘
         │
    ┌────┴────┬──────────┐
    ▼         ▼          ▼
┌────────┐ ┌────────┐ ┌──────────┐
│ OpenAI │ │HuggingF│ │Anthropic │
│  API   │ │ace API │ │   API    │
└────────┘ └────────┘ └──────────┘
    │         │          │
    └────┬────┴──────────┘
         ▼
┌──────────────────────────────┐
│    Database Tables           │
│                              │
│ • ai_feedback_analysis       │
│ • ai_performance_insights    │
│ • ai_weekly_prompts          │
└──────────────────────────────┘
```

### Component Structure

```
app/
├── Services/AI/
│   ├── AiServiceInterface.php
│   ├── AiServiceFactory.php
│   ├── AiFeedbackService.php
│   ├── OpenAiService.php
│   ├── HuggingFaceService.php
│   └── AnthropicService.php
├── Models/
│   ├── AiFeedbackPrompt.php
│   ├── AiFeedbackSentimentAnalysis.php
│   ├── AiWeeklyPrompt.php
│   ├── AiPerformanceInsight.php
│   └── EmployeeFeedback.php
├── Http/
│   ├── Controllers/
│   │   ├── Employee/FeedbackController.php
│   │   ├── Admin/FeedbackController.php
│   │   └── Api/AiFeedbackController.php
│   └── Resources/
│       └── FeedbackResource.php
└── Jobs/
    ├── AnalyzeFeedbackSentiment.php
    ├── GenerateWeeklyPrompts.php
    └── GeneratePerformanceInsights.php

database/
└── migrations/
    ├── 2025_12_18_000001_create_ai_feedback_analysis_table.php
    ├── 2025_12_18_000002_create_ai_performance_insights_table.php
    └── 2025_12_18_000003_create_ai_weekly_prompts_table.php

routes/
├── api.php (AI endpoints)
└── web.php (Feedback views)

resources/views/
└── employee/feedback/
    ├── dashboard.blade.php
    ├── create.blade.php
    ├── show.blade.php
    └── history.blade.php
```

---

## Database Schema

### ai_feedback_analysis Table

```sql
CREATE TABLE ai_feedback_analysis (
    id BIGINT PRIMARY KEY,
    feedback_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,

    -- Sentiment
    sentiment ENUM('positive', 'neutral', 'negative'),
    sentiment_score DECIMAL(3,2),
    sentiment_details JSON,

    -- AI Content
    ai_generated_questions TEXT,
    question_context JSON,
    tags JSON,

    -- Metadata
    ai_model VARCHAR(255),
    ai_provider VARCHAR(255),
    analysis_metadata JSON,

    -- Processing
    tokens_used INT,
    processing_time DECIMAL(8,3),
    status ENUM('processed', 'failed', 'pending'),
    error_message TEXT,

    -- Recommendations
    recommendations JSON,
    requires_manager_attention BOOLEAN,
    manager_notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (feedback_id) REFERENCES employee_feedback(id),
    FOREIGN KEY (employee_id) REFERENCES hrm_employees(id),
    INDEX(feedback_id),
    INDEX(employee_id),
    INDEX(sentiment),
    INDEX(requires_manager_attention),
    INDEX(created_at)
);
```

### ai_performance_insights Table

```sql
CREATE TABLE ai_performance_insights (
    id BIGINT PRIMARY KEY,
    employee_id BIGINT NOT NULL,

    -- Period
    analysis_date DATE,
    period_type VARCHAR(50),

    -- Metrics
    avg_sentiment_score DECIMAL(3,2),
    positive_count INT,
    neutral_count INT,
    negative_count INT,

    -- Engagement
    total_feedback_count INT,
    engagement_score DECIMAL(3,2),
    response_timeliness_score DECIMAL(3,2),

    -- Themes
    positive_themes JSON,
    negative_themes JSON,
    improvement_areas JSON,

    -- Health
    employee_mood ENUM('excellent', 'good', 'neutral', 'concerning', 'critical'),
    burnout_risk BOOLEAN,
    retention_risk BOOLEAN,
    retention_probability DECIMAL(3,2),

    -- Recommendations
    hr_recommendations JSON,
    manager_recommendations JSON,
    employee_recommendations JSON,

    -- Comparison
    department_sentiment_comparison DECIMAL(3,2),
    company_sentiment_comparison DECIMAL(3,2),
    percentile_rank INT,

    -- Analysis
    ai_model VARCHAR(255),
    ai_provider VARCHAR(255),
    analysis_parameters JSON,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (employee_id) REFERENCES hrm_employees(id),
    INDEX(employee_id),
    INDEX(analysis_date),
    INDEX(period_type),
    INDEX(employee_mood)
);
```

### ai_weekly_prompts Table

```sql
CREATE TABLE ai_weekly_prompts (
    id BIGINT PRIMARY KEY,
    employee_id BIGINT NOT NULL,

    -- Prompt
    title VARCHAR(255),
    prompt TEXT,
    category VARCHAR(50),
    follow_up_questions JSON,

    -- Metadata
    prompt_date DATE,
    week_number INT,
    year INT,

    -- Response
    answered BOOLEAN,
    answer TEXT,
    answered_at TIMESTAMP NULL,
    skipped BOOLEAN,

    -- Generation
    ai_model VARCHAR(255),
    ai_provider VARCHAR(255),
    generation_context JSON,
    is_adaptive BOOLEAN,

    -- Context
    employee_context JSON,
    company_context JSON,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (employee_id) REFERENCES hrm_employees(id),
    INDEX(employee_id),
    INDEX(prompt_date),
    INDEX(year, week_number),
    INDEX(category),
    INDEX(answered)
);
```

---

## Service Layer

### AiFeedbackService Class

#### Key Methods

```php
namespace App\Services\AI;

class AiFeedbackService
{
    /**
     * Generate AI-powered feedback questions
     */
    public function generateFeedbackQuestions(
        int $userId,
        int $count = 3,
        ?string $category = null,
        bool $adaptive = true
    ): array

    /**
     * Analyze sentiment of submitted feedback
     */
    public function analyzeFeedbackSentiment(
        EmployeeFeedback $feedback
    ): ?AiFeedbackSentimentAnalysis

    /**
     * Generate weekly prompt for employee
     */
    public function generateWeeklyPrompt(
        HrmEmployee $employee,
        int $weekNumber,
        int $year
    ): AiWeeklyPrompt

    /**
     * Analyze sentiment of text
     */
    public function analyzeSentiment(string $text): float

    /**
     * Generate performance insights
     */
    public function generatePerformanceInsights(
        HrmEmployee $employee,
        string $periodType = 'monthly'
    ): AiPerformanceInsight
}
```

#### Usage Example

```php
use App\Services\AI\AiFeedbackService;

$aiService = new AiFeedbackService();

// Generate questions
$questions = $aiService->generateFeedbackQuestions(
    userId: auth()->id(),
    count: 3,
    category: 'wellbeing',
    adaptive: true
);

// Analyze feedback
$feedback = EmployeeFeedback::find(1);
$analysis = $aiService->analyzeFeedbackSentiment($feedback);

// Generate insights
$insights = $aiService->generatePerformanceInsights(
    employee: auth()->user()->employee,
    periodType: 'monthly'
);
```

### AI Service Factory

```php
use App\Services\AI\AiServiceFactory;

// Automatically creates appropriate service based on config
$service = AiServiceFactory::make();

// Available methods
$service->generateText($prompt);      // Generate text response
$service->analyzeSentiment($text);    // Analyze text sentiment (0-1)
$service->getModel();                 // Get model name
$service->getProvider();              // Get provider name
```

---

## Controller Integration

### Employee Feedback Controller

```php
namespace App\Http\Controllers\Employee;

class FeedbackController extends Controller
{
    public function dashboard()
    {
        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());
        $latestSentiment = AiFeedbackSentimentAnalysis::getLatestForUser(auth()->id());

        return view('employee.feedback.dashboard', [
            'weeklyFeedback' => $weeklyFeedback,
            'latestSentiment' => $latestSentiment,
        ]);
    }

    public function create()
    {
        $weeklyFeedback = EmployeeFeedback::getWeeklyFeedback(auth()->id());

        // Generate AI questions
        $aiService = new AiFeedbackService();
        $aiQuestions = $aiService->generateFeedbackQuestions(auth()->id(), 3);

        return view('employee.feedback.create', [
            'weeklyFeedback' => $weeklyFeedback,
            'aiQuestions' => $aiQuestions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'feelings' => 'required|string|min:10',
            'work_progress' => 'required|string|min:10',
            'self_improvements' => 'required|string|min:10',
        ]);

        $feedback = EmployeeFeedback::updateOrCreate(
            ['user_id' => auth()->id()],
            [...$validated, 'is_submitted' => true, 'submitted_at' => now()]
        );

        // Analyze sentiment asynchronously
        AnalyzeFeedbackSentiment::dispatch($feedback);

        return redirect()->route('employee.feedback.dashboard')
            ->with('success', 'Feedback submitted successfully!');
    }
}
```

---

## Frontend Integration

### Blade Template Example

```blade
@if(config('services.ai.enabled'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <div>
                <h3 class="font-semibold text-emerald-900">AI-Powered Guidance</h3>
                <p class="text-sm text-emerald-700 mt-1">
                    Personalized questions have been generated to help guide your feedback this week.
                </p>
            </div>
        </div>
    </div>

    @if(!empty($aiQuestions))
        <div class="space-y-3 mb-6">
            <label class="block text-sm font-medium text-gray-700">AI-Generated Questions</label>
            @foreach($aiQuestions as $question)
                <div class="flex items-start p-3 bg-gray-50 rounded border border-gray-200">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold mr-3 flex-shrink-0">
                        {{ $loop->iteration }}
                    </span>
                    <p class="text-sm text-gray-700">{{ $question['question'] }}</p>
                </div>
            @endforeach
        </div>
    @endif
@endif
```

### Vue.js Component Example

```vue
<template>
    <div v-if="aiEnabled" class="ai-feedback-section">
        <div class="ai-banner">
            <span class="ai-badge">AI-Powered</span>
            <p>Personalized questions to guide your feedback</p>
        </div>

        <div v-if="loading" class="loading">
            <p>Generating intelligent questions...</p>
        </div>

        <div v-else class="questions-list">
            <div
                v-for="question in aiQuestions"
                :key="question.id"
                class="question-item"
            >
                <span class="question-number">{{ question.sequence }}</span>
                <p class="question-text">{{ question.question }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            aiEnabled: true,
            loading: false,
            aiQuestions: [],
        };
    },
    mounted() {
        this.fetchAiQuestions();
    },
    methods: {
        async fetchAiQuestions() {
            this.loading = true;
            try {
                const response = await fetch("/api/v1/ai/feedback/questions", {
                    headers: {
                        Authorization: `Bearer ${this.getToken()}`,
                    },
                });

                const data = await response.json();
                this.aiQuestions = data.data;
            } catch (error) {
                console.error("Error fetching questions:", error);
            } finally {
                this.loading = false;
            }
        },
        getToken() {
            // Get Sanctum token from meta tag or storage
            return (
                document.querySelector('meta[name="api-token"]')?.content ||
                localStorage.getItem("api_token")
            );
        },
    },
};
</script>
```

---

## API Usage

### Using cURL

```bash
# Generate questions
curl -X GET "http://localhost:8000/api/v1/ai/feedback/questions?count=3" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Analyze sentiment
curl -X POST "http://localhost:8000/api/v1/ai/feedback/analyze-sentiment" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"feedback_id": 1}'

# Get performance insights
curl -X GET "http://localhost:8000/api/v1/ai/feedback/performance-insights" \
  -H "Authorization: Bearer {token}"
```

### Using JavaScript Fetch

```javascript
// Generate questions
async function getAiQuestions(token) {
    const response = await fetch("/api/v1/ai/feedback/questions", {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
        },
    });

    return await response.json();
}

// Analyze sentiment
async function analyzeSentiment(token, feedbackId) {
    const response = await fetch("/api/v1/ai/feedback/analyze-sentiment", {
        method: "POST",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ feedback_id: feedbackId }),
    });

    return await response.json();
}

// Usage
const token = document.querySelector('meta[name="api-token"]').content;
const questions = await getAiQuestions(token);
console.log(questions);
```

### Using Axios

```javascript
import axios from "axios";

const aiClient = axios.create({
    baseURL: "/api/v1/ai/feedback",
    headers: {
        Authorization: `Bearer ${getToken()}`,
        Accept: "application/json",
    },
});

// Get questions
const { data: questions } = await aiClient.get("/questions", {
    params: { count: 3, category: "wellbeing" },
});

// Analyze sentiment
const { data: analysis } = await aiClient.post("/analyze-sentiment", {
    feedback_id: 1,
});

// Get insights
const { data: insights } = await aiClient.get("/performance-insights", {
    params: { period_type: "monthly" },
});
```

---

## Customization

### Custom AI Providers

To add a new AI provider:

1. **Create Service Class**

```php
namespace App\Services\AI;

class CustomAiService implements AiServiceInterface
{
    public function generateText(string $prompt): string
    {
        // Implement API call
    }

    public function analyzeSentiment(string $text): float
    {
        // Implement sentiment analysis
    }

    public function getModel(): string
    {
        return 'custom-model';
    }

    public function getProvider(): string
    {
        return 'custom';
    }
}
```

2. **Register in Factory**

```php
// app/Services/AI/AiServiceFactory.php
public static function make(): AiServiceInterface
{
    $provider = config('services.ai.provider');

    return match($provider) {
        'openai' => new OpenAiService(),
        'huggingface' => new HuggingFaceService(),
        'anthropic' => new AnthropicService(),
        'custom' => new CustomAiService(),
        default => throw new Exception('Unknown AI provider')
    };
}
```

3. **Add Config**

```php
// config/services.php
'ai' => [
    'custom' => [
        'api_key' => env('CUSTOM_API_KEY'),
        'base_url' => env('CUSTOM_API_URL'),
    ],
]
```

### Custom Question Generation

Override in AiFeedbackService:

```php
private function buildQuestionPrompt(
    string $category,
    array $context,
    int $sequence
): string
{
    // Customize prompts based on category
    $basePrompts = [
        'wellbeing' => 'Ask about work-life balance and mental health...',
        'productivity' => 'Ask about achievements and productivity...',
        'culture' => 'Ask about company culture alignment...',
    ];

    $basePrompt = $basePrompts[$category] ?? $basePrompts['general'];

    return $this->buildContextualPrompt($basePrompt, $context, $sequence);
}
```

---

## Troubleshooting

### Common Issues

#### 1. AI Service Not Available

```
Error: "AI service not available"
```

**Solution**:

```bash
# Check configuration
php artisan config:show services.ai

# Verify API key
echo $OPENAI_API_KEY

# Test connection
php artisan tinker
> App\Services\AI\AiServiceFactory::make()->testConnection()
```

#### 2. Rate Limiting

```
Error: 429 Too Many Requests
```

**Solution**:

```env
# Enable caching
AI_CACHE_RESPONSES=true
AI_CACHE_TTL=86400

# Implement request throttling
MIDDLEWARE="App\Http\Middleware\ThrottleRequests:60,1"
```

#### 3. Database Migrations Failed

```bash
# Check migration status
php artisan migrate:status

# Roll back and retry
php artisan migrate:rollback
php artisan migrate

# Reset all
php artisan migrate:reset
php artisan migrate
```

#### 4. Sentiment Analysis Returns NULL

```
# Check feedback text length (minimum 10 characters)
# Verify AI provider is responding
# Check logs: storage/logs/laravel.log
```

### Debug Commands

```bash
# Check AI configuration
php artisan config:show services.ai

# Test AI connection
php artisan tinker
> App\Services\AI\AiServiceFactory::make()->testConnection()

# View recent errors
tail -f storage/logs/laravel.log | grep -i ai

# Check migrations
php artisan migrate:status

# Clear all caches
php artisan cache:clear && php artisan config:clear
```

### Performance Tuning

```bash
# Add database indexes
php artisan migrate

# Enable query caching
CACHE_STORE=redis

# Use queue for batch processing
QUEUE_CONNECTION=database

# Monitor API usage
php artisan queue:work database --tries=3
```

---

**Last Updated**: December 18, 2025
**Version**: 1.0
**Status**: Production Ready
