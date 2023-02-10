<?php

return [
    'apiKey' => env('ACURIS_API_KEY'),
    'uri' => [
        'businesses' => env('ACURIS_API_URI_BUSINESSES'),
        'individuals' => env('ACURIS_API_URI_INDIVIDUALS'),
    ],
    'timeout' => [
        'search' => 240,
        'lookup' => 240,
    ],
    'default' => [
        'threshold' => 50,
        'dobMatching' => 'exact',
    ]
];