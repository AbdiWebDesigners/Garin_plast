<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/components/card.php';
require_once __DIR__ . '/components/stat_card.php';
require_once __DIR__ . '/components/alert.php';
require_once __DIR__ . '/components/badge.php';
require_once __DIR__ . '/components/button.php';
require_once __DIR__ . '/components/table.php';
require_once __DIR__ . '/components/search.php';
require_once __DIR__ . '/components/pagination.php';
require_once __DIR__ . '/components/modal.php';
require_once __DIR__ . '/components/upload.php';
require_once __DIR__.'/components/form.php';
require_once __DIR__ . '/components/empty_state.php';

if (file_exists(__DIR__ . '/permissions.php')) {
    require_once __DIR__ . '/permissions.php';
}

if (file_exists(__DIR__ . '/functions.php')) {
    require_once __DIR__ . '/functions.php';
}

if (!isLoggedIn()) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$pageTitle = $pageTitle ?? 'ERP Garin Plast';
$pageDescription = $pageDescription ?? '';
$pageIcon = $pageIcon ?? 'fa-home';

require_once __DIR__ . '/header.php';

echo '<div class="wrapper">';

require_once dirname(__DIR__) . '/admin/sidebar.php';

echo '<div class="main-content">';

if (file_exists(__DIR__ . '/topbar.php')) {
    require_once __DIR__ . '/topbar.php';
}

if (file_exists(__DIR__ . '/page_header.php')) {
    require_once __DIR__ . '/page_header.php';
}

echo '<div class="container-fluid py-3">';

if (isset($contentFile) && file_exists($contentFile)) {
    require $contentFile;
} else {
    echo '<div class="alert alert-danger">';
    echo 'Content File Not Found : ' . htmlspecialchars($contentFile ?? '');
    echo '</div>';
}

echo '</div>';

require_once __DIR__ . '/footer.php';

echo '</div>';

echo '</div>';
