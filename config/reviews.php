<?php

return [
    'apify' => [
        'token' => env('APIFY_TOKEN'),
    ],

    'google' => [
        'place_ids' => env('GOOGLE_PLACE_IDS'),
        'max_reviews' => env('GOOGLE_MAX_REVIEWS', 20)
    ]
];