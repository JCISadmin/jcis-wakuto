<?php

return [
    'agentList'=> [
        1 => [
            'name' => env('AGENT1_NAME'),
            'dbConnection' => 'mysql_agent1'
        ],
        2 => [
            'name' => env('API_DB_NAME'),
            'dbConnection' => 'hds_api'
        ]
    ]
];
