<?php

declare(strict_types=1);

use PDO;
use PDOException;

require_once __DIR__ . '/helpers.php';

$projectRoot = dirname(__DIR__);
load_env($projectRoot . '/.env');

$host = env('DB_HOST', '127.0.0.1');
$database = env('DB_NAME', 'medaads');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', '');
$charset = 'utf8mb4';

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $database, $charset);

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    throw new PDOException('Database connection failed: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
}

function get_pdo(): PDO
{
    global $pdo;

    return $pdo;
}
