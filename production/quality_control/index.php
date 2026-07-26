<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/db.php';
require_once $rootPath . '/includes/auth.php';

requireLogin();
requirePermission('view_production');

$page_title = 'کنترل کیفیت';

$checks = $pdo->query("
    SELECT qc.*, po.order_number, p.title as product_name
    FROM quality_controls qc
    LEFT JOIN production_orders po ON po.id = qc.production_order_id
    LEFT JOIN products p ON p.id = po.product_id
    ORDER BY qc.checked_at DESC
")->fetchAll();

$contentFile = __DIR__ . '/views/quality_content.php';
require_once $rootPath . '/includes/layout.php';