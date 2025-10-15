<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/db.php';

ensure_session();

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

try {
    $totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Throwable $exception) {
    $totalUsers = 0;
}

try {
    $totalAds = (int) $pdo->query('SELECT COUNT(*) FROM ads')->fetchColumn();
} catch (Throwable $exception) {
    $totalAds = 0;
}

$userName = $_SESSION['user_name'] ?? 'User';

?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedaAds Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-dark text-light d-flex flex-column h-100">
    <header class="border-bottom border-secondary py-3 mb-4">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <span class="fs-4 fw-semibold">MedaAds Dashboard</span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary">Welcome, <?php echo e($userName); ?></span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main class="flex-grow-1">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card bg-secondary text-white shadow-sm h-100">
                        <div class="card-body">
                            <h2 class="card-title h5">Total Users</h2>
                            <p class="display-5 fw-bold mb-0"><?php echo number_format($totalUsers); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-secondary text-white shadow-sm h-100">
                        <div class="card-body">
                            <h2 class="card-title h5">Total Ads</h2>
                            <p class="display-5 fw-bold mb-0"><?php echo number_format($totalAds); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-auto py-3 border-top border-secondary">
        <div class="container text-center text-secondary small">
            &copy; <?php echo date('Y'); ?> MedaAds
        </div>
    </footer>
</body>
</html>
