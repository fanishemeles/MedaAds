<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

$adId = isset($_POST['ad_id']) ? (int) $_POST['ad_id'] : 0;

if ($adId <= 0) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'A valid ad_id must be provided.'
    ]);
    exit;
}

try {
    $pdo = get_pdo();
    $statement = $pdo->prepare('UPDATE ads SET views = views + 1 WHERE id = :id');
    $statement->execute(['id' => $adId]);

    echo json_encode([
        'success' => true,
        'message' => 'Ad view recorded.'
    ]);
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to track ad view.',
        'error' => $exception->getMessage()
    ]);
}
