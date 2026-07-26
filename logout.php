<?php

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/activity_logger.php';

if (isLoggedIn()) {

    logActivity(
        'auth',
        'logout',
        currentUserId(),
        'خروج از سیستم'
    );

}

session_unset();

session_destroy();

header("Location: " . BASE_URL . "login.php");

exit;