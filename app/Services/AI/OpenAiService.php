<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAiService implements AiServiceInterface
{
    protected $apiKey;
    protected $model;
    protected $timeout;
    protected $temperature;
    protected $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.ai.openai.api_key');
        $this->model = config('services.ai.openai.model', 'gpt-4');
        $this->timeout = config('services.ai.timeout', 30);
        $this->temperature = config('services.ai.openai.temperature', 0.7);
        $this->maxTokens = config('services.ai.openai.max_tokens', 1000);
    }

    /**
     * Generate text based on a prompt
     */
    public function generateText(string $prompt, array $options = []): string
    {
        try {
            // Check cache first
            if (config('services.ai.cache_responses')) {
                $cacheKey = 'ai_response_' . hash('sha256', $prompt . json_encode($options));
                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return $cached;
                }
            }

            $payload = [
                'model' => $options['model'] ?? $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful HR assistant for employee feedback. Generate concise, professional, and thoughtful questions or responses.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $options['temperature'] ?? $this->temperature,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            ];

            $response = $this->makeRequest('POST', 'https://api.openai.com/v1/chat/completions', $payload);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $text = trim($response['choices'][0]['message']['content']);

                // Cache the response
                if (config('services.ai.cache_responses')) {
                    Cache::put($cacheKey, $text, config('services.ai.cache_ttl'));
                }

                return $text;
            }

            throw new Exception('Invalid response from OpenAI API');
        } catch (Exception $e) {
            Log::error('OpenAI text generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analyze sentiment of text
     */
    public function analyzeSentiment(string $text): array
    {
        try {
            $prompt = <<<PROMPT
            Analyze the sentiment of the following text. Respond with ONLY a valid JSON object containing:
            - "score": a number between 0 and 1 (0 = very negative, 1 = very positive)
            - "classification": one of "very_negative", "negative", "neutral", "positive", "very_positive"
            - "explanation": brief explanation of the sentiment
            
            Text to analyze:
            "$text"
            PROMPT;

            $response = $this->generateText($prompt, [
                'temperature' => 0.3, // Lower temperature for more consistent results
                'max_tokens' => 200,
            ]);

            // Extract JSON from response
            $json = $this->extractJson($response);

            if ($json) {
                return [
                    'score' => (float) ($json['score'] ?? 0.5),
                    'classification' => $json['classification'] ?? 'neutral',
                    'explanation' => $json['explanation'] ?? '',
                    'raw_response' => $response,
                ];
            }

            // Fallback if JSON extraction fails
            return $this->fallbackSentimentAnalysis($text);
        } catch (Exception $e) {
            Log::error('OpenAI sentiment analysis error: ' . $e->getMessage());
            return $this->fallbackSentimentAnalysis($text);
        }
    }

    /**
     * Check if service is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get model name
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get provider name
     */
    public function getProvider(): string
    {
        return 'openai';
    }

    /**
     * Make HTTP request to OpenAI API
     */
    protected function makeRequest(string $method, string $url, array $data = []): ?array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        $client = new \GuzzleHttp\Client([
            'timeout' => $this->timeout,
        ]);

        $options = [
            'headers' => $headers,
        ];

        if ($method === 'POST') {
            $options['json'] = $data;
        }

        try {
            $response = $client->request($method, $url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('OpenAI API request error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract JSON from text response
     */
    protected function extractJson(string $text): ?array
    {
        // Try to find JSON in the response
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            try {
                return json_decode($matches[0], true);
            } catch (Exception $e) {
                Log::warning('Failed to parse JSON from response: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Fallback sentiment analysis using simple keyword matching
     */
    protected function fallbackSentimentAnalysis(string $text): array
    {
        $text = strtolower($text);

        $positive_words = ['great', 'excellent', 'good', 'happy', 'satisfied', 'motivated', 'progress', 'success', 'wonderful', 'amazing', 'positive', 'improvement'];
        $negative_words = ['bad', 'terrible', 'poor', 'sad', 'frustrated', 'demotivated', 'failure', 'difficult', 'challenge', 'issue', 'problem', 'negative', 'decline'];

        $positive_count = 0;
        $negative_count = 0;

        foreach ($positive_words as $word) {
            $positive_count += substr_count($text, $word);
        }

        foreach ($negative_words as $word) {
            $negative_count += substr_count($text, $word);
        }

        $total = $positive_count + $negative_count;
        $score = $total > 0 ? $positive_count / $total : 0.5;

        if ($score >= 0.7) {
            $classification = 'positive';
        } elseif ($score >= 0.55) {
            $classification = 'neutral';
        } else {
            $classification = 'negative';
        }

        return [
            'score' => $score,
            'classification' => $classification,
            'explanation' => 'Sentiment analyzed using keyword matching (fallback method)',
            'raw_response' => null,
        ];
    }
}
