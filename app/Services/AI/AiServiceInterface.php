<?php

namespace App\Services\AI;

interface AiServiceInterface
{
    /**
     * Generate text based on a prompt
     */
    public function generateText(string $prompt, array $options = []): string;

    /**
     * Analyze sentiment of text
     * Returns a value between 0 and 1, where 0 is very negative and 1 is very positive
     */
    public function analyzeSentiment(string $text): array;

    /**
     * Check if the service is available
     */
    public function isAvailable(): bool;

    /**
     * Get the model name being used
     */
    public function getModel(): string;

    /**
     * Get the provider name
     */
    public function getProvider(): string;
}
