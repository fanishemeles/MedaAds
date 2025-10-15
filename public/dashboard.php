<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/db.php';

require_login();

$pdo = get_pdo();
ensure_session();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $body = trim((string) ($_POST['body'] ?? ''));
            $targetUrl = trim((string) ($_POST['target_url'] ?? ''));
            $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
            $placementInput = $_POST['placement_id'] ?? '';
            $placementId = $placementInput !== '' ? (int) $placementInput : null;
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($title === '' || $body === '') {
                $error = 'Title and body are required to create an ad.';
            } elseif ($targetUrl !== '' && !filter_var($targetUrl, FILTER_VALIDATE_URL)) {
                $error = 'Target URL must be a valid URL.';
            } elseif ($imageUrl !== '' && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $error = 'Image URL must be a valid URL.';
            } else {
                $statement = $pdo->prepare('INSERT INTO ads (placement_id, title, body, target_url, image_url, is_active, views, created_at, updated_at) VALUES (:placement_id, :title, :body, :target_url, :image_url, :is_active, 0, NOW(), NOW())');
                $statement->execute([
                    'placement_id' => $placementId,
                    'title' => $title,
                    'body' => $body,
                    'target_url' => $targetUrl !== '' ? $targetUrl : null,
                    'image_url' => $imageUrl !== '' ? $imageUrl : null,
                    'is_active' => $isActive,
                ]);
                $message = 'Ad created successfully.';
            }
        } elseif ($action === 'update') {
            $adId = (int) ($_POST['id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $body = trim((string) ($_POST['body'] ?? ''));
            $targetUrl = trim((string) ($_POST['target_url'] ?? ''));
            $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
            $placementInput = $_POST['placement_id'] ?? '';
            $placementId = $placementInput !== '' ? (int) $placementInput : null;
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($adId <= 0) {
                $error = 'Invalid ad selected.';
            } elseif ($title === '' || $body === '') {
                $error = 'Title and body are required to update an ad.';
            } elseif ($targetUrl !== '' && !filter_var($targetUrl, FILTER_VALIDATE_URL)) {
                $error = 'Target URL must be a valid URL.';
            } elseif ($imageUrl !== '' && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $error = 'Image URL must be a valid URL.';
            } else {
                $statement = $pdo->prepare('UPDATE ads SET placement_id = :placement_id, title = :title, body = :body, target_url = :target_url, image_url = :image_url, is_active = :is_active, updated_at = NOW() WHERE id = :id');
                $statement->execute([
                    'placement_id' => $placementId,
                    'title' => $title,
                    'body' => $body,
                    'target_url' => $targetUrl !== '' ? $targetUrl : null,
                    'image_url' => $imageUrl !== '' ? $imageUrl : null,
                    'is_active' => $isActive,
                    'id' => $adId,
                ]);
                $message = 'Ad updated successfully.';
            }
        } elseif ($action === 'delete') {
            $adId = (int) ($_POST['id'] ?? 0);
            if ($adId <= 0) {
                $error = 'Invalid ad selected.';
            } else {
                $statement = $pdo->prepare('DELETE FROM ads WHERE id = :id');
                $statement->execute(['id' => $adId]);
                $message = 'Ad deleted successfully.';
            }
        }
    }
}

$placements = $pdo->query('SELECT id, name FROM placements ORDER BY name')->fetchAll();
$ads = $pdo->query('SELECT ads.*, placements.name AS placement_name FROM ads LEFT JOIN placements ON placements.id = ads.placement_id ORDER BY ads.created_at DESC')->fetchAll();
$user = current_user();

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedaAds | Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MedaAds</a>
            <div class="d-flex align-items-center ms-auto">
                <span class="me-3">Welcome, <?php echo e($user['name'] ?? ''); ?></span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <main class="container mb-5">
        <?php if ($message !== ''): ?>
            <div class="alert alert-success" role="alert">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Create new ad</h2>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                            <input type="hidden" name="action" value="create">
                            <div class="mb-3">
                                <label for="create-title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="create-title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="create-body" class="form-label">Body</label>
                                <textarea class="form-control" id="create-body" name="body" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="create-target" class="form-label">Target URL</label>
                                <input type="url" class="form-control" id="create-target" name="target_url" placeholder="https://example.com">
                            </div>
                            <div class="mb-3">
                                <label for="create-image" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="create-image" name="image_url" placeholder="https://example.com/banner.jpg">
                            </div>
                            <div class="mb-3">
                                <label for="create-placement" class="form-label">Placement</label>
                                <select id="create-placement" class="form-select" name="placement_id">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($placements as $placement): ?>
                                        <option value="<?php echo (int) $placement['id']; ?>"><?php echo e($placement['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="create-active" name="is_active" checked>
                                <label class="form-check-label" for="create-active">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Create ad</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <h2 class="h5 mb-3">Existing ads</h2>
                <?php if (count($ads) === 0): ?>
                    <div class="alert alert-secondary" role="alert">
                        No ads yet. Create your first campaign using the form on the left.
                    </div>
                <?php else: ?>
                    <?php foreach ($ads as $ad): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <h3 class="h6 mb-1"><?php echo e($ad['title']); ?></h3>
                                        <span class="badge <?php echo (int) $ad['is_active'] === 1 ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo (int) $ad['is_active'] === 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                    <div class="text-end small text-muted">
                                        <?php if (!empty($ad['placement_name'])): ?>
                                            <div>Placement: <?php echo e($ad['placement_name']); ?></div>
                                        <?php endif; ?>
                                        <div>Views: <?php echo (int) $ad['views']; ?></div>
                                    </div>
                                </div>
                                <?php if (!empty($ad['body'])): ?>
                                    <p class="mt-3 mb-3"><?php echo nl2br(e($ad['body'])); ?></p>
                                <?php endif; ?>
                                <form method="post" class="row g-2 align-items-end">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo (int) $ad['id']; ?>">
                                    <div class="col-md-6">
                                        <label class="form-label" for="title-<?php echo (int) $ad['id']; ?>">Title</label>
                                        <input type="text" class="form-control" id="title-<?php echo (int) $ad['id']; ?>" name="title" value="<?php echo e($ad['title']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="placement-<?php echo (int) $ad['id']; ?>">Placement</label>
                                        <select class="form-select" id="placement-<?php echo (int) $ad['id']; ?>" name="placement_id">
                                            <option value="" <?php echo $ad['placement_id'] === null ? 'selected' : ''; ?>>Unassigned</option>
                                            <?php foreach ($placements as $placement): ?>
                                                <option value="<?php echo (int) $placement['id']; ?>" <?php echo ((int) $ad['placement_id'] === (int) $placement['id']) ? 'selected' : ''; ?>><?php echo e($placement['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="body-<?php echo (int) $ad['id']; ?>">Body</label>
                                        <textarea class="form-control" id="body-<?php echo (int) $ad['id']; ?>" name="body" rows="3" required><?php echo e($ad['body']); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="target-<?php echo (int) $ad['id']; ?>">Target URL</label>
                                        <input type="url" class="form-control" id="target-<?php echo (int) $ad['id']; ?>" name="target_url" value="<?php echo e((string) $ad['target_url']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="image-<?php echo (int) $ad['id']; ?>">Image URL</label>
                                        <input type="url" class="form-control" id="image-<?php echo (int) $ad['id']; ?>" name="image_url" value="<?php echo e((string) $ad['image_url']); ?>">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="active-<?php echo (int) $ad['id']; ?>" name="is_active" <?php echo (int) $ad['is_active'] === 1 ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="active-<?php echo (int) $ad['id']; ?>">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                                        <button type="submit" class="btn btn-outline-danger btn-sm" form="delete-form-<?php echo (int) $ad['id']; ?>" formnovalidate onclick="return confirm('Delete this ad?');">Delete</button>
                                    </div>
                                </form>
                                <form method="post" class="d-none" id="delete-form-<?php echo (int) $ad['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int) $ad['id']; ?>">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
