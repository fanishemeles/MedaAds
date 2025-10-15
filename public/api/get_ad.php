<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

header('Content-Type: application/json');

try {
    $pdo = get_pdo();
    $statement = $pdo->query('SELECT id, title, body, target_url, image_url FROM ads WHERE is_active = 1 ORDER BY RAND() LIMIT 1');
    $ad = $statement->fetch();

    if (!$ad) {
        echo json_encode([
            'success' => false,
            'message' => 'No active ads available.'
        ], JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode([
        'success' => true,
        'ad' => [
            'id' => (int) $ad['id'],
            'title' => $ad['title'],
            'body' => $ad['body'],
            'target_url' => $ad['target_url'],
            'image_url' => $ad['image_url']
        ]
    ], JSON_PRETTY_PRINT);
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load ad.',
        'error' => $exception->getMessage()
    ]);
}
