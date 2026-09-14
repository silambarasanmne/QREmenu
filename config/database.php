<?php

return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite', // 'sqlite' or 'mysql'
    'sqlite' => [
        'path' => __DIR__ . '/../db/database.sqlite'
    ],
    'mysql' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'dbname' => $_ENV['DB_NAME'] ?? 'emenu_db',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => 'utf8mb4'
    ]
];
