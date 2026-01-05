<?php

namespace App\Services\Finance\AI;

use App\Models\AiFinanceCategoryPattern;
use App\Models\AiFinanceCategoryPrediction;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinanceAiCategorizationService
{
    /**
     * Suggest a category for a transaction
     */
    public function suggestCategory(FinanceTransaction $transaction): ?array
    {
        try {
            // First, check pattern cache for fast prediction
            if (config('finance_ai.categorization.use_pattern_cache')) {
                $cachedSuggestion = $this->checkPatternCache($transaction);
                if ($cachedSuggestion) {
                    return $cachedSuggestion;
                }
            }

            // No pattern match, use AI
            return $this->callAiForCategorization($transaction);
        } catch (\Exception $e) {
            Log::error('AI categorization failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback to pattern matching only
            if (config('finance_ai.fallback.use_pattern_fallback')) {
                return $this->checkPatternCache($transaction);
            }

            return null;
        }
    }

    /**
     * Auto-categorize transaction if confidence is high enough
     */
    public function autoCategorize(FinanceTransaction $transaction): bool
    {
        if (!config('finance_ai.enabled') || $transaction->category_id) {
            return false;
        }

        $suggestion = $this->suggestCategory($transaction);

        if (!$suggestion) {
            return false;
        }

        $threshold = config('finance_ai.categorization.auto_assign_threshold', 0.90);

        if ($suggestion['confidence'] >= $threshold) {
            $transaction->update([
                'category_id' => $suggestion['category_id'],
                'ai_categorized' => true,
            ]);

            // Record the prediction
            $this->recordPrediction($transaction, $suggestion, true);

            return true;
        }

        // Record as suggestion only
        $this->recordPrediction($transaction, $suggestion, false);

        return false;
    }

    /**
     * Check pattern cache for matching patterns
     */
    protected function checkPatternCache(FinanceTransaction $transaction): ?array
    {
        $patterns = [];

        // Check vendor pattern
        if ($transaction->vendor_id) {
            $vendorPattern = AiFinanceCategoryPattern::where('company_id', $transaction->company_id)
                ->where('pattern_type', 'vendor')
                ->where('pattern_value', $transaction->vendor_id)
                ->where('success_rate', '>=', config('finance_ai.patterns.min_success_rate', 0.80))
                ->where('usage_count', '>=', config('finance_ai.patterns.min_usage_count', 3))
                ->orderByDesc('confidence_score')
                ->first();

            if ($vendorPattern) {
                $patterns[] = [
                    'pattern' => $vendorPattern,
                    'weight' => 0.8,
                ];
            }
        }

        // Check description pattern
        if ($transaction->description) {
            $descriptionPattern = AiFinanceCategoryPattern::where('company_id', $transaction->company_id)
                ->where('pattern_type', 'description')
                ->where('success_rate', '>=', config('finance_ai.patterns.min_success_rate', 0.80))
                ->where('usage_count', '>=', config('finance_ai.patterns.min_usage_count', 3))
                ->orderByDesc('confidence_score')
                ->get()
                ->first(function ($pattern) use ($transaction) {
                    return str_contains(
                        strtolower($transaction->description),
                        strtolower($pattern->pattern_value)
                    );
                });

            if ($descriptionPattern) {
                $patterns[] = [
                    'pattern' => $descriptionPattern,
                    'weight' => 0.6,
                ];
            }
        }

        // Check amount range pattern
        if ($transaction->amount) {
            $amountPattern = AiFinanceCategoryPattern::where('company_id', $transaction->company_id)
                ->where('pattern_type', 'amount_range')
                ->where('success_rate', '>=', config('finance_ai.patterns.min_success_rate', 0.80))
                ->where('usage_count', '>=', config('finance_ai.patterns.min_usage_count', 3))
                ->orderByDesc('confidence_score')
                ->get()
                ->first(function ($pattern) use ($transaction) {
                    $metadata = $pattern->metadata ?? [];
                    $min = $metadata['min_amount'] ?? 0;
                    $max = $metadata['max_amount'] ?? PHP_FLOAT_MAX;
                    return $transaction->amount >= $min && $transaction->amount <= $max;
                });

            if ($amountPattern) {
                $patterns[] = [
                    'pattern' => $amountPattern,
                    'weight' => 0.4,
                ];
            }
        }

        if (empty($patterns)) {
            return null;
        }

        // Use highest confidence pattern
        $bestPattern = collect($patterns)->sortByDesc(function ($item) {
            return $item['pattern']->confidence_score * $item['weight'];
        })->first();

        $pattern = $bestPattern['pattern'];

        return [
            'category_id' => $pattern->category_id,
            'confidence' => (float) $pattern->confidence_score * $bestPattern['weight'],
            'reasoning' => "Pattern matched based on {$pattern->pattern_type} (Success rate: " .
                round($pattern->success_rate * 100) . "%, Used " .
                $pattern->usage_count . " times)",
            'pattern_matched' => true,
            'pattern_id' => $pattern->id,
        ];
    }

    /**
     * Call AI API for categorization
     */
    protected function callAiForCategorization(FinanceTransaction $transaction): ?array
    {
        if (!config('finance_ai.enabled') || !config('finance_ai.api_key')) {
            return null;
        }

        // Get available categories for this company
        $categories = FinanceCategory::where('company_id', $transaction->company_id)
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'type' => $cat->type,
            ])
            ->toArray();

        if (empty($categories)) {
            return null;
        }

        // Build AI prompt
        $prompt = $this->buildCategorizationPrompt($transaction, $categories);

        // Call OpenAI API
        $response = Http::timeout(config('finance_ai.timeout', 30))
            ->withToken(config('finance_ai.api_key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('finance_ai.model', 'gpt-4'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a financial categorization expert. Analyze transactions and suggest the most appropriate category with confidence score and reasoning.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.3,
                'response_format' => ['type' => 'json_object'],
            ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API call failed: ' . $response->body());
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return null;
        }

        $aiResponse = json_decode($content, true);

        return [
            'category_id' => $aiResponse['category_id'],
            'confidence' => (float) $aiResponse['confidence'],
            'reasoning' => $aiResponse['reasoning'],
            'pattern_matched' => false,
        ];
    }

    /**
     * Build prompt for AI categorization
     */
    protected function buildCategorizationPrompt(FinanceTransaction $transaction, array $categories): string
    {
        $categoriesText = collect($categories)
            ->map(fn($cat) => "- ID: {$cat['id']}, Name: {$cat['name']}, Type: {$cat['type']}")
            ->implode("\n");

        return <<<PROMPT
Analyze this financial transaction and suggest the most appropriate category.

Transaction Details:
- Type: {$transaction->transaction_type}
- Amount: {$transaction->amount}
- Description: {$transaction->description}
- Vendor: {$transaction->vendor?->name}
- Date: {$transaction->transaction_date}

Available Categories:
{$categoriesText}

Provide your response in JSON format with the following structure:
{
  "category_id": <number>,
  "confidence": <0.0 to 1.0>,
  "reasoning": "<brief explanation of why this category was chosen>"
}

Consider:
1. The transaction type and amount
2. Keywords in the description
3. Vendor relationship (if any)
4. Common accounting practices
5. Similar transactions

Respond ONLY with valid JSON.
PROMPT;
    }

    /**
     * Record AI prediction
     */
    protected function recordPrediction(
        FinanceTransaction $transaction,
        array $suggestion,
        bool $wasAutoApplied
    ): void {
        AiFinanceCategoryPrediction::create([
            'transaction_id' => $transaction->id,
            'suggested_category_id' => $suggestion['category_id'],
            'confidence_score' => $suggestion['confidence'],
            'reasoning' => $suggestion['reasoning'],
            'was_accepted' => $wasAutoApplied,
            'pattern_matched' => $suggestion['pattern_matched'] ?? false,
            'ai_model_version' => config('finance_ai.model'),
        ]);

        // Update pattern usage if pattern was matched
        if ($suggestion['pattern_matched'] ?? false) {
            $pattern = AiFinanceCategoryPattern::find($suggestion['pattern_id'] ?? null);
            $pattern?->recordUsage($wasAutoApplied);
        }
    }

    /**
     * Learn from user correction
     */
    public function learnFromCorrection(
        FinanceTransaction $transaction,
        int $correctedCategoryId,
        ?string $feedback = null
    ): void {
        if (!config('finance_ai.categorization.learn_from_corrections')) {
            return;
        }

        // Update the prediction record
        $prediction = AiFinanceCategoryPrediction::where('transaction_id', $transaction->id)
            ->latest()
            ->first();

        if ($prediction) {
            $prediction->update([
                'was_accepted' => $prediction->suggested_category_id === $correctedCategoryId,
                'user_corrected_category_id' => $correctedCategoryId,
                'correction_feedback' => $feedback,
            ]);
        }

        // Create or update patterns based on correction
        $this->updatePatterns($transaction, $correctedCategoryId);
    }

    /**
     * Update patterns based on transaction
     */
    protected function updatePatterns(FinanceTransaction $transaction, int $categoryId): void
    {
        // Vendor pattern
        if ($transaction->vendor_id) {
            $this->updateOrCreatePattern(
                $transaction->company_id,
                'vendor',
                (string) $transaction->vendor_id,
                $categoryId
            );
        }

        // Description keywords pattern
        if ($transaction->description) {
            $keywords = $this->extractKeywords($transaction->description);
            foreach ($keywords as $keyword) {
                $this->updateOrCreatePattern(
                    $transaction->company_id,
                    'description',
                    $keyword,
                    $categoryId
                );
            }
        }

        // Amount range pattern
        if ($transaction->amount) {
            $this->updateAmountRangePattern(
                $transaction->company_id,
                $transaction->amount,
                $categoryId
            );
        }
    }

    /**
     * Update or create a pattern
     */
    protected function updateOrCreatePattern(
        int $companyId,
        string $patternType,
        string $patternValue,
        int $categoryId
    ): void {
        $pattern = AiFinanceCategoryPattern::firstOrCreate(
            [
                'company_id' => $companyId,
                'pattern_type' => $patternType,
                'pattern_value' => $patternValue,
                'category_id' => $categoryId,
            ],
            [
                'confidence_score' => 0.50,
                'usage_count' => 0,
                'success_rate' => 0.50,
            ]
        );

        $pattern->recordUsage(true);
    }

    /**
     * Update amount range pattern
     */
    protected function updateAmountRangePattern(int $companyId, float $amount, int $categoryId): void
    {
        // Define amount ranges
        $ranges = [
            [0, 1000],
            [1000, 5000],
            [5000, 10000],
            [10000, 50000],
            [50000, PHP_FLOAT_MAX],
        ];

        foreach ($ranges as [$min, $max]) {
            if ($amount >= $min && $amount < $max) {
                $pattern = AiFinanceCategoryPattern::firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'pattern_type' => 'amount_range',
                        'pattern_value' => "{$min}-{$max}",
                        'category_id' => $categoryId,
                    ],
                    [
                        'confidence_score' => 0.30,
                        'usage_count' => 0,
                        'success_rate' => 0.30,
                        'metadata' => [
                            'min_amount' => $min,
                            'max_amount' => $max,
                        ],
                    ]
                );

                $pattern->recordUsage(true);
                break;
            }
        }
    }

    /**
     * Extract keywords from description
     */
    protected function extractKeywords(string $description): array
    {
        // Remove common words
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'];

        $words = preg_split('/\s+/', strtolower($description));
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });

        return array_unique($keywords);
    }
}
