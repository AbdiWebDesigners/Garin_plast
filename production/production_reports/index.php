<?php
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/db.php';
require_once $rootPath . '/includes/auth.php';

requireLogin();
requirePermission('view_production');

$page_title = 'گزارشات تولید';

try {
    // آمار کلی
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM production_orders")->fetchColumn();
    $completedOrders = $pdo->query("SELECT COUNT(*) FROM production_orders WHERE status = 'completed'")->fetchColumn();
    $inProgress = $pdo->query("SELECT COUNT(*) FROM production_orders WHERE status = 'in_progress'")->fetchColumn();

    // کل تولید (کیلوگرم)
    $totalKg = $pdo->query("
        SELECT COALESCE(SUM(
            CASE 
                WHEN unit = 'تن' THEN quantity * 1000 
                WHEN unit = 'کیلوگرم' THEN quantity 
                ELSE quantity 
            END
        ), 0) as total_kg 
        FROM production_orders
    ")->fetchColumn();

    // آخرین دستورات
    $recentOrders = $pdo->query("
        SELECT po.*, p.title as product_name 
        FROM production_orders po
        LEFT JOIN products p ON p.id = po.product_id
        ORDER BY po.created_at DESC 
        LIMIT 10
    ")->fetchAll();
} catch(Exception $e) {
    $totalOrders = $completedOrders = $inProgress = 0;
    $totalKg = 0;
    $recentOrders = [];
}

$contentFile = __DIR__ . '/views/reports_content.php';
require_once $rootPath . '/includes/layout.php';