<?php

return [
    'apify' => [
        'token' => env('APIFY_TOKEN'),
    ],

    'google' => [
        'place_ids' => env('GOOGLE_PLACE_IDS'),
        'max_reviews' => env('GOOGLE_MAX_REVIEWS', 1000)
    ],

    'assets' => [
        'container' => 'reviews',

        'disk' => [
            'driver' => 'local',
            'root' => public_path('reviews'),
            'url' => '/reviews',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'download_timeout' => env('REVIEWS_DOWNLOAD_TIMEOUT', 30),
    ],
];
