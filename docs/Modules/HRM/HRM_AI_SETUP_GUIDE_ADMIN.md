# HRM AI Integration - Administrator Setup Guide

## Overview

This guide helps administrators set up and manage the AI features in the HRM system.

---

## Step-by-Step Setup

### Step 1: Choose an AI Provider

You have three options:

#### Option A: OpenAI (Recommended)

-   **Cost**: $0.01-0.10 per 1K tokens
-   **Model**: GPT-4, GPT-3.5-turbo
-   **Quality**: Highest
-   **Setup Time**: 5 minutes

**Get API Key**:

1. Visit https://platform.openai.com/account/api-keys
2. Click "Create new secret key"
3. Copy the key (starts with `sk-`)

#### Option B: HuggingFace (Free)

-   **Cost**: Free with rate limits
-   **Model**: Open source models
-   **Quality**: Good
-   **Setup Time**: 5 minutes

**Get API Token**:

1. Create account at https://huggingface.co
2. Go to https://huggingface.co/settings/tokens
3. Create new token

#### Option C: Anthropic (Claude)

-   **Cost**: $0.008-0.024 per 1K tokens
-   **Model**: Claude 2
-   **Quality**: Very High
-   **Setup Time**: 5 minutes

**Get API Key**:

1. Visit https://console.anthropic.com/account/keys
2. Create new API key

### Step 2: Configure Environment

Edit `.env` file:

```bash
# Enable AI
AI_ENABLED=true

# Choose provider: openai, huggingface, anthropic
AI_PROVIDER=openai

# For OpenAI:
OPENAI_API_KEY=sk-your-key-here
OPENAI_MODEL=gpt-4
OPENAI_TEMPERATURE=0.7

# OR for HuggingFace:
HUGGINGFACE_API_KEY=hf_your-token-here
AI_PROVIDER=huggingface

# OR for Anthropic:
ANTHROPIC_API_KEY=your-key-here
AI_PROVIDER=anthropic

# Enable specific features
AI_FEATURE_FEEDBACK_QUESTIONS=true
AI_FEATURE_SENTIMENT_ANALYSIS=true
AI_FEATURE_PERFORMANCE_INSIGHTS=false

# Feedback settings
AI_FEEDBACK_QUESTIONS_COUNT=3
AI_FEEDBACK_INCLUDE_SENTIMENT=true
AI_FEEDBACK_ADAPTIVE_QUESTIONS=true

# Performance
AI_TIMEOUT=30
AI_CACHE_RESPONSES=true
```

### Step 3: Run Database Migrations

```bash
# Run migrations
php artisan migrate

# Verify
php artisan migrate:status
```

Expected output:

```
Batch  Migration Name
------  ----------------
1      create_ai_feedback_analysis_table
1      create_ai_performance_insights_table
1      create_ai_weekly_prompts_table
```

### Step 4: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 5: Verify Installation

```bash
# Test AI connection
php artisan tinker

# Inside tinker, run:
> App\Services\AI\AiServiceFactory::make()->testConnection()
> exit
```

Expected output:

```
=> true
```

### Step 6: Test in Portal

1. Log in as employee
2. Go to Feedback module
3. Click "Submit Feedback"
4. Should see AI-generated questions

---

## Dashboard Overview

### For HR/Admins

**Location**: `/admin/hrm/feedback`

**Key Metrics**:

-   Total feedbacks received this week
-   Department sentiment distribution
-   Employees requiring attention
-   Trend indicators

**Actions**:

-   View all feedback
-   Analyze trends
-   Export reports
-   Configure alerts

### Employee Dashboard

**Location**: `/employee/feedback`

**Features**:

-   AI-generated questions
-   Previous feedback history
-   Sentiment trend chart
-   Manager notes (if available)

---

## Feature Configuration

### AI Questions

#### Customize Question Categories

Edit `config/services.php`:

```php
'feedback' => [
    'questions_per_week' => 3,
    'include_sentiment' => true,
    'adaptive_questions' => true,
    'categories' => [
        'wellbeing' => 'Work-life balance and mental health',
        'productivity' => 'Achievements and productivity',
        'culture' => 'Company culture alignment',
        'engagement' => 'Team engagement',
        'development' => 'Professional development',
    ],
],
```

#### Adjust Question Frequency

```env
AI_FEEDBACK_QUESTIONS_COUNT=3  # Default: 3
```

### Sentiment Analysis

#### Sensitivity Settings

```env
# 0.0 to 1.0 (lower = more sensitive)
OPENAI_TEMPERATURE=0.7

# More conservative sentiment analysis
OPENAI_TEMPERATURE=0.3

# More variable responses
OPENAI_TEMPERATURE=0.9
```

#### Alert Thresholds

Create/Update in admin panel:

```
Alert if sentiment < 0.33 (Negative)
Alert if sentiment declining > 0.15/week (Downtrend)
Alert if feedback missing > 2 weeks (No engagement)
```

---

## Monitoring & Analytics

### Access Analytics Dashboard

**URL**: `/admin/hrm/analytics/ai`

**Key Metrics**:

-   Average sentiment by department
-   Engagement rates
-   Burnout risk scores
-   Retention predictions

### Generate Reports

**Weekly Report**:

1. Go to Reports → Feedback Reports
2. Select "This Week"
3. Choose output format (PDF, Excel)
4. Click "Generate"

**Monthly Report**:

1. Select "This Month"
2. Optionally filter by department
3. Click "Generate"

**Custom Report**:

1. Select custom date range
2. Choose metrics
3. Add filters
4. Schedule automated delivery

### Export Data

**Excel Export**:

```bash
# Export all feedback with AI analysis
php artisan export:ai-feedback --format=excel --period=monthly
```

**CSV Export**:

```bash
php artisan export:ai-feedback --format=csv --department=sales
```

---

## Manager Alerts

### Alert Types

#### Critical Alerts (Red)

-   Very negative sentiment (< 0.20)
-   Employee mentions burnout
-   Repeated negative feedback
-   Resignation risk detected

#### Warning Alerts (Orange)

-   Negative sentiment (0.20-0.50)
-   Declining trend
-   Missing feedback
-   Engagement declining

#### Info Alerts (Blue)

-   Positive achievements
-   Milestone celebrations
-   Training needs identified
-   Development opportunities

### Configure Alerts

**Email Notifications**:

```bash
# Send alerts daily at 9 AM
MAIL_ALERT_SCHEDULE=daily@9am

# Enable manager alerts
MANAGER_ALERTS_ENABLED=true

# Alert recipients
ALERT_RECIPIENTS=hr@company.com,managers@company.com
```

**In-App Notifications**:

-   Alerts appear in dashboard
-   Bell icon shows pending alerts
-   Click to view details

---

## User Management

### Employee Permissions

**View own feedback**:

-   Automatic for all employees

**View team feedback** (Managers):

1. Go to Settings → Permissions
2. Search for manager
3. Grant "View Team Feedback"
4. Grant "View Team Sentiment"

**Full HR Access** (HR/Admin):

```bash
# Create new HR role
php artisan role:create hr --permissions=view-all-feedback,export-reports,manage-alerts
```

### Restrict Access

**Disable AI for specific department**:

```env
# In .env or admin panel
DISABLED_AI_DEPARTMENTS=finance,legal
```

**Limit data retention**:

```env
# Keep AI data for 90 days only
AI_DATA_RETENTION_DAYS=90
```

---

## Troubleshooting

### AI Not Generating Questions

**Check 1**: Verify AI is enabled

```bash
grep "AI_ENABLED" .env
# Should return: AI_ENABLED=true
```

**Check 2**: Verify API key

```bash
php artisan tinker
> echo config('services.ai.openai.api_key');
# Should show your API key
```

**Check 3**: Check logs

```bash
tail -n 50 storage/logs/laravel.log | grep -i ai
```

### Sentiment Analysis Showing NULL

**Cause**: Feedback text too short or AI timeout

**Solution**:

```bash
# Increase timeout
AI_TIMEOUT=60

# Clear cache
php artisan cache:clear

# Re-analyze
php artisan ai:reanalyze-feedback --since="1 day ago"
```

### High API Usage

**Monitor usage**:

```bash
# Check API call statistics
php artisan ai:stats
```

**Reduce usage**:

```env
# Increase cache TTL (default 24 hours)
AI_CACHE_TTL=604800  # 7 days

# Disable adaptive questions
AI_FEEDBACK_ADAPTIVE_QUESTIONS=false

# Reduce question count
AI_FEEDBACK_QUESTIONS_COUNT=1
```

### Database Errors

**Connection error**:

```bash
# Test database connection
php artisan db:show

# Run migrations
php artisan migrate
```

**Performance issue**:

```bash
# Add database indexes
php artisan db:optimize

# Check table size
php artisan db:table ai_feedback_analysis --size
```

---

## Maintenance Tasks

### Daily Tasks

```bash
# Check system health
php artisan health:check

# Process queued jobs
php artisan queue:work database --max-jobs=1000

# Review critical alerts
# Go to: /admin/hrm/alerts/critical
```

### Weekly Tasks

```bash
# Generate weekly insights
php artisan ai:generate-insights

# Send alerts to managers
php artisan notification:send-alerts --period=weekly

# Backup AI data
php artisan backup:database --tables=ai_feedback_analysis
```

### Monthly Tasks

```bash
# Archive old data
php artisan ai:archive-data --older-than="90 days"

# Generate monthly report
php artisan report:generate --type=monthly --output=pdf

# Review AI provider costs
# Check API usage dashboard in provider console

# Update question templates
# Review and refresh AI question prompts in /admin/ai/templates
```

---

## Cost Optimization

### Estimate Monthly Costs

**OpenAI GPT-4**:

```
- 100 employees
- 3 questions per employee per week
- 3 feedbacks per employee per month
- Average tokens: 500 per request

Monthly cost = 100 × 4 × 3 × 500 / 1000 × $0.03 = ~$18/month
```

### Reduce Costs

1. **Use GPT-3.5-turbo instead of GPT-4**

    - 10x cheaper
    - Still good quality for feedback

2. **Increase cache TTL**

    - Reuse cached responses
    - Reduce API calls

3. **Batch process insights**

    - Generate insights once daily instead of on-demand

4. **Use HuggingFace**
    - Free with rate limits
    - Open-source models

---

## Security & Privacy

### Data Protection

**Encryption**:

-   All API keys encrypted in `.env`
-   Database fields encrypted at rest
-   HTTPS for all API calls

**Access Control**:

```bash
# Restrict admin access
php artisan shield:role:create admin
php artisan shield:permission:create view_ai_data
php artisan shield:permission:create export_ai_data
```

**Audit Logging**:

```bash
# View who accessed what
php artisan audit:log --resource=ai_feedback_analysis

# Export audit trail
php artisan audit:export --format=csv --output=audit.csv
```

### GDPR Compliance

**Data Retention**:

```env
# Delete personal data after 90 days
GDPR_RETENTION_PERIOD_DAYS=90
```

**Right to be Forgotten**:

```bash
# Delete employee data
php artisan employee:delete-data {employee_id}

# Export employee data
php artisan employee:export-data {employee_id}
```

---

## Advanced Configuration

### Custom Prompts

Edit system prompts for question generation:

```php
// config/ai.php
'question_prompts' => [
    'wellbeing' => 'Generate a question about work-life balance considering...',
    'productivity' => 'Ask about accomplishments and productivity...',
],
```

### Scheduling

#### Generate prompts every Monday 8 AM:

```php
// app/Console/Kernel.php
$schedule->command('ai:generate-prompts')
    ->weeklyOn(Carbon::MONDAY, '08:00')
    ->timezone('Asia/Kathmandu');
```

#### Analyze sentiment every evening:

```php
$schedule->command('ai:analyze-pending-feedback')
    ->daily()
    ->at('18:00')
    ->timezone('Asia/Kathmandu');
```

#### Generate insights on Fridays:

```php
$schedule->command('ai:generate-weekly-insights')
    ->weeklyOn(Carbon::FRIDAY, '17:00')
    ->timezone('Asia/Kathmandu');
```

---

## Frequently Asked Questions

**Q: How much will AI cost?**
A: ~$18-50/month depending on provider. See cost optimization above.

**Q: Can I disable AI for certain departments?**
A: Yes, set `DISABLED_AI_DEPARTMENTS` in `.env`.

**Q: How do I change the AI provider?**
A: Update `AI_PROVIDER` in `.env` and restart the application.

**Q: Is employee data private?**
A: Yes, employees can only see their own data. Managers see anonymized team data.

**Q: Can I export AI analysis?**
A: Yes, go to Reports → Feedback Reports → Export.

**Q: What if AI generates inappropriate responses?**
A: Review in admin panel, mark as inappropriate, adjust templates.

---

## Support & Resources

-   **Documentation**: `/docs/HRM_AI_IMPLEMENTATION_PLAN.md`
-   **API Reference**: `/docs/AI_API_REFERENCE.md`
-   **Developer Guide**: `/docs/HRM_AI_IMPLEMENTATION_DEVELOPER_GUIDE.md`
-   **Quick Start**: `/docs/AI_QUICK_START.md`

---

**Last Updated**: December 18, 2025
**Version**: 1.0
**Support Email**: support@company.com
