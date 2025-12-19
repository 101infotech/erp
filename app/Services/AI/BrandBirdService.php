<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class BrandBirdService implements AiServiceInterface
{
    protected $apiKey;
    protected $baseUrl;
    protected $timeout;
    protected $systemPrompt;

    public function __construct()
    {
        $this->apiKey = config('services.ai.brandbird.api_key');
        $this->baseUrl = rtrim(config('services.ai.brandbird.base_url', 'https://ai.brand-bird.workers.dev/'), '/');
        $this->timeout = config('services.ai.timeout', 30);
        $this->systemPrompt = config('services.ai.brandbird.system_prompt', 'You are a knowledgeable assistant.');
    }

    /**
     * Generate text based on a prompt using BrandBird AI (Cloudflare Workers)
     */
    public function generateText(string $prompt, array $options = []): string
    {
        try {
            // Optional caching
            if (config('services.ai.cache_responses')) {
                $cacheKey = 'brandbird_response_' . hash('sha256', $prompt . json_encode($options));
                if ($cached = Cache::get($cacheKey)) {
                    return $cached;
                }
            }

            $payload = [
                'prompt' => $prompt,
                'systemPrompt' => $options['systemPrompt'] ?? $this->systemPrompt,
                'history' => $options['history'] ?? [],
            ];

            $response = $this->makeRequest('POST', $this->baseUrl . '/', $payload);

            if ($response && isset($response['response'])) {
                $text = trim($response['response']);

                if (config('services.ai.cache_responses')) {
                    Cache::put($cacheKey, $text, config('services.ai.cache_ttl'));
                }

                return $text;
            }

            throw new Exception('Invalid response from BrandBird AI');
        } catch (Exception $e) {
            Log::error('BrandBird text generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analyze sentiment using the same BrandBird endpoint via prompt engineering.
     * Returns array { score, classification, explanation, raw_response }
     */
    public function analyzeSentiment(string $text): array
    {
        try {
            $analysisPrompt = <<<PROMPT
            Analyze the sentiment of the following text. Respond with ONLY a valid JSON object containing:
            - "score": a number between 0 and 1 (0 = very negative, 1 = very positive)
            - "classification": one of "very_negative", "negative", "neutral", "positive", "very_positive"
            - "explanation": brief explanation of the sentiment

            Text to analyze:
            "$text"
            PROMPT;

            $raw = $this->generateText($analysisPrompt, [
                'systemPrompt' => 'You are a sentiment analysis assistant for HR.',
                'history' => [],
            ]);

            $json = $this->extractJson($raw);

            if ($json) {
                return [
                    'score' => (float) ($json['score'] ?? 0.5),
                    'classification' => $json['classification'] ?? 'neutral',
                    'explanation' => $json['explanation'] ?? '',
                    'raw_response' => $raw,
                ];
            }

            return $this->fallbackSentimentAnalysis($text);
        } catch (Exception $e) {
            Log::error('BrandBird sentiment analysis error: ' . $e->getMessage());
            return $this->fallbackSentimentAnalysis($text);
        }
    }

    /**
     * Check availability
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    /**
     * Model name (BrandBird Workers has no model concept; return provider tag)
     */
    public function getModel(): string
    {
        return 'brandbird-workers';
    }

    /**
     * Provider name
     */
    public function getProvider(): string
    {
        return 'brandbird';
    }

    /**
     * Perform HTTP request
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
            Log::error('BrandBird API request error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract JSON object from a text blob
     */
    protected function extractJson(string $text): ?array
    {
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            try {
                return json_decode($matches[0], true);
            } catch (Exception $e) {
                Log::warning('BrandBird: failed to parse JSON from response: ' . $e->getMessage());
            }
        }
        return null;
    }

    /**
     * Fallback sentiment analysis
     */
    protected function fallbackSentimentAnalysis(string $text): array
    {
        $text = strtolower($text);
        $positive_words = ['great', 'excellent', 'good', 'happy', 'satisfied', 'motivated', 'progress', 'success', 'wonderful', 'amazing', 'positive', 'improvement'];
        $negative_words = ['bad', 'terrible', 'poor', 'sad', 'frustrated', 'demotivated', 'failure', 'difficult', 'challenge', 'issue', 'problem', 'negative', 'decline'];
        $p = 0;
        $n = 0;
        foreach ($positive_words as $w) {
            $p += substr_count($text, $w);
        }
        foreach ($negative_words as $w) {
            $n += substr_count($text, $w);
        }
        $total = $p + $n;
        $score = $total > 0 ? $p / $total : 0.5;
        $classification = $score >= 0.7 ? 'positive' : ($score >= 0.55 ? 'neutral' : 'negative');
        return [
            'score' => $score,
            'classification' => $classification,
            'explanation' => 'Fallback keyword-based sentiment analysis',
            'raw_response' => null,
        ];
    }
}
