<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Exception;

class AiServiceFactory
{
    /**
     * Create and return the appropriate AI service
     */
    public static function make(): ?AiServiceInterface
    {
        if (!config('services.ai.enabled')) {
            Log::debug('AI services are disabled');
            return null;
        }

        $provider = config('services.ai.provider', 'openai');

        return match ($provider) {
            'openai' => new OpenAiService(),
            'huggingface' => new HuggingFaceService(),
            'brandbird' => new BrandBirdService(),
            default => throw new Exception("Unknown AI provider: {$provider}"),
        };
    }

    /**
     * Get a specific service by provider name
     */
    public static function makeProvider(string $provider): ?AiServiceInterface
    {
        return match ($provider) {
            'openai' => new OpenAiService(),
            'huggingface' => new HuggingFaceService(),
            'brandbird' => new BrandBirdService(),
            default => throw new Exception("Unknown AI provider: {$provider}"),
        };
    }
}
