<?php

return [
    'connection' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'rest_api'),
            'username' => env('DB_USERNAME', 'rest_api'),
            'password' => env('DB_PASSWORD', 'secret'),
        ],
        'postgres' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'rest_api'),
            'username' => env('DB_USERNAME', 'rest_api'),
            'password' => env('DB_PASSWORD', 'secret'),
        ],
    ],
];
