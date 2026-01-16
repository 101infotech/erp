<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Settings
    |--------------------------------------------------------------------------
    |
    | Configure maintenance mode settings for the application.
    |
    */

    /**
     * User IDs that can bypass maintenance mode
     * Add specific user IDs who should always have access even during maintenance
     */
    'allowed_user_ids' => [
        // Example: 1, 2, 3
        // Add user IDs here
    ],

    /**
     * Default maintenance message
     */
    'default_message' => 'We are currently performing scheduled maintenance. We\'ll be back shortly!',

    /**
     * Roles that can bypass maintenance mode
     * These roles are automatically allowed (defined in middleware)
     */
    'allowed_roles' => [
        'super_admin',
        'admin',
    ],
];
