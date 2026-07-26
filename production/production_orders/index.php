<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/db.php';
require_once $rootPath . '/includes/auth.php';

requireLogin();
requirePermission('view_production');

$page_title = 'دستورات تولید';

$orders = $pdo->query("
    SELECT po.*, p.title as product_name 
    FROM production_orders po
    LEFT JOIN products p ON p.id = po.product_id
    ORDER BY po.created_at DESC
")->fetchAll();

$contentFile = __DIR__ . '/views/orders_content.php';
require_once $rootPath . '/includes/layout.php';