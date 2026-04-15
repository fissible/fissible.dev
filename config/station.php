<?php

return [
    'platform' => [
        'enabled' => env('STATION_PLATFORM_ENABLED', false),
        'host' => env('STATION_PLATFORM_HOST', 'platform.fissible.dev'),
    ],

    'domains' => [
        'managed_root' => env('STATION_MANAGED_ROOT_DOMAIN', 'fissible.dev'),
        'app_hosts' => [
            env('STATION_PLATFORM_HOST', 'platform.fissible.dev'),
        ],
        'reserved_slugs' => [
            'www',
            'platform',
            'station-demo',
            'api',
            'admin',
            'mail',
            'staging',
            'app',
            'support',
        ],
    ],

    'demo' => [
        'shared_slug' => env('STATION_SHARED_DEMO_SLUG', 'station-demo'),
        'shared_name' => env('STATION_SHARED_DEMO_NAME', 'Station Demo'),
        'user_name' => env('STATION_DEMO_USER_NAME', 'Station Demo User'),
        'user_email' => env('STATION_DEMO_USER_EMAIL', 'demo@fissible.dev'),
        'user_password' => env('STATION_DEMO_USER_PASSWORD', 'demo-password'),
        'lifetime_days' => (int) env('STATION_DEMO_LIFETIME_DAYS', 14),
        'grace_days' => (int) env('STATION_DEMO_GRACE_DAYS', 7),
    ],
];
