<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Exception;

class HuggingFaceService implements AiServiceInterface
{
    protected $apiKey;
    protected $model;
    protected $endpoint;
    protected $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.ai.huggingface.api_key');
        $this->model = config('services.ai.huggingface.model', 'gpt2');
        $this->endpoint = config('services.ai.huggingface.endpoint', 'https://api-inference.huggingface.co');
        $this->timeout = config('services.ai.timeout', 30);
    }

    /**
     * Generate text based on a prompt
     */
    public function generateText(string $prompt, array $options = []): string
    {
        try {
            $url = "{$this->endpoint}/models/{$options['model'] ?? $this->model}";

            $payload = [
                'inputs' => $prompt,
                'parameters' => [
                    'max_length' => $options['max_tokens'] ?? 200,
                    'temperature' => $options['temperature'] ?? 0.7,
                ],
            ];

            $response = $this->makeRequest('POST', $url, $payload);

            if (is_array($response) && isset($response[0]['generated_text'])) {
                return trim($response[0]['generated_text']);
            }

            throw new Exception('Invalid response from HuggingFace API');
        } catch (Exception $e) {
            Log::error('HuggingFace text generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analyze sentiment of text
     */
    public function analyzeSentiment(string $text): array
    {
        try {
            $url = "{$this->endpoint}/models/distilbert-base-uncased-finetuned-sst-2-english";

            $response = $this->makeRequest('POST', $url, ['inputs' => $text]);

            if (is_array($response) && isset($response[0])) {
                $scores = $response[0];

                // HuggingFace returns array of scores
                $labels = array_column($scores, 'label');
                $values = array_column($scores, 'score', 'label');

                $score = $values['POSITIVE'] ?? 0.5;
                $classification = $score >= 0.5 ? 'positive' : 'negative';

                return [
                    'score' => $score,
                    'classification' => $classification,
                    'explanation' => 'Sentiment analyzed using distilbert model',
                    'raw_response' => $response,
                ];
            }

            throw new Exception('Invalid sentiment response from HuggingFace');
        } catch (Exception $e) {
            Log::error('HuggingFace sentiment analysis error: ' . $e->getMessage());
            // Return neutral sentiment on error
            return [
                'score' => 0.5,
                'classification' => 'neutral',
                'explanation' => 'Error during sentiment analysis',
                'raw_response' => null,
            ];
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
        return 'huggingface';
    }

    /**
     * Make HTTP request to HuggingFace API
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
            Log::error('HuggingFace API request error: ' . $e->getMessage());
            throw $e;
        }
    }
}
