<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';

ensure_session();

if (is_logged_in()) {
    redirect('dashboard.php');
}

redirect('login.php');
