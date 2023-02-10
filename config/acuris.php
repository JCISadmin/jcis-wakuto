<?php

return [
    'apiKey' => env('ACURIS_API_KEY'),
    'uri' => [
        'businesses' => env('ACURIS_API_URI_BUSINESSES'),
        'individuals' => env('ACURIS_API_URI_INDIVIDUALS'),
    ],
    'timeout' => [
        'search' => env('ACURIS_API_SEARCH_TIMEOUT_SECOND'),
        'lookup' => env('ACURIS_API_LOOKUP_TIMEOUT_SECOND'),
    ],
    'default' => [
        'threshold' => 50,
        'dobMatching' => 'exact',
    ]
];