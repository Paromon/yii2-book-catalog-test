<?php

declare(strict_types=1);

return [
    'class' => yii\db\Connection::class,
    'dsn' => sprintf(
        'mysql:host=%s;port=%s;dbname=%s',
        env('MYSQL_HOST', 'db'),
        env('MYSQL_PORT', '3306'),
        env('MYSQL_DATABASE', 'book_catalog')
    ),
    'username' => (string) env('MYSQL_USER', 'app'),
    'password' => (string) env('MYSQL_PASSWORD', 'app'),
    'charset' => 'utf8mb4',
];
