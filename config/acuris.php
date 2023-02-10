<?php

return [
    'apiKey' => env('ACURIS_API_KEY'),
    'uri' => [
        'businesses' => env('ACURIS_API_URI_BUSINESSES'),
        'individuals' => env('ACURIS_API_URI_INDIVIDUALS'),
    ],
    'timeout' => [
        'search' => 300,
        'lookup' => 300,
    ],
    'default' => [
        'threshold' => 50,
        'dobMatching' => 'exact',
    ]
];