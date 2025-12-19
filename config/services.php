<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Jibble API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Jibble time tracking and attendance management API.
    | Used by the HRM module for employee and timesheet synchronization.
    |
    */

    'jibble' => [
        'client_id' => env('JIBBLE_CLIENT_ID'),
        'client_secret' => env('JIBBLE_CLIENT_SECRET'),
        'workspace_id' => env('JIBBLE_WORKSPACE_ID'),
        'base_url' => env('JIBBLE_BASE_URL', 'https://time-attendance.prod.jibble.io'),
        'people_select' => env('JIBBLE_PEOPLE_SELECT', 'id,fullName,email,department'),
        'default_company_id' => env('JIBBLE_DEFAULT_COMPANY_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI/LLM services used throughout the HRM system.
    | Supports multiple providers: OpenAI, HuggingFace, Anthropic
    | Used for feedback generation, sentiment analysis, and HR insights.
    |
    */

    'ai' => [
        'enabled' => env('AI_ENABLED', false),
        'provider' => env('AI_PROVIDER', 'openai'), // openai, huggingface, anthropic
        'timeout' => env('AI_TIMEOUT', 30),
        'retry_attempts' => env('AI_RETRY_ATTEMPTS', 3),
        'cache_responses' => env('AI_CACHE_RESPONSES', true),
        'cache_ttl' => env('AI_CACHE_TTL', 86400), // 24 hours

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4'),
            'temperature' => env('OPENAI_TEMPERATURE', 0.7),
            'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        ],

        'huggingface' => [
            'api_key' => env('HUGGINGFACE_API_KEY'),
            'model' => env('HUGGINGFACE_MODEL', 'gpt2'),
            'endpoint' => env('HUGGINGFACE_ENDPOINT', 'https://api-inference.huggingface.co'),
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-2'),
        ],

        // BrandBird AI (Cloudflare Workers) - custom provider
        'brandbird' => [
            'api_key' => env('BRAND_BIRD_API_KEY'),
            'base_url' => env('BRAND_BIRD_BASE_URL', 'https://ai.brand-bird.workers.dev/'),
            // Optional defaults
            'system_prompt' => env('BRAND_BIRD_SYSTEM_PROMPT', 'You are a knowledgeable assistant.'),
        ],

        'features' => [
            'feedback_questions' => env('AI_FEATURE_FEEDBACK_QUESTIONS', true),
            'sentiment_analysis' => env('AI_FEATURE_SENTIMENT_ANALYSIS', true),
            'performance_insights' => env('AI_FEATURE_PERFORMANCE_INSIGHTS', false),
            'hr_chatbot' => env('AI_FEATURE_HR_CHATBOT', false),
            'resume_analysis' => env('AI_FEATURE_RESUME_ANALYSIS', false),
        ],

        'feedback' => [
            'questions_per_week' => env('AI_FEEDBACK_QUESTIONS_COUNT', 3),
            'include_sentiment' => env('AI_FEEDBACK_INCLUDE_SENTIMENT', true),
            'adaptive_questions' => env('AI_FEEDBACK_ADAPTIVE_QUESTIONS', true),
        ],
    ],

];
