<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/db.php';
require_once $rootPath . '/includes/auth.php';

requireLogin();
requirePermission('view_production');

$page_title = 'خطوط تولید';

$lines = $pdo->query("SELECT * FROM production_lines ORDER BY name")->fetchAll();

$contentFile = __DIR__ . '/views/lines_content.php';
require_once $rootPath . '/includes/layout.php';