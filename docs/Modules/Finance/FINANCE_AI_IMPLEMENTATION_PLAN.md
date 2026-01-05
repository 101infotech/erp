# Finance Module - AI Implementation Plan

**Date**: January 5, 2026  
**Version**: 1.0  
**Based on**: Existing AI patterns from HRM module (AiPerformanceInsight model)

---

## 🎯 Executive Summary

Transform the finance module with intelligent automation, fraud detection, predictive analytics, and smart recommendations using AI/ML capabilities similar to the existing HRM AI features.

---

## 📊 Current State Analysis

### Existing AI Infrastructure
- ✅ `AiPerformanceInsight` model exists (for HRM)
- ✅ OpenAI/Anthropic integration patterns available
- ✅ AI service layer architecture established
- ✅ Database structure for AI insights

### Finance Module Current Capabilities
- ✅ 18 controllers with full CRUD operations
- ✅ Comprehensive transaction tracking
- ✅ Double-entry accounting
- ✅ Nepali BS calendar integration
- ✅ Multiple company support
- ✅ Category management
- ❌ **No AI features implemented**

---

## 🚀 AI Features Roadmap

### Phase 1: AI Transaction Categorization (Foundation)
**Timeline**: 1 week  
**Priority**: HIGH  
**Complexity**: Medium

### Phase 2: Fraud Detection & Anomaly Detection
**Timeline**: 2 weeks  
**Priority**: HIGH  
**Complexity**: High

### Phase 3: Financial Forecasting & Predictions
**Timeline**: 2 weeks  
**Priority**: MEDIUM  
**Complexity**: High

### Phase 4: Smart Recommendations & Insights
**Timeline**: 1 week  
**Priority**: MEDIUM  
**Complexity**: Medium

### Phase 5: AI-Powered Reports & Dashboard
**Timeline**: 1 week  
**Priority**: LOW  
**Complexity**: Medium

---

## 📅 Phase 1: AI Transaction Categorization

### Objective
Automatically categorize transactions based on description, amount patterns, vendor/customer, and historical data.

### Features

1. **Smart Category Suggestion**
   - Analyze transaction description using NLP
   - Match patterns from historical categorization
   - Suggest top 3 categories with confidence scores
   - Learn from user corrections

2. **Auto-Categorization**
   - Auto-assign category for high-confidence matches (>90%)
   - Flag for manual review for medium confidence (70-90%)
   - Require manual categorization for low confidence (<70%)

3. **Vendor/Customer Pattern Learning**
   - Remember vendor→category mappings
   - Track customer purchase patterns
   - Identify recurring transaction types

### Database Changes

```sql
-- AI category predictions table
CREATE TABLE ai_finance_category_predictions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    transaction_id BIGINT,
    suggested_category_id BIGINT,
    confidence_score DECIMAL(5,4), -- 0.0000 to 1.0000
    reasoning TEXT, -- AI explanation
    was_accepted BOOLEAN DEFAULT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES finance_transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (suggested_category_id) REFERENCES finance_categories(id) ON DELETE CASCADE
);

-- Category learning patterns
CREATE TABLE ai_finance_category_patterns (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT,
    pattern_type ENUM('vendor', 'customer', 'description', 'amount_range'),
    pattern_value TEXT,
    category_id BIGINT,
    occurrence_count INT DEFAULT 1,
    success_rate DECIMAL(5,4), -- How often this pattern correctly predicted
    last_used_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES finance_companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES finance_categories(id) ON DELETE CASCADE
);
```

### Service Implementation

```php
<?php

namespace App\Services\Finance\AI;

use App\Models\FinanceTransaction;
use App\Models\FinanceCategory;
use App\Models\AiFinanceCategoryPrediction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinanceAiCategorizationService
{
    protected $apiKey;
    protected $model = 'gpt-4';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Suggest category for a transaction
     */
    public function suggestCategory(FinanceTransaction $transaction): array
    {
        // Check pattern cache first
        $cachedPattern = $this->checkPatternCache($transaction);
        if ($cachedPattern && $cachedPattern['confidence'] > 0.95) {
            return $cachedPattern;
        }

        // Get historical data for AI context
        $historicalContext = $this->getHistoricalContext($transaction->company_id);
        
        // Call AI API
        $aiResponse = $this->callAiForCategorization($transaction, $historicalContext);
        
        // Save prediction
        $this->savePrediction($transaction->id, $aiResponse);
        
        return $aiResponse;
    }

    /**
     * Auto-categorize transaction if confidence is high
     */
    public function autoCategorize(FinanceTransaction $transaction): bool
    {
        $suggestion = $this->suggestCategory($transaction);
        
        if ($suggestion['confidence'] >= 0.90) {
            $transaction->update([
                'category_id' => $suggestion['category_id'],
                'ai_categorized' => true,
            ]);
            
            $this->markPredictionAccepted($transaction->id, true);
            return true;
        }
        
        return false;
    }

    /**
     * Learn from user's manual categorization
     */
    public function learnFromCorrection(FinanceTransaction $transaction, int $correctCategoryId): void
    {
        // Update pattern database
        $this->updatePatternLearning($transaction, $correctCategoryId);
        
        // Mark prediction as corrected
        AiFinanceCategoryPrediction::where('transaction_id', $transaction->id)
            ->update(['was_accepted' => false]);
    }

    /**
     * Check pattern cache for quick predictions
     */
    protected function checkPatternCache(FinanceTransaction $transaction): ?array
    {
        // Check vendor pattern
        if ($transaction->reference_type === 'purchase') {
            $pattern = AiFinanceCategoryPattern::where('company_id', $transaction->company_id)
                ->where('pattern_type', 'vendor')
                ->where('pattern_value', $transaction->description)
                ->where('success_rate', '>', 0.9)
                ->first();
                
            if ($pattern) {
                return [
                    'category_id' => $pattern->category_id,
                    'confidence' => $pattern->success_rate,
                    'reasoning' => "Vendor pattern match: {$pattern->occurrence_count} similar transactions",
                    'source' => 'cache'
                ];
            }
        }
        
        return null;
    }

    /**
     * Call OpenAI API for categorization
     */
    protected function callAiForCategorization(FinanceTransaction $transaction, array $context): array
    {
        $categories = FinanceCategory::where('company_id', $transaction->company_id)
            ->where('is_active', true)
            ->get(['id', 'name', 'type', 'description']);

        $prompt = $this->buildCategorizationPrompt($transaction, $categories, $context);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a financial categorization expert for a Nepal-based ERP system.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.3,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                return $this->parseAiResponse($response->json(), $categories);
            }
        } catch (\Exception $e) {
            Log::error("AI Categorization failed: " . $e->getMessage());
        }

        // Fallback to rule-based categorization
        return $this->fallbackCategorization($transaction, $categories);
    }

    /**
     * Build prompt for AI categorization
     */
    protected function buildCategorizationPrompt(
        FinanceTransaction $transaction, 
        $categories, 
        array $context
    ): string {
        $categoriesList = $categories->map(fn($c) => 
            "- ID {$c->id}: {$c->name} ({$c->type}) - {$c->description}"
        )->implode("\n");

        return <<<PROMPT
Categorize this financial transaction:

Transaction Details:
- Type: {$transaction->transaction_type}
- Amount: NPR {$transaction->amount}
- Description: {$transaction->description}
- Payment Method: {$transaction->payment_method}
- Date: {$transaction->transaction_date_bs}

Available Categories:
{$categoriesList}

Historical Context:
{$context['summary']}

Provide your response in JSON format:
{
    "category_id": <selected_category_id>,
    "confidence": <0.0-1.0>,
    "reasoning": "<brief explanation>",
    "alternative_categories": [<array of alternative category IDs>]
}
PROMPT;
    }

    /**
     * Get historical categorization context
     */
    protected function getHistoricalContext(int $companyId): array
    {
        $recentTransactions = FinanceTransaction::where('company_id', $companyId)
            ->whereNotNull('category_id')
            ->with('category')
            ->latest()
            ->limit(50)
            ->get();

        $summary = "Recent categorization patterns:\n";
        $patterns = $recentTransactions->groupBy('category_id');
        
        foreach ($patterns as $categoryId => $txns) {
            $category = $txns->first()->category;
            $avgAmount = $txns->avg('amount');
            $summary .= "- {$category->name}: {$txns->count()} transactions, avg NPR " . number_format($avgAmount, 2) . "\n";
        }

        return ['summary' => $summary];
    }

    /**
     * Parse AI response
     */
    protected function parseAiResponse(array $response, $categories): array
    {
        $content = $response['choices'][0]['message']['content'] ?? '';
        
        // Extract JSON from response
        if (preg_match('/\{[^}]+\}/', $content, $matches)) {
            $data = json_decode($matches[0], true);
            
            return [
                'category_id' => $data['category_id'] ?? null,
                'confidence' => $data['confidence'] ?? 0.5,
                'reasoning' => $data['reasoning'] ?? '',
                'alternatives' => $data['alternative_categories'] ?? [],
                'source' => 'ai'
            ];
        }

        return $this->fallbackCategorization(null, $categories);
    }

    /**
     * Fallback rule-based categorization
     */
    protected function fallbackCategorization($transaction, $categories): array
    {
        // Simple keyword matching
        $description = strtolower($transaction ? $transaction->description : '');
        
        $rules = [
            'salary|payroll|wages' => 'Salary',
            'rent|lease' => 'Rent',
            'electricity|water|internet' => 'Utilities',
            'tax|vat|tds' => 'Tax',
            'marketing|advertising' => 'Marketing',
        ];

        foreach ($rules as $keywords => $categoryName) {
            if (preg_match("/($keywords)/i", $description)) {
                $category = $categories->firstWhere('name', $categoryName);
                if ($category) {
                    return [
                        'category_id' => $category->id,
                        'confidence' => 0.65,
                        'reasoning' => "Keyword match: $categoryName",
                        'alternatives' => [],
                        'source' => 'rules'
                    ];
                }
            }
        }

        return [
            'category_id' => null,
            'confidence' => 0.0,
            'reasoning' => 'Unable to categorize automatically',
            'alternatives' => [],
            'source' => 'fallback'
        ];
    }

    // Additional helper methods...
}
```

### Controller Integration

```php
// In FinanceTransactionController@store
public function store(Request $request, FinanceAiCategorizationService $aiService)
{
    $validated = $request->validate([...]);
    
    $transaction = FinanceTransaction::create($validated);
    
    // Auto-categorize if category not provided
    if (!$validated['category_id']) {
        $aiService->autoCategorize($transaction);
    }
    
    return redirect()->route('admin.finance.transactions.index')
        ->with('success', 'Transaction created' . 
            ($transaction->ai_categorized ? ' and auto-categorized' : ''));
}
```

### UI Enhancements

```blade
{{-- In transaction form --}}
<div id="category-suggestion" style="display:none;">
    <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
        <p class="text-sm font-medium text-blue-800">AI Suggestion:</p>
        <p class="text-sm text-blue-700">
            <span id="suggested-category"></span>
            <span class="text-xs">(<span id="confidence"></span>% confident)</span>
        </p>
        <p class="text-xs text-blue-600 mt-1" id="reasoning"></p>
        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="acceptSuggestion()">
            Accept Suggestion
        </button>
    </div>
</div>
```

---

## 📅 Phase 2: Fraud Detection & Anomaly Detection

### Objective
Detect unusual patterns, potential fraud, and anomalies in financial transactions.

### Features

1. **Anomaly Detection**
   - Unusual transaction amounts (>3 standard deviations)
   - Duplicate transactions (same amount, vendor, date)
   - Suspicious timing (off-hours, weekends)
   - Velocity checks (too many transactions in short time)

2. **Fraud Patterns**
   - Round number transactions
   - Sequential invoice numbers from different vendors
   - Vendor name variations (typos to bypass limits)
   - Unusual payment methods for transaction types

3. **Risk Scoring**
   - Calculate risk score 0-100 for each transaction
   - Flag high-risk transactions for review
   - Alert managers for critical risks

### Database Changes

```sql
CREATE TABLE ai_finance_anomaly_detections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    transaction_id BIGINT,
    anomaly_type ENUM('amount', 'duplicate', 'timing', 'velocity', 'pattern', 'fraud_risk'),
    risk_score INT, -- 0-100
    severity ENUM('low', 'medium', 'high', 'critical'),
    description TEXT,
    ai_reasoning TEXT,
    flagged_at TIMESTAMP,
    reviewed_by_user_id BIGINT NULL,
    reviewed_at TIMESTAMP NULL,
    review_status ENUM('pending', 'approved', 'rejected', 'needs_investigation'),
    review_notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES finance_transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_flagged_pending (flagged_at, review_status)
);
```

### Service Implementation

```php
<?php

namespace App\Services\Finance\AI;

class FinanceAiFraudDetectionService
{
    /**
     * Analyze transaction for fraud/anomalies
     */
    public function analyzeTransaction(FinanceTransaction $transaction): array
    {
        $anomalies = [];
        
        // Check for amount anomalies
        if ($amountAnomaly = $this->detectAmountAnomaly($transaction)) {
            $anomalies[] = $amountAnomaly;
        }
        
        // Check for duplicates
        if ($duplicate = $this->detectDuplicate($transaction)) {
            $anomalies[] = $duplicate;
        }
        
        // Check timing
        if ($timing = $this->detectTimingAnomaly($transaction)) {
            $anomalies[] = $timing;
        }
        
        // Check fraud patterns
        if ($fraud = $this->detectFraudPatterns($transaction)) {
            $anomalies = array_merge($anomalies, $fraud);
        }
        
        // Calculate overall risk score
        $riskScore = $this->calculateRiskScore($anomalies);
        
        // Save anomalies if any
        if (!empty($anomalies)) {
            $this->saveAnomalies($transaction, $anomalies, $riskScore);
        }
        
        return [
            'anomalies' => $anomalies,
            'risk_score' => $riskScore,
            'requires_review' => $riskScore >= 70
        ];
    }

    /**
     * Detect amount anomalies using statistical analysis
     */
    protected function detectAmountAnomaly(FinanceTransaction $transaction): ?array
    {
        // Get historical transactions of same type
        $historical = FinanceTransaction::where('company_id', $transaction->company_id)
            ->where('transaction_type', $transaction->transaction_type)
            ->where('id', '!=', $transaction->id)
            ->pluck('amount');
        
        if ($historical->count() < 10) {
            return null; // Not enough data
        }
        
        $mean = $historical->avg();
        $stdDev = $this->calculateStdDev($historical, $mean);
        
        $zScore = abs(($transaction->amount - $mean) / $stdDev);
        
        if ($zScore > 3) {
            return [
                'type' => 'amount',
                'severity' => $zScore > 5 ? 'critical' : 'high',
                'description' => "Transaction amount NPR " . number_format($transaction->amount, 2) . 
                    " is {$zScore} standard deviations from the mean (NPR " . number_format($mean, 2) . ")",
                'risk_score' => min(100, (int)($zScore * 20))
            ];
        }
        
        return null;
    }

    /**
     * Detect duplicate transactions
     */
    protected function detectDuplicate(FinanceTransaction $transaction): ?array
    {
        $duplicate = FinanceTransaction::where('company_id', $transaction->company_id)
            ->where('id', '!=', $transaction->id)
            ->where('amount', $transaction->amount)
            ->where('transaction_date_bs', $transaction->transaction_date_bs)
            ->where('description', 'LIKE', '%' . substr($transaction->description, 0, 50) . '%')
            ->first();
        
        if ($duplicate) {
            return [
                'type' => 'duplicate',
                'severity' => 'high',
                'description' => "Potential duplicate of transaction #{$duplicate->transaction_number}",
                'risk_score' => 85
            ];
        }
        
        return null;
    }

    /**
     * Detect timing anomalies
     */
    protected function detectTimingAnomaly(FinanceTransaction $transaction): ?array
    {
        $createdAt = $transaction->created_at;
        $hour = $createdAt->hour;
        $isWeekend = $createdAt->isWeekend();
        
        // Flag transactions created outside business hours (9 AM - 6 PM)
        if ($hour < 9 || $hour > 18 || $isWeekend) {
            return [
                'type' => 'timing',
                'severity' => 'medium',
                'description' => "Transaction created " . ($isWeekend ? 'on weekend' : 'outside business hours') . 
                    " at " . $createdAt->format('Y-m-d H:i'),
                'risk_score' => $isWeekend ? 50 : 30
            ];
        }
        
        return null;
    }

    /**
     * Detect fraud patterns
     */
    protected function detectFraudPatterns(FinanceTransaction $transaction): array
    {
        $patterns = [];
        
        // Round number check (exactly divisible by 1000)
        if ($transaction->amount > 1000 && $transaction->amount % 1000 == 0) {
            $patterns[] = [
                'type' => 'pattern',
                'severity' => 'low',
                'description' => 'Round number transaction (NPR ' . number_format($transaction->amount, 0) . ')',
                'risk_score' => 20
            ];
        }
        
        // Vendor name variation check
        if ($transaction->reference_type === 'purchase') {
            $similarVendors = $this->findSimilarVendorNames($transaction->description);
            if (count($similarVendors) > 0) {
                $patterns[] = [
                    'type' => 'pattern',
                    'severity' => 'medium',
                    'description' => 'Similar vendor names detected: ' . implode(', ', $similarVendors),
                    'risk_score' => 45
                ];
            }
        }
        
        return $patterns;
    }

    // Helper methods...
}
```

---

## 📅 Phase 3: Financial Forecasting & Predictions

### Features

1. **Cashflow Forecasting**
   - 30/60/90 day cashflow predictions
   - Seasonal pattern analysis
   - Confidence intervals

2. **Budget vs Actual Predictions**
   - Predict end-of-month/year spending
   - Early warning for budget overruns

3. **Trend Analysis**
   - Spending trends by category
   - Revenue growth predictions
   - Expense pattern changes

### Database Schema

```sql
CREATE TABLE ai_finance_predictions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT,
    prediction_type ENUM('cashflow', 'budget', 'revenue', 'expense'),
    period_start_bs VARCHAR(10),
    period_end_bs VARCHAR(10),
    predicted_amount DECIMAL(15,2),
    confidence_level DECIMAL(5,4),
    actual_amount DECIMAL(15,2) NULL,
    accuracy_score DECIMAL(5,4) NULL,
    factors JSON, -- AI reasoning
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES finance_companies(id) ON DELETE CASCADE
);
```

---

## 📅 Phase 4: Smart Recommendations

### Features

1. **Budget Optimization**
   - Identify overspending categories
   - Suggest budget reallocations
   - Cost-saving opportunities

2. **Payment Timing Optimization**
   - Optimal payment dates for cashflow
   - Early payment discount recommendations

3. **Vendor Risk Assessment**
   - Vendor reliability scoring
   - Alternative vendor suggestions

---

## 📅 Phase 5: AI Dashboard

### Features

1. **Real-time Alerts**
   - Anomaly notifications
   - Budget warnings
   - Fraud alerts

2. **Insights Widgets**
   - Top spending categories
   - Prediction charts
   - Risk heatmaps

3. **Natural Language Reports**
   - AI-generated summaries
   - Trend explanations
   - Recommendation reports

---

## 🔧 Technical Implementation

### Models to Create

```
app/Models/
  ├── AiFinanceCategoryPrediction.php
  ├── AiFinanceCategoryPattern.php
  ├── AiFinanceAnomalyDetection.php
  └── AiFinancePrediction.php
```

### Services to Create

```
app/Services/Finance/AI/
  ├── FinanceAiCategorizationService.php
  ├── FinanceAiFraudDetectionService.php
  ├── FinanceAiForecastingService.php
  ├── FinanceAiRecommendationService.php
  └── FinanceAiInsightsService.php
```

### Controllers

```
app/Http/Controllers/Admin/
  └── FinanceAiController.php (Dashboard, insights, review anomalies)
```

### Console Commands

```
app/Console/Commands/
  ├── AnalyzeFinanceAnomalies.php (Daily cron)
  ├── GenerateFinanceForecasts.php (Weekly cron)
  └── TrainFinanceAiModels.php (Monthly cron)
```

### Configuration

```php
// config/finance_ai.php
return [
    'enabled' => env('FINANCE_AI_ENABLED', true),
    'provider' => env('FINANCE_AI_PROVIDER', 'openai'), // openai or anthropic
    'model' => env('FINANCE_AI_MODEL', 'gpt-4'),
    'auto_categorize_threshold' => 0.90,
    'fraud_alert_threshold' => 70,
    'prediction_confidence_threshold' => 0.75,
];
```

---

## 📊 Success Metrics

| Metric | Target | Measurement |
|--------|--------|-------------|
| Auto-categorization accuracy | >85% | % of accepted AI suggestions |
| Fraud detection rate | >90% | % of flagged transactions confirmed as issues |
| Categorization time savings | 70% | Time reduction vs manual |
| Forecast accuracy | >80% | Actual vs predicted variance |
| User adoption | >60% | % of transactions using AI features |

---

## 🚀 Implementation Timeline

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Phase 1: Categorization | 1 week | OpenAI API setup, database migrations |
| Phase 2: Fraud Detection | 2 weeks | Phase 1 complete, statistical libraries |
| Phase 3: Forecasting | 2 weeks | Historical data (6+ months), ML libraries |
| Phase 4: Recommendations | 1 week | Phases 1-3 complete |
| Phase 5: Dashboard | 1 week | All phases complete |

**Total Estimated Time**: 7 weeks

---

## 📋 Next Steps

1. ✅ Get stakeholder approval
2. ✅ Set up OpenAI API access
3. ⬜ Create database migrations for Phase 1
4. ⬜ Implement FinanceAiCategorizationService
5. ⬜ Add AI category field to transactions table
6. ⬜ Update transaction creation UI with AI suggestions
7. ⬜ Test with historical data
8. ⬜ Deploy to staging
9. ⬜ Gather user feedback
10. ⬜ Iterate and improve

---

**End of AI Implementation Plan**
