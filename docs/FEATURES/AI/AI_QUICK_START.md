# AI Integration - Quick Start Guide

## ⚡ Get Started in 5 Minutes

### Step 1: Configure API Key

Add to your `.env` file:

```bash
# For OpenAI (Recommended)
OPENAI_API_KEY=sk-your-api-key-here
OPENAI_MODEL=gpt-4

# Or for HuggingFace (Free option)
HUGGINGFACE_API_KEY=hf_your-token-here
AI_PROVIDER=huggingface

# Or for BrandBird (Cloudflare Workers)
AI_PROVIDER=brandbird
BRAND_BIRD_API_KEY=your-secret-api-key
BRAND_BIRD_BASE_URL=https://ai.brand-bird.workers.dev/
```

Get API keys:

-   **OpenAI**: https://platform.openai.com/api-keys
-   **HuggingFace**: https://huggingface.co/settings/tokens

### Step 2: Run Migrations

```bash
php artisan migrate
```

This creates 2 new tables:

-   `ai_feedback_prompts` - Stores AI-generated questions
-   `ai_feedback_sentiment_analysis` - Stores sentiment analysis

### Step 3: Enable AI

Update `.env`:

```bash
AI_ENABLED=true
AI_FEATURE_FEEDBACK_QUESTIONS=true
AI_FEATURE_SENTIMENT_ANALYSIS=true
```

### Step 4: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 5: Test It

Navigate to: **Employee Portal → Weekly Feedback**

You should see:

-   💡 AI-generated personalized questions
-   Improved feedback form experience
-   Real-time sentiment analysis after submission

---

## 🎯 What You Get

### Employee Experience

```
┌─────────────────────────────────────┐
│ 📝 Submit Weekly Feedback           │
├─────────────────────────────────────┤
│                                     │
│ 💡 AI Question 1 (Feelings)         │
│ [Personalized based on role/dept]   │
│                                     │
│ 💡 AI Question 2 (Progress)         │
│ [Context-aware & adaptive]          │
│                                     │
│ 💡 AI Question 3 (Improvements)     │
│ [Growth-focused framing]            │
│                                     │
│ [Submit Feedback Button]            │
│                                     │
└─────────────────────────────────────┘
        ↓
    ✨ Instant AI Analysis ✨
        ↓
┌─────────────────────────────────────┐
│ Dashboard Shows:                    │
│ - Overall Sentiment: 75% 😊         │
│ - Trend: 📈 Improving               │
│ - Alert Status: ✅ All Good         │
└─────────────────────────────────────┘
```

### Manager Insights

-   🚨 **Alerts** for negative sentiment
-   📈 **Trends** showing employee engagement
-   📊 **Analytics** for team planning

---

## 📊 Sentiment Scores

| Score   | Label         | Emoji | Meaning                   |
| ------- | ------------- | ----- | ------------------------- |
| 0-30%   | Very Negative | 😞    | Critical attention needed |
| 30-45%  | Negative      | 😟    | Follow-up recommended     |
| 45-55%  | Neutral       | 😐    | Normal baseline           |
| 55-70%  | Positive      | 😊    | Healthy engagement        |
| 70-100% | Very Positive | 😄    | Excellent morale          |

---

## 🔧 Configuration Options

```env
# Main Settings
AI_ENABLED=true                          # Enable/disable AI
AI_PROVIDER=openai                       # Choose: openai, huggingface
AI_TIMEOUT=30                            # Timeout in seconds
AI_CACHE_RESPONSES=true                  # Cache results (save $$$)

# Feature Flags
AI_FEATURE_FEEDBACK_QUESTIONS=true       # ✅ Live now
AI_FEATURE_SENTIMENT_ANALYSIS=true       # ✅ Live now
AI_FEATURE_PERFORMANCE_INSIGHTS=false    # Coming soon
AI_FEATURE_HR_CHATBOT=false              # Coming soon

# Feedback-Specific
AI_FEEDBACK_QUESTIONS_COUNT=3            # Questions per submission
AI_FEEDBACK_INCLUDE_SENTIMENT=true       # Analyze after submit
AI_FEEDBACK_ADAPTIVE_QUESTIONS=true      # Learn from history
```

---

## 💰 Cost Breakdown

### OpenAI (GPT-4)

-   Per feedback: ~$0.10-0.15
-   100 employees/week: ~$10-15
-   With caching: 80% reduction

### HuggingFace

-   Per feedback: $0.00 (free tier available)
-   100 employees/week: $0
-   Best budget option

### Tips to Reduce Costs

1. ✅ Enable caching (already on by default)
2. Use GPT-3.5-turbo instead of GPT-4
3. Reduce max tokens
4. Use HuggingFace for testing

---

## 🐛 Troubleshooting

### Questions not showing?

```bash
# Check AI is enabled
php artisan tinker
> echo env('AI_ENABLED')  // Should be true

# Check config is loaded
> echo env('OPENAI_API_KEY')  // Should show key

# Clear cache and retry
php artisan config:clear
```

### Sentiment not analyzing?

```bash
# Check feature is enabled
> echo env('AI_FEATURE_SENTIMENT_ANALYSIS')  // true

# Check table exists
> DB::table('ai_feedback_sentiment_analysis')->count()

# Check logs
tail -f storage/logs/laravel.log
```

### API errors?

```bash
# Check logs for details
tail -f storage/logs/laravel.log | grep -i "error\|api"

# Test connection
php artisan tinker
> $service = \App\Services\AI\AiServiceFactory::make()
> $service->isAvailable()  // true/false
> $service->generateText("Hello")  // Test call
```

---

## 📚 Documentation

-   📖 **Full Plan**: `docs/AI_INTEGRATION_PLAN.md`
-   🛠️ **Setup Guide**: `docs/AI_IMPLEMENTATION_GUIDE.md`
-   ✅ **Completion Report**: `docs/AI_PHASE1_COMPLETION.md`

---

## 🚀 Next Steps

### This Week

-   [ ] Get API key
-   [ ] Run migration
-   [ ] Enable AI feature
-   [ ] Test with internal feedback

### This Month

-   [ ] Get team feedback
-   [ ] Refine question quality
-   [ ] Monitor API costs
-   [ ] Plan Phase 2

### This Quarter

-   [ ] Deploy to all users
-   [ ] Collect metrics
-   [ ] Implement performance analytics
-   [ ] Launch HR chatbot

---

## ✨ Features Included

✅ **AI Question Generation** - Personalized questions  
✅ **Sentiment Analysis** - Real-time analysis  
✅ **Trend Tracking** - Week-to-week comparison  
✅ **Manager Alerts** - Flag negative sentiment  
✅ **Multi-Provider** - OpenAI, HuggingFace, more  
✅ **Caching** - Reduce API costs by 80%  
✅ **Fallbacks** - Works even if AI unavailable  
✅ **Error Handling** - Graceful degradation

---

## 🎓 How It Works

```
1. Employee submits feedback form
   ↓
2. AI generates personalized questions
   ↓
3. Employee answers questions
   ↓
4. AI analyzes sentiment automatically
   ↓
5. Results stored in database
   ↓
6. Dashboard shows insights
   ↓
7. Managers get alerts if needed
```

---

## 💡 Tips for Success

### For Employees

-   Be honest in your feedback
-   Your answers are confidential
-   AI learns from your previous feedback

### For Managers

-   Review sentiment alerts promptly
-   Follow up on concerning trends
-   Track team engagement over time

### For IT/Admins

-   Monitor API costs regularly
-   Keep AI keys secure
-   Review logs for errors
-   Update models periodically

---

## 🔐 Security

✅ API keys in `.env` (not in code)  
✅ No sensitive data logged  
✅ Database access restricted  
✅ Error messages sanitized

---

**Questions?** Check the troubleshooting section above or review the detailed implementation guide!

**Ready?** You're all set! 🎉
