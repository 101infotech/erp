<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Finance AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI-powered finance features including auto-categorization,
    | anomaly detection, and predictive analytics.
    |
    */

    'enabled' => env('FINANCE_AI_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | AI Provider Settings
    |--------------------------------------------------------------------------
    */

    'provider' => env('FINANCE_AI_PROVIDER', 'openai'),
    'api_key' => env('OPENAI_API_KEY'),
    'model' => env('FINANCE_AI_MODEL', 'gpt-4'),
    'timeout' => env('FINANCE_AI_TIMEOUT', 30), // seconds

    /*
    |--------------------------------------------------------------------------
    | Auto-Categorization Settings
    |--------------------------------------------------------------------------
    */

    'categorization' => [
        // Automatically assign category if confidence is above this threshold
        'auto_assign_threshold' => 0.90,

        // Minimum confidence to show suggestion
        'suggestion_threshold' => 0.70,

        // Enable pattern-based caching for faster predictions
        'use_pattern_cache' => true,

        // Days to keep prediction history
        'prediction_retention_days' => 90,

        // Learn from user corrections
        'learn_from_corrections' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Anomaly Detection Settings
    |--------------------------------------------------------------------------
    */

    'anomaly_detection' => [
        'enabled' => env('FINANCE_AI_ANOMALY_DETECTION', true),

        // Risk score thresholds (0-100)
        'thresholds' => [
            'critical' => 80,  // Requires immediate review
            'high' => 60,      // Flag for review
            'medium' => 40,    // Monitor
            'low' => 20,       // Informational
        ],

        // Anomaly types to detect
        'detect_types' => [
            'unusual_amount',
            'duplicate_transaction',
            'unusual_vendor',
            'unusual_timing',
            'suspicious_pattern',
            'budget_variance',
        ],

        // Notify users when anomaly detected
        'notify_on_detection' => true,

        // Email addresses for critical anomalies
        'notification_emails' => env('FINANCE_AI_ALERT_EMAILS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Predictive Analytics Settings
    |--------------------------------------------------------------------------
    */

    'predictions' => [
        'enabled' => env('FINANCE_AI_PREDICTIONS', true),

        // Prediction types
        'types' => [
            'cashflow' => [
                'enabled' => true,
                'forecast_days' => 30,
                'min_history_days' => 90,
            ],
            'budget' => [
                'enabled' => true,
                'forecast_months' => 3,
                'min_history_months' => 6,
            ],
            'revenue' => [
                'enabled' => true,
                'forecast_days' => 30,
                'min_history_days' => 90,
            ],
            'expense' => [
                'enabled' => true,
                'forecast_days' => 30,
                'min_history_days' => 90,
            ],
        ],

        // Minimum confidence level to show predictions
        'min_confidence' => 0.60,

        // Re-generate predictions every X days
        'refresh_interval_days' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pattern Learning Settings
    |--------------------------------------------------------------------------
    */

    'patterns' => [
        // Pattern types to track
        'types' => [
            'vendor',          // Vendor → Category mapping
            'description',     // Description keywords → Category
            'amount_range',    // Amount range → Category
            'composite',       // Multiple factors combined
        ],

        // Minimum usage count before pattern is considered reliable
        'min_usage_count' => 3,

        // Minimum success rate to use pattern
        'min_success_rate' => 0.80,

        // Update pattern confidence based on outcomes
        'dynamic_confidence' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */

    'performance' => [
        // Cache AI responses
        'cache_enabled' => true,
        'cache_ttl' => 3600, // seconds

        // Queue heavy AI operations
        'use_queue' => env('FINANCE_AI_USE_QUEUE', false),
        'queue_name' => 'finance-ai',

        // Rate limiting
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 60,
            'per_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging & Debugging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => env('FINANCE_AI_LOGGING', true),
        'log_channel' => 'stack',
        'log_level' => env('FINANCE_AI_LOG_LEVEL', 'info'),

        // Log all AI requests/responses
        'log_requests' => env('FINANCE_AI_LOG_REQUESTS', false),

        // Log prediction accuracy
        'track_accuracy' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Settings
    |--------------------------------------------------------------------------
    */

    'fallback' => [
        // If AI fails, use pattern matching only
        'use_pattern_fallback' => true,

        // Default category if no suggestion available
        'default_category_id' => null,

        // Show error to user or fail silently
        'show_errors' => env('APP_DEBUG', false),
    ],
];
