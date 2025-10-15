<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $projectRoot = dirname(__DIR__);
    load_env($projectRoot . '/.env');

    $host = env('DB_HOST', '127.0.0.1');
    $port = env('DB_PORT', '3306');
    $dbName = env('DB_DATABASE', 'medaads');
    $username = env('DB_USERNAME', 'root');
    $password = env('DB_PASSWORD', '');
    $charset = env('DB_CHARSET', 'utf8mb4');

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbName, $charset);

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

    return $pdo;
}
